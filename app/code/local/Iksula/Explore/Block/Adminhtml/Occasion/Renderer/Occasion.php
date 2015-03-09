<?php

class Iksula_Explore_Block_Adminhtml_Occasion_Renderer_Occasion extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render (Varien_Object $row) {
		
		$occasion = $row->getData($this->getColumn()->getIndex());
		$str = '<span id="occasion_name" style="font-weight:bold;">' . $occasion . '</span>';
		return $str;
	}
}