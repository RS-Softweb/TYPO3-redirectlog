#
# Table structure for table 'tx_redirectlog_urls'
#
CREATE TABLE tx_redirectlog_urls (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	sorting int(10) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	starttime int(11) DEFAULT '0' NOT NULL,
	endtime int(11) DEFAULT '0' NOT NULL,
	partitial tinyint(3) DEFAULT '0' NOT NULL,
	old_url tinytext,
	new_pageid text,
	new_url tinytext,
	header int(11) DEFAULT '0' NOT NULL,
	language int(11) DEFAULT '0' NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_redirectlog_log'
#
CREATE TABLE tx_redirectlog_log (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	old_url tinytext,
	new_url tinytext,
	referer tinytext,
	referer_extern tinyint(3) DEFAULT '0' NOT NULL,
	user_browser tinytext,
	user_ip tinytext,
	user_name tinytext,
	user_spider tinytext,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);