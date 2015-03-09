<?php
class Iksula_Mih_Model_Order extends Mage_Sales_Model_Order
{
	//code to check whether 
    public function sendNewOrderEmail()
    {
		$orderid = $this->getId();

		Mage::log("new order id =".$orderid);
		 $productIds = array();
			foreach ($this->getItemsCollection() as $item) {
				$productIds[] = $item->getProductId();
				Mage::log($item->getProductId);
				}   
			$mih_product_id = Mage::helper('mih')->getMihId();
			// check if cart contains MIH Product then only send email
			if(in_array($mih_product_id,$productIds)){
				// do nothing, no need to send an email as we are sending through observer
				Mage::log("Order contains MIH ticket product");
			}
			else{
				parent::sendNewOrderEmail();	
				}
	}
}
