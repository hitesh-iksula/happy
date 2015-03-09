<?php
class Iksula_Suggest_Block_Adminhtml_Suggest_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("suggest_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("suggest")->__("Vendor Information"));
		}
		protected function _beforeToHtml()
		{
				$this->addTab("form_section", array(
				"label" => Mage::helper("suggest")->__("Vendor Information"),
				"title" => Mage::helper("suggest")->__("Vendor Information"),
				"content" => $this->getLayout()->createBlock("suggest/adminhtml_suggest_edit_tab_form")->toHtml(),
				));
				return parent::_beforeToHtml();
		}

}
