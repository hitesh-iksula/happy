<?php

class Iksula_Explore_Block_Adminhtml_Gender_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('gender_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('explore')->__('Tab'));
	}
	
	protected function _beforeToHtml()
	{
		$this->addTab('form_section', array(
			'label' =>	Mage::helper('explore')->__('Gender Information'),
			'content' => $this->getLayout()->createBlock('explore/adminhtml_gender_edit_tab_form')->toHtml(),
		));
		
		return parent::_beforeToHtml();
	}
}