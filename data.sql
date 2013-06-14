INSERT INTO `asc_checkout_pm_sm_settings` (`checkout_pm_sm_settings_module_id`, `checkout_pm_sm_settings_module_class_name`, `checkout_pm_sm_settings_module_group`, `checkout_pm_sm_settings_status_active_value_id`, `checkout_pm_sm_settings_status_selected_value_id`, `checkout_pm_sm_settings_sort_order`) VALUES
('F2A8C192-7C29-7218-B135-285582382EF6', 'Payment_Module_Wtp_CC', 'payment', 1, 1, 2);

INSERT INTO `asc_module` (`module_name`, `module_groups`, `module_description`, `module_version`, `module_author`, `module_contact`, `module_system`, `module_date`, `module_active`, `module_updated`) VALUES
('Payment_Module_Wtp_CC', 'PaymentModule,OnlineCC', '"Paysera" method payment module. See payment documentation', '0.1', 'EVP International', '', '', '2011-04-27', '1', '1');

INSERT INTO `asc_module_class` (`module_class_name`, `module_class_file`, `module_class_type`, `module_class_active`) VALUES
('Payment_Module_Wtp_CC', 'payment_module_wtp_cc/payment_module_wtp_cc_api.php', 'api', '1');

INSERT INTO `asc_checkout_pm_sm_currency_acceptance_rules` (`checkout_pm_sm_currency_acceptance_rules_module_id`, `checkout_pm_sm_currency_acceptance_rules_rule_name`, `checkout_pm_sm_currency_acceptance_rules_rule_selected`) VALUES
('F2A8C192-7C29-7218-B135-285582382EF6', 'MAIN_STORE_CURRENCY', 'true');

INSERT INTO `asc_resource_labels` (`res_prefix`, `res_label`, `res_text`) VALUES
('PM_WTP', 'FORM_SUBTITLE', 'Details'),
('PM_WTP', 'MODE_001', 'Enabled'),
('PM_WTP', 'MODE_002', 'Disabled'),
('PM_WTP', 'MODULE_DESCR', 'Paysera.com Module'),
('PM_WTP', 'MODULE_DESCR_FIELD_NAME', 'Module Description'),
('PM_WTP', 'MODULE_DESCR_FIELD_NAME_DESCR', ''),
('PM_WTP', 'MODULE_METHOD_ID_FIELD_NAME', 'Your Paysera.com project ID'),
('PM_WTP', 'MODULE_METHOD_MODE_FIELD_NAME', 'Test mode?'),
('PM_WTP', 'MODULE_METHOD_NAME_FIELD_NAME', 'Method Name'),
('PM_WTP', 'MODULE_METHOD_NAME_FIELD_NAME_DESCR', 'This is the name of the payment method that will be displayed to visitors of the ecommerce storefront during checkout.'),
('PM_WTP', 'MODULE_METHOD_PASS_FIELD_NAME', 'Your Paysera.com project password'),
('PM_WTP', 'MODULE_MODE_FIELD_NAME', 'Test mode:'),
('PM_WTP', 'MODULE_NAME', 'Paysera'),
('PM_WTP', 'MODULE_CERT_MORE_FIELD_NAME', 'Paysera'),
('PM_WTP', 'MODULE_ORDER_STATUS_001', 'default'),
('PM_WTP', 'MODULE_ORDER_STATUS_002', 'Delivered'),
('PM_WTP', 'MODULE_ORDER_STATUS_003', 'Pending'),
('PM_WTP', 'MODULE_ORDER_STATUS_004', 'Processing'),
('PM_WTP', 'MODULE_ORDER_STATUS_FIELD_NAME', 'Set Order Status:'),
('PM_WTP', 'MODULE_PAYMENT_WTP_LOG_EVENT_DESCRIPTION', 'Event Description: '),
('PM_WTP', 'MODULE_PAYMENT_WTP_LOG_EVENT_TIME', 'Event Time: '),
('PM_WTP', 'MODULE_PAYMENT_WTP_LOG_MESSAGE_INCOMMING', 'Order added via Paysera.com method.'),
('PM_WTP', 'MODULE_PAYMENT_WTP_LOG_MODULE_ID', 'Payment Module ID: '),
('PM_WTP', 'MODULE_PAYMENT_WTP_LOG_MODULE_NAME', 'Payment Module Name: '),
('PM_WTP', 'MODULE_PAYMENT_WTP_MICROSECONDS', 'microseconds'),
('PM_WTP', 'MODULE_PAYMENT_WTP_SECONDS', 'seconds'),
('PM_WTP', 'MODULE_PAYMENT_WTP_TIMELINE_HEADER', 'Paysera.com Payment Module is used to process payment.'),
('PM_WTP', 'MODULE_STATUS_ACTIVE', 'Active'),
('PM_WTP', 'MODULE_STATUS_INACTIVE', 'Inactive'),
('PM_WTP', 'MODULE_STATUS_FIELD_NAME', 'Module status'),
('PM_WTP', 'MODULE_STATUS_FIELD_NAME_DESCR', 'There are two possible statuses for this module - active and inactive'),
('PM_WTP', 'MODULE_TYPE', 'Payment Module');