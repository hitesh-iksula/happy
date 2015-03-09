<?php
class Iksula_Misc_Model_Observer
{

	public function __construct() {
		// constructor in case
	}

	public function productSave(Varien_Event_Observer $observer) {

		if(Mage::getSingleton('admin/session')->isLoggedIn()){
			$adminUser = Mage::getSingleton('admin/session')->getUser()->getData();
		} else {
			$adminUser = array(
				'firstname'	=>	'Other',
				'lastname'	=>	'Service',
			);
		}
		
		$productData = $observer->getEvent()->getProduct()->getData();
		$productId = $productData['entity_id'];

		$productMinilog = Mage::getModel('misc/misc')->getCollection()
			->addFieldToFilter('product_id', array('eq' => $productId));
		$resultCount = count($productMinilog);

		if($resultCount > 0) {

			$resultEntry = $productMinilog->getFirstItem();

			$skuInLog = $resultEntry['product_sku'];

			if($skuInLog != $productData['sku']) {

				$time = time($productData['updated_at']);

				$timezone = date_default_timezone_get();
				if($timezone == 'UTC') {
					$time += 1 * 60 * 60 * 5.5;
				}

				$arrayToStore = array(
					'admin_user' 		=> $adminUser['firstname'] . " " . $adminUser['lastname'],
					'product_id' 		=> $productData['entity_id'],
					'product_name' 		=> $productData['name'],
					'product_sku_old'	=> $skuInLog,
					'product_sku_new'	=> $productData['sku'],
					'updated_at' 		=> date('d-m-Y h:i:s', $time)
				);

				$path = Mage::getBaseDir();
				$file = 'productSkuChangeLog.csv';
				$filepath = $path . "/" . $file;
				$handle = fopen($filepath, "a");

				fputcsv($handle, $arrayToStore);
				fclose($handle);

				$productName = $productData['name'];

				$this->sendEmail($productName);

			}

		}

	}

	public function sendEmail($productName) {
		
		$mail = new Zend_Mail();

 		// $fromEmail 	= "info@happilyunmarried.com";
 		$fromEmail 	= "hitesh.pachpor@iksula.com";
 		// $fromName 	= "happily Unmarried Catalog";
 		$fromName 	= "HU DEV";
 		// $toEmail 	= "manish@happilyunmarried.com";
 		$toEmail 	= "hitesh.pachpor@iksula.com";
 		// $toBccEmail	= "hitesh.pachpor@iksula.com";
 		$subject 	= "Product Data Changed";
 		$message 	= "Product Data Changed for '" . $productName . "', PFA.";

		$mail->setFrom($fromEmail, $fromName)
			->addTo($toEmail)
			// ->addBcc($toBccEmail)
			->setSubject($subject)
			->setBodyText($message);
		 
		$path = Mage::getBaseDir();
		$file = 'productSkuChangeLog.csv';
		$filepath = $path . "/" . $file;
		$attachment = new Zend_Mime_Part(file_get_contents($filepath));
		$attachment->filename = basename($filepath);
		$attachment->disposition = Zend_Mime::DISPOSITION_ATTACHMENT;
		$attachment->encoding = Zend_Mime::ENCODING_8BIT;
		        
		$mail->addAttachment($attachment);
		$mail->send();

	}

	public function weeklyUpdate() {
		
		$productLogModel = Mage::getModel('misc/misc')->getCollection();
		foreach($productLogModel as $entry) {
			$entry->delete();
		}

		$productCollection = Mage::getModel('catalog/product')->getCollection();
		
		foreach($productCollection as $product) {
			
			$logData = array(
				'product_id' 	=> $product->getData('entity_id'),
				'product_sku'	=> $product->getData('sku'),
				'date_updated' 	=> $product->getData('updated_at')
			);
			
			$newProductEntry = Mage::getModel('misc/misc');
			echo Mage::getModel('misc/misc')->getSelect();
			$newProductEntry->setData($logData);
			$newProductEntry->save();
			
		}

	}

}