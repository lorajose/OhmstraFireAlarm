<?php 

/*
 * Firefront WP Elements Class
 */

class Firefront_Wp_Elements {
	
	private static $_instance = null;

	private static $post_id = null;

	public static $page_title_stat = false; 
		
	public static $firefront_options = null;

	public static $firefront_page_options = null;

	public static $template = null;
	
	public function __construct() {
		
		//Get theme option
		self::$firefront_options = class_exists( 'Firefront_Theme_Option' ) ? Firefront_Theme_Option::$firefront_options : apply_filters( 'firefront_options', get_option( 'firefront_options' ) );
		
		if( function_exists( 'firefront_get_woo_merge_config' ) ){
			if( !isset( self::$firefront_options['shop-title'] ) ) {
				self::$firefront_options = firefront_get_woo_merge_config( self::$firefront_options );
			}
		}
		
		//Back to top
		add_action( 'firefront_footer_after', array( $this, 'firefront_back_to_top' ), 40 );
		
	}

	public static function firefront_options( $element ){ //Firefront_Wp_Elements::firefront_options()
		$opt_array = self::$firefront_options;
		return !empty( $element ) && isset( $opt_array[$element] ) ? $opt_array[$element] : '';
	}
	
	public static function firefront_get_meta_and_option_values( $keys ){
		$post_options = self::$firefront_page_options;

		$options = array();
		foreach( $keys['fields'] as $field => $key ) $options[$field] = null;

		if( is_singular() && !empty( $post_options ) && isset( $post_options[$keys['chk']] ) ){
			$_chk = $post_options[$keys['chk']];
			if( $_chk == 'custom' ){
				foreach( $keys['fields'] as $field => $key ) $options[$field] = is_array( $key ) && isset( $key[0] ) && isset( $post_options[$key[0]] ) ? $post_options[$key[0]] : ( isset( $post_options[$key] ) ? $post_options[$key] : '' );
			}else{
				$post_options = null;
			}
		}else{
			$post_options = null;
		}

		if( empty( $post_options ) ){
			$firefront_options = self::$firefront_options;
			foreach( $keys['fields'] as $field => $key ) {
				$count = 1;
				if( is_array( $key ) ){
					$count = count( $key );
				}
				if( $count > 1 ){
					$options[$field] = isset( $firefront_options[$key[1]] ) ? $firefront_options[$key[1]] : '';					
				}elseif( isset( $firefront_options[$key] ) ){
					$options[$field] = isset( $firefront_options[$key] ) ? $firefront_options[$key] : '';
				}
			}
		}

		return $options;
	}
	
	public static function firefront_get_content_class(){
		$template = self::$template;
		$keys = array(
			'chk' => 'sidebar-chk',
			'fields' => array(
				'sidebar_layout' => array( 'sidebar-layout', $template.'-sidebar-layout' ),
				'right_sidebar' => array( 'right-sidebar', $template.'-right-sidebar' ),
				'left_sidebar' => array( 'left-sidebar', $template.'-left-sidebar' )
			)
		);
		$page_title_values = Firefront_Wp_Elements::firefront_get_meta_and_option_values( $keys );
		$sidebar_layout = $page_title_values['sidebar_layout'];
		$col = 12; $extra_class = '';
		if( $sidebar_layout != 'no-sidebar' ){
			if( $sidebar_layout == 'left-sidebar' ){
				$left_sidebar = $page_title_values['left_sidebar'];
				if( $left_sidebar != 'none' && is_active_sidebar($left_sidebar) ){
					$col -= 4;
					$extra_class = 'order-md-2';
				};
			}elseif( $sidebar_layout == 'right-sidebar' ){
				$right_sidebar = $page_title_values['right_sidebar'];
				if( $right_sidebar != 'none' && is_active_sidebar($right_sidebar) ){
					$col -= 4;
				};
			}elseif( $sidebar_layout == 'both-sidebar' ){
				$left_sidebar = $page_title_values['left_sidebar'];
				$right_sidebar = $page_title_values['right_sidebar'];
				if( $left_sidebar != 'none' && is_active_sidebar($left_sidebar) && $right_sidebar != 'none' && is_active_sidebar($right_sidebar) ){
					$col -= 6;
				}else{
					if( $left_sidebar != 'none' && is_active_sidebar($left_sidebar) ){
						$col -= 4;
					};					
					if( $right_sidebar != 'none' && is_active_sidebar($right_sidebar) ){
						$col -= 4;
					};
				}
				$extra_class = 'order-md-2';
			}
		}
		return 'col-md-'. esc_attr( $col ) . ' order-md-2';
	}

