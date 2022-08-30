<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package dhali
 */

// add multi product on button click
if(isset($_POST['multi_product_add_to_cart'])){
	dhali_add_multi_product();
}

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
			<h1 class="page-title"><span><?php is_product() ? the_title() : woocommerce_page_title() ?></span></h1>
			<?php if ( function_exists('yoast_breadcrumb') )
				{yoast_breadcrumb('<div id="breadcrumbs" class="breadcrumbs" >','</div>');
			} ?>
		</header><!--.grid-col -->
	</div><!--.grid -->
</div><!--.site-featured-image -->

<div class="grid grid-pad">
	<div id="primary" class="content-area grid-col col-12-12">
		<main id="main" class="site-main" role="main">

			<?php woocommerce_content(); ?>

		</main><!-- #main -->
	</div><!-- #primary -->
</div><!--.grid-pad -->
<?php get_footer(); ?>