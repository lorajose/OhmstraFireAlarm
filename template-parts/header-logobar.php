<?php 
	$class_array = array(
		'left'		=> ' element-left',
		'center'	=> ' pull-center justify-content-center',
		'right'		=> ' pull-right justify-content-end'
	);
	$header_keys = array(
		'chk' => 'header-chk',
		'fields' => array(
			'header_layout' => 'header-layout'
		)			
	);
	$header_values = Firefront_Wp_Elements::firefront_get_meta_and_option_values( $header_keys );
	$keys = array(
		'chk' => 'header-logobar-chk',
		'fields' => array(
			'header_logobar_items' => 'logobar-items',
			'header_logobar_text_1' => 'logobar-custom-text-1',
			'header_logobar_text_2' => 'logobar-custom-text-2'
		)			
	);
	$logobar_values = Firefront_Wp_Elements::firefront_get_meta_and_option_values( $keys );
	$logobar_items = $logobar_values['header_logobar_items'];
	if( !empty( $logobar_items ) ):	
		if( isset( $logobar_items['disabled'] ) ) unset( $logobar_items['disabled'] );
		
		$layout = $header_values['header_layout'];
		$container_class = $layout == 'wider' ? 'container-fluid' : 'container';
?>
		<div class="header-logobar navbar elements-<?php echo esc_attr( count( $logobar_items ) ); ?>">
			<div class="<?php echo esc_attr( $container_class ); ?>">
				<?php
				foreach( $logobar_items as $key => $value ){
					$logobar_class = $class_array[$key];
					$logobar_class .= isset( $logobar_items['right'] ) && !empty( $logobar_items['right'] ) ? ' right-element-exist' : '';
					echo '<ul class="nav logobar-ul'. esc_attr( $logobar_class ) .'">';
						foreach( $value as $element => $label ){
							switch( $element ){
								case "custom-text-1":
									if( $logobar_values['header_logobar_text_1'] )
									echo '<li>'. do_shortcode( stripslashes( force_balance_tags( wp_kses_post( get_option( 'firefront_options' )['logobar-custom-text-1'] ) ) ) ) .'</li>';
								break;
								case "custom-text-2":
									if( $logobar_values['header_logobar_text_2'] )
									echo '<li>'. do_shortcode( stripslashes( force_balance_tags( wp_kses_post( get_option( 'firefront_options' )['logobar-custom-text-2'] ) ) ) ) .'</li>';
								break;
								case "social":
									if( class_exists( 'Firefront_Custom_Functions' ) ):
										echo '<li>';
										Firefront_Custom_Functions::firefront_social_links();
										echo '</li>';
									endif;
								break;
								case "email":
									echo '<li>';
									Firefront_Wp_Framework::firefront_email_link( Firefront_Wp_Elements::firefront_options('header-email') );
									echo '</li>';
								break;
								case "address":
									echo '<li>';
	       								Firefront_Wp_Framework::firefront_address( get_option( 'firefront_options' )['header-address'] );
									echo '</li>';
								break;
								case "search":
									$keys = array(
										'chk' => 'header-chk',
										'fields' => array(
											'post-type' => 'search-template',
											'search_type' => 'search-type'
										)			
									);
									$search_values = Firefront_Wp_Elements::firefront_get_meta_and_option_values( $keys );
									$search_type = $search_values['search_type'];
									echo '<li>';
									Firefront_Wp_Framework::firefront_search_modal( $search_type, 'logobar' );
									echo '</li>';
								break;
									case "logo": ?>
									<li class="header-titles-wrapper">
										<div class="header-titles">
											<?php
												// Site title or logo.
												Firefront_Wp_Framework::firefront_site_logo();
												// Sticky logo
												Firefront_Wp_Framework::firefront_sticky_logo();
												// Site description.
												Firefront_Wp_Framework::firefront_site_description();
											?>
										</div><!-- .header-titles -->
									</li><!-- .header-titles-wrapper -->
								<?php
								break;
								case "primary-menu": ?>
									<li class="header-navigation-wrapper">
										<?php
											$menu_name = '';
											$page_option = get_post_meta( get_the_ID(), 'firefront_post_meta', true );
											if( isset( $page_option['header-one-page-menu'] ) && $page_option['header-one-page-menu'] != 'none' ) {
												$menu_name = $page_option['header-one-page-menu'];
											}
										?>
										<?php if ( has_nav_menu( 'primary' ) || !empty( $menu_name ) ) { ?>
											<nav class="primary-menu-wrapper" aria-label="<?php esc_attr_e( 'Horizontal', 'firefront' ); ?>">
												<ul class="nav wp-menu primary-menu">
													<?php
														wp_nav_menu(array(
															'container'      => false,
															'items_wrap'     => '%3$s',
															'theme_location' => 'primary',
															'menu'           => !empty($menu_name) ? $menu_name : '',
															'fallback_cb'    => false,
														));
													?>
												</ul>
											</nav><!-- .primary-menu-wrapper -->
										<?php }  else { 
											echo sprintf( 
												'<a href="%1$s">%2$s</a>',
												admin_url( 'nav-menus.php' ),
												esc_html__( 'Add a menu', 'firefront' )					
											); } 
										?>
									</li><!-- .header-navigation-wrapper -->
								<?php
								break;
								case "secondary-bar": ?>
									<li class="secondary-toggle-wrapper">
										<a href="<?php echo esc_url( home_url() ); ?>" class="secondary-menu-toggle firefront-toggle"><span></span><span></span><span></span><span></span></a>
									</li>
									<?php add_action( 'firefront_footer_after', array( 'Firefront_Wp_Elements', 'firefront_secondary_bar' ), 10 ); ?>
								<?php
								break;
							}
						}
					echo '</ul>';
				}
				?>
			</div><!-- .container -->
			<?php
				/*
				 * Firefront Topbar After Action 
				 * 10 - firefront_fullbar_search_form
				 */
				do_action( 'firefront_logobar_after' );
			?>
		</div><!-- .header-logobar -->
<?php endif; ?>