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
// sicherstellen, dass die Usertabelle bereits aktualisiert ist
include_once(__DIR__ . '/_users.php');
// --------------------------------------------------------
// Tabellenstruktur fuer Tabelle `mx_authors`
if (!isset($tables["{$prefix}_authors"])) {
    $sqlqry[] = "
CREATE TABLE `{$prefix}_authors` (
  `admin_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `aid` varchar(25) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `email` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `pwd` varchar(140) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `pwd_salt` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `counter` int(11) NOT NULL DEFAULT '0',
  `radminarticle` tinyint(1) NOT NULL DEFAULT '0',
  `radmintopic` tinyint(1) NOT NULL DEFAULT '0',
  `radminuser` tinyint(1) NOT NULL DEFAULT '0',
  `radminsurvey` tinyint(1) NOT NULL DEFAULT '0',
  `radminsection` tinyint(1) NOT NULL DEFAULT '0',
  `radminlink` tinyint(1) NOT NULL DEFAULT '0',
  `radminephem` tinyint(1) NOT NULL DEFAULT '0',
  `radminfaq` tinyint(1) NOT NULL DEFAULT '0',
  `radmindownload` tinyint(1) NOT NULL DEFAULT '0',
  `radminreviews` tinyint(1) NOT NULL DEFAULT '0',
  `radminnewsletter` tinyint(1) NOT NULL DEFAULT '0',
  `radminforum` tinyint(1) NOT NULL DEFAULT '0',
  `radmincontent` tinyint(1) NOT NULL DEFAULT '0',
  `radminency` tinyint(1) NOT NULL DEFAULT '0',
  `radminsuper` tinyint(1) NOT NULL DEFAULT '0',
  `radmingroups` tinyint(1) NOT NULL DEFAULT '0',
  `radmincalendar` tinyint(1) NOT NULL DEFAULT '0',
  `user_uid` int(11) unsigned DEFAULT NULL,
  `isgod` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`aid`),
  UNIQUE KEY `admin_id` (`admin_id`),
  UNIQUE KEY `isgod` (`isgod`),
  KEY `user_uid` (`user_uid`),
  KEY `radminarticle` (`radminarticle`),
  KEY `radmintopic` (`radmintopic`),
  KEY `radminuser` (`radminuser`),
  KEY `radminsurvey` (`radminsurvey`),
  KEY `radminsection` (`radminsection`),
  KEY `radminlink` (`radminlink`),
  KEY `radminephem` (`radminephem`),
  KEY `radminfaq` (`radminfaq`),
  KEY `radmindownload` (`radmindownload`),
  KEY `radminreviews` (`radminreviews`),
  KEY `radminnewsletter` (`radminnewsletter`),
  KEY `radminforum` (`radminforum`),
  KEY `radmincontent` (`radmincontent`),
  KEY `radminency` (`radminency`),
  KEY `radminsuper` (`radminsuper`),
  KEY `radmingroups` (`radmingroups`),
  KEY `radmincalendar` (`radmincalendar`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
";
    $god_field_exist = true;
    $uid_field_exist = true;
} else {
    // Felder aktualisieren
    $tf = setupGetTableFields("{$prefix}_authors");
    // Felder ergaenzen/loeschen
    // fb war so schlau und hat die admintabelle ab nuke 7.4 kastriert, deshalb alle Felder checken
    if (!isset($tf['radminarticle'])) $sqlqry[] = "ALTER TABLE `{$prefix}_authors` ADD radminarticle tinyint(1) NOT NULL DEFAULT '0' AFTER counter;";
    if (!isset($tf['radmintopic'])) $sqlqry[] = "ALTER TABLE `{$prefix}_authors` ADD radmintopic tinyint(1) NOT NULL DEFAULT '0' AFTER radminarticle;";
    if (!isset($tf['radminuser'])) $sqlqry[] = "ALTER TABLE `{$prefix}_authors` ADD radminuser tinyint(1) NOT NULL DEFAULT '0' AFTER radmintopic;";
    if (!isset($tf['radminsurvey'])) $sqlqry[] = "ALTER TABLE `{$prefix}_authors` ADD radminsurvey tinyint(1) NOT NULL DEFAULT '0' AFTER radminuser;";
    if (!isset($tf['radminsection'])) $sqlqry[] = "ALTER TABLE `{$prefix}_authors` ADD radminsection tinyint(1) NOT NULL DEFAULT '0' AFTER radminsurvey;";
    if (!isset($tf['radminlink'])) $sqlqry[] = "ALTER TABLE `{$prefix}_authors` ADD radminlink tinyint(1) NOT NULL DEFAULT '0' AFTER radminsection;";
    if (!isset($tf['radminephem'])) $sqlqry[] = "ALTER TABLE `{$prefix}_authors` ADD radminephem tinyint(1) NOT NULL DEFAULT '0' AFTER radminlink;";
    if (!isset($tf['radminfaq'])) $sqlqry[] = "ALTER TABLE `{$prefix}_authors` ADD radminfaq tinyint(1) NOT NULL DEFAULT '0' AFTER radminephem;";
    if (!isset($tf['radmindownload'])) $sqlqry[] = "ALTER TABLE `{$prefix}_authors` ADD radmindownload tinyint(1) NOT NULL DEFAULT '0' AFTER radminfaq;";
    if (!isset($tf['radminreviews'])) $sqlqry[] = "ALTER TABLE `{$prefix}_authors` ADD radminreviews tinyint(1) NOT NULL DEFAULT '0' AFTER radmindownload;";
    if (!isset($tf['radminnewsletter'])) $sqlqry[] = "ALTER TABLE `{$prefix}_authors` ADD radminnewsletter tinyint(1) NOT NULL DEFAULT '0' AFTER radminreviews;";
    if (!isset($tf['radminforum'])) $sqlqry[] = "ALTER TABLE `{$prefix}_authors` ADD radminforum tinyint(1) NOT NULL DEFAULT '0' AFTER radminnewsletter";
    if (!isset($tf['radmincontent'])) $sqlqry[] = "ALTER TABLE `{$prefix}_authors` ADD radmincontent tinyint(1) NOT NULL DEFAULT '0' AFTER radminforum";
    if (!isset($tf['radminency'])) $sqlqry[] = "ALTER TABLE `{$prefix}_authors` ADD radminency tinyint(1) NOT NULL DEFAULT '0' AFTER radmincontent";
    if (!isset($tf['radminsuper'])) $sqlqry[] = "ALTER TABLE `{$prefix}_authors` ADD radminsuper tinyint(1) NOT NULL DEFAULT '0' AFTER radminency";
    if (!isset($tf['radmincalendar'])) $sqlqry[] = "ALTER TABLE `{$prefix}_authors` ADD radmincalendar tinyint(1) NOT NULL DEFAULT '0' AFTER radminsuper";
    if (!isset($tf['radmingroups'])) $sqlqry[] = "ALTER TABLE `{$prefix}_authors` ADD radmingroups tinyint(1) NOT NULL DEFAULT '0' AFTER radminsuper";

    if (!isset($tf['user_uid'])) {
        $sqlqry[] = "ALTER TABLE `{$prefix}_authors` ADD user_uid INT( 11 ) UNSIGNED;";
        $uid_field_exist = false;
    } else {
        $uid_field_exist = true;
    }
    // ab v2.0
    if (!isset($tf['admin_id'])) {
        $sqlqry[] = "ALTER TABLE `{$prefix}_authors` ADD `admin_id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT FIRST , ADD UNIQUE (`admin_id`)";
    }
    if (!isset($tf['pwd_salt'])) {
        if (isset($tf['pwdsalt'])) {
            // in den ersten Betas war das Feld falsch benannt
            $sqlqry[] = "ALTER TABLE `{$prefix}_authors` CHANGE `pwdsalt` `pwd_salt` VARCHAR( 32 ) NULL DEFAULT NULL ;";
        } else {
            $sqlqry[] = "ALTER TABLE `{$prefix}_authors` ADD `pwd_salt` VARCHAR( 32 ) NULL DEFAULT NULL AFTER `pwd` ;";
        }
    }

    if (!isset($tf['isgod'])) {
        $god_field_exist = false;
        $sqlqry[] = "ALTER TABLE `{$prefix}_authors` ADD `isgod` tinyint(1) NULL DEFAULT NULL ;";
    } else {
        $god_field_exist = true;
    }
    if (isset($tf['admlanguage'])) $sqlqry[] = "ALTER TABLE `{$prefix}_authors` DROP admlanguage;";

    /* unnoetig da nie in pragmaMx oder nuke vorhanden */
    if (isset($tf['radminfilem'])) $sqlqry[] = "ALTER TABLE `{$prefix}_authors` DROP radminfilem;";

    switch (true) {
        case $tf['aid']['Default'] === null :
        case $tf['aid']['Type'] != 'varchar(25)' :
        case $tf['name']['Default'] === null :
        case $tf['name']['Type'] != 'varchar(50)' :
        case $tf['url']['Default'] === null :
        case $tf['url']['Type'] != 'varchar(255)' :
        case $tf['email']['Default'] === null :
        case $tf['email']['Type'] != 'varchar(100)' :
        case $tf['pwd']['Type'] != 'varchar(140)' :
            $sqlqry[] = "ALTER TABLE `{$prefix}_authors`
                CHANGE `aid` `aid` varchar(25) NOT NULL default '',
                CHANGE `name` `name` varchar(50) NOT NULL default '',
                CHANGE `url` `url` varchar(255) NOT NULL default '',
                CHANGE `email` `email` varchar(100) NOT NULL default '',
                CHANGE `pwd` `pwd` varchar(140) NOT NULL default ''
                ";
    }
}

