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
// sicherstellen, dass die Modultabelle bereits aktualisiert ist
include_once(__DIR__ . '/_modules.php');
// --------------------------------------------------------
// Tabellenstruktur fuer Tabelle `mx_groups_modules`
// neuinstall oder Tabelle fehlt, Tabelle neu anlegen
if (!isset($tables["${prefix}_groups_modules"])) {
    $sqlqry[] = "
CREATE TABLE `${prefix}_groups_modules` (
  `group_id` int(7) NOT NULL default '0',
  `module_id` int(7) NOT NULL default '0',
  UNIQUE KEY `group_id` (`group_id`,`module_id`),
  KEY `module_id` (`module_id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8  COLLATE utf8_unicode_ci;
";
}

if (isset($sqlqry)) {
    setupDoAllQueries($sqlqry);
    unset($sqlqry);
}
// // die Module in Gruppentabelle einfuegen
$numrows = sql_num_rows(sql_query("SELECT `module_id` FROM `${prefix}_groups_modules` LIMIT 1;"));
if (!$numrows) {
    $sqlqry[] = "INSERT INTO ${prefix}_groups_modules ( group_id, module_id )
    SELECT DISTINCT 1 AS group_id, mid
    FROM ${prefix}_modules
    WHERE ((active=1) AND (view=1))
    ORDER BY mid";
}

if (isset($tables["${prefix}_groups_modules"])) {
    $indexes = setupGetTableIndexes("${prefix}_groups_modules");
    if (!isset($indexes['module_id'])) $sqlqry[] = "ALTER TABLE `${prefix}_groups_modules` ADD INDEX module_id( `module_id` ) ;";
}

if (isset($sqlqry)) {
    setupDoAllQueries($sqlqry);
    unset($sqlqry);
}

?>