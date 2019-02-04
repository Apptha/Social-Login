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
 * Block for close the popup window and redirect the page.
 */
class Apptha_Sociallogin_Block_Paypallogin extends Mage_Core_Block_Template {
    /**
     * Constructor for set template file for javacript.
     */
    protected function _construct() { 
        $this->setTemplate("sociallogin/loginwithpaypal.phtml");
    }

    /**
     * Get redirect url after authendicate from paypal.
     * @return url
     */
    public function getRedirectUrl() {
        if ($this->getError()) {
            return $this->getRedirectError();
        } else {
            return $this->getRedirectSuccess();
        }
    }
    
    /**
     * Redirect to error page after getting authendicate from paypal.
     */
    public function getRedirectError() {
        return $this->getRedirectConfig('error');
    }

    /**
     * Redirect to success page after getting authendicate from paypal.
     */
    public function getRedirectSuccess() {
        return $this->getRedirectConfig('success');
    }

    /**
     * 
     * @param string $code
     * return url
     */
    public function getRedirectConfig($code) {
    $returnUrl = "";      
    $getCustomerLink = Mage::getSingleton("customer/session")->getLink();
    $getPath = trim($getCustomerLink, '/');
    if (strstr($getPath,'onepage')) {
        $returnUrl = $getPath;
     }
     else { 
        $getRedirectUrl = $this->redirectionurl();
        /**
        * This session is before auth url. 
         */
        $authRefererUrl = Mage::getSingleton('customer/session')->getBeforeAuthUrl();
        $getRedirectUrl = ($getRedirectUrl) ? $getRedirectUrl : $authRefererUrl;
        $returnUrl = $getRedirectUrl;
        }
        return $returnUrl;
    }
    /**
     * redirectionurl get the redirct url from session
     * return url string
     */
    public function redirectionurl () {
        $redirectStatus = Mage::getStoreConfig('sociallogin/general/enable_redirect');
        /**
         * Condition to check the enable redirect is set to yes.
        */
        if ($redirectStatus) {
            $redirectUrl = Mage::helper('customer')->getAccountUrl();
        } else {
            /**
             * Redirect to the referer page.
             */
            $redirectUrl = Mage::getSingleton('customer/session')->getLink();
        }
        return  $redirectUrl;
    
    }
    /**
     * Get Login form name (login or checkout page?)
     * @return string
     */
    public function getLoginFormName() {
        if (Mage::getSingleton('customer/session')->hasLoginFormName()) {
            return Mage::getSingleton('customer/session')->getLoginFormName();
        } else {
            return 'login';
        }
    }
}
