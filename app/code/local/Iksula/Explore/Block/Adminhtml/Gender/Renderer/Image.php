<?php

class Iksula_Explore_Block_Adminhtml_Gender_Renderer_Image extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render (Varien_Object $row)
	{
		$path = Mage::getBaseUrl('media') . '/explore/gender/';
		$path = str_replace('\\', '/', $path);

		if ($row->getData($this->getColumn()->getIndex())) {
			$str = '<img src="' . $path . $row->getData($this->getColumn()->getIndex()) . '" ';
		} else {
			$str = '<img src="' . $path . '/default_image.jpg" ';
		}

		$str .= 'id="' . $this->getColumn()->getId() . '" ';
		$str .= 'class="grid_image" style="height:30px" />';
		return $str;
	}
}