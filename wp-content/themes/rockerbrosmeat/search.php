<?php
/**
 * The template for displaying search results pages.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
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
			<h1 class="page-title"><span><?php printf( esc_html__( 'Search Results for: %s', 'dhali' ), get_search_query() ); ?></span></h1>
		</header><!-- .page-header -->
	</div><!--.grid -->
</div><!--.site-featured-image -->

<div class="grid grid-pad">
	<section id="primary" class="content-area grid-col col-12-12 push-right">
		<main id="main" class="site-main" role="main">

		<?php if ( have_posts() ) : ?>

			<?php /* Start the Loop */ ?>
			<?php while ( have_posts() ) : the_post(); ?>

				<?php
				/**
				 * Run the loop for the search to output the results.
				 * If you want to overload this in a child theme then include a file
				 * called content-search.php and that will be used instead.
				 */
				get_template_part( 'template-parts/content', 'search' );
				?>

			<?php endwhile; ?>

			<?php the_posts_navigation(); ?>

		<?php else : ?>

			<?php get_template_part( 'template-parts/content', 'none' ); ?>

		<?php endif; ?>

		</main><!-- #main -->
	</section><!-- #primary -->
</div><!--.grid-pad -->
<?php get_footer(); ?>