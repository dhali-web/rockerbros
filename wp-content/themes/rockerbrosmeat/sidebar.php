<?php
/**
 * The sidebar containing the main widget area.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package dhali
 */

if ( ! is_active_sidebar( 'sidebar-1' ) ) {
	return;
}
?>

<div id="secondary" class="widget-area site-sidebar grid-col col-3-12" role="complementary">

<ul class="list list-subnav">
	<?php wp_list_pages(
		array(
		'title_li'		=> '',
		'sort_column'	=> 'menu_order',
		'sort_order'	=> 'ASC',
		'child_of'		=> get_post_top_ancestor_id())
		);
	?>
</ul>

	<?php dynamic_sidebar( 'sidebar-1' ); ?>
</div><!-- #secondary -->
