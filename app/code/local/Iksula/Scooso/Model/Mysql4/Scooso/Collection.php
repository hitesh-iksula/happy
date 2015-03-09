<?php

class Iksula_Scooso_Model_Mysql4_Scooso_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('scooso/scooso');
    }
}