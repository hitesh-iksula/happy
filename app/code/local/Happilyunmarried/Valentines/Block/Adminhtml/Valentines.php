<?php


class Happilyunmarried_Valentines_Block_Adminhtml_Valentines extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_valentines";
	$this->_blockGroup = "valentines";
	$this->_headerText = Mage::helper("valentines")->__("Valentines Manager");
	$this->_addButtonLabel = Mage::helper("valentines")->__("Add New Item");
	parent::__construct();
	
	}

}
