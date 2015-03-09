<?php

class Iksula_Explore_Block_Adminhtml_Personality extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct() {
		
		$this->_controller = 'adminhtml_personality';
		$this->_blockGroup = 'explore';
		$this->_headerText = Mage::helper('explore')->__('Explore - Personality Manager');
		$this->_addButtonLabel = Mage::helper('explore')->__('Add A New Personality');
		parent::__construct();
	}
}