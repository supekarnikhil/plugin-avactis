<?php
/***********************************************************************
| Avactis (TM) Shopping Cart software developed by Pentasoft Corp.
| http://www.avactis.com
| -----------------------------------------------------------------------
| All source codes & content (c) Copyright 2004-2010, Pentasoft Corp.
| unless specifically noted otherwise.
| =============================================
| This source code is released under the Avactis License Agreement.
| The latest version of this license can be found here:
| http://www.avactis.com/license.php
|
| By using this software, you acknowledge having read this license agreement
| and agree to be bound thereby.
|
 ***********************************************************************/
?><?php

/**
 *
 * @package PaymentModuleCod
 * @author Egor Makarov
 */
class Payment_Module_Wtp_CC extends pm_sm_api {
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * PaymentModuleCod  constructor.
     */
    function Payment_Module_Wtp_CC()
    {
        global $application;
        $obj = &$application->getInstance('MessageResources',"payment-module-wtp-messages", "AdminZone");
        //: gen real UID
        $info = $this->getInfo();

        $settings = $this->getSettings();
        $this->Settings = $settings;

        $this->OrderHistoryMessageTag = $obj->getMessage('MODULE_PAYMENT_WTP_LOG_MODULE_ID') .
                $info["GlobalUniquePaymentModuleID"] . "\n" .
                $this->Settings["MODULE_NAME"];
    }


    function getmicrotime()
    {
        global $application;
        $obj = &$application->getInstance('MessageResources',"payment-module-wtp-messages", "AdminZone");

        list($usec, $sec) = explode(" ", microtime());

        return $obj->getMessage('MODULE_PAYMENT_WTP_LOG_EVENT_TIME') .
                (float)($usec + $sec) . " " .
                $obj->getMessage('MODULE_PAYMENT_WTP_SECONDS');
    }

    /**
     * Installs the specified module in the system.
     *
     * The install() method is called statically.
     * To call other methods of this class from this method,
     * the static call is used, for example,
     * Payment_Module_Paypal_CC::getTables() instead of $this->getTables().
     */
    function install()
    {
        global $application;
        $obj = &$application->getInstance('MessageResources',"payment-module-wtp-messages", "AdminZone");

        $tables = Payment_Module_Wtp_CC::getTables();
        $query = new DB_Table_Create($tables);

        $table = 'pm_wtp_settings';           
        $columns = $tables[$table]['columns'];

        $query = new DB_Insert($table);
        $query->addInsertValue(1, $columns['id']);
        $query->addInsertValue("MODULE_NAME", $columns['key']);
        $query->addInsertValue('s:'._ml_strlen($obj->getMessage('MODULE_NAME')).':"'.$obj->getMessage('MODULE_NAME').'";', $columns['value']);
        $application->db->getDB_Result($query);
        
        $query = new DB_Insert($table);
        $query->addInsertValue(2, $columns['id']);
        $query->addInsertValue("MODULE_METHOD_ID", $columns['key']);
        $query->addInsertValue('s:'._ml_strlen("0").':"'. "0" .'";', $columns['value']);
        $application->db->getDB_Result($query);
        
        $query = new DB_Insert($table);
        $query->addInsertValue(3, $columns['id']);
        $query->addInsertValue("MODULE_METHOD_PASS", $columns['key']);
        $query->addInsertValue('s:'._ml_strlen("0").':"'. "0" .'";', $columns['value']);
        $application->db->getDB_Result($query);
        
        $query = new DB_Insert($table);
        $query->addInsertValue(4, $columns['id']);
        $query->addInsertValue("MODULE_MODE", $columns['key']);
        $query->addInsertValue('s:'._ml_strlen("0").':"'. "0" .'";', $columns['value']);
        $application->db->getDB_Result($query);
    }

    /**
     * This method is invoked by Checkout module in CZ (Customer Zone). It is
     * also invoked in AZ (Admin Zone).
     * returns - Boolean - Show this module during the process of Checkout in
     * Customer Zone or not?
     */
    function isActive()
    {
        global $application;
        $active_modules = modApiFunc("Checkout","getActiveModules","payment");
        return array_key_exists($this->getUid(), $active_modules);
    }

    function getUid()
    {
        include(dirname(__FILE__)."/includes/uid.php");
        return $uid;
    }

