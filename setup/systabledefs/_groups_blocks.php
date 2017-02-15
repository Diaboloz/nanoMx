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
// sicherstellen, dass die Blocktabelle bereits aktualisiert ist
include_once(__DIR__ . '/_blocks.php');
// --------------------------------------------------------
// Tabellenstruktur fuer Tabelle `mx_groups_blocks`
// neuinstall oder Tabelle fehlt, Tabelle neu anlegen
if (!isset($tables["${prefix}_groups_blocks"])) {
    $sqlqry[] = "
CREATE TABLE `${prefix}_groups_blocks` (
  `group_id` int(7) NOT NULL default '0',
  `block_id` int(7) NOT NULL default '0',
  UNIQUE KEY `group_id` (`group_id`,`block_id`),
  KEY `block_id` (`block_id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8  COLLATE utf8_unicode_ci;
";
}

if (isset($sqlqry)) {
    setupDoAllQueries($sqlqry);
    unset($sqlqry);
}
// // die bloecke in Gruppentabelle einfuegen
$result = sql_query("SELECT COUNT(`block_id`) FROM `${prefix}_groups_blocks`");
list($numrows) = sql_fetch_row($result);
if (!$numrows) {
    $sqlqry[] = "INSERT INTO `${prefix}_groups_blocks` ( block_id, group_id )
    SELECT DISTINCT bid, 1 AS group_id
    FROM `${prefix}_blocks`
    WHERE ((active=1) AND (view=1))
    ORDER  BY bid";
}

if (isset($tables["${prefix}_groups_blocks"])) {
    $indexes = setupGetTableIndexes("${prefix}_groups_blocks");
    if (!isset($indexes['block_id'])) $sqlqry[] = "ALTER TABLE `${prefix}_groups_blocks` ADD INDEX block_id( `block_id` ) ;";
}

if (isset($sqlqry)) {
    setupDoAllQueries($sqlqry);
    unset($sqlqry);
}

?>