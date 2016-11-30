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

$installer->run("INSERT INTO  `{$this->getTable('affiliate_types')}` (`id` , `key` , `name` , `order` ) VALUES ('6',  'linkshare',  'LinkShare',  '6');");
$installer->run("INSERT INTO  `{$this->getTable('affiliate_types')}` (`id` , `key` , `name` , `order` ) VALUES ('7',  'commissionJunction',  'Commission Junction',  '7');");

$installer->endSetup();
