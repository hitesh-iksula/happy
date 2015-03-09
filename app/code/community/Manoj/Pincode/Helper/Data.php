<?php
class Manoj_Pincode_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getpincodestatus($pincode){

		$pincodeCollection = Mage::getModel('pincode/pincode')->getCollection();
		$pincodeCollection->addFieldToFilter('pincode',$pincode);
		
		$results = $pincodeCollection->getData();
		$cod = explode(',',trim($results[0]['cod']));
	
		$codcount = count(array_filter($cod));
		$prepaid = explode(',',trim($results[0]['prepaid']));
		$prepaidcount = count(array_filter($prepaid));
	
		switch (true) {
			case $codcount>0 && $prepaidcount==0:
				return 'cod';
				break;
			case $codcount>0 && $prepaidcount>0:
				return 'both';
				break;
			case $codcount==0 && $prepaidcount>0:
				return 'prepaid';
				break;
			case $codcount==0 && $prepaidcount==0:
				return 'none';
				break;
			
			default:
				return 'none';
				break;
		}

	}
	public function setcookievalue($pincode){
		return Mage::getModel('core/cookie')->set('pincode', $pincodeval);
	}
	public function getcookievalue(){
		return Mage::getModel('core/cookie')->get('pincode');
	}

	 public function getActivPaymentMethods()
    {
       $payments = Mage::getSingleton('payment/config')->getActiveMethods();
       $methods = array(array('value'=>'', 'label'=>Mage::helper('adminhtml')->__('--Please Select--')));
       foreach ($payments as $paymentCode=>$paymentModel) {
            $paymentTitle = Mage::getStoreConfig('payment/'.$paymentCode.'/title');
            $methods[$paymentCode] = array(
                'label'   => $paymentTitle,
                'value' => $paymentCode,
            );
        }
        return $methods;
   }

   public function getActivShippingMethods($isMultiSelect = false)
   {
		$methods = Mage::getSingleton('shipping/config')->getActiveCarriers();
		$options = array();
		foreach($methods as $_code => $_method)
		{
			if(!$_title = Mage::getStoreConfig("carriers/$_code/title"))
			$_title = $_code;
			$options[] = array('value' => $_code, 'label' => $_title . " ($_code)");
		}
		if($isMultiSelect)
		{
			array_unshift($options, array('value'=>'', 'label'=> Mage::helper('adminhtml')->__('--Please Select--')));
		}
		return $options;
   }
   //http://www.magentocommerce.com/magento-connect/anatta-design-abandoned-carts-2536.html
   //http://www.extendware.com/abandoned-cart-reminder-magento-extension.html

	   public function salesOrderPlaceAfter($observer)
	   {
		   	$email = $observer->getEvent()->getOrder()->getCustomerEmail();
		   	Mage::log('salesOrderPlaceAfter: '.$email);
		   	$this->_autoSubscribe($email);
	   }
	   public function customerRegisterSuccess($observer)
	   {
		   	$email = $observer->getEvent()->getCustomer()->getEmail();
		   	Mage::log('customerRegisterSuccess: '.$email);
		   	$this->_autoSubscribe($email);
	   }
	   protected function _autoSubscribe($email)
	   {
		   	Mage::log('_autoSubscribe: '.$email);
		   	$subscriber = Mage::getModel('newsletter/subscriber')->loadByEmail($email);
		   	if($subscriber->getStatus() != Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED &&
		   		$subscriber->getStatus() != Mage_Newsletter_Model_Subscriber::STATUS_UNSUBSCRIBED) {
		   		$subscriber->setImportMode(true)->subscribe($email);
		   }
	}
}
	 