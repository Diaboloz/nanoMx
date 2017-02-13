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
 * $Revision: 243 $
 * $Author: PragmaMx $
 * $Date: 2016-09-30 09:39:28 +0200 (Fr, 30. Sep 2016) $
 */
 
/* Nur Parsefehler melden */
error_reporting(E_PARSE);

/* nur für entwicklung */
define ("PMX_DEVELOPMENT",FALSE);

/**
 * Ausgabepuffer auf jeden Fall starten, wird am Ende dieser Datei
 * wieder gelöscht und evtl. der ob_gzhandler gestartet
 */
ob_start();

define('PMX_VERSION', '2.3.0');
/* zu alte php-Version */
(version_compare(PHP_VERSION, '5.4.0', '>=')) or die('Sorry, PHP-Version >= 5.4.0 is required for pragmaMx '.PMX_VERSION.'.');

/* Direktaufruf verhindern */
(stripos($_SERVER['PHP_SELF'], basename(__FILE__)) === false) or die('access denied');

/* Versionsinformation */
preg_match('#([^a-z]{1,}) ([0-9\]{1,2})\s([0-9]{4})[-/]([0-9]{1,2})[-/]([0-9]{1,2})#', '$Id: mainfile.php 243 2016-09-30 07:39:28Z PragmaMx $', $key);
define('PMX_VERSION_NUM', PMX_VERSION . ".".substr($key[1],1,3));
define('PMX_VERSION_DATE', "$key[2]-$key[3]-$key[4]");

/* Versionsinformation Ende */

$keysToSkip = array('GLOBALS', '_SERVER', '_GET', '_POST', '_FILES', '_COOKIE', '_SESSION', '_REQUEST', '_ENV', 'PHP_SELF', 'keysToSkip', 'php_errormsg', 'HTTP_RAW_POST_DATA', 'http_response_header', 'argc', 'argv');
foreach ($_REQUEST as $key => $value) {
    /* pruefen ob gueltiger Schluessel, ansonsten komplett entfernen */
    if (preg_match('#[^a-zA-Z0-9-_\x7f-\xff]#', $key)) {
        unset($_REQUEST[$key], $_COOKIE[$key], $_GET[$key], $_POST[$key]);
        continue;
    }
    /* killt Variablen, die bei register_globals=ON uebergeben werden koennten */
    if (in_array($key, $keysToSkip)) {
        die('unaccepted requestkey: ' . $key);
    }
    unset($$key);
}
unset($key, $value, $keysToSkip);

/* falls die Datei bereits includet wurde, abbrechen */
if (defined('PMX')) {
    return;
}

/* ich bin da... */
define('PMX', true);
define('mxMainFileLoaded', true);

/* Benchmarkanzeige initialisieren */
defined('MX_TIME') OR define('MX_TIME', microtime(true));

defined('ENT_HTML5') OR define('ENT_HTML5', 16 | 32);

/* Kurzform dieser System-Konstanten erstellen */
define('DS', DIRECTORY_SEPARATOR);

/* der wichtigste Pfad: zum mx-Root, ohne Slash am Ende */
define('PMX_REAL_BASE_DIR', __DIR__);

/* Ordner mit den Systemdateien, weitere Ordner werden in der mx_baseconfig definiert */
define('PMX_SYSTEM_DIR', PMX_REAL_BASE_DIR . DS . 'includes');

/* Ordner mit den Administrationsdateien */
define('PMX_ADMIN_DIR', PMX_REAL_BASE_DIR . DS . 'admin');

/* Ordner mit den Administrations Modulen */
define('PMX_ADMINMODULES_DIR', PMX_ADMIN_DIR . DS . 'modules');

/* Ordner mit dynamischen Inhalten (Logdatein, Cache, etc.) */
define('PMX_DYNADATA_DIR', PMX_REAL_BASE_DIR . DS . 'dynadata');

/* Ordner mit dynamischen Medien (Bilder, Dokumente, etc.) */
define('PMX_MEDIA_DIR', PMX_REAL_BASE_DIR . DS . 'media');

/* Ordner mit den Systemdateien fuer die HTML-Ausgabe (view) */
define('PMX_LAYOUT_DIR', PMX_REAL_BASE_DIR . DS . 'layout');

