<?php

//======================================================================
// Action Hooks
//======================================================================

/**
 * Declare WooCommerce support
 */
add_action( 'after_setup_theme', 'woocommerce_support' );
function woocommerce_support() {
	add_theme_support( 'woocommerce' );
}

/**
 * Remove Read More button, Add to Cart Buttons
 */
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );

/**
 * Remove Image Thumbnail
 */
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );

/**
 * Remove Product Sorting
 */
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );

/**
 * Remove Meta Information from Product Detail page
 */
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );

/*
 * Remove Cart totals from cart page
 */
remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cart_totals', 10 );

/*
 * Remove Category title from Category page
 */
remove_action( 'woocommerce_shop_loop_subcategory_title', 'woocommerce_template_loop_category_title', 10 );

/*
 * Remove Link to product detail page
 */
remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );

/*
 * woocommerce_after_cart_table
 *
 * Adds proceed to checkout
 */
add_action( 'woocommerce_after_cart_table', 'woocommerce_button_proceed_to_checkout', 10);

/*
 * woocommerce_after_shop_loop_item_title
 *
 * Adds product variations to shop category pages and search page
 */
add_action('woocommerce_after_shop_loop_item_title','dhali_single_variation', 5);
function dhali_single_variation() {
	global $product;
	if ( $product->product_type == "variable" && ( is_product_category() || is_product_tag() || is_search() ) ) {
		echo woocommerce_variable_add_to_cart();
	}
}

/*
 * woocommerce_archive_description
 *
 * Adds search form
 */
add_action( 'woocommerce_archive_description', 'dhali_product_search', 10);
function dhali_product_search() {
	echo '<div class="product-search-form">';
		echo '<h4 class="divider-title align-center"><span>Search</span></h4>';
		get_product_search_form();
	echo '</div>';
}

/*
 * woocommerce_before_shop_loop
 *
 * Adds grid to category pages
 */
add_action( 'woocommerce_before_shop_loop', 'dhali_product_grid_open', 10);
function dhali_product_grid_open() {
	if ( is_product_category() ) {

		// Gets Term Name
		$page_title = single_term_title( "", false );

		// Product Nav
		echo '<div class="grid"><div class="grid-col col-3-12">';
			get_template_part( 'inc/product', 'cat-nav' );
		echo '</div>';

		// Start Product Grid
		echo '<div class="grid-col col-9-12">';
		echo '<h4 class="divider-title align-center"><span>'. $page_title .'</span></h4>';
	}
}

/*
 * woocommerce_after_shop_loop
 *
 * Closes grid to category pages
 */
add_action( 'woocommerce_after_shop_loop', 'dhali_product_grid_close', 11);
function dhali_product_grid_close() {
	if ( is_product_category() ) {
		// End Product Grid
		echo '</div>';
	}
}

/*
 * woocommerce_shop_loop_item_title
 *
 * Change Product Title to include sku
 */
remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );
add_action( 'woocommerce_shop_loop_item_title', 'dhali_product_title', 10 );
function dhali_product_title() {
	global $product;
	$product_id = $product->id;

	$wc_product = new WC_Product($product_id);
	$product_sku = $wc_product->sku;

	//Get Terms
	$terms = get_the_terms( $product_id, 'product_cat' );

	foreach ( $terms as $term ) {
		$product_cat = $term->name;
	}

	echo '<div class="product-item-title"><div class="product-name">'. get_the_title() .'</div></div>';
	echo '<div class="product-labels"><span class="label-product label-default"># '. $product_sku .'</span> <span class="label-product label-product-cat">'. $product_cat .'</span></div>';

}

/*
 * pre_get_posts
 *
 * Change Query on Search Results to show all
 */
add_action( 'pre_get_posts', 'dhali_product_search_results', 1 );
function dhali_product_search_results( $query ) {
	if ( is_search() && is_archive( 'product' ) && $query->is_main_query() || is_tax('product_cat') && $query->is_main_query() ) {
		// Display all posts
		$query->set( 'posts_per_page', -1 );
		return;
	}
}

/*
 * woocommerce_before_shop_loop_item
 *
 * Adds wrapper div
 */
