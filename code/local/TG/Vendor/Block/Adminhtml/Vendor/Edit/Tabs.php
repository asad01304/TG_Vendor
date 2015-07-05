<?php

class TG_Vendor_Block_Adminhtml_Vendor_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

	public function __construct()
	{
		parent::__construct();
		$this->setId('vendor_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('vendor')->__('Vendor Information'));
	}

	protected function _beforeToHtml()
	{
		$vendorType = $this->getRequest()->getParam('vendor_type', null);
		if(!$vendorType){
			if($vendorData = Mage::registry('vendor_data')){
				$vendorType = $vendorData->getVendorType();
			}
		}	
		//echo $vendorType; exit;
		//print_r(Mage::registry('vendor_data'));
		
		//print_r($product );
		if ($vendorType) {
			$this->addTab('form_section', array(
				'label'     => Mage::helper('vendor')->__('Item Information'),
				'title'     => Mage::helper('vendor')->__('Item Information'),
				'content'   => $this->getLayout()->createBlock('vendor/adminhtml_vendor_edit_tab_form')->initForm()->toHtml(),
			));
		
			$this->addTab('assoc_item_section', array(
					'label'     => Mage::helper('vendor')->__('Asssociated Products'),
					'class'     => 'ajax',
					'url'       => $this->getUrl('*/*/assocGrid', array('_current' => true)),
				 ));
		}else{
			 $this->addTab('set', array(
					'label'     => Mage::helper('catalog')->__('Settings'),
					'content'   => $this->getLayout()->createBlock('vendor/adminhtml_vendor_edit_tab_settings')->toHtml(),
					'active'    => true
				));
		}
		return parent::_beforeToHtml();
	}
}