/* Ordner mit den Modulen */
define('PMX_MODULES_DIR', PMX_REAL_BASE_DIR . DS . 'modules');

/* Ordner mit den System-Bloecken */
define('PMX_BLOCKS_DIR', PMX_REAL_BASE_DIR . DS . 'blocks');

/* Ordner mit den Themes */
define('PMX_THEMES_DIR', PMX_REAL_BASE_DIR . DS . 'themes');

/* Ordner mit den Bildchen */
define('PMX_IMAGE_DIR', PMX_REAL_BASE_DIR . DS . 'images');

/* Ordner mit den Systemsprachen */
define('PMX_LANGUAGE_DIR', PMX_REAL_BASE_DIR . DS . 'language');

/* Ordner mit den Themes */
define('PMX_SETUP_DIR', PMX_REAL_BASE_DIR . DS . 'setup');

/* Ordner mit den Plugins */
define('PMX_PLUGIN_DIR', PMX_REAL_BASE_DIR . DS . 'plugins');

/* Ordner mit Standard Javascripten */
define('PMX_JAVASCRIPT_DIR', PMX_SYSTEM_DIR . DS . 'javascript');

/* die Systemkonfigurationsdatei */
define('PMX_CONFIGFILE' , PMX_REAL_BASE_DIR . DS . 'config.php');


/* sonstige Einstellungen vornehmen */
ini_set('gpc_order', 'GPCS'); // ohne Environment, kann über getenv() abgefragt werden
/* depcreated 
ini_set('magic_quotes_runtime', '0'); 
ini_set('magic_quotes_sybase', '0');
*/

/* alles auf utf8 stellen */
ini_set('default_charset', 'UTF-8');
//ini_set('mbstring.internal_encoding', 'UTF-8');   // sincePHP 5.6 deprecated

/**
 * Alle Fehler ausser E_NOTICE melden,
 * dies ist die Standardeinstellung in php.ini
 */
error_reporting(E_ALL ^ E_NOTICE);

/* System-Funktionen laden  */
require_once(PMX_SYSTEM_DIR . DS . 'mx_system.php');

/* Konfiguration laden */
/* Parsefehler in config.php abfangen und bei Bedarf Setup anbieten. */
if (!@include(PMX_CONFIGFILE)) {

    if (@file_exists('setup') && !is_file(PMX_CONFIGFILE)) {
        /* $msg .= '
		<li>pragmaMx seems not to be installed correctly, or you\'re running pragmaMx for the first time. Click <a href="setup/" rel="nofollow"><b>here</b></a> to run the installer.</li>
		<li>pragmaMx scheint nicht korrekt installiert zu sein oder Sie starten pragmaMx zum erstem Mal. Klicken Sie <a href="setup/" rel="nofollow"><b>hier</b></a>, um den Installer zu starten.</li>
		<li>pragmaMx semble ne pas &ecirc;tre install&eacute; correctement, ou vous ex&eacute;cutez pragmaMx pour la premi&egrave;re fois. Cliquer <a href="setup/" rel="nofollow"><b>ici</b></a> pour commencer l\'installation.</li>
		<li>pragmaMx düzgün kurulmam&#305;&#351; veya ilk defa pragmaMx &#231;al&#305;&#351;t&#305;r&#305;yorsunuz. Kurulumu ba&#351;latmak i&#231;in <a href="setup/" rel="nofollow"><b>buraya</b></a> t&#305;klay&#305;n&#305;z.</li>
		'; */
		$url="http://".$_SERVER['SERVER_NAME']."/".basename(dirname($_SERVER['PHP_SELF'])).'/setup/index.php';
		//$url=str_replace("//","/",$url);
		header('Location: ' .$url);
        die();
		exit;
    } else {
		header('Content-type: text/html; charset=utf-8');
		$msg = '<html><body><img src="http://www.pragmamx.org/images/logo.gif" alt="pragmaMx-Error" /><ul>';        
		$msg .= '
		<li>The config-file is missing or corrupted!</li>
		<li>Die Konfigurationsdatei fehlt oder ist besch&auml;digt!</li>
		<li>Le fichier config.php est absent ou corrompu!</li>
		<li>Ayar dosyas&#305; eksik veya hatal&#305;!</li>
		';
		$msg .= '</ul></body></html>';
		die($msg);
    }
    
    
}
/* Konfiguration in Klasse einlesen  - macht globals überflüssig */
load_class('Base',$mxConf);  


