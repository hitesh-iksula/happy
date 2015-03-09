<?php

class Iksula_Paytmpromo_IndexController extends Mage_Core_Controller_Front_Action {

	public function redirectAction() {
		$emailId = $this->getRequest()->getParam('email_id');
		$phoneNumber = $this->getRequest()->getParam('phone_number');

		$model = Mage::getModel('paytmpromo/paytmpromo')
			->getCollection()
			->addFieldToFilter('email_id', array('eq' => $emailId));

		if($model->getSize() >= 3) {
			Mage::getSingleton('checkout/session')->addError(Mage::helper('checkout')->__("Sorry, maximum 3 transactions per email ID are allowed. Please use a different email ID to proceed."));

			$url = Mage::getBaseUrl() . 'products/hattrick-offer.html';
			$this->_redirectUrl($url);

			return;
		} else {
			$orderIdPrefix = Mage::getStoreConfig('paytmpromo/paytmpromo_group/paytmpromo_increment_id');
			$count = Mage::getModel('paytmpromo/paytmpromo')->getCollection()->getSize();
			$paddedCount = str_pad($count, 8, '0', STR_PAD_LEFT);
			$orderId = $orderIdPrefix . "-" . $paddedCount;

			$promoEntry = Mage::getModel('paytmpromo/paytmpromo');
			$promoEntry->setEmailId($emailId);
			$promoEntry->setPhoneNumber($phoneNumber);
			$promoEntry->setAmount(1);
			$promoEntry->setOrderId($orderId);
			$promoEntry->setStatus('Pending');
			$promoEntry->save();
		}

		$this->loadLayout();
		$this->getLayout()->getBlock('paytm_redirect')->setPromoEntry($promoEntry);
		$this->renderLayout();
	}

	public function responseAction() {
		$request = $this->_checkReturnedPost();

		if(!$request) {
			$this->_redirectUrl(Mage::getBaseUrl());
			return;
		}

		try {
			Mage::log($request);
			$parameters = array();

			foreach($request as $key => $value) {
				$parameters[$key] = $request[$key];
			}

			$isValidChecksum = false;
			$txnstatus = false;
			$authStatus = false;
			$mer_encrypted = Mage::getStoreConfig('payment/paytm_cc/inst_key');
			$const = (string)Mage::getConfig()->getNode('global/crypt/key');
			$mer_decrypted = Mage::helper('paytm')->decrypt_e($mer_encrypted, $const);

			$promoEntry = Mage::getModel('paytmpromo/paytmpromo')
				->getCollection()
				->addFieldToFilter('order_id', array('eq' => $request['ORDERID']))
				->getFirstItem();

			if (!$promoEntry->getId()) {
				Mage::log('No entry for processing found');
			}

			if((isset($request['CHECKSUMHASH'])) && (Mage::getStoreConfig('payment/paytm_cc/enable_checksum')==1)) {
				$return = Mage::helper('paytm')->verifychecksum_e($parameters, $mer_decrypted, $request['CHECKSUMHASH']);
				if($return == "TRUE") {
					$isValidChecksum = true;
				}
			}

			if(Mage::getStoreConfig('payment/paytm_cc/enable_checksum') == 0) {
				$isValidChecksum = true;
			}

			if($request['STATUS'] == "TXN_SUCCESS") {
				$txnstatus = true;
			}

			if($txnstatus && $isValidChecksum) {
				$authStatus = true;

				$coupons = Mage::getModel('paytmpromo/coupons')
					->getCollection()
					->addFieldToFilter('status', array('eq' => 0))
					->getFirstItem();

				$huCouponCode = $coupons->getHuCouponCode();
				$paytmCouponCode = $coupons->getPaytmCouponCode();

				$coupons->setStatus(1);
				$coupons->save();

				$promoEntry->setStatus('Success');
				$promoEntry->setRespmsg($request['RESPMSG']);
				$promoEntry->setAllottedCouponCode($huCouponCode . ", " . $paytmCouponCode);
				$promoEntry->save();

				$to = $promoEntry->getEmailId();
				$subject = "Congratulations! Your 20% OFF discount codes have arrived!";
				$txt = '<html>
				<body>
				<h3>Dear Customer,</h3>

				<h3>Thank you for participating in our The Hattrick Offer.</h3>
				<div>
				<ul>
				<li>Your Happily Unmarried coupon code is <strong>'.$huCouponCode.'</strong>
				This discount code can be redeemed at our website - '.urlencode('www.happilyunmarried.com').'</li>

				<li>Your Paytm market place coupon code is <strong>'.$paytmCouponCode.'</strong>
				This discount code can be redeemed at Paytm Market Place.</li>
				</ul>
				</div>&nbsp;
				<div>
				<p>Terms and Conditions:</p>
				<p style="margin: 12px;">For Happily Unmarried coupon code</p>
				<ul>
				<li>This code is valid till 10th March 2015.</li>
				<li>The code can be redeemed only on our website -> '.urlencode('www.happilyunmarried.com').'</li>
				<li>Happily Unmarried reserves all the rights to alter or cancel the campaign</li>
				</ul>
				<p style="margin: 12px;">For Paytm marketplace coupon code:</p>
				<ul>
				<li>Customer will get 20% cashback on shopping of Rs 499 or above. Max cash back Rs 250.</li>
				<li>Code valid on items Priced at Rs 499 or above.</li>
				<li>Code can be used only once per user.</li>
				<li>Code is not valid on Pen drives , memory cards, gold & silver coins.</li>
				<li>Codes can be used by all users.</li>
				</ul>
				</div>
				<div>
				<p>
				Regards,<br>
				<strong>Team Happily Unmarried</strong><br>
				Ph: 011 - 41042266<br>
				E-mail: <a href="customer.care@happilyunmarried.com">customer.care@happilyunmarried.com</a>
				</p>
				</div>
				</body>

				</html>';

				$headers = "From: " . strip_tags('shopping@happilyunmarried.com') . "\r\n";

				$headers .= "MIME-Version: 1.0\r\n";
				$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

				mail($to,$subject,$txt,$headers);

				$this->loadLayout();
				$this->renderLayout();
			} else {
				Mage::getSingleton('checkout/session')->addError(Mage::helper('checkout')->__("Transaction was cancelled."));

				$promoEntry->setStatus('Failure');
				$promoEntry->setRespmsg($request['RESPMSG']);
				$promoEntry->save();

				$url = Mage::getBaseUrl() . 'products/hattrick-offer.html';
				$this->_redirectUrl($url);

				return;
			}
		} catch (Mage_Core_Exception $e) {
			//
		}
	}

	protected function _checkReturnedPost() {
		if (!$this->getRequest()->isPost())
			Mage::throwException('Wrong request type.');

		$request = $this->getRequest()->getPost();
		if (empty($request))
			Mage::throwException('Request doesn\'t contain POST elements.');

		Mage::log($request);

		if (empty($request['ORDERID']) )
			Mage::throwException('Missing or invalid order ID');

		return $request;
	}

}


/*
Array
(
	[RESPCODE] => 142
	[RESPMSG] => Cancel Request by Customer at login screen
	[STATUS] => TXN_FAILURE
	[MID] => Happil15038241138812
	[TXNAMOUNT] => 2.00
	[ORDERID] => promo-00000066
	[TXNID] => 74063
	[CHECKSUMHASH] => NrS5ajNBCh6KGJVQviLCGdYlORm9T9pcO6/q5noxD9nYi83f6iGq5mvVP0o0HO54fpUHWCPOMoOb+NAG9RLam/08bAx0+W2vCp793wHbPQY=
)
*/
