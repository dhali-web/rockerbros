<?php
/**
 * dhali functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package dhali
 */

if ( ! function_exists( 'dhali_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function dhali_setup() {
	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on dhali, use a find and replace
	 * to change 'dhali' to the name of your theme in all the template files.
	 */
	load_theme_textdomain( 'dhali', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
	 */
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 2000, 1125, array( 'center', 'center')  );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => esc_html__( 'Primary Menu', 'dhali' ),
		'social' => esc_html__( 'Social Menu', 'dhali' ),
		'account' => esc_html__( 'Account Menu', 'dhali' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	) );

	/*
	 * Enable support for Post Formats.
	 * See https://developer.wordpress.org/themes/functionality/post-formats/
	 */
	add_theme_support( 'post-formats', array(
		'aside',
		'image',
		'video',
		'quote',
		'link',
	) );

	// Set up the WordPress core custom background feature.
	// add_theme_support( 'custom-background', apply_filters( 'dhali_custom_background_args', array(
	// 	'default-color' => 'ffffff',
	// 	'default-image' => '',
	// ) ) );
}
endif; // dhali_setup
add_action( 'after_setup_theme', 'dhali_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function dhali_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'dhali_content_width', 640 );
}
add_action( 'after_setup_theme', 'dhali_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function dhali_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'dhali' ),
		'id'            => 'sidebar-1',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h4 class="widget-title align-center"><span>',
		'after_title'   => '</span></h4>',
	) );
	register_sidebar( array(
		'name'          => esc_html__( 'Footer Items', 'dhali' ),
		'id'            => 'footer-items',
		'description'   => 'Appears on the footer',
		'before_widget' => '<div id="%1$s" class="widget %2$s grid-col col-1-3">',
		'after_widget'  => '</div>',
		'before_title'  => '',
		'after_title'   => '',
	) );
	register_sidebar( array(
		'name'          => esc_html__( 'Our Selection', 'dhali' ),
		'id'            => 'our-seletion',
		'description'   => 'Appears on the homepage',
		'before_widget' => '<div id="%1$s" class="widget %2$s grid-col col-1-4"><div class="selection-item clearfix">',
		'after_widget'  => '</div></div>',
		'before_title'  => '<h3 class="widget-title divider-title align-center h6"><span>',
		'after_title'   => '</span></h3>',
	) );
	register_sidebar( array(
		'name'          => esc_html__( 'Featured', 'dhali' ),
		'id'            => 'featured-items',
		'description'   => 'Appears on the homepage',
		'before_widget' => '<div id="%1$s" class="widget %2$s grid-col col-1-3">',
		'after_widget'  => '</div>',
		'before_title'  => '<h2 class="widget-title align-center h4">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'dhali_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function dhali_scripts() {
	wp_enqueue_style( 'dhali-style', get_stylesheet_uri() );
	wp_enqueue_style( 'simplegrid', get_template_directory_uri() . '/css/simplegrid.css' );
	wp_enqueue_style( 'fonts', 'https://fonts.googleapis.com/css?family=PT+Sans:400,400italic,700italic,700|Great+Vibes|Fjalla+One ' );
	wp_enqueue_style( 'custom-style', get_template_directory_uri() . '/css/styles.css' );

	wp_enqueue_script( 'dhali-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20120206', true );
	wp_enqueue_script( 'rb-scripts', get_template_directory_uri() . '/js/rb-scripts.js', array('jquery'), '', true );
	wp_enqueue_script( 'dhali-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20130115', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'dhali_scripts' );

/**
 * Implement the Custom Header feature.
 */
// require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';

/**
 * Load Simplegrid shortcode generator file.
 */
require get_template_directory() . '/inc/simplegrid.php';

/**
 * Woocommerce Customization
*  require_once( get_template_directory() . '/inc/rockerbros-woocommerce.php');
 */



/**
 * Gets the id of the topmost ancestor of the current page. Returns the current
 * page's id if there is no parent.
 */
if(!function_exists('get_post_top_ancestor_id')) {
function get_post_top_ancestor_id(){
		global $post;

		if( $post->post_parent ){
				$ancestors = array_reverse( get_post_ancestors( $post->ID ) );
				return $ancestors[0];
		}

		return $post->ID;
}}

/**
 * Force Crop on medium size image
 */
if ( false === get_option( "medium_crop" ) ) {
	add_option("medium_crop", "1");
} else {
	update_option("medium_crop", "1");
}

/**
 * Enable automatic updates for plugins
 */
add_filter('auto_update_plugin', '__return_true');

/* Woocommerce Product admin table -- Add Addition "Visibility Column" -- */

// add
add_filter( 'manage_edit-product_columns', 'dhali_total_visibility_1', 20 );
// populate
add_action( 'manage_posts_custom_column', 'dhali_total_visibility_2' );
 
function dhali_total_visibility_1( $col_th ) {
 
	// a little different way of adding new columns
	return wp_parse_args( array( 'visibility' => 'Visibility' ), $col_th );
 
}
 
function dhali_total_visibility_2( $column_id ) {
	global $product;
	if( $column_id  == 'visibility' )
		echo $product->get_catalog_visibility();
}

/**
 * Filter products by type
 *
 * @access public
 * @return void
 */
function dhali_filter_products_by_visibility_status() {

	global $typenow, $wp_query;

   if ($typenow=='product') :


	   // Featured/ Not Featured
	   $output .= "<select name='visibility_status' id='dropdown_visibility_status'>";
	   $output .= '<option value="">'.__( 'Show All Visibility Statuses', 'woocommerce' ).'</option>';

	   $output .="<option value='hidden' ";
	   if ( isset( $_GET['visibility_status'] ) ) $output .= selected('hidden', $_GET['visibility_status'], false);
	   $output .=">".__( 'Show Hidden Products', 'woocommerce' )."</option>";

	   $output .="<option value='visible' ";
	   if ( isset( $_GET['visibility_status'] ) ) $output .= selected('visible', $_GET['visibility_status'], false);
	   $output .=">".__( 'Show Visible Products', 'woocommerce' )."</option>";

	   $output .="</select>";

	   echo $output;
   endif;
}

add_action('restrict_manage_posts', 'dhali_filter_products_by_visibility_status');

/**
* Filter the products in admin based on options
*
* @access public
* @param mixed $query
* @return void
*/
function dhali_featured_products_admin_filter_query( $query ) {
    global $typenow;

    if ( $typenow == 'product' ) {

        // Subtypes
        if ( ! empty( $_GET['visibility_status'] ) ) {
            if ( $_GET['visibility_status'] == 'hidden' ) {
                $query->query_vars['tax_query'][] = array(
					'taxonomy' => 'product_visibility',
					'field'    => 'name',
					'terms'    => array( 'exclude-from-search', 'exclude-from-catalog' ),
					'operator' => 'AND',
                );
            } elseif ( $_GET['visibility_status'] == 'visible' ) {
                $query->query_vars['tax_query'][] = array(
					'taxonomy' => 'product_visibility',
					'field'    => 'name',
					'terms'    => array( 'exclude-from-search', 'exclude-from-catalog' ),
					'operator' => 'NOT IN',
                );
            }
        }

    }

}
add_filter( 'parse_query', 'dhali_featured_products_admin_filter_query' );

/* How to Add a Quantity Field to Shop Pages in WooCommerce */

function dhali_shop_page_add_quantity_field() {
	/** @var WC_Product $product */
	$product = wc_get_product( get_the_ID() );
	if ( ! $product->is_sold_individually() && 'variable' != $product->get_type() && $product->is_purchasable() && $product->is_in_stock() ) {
		woocommerce_quantity_input( array( 'min_value' => 1, 'max_value' => $product->backorders_allowed() ? '' : $product->get_stock_quantity() ) );
	}
}
add_action( 'woocommerce_after_shop_loop_item', 'dhali_shop_page_add_quantity_field', 1 );
/**
 * Add required JavaScript.
 */
function dhali_shop_page_quantity_add_to_cart_handler() {
	wc_enqueue_js( '
		$(".woocommerce .products").on("click", ".quantity input", function() {
			return false;
		});
		$(".woocommerce .products").on("change input", ".quantity .qty", function() {
			var add_to_cart_button = $(this).parents( ".product" ).find(".add_to_cart_button");
			// For AJAX add-to-cart actions
			add_to_cart_button.data("quantity", $(this).val());
			// For non-AJAX add-to-cart actions
			add_to_cart_button.attr("href", "?add-to-cart=" + add_to_cart_button.attr("data-product_id") + "&quantity=" + $(this).val());
		});
		// Trigger on Enter press
		$(".woocommerce .products").on("keypress", ".quantity .qty", function(e) {
			if ((e.which||e.keyCode) === 13) {
				$( this ).parents(".product").find(".add_to_cart_button").trigger("click");
			}
		});
	' );
}
add_action( 'init', 'dhali_shop_page_quantity_add_to_cart_handler' );


// Removes Order Notes Title - Additional Information & Notes Field
add_filter( 'woocommerce_enable_order_notes_field', '__return_false', 9999 );



// Remove Order Notes Field
add_filter( 'woocommerce_checkout_fields' , 'remove_order_notes' );

function remove_order_notes( $fields ) {
     unset($fields['order']['order_comments']);
     return $fields;
}






// Conditional Show hide checkout fields based on chosen shipping methods
//add_action( 'wp_footer', 'conditionally_hidding_pickuptime' );
//function conditionally_hidding_pickuptime(){
    // Only on checkout page
/*    if( ! is_checkout() ) return;
 
    // The shipping methods rate ID "Local Pickup"
    $no_delivery = 'local_pickup:3';
    $no_delivery2 = 'local_pickup:7';
    $chosen_methods = WC()->session->get( 'chosen_shipping_methods' );
    $chosen_shipping = $chosen_methods[0];
    ?>
    <script>
        jQuery(function($){
            // Choosen shipping method selectors slug
            var shipMethod = 'input[name^="shipping_method"]',
                shipMethodChecked = shipMethod+':checked';
 
            // Function that shows or hide imput select fields
            function showHide( actionToDo='show', selector='' ){
                if( actionToDo == 'show' )
                    $(selector).show( 200, function(){
                        $(this).addClass("validate-required");
                    });
                else
                    $(selector).hide( 200, function(){
                        $(this).removeClass("validate-required");
                    });
            }
 
            // Initialising: Hide if choosen shipping method is "Local Pickup"
            <?php if ( 0 === strpos( $chosen_shipping, 'local_pickup' ) ) { ?>
            showHide('show','#local-pickup-time-select' );
            <?php  
   } 
            else{
            ?>
            showHide('hide','#local-pickup-time-select' );  
            <?php
            }
            ?>
      
 
            // Live event (When shipping method is changed)
            $( 'form.checkout' ).on( 'change', shipMethod, function() {
                if( $(shipMethodChecked).val() == '<?php echo $no_delivery; ?>' ||$(shipMethodChecked).val() == '<?php echo $no_delivery2; ?>' ){
                    showHide('show','#local-pickup-time-select' );
                }
                else{
                    showHide('hide','#local-pickup-time-select' );
                }
            });
        });
    </script>
    <?php
}
 
add_action( 'woocommerce_after_checkout_validation', 'a4m_validate_pickup', 10, 2);
 
function a4m_validate_pickup( $fields, $errors ){
    $chosen_methods = WC()->session->get( 'chosen_shipping_methods' );
    $chosen_shipping = $chosen_methods[0];
 if ( 0 === strpos( $chosen_shipping, 'local_pickup' ) ) {
    if ( empty($_POST[ 'local_pickup_time_select' ] ) ){
        $errors->add( 'validation', 'Please Select a pickup time.' );
    }
 }
}
*/
//end of it//

add_filter( 'gettext', 'woocomerce_text_strings', 20, 3 );        
function woocomerce_text_strings( $translated_text, $text, $domain ) {            
       switch ( $translated_text ) {            
            case 'shipping calculator' :        
				$translated_text = __( 'Calculate Shipping', 'woocommerce' );    
			case 'Enter a different address' :        
				$translated_text = __( 'Click here to activate shipping', 'woocommerce' );    
                break;    
        }        
        return $translated_text;             
}     