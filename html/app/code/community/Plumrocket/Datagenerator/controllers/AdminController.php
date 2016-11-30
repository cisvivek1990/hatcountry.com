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

class Plumrocket_Datagenerator_AdminController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction()
    {
		$this->_isAllowed();
		$this->loadLayout();
		$this->renderLayout();
    }
	
	public function editAction()
    {
		$this->_isAllowed();
		$result = false;
		
		if ($id = $this->getRequest()->getParam('id')) {
			$template = Mage::getModel('datagenerator/template')->load($id);
			if ($template->getId() && $template->getTypeEntity() == 'feed') {
				Mage::register('template', $template);
				$result = true;
			}
		}
		
		if ($result) {
			$this->loadLayout();
			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(
				Mage::helper('datagenerator')->__('This Data Feed no longer exists.'));
			$this->_redirect('*/*/');
		}
    }
    
	public function newAction()
    {
		$this->_isAllowed();
		
		$template = Mage::getModel('datagenerator/template');
		Mage::register('template', $template);
		
		$this->loadLayout();
		$this->renderLayout();
    }
	
	/**
     * Save action
     */
    public function saveAction()
    {
        // check if data sent
        if (($data = $this->getRequest()->getPost()) && $this->_isAllowed()) {
            $data = $this->_filterPostData($data);
            
	        //validating
            if (!$this->_validatePostData($data)) {
                $this->_redirect('*/*/index', array('_current' => true));
                return;
            }
            
            $model = (isset($data['entity_id']) && $data['entity_id'] > 0) ?
            	 Mage::getModel('datagenerator/template')->load($data['entity_id']) : 
            	 Mage::getModel('datagenerator/template');
				 
			$model->setData($data);
			if ($model->getTypeEntity() != 'feed') {
				$this->_getSession()->addError(
					Mage::helper('datagenerator')->__('You have tried save the template of data feed.'));
			} else {
	            // try to save it
	            try {
	                $model->save();
	
	                Mage::getSingleton('adminhtml/session')->addSuccess(
	                    Mage::helper('datagenerator')->__('The Data Feed has been saved.'));
	            } catch (Mage_Core_Exception $e) {
	                $this->_getSession()->addError(
	                	Mage::helper('datagenerator')->__($e->getMessage()));
	            } catch (Exception $e) {
	                $this->_getSession()->addError($e,
	                    Mage::helper('datagenerator')->__($e->getMessage()));
	            }
			}
        }
        
        if ($this->getRequest()->getParam('back')) {
			$this->_redirect('*/*/edit', array('id' => $model->getId(), 'active_tab' => $this->getRequest()->getParam('active_tab', 'general_section')));
			return;
		}
                
        $this->_redirect('*/*/index', array('_current' => true));
    }

	public function deleteAction()
	{
		$this->_isAllowed();
		$result = false;
	
		if ($id = $this->getRequest()->getParam('id')) {
			$model = Mage::getModel('datagenerator/template')->load($id);
			if ($model->getTypeEntity() == 'feed') {
				$model->delete();
				$result = true;
			}
		}
		
		if ($result) {
			Mage::getSingleton('adminhtml/session')->addSuccess(
				Mage::helper('datagenerator')->__('The Data Feed has been deleted.'));
		} else {
			Mage::getSingleton('adminhtml/session')->addError(
				Mage::helper('datagenerator')->__('The Data Feed has not been deleted.'));
		}
		$this->_redirect('*/*/index', array('_current' => true));
	}

    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        switch ($this->getRequest()->getActionName()) {
            case 'new':
            case 'save':
            case 'edit':
                return Mage::getSingleton('admin/session')->isAllowed('plumrocket/datagenerator/save');
                break;
            case 'delete':
                return Mage::getSingleton('admin/session')->isAllowed('plumrocket/datagenerator/delete');
                break;
            default:
                return Mage::getSingleton('admin/session')->isAllowed('plumrocket/datagenerator');
                break;
        }
    }
	
	
	/**
     * Filtering posted data. Converting localized data if needed
     *
     * @param array
     * @return array
     */
    protected function _filterPostData($data)
    {
    	$data['updated_at'] = strftime('%F %T');
		
    	$entityId = (int)$data['entity_id'];
    	if ($entityId == 0) {
    		unset($data['entity_id']);
			
    		$data['created_at'] = strftime('%F %T');
    	}

    	while (($item = $this->_getTemplateByUrlKey($data['url_key'], $entityId))
    		&& ($item->getId() > 0)
    	) {
			if ($ext = strrchr($data['url_key'], '.')) {
				$data['url_key'] = str_replace($ext, '_re' . $ext, $data['url_key']);
			} else {
				$data['url_key'] .= '_re';
			}
		}

    	$data['count'] = (int)$data['count'];
		$data['cache_time'] = (int)$data['cache_time'];
		$data['type_entity'] = 'feed';

		unset($data['replace_from'], $data['replace_to']);
    	
		return $data;
    }

    private function _getTemplateByUrlKey($url_key, $entityId)
    {
    	return Mage::getModel('datagenerator/template')
			->getCollection()
			->addFieldToFilter('type_entity', 'feed')
			->addFieldToFilter('url_key', $url_key)
			->addFieldToFilter('entity_id', array('neq' => $entityId))
			->getFirstItem();
    }

    /**
     * Validate post data
     *
     * @param array $data
     * @return bool     Return FALSE if someone item is invalid
     */
    protected function _validatePostData($data)
    {
		return true;
    }
	
	public function massAction()
	{
		$action = $this->getRequest()->getParam('action');
		$ids = $this->getRequest()->getParam('feed_id');
		
		if (is_array($ids) && $ids) {
			try {
				foreach ($ids as $id) {
					$model = Mage::getModel('datagenerator/template')->load($id);
					if ($model->getTypeEntity() == 'feed') {
						switch ($action) {
							case 'disable':
								$model->setEnabled('0')
									->save();
								break;
							case 'delete':
								$model->delete();
								break;
							case 'clean':
								$model->cleanCache();
								break;
						}
						/*
						$model->delete();
						 * */
					}
				}
				$messages = array(
					'disable'	=> 'Total of %s record(s) were successfully disabled',
					'delete'	=> 'Total of %s record(s) were successfully deleted',
					'clean'		=> 'The cache was successfully cleaned'
				);
				
				Mage::getSingleton('adminhtml/session')->addSuccess(
					Mage::helper('datagenerator')->__($messages[$action], count($ids))
				);
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError(
					Mage::helper('datagenerator')->__($e->getMessage()));
			}
		} else {
			Mage::getSingleton('adminhtml/session')->addError(
				Mage::helper('datagenerator')->__('Please select item(s)'));
		}
		$this->_redirect('*/*/index');
	}

	public function templateAction()
	{
		if ($this->_request->isXmlHttpRequest()) {
			$template_id = (int)$this->getRequest()->getParam('template_id');
			$data['success'] = false;
					
			if ($template_id) {
				$template = Mage::getModel('datagenerator/template')->load($template_id);
				if ($template->getId() && ($template->getTypeEntity() == 'template')) {
					$data = $template->getData();
					$data['success'] = true;
				}
			}
			
			$jsonData = Zend_Json::encode($data);
			$this->getResponse()
				->setHeader('Content-Type', 'text/html')
				->setBody($jsonData)
				->sendResponse();
			exit;
		}
	}
}
