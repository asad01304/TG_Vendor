<?php

class TG_Vendor_Block_Adminhtml_Vendor_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	
	public function initForm()
	{
		$form = new Varien_Data_Form();
		$form->setHtmlIdPrefix('_vendor');
        $form->setFieldNameSuffix('vendor');
		
		$vendorType = $this->getRequest()->getParam('vendor_type', null);
		if(!$vendorType){
			if($vendorData = Mage::registry('vendor_data')){
				$vendorType = $vendorData->getVendorType();
			}
		}	
		$vendorList = Mage::getConfig()->getNode('default/vendor')->asArray();
		
		$fieldset = $form->addFieldset('vendor_form', 
			array('legend'=>Mage::helper('vendor')->__('Vendor information')));

		$fieldset->addField('name', 'text', array(
			'label'     => Mage::helper('vendor')->__('Vendor Name'),
			'class'     => '',
			'required'  => true,
			'name' 		=> 'name',
		));	
			
		foreach($vendorList[$vendorType]['fields'] as $name => $vendor){
		
			$label = (isset($vendor['label'])) ? (string)$vendor['label']: '';
			$class = (isset($vendor['class'])) ? (string)$vendor['class']: '';
			$type  = (isset($vendor['type']))  ? (string)$vendor['type'] : 'text';
			$required = (isset($vendor['required']) && $vendor['required'] == 1) ? true: false;
		
			$fieldset->addField($name, $type, array(
				'label'     => Mage::helper('vendor')->__($label),
				'class'     => $class,
				'required'  => $required,
				'name'      => $name,
			));
		
		}

		if ( Mage::getSingleton('adminhtml/session')->getVendorData() ){
			$form->setValues(Mage::getSingleton('adminhtml/session')->getVendorData());
			Mage::getSingleton('adminhtml/session')->setVendorData(null);
		} elseif ( Mage::registry('vendor_data') ) {
			$form->setValues(Mage::registry('vendor_data')->getData());
		}
		
		$fieldset->addField('vendor_type', 'hidden', array(
			'name'      => 'vendor_type',
			'value'		=> $vendorType,
		));	

		
		$this->setForm($form);
		return $this;
	}
}