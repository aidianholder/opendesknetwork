<?php
/**
 * Network blocks.
 *
 * odn/publisher-directory — lists the network's publisher sites with each one's
 * latest story.
 * odn/join-form — the "join the network" application form (see join.php).
 *
 * Both are server-rendered, so they need no build step.
 *
 * @package ODN_Network
 */

defined( 'ABSPATH' ) || exit;

/**
 * Registers the network blocks and their editor script.
 */
function odn_register_network_blocks() {
	// content_url(), not plugins_url(): the mu-plugin may be symlinked in development.
	wp_register_script(
		'odn-network-blocks',
		content_url( 'mu-plugins/odn-network/blocks/editor.js' ),
		array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-server-side-render', 'wp-i18n' ),
		filemtime( ODN_NETWORK_DIR . '/blocks/editor.js' ),
		true
	);

	register_block_type(
		'odn/publisher-directory',
		array(
			'api_version'     => 3,
			'title'           => 'Publisher Directory',
			'category'        => 'widgets',
			'attributes'      => array(
				'number'     => array(
					'type'    => 'integer',
					'default' => 24,
				),
				'showLatest' => array(
					'type'    => 'boolean',
					'default' => true,
				),
			),
			'supports'        => array(
				'align'   => array( 'wide', 'full' ),
				'spacing' => array(
					'margin'  => true,
					'padding' => true,
				),
			),
			'editor_script'   => 'odn-network-blocks',
			'render_callback' => 'odn_render_publisher_directory',
		)
	);

	register_block_type(
		'odn/join-form',
		array(
			'api_version'     => 3,
			'title'           => 'Join Form',
			'category'        => 'widgets',
			'supports'        => array(
				'align'   => array( 'wide' ),
				'spacing' => array(
					'margin'  => true,
					'padding' => true,
				),
			),
			'editor_script'   => 'odn-network-blocks',
			'render_callback' => 'odn_render_join_form',
		)
	);
}
add_action( 'init', 'odn_register_network_blocks' );

/**
 * Renders odn/publisher-directory.
 *
 * @param array $attributes Block attributes.
 * @return string
 */
function odn_render_publisher_directory( $attributes ) {
	$sites = odn_get_publisher_sites( max( 1, (int) $attributes['number'] ) );

	if ( ! $sites ) {
		return sprintf(
			'<div %s><p class="odn-directory__empty">%s</p></div>',
			get_block_wrapper_attributes(),
			esc_html__( 'No publishers yet.', 'odn' )
		);
	}

	$items = '';
	foreach ( $sites as $site ) {
		$icon = $site['icon']
			? sprintf( '<img class="odn-directory__icon" src="%s" alt="" width="64" height="64" loading="lazy" />', esc_url( $site['icon'] ) )
			: '';

		$latest = '';
		if ( ! empty( $attributes['showLatest'] ) && $site['latest'] ) {
			$latest = sprintf(
				'<p class="odn-directory__latest"><span class="odn-directory__label">%s</span> <a href="%s">%s</a></p>',
				esc_html__( 'Latest', 'odn' ),
				esc_url( $site['latest']['url'] ),
				esc_html( $site['latest']['title'] )
			);
		}

		$items .= sprintf(
			'<li class="odn-directory__item">%s<div class="odn-directory__body"><h3 class="odn-directory__name"><a href="%s">%s</a></h3>%s%s</div></li>',
			$icon,
			esc_url( $site['url'] ),
			esc_html( $site['name'] ),
			$site['description'] ? '<p class="odn-directory__tagline">' . esc_html( $site['description'] ) . '</p>' : '',
			$latest
		);
	}

	return sprintf( '<div %s><ul class="odn-directory">%s</ul></div>', get_block_wrapper_attributes(), $items );
}
