<?php

class Iksula_Storelocator_Block_Adminhtml_State_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("stateGrid");
				$this->setDefaultSort("state_id");
				$this->setDefaultDir("ASC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("storelocator/state")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("state_id", array(
				"header" => Mage::helper("storelocator")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "state_id",
				));
                
				$this->addColumn("state", array(
				"header" => Mage::helper("storelocator")->__("State"),
				"index" => "state",
				));
				$this->addColumn('country_id', array(
				'header' => Mage::helper('storelocator')->__('Country'),
				'index' => 'country_id',
				'type' => 'options',
				'options'=>$this->getSelectedCountry(),		
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
			$this->setMassactionIdField('state_id');
			$this->getMassactionBlock()->setFormFieldName('state_ids');
			$this->getMassactionBlock()->setUseSelectAll(true);
			$this->getMassactionBlock()->addItem('remove_state', array(
					 'label'=> Mage::helper('storelocator')->__('Remove State'),
					 'url'  => $this->getUrl('*/adminhtml_state/massRemove'),
					 'confirm' => Mage::helper('storelocator')->__('Are you sure?')
				));
			return $this;
		}
			
		static public function getOptionArray5()
		{
            $data_array=array(); 
			$data_array[0]='Yes';
            return($data_array);
		}
		static public function getValueArray5()
		{
            $data_array=array();
			foreach(Iksula_Storelocator_Block_Adminhtml_State_Grid::getOptionArray5() as $k=>$v){
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
		

}