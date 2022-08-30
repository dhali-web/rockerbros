<?php
/**
 * The template for displaying the front page.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package dhali
 */

get_header(); ?>

<?php include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	if ( is_plugin_active('ml-slider/ml-slider.php') )
		$hasMetaSliderPlugin = true;
?>

<?php if ( $hasMetaSliderPlugin ) { ?>

<div class="site-featured-slider">
	<?php echo do_shortcode("[metaslider id=20902]"); ?>
</div><!-- /.site-feature -->

<?php } ?>

<div class="grid grid-pad">
	<div id="primary" class="content-area grid-col col-12-12">
		<main id="main" class="site-main" role="main">

		<?php if ( have_posts() ) : ?>

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

		<?php else : ?>

			<?php get_template_part( 'template-parts/content', 'none' ); ?>

		<?php endif; ?>

		<h1 class="divider-title align-center"><span>Our Selection</span></h1>

		<div class="selection-items-grid grid grid-pad">
			<?php dynamic_sidebar( 'our-selection' ); ?>
		</div><!-- .grid -->

		<div class="entry-content">
		<p style="text-align: center"><strong>MISSION STATEMENT</strong></p>
		<p style="text-align: center">Rocker Bros. Meat &amp; Provision, Inc. is dedicated to providing chefs and restaurateurs with the finest meats available at the fairest prices possible. In addition, we offer extraordinary personal service. We believe that while we offer late night call in times for next day delivery, finding top-of-the-line meats at great prices is also key to serving our customers. The ongoing research and development we do with farmers and other meat vendors, is key to keeping our customers well informed and on the cutting edge of a rapidly changing and expanding marketplace.</p>
		<p style="text-align: center"><strong>LOYALTY,&nbsp;</strong><strong>RELIABILITY,&nbsp;</strong><strong>SATISFACTION,&nbsp;</strong><strong>&amp; TRUST</strong></p>
		<p style="text-align: center"><em>We Are Family, &nbsp;And So Are You!</em></p>
		</div>
		<div class="featured-items-grid grid grid-pad">
			<?php dynamic_sidebar( 'featured-items' ); ?>
		</div><!-- .grid -->
		<?php echo do_shortcode('[instagram-feed type=hashtag hashtag="#rockerbrosmeat"]'); ?>
		</main><!-- #main -->
	</div><!-- #primary -->
</div><!--.grid-pad -->
<?php get_footer(); ?>