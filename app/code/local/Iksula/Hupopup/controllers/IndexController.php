<?php
class Iksula_Hupopup_IndexController extends Mage_Core_Controller_Front_Action{
    public function IndexAction() {
      
    $this->loadLayout();   
    $this->getLayout()->getBlock("head")->setTitle($this->__("Hupopup"));
          $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
      $breadcrumbs->addCrumb("home", array(
                "label" => $this->__("Home Page"),
                "title" => $this->__("Home Page"),
                "link"  => Mage::getBaseUrl()
       ));

      $breadcrumbs->addCrumb("hupopup", array(
                "label" => $this->__("Hupopup"),
                "title" => $this->__("Hupopup")
       ));

      $this->renderLayout(); 
    
    }



    public function postPopupAction(){

        $params     = $this->getRequest()->getParams();
        //print_r($params);exit;
        $name       = $params['name'];
        $urmail     = $params['ur-mail'];
        $friendmail = $params['friend-mail'];
        $baseUrl    = Mage::getBaseUrl();

         $theme      = Mage::getSingleton('core/design_package')->getTheme('frontend');
          if($theme == 'default'){
             $platform = 'Desktop'; 
          }else{
             $platform = 'Mobile'; 
          }

        //$modelObj   = Mage::getModel('hupopup/hupopup');
        $collection = Mage::getModel('hupopup/hupopup')
                      ->getCollection()
                      ->addFieldToFilter('user_email_id', $params['ur-mail'])
                      ->getFirstItem();

          $userMail = $collection->getData('user_email_id');

            if($userMail == $urmail){

                          $msg = 'Email id already exists';
             }else{
                          $collection->setUsername($params['name']);
                          $collection->setUser_email_id($params['ur-mail']);
                          $collection->setFriend_email_id($params['friend-mail']);
                          $collection->setVerify_link($params['link-verification']);
                          $collection->setSource($params['source']);
                          $collection->setPlatform($platform);
                          $collection->setCreated_date(now());
                          $collection->save();
                          $msg = '2 Thank you for registering with us!';
                          Mage::getSingleton('core/session')->setEmailgrab($params['ur-mail']);


                          //--------- send e-mail to user ----------// 

                           $subject_u="Thanks a lot for signing up for our weekly free goodies offer.";
                           $header_u="from: Happily Unmarried Signup <info@happilyunmarried.com>\r\n";
                           $header_u.= "Content-type: text/html\r\n"; 

                           $confirm_code_u = urlencode( base64_encode($urmail) );

                            $message_u="<div style='background: none repeat scroll 0 0 #FFFFFF;border: 1px solid #C32F2F;margin: auto;max-width: 100%;overflow: hidden;padding: 5px;width: 670px;'>
                            <table style='padding-top: 1%;padding-bottom: 1%;background: #e64c3e;color: white;line-height: 1.3em;font-weight: bold;width: 100%;margin-bottom: 10px;'>
                            <tbody><tr><td style='width: 84px;'><a style='display: block;width: 73px;' href='http://www.happilyunmarried.com/' target='_blank'>
                            <img style='margin-right: 6px;margin-left: 6px;display: block;border: none;' alt='Happily Unmarried' src='".$baseUrl."skin/frontend/happy/default/images/emailtemps/hu_logo.png'></a></td><td>
                            <p style='margin: 8px 0;'>Thanks a lot for signing up for our weekly free goodies offer.</p></td></tr>
                            </tbody></table>

                            <div style='max-width: 100%;width: 632px;height: auto;margin: 0 auto;font-size: 14px;font-family: arial;color: #999999;line-height: 22px;'>
                            <div style='max-width: 100%;height: auto;'>
                            <img src='".$baseUrl."skin/frontend/happy/default/css/images/banner-img.jpg' style='width: 100%;height: auto;' />
                            </div>
                            <p style='color: #868686;font-size: 1.6em;text-align: center;line-height: 1.3em;'>Please validate your email by clicking the following button.</p>
                            <p style='text-align: center;margin: 15px 0 20px 0;'>
                            <a href='".$baseUrl."hupopup/index/fconfirmation/?confirmation=$confirm_code_u'>
                              <img src='".$baseUrl."skin/frontend/happy/default/css/images/confirm-entry.jpg' />
                            </a>
                            </p>
                            <div style='width:100%;min-height:5px;max-height:2px;line-height:2px;border-top:1px solid #d4d4d4;border-bottom:1px solid #d4d4d4'>&nbsp;</div>
                            <p style='color: #696969;font-size: 1.3em;font-weight: 600;margin-bottom: 0;text-align: center;padding-top: 18px;font-size: 1.1em'>Thanks again. You're AWESOME!<br/>Cheers!</p>
                            <p style='text-align: center;'>
                            <img src='".$baseUrl."skin/frontend/happy/default/css/images/mailer-logo-2.png'>
                            </p>
                            </div>
                            <div>
                            <div style='width:100%;max-width:100%;min-height:10px;margin:0 auto;padding-top:2px;border-bottom:1px solid #808080;background:#4f4f4f;min-height:auto;overflow:hidden;padding:8px 0 7px'>
                            <ul style='width:296px;margin:0 auto;list-style: none outside none;'>

                            <li style='float:left;width:16%;list-style:none;margin-left: 5%;'>
                            <a href='https://www.facebook.com/Like.HU'>
                            <img width='25' height='25' src='".$baseUrl."skin/frontend/happy/default/images/emailg_facebook.png'>
                            </a>
                            </li>

                            <li style='float:left;width:16%;list-style:none;margin-left: 7%;'>
                            <a href='https://twitter.com/happlyunmarried'>
                            <img width='25' height='25' src='".$baseUrl."skin/frontend/happy/default/images/emailg_twitter.png'>
                            </a>
                            </li>

                            <li style='float:left;width:16%;list-style:none;margin-left: 7%;'>
                            <a href='https://plus.google.com/+happilyunmarried'>
                            <img width='25' height='25' src='".$baseUrl."skin/frontend/happy/default/images/emailg_google.png'>
                            </a>
                            </li>

                            <li style='float:left;width:16%;list-style:none;margin-left: 8%;'>
                            <a href='http://instagram.com/happily_unmarried'>
                            <img width='25' height='25' src='".$baseUrl."skin/frontend/happy/default/images/emailg_instagram.png'>
                            </a>
                            </li>
                            </ul>
                            </div>
                            <div style='background:#4f4f4f;padding:2px 2px;text-align:center;color:#fff;min-height:77px;border-bottom:1px solid #808080;text-align:center;font-family:arial;font-size:13px;padding-top:8px'>
                            <div style='color:#C2C2C2'>IN CASE OF ANY* EMERGENCY:<br>Call us at: <span style='color:#fff'>011 - 41042266</span><br>or<br>
                            <p style='text-align:center'>Write to us at: <a target='_blank' style='color:#ffffff' href='mailto:customer.care@happilyunmarried.com'>customer.care@happilyunmarried.com</a> <br><span style='color:#fff;font-size:10px;float:right'>*Except drink and dial.</span></p>
                            </div>
                            </div>
                            <div style='background:#4f4f4f;padding:3px 15px;text-align:center;color:#fff;min-height:80px;border-bottom:1px solid #808080;padding-top:8px;font-family:arial'>
                            <span style='color:#c2c2c2;font-size:12px'>Just in case you think its not working out between us, please don't take it hard. S*** happens. Its not you but us. If you want to talk about it, call us! We'll understand. But know this, you will be missed... always. Live long and prosper!</span><br><br>
                            <span style='color:#c2c2c2;font-size:10px'>Copyright(c) 2014 HappilyUnmarried.com. All Rights Reserved.</span><br>
                                            <span style='color:#c2c2c2;font-size:10px'><span>Happily</span> <span>Unmarried</span> Marketing Pvt. Ltd. A - 48, FIEE Complex, Okhla Industrial Area, Phase - 2 New Delhi - 110020, India.</span>
                            </div>

                            <div style='background:#333333;padding:3px 15px;text-align:center;color:#fff;line-height:24px;font-family:arial'>
                                        <a target='_blank' style='text-decoration:none;color:#fff;font-family:arial;font-size:15px' href='http://www.happilyunmarried.com/'>www.happilyunmarried.com</a>
                            </div>
                             
                            </div></div>";



                           $sentmail_u = mail($urmail,$subject_u,$message_u,$header_u);
                            if($sentmail_u){
                              echo "Your Confirmation link Has Been Sent To Your Email Address.";
                            }
                            else {
                              echo "Cannot send Confirmation link to your e-mail address";
                            }

                           //--------- send e-mail to friend user ----------// 

                           $subject="Your friend $name has signed you for our free goodies offer.";
                           $header="from: Happily Unmarried Signup <info@happilyunmarried.com>\r\n";
                           $header.= "Content-type: text/html\r\n"; 

                           $confirm_code = urlencode( base64_encode($friendmail) );

                          $message_f=" <div style='background: none repeat scroll 0 0 #FFFFFF;border: 1px solid #C32F2F;margin: auto;max-width: 100%;overflow: hidden;padding: 5px;width: 670px;'>
                        
                          <table style='padding-top: 1%;padding-bottom: 1%;background: #e64c3e;color: white;line-height: 1.3em;font-weight: bold;width: 100%;margin-bottom: 10px;'>
                          <tbody><tr><td style='width: 84px;'><a style='display: block;width: 73px;' href='http://www.happilyunmarried.com/' target='_blank'>
                          <img style='margin-right: 6px;margin-left: 6px;display: block;border: none;' alt='Happily Unmarried' src='".$baseUrl."skin/frontend/happy/default/images/emailtemps/hu_logo.png'></a></td><td>
                          <p style='margin: 8px 0;'>Thanks a lot for signing up for our weekly free goodies offer.</p></td></tr>
                          </tbody></table>

                          <div style='max-width: 100%;width: 632px;height: auto;margin: 0 auto;font-size: 14px;font-family: arial;color: #999999;line-height: 22px;'>
                          <div style='max-width: 100%;height: auto;'>
                          <img src='".$baseUrl."skin/frontend/happy/default/css/images/banner-img.jpg' style='width: 100%;height: auto;' />
                          </div>
                          <div>
                          <p style='color: #868686;font-size: 1.6em;text-align: center;line-height: 1.3em;'><span class='username' style='color: #e74c3c;font-weight: bold;'>Your Friend, $name </span> has signed you up for Happily Unmarried’s weekly free goodies offer. Please click the button below to validate your entry.</p>
                          <p style='text-align: center;margin: 15px 0 20px 0;'><a href='".$baseUrl."hupopup/index/confirmation/?confirmation=$confirm_code'><img src='".$baseUrl."skin/frontend/happy/default/css/images/m-in-btn.jpg' /></a></p>
                          <div style='width:100%;min-height:5px;max-height:2px;line-height:2px;border-top:1px solid #d4d4d4;border-bottom:1px solid #d4d4d4'>&nbsp;</div>
                          <p style='color: #696969;font-size: 1.3em;font-weight: 600;margin-bottom: 0'>What is this offer?</p>
                          <p style='margin: 5px 0'>We are giving away free goodies to 2 friends every week. Your friend $name has registered you and himself/herself for this offer. So, validate your email and pray that you guys win.</p>
                          <p style='color: #696969;font-size: 1.3em;font-weight: 600;margin-bottom: 0'>Terms and Conditions</p>
                          <ul style='margin: 5px 0;padding: 0 0 0 18px'>
                          <li>A couple of people will win Happily Unmarried goodies every week.</li>
                          <li>Winners would be notified by e-mail.</li>
                          <li>Email addresses will not be shared with anyone. Strictly no spam. Only love. Promise!</li>
                          <li>By signing up you agree to receive our email updates and special offers.</li>
                          <li>Happily Unmarried reserves the right to modify this offer anytime.</li>
                          </ul>
                          <br>
                          <div style='width:100%;min-height:5px;max-height:2px;line-height:2px;border-top:1px solid #d4d4d4;border-bottom:1px solid #d4d4d4'>&nbsp;</div>
                          <p style='color: #696969;font-size: 1.3em;font-weight: 600;margin-bottom: 0;text-align: center;padding-top: 18px;padding-top: 15px;font-size: 1.1em'>Thanks again. You're AWESOME!<br/>Cheers!</p>
                          <p style='text-align: center;'><img src='".$baseUrl."skin/frontend/happy/default/css/images/mailer-logo-2.png'></p>
                          </div>
                          </div>



                          <div style='width:100%;max-width:100%;min-height:10px;margin:0 auto;padding-top:2px;border-bottom:1px solid #808080;background:#4f4f4f;min-height:auto;overflow:hidden;padding:8px 0 7px'>
                          <ul style='width:296px;margin:0 auto;list-style: none outside none;'>

                            <li style='float:left;width:16%;list-style:none;margin-left: 5%;'>
                            <a href='https://www.facebook.com/Like.HU'>
                            <img width='25' height='25' src='".$baseUrl."skin/frontend/happy/default/images/emailg_facebook.png'>
                            </a>
                            </li>

                            <li style='float:left;width:16%;list-style:none;margin-left: 7%;'>
                            <a href='https://twitter.com/happlyunmarried'>
                            <img width='25' height='25' src='".$baseUrl."skin/frontend/happy/default/images/emailg_twitter.png'>
                            </a>
                            </li>

                            <li style='float:left;width:16%;list-style:none;margin-left: 7%;'>
                            <a href='https://plus.google.com/+happilyunmarried'>
                            <img width='25' height='25' src='".$baseUrl."skin/frontend/happy/default/images/emailg_google.png'>
                            </a>
                            </li>

                            <li style='float:left;width:16%;list-style:none;margin-left: 8%;'>
                            <a href='http://instagram.com/happily_unmarried'>
                            <img width='25' height='25' src='".$baseUrl."skin/frontend/happy/default/images/emailg_instagram.png'>
                            </a>
                            </li>
                          </ul>
                          </div>


                          <div style='background:#4f4f4f;padding:2px 2px;text-align:center;color:#fff;min-height:77px;border-bottom:1px solid #808080;text-align:center;font-family:arial;font-size:13px;padding-top:8px'>
                          <div style='color:#C2C2C2'>IN CASE OF ANY* EMERGENCY:<br>Call us at: <span style='color:#fff'>011 - 41042266</span><br>or<br>
                          <p style='text-align:center'>Write to us at: <a target='_blank' style='color:#ffffff' href='mailto:customer.care@happilyunmarried.com'>customer.care@<wbr></wbr>happilyunmarried.com</a> <br><span style='color:#fff;font-size:10px;float:right'>*Except drink and dial.</span></p>
                          </div>
                          </div>

                          <div style='background:#4f4f4f;padding:3px 15px;text-align:center;color:#fff;min-height:80px;border-bottom:1px solid #808080;padding-top:8px;font-family:arial'>
                          <span style='color:#c2c2c2;font-size:12px'>Just in case you think its not working out between us, please don't take it hard. S*** happens. Its not you but us. If you want to talk about it, call us! We'll understand. But know this, you will be missed... always. Live long and prosper!</span><br><br>
                          <span style='color:#c2c2c2;font-size:10px'>Copyright(c) 2014 HappilyUnmarried.com. All Rights Reserved.</span><br><span style='color:#c2c2c2;font-size:10px'><span>Happily</span> <span>Unmarried</span> Marketing Pvt. Ltd. A - 48, FIEE Complex, Okhla Industrial Area, Phase - 2 New Delhi - 110020, India.</span>
                          </div>

                          <div style='background:#333333;padding:3px 15px;text-align:center;color:#fff;line-height:24px;font-family:arial'>
                          <a target='_blank' style='text-decoration:none;color:#fff;font-family:arial;font-size:15px' href='http://www.happilyunmarried.com/'>www.happilyunmarried.com</a>
                          </div>


                          </div>";



                            $sentmail = mail($friendmail,$subject,$message_f,$header);
                            if($sentmail){
                              echo "Your Confirmation link Has Been Sent To Your Email Address.";
                            }
                            else {
                              echo "Cannot send Confirmation link to your e-mail address";
                            }


                    }
                          //For page load
                          //$this->_redirectUrl($redirectUrl);
                          //For ajax
                          $this->getResponse()->setBody(json_encode($msg));
        }

