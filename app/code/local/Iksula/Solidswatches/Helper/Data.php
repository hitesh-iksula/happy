<?php
class Iksula_Solidswatches_Helper_Data extends Mage_Core_Helper_Abstract
{

	public function getItem($item) {

		$swatchCollection = Mage::getSingleton('solidswatches/solidswatches')->getCollection()
			->addFieldToFilter('option_id', array('eq' => $item))->getFirstItem();

		return $swatchCollection;

	}

	public function isSwatchConfigured($attributeCode) {

		$configuredSwatches = Mage::getStoreConfig('solidswatches/solidswatches_group/solidswatches_attributes');
		if(strpos($configuredSwatches, $attributeCode) >= 0) {
			return true;
		}
		return false;

	}

}
	 