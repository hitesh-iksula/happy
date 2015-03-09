<?php

class Iksula_Backdrop_Helper_Data extends Mage_Core_Helper_Abstract
{

	public function getIsProduction() {

		$type = Mage::getStoreConfig('backdrop_section/backdrop_group/backdrop_field', Mage::app()->getStore());
		return $type;

	}

	public function isCodEnabled() {

		$codStatus = Mage::getStoreConfig('payment/cashondelivery/active', Mage::app()->getStore());
		return $codStatus;

	}

	public function isPayzippyEnabled() {

		$codStatus = Mage::getStoreConfig('payment/payzippy/active', Mage::app()->getStore());
		return $codStatus;

	}

	public function isPaytmEnabled() {

		$codStatus = Mage::getStoreConfig('payment/paytm_cc/active', Mage::app()->getStore());
		return $codStatus;

	}


}
