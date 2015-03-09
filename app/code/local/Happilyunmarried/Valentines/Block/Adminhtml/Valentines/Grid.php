<?php

class Happilyunmarried_Valentines_Block_Adminhtml_Valentines_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("valentinesGrid");
				$this->setDefaultSort("valentines_id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("valentines/valentines")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("valentines_id", array(
				"header" => Mage::helper("valentines")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    	"type" => "number",
				"index" => "valentines_id",
				));
                
				$this->addColumn("name", array(
				"header" => Mage::helper("valentines")->__("Name"),
				"index" => "name",
				));
				$this->addColumn("email", array(
				"header" => Mage::helper("valentines")->__("Email"),
				"index" => "email",
				));
				$this->addColumn("message", array(
				"header" => Mage::helper("valentines")->__("Message"),
				"index" => "message",
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
			$this->setMassactionIdField('valentines_id');
			$this->getMassactionBlock()->setFormFieldName('valentines_ids');
			$this->getMassactionBlock()->setUseSelectAll(true);
			$this->getMassactionBlock()->addItem('remove_valentines', array(
					 'label'=> Mage::helper('valentines')->__('Remove Valentines'),
					 'url'  => $this->getUrl('*/adminhtml_valentines/massRemove'),
					 'confirm' => Mage::helper('valentines')->__('Are you sure?')
				));
			return $this;
		}
			

}
