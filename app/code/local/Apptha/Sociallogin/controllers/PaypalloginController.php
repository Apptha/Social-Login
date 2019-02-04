<?php
/**
 * Apptha
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.apptha.com/LICENSE.txt
 *
 * ==============================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * ==============================================================
 * This package designed for Magento COMMUNITY edition
 * Apptha does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * Apptha does not provide extension support in case of
 * incorrect edition usage.
 * ==============================================================
 *
 * @category    Apptha
 * @package     Apptha_Sociallogin
 * @version     0.2.0
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2015 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 * 
 * */
?>
<?php

/** 
 * Callback from paypal controller.
 *  */
class Apptha_Sociallogin_PaypalloginController extends Mage_Core_Controller_Front_Action {
    /**
     * Index action, called from PayPal controller.
     */
    public function indexAction() {
        /**
        * Create new block which is paypallogin it will get a new template which is used to close the popup window 
        */
        $block = $this->getLayout()->createBlock('sociallogin/paypallogin');
        $block->setError(filter_input(INPUT_GET, 'error_description'));
        if ($block->getError()) {
            Mage::getSingleton('customer/session')->addError($block->getError());
        }
        $code = filter_input(INPUT_GET, 'code');
        try { 
        /**
        * get login model for fetch the user data and store into customer collection 
        */
            Mage::getModel('sociallogin/login')->setCode($code)->login();
        } catch (Exception $exception) {
            Mage::getSingleton('customer/session')->addError($exception->getMessage());
        }
        $this->getResponse()->setBody($block->toHtml());
    }
}