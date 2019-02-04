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
 * Grant token from paypal authorization code it is abstract the AbstractRequest class . 
 * */
class Apptha_Sociallogin_Model_Grant extends Apptha_Sociallogin_Model_AbstractRequest {
    /**
     * Grant token from authorization code.
     * return boolean
     */
    public function grant() {
        $username = Mage::helper('sociallogin')->getPaypalClientId();
        $password = Mage::helper('sociallogin')->getPaypalSecretKey();
        $post = array('grant_type' => 'authorization_code',
            'code' => $this->getCode(),
            'redirect_uri' => Mage::getBaseUrl().'sociallogin/paypallogin/');
        /**
        * Initialize the curl function
         */
        $ch = curl_init($this->getServiceBaseUrl('/v1/identity/openidconnect/tokenservice'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));

        if (($output = curl_exec($ch)) === FALSE) {
            throw new Zend_Exception("Could not grant authorization code");
        }
        curl_close($ch);
        $this->setResponse(json_decode($output));
        $this->validateResponse();
        Mage::getSingleton('customer/session')
            ->setPayPalAccessToken($this->getResponse()->access_token);
        return true;
    }


    /**
     * Validate response.
     * @throws Zend_Exception
     */
    protected function validateResponse() {
        if (!$this->getResponse()) {
            throw new Zend_Exception("Could not obtain PayPal Grant Access Token");
        }
        if (isset($this->getResponse()->error)) {
            throw new Zend_Exception($this->getResponse()->error_description);
        }
        if (!isset($this->getResponse()->access_token)) {
            throw new Zend_Exception("Could not obtain PayPal Grant Access Token");
        }
    }
}
