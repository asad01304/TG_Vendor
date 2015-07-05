<?php

class TG_Vendor_Block_Adminhtml_Vendor_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('vendorGrid');
		$this->setDefaultSort('entity_id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
	}

	protected function _prepareCollection()
	{
		$collection = Mage::getModel('vendor/vendor')->getCollection();
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

	protected function _prepareColumns()
	{

		$this->addColumn('vendor_id', array(
			'header'    => Mage::helper('vendor')->__('ID'),
			'align'     =>'right',
			'width'     => '50px',
			'index'     => 'vendor_id',
		));

		$this->addColumn('name', array(
			'header'    => Mage::helper('vendor')->__('Vendor Info'),
			'align'     =>'left',
			'index'     => 'name',
		));
		
		
		$vendorList = Mage::getConfig()->getNode('default/vendor')->asArray();
		$vendorOptions = array();
		foreach($vendorList as $key => $vendor){
			$vendorOptions[$key] = $vendor['name'];
		}
		
		 $this->addColumn('vendor_type',
            array(
                'header'=> Mage::helper('catalog')->__('Vendor Type'),
                'index' => 'vendor_type',
                'type'  => 'options',
                'options' => $vendorOptions,
        ));

		$this->addColumn('action',
			array(
				'header'    =>  Mage::helper('vendor')->__('Action'),
				'width'     => '100',
				'type'      => 'action',
				'getter'    => 'getId',
				'actions'   => array(
								array(
									'caption'   => Mage::helper('vendor')->__('Edit'),
									'url'       => array('base'=> '*/*/edit'),
									'field'     => 'id'
									)
								),
				'filter'    => false,
				'sortable'  => false,
				'index'     => 'stores',
				'is_system' => true,
        ));

		return parent::_prepareColumns();
	}
	
	public function getRowUrl($row)
	{
		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}

}