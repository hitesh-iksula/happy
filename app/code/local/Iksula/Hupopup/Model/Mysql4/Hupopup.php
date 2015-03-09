<?php
class Iksula_Hupopup_Model_Mysql4_Hupopup extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("hupopup/hupopup", "hu_popup_id");
    }
}