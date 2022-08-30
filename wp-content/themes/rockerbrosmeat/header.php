<?php
/**
 * The header for our theme.
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package dhali
 */

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<?php wp_head(); ?>
	<script>
	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

	  ga('create', 'UA-73739255-1', 'auto');
	  ga('send', 'pageview');

	</script>
</head>

<body <?php body_class(); ?>>
<div id="page" class="hfeed site <?php if(is_woocommerce()) {echo 'font-woocommerce';}?>">
	<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'dhali' ); ?></a>

	<header id="masthead" class="site-header" role="banner">

		<?php
				echo '<div class="site-utilities upper-case clearfix">';
					wp_nav_menu(
						array(
							'theme_location'	=> 'account',
							'menu_id' 				=> 'account-menu',
							'container'				=> 'div',
							'container_class'	=> 'grid',
							'menu_class'			=> 'list list-inline list-account list-clearfix float-right',
						)
					);
				echo '</div>';
		?>

		<?php
			if ( has_nav_menu( 'account' ) && is_user_logged_in() ) {
				/*
					account-menu menu was here
				*/
			}
		?>


		<div class="header-grid grid grid-pad" style='height:30px!important;min-height:30px!important;'>

			<div class="site-branding">
				<div class="site-title">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><img src="<?php echo get_template_directory_uri(); ?>/images/layout/rockerbros-logo.png" alt="<?php bloginfo( 'name' ); ?>"></a>
				</div><!-- .site-title -->
			</div><!-- .site-branding -->

		</div><!-- .header-grid -->

		<nav id="site-navigation" class="main-navigation leather-bg" role="navigation">
			<div class="navigation-grid grid grid-pad" <?php if((is_woocommerce()) || (get_the_title() == "Checkout") ) {
				echo 'style="height:40px;"';
			} ?>  >
			<?php 
			$title = get_the_title();
			$title_result = $title !== 'Checkout';
			if($title_result) { 
				if(!is_woocommerce()) : ?>
	
					<button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false"><?php esc_html_e( 'Menu', 'dhali' ); ?></button>
					<?php wp_nav_menu(
						array(
							'theme_location'	=> 'primary',
							'menu_id' 				=> 'primary-menu',
							'menu_class'			=> 'main-navigation-list',
							'container' => false,
							)
						);
					?>
				<?php endif;
			}
			 ?>
			</div><!-- .grid -->
		</nav><!-- #site-navigation -->

	</header><!-- #masthead -->

	<div id="content" class="site-content">
