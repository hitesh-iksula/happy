<?php

class Iksula_Mobile_Helper_Data extends Mage_Core_Helper_Abstract
{

	public function getUrlFromThumbnailAttr($value) {
		if(!$value) {
			$url = Mage::getUrl('media/misc') . 'blank.png';
		} else {
			$url = Mage::getUrl('media/catalog/category') . $value;
		}
		return $url;
	}

}

