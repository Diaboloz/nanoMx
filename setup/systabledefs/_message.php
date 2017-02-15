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
// Tabellenstruktur fuer Tabelle `mx_message`
if (!isset($tables["${prefix}_message"])) {
    $sqlqry[] = "
CREATE TABLE `${prefix}_message` (
  `mid` int(11) NOT NULL auto_increment,
  `title` varchar(100) NOT NULL default '',
  `content` text NULL,
  `date` int(10) NOT NULL default '0',
  `expire` int(7) NOT NULL default '0',
  `active` int(1) NOT NULL default '1',
  `view` int(1) NOT NULL default '1',
  `mlanguage` varchar(30) NOT NULL default '',
  PRIMARY KEY  (`mid`),
  KEY `active` (`active`),
  KEY `title` (`title`),
  KEY `date` (`date`),
  KEY `mlanguage` (`mlanguage`)
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8  COLLATE utf8_unicode_ci;
";
}

if (isset($sqlqry)) {
    setupDoAllQueries($sqlqry);
    unset($sqlqry);
}

if (isset($tables["${prefix}_message"])) {
    $indexes = setupGetTableIndexes("${prefix}_message");
    if (isset($indexes['mid'])) $sqlqry[] = "ALTER TABLE `${prefix}_message` DROP INDEX `mid`;";
    if (isset($indexes['PRIMARY']) && $indexes['PRIMARY']['Column_name'] != 'mid') {
        $sqlqry[] = "ALTER TABLE `${prefix}_message` DROP PRIMARY KEY , ADD PRIMARY KEY ( `mid` ) ";
    } else if (!isset($indexes['PRIMARY'])) {
        $sqlqry[] = "ALTER TABLE `${prefix}_message` ADD PRIMARY KEY ( `mid` ) ";
    }
    if (!isset($indexes['active'])) $sqlqry[] = "ALTER TABLE `${prefix}_message` ADD INDEX `active` ( `active` ) ;";
    if (!isset($indexes['title'])) $sqlqry[] = "ALTER TABLE `${prefix}_message` ADD INDEX `title` ( `title` ) ;";
    if (!isset($indexes['date'])) $sqlqry[] = "ALTER TABLE `${prefix}_message` ADD INDEX `date` ( `date` ) ;";
    if (!isset($indexes['mlanguage'])) $sqlqry[] = "ALTER TABLE `${prefix}_message` ADD INDEX `mlanguage` ( `mlanguage` ) ;";
    // Felder aktualisieren
    $tf = setupGetTableFields("${prefix}_message");
    if ($tf['content']['Default'] !== null || $tf['date']['Default'] === null || $tf['date']['Type'] != 'int(10)') {
        $sqlqry[] = "ALTER TABLE `${prefix}_message`
                CHANGE `content` `content` text NULL,
                CHANGE `date` `date` int(10) NOT NULL default '0' ";
    }
}

if (isset($sqlqry)) {
    setupDoAllQueries($sqlqry);
    unset($sqlqry);
}

$numrows = sql_num_rows(sql_query("SELECT `mid` FROM `${prefix}_message` LIMIT 1;"));
if (!$numrows) {
    $sqlqry[] = "INSERT INTO `${prefix}_message` (`mid`, `title`, `content`, `date`, `expire`, `active`, `view`, `mlanguage`) VALUES (1, '" . _NEWINSTALLMESSAGEBLOCKTITLE . "', '" . _NEWINSTALLMESSAGEBLOCK . "', 1058896704, 0, 1, 1, '');";
}

if (isset($sqlqry)) {
    setupDoAllQueries($sqlqry);
    unset($sqlqry);
}

?>
