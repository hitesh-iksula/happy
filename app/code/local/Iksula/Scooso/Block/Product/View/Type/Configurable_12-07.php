<?php

class Iksula_Scooso_Block_Product_View_Type_Configurable  extends Mage_Catalog_Block_Product_View_Type_Configurable
{
	
	//Rewrite getAllowProducts() function:
	public function getAllowProducts()
	{
		if (!$this->hasAllowProducts()) {
			$products = array();
			$allProducts = $this->getProduct()->getTypeInstance(true)
				->getUsedProducts(null, $this->getProduct());
			foreach ($allProducts as $product) {
				$products[] = $product;
			}
			$this->setAllowProducts($products);
		}
		return $this->getData('allow_products');
	}

	public function getJsonConfig()
	{
		$attributes = array();
		$options    = array();
		$store      = $this->getCurrentStore();
		$taxHelper  = Mage::helper('tax');
		$currentProduct = $this->getProduct();
 
		$preconfiguredFlag = $currentProduct->hasPreconfiguredValues();
		if ($preconfiguredFlag) {
			$preconfiguredValues = $currentProduct->getPreconfiguredValues();
			$defaultValues       = array();
		}
 
		foreach ($this->getAllowProducts() as $product) {
			$productId  = $product->getId();
 
			foreach ($this->getAllowAttributes() as $attribute) {
				$productAttribute   = $attribute->getProductAttribute();
				$productAttributeId = $productAttribute->getId();
				$attributeValue     = $product->getData($productAttribute->getAttributeCode());
				if (!isset($options[$productAttributeId])) {
					$options[$productAttributeId] = array();
				}
 
				if (!isset($options[$productAttributeId][$attributeValue])) {
					$options[$productAttributeId][$attributeValue] = array();
				}
				$options[$productAttributeId][$attributeValue][] = $productId;
			}
		}
 
		$this->_resPrices = array(
			$this->_preparePrice($currentProduct->getFinalPrice())
		);
 
		foreach ($this->getAllowAttributes() as $attribute) {
			$productAttribute = $attribute->getProductAttribute();
			$attributeId = $productAttribute->getId();
			$info = array(
			   'id'        => $productAttribute->getId(),
			   'code'      => $productAttribute->getAttributeCode(),
			   'label'     => $attribute->getLabel(),
			   'options'   => array()
			);
 
			$optionPrices = array();
			$prices = $attribute->getPrices();
			if (is_array($prices)) {
				foreach ($prices as $value) {
					if(!$this->_validateAttributeValue($attributeId, $value, $options)) {
						continue;
					}
					$currentProduct->setConfigurablePrice(
						$this->_preparePrice($value['pricing_value'], $value['is_percent'])
					);
					$currentProduct->setParentId(true);
					Mage::dispatchEvent(
						'catalog_product_type_configurable_price',
						array('product' => $currentProduct)
					);
					$configurablePrice = $currentProduct->getConfigurablePrice();
 
					if (isset($options[$attributeId][$value['value_index']])) {
						$productsIndex = $options[$attributeId][$value['value_index']];
					} else {
						$productsIndex = array();
					}

					$stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($productsIndex[0]);
					// Mage::log($stockItem);
					// Mage::log('start');
					// Mage::log($productsIndex);
					// Mage::log('end');
 
					$info['options'][] = array(
						'id'        => $value['value_index'],
						// 'label'     => ($stockItem->getQty() <= 0) ? $value['label'] . ' (out of stock) ' : $value['label'],
						'label'     => ($options['qty'][$value['label']] <= 0) ? $value['label'] . ' (out of stock) ' : $value['label'],
						// 'label'     => $value['label'],
						'price'     => $configurablePrice,
						'oldPrice'  => $this->_preparePrice($value['pricing_value'], $value['is_percent']),
						'products'  => $productsIndex,
					);
					$optionPrices[] = $configurablePrice;
					// Mage::log($info);
				}
			}
			/**
			 * Prepare formated values for options choose
			 */
			foreach ($optionPrices as $optionPrice) {
				foreach ($optionPrices as $additional) {
					$this->_preparePrice(abs($additional-$optionPrice));
				}
			}
			if($this->_validateAttributeInfo($info)) {
			   $attributes[$attributeId] = $info;
			}
 
			// Add attribute default value (if set)
			if ($preconfiguredFlag) {
				$configValue = $preconfiguredValues->getData('super_attribute/' . $attributeId);
				if ($configValue) {
					$defaultValues[$attributeId] = $configValue;
				}
			}
		}
 
		$taxCalculation = Mage::getSingleton('tax/calculation');
		if (!$taxCalculation->getCustomer() && Mage::registry('current_customer')) {
			$taxCalculation->setCustomer(Mage::registry('current_customer'));
		}
 
		$_request = $taxCalculation->getRateRequest(false, false, false);
		$_request->setProductClassId($currentProduct->getTaxClassId());
		$defaultTax = $taxCalculation->getRate($_request);
 
		$_request = $taxCalculation->getRateRequest();
		$_request->setProductClassId($currentProduct->getTaxClassId());
		$currentTax = $taxCalculation->getRate($_request);
 
		$taxConfig = array(
			'includeTax'        => $taxHelper->priceIncludesTax(),
			'showIncludeTax'    => $taxHelper->displayPriceIncludingTax(),
			'showBothPrices'    => $taxHelper->displayBothPrices(),
			'defaultTax'        => $defaultTax,
			'currentTax'        => $currentTax,
			'inclTaxTitle'      => Mage::helper('catalog')->__('Incl. Tax')
		);
 
		$config = array(
			'attributes'        => $attributes,
			'template'          => str_replace('%s', '#{price}', $store->getCurrentCurrency()->getOutputFormat()),
			'basePrice'         => $this->_registerJsPrice($this->_convertPrice($currentProduct->getFinalPrice())),
			'oldPrice'          => $this->_registerJsPrice($this->_convertPrice($currentProduct->getPrice())),
			'productId'         => $currentProduct->getId(),
			'chooseText'        => Mage::helper('catalog')->__('Choose an Option...'),
			'taxConfig'         => $taxConfig
		);
 
		if ($preconfiguredFlag && !empty($defaultValues)) {
			$config['defaultValues'] = $defaultValues;
		}
 
		$config = array_merge($config, $this->_getAdditionalConfig());
		// Mage::log($config);
 
		return Mage::helper('core')->jsonEncode($config);
	}

