<?php

class Iksula_Storelocator_Block_Adminhtml_Storelocator_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("storelocatorGrid");
				$this->setDefaultSort("store_id");
				$this->setDefaultDir("ASC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("storelocator/storelocator")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("store_id", array(
				"header" => Mage::helper("storelocator")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "store_id",
				));

				$this->addColumn('name', array(
				'header' => Mage::helper('storelocator')->__('Name'),
				'index' => 'name',			
				));
                
				$this->addColumn('type', array(
				'header' => Mage::helper('storelocator')->__('Type'),
				'index' => 'type',
				'type' => 'options',
				'options'=>Iksula_Storelocator_Block_Adminhtml_Storelocator_Grid::getTypeOptions(),				
				));
				
				$this->addColumn('country', array(
				'header' => Mage::helper('storelocator')->__('Country'),
				'index' => 'country',
				'type' => 'options',
				'options'=>$this->getSelectedCountry(),				
				));
				
				$this->addColumn('state', array(
				'header' => Mage::helper('storelocator')->__('State'),
				'index' => 'state',
				'type' => 'options',
				'options'=>$this->getSelectedState(),				
				));
				
				$this->addColumn('city', array(
				'header' => Mage::helper('storelocator')->__('City'),
				'index' => 'city',
				'type' => 'options',
				'options'=>$this->getSelectedCity(),				
				));
						
				$this->addColumn("contact", array(
				"header" => Mage::helper("storelocator")->__("Contact Number"),
				"index" => "contact",
				));
				$this->addColumn("person", array(
				"header" => Mage::helper("storelocator")->__("Contact Person"),
				"index" => "person",
				));
				$this->addColumn('enabled', array(
				'header' => Mage::helper('storelocator')->__('Enabled'),
				'index' => 'enabled',
				'type' => 'options',
				'options'=>array('0'=>'No','1'=>'Yes'),				
				));
						
			$this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV')); 
			$this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel'));

				return parent::_prepareColumns();
		}

		public function getRowUrl($row)
		{
			   return $this->getUrl("*/*/edit", array("id" => $row->getId()));
		}


		
		protected function _prepareMassaction()
		{
			$this->setMassactionIdField('store_id');
			$this->getMassactionBlock()->setFormFieldName('store_ids');
			$this->getMassactionBlock()->setUseSelectAll(true);
			$this->getMassactionBlock()->addItem('remove_storelocator', array(
					 'label'=> Mage::helper('storelocator')->__('Remove Storelocator'),
					 'url'  => $this->getUrl('*/adminhtml_storelocator/massRemove'),
					 'confirm' => Mage::helper('storelocator')->__('Are you sure?')
				));
			return $this;
		}
		
		static public function getTypeOptions()
		{
            $data_array=array(); 
			$data_array[-1]='Please select..';
			$data_array[0]='MBO';
			$data_array[1]='Brand Store';
            return($data_array);
		}
		static public function getTypeValues()
		{
            $data_array=array();
			foreach(Iksula_Storelocator_Block_Adminhtml_Storelocator_Grid::getTypeOptions() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);		
			}
            return($data_array);

		}
		
		
		public function getSelectedCountry(){
			$countries = Mage::getModel('storelocator/country')->getCollection();
	    	$selected = array();
	    	foreach ($countries as $country) {
	    		$id = $country->getCountryId();
	    		$name = $country->getCountry();
	    		$value = $id.'=>'.$name;
	    		$selected[$id] = $name;
	    	}
	    	return $selected;
		}

		public function getSelectedState(){

			$states = Mage::getModel('storelocator/state')->getCollection();
	    	$selected = array();
	    	foreach ($states as $state) {
	 	   		$id = $state->getStateId();
	    		$name = $state->getState();
	    		$selected[$id] = $name;
	    	}
	    	return $selected;
		}

		public function getSelectedCity(){

			$cities = Mage::getModel('storelocator/city')->getCollection();
	    	$selected = array();
	    	foreach ($cities as $city) {
	    		$id = $city->getCityId();
	    		$name = $city->getCity();
	    		$selected[$id] = $name;
	    	}
	    	return $selected;
		}
		

}