if (isset($sqlqry)) {
    setupDoAllQueries($sqlqry);
    unset($sqlqry);
}

if (isset($tables["{$prefix}_authors"])) {
    /* Indexe erstellen/aktualisieren */
    $indexes = setupGetTableIndexes("{$prefix}_authors");
    if (isset($indexes['aid'])) $sqlqry[] = "ALTER TABLE `{$prefix}_authors` DROP INDEX `aid` ";
    if (isset($indexes['PRIMARY']) && $indexes['PRIMARY']['Column_name'] != 'aid') {
        $sqlqry[] = "ALTER TABLE `{$prefix}_authors` DROP PRIMARY KEY, ADD PRIMARY KEY ( `aid` );";
    } else if (!isset($indexes['PRIMARY'])) {
        $sqlqry[] = "ALTER TABLE `{$prefix}_authors` ADD PRIMARY KEY ( `aid` );";
    }

    /* ab v2.0 */
    if (!isset($indexes['isgod'])) {
        $sqlqry[] = "ALTER TABLE `{$prefix}_authors` ADD UNIQUE `isgod` ( `isgod` ) ;";
    } else if (isset($indexes['isgod']) && $indexes['isgod']['Non_unique'] != '0') {
        $sqlqry[] = "ALTER TABLE `{$prefix}_authors` DROP INDEX `isgod` , ADD UNIQUE `isgod` ( `isgod` ) ;";
    }

    /* eindeutigen Index für Name entfernen, name kann seit v2.0 beliebig sein */
    if (isset($indexes['name'])) {
        $sqlqry[] = "ALTER TABLE `{$prefix}_authors` DROP INDEX `name` ;";
    }

    if (!isset($indexes['user_uid'])) $sqlqry[] = "ALTER TABLE `{$prefix}_authors` ADD INDEX `user_uid` ( `user_uid` ) ;";
    if (!isset($indexes['radminarticle'])) $sqlqry[] = "ALTER TABLE `{$prefix}_authors` ADD INDEX `radminarticle` ( `radminarticle` );";
    if (!isset($indexes['radmintopic'])) $sqlqry[] = "ALTER TABLE `{$prefix}_authors` ADD INDEX `radmintopic` ( `radmintopic` );";
    if (!isset($indexes['radminuser'])) $sqlqry[] = "ALTER TABLE `{$prefix}_authors` ADD INDEX `radminuser` ( `radminuser` );";
    if (!isset($indexes['radminsurvey'])) $sqlqry[] = "ALTER TABLE `{$prefix}_authors` ADD INDEX `radminsurvey` ( `radminsurvey` );";
    if (!isset($indexes['radminsection'])) $sqlqry[] = "ALTER TABLE `{$prefix}_authors` ADD INDEX `radminsection` ( `radminsection` );";
    if (!isset($indexes['radminlink'])) $sqlqry[] = "ALTER TABLE `{$prefix}_authors` ADD INDEX `radminlink` ( `radminlink` );";
    if (!isset($indexes['radminephem'])) $sqlqry[] = "ALTER TABLE `{$prefix}_authors` ADD INDEX `radminephem` ( `radminephem` );";
    if (!isset($indexes['radminfaq'])) $sqlqry[] = "ALTER TABLE `{$prefix}_authors` ADD INDEX `radminfaq` ( `radminfaq` );";
    if (!isset($indexes['radmindownload'])) $sqlqry[] = "ALTER TABLE `{$prefix}_authors` ADD INDEX `radmindownload` ( `radmindownload` );";
    if (!isset($indexes['radminreviews'])) $sqlqry[] = "ALTER TABLE `{$prefix}_authors` ADD INDEX `radminreviews` ( `radminreviews` );";
    if (!isset($indexes['radminnewsletter'])) $sqlqry[] = "ALTER TABLE `{$prefix}_authors` ADD INDEX `radminnewsletter` ( `radminnewsletter` );";
    if (!isset($indexes['radminforum'])) $sqlqry[] = "ALTER TABLE `{$prefix}_authors` ADD INDEX `radminforum` ( `radminforum` );";
    if (!isset($indexes['radmincontent'])) $sqlqry[] = "ALTER TABLE `{$prefix}_authors` ADD INDEX `radmincontent` ( `radmincontent` );";
    if (!isset($indexes['radminency'])) $sqlqry[] = "ALTER TABLE `{$prefix}_authors` ADD INDEX `radminency` ( `radminency` );";
    if (!isset($indexes['radminsuper'])) $sqlqry[] = "ALTER TABLE `{$prefix}_authors` ADD INDEX `radminsuper` ( `radminsuper` );";
    if (!isset($indexes['radmingroups'])) $sqlqry[] = "ALTER TABLE `{$prefix}_authors` ADD INDEX `radmingroups` ( `radmingroups` );";
    if (!isset($indexes['radmincalendar'])) $sqlqry[] = "ALTER TABLE `{$prefix}_authors` ADD INDEX `radmincalendar` ( `radmincalendar` );";
}

