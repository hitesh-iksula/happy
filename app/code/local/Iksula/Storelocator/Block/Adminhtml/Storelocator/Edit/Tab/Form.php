<?php
class Iksula_Storelocator_Block_Adminhtml_Storelocator_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("storelocator_form", array("legend"=>Mage::helper("storelocator")->__("Item information")));

								
						$fieldset->addField('type', 'select', array(
						'label'     => Mage::helper('storelocator')->__('Type'),
						'values'   => Iksula_Storelocator_Block_Adminhtml_Storelocator_Grid::getTypeValues(),
						'name' => 'type',
						));
						$fieldset->addField('name', 'text', array(
						'label'     => Mage::helper('storelocator')->__('Name'),
						'name' => 'name',
						));				
						 $fieldset->addField('country', 'select', array(
						'label'     => Mage::helper('storelocator')->__('Country'),
						'values'   => $this->getCountryOptions(),
						'name' => 'country',
						));				
						 $fieldset->addField('state', 'select', array(
						'label'     => Mage::helper('storelocator')->__('State'),
						'values'   => $this->getStateOptions(),
						'name' => 'state',
						));				
						 $fieldset->addField('city', 'select', array(
						'label'     => Mage::helper('storelocator')->__('City'),
						'values'   => $this->getCityOptions(),
						'name' => 'city',
						));
						$fieldset->addField("address", "editor", array(
						"label" => Mage::helper("storelocator")->__("Address"),
						"name" => "address",
						"config"    => Mage::getSingleton('cms/wysiwyg_config')->getConfig(),
						"wysiwyg"   => true,
						));
					
						$fieldset->addField("contact", "text", array(
						"label" => Mage::helper("storelocator")->__("Contact Number"),
						"name" => "contact",
						'after_element_html' => 'Multiple Numbers Separate by comma',
						));
					
						$fieldset->addField("person", "text", array(
						"label" => Mage::helper("storelocator")->__("Contact Person"),
						"name" => "person",
						));
									
						 $fieldset->addField('enabled', 'select', array(
						'label'     => Mage::helper('storelocator')->__('Enabled'),
						'values'   =>  array(
                                '-1'=>'Please Select..',
                                '1' => array('value'=> '0','label' => 'No'),
                                '2' => array('value'=> '1','label' => 'Yes')
                           ),
						'name' => 'enabled',
						));

				if (Mage::getSingleton("adminhtml/session")->getStorelocatorData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getStorelocatorData());
					Mage::getSingleton("adminhtml/session")->setStorelocatorData(null);
				} 
				elseif(Mage::registry("storelocator_data")) {
				    $form->setValues(Mage::registry("storelocator_data")->getData());
				}
				return parent::_prepareForm();
		}

		protected function getCountryOptions(){
			$countries = Mage::getModel('storelocator/country')->getCollection();
			$optionArray = array('-1'=>'Please Select..');
			foreach ($countries as $country) {
				$id = $country->getCountryId();
				$name = $country->getCountry();
				array_push($optionArray , array('value'=> $id,'label' => $name));
			}

			return $optionArray;
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
		
		protected function getCityOptions(){
			$cities = Mage::getModel('storelocator/city')->getCollection();
			$optionArray = array('-1'=>'Please Select..');
			foreach ($cities as $city) {
				$id = $city->getCityId();
				$name = $city->getCity();
				array_push($optionArray , array('value'=> $id,'label' => $name));
			}
			return $optionArray;
		}
}
