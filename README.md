cappd.me link shortener
=====

cappd.me is a lightweight link shortener in PHP 5.3 using the Slim framework, and NotORM for database access. It supports setting an
expiration date and total cap before a link stops working and is then deleted from the database.

see http://cappd.me

Requirements
====
Web server with a database supported by PDO (mysql), PHP 5.3+.

Config
====
-Fill in the required information in index.php
-Run the following sql query to create the necessary table

```mysql
-- 
-- Table structure for table `links`
-- 
CREATE TABLE `links` (
  `id` int(50) NOT NULL auto_increment,
  `url` varchar(200) NOT NULL,
  `expiration_time` varchar(50) NOT NULL,
  `daily_cap` int(50) NOT NULL default '0',
  `total_cap` bigint(50) NOT NULL default '0',
  `current_daily_cap` int(50) NOT NULL default '0',
  `current_total_cap` bigint(50) NOT NULL default '0',
  KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1000 DEFAULT CHARSET=latin1 AUTO_INCREMENT=1000 ;
```
-See Slim documentation on how to set it up

Released under
<a rel="license" href="http://creativecommons.org/licenses/by-sa/3.0/deed.en_US"><img alt="Creative Commons License" style="border-width:0" src="http://i.creativecommons.org/l/by-sa/3.0/88x31.png" /></a><br /><span xmlns:dct="http://purl.org/dc/terms/" property="dct:title">cappd.me</span> by <a xmlns:cc="http://creativecommons.org/ns#" href="https://github.com/icechen1/cappd/" property="cc:attributionName" rel="cc:attributionURL">Yu Chen Hou</a> is licensed under a <a rel="license" href="http://creativecommons.org/licenses/by-sa/3.0/deed.en_US">Creative Commons Attribution-ShareAlike 3.0 Unported License</a>.
