<?php
/**
 * Title: Join the network
 * Slug: odn/page-join
 * Categories: odn
 * Description: Join page for the network's main site, with the application form.
 *
 * @package ODN
 */

?>
<!-- wp:paragraph {"className":"is-style-wide-caps","textColor":"muted","fontSize":"small"} -->
<p class="is-style-wide-caps has-muted-color has-text-color has-small-font-size"><?php esc_html_e( 'Join', 'odn' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1} -->
<h1 class="wp-block-heading"><?php esc_html_e( 'Open a desk on the network', 'odn' ); ?></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"fontSize":"large"} -->
<p class="has-large-font-size"><?php esc_html_e( 'We’re looking for independent journalists who want a place to publish under their own name, with the support of a network behind them.', 'odn' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:list -->
<ul class="wp-block-list">
	<!-- wp:list-item -->
	<li><?php esc_html_e( 'Your own site at yourname.opendesknetwork.com', 'odn' ); ?></li>
	<!-- /wp:list-item -->
	<!-- wp:list-item -->
	<li><?php esc_html_e( 'A design you can make your own: layouts, colours, header images and custom CSS', 'odn' ); ?></li>
	<!-- /wp:list-item -->
	<!-- wp:list-item -->
	<li><?php esc_html_e( 'Hosting, security, backups and support handled for you', 'odn' ); ?></li>
	<!-- /wp:list-item -->
	<!-- wp:list-item -->
	<li><?php esc_html_e( 'A spot on the network front page alongside the other publishers', 'odn' ); ?></li>
	<!-- /wp:list-item -->
</ul>
<!-- /wp:list -->

<!-- wp:paragraph -->
<p><?php esc_html_e( 'Tell us a little about yourself and your reporting. We read every application and reply by email.', 'odn' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:odn/join-form {"style":{"spacing":{"margin":{"top":"var:preset|spacing|40"}}}} /-->
