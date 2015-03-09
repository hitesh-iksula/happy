<?php
	
class Happilyunmarried_Valentines_Block_Adminhtml_Valentines_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "valentines_id";
				$this->_blockGroup = "valentines";
				$this->_controller = "adminhtml_valentines";
				$this->_updateButton("save", "label", Mage::helper("valentines")->__("Save Item"));
				$this->_updateButton("delete", "label", Mage::helper("valentines")->__("Delete Item"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("valentines")->__("Save And Continue Edit"),
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
				if( Mage::registry("valentines_data") && Mage::registry("valentines_data")->getId() ){

				    return Mage::helper("valentines")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("valentines_data")->getId()));

				} 
				else{

				     return Mage::helper("valentines")->__("Add Item");

				}
		}
}
