<?php
    class Happilyunmarried_Valentines_Model_Mysql4_Valentines_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
    {

		public function _construct(){
			parent::_construct();
			$this->_init("valentines/valentines");
		}
   }
	 
