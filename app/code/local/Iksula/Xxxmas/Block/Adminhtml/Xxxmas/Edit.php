<?php
	
class Iksula_Xxxmas_Block_Adminhtml_Xxxmas_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "xxxmas_id";
				$this->_blockGroup = "xxxmas";
				$this->_controller = "adminhtml_xxxmas";
				$this->_updateButton("save", "label", Mage::helper("xxxmas")->__("Save Item"));
				$this->_updateButton("delete", "label", Mage::helper("xxxmas")->__("Delete Item"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("xxxmas")->__("Save And Continue Edit"),
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
				if( Mage::registry("xxxmas_data") && Mage::registry("xxxmas_data")->getId() ){

				    return Mage::helper("xxxmas")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("xxxmas_data")->getId()));

				} 
				else{

				     return Mage::helper("xxxmas")->__("Add Item");

				}
		}
}