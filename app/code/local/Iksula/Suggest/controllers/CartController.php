<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Shopping cart controller
 */

require_once 'Mage/Checkout/controllers/CartController.php';
class Iksula_Suggest_CartController extends Mage_Core_Controller_Front_Action
{
	/**
	 * Action list where need check enabled cookie
	 *
	 * @var array
	 */
	protected $_cookieCheckActions = array('add');

	/**
	 * Retrieve shopping cart model object
	 *
	 * @return Mage_Checkout_Model_Cart
	 */
	protected function _getCart()
	{
		return Mage::getSingleton('checkout/cart');
	}

	/**
	 * Get checkout session model instance
	 *
	 * @return Mage_Checkout_Model_Session
	 */
	protected function _getSession()
	{
		return Mage::getSingleton('checkout/session');
	}

	/**
	 * Get current active quote instance
	 *
	 * @return Mage_Sales_Model_Quote
	 */
	protected function _getQuote()
	{
		return $this->_getCart()->getQuote();
	}

	/**
	 * Set back redirect url to response
	 *
	 * @return Mage_Checkout_CartController
	 */
	protected function _goBack()
	{
		$returnUrl = $this->getRequest()->getParam('return_url');
		if ($returnUrl) {
			// clear layout messages in case of external url redirect
			if ($this->_isUrlInternal($returnUrl)) {
				$this->_getSession()->getMessages(true);
			}
			$this->getResponse()->setRedirect($returnUrl);
		} elseif (!Mage::getStoreConfig('checkout/cart/redirect_to_cart')
			&& !$this->getRequest()->getParam('in_cart')
			&& $backUrl = $this->_getRefererUrl()
		) {
			$this->getResponse()->setRedirect($backUrl);
		} else {
			if (($this->getRequest()->getActionName() == 'add') && !$this->getRequest()->getParam('in_cart')) {
				$this->_getSession()->setContinueShoppingUrl($this->_getRefererUrl());
			}
			$this->_redirect('checkout/cart');
		}
		return $this;
	}

	/**
	 * Initialize product instance from request data
	 *
	 * @return Mage_Catalog_Model_Product || false
	 */
	protected function _initProduct()
	{
		$productId = (int) $this->getRequest()->getParam('product');
		if ($productId) {
			$product = Mage::getModel('catalog/product')
				->setStoreId(Mage::app()->getStore()->getId())
				->load($productId);
			if ($product->getId()) {
				return $product;
			}
		}
		return false;
	}

	/**
	 * Determine Vendor Margin
	 */
	public function initializeVendorCoupon()
	{
		if ($this->_getCart()->getQuote()->getItemsCount()) {
			$session = Mage::getSingleton('customer/session');
			if ($session->isLoggedIn()) {
				$customerData = Mage::getModel('customer/customer')->load($session->getId());
				$customerGroup = $customerData->getData('group_id');
				if ($customerGroup == 6 or $customerGroup == 7) {
					$minimumCreditLimitRequired = 100;
					$vendorMarginSimple = $customerData->getData('margin');
					$vendorMargin = 1 - $customerData->getData('margin') / 100;
					$vendorCreditLimit = $customerData->getData('creditlimit');
					if ($customerData->getData('creditlimit_rs')) {
						$vendorCreditLimitRupees = $customerData->getData('creditlimit_rs');
						$currentCartPrice = $this->_getCart()->getQuote()->getGrandTotal();
					}

					$couponCodeGenerate = 'vendor_' . Mage::getSingleton('customer/session')->getCustomerId();
					$couponExists = Mage::getModel('salesrule/coupon')->load($couponCodeGenerate, 'code');
					if (!$couponExists->getId()) {
						// echo "How did I get here?!!"; exit;
						$couponCodeGenerate = $this->createVendorCoupon($vendorMarginSimple, $customerGroup);
						$this->vendorPostAction($couponCodeGenerate);
					}
				}
			}
		}
	}

