<?php
/**
 * Title: Header with image
 * Slug: odn/header-image
 * Categories: header
 * Block Types: core/template-part/header
 * Description: Site title and menu over a full-width header image. Select the image and choose Replace to use your own.
 *
 * @package ODN
 */

$odn_header_image = get_theme_file_uri( 'assets/images/header-halftone.jpg' );
?>
<!-- wp:cover {"url":"<?php echo esc_url( $odn_header_image ); ?>","dimRatio":40,"overlayColor":"deep","isUserOverlayColor":true,"minHeight":340,"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|50"}}},"textColor":"base-2","layout":{"type":"constrained"}} -->
<div class="wp-block-cover alignfull has-base-2-color has-text-color" style="padding-top:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--50);min-height:340px"><span aria-hidden="true" class="wp-block-cover__background has-deep-background-color has-background-dim-40 has-background-dim"></span><img class="wp-block-cover__image-background" alt="" src="<?php echo esc_url( $odn_header_image ); ?>" data-object-fit="cover"/>
<div class="wp-block-cover__inner-container">
	<!-- wp:group {"align":"wide","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} -->
	<div class="wp-block-group alignwide">
		<!-- wp:odn/network-badge {"tone":"light","fontSize":"small"} /-->

		<!-- wp:navigation {"overlayBackgroundColor":"deep","overlayTextColor":"on-deep","layout":{"type":"flex","justifyContent":"right"}} /-->
	</div>
	<!-- /wp:group -->

	<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|50"},"blockGap":"var:preset|spacing|20"}},"layout":{"type":"flex","orientation":"vertical"}} -->
	<div class="wp-block-group alignwide" style="padding-top:var(--wp--preset--spacing--50)">
		<!-- wp:site-logo {"width":240,"shouldSyncIcon":false} /-->
		<!-- wp:site-title {"fontSize":"xx-large"} /-->
		<!-- wp:site-tagline {"textColor":"accent"} /-->
	</div>
	<!-- /wp:group -->
</div>
</div>
<!-- /wp:cover -->
