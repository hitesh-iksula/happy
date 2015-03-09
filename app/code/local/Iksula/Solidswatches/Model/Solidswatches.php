<?php

class Iksula_Solidswatches_Model_Solidswatches extends Mage_Core_Model_Abstract
{
	public function _construct()
	{
		parent::_construct();
		$this->_init('solidswatches/solidswatches');
	}
}