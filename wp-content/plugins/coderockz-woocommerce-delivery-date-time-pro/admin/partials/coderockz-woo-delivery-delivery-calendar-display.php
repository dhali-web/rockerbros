<?php

$delivery_types = [];
$all_placeholder = __('All','coderockz-woo-delivery');
$delivery_placeholder = __('Delivery','coderockz-woo-delivery');
$pickup_placeholder = __('Pickup','coderockz-woo-delivery');
$delivery_types[$all_placeholder] = 'all';
$delivery_types[$delivery_placeholder] = 'delivery';
$delivery_types[$pickup_placeholder] = 'pickup';

$order_status_keys = array_keys(wc_get_order_statuses());
$order_status = ['partially-paid'];
foreach($order_status_keys as $order_status_key) {
	$order_status[] = substr($order_status_key,3);
}
$order_status = array_diff($order_status,['cancelled','failed','refunded']);
$order_status = implode(",",$order_status);

$other_settings = get_option('coderockz_woo_delivery_other_settings');
$spinner_animation_id = (isset($other_settings['spinner-animation-id']) && !empty($other_settings['spinner-animation-id'])) ? $other_settings['spinner-animation-id'] : "";

if($spinner_animation_id != "") {

	$spinner_url = wp_get_attachment_image_src($spinner_animation_id,'full', true);
	$full_size_spinner_animation_path = $spinner_url[0];
} else {
	$full_size_spinner_animation_path = CODEROCKZ_WOO_DELIVERY_URL.'public/images/loading.gif';
}

$spinner_animation_background = (isset($other_settings['spinner_animation_background']) && !empty($other_settings['spinner_animation_background'])) ? $this->helper->hex2rgb($other_settings['spinner_animation_background']) : array('red' => 31, 'green' => 158, 'blue' => 96);

echo "<div data-animation_background='".json_encode($spinner_animation_background)."' data-animation_path='".$full_size_spinner_animation_path."' id='coderockz_woo_delivery_calendar_filter_section'>";
?>

	<div id="coderockz_woo_delivery_calendar_filter">
		<span class="dashicons dashicons-filter" style="color: #bbb;vertical-align: middle;marging-right: 20px;margin-left: 5px;"></span>
		<select data-delivery_type_filter_text="<?php _e('Filter by Delivery Type', 'coderockz-woo-delivery'); ?>" id="coderockz_woo_delivery_calendar_delivery_type_filter">
			<option value=""></option>
			<?php foreach ( $delivery_types as $label => $delivery_type ) : ?>
				<option value="<?php echo esc_attr( $delivery_type ); ?>">
					<?php echo esc_html( $label ); ?>
				</option>
			<?php endforeach; ?>
		</select>
		<select data-filter_type_filter_text="<?php _e('Filter by Order/Products', 'coderockz-woo-delivery'); ?>" id="coderockz_woo_delivery_calendar_filter_type_filter">
			<option value=""></option>
			<option value="order_type"><?php _e('Order', 'coderockz-woo-delivery'); ?></option>
			<option value="product"><?php _e('Product', 'coderockz-woo-delivery'); ?></option>
		</select>
		<select data-order_status_filter_text="<?php _e('Filter by Order Status', 'coderockz-woo-delivery'); ?>" data-order_status="<?php echo $order_status; ?>" id="coderockz_woo_delivery_calendar_order_status_filter" class="coderockz_woo_delivery_calendar_order_status_filter" multiple>
			<?php 
			foreach(wc_get_order_statuses() as $key => $value) {
				echo "<option value='".substr($key, 3)."'>".$value."</option>";
			}
			?>
			<option value="partially-paid">Partially Paid</option>
			<!-- <option value="active">Subscription Active</option>
			<option value="expired">Subscription Expired</option>
			<option value="pending-cancel">Subscription Pending Cancellation</option> -->
		</select>
	</div>
	<div id='coderockz-woo-delivery-delivery-calendar'>
		
	</div>
</div>