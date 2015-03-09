<?php

class Iksula_Explore_Block_Adminhtml_Personality_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'explore';
        $this->_controller = 'adminhtml_personality';
        
        $this->_updateButton('save', 'label', Mage::helper('explore')->__('Save Personality'));
        $this->_updateButton('delete', 'label', Mage::helper('explore')->__('Delete Personality'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('explore_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'personality_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'personality_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('explore_data') && Mage::registry('explore_data')->getId() ) {
            return Mage::helper('explore')->__("Edit Personality '%s'", $this->htmlEscape(Mage::registry('explore_data')->getPersonalityName()));
        } else {
            return Mage::helper('explore')->__('Add Personality');
        }
    }
}