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

defined('MX_TIME') OR define('MX_TIME', microtime(true));

/**
 * jop, die modules.php ist geladen ;)
 */
define('mxModFileLoaded', '1');

/**
 * nur zum initialisieren der Variablen, wird durch das Modul überschrieben
 */
$index = 0;
$plugins = 0;

/**
 * PHP_SELF macht auf manchen Servern Probleme bei mod_rewrite,
 * es wird die umgeschriebene URL verwendet anstatt modules.php
 * deshalb hier umschreiben
 */
$_SERVER['PHP_SELF'] = preg_replace('#^[\\\\/]+#', '/', dirname($_SERVER['PHP_SELF']) . '/' . basename(__file__));

/**
 * pragmaMx Hauptdatei includen
 */
require_once('mainfile.php');

if (empty($_REQUEST['name'])) {
    return mxErrorScreen('Sorry, you can\'t access this file directly, parameter &quot;name&quot; is required...', '');
}

$name = (string)$_REQUEST['name'];
$op = (isset($_REQUEST['op'])) ? (string)$_REQUEST['op'] : '';
$file = (isset($_REQUEST['file'])) ? trim((string)$_REQUEST['file']) : 'index';

/**
 * jpCache laden, nur bei Anonymen Besuchern
 */
switch (true) {
    case !$mxJpCacheUse:
    case MX_IS_ADMIN:
    case MX_IS_USER:
    case isset($_REQUEST['noJpC']):
    case $_SERVER['REQUEST_METHOD'] === 'POST';
    case $name == 'Your_Account':
    case $name == 'User_Registration':
        break;
    default:
        include_once(PMX_SYSTEM_DIR . DS . 'jpcache' . DS . 'mx_jpcache.php');
}

/**
 * pruefen von ungueltigen request-Variablen
 */
if (preg_match('#(:)|(\.\.)|(\/)|(\\\)#', $file . $op)) {
    $file = 'index';
    $op = '';
}

/**
 * Ausgabe der Informationen des pragmaMx-Developer-Team
 * Das Entfernen der Zeilen verhindert nicht die Ausgabe,
 * zerstoert aber das Layout der Seite
 */
if ($name == 'mxcredit') {
    require_once(PMX_SYSTEM_DIR . DS . 'mx_credits.php');
    die(mxcredit());
}

/**
 * falsch uebergebene name's mit 2 Fragezeichen fixen
 */
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (strpos($name, '?') !== false && preg_match('#([^?]*)\?([^?]*)#', $name, $matches)) {
        return mxRedirect('modules.php?name=' . $matches[1] . '&' . $matches[2]);
    }
}

/**
 * Daten des Moduls aus der Datenbank lesen
 */
$result = sql_query("SELECT mid, title as name, active, view, custom_title FROM " . $prefix . "_modules WHERE title='" . mxAddSlashesForSQL($name) . "'");
$mod = sql_fetch_object($result);
if (empty($mod->name) || empty($mod->mid)) {
    return mxErrorScreen(_MODULENOTACTIVE, '');
}

/* Den Dateinamen zum includen ermitteln */
/* $mod->name verwenden wegen Gross/Kleinschreibung */
$modfile = PMX_MODULES_DIR . DS . $mod->name . DS . $file . '.php';
if (!file_exists($modfile)) {
    if (MX_IS_ADMIN) {
        // wenn aufgerufene Datei nicht vorhanden, bei Admin gleich Fehler
        return mxErrorScreen(_MODULEFILENOTFOUND . '<br />(' . $modfile . ')');
    }
    // bei normalen Usern, versuchen die index.php des Moduls anzuzeigen
    $file = 'index';
    $modfile = PMX_MODULES_DIR . DS . $mod->name . DS . 'index.php';
    if (!file_exists($modfile)) {
        return mxErrorScreen(_MODULEFILENOTFOUND);
    }
}

switch (true) {
    case (!$mod->active && !MX_IS_ADMIN):
        return mxErrorScreen(_MODULENOTACTIVE, '');
    case $mod->view == 4 && MX_IS_ADMIN && !mxModuleAllowed($mod->name):
        return mxErrorScreen(_GROUPRESTRICTEDAREA . '<br /><br />' . _MODULESSYSADMINS, _ACCESSDENIED);
    case $mod->view == 4 && !MX_IS_ADMIN && !mxModuleAllowed($mod->name):
        return mxErrorScreen(_RESTRICTEDAREA, _ACCESSDENIED);
    case $mod->view == 2 && !MX_IS_ADMIN:
        return mxErrorScreen(_RESTRICTEDAREA . '<br /><br />' . _MODULESADMINS, _ACCESSDENIED);
    case $mod->view == 1 && !MX_IS_ADMIN && !MX_IS_USER:
        return mxErrorScreen(_RESTRICTEDAREA . '<br /><br />' . _MODULEUSERS, _ACCESSDENIED);
    case $mod->view == 1 && !MX_IS_ADMIN && !mxModuleAllowed($mod->name):
        return mxErrorScreen(_GROUPRESTRICTEDAREA, _ACCESSDENIED);
    default:
        /* einen festen Modulnamen verpassen */
        define('MX_MODULE', $mod->name); // Kompatibilität < 2.0
        define('MX_MODULE_FILE', $file);
        define('PMX_MODULE', $mod->name);
        /* Seitentitel vorbelegen */
        $pagetitle = $mod->custom_title;
        /* unnoetiges entfernen */
        unset($result, $qry, $mod);
        /* Moduldatei includen */
        include_once($modfile);
}

?>