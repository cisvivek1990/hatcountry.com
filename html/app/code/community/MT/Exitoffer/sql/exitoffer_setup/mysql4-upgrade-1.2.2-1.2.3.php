<?php

$installer = $this;
$installer->startSetup();
$table = $installer->getTable('exitoffer/campaign');

$installer->run("
ALTER TABLE `{$table}`
	ADD COLUMN `conditions_serialized` MEDIUMTEXT NULL DEFAULT NULL;

ALTER TABLE `{$table}`
	ADD COLUMN `actions_serialized` MEDIUMTEXT NULL DEFAULT NULL;
");

$installer->endSetup();