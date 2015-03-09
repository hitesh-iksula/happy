<?php
/**
 * Magento
 *
 * This block overrides the default CatalogSearch Result block for the sake of accurate results.
 *
 * @category    Mage
 * @package     Iksula_Override
 * @copyright   Iksula
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Product search result block
 *
 * @category   Mage
 * @package    Iksula_Override
 * @module     Override
 */
class Iksula_Override_Block_Result extends Mage_CatalogSearch_Block_Result
{
    /**
     * Set Search Result collection
     *
     * @return Mage_CatalogSearch_Block_Result
     */
    public function setListCollection()
    {
       $this->getListBlock()
          ->setCollection($this->_getProductCollection());
       return $this;
    }

    /**
     * Retrieve loaded category collection
     *
     * @return Mage_CatalogSearch_Model_Resource_Fulltext_Collection
     */
    protected function _getProductCollection()
    {
        if (is_null($this->_productCollection)) {
            // $this->_productCollection = $this->getListBlock()->getLoadedProductCollection();
            $this->_productCollection = Mage::getSingleton('catalogsearch/layer')->getProductCollection();
        }

        return $this->_productCollection;
    }

}
