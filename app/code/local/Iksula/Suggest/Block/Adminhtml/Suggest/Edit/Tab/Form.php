<?php
class Iksula_Suggest_Block_Adminhtml_Suggest_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{
				$time = time();
				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("suggest_form", array("legend"=>Mage::helper("suggest")->__("Vendor Information")));
				
				$fieldset->addField("name", "text", array(
				"label" => Mage::helper("suggest")->__("Name"),
				"class" => "required-entry",
				"required" => true,
				"name" => "name",
				));
				
				$fieldset->addField("email_id", "text", array(
				"label" => Mage::helper("suggest")->__("Email Id"),
				"class" => "required-entry",
				"required" => true,
				"name" => "email_id",
				));

				$fieldset->addField("mob_no", "text", array(
				"label" => Mage::helper("suggest")->__("Mobile-No"),
				"class" => "required-entry",
				"required" => true,
				"name" => "mob_no",
				));
				
				// $fieldset->addField("date_registered", "text", array(
				// "label" => Mage::helper("suggest")->__("Registration Date"),
				// "class" => "required-entry",
				// "required" => true,
				// "name" => "date_registered",
				// "value" => $time,
				// ));
				$fieldset->addField('verified', 'select', array(
					'label' => Mage::helper('suggest')->__('Have You Verified The Vendor?'),
					'required' => false,
					'name' => 'verified',
					'values' => array (
						array (
							'value' => 0,
							'label' => Mage::helper('suggest')->__('No'),
						),
						
						array (
							'value' => 1,
							'label' => Mage::helper('suggest')->__('Yes'),
						),
					),
				));
				$fieldset->addField('created_account', 'select', array(
					'label' => Mage::helper('suggest')->__('Have You Created The Vendor\'s Account?'),
					'required' => false,
					'name' => 'created_account',
					'values' => array (
						array (
							'value' => 0,
							'label' => Mage::helper('suggest')->__('No'),
						),
						
						array (
							'value' => 1,
							'label' => Mage::helper('suggest')->__('Yes'),
						),
					),
				));


				if (Mage::getSingleton("adminhtml/session")->getSuggestData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getSuggestData());
					Mage::getSingleton("adminhtml/session")->setSuggestData(null);
				} 
				elseif(Mage::registry("suggest_data")) {
				    $form->setValues(Mage::registry("suggest_data")->getData());
				}
				return parent::_prepareForm();
		}
}