if (isset($sqlqry)) {
    setupDoAllQueries($sqlqry);
    unset($sqlqry);
}

/* God Account anpassen */
if (!$god_field_exist) {
    $qry = "SELECT aid, name, isgod, radminsuper FROM `{$prefix}_authors` WHERE `name`='" . PMX_SYSADMIN_NAME . "' LIMIT 1";
    $result = sql_query($qry);
    $row = sql_fetch_assoc($result);
    if ($row) {
        // alle nicht-God zurücksetzen
        $sqlqry[] = "UPDATE `{$prefix}_authors` SET `isgod`=NULL  WHERE `name`<>'" . PMX_SYSADMIN_NAME . "' AND `aid`<>'" . mxAddSlashesForSQL($row['aid']) . "';";
        // beim ersten gefundenen God das neue Feld aktualisieren, ausserdem den Namen korrigieren
        $sqlqry[] = "UPDATE `{$prefix}_authors` SET `isgod`=1, `name`=`aid`, `radminsuper`=1 WHERE `name`='" . PMX_SYSADMIN_NAME . "' AND `aid`='" . mxAddSlashesForSQL($row['aid']) . "';";
    } else {
        // wenn kein God vorhanden (was unwahrscheinlich ist), versuchen den ersten radminsuper zu verwenden
        $sqlqry[] = "UPDATE `{$prefix}_authors` SET `isgod`=1 WHERE `name`<>'" . PMX_SYSADMIN_NAME . "' AND `radminsuper`=1 LIMIT 1 ;";
    }
}

