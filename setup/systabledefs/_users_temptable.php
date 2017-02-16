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
// sicherstellen, dass die Usertabelle bereits aktualisiert ist
include_once(__DIR__ . '/_users.php');

unset($sqlqry);
// /////////////////////////////////////////////////////////////////////////////////////////////////
// check und Anlegen der user_temptabelle:
if (!isset($tables["{$user_prefix}_users_temptable"])) {
    $check = false;
    // Struktur der Usertabelle auslesen
    list($tname, $tcreate) = sql_fetch_row(sql_query("SHOW CREATE TABLE `{$user_prefix}_users`;"));
    // Die Struktur bearbeiten, Tabellennamen ersetzen
    $tcreate = preg_replace('#(CREATE\sTABLE\s`?)(' . $tname . ')(`?\s)#i', '$1' . $user_prefix . '_users_temptable$3', $tcreate);
    // usertemptabelle anlegen, mit der Struktur von der Usertabelle
    $sqlqry[] = $tcreate;
} else {
    $check = true;
    $tf_user = setupGetTableFields("{$user_prefix}_users");
    $tf_temp = setupGetTableFields("{$user_prefix}_users_temptable");
    $newfields = array('check_key', 'check_time', 'check_ip', 'check_host', 'check_thepss', 'check_isactive');
    // pruefen, ob alle Felder der Usertabelle auch korrekt in der Temp-Tabelle vorhanden sind
    foreach ($tf_user as $field => $values) {
        if (!isset($tf_temp[$field]) || $values != $tf_temp[$field]) {
            $check = false;
            break;
        }
    }
    // pruefen ob auch alle zusaetzlichen Felder in der Temp-Tabelle vorhanden sind
    foreach ($newfields as $field) {
        if (!isset($tf_temp[$field])) {
            $check = false;
            break;
        }
    }
    // unnoetige Felder in der Temp-Tabelle entfernen
    foreach ($tf_temp as $field => $values) {
        if (!isset($tf_user[$field]) && !in_array($field, $newfields)) {
            $sqlqry[] = "ALTER TABLE `{$user_prefix}_users_temptable` DROP `$field`";
        }
    }
}

if (isset($sqlqry)) {
    setupDoAllQueries($sqlqry);
    unset($sqlqry);
}
// wenn die Temp-Tabelle bis hierher ok ist, dann das script hier beenden
if (!$check) {
    // eindeutiger Name für temporäre Tabelle
    $temptable = uniqid('temp_tbl_');
    // die zusätzlichen Felder für die temporäre Tabelle
    $createfields = "ALTER TABLE `$temptable`
    ADD `check_key` int(5) NOT NULL default 0,
    ADD `check_time` int(11) NOT NULL default 0,
    ADD `check_ip` varchar(16) NOT NULL default '',
    ADD `check_host` varchar(60) NOT NULL default '',
    ADD `check_thepss` varchar(40) NOT NULL default '',
    ADD `check_isactive` tinyint(1) NOT NULL default 0
    ";
    // Struktur der Usertabelle auslesen
    list($tname, $tcreate) = sql_fetch_row(sql_query("SHOW CREATE TABLE `{$user_prefix}_users`;"));
    // Die Struktur bearbeiten, Tabellennamen ersetzen
    $tcreate = preg_replace('#(CREATE\sTABLE\s`?)(' . $tname . ')(`?\s)#i', '$1' . $temptable . '$3', $tcreate);
    // falls temporäre Tabelle existiert, diese löschen
    $sqlqry[] = "DROP TABLE IF EXISTS `$temptable`;";
    // neue temporäre Tabelle erstellen
    $sqlqry[] = $tcreate;
    // die zusätzlichen Felder in der neuen temporäre Tabelle erstellen
    $sqlqry[] = $createfields;
    if (isset($sqlqry)) {
        setupDoAllQueries($sqlqry);
        unset($sqlqry);
    }
    // prüfen, ob Neuanmeldungen in der alten temporären Tabelle vorhanden sind
    list($newcount) = sql_fetch_row(sql_query("SELECT COUNT(uid) FROM `{$user_prefix}_users_temptable`"));
    // wenn ja, diese in die neue Tabelle importieren
    if ($newcount) {
        // alle Felder der neuen Temp-Table auslesen
        $qry = "SHOW COLUMNS FROM `${temptable}`;";
        $result = sql_query($qry);
        while (list($fieldname) = sql_fetch_row($result)) {
            $new_fields[$fieldname] = $fieldname;
        }
        // alle Felder der alten Temp-Table auslesen
        $qry = "SHOW COLUMNS FROM `{$user_prefix}_users_temptable`;";
        $result = sql_query($qry);
        while (list($fieldname) = sql_fetch_row($result)) {
            // nur wenn bereits das entsprechende Feld auch in der neuen Tabelle existiert..
            if (isset($new_fields[$fieldname])) {
                $old_fields[$fieldname] = $fieldname;
            }
        }
        // die Arrays noch umgekehrt abgleichen und daraus neues Array erstellen,
        // welches nur Feldnamen enthält, die in beiden Tabellen vorkommen
        foreach ($new_fields as $fieldname) {
            // nur wenn bereits das entsprechende Feld auch in der alten Tabelle existiert..
            if (isset($old_fields[$fieldname])) {
                $change_fields[$fieldname] = $fieldname;
            }
        }
        if (isset($change_fields)) {
            $change_fields = '`' . implode('`,`', $change_fields) . '`';
            // die Insertanweisung zusammensetzen
            $qry = "INSERT INTO `$temptable` (" . $change_fields . ") SELECT " . $change_fields . " FROM `{$user_prefix}_users_temptable`;";
            // Insertanweisung ausführen
            $sqlqry[] = $qry;
        }
    }
    // die alte temporäre Tabelle löschen
    $sqlqry[] = "DROP TABLE IF EXISTS `{$user_prefix}_users_temptable`;";
    // die neue Tabelle umbenennen zum Namen der alten...
    $sqlqry[] = "ALTER TABLE `$temptable` RENAME `{$user_prefix}_users_temptable` ;";
    // Aufräumen falls temporäre Tabelle existiert, diese löschen
    $sqlqry[] = "DROP TABLE IF EXISTS `$temptable`;";
    // /////////////////////////////////////////////////////////////////////////////////////////////////
}

if (isset($sqlqry)) {
    setupDoAllQueries($sqlqry);
    unset($sqlqry);
}

?>
