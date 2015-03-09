<?php
class Iksula_Internationalshipping_IndexController extends Mage_Core_Controller_Front_Action{
    public function IndexAction() {  
  	  $this->loadLayout();   
  	  $this->getLayout()->getBlock("head")->setTitle($this->__("International Shipping"));
  	        $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
        $breadcrumbs->addCrumb("home", array(
                  "label" => $this->__("Home Page"),
                  "title" => $this->__("Home Page"),
                  "link"  => Mage::getBaseUrl()
  		   ));

        $breadcrumbs->addCrumb("international shipping", array(
                  "label" => $this->__("International Shipping"),
                  "title" => $this->__("International Shipping")
  		   ));

        $this->renderLayout(); 
  	  
    }
    public function getSuccessUrl(){
      return Mage::getBaseUrl().'international-shipping-thank-you';
    }
    public function SubmitPostAction(){
        $data = $this->getRequest()->getParams();
        if($data){
          $shipping =  Mage::getModel('internationalshipping/internationalshipping');
          $shipping->setData($data);
          $shipping->setStatus(0);
          $shipping->save();
          //Mage::getSingleton('core/session')->addSuccess('Your Query has been submitted successfully . We will get back to you!!');
          $this->_redirectSuccess($this->getSuccessUrl());
      }
    }

}