add_action( 'woocommerce_before_shop_loop_item', 'dhali_before_shop_loop_item', 10);
function dhali_before_shop_loop_item( $post ) {
	global $post;
	echo '<div id="productId-'. $post->ID .'"><div class="product-item">';
}

/*
 * woocommerce_after_shop_loop_item
 *
 * Closes wrapper div
 */
add_action( 'woocommerce_after_shop_loop_item', 'dhali_after_shop_loop_item', 10);
function dhali_after_shop_loop_item() {
	echo '</div></div>';
}

/*
 * woocommerce_before_add_to_cart_button
 *
 * Add product notes field
 */
add_action( 'woocommerce_before_add_to_cart_button', 'dhali_product_notes' );
function dhali_product_notes() {
		echo '<table class="variations variations-notes" cellspacing="0">
					<tbody>
						<tr><td class="label"><label for="color">Product Notes</label></td></tr>
						<tr><td class="value"><input type="text" name="product_notes" value="" /></td></tr>
					</tbody>
					</table>';
}

/*
 * woocommerce_add_cart_item_data
 *
 * Save product notes field
 */
add_action( 'woocommerce_add_cart_item_data', 'dhali_save_product_notes', 10, 2 );
function dhali_save_product_notes( $cart_item_data, $product_id ) {
		if( isset( $_REQUEST['product_notes'] ) ) {
				$cart_item_data[ 'product_notes' ] = $_REQUEST['product_notes'];
				/* below statement make sure every add to cart action as unique line item */
				$cart_item_data['unique_key'] = md5( microtime().rand() );
		}
		return $cart_item_data;
}

/*
 * woocommerce_get_item_data
 *
 * Show product notes field on Cart & Checkout
 */
add_filter( 'woocommerce_get_item_data', 'dhali_show_product_notes', 10, 2 );
function dhali_show_product_notes( $cart_data, $cart_item = null ) {
		$custom_items = array();
		/* Woo 2.4.2 updates */
		if( !empty( $cart_data ) ) {
			$custom_items = $cart_data;
		}	if( isset( $cart_item['product_notes'] ) ) {
			$custom_items[] = array( "name" => 'Product Notes', "value" => $cart_item['product_notes'] );
		}
		return $custom_items;
}

/*
 * woocommerce_add_order_item_meta
 *
 * Show product notes field on Review Order
 */
add_action( 'woocommerce_add_order_item_meta', 'dhali_product_notes_review', 1, 3 );
function dhali_product_notes_review( $item_id, $values, $cart_item_key ) {
	if( isset( $values['product_notes'] ) ) {
		wc_add_order_item_meta( $item_id, "product_notes", $values['product_notes'] );
	}
}

//======================================================================
// Filter Hooks
//======================================================================

/**
 * Removes Free label
*/
add_filter( 'woocommerce_variable_free_price_html', '__return_false' );
add_filter( 'woocommerce_free_price_html', '__return_false' );
add_filter( 'woocommerce_variation_free_price_html', '__return_false' );

/**
 * Removes Pricing from pages
 * https://businessbloomer.com/woocommerce-hide-prices-shop-category-pages/
*/
add_filter( 'woocommerce_variable_sale_price_html', '__return_false' );
add_filter( 'woocommerce_variable_price_html', '__return_false' );
add_filter( 'woocommerce_get_price_html', '__return_false' );

/**
 * Remove category count
*/
add_filter( 'woocommerce_subcategory_count_html' , '__return_false' );

/**
 * Remove page title
*/
add_filter( 'woocommerce_show_page_title' , '__return_false' );

/**
 * Remove product thumbnail from cart
 */
add_filter( 'woocommerce_cart_item_thumbnail', '__return_false' );

/**
 * Remove redirect to single product page when single search result is found
 */
add_filter( 'woocommerce_redirect_single_search_result', '__return_false' );

/**
 * Remove permalink on cart page
 */
add_filter( 'woocommerce_cart_item_permalink', '__return_false' );

/**
 * Remove permalink on view order page
 */
add_filter( 'woocommerce_order_item_permalink', '__return_false' );

/*
 * loop_shop_columns
 *
 * Change number or products per row
 */
