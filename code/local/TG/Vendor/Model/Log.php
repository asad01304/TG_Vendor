<?php

class TG_Vendor_Model_Log extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('vendor/log');
    }
}



