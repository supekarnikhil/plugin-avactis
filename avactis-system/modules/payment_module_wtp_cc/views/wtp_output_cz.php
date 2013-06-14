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
 * CheckoutPaymentModuleCodOutput view.
 *
 * @package PaymentModuleCod
 * @author Egor Makarov
 */
class CheckoutPaymentModuleWtpOutput
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Gets a template format for the given view.
     */
    function getTemplateFormat()
    {
            $format = array(
                'layout-file'        => "checkout-payment-module-wtp-cc-output-config.ini" //; $this->LayoutFile
               ,'files' => array(
                    'OutputContainer'               => TEMPLATE_FILE_SIMPLE
                )
               ,'options' => array(
                )
            );
            return $format;
    }

    /**
     *  CheckoutPaymentModuleCodOutput constructor.
     */
    function CheckoutPaymentModuleWtpOutput()
    {
        global $application;

        #check if fatal errors exist in the block tag
        $this->NoView = false;
        if ($application->issetBlockTagFatalErrors($this->BlockTemplateName))
        {
            $this->NoView = true;
        }
    }

    /**
     *
     */
    function output()
    {
        global $application;


        $application->registerAttributes(array("Local_PaymentMethodName" => "",
                                               "Local_PaymentMethodMessage" => ""));


        $this->templateFiller = &$application->getInstance('TemplateFiller');
        $this->template = $application->getBlockTemplate($this->BlockTemplateName);
        $this->templateFiller->setTemplate($this->template);

        $retval = $this->templateFiller->fill("OutputContainer");
        return $retval;
    }

    function getTag($tag)
    {
        global $application;
        $value = null;
        switch ($tag)
        {
            case 'Local_PaymentMethodName':
            {
                $ModuleInfo = modApiFunc($this->ModuleAPIClassName, "getInfo");
                $value = $ModuleInfo['Name'];
                break;
            }

            case 'Local_PaymentMethodMessage':
                $cost = modApiFunc($this->ModuleAPIClassName, "getPerOrderPaymentModuleShippingFee");
                if(!empty($cost))
                {
                    $value = getMsg("PM_WTP", "MODULE_PER_ORDER_SHIPPING_FEE_CZ_TEXT");
                    $value = str_replace("{cost}", modApiFunc("Localization", "currency_format", $cost), $value);
                }
                else
                {
                    $value = "";
                }
                break;
//                $ModuleInfo = modApiFunc($this->ModuleAPIClassName, "getInfo");
//                $value = "<b>" . $ModuleInfo['Description'] . "</b>";
//                break;

                default:
                        break;
        }
        return $value;
    }

    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    var $ModuleAPIClassName = "Payment_Module_Wtp_CC";
    var $BlockTemplateName = "CheckoutPaymentModuleWtpOutput";
    var $LayoutFile = "checkout-payment-module-wtp-cc-output-config.ini";
    /**#@-*/

}
?>