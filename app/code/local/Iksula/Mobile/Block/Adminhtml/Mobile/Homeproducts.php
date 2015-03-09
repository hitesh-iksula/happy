<?php

class Iksula_Mobile_Block_Adminhtml_Mobile_Homeproducts extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    /*
     * Set template
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('mobile/cache.phtml');
    }

    /**
     * Return element html
     *
     * @param  Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->_toHtml();
    }

    /**
     * Return ajax url for button
     *
     * @return string
     */
    public function getAjaxCheckUrl()
    {
        return Mage::helper('adminhtml')->getUrl('mobile/adminhtml_cache/flush');
    }

    /**
     * Generate button html
     *
     * @return string
     */
    public function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
            'id'        => 'cache_button',
            'label'     => $this->helper('adminhtml')->__('Flush Cache'),
            'onclick'   => 'javascript:flushMobileHomepageProducts(); return false;'
        ));

        return $button->toHtml();
    }

}
