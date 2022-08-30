<?php
/**
 * Default widget template.
 *
 * Copy this template to /simple-image-widget/widget.php in your theme or
 * child theme to make edits.
 *
 * @package   SimpleImageWidget
 * @copyright Copyright (c) 2015 Cedaro, LLC
 * @license   GPL-2.0+
 * @since     4.0.0
 */
?>

<?php if ( ! empty( $image_id ) ) : ?>
	<div class="simple-image-wrap">
		<div class="simple-image">
			<?php
			echo $link_open;
			echo wp_get_attachment_image( $image_id, $image_size );
			echo $link_close;
			?>
			<?php
			if ( ! empty( $title ) ) :
				echo $before_title . $link_open . $title . $after_title . $link_close;
			endif;
			?>
		</div>
	</div>
<?php endif; ?>

<?php
if ( ! empty( $text ) ) :
	echo wpautop( $text );
endif;
?>

<?php if ( ! empty( $link_text ) ) : ?>
	<p class="more">
		<?php
		echo $text_link_open;
		echo $link_text;
		echo $text_link_close;
		?>
	</p>
<?php endif; ?>
