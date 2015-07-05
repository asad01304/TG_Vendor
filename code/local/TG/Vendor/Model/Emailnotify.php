<?php

class TG_Vendor_Model_EmailNotify extends TG_Vendor_Model_Abstract
{
	protected $_vendorType = 'email_notification';
	const EMAIL_TEMPLATE_XML_PATH = 'sales/order_notify/vendor_template';
	
    public function _construct()
    {
        parent::_construct();
		$this->_init('vendor/vendor');
    }
    
    
	
	public function getEmailList()
	{
		//$email = $this->getEmail();
		$email = $this->getVendorEmail();
		
		$delimiter = ',';
		return explode( $delimiter  , $email );
		
	}
	
	public function getCcList()
	{
		$ccList = $this->getCcTo();
		if(empty($ccList)){
			return null;
		}
		$delimiter = ',';
		return explode( $delimiter  , $ccList );
	}	
	
	public function notifyVendor($order, $items)
	{
		$vendorId = $this->getVendorId();
		$orderId  = $order->getId();

		$mailSubject = 'New Order from Nursing Uniforms'; //Should come from config
		$sender = Array('name'  => 'Nursing Unifroms', 'email' => 'pii@invesp.com', ); //Should come from config
				
		$email = $this->getEmailList(); 
		$name  = 'Nursing Unifroms';
		$vars  =  Array('items'  => $items,	'vendor' => $this, /*'product'=> $product,*/ 'order'  => $order, );
	
		$storeId = Mage::app()->getStore()->getId(); 
		$templateId	 = Mage::getStoreConfig(self::EMAIL_TEMPLATE_XML_PATH);	
				
		$translate = Mage::getSingleton('core/translate');
		$mailTemplate = Mage::getModel('core/email_template')
			->setTemplateSubject($mailSubject);
				
		if(($ccList = $this->getCcList()) && is_array($ccList)){
			foreach($ccList as $cc){
				$mailTemplate->addBcc($cc);
			}	
		}

		$mailTemplate->sendTransactional($templateId, $sender, $email, $name, $vars, $storeId);
		$translate->setTranslateInline(true);
				
		foreach($items as $item){
			$vendorLog  = Mage::getModel('vendor/log')
				->setProductId($item->getProductId())
				->setVendorId($this->getVendorId())
				->setItemId($item->getId())
				->setOrderId($orderId)
				->setStatus(1)
				->save();
		}
		return true;

	}
	
}

