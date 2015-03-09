<?php
class Iksula_Paytmpromo_Model_Mysql4_Coupons extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("paytmpromo/coupons", "id");
    }
}
