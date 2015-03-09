<?php
class Iksula_Internationalshipping_Block_Adminhtml_Internationalshipping_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("internationalshipping_form", array("legend"=>Mage::helper("internationalshipping")->__("Item information")));

				
						$fieldset->addField("ship_id", "hidden", array(
						"label" => Mage::helper("internationalshipping")->__("Id"),
						"name" => "ship_id",
						));
					
						$fieldset->addField("name", "text", array(
						"label" => Mage::helper("internationalshipping")->__("Name"),
						"name" => "name",
						));
					
						$fieldset->addField("email", "text", array(
						"label" => Mage::helper("internationalshipping")->__("Email"),
						"name" => "email",
						));
					
						$fieldset->addField("shipping_add", "textarea", array(
						"label" => Mage::helper("internationalshipping")->__("Shipping Address"),
						"name" => "shipping_add",
						));
					
						$fieldset->addField("products", "textarea", array(
						"label" => Mage::helper("internationalshipping")->__("Product List"),
						"name" => "products",
						));
					
						$fieldset->addField("pincode", "text", array(
						"label" => Mage::helper("internationalshipping")->__("Pincode"),
						"name" => "pincode",
						));
					
						$fieldset->addField("country", "text", array(
						"label" => Mage::helper("internationalshipping")->__("Country"),
						"name" => "country",
						));
									
						 $fieldset->addField('status', 'select', array(
						'label'     => Mage::helper('internationalshipping')->__('Status'),
						'values' => array(
                                '-1'=>'Please Select..',
                                '1' => array('value'=> '0','label' => 'No'),
                                '2' => array('value'=> '1','label' => 'Yes')
                           ),
						'name' => 'status',
						));

				if (Mage::getSingleton("adminhtml/session")->getInternationalshippingData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getInternationalshippingData());
					Mage::getSingleton("adminhtml/session")->setInternationalshippingData(null);
				} 
				elseif(Mage::registry("internationalshipping_data")) {
				    $form->setValues(Mage::registry("internationalshipping_data")->getData());
				}
				return parent::_prepareForm();
		}
}
