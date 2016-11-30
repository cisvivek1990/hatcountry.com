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

class Plumrocket_Affiliate_Block_Adminhtml_Affiliate_List_Grid extends Mage_Adminhtml_Block_Widget_Grid 
{
    protected $_types = null;

	public function __construct()
    {
        $this->setId('manage_affiliate_list_grid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('desc');
        $this->setSaveParametersInSession(true);

       	parent::__construct();
    }

    protected function _prepareCollection()
    {
    	$collection = Mage::getModel('affiliate/affiliate')->getCollection();
		$_tp = 	(string) Mage::getConfig()->getTablePrefix();
		$select = $collection
					->getSelect()
					->joinLeft( array('table_types' => ($_tp).'affiliate_types'), 'main_table.type_id = table_types.id', array('type_name' => 'table_types.name'));

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _getTypes()
    {
        if (is_null($this->_types)){
            $this->_types = array();
            $collection = Mage::getSingleton('affiliate/affiliate')->getTypes();
            foreach($collection as $type){
                $this->_types[$type->getId()] = $type->getName();
            } 
        }
        return $this->_types;
    }
 
    protected function _prepareColumns()
    {
		$_helper = Mage::helper('affiliate');
		
        $this->addColumn('id', array(
            'header'    => $_helper->__('ID'),
            'align'     =>'left',
            'index'     => 'id',
            'type' 		=> 'number',
			 'width' => '30px',
        ));
		
        
        $this->addColumn('name', array(
            'header'    => $_helper->__('Name'),
            'align'     =>'left',
            'index'     => 'name',
            'type' => 'text',
        ));
        
        /*
        $this->addColumn('description', array(
            'header'    => $_helper->__('Description'),
            'align'     =>'left',
            'index'     => 'description',
        	'type' => 'text',
        ));
        */
        
        
        $this->addColumn('type_id', array(
            'header'    => $_helper->__('Affiliate Network'),
            'align'     =>'left',
            'index'     => 'type_id',
            'type' => 'options',
            'options' => $this->_getTypes(),
            'frame_callback' => array($this, 'decorateType')
        ));

		       
        $this->addColumn('created_at', array(
            'header'    => $_helper->__('Created At'),
            'align'     => 'center',
            'index'     => 'created_at',
            'type' => 'datetime',
        ));
        
        $this->addColumn('updated_at', array(
            'header'    => $_helper->__('Updated At'),
            'align'     => 'center',
            'index'     => 'updated_at',
            'type' => 'datetime',
        ));
        
        $this->addColumn('status', array(
            'header'    => $_helper->__('Status'),
            'align'     =>'left',
            'index'     => 'status',
            'type' => 'options',
            'options' => Mage::getModel('affiliate/affiliate')->getStatuses(),
            'frame_callback' => array($this, 'decorateStatus')
        ));
        
        $this->addColumn('action',
            array(
                'header'    => $this->__('Action'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'     => 'getId',
                'actions'   => array(
                    array(
                        'caption' => $this->__('Edit'),
                        'url'     => array(
                            'base'=>'*/*/edit',
                            //'params'=>array('store'=>$this->getRequest()->getParam('store'))
                        ),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'id',
        ));

        return parent::_prepareColumns();
    }
    
    public function decorateStatus($value, $row, $column, $isExport)
    {
		if ($row->getStatus() == Plumrocket_Affiliate_Model_Affiliate_Abstract::ENABLED_STATUS) {
			$cell = '<span class="grid-severity-notice"><span>'.$value.'</span></span>';
		} else {
			$cell = '<span class="grid-severity-critical"><span>'.$value.'</span></span>';
		}

        return $cell;
    }

    public function decorateType($value, $row, $column, $isExport)
    {
        $typeId = $row->getType()->getId();
        //var_dump($row->getType());
        if ($typeId == 1){
            $types = $this->_getTypes();
            return '<div style="text-align: center; height: 35px; font-weight: 700; text-transform: uppercase; padding-top: 18px;"><span class="custom-label">'.$types[1].'</span></div>';
        } else {
            return '<div style="text-align: center;"><img style="vertical-align:middle;" src="'.$this->getSkinUrl('images/plumrocket/affiliate/type'.$typeId.'.png').'" /></div>';
        }
    }
    
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('id');

        $this->getMassactionBlock()->addItem('enable', array(
             'label'    => $this->__('Enable'),
             'url'      => $this->getUrl('*/*/enable'),
             'confirm'  => $this->__('Are you sure?'),
        ));
		
		$this->getMassactionBlock()->addItem('disable', array(
             'label'    => $this->__('Disable'),
             'url'      => $this->getUrl('*/*/disable'),
             'confirm'  => $this->__('Are you sure?'),
        ));
		
		$this->getMassactionBlock()->addItem('delete', array(
             'label'    => $this->__('Delete'),
             'url'      => $this->getUrl('*/*/delete'),
             'confirm'  => $this->__('Are you sure?'),
        ));
        
        return $this;
    }
  


    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}
