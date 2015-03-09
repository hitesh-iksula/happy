<?php
/**
 * Main payment controller
 *
 * @category    Controller
 * @package     Junaidbhura_Ccavenue
 * @author      Junaid Bhura <info@junaidbhura.com>
 */

class Junaidbhura_Ccavenue_PaymentController extends Mage_Core_Controller_Front_Action {
	// The cancel action is triggered when someone cancels a payment in CC Avenue
	public function cancelAction() {
        if (Mage::getSingleton('checkout/session')->getLastRealOrderId()) {
            $order = Mage::getModel('sales/order')->loadByIncrementId(Mage::getSingleton('checkout/session')->getLastRealOrderId());
            if($order->getId()) {
				// Flag the order as 'cancelled' and save it
				$order->cancel()->setState(Mage_Sales_Model_Order::STATE_CANCELED, true, 'CCAvenue has declined the payment.')->save();
			}
        }
	}

	// The review action is triggered when CCAvenue sends an AuthDesc as B
	public function reviewAction() {
		if (Mage::getSingleton('checkout/session')->getLastRealOrderId()) {
            $order = Mage::getModel('sales/order')->loadByIncrementId(Mage::getSingleton('checkout/session')->getLastRealOrderId());
            if($order->getId()) {
				// Flag the order as 'payment review' and save it
				$order->setState(Mage_Sales_Model_Order::STATE_PAYMENT_REVIEW, true, 'CCAvenue has sent AuthDesc as B.');
                $order->save();
			}
        }
	}

	// The redirect action is triggered when someone places an order
	public function redirectAction() {
		$this->loadLayout();
        $block = $this->getLayout()->createBlock('Mage_Core_Block_Template','ccavenue',array('template' => 'ccavenue/redirect.phtml'));
		$this->getLayout()->getBlock('content')->append($block);
        $this->renderLayout();
	}

	// The response action is triggered when CC Avenue sends a response
	public function responseAction() {
		if($this->getRequest()->isPost()) {
			// Include CCAvenue Functions
			require_once('libfuncs.php3');

			// Retrieve POST Values
			$WorkingKey = Mage::getStoreConfig('payment/ccavenue/working_key');
			$Merchant_Id = $_POST['Merchant_Id'];
			$Amount = $_POST['Amount'];
			$Order_Id = $_POST['Order_Id'];
			$Merchant_Param = $_POST['Merchant_Param'];
			$Checksum = $_POST['Checksum'];
			$AuthDesc = $_POST['AuthDesc'];
			$cardCategory = $_POST['card_category'];
			$bankName = $_POST['bank_name'];

			// Insert into CCAvenue Response Log Table
			$query = "INSERT INTO tbl_ccavenue_response SET merchant_id='".$Merchant_Id."', amount=".$Amount.", order_id=".$Order_Id.", merchant_param='".$Merchant_Param."', checksum='".$Checksum."', authdesc='".$AuthDesc."', ccavenue_response_ip='".$this->getUserIP()."', card_category='".$cardCategory."', bank_name='".$bankName."', ccavenue_response_dtime = NOW()";
			$db = Mage::getSingleton('core/resource')->getConnection('core_write');
			$db->query($query);
			$db->query("COMMIT");

			$Checksum = verifyChecksum($Merchant_Id, $Order_Id, $Amount, $AuthDesc, $Checksum, $WorkingKey);

			// Check response and take appropriate actions
			if($Checksum == "true" && $AuthDesc == "Y")	{
				 // Payment was successful, so update the order's state, send order email and move to the success page
				$order = Mage::getModel('sales/order');
				$order->loadByIncrementId($Order_Id);

				$order->sendNewOrderEmail();

				$order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, 'CCAvenue has authorized the payment.');

				$order->setEmailSent(true);

				$order->save();

				Mage::getSingleton('checkout/session')->unsQuoteId();

				Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/success', array('_secure'=>true));
			}
			else if($Checksum == "true" && $AuthDesc == "B") {
				// Payment was successful as a 'Batch Processing' order. Status of such payments can only be determined after some time
				$this->reviewAction();
				Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/failure', array('_secure'=>true));
			}
			else if($Checksum == "true" && $AuthDesc == "N") {
				// CCAvenue has declined the payment, so cancel the order and redirect to fail page
				$this->cancelAction();
				Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/failure', array('_secure'=>true));
			}
			else {
				// There is a serious problem in getting a response from CCAvenue
				$this->cancelAction();
				Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/failure', array('_secure'=>true));
			}
		}
		else
			Mage_Core_Controller_Varien_Action::_redirect('');
	}

	// Function to get user's IP
	private function getUserIP() {
		if (isset($_SERVER)) {
			if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
				return $_SERVER["HTTP_X_FORWARDED_FOR"];

			if (isset($_SERVER["HTTP_CLIENT_IP"]))
				return $_SERVER["HTTP_CLIENT_IP"];

			return $_SERVER["REMOTE_ADDR"];
		}

		if (getenv('HTTP_X_FORWARDED_FOR'))
			return getenv('HTTP_X_FORWARDED_FOR');

		if (getenv('HTTP_CLIENT_IP'))
			return getenv('HTTP_CLIENT_IP');

		return getenv('REMOTE_ADDR');
	}
}
