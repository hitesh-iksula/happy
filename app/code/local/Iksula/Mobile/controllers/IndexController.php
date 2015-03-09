<?php

class Iksula_Mobile_IndexController extends Mage_Core_Controller_Front_Action {

	public function getCategoryBlockAction() {

		$color = $this->getRequest()->getParam('color');
		$html = $this->getLayout()->createBlock('mobile/navigation')->setIconType($color)->setTemplate('catalog/navigation/top.phtml')->toHtml();
		$this->getResponse()->setBody($html);
		return;

	}

	public function getHarpreetSliderBlockAction() {

		$html = $this->getLayout()->createBlock('harpreet_slider/slider')->setTemplate('harpreet/slider/slider.phtml')->toHtml();
		$this->getResponse()->setBody($html);
		return;

	}

	public function getCustomerRegistrationBlockAction() {

		$html = $this->getLayout()->createBlock('mobile/register')->setTemplate('customer/form/register.phtml')->toHtml();
		$this->getResponse()->setBody($html);
		return;

	}

	public function getRecentlyViewedBlockAction() {

		$html = $this->getLayout()->createBlock('reports/product_viewed')->setTemplate('reports/product_viewed_on_product_page.phtml')->toHtml();
		$this->getResponse()->setBody($html);
		return;

	}

	public function getCategorySliderBlockAction() {

		$html = $this->getLayout()->createBlock('explore/explore')->setTemplate('explore/category/slider.phtml')->toHtml();
		$this->getResponse()->setBody($html);
		return;

	}

	public function getHomepageProductsBlockAction() {

		$cacheEnabled = Mage::getStoreConfig('mobile/mobile_group/enable_mobile_homepage_products', Mage::app()->getStore());

		if($cacheEnabled) {
			$cacheId = 'getHomepageProductsBlockAction';
			if (false !== ($html = Mage::app()->getCache()->load($cacheId))) {
				$html = unserialize($html);
			} else {
				$html = $this->getLayout()->createBlock('explore/explore')->setTemplate('explore/homepage_products.phtml')->toHtml();
				Mage::app()->getCache()->save(serialize($html), $cacheId, array('custom_cache'));
			}
		} else {
			$html = $this->getLayout()->createBlock('explore/explore')->setTemplate('explore/homepage_products.phtml')->toHtml();
		}

		$this->getResponse()->setBody($html);
		return;

	}

	public function contactusAction() {

		$this->loadLayout();
		$this->_initLayoutMessages('customer/session');
		$this->renderLayout();
		return;

	}

	public function faqAction() {

		$this->loadLayout();
		$this->renderLayout();
		return;

	}

	public function aboutusAction() {

		$this->loadLayout();
		$this->renderLayout();
		return;

	}
}
