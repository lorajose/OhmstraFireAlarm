<?php 

class Firefront_Plugin_Info {
	
	private static $_parent_instance = null;
	
	public function __construct() {
		require_once FIREFRONT_DIR . '/admin/theme-plugins/tgm-init.php';
	}
		
	public static function firefront_get_plugins(){
		return get_plugins();
	}
		
	public static function firefront_does_plugin_require_update( $file_path ) {
		$repo_updates = get_site_transient( 'update_plugins' );
		$available_version = '';
		if ( isset( $repo_updates->response[ $file_path ]->new_version ) ) {
			$available_version = $repo_updates->response[ $file_path ]->new_version;			
		}
		return $available_version;
	}
		
	public static function firefront_tgm_install(){
		
		if ( ! isset( $_POST['firefront_bulk_nonce'] ) || ! wp_verify_nonce( $_POST['firefront_bulk_nonce'], 'firefront-bulk-plugin-install' ) ) wp_die("failed");
				
		require_once FIREFRONT_DIR . '/admin/theme-plugins/tgm-init.php';			
		$plugins = isset( $_POST['plugins'] ) ? $_POST['plugins'] : TGM_Plugin_Activation::$instance->plugins;
		if( isset( $_POST['firefront_bulk_plugins'] ) ) {
			$bulk_plugins = $_POST['firefront_bulk_plugins'];
			$bulk_action = isset( $_POST['firefront_bulk_action'] ) && ! empty( $_POST['firefront_bulk_action'] ) ? sanitize_text_field( $_POST['firefront_bulk_action'] ) : 'install';
		
			$tgm = new TGM_Plugin_Activation;			
			$tgm->plugins = $plugins;
			if( $bulk_action == 'install' ){
				foreach( $bulk_plugins as $plugin_name ){
					$tgm->firefront_do_plugin_install( $plugin_name );
				}
			}elseif( $bulk_action == 'active' ){
				foreach( $bulk_plugins as $plugin_name ){
					$tgm->firefront_do_plugin_activate( $plugin_name );
				}
			}elseif( $bulk_action == 'install-active' ){
				foreach( $bulk_plugins as $plugin_name ){
					$tgm->firefront_do_plugin_install( $plugin_name, true );
				}
			}elseif( $bulk_action == 'deactive' ){
				foreach( $bulk_plugins as $plugin_name ){
					$tgm->firefront_force_deactivation( $plugin_name );
				}
			}
		}
		wp_die("success");
	}
	
