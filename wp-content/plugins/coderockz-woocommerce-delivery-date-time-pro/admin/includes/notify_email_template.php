<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title></title>
        <link href="https://fonts.googleapis.com/css?family=Red+Hat+Display:500&display=swap" rel="stylesheet">
    </head>
    <body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" style="padding: 0;">
        <?php
        if($notify_logo_path != "") {
        ?>
        <img src="<?= $notify_logo_path ?>" alt="" style="display:block;margin:0 auto;max-width:120px;width:120px;margin-top:70px">
        <?php } ?>
        <div id="wrapper" dir="ltr" style="font-family: 'Red Hat Display', sans-serif;font-weight:500;background-color: #f7f7f7; margin: 0; padding: 40px 0 70px 0; width: 100%; -webkit-text-size-adjust: none;">
            <table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
                <tr>
                    <td align="center" valign="top">
                        <table border="0" cellpadding="0" cellspacing="0" width="600" id="template_container" style="background-color: #ffffff; border: 1px solid #dedede; box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1); border-radius: 3px;">
                            <tr>
                                <td align="center" valign="top">
                                    <!-- Header -->
                                    <table border="0" cellpadding="0" cellspacing="0" width="100%" id="template_header" style='color: #ffffff; border-bottom: 0; font-weight: bold; line-height: 100%; vertical-align: middle; border-radius: 3px 3px 0 0;'>
                                        <tr>
                                            <td id="header_wrapper" style="padding: 36px 48px; display: block;">
                                                <h1 style='font-size: 28px; font-weight: 300; line-height: 150%; margin: 0; text-align: left; text-shadow: 0 1px 0 #ab79a1; color: <?= $email_heading_color ?>'><?= $email_heading ?></h1>
                                            </td>
                                        </tr>
                                    </table>
                                    <!-- End Header -->
                                </td>
                            </tr>
                            <tr>
                                <td align="center" valign="top">
                                    <!-- Body -->
                                    <table border="0" cellpadding="0" cellspacing="0" width="600" id="template_body">
                                        <tr>
                                            <td valign="top" id="body_content" style="background-color: #ffffff;">
                                                <!-- Content -->
                                                <table border="0" cellpadding="20" cellspacing="0" width="100%">
                                                    <tr>
                                                        <td valign="top" style="padding: 0px 48px 32px 48px;">
                                                            <div id="body_content_inner" style='color: #636363; font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif; font-size: 14px; line-height: 150%; text-align: left;'>



