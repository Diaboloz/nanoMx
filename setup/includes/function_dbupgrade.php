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

/**
 */
function setup_dbupgrade($dbi)
{
    @ini_set("max_execution_time", 400);
    // $prefix, $user_prefix auf jeden Fall Global stellen, weil in versch. Modulen $GLOBALS verwendet wird
    global $tables, $prefix, $user_prefix;
    $tables = array();
    global $structure;
    $structure = array();
    // das Rückgabearray mit Grundwerten Initialisieren
    $querystat['status'] = 'ok';
    $querystat['count_ok'] = 0;
    $querystat['count_err'] = 0;
    $querystat['count_all'] = 0;
    $querystat['msg'] = array();
    $querystat['msg_ok'] = array();
    $querystat['msg_err'] = array();
    // config.php includen, damit db-Verbindung aufgebaut werden kann und sonstige Einstellungen verfügbar sind
    include(FILE_CONFIG_ROOT);
    // Systemtabellen- Definitionsdateien in Array einlesen
    $requiredfiles = array_flip(setupRequiredTables($prefix, $user_prefix));
    $ignoretabledefinitions = setupIgnoreTabledefinitions();
    if (@file_exists(PATH_SYSTABLES) && !@is_file(PATH_SYSTABLES) && !@is_link(PATH_SYSTABLES)) {
        $sysdir = opendir(realpath(PATH_SYSTABLES));
        while (false !== ($file = readdir($sysdir))) {
            if (preg_match("#^(_.*)\.php$#", $file, $matches) && is_file(PATH_SYSTABLES . "/" . $file)) {
                // die zu ignorierenden Dateien ueberspringen
                if (in_array($matches[1], $ignoretabledefinitions)) {
                    continue;
                }
                // das Array mit den benötigten Tabellen-Dateien verkleinern, damit nur noch fehlende Tabellen übrigbleiben
                // das Array der zu includenden Dateien füllen
                if (isset($requiredfiles[$matches[1]])) {
                    unset($requiredfiles[$matches[1]]);
                    $sysfile[$file] = 'required';
                } else {
                    $sysfile[$file] = '';
                }
            }
        }
        closedir($sysdir);
        unset($sysdir);
    }
    // wenn Ordner komplett leer
    if (!isset($sysfile)) {
        $querystat['msg'][] = '<div class="alert alert-error alert-block">' . _ERRDBSYSFILENOFILES . '</div>';
        // > Setup abbrechen
        $querystat['status'] = 'critical';
        return $querystat;
    }
    // wenn alle benötigten Tabellen vorhanden sind, müsste das Array leer sein
    if (count($requiredfiles)) {
        // mxDebugFuncVars($requiredtables);
        // wenn nicht, entsprechende Meldung und Abbruch des setup
        $requiredfiles = implode(', ', array_flip($requiredfiles));
        $querystat['msg'][] = '
        	<div class="alert alert-error alert-block">
        		' . _ERRDBSYSFILEMISSFILES_1 . '<br />
        		' . _ERRDBSYSFILEMISSFILES_2 . ':
        	<ul>
        		<li><strong>' . $requiredfiles . '</li>
        	<ul>
        	</div>';
        $querystat['status'] = 'critical';
        return $querystat;
    }
    // die bestehende Datenbankstruktur einlesen
    $result = sql_query("SHOW TABLES;");
    while (list($tablename) = sql_fetch_row($result)) {
        if (preg_match("#^(" . RENAME_PREFIX . "|${prefix}_|${user_prefix}_)#", $tablename)) {
            // !!! Die Tabellen vom eBoard werden erst in der eBoard-Definitionsdatei berücksichtigt,
            // !!! weil die Tabellennamen nicht unbedingt mit dem definierten Prefix übereinstimmen müssen
            // Array mit allen bereits vorhandenen Tabellen erstellen
            $tables[$tablename] = $tablename;
            // $query = "SHOW CREATE TABLE `$tablename`;";
            // $xresult = sql_query($query);
            // if (sql_error()) {
            // $msgquery = preg_replace('#[[:space:]]{1,}#', ' ', $query);
            // $querystat['msg_err'][] = "ERR: " . sql_error() . "\nin:  " . $msgquery . "\n\n";
            // }
            // // Array mit der Struktur aller bereits vorhandenen Tabellen erstellen
            // if ($xresult) {
            // list($tname, $structure[$tablename]['before']) = sql_fetch_row($xresult);
            // } else {
            // $tname = $tablename;
            // $structure[$tablename]['before'] = '# error before: SHOW CREATE TABLE `' . $tablename . '`;';
            // }
        }
    }
    // Systemtabellen-Definitionsdateien includen
    // dadurch Systemtabellen prüfen/konvertieren/erstellen
    $includefile = array();
    foreach($sysfile as $file => $stat) {
        // Datei includen
        if (include_once(PATH_SYSTABLES . "/$file")) {
            // bei Erfolg
            $includefile[] = $file;
        } else {
            // wenn unbedingt nötige Datei
            if ($stat == 'required') {
                $errfile['critical'][] = $file;
                // Schleife abbrechen, damit nichts weiter passiert...
                break;
            } else {
                $errfile['check'][] = $file;
            }
        }
        // falls die Variable nicht bereits in der includeten Datei gelöscht wurde
        unset($sqlqry);
    }
    unset($file, $stat);

    /* die globale Aufräum- und Änderungsdatei noch ausführen... */
    /* Hier werden auch die Standardwerte, bzw. die alten Werte in die _sys_config Tabelle eingetragen */
    if (defined('ADD_QUERIESFILE')) {
        $ok = false;
        if (@is_file(ADD_QUERIESFILE)) {
            $ok = include(ADD_QUERIESFILE);
        }
        if (!$ok) {
            $querystat['msg'][] = _MOREDEFFILEMISSING;
            $querystat['status'] = 'check';
        }
    }

    /* alle unbedingt benötigten Tabellen auf vorhandensein prüfen */
    if (!isset($errfile['critical'])) {
        $requiredtables = setupRequiredTables($prefix, $user_prefix);
        foreach ($requiredtables as $tablename => $tmp) {
            if (!sql_num_rows(sql_query("SHOW TABLES LIKE '$tablename';"))) {
                $errtable[] = $tablename;
            }
        }
    }
    // bisherigen Status, nach erstellen der Systemtabellen, überprüfen
    if (isset($errfile['critical'])) {
        // wenn wichtige Dateien nicht includet
        $files = implode(', ', $errfile['critical']);
        $querystat['msg'][] = sprintf(_THESYSTABLES_1, str_replace('.php', '', $files));
        $querystat['status'] = 'critical';
        // Funktion beenden, wenn schwerer Fehler
        return $querystat;
    }
    if (isset($errtable)) {
        // Alle ausgeführten Queries ermitteln
        // die statisch gespeicherten Nachrichten aus der Funktion 'setupDoAllQueries' auslesen
        // es werden folgende array-keys zurückgegeben:
        // $querystat['msg_ok']    = Array mit erfolgreich ausgeführten Queries
        // $querystat['msg_err']   = Array mit nicht ausgeführten Queries, zusätzlich mit sql-Fehlermeldung
        $querystat = array_merge($querystat, setupDoAllQueries(array()));
        $querystat['msg_err'] = implode('', $querystat['msg_err']);
        // wenn wichtige Tabellen fehlen
        $tables = implode(', ', $errtable);
        $querystat['msg'][] = sprintf(_THESYSTABLES_2, $tables);
        // Funktion beenden, wenn schwerer Fehler
        $querystat['status'] = 'critical';
        return $querystat;
    } else if (isset($errfile['check'])) {
        // wenn 'unwichtige' Systemtabellen fehlen
        $files = implode(', ', $errfile['check']);
        $querystat['msg'][] = sprintf(_THESYSTABLES_2, str_replace('.php', '', $files));
        $querystat['status'] = 'check';
    } else {
        // yep, wenn alles klar ist...
        $querystat['msg'][] = sprintf(_SYSTABLECREATED, count($includefile));
    }
    // Modultabellen prüfen/konvertieren/erstellen
    $includefile = array();
    if (@file_exists(PMX_MODULES_DIR) && !@is_file(PMX_MODULES_DIR) && !@is_link(PMX_MODULES_DIR)) {
        $sysdir = opendir(realpath(PMX_MODULES_DIR));
        while (false !== ($modul = readdir($sysdir))) {
            $file = PMX_MODULES_DIR . '/' . $modul . '/core/install.tabledef.php';
            if (@is_file($file) && @filesize($file)) {
                if (include_once($file)) {
                    unset($sqlqry);
                    $includefile[] = $file;
                }
            }
        }
        closedir($sysdir);
        unset($sysdir, $file);
    }
    if (count($includefile)) {
        $querystat['msg'][] = sprintf(_MODTABLESCREATED, count($includefile));
    } else {
        $querystat['msg'][] = _NOMODTABLES;
        $querystat['status'] = 'check';
    }
    // nach Abschluss aller Datenbankänderungen, die neuen Tabellenstrukturen ermitteln
    // gleichzeitig überprüfen, ob die wichtigsten Systemtabellen existieren
    unset($tables); // das alte Tabellenarray löschen, damit evtl. umbenannte/gelöschte Tabellen entfernt sind
    $requiredtables = setupRequiredTables($prefix, $user_prefix);
    $result = sql_query("SHOW TABLES;");
    while (list($tablename) = sql_fetch_row($result)) {
        if (preg_match("#^(" . RENAME_PREFIX . "|${prefix}_|${user_prefix}_)#", $tablename)) {
            // das Array mit den benötigten Tabellen verkleinern, damit nur noch fehlende Tabellen übrigbleiben
            if (isset($requiredtables[$tablename])) unset($requiredtables[$tablename]);
            // // die neue Tabellenstruktur zwischenspeichern
            // $query = "SHOW CREATE TABLE `$tablename`;";
            // $xresult = sql_query($query);
            // if (sql_error()) {
            // $msgquery = preg_replace('#[[:space:]]{1,}#', ' ', $query);
            // $querystat['msg_err'][] = "ERR: " . sql_error() . "\nin:  " . $msgquery . "\n\n";
            // }
            // // Array mit der Struktur aller bereits vorhandenen Tabellen erstellen
            // if ($xresult) {
            // list($tname, $structure[$tablename]['after']) = sql_fetch_row($xresult);
            // } else {
            // $tname = $tablename;
            // $structure[$tablename]['after'] = '# error after: SHOW CREATE TABLE `' . $tablename . '`;';
            // }
            // neues Array mit Tabellennamen erstellen, wird später für Optimize benötigt
            $tables[$tablename] = $tablename;
        }
    }
    // wenn alle benötigten Tabellen vorhanden sind, müsste das Array leer sein
    if (count($requiredtables)) {
        // wenn nicht, entsprechende Meldung und Abbruch des setup
        $requiredtables = implode(', ', array_flip($requiredtables));
        $querystat['msg'][] = '<p>' . _DB_UPDATEFAIL . '</p><p>' . _DB_UPDATEFAIL2 . ' <strong>' . $requiredtables . '</strong></p>';
        $querystat['status'] = 'critical';
    }
    // wenn leer, dann weitere Aktionen, die bestimmte Tabellen voraussetzen...
    unset($requiredtables);
    // falls Tabellen mit anderem Prefix vorhanden sind, z.B. evtl. im eBoard oder coppermine diese,
    // an das Tabellenarray anfügen. Das Array wird in der jeweiligen Modul-Definitionsdatei gefüllt.
    if (isset($more_tables)) {
        $tables = array_merge($more_tables, $tables);
        unset($more_tables);
    }
    // mxDebugFuncVars($tables);
    // Alle ausgeführten Queries ermitteln
    // die statisch gespeicherten Nachrichten aus der Funktion 'setupDoAllQueries' auslesen
    // es werden folgende array-keys zurückgegeben:
    // $querystat['msg_ok']    = Array mit erfolgreich ausgeführten Queries
    // $querystat['msg_err']   = Array mit nicht ausgeführten Queries, zusätzlich mit sql-Fehlermeldung
    $querystat = array_merge($querystat, setupDoAllQueries(array()));
    // Anzahl aller ausgeführten Queries ermitteln
    $querystat['count_ok'] = count($querystat['msg_ok']);
    $querystat['count_err'] = count($querystat['msg_err']);
    $querystat['count_all'] = $querystat['count_err'] + $querystat['count_ok'];
    // Statistik erstellen
    if (!empty($querystat['create'])) $statistic[] = $querystat['create'] . ' ' . _STAT_TABLES_CREATED;
    if (!empty($querystat['rename'])) $statistic[] = $querystat['rename'] . ' ' . _STAT_TABLES_RENAMED;
    if (!empty($querystat['alter'])) $statistic[] = $querystat['alter'] . ' ' . _STAT_TABLES_CHANGED;
    if (!empty($querystat['data'])) $statistic[] = $querystat['data'] . ' ' . _STAT_DATAROWS_CREATED;
    if (!empty($querystat['deleted'])) $statistic[] = $querystat['deleted'] . ' ' . _STAT_DATAROWS_DELETED;
    if (isset($statistic)) {
        $querystat['msg'][] = _STAT_THEREWAS . ' ' . implode(', ', $statistic);
    }
    // weitere Statusmeldungen generieren
    if ($querystat['count_all'] == 0) {
        // wenn keine Queries ausgeführt wurden, dann ist die DB auf dem geforderten aktuellen Stand
        $querystat['msg'][] = _DATABASEISCURRENT;
    } else if ($querystat['count_ok'] == $querystat['count_all'] && $querystat['count_all'] != 0) {
        // alle Queries ohne Fehler
        $querystat['msg'][] = _NODBERRORS;
    } else {
        // teilweise Queries mit Fehler, aber nicht kritisch
        $querystat['status'] = 'check';
    }
    // wenn Queries ausgeführt wurden, Erfolge in Logfiles schreiben
    if ($querystat['count_ok']) {
        $querystat['msg_ok'] = implode('', $querystat['msg_ok']);
        if (file_put_contents(FILE_LOG_OK, $querystat['msg_ok']) !== false) {
            $querystat['logok'] = FILE_LOG_OK;
        }
    }
    // wenn Queries Fehler verursacht haben, Fehler in Logfiles schreiben
    if ($querystat['count_err']) {
        $querystat['msg_err'] = implode('', $querystat['msg_err']);
        if (file_put_contents(FILE_LOG_ERR, $querystat['msg_err']) !== false) {
            $querystat['logerr'] = FILE_LOG_ERR;
        }
    }

    @sql_close($dbi);

    return $querystat;
}

