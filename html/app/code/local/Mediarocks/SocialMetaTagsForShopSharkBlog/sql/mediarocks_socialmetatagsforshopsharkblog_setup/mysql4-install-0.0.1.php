<?php
/**
 * Media Rocks GbR
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA that is bundled with 
 * this package in the file MEDIAROCKS-LICENSE-COMMUNITY.txt.
 * It is also available through the world-wide-web at this URL:
 * http://solutions.mediarocks.de/MEDIAROCKS-LICENSE-COMMUNITY.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package is designed for Magento COMMUNITY edition. 
 * Media Rocks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * Media Rocks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please send an email to support@mediarocks.de
 *
 */

$installer = $this;
$installer->startSetup();

$blogTable = $installer->getTable('blog/blog');
if ($blogTable) {
    $installer->getConnection()->addColumn(
        $blogTable,
        'fb_share_image',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable' => false,
            'default' => '',
            'length' => 255,
            'comment' => 'Facebook Share Image'
        )
    );
    $installer->getConnection()->addColumn(
        $blogTable,
        'facebook_meta_title',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable' => false,
            'default' => '',
            'length' => 255,
            'comment' => 'Facebook Meta Title'
        )
    );
    $installer->getConnection()->addColumn(
        $blogTable,
        'facebook_meta_description',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable' => false,
            'default' => '',
            'length' => 800,
            'comment' => 'Facebook Meta Description'
        )
    );
    $installer->getConnection()->addColumn(
        $blogTable,
        'twitter_share_image',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable' => false,
            'default' => '',
            'length' => 255,
            'comment' => 'Twitter Share Image'
        )
    );
    $installer->getConnection()->addColumn(
        $blogTable,
        'twitter_meta_title',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable' => false,
            'default' => '',
            'length' => 255,
            'comment' => 'Twitter Meta Title'
        )
    );
    $installer->getConnection()->addColumn(
        $blogTable,
        'twitter_meta_description',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable' => false,
            'default' => '',
            'length' => 800,
            'comment' => 'Twitter Meta Description'
        )
    );
}

$catTable = $installer->getTable('blog/cat');
if ($catTable) {
    $installer->getConnection()->addColumn(
        $catTable,
        'fb_share_image',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable' => false,
            'default' => '',
            'length' => 255,
            'comment' => 'Facebook Share Image'
        )
    );
    $installer->getConnection()->addColumn(
        $catTable,
        'facebook_meta_title',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable' => false,
            'default' => '',
            'length' => 255,
            'comment' => 'Facebook Meta Title'
        )
    );
    $installer->getConnection()->addColumn(
        $catTable,
        'facebook_meta_description',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable' => false,
            'default' => '',
            'length' => 800,
            'comment' => 'Facebook Meta Description'
        )
    );
    $installer->getConnection()->addColumn(
        $catTable,
        'twitter_share_image',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable' => false,
            'default' => '',
            'length' => 255,
            'comment' => 'Twitter Share Image'
        )
    );
    $installer->getConnection()->addColumn(
        $catTable,
        'twitter_meta_title',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable' => false,
            'default' => '',
            'length' => 255,
            'comment' => 'Twitter Meta Title'
        )
    );
    $installer->getConnection()->addColumn(
        $catTable,
        'twitter_meta_description',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable' => false,
            'default' => '',
            'length' => 800,
            'comment' => 'Twitter Meta Description'
        )
    );
}

$installer->endSetup();