<div style="margin-bottom: 40px;">
    <table class="td" cellspacing="0" cellpadding="6" border="1" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; width: 100%;">
        <thead>
            <tr>
                <th class="td" scope="col" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; padding: 12px; text-align: left;"><?= $notify_email_product_text; ?></th>
                <th class="td" scope="col" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; padding: 12px; text-align: left;"><?= $notify_email_quantity_text; ?></th>
                <th class="td" scope="col" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; padding: 12px; text-align: left;"><?= $notify_email_price_text; ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($order->get_items() as $item_id => $item) {?>
                <tr class="order_item">
        <td class="td" style="color: #636363; border: 1px solid #e5e5e5; padding: 12px; text-align: left; vertical-align: middle; word-wrap: break-word;"><?= $item->get_name(); ?></td>
        <td class="td" style="color: #636363; border: 1px solid #e5e5e5; padding: 12px; text-align: left; vertical-align: middle;"><?= $item->get_quantity(); ?></td>
        <td class="td" style="color: #636363; border: 1px solid #e5e5e5; padding: 12px; text-align: left; vertical-align: middle;">
            <span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol"><?= $currency_symbol; ?><?= round($item->get_total(),2); ?></td>
    </tr>
    <?php } ?>
    
        </tbody>
        <tfoot>
                    <?php
                    if($shipping_method != "") {
                    ?>
                    <tr>
                        <th class="td" scope="row" colspan="2" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; padding: 12px; text-align: left;"><?= $notify_email_shipping_text ?></th>
                        <td class="td" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; padding: 12px; text-align: left;"><?= $shipping_method ?><?= $shipping_method_amount ?></td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <th class="td" scope="row" colspan="2" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; padding: 12px; text-align: left;"><?= $notify_email_payment_text; ?></th>
                        <td class="td" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; padding: 12px; text-align: left;"><?= $payment_method; ?></td>
                    </tr>
                                        <tr>
                        <th class="td" scope="row" colspan="2" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; padding: 12px; text-align: left;"><?= $notify_email_total_text; ?></th>
                        <td class="td" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; padding: 12px; text-align: left;"><span class="woocommerce-Price-amount amount"><?= $order_total; ?></td>
                    </tr>
                    <?php
            if($pickup_date != "") {
            ?>
                    <tr>
                        <th class="td" scope="row" colspan="2" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; padding: 12px; text-align: left;"><?= $pickup_date_field_label; ?></th>
                        <td class="td" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; padding: 12px; text-align: left;"><?= $pickup_date; ?></td>
                    </tr>
                    <?php
            } 
            if($delivery_date != "") {
              ?>
              <tr>
                        <th class="td" scope="row" colspan="2" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; padding: 12px; text-align: left;"><?= $delivery_date_field_label; ?></th>
                        <td class="td" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; padding: 12px; text-align: left;"><?= $delivery_date; ?></td>
                    </tr>
                    <?php
            }
            ?>
            <?php if($delivery_time != "") {?>
                                        <tr>
                        <th class="td" scope="row" colspan="2" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; padding: 12px; text-align: left;"><?= $delivery_time_field_label; ?></th>
                        <td class="td" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; padding: 12px; text-align: left;"><?= $delivery_time; ?></td>
                    </tr>
                    <?php } ?>
           <?php if($pickup_time != "") {?>
           <tr>
                        <th class="td" scope="row" colspan="2" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; padding: 12px; text-align: left;"><?= $pickup_time_field_label; ?></th>
                        <td class="td" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; padding: 12px; text-align: left;"><?= $pickup_time; ?></td>
                    </tr>
                    <?php } ?>
                    <?php if($pickup_location != "") {?>
           <tr>
                        <th class="td" scope="row" colspan="2" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; padding: 12px; text-align: left;"><?= $pickup_location_field_label; ?></th>
                        <td class="td" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; padding: 12px; text-align: left;"><?= $pickup_location; ?></td>
                    </tr>
                    <?php } ?>
                    <?php if($additional_note != "") {?>
           <tr>
                        <th class="td" scope="row" colspan="2" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; padding: 12px; text-align: left;"><?= $additional_field_field_label; ?></th>
                        <td class="td" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; padding: 12px; text-align: left;"><?= $additional_note; ?></td>
                    </tr>
                    <?php } ?>
                            </tfoot>
    </table>
</div>

<table id="addresses" cellspacing="0" cellpadding="0" border="0" style="width: 100%; vertical-align: top; margin-bottom: 40px; padding: 0;">
    <tr>
        <td valign="top" width="50%" style="text-align: left; border: 0; padding: 0;">
            <h2 style='color: <?= $email_heading_color ?>; display: block; font-size: 18px; font-weight: bold; line-height: 130%; margin: 0 0 18px; text-align: left;'><?= $notify_email_billing_address_heading; ?></h2>

            <address class="address" style="padding: 12px; color: #636363;">
                <?= $billing_address; ?><br><a href="tel:01756666622" style="color: #96588a; font-weight: normal; text-decoration: underline;"><?= $order->get_billing_phone(); ?></a>                                                  <br><?= $order_email; ?></address>
        </td>
                    <td valign="top" width="50%" style="text-align: left; padding: 0;">
                <h2 style='color: <?= $email_heading_color ?>; display: block; font-size: 18px; font-weight: bold; line-height: 130%; margin: 0 0 18px; text-align: left;'><?= $notify_email_shipping_address_heading; ?></h2>

                <address class="address" style="padding: 12px; color: #636363;"><?= $shipping_address; ?></address>
            </td>
            </tr>
</table>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </table>
                                                <!-- End Content -->
                                            </td>
                                        </tr>
                                    </table>
                                    <!-- End Body -->
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

            </table>
        </div>
    </body>
</html>

