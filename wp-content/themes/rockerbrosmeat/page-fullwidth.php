<?php
/**
 * Template Name: Full Width Page
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
	<div class="page-header-wrap grid grid-pad">
		<header class="page-header grid-col col-12-12">
			<h1 class="page-title"><span><?php echo get_the_title(); ?></span></h1>
			<?php if ( function_exists('yoast_breadcrumb') )
				{yoast_breadcrumb('<div id="breadcrumbs" class="breadcrumbs" >','</div>');
			} ?>
		</header><!--.grid-col -->
	</div><!--.grid -->
</div><!--.site-featured-image -->

<div class="grid grid-pad">
	<div id="primary" class="content-area grid-col col-12-12">
		<main id="main" class="site-main" role="main">

			<?php while ( have_posts() ) : the_post(); ?>

				<?php get_template_part( 'template-parts/content', 'page' ); ?>

				<?php
					// If comments are open or we have at least one comment, load up the comment template.
					if ( comments_open() || get_comments_number() ) :
						comments_template();
					endif;
				?>

			<?php endwhile; // End of the loop. ?>

		</main><!-- #main -->
	</div><!-- #primary -->
</div><!--.grid-pad -->
<?php get_footer(); ?>
