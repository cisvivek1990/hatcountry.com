<?php

$installer = $this;
$installer->startSetup();
$table = $installer->getTable('exitoffer/campaign');

$installer->run("
ALTER TABLE `{$table}`
	ADD COLUMN `show_to` INTEGER (1) NULL DEFAULT NULL;

");

$installer->endSetup();