add_filter('loop_shop_columns', 'dhali_loop_columns');
if ( !function_exists('dhali_loop_columns') ) {
	function dhali_loop_columns() {
		return ( is_product_category() ) ? 3 : 4;
	}
}

/*
 * woocommerce_quantity_input_args
 *
 * Default input value for quantity field
 */
add_filter( 'woocommerce_quantity_input_args', 'dhali_default_qty_value', 10, 2 );
function dhali_default_qty_value( $args, $product ) {
	if ( is_product_category() || is_search() && isset( $_POST['quantity'] ) ) {
		$args['input_value'] = 1;
	}
	return $args;
}

/*
 * woocommerce_related_products_args
 *
 * Clear the query arguments for related products so none show.
 */
add_filter('woocommerce_related_products_args','dhali_remove_related_products', 10);
function dhali_remove_related_products( $args ) {
	return array();
}

/*
 * woocommerce_is_purchasable
 *
 * WooCommerce Display 'Add to Cart' button on product without price
 */
add_filter( 'woocommerce_is_purchasable', 'dhali_woocommerce_is_purchasable', 10, 2 );
function dhali_woocommerce_is_purchasable( $purchasable, $product ){
	return true;
}

/*
 * woocommerce_my_account_my_orders_actions
 *
 * Adds the order-again button
 */
add_filter( 'woocommerce_my_account_my_orders_actions', 'dhali_order_again_button', 10, 2 );
function dhali_order_again_button( $actions, $order ) {
	if ( $order->has_status( 'completed' ) ) {

		$order_again_url = esc_url( wp_nonce_url( add_query_arg( 'order_again', $order->id ) , 'woocommerce-order_again' ) );

		$actions['order-again'] = array(
			'url'  => $order_again_url,
			'name' => __( 'Order Again', 'woocommerce' )
		);
	}
	return $actions;
}

/*
 * woocommerce_cart_item_name
 *
 * Add Sku to Cart Item Name
 */
add_filter( 'woocommerce_cart_item_name', 'dhali_cart_item_name', 10, 3 );
function dhali_cart_item_name( $name, $cart_item, $cart_item_key) {
	$product_sku = $cart_item['data']->get_sku();
	$product_id = $cart_item['product_id'];

	//Get Terms
	$terms = get_the_terms( $product_id, 'product_cat' );

	foreach ( $terms as $term ) {
		$product_cat = $term->name;
	}

	$name = $name.' <span class="label-product label-default"># '. $product_sku .'</span> <span class="label-product label-product-cat">'. $product_cat .'</span>';
	return $name;
}

/*
 * woocommerce_order_item_name
 *
 * Add Sku to Order Item Name
 */
add_filter( 'woocommerce_order_item_name', 'dhali_order_item_name', 10, 3 );
function dhali_order_item_name( $name, $item, $is_visible) {
	$product_sku = get_post_meta( $item[ 'product_id' ], '_sku', true );
	$product_id = $item['product_id'];

	//Get Terms
	$terms = get_the_terms( $product_id, 'product_cat' );

	foreach ( $terms as $term ) {
		$product_cat = $term->name;
	}

	$name = $name.' <span class="label-product label-default"># '. $product_sku .'</span> <span class="label-product label-product-cat">'. $product_cat .'</span>';
	return $name;
}

//======================================================================
// Functions
//======================================================================

/**
 * Remove Showing results functionality site-wide
 */
function woocommerce_result_count() {
	return;
}

/*
 * Add Multi-Product Post
 */
function dhali_add_multi_product() {
	global $woocommerce;

	//Create array to hold added to cart items
	$addedCartArray = array();

	//Collection form values
	foreach ($_POST as $key => $value) {
		$sku = str_replace('productId-','',$key);
		if($value > 0)
			{
				//php Associative Arrays > Only add items with quantity > 0
				$addedCartArray[] = array("productId"=>$sku,"quantity"=>$value);
			}
	}

	foreach ($addedCartArray as $singleCart) {
		$woocommerce->cart->add_to_cart( $singleCart['productId'], $singleCart['quantity'] );
	}

	wp_redirect( $woocommerce->cart->get_cart_url(), '301' );
	exit;

}