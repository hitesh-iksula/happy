<?php

class Iksula_Elastic_Model_Search extends Mage_Core_Model_Abstract {

	const BASIC_RESULTS = 'basic';
	const ADVANCED_RESULTS = 'advanced';

	const RESULT_TYPE_PRODUCTS = 'products';
	const RESULT_TYPE_SUGGESTION = 'suggestion';
	const RESULT_TYPE_FAILED = 'no_results';

	private $_searchResponse = array();
	private $_searchResponseType = self::RESULT_TYPE_PRODUCTS;
	private $_searchResponseSuggestion = '';
	private $_totalCount = 0;

	// Default parameters

	private $_productIndex = 'products';
	private $_productType = 'short';

	private $_primaryField = 'name';
	private $_secondaryField = 'short_description';
	private $_tertiaryFiled = 'description';

	private $_autosuggestSearchLimit = 10;
	private $_searchPageProductLimit = 99;

	private $_autosuggestImageSize = 80;

	private $_visibilityAccepted = array(3, 4);
	private $_visibilityRejected = array(1, 2);

	/*
	 *
	 * Indexes all products in ES
	 * No parameters expected
	 * Returns true if indexing is complete, else false
	 *
	 */
	public function createAllProductIndexes() {

		try {

			$elasticaClient = new Elastica_Client();

			// Load index
			$elasticaIndex = $elasticaClient->getIndex('products');

			// Create index
			if(!$elasticaIndex->exists()) {
				$elasticaIndex->create(
					array(
						'number_of_shards' => 4,
						'number_of_replicas' => 1,
						'analysis' => array(
							'analyzer' => array(
								'indexAnalyzer' => array(
									'type' => 'custom',
									'tokenizer' => 'standard',
									'filter' => array('lowercase', 'mySnowball'),
									'char_filter' => array('myMapping')
								),
								'searchAnalyzer' => array(
									'type' => 'custom',
									'tokenizer' => 'standard',
									'filter' => array('lowercase', 'mySnowball')
								)
							),
							'char_filter' => array(
								'myMapping' => array(
									'type' => 'mapping',
									'mappings' => array('-=>')
								)
							),
							'filter' => array(
								'mySnowball' => array(
									'type' => 'snowball',
									'language' => 'English'
								)
							)
						)
					),
					true
				);
			}

			// Get type
			$elasticaType = $elasticaIndex->getType('short');

			// Define mapping
			$mapping = new Elastica_Type_Mapping();
			$mapping->setType($elasticaType);
			$mapping->setParam('index_analyzer', 'indexAnalyzer');
			$mapping->setParam('search_analyzer', 'searchAnalyzer');

			// Define boost field
			$mapping->setParam('_boost', array('name' => '_boost', 'null_value' => 1.0));

			// Set mapping
			$mapping->setProperties(array(
				'id'                => array('type' => 'integer', 'include_in_all' => FALSE),
				'name'              => array('type' => 'string', 'include_in_all' => TRUE),
				'description'       => array('type' => 'string', 'include_in_all' => TRUE),
				'short_description' => array('type' => 'string', 'include_in_all' => TRUE),
				'url'               => array('type' => 'string', 'include_in_all' => FALSE),
				'image'             => array('type' => 'string', 'include_in_all' => FALSE),
				'_boost'            => array('type' => 'float', 'include_in_all' => FALSE)
			));

			// Send mapping to type
			$mapping->send();

			// Add products to documents
			$_productCollection = Mage::getModel('catalog/product')->getCollection()
				->addAttributeToSelect('*')
				->addAttributeToFilter('status', 1)
				->addAttributeToFilter('visibility', array('in' => $this->_visibilityAccepted));

			foreach($_productCollection as $_product) {
				$productId = $_product->getId();
				$_productImage = Mage::helper('catalog/image')->init($_product, 'small_image')->resize($this->_autosuggestImageSize);
				$_productImage = (string)$_productImage;
				$productData = array(
					'id'                => $_product->getId(),
					'name'              => $_product->getName(),
					'description'       => $_product->getDescription(),
					'short_description' => $_product->getShortDescription(),
					'url'               => $_product->getProductUrl(),
					'image'             => $_productImage,
					'_boost'            => 1.0
				);

				$productDocument = new Elastica_Document($productId, $productData);

				$elasticaType->addDocument($productDocument);
			}

			// Remove documents of disabled products
			$_productCollection = Mage::getModel('catalog/product')->getCollection()
				->addAttributeToSelect('*')
				->addAttributeToFilter('status', 0);

			foreach($_productCollection as $_product) {
				$productId = $_product->getId();

				try {
					$productDocument = $elasticaType->getDocument($productId);
				} catch (Exception $e) {
					$productDocument = NULL;
				}

				if ($productDocument != NULL) {
					$elasticaType->deleteById($productId);
				}
			}

			// Remove documents of not visible products
			$_productCollection = Mage::getModel('catalog/product')->getCollection()
				->addAttributeToSelect('*')
				->addAttributeToFilter('visibility', array('in' => $this->_visibilityRejected));

			foreach($_productCollection as $_product) {
				$productId = $_product->getId();

				try {
					$productDocument = $elasticaType->getDocument($productId);
				} catch (Exception $e) {
					$productDocument = NULL;
				}

				if ($productDocument != NULL) {
					$elasticaType->deleteById($productId);
				}
			}

			$elasticaIndex->refresh();
			$elasticaType->getIndex()->refresh();

			return true;

		} catch(Exception $e) {

			echo "<pre>"; print_r($e);
			Mage::log('Elastic Exception while indexing: ' . $e);
			return false;

		}

	}