/* Autologinfeld in Admintabelle anpassen */
if (!$uid_field_exist && isset($tables["{$prefix}_authors"])) {
    if (isset($tables["{$prefix}_authors_users"])) {
        // wenn das alte Modul selfAdmin installiert war
        $result = sql_query("SELECT user_uid, author_aid FROM `{$prefix}_authors_users`;");
        while (list($uid, $aid) = sql_fetch_row($result)) {
            $sqlqry[] = "UPDATE `{$prefix}_authors`
                        SET user_uid=$uid
                        WHERE aid='" . mxAddSlashesForSQL($aid) . "'
                          AND (user_uid Is Null Or user_uid=0);";
        }
    } else {
        // wenn das alte Modul selfAdmin NICHT installiert war
        $result = sql_query("SELECT u.uid, a.aid FROM `{$prefix}_authors` AS a
                            LEFT JOIN `{$user_prefix}_users` AS u
                            ON (a.email = u.email)
                              AND (a.aid = u.uname)
                            WHERE (u.uid Is Not Null)
                              AND (a.user_uid Is Null Or a.user_uid=0);");
        while (list($uid, $aid) = sql_fetch_row($result)) {
            $sqlqry[] = "UPDATE `{$prefix}_authors`
                        SET user_uid=$uid
                        WHERE aid='" . mxAddSlashesForSQL($aid) . "'
                          AND (user_uid Is Null Or user_uid=0);";
        }
    }
    // ende Autologinfeld in Admintabelle anpassen
}

if (isset($sqlqry)) {
    setupDoAllQueries($sqlqry);
    unset($sqlqry);
}

?>