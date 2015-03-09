<?php

class Iksula_Paytmpromo_Model_Mysql4_Coupons_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	public function _construct(){
		$this->_init("paytmpromo/coupons");
	}
}
