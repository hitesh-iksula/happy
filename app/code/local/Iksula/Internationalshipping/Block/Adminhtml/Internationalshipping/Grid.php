<?php

class Iksula_Internationalshipping_Block_Adminhtml_Internationalshipping_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("internationalshippingGrid");
				$this->setDefaultSort("ship_id");
				$this->setDefaultDir("ASC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("internationalshipping/internationalshipping")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("ship_id", array(
				"header" => Mage::helper("internationalshipping")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "ship_id",
				));
                
				$this->addColumn("name", array(
				"header" => Mage::helper("internationalshipping")->__("Name"),
				"index" => "name",
				));
				$this->addColumn("email", array(
				"header" => Mage::helper("internationalshipping")->__("Email"),
				"index" => "email",
				));
				$this->addColumn("pincode", array(
				"header" => Mage::helper("internationalshipping")->__("Pincode"),
				"index" => "pincode",
				));
				$this->addColumn("country", array(
				"header" => Mage::helper("internationalshipping")->__("Country"),
				"index" => "country",
				));
				$this->addColumn("shipping_add", array(
				"header" => Mage::helper("internationalshipping")->__("Shipping Address"),
				"index" => "shipping_add",
				));
				$this->addColumn("products", array(
				"header" => Mage::helper("internationalshipping")->__("List of Products"),
				"index" => "products",
				));
				$this->addColumn('status', array(
				'header' => Mage::helper('internationalshipping')->__('Status'),
				'index' => 'status',
				'type' => 'options',
				'options'=>array('0'=>'No','1'=>'Yes'),				
				));
						
			$this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV'));

				return parent::_prepareColumns();
		}

		public function getRowUrl($row)
		{
			   return $this->getUrl("*/*/edit", array("id" => $row->getId()));
		}


		
		protected function _prepareMassaction()
		{
			$this->setMassactionIdField('ship_id');
			$this->getMassactionBlock()->setFormFieldName('ship_ids');
			$this->getMassactionBlock()->setUseSelectAll(true);
			$this->getMassactionBlock()->addItem('remove_internationalshipping', array(
					 'label'=> Mage::helper('internationalshipping')->__('Remove Internationalshipping'),
					 'url'  => $this->getUrl('*/adminhtml_internationalshipping/massRemove'),
					 'confirm' => Mage::helper('internationalshipping')->__('Are you sure?')
				));
			return $this;
		}
			
		static public function getOptionArray7()
		{
            $data_array=array(); 
			$data_array[0]='Yes';
			$data_array[1]='No';
            return($data_array);
		}
		static public function getValueArray7()
		{
            $data_array=array();
			foreach(Iksula_Internationalshipping_Block_Adminhtml_Internationalshipping_Grid::getOptionArray7() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);		
			}
            return($data_array);

		}
		

}