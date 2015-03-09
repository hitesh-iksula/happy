<?php

class Iksula_Explore_Helper_Data extends Mage_Core_Helper_Abstract {

	/**
	 *
	 *
	 * Return price in the desired format. Replace 'checkout' with 'explore' in the helper call.
	 *
	 *
	 */
	public function formatPrice($price) {
		$defaultPrice = Mage::helper('checkout')->formatPrice($price);
		return $defaultPrice;

		/*$modifiedPrice = str_replace(".00", "", $defaultPrice);
		$modifiedPrice = str_replace("Rs. ", "", $modifiedPrice);
		$modifiedPrice = str_replace("Rs ", "", $modifiedPrice);
		$modifiedPrice = str_replace("Rs", "", $modifiedPrice);
		$rupeesSymbol = '<span class="rupees">Rs.</span>';
		return $modifiedPrice . $rupeesSymbol;*/
	}

	/**
	 *
	 *
	 * Return true if customer is logged in
	 *
	 *
	 */
	public function isCustomerLoggedIn() {
		Mage::getSingleton('core/session', array('name' => 'frontend'));
		$sessionCustomer = Mage::getSingleton("customer/session");
		if($sessionCustomer->isLoggedIn()) {
			return true;
		}
		return false;
	}

	/**
	 *
	 *
	 * Return HTML of the address
	 *
	 *
	 */
	public function formatAddressHtml($addressArray, $format, $className, $idIndex, $htmlIndex, $additionalHtmlAfter) {
		$html = '';
		if($format == 'box') {
			foreach($addressArray as $address) {
				$html .= '<div class="' . $className . '" id="' . $format . '-' . $address[$idIndex] . '"><div class="address_html">' . $address[$htmlIndex] . '</div><div>' . $additionalHtmlAfter . '</div></div>';
			}
		}
		return $html;
	}

	/**
	 *
	 *
	 * Return HTML of the address
	 *
	 *
	 */
	public function getFormattedHtmlFromArray($addressArray) {
		$html = '';
		$html .= $addressArray['firstname'] . ' ' . $addressArray['lastname'] . '<br/><br/>';
		if($addressArray['company'] AND $addressArray['company'] != '') {
			$html .= $addressArray['company'] . '<br/>';
		}
		$html .= $addressArray['street'][0] . '<br/>';
		$html .= $addressArray['street'][1] . '<br/>';
		$html .= $addressArray['city'] . ', ' . Mage::getModel('directory/region')->load($addressArray['region_id'])->getName() . '<br/>';
		$html .= Mage::app()->getLocale()->getCountryTranslation($addressArray['country_id']) . ', ' . $addressArray['postcode'] . '<br/>';
		$html .= $addressArray['telephone'] . '<br/>';
		$html = '<div>' . $html . '</div>';
		return $html;
	}

	/**
	 *
	 *
	 * Return quickview URL for product
	 *
	 *
	 */
	public function getQuickviewUrl($urlKey) {
		return Mage::getUrl('quickview/index/view') . 'path/' . $urlKey . '.html';
	}

	/**
	 *
	 *
	 * Return payment FAQ HTML
	 *
	 *
	 */
	public function getPaymentFAQs() {

		$paymentFaqHtml = $this->getLayout()->createBlock('cms/block')->setBlockId('payment_faqs')->toHtml();

		return $paymentFaqHtml;
	}

	/**
	 *
	 *
	 * Return default category
	 *
	 *
	 */
	public function getDefaultCategory() {

		$defaultCategory = Mage::getStoreConfig('explore/explore_group/explore_category', Mage::app()->getStore());

		return $defaultCategory;
	}

	/**
	 *
	 *
	 * Return homepage category
	 *
	 *
	 */
	public function getHomepageCategory() {

		$categoryId = Mage::getStoreConfig('explore/explore_group/explore_category_single', Mage::app()->getStore());

		return $categoryId;
	}

	/**
	 *
	 *
	 * Called on product list page. Checks if it is search page and appends query string to product URL
	 *
	 *
	 */
	public function checkAndReturnSearchString() {

		$searchString = Mage::app()->getRequest()->getParam('q');
		if($searchString) {
			$searchString = '?search=' . urlencode($searchString);
			return $searchString;
		}

		$giftParams = Mage::app()->getRequest()->getParams();
		if(array_key_exists('gender', $giftParams) OR array_key_exists('occasion', $giftParams) OR array_key_exists('personality', $giftParams)) {
			$searchString = '?';
			foreach($giftParams as $giftParam => $value) {
				if($value) {
					$searchString .= $giftParam . '=' . $value . '&';
				}
			}
			$searchString = rtrim($searchString, '&');
			return $searchString;
		}

		return false;

	}

	/**
	 *
	 *
	 * Return Folofo Category ID defined on the back-end
	 *
	 *
	 */
	public function getFolofoCategory() {

		$categoryId = Mage::getStoreConfig('explore/explore_group/explore_category_folofo', Mage::app()->getStore());

		return $categoryId;
	}

	/**
	 *
	 *
	 * Return Folofo Category ID defined on the back-end
	 *
	 *
	 */
	public function getTotalItemsInCart() {

		$itemCount = Mage::helper('checkout/cart')->getItemsCount();

		if(!$itemCount) {
			$itemCount = 0;
		}

		return $itemCount;
	}

}
