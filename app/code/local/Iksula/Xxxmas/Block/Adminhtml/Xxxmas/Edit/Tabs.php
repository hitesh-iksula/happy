<?php
class Iksula_Xxxmas_Block_Adminhtml_Xxxmas_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("xxxmas_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("xxxmas")->__("Item Information"));
		}
		protected function _beforeToHtml()
		{
				$this->addTab("form_section", array(
				"label" => Mage::helper("xxxmas")->__("Item Information"),
				"title" => Mage::helper("xxxmas")->__("Item Information"),
				"content" => $this->getLayout()->createBlock("xxxmas/adminhtml_xxxmas_edit_tab_form")->toHtml(),
				));
				return parent::_beforeToHtml();
		}

}
