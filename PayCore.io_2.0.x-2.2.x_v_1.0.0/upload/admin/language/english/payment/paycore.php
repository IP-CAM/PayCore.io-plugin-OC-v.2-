<?php
$_['heading_title']           = 'PayCore.io';

// Tab
$_['tab_emails']              = 'E-mail уведомления';
$_['tab_settings']            = 'Payment settings';
$_['tab_log']                 = 'Log';
$_['tab_information']         = 'Information';

// Text
$_['text_extension']          = 'Payments';
$_['text_success']            = 'PayCore.io module settings successfully update!';
$_['text_clear_log_success']  = 'Module log successfully cleaned up!';
$_['text_confirm']            = 'Deleting a log can not be undone! Are you sure you want to do this?';
$_['text_paycore']            = '<a style="cursor: pointer;" onclick="window.open(\'https://paycore.io\');"><img src="view/image/payment/paycore.png" alt="PayCore.io" width="150px" title="PayCore.io"/></a>';
$_['text_order_status_cart']  = 'Pending';
$_['text_log_off']            = 'Disabled';
$_['text_log_short']          = 'Partial (operation results only)';
$_['text_log_full']           = 'Complete (all queries)';


$_['text_info']               =  'After <a href="https://www.dashboard.paycore.io" target="_blank">sign up</a> into PayCore.io and creating your payment page you should provide keys for configuring this module. You can get keys in <a href="https://dashboard.paycore.io/checkout/payment-pages" target="_blank">Checkout settings page</a>, choose needed one and go to Integration tab. You need to enter these keys on "Payment settings" tab of this module.';


// Text FT
$_['text_order_id_ft']              = '№ заказа';
$_['text_store_name_ft']            = 'Название магазина';
$_['text_logo_ft']                  = 'Логотип магазина';
$_['text_products_ft']              = 'Список купленных товаров';
$_['text_total_ft']                 = 'Итого';
$_['text_customer_firstname_ft']    = 'Имя Отчество покупателя';
$_['text_customer_lastname_ft']     = 'Фамилия покупателя';
$_['text_customer_group_ft']        = 'Группа покупателя';
$_['text_customer_email_ft']        = 'e-mail покупателя';
$_['text_customer_telephone_ft']    = 'Телефон покупателя';
$_['text_order_status_ft']          = 'Статус заказа';
$_['text_comment_ft']               = 'Комментарий покупателя к заказу';
$_['text_ip_ft']                    = 'IP адрес покупателя';
$_['text_date_added_ft']            = 'Дата и время добавления заказа';
$_['text_date_modified_ft']         = 'Дата и время изменения заказа';

// Entry
$_['entry_status']                  = 'Status';
$_['entry_order_status']            = 'Order status after payment';
$_['entry_geo_zone']                = 'Geo zone';
$_['entry_sort_order']              = 'Sort order';
$_['entry_minimal_order']           = 'Min order amount';
$_['entry_maximal_order']           = 'Max order amount';
$_['entry_order_confirm_status']    = 'Order status after confirmation';
$_['entry_order_fail_status']       = 'Order status after failure';
$_['entry_order_canceled_status']  = 'Order status after cancellation';
$_['entry_order_expired_status']    = 'Order status after expiration';
$_['entry_title']                   = 'Title';
$_['entry_instruction']             = 'Payment instruction';

$_['entry_public_key']         = 'Public key';
$_['entry_secret_key']         = 'Secret key';
$_['entry_test_public_key']    = 'Test public key';
$_['entry_test_mode']          = 'Test mode';
$_['entry_test_secret_key']    = 'Test secret key';

$_['entry_log']                = 'Log';
$_['entry_log_file']           = 'Log file';

// Placeholder
$_['placeholder_instruction']  = 'Dear customer! Please wait for our manager contact you to confirm the order.';

// Help
$_['help_order_confirm_status'] = 'After click "Confirm" button order gets in this state';
$_['help_order_status']         = 'After success payment order gets in this state';
$_['help_minimal_order']        = 'If order amount is not empty and less then this value, PayCore.io payment method will not available.<br />Example: 190.90';
$_['help_maximal_order']        = 'If order amount is not empty and more then this value, PayCore.io payment method will not available.<br />Example: 5000.01';
$_['help_order_fail_status']    = 'If payment will fail order gets in this state';
$_['help_order_canceled_status']    = 'If payment will canceled order gets in this state';
$_['help_order_expired_status']    = 'If payment will expired order gets in this state';

$_['help_title']                = 'Payment method title on order page';
$_['help_instruction']          = 'Payment instruction will show on order confirmation page, if empty instruction will not show.';

$_['help_public_key']      = 'Your checkout public key on «PayCore.io». You can get it on «Checkout» page (Integration). Example: pk_live_EnYL6gs85wRAFoa6RqdrWed4BMqiijHw9flc6uErx0c';
$_['help_secret_key']      = 'Your checkout secret key on «PayCore.io». You can get it on «Checkout» page (Integration). Example: sk_live_s8R4L6gia6EnYjiwfBMqRAFHw9dlc6uErx0rWq5oced';
$_['help_test_public_key'] = 'Your checkout test public key on «PayCore.io». You can get it on «Checkout» page (Integration). Example: pk_test_qdrWed4Foa6RBMqiw9flc6uErx0ciEnYL6gs85wRAjH';
$_['help_test_secret_key'] = 'Your checkout test secret key on «PayCore.io». You can get it on «Checkout» page (Integration). Example: sk_test_EnYjiwfBMgia6HR4L69wqRA6uErFs8dWqlcxce0r5od';
$_['help_test_mode']       = 'In test mode you can check preference of module and PayCore.io by processing test payments. For accept real payments you should disable test mode.';

$_['help_log_file']      = 'Last %d rows from log file.';
$_['help_log']           = 'Log file: /system/storage/logs/%s';

// Error
$_['error_permission']   = 'You don\'t have access to manage PayCore.io module!';
$_['error_clear_log']    = 'You don\'t have access to clear module log!';
$_['error_form']         = 'You should to fill field "%s" (tab "%s")!';
?>