#
# Table structure for table 'pages'
#
CREATE TABLE pages (
	tx_cagrel_link tinyint(3) DEFAULT '0' NOT NULL,
	tx_cagrel_stdrel int(11) DEFAULT '0' NOT NULL,
	tx_cagrel_specrel varchar(255) DEFAULT '' NOT NULL,
	tx_cagrel_params varchar(255) DEFAULT '' NOT NULL
);