<?php

class Iksula_Suggest_Block_Adminhtml_Suggest extends Mage_Adminhtml_Block_Widget_Grid_Container {

	public function __construct()
	{

	$this->_controller = "adminhtml_suggest";
	$this->_blockGroup = "suggest";
	$this->_headerText = Mage::helper("suggest")->__("Manage Vendors");
	$this->_addButtonLabel = Mage::helper("suggest")->__("Add A New Vendor");
	parent::__construct();

	}
}