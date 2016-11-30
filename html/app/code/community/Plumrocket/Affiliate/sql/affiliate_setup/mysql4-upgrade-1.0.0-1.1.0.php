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

	ALTER TABLE `{$this->getTable('affiliate_affiliate')}` ADD `additional_data` TEXT NOT NULL AFTER `section_bodyend_includeon_id`;
	
	DROP TABLE IF EXISTS `{$this->getTable('affiliate_includeon')}`;
	CREATE TABLE IF NOT EXISTS `{$this->getTable('affiliate_includeon')}` (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `key` char(255) NOT NULL,
	  `name` char(255) NOT NULL,
	  `order` int(11) NOT NULL,
	  PRIMARY KEY (`id`)
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;


	INSERT INTO `{$this->getTable('affiliate_includeon')}` (`id`, `key`, `name`, `order`) VALUES
	(1, 'all', 'All Pages', 10),
	(2, 'registration_success_pages', 'Registration Success Pages', 20),
	(3, 'login_success_pages', 'Login Success Pages', 30),
	(4, 'home_page', 'Home Page', 40),
	(5, 'product_page', 'Product Page', 50),
	(6, 'category_page', 'Category Page', 60),
	(7, 'cart_page', 'Shoping Cart Page', 70),
	(8, 'one_page_chackout', 'One Page Checkout', 80),
	(9, 'checkout_success', 'Order Success Page', 90);

	DROP TABLE IF EXISTS `{$this->getTable('affiliate_types')}`;
	CREATE TABLE IF NOT EXISTS `{$this->getTable('affiliate_types')}` (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `key` char(255) NOT NULL,
	  `name` char(255) NOT NULL,
	  `order` int(11) NOT NULL,
	  PRIMARY KEY (`id`),
	  UNIQUE KEY `key` (`key`)
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

	INSERT INTO `{$this->getTable('affiliate_types')}` (`id`, `key`, `name`, `order`) VALUES
	(3, 'hasoffers', 'HasOffers', 3),
	(2, 'avantLink', 'AvantLink', 2),
	(1, 'custom', 'Custom', 1);
";

$installer->run($sql);
$installer->endSetup();
	 
