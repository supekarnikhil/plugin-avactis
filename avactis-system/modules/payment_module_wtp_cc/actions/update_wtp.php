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
 * Payment Module.
 * This action is responsible for update cash on delivery settings.
 *
 * @package PaymentModuleCod
 * @access  public
 * @author Egor Makarov
 */
class update_wtp extends update_pm_sm
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Action constructor.
     */
    function update_wtp()
    {
    }


    function saveDataToDB($SessionPost)
    {
        modApiFunc("Checkout", "setModuleActive", (modApiFunc("Payment_Module_Wtp_CC", "getUid")), ($SessionPost["status"]=="active")? true:false);

        $Settings = array
            (
                "MODULE_NAME"  			=> $SessionPost["ModuleName"],
            	"MODULE_MODE" 			=> $SessionPost["ModuleMode"],
            	"MODULE_METHOD_ID" 		=> $SessionPost["ModuleID"],
            	"MODULE_METHOD_PASS" 	=> $SessionPost["ModulePass"],
            ); 

        modApiFunc("Payment_Module_Wtp_CC", "updateSettings", $Settings);
    }

    /**
     *
     */
    function onAction()
    {
        global $application;
        $request = $application->getInstance('Request');

        $SessionPost = array();
        if(modApiFunc('Session', 'is_Set', 'SessionPost'))
        {
            _fatal(array( "CODE" => "CORE_050"), __CLASS__, __FUNCTION__);
        }

        $SessionPost = $_POST;

        switch($SessionPost["ViewState"]["FormSubmitValue"])
        {
            case "save" :
            {
                $SessionPost["ViewState"]["ErrorsArray"] = array();
                if (empty($SessionPost["ModuleName"]) == true || trim($SessionPost["ModuleName"]) == '')
                {
                    $SessionPost["ViewState"]["ErrorsArray"][] = "MODULE_ERROR_NO_NAME";
                }

                $nErrors = sizeof($SessionPost["ViewState"]["ErrorsArray"]);

                if($nErrors == 0)
                {
                    unset($SessionPost["ViewState"]["ErrorsArray"]);
                    $this->saveDataToDB($SessionPost);
                    $SessionPost["ViewState"]["hasCloseScript"] = "true";
                }
                break;
            }
            default :
                _fatal(array( "CODE" => "CORE_051"), __CLASS__, __FUNCTION__, $request->getValueByKey('FormSubmitValue'));
                break;
        }

        modApiFunc('Session', 'set', 'SessionPost', $SessionPost);
        // get view name by action name.
        $request = new Request();
        $request->setView(CURRENT_REQUEST_URL);
        $application->redirect($request);
    }

    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    /**
     * Corresponding View file.
     */
    var $ViewFilename;

    /**#@-*/
}
?>