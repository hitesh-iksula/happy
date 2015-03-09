<?php
class Manoj_Pincode_Model_Mysql4_Pincode extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("pincode/pincode", "id");
    }
}