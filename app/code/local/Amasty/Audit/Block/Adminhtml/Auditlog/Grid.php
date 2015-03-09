<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2013 Amasty (http://www.amasty.com)
* @package Amasty_Audit
*/
class Amasty_Audit_Block_Adminhtml_Auditlog_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setDefaultSort('date_time');
    }
    
    protected function _prepareCollection()
    {
        $this->addExportType('*/*/exportCsv', Mage::helper('amaudit')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('amaudit')->__('XML'));

        $collection = Mage::getModel('amaudit/data')->getCollection();

        $this->setCollection($collection);
        return parent::_prepareCollection();

    }

    protected function _prepareColumns()
    {
        $hlp =  Mage::helper('amaudit'); 
      
        $this->addColumn('date_time', array(
            'header'    => $hlp->__('Date'),
            'index'     => 'date_time',
            'type'      => 'date',
            'time' 		=>   'true',
            'format' 	=> Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM),
            'width'     => '170px',
            'gmtoffset' => false,
            'default'    => ' ---- ',
            'filter'  => false,
        ));
       
        $this->addColumn('username', array(
            'header'    => $hlp->__('Username'),
            'index'     => 'username',
        ));
        
        $this->addColumn('name', array(
            'header'      => $hlp->__('Full Name'),
            'index'       => 'name',
        ));
        
        $this->addColumn('ip', array(
            'header'      => $hlp->__('IP Address'),
            'index'       => 'ip',
        ));

        $this->addColumn('location', array(
            'header'      => $hlp->__('Location'),
            'index'       => 'location',
        ));
        
        $this->addColumn('status', array(
            'header'      => $hlp->__('Status'),
            'index'       => 'status',
            'width'     => '120',
            'align'     => 'left',
            'type'      => 'options',
            'options'   => array(0 => $this->__('Failed'), 1 => $this->__('Success'), 2 => $this->__('Locked out')),
            'frame_callback' => array($this, 'decorateStatus')
        ));
        
        return parent::_prepareColumns();
    }
    
    public function decorateStatus($value, $row, $column, $isExport)
    {
        $class = $unlockit = $additionals = '';
        switch ($row->getStatus()) {
            case 0 :
            case 2 :
                $class = 'grid-severity-critical';
                break;
            case 1 :
                $class = 'grid-severity-notice';
                break;
        }

        if($value == $this->__('Locked out')){
            $additionals .= '<script type="text/javascript">
                function cleanLock(elem){
                    new Ajax.Request("' . Mage::helper('adminhtml')->getUrl('amaudit/adminhtml_ajax/unlock') . '", {
                    method:"post",
                    parameters: {admin_username: elem.parentNode.parentNode.children[1].innerHTML.trim()},
                    onSuccess: function(transport) {
                        location.reload();
                    },
                    onFailure: function() { alert("' . $this->__('Something went wrong...') . '"); }
                    });
                }
            </script>';
            $additionals .= '<style>
                .unlockit {
                    height:20px;
                    width: 20px;
                    margin: -20px -2px 0 0;
                    float: right;
                }
                .grid-severity-notice,
                .grid-severity-critical {
                    width: 75%;
                }
            </style>';
            $unlockit .= '<img src="' . $this->getSkinUrl('images/amaudit/unlockit2.png') . '" onclick="cleanLock(this)" class="unlockit" alt="Unlock"/>';
            $unlockit .= '<input type="hidden" class="unlockit-status" value=""/>';
        }
        return $additionals . '<span class="'.$class.'"><span>'.$value.'</span></span>' . $unlockit;
    }
}