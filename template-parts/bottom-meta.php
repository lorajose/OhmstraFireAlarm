<?php
/**
 * Bottom Meta
 */

$bottom_meta_opt = Firefront_Wp_Elements::firefront_options('single-bottom-meta-enable');
if( is_singular( 'post' ) && $bottom_meta_opt ):
	Firefront_Wp_Elements::firefront_get_post_meta( Firefront_Wp_Elements::$template, 'bottom' );
endif;

$blog_meta_opt = Firefront_Wp_Elements::firefront_options('blog-bottom-meta-enable');
if( !is_archive() && !is_singular() && $blog_meta_opt ):
	Firefront_Wp_Elements::firefront_get_post_meta( Firefront_Wp_Elements::$template, 'bottom' );
endif;

$archive_bottom_meta_opt = Firefront_Wp_Elements::firefront_options('archive-bottom-meta-enable');
if( is_archive() && $archive_bottom_meta_opt):
	Firefront_Wp_Elements::firefront_get_post_meta( Firefront_Wp_Elements::$template, 'bottom' );
endif;