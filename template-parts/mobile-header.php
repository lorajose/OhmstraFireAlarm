<?php
	$class_array = array(
		'left'		=> ' element-left',
		'center'	=> ' pull-center justify-content-center',
		'right'		=> ' pull-right justify-content-end'
	);
$mkeys = array(
		'chk' => 'mobile-bar-chk',
		'fields' => array(
			'mobile_header_items' => 'mobilebar-items',
			'mobile_header_text_1' => 'mobile-header-custom-text'
		)			
	);
	$mobilebar_values = Firefront_Wp_Elements::firefront_get_meta_and_option_values( $mkeys );
	$mobilebar_items = Firefront_Wp_Elements::firefront_options('mobilebar-items');
	if( !empty( $mobilebar_items ) ):
	
		if( isset( $mobilebar_items['disabled'] ) ) unset( $mobilebar_items['disabled'] );

		$sticky_opt = Firefront_Wp_Elements::firefront_options('mobilebar-sticky');
		if( $sticky_opt != 'off' ): ?>
		<div class="sticky-outer" data-stickyup="<?php echo esc_attr( $sticky_opt == 'on_scrollup' ? "1" : "0" ); ?>"><div class="sticky-head">
		<?php endif; ?>
		<div class="header-mobilebar navbar">
			<div class="container">
				<?php 
					foreach( $mobilebar_items as $key => $value ){
						$mobilebar_class = $class_array[$key];
						$mobilebar_class .= isset( $mobilebar_items['right'] ) && !empty( $mobilebar_items['right'] ) ? ' right-element-exist' : '';
						
						echo '<ul class="nav mobilebar'. esc_attr( $mobilebar_class ) .'">'; 
						foreach( $value as $element => $label ){
							switch($element){
								case "logo": ?>
									<li class="header-titles-wrapper">
										<div class="header-titles">
											<?php
												// Site title or logo.
												Firefront_Wp_Framework::firefront_mobile_logo();
											?>
										</div><!-- .header-titles -->
									</li><!-- .header-titles-wrapper -->
								<?php
								break;
								case "menu-toggle": ?>
									<li class="header-mobile-toggle-wrapper">
										<a href="<?php echo esc_url( home_url() ); ?>" class="mobile-menu-toggle"><i class="bi bi-list"></i></a>
										<?php add_action( 'firefront_footer_after', function(){ get_template_part( 'template-parts/mobile', 'menu' ); }, 20 ); ?>
									</li><!-- .header-navigation-wrapper -->
								<?php
								break;
								case "search": ?>
									<li class="header-search-wrapper">
										<?php Firefront_Wp_Framework::firefront_search_modal( '1', 'mobile_bar' ); ?>
									</li>
								<?php
								break;
								case "mobile-header-custom-text": ?>
									<li class="mobile-cutom-text">
										<?php 
												echo do_shortcode( stripslashes( force_balance_tags( wp_kses_post( get_option( 'firefront_options' )['mobile-header-custom-text'] ) ) ) ); 
										?>
									</li>
								<?php
								break;	
							}
						}
						echo '</ul>';
					}
				?>
			</div><!-- .container -->
		</div><!-- .header-mobilebar --> <?php 
	if( $sticky_opt != 'off' ): ?>
	</div> <!-- .sticky-head --></div> <!-- .sticky-outer -->
	<?php endif; ?>	
<?php endif; ?>