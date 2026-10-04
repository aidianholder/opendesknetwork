<?php
/**
 * Defaults applied to every new site on the network.
 *
 * @package ODN_Network
 */

defined( 'ABSPATH' ) || exit;

/**
 * Sets up a freshly created site: Publisher role, network theme, and the
 * site's owner as its Publisher rather than an Administrator.
 *
 * Runs after core's own initialisation (priority 10), which makes the owner an
 * administrator.
 *
 * @param WP_Site $site New site.
 * @param array   $args Arguments passed to wp_insert_site(), including user_id.
 */
function odn_initialize_site( $site, $args ) {
	switch_to_blog( $site->blog_id );

	odn_install_roles();
	odn_remove_default_content();

	if ( wp_get_theme( ODN_DEFAULT_THEME )->exists() ) {
		switch_theme( ODN_DEFAULT_THEME );
	}

	// Core starts subdomain sites on http (it can't assume a wildcard certificate).
	// Every site here gets its own certificate, so follow the main site's scheme.
	if ( 'https' === wp_parse_url( get_home_url( get_main_site_id() ), PHP_URL_SCHEME ) ) {
		update_option( 'home', set_url_scheme( get_option( 'home' ), 'https' ) );
		update_option( 'siteurl', set_url_scheme( get_option( 'siteurl' ), 'https' ) );
	}

	if ( ! empty( $args['user_id'] ) && ! is_super_admin( $args['user_id'] ) ) {
		$owner = new WP_User( $args['user_id'] );
		$owner->set_role( 'publisher' );
	}

	restore_current_blog();
}
add_action( 'wp_initialize_site', 'odn_initialize_site', 20, 2 );

/**
 * Removes WordPress's sample post, page and comment from the current site.
 */
function odn_remove_default_content() {
	$hello = get_page_by_path( 'hello-world', OBJECT, 'post' );
	if ( $hello ) {
		wp_delete_post( $hello->ID, true );
	}
	$sample = get_page_by_path( 'sample-page' );
	if ( $sample ) {
		wp_delete_post( $sample->ID, true );
	}
}

/**
 * Lists the network's publisher sites (every public site except the main one).
 *
 * @param int $number Maximum sites to return.
 * @return array[] Each with id, name, description, url, icon, latest.
 */
function odn_get_publisher_sites( $number = 100 ) {
	$cached = get_site_transient( 'odn_publisher_sites' );
	if ( is_array( $cached ) ) {
		return array_slice( $cached, 0, $number );
	}

	$sites = get_sites(
		array(
			'number'       => 500,
			'public'       => 1,
			'archived'     => 0,
			'spam'         => 0,
			'deleted'      => 0,
			'site__not_in' => array( get_main_site_id() ),
			'orderby'      => 'domain',
		)
	);

	$out = array();
	foreach ( $sites as $site ) {
		switch_to_blog( $site->blog_id );

		$latest = get_posts(
			array(
				'numberposts'      => 1,
				'post_status'      => 'publish',
				'suppress_filters' => false,
			)
		);

		$out[] = array(
			'id'          => (int) $site->blog_id,
			'name'        => get_bloginfo( 'name' ),
			'description' => get_bloginfo( 'description' ),
			'url'         => home_url( '/' ),
			'icon'        => get_site_icon_url( 192 ),
			'latest'      => $latest ? array(
				'title' => get_the_title( $latest[0] ),
				'url'   => get_permalink( $latest[0] ),
				'date'  => get_the_date( '', $latest[0] ),
			) : null,
		);

		restore_current_blog();
	}

	set_site_transient( 'odn_publisher_sites', $out, 15 * MINUTE_IN_SECONDS );
	return array_slice( $out, 0, $number );
}

/**
 * Drops the cached publisher list when something it shows changes.
 */
function odn_flush_publisher_sites_cache() {
	delete_site_transient( 'odn_publisher_sites' );
}
add_action( 'wp_initialize_site', 'odn_flush_publisher_sites_cache' );
add_action( 'wp_update_site', 'odn_flush_publisher_sites_cache' );
add_action( 'wp_delete_site', 'odn_flush_publisher_sites_cache' );
add_action( 'update_option_blogname', 'odn_flush_publisher_sites_cache' );
add_action( 'update_option_blogdescription', 'odn_flush_publisher_sites_cache' );
add_action( 'update_option_site_icon', 'odn_flush_publisher_sites_cache' );

/**
 * Flushes the list when a post is published or unpublished.
 *
 * @param string  $new_status New status.
 * @param string  $old_status Old status.
 * @param WP_Post $post       Post.
 */
function odn_flush_on_publish( $new_status, $old_status, $post ) {
	if ( 'post' === $post->post_type && ( 'publish' === $new_status || 'publish' === $old_status ) ) {
		odn_flush_publisher_sites_cache();
	}
}
add_action( 'transition_post_status', 'odn_flush_on_publish', 10, 3 );
