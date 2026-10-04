<?php
/**
 * Title: About the network
 * Slug: odn/page-about
 * Categories: odn, about
 * Description: About page for the network's main site.
 *
 * @package ODN
 */

?>
<!-- wp:paragraph {"className":"is-style-wide-caps","textColor":"muted","fontSize":"small"} -->
<p class="is-style-wide-caps has-muted-color has-text-color has-small-font-size"><?php esc_html_e( 'About', 'odn' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1} -->
<h1 class="wp-block-heading"><?php esc_html_e( 'Independent journalists, sharing one desk', 'odn' ); ?></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"fontSize":"large"} -->
<p class="has-large-font-size"><?php esc_html_e( 'The Open Desk Network is a home for independent reporters. Each journalist runs their own site, under their own name, with full control of what they publish. The network handles the rest: hosting, security, design and a shared front door for readers.', 'odn' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:separator {"backgroundColor":"accent","className":"is-style-thick"} -->
<hr class="wp-block-separator has-text-color has-accent-color has-alpha-channel-opacity has-accent-background-color has-background is-style-thick"/>
<!-- /wp:separator -->

<!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|40"}}}} -->
<div class="wp-block-columns alignwide">
	<!-- wp:column -->
	<div class="wp-block-column">
		<!-- wp:heading {"level":3} -->
		<h3 class="wp-block-heading"><?php esc_html_e( 'Your desk, your name', 'odn' ); ?></h3>
		<!-- /wp:heading -->
		<!-- wp:paragraph -->
		<p><?php esc_html_e( 'Every journalist gets a site at their own subdomain. You decide what to cover, how it looks and when it goes out. Your reporting stays yours.', 'odn' ); ?></p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:column -->

	<!-- wp:column -->
	<div class="wp-block-column">
		<!-- wp:heading {"level":3} -->
		<h3 class="wp-block-heading"><?php esc_html_e( 'Shared infrastructure', 'odn' ); ?></h3>
		<!-- /wp:heading -->
		<!-- wp:paragraph -->
		<p><?php esc_html_e( 'Fast, secure hosting, backups, a clean design you can make your own, and someone to call when something breaks. Less time on the plumbing, more time reporting.', 'odn' ); ?></p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:column -->

	<!-- wp:column -->
	<div class="wp-block-column">
		<!-- wp:heading {"level":3} -->
		<h3 class="wp-block-heading"><?php esc_html_e( 'Stronger together', 'odn' ); ?></h3>
		<!-- /wp:heading -->
		<!-- wp:paragraph -->
		<p><?php esc_html_e( 'Readers who find one desk can find the rest. The network front page puts every publisher’s latest work in one place.', 'odn' ); ?></p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:column -->
</div>
<!-- /wp:columns -->

<!-- wp:heading -->
<h2 class="wp-block-heading"><?php esc_html_e( 'Our standards', 'odn' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php esc_html_e( 'Publishers on the network commit to accuracy, transparency about sources and funding, and correcting mistakes promptly and in public. Each desk is editorially independent; the network does not assign, edit or approve stories.', 'odn' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading"><?php esc_html_e( 'Who runs it', 'odn' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php esc_html_e( 'The Open Desk Network is run by a small team of journalists and technologists. Questions, tips or corrections can go to the network directly or to any publisher through their own site.', 'odn' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40","right":"var:preset|spacing|40"},"margin":{"top":"var:preset|spacing|50"}}},"backgroundColor":"accent","textColor":"on-accent","layout":{"type":"constrained"}} -->
<div class="wp-block-group has-on-accent-color has-accent-background-color has-text-color has-background" style="margin-top:var(--wp--preset--spacing--50);padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)">
	<!-- wp:heading {"level":3} -->
	<h3 class="wp-block-heading"><?php esc_html_e( 'Want a desk of your own?', 'odn' ); ?></h3>
	<!-- /wp:heading -->

	<!-- wp:buttons -->
	<div class="wp-block-buttons">
		<!-- wp:button -->
		<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/join/' ) ); ?>"><?php esc_html_e( 'Apply to join', 'odn' ); ?></a></div>
		<!-- /wp:button -->
	</div>
	<!-- /wp:buttons -->
</div>
<!-- /wp:group -->
