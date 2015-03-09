<?php
class Iksula_Storelocator_Block_Adminhtml_Country_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("storelocator_form", array("legend"=>Mage::helper("storelocator")->__("Item information")));

				
						$fieldset->addField("country_id", "text", array(
						"label" => Mage::helper("storelocator")->__("Id"),
						"name" => "country_id",
						));
					
						$fieldset->addField("country", "text", array(
						"label" => Mage::helper("storelocator")->__("Country"),
						"name" => "country",
						));
					

				if (Mage::getSingleton("adminhtml/session")->getCountryData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getCountryData());
					Mage::getSingleton("adminhtml/session")->setCountryData(null);
				} 
				elseif(Mage::registry("country_data")) {
				    $form->setValues(Mage::registry("country_data")->getData());
				}
				return parent::_prepareForm();
		}
}