    function getInfo()
    {
        global $application;
        $obj = &$application->getInstance('MessageResources',"payment-module-wtp-messages", "AdminZone");

        $settings = $this->getSettings();
        $this->Settings = $settings;

        return array("GlobalUniquePaymentModuleID" => $this->getUid(),
                     "Name"        => $this->Settings["MODULE_NAME"],
                     "StatusMessage" => ($this->IsActive())? "<span class=\"status_online\">".$obj->getMessage('MODULE_STATUS_ACTIVE')."</span>":$obj->getMessage('MODULE_STATUS_INACTIVE')."<span class=\"required\">*</span>",

					 "PreferencesAZViewClassName" 	=> "CheckoutPaymentModuleWtpInputAZ",
                     "CZInputViewClassName" 		=> "CheckoutPaymentModuleWtpInput",
                     "CZOutputViewClassName" 		=> "CheckoutPaymentModuleWtpOutput",
                     "APIClassName" 				=> __CLASS__
                    );
    }

    /**
     * Clears the Settings table.
     */
    function clearSettingsInDB()
    {
        global $application;
        $query = new DB_Delete('pm_wtp_settings');
        $application->db->getDB_Result($query);
    }

    /**
     * Gets current module settings from Settings.
     *
     * @return array - module settings array
     */
    function getSettings()
    {
        $result = execQuery('SELECT_PM_SM_SETTINGS',array('ApiClassName' => __CLASS__, 'settings_table_name' => 'pm_wtp_settings'));
        $Settings = array();
        for ($i=0; $i<sizeof($result); $i++)
        {
            $Settings[$result[$i]['set_key']] = unserialize($result[$i]['set_value']);
        }
        if (isset($this))
        {
            $this->Settings = $Settings;
        }
        return $Settings;
    }

    /**
     * Sets up module attributes and logs it to the database.
     *
     * @param array $Settings - module settings array.
     */
    function setSettings($Settings)
    {
        global $application;
        $this->clearSettingsInDB();
        $tables = $this->getTables();
        $columns = $tables['pm_wtp_settings']['columns'];

        foreach($Settings as $key => $value)
        {
            $query = new DB_Insert('pm_wtp_settings');
            $query->addInsertValue($key, $columns['key']);
            $query->addInsertValue(serialize($value), $columns['value']);
            $application->db->getDB_Result($query);

            $inserted_id = $application->db->DB_Insert_Id();
        }
    }

    /**
     * Sets up module attributes and logs it to the database.
     *
     * @param array $Settings -  module settings array. The undefined parameter values
     * remain unchanged.
     */
    function updateSettings($Settings)
    {
        global $application;
        $tables = $this->getTables();
        $columns = $tables['pm_wtp_settings']['columns'];

        foreach($Settings as $key => $value)
        {
            $query = new DB_Update('pm_wtp_settings');
            $query->addUpdateValue($columns['value'], serialize($value));
            $query->WhereValue($columns['key'], DB_EQ, $key);
            $application->db->getDB_Result($query);
        }
    }

    /**
     *                      Shipping&Handling.                                     FS_OO, FH_OO.
     */
    function getPerOrderPaymentModuleShippingFee()
    {
        $settings = Payment_Module_Wtp_CC::getSettings();
        return floatval($settings["PER_ORDER_SHIPPING_FEE"]);
    }

    /**
     * It is called from Checkout. It returns the cost of selected delivery
     * method. The cost should be 0 for the majority of payment methods.
     */
    function getPaymentCost($method_id)
    {
        return 0;
    }

    /**
     * Converts the value of the monetary sum to be used _out_ ASC.
     * If the price equals PRICE_N_A, then it is changed to 0.0.
     */
    function export_PRICE_N_A($price)
    {
        return ($price == PRICE_N_A) ? 0.0 : $price;
    }

