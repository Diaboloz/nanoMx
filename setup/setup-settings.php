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
 * $Revision: 231 $
 * $Author: PragmaMx $
 * $Date: 2016-09-29 10:15:05 +0200 (Do, 29. Sep 2016) $
 */

defined('mxMainFileLoaded') or die('access denied');

/**
 * Welches soll die Hauptsprache sein?
 * Dieser Wert wird benoetigt, wenn mehr Sprachen im System Setup existieren
 * als das eigentliche Setup tatsaechlich besitzt
 */
define("SETUP_DEFAULTLANG", "french");

define("MX_SETUP_VERSION_NUM", "1.0");
define("MX_SETUP_VERSION", "nanoMx " . MX_SETUP_VERSION_NUM);
define("MX_SETUP_VERSION_SUB", "new-setup");
define("MX_SETUP_MIN_PHPVERSION", "5.4");
define("MX_SETUP_MIN_MYSQLVERSION", "5.1.");
// only for phpNuke modules compatibility!
define("MX_NUKE_VERSION", "5.6");

/* Kurzform dieser System-Konstanten erstellen */
defined('DS') OR define('DS', DIRECTORY_SEPARATOR);

/* Sprachauswahl */
define('_SELECT_LANG', '
Bitte w&auml;hlen Sie eine Sprache  - Please select a language - Veuillez s&eacute;lectionner votre langue - V&aelig;lg et sprog - L&uuml;tfen bir lisan se&#231;iniz
');

/**
 * Setup Optionen zur Auswahl
 */
// Neuinstallation
$opt[1]['type'] = 'system';
$opt[1]['version'] = 'new';
$opt[1]['name'] = '_SETUPOPTION_NEW';
$opt[1]['description'] = '_SETUPOPTION_NEW_DESC';
// Update
$opt[2]['type'] = 'system';
$opt[2]['version'] = 'update';
$opt[2]['name'] = '_SETUPOPTION_UPDATE';
$opt[2]['description'] = '_SETUPOPTION_UPDATE_DESC';
// Pfade zur eigentlichen pragmaMx Installation, ausgehend vom setup-Ordner
define('PMX_REAL_BASE_DIR', dirname(__DIR__));
define('PMX_SYSTEM_DIR', PMX_REAL_BASE_DIR . DS . 'includes');
define('PMX_MODULES_DIR', PMX_REAL_BASE_DIR . DS . 'modules');
define('PMX_BLOCKS_DIR', PMX_REAL_BASE_DIR . DS . 'blocks');
define('PMX_LANGUAGE_DIR', PMX_REAL_BASE_DIR . DS . 'language');
define('PMX_DYNADATA_DIR', PMX_REAL_BASE_DIR . DS . 'dynadata');

    /* Basis-Pfad ermitteln etc. */
	
    $info_script_name = pathinfo($_SERVER['SCRIPT_NAME']);
    $scriptpath = str_replace(DS, '/', realpath(dirname($_SERVER['SCRIPT_FILENAME'])));
    $basepath = str_replace(DS, '/', PMX_REAL_BASE_DIR)."/";

    /* just the subfolder part between <installation_path> and the page */
    $scriptpath = substr($scriptpath, strlen($basepath));
	/* neu, da subst unter PHP7 anders funktioniert */
	$scriptpath =($scriptpath=="")?FALSE:$scriptpath;
	
    $rootpath = str_replace(DS, '/', $info_script_name['dirname']);
    /*we subtract the subfolder part from the end of <installation_path>, leaving us with just <installation_path> :)*/
    if ($scriptpath !== false) {
        $rootpath = str_replace('//', '/', substr($rootpath, 0, - strlen($scriptpath)) . '/');
        $scriptpath = trim($scriptpath, '/') . '/';
    } else {
        $rootpath = str_replace('//', '/', $rootpath . '/');
        $scriptpath = '';
    }
	
	
/* der wichtigste Pfad: zum mx-Root */
define('PMX_BASE_PATH', $rootpath);

// Pfad fuer Logfiles
if (file_exists(PMX_DYNADATA_DIR . DS . 'logfiles') && is_writable(PMX_DYNADATA_DIR . DS . 'logfiles')) {
    define('PATH_LOGFILES', PMX_DYNADATA_DIR . DS . 'logfiles');
} else {
    define('PATH_LOGFILES', __DIR__ . '/logs');
}
// Pfad fuer Backup's
if (file_exists(PMX_DYNADATA_DIR . DS . 'backup') && is_writable(PMX_DYNADATA_DIR . DS . 'backup')) {
    define('PATH_BACKUP', PMX_DYNADATA_DIR . DS . 'backup');
} else {
    define('PATH_BACKUP', PATH_LOGFILES);
}
// Pfad fuer Setup Includes
define('PATH_SETUP_INCLUDES', __DIR__ . '/includes');
// die config Datei mit den Standardeinstellungen der Config-Klasse
define('FILE_CONFIG_DEFAULTS', PMX_SYSTEM_DIR . '/classes/Config/defaults.php');
// html-template fuer das setup
define("FILE_SETUP_TEMPLATE", "html/index.html");
// die bestehende config.php im ROOT
define('FILE_CONFIG_ROOT', PMX_REAL_BASE_DIR . DS . 'config.php');
// config.first.php entspricht der originalen config.php mit kleinen Aenderungen
// weitere Hinweise in dieser Datei !
define('FILE_CONFIG_BASE', PATH_SETUP_INCLUDES . '/config.first.php');
// der Code von mx_configstring.php wird direkt, ohne Aenderungen
// aus /admin/modules/settings.php kopiert !!
define('FILE_CONFIG_NEWCONTENT', PATH_SETUP_INCLUDES . '/mx_configstring.php');
// Array mit einzufuegenden Modulen
define('FILE_ADD_MODULES', PATH_SETUP_INCLUDES . '/addmodules.php');

/* Funktionen zur Behandlung von unnötig gewordenen Dateien */
define('FILE_DELETE_FILES', PMX_SYSTEM_DIR . DS . 'delfiles/functions.php');
// Standard chmod
// -- ACHTUNG, das muss fuer die Funktion chmod() mit octdec() noch umgewandelt werden
define("CHMODLOCK" , "0444");
define("CHMODNORMAL", "0644");
define("CHMODUNLOCK", "0666");
define("CHMODFULLOCK", "0400");
define("CHMODFULLUNOCK", "0777");
// Setup-ID: die Variable mxsetupid wird in allen Formularen und Links uebergeben
if (empty($_REQUEST['mxsetupid'])) {
    define("SETUP_ID", date("Ymdhis"));
} else {
    define("SETUP_ID", $_REQUEST['mxsetupid']);
}
// Logfiles
define('FILE_LOG_ERR', PATH_LOGFILES . '/setup_' . SETUP_ID . '.errlog.txt');
define('FILE_LOG_OK', PATH_LOGFILES . '/setup_' . SETUP_ID . '.oklog.txt');
define('FILE_LOG_QUERIES', PATH_LOGFILES . '/setup_' . SETUP_ID . '.queries.txt');
// Ordner mit den Systemtabellen-Definitionen
define('PATH_SYSTABLES', 'systabledefs');
// Tabellenstruktur-Tabelle
define('TABLE_STRUCTURE', '_mx_installed_tables');
// Prefix fuer umbenannte Tabellen
define('RENAME_PREFIX', 'zz' . SETUP_ID . '_');
// Ordnername fuer umbenanntes Setup-Dir
define('SETUP_RENAME', '_' . SETUP_ID . '.' . md5 (uniqid (rand())));
// die globale Aufraeumdatei mit zusaetzlichen sql-Befehlen
define('ADD_QUERIESFILE', PATH_SYSTABLES . '/update.php');
// versch. Links zu pragmaMx
define('_MXDOKUSITE' , 'http://www.pragmamx.org/Documents.html');
define('_MXSUPPORTSITE' , 'http://www.pragmamx.org/Forum.html');
define('_MXKNOWPROBSITE', 'http://www.pragmamx.org/Forum-board-103.html');
// Backupoptionen
define('FILE_BACKUP', realpath(PATH_BACKUP) . DIRECTORY_SEPARATOR . 'mxInstallBackup_' . SETUP_ID . '.sql');
$o[] = '--add-drop-table'; // Ein drop table vor jedem create-Statement hinzufuegen.
$o[] = '--add-locks'; // Fuehrt LOCK TABLES vor und UNLOCK TABLE nach jedem Tabellen-Dump durch (um schnelleres Einfuegen in MySQL zu erreichen).
$o[] = '-a'; // Alle MySQL-spezifischen Optionen fuer create benutzen.
$o[] = '-c'; // Vollstaendige insert-Statements benutzen (mit Spaltennamen).
$o[] = '-f'; // Fortfahren, selbst wenn beim Dump einer Tabelle ein SQL-Fehler auftritt.
$o[] = '-l'; // Alle Tabellen sperren, bevor mit dem Dump begonnen wird. Die Tabellen werden mit READ LOCAL gesperrt, um gleichzeitiges Einfuegen zu erlauben (bei MyISAM-Tabellen).
$o[] = '-C'; // Alle Informationen zwischen Client und Server komprimieren, wenn bei Kompression unterstuetzen.
// $o[] = '-n';                    // 'CREATE DATABASE /*!32312 IF NOT EXISTS*/ datenbank;' wird nicht in die Ausgabe gschrieben. Diese Zeile wird ansonsten hinzugefuegt, wenn --databases oder --all-databases angegeben wurde.
define('BACKUP_DUMP_OPTIONS', ' ' . implode(' ', $o) . ' ');
unset($o);

if (!defined("SYS_BLOCKPOS_CENTER")) define("SYS_BLOCKPOS_CENTER", "c");
if (!defined("SYS_BLOCKPOS_LEFT")) define("SYS_BLOCKPOS_LEFT", "l");
if (!defined("SYS_BLOCKPOS_RIGHT")) define("SYS_BLOCKPOS_RIGHT", "r");
if (!defined("SYS_BLOCKPOS_DOWN")) define("SYS_BLOCKPOS_DOWN", "d");
if (!defined("SYS_BLOCKPOS_TOP")) define("SYS_BLOCKPOS_TOP", "t");

/* Standardname für Chefadmin */
if (!defined("PMX_SYSADMIN_NAME")) define('PMX_SYSADMIN_NAME', 'God');

/* max. Laenge des Tabellenprefix */
define('PREFIX_MAXLENGTH', '20');

/**
 * setupFormDefaults()
 * Standardwerte fuer Formulare, wenn irgendwelche Einstellungen leer oder nicht gesetzt sind
 *
 * @return
 */
function setupFormDefaults()
{
    if (@file_exists(FILE_CONFIG_ROOT)) {
        include(FILE_CONFIG_ROOT);
    }
    // Datenbankzugangsdaten
    $def['dbhost'] = (!empty($dbhost)) ? $dbhost : "localhost";
    $def['dbname'] = (!empty($dbname)) ? $dbname : "";
    $def['dbuname'] = (isset($dbuname)) ? $dbuname : "";
    $def['dbpass'] = (isset($dbpass)) ? $dbpass : "";
	$def['dbtype'] = (isset($dbtype)) ? $dbtype : "mysql";
    $def['dbconnect'] = (isset($dbconnect)) ? $dbconnect : "1";
    // Tabellen-Prefixe
    $xpre = strtolower(substr(md5(SETUP_ID), 3, 6));
    $def['prefix'] = (!empty($prefix)) ? $prefix : 'mx' . $xpre;
    $def['user_prefix'] = (!empty($user_prefix)) ? $user_prefix : 'mx' . $xpre;
    // sonstige Einstellungen
    if (empty($sitename)) {
        $def['sitename'] = MX_SETUP_VERSION;
    } else {
        $old_ansi = (isset($adminpanel) || isset($vkpUserregoption) || isset($nukeurl) || isset($sitekey) || !isset($admintheme) || !isset($mxConf));
        if ($old_ansi) {
            $sitename = utf8_encode($sitename);
        }
        $def['sitename'] = $sitename;
    }
    $def['startdate'] = (!empty($startdate)) ? $startdate : strftime(_DATESTRING, time());
    $def['adminmail'] = (!empty($adminmail)) ? $adminmail : $_SERVER['SERVER_ADMIN'];
    $def['language'] = (isset($language)) ? $language : SETUP_DEFAULTLANG;
    if (isset($vkpIntranet)) {
        $def['vkpIntranet'] = (empty($vkpIntranet)) ? 0 : 1;
    } else {
        // Intranet versuchen voreinzustellen, localhost oder kein Punkt im Servername >> ein
        $def['vkpIntranet'] = (strtolower($_SERVER['HTTP_HOST']) == 'localhost' || (strpos($_SERVER['HTTP_HOST'], '.') == false)) ? 1 : 0;
    }
    // mxDebugFuncVars($def);
    return $def;
}

/**
 * setupRequiredTables()
 * unbedingt noetige System-Tabellen die auf vorhandensein kontrolliert werden muessen
 * kann gleichzeitig als Array zur ueberpruefung der entsprechenden Setup-Dateien verwendet werden
 *
 * @param mixed $prefix
 * @param mixed $user_prefix
 * @return
 */
function setupRequiredTables($prefix, $user_prefix)
{
    $table[$prefix . '_authors'] = '_authors';
    $table[$prefix . '_blocks'] = '_blocks';
    $table[$prefix . '_captcher'] = '_captcher';
    $table[$prefix . '_groups_access'] = '_groups_access';
    $table[$prefix . '_groups_blocks'] = '_groups_blocks';
    $table[$prefix . '_groups_modules'] = '_groups_modules';
    $table[$prefix . '_main'] = '_main';
    $table[$prefix . '_menu'] = '_menu';
    $table[$prefix . '_modules'] = '_modules';
    $table[$prefix . '_sys_config'] = '_sys_config';
    $table[$prefix . '_sys_session'] = '_sys_session';
    $table[$prefix . '_visitors'] = '_visitors';
    $table[$user_prefix . '_users'] = '_users';
    $table[$user_prefix . '_users_temptable'] = '_users_temptable';
    return $table;
}

/**
 * setupIgnoreTabledefinitions()
 * veraltete Setup-Dateien die als Karteileichen nicht mehr verwendet
 * werden duerfen und deswegen ignoriert werden, falls noch da...
 *
 * @return
 */
function setupIgnoreTabledefinitions()
{
    return array('_ephem', '_menu_var');
}

/**
 * Feststellen ob ein bestimmter Block mit dem Paket mitgeliefert wurde
 * Also alle Blöcke im Zipfile
 * Zusätzlich noch versch. relevante Addonblöcke
 *
 * DEPRECATED!! seit pragmaMx 1.12 nicht mehr in Verwendung
 */
function is_vkp_block($blockfile)
{
    return true;
}

/**
 * setup_NewsBlocks()
 * Bloecke die nur fuer das News-Modul vorhanden sind
 *
 * @return
 */
function setup_NewsBlocks()
{
    return array(/* Pfad.Dateiname => Blocktitel */
      //  'block-vkp_News_Lastarticles.php' => 'Last Articles',
        'block-vkp_News_Login.php' => 'Login',
        'block-vkp_News_Options.php' => 'Options',
     // 'block-vkp_News_Poll.php' => 'Survey',
     // 'block-vkp_News_Rating.php' => 'Article Rating',
     // 'block-vkp_News_Related.php' => 'Related Links',
     // 'block-vkp_News_Socialshare.php' => 'Social sharing'
        );
}

?>