	/**
     * Composes configuration for js
     *
     * @return string
     */
    public function ORIGINALgetJsonConfig()
    {
        $attributes = array();
        $options    = array();
        $store      = $this->getCurrentStore();
        $taxHelper  = Mage::helper('tax');
        $currentProduct = $this->getProduct();

        $preconfiguredFlag = $currentProduct->hasPreconfiguredValues();
        if ($preconfiguredFlag) {
            $preconfiguredValues = $currentProduct->getPreconfiguredValues();
            $defaultValues       = array();
        }

        foreach ($this->getAllowProducts() as $product) {
            $productId  = $product->getId();

            foreach ($this->getAllowAttributes() as $attribute) {
                $productAttribute   = $attribute->getProductAttribute();
                $productAttributeId = $productAttribute->getId();
                $attributeValue     = $product->getData($productAttribute->getAttributeCode());
                if (!isset($options[$productAttributeId])) {
                    $options[$productAttributeId] = array();
                }

                if (!isset($options[$productAttributeId][$attributeValue])) {
                    $options[$productAttributeId][$attributeValue] = array();
                }
                $options[$productAttributeId][$attributeValue][] = $productId;
            }
        }

        $this->_resPrices = array(
            $this->_preparePrice($currentProduct->getFinalPrice())
        );

        foreach ($this->getAllowAttributes() as $attribute) {
            $productAttribute = $attribute->getProductAttribute();
            $attributeId = $productAttribute->getId();
            $info = array(
               'id'        => $productAttribute->getId(),
               'code'      => $productAttribute->getAttributeCode(),
               'label'     => $attribute->getLabel(),
               'options'   => array()
            );

            $optionPrices = array();
            $prices = $attribute->getPrices();
            if (is_array($prices)) {
                foreach ($prices as $value) {
                    if(!$this->_validateAttributeValue($attributeId, $value, $options)) {
                        continue;
                    }
                    $currentProduct->setConfigurablePrice(
                        $this->_preparePrice($value['pricing_value'], $value['is_percent'])
                    );
                    $currentProduct->setParentId(true);
                    Mage::dispatchEvent(
                        'catalog_product_type_configurable_price',
                        array('product' => $currentProduct)
                    );
                    $configurablePrice = $currentProduct->getConfigurablePrice();

                    if (isset($options[$attributeId][$value['value_index']])) {
                        $productsIndex = $options[$attributeId][$value['value_index']];
                    } else {
                        $productsIndex = array();
                    }

                    $info['options'][] = array(
                        'id'        => $value['value_index'],
                        'label'     => $value['label'],
                        'price'     => $configurablePrice,
                        'oldPrice'  => $this->_preparePrice($value['pricing_value'], $value['is_percent']),
                        'products'  => $productsIndex,
                    );
                    $optionPrices[] = $configurablePrice;
                    //$this->_registerAdditionalJsPrice($value['pricing_value'], $value['is_percent']);
                }
            }
            /**
             * Prepare formated values for options choose
             */
            foreach ($optionPrices as $optionPrice) {
                foreach ($optionPrices as $additional) {
                    $this->_preparePrice(abs($additional-$optionPrice));
                }
            }
            if($this->_validateAttributeInfo($info)) {
               $attributes[$attributeId] = $info;
            }

            // Add attribute default value (if set)
            if ($preconfiguredFlag) {
                $configValue = $preconfiguredValues->getData('super_attribute/' . $attributeId);
                if ($configValue) {
                    $defaultValues[$attributeId] = $configValue;
                }
            }
        }

        $taxCalculation = Mage::getSingleton('tax/calculation');
        if (!$taxCalculation->getCustomer() && Mage::registry('current_customer')) {
            $taxCalculation->setCustomer(Mage::registry('current_customer'));
        }

        $_request = $taxCalculation->getRateRequest(false, false, false);
        $_request->setProductClassId($currentProduct->getTaxClassId());
        $defaultTax = $taxCalculation->getRate($_request);

        $_request = $taxCalculation->getRateRequest();
        $_request->setProductClassId($currentProduct->getTaxClassId());
        $currentTax = $taxCalculation->getRate($_request);

        $taxConfig = array(
            'includeTax'        => $taxHelper->priceIncludesTax(),
            'showIncludeTax'    => $taxHelper->displayPriceIncludingTax(),
            'showBothPrices'    => $taxHelper->displayBothPrices(),
            'defaultTax'        => $defaultTax,
            'currentTax'        => $currentTax,
            'inclTaxTitle'      => Mage::helper('catalog')->__('Incl. Tax')
        );

        $config = array(
            'attributes'        => $attributes,
            'template'          => str_replace('%s', '#{price}', $store->getCurrentCurrency()->getOutputFormat()),
//            'prices'          => $this->_prices,
            'basePrice'         => $this->_registerJsPrice($this->_convertPrice($currentProduct->getFinalPrice())),
            'oldPrice'          => $this->_registerJsPrice($this->_convertPrice($currentProduct->getPrice())),
            'productId'         => $currentProduct->getId(),
            'chooseText'        => Mage::helper('catalog')->__('Choose an Option...'),
            'taxConfig'         => $taxConfig
        );

        if ($preconfiguredFlag && !empty($defaultValues)) {
            $config['defaultValues'] = $defaultValues;
        }

        $config = array_merge($config, $this->_getAdditionalConfig());

        return Mage::helper('core')->jsonEncode($config);
    }

}