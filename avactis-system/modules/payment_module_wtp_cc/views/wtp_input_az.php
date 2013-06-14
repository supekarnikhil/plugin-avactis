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
 * CheckoutPaymentModuleCodInputAZ view
 *
 * @package PaymentModuleCod
 * @author Egor Makarov
 */
class CheckoutPaymentModuleWtpInputAZ extends pm_sm_input_az
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Modules_Manager constructor.
     */
    function CheckoutPaymentModuleWtpInputAZ()
    {
        global $application;
        $this->MessageResources = &$application->getInstance('MessageResources',"payment-module-wtp-messages", "AdminZone");
        $this->Hints = &$application->getInstance('Hint');
        $this->mTmplFiller = &$application->getInstance('TmplFiller');

        if(modApiFunc("Session", "is_Set", "SessionPost"))
        {
            $this->copyFormData();
            modApiFunc('Session', 'un_Set', 'SessionPost');
        }
        else
        {
            $this->initFormData();
        }
    }

    /**
     * Initializes data from the POST array.
     */
    function copyFormData()
    {
        // eliminate copying on construction
        $SessionPost = modApiFunc("Session", "get", "SessionPost");
        $this->ViewState = $SessionPost["ViewState"];
        //Remove some data, that should not be resent to action, from ViewState.
        if(isset($this->ViewState["ErrorsArray"]) &&
           count($this->ViewState["ErrorsArray"]) > 0)
        {
            $this->ErrorsArray = $this->ViewState["ErrorsArray"];
            unset($this->ViewState["ErrorsArray"]);
        }

        $this->POST = $SessionPost;
    }

    /**
     * Initializes data from the database.
     */
    function initFormData()
    {
        $this->POST = array();
        $settings = modApiFunc("Payment_Module_Wtp_CC", "getSettings");
        foreach ($settings as $key => $value)
        {
            switch($key)
            {
                case "MODULE_NAME"			: $this->POST["ModuleName"] = $value; break;
                case "MODULE_MODE" 			: $this->POST["ModuleMode"] = $value; break;
                case "MODULE_METHOD_ID"		: $this->POST["ModuleID"] 	= $value; break;
                case "MODULE_METHOD_PASS"	: $this->POST["ModulePass"] = $value; break;
            }
        }
        $this->POST["status"] = modApiFunc("Payment_Module_Wtp_CC", "isActive");
        $this->ViewState = array("hasCloseScript"  => "false","FormSubmitValue" => "save");
    }

    /**
     * @return String Return html code for hidden form fields representing
     * @var $this->ViewState array.
     */
    function outputViewState()
    {
        $retval = "";
        foreach ($this->ViewState as $key => $value)
        {
            $retval .= "<input type=\"hidden\" name=\"ViewState[" .$key . "]\" value=\"" . $value . "\">";
        }
        return $retval;
    }

    /**
     * Outputs errors.
     */
    function outputErrors()
    {
        global $application;
        if (!is_array($this->ErrorsArray) || sizeof($this->ErrorsArray) == 0)
        {
            return;
        }
        $result = "";
        $application->registerAttributes(array('ErrorIndex', 'Error'));
        $this->_error_index = 0;
        foreach ($this->ErrorsArray as $error)
        {
            $this->_error_index++;
            $this->_error = $this->MessageResources->getMessage($error);
            $result .= $this->mTmplFiller->fill("payment_module_wtp/", "error.tpl.html", array());
        }
        return $result;
    }

    /**
     * Outputs the module status.
     */
    function outputStatus()
    {
        global $application;
        $retval = "";
        $status = $this->POST["status"];
        $this->_Template_Contents = array(
                                          "Active"          => ($status)? "checked" : ""
                                         ,"ActiveMessage"   => $this->MessageResources->getMessage('MODULE_STATUS_ACTIVE')
                                         ,"Inactive"        => ($status)? "" : "checked"
                                         ,"InactiveMessage" => $this->MessageResources->getMessage('MODULE_STATUS_INACTIVE')
                                         );
        $application->registerAttributes($this->_Template_Contents);
        $retval.= $this->mTmplFiller->fill("payment_module_wtp/", "status.tpl.html", array());
        return $retval;
    }

    /**
     *
     */
    
	function outputModeOptions()
    {
        $retval = "";
        $options = array(array("value" => "1", "text" => $this->MessageResources->getMessage('MODE_001')), array("value" => "0", "text" => $this->MessageResources->getMessage('MODE_002')));
        foreach ($options as $option)
        {
            $retval.= "<option value='".$option["value"]."' ".($option["value"]==$this->POST["ModuleMode"] ? "selected":"").">".$option["text"]."</option>";
        }
        return $retval;
    }
     
    function outputOrderStatus()
    {
        global $application;
        $retval = "";
        for ($i=1; $i<5; $i++)
        {
            $retval.= "<option value=\"$i\" ";
            $retval.= ($this->POST["ModuleOrderStatusId"] == $i)? "SELECTED " : "";
            $retval.= ">" . $this->MessageResources->getMessage('MODULE_ORDER_STATUS_00'.$i) . "</option>";
        }
        $status = $this->POST["status"];
        $this->_Template_Contents = array(
                                          "Active"          => ($status)? "checked":""
                                         ,"ActiveMessage"   => $this->MessageResources->getMessage('MODULE_STATUS_ACTIVE')
                                         ,"Inactive"        => ($status)? "":"checked"
                                         ,"InactiveMessage" => $this->MessageResources->getMessage('MODULE_STATUS_INACTIVE')
                                         );
        $application->registerAttributes($this->_Template_Contents);
        $retval.= $this->mTmplFiller->fill("payment_module_wtp/", "status.tpl.html", array());
        return $retval;
    }

    /**
     *
     */
    function output()
    {
        global $application;
        loadCoreFile('html_form.php');
        $HtmlForm1 = new HtmlForm();
        if($this->ViewState["hasCloseScript"] == "true")
        {
            modApiFunc("application", "closeChild_UpdateParent");
            return;
        }

        $request = new Request();
        $request->setView('CheckoutPaymentModuleSettings');
        $request->setAction("update_wtp");
        $form_action = $request->getURL();

        $template_contents = array(
                                    "EditWtpForm"        => $HtmlForm1->genForm($form_action, "POST", "EditWtpForm"),
                                    "HiddenArrayViewState"  => $this->outputViewState(),
                                    "ModuleType"            => $this->MessageResources->getMessage('MODULE_TYPE'),
                                    "ModuleName"            => $this->MessageResources->getMessage('MODULE_NAME'),
                                    "Subtitle"              => $this->MessageResources->getMessage('FORM_SUBTITLE'),
                                    "Errors"                => $this->outputErrors(),

                                    "ModuleStatusFieldName" => $this->MessageResources->getMessage('MODULE_STATUS_FIELD_NAME'),
                                    "ModuleStatusFieldHint" => $this->Hints->getHintLink(array('MODULE_STATUS_FIELD_NAME', 'payment-module-wtp-messages')),
                                    "ModuleStatusField"     => $this->outputStatus(),

                                    "ModuleMethodNameFieldName"   => $this->MessageResources->getMessage('MODULE_METHOD_NAME_FIELD_NAME'),
                                    "ModuleNameFieldHint"   => $this->Hints->getHintLink(array('MODULE_METHOD_NAME_FIELD_NAME', 'payment-module-wtp-messages')),
                                    "ModuleNameField"       => $HtmlForm1->genInputTextField("128", "ModuleName", "75", prepareHTMLDisplay($this->POST["ModuleName"])),
        
        							"ModuleMethodIDFieldName"   => $this->MessageResources->getMessage('MODULE_METHOD_ID_FIELD_NAME'),
                                    "ModuleIDFieldHint"   => $this->Hints->getHintLink(array('MODULE_METHOD_ID_FIELD_NAME', 'payment-module-wtp-messages')),
                                    "ModuleIDField"       => $HtmlForm1->genInputTextField("128", "ModuleID", "75", prepareHTMLDisplay($this->POST["ModuleID"])),
        
        							"ModuleMethodPassFieldName"   => $this->MessageResources->getMessage('MODULE_METHOD_PASS_FIELD_NAME'),
                                    "ModulePassFieldHint"   => $this->Hints->getHintLink(array('MODULE_METHOD_PASS_FIELD_NAME', 'payment-module-wtp-messages')),
                                    "ModulePassField"       => $HtmlForm1->genInputTextField("128", "ModulePass", "75", prepareHTMLDisplay($this->POST["ModulePass"])),
        
        							"ModuleModeFieldName"    => $this->MessageResources->getMessage('MODULE_MODE_FIELD_NAME'),
                                    "ModuleModeFieldHint"    => $this->Hints->getHintLink(array('MODULE_MODE_FIELD_NAME', 'payment-module-paypal-messages')),
                                    "ModuleModeOptions"      => $this->outputModeOptions(),

                                    "Alert_001"             => $this->MessageResources->getMessage('ALERT_001'),
                                    "Alert_002"             => $this->MessageResources->getMessage('ALERT_002'),
                                    "Alert_003"             => $this->MessageResources->getMessage('ALERT_003'),
                                  );

        $this->_Template_Contents = $template_contents;
        $application->registerAttributes($this->_Template_Contents);
        $obj = &$application->getInstance('MessageResources');
        $output = modApiFunc('TmplFiller', 'fill',
                              './../../js/','validate.msgs.js.tpl',
                              array(
                                    "INTEGER" => $obj->getMessage( new ActionMessage(array('PRDADD_001')) )
                                   ,"FLOAT"   => $obj->getMessage( new ActionMessage(array('PRDADD_002')) )
                                   ,"STRING1024"=> $obj->getMessage( new ActionMessage(array('PRDADD_007')) )
                                   ,"STRING128"=> $obj->getMessage( new ActionMessage(array('PRDADD_008')) )
                                   ,"STRING256"=> $obj->getMessage( new ActionMessage(array('PRDADD_009')) )
                                   ,"STRING512"=> $obj->getMessage( new ActionMessage(array('PRDADD_010')) )
                                   ,"CURRENCY"=> addslashes($obj->getMessage( new ActionMessage(array('CURRENCY_FIELD',
                                                         modApiFunc("Localization", "FloatToFormatStr", 12.35, "currency"),
                                                         modApiFunc("Localization", "FloatToFormatStr", 34, "currency"),
                                                         modApiFunc("Localization", "FloatToFormatStr", 99.00, "currency")))))
                                   ,"WEIGHT"   => addslashes($obj->getMessage( new ActionMessage(array('WEIGHT_FIELD',
                                                         modApiFunc("Localization", "FloatToFormatStr", 23.325, "weight"),
                                                         modApiFunc("Localization", "FloatToFormatStr", 34, "weight"),
                                                         modApiFunc("Localization", "FloatToFormatStr", 99.2, "weight")))))
                                   ,"ITEM"     => $obj->getMessage( new ActionMessage(array('ITEM_FIELD')))
                                   )
                            );
        return $output. $this->mTmplFiller->fill("payment_module_wtp/", "list.tpl.html", array());
    }

    /**
     *
     */
    function genInputCheckBox($name, $checked, $text)
    {
        $retval = "name=\"{$name}\" " . ($checked ? " checked " : "") . " value=\"{$text}\"";
        return $retval;
    }

    /**
     * Returns the tag output, whose name is specified in $tag.
     */
    function getTag($tag)
    {
        global $application;
        $value = "";

        if ($tag == "Error")
        {
            $value = $this->_error;
        }
        elseif ($tag == "ErrorIndex")
        {
                $value = $this->_error_index;
        }
        else
        {
            $value = getKeyIgnoreCase($tag, $this->_Template_Contents);
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

    var $POST;

    /**
     * View state structure. It comes from action.
     * $SessionPost["ViewState"] structure example:
     * <br>array
     * <br>(
     * <br>    "hasCloseScript"  = "false"           //true/false
     * <br>    "ErrorsArray"     =  array()          //true/false
     * <br>    "LargeImage"      = "image.jpg"       //
     * <br>    "SmallImage"      = "image_small.jpg" //
     * <br>)
     */
    var $ViewState;

    /**
     * List of error ids. It comes from action.
     */
    var $ErrorsArray;
    var $ErrorMessages;

    var $_Template_Contents;

    var $MessageResources;
    var $_error_index;
    var $_error;

    /**#@-*/

}
?>