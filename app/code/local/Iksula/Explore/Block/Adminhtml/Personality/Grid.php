<?php

class Iksula_Explore_Block_Adminhtml_Personality_Grid extends Mage_Adminhtml_Block_Widget_Grid
{	
	public function __construct() {

		parent::__construct();
		$this->setID('personalityGrid');
		$this->setDefaultSort('personality_id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
		
		// echo "grid"; exit;
	}
	
	protected function _prepareCollection() {

		$collection = Mage::getModel('explore/personality')->getCollection();
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}
	
	protected function _prepareColumns() {

		$this->addColumn('personality_id', array(
			'header' => Mage::helper('explore')->__('ID'),
			'align'	 => 'right',
			'width'	 => '40px',
			'index'	 => 'personality_id',
		));
		
		$this->addColumn('personality_name', array(
			'header' => Mage::helper('explore')->__('Personality'),
			'align'	 => 'center',
			// 'width'	 => '200px',
			'index'	 => 'personality_name',
			'renderer' => 'explore/adminhtml_personality_renderer_personality',
		));
		
		$this->addColumn('personality_logo_1', array(
			'header' => Mage::helper('explore')->__('Personality Logo (Active)'),
			'align'	 => 'center',
			// 'width'	 => '70px',
			'index'	 => 'personality_logo_1',
			'type'	 => 'image',
			'renderer' => 'explore/adminhtml_personality_renderer_image',
			'escape'   => true,
            'sortable' => false,
            'filter'   => false,
		));
		
		$this->addColumn('personality_logo_2', array(
			'header' => Mage::helper('explore')->__('Personality Logo (Inactive)'),
			'align'	 => 'center',
			// 'width'	 => '70px',
			'index'	 => 'personality_logo_2',
			'type'	 => 'image',
			'renderer' => 'explore/adminhtml_personality_renderer_image',
			'escape'   => true,
            'sortable' => false,
            'filter'   => false,
		));
		
		$this->addColumn('status', array(
			'header' => Mage::helper('explore')->__('Status'),
			'align'	 => 'center',
			'index'	 => 'status',
			'type'	 => 'options',
			'options'=> array(
				1 => 'Enabled',
				2 => 'Disabled',
			),
		));
		
		$this->addColumn('action', array(
			'header'    =>  Mage::helper('explore')->__('Action'),
			// 'width'     => '140px',
			'type'      => 'action',
			'getter'    => 'getId',
			'actions'   => array(
				array(
					'caption'   => Mage::helper('explore')->__('Edit'),
					'url'       => array('base'=> '*/*/edit'),
					'field'     => 'id'
				),
				
				array(
					'caption'   => Mage::helper('explore')->__('Delete'),
					'url'       => array('base'=> '*/*/delete'),
					'field'     => 'id'
				),
			),
			'filter'    => false,
			'sortable'  => false,
			'index'     => 'stores',
			'is_system' => true,
        ));
		
		// $this->addExportType('*/*/exportCsv', Mage::helper('explore')->__('CSV'));
		// $this->addExportType('*/*/exportXml', Mage::helper('explore')->__('XML'));
		
		return parent::_prepareColumns();
	}
	
	protected function _prepareMassaction() {

		$this->setMassactionIdField('personality_id');
		$this->getMassactionBlock()->setFormFieldName('explore');

		$this->getMassactionBlock()->addItem('delete', array(
			'label'    => Mage::helper('explore')->__('Delete'),
			'url'      => $this->getUrl('*/*/massDelete'),
			'confirm'  => Mage::helper('explore')->__('Are you sure?')
		));

		$statuses = Mage::getSingleton('explore/status')->getOptionArray();

		array_unshift($statuses, array('label'=>'', 'value'=>''));
		$this->getMassactionBlock()->addItem('status', array(
			'label'=> Mage::helper('explore')->__('Change status'),
			'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
			'additional' => array(
				'visibility' => array(
					'name' => 'status',
					'type' => 'select',
					'class' => 'required-entry',
					'label' => Mage::helper('explore')->__('Status'),
					'values' => $statuses
				)
			)
		));
		return $this;
	}
	
	public function getRowUrl($row) {
		
		return $this->getUrl('*/*/edit', array('id'=>$row->getId()));
	}

}