<?php

require_once CODEROCKZ_WOO_DELIVERY_DIR . 'includes/class-coderockz-woo-delivery-helper.php';

if( !class_exists( 'Coderockz_Woo_Delivery_Pickup_Option' ) ) {
	
	class Coderockz_Woo_Delivery_Pickup_Option {
		
		public static function pickup_time_option($pickup_time_settings,$meta_box=null) {
			
			$helper = new Coderockz_Woo_Delivery_Helper();
			$timezone = $helper->get_the_timezone();
			date_default_timezone_set($timezone);

			$currency_symbol = get_woocommerce_currency_symbol();
			
			$start = (isset($pickup_time_settings['pickup_time_starts']) && !empty($pickup_time_settings['pickup_time_starts'])) ? $pickup_time_settings['pickup_time_starts'] : "0";
			$end = (isset($pickup_time_settings['pickup_time_ends']) && !empty($pickup_time_settings['pickup_time_ends'])) ? $pickup_time_settings['pickup_time_ends'] : "1440";
			$time_slot = (isset($pickup_time_settings['each_time_slot']) && !empty($pickup_time_settings['each_time_slot'])) ? $pickup_time_settings['each_time_slot'] : (int)$end-(int)$start;


			$delivery_fee_settings = get_option('coderockz_woo_delivery_fee_settings');

			$enable_pickup_slot_fee = (isset($delivery_fee_settings['enable_pickup_slot_fee']) && !empty($delivery_fee_settings['enable_pickup_slot_fee'])) ? $delivery_fee_settings['enable_pickup_slot_fee'] : false;


			$time_format = (isset($pickup_time_settings['time_format']) && !empty($pickup_time_settings['time_format'])) ? $pickup_time_settings['time_format'] : "12";
			if($time_format == 12) {
				$time_format = "h:i A";
			}
			elseif ($time_format == 24) {
				$time_format = "H:i";
			}

			$result = [];
			$it = $end;
			if(($end-$start)%$time_slot !=0){
				$remaining_time = ($end-$start)%$time_slot;
				$it = $end-$remaining_time;
				$fractional_from_hour = date($time_format, mktime(0, $it));
				if($time_format == "H:i" && $end == 1440){
					$fractional_to_hour = "24:00";
				} else {
					$fractional_to_hour = date($time_format, mktime(0, $end));
				}
				$result[date("H:i", mktime(0, (int)$it)) . ' - ' . date("H:i", mktime(0, (int)$end))] = $fractional_from_hour . ' - ' . $fractional_to_hour;
							
			}
			while($it > $start) {
				$to = $it;
				$from = $it - $time_slot;
				$from_hour = date($time_format, mktime(0, $from));
				if($time_format == "H:i" && $to == 1440){
					$to_hour = "24:00";
				} else {
					$to_hour = date($time_format, mktime(0, $to));
				}
				$result[date("H:i", mktime(0, (int)$from)) . ' - ' . date("H:i", mktime(0, (int)$to))] = $from_hour . ' - ' . $to_hour;
				
				$it = $from;
			}
			$new_result = [];
			$custom_result = [];
			if(is_null($meta_box)){
				$result[''] = '';
				$new_result[''] = '';
				$custom_result[''] = '';
			}
			
			$result = array_reverse($result);
			

			
			$custom_time_slot_settings = get_option('coderockz_woo_delivery_pickup_slot_settings');
			$enable_custom_time_slot_pickup = (isset($custom_time_slot_settings['enable_custom_pickup_slot']) && !empty($custom_time_slot_settings['enable_custom_pickup_slot'])) ? $custom_time_slot_settings['enable_custom_pickup_slot'] : false;

			if($enable_custom_time_slot_pickup) {
				if(isset($custom_time_slot_settings['time_slot']) && count($custom_time_slot_settings['time_slot'])>0){

					if(is_null($meta_box)){
						$checkout_product_categories = $helper->checkout_product_categories(true);
						$checkout_products = $helper->checkout_product_id();
					} else {
						global $post;
						$order = wc_get_order( $post->ID );
						$order_items = $order->get_items();
						$checkout_product_categories = $helper->order_product_categories($order_items,true);
						$checkout_products = $helper->order_product_id($order_items);
					}

					$helper = new Coderockz_Woo_Delivery_Helper();
					$sorted_custom_slot = $helper->array_sort_by_column($custom_time_slot_settings['time_slot'],'start');

			  		foreach($sorted_custom_slot as $individual_time_slot) {

			  			if($individual_time_slot['enable']) {

			  				$timeslot_hide_categories = [];

			  				$timeslot_hide_categories_array = (isset($individual_time_slot['hide_categories']) && !empty($individual_time_slot['hide_categories'])) ? $individual_time_slot['hide_categories'] : array();

			  				foreach($timeslot_hide_categories_array as $timeslot_hide_category) {
			  					$timeslot_hide_categories [] = stripslashes($timeslot_hide_category);
			  				}


			  				$timeslot_hide_products = (isset($individual_time_slot['hide_products']) && !empty($individual_time_slot['hide_products'])) ? $individual_time_slot['hide_products'] : array();

			  				$hide_categories_condition = (count(array_intersect($checkout_product_categories, $timeslot_hide_categories)) <= count($checkout_product_categories)) && count(array_intersect($checkout_product_categories, $timeslot_hide_categories))>0;
  			
				  			$hide_products_condition = (count(array_intersect($checkout_products, $timeslot_hide_products)) <= count($checkout_products)) && count(array_intersect($checkout_products, $timeslot_hide_products))>0;

							if($hide_categories_condition) {
								$hide_for_product_categories = true;
								if($individual_time_slot['time_slot_shown_other_categories_products'] && count($checkout_product_categories) > 1 && count($checkout_products) > 1 && count(array_diff($checkout_product_categories, $timeslot_hide_categories))>0) {
					  				$hide_for_product_categories = !$hide_for_product_categories;
					  			}
							} elseif($hide_products_condition) {
								$hide_for_product_categories = true;
								if($individual_time_slot['time_slot_shown_other_categories_products'] && count($checkout_products) > 1 && count(array_diff($checkout_products, $timeslot_hide_products))>0) {
					  				$hide_for_product_categories = !$hide_for_product_categories;
					  			}
							} else {
								$hide_for_product_categories = false;
							}

				  			if(!$hide_for_product_categories) {

			  				$temp_custom_result = [];
			  				/*$fee = (isset($individual_time_slot['fee']) && $individual_time_slot['fee'] != "") ? " (+".$currency_symbol.$individual_time_slot['fee'].")" :"";*/
			  				if(class_exists('WOOCS_STARTER')){
								global $WOOCS;
                            	$currencies=$WOOCS->get_currencies();
                            	$currency_symbol = $currencies[$WOOCS->current_currency]['symbol'];
								$fee = (isset($individual_time_slot['fee']) && $individual_time_slot['fee'] != "") ? " (+".$helper->postion_currency_symbol($currency_symbol,apply_filters('woocs_exchange_value', $individual_time_slot['fee'])).")" :"";
							} else {
								$fee = (isset($individual_time_slot['fee']) && $individual_time_slot['fee'] != "") ? " (+".$helper->postion_currency_symbol($currency_symbol,$individual_time_slot['fee']).")" :"";
							}
			  				$from = $individual_time_slot["start"];
			  				$to = $individual_time_slot["end"];

			  				if($individual_time_slot['enable_split']) {
			  					$split_time_slot = (isset($individual_time_slot['split_slot_duration']) && !empty($individual_time_slot['split_slot_duration'])) ? $individual_time_slot['split_slot_duration'] : "";
			  					
			  					
				  					/*if($individual_time_slot['enable_single_splited_slot']) {
										$temp_custom_result["$to,"] = date($time_format, mktime(0, $to)) . $fee;
									}*/

									$it = $to;
									if(($to-$from)%$split_time_slot !=0){

										$remaining_time = ($to-$from)%$split_time_slot;
										$it = $to-$remaining_time;
										$fractional_from_hour = date($time_format, mktime(0, $it));
										
										if($time_format == "H:i" && $to == 1440){
											$fractional_to_hour = "24:00";
										} else {
											$fractional_to_hour = date($time_format, mktime(0, $to));
										}

										if($individual_time_slot['enable_single_splited_slot']) {
											$temp_custom_result[date("H:i", mktime(0, (int)$it))] = $fractional_from_hour . $fee;
										} else {

											$temp_custom_result[date("H:i", mktime(0, (int)$it)) . ' - ' . date("H:i", mktime(0, (int)$to))] = $fractional_from_hour . ' - ' . $fractional_to_hour . $fee;
										}

										
													
									}
									while($it > $from) {
										$end = $it;
										$start = $it - $split_time_slot;
										$from_hour = date($time_format, mktime(0, $start));
										if($time_format == "H:i" && $end == 1440){
											$to_hour = "24:00";
										} else {
											$to_hour = date($time_format, mktime(0, $end));
										}
										if($individual_time_slot['enable_single_splited_slot']) {
											$temp_custom_result[date("H:i", mktime(0, (int)$start))] = $from_hour . $fee;
										} else {
											$temp_custom_result[date("H:i", mktime(0, (int)$start)) . ' - ' . date("H:i", mktime(0, (int)$end))] = $from_hour . ' - ' . $to_hour . $fee;
										}
										$it = $start;

									}	 

									$temp_custom_result = array_reverse($temp_custom_result);
									$custom_result = array_merge($custom_result,$temp_custom_result);

							
			  				} else {

			  					if($individual_time_slot['enable_single_slot']) {
									$custom_result[date("H:i", mktime(0, (int)$individual_time_slot['start']))] = date($time_format, mktime(0, $individual_time_slot['start'])) . $fee;
								} else {

									if($time_format == "H:i" && $to == 1440){
										$custom_result[date("H:i", mktime(0, (int)$individual_time_slot['start'])) . ' - ' . date("H:i", mktime(0, (int)$individual_time_slot['end']))] = date($time_format, mktime(0, $individual_time_slot['start'])) . ' - ' . "24:00" . $fee;
									} else {
										$custom_result[date("H:i", mktime(0, (int)$individual_time_slot['start'])) . ' - ' . date("H:i", mktime(0, (int)$individual_time_slot['end']))] = date($time_format, mktime(0, $individual_time_slot['start'])) . ' - ' . date($time_format, mktime(0, $individual_time_slot['end'])) . $fee;
									}
									
								}

			  				}

			  			}

			  			}

			  		}
			  	}

			  	ksort($custom_result);
			  	return $custom_result;
			}

			if($enable_pickup_slot_fee) {

				$pickup_slot_fees = $delivery_fee_settings['pickup_slot_fee'];

				foreach($result as $key => $value) {
					/*$slot_key = implode('-', explode(',', $key));*/

					$slot_key = explode(' - ', $key);
					$slot_key_one = explode(':', $slot_key[0]);
					$slot_key_two = explode(':', $slot_key[1]);
					$slot_key = ((int)$slot_key_one[0]*60+(int)$slot_key_one[1]).'-'.((int)$slot_key_two[0]*60+(int)$slot_key_two[1]);

					if(isset($pickup_slot_fees[$slot_key])) {
						if($pickup_slot_fees[$slot_key] > 0) {

							if(class_exists('WOOCS_STARTER')){
								global $WOOCS;
                            	$currencies=$WOOCS->get_currencies();
                            	$currency_symbol = $currencies[$WOOCS->current_currency]['symbol'];
								$fee = " (+".$helper->postion_currency_symbol($currency_symbol,apply_filters('woocs_exchange_value', $pickup_slot_fees[$slot_key])).")";
							} else {
								$fee = " (+". $helper->postion_currency_symbol($currency_symbol,$pickup_slot_fees[$slot_key]) . ")";
							}


							$value = $value . $fee;
						}
					}
					$new_result[$key] = $value;
				}
				return $new_result;
			}
			return $result;
		}
	}
}