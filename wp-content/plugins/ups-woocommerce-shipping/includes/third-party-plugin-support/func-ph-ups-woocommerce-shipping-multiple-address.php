<?php

/**
 * This file is to support Woocommerce shipping multiple address. It will be included only when Woocommerce shipping multiple address.
 */

if( ! function_exists('ph_ups_label_packages_from_wcms') ) {

	/**
	 * Support for shipping multiple address. Get customized package.
	 */
	function ph_ups_label_packages_from_wcms( $packages, $address,$order_id ) {
		$wms_packages           = get_post_meta($order_id, '_wcms_packages', true);	
		if(is_array($wms_packages)){
			return $wms_packages;
		}
		return $packages;
	}
	add_filter( 'wf_ups_filter_label_from_packages', 'ph_ups_label_packages_from_wcms', 10, 3 );
}



if( ! function_exists('ph_ups_split_shipments_based_on_destination') ) {

	/**
	 * Support for shipping multiple address. Get customized package.
	 */
	function ph_ups_split_shipments_based_on_destination( $shipments_split_based_on_service,$order ) {
		$shipment_data_split_based_on_destination_and_service=array();
		$i=0;
		foreach ($shipments_split_based_on_service as $shipment_key => $shipment) {
			if(sizeof($shipment['packages'])>1)
			{
				$destination_array_for_lookup=array();
				foreach ($shipment['packages'] as $package_index => $package) {
					$current_destination=implode(',',$package['destination']);
					if(in_array($current_destination, $destination_array_for_lookup))
					{
						$index = array_search($current_destination, $destination_array_for_lookup);
						$shipment_data_split_based_on_destination_and_service[$index]['packages'][]=$package;
					}
					else
					{
						$shipment_data_split_based_on_destination_and_service[$i]['shipping_service']=$shipment['shipping_service'];
						$shipment_data_split_based_on_destination_and_service[$i]['packages'][]=$package;
						$destination_array_for_lookup[$i]=$current_destination;
						$i++;
					}
				}
			}
			else
			{
				$shipment_data_split_based_on_destination_and_service[$i]=$shipment;
				$i++;
			}
		}
		return $shipment_data_split_based_on_destination_and_service;
	}
	add_filter( 'wf_ups_shipment_data', 'ph_ups_split_shipments_based_on_destination', 10, 2 );
}


if( ! function_exists('ph_ups_add_destination_to_packages') ) {

	/**
	 * Support for shipping multiple address. Get customized package.
	 */
	function ph_ups_add_destination_to_packages($packages, $destination ) {
		
		foreach ($packages as $package_index => &$package_value) {
				$package_value['destination']=$destination;
		}
		return $packages;
	}
	add_filter( 'ph_ups_customize_package_by_desination', 'ph_ups_add_destination_to_packages', 10, 2 );
}

if( ! function_exists('ph_ups_get_shipping_address_from_shipment') ) {

	/**
	 * Support for shipping multiple address. Get  product data.
	 */
	function ph_ups_get_shipping_address_from_shipment($address,$shipment, $ship_from_address,$order_id,$from_to ) {
		$wms_packages           = get_post_meta($order_id, '_wcms_packages', true);	
		if(empty($wms_packages)){
			return $address;
		}
		if($ship_from_address=='billing_address' && $from_to=='from' && isset($shipment['packages'][0]['destination']))
		{
			$shipping_address=$shipment['packages'][0]['destination'];   // Will take the destination address from the first package, since all package have same destination.
			$address=array(
				'name'		=> htmlspecialchars($shipping_address['first_name']).' '.htmlspecialchars($shipping_address['last_name']),
				'company' 	=> !empty($shipping_address['company']) ? htmlspecialchars($shipping_address['company']) : '-',
				'phone' 	=> $address['phone'],
				'email' 	=> htmlspecialchars($address['email']),
				'address_1'	=> htmlspecialchars($shipping_address['address_1']),
				'address_2'	=> htmlspecialchars($shipping_address['address_2']),
				'city' 		=> htmlspecialchars($shipping_address['city']),
				'state' 	=> htmlspecialchars($shipping_address['state']),
				'country' 	=> $shipping_address['country'],
				'postcode' 	=> $shipping_address['postcode'],
			);
		}
		elseif ($ship_from_address!= 'billing_address' && $from_to=='to' && isset($shipment['packages'][0]['destination'])) {
			$shipping_address=$shipment['packages'][0]['destination'];   // Will take the destination address from the first package, since all package have same destination.
			$address=array(
				'name'		=> htmlspecialchars($shipping_address['first_name']).' '.htmlspecialchars($shipping_address['last_name']),
				'company' 	=> !empty($shipping_address['company']) ? htmlspecialchars($shipping_address['company']) : '-',
				'phone' 	=> $address['phone'],
				'email' 	=> htmlspecialchars($address['email']),
				'address_1'	=> htmlspecialchars($shipping_address['address_1']),
				'address_2'	=> htmlspecialchars($shipping_address['address_2']),
				'city' 		=> htmlspecialchars($shipping_address['city']),
				'state' 	=> htmlspecialchars($shipping_address['state']),
				'country' 	=> $shipping_address['country'],
				'postcode' 	=> $shipping_address['postcode'],
			);
		}
		return $address;
	}
	add_filter( 'ph_ups_address_customization', 'ph_ups_get_shipping_address_from_shipment', 10, 5 );
}



