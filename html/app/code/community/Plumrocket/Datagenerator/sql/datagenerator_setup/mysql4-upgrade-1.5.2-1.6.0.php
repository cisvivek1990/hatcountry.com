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
@copyright	Copyright (c) 2015 Plumrocket Inc. (http://www.plumrocket.com)
@license	http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 
*/


$installer = $this;
$installer->startSetup();


// UPD: Commission Junction (cj.com)
$dateFormat = 'm/d/Y h:i:s A';
$codeItem  = '{product.name},{product.url},{product.updated_at|date_format:$dateFormat},{product.name},{product.meta_keyword},{product.description},{product.sku},{product.manufacturer},,,,"USD",{product.special_price},{product.price},{product.price},,{product.url},{product.thumbnail_url},{product.image_url},{category.name},{category.url_key},{category.name},,,,,,,No,, {product.short_description},{category.privatesale_date_start|date_format:$dateFormat},{category.privatesale_date_end|date_format:$dateFormat},No,Yes,Yes,New,,""';
$installer->run("UPDATE `{$this->getTable('datagenerator_templates')}` SET `code_item` = '$codeItem' WHERE `type_entity` = 'template' AND `url_key` = 'cj.csv'");
// .. update feeds.
$cjTemplateId = $installer->getConnection()->fetchOne("SELECT `entity_id` FROM {$this->getTable('datagenerator_templates')} WHERE `type_entity`='template' AND `url_key`='cj.csv'");
$installer->run("UPDATE `{$this->getTable('datagenerator_templates')}` SET `code_item` = '$codeItem' WHERE `type_entity` = 'feed' AND `template_id` = '{$cjTemplateId}'");

// UPD: LinkShare (linkshare.com)
$dateFormat = 'Y-m-d/H:i:s';
$codeHeader = 'HDR|__PLEASE_PROVICE_MERCHANT_ID__|{site.name}|{site.now|date_format:$dateFormat}';
$codeItem  = '{product.id}|{product.name}|{product.sku}|{category.name}||{product.url}|{product.image_url}||{no_br_html}{product.short_description}{/no_br_html}|{no_br_html}{product.description}{/no_br_html}||||{product.price}|{category.privatesale_date_start|date_format:$dateFormat}|{category.privatesale_date_end|date_format:$dateFormat}|{category.brand_name}||N|{product.meta_keyword}|N||||In Stock|||Y|Y|Y|USD||';
$installer->run("UPDATE `{$this->getTable('datagenerator_templates')}` SET `code_header` = '$codeHeader', `code_item` = '$codeItem', `replace_from` = '|', `replace_to` = NULL WHERE `type_entity` = 'template' AND `url_key` = 'linkshare.txt'");
// .. update feeds.
$linkshareTemplateId = $installer->getConnection()->fetchOne("SELECT `entity_id` FROM {$this->getTable('datagenerator_templates')} WHERE `type_entity`='template' AND `url_key`='linkshare.txt'");
$installer->run("UPDATE `{$this->getTable('datagenerator_templates')}` SET `code_header` = '$codeHeader', `code_item` = '$codeItem', `replace_from` = '|', `replace_to` = NULL WHERE `type_entity` = 'feed' AND `template_id` = '{$linkshareTemplateId}'");

// Clear "Replace_from" and "Replace_to" (all feeds except LinkShare).
$installer->run("UPDATE `{$this->getTable('datagenerator_templates')}` SET `replace_from` = NULL, `replace_to` = NULL WHERE `type_entity` = 'feed' AND `template_id` != '{$linkshareTemplateId}'");

// Remove column "Date_format".
$installer->run("ALTER TABLE `{$this->getTable('datagenerator_templates')}` DROP `date_format`;");


// Add new templates.
$insert = <<<SQL
INSERT INTO `{$this->getTable('datagenerator_templates')}` (`type_entity`, `type_feed`, `name`, `url_key`, `count`, `enabled`, `store_id`, `cache_time`, `template_id`, `code_header`, `code_item`, `code_footer`, `created_at`, `updated_at`, `replace_from`, `replace_to`) VALUES
('template', 'product', 'eBay Enterprise Display & Retargeting (formerly Fetchback, fetchback.com)', 'fetchback.csv', 0, 1, '0', 86400, 0, '"Id","Name","Description","Price","Image_URL","Click_URL","Category"', '{product.sku},{product.name},{product.short_description},{product.price},{product.image_url|size:150},{product.url},{category.breadcrumb_path}', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL, NULL),
('template', 'product', 'eBay Enterprise Affiliate Network (formerly PepperJam Exchange, pepperjamnetwork.com)', 'pepperjamnetwork.txt', 0, 1, '0', 86400, 0, 'name	sku	buy_url	image_url	image_thumb_url	description_short	description_long	price	keywords	category_program	weight', '{product.name|truncate:128}	{product.sku}	{product.url}	{product.image_url}	{product.thumbnail_url}	{product.short_description|truncate:512}	{product.description|truncate:2000}	{product.price}	{product.meta_keyword|replace:,:}	{category.breadcrumb_path|truncate:256}	{product.weight}', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL, NULL),
('template', 'product', 'Affiliate Window (affiliatewindow.com)', 'affiliatewindow.csv', 0, 1, '0', 86400, 0, '"product_id","product_name","price","deep_link","description","image_url","thumb_url","keywords","last_updated"', '{product.sku},{product.name|truncate:255},{product.price_with_tax},{product.url},{product.short_description},{product.image_url|size:200},{product.thumbnail_url|size:70},{product.meta_keyword},{product.updated_at}', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL, NULL),
('template', 'product', 'Zanox (zanox.com)', 'zanox.csv', 0, 1, '0', 86400, 0, '"ID","Product name","Price","Product URL","Category Path","Description","Long Description","Small image URL","Medium image URL","Large image URL"', '{product.sku},{product.name|truncate:150},{product.price_with_tax},{product.url},{category.breadcrumb_path|replace: > : / },{product.short_description|truncate:512},{product.description|truncate:4096},{product.small_image_url|size:100},{product.image_url|size:400},{product.image_url}', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL, NULL),
('template', 'product', 'Linkconnector (linkconnector.com)', 'linkconnector.csv', 0, 1, '0', 86400, 0, '"ProductID","Categories","Title","Description","Price","URL","ThumbURL","ImageURL","Quantity","Keywords"', '{product.sku},{category.breadcrumb_path|truncate:60},{product.name|truncate:80},{product.short_description},{product.price},{product.url},{product.thumbnail_url},{product.image_url},{product.qty},{product.meta_keyword}', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL, NULL),
('template', 'product', 'Webgains (webgains.com)', 'webgains.csv', 0, 1, '0', 86400, 0, '"product_name","product_id","description","deeplink","price","image_url","category_name","delivery_period","delivery_cost"', '{product.name},{product.sku},{product.short_description},{product.url},{product.price},{product.image_url},{category.breadcrumb_path},"",""', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL, NULL),
('template', 'product', 'PerfomanceHorizon (performancehorizon.com)', 'performancehorizon.csv', 0, 1, '0', 86400, 0, '"ID","Product name","Price","Product URL","Category Path","Description","Image URL","Thumbnail URL"', '{product.sku},{product.name},{product.price_with_tax},{product.url},{category.breadcrumb_path},{product.short_description},{product.image_url},{product.thumbnail_url}', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL, NULL),
('template', 'product', 'ImpactRadius (impactradius.com)', 'impactradius.csv', 0, 1, '0', 86400, 0, '"ID","Product name","Price","Product URL","Category Path","Description","Image URL","Thumbnail URL"', '{product.sku},{product.name},{product.price_with_tax},{product.url},{category.breadcrumb_path},{product.short_description},{product.image_url},{product.thumbnail_url}', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL, NULL);
SQL;


$installer->run($insert);
$installer->endSetup();