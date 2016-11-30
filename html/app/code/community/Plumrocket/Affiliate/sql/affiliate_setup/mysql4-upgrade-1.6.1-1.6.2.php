<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2015 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */
?>
<?php


$installer = $this;
$installer->startSetup();

$installer->run("UPDATE `{$this->getTable('affiliate_affiliate')}` SET `section_bodyend_includeon_id` = '1' WHERE `type_id` = 12 LIMIT 1");

// Add group id attribute.
$setup = Mage::getModel('eav/entity_setup', 'core_setup');

$entityTypeId = $setup->getEntityTypeId('catalog_product');
$attributeSetId   = $setup->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $setup->getAttributeGroupId($entityTypeId, $attributeSetId, 'Affiliate Programs');

$setup->addAttribute('catalog_product', 'affiliate_tradedoubler_groupid', array(
    'group'         => 'Affiliate Programs',
    'input'         => 'text',
    'type'          => 'text',
    'label'         => 'TradeDoubler Group ID',
    'backend'       => '',
    'visible'       => 1,
    'required'      => 0,
    'visible_on_front' => 1,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
	'note'			=> "Product group ID as provided by Tradedoubler. Used to distinguish different product categories.",
	'sort_order'	=> 50
));
$setup->addAttributeToGroup($entityTypeId, $attributeSetId, $attributeGroupId, 'affiliate_tradedoubler_groupid', '50');
$setup->addAttributeToSet($entityTypeId, $attributeSetId, $attributeGroupId, 'affiliate_tradedoubler_groupid', '50');


$installer->endSetup();