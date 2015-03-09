<?php

class IWD_OnepageCheckout_Block_Onepage_Billship extends Mage_Checkout_Block_Onepage_Billing
{
    public function getBillAddress()
    {
        return $this->getQuote()->getBillingAddress();
    }

    public function getShipAddress()
    {
        return $this->getQuote()->getShippingAddress();
    }

    public function getCustomerBillAddr($format_type)
    {
    	if($format_type == null OR $format_type == '') {
            $format_type = 'select';
        }
        return $this->buildCustomerAddress('billing', $format_type);
    }

    public function getBillingCountriesSelectBox()
    {
    	return $this->buildCountriesSelectBox('billing');
    }

    public function getCustomerShipAddr()
    {
    	return $this->buildCustomerAddress('shipping');
    }

    public function getShippingCountriesSelectBox()
    {
    	return $this->buildCountriesSelectBox('shipping');
    }

    public function buildCustomerAddress($addr_type, $format_type = 'select')
    {
        if ($this->isCustomerLoggedIn()) {

            $address_format = 'oneline';
            if($format_type != 'select') {
                $address_format = 'html';
            }

            $options = array();
            foreach ($this->getCustomer()->getAddresses() as $address) {
                $options[] = array(
                    'value'=>$address->getId(),
                    'label'=>$address->format($address_format)
                );
            }

        	switch($addr_type)
        	{
        		case 'billing':
        			$address = $this->getCustomer()->getPrimaryBillingAddress();
        			break;
        		case 'shipping':
        			$address = $this->getCustomer()->getPrimaryShippingAddress();
        			break;
        	}

            if ($address) {
                $addressId = $address->getId();
            } else {
            	if($addr_type == 'billing')
            		$obj	= $this->getBillAddress();
            	else
            		$obj	= $this->getShipAddress();

                $addressId = $obj->getId();
            }

            if($format_type != 'select') {
                return $options;
            }

            $select = $this->getLayout()->createBlock('core/html_select')
            							->setId("{$addr_type}_customer_address")->setName("{$addr_type}_address_id")
            							->setValue('')->setOptions($options)
										->setExtraParams('onchange="'.$addr_type.'.newAddress(!this.value)"')
										->setClass('customer_address');

            $select->addOption('', Mage::helper('onepagecheckout')->__('New Address'));
            return $select->getHtml();
        }
        return '';
    }

    public function buildCountriesSelectBox($addr_type)
    {
		if($addr_type == 'billing')
			$obj	= $this->getBillAddress();
		else
			$obj	= $this->getShipAddress();

        $countryId = $obj->getCountryId();
        if (is_null($countryId)) {
            $countryId = Mage::getStoreConfig('general/country/default');
        }
        $select = $this->getLayout()->createBlock('core/html_select')
        							->setId("{$addr_type}:country_id")->setName("{$addr_type}[country_id]")
									->setValue($countryId)->setOptions($this->getCountryOptions())
									->setTitle(Mage::helper('onepagecheckout')->__('Country'))
									->setClass('validate-select');

		if($addr_type == 'shipping')
			$select->setExtraParams('onchange="shipping.setSameAsBilling(false);"');

        return $select->getHtml();
    }
}