/**
 * alle erstellten Queries ausfuehren
 * und Fehler / Erfolge loggen
 */
function setupDoAllQueries($allqueries)
{
    static $querystat;
    if (!isset($querystat)) {
        $querystat['msg_ok'] = array();
        $querystat['msg_err'] = array();
        $querystat['create'] = 0;
        $querystat['rename'] = 0;
        $querystat['alter'] = 0;
        $querystat['data'] = 0;
        $querystat['deleted'] = 0;
    }
    $querystat['error'] = 0; // fuer den Erfolg des aktuellen Funktionsaufrufs
    if (!is_array($allqueries)) {
        $allqueries = array($allqueries);
    }
    foreach($allqueries as $query) {
        $query = trim($query);
        if (stripos($query, 'type')) {
            // TYPE=MyISAM nach ENGINE=MyISAM DEFAULT CHARACTER SET utf8  COLLATE utf8_unicode_ci umschreiben
            $query = preg_replace('#(^.+(?:\)|\s))(type\s*=)([^)(]+$)#is', '$1ENGINE=$3', $query);
        }
        sql_query($query);
        $msgquery = preg_replace('#[[:space:]]{1,}#', ' ', $query);
        if (sql_error()) {
            $querystat['msg_err'][] = "ERR: " . sql_error() . "\nin:  " . $msgquery . "\n\n";
            $querystat['error'] = 1;
        } else {
            $querystat['msg_ok'][] = "" . $msgquery . "\n\n";
            preg_match('#(^CREATE)|(^RENAME)|(^ALTER)|(^INSERT|^REPLACE|^UPDATE)|(^ALTER)[[:space:]]#i', $query, $matches);
            if (!empty($matches[1])) $querystat['create']++;
            if (!empty($matches[2])) $querystat['rename']++;
            if (!empty($matches[3])) $querystat['alter']++;
            if (!empty($matches[4])) {
                $querystat['data'] = $querystat['data'] + sql_affected_rows();
            }
            if (!empty($matches[5])) {
                $querystat['deleted'] = $querystat['deleted'] + sql_affected_rows();
            }
        }
    }
    // mxDebugFuncVars($querystat);
    return $querystat;
}

