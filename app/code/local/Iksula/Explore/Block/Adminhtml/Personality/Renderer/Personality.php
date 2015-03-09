<?php

class Iksula_Explore_Block_Adminhtml_Personality_Renderer_Personality extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render (Varien_Object $row) {
		
		$personality = $row->getData($this->getColumn()->getIndex());
		$str = '<span id="personality_name" style="font-weight:bold;">' . $personality . '</span>';
		return $str;
	}
}