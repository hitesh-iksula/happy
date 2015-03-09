<?php

class Iksula_Explore_Model_Mysql4_Gender extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the gender_id refers to the key field in your database table.
        $this->_init('explore/gender', 'gender_id');
    }
}