<?php
class Manoj_Pincode_IndexController extends Mage_Core_Controller_Front_Action{
    public function IndexAction() {
      
	  $this->loadLayout();   
	  $this->getLayout()->getBlock("head")->setTitle($this->__("Zipcode"));
	        $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
      $breadcrumbs->addCrumb("home", array(
                "label" => $this->__("Home Page"),
                "title" => $this->__("Home Page"),
                "link"  => Mage::getBaseUrl()
		   ));

      $breadcrumbs->addCrumb("zipcode", array(
                "label" => $this->__("Zipcode"),
                "title" => $this->__("Zipcode")
		   ));

      $this->renderLayout(); 
	  
    }

    public function PincodevalidationAction(){
      $pincode = Mage::app()->getRequest()->getParam('pincode');
      Mage::getModel('core/cookie')->set('pincode', $pincode);
      $pincodeHelper = Mage::helper('pincode/data');
      // $cookievalue = Mage::getModel('core/cookie')->getPincode();
    //  if($pincode==''){
        // Mage::getModel('core/cookie')->
      //  $pincode = $cookievalue;
      //}
      echo $helperdata = Mage::helper('pincode')->getpincodestatus($pincode);
      //$loadFromAnyField=Mage::getModel("pincode/pincode");
    //  print_r(get_class_methods($loadFromAnyField));  
      //$loadFromAnyField->addAttributeToSelect('pincode');//($pincode);
     // print_r($loadFromAnyField->printLogQuery(true));
      //exit;
    }

}