	/**
	 * Create Vendor discount coupon
	 */
	public function createVendorCoupon($vendorMargin, $customerGroup)
	{
		$couponCodeGenerate = 'vendor_' . Mage::getSingleton('customer/session')->getCustomerId();
		$data = array(
			'product_ids' => null,
			'name' => sprintf('Vendor_%s Margin', Mage::getSingleton('customer/session')->getCustomerId()),
			'description' => null,
			'is_active' => 1,
			'website_ids' => array(1),
			'customer_group_ids' => array($customerGroup),
			'coupon_type' => 2,
			'coupon_code' => $couponCodeGenerate,
			'uses_per_coupon' => 1,
			'uses_per_customer' => 1,
			'from_date' => null,
			'to_date' => null,
			'sort_order' => null,
			'is_rss' => 0,
			'rule' => array(
				'conditions' => array(
					array(
						'type' => 'salesrule/rule_condition_combine',
						'aggregator' => 'all',
						'value' => 1,
						'new_child' => null
					)
				)
			),
			'simple_action' => 'by_percent',
			'discount_amount' => $vendorMargin,
			'discount_qty' => 0,
			'discount_step' => null,
			'apply_to_shipping' => 0,
			'simple_free_shipping' => 0,
			'stop_rules_processing' => 0,
			'rule' => array(
				'actions' => array(
					array(
						'type' => 'salesrule/rule_condition_product_combine',
						'aggregator' => 'all',
						'value' => 1,
						'new_child' => null
					)
				)
			),
			'store_labels' => array('Vendor Margin')
		);

		$model = Mage::getModel('salesrule/rule');
		$data = $this->_filterDates($data, array('from_date', 'to_date'));
		$validateResult = $model->validateData(new Varien_Object($data));
		if ($validateResult == true) {
			if (isset($data['simple_action']) && $data['simple_action'] == 'by_percent'
					&& isset($data['discount_amount'])) {
				$data['discount_amount'] = min(100, $data['discount_amount']);
			}
			if (isset($data['rule']['conditions'])) {
				$data['conditions'] = $data['rule']['conditions'];
			}
			if (isset($data['rule']['actions'])) {
				$data['actions'] = $data['rule']['actions'];
			}
			unset($data['rule']);
			$model->loadPost($data);
			$model->save();
		}
		return $couponCodeGenerate;
	}

	/**
	 * Apply Vendor discount on final price
	 */
	public function vendorPostAction($couponCodeGenerate)
	{
		/**
		 * No reason continue with empty shopping cart
		 */
		// if (!$this->_getCart()->getQuote()->getItemsCount()) {
			// $this->_goBack();
			// return;
		// }

		$couponCode = $couponCodeGenerate;
		// if ($this->getRequest()->getParam('remove') == 1) {
			// $couponCode = '';
		// }
		// $oldCouponCode = $this->_getQuote()->getCouponCode();

		// if (!strlen($couponCode) && !strlen($oldCouponCode)) {
			// $this->_goBack();
			// return;
		// }

		// echo $couponCode . "<br />";

		try {
			$this->_getQuote()->getShippingAddress()->setCollectShippingRates(true);

			// echo "<pre>"; print_r($this->_getQuote()->setCouponCode('vendor')); exit;
			// echo "<pre>"; print_r($this->_getQuote()->setCouponCode('vendor_nouse')->collectTotals()->getData()); exit;

			$this->_getQuote()->setCouponCode($couponCode)
				->setTotalsCollectedFlag(true)
				->collectTotals()
				->save();

			$this->_redirect('checkout/cart');

			// if ($couponCode) {
				// if ($couponCode == $this->_getQuote()->getCouponCode()) {
					// $this->_getSession()->addSuccess('');
				// }
				// else {
					// $this->_getSession()->addError(
						// $this->__('Coupon code "%s" is not valid.', Mage::helper('core')->htmlEscape($couponCode))
					// );
				// }
			// } else {
				// $this->_getSession()->addSuccess($this->__('Coupon code was canceled.'));
			// }

		} catch (Mage_Core_Exception $e) {
			$this->_getSession()->addError($e->getMessage());
		} catch (Exception $e) {
			$this->_getSession()->addError($this->__('Cannot apply the coupon code.'));
			Mage::logException($e);
		}

		// $this->_goBack();
	}