    /**
     * Prepares and returns necessary data, passed to the payment gateway.
     *
     * @ not all data is defined
     */
	function getConfirmationData($orderId){
		
        global $application;
        loadCoreFile('aal.class.php');

        $request = new Request();
        $request->setView(CURRENT_REQUEST_URL);
        $request->setAction("UpdatePaymentStatus");
        $request->setKey("asc_oid", $orderId);
        
        $self_link 		= $request->getURL("", true);

        $currency_id 	= modApiFunc("Localization", "whichCurrencySendOrderToPaymentShippingGatewayIn", $orderId, $this->getUid());
        $currency 		= modApiFunc("Localization", "getCurrencyCodeById", $currency_id);

        $ocntr 			= modApiFunc("Location","getCountryCode",modApiFunc("Configuration","getValue",SYSCONFIG_STORE_OWNER_COUNTRY));
        $bn_code 		= "PentasoftCorp_Cart_WPS_" . $ocntr;

        $orderInfo 		= modApiFunc("Checkout", "getOrderInfo", $orderId, $currency_id);

        $discount 		= $this->export_PRICE_N_A(modApiFunc("Checkout", "getOrderPrice", "DiscountsSum", $currency_id));
        $amount 		= $this->export_PRICE_N_A(modApiFunc("Checkout", "getOrderPrice", "Subtotal", $currency_id) - $discount);
        $moduleData		= $this->getSettings();
        
        require_once 'libwebtopay/WebToPay.php';
        
        try {
            $buildRequest = WebToPay::buildRequest(array(
                    'projectid'     => $moduleData['MODULE_METHOD_ID'],
                    'sign_password' => $moduleData['MODULE_METHOD_PASS'],

                    'orderid'       => $orderInfo['ID'],
                    'amount'        => intval(number_format($orderInfo['Total'],2,'','')),
                    'currency'      => $currency,

                    'accepturl'     => $self_link ."&status=return",
                    'cancelurl'     => $self_link ."&status=cancel",
                    'callbackurl'   => $self_link ."&status=notify",
            
                    'payment'       => '',
                    'country'       => 'LT',

                    'logo'          => '',
                    'p_firstname'   => $orderInfo['Billing']['attr']['Firstname']['value'],
                    'p_lastname'    => $orderInfo['Billing']['attr']['Lastname']['value'],
                    'p_email'       => $orderInfo['Billing']['attr']['Email']['value'],
                    'p_street'      => $orderInfo['Billing']['attr']['Streetline1']['value'].' '.$orderInfo['Billing']['attr']['Streetline2']['value'],
                    'p_city'        => $orderInfo['Billing']['attr']['City']['value'],
                    //'p_state'       => '',
                    'p_zip'         => $orderInfo['Billing']['attr']['Postcode']['value'],
                    //'p_countrycode' => $p_countrycode,
                    'test'          => $moduleData['MODULE_MODE'],
                ));
        }
        catch (WebToPayException $e) {
            echo get_class($e).': '.$e->getMessage();
        }
        
        $acceptURL 		= str_replace('&amp;','&',$buildRequest['accepturl']);
		$cancelURL 		= str_replace('&amp;','&',$buildRequest['cancelurl']);
		$callbackURL 	= str_replace('&amp;','&',$buildRequest['callbackurl']);
        
        
        $confirmationData = array(
                "FormAction" => WebToPay::PAY_URL,
                "FormMethod" => "POST",
                "DataFields" => array(
        				'projectid' 	=> $buildRequest['projectid'],
        				'orderid'		=> $buildRequest['orderid'],
        				
        				'amount'		=> $buildRequest['amount'],
        				'currency'		=> $buildRequest['currency'],
        				'lang'			=> $buildRequest['lang'],
        
        				'accepturl' 	=> $buildRequest['accepturl'],
        				'cancelurl' 	=> $buildRequest['cancelurl'],
        				'callbackurl' 	=> $buildRequest['callbackurl'],
        
        				'payment'		=> $buildRequest['payment'],
        				'country'		=> $buildRequest['country'],

        				'p_firstname'   => $buildRequest['p_firstname'],
	                    'p_lastname'    => $buildRequest['p_lastname'],
	                    'p_email'       => $buildRequest['p_email'],
	                    'p_street'      => $buildRequest['p_street'],
	                    'p_city'        => $buildRequest['p_city'],
	                    'p_zip'         => $buildRequest['p_zip'],
        
        				'test'			=> $buildRequest['test'],
        				'version'		=> $buildRequest['version'],
        				'sign'			=> $buildRequest['sign'],
                    ));
                    
    //=========================== logging request ========================

        $msgObj	= $application->getInstance("MessageResources", "payment-module-paypal-messages", "AdminZone");
        $title 	= $msgObj->getMessage("MODULE_PAYMENT_TIMELINE_HEADER_CONFIRMATION_DATA");
        $title 	= str_replace('{ORDER_ID}', $orderId, $title);
        $this->addRequestLog("LOG_PM_INPUT", "Payment Module Logs", $title, prepareArrayDisplay($confirmationData));

    //=========================== logging request ========================

        return $confirmationData;
    }


    /**
     * Processes data on updating the order status, come from the payment gateway.
     * The flag &$bStop is set by the payment module. If it is true on return,
     * then Checkout module stops the data process, come from the payment gateway.
     */
    function processData($data, $order_id){
    	
    	global $application;
        $msg = &$application->getInstance('MessageResources',"payment-module-wtp-messages", "AdminZone");
		require_once 'libwebtopay/WebToPay.php';
        //
        // The checking on the delivery to the selected place might be
        // added here
        //
        
        if($_GET[WebToPay::PREFIX.'status'] == 1) {
        	$this->validateOrder($order_id);
        }

        $EventType = "ConfirmationSuccess";
        $result = modApiFunc("Checkout", "addOrderHistory", $order_id,
            $this->OrderHistoryMessageTag . "\n" . $this->getmicrotime() . "\n" .
            $msg->getMessage('MODULE_PAYMENT_WTP_LOG_EVENT_DESCRIPTION') .
            $msg->getMessage('MODULE_PAYMENT_WTP_LOG_MESSAGE_INCOMMING'));

        return array("EventType" => $EventType, "statusChanged" => $result);
    }
    
