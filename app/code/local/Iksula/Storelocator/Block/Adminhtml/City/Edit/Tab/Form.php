<?php
class Iksula_Storelocator_Block_Adminhtml_City_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("storelocator_form", array("legend"=>Mage::helper("storelocator")->__("Item information")));

				
						/* $fieldset->addField("city_id", "text", array(
						"label" => Mage::helper("storelocator")->__("Id"),
						"name" => "city_id",
						)); */
					
						$fieldset->addField("city", "text", array(
						"label" => Mage::helper("storelocator")->__("City"),
						"name" => "city",
						));
									
						$fieldset->addField('state_id', 'select', array(
						'label'     => Mage::helper('storelocator')->__('State'),
						'values'   => $this->getStateOptions(),
						'name' => 'state_id',
						));

				if (Mage::getSingleton("adminhtml/session")->getCityData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getCityData());
					Mage::getSingleton("adminhtml/session")->setCityData(null);
				} 
				elseif(Mage::registry("city_data")) {
				    $form->setValues(Mage::registry("city_data")->getData());
				}
				return parent::_prepareForm();
		}

		protected function getStateOptions(){
			$states = Mage::getModel('storelocator/state')->getCollection();
			$optionArray = array('-1'=>'Please Select..');
			foreach ($states as $state) {
				$id = $state->getStateId();
				$name = $state->getState();
				array_push($optionArray , array('value'=> $id,'label' => $name));
			}
			return $optionArray;
		}
}
