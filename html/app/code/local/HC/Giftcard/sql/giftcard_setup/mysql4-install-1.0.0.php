<?php
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$installer = $this;
$installer->startSetup();

//$installer->run("ALTER TABLE  sales_flat_order ADD COLUMN hc_gift_card_sent  BIT;");

/* @var $this Mage_Core_Model_Resource_Setup */
Mage::log('ins');

$installer->getConnection()->addColumn('sales_flat_order',
										'hc_gift_card_sent',
										array(
										'type'=>Varien_Db_Ddl_Table::TYPE_BOOLEAN,
										'unsigned' => true,
										'nullable' => true,
										'comment'=>'test',
										'primary'=>false,
										'default'=> 0
										));


$partialGiftCardName = 'hc_gift_card_coupons';
$tableName = $installer->getTable($partialGiftCardName);

if ($installer->getConnection()->isTableExists($tableName) != true) {
    $table = $installer->getConnection()
        ->newTable($installer->getTable($partialGiftCardName))
        ->addColumn('coupon_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
        ), 'Coupon Id')
      ->addColumn('code', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'nullable'  => false,
        ), 'Code')
          ->addColumn('amount', Varien_Db_Ddl_Table::TYPE_DECIMAL,'12,4', array(
              'nullable'  => false,
              'default'   => '0.0000'
        ), 'Amount')
        ->addColumn('activated_at', Varien_Db_Ddl_Table::TYPE_DATETIME, CURRENT_TIMESTAMP, array(
            'nullable'  => true,
        ), 'Activated At')
        ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
           'nullable'  => false
        ), 'Created At')
        ->setComment('Table for storing partial gift card data');

    $installer->getConnection()->createTable($table);
}

$helper = Mage::helper('hc_giftcard');
$data = array(
    'product_ids' => null,
    'name' =>  $helper ->getRuleName(),
    'description' => null,
    'is_active' => 1,
    'website_ids' => array(1),
    'customer_group_ids' => array(1),
    'coupon_type' => 2,
    'use_auto_generation' => 1,
    'uses_per_coupon' => 1,
    'uses_per_customer' => 1,
    'from_date' => null,
    'to_date' => null,
    'sort_order' => null,
    'is_rss' => 1,
    'rule' => array(
        'conditions' => array()
    ),
    'simple_action' => 'cart_fixed',
    'discount_amount' => 0,
    'discount_qty' => 0,
    'discount_step' => null,
    'apply_to_shipping' => 0,
    'simple_free_shipping' => 0,
    'stop_rules_processing' => 0,
    'rule' => array(
        'actions' => array(
            array()
        )
    ),
    'store_labels' => array('Partial Gift Card')
);

$model = Mage::getModel('salesrule/rule');

$validateResult = $model->validateData(new Varien_Object($data));

if ($validateResult == true) {

    if (isset($data['rule']['conditions'])) {
        $data['conditions'] = $data['rule']['conditions'];
    }

    if (isset($data['rule']['actions'])) {
        $data['actions'] = $data['rule']['actions'];
    }

    unset($data['rule']);

    $model->loadPost($data);

    $model->save();
}

$installer->endSetup();