/*  API's einbinden */

require_once(PMX_SYSTEM_DIR . DS . 'mx_api.php');
require_once(PMX_SYSTEM_DIR . DS . 'mx_date.php');
require_once(PMX_SYSTEM_DIR . DS . 'mx_api_2.php');
require_once(PMX_SYSTEM_DIR . DS . 'mx_blockfunctions.php');
require_once(PMX_SYSTEM_DIR . DS . 'mx_file.php');

/* Brücke für UTF-8 relevante String-Funktionen  */
define('UTF8', PMX_SYSTEM_DIR . DS . 'utf8');
include_once(UTF8 . DS . 'utf8.php');


/* Länderspezifische Einstellungen, wird teilweise durch die Einstellung der Sprachdateien überschrieben */
setlocale(LC_ALL, array('en_GB.UTF-8', 'en_GB.UTF8', 'en_GB.ISO-8859-1', 'en_GB', 'en_US', 'en', 'eng', 'english-uk', 'english-us', 'uk', 'us', 'GB', 'GBR', '826', 'CTRY_UNITED_KINGDOM', '840', 'CTRY_UNITED_STATES'));
/* Standardzeitzone, die von allen Zeitfunktionen verwendet wird, einstellen  */
date_default_timezone_set($default_timezone);


/* nur wenn die mod.php aufgerufen wurde ist die Funktion aus der mx_modrewrite.php */
/* hier bereits vorhanden, dann die uebergabeparameter aus der mod.php behandeln. */
if (defined('PMXMODREWRITE')) {
    load_class('Modrewrite', false);
    pmxModrewrite::undo();
}

/* Systemkonstanten definieren, diese Datei kann in gewissem Masse angepasst werden */
require_once(PMX_SYSTEM_DIR . DS . 'mx_baseconfig.php');

/* Fehlerbehandlung und Debugmethoden aktivieren */
load_class('Debug', false);
pmxDebug::init();

/* Datenbankverbindung herstellen */

if (!(pmxBase::get('dbconnect'))) pmxBase::set('dbconnect',0);

require_once(PMX_SYSTEM_DIR . DS . 'mx_database.php');
$dbi = sql_system_connect();

// pmxBase::set('dbi',$dbi);

if (is_null($dbi)) {
    die('Selection from database failed - please check the settings!');
}

/* auf aktuelle MySQL-Version pruefen */
if (version_compare(MX_SQL_VERSION, '5.0.33', '<')) {
    die('Sorry, MySQL-Version >= 5.0.33 is required for pragmaMx. Your MySQL-Version is :' .MX_SQL_VERSION);
}


/* Session starten */
require_once(PMX_SYSTEM_DIR . DS . 'mx_session.php');
if (!mxSessionStart()) {
    die('Session: initialisation failed');
}

/* letzte URL abfragen, diese wird nur in der header.php aktualisiert */
define('PMX_REFERER', mxSessionGetVar('lasturl'));

/* Detection System starten */
if ($vkpSafeSqlinject) {
    require_once(PMX_SYSTEM_DIR . DS . 'mx_detect.php');
    pmxDetect::check_banning();
    pmxDetect::start();
}

/* Userberechtigungen */
pmxUserStored::init();

/* Fehlerbehandlung und Debugmethoden aktivieren */
pmxDebug::start();

load_class('Header', false);

/**
 * restrictor von http://www.bot-trap.de/
 */
if (@file_exists(PMX_REAL_BASE_DIR . DS . 'restrictor' . DS . 'bridge.php')) {
    @include_once(PMX_REAL_BASE_DIR . DS . 'restrictor' . DS . 'bridge.php');
}

/**
 * Superglobals zwischenspeichern, fuer Module, die eine eigene Behandlung
 * der Superglobals vorsehen
 */
$_MX_UNCLEAN_GET = $_GET;
$_MX_UNCLEAN_POST = $_POST;
$_MX_UNCLEAN_COOKIE = $_COOKIE;
$_MX_UNCLEAN_FILES = $_FILES;
$_MX_UNCLEAN_SERVER = $_SERVER;

/**
 * - simuliert magic_quotes_gpc=1
 * - ersetzt bestimmte Sonderzeichen durch ihren HTML-Code
 */
