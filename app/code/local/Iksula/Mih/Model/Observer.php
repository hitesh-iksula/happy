<?php 
class Iksula_Mih_Model_Observer {

	public function handleOrder($observer){

		try {
			// Mage::log("Under observer after order placed");
			// $order = $observer->getEvent()->getOrder();
			$orderId = $observer->getOrderIds();
			$order = Mage::getModel('sales/order')->load($orderId[0]);
			// $order = Mage::getModel('sales/order')->loadByIncrementId(100005522);
			$productIds = array();
			foreach ($order->getItemsCollection() as $item) {
				$productIds[] = $item->getProductId();
			}   
			$mih_product_id = Mage::helper('mih')->getMihId();
			// check if cart contains MIH Product then only send email
			if(in_array($mih_product_id,$productIds)){
				$customeremail = $order->getCustomerEmail();
				$customername = $order->getCustomerName();
				// Mage::log("customer email=".$customeremail."  name=".$customername);
				$emailTemplate  = Mage::getModel('core/email_template')
				->loadDefault('custom_template');
				$emailTemplateVariables = array();
				$emailTemplateVariables['order'] = $order;
				$emailTemplate->setSenderName('Happily Unmarried');
				$emailTemplate->setSenderEmail('info@happilyunmarried.com');
				$emailTemplate->setTemplateSubject('Congratulations on your purchase of MIH Tickets from Happily Unmarried');
				// use $customeremail and $customername instead of static
				$emailTemplate->send($customeremail, $customername, $emailTemplateVariables);
				// Mage::log("Mail Sent");
			}
		} catch(Exception $ex){
			Mage::logException($ex);
			// Mage::log("end of the observer");
		}

	}

}
