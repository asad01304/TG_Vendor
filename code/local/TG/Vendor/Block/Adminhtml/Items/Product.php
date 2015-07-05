<?php

class TG_Vendor_Block_Adminhtml_Items_Product extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('vendor_product_search_grid');
        $this->setDefaultSort('product_id');
        $this->setUseAjax(true);
    }

    protected function _beforeToHtml()
    {
        $this->setId($this->getId().'_'.$this->getIndex());
        $this->getChild('reset_filter_button')->setData('onclick', $this->getJsObjectName().'.resetFilter()');
        $this->getChild('search_button')->setData('onclick', $this->getJsObjectName().'.doFilter()');
        return parent::_beforeToHtml();
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->setStore($this->_getStore())
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('price')
            ->addAttributeToSelect('attribute_set_id');
           
		$vendorId = $this->getRequest()->getParam('id', null);
		$assocProducts = Mage::getModel('vendor/product')
			->getCollection()
			->addFieldToFilter('vendor_id',$vendorId);
		$excludeIds = array();	
		foreach($assocProducts as $item){
			$excludeIds[] = $item->getProductId();
		}
		
        //$collection->addIdFilter($excludeIds, true);
		$collection->addFieldToFilter('entity_id', array('in'=>$excludeIds));

        Mage::getSingleton('catalog/product_status')->addSaleableFilterToCollection($collection);

        $this->setCollection($collection);
		return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {/*
		 $this->addColumn('in_vendor', array(
			'header_css_class' => 'a-center',
			'type'      => 'checkbox',
			'name'      => 'in_vendor',
			'values'    => $this->_getSelectedProducts(),
			'align'     => 'center',
			'index'     => 'in_vendor'
		));*/
			
        $this->addColumn('product_id', array(
            'header'    => Mage::helper('sales')->__('ID'),
            'sortable'  => true,
            'width'     => '60px',
            'index'     => 'entity_id'
        ));
        $this->addColumn('name', array(
            'header'    => Mage::helper('sales')->__('Product Name'),
            'index'     => 'name',
            'column_css_class'=> 'name'
        ));

        $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
            ->load()
            ->toOptionHash();

        $this->addColumn('set_name',
            array(
                'header'=> Mage::helper('catalog')->__('Attrib. Set Name'),
                'width' => '100px',
                'index' => 'attribute_set_id',
                'type'  => 'options',
                'options' => $sets,
        ));

        $this->addColumn('sku', array(
            'header'    => Mage::helper('sales')->__('SKU'),
            'width'     => '80px',
            'index'     => 'sku',
            'column_css_class'=> 'sku'
        ));
        $this->addColumn('price', array(
            'header'    => Mage::helper('sales')->__('Price'),
            'align'     => 'center',
            'type'      => 'currency',
            'currency_code' => $this->_getStore()->getCurrentCurrencyCode(),
            'rate'      => $this->_getStore()->getBaseCurrency()->getRate($this->_getStore()->getCurrentCurrencyCode()),
            'index'     => 'price'
        ));

        return parent::_prepareColumns();
    }
	/*
	protected function _prepareMassaction()
    {
        $this->setMassactionIdField('in_vendor');
        $this->getMassactionBlock()->setFormFieldName('delete_product');

        $this->getMassactionBlock()->addItem('add', array(
             'label'    => $this->__('Delete Products from Vendor'),
             'url'      => $this->getUrl('\*//*/massdel', array('_current'=>true)),
        ));
        return $this;
    }*/
	
	
    
    protected function _getStore()
    {
        return Mage::app()->getStore($this->getRequest()->getParam('store'));
    }
}