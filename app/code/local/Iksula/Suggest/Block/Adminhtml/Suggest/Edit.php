<?php
	
class Iksula_Suggest_Block_Adminhtml_Suggest_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "suggest_id";
				$this->_blockGroup = "suggest";
				$this->_controller = "adminhtml_suggest";
				$this->_updateButton("save", "label", Mage::helper("suggest")->__("Save Vendor"));
				$this->_updateButton("delete", "label", Mage::helper("suggest")->__("Delete Vendor"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("suggest")->__("Save And Continue Edit"),
					"onclick"   => "saveAndContinueEdit()",
					"class"     => "save",
				), -100);

				$createNew = Mage::getModel('adminhtml/url')->getUrl('adminhtml/customer/new');

				$this->_addButton("createnew", array(
					"label"     => Mage::helper("suggest")->__("Create Vendor Profile"),
					"onclick"   => "setLocation('$createNew')",
					"class"     => "new",
				), -100);

				$this->_formScripts[] = "

							function saveAndContinueEdit(){
								editForm.submit($('edit_form').action+'back/edit/');
							}
						";
		}

		public function getHeaderText()
		{
				if( Mage::registry("suggest_data") && Mage::registry("suggest_data")->getId() ){

				    return Mage::helper("suggest")->__("Edit Vendor '%s'", $this->htmlEscape(Mage::registry("suggest_data")->getName()));

				} 
				else{

				     return Mage::helper("suggest")->__("Add Vendor");

				}
		}
}