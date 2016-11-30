<?php

/*

Plumrocket Inc.

NOTICE OF LICENSE

This source file is subject to the End-user License Agreement
that is available through the world-wide-web at this URL:
http://wiki.plumrocket.net/wiki/EULA
If you are unable to obtain it through the world-wide-web, please
send an email to support@plumrocket.com so we can send you a copy immediately.

DISCLAIMER

Do not edit or add to this file

@package	Plumrocket_Rss_Generator-v1.4.x
@copyright	Copyright (c) 2013 Plumrocket Inc. (http://www.plumrocket.com)
@license	http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 
*/

$installer = $this;

$installer->startSetup();

$installer->run("
CREATE TABLE IF NOT EXISTS `{$this->getTable('datagenerator_templates')}` (
  `entity_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL DEFAULT '',
  `address` varchar(256) NOT NULL DEFAULT '',
  `count` int(8) NOT NULL DEFAULT '0',
  `type` int(2) NOT NULL DEFAULT '0',
  `enabled` INT(1) NOT NULL DEFAULT '0',
  `code_header` TEXT NOT NULL,
  `code_item` TEXT NOT NULL,
  `code_footer` TEXT NOT NULL,
  PRIMARY KEY (`entity_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
");
$installer->endSetup();