    function validateOrder($orderID){
    	
    	if(ltrim($_GET[WebToPay::PREFIX.'orderid'], "0") != $orderID) {
    		exit('Order ID mismatch!');	
    	}
    	
        global $application;
        $msg 		= &$application->getInstance('MessageResources',"payment-module-wtp-messages", "AdminZone");
		$moduleData	= $this->getSettings();

		$query 	= new DB_Select();
        $query->addSelectTable('order_prices');
        $query->addSelectField('*');
        $query->WhereValue('order_id', DB_EQ, $orderID);
        $Order	= $application->db->getDB_Result($query);
        
	    try {
	        WebToPay::toggleSS2(true);
	        $response = WebToPay::checkResponse($_GET, array(
		        'projectid'     => $moduleData['MODULE_METHOD_ID'],
		        'sign_password' => $moduleData['MODULE_METHOD_PASS'],
	    	));
	    } catch (Exception $e) {
	        exit(get_class($e).': '.$e->getMessage());
	    }
	    
    	if (intval(number_format($Order[0]['order_total'],2,'','')) > $_GET[WebToPay::PREFIX.'amount']){
    		exit('Bad amount!');
    	}
    	else if ($Order[0]['currency_code'] != $_GET[WebToPay::PREFIX.'currency']){
    		exit('Bad currency!');
    	} else {
            modApiFunc("Checkout", "UpdatePaymentStatusInDB", $orderID, 2, 'Payment accepted.');
  			exit('OK');
    	}
    }

    /**
     * Uninstalls the module.
     * It deletes all module tables.
     *
     * The uninstall() method is called statically.
     * To call other methods of this class from this method,
     * the static call is used, for example,
     * Payment_Module_Paypal_CC::getTables() instead of $this->getTables()
     */
    function uninstall()
    {
        $query = new DB_Table_Delete(Payment_Module_Wtp_CC::getTables());
        global $application;
        $application->db->getDB_Result($query);
    }

    /**
     * Gets the array of meta description of module tables.
     *
     * The array structure of meta description of the table:
     * <code>
     *      $tables = array ();
     *      $table_name = 'table_name';
     *      $tables[$table_name] = array();
     *      $tables[$table_name]['columns'] = array
     *      (
     *          'fn1'               => 'table_name.field_name_1'
     *         ,'fn2'               => 'table_name.field_name_2'
     *         ,'fn3'               => 'table_name.field_name_3'
     *         ,'fn4'               => 'table_name.field_name_4'
     *      );
     *      $tables[$table_name]['types'] = array
     *      (
     *          'fn1'               => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
     *         ,'fn2'               => DBQUERY_FIELD_TYPE_INT .' NOT NULL'
     *         ,'fn3'               => DBQUERY_FIELD_TYPE_CHAR255
     *         ,'fn4'               => DBQUERY_FIELD_TYPE_TEXT
     *      );
     *      $tables[$table_name]['primary'] = array
     *      (
     *          'fn1'       # several key fields may be used, e.g. - 'fn1', 'fn2'
     *      );
     *      $tables[$table_name]['indexes'] = array
     *      (
     *          'index_name1' => 'fn2'      # several fields can be used in one index, e.g. - 'fn2, fn3'
     *         ,'index_name2' => 'fn3'
     *      );
     * </code>
     *
     * @return array -  the meta description of module tables
     */
    function getTables(){
    	
        static $tables;

        if (is_array($tables))
        {
            return $tables;
        }

        $tables = array ();

        $settings = 'pm_wtp_settings';
        $tables[$settings] = array();
        $tables[$settings]['columns'] = array
            (
                'id'	=> $settings.'.pm_wtp_setting_id'
               ,'key'	=> $settings.'.pm_wtp_setting_key'
               ,'value'	=> $settings.'.pm_wtp_setting_value'
            );
        $tables[$settings]['types'] = array
            (
                'id'	=> DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'key'	=> DBQUERY_FIELD_TYPE_CHAR50
               ,'value'	=> DBQUERY_FIELD_TYPE_CHAR255
            );
        $tables[$settings]['primary'] = array
            (
                'id'
            );

        global $application;
        return $application->addTablePrefix($tables);
    }

    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    /**
     * A flag, which is stored in the database. It indicates, if the specified
     * module is mapped in CZ Checkout.
     */
    var $bActive;

    var $OrderHistoryMessageTag;
    /**#@-*/

}
?>