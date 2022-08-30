<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
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
			<h1 class="page-title"><span><?php esc_html_e( 'Oops! That page can&rsquo;t be found.', 'dhali' ); ?></span></h1>
		</header><!-- .page-header -->
	</div><!--.grid -->
</div><!--.site-featured-image -->

<div class="grid grid-pad">
	<div id="primary" class="content-area grid-col col-12-12">
		<main id="main" class="site-main" role="main">

			<section class="error-404 not-found">

				<div class="page-content">
					<p><?php esc_html_e( 'It looks like nothing was found at this location. Maybe try one of the links below or a search?', 'dhali' ); ?></p>

					<?php get_search_form(); ?>

					<hr>

					<div class="grid grid-pad">
						<div class="grid-col col-3-12">
							<?php the_widget( 'WP_Widget_Recent_Posts' ); ?>
						</div><!--.grid-col -->

						<?php if ( dhali_categorized_blog() ) : // Only show the widget if site has multiple categories. ?>
							<div class="grid-col col-3-12">
								<div class="widget widget_categories">
									<h2 class="widget-title"><?php esc_html_e( 'Most Used Categories', 'dhali' ); ?></h2>
									<ul>
									<?php
										wp_list_categories( array(
											'orderby'    => 'count',
											'order'      => 'DESC',
											'show_count' => 1,
											'title_li'   => '',
											'number'     => 10,
										) );
									?>
									</ul>
								</div><!-- .widget -->
							</div><!--.grid-col -->
						<?php endif; ?>

						<div class="grid-col col-3-12">
							<?php
								/* translators: %1$s: smiley */
								$archive_content = '<p>' . sprintf( esc_html__( 'Try looking in the monthly archives. %1$s', 'dhali' ), convert_smilies( ':)' ) ) . '</p>';
								the_widget( 'WP_Widget_Archives', 'dropdown=1', "after_title=</h2>$archive_content" );
							?>
						</div><!--.grid-col -->

						<div class="grid-col col-3-12">
							<?php the_widget( 'WP_Widget_Tag_Cloud' ); ?>
						</div><!--.grid-col -->
					</div><!--.grid -->

				</div><!-- .page-content -->
			</section><!-- .error-404 -->

		</main><!-- #main -->
	</div><!-- #primary -->
</div><!--.grid-pad -->
<?php get_footer(); ?>
