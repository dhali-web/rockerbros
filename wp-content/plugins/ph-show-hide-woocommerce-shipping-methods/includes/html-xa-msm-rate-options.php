<?php
	// include_once 'html-xa-msm-market.php';
	$saved_matrix = get_option('xa_msm_shipping_addon_rate_role_matrix');
	$zone_list = array();
	if( class_exists('WC_Shipping_Zones') ) {
		$zones_obj = new WC_Shipping_Zones;
		$zones = $zones_obj::get_zones();
	}
    $is_enabled_residencial = isset($saved_matrix['rate_section_1']['break_on_first_occurance']) ? $saved_matrix['rate_section_1']['break_on_first_occurance'] : false;
    $cart_logic_arr         = array(
        'le'        =>  __( 'Less Than or Equal to', 'xa_msm_hide_shipping_method' ),
        'gt'        =>  __( 'Greater Than', 'xa_msm_hide_shipping_method' ),
    );
?>
<table style="width:70%">
    <tr>
        <td style="width:30%"><strong><?php _e('Break on first Occurance', 'xa_msm_hide_shipping_method' ); ?></strong></td><td >
            <?php
            woocommerce_form_field(
                "xa_msm_shipping_addon_rate_role_matrix[rate_section_1][break_on_first_occurance]", array(
                    'id'        => "break_on_first_occurance",
                    'type'      => "checkbox",
                    'label'     => __("Enable"),
                ),$is_enabled_residencial
            ); ?>   
        </td>
    </tr>
</table>

<hr/>

