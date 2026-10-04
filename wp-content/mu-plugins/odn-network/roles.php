<?php
/**
 * The Publisher role and the network-wide capability rules.
 *
 * A publisher is a journalist who runs one subdomain. On their own site they
 * can write and publish, manage media, change site settings, and customise the
 * theme (templates, header/footer layouts, styles and custom CSS). They hold no
 * role on anyone else's site, so they cannot touch other subdomains.
 *
 * Plugin and theme management is reserved for super admins everywhere.
 *
 * @package ODN_Network
 */

defined( 'ABSPATH' ) || exit;

// Bump to re-sync the role definition on every site (on next page load).
const ODN_ROLES_VERSION = 1;

/**
 * Capabilities granted to the Publisher role.
 *
 * @return array<string,bool>
 */
function odn_publisher_capabilities() {
	$caps = array(
		'read',

		// Posts.
		'edit_posts',
		'edit_others_posts',
		'edit_published_posts',
		'edit_private_posts',
		'publish_posts',
		'delete_posts',
		'delete_others_posts',
		'delete_published_posts',
		'delete_private_posts',
		'read_private_posts',

		// Pages.
		'edit_pages',
		'edit_others_pages',
		'edit_published_pages',
		'edit_private_pages',
		'publish_pages',
		'delete_pages',
		'delete_others_pages',
		'delete_published_pages',
		'delete_private_pages',
		'read_private_pages',

		// Taxonomy, comments, media.
		'manage_categories',
		'moderate_comments',
		'upload_files',

		// Their own site's settings and appearance (Site Editor, header/footer, styles).
		'manage_options',
		'edit_theme_options',
		'export',

		// Custom CSS for their own site. See odn_map_meta_cap().
		'odn_edit_css',
	);

	return array_fill_keys( $caps, true );
}

/**
 * (Re)creates the Publisher role on the current site.
 */
function odn_install_roles() {
	remove_role( 'publisher' );
	add_role( 'publisher', 'Publisher', odn_publisher_capabilities() );
	update_option( 'odn_roles_version', ODN_ROLES_VERSION );
}

/**
 * Keeps each site's role definition current without a manual migration.
 */
function odn_maybe_install_roles() {
	if ( (int) get_option( 'odn_roles_version' ) !== ODN_ROLES_VERSION ) {
		odn_install_roles();
	}
}
add_action( 'init', 'odn_maybe_install_roles', 1 );

/**
 * Capabilities no one but a super admin may use, on any site.
 */
function odn_super_admin_only_capabilities() {
	return array(
		'activate_plugins',
		'activate_plugin',
		'deactivate_plugin',
		'install_plugins',
		'update_plugins',
		'delete_plugins',
		'edit_plugins',
		'upload_plugins',
		'resume_plugin',
		'install_themes',
		'update_themes',
		'delete_themes',
		'edit_themes',
		'upload_themes',
		'switch_themes',
		'resume_theme',
		'update_core',
		'install_languages',
		'update_languages',
		'delete_site',
	);
}

/**
 * Applies the network's capability rules.
 *
 * @param string[] $caps    Primitive capabilities required.
 * @param string   $cap     Capability being checked.
 * @param int      $user_id User ID.
 * @return string[]
 */
function odn_map_meta_cap( $caps, $cap, $user_id ) {
	if ( is_super_admin( $user_id ) ) {
		return $caps;
	}

	if ( in_array( $cap, odn_super_admin_only_capabilities(), true ) ) {
		return array( 'do_not_allow' );
	}

	// Core limits custom CSS to super admins on multisite. Let anyone holding
	// odn_edit_css on this site edit this site's CSS. Core still rejects markup
	// (e.g. a closing </style>) in the CSS itself.
	if ( 'edit_css' === $cap && ! ( defined( 'DISALLOW_UNFILTERED_HTML' ) && DISALLOW_UNFILTERED_HTML ) ) {
		return array( 'odn_edit_css' );
	}

	return $caps;
}
add_filter( 'map_meta_cap', 'odn_map_meta_cap', 10, 3 );