        public function saveImpressionsAction(){

            //$params       = $this->getRequest()->getParams();
            //$impressions  = $params['impressions'];

            //if($impressions == 'true'){

                $impressions_count = Mage::getStoreConfig('helloworld_options/impressions/impressions_count');
                $impressions_count++;
                $impressions_system_data = new Mage_Core_Model_Config();
                $impressions_system_data->saveConfig('helloworld_options/impressions/impressions_count', $impressions_count, 'default', 0);

           // }

        }

        public function confirmationAction(){

          $params  = $this->getRequest()->getParams();
          $email_decoded = base64_decode( urldecode( $params['confirmation'] ) );
  
          $collection = Mage::getModel('hupopup/hupopup')
                     ->getCollection()
                     ->addFieldToFilter('friend_email_id', $email_decoded)
                     ->getFirstItem();

          $userMail    = $collection->getData('friend_email_id');
          $verify_link = $collection->getData('verify_link');

          if($userMail == $email_decoded){

            if($verify_link != 'Yes'){

               if($verify_link == ''){
                  $update_verify_link = 'U-NA/F-Yes';
               }else{
                  $update_verify_link = 'Yes';
               }

               $id = $collection->getData('hu_popup_id');
                //$date = now();
                //$data = array('username'=>'Order Placed','user_email_id'=>$email,'Friend_email_id'=>'NA','verify_link'=>'NA','source'=>'Order Flow','platform'=>$platform,'created_date'=>$date);
                $data = array('verify_link'=>$update_verify_link);
                $collection->load($id)->addData($data);

                try {
                     $collection->setId($id)->save();
                     //echo "Data updated successfully.";//success message
                  } catch (Exception $e){
                   echo $e->getMessage(); 
                }
            }
          }

          $block = $this->getLayout()->createBlock('core/template')->setTemplate('hupopup/confirmation.phtml');
          $this->getResponse()->setBody($block->toHtml());

        }