	public function indexAction()
	{
		// Initialize vendor coupon before indexAction does anything. And redirect back to indexAction when coupon is created.
		$this->initializeVendorCoupon();

		$cart = $this->_getCart();
		if ($cart->getQuote()->getItemsCount()) {
			$cart->init();
			$cart->save();

			if (!$this->_getQuote()->validateMinimumAmount()) {
				$warning = Mage::getStoreConfig('sales/minimum_order/description');
				$cart->getCheckoutSession()->addNotice($warning);
			}
		}

		// Compose array of messages to add
		$messages = array();
		foreach ($cart->getQuote()->getMessages() as $message) {
			if ($message) {
				$messages[] = $message;
			}
		}
		$cart->getCheckoutSession()->addUniqueMessages($messages);

		// echo "in index controller"; exit;

		/**
		 * if customer enters shopping cart we should mark quote
		 * as modified bc he can has checkout page in another window.
		 */
		$this->_getSession()->setCartWasUpdated(true);

		Varien_Profiler::start(__METHOD__ . 'cart_display');
		$this
			->loadLayout()
			->_initLayoutMessages('checkout/session')
			->_initLayoutMessages('catalog/session')
			->getLayout()->getBlock('head')->setTitle($this->__('Shopping Cart'));
		$this->renderLayout();
		Varien_Profiler::stop(__METHOD__ . 'cart_display');
	}

