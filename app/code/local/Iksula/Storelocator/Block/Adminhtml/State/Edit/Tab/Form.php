<?php
class Iksula_Storelocator_Block_Adminhtml_State_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("storelocator_form", array("legend"=>Mage::helper("storelocator")->__("Item information")));

				
						/* $fieldset->addField("state_id", "text", array(
						"label" => Mage::helper("storelocator")->__("Id"),
						"name" => "state_id",
						)); */
					
						$fieldset->addField("state", "text", array(
						"label" => Mage::helper("storelocator")->__("State"),
						"name" => "state",
						));
									
						$fieldset->addField('country_id', 'select', array(
						'label'     => Mage::helper('storelocator')->__('Country'),
						'values'   => $this->getCountryOptions(),
						'name' => 'country_id',
						));

				if (Mage::getSingleton("adminhtml/session")->getStateData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getStateData());
					Mage::getSingleton("adminhtml/session")->setStateData(null);
				} 
				elseif(Mage::registry("state_data")) {
				    $form->setValues(Mage::registry("state_data")->getData());
				}
				return parent::_prepareForm();
		}

		public function getCountryOptions(){
			$countries = Mage::getModel('storelocator/country')->getCollection();
			$optionArray = array('-1'=>'Please Select..');
			foreach ($countries as $country) {
				$id = $country->getCountryId();
				$name = $country->getCountry();
				array_push($optionArray , array('value'=> $id,'label' => $name));
			}

			return $optionArray;
		}


}
