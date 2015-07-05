<?php

class TG_Vendor_Model_Mysql4_Log extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the functionalarea_id refers to the key field in your database table.
        $this->_init('vendor/log', 'entity_id');
    }
}