	/**
	 * Add product to shopping cart action
	 */
	public function addAction()
	{
		$index = $this->getRequest()->getParam('product');
		$categoryName = $this->getRequest()->getParam('category_name');

		$categoriesInCart = Mage::getSingleton('core/session')->getCategoryArray();
		if(!$categoriesInCart) {
			$productCategories = array(
				$index => $categoryName
			);
			Mage::getSingleton('core/session')->setCategoryArray($productCategories);
		} else {
			$categoriesInCart[$index] = $categoryName;
			Mage::getSingleton('core/session')->setCategoryArray($categoriesInCart);
		}

		/*category name code end*/
		$response = array();
		$cart   = $this->_getCart();
		$params = $this->getRequest()->getParams();
		

		$session = Mage::getSingleton('core/session');
		$productobj = Mage::getModel('catalog/product')->load($params['product']);
		/* MIH Functionality | START */
		if($params['product'] == Mage::helper('mih')->getMihId()) {  //this code is executes from MIH page
			$totalItemsInCart = Mage::helper('checkout/cart')->getCart()->getItemsCount();

			if($totalItemsInCart > 0) {
				//echo "this aint allowed";
				$flag = "error";
				foreach($this->_getSession()->getQuote()->getAllItems() as $cartItem) {
				$productId = $cartItem->getProductId();
				if($productId == Mage::helper('mih')->getMihId()) {
					$flag = "noerror";
					}
				}
				if($flag == "error"){
					$response['status'] = 'ERROR';
					$homeLink = Mage::getBaseUrl();
					$homeLink = "<a href='$homeLink'>here</a>";
					$response['message'] = $this->__('You can not Add MIH9 tickets with other products in your cart. Please clear your cart before buying MIH9 tickets. Click ' . $homeLink . ' to go to homepage.');
					$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
					return;
				}

			}
		}else{ //this code is executes from any product buy button click
			foreach($this->_getSession()->getQuote()->getAllItems() as $cartItem) {
				$productId = $cartItem->getProductId();

				if($productId == Mage::helper('mih')->getMihId()) {
					$response['status'] = 'ERROR';
					$response['message'] = $this->__('You can not Add <b>%s</b>  with MIH9 Ticket in Cart!', Mage::helper('core')->htmlEscape($productobj->getName()));
					$this->loadLayout();
					$response['toplink'] = $toplink;
					$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
					return ;
				}
			}
		}

		/* MIH Functionality | END */

		/* AJAX ADD TO CART STARTS */

		if($params['isAjax'] == 1){

			try {
				if (isset($params['qty'])) {
					$filter = new Zend_Filter_LocalizedToNormalized(
					array('locale' => Mage::app()->getLocale()->getLocaleCode())
					);
					$params['qty'] = $filter->filter($params['qty']);
				}

				$product = $this->_initProduct();
				$related = $this->getRequest()->getParam('related_product');

				/**
				 * Check product availability
				 */
				if (!$product) {
					$response['status'] = 'ERROR';
					$response['message'] = $this->__('Unable to find Product ID');
				}

				$cart->addProduct($product, $params);
				if (!empty($related)) {
					$cart->addProductsByIds(explode(',', $related));
				}

				$cart->save();
				if($params['product'] == Mage::helper('mih')->getMihId()) {
					$session->setRenderSidebarCart(0);
				}else{
					$session->setRenderSidebarCart(1);
				}
				$this->_getSession()->setCartWasUpdated(true);

				/**
				 * @todo remove wishlist observer processAddToCart
				 */
				Mage::dispatchEvent('checkout_cart_add_product_complete',
				array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
				);

				if (!$this->_getSession()->getNoCartRedirect(true)) {
					if (!$cart->getQuote()->getHasError()){
						$message = $this->__('%s is added to your shopping cart.', Mage::helper('core')->htmlEscape($product->getName()));
						$response['status'] = 'SUCCESS';
						$response['message'] = $message;

						$this->loadLayout();

						$cartMessage = array(
							'message' => $this->__('<b>%s</b> added to your Carton!', Mage::helper('core')->htmlEscape($product->getName())),
							'type'    => 'add-success'
						);
						$session->setCustomCartActionMessage($cartMessage);

						$response['toplink'] = $toplink;

						$totalItemsInCart = Mage::helper('checkout/cart')->getCart()->getItemsCount();
						if($totalItemsInCart < 10) {
							$totalItemsInCart = '0' . $totalItemsInCart;
						}

						$response['cartitemqty'] = $totalItemsInCart;

						if($params['referer'] == 'cart_modal') {
							$response['referer'] = 'cart_modal';
							$cartTotals = $this->getLayout()->createBlock('checkout/cart_totals')->setTemplate('checkout/cart/totals.phtml')->toHtml();
							$response['carttotals'] = $cartTotals;

							$headerCartButtonHtml = $this->getLayout()->createBlock('core/template')->setTemplate('explore/header/cart_button.phtml')->toHtml();
							$response['header_cart'] = $headerCartButtonHtml;

							$cartBlock = $this->getLayout()->createBlock('checkout/cart')->setCartTemplate('checkout/cart.phtml');
							$cartBlock->addItemRender('simple', 'checkout/cart_item_renderer', 'checkout/cart/item/default.phtml');
							$cartBlock->addItemRender('grouped', 'checkout/cart_item_renderer_grouped', 'checkout/cart/item/default.phtml');
							$cartBlock->addItemRender('configurable', 'checkout/cart_item_renderer_configurable', 'checkout/cart/item/default.phtml');

							$activityMessage = $this->getLayout()->createBlock('explore/explore')->setTemplate('checkout/cart/flag.phtml')->toHtml();
							$response['activitymessage'] = $activityMessage;

							$itemHtml = '';
							foreach($cartBlock->getItems() as $_item) {
								$itemHtml .= $cartBlock->getItemHtml($_item);
							}
							$response['cartitems'] = $itemHtml;
							$response['errorsign'] = 0;
						}
					}
				}
			} catch (Mage_Core_Exception $e) {
				$msg = "";
				if ($this->_getSession()->getUseNotice(true)) {
					$msg = $e->getMessage();
				} else {
					$messages = array_unique(explode("\n", $e->getMessage()));
					foreach ($messages as $message) {
						$msg .= $message.'<br/>';
					}
				}

				$response['status'] = 'ERROR';
				$response['message'] = $msg;
			} catch (Exception $e) {
				$response['status'] = 'ERROR';
				$response['message'] = $this->__('Cannot add the item to shopping cart.');
				Mage::logException($e);
			}
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
			return;
		}

		else {

			try {
				if (isset($params['qty'])) {
					$filter = new Zend_Filter_LocalizedToNormalized(
						array('locale' => Mage::app()->getLocale()->getLocaleCode())
					);
					$params['qty'] = $filter->filter($params['qty']);
				}

				$product = $this->_initProduct();
				$related = $this->getRequest()->getParam('related_product');

				/**
				 * Check product availability
				 */
				if (!$product) {
					$this->_goBack();
					return;
				}

				$cart->addProduct($product, $params);
				if (!empty($related)) {
					$cart->addProductsByIds(explode(',', $related));
				}

				$cart->save();

				$this->_getSession()->setCartWasUpdated(true);

				/**
				 * @todo remove wishlist observer processAddToCart
				 */
				Mage::dispatchEvent('checkout_cart_add_product_complete',
					array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
				);

				if (!$this->_getSession()->getNoCartRedirect(true)) {
					if (!$cart->getQuote()->getHasError()) {
						$message = $this->__('%s was added to your shopping cart.', Mage::helper('core')->escapeHtml($product->getName()));
						$this->_getSession()->addSuccess($message);
					}
					$this->_goBack();
				}
			} catch (Mage_Core_Exception $e) {
				if ($this->_getSession()->getUseNotice(true)) {
					$this->_getSession()->addNotice(Mage::helper('core')->escapeHtml($e->getMessage()));
				} else {
					$messages = array_unique(explode("\n", $e->getMessage()));
					foreach ($messages as $message) {
						$this->_getSession()->addError(Mage::helper('core')->escapeHtml($message));
					}
				}

				$url = $this->_getSession()->getRedirectUrl(true);
				if ($url) {
					$this->getResponse()->setRedirect($url);
				} else {
					$this->_redirectReferer(Mage::helper('checkout/cart')->getCartUrl());
				}
			} catch (Exception $e) {
				$this->_getSession()->addException($e, $this->__('Cannot add the item to shopping cart.'));
				Mage::logException($e);
				$this->_goBack();
			}

		}

	}

