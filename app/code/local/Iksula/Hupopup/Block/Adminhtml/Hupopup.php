<?php


class Iksula_Hupopup_Block_Adminhtml_Hupopup extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_hupopup";
	$this->_blockGroup = "hupopup";
	$this->_headerText = Mage::helper("hupopup")->__("Hupopup Manager");
	$this->_addButtonLabel = Mage::helper("hupopup")->__("Add New Item");
	parent::__construct();
	
	}
}