	public static function firefront_plugin_link( $item ) {
		
		include_once ABSPATH . 'wp-admin/includes/plugin.php';

		$installed_plugins = Firefront_Plugin_Info::firefront_get_plugins();
		$item['sanitized_plugin'] = $item['name'];
		 $is_plug_act = 'is_plugin_active';
		 
		if ( $is_plug_act( $item['file_path'] ) ) {
			
			$available_version = $item['source'] == 'repo' ? Firefront_Plugin_Info::firefront_does_plugin_require_update( $item['file_path'] ) : $item['version'];
			
			if ( version_compare( $available_version, $installed_plugins[$item['file_path']]['Version'], '>' ) ) {
				$actions = array(
					'update' => sprintf(
						'<a href="%1$s" class="firefront-btn btn-default" title="%3$s %2$s">%3$s</a>',
						wp_nonce_url(
							add_query_arg(
								array(
									'page'		  	=> urlencode( TGM_Plugin_Activation::$instance->menu ),
									'plugin'		=> urlencode( $item['slug'] ),
									'plugin_name'   => urlencode( $item['sanitized_plugin'] ),
									'plugin_source' => urlencode( $item['source'] ),
									'tgmpa-update' 	=> 'update-plugin',
									'version' 		=> urlencode( $item['version'] ),
									'return_url' 	=> 'firefront-plugins'
								),
								admin_url( TGM_Plugin_Activation::$instance->parent_slug )
							),
							'tgmpa-update',
							'tgmpa-nonce'
						),
						$item['sanitized_plugin'],
						esc_html__( 'Update', 'firefront' )
					),
				);
			}else{
				$actions = array(
					'deactivate' => sprintf(
						'<a href="%1$s" class="firefront-btn btn-default" title="%3$s %2$s">%3$s</a>',
						esc_url( add_query_arg(
							array(
								'plugin'					=> urlencode( $item['slug'] ),
								'plugin_name'		  		=> urlencode( $item['sanitized_plugin'] ),
								'plugin_source'				=> urlencode( $item['source'] ),
								'firefront-deactivate'	   		=> 'deactivate-plugin',
								'firefront-deactivate-nonce' 	=> wp_create_nonce( 'firefront-deactivate' ),
							),
							admin_url( 'admin.php?page=firefront-plugins' )
						) ),
						$item['sanitized_plugin'],
						esc_html__( 'Deactivate', 'firefront' )
					),
				);
			}
		}elseif ( ! isset( $installed_plugins[$item['file_path']] ) ) {
			$actions = array(
				'install' => sprintf(
					'<a href="%1$s" class="firefront-btn btn-default" title="%3$s %2$s">%3$s</a>',
					esc_url( wp_nonce_url(
						add_query_arg(
							array(
								'page'		  	=> urlencode( TGM_Plugin_Activation::$instance->menu ),
								'plugin'		=> urlencode( $item['slug'] ),
								'plugin_name'   => urlencode( $item['sanitized_plugin'] ),
								'plugin_source' => urlencode( $item['source'] ),
								'tgmpa-install' => 'install-plugin',
								'return_url' 	=> 'firefront-plugins'
							),
							admin_url( TGM_Plugin_Activation::$instance->parent_slug )
						),
						'tgmpa-install',
						'tgmpa-nonce'
					) ),
					$item['sanitized_plugin'],
					esc_html__( 'Install', 'firefront' )
				),
			);
		}elseif ( is_plugin_inactive( $item['file_path'] ) ) {

			if ( version_compare( $item['version'], $installed_plugins[$item['file_path']]['Version'], '>' ) ) {
				$actions = array(
					'update' => sprintf(
						'<a href="%1$s" class="firefront-btn btn-default" title="%3$s %2$s">%3$s</a>',
						wp_nonce_url(
							add_query_arg(
								array(
									'page'		  	=> urlencode( TGM_Plugin_Activation::$instance->menu ),
									'plugin'		=> urlencode( $item['slug'] ),
									'plugin_name'   => urlencode( $item['sanitized_plugin'] ),
									'plugin_source' => urlencode( $item['source'] ),
									'tgmpa-update' 	=> 'update-plugin',
									'version' 		=> urlencode( $item['version'] ),
									'return_url' 	=> 'firefront-plugins'
								),
								admin_url( TGM_Plugin_Activation::$instance->parent_slug )
							),
							'tgmpa-update',
							'tgmpa-nonce'
						),
						$item['sanitized_plugin'],
						esc_html__( 'Update', 'firefront' )
					),
				);
			} else {
				$actions = array(
					'activate' => sprintf(
						'<a href="%1$s" class="firefront-btn btn-default" title="%3$s %2$s">%3$s</a>',
						esc_url( add_query_arg(
							array(
								'plugin'			   	=> urlencode( $item['slug'] ),
								'plugin_name'		  	=> urlencode( $item['sanitized_plugin'] ),
								'plugin_source'			=> urlencode( $item['source'] ),
								'firefront-activate'	   		=> 'activate-plugin',
								'firefront-activate-nonce' 	=> wp_create_nonce( 'firefront-activate' ),
							),
							admin_url( 'admin.php?page=firefront-plugins' )
						) ),
						$item['sanitized_plugin'],
						esc_html__( 'Activate', 'firefront' )
					),
				);
			}
		}elseif ( version_compare( $item['version'], $installed_plugins[$item['file_path']]['Version'], '>' ) ) {
			$actions = array(
				'update' => sprintf(
					'<a href="%1$s" class="firefront-btn btn-default" title="%3$s %2$s">%3$s</a>',
					wp_nonce_url(
						add_query_arg(
							array(
								'page'		  	=> urlencode( TGM_Plugin_Activation::$instance->menu ),
								'plugin'		=> urlencode( $item['slug'] ),
								'plugin_name'   => urlencode( $item['sanitized_plugin'] ),
								'plugin_source' => urlencode( $item['source'] ),
								'tgmpa-update' 	=> 'update-plugin',
								'version' 		=> urlencode( $item['version'] ),
								'return_url' 	=> 'firefront-plugins'
							),
							admin_url( TGM_Plugin_Activation::$instance->parent_slug )
						),
						'tgmpa-update',
						'tgmpa-nonce'
					),
					$item['sanitized_plugin'],
					esc_html__( 'Update', 'firefront' )
				),
			);
		}
		
		return $actions;
	}
	
	public static function get_parent_instance() {
		if ( is_null( self::$_parent_instance ) ) {
			self::$_parent_instance = new self();
		}
		return self::$_parent_instance;
	}
	
}

class Firefront_Plugins {
	
	private static $_instance = null;

