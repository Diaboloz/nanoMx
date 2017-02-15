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
// Tabellenstruktur fuer Tabelle `mx_sys_session`
// falls noch alte Version mit prefix, Tabelle umbenennen
if (isset($tables["${prefix}_sys_session"]) && !isset($tables["${prefix}_sys_session"]) && $prefix != $user_prefix) {
    $sql = "RENAME TABLE `${prefix}_sys_session` TO `${prefix}_sys_session`;";
    setupDoAllQueries($sql);
    unset($sql);
    if (setupTableExist("${prefix}_sys_session", 'refresh')) {
        unset($tables["${prefix}_sys_session"]);
    } 
} 

if (!isset($tables["${prefix}_sys_session"])) {
    $sqlqry[] = "
CREATE TABLE `${prefix}_sys_session` (
  `sesskey` varchar(32) NOT NULL default '',
  `expiry` int(11) unsigned NOT NULL default '0',
  `data` text NULL,
  PRIMARY KEY  (`sesskey`),
  KEY `expiry` (`expiry`)
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8  COLLATE utf8_unicode_ci;
";
} 

if (isset($sqlqry)) {
    setupDoAllQueries($sqlqry);
    unset($sqlqry);
} 

$indexes = setupGetTableIndexes("${prefix}_sys_session");
if (!isset($indexes['expiry'])) $sqlqry[] = "ALTER TABLE `${prefix}_sys_session` ADD INDEX `expiry` ( `expiry` ) ";

if (isset($sqlqry)) {
    setupDoAllQueries($sqlqry);
    unset($sqlqry);
} 

?>