<?php 

class Iksula_Explore_Model_Entity_Attribute_Source_Occasion extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
	/**
     * Retreive all options
     *
     * @return array
     */
    public function getAllOptions() {
    	
		$collection = Mage::getModel('explore/occasion')->getCollection()->setOrder('occasion_name', 'ASC');
		$dataArr = array();
		$returnArr = array();
		$dataArr = $collection->getData();
		$i = 1;

		$returnArr[0]['value'] = 0;
		$returnArr[0]['label'] = "None";
		foreach($dataArr as $data){
			
			if ($data['status'] == 1)
			{				
				$returnArr[$i]['value'] = $data['occasion_id'];
				$returnArr[$i]['label'] = $data['occasion_name'];

				$i++;
			}
		}
		
		return $returnArr;
    }

}