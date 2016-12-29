<?php
/**
 * Engage Commerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category    EngageCommerce
 * @package     EngageCommerce_Twitter
 * @copyright   Copyright (c) 2016 Engage Commerce (http://engagecommerce.io)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class EngageCommerce_Twitter_Model_LoadOrder extends Mage_Adminhtml_Model_System_Config_Source_Yesno
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'before_head_end', 'label'=>Mage::helper('adminhtml')->__('Before Head End')),
            array('value' => 'after_body_start', 'label'=>Mage::helper('adminhtml')->__('After Body Start')),
            array('value' => 'before_body_end', 'label'=>Mage::helper('adminhtml')->__('Before Body End'))
        );
    }
}