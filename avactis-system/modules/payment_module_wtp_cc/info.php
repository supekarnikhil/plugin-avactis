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
 * @package PaymentModuleCod
 * @author Egor Makarov
 */

$moduleInfo = array
    (
        'name'         => 'Payment_Module_Wtp_CC', # this is also a main class name
        'shortName'    => 'PM_WTP',
        'groups'       => 'PaymentModule,OnlineCC', //  change to "...,Offline in release"
        'description'  => '"Paysera" method payment module. See payment documentation',
        'version'      => '0.1',
        'author'       => 'EVP International',
        'contact'      => 'info@evp.lt',
        'systemModule' => false,
        'mainFile'     => 'payment_module_wtp_cc_api.php',
        'resFile'      => 'payment-module-wtp-messages',

        'actions' => array
        (
            # We suppose, the action name matches
            # the class name of this action.
            # 'action_class_name' => 'action_file_name'
            'AdminZone' => array(
                'update_wtp' => 'update_wtp.php'
            ),
        ),

        'hooks' => array
        (
        ),

        'views' => array
        (
            'AdminZone' => array
            (
                'CheckoutPaymentModuleWtpInputAZ' => 'wtp_input_az.php',
            ),
            'CustomerZone' => array
            (
                'CheckoutPaymentModuleWtpInput'  => 'wtp_input_cz.php',
                'CheckoutPaymentModuleWtpOutput' => 'wtp_output_cz.php'
            )
        )
    );
?>