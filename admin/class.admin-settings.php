<?php 

class Firefront_Admin_Class {
	
	private static $_instance = null;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'firefront_admin_menu' ) );		
		add_action( 'admin_menu', array( $this, 'change_admin_menu_name' ), 99 );
		add_action( 'admin_enqueue_scripts', array( $this, 'firefront_framework_admin_scripts' ), 10 );
		
		//Call plugin page
		$this->firefront_plugin_menu_connect();
	}
	
	public static function firefront_framework_admin_scripts(){
		if( isset( $_GET['page'] ) && ( $_GET['page'] == 'firefront-welcome' || $_GET['page'] == 'firefront-options' || $_GET['page'] == 'firefront-sidebars' || $_GET['page'] == 'firefront-fonts' || $_GET['page'] == 'firefront-plugins' || $_GET['page'] == 'firefront-importer' || $_GET['page'] == 'firefront-verification' ) ){
			wp_enqueue_style( 'firefront-admin', get_template_directory_uri() . '/admin/assets/css/firefront-admin-page.css', array(), '1.0', 'all' );
		}
		if( isset( $_GET['page'] ) && $_GET['page'] == 'firefront-welcome' ) {
			wp_enqueue_style( 'owl-carousel', get_template_directory_uri() . '/assets/css/owl-carousel.min.css', array(), '2.3.4', 'all' );
			wp_enqueue_script( 'owl-carousel', get_template_directory_uri() . '/assets/js/owl.carousel.min.js', array( 'jquery' ), '2.3.4', true );
		}
		wp_enqueue_style( 'firefront-admin-common', get_template_directory_uri() . '/admin/assets/css/firefront-admin-common.css', array(), '1.0', 'all' );	
		wp_enqueue_script( 'firefront-admin-js', esc_url( get_template_directory_uri() . '/admin/assets/js/firefront-admin-script.js' ), array( 'jquery' ), '1.0' );
		
		if( isset( $_GET['page'] ) && $_GET['page'] == 'firefront-plugins' ){
			require_once FIREFRONT_DIR . '/admin/theme-plugins/tgm-init.php';			
			$plugins = TGM_Plugin_Activation::$instance->plugins;
			$args = array( 'tgm_plugins' => $plugins );
			$admin_local_args = apply_filters( 'firefront_admin_local_js_args', $args );
			wp_localize_script('firefront-admin-js', 'firefront_admin_ajax_var', $admin_local_args );
		}
		
		if( isset( $_GET['page'] ) && $_GET['page'] == 'firefront-verification' ){
			$html = '<p><strong>This purchase code already registered with another domain</strong></p><p>Please go to your previous working environment and deactivate the purchase code to use it again ( WP dashboard -> Firefront -> Token Verification -> click on the button "Deactivate" ).</p>';
			$args = array( 'already_used' => $html );
			$admin_local_args = apply_filters( 'firefront_admin_local_js_args', $args );
			wp_localize_script('firefront-admin-js', 'firefront_admin_ajax_var', $admin_local_args );
		}
	}
	
	public static function firefront_admin_menu(){
		add_menu_page( 
			esc_html__( 'Firefront', 'firefront' ),
			esc_html__( 'Firefront', 'firefront' ),
			'manage_options',
			'firefront-welcome', 
			array( 'Firefront_Admin_Class', 'firefront_admin_page' ),
			get_template_directory_uri() . '/assets/images/brand-icon.png',
			6
		);
		add_submenu_page( 
			'firefront-welcome', 
			esc_html__( 'Token Verification', 'firefront' ),
			esc_html__( 'Token Verification', 'firefront' ), 
			'manage_options', 
			'firefront-verification', 
			array( 'Firefront_Admin_Class', 'firefront_verification_admin_page' )
		);
	}
	
	public static function change_admin_menu_name(){
		global $submenu;
		if(isset($submenu['firefront-welcome'])){
			$submenu['firefront-welcome'][0][0] = esc_html__( 'Welcome', 'firefront' );
		}
	}
	
	public static function firefront_admin_page(){
	
		$firefront_theme = wp_get_theme();
		
		?>
		<div class="firefront-settings-wrap">
			<div class="firefront-header-bar">
				<div class="firefront-header-left">
					<div class="firefront-admin-logo-inline">
						<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/brand-logo.png' ); ?>" alt="firefront-logo">
					</div><!-- .firefront-admin-logo-inline -->
					<h2 class="title"><?php esc_html_e( 'Firefront', 'firefront' ); ?><span class="firefront-version"><?php echo esc_html( $firefront_theme->get( 'Version' ) ); ?></span></h2>
				</div><!-- .firefront-header-left -->
				<div class="firefront-header-right">
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=firefront-verification' ) ) ?>" class="button firefront-btn"><?php esc_html_e( 'Verify Token', 'firefront' ); ?></a>
				</div><!-- .firefront-header-right -->
			</div><!-- .firefront-header-bar -->
			
			<div class="firefront-settings-tabs">
				<div id="firefront-general" class="firefront-settings-tab firefront-elements-list active">
					<div class="container">
						<div class="row">
							<div class="col-8">
								<div class="row">
									<div class="col-6 mb-4">
										<div class="banner-img-wrap">
											<img class="firefront-preview-img img-fluid" src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/banner.png' ); ?>" alt="essential-addons-for-elementor-featured">
										</div>
									</div><!-- .col -->
									<div class="col-6 mb-4">
										<div class="media admin-box">
											<div class="admin-box-icon mr-3">
												<span class="dashicons dashicons-admin-generic"></span>								
											</div>
											<div class="media-body admin-box-info">
												<h3 class="admin-box-title"><?php esc_html_e( 'Requirements', 'firefront' ); ?></h3>
												<div class="admin-box-content">
												<?php
													$php_version = phpversion();
													$php_version_class = version_compare( $php_version, '8.0', '>=') ? ' success' : ' warning';
													$wp_version = get_bloginfo('version');
													$wp_version_class = version_compare( $wp_version, '6.0', '>=') ? ' success' : ' warning';
													
													ob_start();
													phpinfo(INFO_MODULES);
													$info = ob_get_contents();
													ob_end_clean();
													$info = stristr($info, 'Client API version');
													preg_match('/[1-9].[0-9].[1-9][0-9]/', $info, $match);
													$mysql_version = $match[0]; 
													$mysql_version_class = version_compare( $mysql_version, '8', '>=') ? ' success' : ' warning';
													
													$post_max_size = ini_get('post_max_size');
													$post_max = str_replace("M","",$post_max_size);
													$post_max_class = $post_max >= 10 ? ' success' : ' warning';
													
													$max_execution_time = ini_get('max_execution_time');
													$max_exe_class = $max_execution_time >= 300 ? ' success' : ' warning';
													
													$max_input_vars = ini_get('max_input_vars');
													$max_input_class = $max_input_vars >= 2000 ? ' success' : ' warning';
													
												?>
													<table class="firefront-admin-table no-spacing-table">
														<thead>
															<tr>
																<td><?php esc_html_e( 'Core', 'firefront' ); ?></td>
																<td><?php esc_html_e( 'Required', 'firefront' ); ?></td>
																<td><?php esc_html_e( 'Current', 'firefront' ); ?></td>
																<td><?php esc_html_e( 'Status', 'firefront' ); ?></td>
															</tr>
														</thead>
														<tbody>
															<tr>
																<td><?php esc_html_e( 'PHP', 'firefront' ); ?></td>
																<td>8.0</td>
																<td><?php echo esc_html( $php_version ); ?></td>
																<td class="text-center"><span class="requirement-icon <?php echo esc_attr( $php_version_class ); ?>"></span></td>
															</tr>
															<tr>
																<td><?php esc_html_e( 'MySQL', 'firefront' ); ?></td>
																<td>8.0</td>
																<td><?php echo esc_html( $mysql_version ); ?></td>
																<td class="text-center"><span class="requirement-icon <?php echo esc_attr( $mysql_version_class ); ?>"></span></td>
															</tr>
															<tr>
																<td><?php esc_html_e( 'WordPress', 'firefront' ); ?></td>
																<td>6.0</td>
																<td><?php echo esc_html( $wp_version ); ?></td>
																<td class="text-center"><span class="requirement-icon <?php echo esc_attr( $wp_version_class ); ?>"></span></td>
															</tr>															
															<tr>
																<td><?php esc_html_e( 'post_max_size', 'firefront' ); ?></td>
																<td>10M</td>
																<td><?php echo esc_html( $post_max_size ); ?></td>
																<td class="text-center"><span class="requirement-icon <?php echo esc_attr( $post_max_class ); ?>"></span></td>
															</tr>
															<tr>
																<td><?php esc_html_e( 'max_input_vars', 'firefront' ); ?></td>
																<td>2000</td>
																<td><?php echo esc_html( $max_input_vars ); ?></td>
																<td class="text-center"><span class="requirement-icon <?php echo esc_attr( $max_input_class ); ?>"></span></td>
															</tr>
															<tr>
																<td><?php esc_html_e( 'max_execution_time', 'firefront' ); ?></td>
																<td>300</td>
																<td><?php echo esc_html( $max_execution_time ); ?></td>
																<td class="text-center"><span class="requirement-icon <?php echo esc_attr( $max_exe_class ); ?>"></span></td>
															</tr>
														</tbody>
													</table>
												</div>
											</div>
										</div>
									</div><!-- .col -->
									<div class="col-6 mb-4">
										<div class="media admin-box">
											<div class="admin-box-icon mr-3">
												<span class="dashicons dashicons-media-document"></span>								
											</div>
											<div class="media-body admin-box-info">
												<h3 class="admin-box-title"><?php esc_html_e( 'Documention', 'firefront' ); ?></h3>
												<div class="admin-box-content">
													<?php esc_html_e( 'Get started by spending some time with the documentation to get familiar with Firefronts. Build awesome websites for you or your clients with ease.', 'firefront' ); ?>
												</div>
												<a href="<?php echo esc_url( __( 'https://docs.zozothemes.com/firefront/', 'firefront' ) );?> " class="firefront-btn btn-default" target="__blank"><?php esc_html_e( 'Go Here', 'firefront' ); ?></a>
											</div>
										</div>
									</div><!-- .col -->
									<div class="col-6">
										<div class="media admin-box">
											<div class="admin-box-icon mr-3">
												<span class="dashicons dashicons-admin-users"></span>								
											</div>
											<div class="media-body admin-box-info">
												<h3 class="admin-box-title"><?php esc_html_e( 'Need Help?', 'firefront' ); ?></h3>
												<div class="admin-box-content">
													<?php esc_html_e( 'Stuck with something? Get help from the community on WordPress.org Forum initiate a live chat at Firefronts website and get support.', 'firefront' ); ?>
												</div>
												<a href="<?php echo esc_url( __( 'https://zozothemes.ticksy.com/', 'firefront' ) );?>" class="firefront-btn btn-default" target="__blank"><?php esc_html_e( 'Get Support', 'firefront' ); ?></a>
											</div>
										</div>
									</div><!-- .col -->
									<div class="col-6 mb-4">
									    <div class="media admin-box">
									        <div class="admin-box-icon mr-3">
									            <span class="dashicons dashicons-video-alt3"></span>
									        </div>
									        <div class="media-body admin-box-info">
									            <h3 class="admin-box-title"><?php esc_html_e('Video Tutorials?', 'firefront'); ?></h3>
									            <div class="admin-box-content">
									                <?php esc_html_e('Get started by spending some time with the Video tutorials to get familiar with Firefront. Here is a full video tutorial to how to setup a theme.', 'firefront'); ?>
									            </div>
									            <a href="<?php echo esc_url( __('https://www.youtube.com/watch?v=Snlj8-ASlyk', 'firefront' ));?>" class="firefront-btn btn-default" target="__blank"><?php esc_html_e('Click Here', 'firefront'); ?></a>
									        </div>
									    </div>
									</div>

									<div class="col-12">								
										<div class="admin-box-slide-wrap text-center">	
											<?php										
												//Banner
											?>
										</div>
									</div><!-- .col -->
								</div><!-- .row -->
							</div><!-- .col -->
							<div class="col-4">
							<?php
								if( !class_exists( 'Zozothemes_API' ) ){
									require_once FIREFRONT_DIR . '/admin/class.zozo-api.php';
								}
								$zozo_api = new Zozothemes_API;
								$response = $zozo_api->get_response();
							?>
								<div class="admin-box">
									<div class="admin-box-info">
										<h3 class="admin-box-title"><?php esc_html_e( 'Live Updates', 'firefront' ); ?></h3>
										<div class="admin-box-pro text-center">
											
										</div>									
											<div class="full-logo-wrap"><img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/brand.png' ); ?>" alt="firefront-logo"></div>
										
										<h3 class="admin-box-title my-4"><?php esc_html_e( 'Featured Themes', 'firefront' ); ?></h3>
										<div class="admin-box-slide-wrap">
										<?php	
										if( !is_wp_error( $response ) ){										
											if( !empty( $response ) && isset( $response['products'] ) ) {
												echo '<div class="owl-carousel">';
												foreach( $response['products'] as $key => $product ){
													echo '<a href="'. esc_url( $product['link'] ) .'" target="_blank"><img src="'. esc_url( $product['img'] ) .'" alt="'. esc_url( $product['alt'] ) .'"></a>';
												}
												echo '</div>';
											}
										}else{ ?>
											<p><?php esc_html_e( 'Featured products will show here..', 'firefront' ); ?></p>
										<?php
										}
										?>
										</div>
									</div>
								</div>
							</div>
						</div><!-- .row -->
					</div><!-- .container -->
				</div><!-- .firefront-settings-tab -->
			</div><!-- .firefront-settings-tabs -->
			
		</div><!-- .firefront-settings-wrap -->
		<?php
	}

	public static function firefront_verification_admin_page(){		
	
		$firefront_theme = wp_get_theme();		
	?>
		<div class="firefront-settings-wrap">
		
			<div class="firefront-header-bar">
				<div class="firefront-header-left">
					<div class="firefront-admin-logo-inline">
						<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/brand-logo.png' ); ?>" alt="firefront-logo">
					</div><!-- .firefront-admin-logo-inline -->
					<h2 class="title"><?php esc_html_e( 'Purchase Code Verification', 'firefront' ); ?><span class="firefront-version"><?php echo esc_html( $firefront_theme->get( 'Version' ) ); ?></span></h2>
				</div><!-- .firefront-header-left -->
				<div class="firefront-header-right">
					<a href="<?php echo esc_url( 'https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-' ); ?>" class="button firefront-btn"><?php esc_html_e( 'Get Purchase Code', 'firefront' ); ?></a>
				</div><!-- .firefront-header-right -->
			</div><!-- .firefront-header-bar -->
			
			<div class="firefront-inner-wrap">
				<div class="firefront-settings-tabs">
					<div id="firefront-general" class="firefront-settings-tab firefront-elements-list active">
						<div class="container">
							<?php 
								$verfied_stat = get_option('verified_purchase_status');
							?>
							<div class="zozo-envato-registration-form-wrap">
								<?php if( !$verfied_stat ): ?>
								<h2 class="text-center"><?php esc_html_e( "Activate your Licence", "firefront" ); ?></h2>
								<p class="text-center"><?php esc_html_e( "Welcome and thank you for Choosing Firefront Theme!
The Firefront theme needs to be activated to enable demo import installation and customer support service.", "firefront" ); ?></p>	
								<a href="<?php echo esc_url( 'https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-' ); ?>" target="_blank"><?php esc_html_e( "How to find purchase code?", "firefront" ); ?></a>
								<form id="zozo-envato-registration-form" class="zozo-envato-registration-form" method="post">
									<?php wp_nonce_field( 'firefront_theme_verify^%&^%', 'zozo_verify_nonce' ); ?>
									<div class="form-fields">
										<div class="zozo-input-group">
											<input type="text" name="zozo_registration_email" value="" placeholder="<?php esc_attr_e( 'Enter E-mail address', 'firefront' ); ?>">
											<input type="text" name="zozo_purchase_code" value="" placeholder="<?php esc_attr_e( 'Enter your theme purchase code', 'firefront' ); ?>">
										</div>
										<div class="submit-group">
											<input type="submit" name="submit" id="submit" class="button firefront-btn" value="<?php esc_attr_e( 'Activate', 'firefront' ); ?>" />
											<span class="process-loader"><img src="<?php echo esc_url( FIREFRONT_URI . '/admin/assets/images/loader.gif' ); ?>" alt="<?php esc_attr_e( 'Loader', 'firefront' ) ?>" /></span>
										</div>
									</div>	

									<div class="verfication-alert text-center"><span class="verfication-txt"></span></div>
									
								</form>
								<?php else: ?>
								<div class="theme-activated-wrap text-center">
									<h2><?php esc_html_e( 'Thank you!', 'firefront' ) ?></h2>
									<p><strong><?php esc_html_e( 'Your theme\'s license is activated successfully.', 'firefront' ) ?></strong></p>
								</div>
								<form id="zozo-envato-deactivation-form" class="zozo-envato-deactivation-form text-center" method="post">
									<?php wp_nonce_field( 'firefront_theme_deactivate^%&^%', 'zozo_deactivate_nonce' ); ?>
									<div class="submit-group">
										<input type="submit" name="submit" class="button firefront-btn" value="<?php esc_attr_e( 'Deactivate', 'firefront' ); ?>" />
										<span class="process-loader"><img src="<?php echo esc_url( FIREFRONT_URI . '/admin/assets/images/loader.gif' ); ?>" alt="<?php esc_attr_e( 'Loader', 'firefront' ) ?>" /></span>
									</div>
								</form>
								<?php endif; ?>
								
								<div class="registration-token-instruction">
									<p class="text-center"><strong><?php esc_html_e( '1 license = 1 domain = 1 website', 'firefront' ); ?></strong></p>
									<p class="text-center"><?php printf( '%1$s <a href="%2$s" target="_blank">%3$s</a>',
										esc_html__( 'You can always buy more licences for this product:', 'firefront' ),
										esc_url( 'https://themeforest.net/user/zozothemes/portfolio' ),
										esc_html__( 'ThemeForest ZOZOTHEMES', 'firefront' )
										); ?>
									</p>
									<p class="text-left notice-wrap"><span class="info-notice"><?php esc_html_e( 'Notice', 'firefront' ); ?></span><?php esc_html_e( 'If you are developing a website in the staging site means, While moving to the production site, please deactivate the license in staging and activate it in the Production site.', 'firefront' ); ?></p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php
	}
	
	public static function firefront_plugin_menu_connect(){
		require_once FIREFRONT_DIR . '/admin/class.token-verification.php';
		$verfied_stat = Zozo_Purchase_Code_Verification::check_theme_activated();
		if( !empty( $verfied_stat ) && !is_array( $verfied_stat ) ) {
			require_once FIREFRONT_DIR . '/admin/class.plugin-settings.php';	
		}
	}
	
	public static function firefront_theme_verification(){
		
		$nonce = isset( $_POST['zozo_verify_nonce'] ) ? sanitize_text_field( $_POST['zozo_verify_nonce'] ) : '';	  
		if ( ! wp_verify_nonce( $nonce, 'firefront_theme_verify^%&^%' ) )
			wp_die ( esc_html__( 'Busted', 'firefront' ) );
		
		if( isset( $_POST['zozo_registration_email'] ) && !empty( $_POST['zozo_purchase_code'] ) ){
			require_once FIREFRONT_DIR . '/admin/class.token-verification.php';
			$verfy_obj = new Zozo_Purchase_Code_Verification;
			$status = $verfy_obj->verify_token();
			wp_send_json($status);
		}
		
		wp_die('finished');
	}
	
	public static function firefront_theme_deactivate(){
			
		$nonce = isset( $_POST['zozo_deactivate_nonce'] ) ? sanitize_text_field( $_POST['zozo_deactivate_nonce'] ) : '';	  
		if ( ! wp_verify_nonce( $nonce, 'firefront_theme_deactivate^%&^%' ) )
			wp_die ( esc_html__( 'Busted', 'firefront' ) );
				
		require_once FIREFRONT_DIR . '/admin/class.token-verification.php';
		$verfy_obj = new Zozo_Purchase_Code_Verification;
		$status = $verfy_obj->deactivate_api_call();
		wp_send_json($status);
		
		wp_die('finished');
	}
	
	public static function get_instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

} Firefront_Admin_Class::get_instance();

//Theme verification ajax functions
add_action( 'wp_ajax_firefront_theme_verify', array( 'Firefront_Admin_Class', 'firefront_theme_verification' ) );
add_action( 'wp_ajax_nopriv_firefront_theme_verify', array( 'Firefront_Admin_Class', 'firefront_theme_verification' )  );

//Theme deactivate
add_action( 'wp_ajax_firefront_theme_deactivate', array( 'Firefront_Admin_Class', 'firefront_theme_deactivate' ) );
add_action( 'wp_ajax_nopriv_firefront_theme_deactivate', array( 'Firefront_Admin_Class', 'firefront_theme_deactivate' )  );