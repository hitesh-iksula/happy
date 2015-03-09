<?php

class Iksula_Explore_Block_Folofo_Collection extends Mage_Catalog_Block_Product_List {

	protected function _getProductCollection() {

		$collection = parent::_getProductCollection();

		$collection->setPageSize(3);

		$this->setProductCollection($collection);
		return $collection;

	}

}
