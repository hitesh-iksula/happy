<?php



/**
 * Wallet Transact API
 */
class Wallet_Model_Api_Transact extends Varien_Object
{

    protected $_checksum = null;

    protected $_globalMap = array(
        // commands
        'email' => '',
        'amount' => '',
        'cell' => '',
        'orderid' => '',
        'mid' => '',
        'merchantname' => '',
        'redirecturl' => ''
    );

    protected $_mandatory = array(
        'email',
        'amount',
        'cell',
        'orderid',
        'mid',
        'merchantname',
        'redirecturl'
    );

    private function _validateFields($fields) 
    {
        
        foreach ($fields as $key=>$value) {
            if (in_array($key, $this->_mandatory) && !$value) {
                throw new Exception('Wallet requires the field ' . $key . ' to be mandatory.');
            }
        }        
    }

    private function _buildRequestFields() 
    {
        $fields = $this->_globalMap;
        $walletConfig = $this->getWalletConfig();
        $order = $this->getOrder();
        $order_data = $order->getData();
        $billingAddress = $this->getBillingAddress();
        $shippingAddress = $this->getShippingAddress();
        $amount = $this->_convertAmount($order->getGrandTotal(), $order->getOrderCurrencyCode());
        $currency = 'INR';
        // merchant identifier
        $fields = array_merge($this->_globalMap, array(
            'mid' => $walletConfig['merchant_id'],
            'orderid' => $order->getIncrementId(),
            'email' => $order->getCustomerEmail(),
            'cell' => $billingAddress->getTelephone(),
            'amount' => $amount,
			'merchantname' => $walletConfig['merchant_name'],
			'redirecturl'  => $walletConfig['redirect_url']
            
           
        ));      
		
        return $fields;
    }

    /**
     * Method to convert the amount to INR
     * @param decimal $amount
     * @param String $currency
     */
    private function _convertAmount($amount, $currencyCode) 
    {
        if ($currencyCode !== 'INR') {
            $amount = Mage::helper('directory')->currencyConvert($amount, $currencyCode, 'INR');
        }
		$amountInt = ($amount * 1);
        return $amountInt;
    }

    

    /**
     * Method to concatenate the fields into a string
     * using which the checksum will be creaeted
     * @param Array $fields
     * @param String
     */
    public function _concatFields($fields) 
    {
        unset($fields['checksum']);        
        return "'" . implode("'", $fields) . "'";
    }

    public function getRequestFields() 
    {
        $fields = $this->_buildRequestFields();
        // pass it through validate so that an exception is thrown
        $this->_validateFields($fields);		
        return $fields;
    }

    
}

