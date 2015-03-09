<?php
	
class Iksula_Hupopup_Block_Adminhtml_Hupopup_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "hu_popup_id";
				$this->_blockGroup = "hupopup";
				$this->_controller = "adminhtml_hupopup";
				$this->_updateButton("save", "label", Mage::helper("hupopup")->__("Save Item"));
				$this->_updateButton("delete", "label", Mage::helper("hupopup")->__("Delete Item"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("hupopup")->__("Save And Continue Edit"),
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
				if( Mage::registry("hupopup_data") && Mage::registry("hupopup_data")->getId() ){

				    return Mage::helper("hupopup")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("hupopup_data")->getId()));

				} 
				else{

				     return Mage::helper("hupopup")->__("Add Item");

				}
		}
}