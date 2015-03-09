<?php
class Iksula_Hupopup_Block_Adminhtml_Hupopup_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("hupopup_form", array("legend"=>Mage::helper("hupopup")->__("Item information")));

				
						$fieldset->addField("username", "text", array(
						"label" => Mage::helper("hupopup")->__("Name"),					
						"class" => "required-entry",
						"required" => true,
						"name" => "username",
						));
					
						$fieldset->addField("user_email_id", "text", array(
						"label" => Mage::helper("hupopup")->__("Email ID"),					
						"class" => "required-entry",
						"required" => true,
						"name" => "user_email_id",
						));
					
						$fieldset->addField("friend_email_id", "text", array(
						"label" => Mage::helper("hupopup")->__("Friend Email ID"),					
						"class" => "required-entry",
						"required" => true,
						"name" => "friend_email_id",
						));
									
						 $fieldset->addField('verify_link', 'select', array(
						'label'     => Mage::helper('hupopup')->__('Link Verified ?'),
						'values'   => Iksula_Hupopup_Block_Adminhtml_Hupopup_Grid::getValueArray6(),
						'name' => 'verify_link',					
						"class" => "required-entry",
						"required" => true,
						));
						$dateFormatIso = Mage::app()->getLocale()->getDateTimeFormat(
							Mage_Core_Model_Locale::FORMAT_TYPE_SHORT
						);

						$fieldset->addField('created_date', 'date', array(
						'label'        => Mage::helper('hupopup')->__('Date'),
						'name'         => 'created_date',					
						"class" => "required-entry",
						"required" => true,
						'time' => true,
						'image'        => $this->getSkinUrl('images/grid-cal.gif'),
						'format'       => $dateFormatIso
						));

				if (Mage::getSingleton("adminhtml/session")->getHupopupData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getHupopupData());
					Mage::getSingleton("adminhtml/session")->setHupopupData(null);
				} 
				elseif(Mage::registry("hupopup_data")) {
				    $form->setValues(Mage::registry("hupopup_data")->getData());
				}
				return parent::_prepareForm();
		}
}
