<?php
class Happilyunmarried_Valentines_Model_Mysql4_Valentines extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("valentines/valentines", "valentines_id");
    }
}
