# TYPO3 Extension Manager dump 1.0
#
# Host: TYPO3_host    Database: t3_typo3site_new
#--------------------------------------------------------


#
# Table structure for table 'tx_extrepwizard_static_presets'
#
CREATE TABLE tx_extrepwizard_static_presets (
  fieldname varchar(40) DEFAULT '' NOT NULL,
  title tinytext NOT NULL,
  type varchar(40) DEFAULT '' NOT NULL,
  appdata blob NOT NULL,
  tstamp int(11) DEFAULT '0' NOT NULL,
  PRIMARY KEY (fieldname)
);