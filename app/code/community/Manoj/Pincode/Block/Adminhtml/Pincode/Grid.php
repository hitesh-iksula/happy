<?php

class Manoj_Pincode_Block_Adminhtml_Pincode_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("pincodeGrid");
				$this->setDefaultSort("id");
				$this->setDefaultDir("ASC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("pincode/pincode")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("id", array(
				"header" => Mage::helper("pincode")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "id",
				));
                
				$this->addColumn("pincode", array(
				"header" => Mage::helper("pincode")->__("pincode"),
				"index" => "pincode",
				));
				$this->addColumn("area", array(
				"header" => Mage::helper("pincode")->__("Area "),
				"index" => "area",
				));
				$this->addColumn("location", array(
				"header" => Mage::helper("pincode")->__("Location"),
				"index" => "location",
				));
				$this->addColumn("city", array(
				"header" => Mage::helper("pincode")->__("City"),
				"index" => "city",
				));
				$this->addColumn("state", array(
				"header" => Mage::helper("pincode")->__("State"),
				"index" => "state",
				));
				$this->addColumn("code", array(
				"header" => Mage::helper("pincode")->__("Code"),
				"index" => "code",
				));
				$this->addColumn("zone", array(
				"header" => Mage::helper("pincode")->__("Zone"),
				"index" => "zone",
				));
				$this->addColumn("cod", array(
				"header" => Mage::helper("pincode")->__("Cod"),
				"index" => "cod",
				));
				$this->addColumn("prepaid", array(
				"header" => Mage::helper("pincode")->__("Prepaid"),
				"index" => "prepaid",
				));
				$this->addColumn('status', array(
					'header' => Mage::helper('pincode')->__('Status'),
					'index' => 'status',
					'type' => 'options',
					'options'=>Manoj_Pincode_Block_Adminhtml_Pincode_Grid::getOptionArray10(),				
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
			$this->setMassactionIdField('id');
			$this->getMassactionBlock()->setFormFieldName('ids');
			$this->getMassactionBlock()->setUseSelectAll(true);
			$this->getMassactionBlock()->addItem('remove_pincode', array(
					 'label'=> Mage::helper('pincode')->__('Remove Pincode'),
					 'url'  => $this->getUrl('*/adminhtml_pincode/massRemove'),
					 'confirm' => Mage::helper('pincode')->__('Are you sure?')
				));
			return $this;
		}
			static public function getOptionArray10()
		{
            $data_array=array(); 
			$data_array[1]='Enabled';
			$data_array[2]='Disabled';
            return($data_array);
		}
		static public function getValueArray10()
		{
            $data_array=array();
			foreach(Manoj_Pincode_Block_Adminhtml_Pincode_Grid::getOptionArray10() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);		
			}
            return($data_array);

		}
		
		static public function getCarrierArray20()
		{
			$methods = array(array('value'=>'','label'=>Mage::helper('adminhtml')->__('--Please Select--')));

			$activeCarriers = Mage::getSingleton('shipping/config')->getActiveCarriers();
			foreach($activeCarriers as $carrierCode => $carrierModel)
			{
				$options = array();
				if( $carrierMethods = $carrierModel->getAllowedMethods() )
				{
					foreach ($carrierMethods as $methodCode => $method)
					{
						$code= $carrierCode.'_'.$methodCode;
						$options[]=array('value'=>$code,'label'=>$method);

					}
					$carrierTitle = Mage::getStoreConfig('carriers/'.$carrierCode.'/title');

				}
				unset($methods[0]);
				$methods[]=$carrierTitle;
			}
			return $methods;
		}
		static public function getCarrierValueArray20()
		{
            $data_array=array();
			foreach(Manoj_Pincode_Block_Adminhtml_Pincode_Grid::getCarrierArray20() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);		
			}
            return($data_array);

		}


}