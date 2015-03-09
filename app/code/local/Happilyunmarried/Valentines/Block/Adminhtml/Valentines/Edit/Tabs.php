<?php
class Happilyunmarried_Valentines_Block_Adminhtml_Valentines_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("valentines_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("valentines")->__("Item Information"));
		}
		protected function _beforeToHtml()
		{
				$this->addTab("form_section", array(
				"label" => Mage::helper("valentines")->__("Item Information"),
				"title" => Mage::helper("valentines")->__("Item Information"),
				"content" => $this->getLayout()->createBlock("valentines/adminhtml_valentines_edit_tab_form")->toHtml(),
				));
				return parent::_beforeToHtml();
		}

}
