<?php

class Iksula_Storelocator_Block_Adminhtml_City_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("cityGrid");
				$this->setDefaultSort("city_id");
				$this->setDefaultDir("ASC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("storelocator/city")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("city_id", array(
				"header" => Mage::helper("storelocator")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "city_id",
				));
                
				$this->addColumn("city", array(
				"header" => Mage::helper("storelocator")->__("City"),
				"index" => "city",
				));
				$this->addColumn('state_id', array(
				'header' => Mage::helper('storelocator')->__('State'),
				'index' => 'state_id',
				'type' => 'options',
				'options'=>$this->getSelectedState(),		
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
			$this->setMassactionIdField('city_id');
			$this->getMassactionBlock()->setFormFieldName('city_ids');
			$this->getMassactionBlock()->setUseSelectAll(true);
			$this->getMassactionBlock()->addItem('remove_city', array(
					 'label'=> Mage::helper('storelocator')->__('Remove City'),
					 'url'  => $this->getUrl('*/adminhtml_city/massRemove'),
					 'confirm' => Mage::helper('storelocator')->__('Are you sure?')
				));
			return $this;
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
		

}