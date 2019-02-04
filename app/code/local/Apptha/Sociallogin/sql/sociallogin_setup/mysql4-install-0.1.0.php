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
 * Store the current object in installer variable
 */
$installer = $this;
/**
 * Start setup 
 * variable installer
 */
$installer->startSetup();
/**
 * Core setup object reference in installer variable.
 */
$installer = new Mage_Eav_Model_Entity_Setup('core_setup');
/**
 * Add attribute called login_provider in customer collection.
 * make require as false and visible as true
 */
$installer->addAttribute('customer', 'login_provider', array('label'=> 'Provider',
'type' => 'varchar',
'input' => 'text',
'visible'=> true,
'required' => false
)); 

$eavConfig = Mage::getSingleton('eav/config');
$attribute = $eavConfig->getAttribute('customer', 'login_provider');
/**
 * Setdata used_in_forms
 */
$attribute->setData('used_in_forms', array('adminhtml_customer','customer_account_create','customer_account_edit'));  
/**
 * Save the attribute in database table.
 * 
 */
$attribute->save();

/**
 * End setup 
 */
$installer->endSetup(); 