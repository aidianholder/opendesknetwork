<?php
/**
 * Title: About the journalist
 * Slug: odn/about-publisher
 * Categories: odn, about
 * Description: Photo, short bio and contact button for a publisher's About page.
 *
 * @package ODN
 */

?>
<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50"}}},"layout":{"type":"constrained","contentSize":"960px"}} -->
<div class="wp-block-group alignwide" style="padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50)">
	<!-- wp:media-text {"mediaWidth":38,"verticalAlignment":"center","className":"is-style-default"} -->
	<div class="wp-block-media-text is-stacked-on-mobile is-vertically-aligned-center is-style-default" style="grid-template-columns:38% auto"><figure class="wp-block-media-text__media"></figure><div class="wp-block-media-text__content">
		<!-- wp:paragraph {"className":"is-style-wide-caps","textColor":"muted","fontSize":"small"} -->
		<p class="is-style-wide-caps has-muted-color has-text-color has-small-font-size"><?php esc_html_e( 'About', 'odn' ); ?></p>
		<!-- /wp:paragraph -->

		<!-- wp:heading {"level":1} -->
		<h1 class="wp-block-heading"><?php esc_html_e( 'Your name', 'odn' ); ?></h1>
		<!-- /wp:heading -->

		<!-- wp:paragraph -->
		<p><?php esc_html_e( 'A few sentences about your beat, your background, and what readers can expect from this desk.', 'odn' ); ?></p>
		<!-- /wp:paragraph -->

		<!-- wp:buttons -->
		<div class="wp-block-buttons">
			<!-- wp:button -->
			<div class="wp-block-button"><a class="wp-block-button__link wp-element-button"><?php esc_html_e( 'Get in touch', 'odn' ); ?></a></div>
			<!-- /wp:button -->
		</div>
		<!-- /wp:buttons -->
	</div></div>
	<!-- /wp:media-text -->
</div>
<!-- /wp:group -->
