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
// sicherstellen, dass die Block- und Modul-Tabelle bereits aktualisiert sind
include_once(__DIR__ . '/_blocks.php');
include_once(__DIR__ . '/_modules.php');

unset($sqlqry);
// Tabellenstruktur fuer Tabelle `mx_menu`
if (!isset($tables["${prefix}_menu"])) {
    $sqlqry[] = "
CREATE TABLE `${prefix}_menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bid` int(10) NOT NULL,
  `pid` int(10) NOT NULL,
  `title` varchar(128) NOT NULL,
  `description` varchar(200) NOT NULL,
  `url` varchar(255) NOT NULL,
  `weight` int(10) NOT NULL DEFAULT '0',
  `expanded` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `target` varchar(10) DEFAULT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8  COLLATE utf8_unicode_ci;
";
} else {
    /* Felder auslesen */
    $tf = setupGetTableFields("${prefix}_menu");

    /* Felder ergaenzen */
    if (!isset($tf['target'])) $sqlqry[] = "ALTER TABLE `${prefix}_menu` ADD `target` VARCHAR( 10 ) NULL AFTER `weight`";
    if (!isset($tf['expanded'])) $sqlqry[] = "ALTER TABLE `${prefix}_menu` ADD `expanded` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0' AFTER `weight`";

    /* alte Daten konvertieren */
    if (isset($tf['type'])) {
        $sqlqry[] = "UPDATE `${prefix}_menu` SET `expanded`=1 WHERE `type` LIKE '%|1'";
        $sqlqry[] = "ALTER TABLE `${prefix}_menu` DROP `type`";
    }

    $deactives = array();
    if (isset($tf['active'])) {
        $menresult = sql_query("SELECT id FROM `${prefix}_menu` WHERE `active`=0 OR NOT `active`");
        while ($row = sql_fetch_assoc($menresult)) {
            $deactives[] = $row['id'];
        }
        $sqlqry[] = "ALTER TABLE `${prefix}_menu` DROP `active`";
    }
}

if (isset($sqlqry)) {
    setupDoAllQueries($sqlqry);
    unset($sqlqry);
}

$menresult = sql_query("SELECT bid FROM `${prefix}_blocks` WHERE `blockfile`='block-Menu.php' LIMIT 1");
list($ismenu) = sql_fetch_row($menresult);

if (!$ismenu) {
    $menresult = sql_query("SELECT MAX(bid) FROM `${prefix}_blocks`");
    list($maxblock) = sql_fetch_row($menresult);
    $maxblock++;

    $sqlqry[] = "INSERT IGNORE INTO `${prefix}_blocks` (`bid`, `bkey`, `title`, `content`, `url`, `position`, `weight`, `active`, `refresh`, `time`, `blanguage`, `blockfile`, `view`) VALUES ($maxblock , '', 'myCommunity', '', '', 'r', 3, 1, 0, 0, '', 'block-Menu.php', 1)";

    $i = 0;
    $sqlqry[] = "INSERT IGNORE INTO `${prefix}_menu` (`bid`, `pid`, `title`, `description`, `url`, `weight`) VALUES
        ($maxblock , 3, 'Daten', 'Informationen zum Benutzeraccount', 'modules.php?name=Your_Account&op=edituser', " . $i++ . "),
        ($maxblock , 0, 'Gästebuch', 'Hier können Sie uns Ihre Nachrichten und Kommentare hinterlassen.', 'modules.php?name=Guestbook', " . $i++ . "),
        ($maxblock , 0, 'Mein Account', 'Ihr persönliches Kontrollzentrum', 'modules.php?name=Your_Account', " . $i++ . "),
        ($maxblock , 3, 'Nachrichten', 'Ihre privaten Nachrichten', 'modules.php?name=Private_Messages', " . $i++ . "),
        ($maxblock , 1, 'Benutzerliste', 'Liste aller angemeldeten Benutzer', 'modules.php?name=Members_List', " . $i++ . "),
        ($maxblock , 0, 'Benutzer Anmeldung', 'Benutzer Anmeldung', 'modules.php?name=User_Registration', " . $i++ . "),
        ($maxblock , 1, 'Ungel. Beiträge', 'Ungelesene Beiträge seit Ihrem letzten Besuch', 'modules.php?name=Forum&action=unread', " . $i++ . "),
        ($maxblock , 0, 'Forum', 'Forum', 'modules.php?name=Forum', " . $i++ . "),
        ($maxblock , 1, 'Ungel. Antworten', 'Ungelesene Antworten zu Ihren Beiträgen', 'modules.php?name=Forum&action=unreadreplies', " . $i++ . "),
        ($maxblock , 2, 'Neuer Eintrag', 'Neuen Eintrag im Gästebuch hinzufügen', 'modules.php?name=Guestbook&op=AddEntry', " . $i++ . "),
        ($maxblock , 3, 'Einstellungen', 'Seitendarstellung konfigurieren', 'modules.php?name=Your_Account&op=edithome', " . $i++ . "),
        ($maxblock , 3, 'Ausloggen', 'Ausloggen', 'modules.php?name=Your_Account&op=logout', " . $i++ . ")
    ";
} else {
    /* alte Daten konvertieren */
    // deaktivierte verschieben
    if ($deactives) {
        $blockname = '{deactivated menupoints}';
        $menresult = sql_query("SELECT bid FROM `${prefix}_blocks` WHERE `blockfile`='block-Menu.php' AND `title`='$blockname'");
        list($ismenu) = sql_fetch_row($menresult);

        if (!$ismenu) {
            $menresult = sql_query("SELECT MAX(bid) FROM `${prefix}_blocks`");
            list($maxblock) = sql_fetch_row($menresult);
            $maxblock++;

            $sqlqry[] = "INSERT IGNORE INTO `${prefix}_blocks` (`bid`, `title`, `position`, `weight`, `active`, `blockfile`, `view`) VALUES ($maxblock , '$blockname', 'r', 30, 0, 'block-Menu.php', 1)";
            $ismenu = $maxblock;
        }

        $items = implode(',', $deactives);
        $sqlqry[] = "UPDATE `${prefix}_menu` SET `bid`=$ismenu, `pid`=0 WHERE `id` IN($items) AND `bid`<>$ismenu";
    }
}

if (isset($sqlqry)) {
    setupDoAllQueries($sqlqry);
    unset($sqlqry);
}

unset($ismenu, $maxblock, $deactives, $blockname, $items, $menresult);

/*
// versuchen, ein Menue aus der Modultabelle zu erstellen
#`mid`, `title`, `main_id`, `active`
$result = sql_query("SELECT * FROM `${prefix}_modules`");
if ($result) {
    // Datei mit Moduldefinitionen includen
    include_once(FILE_ADD_MODULES);
    while ($mod = sql_fetch_assoc($result)) {
        mxDebugFuncVars($mod);
    }
}

if (!isset($tables["${prefix}_menu"])) {
unset($tables["${prefix}_menu"]);
    // check ob menuetabelle wirklich leer
    $numrows = sql_num_rows(sql_query("SELECT `id` FROM `${prefix}_menu` LIMIT 1;"));
    // wenn leer...
    if ($numrows) {
        // sicherstellen, dass die Modultabelle bereits aktualisiert ist
        include_once(__DIR__ . '/_modules.php');
        $menresult = sql_query("SELECT * FROM ${prefix}_modules WHERE ((active=1) AND (view=1)) ORDER BY mid");
        while ($row = sql_fetch_assoc($menresult)) {
            mxDebugFuncVars($row);
        }
    }
}
// exit;

 */

?>