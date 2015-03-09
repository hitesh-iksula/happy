<?php

/**
 * 
 * 
 * Controller to Set The Selected Banner as a Cover Pic in Facebook Profile
 * 
 * 
 */
class Iksula_Explore_CoverController extends Mage_Core_Controller_Front_Action {

	public function IndexAction() {

		// Initialize Parameters, received from Backend
		$app_id = Mage::getStoreConfig('explore/facebook_cover/app_id', Mage::app()->getStore());
		$app_secret = Mage::getStoreConfig('explore/facebook_cover/secret_id', Mage::app()->getStore());
		$post_login_url_extension = Mage::getStoreConfig('explore/facebook_cover/post_login_url', Mage::app()->getStore());
		$post_login_url = Mage::getUrl() . $post_login_url_extension;

		// Save Selected Image from REQUEST Data into Session
		if($this->getRequest()->getParam('slide') != '') {
			
			$slideId = $this->getRequest()->getParam('slide');
			$harpreet = Mage::helper('harpreet_slider/config');
			$_counter = 0;
			foreach ($harpreet->getSliders() as $_slider) {
				if($_counter == $slideId) {
					$fileName = $_slider['image'];
				}
				$_counter++;
			}
			$fileName = rtrim($fileName, '/');
			$fileBreakdown = explode('/', $fileName);
			$num = (count($fileBreakdown) - 1);
			$fileName = $fileBreakdown[$num];
			Mage::getSingleton('core/session')->setFacebookCoverPic($fileName);
			
		}

		// Authentication Code
		$code = @$_REQUEST["code"];

		// Initialize by OAUTH
		if(empty($code)){
			
			$dialog_url= "http://www.facebook.com/dialog/oauth?" . "client_id=" .  $app_id . "&redirect_uri=" . urlencode( $post_login_url ) . "&scope=publish_stream";
			echo("<script>top.location.href='" . $dialog_url . "'</script>");

		// Once Initiliazed and returned here from the Post Login URL, Continue with the process
		} else {
			
			// Create the Token URL
			$token_url = "https://graph.facebook.com/oauth/access_token?" . "client_id=" . $app_id . "&redirect_uri=" . urlencode( $post_login_url ) . "&client_secret=" . $app_secret . "&code=" . $code;
			$response = file_get_contents($token_url);
			$params = null;
			parse_str($response, $params);
			$access_token = $params['access_token'];

			// Get Selected Image from Session Data
			$fileName = Mage::getSingleton('core/session')->getFacebookCoverPic();

			// Get Default Message from the Backend
			$message = Mage::getStoreConfig('explore/facebook_cover/post_message', Mage::app()->getStore());

			// Resolve Photo Arguments
			$fileDirectory = 'media/harpreet/slider/default';
			$filePath = Mage::getBaseDir() . DS . $fileDirectory . DS . $fileName;
			$args = array(
			   'message' => $message,
			);

			// Send A CURL Request to Facebook and Upload the image in User's Profile
			$args[basename($fileName)] = '@' . $filePath;
			$ch = curl_init();
			$url = 'https://graph.facebook.com/me/photos?access_token='.$access_token;
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
			$data = curl_exec($ch);

			// In the Data received by hitting URL with CURL, we get the ID of the Uploaded Photo.
			$returnArray = json_decode($data,true);
			$photoId = $returnArray['id'];

			// Pass Photo ID to the URL and Redirect to Facebook
			$redirectUrl = "http://www.facebook.com/profile.php?preview_cover=" . $photoId;
			$this->_redirectUrl($redirectUrl);

		}

	}

	public function getSocialSharingBlockAction() {

		$response = array();
		$response['html'] = $this->getLayout()->createBlock('core/template')->setTemplate('explore/modals/product/share.phtml')->toHtml();
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
		return;

	}

	public function getFacebookLikeboxAction() {

		$response = array();
		$response['html'] = $this->getLayout()->createBlock('core/template')->setTemplate('explore/fb_likebox.phtml')->toHtml();
		// $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
		$this->getResponse()->setBody($response['html']);
		return;

	}

	public function SubcategoriesAction() {

			$sub_cat_id = $_POST['sub_cat_id'];

			if($sub_cat_id){
				$cat = Mage::getModel('catalog/category')->load($sub_cat_id);
		        $subcats = $cat->getChildren();

				$subcats_html = "<select name='models' class='models'>";
		        $subcats_html .= "<option value=''>MODELS</option>";


		            foreach(explode(',',$subcats) as $sub_subCatid)
		            {
		                $_sub_category = Mage::getModel('catalog/category')->load($sub_subCatid);
		                //print_r($_sub_category);

		                //   	if($sub_subCatid == $sub_cat_id){
		                //   		$selected = "selected";
		              		// }else{
		              		// 	$selected = "";
		              		// }

		                  if($_sub_category->getIsActive()) {
		                    $subcats_html .= "<option value='".$_sub_category->getUrl()."'>".$_sub_category->getName()."</option>";
		                }
		            }

				$subcats_html .= "</select>";

	        }else{

	        	$subcats_html = "<select name='models' class='models'>";
		        $subcats_html .= "<option value=''>MODELS</option>";
		        $subcats_html .= "</select>";

	        }  
	        	echo $subcats_html;  

	}

}		