<?php

class Iksula_Scooso_Model_Scooso extends Mage_Core_Model_Abstract
{
	public function _construct()
	{
		parent::_construct();
		$this->_init('scooso/scooso');
	}
}