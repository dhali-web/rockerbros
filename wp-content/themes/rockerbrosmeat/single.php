<?php
/**
 * The template for displaying all single posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package dhali
 */

get_header(); ?>

<?php
	if ( has_post_thumbnail() ) {
		$featured_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'post-thumbnail' );
		$featured_image_url = $featured_image_url[0];
	} else {
		$featured_image_url = get_template_directory_uri().'/images/layout/featured-image.jpg';
	}
?>
<div class="site-featured-image" style="background-image: url('<?php echo $featured_image_url; ?>')">
	<div class="grid grid-pad">
		<header class="page-header grid-col col-12-12">
			<h1 class="page-title">
				<span>
				<?php
						
					if( is_home() && get_option('page_for_posts') ) {
						$post_page_id = get_option('page_for_posts');
						$post_page_object = get_post($post_page_id);
						$post_page_title = $post_page_object->post_title;
						echo $post_page_title;
						} else {
						echo get_the_title();
					}

				?>
				</span>
			</h1>
		</header><!--.grid-col -->
	</div><!--.grid -->
</div><!--.site-featured-image -->

<div class="grid grid-pad">
	<div id="primary" 
		class="
			content-area 
			grid-col
			<?php if(!is_woocommerce()) {
				echo 'col-8-12';
			} ?>  
			push-right">
		<main id="main" class="site-main" role="main">

		<?php while ( have_posts() ) : the_post(); ?>

			<?php get_template_part( 'template-parts/content', 'single' ); ?>

			<?php the_post_navigation(); ?>

			<?php
				// If comments are open or we have at least one comment, load up the comment template.
				if ( comments_open() || get_comments_number() ) :
					comments_template();
				endif;
			?>

		<?php endwhile; // End of the loop. ?>

		</main><!-- #main -->
	</div><!-- #primary -->
	<?php if(!is_woocommerce()) {
		get_sidebar();
	} ?>
</div><!--.grid-pad -->

<?php get_footer(); ?>
