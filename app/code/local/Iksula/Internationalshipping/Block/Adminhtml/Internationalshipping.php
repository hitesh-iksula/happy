<?php


class Iksula_Internationalshipping_Block_Adminhtml_Internationalshipping extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_internationalshipping";
	$this->_blockGroup = "internationalshipping";
	$this->_headerText = Mage::helper("internationalshipping")->__("Internationalshipping Manager");
	$this->_addButtonLabel = Mage::helper("internationalshipping")->__("Add New Item");
	parent::__construct();
	
	}

}