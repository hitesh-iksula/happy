<?php

class Iksula_Scooso_Model_Mysql4_Scooso extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the gender_id refers to the key field in your database table.
        $this->_init('scooso/scooso', 'id');
    }
}