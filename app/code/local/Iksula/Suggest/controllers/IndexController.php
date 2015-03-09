<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

// require_once('Mage/Checkout/IndexController.php');
class Iksula_Suggest_IndexController extends Mage_Core_Controller_Front_Action
{

	public $_failureResponse = '';
	public $_failedOption = '';
	public $_failedProducts = array();
	public $_wrongSkus = array();
	public $_errorText = false;

    public function indexAction()
    {
        // echo "here lol"; exit;

         // else {

			// echo $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();

			/*?><form action="<?php echo $this->getBaseUrl() . 'atcp'; ?>" method="post" enctype="multipart/form-data">
				<table width="600">

					<tr>
						<td width="20%">Select file</td>
						<td width="80%"><input type="file" name="file" id="file" /></td>
					</tr>

					<tr>
						<td><input type="hidden" value="csv" name="csv" id="csv" /></td>
						<td><input type="submit" name="submit" /></td>
					</tr>

				</table>
			</form><?php*/

		// }

		$this->loadLayout();
		$this->renderLayout();
		// $this->_redirect('checkout/onepage', array('_secure'=>true));
    }

    public function saveAction() {    	

		// get configurable product data - sample
		// $productSku = 'stationery-notebooks-pulpfiction';
		// $productId = Mage::getModel('catalog/product')->getIdBySku($productSku);
		// $product = Mage::getModel('catalog/product')->load($productId);
		// $quantity = 4;
		// $options = array('Deewanon Ka Jaloos');


		if (isset($_FILES["file"])) {

			//if there was an error uploading the file
			if ($_FILES["file"]["error"] > 0) {
				
				echo "Return Code: " . $_FILES["file"]["error"] . "<br />Please upload a CSV file."; exit;

			} else {
				
				//Print file details
				// echo "Upload: " . $_FILES["file"]["name"] . "<br />";
				// echo "Type: " . $_FILES["file"]["type"] . "<br />";
				// echo "Size: " . ($_FILES["file"]["size"] / 1024) . " Kb<br />";
				// echo "Temp file: " . $_FILES["file"]["tmp_name"] . "<br />";

				//Store file in directory "upload" with the name of "uploaded_file.txt"
				$storagename = "b2b-cart.csv";
				move_uploaded_file($_FILES["file"]["tmp_name"], Mage::getBaseDir() . $storagename);
				// echo "Stored in: " . $this->getBaseDir() . $_FILES["file"]["name"] . "<br />";
				
			}
		} else {
			echo "No file selected <br />"; exit;
		}

		// if checkbox to clear cart items is checked
		if(isset($_POST['truncate_cart'])) {
			$this->clear_cart();
		}

		$failure = '';
		$filePath = Mage::getBaseDir() . $storagename;
		$row = 1;
		// $handle = fopen($filePath, "r");
		if (($handle = fopen($filePath, "r")) !== FALSE) {
			
			while(($row_entry = fgetcsv($handle, 1000, ",")) != FALSE) {
				
				$field_entry = $row_entry;
				// echo $row;
				// print_r($field_entry);
				if($row != 1) {

					$productSku = $field_entry[0];
					$productId = Mage::getModel('catalog/product')->getIdBySku($productSku);
					if(!$productId) {

						$this->_wrongSkus[] = $productSku;
						$this->_errorText = true;
						// break;

					} else {

						$product = Mage::getModel('catalog/product')->load($productId);
						$quantity = $field_entry[1];

						if($product->getData('type_id') == 'simple') {

							// get the final parameter array which we will use while adding the simple product to cart
							$productParams = array(
								'product' => $productId,
								'qty' => $quantity
							);
							// print_r($productParams);
							
						}
						// echo "here"; exit;

						if($product->getData('type_id') == 'configurable') {

							$options = array_slice($field_entry, 2);
							$options = array_filter($options);

							// get an array containing all the super attribute options for the configurable
							$allConfigurableOptions = $this->get_configurable_options($productSku);
							// print_r($allConfigurableOptions);

							// get the final parameter array which we will use while adding the configurable product to cart
							$productParams = $this->get_configurable_parameters($allConfigurableOptions, $productId, $quantity, $options);
							// print_r($productParams);
							
						}

						// finally add the configurable product to cart
						if(!in_array($productId, $this->_failedProducts)) {

							$response = $this->add_product_to_cart($product, $productParams);

						}
					
					}
					
				}

				$row++;

			}
			fclose($handle);
		}

		Mage::getSingleton('checkout/session')->setCartWasUpdated(true);

		if($this->_errorText == true) {
			
			if(!empty($this->_failedProducts)) {

				$productNames = array();
				foreach($this->_failedProducts as $productId) {
					$productNames[] = Mage::getSingleton('catalog/product')->load($productId)->getData('name');
				}
				
				$_failureResponse = implode(', ', $productNames);
				Mage::getSingleton('core/session')->addError('Following products could not be added due to incorrect / inadequate options: ' . $_failureResponse);
			}

			if(!empty($this->_wrongSkus)) {

				$productSkus = implode(', ', $this->_wrongSkus);
				Mage::getSingleton('core/session')->addError('Following SKUs are incorrect: ' . $productSkus);

			}

		} else {

			Mage::getSingleton('core/session')->addSuccess('Products added to cart');

		}
		session_write_close();
		
		$this->_redirect('checkout/cart');

    }