	public function __construct() {
		Firefront_Plugin_Info::get_parent_instance();
		add_action( 'admin_menu', array( $this, 'firefront_admin_menu' ) );
	}
	
	public static function firefront_admin_menu(){
		add_submenu_page( 
			'firefront-welcome', 
			esc_html__( 'Theme Plugins', 'firefront' ),
			esc_html__( 'Theme Plugins', 'firefront' ), 
			'manage_options', 
			'firefront-plugins', 
			array( 'Firefront_Plugins', 'firefront_plugins_admin_page' )
		);		
	}
	
	public static function firefront_plugins_admin_page(){
		$firefront_theme = wp_get_theme(); ?>
		<div class="firefront-settings-wrap">	
			<div class="firefront-header-bar">
				<div class="firefront-header-left">
					<div class="firefront-admin-logo-inline">
						<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/brand-logo.png' ); ?>" alt="firefront-logo">
					</div><!-- .firefront-admin-logo-inline -->
					<h2 class="title"><?php esc_html_e( 'Firefront Plugins', 'firefront' ); ?><span class="firefront-version"><?php echo esc_html( $firefront_theme->get( 'Version' ) ); ?></span></h2>
				</div><!-- .firefront-header-left -->
				<div class="firefront-header-right">
					<a href="<?php echo class_exists( 'Firefront_Addon' ) ? esc_url( admin_url( 'admin.php?page=firefront-importer' ) ) : '#'; ?>" class="button firefront-btn"><?php esc_html_e( 'Import Demo', 'firefront' ); ?></a>
				</div><!-- .firefront-header-right -->
			</div><!-- .firefront-header-bar -->
			
			<div class="firefront-inner-wrap">
			<?php 
				require_once FIREFRONT_DIR . '/admin/theme-plugins/tgm-init.php';			
				$plugins = TGM_Plugin_Activation::$instance->plugins;
				$plugin_custom_order = array(
					'elementor' 		=> $plugins['elementor'],
					'firefront-addon' 	=> $plugins['firefront-addon'],
					'classic-elementor-addons-pro' => $plugins['classic-elementor-addons-pro'],
					'cea-post-types' => $plugins['cea-post-types'],
					'zozo-header-footer' => $plugins['zozo-header-footer'],					
					'revslider'			=> $plugins['revslider'],
					'contact-form-7' 	=> $plugins['contact-form-7'],
					'woocommerce' 	=> $plugins['woocommerce'],
					'envato-market' 	=> $plugins['envato-market']
				);
			
				$installed_plugins = Firefront_Plugin_Info::firefront_get_plugins();
				
				if( isset( $_GET['firefront-deactivate'] ) && $_GET['firefront-deactivate'] == 'deactivate-plugin' ) {
					check_admin_referer( 'firefront-deactivate', 'firefront-deactivate-nonce' );
					$plugins = TGM_Plugin_Activation::$instance->plugins;
					foreach( $plugins as $plugin ) {
						if( $plugin['slug'] == $_GET['plugin'] ) {
							deactivate_plugins( $plugin['file_path'] );
						}
					}
				}
				if( isset( $_GET['firefront-activate'] ) && $_GET['firefront-activate'] == 'activate-plugin' ) {
					check_admin_referer( 'firefront-activate', 'firefront-activate-nonce' );
					$plugins = TGM_Plugin_Activation::$instance->plugins;
					foreach( $plugins as $plugin ) {
						if( $plugin['slug'] == $_GET['plugin'] ) {
							activate_plugin( $plugin['file_path'] );
						}
					}
				}
				$plugins = $plugin_custom_order;
			?>
			
				<div class="firefront-settings-tabs">
					<div id="firefront-general" class="firefront-settings-tab firefront-elements-list active">
						<div class="container">
							<form id="multi-plugins-active-form" method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=firefront-plugins' ) ); ?>" enctype="multipart/form-data">
								<input type="hidden" name="action" value="firefront_tgm_install" />
								<?php wp_nonce_field( 'firefront-bulk-plugin-install', 'firefront_bulk_nonce' ); ?>
								<p class="firefront-settings-msg">
									<span class="multi-select-wrap">
										<input type="checkbox" class="bulk-select-all" /> <?php echo esc_html__('Bulk Select', 'firefront'); ?>
									</span>
									<select class="bulk-plugins-action-trigger btn btn-default" name="firefront_bulk_action">
										<option value="install"><?php echo esc_html__('Install', 'firefront'); ?></option>
										<option value="active"><?php echo esc_html__('Activate', 'firefront'); ?></option>
										<option value="deactive"><?php echo esc_html__('Deactivate', 'firefront'); ?></option>
									</select>
									<a href="#" class="button firefront-bulk-action"><?php echo esc_html__('Apply', 'firefront'); ?></a>
									<img src="<?php echo esc_url( FIREFRONT_URI . '/admin/assets/images/loader.gif' ); ?>" alt="<?php echo esc_attr_e('Loader', 'firefront'); ?>" class="bulk-process-loader" />
								</p>
								<?php echo wp_nonce_field( 'firefront_activate_nonce', 'firefront-multi-plugin*^*' ); ?>
							</form>
							<div class="row multi-cols">
							<?php
								$active_action = '';
								if( isset( $_GET['plugin_status'] ) ) {
									$active_action = $_GET['plugin_status'];
								}
								$req_plugs = array();
					
								foreach( $plugins as $plugin ):
									$class = '';
									$plugin_status = '';
									$active_action_class = '';
									$file_path = $plugin['file_path'];
									$plugin_action = Firefront_Plugin_Info::firefront_plugin_link( $plugin );
									foreach( $plugin_action as $action => $value ) {
										if( $active_action == $action ) {
											$active_action_class = ' plugin-' .$active_action. '';
										}
									}
									
									$is_plug_act = 'is_plugin_active';
									if( $is_plug_act( $file_path ) ) {
										$plugin_status = 'active';
										$class = ' active';
										$req_plugs[] = esc_html( $plugin['slug'] );
									}
									
									$class .= $active_action_class;
							?>
								<div class="col-4<?php echo esc_attr( $class ); ?>">
									<div class="media admin-box firefront-plugins-box">
										<div class="admin-box-icon p-0 mr-3">
											<span class="plugin-image-wrap"><img src="<?php echo esc_url( $plugin['image_url'] ); ?>" alt="<?php echo esc_attr( $plugin['name'] ); ?>" /></span>
										</div>
										<div class="media-body admin-box-info">
											<h3 class="admin-box-title"><?php echo esc_html( $plugin['name'] ); ?></h3>
											<div class="admin-box-content">
												<?php if( $plugin['required'] ): ?>
												<div class="plugin-required"><span class="dashicons dashicons-star-filled" title="<?php echo esc_attr__('Required', 'firefront'); ?>"></span></div>
												<?php endif; ?>
												<?php if( isset( $installed_plugins[$plugin['file_path']] ) ): ?> 
												<div class="plugin-info"><?php 
													$current_version = $installed_plugins[$plugin['file_path']]['Version'];
													$available_version = $current_version;
													if( $plugin['source'] == 'repo' ){
														$available_version = Firefront_Plugin_Info::firefront_does_plugin_require_update( $plugin['file_path'] );
													}
												?>
													<?php echo sprintf( 'v%s | %s', $installed_plugins[$plugin['file_path']]['Version'], $installed_plugins[$plugin['file_path']]['Author'] ); ?>
												</div>
												<?php endif; ?>
												<div class="theme-actions--">
													<?php foreach( $plugin_action as $action ) { echo ( ''. $action ); } ?>
												</div>
												<?php $available_version = '';
												      $current_version = '';
													if( $plugin['source'] == 'repo' && version_compare( $available_version, $current_version, '>' ) ): ?>
													<div class="theme-update"><?php echo esc_html__('Update Available: Version', 'firefront'); ?> <?php echo esc_attr( $available_version ); ?></div>
												<?php
												elseif( isset( $plugin_action['update'] ) && $plugin_action['update'] ): ?>
													<div class="theme-update"><?php echo esc_html__('Update Available: Version', 'firefront'); ?> <?php echo esc_attr( $plugin['version'] ); ?></div>
												<?php endif; ?>
												<span class="multi-active-wrap"><input type="checkbox" class="bulk-activator" value="<?php echo esc_attr( $plugin['slug'] ); ?>" /></span>
											</div>
										</div>
									</div>
								</div><!-- .col -->
							<?php endforeach; ?>
							</div><!-- .row -->
						</div><!-- .container -->
					</div><!-- .firefront-settings-tab -->
				</div><!-- .firefront-settings-tabs -->
			
			</div><!-- .firefront-inner-wrap -->
		</div><!-- .firefront-settings-wrap -->
	<?php
	}
	
	public static function get_instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

} Firefront_Plugins::get_instance();

//Plugin ajax functions
add_action( 'wp_ajax_firefront_tgm_install', array( 'Firefront_Plugin_Info', 'firefront_tgm_install' ) );
add_action( 'wp_ajax_nopriv_firefront_tgm_install', array( 'Firefront_Plugin_Info', 'firefront_tgm_install' )  );