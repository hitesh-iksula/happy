<?php

class Iksula_Paytmpromo_Model_Coupons extends Mage_Core_Model_Abstract
{
	public function _construct()
	{
		parent::_construct();
		$this->_init('paytmpromo/coupons');
	}
}
