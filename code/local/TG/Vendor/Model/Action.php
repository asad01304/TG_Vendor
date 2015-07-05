<?php

class TG_Vendor_Model_Action extends Mage_Core_Model_Abstract
{
	function notifyVendors($observer = null)
	{
		$incrementId = Mage::getSingleton('checkout/session')->getLastRealOrderId(); 	
		#$incrementId = '100000091';	
		$order = Mage::getModel('sales/order')->loadByIncrementId($incrementId);
		$vendorInfo = array();
		if($order && $order->getId()){

			foreach($order->getItemsCollection() as $item){
				$productId  = $item->getProductId();
				$qtyOrdered = $item->getQtyOrdered();
				if(($vendorId  = Mage::helper('vendor')->getVendorId($productId))){
					if(!array_key_exists($vendorId, $vendorInfo)){
						$vendorInfo[$vendorId] = array();
					}
					$product = Mage::getModel('catalog/product')->load($productId);
					$vendorInfo[$vendorId][] = $item;
				}
			}
			
			foreach($vendorInfo as $vendorId => $items ){
				if(!($vendor = Mage::getModel('vendor/vendor')->load($vendorId)) || !$vendor->getId()){
					continue; //associated vendor not found, skip
				}
				$vendorType 	= $vendor->getVendorType(); 
				$vendorInstance = Mage::helper('vendor')->getVendorInstance($vendorType);
				$vendorInstance->load($vendorId);
				
				$method = 'notifyVendor';
				$params = array($order, $items , $vendor);
				call_user_func_array(array($vendorInstance, $method), $params);
			}
		}
	
		return $observer;
	}
}		
?>