	public static function firefront_get_template_sidebars( $page_title_values, $side, $col_class = '' ){
		$selected_sidebar = $page_title_values[$side.'_sidebar'];
		if( $selected_sidebar != 'none' && is_active_sidebar($selected_sidebar) ):
			$inner_class = ' widget-area-'. $side;
		?>
		<div class="<?php echo esc_attr( $col_class ); ?>">
			<aside class="content-widgets-outer-wrapper">
				<div class="content-widgets-wrapper">
					<div class="content-widgets<?php echo esc_attr( $inner_class ); ?>">
						<?php dynamic_sidebar( $selected_sidebar ); ?>
					</div>
				</div><!-- .content-widgets-wrapper -->
			</aside><!-- .content-widgets-outer-wrapper -->
		</div>
		<?php
		endif;
	}

	public static function firefront_show_page_title( $title_items ) {
    
		$output = $archive_subtitle = '';
		
		if ( is_singular() ) {
			$archive_title = get_the_title();
		} elseif ( is_search() ) {
			global $wp_query;
			$archive_title = sprintf(
				'%1$s %2$s',
				'<span>' . esc_html__( 'Search:', 'firefront' ),
				'<span>&ldquo;' . get_search_query() . '&rdquo;</span>'
			);
			if ( $wp_query->found_posts ) {
				$archive_subtitle = sprintf(
					_n( 'We found %s result for your search.', 'We found %s results for your search.', $wp_query->found_posts, 'firefront' ),
					number_format_i18n( $wp_query->found_posts )
				);
			} else {
				$archive_subtitle = esc_html__( 'We could not find any results for your search. You can give it another try through the search form below.', 'firefront' );
			}
		} elseif ( is_home() ) {
			$archive_title = self::firefront_options( 'blog-page-title' );
			$archive_subtitle = self::firefront_options( 'blog-page-description' );
		} elseif ( is_404() ) {
			$archive_title = esc_html__( 'Error 404', 'firefront' );
		} else {
			$archive_title = get_the_archive_title();
			$archive_subtitle = get_the_archive_description();
		}
	
		$archive_title = apply_filters( 'firefront_page_title', $archive_title );
		$archive_subtitle = apply_filters( 'firefront_page_title_description', $archive_subtitle );
		
		if ( !empty( $title_items ) ) {
			
			if ( isset( $title_items['disabled'] ) ) unset( $title_items['disabled'] );
			
			echo '<div class="page-title-wrap">';
				foreach ( $title_items as $key => $elements ) {
					$class_name = 'page-title-elements';
					if ( $key == 'right' ) $class_name .= ' page-title-right pull-right';
					elseif ( $key == 'center' ) $class_name .= ' page-title-center pull-center';
				
					echo '<ul class="' . esc_attr( $class_name ) . '">';
					foreach ( $elements as $element => $label ) {
						switch ( $element ) {
							case "title":
								if( $archive_title ) echo '<h1 class="page-title">'. $archive_title .'</h1>';
								self::$page_title_stat = true;
							break;
							case "description":
								if( $archive_subtitle ) echo '<div class="page-subtitle">'. ( $archive_subtitle ) .'</div>';
							break;
							case "breadcrumb":
								$bread_out = self::firefront_breadcrumbs();
								if ( $bread_out ) echo '<div class="breadcrumbs-wrap">' . $bread_out . '</div><!-- .breadcrumbs-wrap -->';
							break;
						}
					}
					echo '</ul>';
				}
			echo '</div><!-- .page-title-wrap -->';
		}
	}
	public static function firefront_get_post_meta( $template = 'single', $part = 'left' ) {
		$meta_items = Firefront_Wp_Elements::firefront_options( esc_attr( $template ) . '-' . esc_attr( $part ) . '-meta-items' );
		if ( !empty( $meta_items ) ):
			if ( isset( $meta_items['disabled'] ) ) unset( $meta_items['disabled'] ); ?>
			<div class="<?php echo esc_attr( $part ); ?>-meta-wrap">
			<?php
				foreach ( $meta_items as $key => $value ) {
					if ( !empty( $value ) ):
						$class_name = $key == 'right' ? ' pull-right' : '';
						echo '<ul class="nav post-meta' . esc_attr( $class_name ) . '">';
						foreach ( $value as $element => $label ) {
							self::firefront_post_meta_items( $element );
						}
						echo '</ul>';
					endif;
				}
			?>
			</div><!-- .top-meta-wrap -->
			<?php
		endif;
	}

