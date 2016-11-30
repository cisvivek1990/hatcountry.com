<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Advanced SEO Suite
 * @version   1.1.1
 * @build     899
 * @copyright Copyright (C) 2015 Mirasvit (http://mirasvit.com/)
 */


$installer = $this;
$version = Mage::helper('mstcore/version')->getModuleVersionFromDb('seoautolink');
if ($version == '1.0.3') {
    return;
} elseif ($version != '1.0.2') {
    die("Please, run migration 1.0.2");
}

$installer->startSetup();

$table = $this->getTable('seoautolink/link');

$write = Mage::getSingleton('core/resource')->getConnection('core_write');

$sql = <<<__SQLPRC
DROP PROCEDURE IF EXISTS upgrade_seoautolink_db_1_0_2_to_1_0_3;
CREATE PROCEDURE upgrade_seoautolink_db_1_0_2_to_1_0_3()
BEGIN
     IF NOT EXISTS( (SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE()
    AND COLUMN_NAME='url_title' AND TABLE_NAME='" . $table . "') ) THEN
        ALTER TABLE `{$this->getTable('seoautolink/link')}` ADD url_title VARCHAR(255) COMMENT 'URL Title' AFTER url_target;
    END IF;
END;

CALL upgrade_seoautolink_db_1_0_2_to_1_0_3();
__SQLPRC;

$write->exec($sql);

$installer->endSetup();