	/*
	 *
	 * Updates a product's index in ES
	 * Product Id as input parameter
	 * Returns true if successful, else false
	 *
	 */
	public function updateProductIndex($productId) {

		if(!$productId) {
			return false;
		}

		$elasticaClient = new Elastica_Client();

		// Load index
		$elasticaIndex = $elasticaClient->getIndex($this->_productIndex);

		if($elasticaIndex->exists()) {

			// Get type
			$elasticaType = $elasticaIndex->getType($this->_productType);

			$_product = Mage::getModel('catalog/product')->load($productId);

			if($_product->getStatus() == 1 AND in_array($_product->getVisibility(), $this->_visibilityAccepted)) {

				$_productImage = Mage::helper('catalog/image')->init($_product, 'small_image')->resize($this->_autosuggestImageSize);
				$_productImage = (string)$_productImage;
				$productData = array(
					'id'                => $_product->getId(),
					'name'              => $_product->getName(),
					'description'       => $_product->getDescription(),
					'short_description' => $_product->getShortDescription(),
					'url'               => $_product->getProductUrl(),
					'image'             => $_productImage,
					'_boost'            => 1.0
				);

				$productDocument = new Elastica_Document($productId, $productData);

				$elasticaType->addDocument($productDocument);

			} else {

				$productId = $_product->getId();

				try {
					$productDocument = $elasticaType->getDocument($productId);
				} catch (Exception $e) {
					$productDocument = NULL;
				}

				if ($productDocument != NULL) {
					$elasticaType->deleteById($productId);
				}

			}

			$elasticaIndex->refresh();

		} else {
			return false;
		}

		return true;

	}

	/*
	 *
	 * Deletes a product's index in ES
	 * Product Id as input parameter
	 * Returns true if successful, else false
	 *
	 */
	public function deleteProductIndex($productId) {

		if(!$productId) {
			return false;
		}

		$elasticaClient = new Elastica_Client();

		// Load index
		$elasticaIndex = $elasticaClient->getIndex($this->_productIndex);

		if($elasticaIndex->exists()) {

			// Get type
			$elasticaType = $elasticaIndex->getType($this->_productType);

			try {

				$elasticaType->deleteById($productId);

			} catch(Exception $e) {

				Mage::log('Elastic Exception while deleting product ID ' . $productId . '. Exception: ' . $e);
				return false;

			}

			$elasticaIndex->refresh();

		} else {
			return false;
		}

		return true;

	}

