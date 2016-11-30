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
$sql = "
	CREATE TABLE IF NOT EXISTS `{$this->getTable('affiliate_affiliate')}` (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `name` varchar(255) NOT NULL,
	  `description` text NOT NULL,
	  `status` enum('ENABLED','DISABLED') NOT NULL,
	  `type_id` int(11) NOT NULL,
	  `section_head_library` varchar(255) NOT NULL,
	  `section_head_code` text NOT NULL,
	  `section_head_includeon_id` int(11) NOT NULL,
	  `section_bodybegin_library` varchar(255) NOT NULL,
	  `section_bodybegin_code` text NOT NULL,
	  `section_bodybegin_includeon_id` int(11) NOT NULL,
	  `section_bodyend_library` varchar(255) NOT NULL,
	  `section_bodyend_code` text NOT NULL,
	  `section_bodyend_includeon_id` int(11) NOT NULL,
	  `updated_at` datetime NOT NULL,
	  `created_at` datetime NOT NULL,
	  PRIMARY KEY (`id`),
	  KEY `status` (`status`,`type_id`,`section_head_includeon_id`,`section_bodybegin_includeon_id`,`section_bodyend_includeon_id`)
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;


	CREATE TABLE IF NOT EXISTS `{$this->getTable('affiliate_includeon')}` (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `key` char(255) NOT NULL,
	  `name` char(255) NOT NULL,
	  `order` int(11) NOT NULL,
	  PRIMARY KEY (`id`)
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;


	INSERT INTO `{$this->getTable('affiliate_includeon')}` (`id`, `key`, `name`, `order`) VALUES
	(1, 'all', 'All Pages', 10),
	(2, 'registration_success', 'Registration Success Pages', 20),
	(3, 'login_success', 'Login Success Pages', 30),
	(4, 'home_page', 'Home Page', 40),
	(5, 'product_page', 'Product Page', 50),
	(6, 'category_page', 'Category Page', 60),
	(7, 'shoping_cart', 'Shoping Cart Page', 70),
	(8, 'chackout', 'OnePage Chackout', 80),
	(9, 'checkout_success', 'Order Success Page', 90);


	CREATE TABLE IF NOT EXISTS `{$this->getTable('affiliate_types')}` (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `key` char(255) NOT NULL,
	  `name` char(255) NOT NULL,
	  `order` int(11) NOT NULL,
	  PRIMARY KEY (`id`),
	  UNIQUE KEY `key` (`key`)
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;
	
	INSERT INTO `{$this->getTable('affiliate_types')}` (`id`, `key`, `name`, `order`) VALUES
	(2, 'avantLink', 'AvantLink', 1);
";

$installer->run($sql);
$installer->endSetup();
	 
