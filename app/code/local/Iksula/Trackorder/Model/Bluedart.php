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
 */

class Iksula_Trackorder_Model_Bluedart extends Mage_Shipping_Model_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface {

    protected $_code = 'bluedart';

    public function collectRates(Mage_Shipping_Model_Rate_Request $request) {
       
        $result = Mage::getModel('shipping/rate_result');
        $show = true;
        if ($show) { // This if condition is just to demonstrate how to return success and error in shipping methods
            $method = Mage::getModel('shipping/rate_result_method');
            $method->setCarrier($this->_code);
            $method->setMethod($this->_code);
            $method->setCarrierTitle(Mage::getStoreConfig('bluedart/bluedart_general/title'));
            $method->setMethodTitle(Mage::getStoreConfig('bluedart/bluedart_general/name'));
            $method->setPrice(Mage::getStoreConfig('bluedart/bluedart_general/handling_fee'));
            $method->setCost(Mage::getStoreConfig('bluedart/bluedart_general/handling_fee'));
            $result->append($method);
        } else {
            $error = Mage::getModel('shipping/rate_result_error');
            $error->setCarrier($this->_code);
            $error->setCarrierTitle(Mage::getStoreConfig('bluedart/bluedart_general/name'));
            $error->setErrorMessage(Mage::getStoreConfig('bluedart/bluedart_general/specificerrmsg'));
            $result->append($error);
        }
        return $result;
    }

    /**
     * Get allowed shipping methods
     *
     * @return array
     */
    public function getAllowedMethods() {
        //we only have one method so just return the name from the admin panel
        return array('bluedart' => Mage::getStoreConfig('bluedart/bluedart_general/title'));
    }

    public function isTrackingAvailable() {
        return true;
    }

    public function getTrackingInfo($tracking_number) {
        $tracking_result = $this->getTracking($tracking_number);

        if ($tracking_result instanceof Mage_Shipping_Model_Tracking_Result) {
            if ($trackings = $tracking_result->getAllTrackings()) {
                return $trackings[0];
            }
        } elseif (is_string($tracking_result) && !empty($tracking_result)) {
            return $tracking_result;
        }

        return false;
    }

    public function getTracking($tracking_number) {
        $tracking_result = Mage::getModel('shipping/tracking_result');

        $tracking_status = Mage::getModel('shipping/tracking_result_status');
        $tracking_status->setCarrier($this->_code);
        $tracking_status->setCarrierTitle(Mage::getStoreConfig('bluedart/bluedart_general/carrier_title'));
        $tracking_status->setTracking($tracking_number);
        //Getting xml of shippment by curl
        $path = Mage::getStoreConfig('bluedart/bluedart_general/gateway_url');
        //$loginid = $this->getConfigData('login_id'); 
        $loginid = Mage::getStoreConfig('bluedart/bluedart_general/login_id');
        //$lickey = $this->getConfigData('license_key');
        $lickey = Mage::getStoreConfig('bluedart/bluedart_general/license_key');
        
        $path .= "&action=custawbquery&loginid=$loginid&awb=awb&numbers=$tracking_number&format=xml&lickey=$lickey&verno=1.3&scan=0";
		
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $path);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        $retValue = curl_exec($ch);
        curl_close($ch);
        $xmlobject = new SimpleXMLElement($retValue);
            $xmlarray = Mage::helper('trackorder/data')->simpleXMLToArray($xmlobject);
        try {
            $xmlobject = new SimpleXMLElement($retValue);
            $xmlarray = Mage::helper('trackorder/data')->simpleXMLToArray($xmlobject);
            return $xmlarray;
            //echo "<pre>";print_r($xmlarray);echo "</pre>";
            $status = "";

            if (isset($xmlarray['Shipment'])) {
                foreach ((array) $xmlarray['Shipment'] as $k => $v) {
                    $status .= "<strong>" . $k . "</strong>: " . $v . "<br/>";
                }
            }
        } catch (Exception $e) {
            Mage::logException($e);
            $status = "Something went wrong while getting tracking information";
        }
        // $tracking_status->addData(
        //         array(
        //             'status' => $status
        //         )
        // );
        // $tracking_result->append($tracking_status);
        
        //  return $tracking_result;
    }

}
?>
