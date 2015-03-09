<?php

class Iksula_Explore_Block_Adminhtml_Gender_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{		
		$form = new Varien_Data_Form();
		
		$this->setForm($form);
		$fieldset = $form->addFieldSet('explore_form', array('legend'=>Mage::helper('explore')->__('Gender Information')));
		
		$fieldset->addField('gender_name', 'text', array(
			'label' => Mage::helper('explore')->__('Gender'),
			'required' => true,
			'name' => 'gender_name',
		));
		
		$fieldset->addField('gender_logo_1', 'image', array(
			'label' => Mage::helper('explore')->__('Logo (Active)'),
			'required' => false,
			'name' => 'gender_logo_1',
		));
		
		$fieldset->addField('gender_logo_2', 'image', array(
			'label' => Mage::helper('explore')->__('Logo (Inactive)'),
			'required' => false,
			'name' => 'gender_logo_2',
		));
		
		$fieldset->addField('status', 'select', array(
			'label' => Mage::helper('explore')->__('Status'),
			'required' => true,
			'name' => 'status',
			'values' => array (
				array (
					'value' => 1,
					'label' => Mage::helper('explore')->__('Enabled'),
				),
				
				array (
					'value' => 2,
					'label' => Mage::helper('explore')->__('Disabled'),
				),
			),
		));
		
		if ( Mage::getSingleton('adminhtml/session')->getExploreData() )
		{
			$data = Mage::getSingleton('adminhtml/session')->getExploreData();
			Mage::getSingleton('adminhtml/session')->setExploreData(null);
		}
		elseif ( Mage::registry('explore_data') )
		{
			$data = Mage::registry('explore_data')->getData();
		}
		
		$form->setValues($data);
		
		return parent::_prepareForm();
	}
}