<?php
class Iksula_Xxxmas_Block_Adminhtml_Xxxmas_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("xxxmas_form", array("legend"=>Mage::helper("xxxmas")->__("Item information")));

				
						$fieldset->addField("name", "text", array(
						"label" => Mage::helper("xxxmas")->__("Name"),
						"name" => "name",
						));
					
						$fieldset->addField("email", "text", array(
						"label" => Mage::helper("xxxmas")->__("Email"),
						"name" => "email",
						));
					
						$fieldset->addField("message", "textarea", array(
						"label" => Mage::helper("xxxmas")->__("Message"),
						"name" => "message",
						));
					

				if (Mage::getSingleton("adminhtml/session")->getXxxmasData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getXxxmasData());
					Mage::getSingleton("adminhtml/session")->setXxxmasData(null);
				} 
				elseif(Mage::registry("xxxmas_data")) {
				    $form->setValues(Mage::registry("xxxmas_data")->getData());
				}
				return parent::_prepareForm();
		}
}
