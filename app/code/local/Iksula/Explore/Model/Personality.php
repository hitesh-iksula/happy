<?php

class Iksula_Explore_Model_Personality extends Mage_Core_Model_Abstract
{
	public function _construct()
	{
		parent::_construct();
		$this->_init('explore/personality');
	}
}