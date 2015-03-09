<?php

class Iksula_Explore_Model_Mysql4_Gender_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('explore/gender');
    }
}