	/**
	 * Delete shoping cart item action : includes items removed via ajax
	 */
	public function deleteAction()
	{
		$id = (int) $this->getRequest()->getParam('id');
		if ($id) {
			if($this->getRequest()->getParam('isAjax')) {

				try {
					$this->_getCart()->removeItem($id)
						->save();
				} catch (Exception $e) {
					$this->_getSession()->addError($this->__('Cannot remove the item.'));
					Mage::logException($e);
				}

				$response = array();

				$totalItemsInCart = Mage::helper('checkout/cart')->getCart()->getItemsCount();

				if($totalItemsInCart == 0) {

					$emptyCart = $this->getLayout()->createBlock('checkout/cart')->setTemplate('checkout/cart/noItems.phtml')->toHtml();

					$response['emptycart'] = $emptyCart;
					$response['cartitemqty'] = '00';
					$response['cartstatus'] = 'empty';

				} else {

					$cartTotals = $this->getLayout()->createBlock('checkout/cart_totals')->setTemplate('checkout/cart/totals.phtml')->toHtml();

					if($totalItemsInCart < 10) {
						$totalItemsInCart = '0' . $totalItemsInCart;
					}

					$response['carttotals'] = $cartTotals;
					$response['cartitemqty'] = $totalItemsInCart;
					$response['cartstatus'] = 'filled';

				}

				$response['productid'] = $id;

				$productName = $this->getRequest()->getParam('productName');
				$session = Mage::getSingleton('core/session');
				$cartMessage = array(
					'message' => $this->__('<b>%s</b> removed from your Carton!', $productName),
					'type'    => 'remove-success'
				);
				$session->setCustomCartActionMessage($cartMessage);
				$activityMessage = $this->getLayout()->createBlock('explore/explore')->setTemplate('checkout/cart/flag.phtml')->toHtml();

				$headerCartButtonHtml = $this->getLayout()->createBlock('core/template')->setTemplate('explore/header/cart_button.phtml')->toHtml();
				$response['header_cart'] = $headerCartButtonHtml;

				$response['activitymessage'] = $activityMessage;

				$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
				return;

			} else {

				try {
					$cartHelper = Mage::helper('checkout/cart');
					$items = $cartHelper->getCart()->getItems();
					foreach ($items as $item) {
						if ($item->getId() == $id) {
							$productName = $item->getName();
						}
					}
					$this->_getCart()->removeItem($id)
						->save();
					$this->_getSession()->addNotice($this->__('<b>%s</b> was removed from your cart!', $productName));
				} catch (Exception $e) {
					$this->_getSession()->addError($this->__('Cannot remove the item.'));
					Mage::logException($e);
				}

			}
		}
		$this->_redirectReferer(Mage::getUrl('*/*'));
	}

