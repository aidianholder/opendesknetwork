<?php
/**
 * WP-CLI commands for running the network.
 *
 * @package ODN_Network
 */

defined( 'ABSPATH' ) || exit;

/**
 * Manage Open Desk Network publishers.
 */
class ODN_Publisher_Command {

	/**
	 * Creates a publisher: a subdomain site owned by a user with the Publisher role.
	 *
	 * ## OPTIONS
	 *
	 * <subdomain>
	 * : Subdomain for the site, e.g. "janedoe" for janedoe.<network domain>.
	 *
	 * <email>
	 * : The journalist's email. An existing user with this email is reused.
	 *
	 * [--name=<display-name>]
	 * : Display name for a new user. Defaults to the subdomain.
	 *
	 * [--title=<site-title>]
	 * : Site title. Defaults to the display name.
	 *
	 * [--username=<login>]
	 * : Login for a new user. Defaults to the subdomain.
	 *
	 * [--send-email]
	 * : Email a new user a link to set their password.
	 *
	 * ## EXAMPLES
	 *
	 *     wp odn publisher create janedoe jane@example.com --name="Jane Doe" --send-email
	 *
	 * @param array $args       Positional arguments.
	 * @param array $assoc_args Flags.
	 */
	public function create( $args, $assoc_args ) {
		list( $subdomain, $email ) = $args;

		$subdomain = strtolower( $subdomain );
		if ( ! preg_match( '/^[a-z0-9]([a-z0-9-]{0,61}[a-z0-9])?$/', $subdomain ) ) {
			WP_CLI::error( 'Subdomain may contain only lowercase letters, numbers and hyphens.' );
		}
		if ( in_array( $subdomain, get_subdirectory_reserved_names(), true ) || 'www' === $subdomain ) {
			WP_CLI::error( "'{$subdomain}' is reserved." );
		}
		if ( ! is_email( $email ) ) {
			WP_CLI::error( "'{$email}' is not a valid email address." );
		}

		$network = get_network();
		$domain  = $subdomain . '.' . preg_replace( '/^www\./', '', $network->domain );
		if ( domain_exists( $domain, $network->path, $network->id ) ) {
			WP_CLI::error( "A site already exists at {$domain}." );
		}

		$name = WP_CLI\Utils\get_flag_value( $assoc_args, 'name', $subdomain );
		$user = get_user_by( 'email', $email );

		if ( $user ) {
			WP_CLI::log( "Using existing user {$user->user_login} (#{$user->ID})." );
			$user_id = $user->ID;
		} else {
			$login = WP_CLI\Utils\get_flag_value( $assoc_args, 'username', $subdomain );
			if ( username_exists( $login ) ) {
				WP_CLI::error( "Username '{$login}' is taken. Pass --username." );
			}
			$user_id = wp_insert_user(
				array(
					'user_login'   => $login,
					'user_email'   => $email,
					'user_pass'    => wp_generate_password( 24 ),
					'display_name' => $name,
					'role'         => '',
				)
			);
			if ( is_wp_error( $user_id ) ) {
				WP_CLI::error( $user_id );
			}
			WP_CLI::log( "Created user {$login} (#{$user_id})." );
		}

		$title   = WP_CLI\Utils\get_flag_value( $assoc_args, 'title', $name );
		$site_id = wpmu_create_blog( $domain, $network->path, $title, $user_id, array( 'public' => 1 ), $network->id );
		if ( is_wp_error( $site_id ) ) {
			WP_CLI::error( $site_id );
		}

		// wp_insert_user() makes a new user a (role-less) member of the main site.
		if ( ! $user ) {
			remove_user_from_blog( $user_id, get_main_site_id() );
		}

		if ( ! $user && WP_CLI\Utils\get_flag_value( $assoc_args, 'send-email', false ) ) {
			wp_new_user_notification( $user_id, null, 'user' );
			WP_CLI::log( "Sent a set-password email to {$email}." );
		}

		WP_CLI::success( "Created {$title} at " . get_home_url( $site_id, '/' ) . " (site #{$site_id}), owned by user #{$user_id} as Publisher." );
		WP_CLI::log( "Remember: {$domain} must also be added as a domain on the hosting account and in DNS." );
	}

	/**
	 * Lists publisher sites and their owners.
	 *
	 * [--format=<format>]
	 * : table, csv, json, yaml. Default table.
	 *
	 * @subcommand list
	 *
	 * @param array $args       Positional arguments.
	 * @param array $assoc_args Flags.
	 */
	public function list_( $args, $assoc_args ) {
		$rows = array();
		foreach ( get_sites( array( 'number' => 0 ) ) as $site ) {
			$publishers = get_users(
				array(
					'blog_id' => $site->blog_id,
					'role'    => 'publisher',
					'fields'  => array( 'user_login' ),
				)
			);
			$rows[]     = array(
				'site_id'    => $site->blog_id,
				'url'        => get_home_url( $site->blog_id, '/' ),
				'publishers' => implode( ', ', wp_list_pluck( $publishers, 'user_login' ) ),
			);
		}
		WP_CLI\Utils\format_items( WP_CLI\Utils\get_flag_value( $assoc_args, 'format', 'table' ), $rows, array( 'site_id', 'url', 'publishers' ) );
	}

