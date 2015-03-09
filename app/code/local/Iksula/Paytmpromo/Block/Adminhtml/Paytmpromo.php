<?php


class Iksula_Paytmpromo_Block_Adminhtml_Paytmpromo extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_paytmpromo";
	$this->_blockGroup = "paytmpromo";
	$this->_headerText = Mage::helper("paytmpromo")->__("Paytmpromo Manager");
	$this->_addButtonLabel = Mage::helper("paytmpromo")->__("Add New Item");
	parent::__construct();
	$this->_removeButton('add');
	}

}