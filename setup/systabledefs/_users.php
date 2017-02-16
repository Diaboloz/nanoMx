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
 * $Revision: 81 $
 * $Author: PragmaMx $
 * $Date: 2015-08-19 08:59:13 +0200 (Mi, 19. Aug 2015) $
 */

defined('mxMainFileLoaded') or die('access denied');
unset($sqlqry);

$deflang = (isset($_REQUEST['slang'])) ? $_REQUEST['slang'] : SETUP_DEFAULTLANG;
// --------------------------------------------------------
// Tabellenstruktur fuer Tabelle `mx_users`
// falls noch alte Version mit prefix, Tabelle umbenennen
if (isset($tables["${prefix}_users"]) && !isset($tables["{$user_prefix}_users"]) && $prefix != $user_prefix) {
    $sql = "RENAME TABLE `${prefix}_users` TO `{$user_prefix}_users`;";
    setupDoAllQueries($sql);
    unset($sql);
    if (setupTableExist("{$user_prefix}_users", 'refresh')) {
        unset($tables["${prefix}_users"]);
    }
}

$changeuserstat = false;
if (!isset($tables["{$user_prefix}_users"])) {
    $sqlqry[] = "
CREATE TABLE `{$user_prefix}_users` (
  `uid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(60) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `uname` varchar(25) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `email` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `femail` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `user_avatar` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_regdate` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `user_icq` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_occ` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_from` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_intrest` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_sig` text COLLATE utf8_unicode_ci,
  `user_viewemail` tinyint(1) DEFAULT NULL,
  `user_theme` int(3) DEFAULT NULL,
  `user_aim` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_yim` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_msnm` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pass` varchar(140) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `pass_salt` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `storynum` tinyint(4) NOT NULL DEFAULT '10',
  `umode` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `uorder` tinyint(1) NOT NULL DEFAULT '0',
  `thold` tinyint(1) NOT NULL DEFAULT '0',
  `noscore` tinyint(1) NOT NULL DEFAULT '0',
  `bio` tinytext COLLATE utf8_unicode_ci,
  `ublockon` tinyint(1) NOT NULL DEFAULT '0',
  `ublock` text COLLATE utf8_unicode_ci,
  `theme` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `commentmax` int(11) NOT NULL DEFAULT '4096',
  `counter` int(11) NOT NULL DEFAULT '0',
  `user_posts` int(10) NOT NULL DEFAULT '0',
  `user_attachsig` int(2) NOT NULL DEFAULT '0',
  `user_rank` int(10) NOT NULL DEFAULT '0',
  `user_level` int(10) NOT NULL DEFAULT '1',
  `user_ingroup` int(10) NOT NULL DEFAULT '1',
  `user_lastvisit` int(11) NOT NULL DEFAULT '0',
  `user_regtime` int(11) unsigned NOT NULL DEFAULT '0',
  `user_lastip` varchar(60) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `user_lastmod` varchar(40) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `user_lasturl` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `user_pm_poptime` smallint(4) unsigned NOT NULL DEFAULT '0',
  `user_stat` tinyint(1) NOT NULL DEFAULT '0',
  `user_lang` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `user_bday` date DEFAULT NULL,
  `user_sexus` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `user_guestbook` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `user_pm_mail` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `uname` (`uname`),
  KEY `user_ingroup` (`user_ingroup`),
  KEY `user_stat` (`user_stat`),
  KEY `user_lastvisit` (`user_lastvisit`),
  KEY `user_lastmod` (`user_lastmod`),
  KEY `user_bday` (`user_bday`),
  KEY `email` (`email`),
  KEY `user_sexus` (`user_sexus`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
";
} else {
    // Felder aktualisieren
    $tf = setupGetTableFields("{$user_prefix}_users");
    // evtl. fehlende Felder hinzufuegen
    // if (!isset($tf['newsletter'])) $sqlqry[] = "ALTER TABLE `{$user_prefix}_users` ADD newsletter INT (1) NOT NULL DEFAULT '0' ;";
    if (!isset($tf['user_ingroup'])) $sqlqry[] = "ALTER TABLE `{$user_prefix}_users` ADD `user_ingroup` INT( 10 ) DEFAULT '1' NOT NULL ;";
    if (!isset($tf['user_lastvisit'])) $sqlqry[] = "ALTER TABLE `{$user_prefix}_users` ADD `user_lastvisit` INT( 11 ) NOT NULL DEFAULT '0' ;";
    if (!isset($tf['user_regtime'])) $sqlqry[] = "ALTER TABLE `{$user_prefix}_users` ADD `user_regtime` INT( 11 ) UNSIGNED NOT NULL DEFAULT '0' ;";
    if (!isset($tf['user_lastip'])) $sqlqry[] = "ALTER TABLE `{$user_prefix}_users` ADD `user_lastip` VARCHAR( 60 ) NOT NULL default ''";
    if (!isset($tf['user_lastmod'])) $sqlqry[] = "ALTER TABLE `{$user_prefix}_users` ADD `user_lastmod` VARCHAR( 40 ) NOT NULL default '' AFTER `user_lastip` ;";
    if (!isset($tf['user_lasturl'])) $sqlqry[] = "ALTER TABLE `{$user_prefix}_users` ADD `user_lasturl` VARCHAR( 255 ) NOT NULL default '' AFTER `user_lastmod` ;";
    if (!isset($tf['user_pm_poptime'])) $sqlqry[] = "ALTER TABLE `{$user_prefix}_users` ADD `user_pm_poptime` SMALLINT( 4 ) UNSIGNED NOT NULL DEFAULT '0' AFTER `user_lasturl` ;";
    if (!isset($tf['user_stat'])) {
        $sqlqry[] = "ALTER TABLE `{$user_prefix}_users` ADD `user_stat` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `user_pm_poptime` ;";
        $changeuserstat = true;
    }
    if (!isset($tf['user_bday'])) $sqlqry[] = "ALTER TABLE `{$user_prefix}_users` ADD `user_bday` DATE AFTER `user_stat`;";
    if (!isset($tf['user_sexus'])) $sqlqry[] = "ALTER TABLE `{$user_prefix}_users` ADD `user_sexus` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0' AFTER `user_bday`;";
    if (!isset($tf['user_guestbook'])) $sqlqry[] = "ALTER TABLE `{$user_prefix}_users` ADD `user_guestbook` tinyint( 1 ) unsigned NOT NULL default '1' AFTER `user_sexus`;";
    // ab 0.1.11, bereits in nuke6.5 enthalten
    if (!isset($tf['user_lang'])) $sqlqry[] = "ALTER TABLE `{$user_prefix}_users` ADD `user_lang` VARCHAR( 50 ) NOT NULL  DEFAULT '' AFTER `user_stat`;";
    // cportal..
    if (!isset($tf['femail'])) $sqlqry[] = "ALTER TABLE `{$user_prefix}_users` ADD `femail` VARCHAR( 100 ) NOT NULL default '' AFTER `email` ;";
    // nuke 6.5 Konverter
    // ab nuke 6.5, falsch benannte Felder umbenennen
    if (!isset($tf['uname']) && isset($tf['username'])) $sqlqry[] = "ALTER TABLE `{$user_prefix}_users` CHANGE `username` `uname` VARCHAR( 25 ) NOT NULL default ''";
    if (!isset($tf['uid']) && isset($tf['user_id'])) $sqlqry[] = "ALTER TABLE `{$user_prefix}_users` CHANGE `user_id` `uid` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT;";
    if (!isset($tf['pass']) && isset($tf['user_password'])) $sqlqry[] = "ALTER TABLE `{$user_prefix}_users` CHANGE `user_password` `pass` VARCHAR( 32 ) NOT NULL default ''";
    if (!isset($tf['email']) && isset($tf['user_email'])) $sqlqry[] = "ALTER TABLE `{$user_prefix}_users` CHANGE `user_email` `email` VARCHAR( 100 ) NOT NULL default ''";
    if (!isset($tf['url']) && isset($tf['user_website'])) $sqlqry[] = "ALTER TABLE `{$user_prefix}_users` CHANGE `user_website` `url` VARCHAR( 255 ) NOT NULL default ''";
    if (!isset($tf['user_intrest']) && isset($tf['user_interests'])) $sqlqry[] = "ALTER TABLE `{$user_prefix}_users` CHANGE `user_interests` `user_intrest` VARCHAR( 150 ) NULL;";
    // ENDE nuke 6.5 Konverter
    /* Felder anpassen */
    if (!isset($tf['user_pm_mail'])) $sqlqry[] = "ALTER TABLE `{$user_prefix}_users` ADD `user_pm_mail` tinyint( 1 ) unsigned NOT NULL default '1' AFTER `user_guestbook`;";
    // ab v2.0
    if (!isset($tf['pass_salt'])) $sqlqry[] = "ALTER TABLE `{$user_prefix}_users` ADD `pass_salt` VARCHAR( 32 ) NULL DEFAULT NULL AFTER `pass` ;";

    if (isset($sqlqry)) {
        setupDoAllQueries($sqlqry);
        unset($sqlqry);
    }
    // Felder neu einlesen
    $tf = setupGetTableFields("{$user_prefix}_users");

    switch (true) {
        case $tf['user_avatar']['Default'] !== null :
        case $tf['user_avatar']['Type'] != 'varchar(255)' :
        case $tf['theme']['Default'] === null :
        case $tf['theme']['Type'] != 'varchar(100)' :
        case $tf['user_sig']['Default'] !== null :
        case $tf['user_sig']['Type'] != 'text' :
        case $tf['bio']['Default'] !== null :
        case $tf['bio']['Type'] != 'tinytext' :
        case $tf['ublock']['Default'] !== null :
        case $tf['ublock']['Type'] != 'text' :
        case $tf['email']['Default'] === null :
        case $tf['email']['Type'] != 'varchar(100)' :
        case $tf['femail']['Default'] === null :
        case $tf['femail']['Type'] != 'varchar(100)' :
        case $tf['url']['Default'] === null :
        case $tf['url']['Type'] != 'varchar(255)' :
        case $tf['user_viewemail']['Type'] != 'tinyint(1)' :
        case $tf['user_viewemail']['Default'] !== null :
        case $tf['uid']['Type'] != 'int(11) unsigned' :
        case $tf['pass']['Type'] != 'varchar(140)' :
        case $tf['user_lastip']['Default'] === null :
        case $tf['user_lastmod']['Default'] === null :
        case $tf['user_lasturl']['Default'] === null :
        case $tf['user_icq']['Type'] != 'varchar(50)' :
        case $tf['user_icq']['Default'] !== null :
        case $tf['user_aim']['Type'] != 'varchar(100)' :
        case $tf['user_aim']['Default'] !== null :
        case $tf['user_yim']['Type'] != 'varchar(100)' :
        case $tf['user_yim']['Default'] !== null :
        case $tf['user_msnm']['Type'] != 'varchar(100)' :
        case $tf['user_msnm']['Default'] !== null :
        case $tf['user_lang']['Type'] != 'varchar(50)' :
        case $tf['user_pm_mail']['Type'] != 'tinyint(1) unsigned' :
            $sqlqry[] = "ALTER TABLE `{$user_prefix}_users`
                CHANGE `user_avatar` `user_avatar` VARCHAR( 255 ) NULL,
                CHANGE `theme` `theme` VARCHAR( 100 ) NOT NULL DEFAULT '',
                CHANGE `user_sig` `user_sig` text NULL,
                CHANGE `bio` `bio` tinytext NULL,
                CHANGE `ublock` `ublock` text NULL,
                CHANGE `email` `email` varchar(100) NOT NULL default '',
                CHANGE `femail` `femail` varchar(100) NOT NULL default '',
                CHANGE `url` `url` varchar(255) NOT NULL default '',
                CHANGE `user_viewemail` `user_viewemail` tinyint(1) default NULL,
                CHANGE `uid` `uid` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT,
                CHANGE `pass` `pass` varchar(140) NOT NULL default '',
                CHANGE `user_lastip` `user_lastip` varchar(60) NOT NULL default '',
                CHANGE `user_lastmod` `user_lastmod` varchar(40) NOT NULL default '',
                CHANGE `user_lasturl` `user_lasturl` varchar(255) NOT NULL default '',
                CHANGE `user_icq` `user_icq` varchar(50) default NULL,
                CHANGE `user_aim` `user_aim` varchar(100) default NULL,
                CHANGE `user_yim` `user_yim` varchar(100) default NULL,
                CHANGE `user_msnm` `user_msnm` varchar(100) default NULL,
                CHANGE `user_lang` `user_lang` VARCHAR( 50 ) DEFAULT '' NOT NULL,
                CHANGE `user_pm_mail` `user_pm_mail` tinyint(1) unsigned NOT NULL default '0'
                ";
    }
    // ende Usertabelle aendern
}

if (isset($sqlqry)) {
    setupDoAllQueries($sqlqry);
    unset($sqlqry);
}
// Datenanpassung fuer Usertabelle
// feststellen, ob bereits Datensaetze vorhanden
$usercount = sql_num_rows(sql_query("SELECT uid FROM `{$user_prefix}_users` LIMIT 1;"));
// wenn User vorhanden, versch. aenderungen an den Datensaetzen
if ($usercount) {
    // wenn die neuen Felder noch nicht vorhanden waren
    if (!isset($tf['user_stat']) || $changeuserstat) {
        // wenn neue Felder noch nicht vorhanden waren, Daten anpassen
        $sqlqry[] = "UPDATE `{$user_prefix}_users` SET user_stat = 1, user_pm_poptime = 0;"; #user_ingroup = 1, SET user_ingroup = 0,
        $sqlqry[] = "UPDATE `{$user_prefix}_users` SET user_stat = -1, user_pm_poptime = 0 WHERE uname='Anonymous' OR uname='Gast' OR uname='Guest' OR uname='Exmitglied' OR uid <= 1;";
    }
    // Registrierdatum aendern
    // Laendereinstellung, auf Englisch setzen
    $old_setlocale = setlocale(LC_TIME, 0);
    setlocale(LC_TIME, 'en_EN');

    unset($false_user_regdate);
    // user_regdate vorhanden, aber user_regtime leer
    $result = sql_query("SELECT DISTINCT user_regdate, user_regtime FROM `{$user_prefix}_users` WHERE user_regdate <> '' AND  user_regtime <= 0 AND user_stat >= 0 ORDER BY user_regdate");
    while ($row = sql_fetch_assoc($result)) {
        // user_regtime aus user_regdate ermitteln
        $new_regtime = strtotime($row['user_regdate']);
        // wenn nicht erkannt...
        if ($new_regtime <= 0) {
            // dieses Format versuchen '04. May. 2004', dazu einfach die Punkte entfernen
            $new_regtime = strtotime(str_replace('.', '', $row['user_regdate']));
        }
        // wenn noch nicht erkannt...
        if ($new_regtime <= 0) {
            // dieses Format versuchen '04 May, 2005', dazu einfach das Komma entfernen
            $new_regtime = strtotime(str_replace(',', '', $row['user_regdate']));
        }
        // wenn user_regtime jetzt ok, dann user_regtime in Tabelle aktualisieren
        // bei fehlerhaftem Datum kann auch -3600 rauskommen, deshalb Test auf > 0
        if ($new_regtime > 0) {
            $nuke_regdate = "";//mxGetNukeUserregdate($new_regtime);
            $sqlqry[] = "UPDATE `{$user_prefix}_users` SET user_regtime='" . $new_regtime . "', user_regdate='" . $nuke_regdate . "' WHERE user_regdate='" . $row['user_regdate'] . "' AND user_regtime <= 0 AND user_stat >= 0";
        } else {
            // wenn nicht, diese Kombination zur weiteren Behandlung zwischenspeichern
            $false_user_regdate[] = $row;
        }
    }

    if (isset($sqlqry)) {
        setupDoAllQueries($sqlqry);
        unset($sqlqry);
    }
    // falls user_regdate zwar vorhanden, aber ungueltig
    if (isset($false_user_regdate)) {
        $falsedate[] = 'Jan 01, 1970';
        foreach($false_user_regdate as $xx) {
            $falsedate[] = $xx['user_regdate'];
        }
        $falsedate = implode("', '", $falsedate);
        $result = sql_query("SELECT uid FROM `{$user_prefix}_users` WHERE (user_regdate='' OR user_regdate IN('" . $falsedate . "')) AND user_regtime <= 0 AND user_stat >= 0 ORDER BY uid");
        while ($row = sql_fetch_assoc($result)) {
            $result1 = sql_query("SELECT user_regtime FROM `{$user_prefix}_users` WHERE user_regtime > 0 AND uid < " . intval($row['uid']) . " ORDER BY uid DESC LIMIT 1");
            $row1 = sql_fetch_assoc($result1);
            $start = (empty($row1['user_regtime']) || intval($row1['user_regtime']) <= 0) ? time() : $row1['user_regtime'];

            $result2 = sql_query("SELECT user_regtime FROM `{$user_prefix}_users` WHERE user_regtime > 0 AND uid > " . intval($row['uid']) . " ORDER BY uid ASC LIMIT 1");
            $row2 = sql_fetch_assoc($result2);
            $end = (empty($row2['user_regtime']) || intval($row2['user_regtime']) <= 0) ? time() : $row2['user_regtime'];

            $new_regtime = intval(($start + $end) / 2);
            if ($new_regtime) {
                $nuke_regdate = "";//mxGetNukeUserregdate($new_regtime);
                $sqlqry[] = "UPDATE `{$user_prefix}_users` SET user_regtime='" . $new_regtime . "', user_regdate='" . $nuke_regdate . "' WHERE uid=" . intval($row['uid']) . " AND user_stat >= 0";
            }
        }
    }

    if (isset($sqlqry)) {
        setupDoAllQueries($sqlqry);
        unset($sqlqry);
    }
    // fehlerhaftes user_regdate korrigieren, wenn gueltiges user_regtime vorhanden
    $result = sql_query("SELECT DISTINCT user_regtime, user_regdate, DATE_FORMAT(FROM_UNIXTIME(user_regtime),'%b %d, %Y') AS user_regdate_new FROM `{$user_prefix}_users` WHERE user_regtime>0 AND user_regdate <> DATE_FORMAT(FROM_UNIXTIME(user_regtime),'%b %d, %Y') AND user_stat >= 0 ORDER BY uid");
    while ($row = sql_fetch_assoc($result)) {
        $nuke_regdate = $row['user_regdate_new'];
        $sqlqry[] = "UPDATE `{$user_prefix}_users` SET user_regdate='" . $nuke_regdate . "' WHERE user_regtime=" . intval($row['user_regtime']) . " AND user_regdate<>'" . $nuke_regdate . "' AND user_stat >= 0";
    }

    if (isset($sqlqry)) {
        setupDoAllQueries($sqlqry);
        unset($sqlqry);
    }
    // Laendereinstellung, mit den Werten aus dem globalen Sprachfile zuruecksetzen
    setlocale(LC_TIME, $old_setlocale);
    // ende Registrierdatum aendern
    // LastSeen-Datum und letzte IP aendern
    if (isset($tables["${prefix}_lastseen"])) {
        $result = sql_query("SELECT username, date, ip FROM ${prefix}_lastseen;");
        $i1 = 0;
        while (list($username, $date, $ip) = sql_fetch_row($result)) {
            $i1++;
            $sqlqry[] = "UPDATE `{$user_prefix}_users` SET user_lastvisit='$date', user_lastip='$ip' WHERE uname='$username' AND user_lastvisit=0 AND uname<>'Anonymous';";
        }
    } //// ende LastSeen-Datum und letzte IP aendern
    // Usertheme entfernen
    if (sql_num_rows(sql_query("SELECT uid FROM `{$user_prefix}_users` WHERE `theme` <> '' LIMIT 1;"))) {
        $sqlqry[] = "UPDATE `{$user_prefix}_users` SET `theme` = '' WHERE `theme` <> ''";
    }

    if (function_exists('load_class')) {
        $userconfig = load_class('Userconfig');
        // von nuke6.0  popmeson
        if (isset($tf['popmeson'])) {
            if (sql_num_rows(sql_query("SELECT uid FROM `{$user_prefix}_users` WHERE popmeson<>0 LIMIT 1;"))) {
                $sqlqry[] = "UPDATE `{$user_prefix}_users` SET user_pm_poptime='" . $userconfig->pm_poptime . "' WHERE user_pm_poptime=0 AND popmeson<>0 AND uname<>'Anonymous';";
            }
        }
        // von nuke6.5  user_popup_pm
        if (isset($tf['user_popup_pm'])) {
            if (sql_num_rows(sql_query("SELECT uid FROM `{$user_prefix}_users` WHERE user_popup_pm<>0 LIMIT 1;"))) {
                $sqlqry[] = "UPDATE `{$user_prefix}_users` SET user_pm_poptime='" . $userconfig->pm_poptime . "' WHERE user_pm_poptime=0 AND user_popup_pm<>0 AND uname<>'Anonymous';";
            }
        }
    }
    // von nuke7.4  last_ip, verwenden fuer user_lastip
    if (isset($tf['last_ip'])) {
        $sqlqry[] = "UPDATE `{$user_prefix}_users` SET user_lastip = last_ip WHERE user_lastip = ''";
    }
}
// Anonymous pruefen u. ggf. anpassen, nur wenn ueberhaupt user vorhanden
if ($usercount) {
    // feststellen, ob 'Anonymous' vorhanden
    $result = sql_query("SELECT uid, user_stat, user_ingroup FROM `{$user_prefix}_users` WHERE uname='Anonymous' OR (name='' AND uname='Gast');");
    $anoncount = sql_num_rows($result);
}
if (!empty($anoncount)) {
    // wenn 'Anonymous' vorhanden, diesen aktualisieren
    if ($anoncount > 1) {
        $sqlqry[] = "DELETE FROM `{$user_prefix}_users` WHERE uname='Anonymous' OR (name='' AND uname='Gast');";
        $anoncount = 0;
    } else {
        $use = sql_fetch_array($result);
        if ($use['user_stat'] != -1 || $use['user_ingroup'] != 0) {
            $sqlqry[] = "UPDATE `{$user_prefix}_users` SET `user_ingroup` = 0, `user_stat` = -1 WHERE uname='Anonymous' OR (name='' AND uname='Gast');";
        }
    }
}
if (empty($anoncount)) {
    // wenn 'Anonymous' nicht vorhanden, diesen einfuegen
    $sqlqry[] = "REPLACE INTO `{$user_prefix}_users` SET
    `uid` = 1,
    `name` = 'Anonymous',
    `uname` = 'Anonymous',
    `user_ingroup` = 0,
    `user_lastvisit` = " . time() . ",
    `user_regtime` = " . time() . ",
    `user_stat` = -1;    ";
}
unset($anoncount);
// ende Anonymous
// cportal..
if ($usercount) {
    // versch. Felder versuchen zu fixen
    if (isset($tf['user_showmail']) && isset($tf['femail'])) {
        $result = sql_query("SELECT count(uid) FROM `{$user_prefix}_users` WHERE femail='' AND user_showmail<>''");
        list($cnt) = sql_fetch_row($result);
        if ($cnt) {
            $sqlqry[] = "UPDATE `{$user_prefix}_users` SET femail=user_showmail WHERE femail='' AND user_showmail<>''";
        }
    }
    if (isset($tf['user_website']) && isset($tf['url'])) {
        $result = sql_query("SELECT count(uid) FROM `{$user_prefix}_users` WHERE url='' AND user_website<>''");
        list($cnt) = sql_fetch_row($result);
        if ($cnt) {
            $sqlqry[] = "UPDATE `{$user_prefix}_users` SET url=user_website WHERE url='' AND user_website<>''";
        }
    }
    // geloeschte User in cPortal
    $result = sql_query("SELECT uid, uname, name FROM `{$user_prefix}_users` WHERE uname='Exmitglied' AND name='Gast' AND email='';");
    while (list($uid, $uname, $name) = sql_fetch_row($result)) {
        $sqlqry[] = "UPDATE `{$user_prefix}_users` SET uname='Exmitglied_" . $uid . "', name='deleted', `user_ingroup` = 0, `user_stat` = -1 WHERE uid=" . intval($uid) . "";
    }

    if (isset($sqlqry)) {
        setupDoAllQueries($sqlqry);
        unset($sqlqry);
    }
    // Kontrolle, ob fuer uname der eindeutige Index existiert, bzw. angelegt werden kann
    // dazu alle doppelten uname suchen und ggf. den Namen mit der uid ergaenzen
    $qry = "SELECT u1.uid, u1.uname, u2.uid AS uid2
            FROM `{$user_prefix}_users` AS u1 JOIN `{$user_prefix}_users` AS u2 ON (u1.uname = u2.uname)
            WHERE u1.uid <> u2.uid
            ORDER BY u1.uname ASC, u1.uid ASC
            ";
    $result = sql_query($qry);
    if ($result) {
        $names = array();
        while ($row = sql_fetch_assoc($result)) {
            // fuer jeden uname, der doppelt vorkommt, ein Array erstellen
            $names[strtolower($row['uname'])][$row['uid2']] = $row;
        }
        if (!empty($names)) {
            $uids = array();
            // das Array mit den doppelten uname durchlaufen
            foreach($names as $name => $row) {
                // immer den letzten gefundenen Datensatz entfernen, weil der das Original sein soll
                // Angenommen wird immer, dass der aelteste uname der korrekte ist, dieser bleibt unveraendert
                array_pop($row);
                // alle uid's der doppelten unames in ein Array einlesen
                foreach($row as $values) {
                    $uids[] = $values['uid2'];
                }
            }
            // die gesammelten uid's als Bedingung fuer die Aenderungsabfrage verwenden
            if (!empty($uids)) {
                $sqlqry[] = "UPDATE `{$user_prefix}_users` SET uname=CONCAT(uname, '_', uid) WHERE uid IN(" . implode(',', $uids) . ")";
            }
        }
    }
}
// ab nuke 6.0 u. 6.5 u. cPortal, unnoetige Felder loeschen erst hier,
// falls diese Daten noch zur Konvertierung gebraucht werden
// nur wenn Tabelle noch nicht von Nuke konvertiert wurde
$delfields = array('broadcast',
    'karma',
    'last_ip',
    'points',
    'popmeson',
    'user_active',
    'user_actkey',
    'user_allow_pm',
    'user_allow_viewonline',
    'user_allowavatar',
    'user_allowbbcode',
    'user_allowhtml',
    'user_allowsmile',
    'user_avatar_type',
    'user_dateformat',
    'user_emailtime',
    'user_last_privmsg',
    'user_new_privmsg',
    'user_newpasswd',
    'user_notify_pm',
    'user_notify',
    'user_popup_pm',
    'user_session_page',
    'user_session_time',
    'user_sig_bbcode_uid',
    'user_style',
    'user_unread_privmsg',
    // cportal
    'user_website',
    'user_showmail',
    'user_skype',
    'user_group_cp',
    'user_group_list_cp',
    'user_active_cp',
    'user_lastvisit_cp',
    'user_regdate_cp',
    );
foreach($delfields as $delfield) {
    if (isset($tf[$delfield])) $sqlqry[] = "ALTER TABLE `{$user_prefix}_users` DROP $delfield";
}

if (isset($sqlqry)) {
    setupDoAllQueries($sqlqry);
    unset($sqlqry);
}
// Indexe anpassen u. erstellen
$indexes = setupGetTableIndexes("{$user_prefix}_users");
if (isset($indexes['uid'])) $sqlqry[] = "ALTER TABLE `{$user_prefix}_users` DROP INDEX `uid` ";
if (isset($indexes['PRIMARY']) && $indexes['PRIMARY']['Column_name'] != 'uid') {
    $sqlqry[] = "ALTER TABLE `{$user_prefix}_users` DROP PRIMARY KEY , ADD PRIMARY KEY ( `uid` ) ";
} else if (!isset($indexes['PRIMARY'])) {
    $sqlqry[] = "ALTER TABLE `{$user_prefix}_users` ADD PRIMARY KEY ( `uid` ) ";
}
if (!isset($indexes['uname'])) {
    $sqlqry[] = "ALTER TABLE `{$user_prefix}_users` ADD UNIQUE `uname` (`uname`);";
} else if (isset($indexes['uname']) && $indexes['uname']['Non_unique'] == 1) {
    $sqlqry[] = "ALTER TABLE `{$user_prefix}_users` DROP INDEX `uname` , ADD UNIQUE `uname` ( `uname` );";
}
if (!isset($indexes['user_ingroup'])) $sqlqry[] = "ALTER TABLE `{$user_prefix}_users` ADD INDEX `user_ingroup` ( `user_ingroup` );";
if (!isset($indexes['user_stat'])) $sqlqry[] = "ALTER TABLE `{$user_prefix}_users` ADD INDEX `user_stat` ( `user_stat` );";
if (!isset($indexes['user_lastvisit'])) $sqlqry[] = "ALTER TABLE `{$user_prefix}_users` ADD INDEX `user_lastvisit` ( `user_lastvisit` )";
if (!isset($indexes['user_lastmod'])) $sqlqry[] = "ALTER TABLE `{$user_prefix}_users` ADD INDEX `user_lastmod` ( `user_lastmod` )";
if (!isset($indexes['user_bday'])) $sqlqry[] = "ALTER TABLE `{$user_prefix}_users` ADD INDEX `user_bday` ( `user_bday` );";
if (!isset($indexes['email'])) $sqlqry[] = "ALTER TABLE `{$user_prefix}_users` ADD INDEX `email` ( `email` ) ";
if (!isset($indexes['user_sexus'])) $sqlqry[] = "ALTER TABLE `{$user_prefix}_users` ADD INDEX `user_sexus` ( `user_sexus` ) ";
if (isset($indexes['karma'])) $sqlqry[] = "ALTER TABLE `{$user_prefix}_users` DROP INDEX `karma` ";
if (isset($indexes['pass'])) $sqlqry[] = "ALTER TABLE `{$user_prefix}_users` DROP INDEX `pass` ";

if (isset($sqlqry)) {
    setupDoAllQueries($sqlqry);
    unset($sqlqry);
}

if ($usercount) {
    /* alte Avatare, von vor 1.12, konvertieren */

    $qry = "SELECT DISTINCT user_avatar
        FROM `{$user_prefix}_users`
        WHERE user_avatar NOT LIKE '%/%'
        ORDER BY user_avatar";
    $result = sql_query($qry);
    $used_avatars = array();
    while (list($ava) = sql_fetch_row($result)) {
        $used_avatars[$ava] = $ava;
    }

    if ($used_avatars) {
        switch (true) {
            case function_exists('load_class'):
                $pici = load_class('Userpic');
                $path_avatars = $pici->path_avatars;
                $available_avatars = $pici->get_available_avatars();
                break;
            case file_exists(PMX_MODULES_DIR . DS . 'Your_Account' . DS . 'config.php') && include(PMX_MODULES_DIR . DS . 'Your_Account' . DS . 'config.php'):
                if (isset($path_avatars)) {
                    $available_avatars = setup_get_available_avatars(realpath(PMX_REAL_BASE_DIR . DS . $path_avatars));
                    break;
                }
            default:
                $path_avatars = 'images/forum/avatar';
                $available_avatars = setup_get_available_avatars(realpath(PMX_REAL_BASE_DIR . DS . $path_avatars));
        }

        $updates = array_filter(array_intersect_key($available_avatars, $used_avatars));
        $deletes = array_filter(array_diff_key($used_avatars, $available_avatars));

        if ($updates) {
            if (isset($updates['blank.gif'])) {
                unset($updates['blank.gif']);
                $deletes['blank.gif'] = 'blank.gif';
            }
            $sqlqry[] = "UPDATE `{$user_prefix}_users`
                  SET user_avatar=concat('" . $path_avatars . "/', user_avatar)
                  WHERE user_avatar IN('" . implode("','", array_keys($updates)) . "')";
        }

        if ($deletes) {
            $sqlqry[] = "UPDATE `{$user_prefix}_users`
                  SET user_avatar=''
                  WHERE user_avatar IN('" . implode("','", array_keys($deletes)) . "')";
        }
    }
}

if (isset($sqlqry)) {
    setupDoAllQueries($sqlqry);
    unset($sqlqry);
}

?>