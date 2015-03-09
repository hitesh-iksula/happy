<?php


class Iksula_Storelocator_Block_Adminhtml_Country extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_country";
	$this->_blockGroup = "storelocator";
	$this->_headerText = Mage::helper("storelocator")->__("Country Manager");
	$this->_addButtonLabel = Mage::helper("storelocator")->__("Add New Item");
	parent::__construct();
	
	}

}