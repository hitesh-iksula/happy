<?php

class Iksula_Impressionsemail_Block_Adminhtml_Impressionsemail_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("impressionsemailGrid");
				$this->setDefaultSort("impressions_id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("impressionsemail/impressionsemail")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("impressions_id", array(
				"header" => Mage::helper("impressionsemail")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "impressions_id",
				));
                
				$this->addColumn("impressions", array(
				"header" => Mage::helper("impressionsemail")->__("Impressions"),
				"index" => "impressions",
				));
				$this->addColumn("source", array(
				"header" => Mage::helper("impressionsemail")->__("Platform"),
				"index" => "source",
				));
					$this->addColumn('created_date', array(
						'header'    => Mage::helper('impressionsemail')->__('Date'),
						'index'     => 'created_date',
						'type'      => 'datetime',
					));
			$this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV')); 
			$this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel'));

				return parent::_prepareColumns();
		}

		public function getRowUrl($row)
		{
			   return '#';
		}


		
		protected function _prepareMassaction()
		{
			$this->setMassactionIdField('impressions_id');
			$this->getMassactionBlock()->setFormFieldName('impressions_ids');
			$this->getMassactionBlock()->setUseSelectAll(true);
			// $this->getMassactionBlock()->addItem('remove_impressionsemail', array(
			// 		 'label'=> Mage::helper('impressionsemail')->__('Remove Impressionsemail'),
			// 		 'url'  => $this->getUrl('*/adminhtml_impressionsemail/massRemove'),
			// 		 'confirm' => Mage::helper('impressionsemail')->__('Are you sure?')
			// 	));
			return $this;
		}
			

}