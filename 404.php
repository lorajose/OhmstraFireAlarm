<?php
/**
 * The template for displaying pages.
 */

get_header();

Firefront_Wp_Elements::$template = 'archive';
Firefront_Wp_Elements::$firefront_page_options = get_post_meta( get_the_ID(), 'firefront_post_meta', true );

?>

<main id="site-content">

	<?php 
		/*
		* Page title template call
		*/
		get_template_part( 'template-parts/page', 'title' );
	?>

	<div class="firefront-content-wrap container page">
		<div class="row">
			<div class="col">
				<div class="section-inner thin error404-content">				
					<?php
				$building_tool = Firefront_Wp_Elements::firefront_options('404_building_tool');
				switch ($building_tool) {
				    case 'elementor':
				        $selected_page_id = Firefront_Wp_Elements::firefront_options('404-page-selector');
				        $page = get_post($selected_page_id);
				        if ($page && !is_wp_error($page)) {
				            echo '<div class="elementor-content">';
				            echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display($selected_page_id);
				            echo '</div>';
				        } else {
				            echo '<h1 class="entry-title">' . __('Page Not Found', 'firefront') . '</h1>';
				            echo '<div class="intro-text"><p>' . __('The page you were looking for could not be found. It might have been removed, renamed, or did not exist in the first place.', 'firefront') . '</p></div>';
				            get_search_form(array('label' => __('404 not found', 'firefront')));
				        }
				        break;

				    case 'default':
				        echo '<h1 class="entry-title">' . __('Page Not Found', 'firefront') . '</h1>';
				        echo '<div class="intro-text"><p>' . __('The page you were looking for could not be found. It might have been removed, renamed, or did not exist in the first place.', 'firefront') . '</p></div>';
				        get_search_form(array('label' => __('404 not found', 'firefront')));
				        break;

				    default:
				        echo '<h1 class="entry-title">' . __('No template is selected', 'firefront') . '</h1>';
				        echo '<div class="intro-text"><p>' . __('Choose the template that should be shown in the 404 Page', 'firefront') . '</p></div>';
				        get_search_form(array('label' => __('404 not found', 'firefront')));
				}
				?>
				</div><!-- .section-inner -->
			</div><!-- .col -->
		</div><!-- .row -->
	</div><!-- .container -->
</main><!-- #site-content -->

<?php get_footer(); ?>
