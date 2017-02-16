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
 * Backup erstellen
 */
function setup_dbbackup($only_check = false)
{
    include(FILE_CONFIG_ROOT);
    if (is_file(FILE_BACKUP)) {
        rename(FILE_BACKUP, FILE_BACKUP . '.' . time() . '.bak');
        @clearstatcache();
    }
    if ($only_check) {
        // DB-Verbindung aufbauen
        $dbstat = setupConnectDb();
        if (isset($dbstat['dbi'])) {
            // eine Tabelle ermitteln fuer den Test
            $result = sql_query("SHOW TABLES;");
            list($tablename) = sql_fetch_row($result);
        }
    }
    if (function_exists('system') && function_exists('escapeshellcmd')) {
        // optionen
        $u = ($dbuname) ? " -u $dbuname " : '';
        $p = ($dbpass) ? " -u $dbpass " : '';
        // bei only_check, wird ein Tabellenname uebergeben
        $t = empty($tablename) ? '' : $tablename;
        // Shell-Kommando zusammensetzen, doppelte leerzeichen entfernen und sonderzeichen maskieren
        $cmd = @escapeshellcmd(BACKUP_DUMP_OPTIONS . " -h $dbhost $u $p $dbname $t ");
        $cmd = preg_replace('#[[:space:]]+#', ' ', "mysqldump $cmd > " . FILE_BACKUP);
        // mysqldump starten
        @system($cmd, $out);
        // wenn datei nicht geschrieben wurde
        @clearstatcache();
    }

    if (!is_file(FILE_BACKUP) || @filesize(FILE_BACKUP) == 0) {
        // und ist windows
        if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
            // DB-Verbindung aufbauen
            $dbstat = setupConnectDb();
            if (isset($dbstat['dbi'])) {
                // mysql-basedir ermitteln
                $result = sql_query("SHOW VARIABLES LIKE 'basedir'");
                if ($result) {
                    $var = sql_fetch_row($result);
                    // den Pfad zu mysqldump erstellen
                    $dumpprog = $var[1] . 'bin\\mysqldump.exe';
                    $cmd = str_replace('mysqldump', $dumpprog, $cmd);
                    // mysqldump starten
                    @system($cmd, $out);
                }
            }
        }
    }

    clearstatcache();
    if (is_file(FILE_BACKUP)) {
        $cont = file_get_contents(FILE_BACKUP);
        if (preg_match('#CREATE[[:space:]]+TABLE[[:space:]]#i', $cont)) {
            @chmod(FILE_BACKUP, octdec(CHMODFULLOCK));
            $stat['msg'] = '<strong>' . _HAVE_CREATE_DBBACKUP . '</strong><br />&nbsp;&nbsp;' . str_replace(dirname(__DIR__) . '/', '', FILE_BACKUP) . ' (' . filesize(FILE_BACKUP) . '&nbsp;byte)';
            $stat['stat'] = 1;
        } else {
            @unlink(FILE_BACKUP);
            $stat['msg'] = '
            	<div class="alert alert-block">' . _HAVE_CREATE_BACKUPERR_1 . '</div>
            	<div class="alert alert-info alert-block">' . _HAVE_CREATE_BACKUPERR_2 . '</div>';
            $stat['stat'] = 0;
        }
    } else {
        $stat['msg'] = '
        	<div class="alert alert-block">' . _HAVE_CREATE_BACKUPERR_1 . '</div>
        	<div class="alert alert-info alert-block">' . _HAVE_CREATE_BACKUPERR_2 . '</div>';
        $stat['stat'] = 0;
    }
    if ($only_check) {
        if ($stat['stat'] == 1) {
            @unlink(FILE_BACKUP);
            return true;
        } else {
            return false;
        }
    }
    return $stat;
}

?>