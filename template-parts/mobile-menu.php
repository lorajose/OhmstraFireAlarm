<?php
/*
 * Mobile Menu Template
 */
?>
<div class="mobile-menu-floating">
	<a href="<?php echo esc_url( home_url() ); ?>" class="mobile-menu-toggle"><i class="close-icon"></i></a>

	<?php
	do_action( 'firefront_mobile_menu_before' );
	$mobilebar_items = Firefront_Wp_Elements::firefront_options('mobilebar-menu-items');
	$mobilebar_items = isset( $mobilebar_items['enabled'] ) ? $mobilebar_items['enabled'] : ''; 
	$mkeys = array(
		'chk' => 'mobile-bar-chk',
		'fields' => array(
			'mobilebar-menu-items' => 'mobilebar-menu-items',
			'mobile_menu_custom_text_1' => 'mobile-menu-custom-text-1',
			'mobile_menu_custom_text_2' => 'mobile-menu-custom-text-2'
		)			
	);
	
	$mobile_menu_bar_values = Firefront_Wp_Elements::firefront_get_meta_and_option_values( $mkeys );
	if( !empty( $mobilebar_items ) && is_array( $mobilebar_items ) ):	
		foreach( $mobilebar_items as $element => $value ){
			switch($element){ 

				case "logo": ?>
				<div class="header-titles">
					<?php
						// Site title or logo.
						Firefront_Wp_Framework::firefront_mobile_logo( array(), 'div' );
					?>
				</div><!-- .header-titles --> <?php
				break;
				case "menu":
					$menu_name = '';
					$page_option = get_post_meta(get_the_ID(), 'firefront_post_meta', true);
					if (isset($page_option['header-one-page-menu']) && $page_option['header-one-page-menu'] != 'none') {
						$menu_name = $page_option['header-one-page-menu'];
					}
					if (has_nav_menu('mobile') || !empty($menu_name)) { ?>
						<nav class="mobile-menu-wrapper">
							<ul class="wp-menu mobile-menu">
								<?php
									wp_nav_menu(array(
										'container'      => false,
										'items_wrap'     => '%3$s',
										'theme_location' => 'mobile', // Always include theme_location
										'menu'           => !empty($menu_name) ? $menu_name : '',
										'fallback_cb'    => false,
									));
								?>
							</ul>
						</nav><!-- .mobile-menu-wrapper -->
					<?php }
					break;
				case "search":
					echo get_search_form();
					break;
				

				case "social": 
					if( class_exists( 'Firefront_Custom_Functions' ) ):
				?>
					<div class="mobile-menu-social-wrap">
						<?php
							// Mobile menu social links.
							Firefront_Custom_Functions::firefront_social_links();
						?>
					</div>
				<?php
					endif;
				break;
				case "mobile-menu-custom-text-1":					
					if( $mobile_menu_bar_values['mobile_menu_custom_text_1'] )
					echo '<div class="custom-text-1">'. do_shortcode( stripslashes( force_balance_tags( wp_kses_post( get_option( 'firefront_options' )['mobile-menu-custom-text-1'] ) ) ) ) .'</div>';
				break;
				case "mobile-menu-custom-text-2":
					if( $mobile_menu_bar_values['mobile_menu_custom_text_2'] )
					echo '<div class="custom-text-2">'. do_shortcode( stripslashes( force_balance_tags( wp_kses_post( get_option( 'firefront_options' )['mobile-menu-custom-text-2'] ) ) ) ) .'</div>';
				break;

			} //switch	
		} //foreach
	endif; 	
	do_action( 'firefront_mobile_menu_after' ); 
	?>

</div><!-- .mobile-menu-floating -->