/**
 * die Datenbank-Struktur-Tabelle erstellen, falls noch nicht vorhanden
 */
// function setupCreateStructurTable($structure)
// {
// // PRIMARY KEY  (`setup_id`,`tablename`,`status`)
// sql_query(trim("CREATE TABLE IF NOT EXISTS `" . TABLE_STRUCTURE . "` (
// `setup_id` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
// `tablename` varchar(150) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
// `sdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
// `status` varchar(15) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
// `before` text COLLATE utf8_unicode_ci,
// `after` text COLLATE utf8_unicode_ci NOT NULL
// ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
// "));
// // alte und neue Tabellenstruktur in Install-Tabelle speichern
// foreach ($structure as $tablename => $stat) {
// // mxDebugFuncVars($stat);
// if (empty($stat['before']) && empty($stat['after'])) {
// continue;
// }
// if (empty($stat['before'])) {
// $stat['before'] = '';
// $status = 'new created';
// } else if (empty($stat['after'])) {
// $stat['after'] = '';
// $status = 'renamed/deleted';
// } else if ($stat['before'] != $stat['after']) {
// $status = 'changed';
// } else {
// $status = 'current';
// }
// $qry = "INSERT INTO `" . TABLE_STRUCTURE . "` set
// setup_id='" . SETUP_ID . "',
// tablename='" . $tablename . "',
// sdate=now(),
// status='" . $status . "',
// before='" . addslashes($stat['before']) . "',
// after='" . addslashes($stat['after']) . "'
// ;";
// // mxDebugFuncVars($qry);
// sql_query($qry);
// }
// }
// /**
// * optimiert alle Tabellen, die im Array $tables gespeichert sind
// * erst am Ende optimieren, wegen evtl. Abbruch durch max-execution-time
// */
// function setupOptimizeTables($tables)
// {
// foreach ($tables as $tablename) {
// sql_query("OPTIMIZE TABLE `$tablename`;");
// }
// }

?>