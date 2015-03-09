<?php
class Happilyunmarried_Valentines_IndexController extends Mage_Core_Controller_Front_Action{
    
     public function IndexAction() {
      
	    $this->loadLayout();
      	    $this->renderLayout(); 
	  
    }

    public function ValentinesAction() {
      
      $params     = $this->getRequest()->getParams();
      $name       = trim($params['name']);
      $email      = trim($params['email']);
      $message    = trim($params['message']);
      $collection = Mage::getModel('valentines/valentines');

          try{
                  $collection->setName($name);
                  $collection->setEmail($email);
                  $collection->setMessage($message);
                  $collection->save();
                  $msgs = 'success';
              }catch(Exception $e){
                  $msgs = 'fail';
            }

        $this->getResponse()->setBody(json_encode($msgs));

    }	
}
