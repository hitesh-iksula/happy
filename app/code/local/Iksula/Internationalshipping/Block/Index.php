<?php   
class Iksula_Internationalshipping_Block_Index extends Mage_Core_Block_Template{   
	
	public function getAction(){
		return Mage::getBaseUrl().'internationalshipping/index/submitpost';
	}
}