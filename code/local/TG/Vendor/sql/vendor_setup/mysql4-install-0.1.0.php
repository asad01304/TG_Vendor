<?php

$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('vendor')};

CREATE TABLE {$this->getTable('vendor')} (
  `vendor_id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `field_values` text NOT NULL,
  `vendor_type` varchar(127) NOT NULL,
  PRIMARY KEY  (`vendor_id`)
) ENGINE=MyISAM  ;


DROP TABLE IF EXISTS {$this->getTable('product_vendor')};

CREATE TABLE {$this->getTable('product_vendor')} (
  `entity_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  PRIMARY KEY (`entity_id`),
  UNIQUE KEY `product_id` (`product_id`)
) ENGINE=MyISAM ;

DROP TABLE IF EXISTS {$this->getTable('vendor_notification_log')};

CREATE TABLE {$this->getTable('vendor_notification_log')} (
	`entity_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`order_id` INT NOT NULL ,
	`item_id` INT NOT NULL ,
	`product_id` INT NOT NULL,
	`vendor_id` INT NOT NULL ,
	`status` SMALLINT NOT NULL
) ENGINE = MYISAM ;

    ");
 
$installer->endSetup(); 