	/**
	 * Re-installs the Publisher role on every site.
	 *
	 * @subcommand sync-roles
	 */
	public function sync_roles() {
		foreach ( get_sites( array( 'number' => 0 ) ) as $site ) {
			switch_to_blog( $site->blog_id );
			odn_install_roles();
			restore_current_blog();
		}
		WP_CLI::success( 'Publisher role synced on every site.' );
	}
}

WP_CLI::add_command( 'odn publisher', 'ODN_Publisher_Command' );

/**
 * One-time network setup tasks.
 */
class ODN_Setup_Command {

	/**
	 * Sets up the network's main site: name, logo, home page with the publisher
	 * directory, and a logo-only header. Safe to run more than once.
	 *
	 * ## EXAMPLES
	 *
	 *     wp odn setup main-site
	 *
	 * @subcommand main-site
	 */
	public function main_site() {
		switch_to_blog( get_main_site_id() );

		if ( get_stylesheet() !== ODN_DEFAULT_THEME ) {
			switch_theme( ODN_DEFAULT_THEME );
		}

		odn_remove_default_content();

		update_option( 'blogname', 'Open Desk Network' );
		update_option( 'blogdescription', 'Independent journalists, one network' );

		// Logo.
		if ( ! get_option( 'site_logo' ) ) {
			$logo_id = $this->import_theme_image( 'assets/images/odn-logo.png', 'Open Desk Network logo' );
			update_option( 'site_logo', $logo_id );
			WP_CLI::log( "Set site logo (attachment #{$logo_id})." );
		}

		// Home page.
		$home = get_page_by_path( 'home' );
		if ( ! $home ) {
			$home_id = wp_insert_post(
				array(
					'post_type'    => 'page',
					'post_status'  => 'publish',
					'post_title'   => 'Home',
					'post_name'    => 'home',
					'post_content' => '<!-- wp:pattern {"slug":"odn/network-home"} /-->',
				),
				true
			);
			if ( is_wp_error( $home_id ) ) {
				WP_CLI::error( $home_id );
			}
			update_post_meta( $home_id, '_wp_page_template', 'page-landing' );
			WP_CLI::log( "Created Home page (#{$home_id})." );
		} else {
			$home_id = $home->ID;
		}
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $home_id );

		// The logo already spells the name, so the main site's header shows the logo alone.
		$this->save_template_part(
			'header',
			'Header – Classic',
			'header',
			'<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30"}},"border":{"bottom":{"color":"var:preset|color|accent","width":"4px","style":"solid"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull" style="border-bottom-color:var(--wp--preset--color--accent);border-bottom-style:solid;border-bottom-width:4px;padding-top:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30)"><!-- wp:group {"align":"wide","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} -->
<div class="wp-block-group alignwide"><!-- wp:site-logo {"width":200,"shouldSyncIcon":false} /-->

<!-- wp:navigation {"overlayBackgroundColor":"base","overlayTextColor":"contrast","layout":{"type":"flex","justifyContent":"right"}} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group -->'
		);

		restore_current_blog();
		odn_flush_publisher_sites_cache();
		WP_CLI::success( 'Main site is set up.' );
	}

	/**
	 * Copies an image from the theme into the media library.
	 *
	 * @param string $relative Path inside the theme.
	 * @param string $title    Attachment title.
	 * @return int Attachment ID.
	 */
	private function import_theme_image( $relative, $title ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$tmp = wp_tempnam( basename( $relative ) );
		copy( get_theme_file_path( $relative ), $tmp );

		$id = media_handle_sideload(
			array(
				'name'     => basename( $relative ),
				'tmp_name' => $tmp,
			),
			0,
			$title
		);
		if ( is_wp_error( $id ) ) {
			WP_CLI::error( $id );
		}
		return $id;
	}

	/**
	 * Saves a site-level customisation of a theme template part, as the Site Editor would.
	 *
	 * @param string $slug    Template part slug.
	 * @param string $title   Title.
	 * @param string $area    Area (header, footer, uncategorized).
	 * @param string $content Block markup.
	 */
	private function save_template_part( $slug, $title, $area, $content ) {
		$existing = get_block_template( get_stylesheet() . '//' . $slug, 'wp_template_part' );
		$postarr  = array(
			'post_type'    => 'wp_template_part',
			'post_status'  => 'publish',
			'post_name'    => $slug,
			'post_title'   => $title,
			'post_content' => $content,
		);
		if ( $existing && $existing->wp_id ) {
			$postarr['ID'] = $existing->wp_id;
		}
		$id = wp_insert_post( wp_slash( $postarr ), true );
		if ( is_wp_error( $id ) ) {
			WP_CLI::error( $id );
		}
		wp_set_object_terms( $id, get_stylesheet(), 'wp_theme' );
		wp_set_object_terms( $id, $area, 'wp_template_part_area' );
		WP_CLI::log( "Saved {$slug} template part (#{$id})." );
	}
}

WP_CLI::add_command( 'odn setup', 'ODN_Setup_Command' );
