<?php
/**
 * @author Amasty Team
 * @copyright Amasty
 * @package Amasty_Amaudit
 */

class Amasty_Audit_Block_Adminhtml_Settings_Import extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);

        $onclick = 'var inputCaller = this;';

        $importTypes = array(
            'location',
            'block'
        );

        foreach($importTypes as $type)
        {
            $startUrl = $this->getUrl('amaudit/adminhtml_import/start', array(
                'type' => $type
            ));

            $processUrl = $this->getUrl('amaudit/adminhtml_import/process', array(
                'type' => $type
            ));

            $commitUrl = $this->getUrl('amaudit/adminhtml_import/commit', array(
                'type' => $type
            ));

            $onclick .= 'window.setTimeout(function(){ amImportObj.run(\''.$startUrl.'\', \''.$processUrl.'\', \''.$commitUrl.'\', inputCaller);}, 100); ';
        }

        $import = Mage::getSingleton('amaudit/import');
        $importAvailable = $import->filesAvailable();

        $element->setComment($importAvailable ? '' : $this->__('Required files:').' '.implode(', ', $import->getRequiredFiles()));

        $db = Mage::getSingleton('core/resource')->getConnection('core_read');
        $locationTableExist = $db->isTableExists($locationTableName = Mage::getSingleton('core/resource')->getTableName('amaudit/location'));
        $blockTableExist = $db->isTableExists($blockTableName = Mage::getSingleton('core/resource')->getTableName('amaudit/block'));

        $comment = $element->getComment();

        if (Mage::getModel('amaudit/import')->isDone()) {
            $element->setComment($comment.'GeoIP data is imported and in use.');
        } else {
            $element->setComment($comment.'GeoIP data was not imported yet.');
        }

        $html = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setType('button')
            ->setClass('scalable')
            ->setLabel($this->__('Import'))
            ->setOnClick($onclick)
            ->setDisabled(!$importAvailable)
            ->toHtml();

        return $html;
    }
}
