document.addEventListener('DOMContentLoaded',function(){function c(){calendarData=[],jQuery('.coderockz-woo-delivery-loading-image').fadeIn();for(var a=0;a<4;a++)jQuery.ajax({url:coderockz_woo_delivery_ajax_obj.coderockz_woo_delivery_ajax_url,type:'post',data:{_ajax_nonce:coderockz_woo_delivery_ajax_obj.nonce,action:'coderockz_woo_delivery_get_order_details_for_delivery_calender',filteredDeliveryType:h,filteredFilterType:i,filteredStatusType:d,counter:a},success:function(b){if(b.success&&(calendarData=calendarData.concat(b.data.orders),timezone=b.data.timezone,timeFormat=b.data.time_format,a==4)){var d=timezone;var e='en';var f=document.getElementById('coderockz-woo-delivery-delivery-calendar');var c=new FullCalendar.Calendar(f,{timeZone:d,headerToolbar:{left:'prev,next today',center:'title',right:'dayGridMonth,listDay,listWeek,listMonth'},views:{listDay:{buttonText:'Today'},listWeek:{buttonText:'This Week'},listMonth:{buttonText:'This Month'}},allDayText:'',allDaySlot:!1,initialView:'dayGridMonth',dayMaxEvents:!0,events:calendarData,eventTimeFormat:{hour:'2-digit',minute:'2-digit',hour12:timeFormat}});c.setOption('locale',e),c.render();}}});jQuery('.coderockz-woo-delivery-loading-image').fadeOut();}var b=jQuery('#coderockz_woo_delivery_calendar_filter_section').data('animation_background');if(typeof b!==typeof undefined&&b!==!1){var e=b.red;var f=b.green;var g=b.blue;}else{var e;var f;var g;}var a='';a+='<div class="coderockz-woo-delivery-loading-image" style="background-color:rgba('+e+','+f+','+g+', 0.6)!important">',a+='<div class="coderockz-woo-delivery-loading-gif">',a+='<img src="'+jQuery('#coderockz_woo_delivery_calendar_filter_section').data('animation_path')+'" alt="" style="max-width:60px!important"/>',a+='</div>',a+='</div>',jQuery('#coderockz_woo_delivery_calendar_filter_section').append(a);var h='';var i='';orderStatus=jQuery('#coderockz_woo_delivery_calendar_order_status_filter').data('order_status'),orderStatus=orderStatus.toString(),orderStatus=orderStatus.split(',');var d=orderStatus;deliveryTypeFilterText=jQuery('#coderockz_woo_delivery_calendar_delivery_type_filter').data('delivery_type_filter_text'),jQuery('#coderockz_woo_delivery_calendar_delivery_type_filter').selectWoo({dropdownCssClass:'coderockz-order-page-filter-no-search',placeholder:deliveryTypeFilterText}),filterTypeFilterText=jQuery('#coderockz_woo_delivery_calendar_filter_type_filter').data('filter_type_filter_text'),jQuery('#coderockz_woo_delivery_calendar_filter_type_filter').selectWoo({dropdownCssClass:'coderockz-order-page-filter-no-search',placeholder:filterTypeFilterText}),orderStatusFilterText=jQuery('.coderockz_woo_delivery_calendar_order_status_filter').data('order_status_filter_text'),jQuery('.coderockz_woo_delivery_calendar_order_status_filter').selectize({placeholder:orderStatusFilterText,plugins:['remove_button'],render:{item:function(a,b){return'<div class="item coderockz_woo_delivery_calendar_order_status_filter_item">'+b(a.text)+'</div>';}}}),jQuery(document).on('change','#coderockz_woo_delivery_calendar_delivery_type_filter',function(a){h=jQuery(this).val(),c();}),jQuery(document).on('change','#coderockz_woo_delivery_calendar_filter_type_filter',function(a){i=jQuery(this).val(),c();}),jQuery(document).on('change','#coderockz_woo_delivery_calendar_order_status_filter',function(a){jQuery(this).val()!=null?d=jQuery(this).val():d=orderStatus,c();}),c();});