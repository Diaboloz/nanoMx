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

if (!isset($tables["${prefix}_tracking"])) {
    $sqlqry[] = "
CREATE TABLE `${prefix}_tracking` (
  `tracktime` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `ip` varchar(40) NOT NULL default '',
  `uid` int(11) NOT NULL default '0',
  `server` varchar(90) NOT NULL default '',
  `referer` varchar(255) NOT NULL default '',
  `requrl` varchar(255) NOT NULL default '',
  `trackid` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`ip`,`tracktime`),
  KEY `requrl` (`requrl`),
  KEY `uid` (`uid`),
  KEY `tracktime` (`tracktime`),
  KEY `trackid` (`trackid`)
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8  COLLATE utf8_unicode_ci;
";
} else {
    $tf = setupGetTableFields("{$prefix}_tracking");
    switch (true) {
        case $tf['ip']['Type'] != 'varchar(40)':
        case $tf['tracktime']['Type'] != 'timestamp':
        case $tf['tracktime']['Default'] != 'CURRENT_TIMESTAMP':
            /* vorher gültige Daten erzeugen, sonst Fehler:
             * Incorrect datetime value: '0000-00-00 00:00:00' for column '***' at....*/
            $sqlqry[] = "UPDATE `${prefix}_tracking` SET `tracktime` = NOW( ) WHERE `tracktime` LIKE '0000-00-00%'";
            $sqlqry[] = "ALTER TABLE `${prefix}_tracking`
                CHANGE `ip` `ip` varchar(40) NOT NULL default '',
                CHANGE `tracktime` `tracktime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
                ";
    }
}

if (isset($sqlqry)) {
    setupDoAllQueries($sqlqry);
    unset($sqlqry);
}

$indexes = setupGetTableIndexes("${prefix}_tracking");
if (isset($indexes['ip'])) $sqlqry[] = "ALTER TABLE `${prefix}_tracking` DROP INDEX `ip` ";
if (isset($indexes['PRIMARY']) && $indexes['PRIMARY']['Column_name'] != 'ip') {
    $sqlqry[] = "ALTER TABLE `${prefix}_tracking` DROP PRIMARY KEY , ADD PRIMARY KEY ( `ip` ) ";
} else if (!isset($indexes['PRIMARY'])) {
    $sqlqry[] = "ALTER TABLE `${prefix}_tracking` ADD PRIMARY KEY ( `ip` ) ";
}

if (isset($sqlqry)) {
    setupDoAllQueries($sqlqry);
    unset($sqlqry);
}

?>
