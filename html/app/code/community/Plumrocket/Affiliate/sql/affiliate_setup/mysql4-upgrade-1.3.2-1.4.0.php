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
 * @copyright   Copyright (c) 2014 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */
?>
<?php


$installer = $this;
$installer->startSetup();

$installer->run("INSERT INTO  `{$this->getTable('affiliate_types')}` (`id` , `key` , `name` , `order` ) VALUES ('8',  'chango',  'Chango',  '8');");

$installer->run("UPDATE  `{$this->getTable('affiliate_types')}` SET  `order` =  '10' WHERE  `{$this->getTable('affiliate_types')}`.`id` =2;");
$installer->run("UPDATE  `{$this->getTable('affiliate_types')}` SET  `order` =  '20' WHERE  `{$this->getTable('affiliate_types')}`.`id` =8;");
$installer->run("UPDATE  `{$this->getTable('affiliate_types')}` SET  `order` =  '30' WHERE  `{$this->getTable('affiliate_types')}`.`id` =7;");
$installer->run("UPDATE  `{$this->getTable('affiliate_types')}` SET  `order` =  '500' WHERE  `{$this->getTable('affiliate_types')}`.`id` =1;");
$installer->run("UPDATE  `{$this->getTable('affiliate_types')}` SET  `order` =  '40' WHERE  `{$this->getTable('affiliate_types')}`.`id` =4;");
$installer->run("UPDATE  `{$this->getTable('affiliate_types')}` SET  `order` =  '50' WHERE  `{$this->getTable('affiliate_types')}`.`id` =3;");
$installer->run("UPDATE  `{$this->getTable('affiliate_types')}` SET  `order` =  '60' WHERE  `{$this->getTable('affiliate_types')}`.`id` =6;");
$installer->run("UPDATE  `{$this->getTable('affiliate_types')}` SET  `order` =  '70' WHERE  `{$this->getTable('affiliate_types')}`.`id` =5;");

$installer->endSetup();
