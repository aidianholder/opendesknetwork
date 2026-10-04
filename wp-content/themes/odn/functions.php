<?php
/**
 * Open Desk Network theme.
 *
 * @package ODN
 */

defined( 'ABSPATH' ) || exit;

/**
 * Theme supports and editor styles.
 */
function odn_theme_setup() {
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/theme.css' );
}
add_action( 'after_setup_theme', 'odn_theme_setup' );

/**
 * Front-end stylesheet.
 */
function odn_theme_enqueue() {
	wp_enqueue_style(
		'odn-theme',
		get_theme_file_uri( 'assets/css/theme.css' ),
		array(),
		filemtime( get_theme_file_path( 'assets/css/theme.css' ) )
	);
}
add_action( 'wp_enqueue_scripts', 'odn_theme_enqueue' );

/**
 * Block styles publishers can pick from the editor's Styles panel.
 */
function odn_register_block_styles() {
	foreach ( array( 'core/group', 'core/cover' ) as $block ) {
		register_block_style( $block, array( 'name' => 'torn-edge', 'label' => __( 'Torn edge', 'odn' ) ) );
		register_block_style( $block, array( 'name' => 'torn-edge-top', 'label' => __( 'Torn top edge', 'odn' ) ) );
	}

	foreach ( array( 'core/paragraph', 'core/heading', 'core/site-title', 'core/post-title' ) as $block ) {
		register_block_style( $block, array( 'name' => 'wide-caps', 'label' => __( 'Wide caps', 'odn' ) ) );
	}

	register_block_style( 'core/separator', array( 'name' => 'thick', 'label' => __( 'Thick rule', 'odn' ) ) );
	register_block_style( 'core/image', array( 'name' => 'framed', 'label' => __( 'Framed', 'odn' ) ) );
}
add_action( 'init', 'odn_register_block_styles' );

/**
 * Pattern category for the theme's patterns.
 */
function odn_register_pattern_category() {
	register_block_pattern_category( 'odn', array( 'label' => __( 'Open Desk Network', 'odn' ) ) );
}
add_action( 'init', 'odn_register_pattern_category' );

/**
 * odn/network-badge: links a publisher's site back to the network home.
 */
function odn_register_theme_blocks() {
	wp_register_script(
		'odn-theme-blocks',
		get_theme_file_uri( 'assets/js/blocks.js' ),
		array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-server-side-render', 'wp-i18n' ),
		filemtime( get_theme_file_path( 'assets/js/blocks.js' ) ),
		true
	);

	register_block_type(
		'odn/network-badge',
		array(
			'api_version'     => 3,
			'title'           => 'Network Badge',
			'category'        => 'theme',
			'attributes'      => array(
				'tone'       => array(
					'type'    => 'string',
					'default' => 'dark',
				),
				'showOnMain' => array(
					'type'    => 'boolean',
					'default' => false,
				),
			),
			'supports'        => array(
				'align'      => array( 'left', 'center', 'right' ),
				'typography' => array( 'fontSize' => true ),
			),
			'editor_script'   => 'odn-theme-blocks',
			'render_callback' => 'odn_render_network_badge',
		)
	);
}
add_action( 'init', 'odn_register_theme_blocks' );

/**
 * Renders odn/network-badge.
 *
 * @param array $attributes Block attributes.
 * @return string
 */
function odn_render_network_badge( $attributes ) {
	if ( is_main_site() && empty( $attributes['showOnMain'] ) && ! wp_is_serving_rest_request() ) {
		return '';
	}

	$mark = 'light' === $attributes['tone'] ? 'odn-mark-white.png' : 'odn-mark.png';

	return sprintf(
		'<div %1$s><a class="odn-badge" href="%2$s"><img class="odn-badge__mark" src="%3$s" alt="Open" width="58" height="40" /><span class="odn-badge__text">%4$s</span></a></div>',
		get_block_wrapper_attributes( array( 'class' => 'is-tone-' . sanitize_html_class( $attributes['tone'] ) ) ),
		esc_url( network_home_url( '/' ) ),
		esc_url( get_theme_file_uri( 'assets/images/' . $mark ) ),
		esc_html__( 'Desk Network', 'odn' )
	);
}

/**
 * Sites without their own icon use the network mark.
 *
 * @param string $url Site icon URL.
 * @return string
 */
function odn_default_site_icon( $url ) {
	return $url ? $url : get_theme_file_uri( 'assets/images/odn-icon.png' );
}
add_filter( 'get_site_icon_url', 'odn_default_site_icon' );
