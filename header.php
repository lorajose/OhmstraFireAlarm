<?php
/**
 * Header file for the Firefront WordPress theme.
 */

?><!DOCTYPE html>

<html class="no-js" <?php language_attributes(); ?>>

	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1.0" >
		<link rel="profile" href="https://gmpg.org/xfn/11">
		<?php wp_head(); ?>
	</head>

	<body <?php body_class(); ?>>

		<?php wp_body_open(); // For wp wp_body_open action hook ?>

		<?php
			/*
			* Set firefront page meta
			*/
			if( is_singular() ){
				Firefront_Wp_Elements::$firefront_page_options = get_post_meta( get_the_ID(), 'firefront_post_meta', true );
			}
			$keys = array(
				'chk' => 'general-chk',
				'fields' => array(
					'site_layout' => 'site-layout'
				)			
			);
			$layout = Firefront_Wp_Elements::firefront_get_meta_and_option_values( $keys );
			$pageloader_opt = Firefront_Wp_Elements::firefront_options('page-loader-option');
		?>

		<div class="firefront-body-inner<?php if( $layout['site_layout'] == 'boxed' ) echo esc_attr( ' container' ); ?>">
		
			<?php if( $pageloader_opt == '1' ) : ?>
			<div class="page-loader"><span class="page-loader-divider"></span></div>
			<?php endif; ?>	

			<?php
			/*
			 * Firefront Header Before Action 
			 * 10 - firefront_mobile_header
			 */
			do_action( 'firefront_header_before' );
			?>
			
			<?php
			/*
			 * Firefront Header Action 
			 * 10 - firefront_desktop_header
			 */
			do_action( 'firefront_header' );
			?>
			
			<?php
			/*
			 * Firefront Header After Action 
			 * 10 - firefront_header_slider
			 */
			do_action( 'firefront_header_after' );
			?>