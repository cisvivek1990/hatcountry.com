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
 * @copyright   Copyright (c) 2015 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */
?>
<?php


$installer = $this;
$installer->startSetup();

// Update order.
$newOrder = array(
	'6', // LinkShare
	'5', // ShareASale
	'7', // Commission Junction
	'3', // HasOffers
	'2', // AvantLink
	'9', // Shopzilla
	'8', // Chango
	'4', // Google Analytics Ecommerce Tracking
);

foreach ($newOrder as $n => $id) {
	$installer->run("UPDATE `{$this->getTable('affiliate_types')}` SET `order` = ". ( ($n+1) * 10 ) ." WHERE `id` = $id LIMIT 1");
}

// Add new networks.
$installer->run("INSERT INTO `{$this->getTable('affiliate_types')}` (`id`, `key`, `name`, `order`) VALUES
																	(10, 'ebayEnterprise', 'eBay Enterprise', 100),
																	(11, 'affiliateWindow', 'Affiliate Window', 90),
																	(12, 'tradedoubler', 'Tradedoubler', 110),
																	(13, 'linkconnector', 'Linkconnector', 160),
																	(14, 'zanox', 'Zanox', 120),
																	(15, 'webGains', 'WebGains', 130),
																	(16, 'performanceHorizon', 'PerformanceHorizon', 140),
																	(17, 'impactRadius', 'ImpactRadius', 150);");

$installer->endSetup();