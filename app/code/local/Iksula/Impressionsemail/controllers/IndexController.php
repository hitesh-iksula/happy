<?php
class Iksula_Impressionsemail_IndexController extends Mage_Core_Controller_Front_Action{
  
    public function IndexAction() {

	    $this->loadLayout();   
	  
        $params       = $this->getRequest()->getParams();
        $impressions  = $params['impressions'];

        if($impressions == 'true'){

            $theme      = Mage::getSingleton('core/design_package')->getTheme('frontend');
              if($theme == 'default'){
                 $platform = 'Desktop'; 
              }else{
                 $platform = 'Mobile'; 
              }

            $collection = Mage::getModel('impressionsemail/impressionsemail');
            $collection->setImpressions('1');
                          $collection->setSource($platform);
                          $collection->setCreated_date(now());
                          $collection->save();
        }

      $this->renderLayout(); 
	  
    }

}