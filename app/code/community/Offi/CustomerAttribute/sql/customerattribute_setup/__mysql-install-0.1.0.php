 <?php

$installer = $this;

$installer->startSetup();

$installer->addAttribute( 'customer', 'tax_class_id', array(
            'type'                => 'int',
            'input'                => 'select',
            'label'                => 'Tax Class',
            'source'            => 'tax/class_source_customer',
            'required'            => true,
            'visible'            => true
    )
);

$installer->endSetup();
?> 