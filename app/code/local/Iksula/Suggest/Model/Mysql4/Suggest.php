<?php
    class Iksula_Suggest_Model_Mysql4_Suggest extends Mage_Core_Model_Mysql4_Abstract
    {
        protected function _construct()
        {
            $this->_init("suggest/suggest", "suggest_id");
        }
    }
	 