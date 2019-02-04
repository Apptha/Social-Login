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
 *  Abstract class for get the paypal endpont url
 * */
abstract class Apptha_Sociallogin_Model_AbstractRequest extends Mage_Core_Model_Abstract {
    /**
     * Get service base url.
     * @return url
     */
    protected function getServiceBaseUrl($req = '') {
        $url = 'https://';
        if (Mage::helper('sociallogin')->isSandBox()) {
            $url .= Mage::helper('sociallogin')->getPaypalEndpoint();
        } else {
           $url .= Mage::helper('sociallogin')->getPaypalEndpoint();
        }
        return $url . $req;
    }
}