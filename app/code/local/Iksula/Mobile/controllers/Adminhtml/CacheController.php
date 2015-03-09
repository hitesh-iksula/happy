<?php

class Iksula_Mobile_Adminhtml_CacheController extends Mage_Adminhtml_Controller_Action
{

	/**
     * Flush Mobile Homepage Product Cache
     *
     * @return 1
     */
    public function flushAction()
    {
        $cache = Mage::app()->getCache();

        $cacheId = 'getHomepageProductsBlockAction';
		if (false !== ($html = $cache->load($cacheId))) {
			$cache->remove($cacheId);
		}
		$result = 1;

        Mage::app()->getResponse()->setBody($result);
    }

}
