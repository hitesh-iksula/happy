<?php   
class Iksula_Storelocator_Block_Index extends Mage_Core_Block_Template{   
	
	public function getBrandStores(){
		$storesCollection = Mage::getModel('storelocator/storelocator')->getCollection()
								->addFieldToFilter('type','1');
		return $storesCollection;
	}

	public function getMboStores($city_id = null,$country_id = null){
		if($city_id){
			$storesCollection = Mage::getModel('storelocator/storelocator')->getCollection()
									->addFieldToFilter('type','0')
									->addFieldToFilter('city',$city_id);
		}else if($country_id){
			$storesCollection = Mage::getModel('storelocator/storelocator')->getCollection()
									->addFieldToFilter('type','0')
									->addFieldToFilter('country',$country_id);
		}else{
			$storesCollection = Mage::getModel('storelocator/storelocator')->getCollection()
									->addFieldToFilter('type','0');
		}
		return $storesCollection;
	}

	public function getCountries(){
		$countriesCollection = Mage::getModel('storelocator/country')->getCollection()
								->getData();
		return $countriesCollection;
	}

	public function getCities(){
		$countriesCollection = Mage::getModel('storelocator/city')->getCollection()
								->getData();
		return $countriesCollection;
	}

	public function getStates($country_id){
		if($country_id){
			$countriesCollection = Mage::getModel('storelocator/state')->getCollection()
									->addFieldToFilter('country_id',$country_id)
									->getData();
		}else{
			$countriesCollection = Mage::getModel('storelocator/state')->getCollection()
									->getData();
		}

		return $countriesCollection;
	}
}