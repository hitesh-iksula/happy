<?php
class Happilyunmarried_Confusedshopper_Block_List extends Mage_Catalog_Block_Product_List
{
	/**
     * Retrieve loaded category collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected function _getProductCollection()
    {	
		if (is_null($this->_productCollection)) {
            $layer = $this->getLayer();
            /* @var $layer Mage_Catalog_Model_Layer */
            if ($this->getShowRootCategory()) {
                $this->setCategoryId(Mage::app()->getStore()->getRootCategoryId());
            }

            // if this is a product view page
            if (Mage::registry('product')) {
                // get collection of categories this product is associated with
                $categories = Mage::registry('product')->getCategoryCollection()
                    ->setPage(1, 1)
                    ->load();
                // if the product is associated with any category
                if ($categories->count()) {
                    // show products from this category
                    $this->setCategoryId(current($categories->getIterator()));
                }
            }

            $origCategory = null;
            if ($this->getCategoryId()) {
                $category = Mage::getModel('catalog/category')->load($this->getCategoryId());
                if ($category->getId()) {
                    $origCategory = $layer->getCurrentCategory();
					
					$layer->setCurrentCategory($category);
                }
            }
            $this->_productCollection = $layer->getProductCollection();

            $this->prepareSortableFieldsByCategory($layer->getCurrentCategory());

            if ($origCategory) {
                $layer->setCurrentCategory($origCategory);
            }
        }
		
		/* EDIT START */
		// Get confused filters
		$confused = $this->htmlEscape(Mage::app()->getRequest()->getParam('confused'));
		if($confused != '') {
			$filters = explode(',',$confused);
			if(count($filters) > 0) {
				// Traverse filters
				$_product = Mage::getModel('catalog/product');
				foreach($filters as $filter) {
					$filter_array = explode('=', trim($filter));

					// Check for price filter
					if($filter_array[0] == 'price') {
						switch($filter_array[1]) {
							case 'cheap':
								$this->_productCollection->addFieldToFilter(array(
									array('attribute' => 'price', 'lt' => '100')
								));
								break;
							case 'lt500':
								$this->_productCollection->addFieldToFilter(array(
									array('attribute' => 'price', 'lt' => '500')
								));
								break;
							case '500-1000':
								$this->_productCollection->addFieldToFilter(array(
									array('attribute' => 'price', 'gt' => '500', 'lt' => '1000')
								));
								break;
						}
					}
					// Not price filter
					else {
						$_attributes = Mage::getResourceModel('eav/entity_attribute_collection')
							->setEntityTypeFilter($_product->getResource()->getTypeId())
							->addFieldToFilter('attribute_code', $filter_array[0]);
						
						$_attribute = $_attributes->getFirstItem()->setEntity($_product->getResource());
						$attribute_options = $_attribute->getSource()->getAllOptions(false);
						
						foreach($attribute_options as $options) {
							if($options['label'] == $filter_array[1])
								$this->_productCollection->addAttributeToFilter($filter_array[0], $options['value']);
						}
					}
				}
			}
		}
		/* EDIT END */

        return $this->_productCollection;
    }
}
