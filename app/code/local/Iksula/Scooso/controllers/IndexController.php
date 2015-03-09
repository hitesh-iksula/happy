<?php
/**
 *
 *
 * Scooso IndexController.
 *
 *
 */
?>
<?php

class Iksula_Scooso_IndexController extends Mage_Core_Controller_Front_Action {

	/**
	 *
	 *
	 * This function gets called when we want to check quantity of a simple product inside a configurable product.
	 *
	 * @param configurable product id, attribute codes and their values
	 * @return 1 if the choice is in stock, 0 if the choice is not in stock
	 *
	 */
	public function QuantityAction() {

		$params = $this->getRequest()->getParams();
		$productId = $params['pid'];
		$_product = Mage::getSingleton('catalog/product')->load($productId);

		$_attributes = $_product->getTypeInstance(true)->getConfigurableAttributes($_product);

		$count = count($_attributes);
		$n = 1;

		foreach($_attributes as $attribute)
		{
			$attributeCode = $attribute->getProductAttribute()->getAttributeCode();

			$_allSubProducts = $_product->getTypeInstance(true)->getUsedProducts(null, $_product);

			foreach($_allSubProducts as $_subProduct)
			{

				$value = $_subProduct->getData($attributeCode);

				$attributeId = $attribute->getProductAttribute()->getAttributeId();
				if($params['attribute' . $attributeId] == $value) {
					if($n == $count) {
						if(in_array($_subProduct->getId(), $productArray) OR $count == 1) {
							if($_subProduct->isSaleable()) {
								echo 1;
							} else {
								echo 0;
							}
						}
					} else {
						$productArray[] = $_subProduct->getId();
					}

				}

			}
			$n++;
		}

		return false;

	}

	/**
	 *
	 *
	 * This function gets called when we want to check quantity of an associated simple product inside a configurable product.
	 *
	 * @param configurable product id, attribute codes and their values via POST or GET
	 * @return json array of stock status of all options
	 *
	 */
	public function PrequantityAction() {

		$params = $this->getRequest()->getParams();
		$productId = $params['pid'];
		$_product = Mage::getSingleton('catalog/product')->load($productId);

		$attributeOptions = array();

		if($_product->getTypeId() == 'configurable') {

			$_allSubProducts = $_product->getTypeInstance(true)->getUsedProducts(null, $_product);
			$_attributes = $_product->getTypeInstance(true)->getConfigurableAttributes($_product);

			foreach($_allSubProducts as $_subProduct) {

				$_subProductId = $_subProduct->getId();

				$n = 0;

				if($_subProduct->isSaleable()) {
					$qty = 1;
				} else {
					$qty = 0;
				}
				$attributeString = '';
				foreach($_attributes as $attribute) {

					$attributeCode = $attribute->getProductAttribute()->getAttributeCode();
					$attributeId = $attribute->getProductAttribute()->getAttributeId();

					// $attributeOptions[$attributeId][$_subProductId] = $qty;
					$attributeString = $attributeString . "-" . $_subProduct->getData($attributeCode);

				}
				$attributeString = substr($attributeString, 1);

				$attributeOptions[$attributeString] = $qty;

			}

		}

		if($_product->getTypeId() == 'bundle') {
			$attributeOptions['type'] = 'bundle';
			if($_product->isSaleable()) {
				$attributeOptions['inStock'] = true;
			} else {
				$attributeOptions['inStock'] = false;
			}
		}

		echo json_encode($attributeOptions);

		return false;

	}

	/**
	 *
	 *
	 * This function gets called when customer wants to be notified when the product comes back in stock
	 *
	 * @param configurable product id, attribute codes and their values, customer Email ID via POST or GET
	 * @return json array with status and response string
	 *
	 */
	public function NotifymeAction() {

		$params = $this->getRequest()->getParams();

		$postObject = new Varien_Object();
        $postObject->setData($params);

        $error = 0;

		if(isset($params["email_id"]) AND $params["email_id"] != 'Email ID') {

	        if (!Zend_Validate::is($params["email_id"], 'EmailAddress')) {

	            $response = "Please Enter A Valid Email ID";
	            $error = 1;

	        } else {

	        	$checkForDuplicate = Mage::getModel('scooso/scooso')->getCollection()
	        		->addFieldToFilter('email_id', array('eq' => $params["email_id"]))
	        		->addFieldToFilter('product_id', array('eq' => $params["id"]));

	        	if($params["options"]) {
	        		$checkForDuplicate->addFieldToFilter('option_ids', array('eq' => implode(",", $params["options"])));
	        	}

	        	$checkForDuplicate = $checkForDuplicate->getFirstItem();

	        	if($checkForDuplicate->getEmailId()) {

	        		$response = "You have already subscribed for this product. You will be notified when the product is back in stock!";

	        	} else {

					$information = array(
						'product_id' => $params["id"],
						'email_id'   => $params["email_id"],
						'status'     => '0'
					);

					if(isset($params["options"])) {
						$information['option_ids'] = implode(",", $params["options"]);
					}
					if(isset($params["label"])) {
						$information['additional_field'] = $params["label"];
					}

					$scooso = Mage::getModel('scooso/scooso');
					$scooso->setData($information);
					$scooso->save();

					$response = "Thank You! You will receive an Email notification when the product is back in stock.";

					//$platform   = $params["platforms"];

					
			         $theme      = Mage::getSingleton('core/design_package')->getTheme('frontend');
			          if($theme == 'default'){
			             $platform = 'Desktop'; 
			          }else{
			             $platform = 'Mobile'; 
			          }

					
                    $email      = $params["email_id"];
                    $collection = Mage::getModel('hupopup/hupopup')
                          ->getCollection()
                          ->addFieldToFilter('user_email_id', $params['ur-mail'])
                          ->getFirstItem();

                    $userMail = $collection->getData('user_email_id');

                    if($userMail == $email){
                                  //$msg = 'Email id already exists';
                    }else{
                        //echo "okk";exit;
                          $collection->setUsername('NA');
                          $collection->setUser_email_id($email);
                          $collection->setFriend_email_id('NA');
                          $collection->setVerify_link('NA');
                          $collection->setSource('In stock reminder');
                          $collection->setPlatform($platform);
                          $collection->setCreated_date(now());
                          $collection->save();
                   }


	        	}

	        }

		} else {

			$response = "Please Enter A Valid Email ID";
			$error = 1;

		}

		$jsonResponse = array('response' => $response, 'error' => $error);

		echo json_encode($jsonResponse);
		return false;

	}

}
