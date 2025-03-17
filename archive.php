<?php
/**
 * Archive template
 */

get_header();

Firefront_Wp_Elements::$template = 'archive';

$blog_structure = Firefront_Wp_Elements::firefront_options('blog-layout');
$blog_grid_columns = Firefront_Wp_Elements::firefront_options('blog-grid-columns');
$blog_grid_gutter = Firefront_Wp_Elements::firefront_options('blog-grid-gutter');

?>

<main id="site-content">

	<?php 
		/*
		* Page title template call
		*/
		get_template_part( 'template-parts/page', 'title' );
	?>

	<div class="firefront-content-wrap container">
		<div class="row">
			<?php
				$content_col_class = Firefront_Wp_Elements::firefront_get_content_class();
			?>
			<div class="<?php echo esc_attr( $content_col_class ); ?>">
				<?php				
				if ( have_posts() ) { 
					if ( $blog_structure === 'list' ){
						echo '<div class="firefront-masonry" data-columns="1" data-gutter="30">';
						while ( have_posts() ) {
							the_post();
							get_template_part( 'template-parts/content', 'excerpt' );
						} 
					echo '</div>';		
					}elseif( $blog_structure === 'grid' ) {
						echo '<div class="firefront-masonry" data-columns='. $blog_grid_columns .' data-gutter='. $blog_grid_gutter.'>';
						while ( have_posts() ) {
							the_post();
							get_template_part( 'template-parts/content', 'excerpt' );
						} 
					echo '</div>';		
					}else{
						echo '<div class="firefront-masonry" data-columns="1" data-gutter="30">';
						while ( have_posts() ) {
							the_post();
							get_template_part( 'template-parts/content', 'excerpt' );
						} 
						echo '</div>';
					}
				}
				?>
				<?php get_template_part( 'template-parts/pagination' ); ?>
			</div><!-- .col -->
			<?php get_template_part( 'template-parts/content-sidebar' ); ?>
		</div><!-- .row -->
	</div><!-- .firefront-content-wrap -->

</main><!-- #site-content -->

<?php
get_footer();
