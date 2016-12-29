<?php

$installer = $this;
$installer->startSetup();
$tableName = $installer->getTable('exitoffer/campaign');

$resource = Mage::getSingleton('core/resource');
$db = $resource->getConnection('core_write');


$db->addColumn($tableName, 'mobile_trigger',  array(
    'TYPE'      => Varien_Db_Ddl_Table::TYPE_TEXT,
    'LENGTH' =>  20,
    'COMMENT'   => 'Mobile trigger'
));

$db->addColumn($tableName, 'show_on_mobile',  array(
    'TYPE'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
    'DEFAULT' =>  0,
    'COMMENT'   => 'Show on mobile'
));

$installer->endSetup();