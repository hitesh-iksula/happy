<?php
class Iksula_Hupopup_Block_Adminhtml_Hupopup_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("hupopup_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("hupopup")->__("Item Information"));
		}
		protected function _beforeToHtml()
		{
				$this->addTab("form_section", array(
				"label" => Mage::helper("hupopup")->__("Item Information"),
				"title" => Mage::helper("hupopup")->__("Item Information"),
				"content" => $this->getLayout()->createBlock("hupopup/adminhtml_hupopup_edit_tab_form")->toHtml(),
				));
				return parent::_beforeToHtml();
		}

}
