<?php
/**
 * This file is part of
 * pragmaMx - Web Content Management System.
 * Copyright by pragmaMx Developer Team - http://www.pragmamx.org
 *
 * pragmaMx is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * $Revision: 6 $
 * $Author: PragmaMx $
 * $Date: 2015-07-08 09:07:06 +0200 (Mi, 08. Jul 2015) $
 */

defined('mxMainFileLoaded') or die('access denied');
unset($sqlqry);

if (!isset($tables["${prefix}_securelog"])) {
    $sqlqry[] = "
CREATE TABLE `${prefix}_securelog` (
  `id` int(11) NOT NULL auto_increment,
  `log_ip` varchar(40) NOT NULL default '',
  `log_time` int(14) NOT NULL default '0',
  `log_eventid` varchar(25) NOT NULL default '',
  `log_event` text NULL,
  `uname` varchar(25) NOT NULL default '',
  `aid` varchar(25) NOT NULL default '',
  `request` text NULL,
  PRIMARY KEY  (`id`),
  KEY `log_time` (`log_time`)
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8  COLLATE utf8_unicode_ci;
";
}

if (isset($tables["${prefix}_securelog"])) {
    // Felder aktualisieren
    $tf = setupGetTableFields("${prefix}_securelog");
    if (!isset($tf['uname'])) $sqlqry[] = "ALTER TABLE `${prefix}_securelog` ADD `uname` VARCHAR( 25 ) NOT NULL default '', ADD `aid` VARCHAR( 25 ) NOT NULL  default '';";
    if (!isset($tf['request'])) $sqlqry[] = "ALTER TABLE `${prefix}_securelog` ADD `request` text NULL ;";
    if (!isset($tf['id'])) $sqlqry[] = "ALTER TABLE `${prefix}_securelog` ADD `id` int(11) NOT NULL auto_increment FIRST, ADD PRIMARY KEY ( `id` );";
    // $indexes = setupGetTableIndexes("${prefix}_securelog");
    // if (!isset($indexes['PRIMARY'])) {
    // $sqlqry[] = "ALTER TABLE `${prefix}_securelog` ADD PRIMARY KEY ( `id` ) ";
    // }
}

if (isset($tables["${prefix}_securelog"])) {
    $tf = setupGetTableFields("${prefix}_securelog");
    switch (true) {
        case $tf['log_ip']['Type'] != 'varchar(40)':
        case $tf['log_event']['Default'] !== null:
        case $tf['request']['Default'] !== null:
            $sqlqry[] = "ALTER TABLE `${prefix}_securelog`
                CHANGE `log_ip` `log_ip` varchar(40) NOT NULL default '',
                CHANGE `log_event` `log_event` text NULL,
                CHANGE `request` `request` text NULL";
    }
}

if (isset($sqlqry)) {
    setupDoAllQueries($sqlqry);
    unset($sqlqry);
}

?>