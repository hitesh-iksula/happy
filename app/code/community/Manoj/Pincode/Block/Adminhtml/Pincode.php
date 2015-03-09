<?php


class Manoj_Pincode_Block_Adminhtml_Pincode extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_pincode";
	$this->_blockGroup = "pincode";
	$this->_headerText = Mage::helper("pincode")->__("Pincode Manager");
	$this->_addButtonLabel = Mage::helper("pincode")->__("Add New Item");
	parent::__construct();
	
	}

}