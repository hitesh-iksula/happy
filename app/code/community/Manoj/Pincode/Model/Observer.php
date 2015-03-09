<?php
class Manoj_Pincode_Model_Observer
{

	public function paymentMethods(Varien_Event_Observer $observer) {
		$event           = $observer->getEvent();
		$method          = $event->getMethodInstance();
		$result          = $event->getResult();
		$currencyCode    = Mage::app()->getStore()->getCurrentCurrencyCode();
	
	    $enableStatus = Mage::getStoreConfig('paymentfilter/paymentstep/enable');
	    if($enableStatus==1){
		$shippingPincode =Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress()->getPostcode();
		$isAdmin = Mage::app()->getStore()->isAdmin();
		if($isAdmin){
			return;
		}
		$pincodeHelper = Mage::helper('pincode/data');
		$pincodeStatus = $pincodeHelper->getpincodestatus($shippingPincode);

	
		$codArray = array();
		$prepaidArray = array();
		$codArray = explode(',',Mage::getStoreConfig('paymentfilter/paymentstep/codenable'));
		$prepaidArray = explode(',',Mage::getStoreConfig('paymentfilter/paymentstep/prepaidenable'));

		$codPrepaid = array_merge($codArray,$prepaidArray);
		$filterArray = array_unique($codPrepaid);

		switch ($pincodeStatus) {
			case 'cod':
					if(in_array($method->getCode(),$codArray)){

						$result->isAvailable = true;
					}
					else{
						$result->isAvailable = false;
					}
			break;	
			case 'prepaid':
				if(in_array($method->getCode(),$prepaidArray)){

						$result->isAvailable = true;
					}
					else{
						$result->isAvailable = false;
					}
			break;	
			case 'both':
				if(in_array($method->getCode(),$filterArray)){

						$result->isAvailable = true;
					}
					else{
						$result->isAvailable = false;
					}
			break;	
			
			default:
					$result->isAvailable = false;
					
			break;
		}

	}
}
		
}
