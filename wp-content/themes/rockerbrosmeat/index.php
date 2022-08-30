<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
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
		</header>
	</div><!--.grid -->
</div><!--.site-featured-image -->

<div class="grid grid-pad">
	<div id="primary" class="content-area grid-col col-8-12 push-right">
		<main id="main" class="site-main" role="main">

		<?php if ( have_posts() ) : ?>

			<?php if ( is_home() && ! is_front_page() ) : ?>
				<header>
					<h1 class="page-title screen-reader-text"><?php single_post_title(); ?></h1>
				</header>
			<?php endif; ?>

			<?php /* Start the Loop */ ?>
			<?php while ( have_posts() ) : the_post(); ?>

				<?php

					/*
					 * Include the Post-Format-specific template for the content.
					 * If you want to override this in a child theme, then include a file
					 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
					 */
					get_template_part( 'template-parts/content', get_post_format() );
				?>

			<?php endwhile; ?>

			<?php the_posts_navigation(); ?>

		<?php else : ?>

			<?php get_template_part( 'template-parts/content', 'none' ); ?>

		<?php endif; ?>

		</main><!-- #main -->
	</div><!-- #primary -->
	<?php if(is_woocommerce()) {echo "This is Woocommerce - Testing";} ?>
<?php get_sidebar(); ?>
</div><!--.grid-pad -->
<?php get_footer(); ?>