	/**
	 * Update quantity via AJAX
	 */
	public function ajaxUpdateAction() {

		$productId = $this->getRequest()->getParam('productid');
		// $activity = $this->getRequest()->getParam('activity');
		
		$qty = $this->getRequest()->getParam('productQty');;
		$response = array();

		$cartHelper = Mage::helper('checkout/cart');
		$items = $cartHelper->getCart()->getItems();
		foreach ($items as $item) {
			if ($item->getId() == $productId) {

				
				/*if($activity == 'increment') {
					$qty = $item->getQty() + 1;
				} elseif($activity == 'decrement') {
					$qty = $item->getQty() - 1;
				}*/

				// For Mobile
				$explicitQuantity = $this->getRequest()->getParam('quantity');
				if($explicitQuantity) {
					$qty = $explicitQuantity;
				}

				if($qty != 0) {
					$item->setQty($qty);
					$response['errorsign'] = 0;
				} else {
					$response['errorsign'] = 1;
					$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
					return;
				}
				$cartHelper->getCart()->save();

				if($this->getRequest()->getParam('referer') == 'checkout') {
					$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
					return;
				}

				if($qty > 10) {
					$qty = '0' . $qty;
				}
				$response['updatedqty'] = $qty;

				$productName = $this->getRequest()->getParam('productName');
				$session = Mage::getSingleton('core/session');
				$cartMessage = array(
					'message' => $this->__('<b>%s</b> quantity updated for your Carton!', $productName),
					'type'    => 'update-success'
				);
				$session->setCustomCartActionMessage($cartMessage);
				$activityMessage = $this->getLayout()->createBlock('explore/explore')->setTemplate('checkout/cart/flag.phtml')->toHtml();

				$response['activitymessage'] = $activityMessage;

				$cartTotals = $this->getLayout()->createBlock('checkout/cart_totals')->setTemplate('checkout/cart/totals.phtml')->toHtml();
				$response['carttotals'] = $cartTotals;

				$cartBlock = $this->getLayout()->createBlock('checkout/cart')->setCartTemplate('checkout/cart.phtml');
				$cartBlock->addItemRender('simple', 'checkout/cart_item_renderer', 'checkout/cart/item/default.phtml');
				$cartBlock->addItemRender('grouped', 'checkout/cart_item_renderer_grouped', 'checkout/cart/item/default.phtml');
				$cartBlock->addItemRender('configurable', 'checkout/cart_item_renderer_configurable', 'checkout/cart/item/default.phtml');

				$itemHtml = '';
				foreach($cartBlock->getItems() as $_item) {
					$itemHtml .= $cartBlock->getItemHtml($_item);
				}
				$response['cartitems'] = $itemHtml;
			}
		}

		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
		return;

	}

