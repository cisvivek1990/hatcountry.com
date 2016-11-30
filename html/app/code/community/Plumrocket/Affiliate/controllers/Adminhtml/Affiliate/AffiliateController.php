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

class Plumrocket_Affiliate_Adminhtml_Affiliate_AffiliateController extends Mage_Adminhtml_Controller_Action
{
		protected function _initAction()
		{
				$this->loadLayout()->_setActiveMenu('plumrocket')->_addBreadcrumb(Mage::helper('adminhtml')->__('Affiliate Programs'),Mage::helper('adminhtml')->__('Affiliate Programs'));
				return $this;
		}
		public function indexAction() 
		{
			    $this->_title($this->__('Manage Affiliate Programs'));

				$this->_initAction();
				$this->renderLayout();
		}
		
		public function editAction()
		{	
			$_request = $this->getRequest();		    
			$this->_title($this->__('Edit Affiliate Program'));
				
			if ($id = $_request->getParam('id')){
				$affiliate = Mage::getModel('affiliate/affiliate')->load($id);
			} else {
				$affiliate = Mage::getModel('affiliate/affiliate')->getTypedModel($_request->getParam('type_id'));
			}
			
			if (!$id || $affiliate->getId()) {
				
				if ($typeId = $_request->getParam('type_id')){
					$affiliate->setTypeId($typeId);
					}
				
				Mage::register('current_affiliate', $affiliate);
				$this->loadLayout();
				$this->renderLayout();
			} else {
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('affiliate')->__('Item does not exist.'));
				$this->_redirect('*/*/');
			}
		}

		public function newAction()
		{
			$this->_title($this->__('New Affiliate Program'));
			Mage::register('current_affiliate', Mage::getModel('affiliate/affiliate'));
			$this->loadLayout();
			$this->renderLayout();
		}
		
		public function saveAction()
		{
			$_request = $this->getRequest();
			
			//if (!$_request->getParam('affiliate_id') && $_request->getParam('type_id')){
			if (!$_request->getParam('save')){
				$this->_redirect('*/*/edit', array('type_id' => $_request->getParam('type_id')));
				return $this;
			}
			
			$data = $_request->getParams();

			if ($data) {
				try {
					$date			= Mage::getModel('core/date')->date();

					if (!empty($data['id'])){
						$affiliate = Mage::getModel('affiliate/affiliate')->load($data['id']);	
					} else {
						//$affiliate = Mage::getModel('affiliate/affiliate');
						$affiliate = Mage::getModel('affiliate/affiliate')->getTypedModel($_request->getParam('type_id'));
					}
					
					$aMediaDName	= $affiliate->getAffiliateMediaDirName() . DS . time();
					$affiliateData 	= $affiliate->getData();
					$js_path		= Mage::getBaseDir('media') . DS . $aMediaDName . DS;
					
					$data['updated_at'] = $date;
					if (empty($data['id'])){
						$data['created_at'] = $date;
						unset($data['id']);
					}
					
					
					if (!empty($_FILES)){
						foreach($affiliate->getPageSections() as $section){
							$fileLable = 'section_'.$section['key'].'_library';
							if (isset($data[$fileLable]['delete']) && $data[$fileLable]['delete'] == 1) {}
							elseif (!empty($_FILES[$fileLable]['name'])) {
								$uploader = new Varien_File_Uploader($fileLable);
								$uploader->setAllowedExtensions(array('js'));
								$uploader->setAllowRenameFiles(false);
								$uploader->setFilesDispersion(false);
									// Set media as the upload dir
								$js_path  = Mage::getBaseDir('media') . DS . $aMediaDName . DS;
								$uploader->save($js_path, $_FILES[$fileLable]['name']);
								$data[$fileLable] = $aMediaDName . DS . Varien_File_Uploader::getCorrectFileName($_FILES[$fileLable]['name']);
							}/* elseif ($oldImg) {
								$data[$fileLable] = $affiliateData[$fileLable];
							}*/
						}
					}
					
					foreach($affiliate->getPageSections() as $section){
						$fileDeleteLable = 'section_'.$section['key'].'_library_delete';
						$fileLable = 'section_'.$section['key'].'_library';
						if (isset($data[$fileDeleteLable])){
							$data[$fileLable] = '';
							@unlink(Mage::getBaseDir('media') . DS . $affiliate->getData($fileLable));
						}
					}

					if (!empty($data['additional_data'])) {
						$affiliate->setAdditionalDataValues($data['additional_data']);
						unset($data['additional_data']);
					}

					$affiliate->setStores($data['stores']);
					unset($data['stores']);

					$affiliate->addData($data)->save();

					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Affiliate was successfully saved'));
					Mage::getSingleton('adminhtml/session')->setAffiliateData(false);

					if ($this->getRequest()->getParam('back')) {
						$this->_redirect('*/*/edit', array('id' => $affiliate->getId()));
						return;
					}
					
					$this->_redirect('*/*/');
					return;
				} 
				catch (Exception $e) {
					Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
					Mage::getSingleton('adminhtml/session')->setAffiliateData($this->getRequest()->getPost());
					$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id'), 'type_id' => $this->getRequest()->getParam('type_id')));
					
					return;
				}
			}
			$this->_redirect('*/*/');
		}


		public function deleteAction()
		{
			if( $ids = $this->getRequest()->getParam('id') ) {
				if (!is_array($ids)){
					$ids = array($ids);
				}
				try {
					$affiliate = Mage::getModel('affiliate/affiliate');
					foreach($ids as $id){
						$affiliate->setId($id)->delete();
					}
					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Affiliate(s) was successfully deleted'));
					$this->_redirect('*/*/');
				} 
				catch (Exception $e) {
					Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
					$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				}
			}
			$this->_redirect('*/*/');
		}
		
		
		public function enableAction()
		{
			$this->_changeStatusAction(Plumrocket_Affiliate_Model_Affiliate::ENABLED_STATUS);
		}
		
		public function disableAction()
		{
			$this->_changeStatusAction(Plumrocket_Affiliate_Model_Affiliate::DISABLED_STATUS);
		}
		
		private function _changeStatusAction($status)
		{
			if( $ids = $this->getRequest()->getParam('id') ) {
				if (!is_array($ids)){
					$ids = array($ids);
				}
				try {
					$affiliate = Mage::getModel('affiliate/affiliate');
					foreach($ids as $id){
						$affiliate->load($id)->setStatus($status)->save();
					}
					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Affiliate(s) was successfully updated'));
					$this->_redirect('*/*/');
				} 
				catch (Exception $e) {
					Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				}
			}
			$this->_redirect('*/*/');
		}
		

		
}