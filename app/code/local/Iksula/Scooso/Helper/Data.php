<?php

class Iksula_Scooso_Helper_Data extends Mage_Core_Helper_Abstract {
	
	/**
	 * 
	 * 
	 * This function gets called when we want to check quantity of an associated simple product inside a configurable product.
	 * 
	 * @param configurable product id, attribute codes and their values via POST or GET
	 * @return json array of stock status of all options
	 * 
	 */
	public function PrequantityAction($productId) {

		// $params = $this->getRequest()->getParams();
		// $productId = $params['pid'];
		$_product = Mage::getSingleton('catalog/product')->load($productId);

		$_allSubProducts = $_product->getTypeInstance(true)->getUsedProducts(null, $_product);

		$_attributes = $_product->getTypeInstance(true)->getConfigurableAttributes($_product);

		$attributeOptions = array();
		foreach($_allSubProducts as $_subProduct) {

			$_subProductId = $_subProduct->getId();

			$n = 0;

			if($_subProduct->isSaleable()) {
				$qty = 1;
			} else {
				$qty = 0;
			}
			$attributeString = '';
			foreach($_attributes as $attribute) {

				$attributeCode = $attribute->getProductAttribute()->getAttributeCode();
				$attributeId = $attribute->getProductAttribute()->getAttributeId();

				// $attributeOptions[$attributeId][$_subProductId] = $qty;
				$attributeString = $attributeString . "-" . $_subProduct->getData($attributeCode);

			}
			$attributeString = substr($attributeString, 1);

			$attributeOptions[$attributeString] = $qty;

		}

		return json_encode($attributeOptions);

		// return false;

	}

}