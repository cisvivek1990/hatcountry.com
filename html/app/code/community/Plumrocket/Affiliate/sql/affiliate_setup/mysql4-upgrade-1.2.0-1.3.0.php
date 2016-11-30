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
 * @copyright   Copyright (c) 2013 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */
?>
<?php


$installer = $this;
$installer->startSetup();

$installer->run("INSERT INTO  `{$this->getTable('affiliate_types')}` (`id` , `key` , `name` , `order` ) VALUES ('5',  'shareasale',  'ShareASale',  '5');");
$installer->run("ALTER TABLE  `{$this->getTable('affiliate_affiliate')}` ADD  `stores` CHAR( 255 ) NOT NULL AFTER  `name`;");
$installer->run("UPDATE `{$this->getTable('affiliate_affiliate')}` SET `stores` = 0;");


$installer->endSetup();
