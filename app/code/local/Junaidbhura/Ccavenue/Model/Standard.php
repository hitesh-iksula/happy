<?php
/**
 * Main payment model
 *
 * @category    Model
 * @package     Junaidbhura_Ccavenue
 * @author      Junaid Bhura <info@junaidbhura.com>
 */

class Junaidbhura_Ccavenue_Model_Standard extends Mage_Payment_Model_Method_Abstract {
	protected $_code = 'ccavenue';
	
	protected $_isInitializeNeeded      = true;
	protected $_canUseInternal          = true;
	protected $_canUseForMultishipping  = false;

	protected $_formBlockType = 'ccavenue/form_ccavenue';
    protected $_infoBlockType = 'ccavenue/info_options';
	
	public function getOrderPlaceRedirectUrl() {
		return Mage::getUrl('ccavenue/payment/redirect', array('_secure' => true));
	}

	public function assignData($data)
    {
        if (!($data instanceof Varien_Object)) {
            $data = new Varien_Object($data);
        }
        $info = $this->getInfoInstance();

        $info->setCcavenuePaymentMethod($data->getCcavenuePaymentMethod())
             ->setCcavenueMethodOption($data->getCcavenueMethodOption());

        return $this;
    }
 
    public function validate()
    {
        parent::validate();
 
        $info = $this->getInfoInstance();
 
        // $no = $info->getCcavenueCardOption();
        // $date = $info->getVendorAmount();
        // if(empty($no)){
        //     $errorCode = 'invalid_data';
        //     $errorMsg = $this->_getHelper()->__('Please Select a Payment Option');
        // }
 
        if($errorMsg){
            Mage::throwException($errorMsg);
        }
        return $this;
    }
}
?>