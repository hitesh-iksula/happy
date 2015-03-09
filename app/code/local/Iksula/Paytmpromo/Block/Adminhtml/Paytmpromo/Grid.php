<?php

class Iksula_Paytmpromo_Block_Adminhtml_Paytmpromo_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("paytmpromoGrid");
				$this->setDefaultSort("id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("paytmpromo/paytmpromo")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("id", array(
				"header" => Mage::helper("paytmpromo")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "id",
				));
                
				$this->addColumn("order_id", array(
				"header" => Mage::helper("paytmpromo")->__("Order ID"),
				"index" => "order_id",
				));
				$this->addColumn("name", array(
				"header" => Mage::helper("paytmpromo")->__("Name"),
				"index" => "name",
				));
				$this->addColumn("email_id", array(
				"header" => Mage::helper("paytmpromo")->__("Email ID"),
				"index" => "email_id",
				));
				$this->addColumn("phone_number", array(
				"header" => Mage::helper("paytmpromo")->__("Phone Number"),
				"index" => "phone_number",
				));
				$this->addColumn("amount", array(
				"header" => Mage::helper("paytmpromo")->__("Transaction Amount"),
				"index" => "amount",
				));
				$this->addColumn("allotted_coupon_code", array(
				"header" => Mage::helper("paytmpromo")->__("Allotted Coupon Code"),
				"index" => "allotted_coupon_code",
				));
				$this->addColumn("status", array(
				"header" => Mage::helper("paytmpromo")->__("Status"),
				"index" => "status",
				));
				$this->addColumn("respmsg", array(
				"header" => Mage::helper("paytmpromo")->__("Response"),
				"index" => "respmsg",
				));
					$this->addColumn('timestamp', array(
						'header'    => Mage::helper('paytmpromo')->__('Time'),
						'index'     => 'timestamp',
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
			$this->setMassactionIdField('id');
			$this->getMassactionBlock()->setFormFieldName('ids');
			$this->getMassactionBlock()->setUseSelectAll(true);
			$this->getMassactionBlock()->addItem('remove_paytmpromo', array(
					 'label'=> Mage::helper('paytmpromo')->__('Remove Paytmpromo'),
					 'url'  => $this->getUrl('*/adminhtml_paytmpromo/massRemove'),
					 'confirm' => Mage::helper('paytmpromo')->__('Are you sure?')
				));
			return $this;
		}
			

}