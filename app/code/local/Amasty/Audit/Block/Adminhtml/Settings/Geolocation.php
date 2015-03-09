<?php
/**
 * @author Amasty Team
 * @copyright Amasty
 * @package Amasty_Amaudit
 */

class Amasty_Audit_Block_Adminhtml_Settings_Geolocation extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $element->setDisabled(!Mage::getModel('amaudit/import')->isDone());

        return parent::_getElementHtml($element);
    }
}
