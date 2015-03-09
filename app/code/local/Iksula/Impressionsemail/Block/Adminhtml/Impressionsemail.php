<?php


class Iksula_Impressionsemail_Block_Adminhtml_Impressionsemail extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_impressionsemail";
	$this->_blockGroup = "impressionsemail";
	$this->_headerText = Mage::helper("impressionsemail")->__("Impressionsemail Manager");
	$this->_addButtonLabel = Mage::helper("impressionsemail")->__("Add New Item");
	parent::__construct();
	$this->_removeButton('add');
	}

}