        public function fconfirmationAction(){

          $params  = $this->getRequest()->getParams();
          $email_decoded = base64_decode( urldecode( $params['confirmation'] ) );
  
          $collection = Mage::getModel('hupopup/hupopup')
                     ->getCollection()
                     ->addFieldToFilter('user_email_id', $email_decoded)
                     ->getFirstItem();

          $Friend_email_id    = $collection->getData('user_email_id');
          $verify_link = $collection->getData('verify_link');

          if($Friend_email_id == $email_decoded){

            if($verify_link != 'Yes'){

               if($verify_link == ''){
                  $update_verify_link = 'U-Yes/F-NA';
               }else{

                  $update_verify_link = 'Yes';
               }

               $id = $collection->getData('hu_popup_id');
                //$date = now();
                //$data = array('username'=>'Order Placed','user_email_id'=>$email,'Friend_email_id'=>'NA','verify_link'=>'NA','source'=>'Order Flow','platform'=>$platform,'created_date'=>$date);
                $data = array('verify_link'=>$update_verify_link);
                $collection->load($id)->addData($data);

                try {
                     $collection->setId($id)->save();
                     //echo "Data updated successfully.";//success message
                  } catch (Exception $e){
                   echo $e->getMessage(); 
                }
            }
          }

          $block = $this->getLayout()->createBlock('core/template')->setTemplate('hupopup/fconfirmation.phtml');
          $this->getResponse()->setBody($block->toHtml());

        }

}
