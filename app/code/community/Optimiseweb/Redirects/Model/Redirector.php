<?php

/**
 * Optimiseweb Redirects Model Redirector
 *
 * @package     Optimiseweb_Redirects
 * @author      Kathir Vel (sid@optimiseweb.co.uk)
 * @copyright   Copyright (c) 2013 Optimise Web Limited
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Optimiseweb_Redirects_Model_Redirector {

    /**
     * Redirect Function
     *
     * Looks at 404 pages and then loads up the csv file to see if a match exists
     *
     * @param Varien_Event_Observer $observer
     */
    public function doRedirects(Varien_Event_Observer $observer) {
        $request = $observer->getEvent()->getControllerAction()->getRequest();

        $actionName = $request->getActionName();
        $requestUrl = rtrim($request->getScheme() . '://' . $request->getHttpHost() . $request->getRequestUri(), '/');

        if ($actionName == 'noRoute') {
            if (Mage::getStoreConfig('optimisewebredirects/general/upload') AND file_exists(Mage::getBaseDir('media') . '/optimiseweb/redirects/' . Mage::getStoreConfig('optimisewebredirects/general/upload'))) {
                $redirectLines = file(Mage::getBaseDir('media') . '/optimiseweb/redirects/' . Mage::getStoreConfig('optimisewebredirects/general/upload'));

                foreach ($redirectLines AS $redirectLine) {
                    $sourceDestination = explode(';', $redirectLine);

                    $sourceUrl = rtrim(trim($sourceDestination[0]), '/');

                    if (count($sourceDestination) == 2) {
                        $destinationUrl = trim($sourceDestination[1]);

                        if ($sourceUrl == $requestUrl) {
                            $response = Mage::app()->getResponse();
                            $response->setRedirect($destinationUrl, 301);
                            $response->sendResponse();
                            exit;
                        }
                        continue;
                    }
                }
            }
        }
    }

}