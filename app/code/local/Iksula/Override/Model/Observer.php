<?php

class Iksula_Override_Model_Observer {

	public function beforePageLoad($event) {

		$currentUrl = Mage::helper('core/url')->getCurrentUrl();
		$urlData = Mage::getSingleton('core/session')->getUrlData();

		$route = Mage::app()->getFrontController()->getRequest()->getRouteName();
		$action = Mage::app()->getFrontController()->getRequest()->getActionName();

		if(!$urlData) {
			$urlData = array();
		} else {
			if($urlData['current_url'] != $currentUrl) {
				$urlData['previous_url'] = $urlData['current_url'];
			}
		}

		if($route == 'customer' && $action == 'login' && array_key_exists('previous_url', $urlData)) {
			$urlData['login_referer'] = $urlData['previous_url'];
		}

		$urlData['current_url'] = $currentUrl;
		Mage::getSingleton('core/session')->setUrlData($urlData);
		// echo "<pre>"; print_r(Mage::getSingleton('core/session')->getUrlData()); echo "</pre>";

	}

}
