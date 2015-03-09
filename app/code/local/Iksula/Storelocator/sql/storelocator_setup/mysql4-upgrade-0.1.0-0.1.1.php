<?php

$installer = $this;

$installer->startSetup();

$installer->run("

ALTER TABLE {$this->getTable('stores')} ADD `name` varchar(100);
ALTER TABLE {$this->getTable('stores')} ADD `google_map` varchar(255);

    ");

$installer->endSetup();