	public static function firefront_post_meta_items( $element = '' ) {
		switch ( $element ) {
			case "date": ?>
				<li class="post-date"><?php Firefront_Wp_Framework::firefront_get_post_date_as_link(); ?></li>
			<?php 
			break;
			case "author": ?>
				<li class="post-author"><?php Firefront_Wp_Framework::firefront_get_post_author(); ?></li>
			<?php 
			break;
			case "category":
				$terms = get_the_terms( get_the_ID(), 'category' );
				if ( $terms && !is_wp_error( $terms ) ) : ?>
					<li class="post-category"> <?php Firefront_Wp_Framework::firefront_get_the_terms_as_out( get_the_ID(), 'category' ); ?></li>
				<?php endif;
			break;
			case "tag":
				$posttags = get_the_tags();
				$terms = get_the_terms( get_the_ID(), 'post_tag' );
				if ( $terms && !is_wp_error( $terms ) ) : ?>
					<li class="post-tag"><span class="bi bi-tag"></span> <?php Firefront_Wp_Framework::firefront_get_the_terms_as_out( get_the_ID(), 'post_tag' ); ?></li>
				<?php endif;
			break;
			case "more": if ( !is_singular() ):
				$blog_read_more = Firefront_Wp_Elements::firefront_options('blog-read-more');?>
				<li class="post-more pull-right"><a href="<?php echo esc_url( get_the_permalink() ); ?>"><?php echo esc_html( sprintf( __( '%s', 'firefront' ), $blog_read_more ) ); ?></a></li>
			<?php endif;
			break;
			case "share":
				if ( class_exists( 'Firefront_Custom_Functions' ) ): ?>
					<li class="post-share-wrap"><?php Firefront_Custom_Functions::firefront_social_share(); ?></li>
				<?php endif;
			break;
		}
	}
	
	public static function firefront_values_to_currency_format( $value ){
		$result = '';
		if( $value > 999 && $value <= 999999 ) {
			$qcnt = round( ( $value % 1000 ) / 100 );
			$val = floor( $value / 1000 );
			if( $qcnt == 10 ){
				$result .= $val + 1;
			}else{
				$result .= $qcnt ? $val . '.' . $qcnt : $val;
			}			
			$result .= 'k';
		} elseif( $value > 999999 ) {
			$result = floor( $value / 1000000 ) . 'm';
		} else {
			$result = $value;
		}
		return $result;
	}

