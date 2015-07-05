<?php

class TG_Vendor_Adminhtml_VendorController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('system/customer_form/functionalarea');
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}
	public function massProductSubscriptionAction()
	{
		$productIds = $this->getRequest()->getParam('product');
		$vendorId   = $this->getRequest()->getParam('vendor_id');
		
		if(isset($vendorId) && is_array($productIds)){

			$vendor = Mage::getModel('vendor/vendor')->load($vendorId);
			if($vendor && $vendor->getId()){

				foreach($productIds as $productId){

					$assocVendor = Mage::getModel('vendor/product')
						->getCollection()
						->addFieldToFilter('product_id',$productId)
						->load();

					$assocId = ($assocVendor->count())? $assocVendor->getFirstItem()->getId():null;
					$model = Mage::getModel('vendor/product');
					if($assocId){
						$model->load($assocId);
					}

					$model->setProductId($productId)
						->setVendorId($vendorId)
						->save();
				}
			}
		}
		$this->_redirect('adminhtml/catalog_product/');
	}

	public function editAction() {
	
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('vendor/abstract')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('vendor_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('system/customer_form/functionalarea');

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('vendor/adminhtml_vendor_edit'))
				->_addLeft($this->getLayout()->createBlock('vendor/adminhtml_vendor_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')
				->addError(Mage::helper('vendor')
				->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() 
	{
		if ($data = $this->getRequest()->getPost('vendor')) {
		
			try {
				$vendorList = Mage::getConfig()->getNode('default/vendor')->asArray();
				$vendorType = (empty($data['vendor_type']))? null: $data['vendor_type'];
			
				if(!array_key_exists($vendorType, $vendorList)){
					throw new Exception('Wrong vendor type!');
				}
			
				$vendorConfig = $vendorList[$vendorType];
				
				$vendorInit = (empty($vendorConfig['class']))? 'vendor/vendor' : $vendorConfig['class'];
				$vendorInstance = Mage::getModel($vendorInit);
			
				if(!$vendorInstance){
					throw new Exception("Couldn't load model for vendor type {$vendorType}!");
				}
			
				

				$vendorInstance->setData($data);
				$vendorInstance->setId($this->getRequest()->getParam('id', null));
				$vendorInstance->validate()->save();

				Mage::getSingleton('adminhtml/session')
					->addSuccess(Mage::helper('vendor')
					->__('Vendor Information was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
		
        Mage::getSingleton('adminhtml/session')
			->addError(Mage::helper('vendor')
			->__('Unable to find item to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('vendor/vendor');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')
					->addSuccess(Mage::helper('adminhtml')
					->__('Vendor Information was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $functionalareaIds = $this->getRequest()->getParam('vendor');
        if(!is_array($functionalareaIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($functionalareaIds as $functionalareaId) {
                    $functionalarea = Mage::getModel('vendor/vendor')->load($functionalareaId);
                    $functionalarea->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($functionalareaIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
	public function assocGridAction()
    {
        $this->loadLayout();
        return $this->getResponse()->setBody(
            $this->getLayout()
                ->createBlock('vendor/adminhtml_items_product')
                ->setIndex($this->getRequest()->getParam('index'))
                ->toHtml()
           );
    }
}