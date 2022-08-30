<?php

	$terms = get_terms( array(
		'taxonomy' => 'product_cat',
		'hide_empty' => false,
	) );

	if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {

		echo '<h4 class="divider-title align-center"><span>Products</span></h4>';
		echo '<ul class="list list-subnav">';
			foreach ( $terms as $term ) {
				echo '<li><a href="' . esc_url( get_term_link( $term ) ) . '">' . $term->name . '</a></li>';
			}
		echo '</ul>';
	}