<?php
	
class Iksula_Internationalshipping_Block_Adminhtml_Internationalshipping_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "ship_id";
				$this->_blockGroup = "internationalshipping";
				$this->_controller = "adminhtml_internationalshipping";
				$this->_updateButton("save", "label", Mage::helper("internationalshipping")->__("Save Item"));
				$this->_updateButton("delete", "label", Mage::helper("internationalshipping")->__("Delete Item"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("internationalshipping")->__("Save And Continue Edit"),
					"onclick"   => "saveAndContinueEdit()",
					"class"     => "save",
				), -100);



				$this->_formScripts[] = "

							function saveAndContinueEdit(){
								editForm.submit($('edit_form').action+'back/edit/');
							}
						";
		}

		public function getHeaderText()
		{
				if( Mage::registry("internationalshipping_data") && Mage::registry("internationalshipping_data")->getId() ){

				    return Mage::helper("internationalshipping")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("internationalshipping_data")->getId()));

				} 
				else{

				     return Mage::helper("internationalshipping")->__("Add Item");

				}
		}
}