<table class="wp-list-table widefat fixed posts role_matrix" style="width:100%; margin-bottom:20px">
    <thead>
        <tr>
            <th><?php  _e('Shipping class', 'xa_msm_hide_shipping_method' ); ?></th>
            <th><?php  _e('Shipping Zone', 'xa_msm_hide_shipping_method'); ?></th>
            <th><?php  _e('Shipping method', 'xa_msm_hide_shipping_method'); ?></th>
            <th><?php  _e('Cart Subtotal', 'xa_msm_hide_shipping_method'); ?></th>
            <th><?php  _e('Logic on Cart Subtotal', 'xa_msm_hide_shipping_method'); ?></th>
        </tr>
    </thead>
    
    <tbody>
        <?php 
        $test = new WC_Shipping;
        $shipping_classes = ! empty( $temp = $test->get_shipping_classes() ) ? $temp : array(); //Get all the Shipping classes
        

        $this->role_matrix = array();
        if( empty($saved_matrix['rate_section_2']) ){
                $this->role_matrix[0]['shipping_class']         = '';
                $this->role_matrix[0]['shipping_zone']	        = '';
                $this->role_matrix[0]['shipping_method']        = '';
                $this->role_matrix[0]['cart_subtotal']          = null;
                $this->role_matrix[0]['logic_on_cart_subtotal']    = null;

        } else {
            foreach ( $saved_matrix['rate_section_2'] as $id => $matrix ) {
                $this->role_matrix[$id]['shipping_class']	    = !empty($matrix['shipping_class']) ? $matrix['shipping_class']: '';
                $this->role_matrix[$id]['shipping_zone']	    = !empty($matrix['shipping_zone']) ? $matrix['shipping_zone'] : '';
                $this->role_matrix[$id]['shipping_method']	    = !empty($matrix['shipping_method']) ? $matrix['shipping_method'] : '';
                $this->role_matrix[$id]['cart_subtotal']          = ! empty($matrix['cart_subtotal']) ? $matrix['cart_subtotal'] : '';
                $this->role_matrix[$id]['logic_on_cart_subtotal']    = ! empty($matrix['logic_on_cart_subtotal']) ? $matrix['logic_on_cart_subtotal'] : '';
            }
        }
	foreach ( $this->role_matrix as $key => $value ){
            global $woocommerce;
            if(!array_filter($value))
                continue;
            ?>
			<tr>
				<td>    
                    <?php
                    if( !empty($shipping_classes) ){?>
                        <select name="xa_msm_shipping_addon_rate_role_matrix[rate_section_2][<?php  echo $key; ?>][shipping_class]" class="wc-enhanced-select" multiple="multiple"><?php
                        foreach ($shipping_classes as $id => $shipping_class) {
                            if( $this->role_matrix[ $key ]['shipping_class'] == $shipping_class->slug ){
                                echo"<option value='$shipping_class->slug' selected>$shipping_class->name</option>";
                            }else{
                                echo"<option value='$shipping_class->slug'>$shipping_class->name</option>";
                            }
                        }?>
                        </select>
			    <?php
                    }?>
				</td>
                <td>
			<select name="xa_msm_shipping_addon_rate_role_matrix[rate_section_2][<?php  echo $key; ?>][shipping_zone]" /><?php
		    echo "<option value=''>--Select One--</option>";
                    foreach ($zones as $zone_key => $zone) {

			    if( $value['shipping_zone'] == $zone['zone_id'])
                    echo "<option value=".$zone['zone_id']." selected>".$zone['zone_name']."</option>";
                else
				    echo "<option value=".$zone['zone_id'].">".$zone['zone_name']."</option>";
                    }
		?> 
                </td>
                <td >
					<input type="text" placeholder="Eg: free_shipping:1" size="20" name="xa_msm_shipping_addon_rate_role_matrix[rate_section_2][<?php  echo $key; ?>][shipping_method]" value="<?php  echo isset( $this->role_matrix[ $key ]['shipping_method'] ) ? $this->role_matrix[ $key ]['shipping_method'] : ''; ?>"/>
		        </td>
                <td >
					<input type="number" step="any" size="10" name="xa_msm_shipping_addon_rate_role_matrix[rate_section_2][<?php  echo $key; ?>][cart_subtotal]" value="<?php  echo isset( $this->role_matrix[ $key ]['cart_subtotal'] ) ? $this->role_matrix[ $key ]['cart_subtotal'] : ''; ?>"/>
		        </td>
                <td>
                    <select name="xa_msm_shipping_addon_rate_role_matrix[rate_section_2][<?php  echo $key; ?>][logic_on_cart_subtotal]" />
                    <?php
                        foreach( $cart_logic_arr as $cart_logic_key => $cart_logic_name ) {
                            if( $cart_logic_key == $this->role_matrix[$key]['logic_on_cart_subtotal'] ) {
                                echo "<option value=".$cart_logic_key." selected>".$cart_logic_name."</option>";
                            }
                            else{
                                echo "<option value=".$cart_logic_key.">".$cart_logic_name."</option>";
                            }
                        }
                    ?>
                </td>
			</tr><?php 
		}?>
			
			
			
        <tr>
            <td>    
                <?php
                $key = !isset($key) ? 0 : $key+1;
                if( !empty($shipping_classes) ){?>
                    <select name="xa_msm_shipping_addon_rate_role_matrix[rate_section_2][<?php  echo $key; ?>][shipping_class]" class="wc-enhanced-select" multiple="multiple"><?php
                    foreach ($shipping_classes as $id => $shipping_class) {
                        echo"<option value=".$shipping_class->slug.">$shipping_class->name</option>";
                    }?>
                    </select><?php
                }?>
            </td>
	    
            <td>
		    <select name="xa_msm_shipping_addon_rate_role_matrix[rate_section_2][<?php  echo $key; ?>][shipping_zone]" /><?php
		    echo "<option value=''>--Select One--</option>";
                    //$zone_list[0] = 'Rest of the World'; //rest of the shipping_zone always have id 0, which is not available in the method get_zone()
                    foreach ($zones as $zone_key => $zone) {
			    echo "<option value=".$zone['zone_id'].">".$zone['zone_name']."</option>";
                    }
		?>
            </td>
            <td >
                <input type="text" size="20" name="xa_msm_shipping_addon_rate_role_matrix[rate_section_2][<?php  echo $key; ?>][shipping_method]" />
            </td>
            <td >
				<input type="number" step="any" size="10" name="xa_msm_shipping_addon_rate_role_matrix[rate_section_2][<?php  echo $key; ?>][cart_subtotal]" />
		    </td>
            <td>
                    <select name="xa_msm_shipping_addon_rate_role_matrix[rate_section_2][<?php  echo $key; ?>][logic_on_cart_subtotal]" />
                    <?php
                        foreach( $cart_logic_arr as $cart_logic_key => $cart_logic_name ) {
                            echo "<option value=".$cart_logic_key.">".$cart_logic_name."</option>";
                        }
                    ?>
                </td>
        </tr>
	</tbody>
</table>
<hr/>
