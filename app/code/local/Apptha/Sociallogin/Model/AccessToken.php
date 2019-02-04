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
 * Access token class for get access toket from paypal
 *
 */
class Apptha_Sociallogin_Model_AccessToken extends Apptha_Sociallogin_Model_AbstractRequest {
    /**
     * Retrieve access token from PayPal oauth.
     * return boolean
     */
    public function retrieve() {
        $username = Mage::helper('sociallogin')->getPaypalClientId();
        $password = Mage::helper('sociallogin')->getPaypalSecretKey();
        $post = array('grant_type' => 'client_credentials');
        $headers = array('Accept: application/json', 'Accept-Language: en_US');
        /**
         * Initialize the curl function
         */
        $ch = curl_init($this->getServiceBaseUrl('/v1/oauth2/token'));
        /**
         * set the curl parameters for access paypal
         */
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
        curl_setopt($ch, CURLOPT_POST, count($post));
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        if (($output = curl_exec($ch)) === FALSE) {
            throw new Zend_Exception("Could not obtain PayPal Access Token");
        }
        curl_close($ch);
        $this->setResponse(json_decode($output));
        $this->validateResponse();

        return true;
    }

    /**
     * Validate response.
     * @throws Zend_Exception
     */
    protected function validateResponse() {
        if (!$this->getResponse()) {
            throw new Zend_Exception("Could not obtain PayPal Access Token");
        }
        if (isset($this->getResponse()->error)) {
            throw new Zend_Exception($this->getResponse()->error_description);
        }
        if (!isset($this->getResponse()->access_token)) {
            throw new Zend_Exception("Could not obtain PayPal Access Token");
        }
    }
}
