<?php
/**
 * Title: Comments
 * Slug: odn/comments
 * Inserter: no
 * Block Types: core/comments
 * Description: Comments list, pagination and form.
 *
 * @package ODN
 */

?>
<!-- wp:comments {"style":{"spacing":{"margin":{"top":"var:preset|spacing|50"}}}} -->
<div class="wp-block-comments" style="margin-top:var(--wp--preset--spacing--50)">
	<!-- wp:comments-title {"level":2,"fontSize":"large"} /-->

	<!-- wp:comment-template -->
		<!-- wp:group {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|40"}}},"layout":{"type":"flex","flexWrap":"nowrap","verticalAlignment":"top"}} -->
		<div class="wp-block-group" style="margin-bottom:var(--wp--preset--spacing--40)">
			<!-- wp:avatar {"size":44} /-->
			<!-- wp:group {"style":{"spacing":{"blockGap":"0.25rem"}}} -->
			<div class="wp-block-group">
				<!-- wp:comment-author-name {"fontSize":"small"} /-->
				<!-- wp:comment-date {"fontSize":"small"} /-->
				<!-- wp:comment-content /-->
				<!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap"}} -->
				<div class="wp-block-group">
					<!-- wp:comment-edit-link {"fontSize":"small"} /-->
					<!-- wp:comment-reply-link {"fontSize":"small"} /-->
				</div>
				<!-- /wp:group -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:group -->
	<!-- /wp:comment-template -->

	<!-- wp:comments-pagination {"layout":{"type":"flex","justifyContent":"space-between"}} -->
		<!-- wp:comments-pagination-previous /-->
		<!-- wp:comments-pagination-next /-->
	<!-- /wp:comments-pagination -->

	<!-- wp:post-comments-form /-->
</div>
<!-- /wp:comments -->
