<?php
class Junaidbhura_Ccavenue_Block_Info_Options extends Mage_Payment_Block_Info
{
    // protected function _construct() // sample code, not used in current module.
    // {
    //     parent::_construct();
    //     $this->setTemplate('vendorcheckout/info/vendorcheckoutinfo.phtml'); 
    // }

    protected function _prepareSpecificInformation($transport = null)
    {
        if (null !== $this->_paymentSpecificInformation) {
            return $this->_paymentSpecificInformation;
        }
        $info = $this->getInfo();
        //echo "<pre>";print_r($info->getData());echo "</pre>";
        $transport = new Varien_Object();
        $transport = parent::_prepareSpecificInformation($transport);
        $transport->addData(array(
            //Mage::helper('payment')->__('Payment Method') => $info->getCcavenuePaymentMethod(),
            //Mage::helper('payment')->__('Method Option') => $info->getCcavenueMethodOption()
            Mage::helper('payment')->__('Payment Method') => $info->getMethod(),
            Mage::helper('payment')->__('Method Option') => $info->getPayuPaymentMethod()
        ));
        return $transport;
    }
}