$_GET = mxSecureValue($_GET, true);
$_POST = mxSecureValue($_POST, false);
$_COOKIE = mxSecureValue($_COOKIE, true);
$_SERVER = mxSecureValue($_SERVER, true); // $_GET in query_string etc.

if (count($_FILES) && !empty($_FILES)) {
    foreach ($_FILES as $upfile => $upfile_data) {
        if (is_array($upfile_data['name'])) {
            foreach ($upfile_data['name'] as $key => $upfile_name) {
                $_FILES[$upfile]['name'][$key] = mxSecureValue($upfile_name, true);
            }
        } else {
            $_FILES[$upfile]['name'] = mxSecureValue($upfile_data['name'], true);
        }
    }
    unset($upfile, $upfile_name, $upfile_data);
}

/* Speicher aufräumen */
unset($key);
if (!$mxSiteService) {
    unset($mxSiteServiceText);
}
if (MX_IS_USER) {
    mxSessionDelVar('vkpnewuser');
}
// neue globale Request-Variablen erstellen
$_REQUEST = array_merge($_GET, $_POST); /// ohne Cookie, weil unnoetig
// die jetzt fertig behandelten Request-Variablen in den globalen Scope schreiben
// TODO: der Schwachsinn muss raus !!!
    extract($_REQUEST, EXTR_SKIP);

/* neue globale Server-Variablen erstellen, falls register_globals aus */
if (!mxIniGet('register_globals')) {
    // TODO: der Schwachsinn muss raus !!!
    //extract($_SERVER, EXTR_OVERWRITE);
}

/**
 * ENDE ... simuliert magic_quotes_gpc=1...
 */

/* Sprache einstellen */
load_class('Language', false);
$langinstance = pmxLanguage::instance();

pmxBase::set("currentlang",$langinstance->current);
/* abwärtskompatibilität */
$currentlang = pmxBase::currentlang();
$GLOBALS['language_avalaible'] = mxGetAvailableLanguages();

setlocale(LC_TIME, _SETLOCALE);
setlocale(LC_COLLATE, _SETLOCALE);
setlocale(LC_MONETARY, _SETLOCALE);

/* Seitentitel vorbelegen */
$pagetitle = '';

/* Theme definieren */

switch (true) {
	case array_key_exists('thememobile',$_GET):
		$mobile_device=true;
		mxSessionSetVar("mobiletheme",true);
		break;
	case array_key_exists('themenomobile',$_GET):
		$mobile_device=false;
		mxSessionSetVar("mobiletheme",false);
		break;
	default:
		$mobile_device=pmxGetMobileDevice();//(mxSessionGetVar("mobiletheme",0)==0)?pmxGetMobileDevice():mxSessionGetVar("mobiletheme",false);
		break;
}

define('MX_MOBILE_DEVICE', $mobile_device);
define('MX_THEME', mxGetTheme());
define('MX_THEME_DIR', 'themes/' . MX_THEME);

/* Website Tracking aktualisieren */
if (@$vkpTracking) {
    include_once(PMX_SYSTEM_DIR . DS . 'trackhack.php');
}


/* Referer aktualisieren */
mxReferer();

/* Bannfunktion ausfuehren */
vkpIpBanning();

/**
 * output-handling
 * falls bereits ausgaben erfolgt, diese zwischenspeichern und Puffer beenden
 */
$obtemp = trim(@ob_get_clean());

/* Pufferhandler ermitteln */
$mxoutputhandler = mxGetOutputHandler();

/* Ausgabepuffer starten und evtl. Ausgabe komprimieren */
ob_implicit_flush(0);
if ($mxoutputhandler) {
    ob_start($mxoutputhandler);
}
unset($mxoutputhandler);

/*
 * Ausgabepuffer auf jeden Fall ein 2tes mal starten, dass bei Fehlern, die Ausgabe,
 * auch erst am Ende des scripts in den komprimierten Handler geschrieben werden
 * dies verhindert fruehzeitiges senden von HTTP-Headern (session)
 */
ob_start();

/* evtl. bereits vorhandene zwischengespeicherte Ausgaben jetzt erst ausgeben */
if (!empty($obtemp)) {
    echo $obtemp;
}
unset($obtemp);

?>