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
// --------------------------------------------------------
// Tabellenstruktur fuer Tabelle `mx_counter`
if (!isset($tables["${prefix}_counter"])) {
    $sqlqry[] = "
CREATE TABLE `${prefix}_counter` (
  `type` varchar(80) NOT NULL DEFAULT '',
  `var` varchar(80) NOT NULL DEFAULT '',
  `icon` varchar(100) DEFAULT NULL,
  `count` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY  (`type`,`var`)
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8  COLLATE utf8_unicode_ci;
";
    // Daten fuer Tabelle `mx_counter`
    $sqlqry[] = "INSERT INTO `${prefix}_counter` (`type`, `var`, `count`) VALUES ('total', 'hits', 0);";
    $sqlqry[] = "INSERT INTO `${prefix}_counter` (`type`, `var`, `count`) VALUES ('reset', 'date', " . time() . ");";
} else {
    $tf = setupGetTableFields("${prefix}_counter");
    // zusaetzliche Felder
    if (!isset($tf['icon'])) {
        $sqlqry[] = "TRUNCATE TABLE `${prefix}_counter`";
        $sqlqry[] = "ALTER TABLE `${prefix}_counter` ADD `icon` VARCHAR( 100 ) NULL AFTER var;";
        $sqlqry[] = "INSERT INTO `${prefix}_counter` SET `type`='reset', `var`='date', `count`=" . time() . ";";
        $sqlqry[] = "INSERT INTO `${prefix}_counter` SET `type`='total', `var`='hits', `count`=0;";
    } else {
        // TODO: evtl. TOTAL noch neu berechnen
    }
}

if (isset($sqlqry)) {
    setupDoAllQueries($sqlqry);
    unset($sqlqry);
}

$indexes = setupGetTableIndexes("${prefix}_counter");

if (!isset($indexes['PRIMARY']) || !isset($indexes['PRIMARY']['all_fields']['type']) || !isset($indexes['PRIMARY']['all_fields']['var'])) {
    $sqlqry[] = "DELETE FROM `${prefix}_counter`";
    if (isset($indexes['PRIMARY'])) {
        $sqlqry[] = "ALTER TABLE `${prefix}_counter` DROP PRIMARY KEY , ADD PRIMARY KEY ( `type` , `var` ) ";
    }
    $qry = "SELECT `type` , `var` , SUM(`count`) FROM `${prefix}_counter` GROUP BY `type` , `var`";
    $result = sql_query($qry);
    print sql_error();
    while (list($type , $var , $count) = sql_fetch_row($result)) {
        $sqlqry[] = "REPLACE INTO `${prefix}_counter` ( `type` , `var` , `count` ) VALUES ('$type' , '$var' , '$count');";
    }
}

if (!isset($indexes['PRIMARY'])) {
    $sqlqry[] = "ALTER TABLE `${prefix}_counter` ADD PRIMARY KEY ( `type` , `var` ) ";
}

if (isset($sqlqry)) {
    setupDoAllQueries($sqlqry);
    unset($sqlqry);
}

?>