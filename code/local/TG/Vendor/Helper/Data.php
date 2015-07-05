<?php

class TG_Vendor_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getVendorConfig($vendorType = null)
	{
		if($vendorType){
			$vendorList = Mage::getConfig()->getNode('default/vendor')->asArray();
			if(array_key_exists($vendorType, $vendorList)){
				return $vendorList[$vendorType];
			}
		}
		return null;
	}
	
	public function getVendorInstance($vendorType)
	{
		$vendorConfig = $this->getVendorConfig($vendorType);
		$vendorInit = (empty($vendorConfig['class']))? 'vendor/vendor' : $vendorConfig['class'];
		$vendorInstance = Mage::getModel($vendorInit);
			
		if(!$vendorInstance){
			throw new Exception("Couldn't load model for vendor type {$vendorType}!");
		}
		return $vendorInstance;
	}
	
	public function getVendorId($productId)
	{
		$vendorCollection = Mage::getModel('vendor/product')
			->getCollection()
			->addFieldToFilter('product_id', $productId)
			->load();
		
		if($vendorCollection->count() )	{
			return $vendorCollection
				->getFirstItem()->getVendorId();
		}
		return null;
	}
	
	public function getVendorOptions()
	{
		$vendorCollection = Mage::getModel('vendor/vendor')
			->getCollection()
			->load();
	
		$vendorArray = array();	
		foreach($vendorCollection->getItems() as $vendor){
			$vendorArray[] = array( 'label'=>$vendor->getName(), 'value'=>$vendor->getId());
		}

		return 	$vendorArray;
	}

}