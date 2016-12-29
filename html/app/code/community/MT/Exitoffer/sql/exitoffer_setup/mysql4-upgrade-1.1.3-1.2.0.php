<?php

$installer = $this;
$installer->startSetup();
$popupTable = $installer->getTable('exitoffer/popup');

$installer->run("

ALTER TABLE `{$popupTable}`
	ADD COLUMN `text_5` TEXT NULL AFTER `text_4`,
	ADD COLUMN `text_6` TEXT NULL AFTER `text_5`,
	ADD COLUMN `text_7` TEXT NULL AFTER `text_6`,
	ADD COLUMN `text_8` TEXT NULL AFTER `text_7`;

");

$installer->endSetup();