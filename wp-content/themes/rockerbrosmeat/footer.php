<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package dhali
 */

?>

	</div><!-- #content -->

	<footer id="colophon" class="site-footer" role="contentinfo">

		<div class="site-contact">
			<div class="grid grid-pad">
				<?php dynamic_sidebar( 'footer-items' ); ?>
			</div><!-- .grid -->
		</div><!-- .grid -->

		<div class="site-info leather-bg">
			<div class="grid grid-pad">
				<div class="grid-col col-12-12 align-center">&copy; <?php echo date("Y") ?> <?php bloginfo( 'name' ); ?> <?php printf( esc_html__( 'All Rights Reserved. Designed by %1$s.', 'dhali' ), '<a href="http://dhali.com/" rel="designer">Dhali.com</a>'); ?></div><!-- .grid-col -->
			</div><!-- .grid -->

		<div style='text-align:center;padding-bottom:20px;'>
		<?php
				if ( has_nav_menu( 'social' ) ) {
					wp_nav_menu(
						array(
							'theme_location'	=> 'social',
							'menu_id' 				=> 'social-menu',
							'container'				=> 'div',
							'menu_class'			=> 'list-social list',
							)
						);
				}
			?>
		</div>

		</div><!-- .site-info -->

	</footer><!-- #colophon -->

</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>