<?php

/**
 *
 *
 * Used for sending Email to the Admin From the Contact Us on Explore Page. AJAX Call.
 *
 *
 */
class Iksula_Explore_TrackorderController extends Mage_Core_Controller_Front_Action {

	const XML_PATH_EMAIL_RECIPIENT  = 'contacts/email/recipient_email';
    const XML_PATH_EMAIL_SENDER     = 'contacts/email/sender_email_identity';
    const XML_PATH_EMAIL_TEMPLATE   = 'contacts/email/email_template';
    const XML_PATH_ENABLED          = 'contacts/contacts/enabled';

    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

	public function postAction()
    {
        $post = $this->getRequest()->getPost();
        if ( $post ) {
            $translate = Mage::getSingleton('core/translate');
            /* @var $translate Mage_Core_Model_Translate */
            $translate->setTranslateInline(false);
            try {
                $postObject = new Varien_Object();
                $postObject->setData($post);

                $error = false;

                if (!Zend_Validate::is(trim($post['name']) , 'NotEmpty')) {
                    $error = true;
                    echo "Please Enter Your Name"; return;
                }

                if (!Zend_Validate::is(trim($post['comment']) , 'NotEmpty')) {
                    $error = true;
                    echo "Please Enter A Message"; return;
                }

                if (!Zend_Validate::is(trim($post['email']), 'EmailAddress')) {
                    $error = true;
                    echo "Please Write A Proper Email ID"; return;
                }

                if (Zend_Validate::is(trim($post['hideit']), 'NotEmpty')) {
                    $error = true;
                }

                if ($error) {
                    throw new Exception();
                }

                $contactsModel = Mage::getModel('explore/contacts');
                $contactsModel->setData($post)->save();

                $mailTemplate = Mage::getModel('core/email_template');
                /* @var $mailTemplate Mage_Core_Model_Email_Template */
                $mailTemplate->setDesignConfig(array('area' => 'frontend'))
                    ->setReplyTo($post['email'])
                    ->sendTransactional(
                        Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE),
                        Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
                        Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT),
                        null,
                        array('data' => $postObject)
                    );

                if (!$mailTemplate->getSentSuccess()) {
                    throw new Exception();
                }

                $translate->setTranslateInline(true);

                echo "Your inquiry was submitted and will be responded to as soon as possible. Thank you for contacting us!";
                // $this->_redirect('*/*/');

                return;
            } catch (Exception $e) {
                $translate->setTranslateInline(true);

                // Mage::getSingleton('customer/session')->addError(Mage::helper('contacts')->__('Unable to submit your request. Please, try again later'));
                echo "Unable to submit your request. Please try again later";
                // $this->_redirect('*/*/');
                return;
            }

        } else {
            // $this->_redirect('*/*/');
            echo "Unable to submit your request. Please try again later";
        }
    }

    public function postmobileAction()
    {
        $post = $this->getRequest()->getPost();
        if ( $post ) {
            $translate = Mage::getSingleton('core/translate');
            /* @var $translate Mage_Core_Model_Translate */
            $translate->setTranslateInline(false);
            try {

                $postObject = new Varien_Object();
                $postObject->setData($post);

                $mailTemplate = Mage::getModel('core/email_template');
                /* @var $mailTemplate Mage_Core_Model_Email_Template */
                $mailTemplate->setDesignConfig(array('area' => 'frontend'))
                    ->setReplyTo($post['email'])
                    ->sendTransactional(
                        Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE),
                        Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
                        Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT),
                        null,
                        array('data' => $postObject)
                    );

                if (!$mailTemplate->getSentSuccess()) {
                    throw new Exception();
                }

                $translate->setTranslateInline(true);

                Mage::getSingleton('customer/session')->addSuccess(Mage::helper('mobile')->__('Your inquiry was submitted and will be responded to as soon as possible.<br/>Thank you for contacting us!'));
                $this->_redirect('mobile/index/contactus');
                return;

            } catch (Exception $e) {

                $translate->setTranslateInline(true);
                Mage::getSingleton('customer/session')->addNotice(Mage::helper('mobile')->__('Unable to submit your request. Please try again later'));
                $this->_redirect('mobile/index/contactus');
                return;

            }

        } else {

            Mage::getSingleton('customer/session')->addNotice(Mage::helper('mobile')->__('Unable to submit your request. Please try again later'));
            $this->_redirect('mobile/index/contactus');
            return;

        }
    }

}
