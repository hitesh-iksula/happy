<?php
/**
 *
 * BSchool Campaign
 * 
 * Address Controller
 * 
 * @ UpdateShippingAction()			-> Action where customer lands from the reminder Email
 * +								-> @ decryptFromUrl($encryptedData)	-> Decrypt the received data
 * +								-> @ trim_data(&$value, $key) 		-> Trim the received contents
 * @ UpdateShippingGuestAction()	-> If the customer is not a registered customer redirect him to a non-dashboard page
 * @ UpdateAddressAction()			-> Save the Shipping Address provided by the customer in the Order. Send notification emails to customer and admin
 * +								-> @ sendCustomerNotificationEmail($emailParameters, $addressLines)	-> Send Customer Notification Email
 * +								-> @ sendAdminNotificationEmail($emailParameters)					-> Send Admin Notification Email
 * 
 * 
 */
?>

<?php

class Iksula_AdvancedBundle_AddressController extends Mage_Core_Controller_Front_Action {

	// function to decrypt the received data
	protected function decryptFromUrl($encryptedData) {

		$key = "spOngEbObsqUArEpAnts";

		$iv = base64_decode(rawurldecode($encryptedData['iv']));
		$encOID = base64_decode(rawurldecode($encryptedData['orderId']));
		$encCID = base64_decode(rawurldecode($encryptedData['customerId']));

		$crypttextOID = mcrypt_decrypt(MCRYPT_BLOWFISH, $key, $encOID, MCRYPT_MODE_ECB, $iv);
		$crypttextCID = mcrypt_decrypt(MCRYPT_BLOWFISH, $key, $encCID, MCRYPT_MODE_ECB, $iv);

		$returnParameters = array('orderId' => $crypttextOID, 'customerId' => $crypttextCID);

		return $returnParameters;

	}

	// function to trim the received contents
	public function trim_data(&$value, $key) {

		$value = trim($value);

	}

	// action where customer lands from the reminder Email
	public function UpdateShippingAction() {

		// URL Parameters
		$orderId = $this->getRequest()->getParam('orderid');
		$customerId = $this->getRequest()->getParam('cid');
		$iv = $this->getRequest()->getParam('iv');

		// decrypt parameters
		$encryptedData = array('orderId' => $orderId, 'customerId' => $customerId, 'iv' => $iv);
		$returnData = $this->decryptFromUrl($encryptedData);
		array_walk($returnData, array($this, 'trim_data'));

		// check if order exists
		$order = Mage::getModel('sales/order')->loadByIncrementId($returnData['orderId']);

		// redirect to homepage if shipping address is already entered
		if($order->getId() AND $order->getData('status') == 'bs_ready_to_ship') {

			Mage::getSingleton('core/session')->addSuccess("Your Shipping Address has already been updated.");
			session_write_close();
			
			$this->_redirect();

		}

		// redirect to homepage if order is already complete
		if($order->getId() AND $order->getData('status') == 'complete') {

			Mage::getSingleton('core/session')->addSuccess("Your Order has already been shipped.");
			session_write_close();
			
			$this->_redirect();

		}

		// if Order ID exists, check if Customer ID matches that passed in the URL
		if($order->getId()) {
			
			$customerId = $returnData['customerId'];
			if($customerId == "Guest") {

				Mage::getSingleton('core/session')->setReturnOrderData($returnData);
				$this->_redirect('beforecheckout/address/updateshippingguest');

			} elseif($customerId == $order->getData('customer_id')) {
				
				Mage::getSingleton('core/session')->setReturnOrderData($returnData);
				$customer = Mage::getModel('customer/customer')->load($customerId);

				if($customer->getId()) {

					Mage::getSingleton('customer/session')->setCustomerAsLoggedIn($customer);

				} else {

					$this->_redirect('beforecheckout/address/updateshippingguest');

				}

				$this->loadLayout();
				$this->getLayout()->getBlock('head')->setTitle($this->__('Provide Shipping Address For Order'));

				$this->renderLayout();

			} else {

				Mage::getSingleton('core/session')->addError("Customer ID Doesn't Exist.");
				session_write_close();
				
				$this->_redirect();

			}

		} else {
			
			Mage::getSingleton('core/session')->addError("Order Does Not Exist.");
			session_write_close();
			
			$this->_redirect();

		}


	}

	// if the customer is not a registered customer, redirect him to a non-dashboard page
	public function UpdateShippingGuestAction() {

		$this->loadLayout();
		$this->getLayout()->getBlock('head')->setTitle($this->__('Provide Shipping Address For Order'));

		$this->renderLayout();

	}

