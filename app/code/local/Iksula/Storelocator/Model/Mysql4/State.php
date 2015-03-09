<?php
class Iksula_Storelocator_Model_Mysql4_State extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("storelocator/state", "state_id");
    }
}