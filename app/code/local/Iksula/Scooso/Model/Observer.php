<?php

class Iksula_Scooso_Model_Observer {

	/**
	 *
	 *
	 * Function hooked with the "catalog_product_save_after" event.
	 * Not in use.
	 *
	 *
	 */
	public function __productSave(Varien_Event_Observer $observer) {
		$productData = $observer->getEvent()->getProduct()->getData();
		$productId = $productData['entity_id'];
		$stocklevel = (int)Mage::getModel('cataloginventory/stock_item')->loadByProduct($productId)->getQty();
		if($productData["status"] == 1) {
			if($stocklevel > 0) {
				// check here if this product is required. approach discarded
			}
		}
	}

	/**
	 *
	 *
	 * Function called by CRON
	 *
	 *
	 */
	public function checkStock() {

		try {
			/*$myFile = Mage::getBaseDir() . "/cronlog.txt";
			$fh = fopen($myFile, 'a');
			$stringData = date('l jS \of F Y h:i:s A') . " scooso\n";
			fwrite($fh, $stringData);
			fclose($fh);*/
			$this->startRoutine();

		} catch (Exception $e) {
			Mage::printException($e);
		}

	}

	/**
	 *
	 *
	 * Function checks if products enquired by customers are in stock. If yes, sends them notifier Emails.
	 * Gets called by a bootstrapper method "checkStock" run by the CRON
	 *
	 *
	 */
	public function startRoutine() {

		$scoosoCollection = Mage::getModel('scooso/scooso')->getCollection()
			->addFieldToFilter('status', array('neq' => '1'));

		foreach($scoosoCollection as $request) {

			if($request->getEmailId() AND $request->getProductId()) {

				$_product = Mage::getModel('catalog/product')->load($request->getProductId());

				if($_product->getStatus() == 1) {

					$emailId = $request->getEmailId();
					$imgUrl = Mage::helper('catalog/image')->init($_product, 'image')->resize(300);

					if($_product->getTypeId() == 'simple') {

						$stocklevel = (int)Mage::getModel('cataloginventory/stock_item')->loadByProduct($request->getProductId())->getQty();

						if($stocklevel > 0) {

							$this->sendEmail($emailId, $_product, $imgUrl);
							$this->disableEntry($request->getId());

						}

						$myFile = Mage::getBaseDir() . "/cronlog_scooso.txt";
						$fh = fopen($myFile, 'a');
						$stringData = date('l jS \of F Y h:i:s A') . " " . $request->getProductId() . " " . $stocklevel . " \n";
						fwrite($fh, $stringData);
						fclose($fh);

					} else {

						$_allSubProducts = $_product->getTypeInstance(true)->getUsedProducts(null, $_product);

						$_attributes = $_product->getTypeInstance(true)->getConfigurableAttributes($_product);
						$count = count($_attributes);

						$attributeOptions = explode(",", $request->getOptionIds());

						foreach($_allSubProducts as $_subProduct) {

							$_subProductId = $_subProduct->getId();
							$n = 0;

							foreach($_attributes as $attribute) {

								$attributeId = $attribute->getProductAttribute()->getAttributeId();
								$attributeCode = $attribute->getProductAttribute()->getAttributeCode();
								if(in_array($_subProduct->getData($attributeCode), $attributeOptions)) {

									$n++;

									if($n == $count) {

										$stocklevel = (int)Mage::getModel('cataloginventory/stock_item')->loadByProduct($_subProductId)->getQty();

										if($stocklevel > 0) {

											if($request->getAdditionalField()) {
												foreach ($_product->getMediaGalleryImages() as $image) {
													if($image->getLabel() == $request->getAdditionalField()) {
														$imgUrl = $image->getUrl();
													}
												}
											}
											$this->sendEmail($emailId, $_product, $imgUrl);
											$this->disableEntry($request->getId());

										}

										$myFile = Mage::getBaseDir() . "/cronlog_scooso.txt";
										$fh = fopen($myFile, 'a');
										$stringData = date('l jS \of F Y h:i:s A') . " " . $_subProduct->getId() . " " . $stocklevel . " \n";
										fwrite($fh, $stringData);
										fclose($fh);

									}

								}

							}

						}

					}

				}

			}

		}

	}

	/**
	 *
	 *
	 * Email function called by startRoutine
	 *
	 *
	 */
	public function sendEmail($emailId, $_product, $imgUrl) {

		// Transactional Email Template's ID
		$templateId = 12;

		// Set sender information
		$senderName = Mage::getStoreConfig('trans_email/ident_support/name');
		$senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');
		$sender = array(
			'name' => $senderName,
			'email' => $senderEmail
		);

		// Set recepient information
		$recepientName = "Customer";
		$recepientEmail = $emailId;

		// Get Store ID
		$storeId = Mage::app()->getStore()->getId();

		$vars = array(
			'productName' => $_product->getName(),
			'productUrl'  => $_product->getProductUrl(),
			'productImg'  => $imgUrl
		);

		$translate = Mage::getSingleton('core/translate');

		// Send Transactional Email
		Mage::getModel('core/email_template')
			->sendTransactional($templateId, $sender, $recepientEmail, $recepientName, $vars, $storeId);
		$translate->setTranslateInline(true);

	}

	/**
	 *
	 *
	 * disables the entry
	 *
	 *
	 */
	public function disableEntry($id) {

		$scoosoModel = Mage::getModel('scooso/scooso')->setId($id)->setStatus('1')->save();

	}

}
