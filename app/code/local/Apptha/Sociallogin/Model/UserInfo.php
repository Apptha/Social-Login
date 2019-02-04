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
 *  User info abstract the AbstractRequest class.
 * 
 */
class Apptha_Sociallogin_Model_UserInfo extends Apptha_Sociallogin_Model_AbstractRequest {
    /**
     * Retrieve access token from PayPal oauth.
     * @return boolean
     */
    public function retrieve() {
        $headers = array('Accept: application/json', 'Authorization: Bearer '
            . $this->getAccessToken()->getResponse()->access_token);
        $ch = curl_init($this->getServiceBaseUrl('/v1/identity/openidconnect/userinfo/?schema=openid'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        if (($output = curl_exec($ch)) === FALSE) {
            throw new Zend_Exception("Could not obtain user info");
        }
        curl_close($ch);
        $data = json_decode($output);
        $this->validateData($data);

        $retriveObject = new Varien_Object();
        $retriveObject->addData((array) $data);
        $this->setResponse($retriveObject);

        return true;
    }

    /**
     *  Validate data from response.
     * 
     * */
    public function validateData($data) {
        if (!$data) {
            throw new Zend_Exception("Could not obtain user info");
        }
        if (isset($data->error)) {
            throw new Zend_Exception($data->error_description);
        }
    }
    }
