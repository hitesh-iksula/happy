<?php


class Iksula_Storelocator_Block_Adminhtml_City extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_city";
	$this->_blockGroup = "storelocator";
	$this->_headerText = Mage::helper("storelocator")->__("City Manager");
	$this->_addButtonLabel = Mage::helper("storelocator")->__("Add New Item");
	parent::__construct();
	
	}

}