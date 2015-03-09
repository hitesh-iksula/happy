<?php
class Iksula_Storelocator_IndexController extends Mage_Core_Controller_Front_Action{
    
    public function IndexAction() {
      
      $this->loadLayout();
      $this->renderLayout(); 
	  
    }

    public function getAllDataAction(){
      $finalArray = array();
      $countries = $this->getLayout()->createBlock('storelocator/index')->getCountries();
      $states = $this->getLayout()->createBlock('storelocator/index')->getStates();
      $cities = $this->getLayout()->createBlock('storelocator/index')->getCities();
      foreach($countries as $country){
        $finalArray[$country['country_id']]['c_name'] = $country['country'];
      }
      foreach($states as $state){
        $finalArray[$state['country_id']]['state'][$state['state_id']]['s_name'] = $state['state'];
        foreach ($cities as $city) {
          if($city['state_id']==$state['state_id']){
            $finalArray[$state['country_id']]['state'][$city['state_id']]['city'][$city['city_id']]= $city['city'];
          }
        }
      }
      $arrayFinal = json_encode($finalArray);      
      print_r($arrayFinal);
    }
}