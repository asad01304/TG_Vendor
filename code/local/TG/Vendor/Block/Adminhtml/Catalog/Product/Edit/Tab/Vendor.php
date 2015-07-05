<?php

class TG_Vendor_Block_Adminhtml_Catalog_Product_Edit_Tab_Vendor extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('vendor/catalog/product/vendor.phtml');
    }
	
	public function getVendorList()
	{
		return Mage::getModel('vendor/vendor')
			->getCollection();
	}
	
	public function getProductVendorId()
	{
		$product = Mage::registry('product');
		//$product = $this->getProduct();
		if($product->getId()){
			$productId = $product->getId();
			$assocVendor = Mage::getModel('vendor/product')
				->getCollection()
				->addFieldToFilter('product_id',$productId );
			if($assocVendor->count()){
				return $assocVendor->getFirstItem()->getVendorId ();
			}
		}
		return null;
	}
}

