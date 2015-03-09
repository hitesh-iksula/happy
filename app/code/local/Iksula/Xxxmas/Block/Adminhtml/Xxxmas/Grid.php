<?php

class Iksula_Xxxmas_Block_Adminhtml_Xxxmas_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("xxxmasGrid");
				$this->setDefaultSort("xxxmas_id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("xxxmas/xxxmas")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("xxxmas_id", array(
				"header" => Mage::helper("xxxmas")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "xxxmas_id",
				));
                
				$this->addColumn("name", array(
				"header" => Mage::helper("xxxmas")->__("Name"),
				"index" => "name",
				));
				$this->addColumn("email", array(
				"header" => Mage::helper("xxxmas")->__("Email"),
				"index" => "email",
				));
				$this->addColumn("message", array(
				"header" => Mage::helper("xxxmas")->__("Message"),
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
			$this->setMassactionIdField('xxxmas_id');
			$this->getMassactionBlock()->setFormFieldName('xxxmas_ids');
			$this->getMassactionBlock()->setUseSelectAll(true);
			$this->getMassactionBlock()->addItem('remove_xxxmas', array(
					 'label'=> Mage::helper('xxxmas')->__('Remove Xxxmas'),
					 'url'  => $this->getUrl('*/adminhtml_xxxmas/massRemove'),
					 'confirm' => Mage::helper('xxxmas')->__('Are you sure?')
				));
			return $this;
		}
			

}