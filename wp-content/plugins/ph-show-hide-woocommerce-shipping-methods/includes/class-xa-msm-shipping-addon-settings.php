<?php

if ( !class_exists('xa_msm_shipping_addon_settings') )
{
	class xa_msm_shipping_addon_settings extends WC_Settings_Page {

		public function __construct() {    
			$this->id    = 'xa_manage_shipping_method';
			$this->label = __( 'Manage Shipping Methods', 'xa-shipping-addon' );
			add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_page' ), 21 );

			add_action( 'woocommerce_settings_' . $this->id, array( $this, 'xa_msm_shipping_addon_output' ) );
			add_action( 'woocommerce_settings_save_' . $this->id, array( $this, 'xa_msm_shipping_addon_save' ) );

			add_action('woocommerce_admin_field_rate_options',array( $this, 'generate_rate_options_html'));
		}


		public function xa_msm_shipping_addon_get_settings( $current_section = '' ) {
			global $current_section;
			switch($current_section){
				case '':
				case 'xa_rate_options':
					$settings = apply_filters( 'xa_msm_addon_rate_settings', array(
						'rate_options_options_title'	=>	array(
							'name' => __( 'Rate Options', 'xa-shipping-addon' ),
							'type' => 'title',
							'desc' => '',
							'id'   => 'xa_msm_shipping_addon_rate_role_matrix_options_title',
						),	
						'rate_options'	=>	array(
						'type'     => 'rate_options',
						'id'       => 'xa_msm_shipping_addon_rate_role_matrix',
						),			
						'rate_options_options_sectionend'	=>	array(
							'type' => 'sectionend',
							'id'   => 'xa_msm_shipping_addon_rate_role_matrix_options_sectionend'
						),			
					) );
					break;
			}
			return apply_filters( 'woocommerce_get_settings_' . $this->id, $settings, $current_section );	
		}

		public function xa_msm_shipping_addon_output() {
		    global $current_section;
		    $settings = $this->xa_msm_shipping_addon_get_settings( $current_section );
		    WC_Admin_Settings::output_fields( $settings );
		}

		public function xa_msm_shipping_addon_save() {   
		    global $current_section;
		    //Remove the array key for empty data
		    if( ! empty($_POST['xa_msm_shipping_addon_rate_role_matrix']['rate_section_2']) )
		    {
			    $temp = TRUE;
			    while($temp)
			    {
				    $last_key = key(array_slice($_POST['xa_msm_shipping_addon_rate_role_matrix']['rate_section_2'],-1,1,TRUE));
				    if( !empty($_POST['xa_msm_shipping_addon_rate_role_matrix']['rate_section_2'][$last_key]) && ( empty($_POST['xa_msm_shipping_addon_rate_role_matrix']['rate_section_2'][$last_key]['shipping_class']) && empty($_POST['xa_msm_shipping_addon_rate_role_matrix']['rate_section_2'][$last_key]['shipping_zone']) && empty($_POST['xa_msm_shipping_addon_rate_role_matrix']['rate_section_2'][$last_key]['shipping_method']) ) )
				    {
					unset($_POST['xa_msm_shipping_addon_rate_role_matrix']['rate_section_2'][$last_key]);
				    }
				    else
				    {
					    $temp = FALSE;
				    }
			    }

		    }
		    $settings = $this->xa_msm_shipping_addon_get_settings( $current_section );
		    WC_Admin_Settings::save_fields( $settings );
		}

		public function generate_rate_options_html() {
			include( 'html-xa-msm-rate-options.php' );
		}

	}
}
return new xa_msm_shipping_addon_settings();