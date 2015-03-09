<?php
class Iksula_Vendorcheckout_Block_Info_Vendorcheckout extends Mage_Payment_Block_Info
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
        $transport = new Varien_Object();
        $transport = parent::_prepareSpecificInformation($transport);
        $transport->addData(array(
            Mage::helper('payment')->__('Bank Name') => $info->getVendorBankname(),
            Mage::helper('payment')->__('NEFT Amount') => $info->getVendorAmount(),
            Mage::helper('payment')->__('Transaction / Reference ID') => $info->getVendorTransactionid()
        ));
        return $transport;
    }
}