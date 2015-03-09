<?php
class Iksula_Storelocator_Block_Adminhtml_Country_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("country_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("storelocator")->__("Item Information"));
		}
		protected function _beforeToHtml()
		{
				$this->addTab("form_section", array(
				"label" => Mage::helper("storelocator")->__("Item Information"),
				"title" => Mage::helper("storelocator")->__("Item Information"),
				"content" => $this->getLayout()->createBlock("storelocator/adminhtml_country_edit_tab_form")->toHtml(),
				));
				return parent::_beforeToHtml();
		}

}
