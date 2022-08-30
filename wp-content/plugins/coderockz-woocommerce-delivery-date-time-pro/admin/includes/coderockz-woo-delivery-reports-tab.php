<?php
	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\IOFactory;
	use PhpOffice\PhpSpreadsheet\Style\Alignment;
	use PhpOffice\PhpSpreadsheet\Style\Fill;
	if ( isset($_REQUEST['_wpnonce']) && wp_verify_nonce($_REQUEST['_wpnonce'], 'coderockz_woo_delivery_nonce' ) && isset($_POST['coderockz-woo-delivery-export-excel-quantity-btn']) ) {

		$helper = new Coderockz_Woo_Delivery_Helper();

		$filtered_date = sanitize_text_field($_POST['coderockz-woo-delivery-export-excel-quantity-date']);
    	$filtered_delivery_type = sanitize_text_field($_POST[ 'coderockz-woo-delivery-export-excel-quantity-type' ]);
    	$filtered_order_status = $helper->coderockz_woo_delivery_array_sanitize(explode(",",$_POST[ 'coderockz-woo-delivery-export-excel-quantity-order-status' ]));
    	$timezone = $helper->get_the_timezone();
		date_default_timezone_set($timezone);
		if(strpos($filtered_date, ' - ') !== false) {
			$filtered_dates = explode(' - ', $filtered_date);
			$orders = [];
			$delivery_orders = [];
			$pickup_orders = [];
			$period = new DatePeriod(new DateTime($filtered_dates[0]), new DateInterval('P1D'), new DateTime($filtered_dates[1].' +1 day'));
			foreach ($period as $date) {
		        $dates[] = $date->format("Y-m-d");
		    }
			
		    
		    foreach ($dates as $date) {
		    	if($filtered_delivery_type == "delivery"){
		    		$args = array(
				        'limit' => -1,
				        'delivery_date' => date("Y-m-d", strtotime($date)),
				        'status' => $filtered_order_status,
				        'delivery_type' => "delivery",
				    );
				    $orders_array = wc_get_orders( $args );
				    foreach ($orders_array as $order) {
				    	$orders[] = $order;
				    }
		    	} elseif($filtered_delivery_type == "pickup") {
		    		$args = array(
				        'limit' => -1,
				        'pickup_date' => date("Y-m-d", strtotime($date)),
				        'status' => $filtered_order_status,
				        'delivery_type' => "pickup",
				    );
				    $orders_array = wc_get_orders( $args );
				    foreach ($orders_array as $order) {
				    	$orders[] = $order;
				    }
		    	} else {
		    		$args = array(
				        'limit' => -1,
				        'delivery_date' => date("Y-m-d", strtotime($date)),
				        'status' => $filtered_order_status,
				    );

				    $orders_array = wc_get_orders( $args );
				    foreach ($orders_array as $order) {
				    	$delivery_orders[] = $order;
				    }

				    $args = array(
				        'limit' => -1,
				        'pickup_date' => date("Y-m-d", strtotime($date)),
				        'status' => $filtered_order_status,
				    );

				    $orders_array = wc_get_orders( $args );
				    foreach ($orders_array as $order) {
				    	$pickup_orders[] = $order;
				    }

				    $orders = array_merge($delivery_orders, $pickup_orders);
		    	}
		    	
			    
		    }
			

		} else {

		    if($filtered_delivery_type == "delivery"){
	    		$args = array(
			        'limit' => -1,
			        'delivery_date' => date('Y-m-d', strtotime($filtered_date)),
			        'status' => $filtered_order_status,
			        'delivery_type' => "delivery",
			    );
			    $orders = wc_get_orders( $args );
	    	} elseif($filtered_delivery_type == "pickup") {
	    		$args = array(
			        'limit' => -1,
			        'pickup_date' => date('Y-m-d', strtotime($filtered_date)),
			        'status' => $filtered_order_status,
			        'delivery_type' => "pickup",
			    );
			    $orders = wc_get_orders( $args );
	    	} else {
	    		$args = array(
			        'limit' => -1,
			        'delivery_date' => date('Y-m-d', strtotime($filtered_date)),
			        'status' => $filtered_order_status,
			    );
			    $delivery_orders = wc_get_orders( $args );

			    $args = array(
			        'limit' => -1,
			        'pickup_date' => date('Y-m-d', strtotime($filtered_date)),
			        'status' => $filtered_order_status,
			    );
			    $pickup_orders = wc_get_orders( $args );

			    $orders = array_merge($delivery_orders, $pickup_orders);
	    	}
		    
		}

		$tableHead = [
			'font'=>[
				'color'=>[
					'rgb'=>'FFFFFF'
				],
				'bold'=>true,
				'size'=>10
			],
			'fill'=>[
				'fillType' => Fill::FILL_SOLID,
				'startColor' => [
					'rgb' => '538ED5'
				]
			],
		];
		//even row
		$evenRow = [
			'fill'=>[
				'fillType' => Fill::FILL_SOLID,
				'startColor' => [
					'rgb' => 'ffffff'
				]
			]
		];
		//odd row
		$oddRow = [
			'fill'=>[
				'fillType' => Fill::FILL_SOLID,
				'startColor' => [
					'rgb' => 'eeeeee'
				]
			]
		];

		//make a new spreadsheet object
		$spreadsheet = new Spreadsheet();
		//get current active sheet (first sheet)
		$sheet = $spreadsheet->getActiveSheet();

		//set default font
		$spreadsheet->getDefaultStyle()
			->getFont()
			->setName('Arial')
			->setSize(10);

		$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(40);
		$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(10);

		$spreadsheet->getActiveSheet()
			->setCellValue('A1',__("Product Name","coderockz-woo-delivery"))
			->setCellValue('B1',__("Quantity","coderockz-woo-delivery"));

		$spreadsheet->getActiveSheet()->getStyle('A1:B1')->applyFromArray($tableHead);

		$row=2;
		$product_name = [];
		$product_quantity = [];
		foreach($orders as $order) {
			foreach ( $order->get_items() as $item_id => $item ) {
			   if($item->get_variation_id() == 0) {
			   		if(array_key_exists($item->get_product_id(),$product_quantity)) {
				   		$product_quantity[$item->get_product_id()] = $product_quantity[$item->get_product_id()]+$item->get_quantity();
				   } else {
				   		$product_quantity[$item->get_product_id()] = $item->get_quantity();
				   }
				   if(!array_key_exists($item->get_product_id(),$product_name)) {
				   		$product_name[$item->get_product_id()] = $item->get_name();
				   }
			   } else {
			   		if(array_key_exists($item->get_variation_id(),$product_quantity)) {
				   		$product_quantity[$item->get_variation_id()] = $product_quantity[$item->get_variation_id()]+$item->get_quantity();
				   } else {
				   		$product_quantity[$item->get_variation_id()] = $item->get_quantity();
				   }
				   if(!array_key_exists($item->get_variation_id(),$product_name)) {

					   	$variation = new WC_Product_Variation($item->get_variation_id());
						$product_name[$item->get_variation_id()] = $variation->get_title()." - ".implode(", ", $variation->get_variation_attributes());
				   }
			   }

			}
		}

		foreach ($product_name as $id => $name) {
			
			$spreadsheet->getActiveSheet()
				->setCellValue('A'.$row , $name)
				->setCellValue('B'.$row , $product_quantity[$id]);
			
			if( $row % 2 == 0 ){

				$spreadsheet->getActiveSheet()->getStyle('A'.$row.':B'.$row)->applyFromArray($evenRow)->getAlignment()->setWrapText(true)->setVertical(Alignment::VERTICAL_CENTER);
			}else{

				$spreadsheet->getActiveSheet()->getStyle('A'.$row.':B'.$row)->applyFromArray($oddRow)->getAlignment()->setWrapText(true)->setVertical(Alignment::VERTICAL_CENTER);
			}
			
			$row++;
		}
		$firstRow=1;
		$lastRow=$row-1;
 		$filename = "quantity_sheet(".$filtered_date.").xlsx";
 		ob_end_clean();
		header('Content-disposition: attachment; filename="'.$filename.'"');
		header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
		header('Content-Transfer-Encoding: binary');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		ob_clean();
		flush();

 		$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
		$writer->save('php://output');
		exit;

	}

	if ( isset($_REQUEST['_wpnonce']) && wp_verify_nonce($_REQUEST['_wpnonce'], 'coderockz_woo_delivery_nonce' ) && isset($_POST['coderockz-woo-delivery-export-excel-btn']) ) {

		$helper = new Coderockz_Woo_Delivery_Helper();

		$filtered_date = sanitize_text_field($_POST['coderockz-woo-delivery-export-excel-date']);
    	$filtered_delivery_type = sanitize_text_field($_POST[ 'coderockz-woo-delivery-export-excel-type' ]);
    	$filtered_order_status = $helper->coderockz_woo_delivery_array_sanitize(explode(",",$_POST[ 'coderockz-woo-delivery-export-excel-order-status' ]));

    	$delivery_date_settings = get_option('coderockz_woo_delivery_date_settings');			
    	$pickup_date_settings = get_option('coderockz_woo_delivery_pickup_date_settings');			
		$delivery_time_settings = get_option('coderockz_woo_delivery_time_settings');
		$pickup_time_settings = get_option('coderockz_woo_delivery_pickup_settings');
		$delivery_pickup_settings = get_option('coderockz_woo_delivery_pickup_location_settings');
		$additional_field_settings = get_option('coderockz_woo_delivery_additional_field_settings');
		// if any timezone data is saved, set default timezone with the data
		$timezone = $helper->get_the_timezone();
		date_default_timezone_set($timezone);
		$delivery_date_format = (isset($delivery_date_settings['date_format']) && !empty($delivery_date_settings['date_format'])) ? $delivery_date_settings['date_format'] : "F j, Y";
		
		$add_weekday_name = (isset($delivery_date_settings['add_weekday_name']) && !empty($delivery_date_settings['add_weekday_name'])) ? $delivery_date_settings['add_weekday_name'] : false;

		if($add_weekday_name) {
			$delivery_date_format = "l ".$delivery_date_format;
		}

		$pickup_date_format = (isset($pickup_date_settings['date_format']) && !empty($pickup_date_settings['date_format'])) ? $pickup_date_settings['date_format'] : "F j, Y";

		$pickup_add_weekday_name = (isset($pickup_date_settings['add_weekday_name']) && !empty($pickup_date_settings['add_weekday_name'])) ? $pickup_date_settings['add_weekday_name'] : false;

		if($pickup_add_weekday_name) {
			$pickup_date_format = "l ".$pickup_date_format;
		}

		$time_format = (isset($delivery_time_settings['time_format']) && !empty($delivery_time_settings['time_format']))?$delivery_time_settings['time_format']:"12";
		if($time_format == 12) {
			$time_format = "h:i A";
		} elseif ($time_format == 24) {
			$time_format = "H:i";
		}

		$pickup_time_format = (isset($pickup_time_settings['time_format']) && !empty($pickup_time_settings['time_format']))?$pickup_time_settings['time_format']:"12";
		if($pickup_time_format == 12) {
			$pickup_time_format = "h:i A";
		} elseif ($pickup_time_format == 24) {
			$pickup_time_format = "H:i";
		}

		$delivery_date_field_label = (isset($delivery_date_settings['field_label']) && !empty($delivery_date_settings['field_label'])) ? stripslashes($delivery_date_settings['field_label']) : __("Delivery Date","coderockz-woo-delivery");
		$pickup_date_field_label = (isset($pickup_date_settings['pickup_field_label']) && !empty($pickup_date_settings['pickup_field_label'])) ? stripslashes($pickup_date_settings['pickup_field_label']) : __("Pickup Date","coderockz-woo-delivery");
		$delivery_time_field_label = (isset($delivery_time_settings['field_label']) && !empty($delivery_time_settings['field_label'])) ? stripslashes($delivery_time_settings['field_label']) : __("Delivery Time","coderockz-woo-delivery");
		$pickup_time_field_label = (isset($pickup_time_settings['field_label']) && !empty($pickup_time_settings['field_label'])) ? stripslashes($pickup_time_settings['field_label']) : __("Pickup Time","coderockz-woo-delivery");
		$pickup_location_field_label = (isset($pickup_location_settings['field_label']) && !empty($pickup_location_settings['field_label'])) ? stripslashes($pickup_location_settings['field_label']) : __("Pickup Location","coderockz-woo-delivery");
		$additional_field_label = (isset($additional_field_settings['field_label']) && !empty($additional_field_settings['field_label'])) ? $additional_field_settings['field_label'] : __("Special Note for Delivery","coderockz-woo-delivery");

		$localization_settings = get_option('coderockz_woo_delivery_localization_settings');
		$delivery_status_not_delivered_text = (isset($localization_settings['delivery_status_not_delivered_text']) && !empty($localization_settings['delivery_status_not_delivered_text'])) ? stripslashes($localization_settings['delivery_status_not_delivered_text']) : __("Not Delivered","coderockz-woo-delivery");
		$delivery_status_delivered_text = (isset($localization_settings['delivery_status_delivered_text']) && !empty($localization_settings['delivery_status_delivered_text'])) ? stripslashes($localization_settings['delivery_status_delivered_text']) : __("Delivery Completed","coderockz-woo-delivery");
		$pickup_status_not_picked_text = (isset($localization_settings['pickup_status_not_picked_text']) && !empty($localization_settings['pickup_status_not_picked_text'])) ? stripslashes($localization_settings['pickup_status_not_picked_text']) : __("Not Picked","coderockz-woo-delivery");
		$pickup_status_picked_text = (isset($localization_settings['pickup_status_picked_text']) && !empty($localization_settings['pickup_status_picked_text'])) ? stripslashes($localization_settings['pickup_status_picked_text']) : __("Pickup Completed","coderockz-woo-delivery");

		if(strpos($filtered_date, ' - ') !== false) {
			$filtered_dates = explode(' - ', $filtered_date);
			$orders = [];
			$delivery_orders = [];
			$pickup_orders = [];
			$period = new DatePeriod(new DateTime($filtered_dates[0]), new DateInterval('P1D'), new DateTime($filtered_dates[1].' +1 day'));
		    foreach ($period as $date) {
		        $dates[] = $date->format("Y-m-d");
		    }
		    foreach ($dates as $date) {
		    	if($filtered_delivery_type == "delivery"){
		    		$args = array(
				        'limit' => -1,
				        'delivery_date' => date("Y-m-d", strtotime($date)),
				        'status' => $filtered_order_status,
				        'delivery_type' => "delivery",
				    );
				    $orders_array = wc_get_orders( $args );
				    foreach ($orders_array as $order) {
				    	$orders[] = $order;
				    }
		    	} elseif($filtered_delivery_type == "pickup") {
		    		$args = array(
				        'limit' => -1,
				        'pickup_date' => date("Y-m-d", strtotime($date)),
				        'status' => $filtered_order_status,
				        'delivery_type' => "pickup",
				    );
				    $orders_array = wc_get_orders( $args );
				    foreach ($orders_array as $order) {
				    	$orders[] = $order;
				    }
		    	} else {
		    		$args = array(
				        'limit' => -1,
				        'delivery_date' => date("Y-m-d", strtotime($date)),
				        'status' => $filtered_order_status,
				    );

				    $orders_array = wc_get_orders( $args );
				    foreach ($orders_array as $order) {
				    	$delivery_orders[] = $order;
				    }

				    $args = array(
				        'limit' => -1,
				        'pickup_date' => date("Y-m-d", strtotime($date)),
				        'status' => $filtered_order_status,
				    );

				    $orders_array = wc_get_orders( $args );
				    foreach ($orders_array as $order) {
				    	$pickup_orders[] = $order;
				    }

				    $orders = array_merge($delivery_orders, $pickup_orders);
		    	}
		    	
			    
		    }
			

		} else {

		    if($filtered_delivery_type == "delivery"){
	    		$args = array(
			        'limit' => -1,
			        'delivery_date' => date('Y-m-d', strtotime($filtered_date)),
			        'status' => $filtered_order_status,
			        'delivery_type' => "delivery",
			    );
			    $orders = wc_get_orders( $args );
	    	} elseif($filtered_delivery_type == "pickup") {
	    		$args = array(
			        'limit' => -1,
			        'pickup_date' => date('Y-m-d', strtotime($filtered_date)),
			        'status' => $filtered_order_status,
			        'delivery_type' => "pickup",
			    );
			    $orders = wc_get_orders( $args );
	    	} else {
	    		$args = array(
			        'limit' => -1,
			        'delivery_date' => date('Y-m-d', strtotime($filtered_date)),
			        'status' => $filtered_order_status,
			    );
			    $delivery_orders = wc_get_orders( $args );

			    $args = array(
			        'limit' => -1,
			        'pickup_date' => date('Y-m-d', strtotime($filtered_date)),
			        'status' => $filtered_order_status,
			    );
			    $pickup_orders = wc_get_orders( $args );

			    $orders = array_merge($delivery_orders, $pickup_orders);
	    	}
		    
		}

		$tableHead = [
			'font'=>[
				'color'=>[
					'rgb'=>'FFFFFF'
				],
				'bold'=>true,
				'size'=>10
			],
			'fill'=>[
				'fillType' => Fill::FILL_SOLID,
				'startColor' => [
					'rgb' => '538ED5'
				]
			],
		];
		//even row
		$evenRow = [
			'fill'=>[
				'fillType' => Fill::FILL_SOLID,
				'startColor' => [
					'rgb' => 'ffffff'
				]
			]
		];
		//odd row
		$oddRow = [
			'fill'=>[
				'fillType' => Fill::FILL_SOLID,
				'startColor' => [
					'rgb' => 'eeeeee'
				]
			]
		];

		//make a new spreadsheet object
		$spreadsheet = new Spreadsheet();
		//get current active sheet (first sheet)
		$sheet = $spreadsheet->getActiveSheet();

		//set default font
		$spreadsheet->getDefaultStyle()
			->getFont()
			->setName('Arial')
			->setSize(10);

		$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(10);
		$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(15);
		$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(15);
		$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(30);
		$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(15);
		$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(30);
		$spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(30);
		$spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(30);
		$spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(30);
		$spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(10);
		$spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(20);
		$spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(20);
		$spreadsheet->getActiveSheet()->getColumnDimension('M')->setWidth(20);


		$spreadsheet->getActiveSheet()
			->setCellValue('A1',__("Order No","coderockz-woo-delivery"))
			->setCellValue('B1',__("Order Date","coderockz-woo-delivery"))
			->setCellValue('C1',__("Order Status","coderockz-woo-delivery"))
			->setCellValue('D1',__("Delivery Details","coderockz-woo-delivery"))
			->setCellValue('E1',__("Delivery Status","coderockz-woo-delivery"))
			->setCellValue('F1',__("Billing Address","coderockz-woo-delivery"))
			->setCellValue('G1',__("Shipping Address","coderockz-woo-delivery"))
			->setCellValue('H1',__("Contact Details","coderockz-woo-delivery"))
			->setCellValue('I1',__("Products","coderockz-woo-delivery"))
			->setCellValue('J1',__("Total","coderockz-woo-delivery"))
			->setCellValue('K1',__("Payment Method","coderockz-woo-delivery"))
			->setCellValue('L1',__("Shipping Method","coderockz-woo-delivery"))
			->setCellValue('M1',__("Customer Note","coderockz-woo-delivery"));

		$spreadsheet->getActiveSheet()->getStyle('A1:M1')->applyFromArray($tableHead);

		$row=2;
		$unsorted_orders = [];
		foreach($orders as $order) {
			if( version_compare( get_option( 'woocommerce_version' ), '3.0.0', ">=" ) ) {            
		        $order_id = $order->get_id();
		    } else {
		        $order_id = $order->id;
		    }

		    $delivery_date_timestamp = 0;
	    	$delivery_time_start = 0;
	    	$delivery_time_end = 0;

	    	if(metadata_exists('post', $order_id, 'delivery_date') && get_post_meta($order_id, 'delivery_date', true) !="") {
		    	$delivery_date_timestamp = strtotime(get_post_meta( $order_id, 'delivery_date', true ));
		    } elseif(metadata_exists('post', $order_id, 'pickup_date') && get_post_meta($order_id, 'pickup_date', true) !="") {
		    	$delivery_date_timestamp = strtotime(get_post_meta( $order_id, 'pickup_date', true ));
		    }

	    	if(metadata_exists('post', $order_id, 'delivery_time') && get_post_meta($order_id, 'delivery_time', true) !="") {
	    		if(get_post_meta($order_id, 'delivery_time', true) !="as-soon-as-possible") {
	    			$minutes = get_post_meta($order_id,"delivery_time",true);

			    	$slot_key = explode(' - ', $minutes);
					$slot_key_one = explode(':', $slot_key[0]);
					$delivery_time_start = ((int)$slot_key_one[0]*60+(int)$slot_key_one[1]);

			    	if(!isset($slot_key[1])) {
			    		$delivery_time_end = 0;
			    	} else {
			    		$slot_key_two = explode(':', $slot_key[1]);
			    		$delivery_time_end = ((int)$slot_key_two[0]*60+(int)$slot_key_two[1]);
			    	}
	    		} else {
	    			$delivery_time_end = 0;
	    		}
		    	
		    	
		    } elseif(metadata_exists('post', $order_id, 'pickup_time') && get_post_meta($order_id, 'pickup_time', true) !="") {
		    	$minutes = get_post_meta($order_id,"pickup_time",true);
		    	$slot_key = explode(' - ', $minutes);
				$slot_key_one = explode(':', $slot_key[0]);
				$delivery_time_start = ((int)$slot_key_one[0]*60+(int)$slot_key_one[1]);
		    	if(!isset($slot_key[1])) {
		    		$delivery_time_end = 0;
		    	} else {
		    		$slot_key_two = explode(':', $slot_key[1]);
			    	$delivery_time_end = ((int)$slot_key_two[0]*60+(int)$slot_key_two[1]);
		    	}
		    }

	    	$delivery_details_in_timestamp = (int)$delivery_date_timestamp+(int)$delivery_time_start+(int)$delivery_time_end;

	    	$unsorted_orders[$order_id] = $delivery_details_in_timestamp;
		}

		asort($unsorted_orders);

		foreach ($unsorted_orders as $order_id => $value) {
			
			$order = wc_get_order($order_id);

		    $order_created_obj= new DateTime($order->get_date_created());

			$delivery_details = "";
		    if(metadata_exists('post', $order_id, 'delivery_date') && get_post_meta($order_id, 'delivery_date', true) !="") {

		    	$delivery_details .= $delivery_date_field_label.': ' . date($delivery_date_format, strtotime(get_post_meta( $order_id, 'delivery_date', true ))) . "\r";

		    }

		    if(metadata_exists('post', $order_id, 'pickup_date') && get_post_meta($order_id, 'pickup_date', true) !="") {

		    	$delivery_details .= $pickup_date_field_label.': ' . date($pickup_date_format, strtotime(get_post_meta( $order_id, 'pickup_date', true ))) . "\r"; 

		    }

		    if(metadata_exists('post', $order_id, 'delivery_time') && get_post_meta($order_id, 'delivery_time', true) !="") {

		    	if(get_post_meta($order_id, 'delivery_time', true) !="as-soon-as-possible") {
			    	$minutes = get_post_meta($order_id,"delivery_time",true);
			    	$minutes = explode(' - ', $minutes);

		    		if(!isset($minutes[1])) {
		    			$delivery_details .= $delivery_time_field_label.': ' . date($time_format, strtotime($minutes[0])) . "\r";
		    		} else {

		    			$delivery_details .= $delivery_time_field_label.': ' . date($time_format, strtotime($minutes[0])) . ' - ' . date($time_format, strtotime($minutes[1])) . "\r";  			
		    		}
	    		} else {
	    			$as_soon_as_possible_text = (isset($delivery_time_settings['as_soon_as_possible_text']) && !empty($delivery_time_settings['as_soon_as_possible_text'])) ? stripslashes($delivery_time_settings['as_soon_as_possible_text']) : "As Soon As Possible";
	    			$delivery_details .= $delivery_time_field_label.': ' . $as_soon_as_possible_text . "\r";
	    		}
		    	
		    }

		    if(metadata_exists('post', $order_id, 'pickup_time') && get_post_meta($order_id, 'pickup_time', true) !="") {
		    	$pickup_minutes = get_post_meta($order_id,"pickup_time",true);
		    	$pickup_minutes = explode(' - ', $pickup_minutes);

	    		if(!isset($pickup_minutes[1])) {
	    			$delivery_details .= $pickup_time_field_label.': ' . date($pickup_time_format, strtotime($pickup_minutes[0])) . "\r";
	    		} else {

	    			$delivery_details .= $pickup_time_field_label.': ' . date($pickup_time_format, strtotime($pickup_minutes[0])) . ' - ' . date($pickup_time_format, strtotime($pickup_minutes[1])) . "\r";  			
	    		}
		    	
		    }

		    if(metadata_exists('post', $order_id, 'delivery_pickup') && get_post_meta($order_id, 'delivery_pickup', true) !="") {
				$delivery_details .= $pickup_location_field_label.': ' . get_post_meta($order_id, 'delivery_pickup', true) . "\r";
			}

			if(metadata_exists('post', $order_id, 'additional_note') && get_post_meta($order_id, 'additional_note', true) !="") {
				$delivery_details .= $additional_field_label.': ' . get_post_meta($order_id, 'additional_note', true) . "\r";
			}


			if(metadata_exists('post', $order_id, 'delivery_status') && get_post_meta($order_id, 'delivery_status', true) !="" && get_post_meta($order_id, 'delivery_status', true) =="delivered") {
				if(metadata_exists('post', $order_id, 'delivery_type') && get_post_meta($order_id, 'delivery_type', true) !="" && get_post_meta($order_id, 'delivery_type', true) =="pickup") {
					$delivery_status = $pickup_status_picked_text;
				} else {
					$delivery_status = $delivery_status_delivered_text;
				}
				
			} else {

				if(metadata_exists('post', $order_id, 'delivery_type') && get_post_meta($order_id, 'delivery_type', true) !="" && get_post_meta($order_id, 'delivery_type', true) =="pickup") {
					$delivery_status = $pickup_status_not_picked_text;
				} else {
					$delivery_status = $delivery_status_not_delivered_text;
				}
			}

			$i=1;
			$product_details = "";
			foreach ($order->get_items() as $item) {
				$product_details .= $i.'. ';
				$product_details .= $item->get_name();
				$product_details .= '   '.str_replace("&nbsp;","",$helper->format_price($order->get_item_total( $item ),$order->get_id())).'x';
				$product_details .= $item->get_quantity().'=';
				$product_details .= str_replace("&nbsp;","",$helper->format_price(number_format($item->get_total(),2),$order->get_id()));
				$product_details .= "\r";
				$i = $i+1;
			}

			$order_billing_address = str_replace("<br/>","\r",$order->get_formatted_billing_address());
			$order_shipping_address = str_replace("<br/>","\r",$order->get_formatted_shipping_address());
			$order_contact_details = 'Phone: '.$order->get_billing_phone()."\r".'Email: '.$order->get_billing_email();;

			if(metadata_exists('post', $order_id, '_wcj_order_number') && get_post_meta($order_id, '_wcj_order_number', true) !="") {
				$order_id_with_custom = '#'.get_post_meta($order_id, '_wcj_order_number', true);
			} elseif(class_exists('WC_Seq_Order_Number') || class_exists('WC_Seq_Order_Number_Pro')) {
				$order_id_with_custom = $order->get_order_number();
			} else {
				$order_id_with_custom = '#'.$order->get_id();
			}

			$shipping_method = $order->get_shipping_method() != null && $order->get_shipping_method() != "" ? $order->get_shipping_method() : "";
	        $shipping_method_amount = $order->get_shipping_total() != null && $order->get_shipping_total() != "" && $order->get_shipping_total() != 0 ? " - ".$order->get_currency().$order->get_shipping_total(): "";

			$spreadsheet->getActiveSheet()
				->setCellValue('A'.$row , $order_id_with_custom)
				->setCellValue('B'.$row , $order_created_obj->format("F j, Y"))
				->setCellValue('C'.$row , $order->get_status())
				->setCellValue('D'.$row , $delivery_details)
				->setCellValue('E'.$row , $delivery_status)
				->setCellValue('F'.$row , $order_billing_address)
				->setCellValue('G'.$row , $order_shipping_address)
				->setCellValue('H'.$row , $order_contact_details)
				->setCellValue('I'.$row , $product_details)
				->setCellValue('J'.$row , $order->get_currency() . $order->get_total())
				->setCellValue('K'.$row , $order->get_payment_method_title())
				->setCellValue('L'.$row , $shipping_method . $shipping_method_amount)
				->setCellValue('M'.$row , $order->get_customer_note());
			
			if( $row % 2 == 0 ){

				$spreadsheet->getActiveSheet()->getStyle('A'.$row.':M'.$row)->applyFromArray($evenRow)->getAlignment()->setWrapText(true)->setVertical(Alignment::VERTICAL_CENTER);
			}else{

				$spreadsheet->getActiveSheet()->getStyle('A'.$row.':M'.$row)->applyFromArray($oddRow)->getAlignment()->setWrapText(true)->setVertical(Alignment::VERTICAL_CENTER);
			}
			
			$row++;
		}
		$firstRow=1;
		$lastRow=$row-1;
 		$filename = "delivery_sheet(".$filtered_date.").xlsx";
 		ob_end_clean();
		header('Content-disposition: attachment; filename="'.$filename.'"');
		header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
		header('Content-Transfer-Encoding: binary');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		ob_clean();
		flush();

 		$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
		$writer->save('php://output');
		exit;

	}

	$helper = new Coderockz_Woo_Delivery_Helper();
	$timezone = $helper->get_the_timezone();
	date_default_timezone_set($timezone);

	$delivery_date_settings = get_option('coderockz_woo_delivery_date_settings');

	/*This week range*/	
	$week_starts_from = (isset($delivery_date_settings['week_starts_from']) && !empty($delivery_date_settings['week_starts_from'])) ? $delivery_date_settings['week_starts_from']:"0";

	switch ($week_starts_from) {
	    case "0":
	        $week_day = "sunday";
	        break;
	    case "1":
	        $week_day = "monday";
	        break;
	    case "2":
	        $week_day = "tuesday";
	        break;
	    case "3":
	        $week_day = "wednesday";
	        break;
	    case "4":
	        $week_day = "thursday";
	        break;
	    case "5":
	        $week_day = "friday";
	        break;
	    case "6":
	        $week_day = "saturday";
	        break;
	}

	$week_start = strtotime("last ".$week_day);
	$week_start = date('w', $week_start)==date('w') ? $week_start+7*86400 : $week_start;

	$week_end = strtotime(date("F j, Y",$week_start)." +6 days");

	$this_week_start = date("F j, Y",$week_start);
	$this_week_end = date("F j, Y",$week_end);

	$this_week = $this_week_start." - ".$this_week_end;
	/*This week range*/

	/*This Month range*/
	$day_today = strtotime (date('F j, Y', time()));
    $this_month_first_day = date ('F j, Y', strtotime ('first day of this month', $day_today));
    $this_month_last_day = date ('F j, Y', strtotime ('last day of this month', $day_today));

    $this_month = $this_month_first_day." - ".$this_month_last_day;

	/*This Month range*/

	$today = date('F j, Y', time());
	$date_obj = new DateTime($today);
	$tomorrow = $date_obj->modify("+1 day")->format("F j, Y");

	$order_details_html = '';


