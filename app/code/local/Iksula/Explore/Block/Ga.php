<?php

class Iksula_Explore_Block_Ga extends Mage_GoogleAnalytics_Block_Ga
{

    /**
     * Render GA tracking scripts
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!Mage::helper('googleanalytics')->isGoogleAnalyticsAvailable()) {
            return '';
        }
        $accountId = Mage::getStoreConfig(Mage_GoogleAnalytics_Helper_Data::XML_PATH_ACCOUNT);
        return '

    <!-- BEGIN GOOGLE ANALYTICS CODE -->
    <script type="text/javascript">
    //<![CDATA[


    (function(i,s,o,g,r,a,m){i[\'GoogleAnalyticsObject\']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,\'script\',\'//www.google-analytics.com/analytics.js\',\'ga\');

    ' . $this->_getPageTrackingCode($accountId) . '
    ' . $this->_getOrdersTrackingCode() . '

    //]]>
    </script>
    <!-- END GOOGLE ANALYTICS CODE -->';
    }

    protected function _getPageTrackingCode($accountId)
    {
        $pageName   = trim($this->getPageName());
        $optPageURL = '';
        if ($pageName && preg_match('/^\/.*/i', $pageName)) {
            $optPageURL = ", '{$this->jsQuoteEscape($pageName)}'";
        }
        return "
        ga('create', '{$this->jsQuoteEscape($accountId)}','auto');
        ga('require','displayfeatures');
        ga('send', 'pageview');
        ";
    }

    protected function _getOrdersTrackingCode()
    {
        $orderIds = $this->getOrderIds();
        if (empty($orderIds) || !is_array($orderIds)) {
            return;
        }
        $collection = Mage::getResourceModel('sales/order_collection')
            ->addFieldToFilter('entity_id', array('in' => $orderIds))
        ;
        $result = array("ga('require', 'ecommerce');");

        foreach ($collection as $order) {
            if ($order->getIsVirtual()) {
                $address = $order->getBillingAddress();
            } else {
                $address = $order->getShippingAddress();
            }

            $result[] = "ga('ecommerce:addTransaction', {
                'id': '".$order->getIncrementId()."',
                'affiliation': '".$this->jsQuoteEscape(Mage::app()->getStore()->getFrontendName())."',
                'revenue': '".$order->getBaseGrandTotal()."',
                'shipping': '".$order->getBaseShippingAmount()."',
                'tax': '".$order->getBaseTaxAmount()."'
            });
            ";

            foreach ($order->getAllVisibleItems() as $item) {
                $result[] = "ga('ecommerce:addItem', {
                    'id': '".$order->getIncrementId()."',
                    'name': '".$this->jsQuoteEscape($item->getName())."',
                    'sku': '".$this->jsQuoteEscape($item->getSku())."',
                    'category': '',
                    'price': '".$item->getBasePrice()."',
                    'quantity': '".$item->getQtyOrdered()."'
                });
            ";
            }

            $result[] = "ga('ecommerce:send');";
        }
        return implode("\n", $result);
    }

}
