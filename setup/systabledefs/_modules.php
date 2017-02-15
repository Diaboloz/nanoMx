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
// Tabellenstruktur fuer Tabelle `mx_modules`
// zum Testen:
// sql_query("DROP TABLE ${prefix}_modules");
// unset($tables["${prefix}_modules"]);
$created = false;
if (!isset($tables["${prefix}_modules"])) {
    $created = true;
    $sqlqry[] = "
CREATE TABLE `${prefix}_modules` (
  `mid` int(10) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `custom_title` varchar(255) NOT NULL default '',
  `active` int(1) NOT NULL default '0',
  `view` int(1) NOT NULL default '0',
  `main_id` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`mid`),
  UNIQUE KEY `title` (`title`),
  KEY `active` (`active`),
  KEY `view` (`view`),
  KEY `main_id` (`main_id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8  COLLATE utf8_unicode_ci;
";
} else {
    // Felder aktualisieren
    $tf = setupGetTableFields("${prefix}_modules");
    if (!isset($tf['custom_title'])) $sqlqry[] = "ALTER TABLE `${prefix}_modules` ADD custom_title VARCHAR(60) NOT NULL DEFAULT '' AFTER title;";
    if (isset($tf['inmenu'])) $sqlqry[] = "ALTER TABLE `${prefix}_modules` DROP `inmenu`;";
    if (!isset($tf['main_id'])) {
        $sqlqry[] = "ALTER TABLE `${prefix}_modules` ADD `main_id` VARCHAR( 20 ) NOT NULL ;";
        // } else {
        // if ($tf['main_id']['Type'] != 'varchar(30)') $sqlqry[] = "ALTER TABLE `${prefix}_modules` CHANGE `main_id` `main_id` VARCHAR( 30 ) NOT NULL DEFAULT '';";
    }
    // aenderung von nuke >= 7.0 rueckgaengig
    if (isset($tf['mod_group'])) $sqlqry[] = "ALTER TABLE `${prefix}_modules` DROP `mod_group`;";
    // aenderung von nuke >= 7.5 rueckgaengig
    if (isset($tf['admins'])) $sqlqry[] = "ALTER TABLE `${prefix}_modules` DROP `admins`;";
    // aenderung von cp rueckgaengig
    if (isset($tf['custom_title_german'])) $sqlqry[] = "ALTER TABLE `${prefix}_modules` DROP `custom_title_german` "; // cp
    if (isset($tf['custom_title_english'])) $sqlqry[] = "ALTER TABLE `${prefix}_modules` DROP `custom_title_english` "; // cp
}

if (isset($sqlqry)) {
    setupDoAllQueries($sqlqry);
    unset($sqlqry);
}
// ueberpruefen ob Module in der Datenbank auch wirklich als Datei existieren
// ggf. loeschen, Array aller Module in Db erstellen
$result = sql_query("SELECT `mid`, `title`, `main_id`, `active` FROM `${prefix}_modules`");
if ($result) {
    // Datei mit Moduldefinitionen includen
    include_once(FILE_ADD_MODULES);
    while (list($m_id, $modname, $main_id, $active) = sql_fetch_row($result)) {
        // wenn Modul nicht existiert > deaktivieren
        if ($active && !@is_file(PMX_MODULES_DIR . "/$modname/index.php")) {
            $sqlqry[] = "UPDATE `${prefix}_modules` SET active=0 WHERE mid=$m_id";
            continue;
        }
        // alte Menueblock Zuordnung von VKP-Maxi anpassen
        else if ($main_id == 'modules_one' || $main_id == '0') {
            $sqlqry[] = "UPDATE `${prefix}_modules` SET main_id = 'Modules_one' WHERE main_id = 'modules_one' OR main_id = '0';";
        } else if ($main_id == 'modules_two') {
            $sqlqry[] = "UPDATE `${prefix}_modules` SET main_id = 'Modules_one' WHERE main_id = 'Modules_two';";
        } else if ($main_id == 'modules' || $main_id == '0') {
            $sqlqry[] = "UPDATE `${prefix}_modules` SET main_id = '' WHERE main_id = 'Modules_two';";
        } else if ($main_id == '' && isset($modarry[$modname]) && (isset($tf['inmenu']) || !isset($tf['main_id']))) {
            $sqlqry[] = "UPDATE ${prefix}_modules SET `main_id`='" . $modarry[$modname] . "' WHERE `mid`='" . $m_id . "'";
        }
        // Modul-Array erweitern
        $dbmodlist[$modname] = $modname;
    }
}

/* Datei mit Moduldefinitionen includen */
include_once(FILE_ADD_MODULES);
// den Modulordner durchlaufen
foreach ((array)glob(PMX_MODULES_DIR . '/*/index.php', GLOB_NOSORT) as $modname) {
    $modname = basename(dirname($modname));
    if ($modname && strpos($modname, '.') === false && !isset($dbmodlist[$modname])) {
        // Wenn Modulname als Konstante definiert, diese als Modultitel verwenden,
        // ansonsten den Modulnamen ohne Unterstriche
        $ctitle = (defined($modname)) ? constant($modname) : str_replace("_", " ", $modname);
        // Modul in DB einfuegen
        if (isset($modarry[$modname])) {
            // neue pragmaMx Module extra anfuegen
            $sqlqry[] = "INSERT INTO `${prefix}_modules` (`title`, `custom_title`, `active`, `view`, `main_id`) VALUES ('" . $modname . "', '" . $ctitle . "', '" . $modarry[$modname][0] . "', '" . $modarry[$modname][1] . "', '" . $modarry[$modname][2] . "');";
        } else {
            $sqlqry[] = "INSERT INTO `${prefix}_modules` (`title`, `custom_title`, `active`, `view`, `main_id`) VALUES ('" . $modname . "', '" . $ctitle . "', 0, 2,'')";
        }
    }
}

if (isset($sqlqry)) {
    setupDoAllQueries($sqlqry);
    unset($sqlqry);
}
// Indexe aktualisieren
$indexes = setupGetTableIndexes("${prefix}_modules");
if (isset($indexes['PRIMARY']) && $indexes['PRIMARY']['Column_name'] != 'mid') {
    $sqlqry[] = "ALTER TABLE `${prefix}_modules` DROP PRIMARY KEY , ADD PRIMARY KEY ( `mid` ) ";
} else if (!isset($indexes['PRIMARY'])) {
    $sqlqry[] = "ALTER TABLE `${prefix}_modules` ADD PRIMARY KEY ( `mid` ) ";
}
if (!isset($indexes['title'])) {
    $sqlqry[] = "ALTER TABLE `${prefix}_modules` ADD UNIQUE `title` (`title`);";
} else if (isset($indexes['title']) && $indexes['title']['Non_unique'] == 1) {
    $sqlqry[] = "ALTER TABLE `${prefix}_modules` DROP INDEX `title` , ADD UNIQUE `title` ( `title` );";
}
if (!isset($indexes['main_id'])) $sqlqry[] = "ALTER TABLE `${prefix}_modules` ADD INDEX `main_id` ( `main_id` );";
if (!isset($indexes['active'])) $sqlqry[] = "ALTER TABLE `${prefix}_modules` ADD INDEX `active` ( `active` );";
if (!isset($indexes['view'])) $sqlqry[] = "ALTER TABLE `${prefix}_modules` ADD INDEX `view` ( `view` );";
if (isset($indexes['mid'])) $sqlqry[] = "ALTER TABLE `${prefix}_modules` DROP INDEX `mid` ";

if (isset($sqlqry)) {
    setupDoAllQueries($sqlqry);
    unset($sqlqry);
}

if (isset($sqlqry)) {
    setupDoAllQueries($sqlqry);
    unset($sqlqry);
}

unset($indexes, $modarry, $m_id, $modname);

?>