	public static function firefront_breadcrumbs() { //Firefront_Wp_Elements::firefront_breadcrumbs()	 
		$showOnHome = 1; // 1 - show breadcrumbs on the homepage, 0 - don't show
		$delimiter = '<i class="breadcrumb-delimiter"></i>';//'&raquo;'; // delimiter between crumbs
		$home = esc_html__( 'Home', 'firefront' ); // text for the 'Home' link
		$showCurrent = 1; // 1 - show current post/page title in breadcrumbs, 0 - don't show
		$before = '<li><span class="current">'; // tag before the current crumb
		$after = '</span></li>'; // tag after the current crumb
		
		$allowed_html = array(
			'li' => array(
				'itemprop' => array(),
				'itemscope' => array(),
				'itemtype' => array()
			),
			'a' => array(
				'href' => array(),
				'title' => array(),
				'itemprop' => array()
			)
		);

		global $post;
		$homeLink = home_url( '/' );
		$bread_out = '';
		$bread_out .= '<li class="breadcrumb-wrap"><ul id="breadcrumb" class="breadcrumb nav">';

			// Handle Front Page and Blog Page Separately
			if (is_home() && is_front_page()) {
				// Home Page
				if ($showOnHome == 1) $bread_out .= wp_kses( $before . $home . $after, $allowed_html );
			} elseif (is_home()) {
				
			// Blog Page when set in reading settings
			$blog_page_id = get_option('page_for_posts');
			if ($blog_page_id) {
					$blog_page_title = get_the_title($blog_page_id);
				$bread_out .= '<li><a href="' . $homeLink . '"><span>' . $home . '</span></a>' . $delimiter . '</li> ';
				$bread_out .= wp_kses($before . $blog_page_title . $after, $allowed_html);
				}
			} else {
			$bread_out .= '<li><a href="' . $homeLink . '"><span>' . $home . '</span></a>' . $delimiter . '</li> ';
			if ( !is_single() && !is_page() && get_post_type() != 'post' && !is_404() ) {
				$post_type = get_post_type_object(get_post_type());
				if( $post_type ){
					$bread_out .= wp_kses( $before . $post_type->labels->singular_name . $after, $allowed_html );
				}else{
					$queried_object = get_queried_object();
					if( $queried_object )
					$bread_out .= wp_kses( $before . $queried_object->name . $after, $allowed_html );
				}
			} elseif ( is_category() ) {
				$thisCat = get_category(get_query_var('cat'), false);
				if ($thisCat->parent != 0){     						
					$bread_out .= self::firefront_get_term_parents_list( $thisCat, 'category', array( 'separator' => ' ' . $delimiter . ' ' ) );
				}
				$bread_out .= wp_kses( $before . single_cat_title('', false) . $after, $allowed_html );
			} elseif ( is_search() ) {
				$bread_out .= wp_kses( $before . get_search_query() . $after, $allowed_html );
			} elseif ( is_day() ) {
				$bread_out .= '<li><a href="' . get_year_link(get_the_time('Y')) . '"><span>' . get_the_time('Y') . '</span></a> ' . $delimiter . '</li> ';
				$bread_out .= '<li><a href="' . get_month_link(get_the_time('Y'),get_the_time('m')) . '"><span>' . get_the_time('F') . '</span></a> ' . $delimiter . '</li> ';
				$bread_out .= wp_kses( $before . get_the_time('d') . $after, $allowed_html );
			} elseif ( is_month() ) {
				$bread_out .= '<li><a href="' . get_year_link(get_the_time('Y')) . '"><span>' . get_the_time('Y') . '</span></a> ' . $delimiter . '</li> ';
				$bread_out .= wp_kses( $before . get_the_time('F') . $after, $allowed_html );
			} elseif ( is_year() ) {
				$bread_out .= wp_kses( $before . get_the_time('Y') . $after, $allowed_html );
			} elseif ( is_single() && !is_attachment() ) {
				if ( get_post_type() != 'post' ) {
					$post_type = get_post_type_object(get_post_type()); //print_r( $post_type );
					$slug = $post_type->rewrite;
					$slug = is_array( $slug ) && isset( $slug['slug'] ) ? $slug['slug'] : '';
					$bread_out .= '<li><a href="' . $homeLink . $slug . '/"><span>' . $post_type->labels->singular_name . '</span></a>';
					if ($showCurrent == 1) $bread_out .= ' ' . $delimiter . ' ' . $before . get_the_title() . $after;
					$bread_out .= '</li>';					
				} else {
					$cat = get_the_category(); $cat = $cat[0];
					//$cats = get_category_parents( $cat, TRUE, ' ' . $delimiter . ' ' );
					$cats = self::firefront_get_term_parents_list( $cat, 'category', array( 'separator' => ' ' . $delimiter . ' ' ) );
					if ($showCurrent == 0) $cats = preg_replace("#^(.+)\s$delimiter\s$#", "$1", $cats);
					$bread_out .= $cats;
					if ($showCurrent == 1) $bread_out .= $before . get_the_title() . $after;
				}
			} elseif ( is_attachment() ) {
				if ($showCurrent == 1) $bread_out .= ' ' . $delimiter . ' ' . $before . get_the_title() . $after;	 
			} elseif ( is_page() && !$post->post_parent ) {
				if ($showCurrent == 1) $bread_out .= wp_kses( $before . get_the_title() . $after, $allowed_html );
			} elseif ( is_page() && $post->post_parent ) {
				$parent_id  = $post->post_parent;
				$breadcrumbs = array();
				while ($parent_id) {
					$page = get_page($parent_id);
					$breadcrumbs[] = '<li><a href="' . get_permalink($page->ID) . '"><span>' . get_the_title($page->ID) . '</span></a></li>';
					$parent_id  = $page->post_parent;
				}
				$breadcrumbs = array_reverse($breadcrumbs);
				for ($i = 0; $i < count($breadcrumbs); $i++) {
					$bread_out .= wp_kses( $breadcrumbs[$i], $allowed_html );
					if ($i != count($breadcrumbs)-1) $bread_out .= ' ' . $delimiter . ' ';
				}
				if ($showCurrent == 1) $bread_out .= ' ' . $delimiter . ' ' . $before . get_the_title() . $after;
			} elseif ( is_tag() ) {
				$bread_out .= wp_kses( $before . single_tag_title('', false) . $after, $allowed_html );
			} elseif ( is_author() ) {
				global $author;
				$userdata = get_userdata($author);
				$bread_out .= wp_kses( $before . esc_html__( 'Posts by ', 'firefront' ) . $userdata->display_name . $after, $allowed_html );
			} elseif ( is_404() ) {
				$bread_out .= wp_kses( $before . esc_html__( 'Error 404', 'firefront' ) . $after, $allowed_html );
			}
			if ( get_query_var('paged') ) {
				if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) $bread_out .= ' (';
				$bread_out .= esc_html__( 'Page', 'firefront' ) . ' ' . get_query_var('paged');
				if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) $bread_out .= ')';
			}
		}
		$bread_out .= '</ul></li>';
		return $bread_out;
	} 
	
	public static function firefront_get_term_parents_list( $term_id, $taxonomy, $args = array() ) {
		$list = '';
		$term = get_term( $term_id, $taxonomy );
	 
		if ( is_wp_error( $term ) ) {
			return $term;
		}
	 
		if ( ! $term ) {
			return $list;
		}
	 
		$term_id = $term->term_id;
	 
		$defaults = array(
			'format'    => 'name',
			'separator' => '/',
			'link'      => true,
			'inclusive' => true,
		);
	 
		$args = wp_parse_args( $args, $defaults );
	 
		foreach ( array( 'link', 'inclusive' ) as $bool ) {
			$args[ $bool ] = wp_validate_boolean( $args[ $bool ] );
		}
	 
		$parents = get_ancestors( $term_id, $taxonomy, 'taxonomy' );
	 
		if ( $args['inclusive'] ) {
			array_unshift( $parents, $term_id );
		}
	 
		foreach ( array_reverse( $parents ) as $term_id ) {
			$parent = get_term( $term_id, $taxonomy );
			$name   = ( 'slug' === $args['format'] ) ? $parent->slug : $parent->name;
			if ( $args['link'] ) {
				$list .= '<li><a href="' . esc_url( get_term_link( $parent->term_id, $taxonomy ) ) . '"><span>' . $name . '</span></a>' . $args['separator'] .'</li>';
			} else {
				$list .= '<li><span>'. $name .'</span>'. $args['separator'] .'</li>';
			}
		}
	 
		return $list;
	}

	public static function firefront_secondary_bar(){
		$secondary_bar = Firefront_Wp_Elements::firefront_options('secondary-sidebar');
		if( !empty( $secondary_bar ) && is_active_sidebar( $secondary_bar ) ){ 
			$animation_from = Firefront_Wp_Elements::firefront_options('secondary-sidebar-from');
			$class_name = !empty( $animation_from ) && $animation_from == 'left' ? ' from-left' : ' from-right';
		?>
		<div class="secondary-bar-wrapper<?php echo esc_attr( $class_name ); ?>">
			<div class="secondary-bar-inner">
				<a href="<?php echo esc_url( home_url() ); ?>" class="secondary-menu-toggle firefront-toggle"><span></span><span></span><span></span><span></span></a>
				<?php dynamic_sidebar( $secondary_bar ); ?>
			</div>
		</div>
		<?php
		}
	}
	
	public static function firefront_back_to_top(){ ?>
		<a href="<?php echo esc_url( home_url() ); ?>" class="back-to-top" id="back-to-top"><i class="bi bi-caret-up-fill"></i></a>
	<?php
	}

	public static function get_instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

}
Firefront_Wp_Elements::get_instance();