	/**
	 * Initialize coupon
	 */
	public function couponPostAction()
	{

		/**
		 * No reason continue with empty shopping cart
		 */
		if (!$this->_getCart()->getQuote()->getItemsCount()) {
			$this->_goBack();
			return;
		}

		$couponCode = (string) $this->getRequest()->getParam('coupon_code');
		if ($this->getRequest()->getParam('remove') == 1) {
			$couponCode = '';
		}
		$oldCouponCode = $this->_getQuote()->getCouponCode();

		if (!strlen($couponCode) && !strlen($oldCouponCode)) {
			$this->_goBack();
			return;
		}

		/* AJAX */
		$params = $this->getRequest()->getParams();

		/* Incorporate ICICI Coupon Codes | Start */
		try {
			$this->_getQuote()->getShippingAddress()->setCollectShippingRates(true);
			$this->_getQuote()->setCouponCode(strlen($couponCode) ? $couponCode : '')
				->collectTotals()
				->save();

			/* AJAX */
			if(array_key_exists('isAjax', $params)) {

				$response = array();

				if ($couponCode) {
					if ($couponCode == $this->_getQuote()->getCouponCode()) {
						$response['actionCode'] = 1; // Coupon applied successfully
					}
					else {
						$response['actionCode'] = 3; // Coupon code is invalid
					}
				}
				else {
					$response['actionCode'] = 2; // Coupon removed successfully
				}

				/* Reload coupon and totals blocks */
				if($response['actionCode'] != 3) {
					$cartTotals = $this->getLayout()->createBlock('checkout/cart_totals')->setTemplate('checkout/cart/totals.phtml')->toHtml();
					$response['carttotals'] = $cartTotals;
				}

				$discountBlock = $this->getLayout()->createBlock('checkout/cart_coupon')->setTemplate('checkout/cart/coupon.phtml')->toHtml();
				$response['discountblock'] = $discountBlock;

				$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
				return;

			} else {

				if ($couponCode) {
					if ($couponCode == $this->_getQuote()->getCouponCode()) {
						$this->_getSession()->addSuccess(
							$this->__('Coupon code "%s" was applied.', Mage::helper('core')->htmlEscape($couponCode))
						);
					}
					else {
						if (strpos($couponCode, 'HUICICI') === 0 AND $this->_getQuote()->getGrandTotal()) {
							$iciciCouponExists = Mage::getSingleton('salesrule/coupon')->load($couponCode, 'code');
							if (!$iciciCouponExists->getId()) {
								$this->_getSession()->addError(
									$this->__('Coupon code "%s" is not valid.', Mage::helper('core')->htmlEscape($couponCode))
								);
							}
							else {
								$this->_getSession()->addError(
									$this->__('Sorry, your Cart Total is less than Rs. 450. "%s" was not applied.', Mage::helper('core')->htmlEscape($couponCode))
								);
							}
						} else {
							$this->_getSession()->addError(
								$this->__('Coupon code "%s" is not valid.', Mage::helper('core')->htmlEscape($couponCode))
							);
						}
					}
				} else {
					$this->_getSession()->addSuccess($this->__('Coupon code was cancelled.'));
				}

			}

		} catch (Mage_Core_Exception $e) {
			$this->_getSession()->addError($e->getMessage());
		} catch (Exception $e) {
			$this->_getSession()->addError($this->__('Cannot apply the coupon code.'));
			Mage::logException($e);
		}
		/* Incorporate ICICI Coupon Codes | End */

		$this->_goBack();
	}
}
