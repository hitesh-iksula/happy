<?php

class Iksula_Suggest_Block_Adminhtml_Suggest_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("suggestGrid");
				$this->setDefaultSort("suggest_id");
				$this->setDefaultDir("ASC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{		
			
				$collection = Mage::getModel("suggest/suggest")->getCollection();
				
				
				
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("suggest_id", array(
				"header" => Mage::helper("suggest")->__("ID"),
				"align" =>"right",
				"width" => "50px",
				"index" => "suggest_id",
				));
				$this->addColumn("name", array(
				"header" => Mage::helper("suggest")->__("Name"),
				"align" =>"left",
				"index" => "name",
				));
				$this->addColumn("email_id", array(
				"header" => Mage::helper("suggest")->__("Email-Id"),
				"align" =>"left",
				"index" => "email_id",
				));
				$this->addColumn("mob_no", array(
				"header" => Mage::helper("suggest")->__("Mobile-No"),
				"align" =>"left",
				"index" => "mob_no",
				));
				
				
				$this->addColumn("date_registered", array(
				"header" => Mage::helper("suggest")->__("Date Registered"),
				"align" =>"right",
				"width" => "200px",
				"index" => "date_registered",
				));				
				$this->addColumn('verified', array(
					'header' => Mage::helper('suggest')->__('Verified'),
					'align'	 => 'center',
					'index'	 => 'verified',
					'type'	 => 'options',
					'options'=> array(
						0 => 'No',
						1 => 'Yes',
					),
				));				
				$this->addColumn('created_account', array(
					'header' => Mage::helper('suggest')->__('Created Account'),
					'align'	 => 'center',
					'index'	 => 'created_account',
					'type'	 => 'options',
					'options'=> array(
						0 => 'No',
						1 => 'Yes',
					),
				));	


				return parent::_prepareColumns();
		}

		public function getRowUrl($row)
		{
			   return $this->getUrl("*/*/edit", array("id" => $row->getId()));
		}
		
		protected function _prepareMassaction()
		{
			$this->setMassactionIdField('suggest_id');
			$this->getMassactionBlock()->setFormFieldName('delete_id');
			$this->getMassactionBlock()->addItem('delete', array(
				'label'=> Mage::helper('suggest')->__('Delete Selected'),
				'url'  => $this->getUrl('*/*/massDelete', array('' => '')),
				'confirm' => Mage::helper('suggest')->__('Are you sure?')
			));
			return $this;
		}

}