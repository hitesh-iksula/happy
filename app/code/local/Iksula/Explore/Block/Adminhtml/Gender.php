<?php

class Iksula_Explore_Block_Adminhtml_Gender extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct() {
		
		$this->_controller = 'adminhtml_gender';
		$this->_blockGroup = 'explore';
		$this->_headerText = Mage::helper('explore')->__('Explore - Gender Manager');
		$this->_addButtonLabel = Mage::helper('explore')->__('Add A New Gender');
		parent::__construct();
	}
}