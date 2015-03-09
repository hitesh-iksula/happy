<?php
class Manoj_Pincode_Block_Adminhtml_Pincode_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("pincode_form", array("legend"=>Mage::helper("pincode")->__("Item information")));
						$fieldset->addField("pincode", "text", array(
						"label" => Mage::helper("pincode")->__("Pincode "),
						"name" => "pincode",
						));
					
						$fieldset->addField("area", "text", array(
						"label" => Mage::helper("pincode")->__("Area "),
						"name" => "area",
						));
					
						$fieldset->addField("location", "text", array(
						"label" => Mage::helper("pincode")->__("Location"),
						"name" => "location",
						));
					
						$fieldset->addField("city", "text", array(
						"label" => Mage::helper("pincode")->__("City"),
						"name" => "city",
						));
					
						$fieldset->addField("state", "text", array(
						"label" => Mage::helper("pincode")->__("State"),
						"name" => "state",
						));
					
						$fieldset->addField("code", "text", array(
						"label" => Mage::helper("pincode")->__("Code"),
						"name" => "code",
						));
					
						$fieldset->addField("zone", "text", array(
						"label" => Mage::helper("pincode")->__("Zone"),
						"name" => "zone",
						));
					
						$fieldset->addField("cod", "multiselect", array(
						"label" => Mage::helper("pincode")->__("Cod"),
						'values'   => Manoj_Pincode_Block_Adminhtml_Pincode_Grid::getCarrierValueArray20(),
						"name" => "cod",
						));
					
						$fieldset->addField("prepaid", "multiselect", array(
						"label" => Mage::helper("pincode")->__("Prepaid"),
						'values'   => Manoj_Pincode_Block_Adminhtml_Pincode_Grid::getCarrierValueArray20(),
						"name" => "prepaid",
						));
									
						 $fieldset->addField('status', 'select', array(
						'label'     => Mage::helper('pincode')->__('Status'),
						'values'   => Manoj_Pincode_Block_Adminhtml_Pincode_Grid::getValueArray10(),
						'name' => 'status',
						));				
						$fieldset->addField('fileupload', 'image', array(
						'label' => Mage::helper('pincode')->__('Fileupload'),
						'name' => 'fileupload',
						'note' => '(*.jpg, *.png, *.gif)',
						));
					

				if (Mage::getSingleton("adminhtml/session")->getPincodeData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getPincodeData());
					Mage::getSingleton("adminhtml/session")->setPincodeData(null);
				} 
				elseif(Mage::registry("pincode_data")) {
				    $form->setValues(Mage::registry("pincode_data")->getData());
				}
				return parent::_prepareForm();
		}
}
