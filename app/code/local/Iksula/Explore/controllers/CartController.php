<?php

require_once 'Mage/Checkout/controllers/CartController.php';
class Iksula_Explore_CartController extends Mage_Checkout_CartController {

	public function CartmodalAction() {

		$this->loadLayout();
		$this->renderLayout();

	}

	public function getCustomerAddressByIdAction() {

		$id = $this->getRequest()->getParam('address_id');
		$address = Mage::getModel('customer/address')->load($id)->getData();
		$jsonData = Mage::helper('core')->jsonEncode($address);
		$this->getResponse()->setHeader('Content-type', 'application/json');
		$this->getResponse()->setBody($jsonData);

	}

	public function getFaqToolbarHtmlAction() {

		$html = $this->getLayout()->createBlock('core/template')->setTemplate('explore/sidebars/faq.phtml')->toHtml();
		$this->getResponse()->setBody($html);
		return;

	}

}
