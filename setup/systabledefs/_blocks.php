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
// Tabellenstruktur fuer Tabelle `mx_blocks`
if (!isset($tables["${prefix}_blocks"])) {
    $sqlqry[] = "
CREATE TABLE `${prefix}_blocks` (
  `bid` int(10) NOT NULL auto_increment,
  `bkey` varchar(15) NOT NULL default '',
  `title` varchar(60) NOT NULL default '',
  `content` text,
  `url` varchar(200) NOT NULL default '',
  `position` char(1) NOT NULL default 'l',
  `weight` int(10) NOT NULL default '1',
  `active` int(1) NOT NULL default '1',
  `refresh` int(10) NOT NULL default '0',
  `time` int(10) NOT NULL default '0',
  `blanguage` varchar(30) NOT NULL default '',
  `blockfile` varchar(255) NOT NULL default '',
  `module` varchar(255) NULL,
  `view` int(1) NOT NULL default '0',
  `config` text,
  PRIMARY KEY  (`bid`),
  KEY `blanguage` (`blanguage`),
  KEY `active` (`active`),
  KEY `view` (`view`),
  KEY `posweight` (`position`,`weight`)
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8  COLLATE utf8_unicode_ci;
    ";
} else {
    $tf = setupGetTableFields("${prefix}_blocks");
    // alte Block-Cache Felder entfernen, falls vorhanden
    foreach ($tf as $fieldname => $temp) {
        if (preg_match('#^cache_([[:alnum:]]|_)*#i', $fieldname)) {
            $sqlqry[] = "ALTER TABLE `${prefix}_blocks` DROP `$fieldname`";
            unset($tf[$fieldname]);
        }
    }
    // zusätzliche Felder
    if (!isset($tf['view'])) $sqlqry[] = "ALTER TABLE `${prefix}_blocks` ADD `view` int(1) NOT NULL default '0' AFTER blockfile";
    // ////  ab mX 2.2:
    // if (!isset($tf['morepara'])) $sqlqry[] = "ALTER TABLE `${prefix}_blocks` ADD `morepara` text";
    // if (!isset($tf['start_date'])) $sqlqry[] = "ALTER TABLE `${prefix}_blocks` ADD `start_date` INT( 11 ) NOT NULL DEFAULT '0'";
    // if (!isset($tf['end_date'])) $sqlqry[] = "ALTER TABLE `${prefix}_blocks` ADD `end_date` INT( 11 ) NOT NULL DEFAULT '0'";
    // Felder aus bestimmten nuke Versionen umbenennen und aendern
    if (isset($tf['bposition']) && !isset($tf['position'])) $sqlqry[] = "ALTER TABLE `${prefix}_blocks` CHANGE `bposition` `position` CHAR( 1 ) NOT NULL DEFAULT '" . SYS_BLOCKPOS_LEFT . "'";
    if (isset($tf['position']) && ($tf['position']['Type'] != 'char(1)' || $tf['position']['Default'] != 'l')) $sqlqry[] = "ALTER TABLE `${prefix}_blocks` CHANGE `position` `position` CHAR( 1 ) NOT NULL DEFAULT 'l'";
    if (isset($tf['time']) && $tf['time']['Type'] != 'int(10)') {
        $sqlqry[] = "UPDATE `${prefix}_blocks` SET `time` = 0";
        $sqlqry[] = "ALTER TABLE `${prefix}_blocks` CHANGE `time` `time` int(10) NOT NULL default 0";
    }
    // nuke >= 7.0
    if (isset($tf['expire'])) $sqlqry[] = "ALTER TABLE `${prefix}_blocks` DROP `expire` ";
    if (isset($tf['action'])) $sqlqry[] = "ALTER TABLE `${prefix}_blocks` DROP `action` ";
    // nuke >= 7.1
    if (isset($tf['subscription'])) $sqlqry[] = "ALTER TABLE `${prefix}_blocks` DROP `subscription` ";
    if (isset($tf['custom_title_german'])) $sqlqry[] = "ALTER TABLE `${prefix}_blocks` DROP `custom_title_german` "; // cp
    if (isset($tf['custom_title_english'])) $sqlqry[] = "ALTER TABLE `${prefix}_blocks` DROP `custom_title_english` "; // cp
    // Standardwert fuer content fixen
    if (isset($tf['content']) && $tf['content']['Null'] != 'YES') $sqlqry[] = "ALTER TABLE `${prefix}_blocks` CHANGE `content` `content` TEXT NULL ";
}

$updatemoduleblocks = false;
if (isset($tables["${prefix}_blocks"])) {
    $tf = setupGetTableFields("${prefix}_blocks");
    if ($tf['content']['Default'] !== null) {
        $sqlqry[] = "ALTER TABLE `${prefix}_blocks` CHANGE `content` `content` text NULL";
    }
    // ab 1.13
    if (!isset($tf['module'])) {
        $sqlqry[] = "ALTER TABLE `${prefix}_blocks` ADD `module` VARCHAR( 255 ) NULL AFTER `blockfile`";
        $updatemoduleblocks = true;
    } else {
        if ($tf['module']['Type'] != 'varchar(255)') $sqlqry[] = "ALTER TABLE `${prefix}_blocks` CHANGE `module` `module` VARCHAR( 255 ) NULL DEFAULT NULL";
    }
	// ab 2.2.
    if (!isset($tf['config'])) {
        $sqlqry[] = "ALTER TABLE `${prefix}_blocks` ADD `config` TEXT";
        $updatemoduleblocks = true;
    }	
}

if (isset($sqlqry)) {
    setupDoAllQueries($sqlqry);
    unset($sqlqry);
}
// ##########################################################################
// TODO:  setupInsertBlock()
// array der News-Blöcke
$newsblocks = setup_NewsBlocks();
$isnewsblock = array();
// Daten fuer Blocktabelle
if (!isset($tables["${prefix}_blocks"])) {
    // Daten fuer Tabelle `mx_blocks`
    // ACHTUNG!!! ohne `bid`
    $sqlqry[] = "INSERT INTO `${prefix}_blocks` (`title`, `content`, `url`, `position`, `weight`, `active`, `refresh`, `time`, `blanguage`, `blockfile`, `module`, `view`) VALUES ('" . _BLOCK_CAPTION_1 . "', '', '', '" . SYS_BLOCKPOS_CENTER . "', 1, 1, 0, 0, '', 'block-AdminAlert.php', NULL, 2)";
  //  $sqlqry[] = "INSERT INTO `${prefix}_blocks` (`title`, `content`, `url`, `position`, `weight`, `active`, `refresh`, `time`, `blanguage`, `blockfile`, `module`, `view`) VALUES ('" . _BLOCK_CAPTION_2 . "', '', '', '" . SYS_BLOCKPOS_LEFT . "', 2, 1, 0, 0, '', 'block-AdminNews.php', NULL, 2)";
  //  $sqlqry[] = "INSERT INTO `${prefix}_blocks` (`title`, `content`, `url`, `position`, `weight`, `active`, `refresh`, `time`, `blanguage`, `blockfile`, `module`, `view`) VALUES ('" . _BLOCK_CAPTION_3 . "', '', '', '" . SYS_BLOCKPOS_RIGHT . "', 11, 1, 3600, 0, '', 'block-Languages.php', NULL, 0)";
  //  $sqlqry[] = "INSERT INTO `${prefix}_blocks` (`title`, `content`, `url`, `position`, `weight`, `active`, `refresh`, `time`, `blanguage`, `blockfile`, `module`, `view`) VALUES ('" . _BLOCK_CAPTION_4 . "', '', '', '" . SYS_BLOCKPOS_RIGHT . "', 3, 1, 0, 0, '', 'block-Login.php', 'Your_Account', 3)";
  //  $sqlqry[] = "INSERT INTO `${prefix}_blocks` (`title`, `content`, `url`, `position`, `weight`, `active`, `refresh`, `time`, `blanguage`, `blockfile`, `module`, `view`) VALUES ('" . _BLOCK_CAPTION_5 . "', '" . _BLOCK_CAPTION_5A . "', '', '" . SYS_BLOCKPOS_RIGHT . "', 5, 1, 0, 0, '', 'block-Userblock.php', 'Your_Account', 1)";
  //  $sqlqry[] = "INSERT INTO `${prefix}_blocks` (`title`, `content`, `url`, `position`, `weight`, `active`, `refresh`, `time`, `blanguage`, `blockfile`, `module`, `view`) VALUES ('" . _BLOCK_CAPTION_6 . "', '', '', '" . SYS_BLOCKPOS_RIGHT . "', 7, 1, 0, 0, '', 'block-Who_is_Online.php', 'Your_Account', 1)";
  //  $sqlqry[] = "INSERT INTO `${prefix}_blocks` (`title`, `content`, `url`, `position`, `weight`, `active`, `refresh`, `time`, `blanguage`, `blockfile`, `module`, `view`) VALUES ('" . _BLOCK_CAPTION_8 . "', '', '', '" . SYS_BLOCKPOS_RIGHT . "', 10, 1, 3600, 0, '', 'block-Survey.php', 'Surveys', 1)";
  //  $sqlqry[] = "INSERT INTO `${prefix}_blocks` (`title`, `content`, `url`, `position`, `weight`, `active`, `refresh`, `time`, `blanguage`, `blockfile`, `module`, `view`) VALUES ('" . _BLOCK_CAPTION_9 . "', '', 'http://www.pragmamx.org/backend.php', '" . SYS_BLOCKPOS_RIGHT . "', 12, 1, 36000, 1079910549, '', '', NULL, 0)";
}

if (isset($sqlqry)) {
    setupDoAllQueries($sqlqry);
    unset($sqlqry);
}

/* ab 2.0, bei Update die Blockpfade anpassen */
$result = sql_query("SELECT bid, module, blockfile FROM ${prefix}_blocks WHERE (ISNULL(`module`) OR `module`='') AND `blockfile`<>''");
if (sql_num_rows($result)) {
    /* Blöcke aus den Modulen auslesen */
    foreach ((array)glob(PMX_MODULES_DIR . '/*/blocks/block-*.php', GLOB_NOSORT) as $filename) {
        $blocks[basename($filename)] = basename(dirname(dirname($filename)));
    }
    /* Blöcke in DB aktualisieren */
    while (list($bid, $module, $blockfile) = sql_fetch_row($result)) {
        if (isset($blocks[$blockfile]) && $module != $blocks[$blockfile]) {
            $sqlqry[] = "UPDATE `${prefix}_blocks` SET `module` = '" . $blocks[$blockfile] . "' WHERE `bid` = " . intval($bid) . "";
        }
    }
}

if (isset($sqlqry)) {
    setupDoAllQueries($sqlqry);
    unset($sqlqry);
}

$result = sql_query("SELECT * FROM ${prefix}_blocks");
$blcount = sql_num_rows($result);
// wenn blöcke in Tabelle vorhanden
if ($blcount) {
    // alle Blöcke in Tabelle pruefen
    while ($block = sql_fetch_assoc($result)) {
        if ($block['blockfile']) {
            // nicht vorhandene System-Blöcke aus Tabelle löschen
            if (empty($block['module']) && !file_exists(PMX_BLOCKS_DIR . '/' . $block['blockfile'])) {
                $sqlqry[] = "DELETE FROM `${prefix}_blocks` WHERE `bid`=" . intval($block['bid']);
                continue;
            }
            // nicht vorhandene Modul-Blöcke aus Tabelle löschen
            if (!empty($block['module']) && !file_exists(PMX_MODULES_DIR . '/' . $block['module'] . '/blocks/' . $block['blockfile'])) {
                $sqlqry[] = "DELETE FROM `${prefix}_blocks` WHERE `bid`=" . intval($block['bid']);
                continue;
            }
            // alle Dateiblöcke in extra Array speichern
            $allblocks[$block['blockfile']] = $block;
            // alte Systemblöcke von VKP-MAxi in Dateiblöcke umwandeln
            // neue Blöcke fur pragmaMx umschreiben
            switch (true) {
                case $block['blockfile'] == 'block-AdminLogin.php' :
                case $block['bkey'] == 'admin' :
                    $sqlqry[] = "UPDATE `${prefix}_blocks` SET `blockfile` = 'block-AdminNews.php', view='2', bkey='' WHERE `bid`=" . intval($block['bid']);
                    break;

                case $block['bkey'] == 'userbox' :
                    $sqlqry[] = "UPDATE `${prefix}_blocks` SET `blockfile` = 'block-Userblock.php', module='Your_Account', view='1', bkey='' WHERE `bid`=" . intval($block['bid']);
                    break;

                case $block['blockfile'] == 'block-Advertising.php' :
                case $block['blockfile'] == 'block-Banner_Footer.php' :
                case $block['blockfile'] == 'block-Banner_Center.php' :
                    $sqlqry[] = "UPDATE `${prefix}_blocks` SET `blockfile` = 'block-Banner.php', module='Banners' WHERE `bid`=" . intval($block['bid']);
                    break;

                case $block['blockfile'] == 'block-User_Info.php' :
                    $sqlqry[] = "UPDATE `${prefix}_blocks` SET `blockfile` = 'block-Who_is_Online.php', module='Your_Account' WHERE `bid`=" . intval($block['bid']);
                    break;

                case $block['blockfile'] == 'block-Avatar_Random.php' :
                case $block['blockfile'] == 'block-Avatar_Last.php' :
                    $sqlqry[] = "UPDATE `${prefix}_blocks` SET `blockfile` = 'block-userimage_random.php', module='Your_Account' WHERE `bid`=" . intval($block['bid']);
                    break;

                case $block['blockfile'] == 'block-mxTabs_center.php' :
                    $sqlqry[] = "UPDATE `${prefix}_blocks` SET `blockfile` = 'block-Multiblock_Tabs.php' WHERE `bid`=" . intval($block['bid']);
                    break;

                case $block['blockfile'] == 'block-AdminAlert.php' && $block['active'] != 1 && $block['view'] != 2 :
                    // Setup-Alarm-Block aktivieren
                    $sqlqry[] = "UPDATE `${prefix}_blocks` SET `active` = 1, view=2 WHERE `bid`=" . intval($block['bid']);
                    break;

                case isset($newsblocks[$block['blockfile']]):
                    // news-Blöcke pruefen
                    $isnewsblock[$block['blockfile']] = 1;
                    // news-Blöcke an richtige Position setzen
                    if ($block['position'] != SYS_BLOCKPOS_RIGHT) {
                        $sqlqry[] = "UPDATE ${prefix}_blocks set position='" . SYS_BLOCKPOS_RIGHT . "', module='News' WHERE `bid`=" . intval($block['bid']);
                    }
                    break;
            }
        }
    }
}
// sonstige Pflichtblöcke bei Neuinstall oder Update von Nuke einfuegen
/*if (!isset($tables["${prefix}_blocks"])) {
   if (!isset($allblocks['block-Modules_one.php'])) $sqlqry[] = "INSERT INTO `${prefix}_blocks` (`title`, `content`, `url`, `position`, `weight`, `active`, `refresh`, `time`, `blanguage`, `blockfile`, `view`) VALUES ('" . _BLOCK_CAPTION_MAINMENU . "', '', '', '" . SYS_BLOCKPOS_LEFT . "', 3, 1, 3600, 0, '', 'block-Modules_one.php', 0)";
    if (!isset($allblocks['block-Modules_two.php'])) $sqlqry[] = "INSERT INTO `${prefix}_blocks` (`title`, `content`, `url`, `position`, `weight`, `active`, `refresh`, `time`, `blanguage`, `blockfile`, `view`) VALUES ('" . _BLOCK_CAPTION_INTERNAL . "', '', '', '" . SYS_BLOCKPOS_LEFT . "', 4, 1, 3600, 0, '', 'block-Modules_two.php', 0)";
    if (!isset($allblocks['block-Modules_three.php'])) $sqlqry[] = "INSERT INTO `${prefix}_blocks` (`title`, `content`, `url`, `position`, `weight`, `active`, `refresh`, `time`, `blanguage`, `blockfile`, `view`) VALUES ('" . _BLOCK_CAPTION_COMMUNITY . "', '', '', '" . SYS_BLOCKPOS_LEFT . "', 5, 1, 0, 0, '', 'block-Modules_three.php', 0)";
    if (!isset($allblocks['block-Modules.php'])) $sqlqry[] = "INSERT INTO `${prefix}_blocks` (`title`, `content`, `url`, `position`, `weight`, `active`, `refresh`, `time`, `blanguage`, `blockfile`, `view`) VALUES ('" . _BLOCK_CAPTION_OTHER . "', '', '', '" . SYS_BLOCKPOS_LEFT . "', 6, 1, 0, 0, '', 'block-Modules.php', 0)";
}
*/
if (!isset($allblocks['block-AdminAlert.php'])) {
    $sqlqry[] = "INSERT INTO `${prefix}_blocks` (`title`, `position`, `weight`, `active`, `blockfile`, `view`) VALUES ('setup-alarm', '" . SYS_BLOCKPOS_CENTER . "', 1, 1, 'block-AdminAlert.php', 2)";
} else if (empty($allblocks['block-AdminAlert.php']['active'])) {
    $sqlqry[] = "UPDATE `${prefix}_blocks` SET `active` = 1, view=2 WHERE `blockfile` = 'block-AdminAlert.php'";
}
// ENDE Blöcke einfuegen / ändern
// wenn nicht alle news-Blöcke in Tabelle vorhanden sind
if (count($newsblocks) != count($isnewsblock)) {
    // News-Blöcke anfuegen
    $qry = "SELECT Max(weight) AS bmax FROM ${prefix}_blocks WHERE position='" . SYS_BLOCKPOS_RIGHT . "'";
    list($bmax) = sql_fetch_row(sql_query($qry));
    $bmax = intval($bmax);
    foreach($newsblocks as $blockfile => $blocktitle) {
        if (!isset($isnewsblock[$blockfile])) {
            $bmax++;
            $sqlqry[] = "INSERT INTO `${prefix}_blocks` (title, position, weight, active, refresh, blockfile, module, view) VALUES ('" . $blocktitle . "'  , '" . SYS_BLOCKPOS_RIGHT . "', " . $bmax . ", 1, 0, '" . $blockfile . "', 'News', 0)";
        }
    }
}
// Indexe aktualisieren
$indexes = setupGetTableIndexes("${prefix}_blocks");
if (!isset($indexes['blanguage'])) $sqlqry[] = "ALTER TABLE `${prefix}_blocks` ADD INDEX `blanguage` ( `blanguage` )";
if (!isset($indexes['active'])) $sqlqry[] = "ALTER TABLE `${prefix}_blocks` ADD INDEX `active` ( `active` )";
if (!isset($indexes['view'])) $sqlqry[] = "ALTER TABLE `${prefix}_blocks` ADD INDEX `view` ( `view` )";
if (!isset($indexes['posweight'])) $sqlqry[] = "ALTER TABLE `${prefix}_blocks` ADD INDEX `posweight` ( `position` , `weight` )";
if (isset($indexes['bid'])) $sqlqry[] = "ALTER TABLE `${prefix}_blocks` DROP INDEX `bid` ";
if (isset($indexes['title'])) $sqlqry[] = "ALTER TABLE `${prefix}_blocks` DROP INDEX `title` ";

if (isset($sqlqry)) {
    setupDoAllQueries($sqlqry);
    unset($sqlqry);
}

unset($isnewsblock, $newsblocks, $blocks, $allblocks, $updatemoduleblocks);

?>