?>
<div class="coderockz-woo-delivery-card">
	<p class="coderockz-woo-delivery-card-header"><?php _e('Delivery Reports', 'coderockz-woo-delivery'); ?></p>
	<div class="coderockz-woo-delivery-card-body">
		<div class="coderockz-woo-delivery-current-filter-section">
			
			<div style="float:left;">
				<span class="coderockz-woo-delivery-current-filter" data-filter_text="Today <span style='color:#bbb'> (<?php echo $today; ?>)</span>"><?php _e('Select Date', 'coderockz-woo-delivery'); ?></span>
				<span class="coderockz-woo-delivery-filter-change-btn dashicons dashicons-arrow-down-alt2"></span>
			</div>
			<div style="float:right;">
				
			</div>
		</div>
		<div class="coderockz_woo_delivery_report_filter_modal" id="coderockz_woo_delivery_report_filter_modal">
			<div class="coderockz_woo_delivery_report_filter_modal_wrap">
				<div class="coderockz_woo_delivery_report_filter_modal_header">
					<p class="coderockz_woo_delivery_report_filter_modal_header_text" style="margin:0;"><?php _e('Today', 'coderockz-woo-delivery'); ?> <span style="color:#bbb"> (<?php echo $today; ?>)</span></p>
				</div>

				<div class="coderockz_woo_delivery_report_filter_modal_body">
					<div class="coderockz-woo-delivery-report-header-wrapper">    
						<div class="coderockz-woo-delivery-report-header-delivery-type">
						<label style="margin-right: 5px;">
						    <input type="radio" name="coderockz-woo-delivery-report-filter-delivery-type" value="all" checked/><?php _e('All', 'coderockz-woo-delivery'); ?>
						</label>
						<label style="margin-right: 5px;">
						    <input type="radio" name="coderockz-woo-delivery-report-filter-delivery-type" value="delivery"/><?php _e('Delivery', 'coderockz-woo-delivery'); ?>
						</label>
						<label>
						    <input type="radio" name="coderockz-woo-delivery-report-filter-delivery-type" value="pickup"/><?php _e('Pickup', 'coderockz-woo-delivery'); ?>
						</label>
						</div>
						<div class="coderockz-woo-delivery-report-header-radio-btn">
							<input type="radio" id="coderockz-woo-delivery-report-header-radio-btn-1" name="coderockz-woo-delivery-report-filter-value" value="<?php echo $today; ?>" checked/>
							<label for="coderockz-woo-delivery-report-header-radio-btn-1"></label>
							<span><?php _e('Today', 'coderockz-woo-delivery'); ?> <span style="color:#bbb"> (<?php echo $today; ?>)</span></span>
						</div>

						<div class="coderockz-woo-delivery-report-header-radio-btn">
							<input type="radio" id="coderockz-woo-delivery-report-header-radio-btn-2" name="coderockz-woo-delivery-report-filter-value" value="<?php echo $tomorrow; ?>"/>
							<label for="coderockz-woo-delivery-report-header-radio-btn-2"></label>
							<span><?php _e('Tomorrow', 'coderockz-woo-delivery'); ?> <span style="color:#bbb"> (<?php echo $tomorrow; ?>)</span></span>
						</div>

						<div class="coderockz-woo-delivery-report-header-radio-btn">
							<input type="radio" id="coderockz-woo-delivery-report-header-radio-btn-3" name="coderockz-woo-delivery-report-filter-value" value="<?php echo $this_week; ?>"/>
							<label for="coderockz-woo-delivery-report-header-radio-btn-3"></label>
							<span><?php _e('This Week', 'coderockz-woo-delivery'); ?> <span style="color:#bbb"> (<?php echo $this_week; ?>)</span></span>
						</div>

						<div class="coderockz-woo-delivery-report-header-radio-btn">
							<input type="radio" id="coderockz-woo-delivery-report-header-radio-btn-4" name="coderockz-woo-delivery-report-filter-value" value="<?php echo $this_month; ?>"/>
							<label for="coderockz-woo-delivery-report-header-radio-btn-4"></label>
							<span><?php _e('This Month', 'coderockz-woo-delivery'); ?> <span style="color:#bbb"> (<?php echo $this_month; ?>)</span>
						</div>
						<div class="coderockz-woo-delivery-report-header-radio-btn">
							<input data-custom_range_text="<?php _e('Custom Date Range', 'coderockz-woo-delivery'); ?>" type="radio" id="coderockz-woo-delivery-report-header-radio-btn-5" name="coderockz-woo-delivery-report-filter-value" value=""/>
							<label for="coderockz-woo-delivery-report-header-radio-btn-5"></label>
							<span><?php _e('Custom Date Range', 'coderockz-woo-delivery'); ?><span style="color:#bbb"> <?php _e('(Max 30 Days)', 'coderockz-woo-delivery'); ?></span></span>
						</div>
					</div>
					<div class="coderockz-woo-delivery-date-range-wrapper">

					</div>
					<div>
						<select data-filter_order_status_text="<?php _e('Filter by Order Status', 'coderockz-woo-delivery'); ?>" id="coderockz_woo_delivery_calendar_order_status_filter_report" class="coderockz_woo_delivery_calendar_order_status_filter_report" multiple>
							<?php 
							foreach(wc_get_order_statuses() as $key => $value) {
								echo "<option value='".substr($key, 3)."'>".$value."</option>";
							}
							?>
							<option value="partially-paid">Partially Paid</option>
						</select>
					</div>
				</div>

				<div class="coderockz_woo_delivery_report_filter_modal_footer" style="overflow:hidden">
					<?php
					$order_status_keys = array_keys(wc_get_order_statuses());
					$order_status = ['partially-paid'];
					foreach($order_status_keys as $order_status_key) {
						$order_status[] = substr($order_status_key,3);
					}
					$order_status = array_diff($order_status,['cancelled','failed','refunded']);
					$order_status = implode(",",$order_status);
					?>
					<form style="float: left;margin-right: 2px;" action="" method="post" id ="coderockz_woo_delivery_export_quantity_form_submit">
                        <?php wp_nonce_field('coderockz_woo_delivery_nonce'); ?>

                        <input style="display:none;visibility:hidden;" class="button-primary" type="text" name="coderockz-woo-delivery-export-excel-quantity-type" value="all" />
                        <input style="display:none;visibility:hidden;" class="button-primary" type="text" name="coderockz-woo-delivery-export-excel-quantity-date" value="<?php echo $today; ?>" />

                        <input data-order_status="<?php echo $order_status; ?>" style="display:none;visibility:hidden;" class="button-primary" type="text" name="coderockz-woo-delivery-export-excel-quantity-order-status" value="<?php echo $order_status; ?>" />

                        <input class="button-primary" type="submit" name="coderockz-woo-delivery-export-excel-quantity-btn" value="<?php _e('Export(Quantity)', 'coderockz-woo-delivery'); ?>" />
                    </form>

                    <form style="float: left;margin-right: 2px;" action="" method="post" id ="coderockz_woo_delivery_export_form_submit">
                        <?php wp_nonce_field('coderockz_woo_delivery_nonce'); ?>

                        <input style="display:none;visibility:hidden;" class="button-primary" type="text" name="coderockz-woo-delivery-export-excel-type" value="all" />
                        <input style="display:none;visibility:hidden;" class="button-primary" type="text" name="coderockz-woo-delivery-export-excel-date" value="<?php echo $today; ?>" />

                        <input data-order_status="<?php echo $order_status; ?>" style="display:none;visibility:hidden;" class="button-primary" type="text" name="coderockz-woo-delivery-export-excel-order-status" value="<?php echo $order_status; ?>" />

                        <input class="button-primary" type="submit" name="coderockz-woo-delivery-export-excel-btn" value="<?php _e('Export(Details)', 'coderockz-woo-delivery'); ?>" />
                    </form>

					<button data-processing_text="<?php _e('Processing', 'coderockz-woo-delivery'); ?>" style="float: left;margin-right: 2px;" class="coderockz-woo-delivery-report-product-quantity-button button-primary"><?php _e('Quantity', 'coderockz-woo-delivery'); ?></button>
					<button data-processing_text="<?php _e('Processing', 'coderockz-woo-delivery'); ?>" style="float: left;margin-right: 2px;" class="coderockz-woo-delivery-report-filter-apply-button button-primary"><?php _e('Details', 'coderockz-woo-delivery'); ?></button>
					<button style="float: left;" class="coderockz-woo-delivery-report-filter-cancel-button button-secondary"><?php _e('Cancel', 'coderockz-woo-delivery'); ?></button>
				</div>
			</div>
		</div>
		<p class="coderockz_woo_deivery_total_sales_text" style="display:none;font-weight:700"><?php _e('Total Sales: ', 'coderockz-woo-delivery'); ?><span style="color:#3e1a90"></span></p>
		<div class="coderockz_woo_delivery_report_result">
			<table id="coderockz_woo_delivery_report_table" class="display" style="width:100%">
		        <thead>
		            <tr>
		                <th class="details-control sorting_disabled"></th>
		                <th><?php _e('Order No', 'coderockz-woo-delivery'); ?></th>
		                <th><?php _e('Order Date', 'coderockz-woo-delivery'); ?></th>
		                <th><?php _e('Delivery Details', 'coderockz-woo-delivery'); ?></th>
		                <th><?php _e('Delivery Status', 'coderockz-woo-delivery'); ?></th>
		                <th><?php _e('Order Status', 'coderockz-woo-delivery'); ?></th>
		                <th><?php _e('Total', 'coderockz-woo-delivery'); ?></th>
		                <th><?php _e('Action', 'coderockz-woo-delivery'); ?></th>
		            </tr>
		        </thead>
		        <tbody>
		        	<?php echo $order_details_html; ?>
	        	</tbody>
		        <tfoot>
		            <tr>
		                <th class="details-control sorting_disabled"></th>
		                <th><?php _e('Order No', 'coderockz-woo-delivery'); ?></th>
		                <th><?php _e('Order Date', 'coderockz-woo-delivery'); ?></th>
		                <th><?php _e('Delivery Details', 'coderockz-woo-delivery'); ?></th>
		                <th><?php _e('Delivery Status', 'coderockz-woo-delivery'); ?></th>
		                <th><?php _e('Order Status', 'coderockz-woo-delivery'); ?></th>
		                <th><?php _e('Total', 'coderockz-woo-delivery'); ?></th>
		                <th><?php _e('Action', 'coderockz-woo-delivery'); ?></th>
		            </tr>
		        </tfoot>
		    </table>
		</div>
	</div>
</div>