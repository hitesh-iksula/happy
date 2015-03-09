<?php
/**
 * 
 * 
 * Explore Block
 * @author Hitesh Pachpor
 * 
 * 
 */
?>
<?php

class Iksula_Explore_Block_Explore extends Mage_Core_Block_Template {
	
	/**
	 * 
	 * 
	 * this function returns the categories to be shown on the Explore page
	 * @param none
	 * @return all child categories of the category chosen in the back-end
	 * 
	 * 
	 */
	public function getCategories() {

		$parentId = Mage::getStoreConfig('explore/explore_group/explore_category', Mage::app()->getStore());
		$childCategories = array();
		$children = Mage::getSingleton('catalog/category')->getCategories($parentId, 1, true, true);
		foreach ($children as $category) {		
		    $childCategories[$category->getPosition()] = $category->getId();
		}
		ksort($childCategories);

		return $childCategories;

	}

	/**
	 * 
	 * 
	 * this function returns the categories to be shown on the Explore page
	 * @param none
	 * @return all child categories of the category chosen in the back-end
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
	 * this function returns a dynamically created product-list for the category id provided
	 * @param category ID
	 * @return HTML string of the product list
	 * 
	 * 
	 */
	public function getCategoryProducts($childCategory) {

		if(!$childCategory) {
			$childCategory = $this->getHomepageCategory();
		}

		$category = Mage::getSingleton('catalog/category')->load($childCategory);
		
		if($category->getData('is_active') == '1') {

			$category = $childCategory;

			$block = $this->getLayout()->createBlock('catalog/product_list');
			$block->setTemplate('explore/product/list.phtml')->setData('category_id', $childCategory);

			$block2 = $this->getLayout()->createBlock('core/template')->setTemplate('explore/product/category.phtml');

			$block->setChild('category_header', $block2);

			return $block->toHtml();
			
		}

	}

	/**
	 * 
	 * 
	 * get child categories of the given category
	 * @param category ID
	 * @return child categories
	 * 
	 * 
	 */
	public function getChildCategories($categoryId) {

		if(!$categoryId) {
			return array();
		}
		
		$childCategories = Mage::getSingleton('catalog/category')->getCategories($categoryId);
		
		if($childCategories) {
			return $childCategories;
		}

	}

	/**
	 * 
	 * 
	 * this function returns true or false if a given url parameter is present in the url
	 * @param url parameter
	 * @return true / false
	 * 
	 * 
	 */
	public function getUrlParam($param) {

		$value = $this->getRequest()->getParam($param);
		if($value) {
			return $value;
		}
		return false;

	}

	/**
	 * 
	 * 
	 * this function returns product ID from the given URL key
	 * @param url parameter
	 * @return true / false
	 * 
	 * 
	 */
	public function getProductId($urlKey) {

		$productId = Mage::getModel('core/url_rewrite')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->loadByRequestPath($urlKey)
            ->getProductId();
		return $productId;

	}

}