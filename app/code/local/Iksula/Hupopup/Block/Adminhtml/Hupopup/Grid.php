<?php

class Iksula_Hupopup_Block_Adminhtml_Hupopup_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("hupopupGrid");
				$this->setDefaultSort("hu_popup_id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("hupopup/hupopup")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("hu_popup_id", array(
				"header" => Mage::helper("hupopup")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "hu_popup_id",
				));
                
				$this->addColumn("username", array(
				"header" => Mage::helper("hupopup")->__("Name"),
				"index" => "username",
				));
				$this->addColumn("user_email_id", array(
				"header" => Mage::helper("hupopup")->__("Email ID"),
				"index" => "user_email_id",
				));
				$this->addColumn("friend_email_id", array(
				"header" => Mage::helper("hupopup")->__("Friend Email ID"),
				"index" => "friend_email_id",
				));
				$this->addColumn("source", array(
				"header" => Mage::helper("hupopup")->__("Source"),
				"index" => "source",
				));
				$this->addColumn("platform", array(
				"header" => Mage::helper("hupopup")->__("Platform"),
				"index" => "platform",
				));
				$this->addColumn('verify_link', array(
				'header' => Mage::helper('hupopup')->__('Link Verified ?'),
				'index' => 'verify_link',
				// 'type' => 'options',
				// 'options'=>Iksula_Hupopup_Block_Adminhtml_Hupopup_Grid::getOptionArray6(),				
				));
				$this->addColumn('created_date', array(
					'header'    => Mage::helper('hupopup')->__('Date'),
					'index'     => 'created_date',
					'type'      => 'datetime',
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
			$this->setMassactionIdField('hu_popup_id');
			$this->getMassactionBlock()->setFormFieldName('hu_popup_ids');
			$this->getMassactionBlock()->setUseSelectAll(true);
			$this->getMassactionBlock()->addItem('remove_hupopup', array(
					 'label'=> Mage::helper('hupopup')->__('Remove Hupopup'),
					 'url'  => $this->getUrl('*/adminhtml_hupopup/massRemove'),
					 'confirm' => Mage::helper('hupopup')->__('Are you sure?')
				));
			return $this;
		}
			
		static public function getOptionArray6()
		{
            $data_array=array(); 
			$data_array[0]='Yes';
			$data_array[1]='No';
            return($data_array);
		}
		static public function getValueArray6()
		{
            $data_array=array();
			foreach(Iksula_Hupopup_Block_Adminhtml_Hupopup_Grid::getOptionArray6() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);		
			}
            return($data_array);

		}
		

}