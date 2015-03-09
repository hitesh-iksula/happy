<?php
/**
 * 
 * BSchool Campaign
 * 
 * This block file overrides "Mage_Checkout_Block_Cart"
 * 
 * Purpose is to add a function to get a marker if a bundled product is present in the customer's cart
 * If so, the customer will be redirected to a custom page before the Checkout page
 * 
 * 
 * 
 * 
 * @category    Mage
 * @package     Mage_Checkout
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Shopping cart block
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Iksula_AdvancedBundle_Block_Cart extends Mage_Checkout_Block_Cart
{
    /**
     * Prepare Quote Item Product URLs
     *
     */
    public function __construct()
    {
        parent::__construct();
        // $this->prepareItemUrls();
    }

    public function returnBundleExistence() {
        $sessionCustom = Mage::getSingleton('checkout/session');
        $cartHasBundleProduct = 0;
        foreach($sessionCustom->getQuote()->getAllItems() as $item) {
            if($item->getData('product_type') == 'bundle') {
                $cartHasBundleProduct = 1; break;
            }
        }
        return $cartHasBundleProduct;
    }
}
