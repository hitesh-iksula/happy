<?php

class Iksula_Explore_Block_Adminhtml_Gender_Renderer_Gender extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render (Varien_Object $row) {
		
		$gender = $row->getData($this->getColumn()->getIndex());
		$str = '<span id="gender_name" style="font-weight:bold;">' . $gender . '</span>';
		return $str;
	}
}