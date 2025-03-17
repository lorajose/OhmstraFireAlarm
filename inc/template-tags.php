<?php
/**
 * Custom template tags for this theme.
 */

/**
 * Comments
 */
function firefront_is_comment_by_post_author( $comment = null ) {
	if ( is_object( $comment ) && $comment->user_id > 0 ) {
		$user = get_userdata( $comment->user_id );
		$post = get_post( $comment->comment_post_ID );
		if ( ! empty( $user ) && ! empty( $post ) ) {
			return $comment->user_id === $post->post_author;
		}
	}
	return false;
}

/**
 * Filters the edit post link to add an icon and use the post meta structure.
 */
function firefront_edit_post_link( $link, $post_id, $text ) {
	if ( is_admin() ) {
		return $link;
	}
	$edit_url = get_edit_post_link( $post_id );
	if ( ! $edit_url ) {
		return;
	}
	$text = sprintf(
		wp_kses(
			/* translators: %s: Post title. Only visible to screen readers. */
			__( '%s <span class="screen-reader-text">%s</span>', 'firefront' ),
			array(
				'span' => array(
					'class' => array(),
				),
			)
		),
		esc_html__( 'Edit', 'firefront' ),
		get_the_title( $post_id )
	);
	return '<div class="post-meta-wrapper post-meta-edit-link-wrapper"><ul class="post-meta"><li class="post-edit meta-wrapper"><span class="meta-icon"></span><span class="meta-text"><a href="' . esc_url( $edit_url ) . '">' . $text . '</a></span></li></ul><!-- .post-meta --></div><!-- .post-meta-wrapper -->';
}
add_filter( 'edit_post_link', 'firefront_edit_post_link', 10, 3 );

/**
 * Classes
 */
function firefront_no_js_class() {

	?>
	<script>document.documentElement.className = document.documentElement.className.replace( 'no-js', 'js' );</script>
	<?php

}
add_action( 'wp_head', 'firefront_no_js_class' );