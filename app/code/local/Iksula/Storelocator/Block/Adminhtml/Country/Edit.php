<?php
	
class Iksula_Storelocator_Block_Adminhtml_Country_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "country_id";
				$this->_blockGroup = "storelocator";
				$this->_controller = "adminhtml_country";
				$this->_updateButton("save", "label", Mage::helper("storelocator")->__("Save Item"));
				$this->_updateButton("delete", "label", Mage::helper("storelocator")->__("Delete Item"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("storelocator")->__("Save And Continue Edit"),
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
				if( Mage::registry("country_data") && Mage::registry("country_data")->getId() ){

				    return Mage::helper("storelocator")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("country_data")->getId()));

				} 
				else{

				     return Mage::helper("storelocator")->__("Add Item");

				}
		}
}