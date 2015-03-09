<?php
 
/**
* Our test CC module adapter
*/
class Iksula_Vendorcheckout_Model_Vendorcheckout extends Mage_Payment_Model_Method_Abstract
{
    protected $_code = 'vendorcheckout';
    
    protected $_formBlockType = 'vendorcheckout/form_vendorcheckout';

    protected $_infoBlockType = 'vendorcheckout/info_vendorcheckout';

    public function assignData($data)
    {
        if (!($data instanceof Varien_Object)) {
            $data = new Varien_Object($data);
        }
        $info = $this->getInfoInstance();
        $info->setVendorBankname($data->getVendorBankname())
        ->setVendorAmount($data->getVendorAmount())
        ->setVendorTransactionid($data->getVendorTransactionid());
        return $this;
    }
 
    public function validate()
    {
        parent::validate();
 
        $info = $this->getInfoInstance();
 
        $no = $info->getVendorBankname();
        $date = $info->getVendorAmount();
        if(empty($no) || empty($date)){
            $errorCode = 'invalid_data';
            $errorMsg = $this->_getHelper()->__('Bank Name and Transaction Amount are required fields');
        }
 
        if($errorMsg){
            Mage::throwException($errorMsg);
        }
        return $this;
    }
}
?>