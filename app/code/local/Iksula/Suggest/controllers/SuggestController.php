<?php
class Iksula_Suggest_SuggestController extends Mage_Core_Controller_Front_Action
{
    public function IndexAction() {   
		$this->loadLayout();   
		// $this->getLayout()->getBlock("head")->setTitle($this->__("Suggest"));
		// $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
		// $breadcrumbs->addCrumb("home", array(
			// "label" => $this->__("Home Page"),
			// "title" => $this->__("Home Page"),
			// "link"  => Mage::getBaseUrl()
		// ));

		// $breadcrumbs->addCrumb("suggest", array(
			// "label" => $this->__("suggest"),
			// "title" => $this->__("suggest")
		// ));

		$this->renderLayout();  
    }
	
	public function SaveAction()
	{ 
		$data = $this->getRequest()->getPost();
		// echo "<pre>"; var_dump($data);exit;
		try {
			// save form data to suggest table
			$model = Mage::getModel('suggest/suggest');
			$model->setData($data);
			$model->save();
				
			// send mail to admin
			$mail = new Zend_Mail();
			$admin_email = Mage::getStoreConfig('trans_email/ident_custom1/email');
			$admin_subject="A New Vendor Just Registered";
			$admin_autobody="New Vendor Contact Information- \nName- ".$data['name']." \nEmail Id- ".$data['email_id']." \nMobile No- ".$data['mob_no'];
			$mail->setSubject($admin_subject);							    
			$mail->setBodyText($admin_autobody);
			$mail->setFrom($admin_email, "Happily Unmarried");							   
			$mail->addTo($admin_email);
			$mail->send();
			
			// send mail to the store owner
			$vendor_mail = new Zend_Mail();
			$vendor_email = $data['email_id'];
			$vendor_subject = "Thank you for your interest. We will contact you soon";							
			$vendor_body = "Your request has been forwarded to the concerning department and they will be in touch shortly. Thank you for visiting our site and contacting us.";
			$vendor_mail->setSubject($vendor_subject);
			$vendor_mail->setBodyText($vendor_body);			
			$vendor_mail->setFrom($admin_email, "Happily Unmarried");
			$vendor_mail->addTo($vendor_email);
			$vendor_mail->send();

			// redirect on success to the same page
			$url = Mage::getBaseUrl() . "new_store?success=true";
			$this->_redirectUrl($url);
			
		}
		catch (Mage_Core_Exception $e) {
			$message = $e->getMessage();
			echo $message;
		}
	}

}
