<?php

class Iksula_Explore_Block_Gift_Collection extends Mage_Catalog_Block_Product_List {

	protected function _getProductCollection() {

		$attributes = $this->getRequest()->getParams();
		
		$collection = parent::_getProductCollection();

		$collection->clear();
		$collection->setPageSize(2000);

		$rootCategory = Mage::getModel('catalog/category')->load(5);

		$collection = Mage::getResourceModel('catalog/product_collection')->addCategoryFilter($rootCategory) ;
		$collection->setVisibility(Mage::getSingleton('catalog/product_visibility')->getVisibleInCatalogIds());

		$collection = $this->_addProductAttributesAndPrices($collection);

		$collection->addAttributeToFilter('status', array('eq' => 1));
		$collection->addAttributeToFilter('visibility', array('eq' => 4));

		foreach($attributes as $attribute => $value) {
			if($value) {
				$collection->addAttributeToFilter('explore_' . $attribute, array('finset' => (int)substr($value, 3)));
			}
		}

		$collection->setOrder('name', 'ASC');
		
		$collection->load();

		$this->setProductCollection($collection);
		return $collection;
		
	}

}
