<?php

/**
 * 
 * 
 * Sends product collection HTML based on selected options via AJAX Call.
 * 
 * 
 */
class Iksula_Explore_GiftController extends Mage_Core_Controller_Front_Action {

	public function indexAction() {
		
		$attributes = $this->getRequest()->getParams();

		$count = 0;
		foreach($attributes as $attribute => $value) {
			if($value) $count++;
		}

		if($count > 0) {
			$code = 1;
			$products = $this->getLayout()->createBlock('explore/gift_collection')->setTemplate('catalog/product/list.phtml')->toHtml();
		} else {
			$code = 2;
			$products = '';
		}

		$response = array(
			'code' => $code,
			'html' => $products
		);

		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
		return;

	}

}