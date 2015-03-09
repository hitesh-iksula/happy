<?php
class Happilyunmarried_Valentines_Block_Adminhtml_Valentines_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("valentines_form", array("legend"=>Mage::helper("valentines")->__("Item information")));

				
						$fieldset->addField("name", "text", array(
						"label" => Mage::helper("valentines")->__("Name"),
						"name" => "name",
						));
					
						$fieldset->addField("email", "text", array(
						"label" => Mage::helper("valentines")->__("Email"),
						"name" => "email",
						));
					
						$fieldset->addField("message", "textarea", array(
						"label" => Mage::helper("valentines")->__("Message"),
						"name" => "message",
						));
					

				if (Mage::getSingleton("adminhtml/session")->getValentinesData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getValentinesData());
					Mage::getSingleton("adminhtml/session")->setValentinesData(null);
				} 
				elseif(Mage::registry("valentines_data")) {
				    $form->setValues(Mage::registry("valentines_data")->getData());
				}
				return parent::_prepareForm();
		}
}
