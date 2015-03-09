<?php

class Iksula_Explore_Block_Adminhtml_Occasion extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct() {
		
		$this->_controller = 'adminhtml_occasion';
		$this->_blockGroup = 'explore';
		$this->_headerText = Mage::helper('explore')->__('Explore - Occasion Manager');
		$this->_addButtonLabel = Mage::helper('explore')->__('Add A New Occasion');
		parent::__construct();
	}
}