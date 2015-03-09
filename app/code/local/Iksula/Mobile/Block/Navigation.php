<?php

class Iksula_Mobile_Block_Navigation extends Mage_Catalog_Block_Navigation {

	/**
	 * Render category to html
	 *
	 * @param Mage_Catalog_Model_Category $category
	 * @param int Nesting level number
	 * @param boolean Whether ot not this item is last, affects list item class
	 * @param boolean Whether ot not this item is first, affects list item class
	 * @param boolean Whether ot not this item is outermost, affects list item class
	 * @param string Extra class of outermost list items
	 * @param string If specified wraps children list in div with this class
	 * @param boolean Whether ot not to add on* attributes to list item
	 * @return string
	 */

	public $count = 1;

	protected function _renderCategoryMenuItemHtml($category, $level = 0, $isLast = false, $isFirst = false,
		$isOutermost = false, $outermostItemClass = '', $childrenWrapClass = '', $noEventAttributes = false)
	{
		$categoryModel = Mage::getModel('catalog/category')->load($category->getId());
		$categorychooser = $categoryModel->getCategorychooser();

		if (!$category->getIsActive()) {
			return '';
		}

		$html  = array();

		if($categorychooser != '1' && $categorychooser != '3') {
			if(isset($categorychooser)) {
				return '';
			}
		}


		// get all children
		// If Flat Data enabled then use it but only on frontend
		$flatHelper = Mage::helper('catalog/category_flat');
		if ($flatHelper->isAvailable() && $flatHelper->isBuilt(true) && !Mage::app()->getStore()->isAdmin()) {
			$children = (array)$category->getChildrenNodes();
			$childrenCount = count($children);
		} else {
			$children = $category->getChildren();
			$childrenCount = $children->count();
		}
		$hasChildren = ($children && $childrenCount);

		// select active children
		$activeChildren = array();
		foreach ($children as $child) {
			if ($child->getIsActive()) {
				$activeChildren[] = $child;
			}
		}
		$activeChildrenCount = count($activeChildren);
		$hasActiveChildren = ($activeChildrenCount > 0);

		// prepare list item html classes
		$classes = array();
		$classes[] = 'level' . $level;
		$classes[] = 'nav-' . $this->_getItemPosition($level);
		if ($this->isCategoryActive($category)) {
			$classes[] = 'active';
		}
		$linkClass = '';
		if ($isOutermost && $outermostItemClass) {
			$classes[] = $outermostItemClass;
			$linkClass = ' class="'.$outermostItemClass.'"';
		}
		if ($isFirst) {
			$classes[] = 'first';
		}
		if ($isLast) {
			$classes[] = 'last';
		}
		if ($hasActiveChildren) {
			$classes[] = 'parent';
		}

		// prepare list item attributes
		$attributes = array();
		if (count($classes) > 0) {
			$attributes['class'] = implode(' ', $classes);
		}
		if ($hasActiveChildren && !$noEventAttributes) {
			 $attributes['onmouseover'] = 'toggleMenu(this,1)';
			 $attributes['onmouseout'] = 'toggleMenu(this,0)';
		}
		
					$parentId 		= Mage::getModel('catalog/category')->load($category->getId())->getParentId();
					$topparentId 	= Mage::getModel('catalog/category')->load($parentId)->getParentId();
					$categoryModel 	= Mage::getModel('catalog/category')->load($category->getId());
					//if($parentId != 85 && $topparentId != 85 && $categoryModel['featured_category'] == 1 ){

					$_categoryparentId = Mage::getModel('catalog/category')->load($parentId);
					if($_categoryparentId['featured_category_parent'] != 1){
					//if($parentId != 75){

					$_categorytopparentId = Mage::getModel('catalog/category')->load($topparentId);
					if($_categorytopparentId['featured_category_parent'] == 1){
					//if($topparentId == 75){
							
							if($categoryModel['featured_category'] == 1 && $this->count <= 6){
								$htmlLi = '<li';
								foreach ($attributes as $attrName => $attrValue) {
									$htmlLi .= ' ' . $attrName . '="' . str_replace('"', '\"', $attrValue) . '"';
								}
								$htmlLi .= '>';
								$html[] = $htmlLi;

								$thumbnailType = $this->getIconType();

								if($thumbnailType == 'white') {
									$thumbnailUrl = $categoryModel->getHoverThumbnail();
								} elseif($thumbnailType == 'grey') {
									$thumbnailUrl = $categoryModel->getInactiveThumbnail();
								} else {
									$thumbnailUrl = $categoryModel->getActiveThumbnail();
								}

								$categoryThumbnail = '';
								if($thumbnailUrl) {
									$categoryThumbnail = "<img class='category_thumbnail' src='" . Mage::getBaseUrl('media') . 'catalog' . DS . 'category' . DS . $thumbnailUrl . "' />";
								}

								$html[] = '<a href="'.$this->getCategoryUrl($category).'"'.$linkClass.'>';
								$html[] = $categoryThumbnail . '<span>' . $this->escapeHtml($category->getName()) .'</span>';
								$html[] = '</a>';
								$this->count++;
								$moreUrl = $this->getUrl();

								if($categoryModel['featured_category'] == 1 && $this->count == 6){
										$htmlLi = '<li';
										foreach ($attributes as $attrName => $attrValue) {
											$htmlLi .= ' ' . $attrName . '="' . str_replace('"', '\"', $attrValue) . '"';
										}
										$htmlLi .= '>';
										$html[] = $htmlLi;

										$thumbnailType = $this->getIconType();

										if($thumbnailType == 'white') {
											$thumbnailUrl = $categoryModel->getHoverThumbnail();
										} elseif($thumbnailType == 'grey') {
											$thumbnailUrl = $categoryModel->getInactiveThumbnail();
										} else {
											$thumbnailUrl = $categoryModel->getActiveThumbnail();
										}

										$categoryThumbnail = '';
										if($thumbnailUrl) {
											$categoryThumbnail = "<img class='category_thumbnail' src='" . Mage::getBaseUrl('media') . 'catalog' . DS . 'category' . DS . $thumbnailUrl . "' />";
										}

										$html[] = '<a href="'.$moreUrl.'products/phone-covers.html"'.$linkClass.'>';
										$html[] = $categoryThumbnail . '<span>SEE MORE</span>';
										$html[] = '</a>';
										$this->count++;
									}


							}

						}else{

							$htmlLi = '<li';
							foreach ($attributes as $attrName => $attrValue) {
								$htmlLi .= ' ' . $attrName . '="' . str_replace('"', '\"', $attrValue) . '"';
							}
							$htmlLi .= '>';
							$html[] = $htmlLi;

							$thumbnailType = $this->getIconType();

							if($thumbnailType == 'white') {
								$thumbnailUrl = $categoryModel->getHoverThumbnail();
							} elseif($thumbnailType == 'grey') {
								$thumbnailUrl = $categoryModel->getInactiveThumbnail();
							} else {
								$thumbnailUrl = $categoryModel->getActiveThumbnail();
							}

							$categoryThumbnail = '';
							if($thumbnailUrl) {
								$categoryThumbnail = "<img class='category_thumbnail' src='" . Mage::getBaseUrl('media') . 'catalog' . DS . 'category' . DS . $thumbnailUrl . "' />";
							}

							$html[] = '<a href="'.$this->getCategoryUrl($category).'"'.$linkClass.'>';
							$html[] = $categoryThumbnail . '<span>' . $this->escapeHtml($category->getName()) .'</span>';
							$html[] = '</a>';

						}

					}
									// render children
									$htmlChildren = '';
									$j = 0;
									foreach ($activeChildren as $child) {
										$htmlChildren .= $this->_renderCategoryMenuItemHtml(
											$child,
											($level + 1),
											($j == $activeChildrenCount - 1),
											($j == 0),
											false,
											$outermostItemClass,
											$childrenWrapClass,
											$noEventAttributes
										);
										$j++;
									}
									if (!empty($htmlChildren)) {
										if ($childrenWrapClass) {
											$html[] = '<div class="' . $childrenWrapClass . '">';
										}
										$html[] = '<ul class="level' . $level . '">';
										$html[] = $htmlChildren;
										$html[] = '</ul>';
										if ($childrenWrapClass) {
											$html[] = '</div>';
										}
									}

					$_categoryparentId = Mage::getModel('catalog/category')->load($parentId);
					if($_categoryparentId['featured_category_parent'] != 1){
					//if($parentId != 75){

					$_categorytopparentId = Mage::getModel('catalog/category')->load($topparentId);
					if($_categorytopparentId['featured_category_parent'] == 1){
					//if($topparentId == 75){
						
							if($categoryModel['featured_category'] == 1){
								$html[] = '</li>';
							}
						}else{
							$html[] = '</li>';
						}
					}

		$html = implode("\n", $html);
		return $html;
	}

}
