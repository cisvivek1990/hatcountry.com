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

class Plumrocket_Datagenerator_Block_Adminhtml_List_Grid extends Mage_Adminhtml_Block_Widget_Grid 
{
	public function __construct()
    {
       	parent::__construct();
        $this->setId('manage_datagenerator_list_grid');
        $this->setDefaultSort('name');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
    }
	
	protected function _prepareCollection()
	{	
		$collection = Mage::getModel('datagenerator/template')
			->getCollection()
			->addFieldToFilter('type_entity', 'feed');

        $this->setCollection($collection);
        parent::_prepareCollection();
			
	    foreach($this->getCollection() as $template) {
	        if ($template->getStoreId() && $template->getStoreId() != '0') {
	            $template->setStoreId(explode(',', $template->getStoreId()));
	        } else {
	            $template->setStoreId(array('0'));
	        }
	    }
        return $this;
	}
 
    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('datagenerator')->__('ID'),
            'align'     =>'left',
            'index'     => 'entity_id',
            'type' 		=> 'text',
			'width' => '3%',
        ));
        
        $this->addColumn('name', array(
            'header'    => Mage::helper('datagenerator')->__('Name'),
            'align'     => 'left',
            'index'     => 'name',
            'type' 		=> 'text',
			'width' 	=> '20%',
        ));
        
        $this->addColumn('url_key', array(
            'header'    => Mage::helper('datagenerator')->__('URL Key'),
            'align'     => 'left',
            'index'     => 'url_key',
            'type' 		=> 'text',
			'width' 	=> '16%',
        ));
        
        $this->addColumn('count', array(
            'header'    => Mage::helper('datagenerator')->__('Number of Items'),
            'align'     =>'left',
            'index'     => 'count',
            'type' 		=> 'number',
            'renderer'  => 'datagenerator/adminhtml_list_render_count',
			'width' 	=> '7%',
        ));
        
        $this->addColumn('type_feed', array(
        	'header'    => Mage::helper('datagenerator')->__('Type'),
            'align'     =>'left',
            'index'     => 'type_feed',
            'type' 		=> 'options',
            'options' 	=> Mage::getModel('datagenerator/template')->getTypesOptions(),
        	'width' 	=> '10%',
        ));
		
		if (!Mage::app()->isSingleStoreMode()) {
			$this->addColumn('store_id', array(
				'header'        => Mage::helper('datagenerator')->__('Store View'),
				'index'         => 'store_id',
				'type'          => 'store',
				'store_all'     => true,
				'store_view'    => true,
				'sortable'      => true,
				'width' 		=> '10%',
			));
		}
		
		$this->addColumn('enabled', array(
        	'header'    => Mage::helper('datagenerator')->__('Status'),
            'align'     =>'left',
            'index'     => 'enabled',
            'type' 		=> 'options',
            'options' 	=> Mage::getModel('datagenerator/template')->getEnabledOptions(),
        	'width' 	=> '10%',
        ));
		
		$this->addColumn('created_at', array(
        	'header'    => Mage::helper('datagenerator')->__('Created At'),
            'align'     =>'left',
            'index'     => 'created_at',
            'type' 		=> 'datetime',
        	'width' 	=> '12%',
        ));
		
		$this->addColumn('updated_at', array(
        	'header'    => Mage::helper('datagenerator')->__('Updated At'),
            'align'     =>'left',
            'index'     => 'updated_at',
            'type' 		=> 'datetime',
        	'width' 	=> '12%',
        ));
		
        return parent::_prepareColumns();
    }

	protected function _prepareMassaction()
	{
		$this->setMassactionIdField('entity_id');
		$this->getMassactionBlock()->setFormFieldName('feed_id');
		$this->getMassactionBlock()
			->addItem('disable', array(
				'label'		=> Mage::helper('datagenerator')->__('Disable'),
				'url'		=> $this->getUrl('*/*/mass', array('action' => 'disable'))
			))
			->addItem('delete', array(
				'label'		=> Mage::helper('datagenerator')->__('Delete'),
				'url'		=> $this->getUrl('*/*/mass', array('action' => 'delete')),
				'confirm'	=> Mage::helper('datagenerator')->__('Are you sure?')
			))
			->addItem('clean', array(
				'label'		=> Mage::helper('datagenerator')->__('Clean Cache'),
				'url'		=> $this->getUrl('*/*/mass', array('action' => 'clean'))
			));
		return $this;
	}


    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}
