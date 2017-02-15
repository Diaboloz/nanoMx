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
// Tabellenstruktur fuer Tabelle `mx_main`
// Startseitenmodul
if (!isset($tables["${prefix}_main"])) {
    $sqlqry[] = "
CREATE TABLE `${prefix}_main` (
  `main_module` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`main_module`)
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8  COLLATE utf8_unicode_ci;
";
} else {
    // Indexe
    $indexes = setupGetTableIndexes("${prefix}_main");
    if (!isset($indexes['PRIMARY'])) {
        $sqlqry[] = "ALTER TABLE `${prefix}_main` ADD PRIMARY KEY ( `main_module` );";
    } else if ($indexes['PRIMARY']['Column_name'] != 'main_module') {
        $sqlqry[] = "ALTER TABLE `${prefix}_main` DROP PRIMARY KEY, ADD PRIMARY KEY ( `main_module` );";
    }
}

if (isset($sqlqry)) {
    setupDoAllQueries($sqlqry);
    unset($sqlqry);
}
// Daten fuer Tabelle `mx_main`
$numrows = sql_num_rows(sql_query("SELECT `main_module` FROM `${prefix}_main`"));
// falls mehr als 1 Datensatz, alle loeschen
if ($numrows > 1) {
    $sqlqry[] = "TRUNCATE TABLE `${prefix}_main`";
}
// falls nicht genau 1 Datensatz, Standard einfuegen
if ($numrows != 1) {
    $sqlqry[] = "REPLACE INTO `${prefix}_main` (`main_module`) VALUES ('blank_Home');";
}

if (isset($sqlqry)) {
    setupDoAllQueries($sqlqry);
    unset($sqlqry);
}

?>