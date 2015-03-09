<?php

class Iksula_Explore_Block_Adminhtml_Occasion_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'explore';
        $this->_controller = 'adminhtml_occasion';
        
        $this->_updateButton('save', 'label', Mage::helper('explore')->__('Save Occasion'));
        $this->_updateButton('delete', 'label', Mage::helper('explore')->__('Delete Occasion'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('explore_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'occasion_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'occasion_content');
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
            return Mage::helper('explore')->__("Edit Occasion '%s'", $this->htmlEscape(Mage::registry('explore_data')->getOccasionName()));
        } else {
            return Mage::helper('explore')->__('Add Occasion');
        }
    }
}