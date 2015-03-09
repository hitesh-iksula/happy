<?php
class Manoj_Pincode_Block_Adminhtml_Pincode_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("pincode_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("pincode")->__("Item Information"));
		}
		protected function _beforeToHtml()
		{
				$this->addTab("form_section", array(
				"label" => Mage::helper("pincode")->__("Item Information"),
				"title" => Mage::helper("pincode")->__("Item Information"),
				"content" => $this->getLayout()->createBlock("pincode/adminhtml_pincode_edit_tab_form")->toHtml(),
				));
				return parent::_beforeToHtml();
		}

}
