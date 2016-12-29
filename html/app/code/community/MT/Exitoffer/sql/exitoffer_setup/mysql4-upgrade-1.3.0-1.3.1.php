<?php

$installer = $this;
$installer->startSetup();
$tableName = $installer->getTable('exitoffer/popup');

$resource = Mage::getSingleton('core/resource');
$db = $resource->getConnection('core_write');

$db->addColumn($tableName, 'email_template',  array(
    'TYPE'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
    'LENGTH' =>  5,
    'COMMENT'   => 'Contact Email Template'
));

$installer->endSetup();