	/*
	 *
	 * Fetches results for auto-suggest
	 * Search term and type of expected result as input parameters
	 * Returns array of ES results
	 *
	 */
	public function fetchAutoSuggestResults($query, $searchType = self::BASIC_RESULTS) {

		if(!$query) {
			return false;
		}

		$queryText = $query;

		if($searchType == self::BASIC_RESULTS) {

			$this->search($this->_productIndex, $this->_productType, $queryText);

		} elseif($searchType == self::ADVANCED_RESULTS) {

			$this->search($this->_productIndex, $this->_productType, $queryText, array($this->_primaryField));

			if($this->_totalCount < $this->_autosuggestSearchLimit) {

				$this->search($this->_productIndex, $this->_productType, $queryText, array($this->_secondaryField), $this->_autosuggestSearchLimit - $this->_totalCount);

				if($this->_totalCount < $this->_autosuggestSearchLimit) {

					$this->search($this->_productIndex, $this->_productType, $queryText, array($this->_tertiaryFiled), $this->_autosuggestSearchLimit - $this->_totalCount);

				}

			}

		}

		if($this->_totalCount < $this->_autosuggestSearchLimit) {

			$this->search($this->_productIndex, $this->_productType, $queryText . '*', array($this->_primaryField), $this->_autosuggestSearchLimit - $this->_totalCount);

		}

		if($this->_totalCount == 0) {

			$queryJsonFormat = '{
					"query": {
						"multi_match": {
						"query": "' . $queryText . '",
						"fields": [
							"name"
						],
						"operator": "and"
					}
				},
				"suggest": {
					"text": "' . $queryText . '",
					"film": {
						"phrase": {
							"analyzer": "searchAnalyzer",
							"field": "name",
							"size": 6,
							"real_word_error_likelihood": 0.9,
							"max_errors": 0.5,
							"gram_size": 2
						}
					}
				},
				"from": 0,
				"size": 1,
				"sort": [],
				"facets": []
			}';

			$queryBuilder = new Elastica_Query_Builder($queryJsonFormat);
			$elasticaQuery = new Elastica_Query($queryBuilder->toArray());

			$elasticaClient = new Elastica_Client();

			$search = new Elastica_Search($elasticaClient);

			$elasticaResultSet = $search
				->addIndex($this->_productIndex)
				->addType($this->_productType)
				->search($elasticaQuery);

			$phraseSuggestions = $elasticaResultSet->getResponse()->getData();
			$phraseSuggestions = $phraseSuggestions['suggest']['film'][0]['options'];
			if(count($phraseSuggestions) > 0) {

				$closestMatch = $phraseSuggestions[0]['text'];

				$this->_searchResponseType = self::RESULT_TYPE_SUGGESTION;
				$this->_searchResponseSuggestion = $closestMatch;

			} else {

				$this->_searchResponseType = self::RESULT_TYPE_FAILED;

			}

		}

		return array($this->_searchResponse, $this->_searchResponseType, $this->_searchResponseSuggestion);

	}

	/*
	 *
	 * Formats the ES result set into array response to be sent to client
	 *
	 */
	private function prepareResponse($elasticaResultSet) {

		$resultCount = $elasticaResultSet->getTotalHits();
		$searchResult = $elasticaResultSet->getResults();

		if($resultCount > 0) {
			foreach($searchResult as $searchItem) {
				$searchItemData = $searchItem->getData();
				$this->_searchResponse[$searchItemData['id']] = $searchItemData;
			}
		}

		$this->_searchResponseType = 'products';

		$this->_totalCount = count($this->_searchResponse);

	}

	/*
	 *
	 * Performs actual search and interacts with the ES client
	 * Input parameters:
	 *
	 * $index     => ES index. Defaults to $_productIndex
	 * $type      => ES type. Defaults to $_productType
	 * $queryText => Search term entered
	 * $fields    => Fields to perform search in. Defaults to 'all'
	 * $size      => Size of search results to fetch. Defaults to $_autosuggestSearchLimit
	 * $from      => Used for pagination. Defines the index from which to fetch results. Defaults to 0
	 *
	 * Sets the result and result count parameters
	 *
	 */
	private function search($index, $type, $queryText, $fields = array('_all'), $size, $from = 0) {

		if(!$queryText) {
			return false;
		}

		$elasticaClient = new Elastica_Client();

		// Load index
		$elasticaIndex = $elasticaClient->getIndex($index);

		// Get type
		$elasticaType = $elasticaIndex->getType($type);

		// Define a Query. We want a string query.
		$elasticaQueryString = new Elastica_Query_QueryString();

		$elasticaQueryString->setQuery((string)$queryText)->setFields($fields);

		if(!$size) {
			$size = $this->_autosuggestSearchLimit;
		}

		// Create the actual search object with some data.
		$elasticaQuery = new Elastica_Query();
		$elasticaQuery->setQuery($elasticaQueryString)->setFrom($from)->setSize($size);

		//Search on the index.
		$elasticaResultSet = $elasticaType->search($elasticaQuery);

		$this->prepareResponse($elasticaResultSet);

		return $this;

	}

	/*
	 *
	 * Fetches results for search page
	 * Search term as input parameter
	 * Returns array of ES results
	 *
	 */
	public function fetchResults($query) {

		if(!$query) {
			return false;
		}

		$queryText = $query;
		$fields = array('_all');
		$resultSize = 99;
		$this->search($this->_productIndex, $this->_productType, $queryText, $fields, $resultSize);

		return $this->_searchResponse;

	}

}
