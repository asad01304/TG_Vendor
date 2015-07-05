<?php

class TG_Vendor_Block_Adminhtml_Vendor extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$this->_controller = 'adminhtml_vendor';
		$this->_blockGroup = 'vendor';
		$this->_headerText = Mage::helper('vendor')->__('Vendor Information');
		$this->_addButtonLabel = Mage::helper('vendor')->__('Add Vendor');
		parent::__construct();
	}
}