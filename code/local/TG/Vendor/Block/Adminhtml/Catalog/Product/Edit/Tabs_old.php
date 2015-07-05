<?php


class TG_Vendor_Block_Adminhtml_Catalog_Product_Edit_Tabs extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs
{

    protected function _prepareLayout()
	{
		parent::_prepareLayout();
		$product = $this->getProduct();

        if (!($setId = $product->getAttributeSetId())) {
            $setId = $this->getRequest()->getParam('set', null);
        }

        if ($setId) {
			
			//add logic to check if vendor module on and have any vendor information
			$this->addTab('vendor_information', array(
				'label'     => Mage::helper('vendor')->__('Vendor Information'),
				'content'   => $this->getLayout()->createBlock('vendor/adminhtml_catalog_product_edit_tab_vendor')->toHtml(),
				'active'    => false ,
			));
		}		
		//return parent::_prepareLayout();
    }

}
