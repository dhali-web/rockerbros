<?php
/**
 * The template for displaying archive pages.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package dhali
 */

get_header(); ?>

<?php
	$featured_image_url = get_template_directory_uri().'/images/layout/featured-image.jpg';
?>

<div class="site-featured-image" style="background-image: url('<?php echo $featured_image_url; ?>')">
	<div class="grid grid-pad">
		<header class="page-header grid-col col-12-12">
			<?php the_archive_title( '<h1 class="page-title"><span>', '</span></h1>' ); ?>
		</header><!-- .page-header -->
	</div><!--.grid -->
</div><!--.site-featured-image -->

<div class="grid grid-pad">
	<div id="primary" class="content-area grid-col col-8-12 push-right">
		<main id="main" class="site-main" role="main">

		<?php if ( have_posts() ) : ?>

			<?php the_archive_description( '<div class="taxonomy-description">', '</div>' ); ?>

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
<?php get_sidebar(); ?>
</div><!--.grid-pad -->
<?php get_footer(); ?>
