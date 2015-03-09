<?php 

class Iksula_Explore_Model_Entity_Attribute_Source_Personality extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
	/**
     * Retreive all options
     *
     * @return array
     */
    public function getAllOptions() {
    	
		$collection = Mage::getModel('explore/personality')->getCollection()->setOrder('personality_name', 'ASC');
		$dataArr = array();
		$returnArr = array();
		$dataArr = $collection->getData();
		$i = 1;

		$returnArr[0]['value'] = 0;
		$returnArr[0]['label'] = "None";
		foreach($dataArr as $data){
			
			if ($data['status'] == 1)
			{				
				$returnArr[$i]['value'] = $data['personality_id'];
				$returnArr[$i]['label'] = $data['personality_name'];

				$i++;
			}
		}
		
		return $returnArr;
    }

}