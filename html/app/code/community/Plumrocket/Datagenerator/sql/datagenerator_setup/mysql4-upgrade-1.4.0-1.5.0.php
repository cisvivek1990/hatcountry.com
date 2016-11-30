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
DELETE FROM `{$this->getTable('datagenerator_templates')}` WHERE `url_key` = 'cj.csv' AND `type_entity` = 'template';

ALTER TABLE `{$this->getTable('datagenerator_templates')}`
ADD `date_format` VARCHAR( 32 ),
ADD `replace_from` VARCHAR( 16 ),
ADD `replace_to` VARCHAR( 16 );

INSERT INTO `{$this->getTable('datagenerator_templates')}` (`type_entity`, `type_feed`, `name`, `url_key`, `count`, `enabled`, `store_id`, `cache_time`, `template_id`, `code_header`, `code_item`, `code_footer`, `created_at`, `updated_at`, `date_format`, `replace_from`, `replace_to`) VALUES
('template', 'product', 'Commission Junction (cj.com)', 'cj.csv', 0, 1, '0', 86400, 0, 'PROGRAMNAME,PROGRAMURL,LASTUPDATED,NAME,KEYWORDS,DESCRIPTION,SKU,MANUFACTURER,MANUFACTURERID,UPC,ISBN,CURRENCY,SALEPRICE,PRICE,RETAILPRICE,FROMPRICE,BUYURL,IMPRESSIONURL,IMAGEURL,ADVERTISERCATEGORY,THIRDPARTYID,THIRDPARTYCATEGORY,AUTHOR,ARTIST,TITLE,PUBLISHER,LABEL,FORMAT,SPECIAL,GIFT,PROMOTIONALTEXT,STARTDATE,ENDDATE,OFFLINE,ONLINE,INSTOCK,CONDITION,WARRANTY,STANDARDSHIPPINGCOST', '{product.name},{product.url},{product.updated_at},{product.name},{product.meta_keyword},{product.description},{product.sku},{product.manufacturer},,,,\"USD\",{product.special_price},{product.price},{product.price},,{product.url},{product.thumbnail_url},{product.image_url},{category.name},{category.url_key},{category.name},,,,,,,No,, {product.short_description},{category.privatesale_date_start},{category.privatesale_date_end},No,Yes,Yes,New,,\"\"', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'm/d/Y h:i:s A', '', ''),
('template', 'product', 'ShareASale (shareasale.com)', 'shareasale.csv', 0, 1, '0', 86400, 0, '\"ProductID\",\"Name\",\"MerchantID\",\"Link\",\"Thumbnail\",\"BigImage\",\"Price\",\"RetailPrice\",\"Category\",\"Subcategory\",\"Description\",\"Lastupdated\",\"Status\",\"Manufacturer\",\"ShortDescription\",\"SKU\"', '{product.id},{product.name},,{product.url},{product.thumbnail_url},{product.image_url},{product.special_price},{product.price},{category.name},\"\",{product.description},{product.updated_at},\"instock\",{product.manufacturer},{product.short_description},{product.sku}', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', '', ''),
('template', 'product', 'LinkShare (linkshare.com)', 'linkshare.txt', 0, 1, '0', 86400, 0, 'HDR|__PLEASE_PROVICE_MERCHANT_ID__|{site.name}|{site.now}', '{product.id}|{product.name}|{product.sku}|{category.name}||{product.url}|{product.image_url}||{no_br_html}{product.short_description}{/no_br_html}|{no_br_html}{product.description}{/no_br_html}||||{product.price}|{category.privatesale_date_start}|{category.privatesale_date_end}|{category.brand_name}||N|{product.meta_keyword}|N||||In Stock|||Y|Y|Y|USD||', 'TRL|{site.count}', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Y-m-d/H:i:s', '|', '');
");


$installer->endSetup();