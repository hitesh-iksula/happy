<?php

class Iksula_Trackorder_IndexController extends Mage_Core_Controller_Front_Action {

	/*
	 *
	 * Controller action called for the Track My Order page.
	 *
	 * Checks the type of order and makes the appropriate calls.
	 * Sets appropriate data to block and is then retrieved by the phtml file for formatting
	 *
	 *
	 */
	public function IndexAction() {

		$orderid = $this->getRequest()->getParam('orderid');
		if(!empty($orderid)) {
			try {
				$order = Mage::getModel('sales/order')->load($orderid, 'increment_id');
				if($order->getId() != null) {
					Mage::register('trackorderid', $orderid);
					$paymentMethod = $order->getPayment();
					$methodType = $paymentMethod->getMethod();
					try {
						$shipmentCollection = Mage::getResourceModel('sales/order_shipment_collection')
						->setOrderFilter($order)
						->load();
					} catch(Exception $ex) {

					}
					$tracknums = array();
					$shipmentNotCreated = false;
					$counter = 0;

					$BlueDartArr = array();
					$DelhiveryArr = array();

					foreach ($shipmentCollection as $shipment) {
						foreach($shipment->getAllTracks() as $tracknum) {
							//echo $tracknums[$counter]['number'] = $tracknum->getNumber();
							//echo $tracknums[$counter]['title'] = $tracknum->getTitle();
							$trackNumber = $tracknum->getNumber();
							$trackTitle  = $tracknum->getTitle();
							//echo "--".$trackTitle."--";
									if($trackNumber) {
										// If COD, make call to Gojavas
										//if(trim($methodType) == 'cashondelivery') {
										if($trackTitle == 'cashondelivery') { //Modified code

										} else {
											//Mage::register('ordertype', 'prepaid');
											// Order shipped with Blue Dart

											if($trackTitle == 'Blue Dart') {

												$bluedart  = Mage::getModel('trackorder/bluedart');
												$xmlarray  = $bluedart->getTracking($trackNumber);
												$BlueDartArr[] = $xmlarray;
												//echo "<pre>";print_r($BlueDartArr);echo "</pre>";
												if(empty($BlueDartArr)) {
													// Mage::getSingleton('core/session')->addError('Your order has not been shipped yet. We will inform you once it has been shipped!');
													$shipmentNotCreated = true;
												}

											}

											// Order shipped with Delhivery
											if($trackTitle == 'Delhivery') {

												$token = 'dd43062cfe6417837998a7eb5aa76f41e9599171';
												$waybill = $trackNumber;
												$url = 'http://track.delhivery.com/api/packages/json/?token=' . $token . '&waybill=' . $waybill;

												$ch = curl_init();
												curl_setopt($ch, CURLOPT_URL, $url);
												curl_setopt($ch, CURLOPT_POST, 0);
												curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

												$response = json_decode(curl_exec($ch));
												$DelhiveryArr[] = $response;
												//echo "<pre>";print_r($DelhiveryArr);echo "</pre>";

											}

										}

									}

									// Order shipment does not exist
									else {
										// Mage::getSingleton('core/session')->addError('Your order has not been shipped yet. We will inform you once it has been shipped!');
										$shipmentNotCreated = true;
									}


							$counter++;
						}
					}							



				}

				else {
					//Mage::getSingleton('core/session')->addError('Sorry, this order ID does not exist');
					Mage::getSingleton('core/session')->setNotid('true');
				}

			} catch(Exception $ex) {

				Mage::getSingleton('core/session')->addError('Something went wrong while getting tracking information');
			}

		}

		else {
			//Mage::getSingleton('core/session')->addError('order id is not supplied, please provide order id');
			Mage::getSingleton('core/session')->setNotid('true');
		}

		$this->loadLayout();

		if(!empty($xmlarray)) {
			$block = $this->getLayout()->getBlock("trackorder_index");
			$block->setData('resultset', $BlueDartArr);
		}

		if(!empty($response)) {
			$block = $this->getLayout()->getBlock("trackorder_index");
			$block->setData('response', $DelhiveryArr);
		}

		$block = $this->getLayout()->getBlock("trackorder_index");
		$block->setData('shipmentnotcreated', $shipmentNotCreated);

		$this->getLayout()->getBlock("head")->setTitle($this->__("Track My Order"));

		/*$breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
		$breadcrumbs->addCrumb("home", array(
			"label" => $this->__("Home Page"),
			"title" => $this->__("Home Page"),
			"link"=> Mage::getBaseUrl()
		));

		$breadcrumbs->addCrumb("trackorder", array(
			"label" => $this->__("Trackorder"),
			"title" => $this->__("Trackorder")
		));*/

		$this->renderLayout();

	}

	/*
	 *
	 * Controller action called for the Track My Order page.
	 *
	 * Checks if entered order ID exists or not.
	 * If exists, redirects to the Track My Order page..
	 *
	 *
	 */
	public function orderexistenceAction() {
		$orderid = $this->getRequest()->getParam('orderid');
		if(!empty($orderid)) {
			$order = Mage::getModel('sales/order')->load($orderid, 'increment_id');
			if($order->getId()) {
				echo "true";
			}
			else {
				echo "false";
			}
		}

	}

	public function trackorderAction() { 
		$this->loadLayout();
		$block = $this->getLayout()->getBlock("trackorder_trackorder");
		$this->renderLayout();
	}

}
