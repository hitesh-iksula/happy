<?php


class Iksula_Xxxmas_Block_Adminhtml_Xxxmas extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_xxxmas";
	$this->_blockGroup = "xxxmas";
	$this->_headerText = Mage::helper("xxxmas")->__("Xxxmas Manager");
	$this->_addButtonLabel = Mage::helper("xxxmas")->__("Add New Item");
	parent::__construct();
	
	}

}