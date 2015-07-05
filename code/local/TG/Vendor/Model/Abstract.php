<?php

class TG_Vendor_Model_Abstract extends Mage_Core_Model_Abstract
{
	protected $_vendorType = Null;
	
    public function _construct()
    {
        parent::_construct();
        $this->_init('vendor/vendor');
    }

	public function validate()
	{
		return $this;
	}
	
	protected function _beforeSave()
	{
		$_fieldValues = array();
		if($this->_vendorType){
			$vendorConfig = Mage::helper('vendor')->getVendorConfig($this->_vendorType);
			
			if(!empty($vendorConfig) && !empty($vendorConfig['fields'])){
				foreach($vendorConfig['fields'] as $key => $field){
					$_fieldValues[$key] = $this->getData($key);
				}
			}
		}
		$this->setFieldValues(serialize($_fieldValues));
	}
	
	public function _afterLoad()
	{
		$_fieldValues = unserialize($this->getFieldValues());
		if($_fieldValues){
			foreach($_fieldValues as $key => $val){
				$this->setData($key , $val);
			}
		}
	}
}

