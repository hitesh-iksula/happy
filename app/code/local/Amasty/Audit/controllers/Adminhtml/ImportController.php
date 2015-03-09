<?php
/**
* @author Amasty Team
* @copyright Amasty
* @package Amasty_Qcheckout
*/
class Amasty_Audit_Adminhtml_ImportController extends Mage_Adminhtml_Controller_Action
{
    protected $_geoipFiles = array(
        'block' => 'GeoLiteCity-Blocks.csv',
        'location' => 'GeoLiteCity-Location.csv'
    );
    
     protected $_geoipIgnoredLines = array(
        'block' => 2,
        'location' => 2
    );
    
    public function startAction()
    {
        $result = array();
        try {
            $type = $this->getRequest()->getParam('type');
    
            $dir = Mage::getModuleDir('sql', 'Amasty_Audit');
            $filePath = $dir.'/geoip/'.$this->_geoipFiles[$type];

            $ret = Mage::getModel('amaudit/import')->startProcess($type, $filePath, $this->_geoipIgnoredLines[$type]);
            
            $result['position'] = ceil($ret['current_row'] / $ret['rows_count'] * 100);
            $result['status'] = 'started';
            $result['file'] = $this->_geoipFiles[$type];

        } catch(Exception $e){
            $result['error'] = $e->getMessage();
        }
        
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
    
    public function processAction()
    {
        $result = array();
        try {
            $type = $this->getRequest()->getParam('type');

            $dir = Mage::getModuleDir('sql', 'Amasty_Audit');
            $filePath = $dir.'/geoip/'.$this->_geoipFiles[$type];

            $ret = Mage::getModel('amaudit/import')->doProcess($type, $filePath);
            
            $result['status'] = 'processing';
            $result['position'] = ceil($ret['current_row'] / $ret['rows_count'] * 100);
            
        } catch(Exception $e){
            $result['error'] = $e->getMessage();
        }
        
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
    
    public function commitAction()
    {
        $result = array();
        
        try {
            $type = $this->getRequest()->getParam('type');
    
            $import = Mage::getModel('amaudit/import');
            
            $import->commitProcess($type); 
            
            $result['status'] = 'done';
            
            $result['full_import_done'] = Mage::getModel('amaudit/import')->isDone() ? "1" : "0";
            
        }  catch(Exception $e){
            $result['error'] = $e->getMessage();
        }
        
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
}
