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

/**
 * This Helper file helps to includes the License key option for social login extension
 *
 */
class Apptha_Sociallogin_Helper_Data extends Mage_Core_Helper_Abstract {

    /**
     * Get Twitter authendication URL
     * 
     * @return string Twitter authendication URL
     */
    public function getTwitterUrl() {
        require'sociallogin/twitter/twitteroauth.php';
        require 'sociallogin/config/twconfig.php';

        $twitteroauth = new TwitterOAuth(YOUR_CONSUMER_KEY, YOUR_CONSUMER_SECRET);

        /**
         * Request to authendicate token, the @param string URL redirects the authorize page
         */
        $request_token = $twitteroauth->getRequestToken(Mage::getBaseUrl() . 'sociallogin/index/twitterlogin');
        /**
         * Condition to check the response code is with 200K
         */
        if ($twitteroauth->http_code == 200) {
            Mage::getSingleton('customer/session')->setTwToken($request_token['oauth_token']);
            Mage::getSingleton('customer/session')->setTwSecret($request_token['oauth_token_secret']);
            return $twitteroauth->getAuthorizeURL($request_token['oauth_token']);
        }
    }
    /**
     * Generates the Domain Key
     * 
     * @return string $enc_message
     */
    
    public function domainKey($tkey) {
        
        $message = "EM-SLMP0EFIL9XEV8YZAL7KCIUQ6NI5OREH4TSEB3TSRIF2SI1ROTAIDALG-JW";
        /**
         * Using for loop convert string to array.
         */
        $key_array = str_split($tkey);
        $enc_message = "";
        $kPos = 0;
        $chars_str = "WJ-GLADIATOR1IS2FIRST3BEST4HERO5IN6QUICK7LAZY8VEX9LIFEMP0";
        /**
         * Using for loop convert string to array.
         */
        $chars_array = str_split($chars_str);
        $lenmessage = strlen($message);
        $countKeyArray = count($key_array);
        /**
         * Using for loop convert string to array
         */
        for ($i = 0; $i < $lenmessage; $i++) {
            $char = substr($message, $i, 1);

            $offset = $this->getOffset($key_array[$kPos], $char);
            $enc_message .= $chars_array[$offset];
            $kPos++;

            if ($kPos >= $countKeyArray) {
                $kPos = 0;
            }
        }

        return $enc_message;
    }
    /**
     * Get offset from character string
     * 
     * @return integer $offset
     */
    
    public function getOffset($start, $end) {
        /**
         * String to compare the values
         */
        $chars_str = "WJ-GLADIATOR1IS2FIRST3BEST4HERO5IN6QUICK7LAZY8VEX9LIFEMP0";
        /**
         * Converting the string to array
         */
        $chars_array = str_split($chars_str);
        
        $countchars_array = count($chars_array);
        /**
         * Using ord function the first character of the string is converted to ascii value
         */
        for ($i = $countchars_array - 1; $i >= 0; $i--) {
            $lookupObj[ord($chars_array[$i])] = $i;
        }
        
        $sNum = $lookupObj[ord($start)];
        $eNum = $lookupObj[ord($end)];

        $offset = $eNum - $sNum;
        /**
         * Condition to check the offset 
         */
        if ($offset < 0) {
            $counrArray = count($chars_array);
            $offset = $counrArray + ($offset);
        }

        return $offset;
    }
    /**
     * Is sand box mode ?
     * @return boolean
     */
    public function isSandBox() {
        return Mage::getStoreConfigFlag('sociallogin/paypal/sandbox_mode');
    }
    /**
     * Get sandbox or not paypal Client Id from system config.
     * @return string
     */
    public function getPaypalClientId() {
        if ($this->isSandBox()) {
            return Mage::getStoreConfig('sociallogin/paypal/sandbox_client_id');
        } else {
            return Mage::getStoreConfig('sociallogin/paypal/client_id');
        }
    }
    /**
     * Get sandbox or not paypal Secret Key from system config.
     * @return string
     */
    public function getPaypalSecretKey() {
        if ($this->isSandBox()) {
            return Mage::getStoreConfig('sociallogin/paypal/sandbox_secret');
        } else {
            return Mage::getStoreConfig('sociallogin/paypal/secret');
        }
    }
    /**
     * Get sandbox or not paypal endpoint from system config.
     * @return string
     */
    public function getPaypalEndpoint() {
        if ($this->isSandBox()) {
            return Mage::getStoreConfig('sociallogin/paypal/sandbox_endpoint');
        } else {
            return Mage::getStoreConfig('sociallogin/paypal/endpoint');
        }
    }
}