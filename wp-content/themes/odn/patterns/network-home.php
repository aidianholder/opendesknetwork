<?php
/**
 * Title: Network home
 * Slug: odn/network-home
 * Categories: odn, featured
 * Description: Front page for the network's main site: logo, a torn yellow banner, and the publisher directory.
 *
 * @package ODN
 */

?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|50"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--50)">
	<!-- wp:image {"width":"560px","sizeSlug":"full","linkDestination":"none","align":"center"} -->
	<figure class="wp-block-image aligncenter size-full is-resized"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/odn-logo.png' ) ); ?>" alt="<?php esc_attr_e( 'Open Desk Network', 'odn' ); ?>" style="width:560px"/></figure>
	<!-- /wp:image -->
</div>
<!-- /wp:group -->

<!-- wp:group {"align":"full","className":"is-style-torn-edge","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|60"}}},"backgroundColor":"accent","textColor":"on-accent","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull is-style-torn-edge has-on-accent-color has-accent-background-color has-text-color has-background" style="padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--60)">
	<!-- wp:heading {"textAlign":"center","level":1,"className":"is-style-wide-caps","fontSize":"xx-large"} -->
	<h1 class="wp-block-heading has-text-align-center is-style-wide-caps has-xx-large-font-size"><?php esc_html_e( 'Desk Network', 'odn' ); ?></h1>
	<!-- /wp:heading -->

	<!-- wp:paragraph {"align":"center","fontSize":"large"} -->
	<p class="has-text-align-center has-large-font-size"><?php esc_html_e( 'Independent journalists, each with their own desk. One network.', 'odn' ); ?></p>
	<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|60"},"blockGap":"var:preset|spacing|40"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull" style="padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--60)">
	<!-- wp:heading {"align":"wide","className":"is-style-wide-caps","textColor":"muted","fontSize":"small"} -->
	<h2 class="wp-block-heading alignwide is-style-wide-caps has-muted-color has-text-color has-small-font-size"><?php esc_html_e( 'Our publishers', 'odn' ); ?></h2>
	<!-- /wp:heading -->

	<!-- wp:odn/publisher-directory {"align":"wide"} /-->
</div>
<!-- /wp:group -->