	// get an array containing all the super attribute options for the configurable
	public function get_configurable_options($productSku) {

		$productId = Mage::getModel('catalog/product')->getIdBySku($productSku);
		$product = Mage::getModel('catalog/product')->load($productId);

		$configurableAttributeCollection = $product->getTypeInstance()->getConfigurableAttributes();

		$result = array();
		$attributeArray = array();
		$i = 0;
		foreach($configurableAttributeCollection as $attribute){
			$attributeArray['ID'] = $attribute->getProductAttribute()->getId();
			$attributeArray['Label'] = $attribute->getProductAttribute()->getFrontend()->getLabel();
			
			$attributeOptions = $attribute->getProductAttribute()->getSource()->getAllOptions();
			foreach($attributeOptions as $attributeOption) {
				if($attributeOption['value'] != '') {
					$attributeArray[$attributeOption['label']] = $attributeOption['value'];
				}
			}

			$result[$i] = $attributeArray;
			$i++;
			$attributeArray = array();

			//Attr-Code:    $attribute->getProductAttribute()->getAttributeCode()
			//Attr-Label:   $attribute->getProductAttribute()->getFrontend()->getLabel()
			//Attr-Id:      $attribute->getProductAttribute()->getId()
		}

		return $result;
	}

	// get the final parameter array which we will use while adding the configurable product to cart
	public function get_configurable_parameters($allConfigurableOptions, $productId, $quantity, $options) {

		$superAttributes = array();
		$optionCount = count($options);
		$totalOptions = count($allConfigurableOptions);
		$m = 0;
		foreach($allConfigurableOptions as $superAttribute) {
			foreach($options as $option) {
				if(isset($superAttribute[$option])) {
					$superAttributes[$superAttribute['ID']] = $superAttribute[$option];
					$m++;
				}
			}
		}
		if($optionCount != $m OR $optionCount != $totalOptions) {
			$this->_failedProducts[] = $productId;
			$this->_errorText = true;
		}

		$productParams = array(
			'product' => $productId,
			'super_attribute' => $superAttributes,
			'qty' => $quantity
		);

		return $productParams;

	}

	// add the configurable product to cart
	public function add_product_to_cart($product, $productParams) {

		// $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
		// $cart = Mage::getModel('sales/quote')->loadByCustomer($customerId);
		$cart = Mage::getModel('checkout/cart');
		$cart->addProduct($product, $productParams);
		$cart->save(); 

		// echo 'added product to cart';
		return 'added product to cart';
	}

	public function clear_cart() {

		//Get cart helper
        $cartHelper = Mage::helper('checkout/cart');
        //Get all items from cart
        $items = $cartHelper->getCart()->getItems();
        //Loop through all of cart items
        foreach ($items as $item) {
            $itemId = $item->getItemId();
            //Remove items, one by one
            $cartHelper->getCart()->removeItem($itemId)->save();
        }

	}
}
