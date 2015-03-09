<?php

class Iksula_Solidswatches_Model_Mysql4_Solidswatches_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('solidswatches/solidswatches');
    }
}