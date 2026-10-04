<?php
/**
 * Plugin Name: Open Desk Network
 * Description: Network rules for Open Desk Network — the Publisher role, per-site permissions, new-site defaults, network blocks and WP-CLI tools.
 * Version: 1.0.0
 * Author: Open Desk Network
 * Network: true
 *
 * @package ODN_Network
 */

defined( 'ABSPATH' ) || exit;

if ( ! is_multisite() ) {
	return;
}

define( 'ODN_NETWORK_DIR', __DIR__ . '/odn-network' );

// Theme every new site starts on.
if ( ! defined( 'ODN_DEFAULT_THEME' ) ) {
	define( 'ODN_DEFAULT_THEME', 'odn' );
}

require ODN_NETWORK_DIR . '/roles.php';
require ODN_NETWORK_DIR . '/sites.php';
require ODN_NETWORK_DIR . '/blocks.php';
require ODN_NETWORK_DIR . '/join.php';

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require ODN_NETWORK_DIR . '/cli.php';
}
