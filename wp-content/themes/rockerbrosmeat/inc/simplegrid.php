<?php

/**
 * This page generates the simplegrid shortcode
 * Example Shortcode below.
 *
 *	[grid]
 *		[column size="4"][/column]
 *		[column size="8"][/column]
 *	[/grid]
 *
 * Taken from following example
 * http://premium.wpmudev.org/blog/create-content-grids-wordpress/
 */

/**
 * Removes <br> <p> tags for shortcodes.
 * http://charlesforster.com/shortcodes-and-line-breaks-in-wordpress/
 */
function parse_shortcode_content( $content ) {

	/* Parse nested shortcodes and add formatting. */
	$content = trim( wpautop( do_shortcode( $content ) ) );

	/* Remove '</p>' from the start of the string. */
	if ( substr( $content, 0, 4 ) == '</p>' )
		$content = substr( $content, 4 );

	/* Remove '<p>' from the end of the string. */
	if ( substr( $content, -3, 3 ) == '<p>' )
		$content = substr( $content, 0, -3 );

	/* Remove any instances of '<p></p>'. */
	$content = str_replace( array( '<p></p>' ), '', $content );

	return $content;
}

function grid( $atts, $content ) {
	$content = parse_shortcode_content( $content );
	return '<div class="grid grid-pad">' . do_shortcode( $content ) . '</div>';
}
add_shortcode('grid', 'grid');

function grid_col( $atts, $content ) {
	$atts = shortcode_atts( array(
	'size' => 12 // Grid Column Defaults to 12 if no size is set.
	), $atts );

	extract( $atts );
	$column_size = 'col-'. $size .'-12';
	return '<div class="grid-col ' . $column_size . '">' . do_shortcode( $content ) . '</div>';
}
add_shortcode( 'column', 'grid_col');