	// Save the Shipping Address provided by the customer in the Order. Send notification emails to customer and admin
	public function UpdateAddressAction() {

		$postData = $this->getRequest()->getParams();

		$orderId = $postData['order_id'];
		$order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
		$scheduledDelivery = date("d - M - Y", $order->getData('scheduled_delivery'));

		$shipping_address = array(
			'firstname' => $postData['firstname'],
			'lastname' => $postData['lastname'],
			'street' => $postData['street1'] . " " . $postData['street2'],
			'city' => $postData['city'],
			'country_id' => $postData['country_id'],
			'region' => $postData['region'],
			'region_id' => $postData['region_id'],
			'postcode' => $postData['postcode'],
		);

		$order->setStatus("bs_ready_to_ship")->save();
		$order->getShippingAddress()->addData($shipping_address)->save();

		$emailParameters = array(
			'customerName' => $postData['firstname'] . " " . $postData['lastname'],
			'orderId' => $orderId,
			'deliveryDate' => $scheduledDelivery,
		);
		$addressLines = array(
			1 => $postData['firstname'] . " " . $postData['lastname'],
			2 => $postData['street1'] . ", " . $postData['street2'],
			3 => $postData['city'] . ", " . $postData['region'],
			4 => $postData['country_id'] . ", " . $postData['postcode'],
		);

		$this->sendCustomerNotificationEmail($emailParameters, $addressLines);
		$this->sendAdminNotificationEmail($emailParameters);

		Mage::getSingleton('core/session')->unsReturnOrderData();

		Mage::getSingleton('core/session')->addSuccess("Your Shipping Address has been successfully saved.");
		session_write_close();

		if($postData['user_type'] == '1') {

			$this->_redirect('customer/account/index');
		
		} else {

			$this->_redirect();
		
		}

	}

	// notification mail to the customer upon saving his shipping address
	public function sendCustomerNotificationEmail($emailParameters, $addressLines) {

		// Load order details
		$orderId = $emailParameters['orderId'];
		$order = Mage::getModel('sales/order')->loadByIncrementId($orderId);

		// Transactional Email Template's ID
		$templateId = 9;

		// Set sender information
		$senderName = Mage::getStoreConfig('trans_email/ident_support/name');
		$senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');
		$sender = array(
			'name' => $senderName,
			'email' => $senderEmail
		);

		// Set recepient information
		$recepientName = $order->getData('customer_firstname') . " " . $order->getData('customer_lastname');
		$recepientEmail = $order->getData('customer_email');
		$recepientEmail = 'hitesh.pachpor@iksula.com';

		// Get Store ID
		$storeId = Mage::app()->getStore()->getId();

		$vars = array(
			'customerName' => $emailParameters['customerName'],
			'orderId' => $orderId,
			'deliveryDate' => $emailParameters['deliveryDate'],
			'addressLine1' => $addressLines[1],
			'addressLine2' => $addressLines[2],
			'addressLine3' => $addressLines[3],
			'addressLine4' => $addressLines[4]
		);

		$translate = Mage::getSingleton('core/translate');

		// Send Transactional Email
		Mage::getModel('core/email_template')
			->sendTransactional($templateId, $sender, $recepientEmail, $recepientName, $vars, $storeId);
		$translate->setTranslateInline(true);
		
	}

	// notification mail to the admin upon a customer saving his shipping address
	public function sendAdminNotificationEmail($emailParameters) {

		// Load order details
		$orderId = $emailParameters['orderId'];
		$order = Mage::getModel('sales/order')->loadByIncrementId($orderId);

		// Transactional Email Template's ID
		$templateId = 8;

		// Set sender information
		$senderName = Mage::getStoreConfig('trans_email/ident_support/name');
		$senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');
		$sender = array(
			'name' => $senderName,
			'email' => $senderEmail
		);

		// Set recepient information
		// $recepientEmail = $order->getData('customer_email');
		$recepientName = Mage::getStoreConfig('trans_email/ident_support/name');
		$recepientEmail = Mage::getStoreConfig('trans_email/ident_support/email');

		// Get Store ID
		$storeId = Mage::app()->getStore()->getId();

		$vars = array(
			'customerName' => $emailParameters['customerName'],
			'orderId' => $orderId,
			'deliveryDate' => $emailParameters['deliveryDate']
		);

		$translate = Mage::getSingleton('core/translate');

		// Send Transactional Email
		Mage::getModel('core/email_template')
			->sendTransactional($templateId, $sender, $recepientEmail, $recepientName, $vars, $storeId);
		$translate->setTranslateInline(true);

	}

}
