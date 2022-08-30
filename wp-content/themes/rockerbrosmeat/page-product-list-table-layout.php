<?php
/**
 * Template Name: Full Width Page
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package dhali
 */

if ( ! is_user_logged_in() ) {
	return;
}

// Clear cart and add multi product on button click
if(isset($_POST['multi_product_add_to_cart'])){
	dhali_add_multi_product();
}

get_header();

?>

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

		<?php get_product_search_form(); ?>

		<?php echo get_search_query(); ?>

			<?php

				$terms = get_terms( array(
					'taxonomy'		=> 'product_cat',
					'orderby'			=> 'name',
					'order'				=> 'ASC',
					'hide_empty'	=> false,
				) );

				foreach ( $terms as $term ) :

					echo '<h2 class="divider-title"><span>'. $term->name .'</span></h2>';

					$args = array(
						'posts_per_page'	=> -1,
						'post_type'				=> 'product',
						'product_cat'			=> $term->slug,
						'orderby'					=> 'title',
						'order'						=> 'ASC',
					);

					$products = get_posts( $args ); ?>

			<form action="" class="cart" method="post" enctype="multipart/form-data">

				<table class="table-product-items">
					<thead>
						<tr>
							<th width="5%">SKU</th>
							<th width="80%">Name</th>
							<th width="15%">Qty</th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ( $products as $post ) : setup_postdata( $post ); ?>

					<tr id="productId-<?php echo $post->ID ?>" class="product-item">
						<td><?php echo $product->get_sku(); ?></td>
						<td><?php the_title(); ?></td>
						<td><?php dhali_product_quantity(); ?></td>
					</tr>

					<?php endforeach; wp_reset_postdata(); ?>
					</tbody>
				</table>

				<?php endforeach; ?>

				<button type="submit" name="multi_product_add_to_cart" class="single_add_to_cart_button button alt">Add to cart</button>
			</form>

		</main><!-- #main -->
	</div><!-- #primary -->
</div><!--.grid-pad -->

<?php get_footer(); ?>