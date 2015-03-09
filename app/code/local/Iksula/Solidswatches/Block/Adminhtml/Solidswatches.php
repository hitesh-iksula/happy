<?php

class Iksula_Solidswatches_Block_Adminhtml_Solidswatches extends Mage_Adminhtml_Block_Template {

	public function __construct() {

		$this->_headerText = Mage::helper("solidswatches")->__("Manage Solidswatches");
		parent::__construct();

	}

}