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
 * $Revision: 259 $
 * $Author: PragmaMx $
 * $Date: 2016-11-27 18:20:19 +0100 (So, 27. Nov 2016) $
 */

if (!defined('MX_TIME')) {
    define('MX_TIME', microtime(true));
}

/**
 * wenn $name uebergeben wurde, die modules.php verwenden
 */
if (isset($_GET['name']) || isset($_POST['name'])) {
    include('modules.php');
    return;
}

/**
 * jop, die modules.php ist geladen ;)
 */
define('mxModFileLoaded', '2');

/**
 * und noch die Startseite kennzeichnen...
 */
//$home = 1; // nur noch nuke-Kompatibilitaet
define('MX_HOME_FILE', true);

/**
 * nur zum initialisieren der Variablen, wird durch das Modul überschrieben
 */
$index = 0;

/**
 * PHP_SELF macht auf manchen Servern Probleme bei mod_rewrite,
 * es wird die umgeschriebene URL verwendet anstatt modules.php
 * deshalb hier umschreiben, aber hier modules.php verwenden, wegen nuke-Kompatibilität
 */
$_SERVER['PHP_SELF'] = preg_replace('#^[\\\\/]+#', '/', dirname($_SERVER['PHP_SELF']) . '/modules.php');

/**
 * pragmaMx Hauptdatei includen
 */
require_once('mainfile.php');

/**
 * jpCache laden, nur bei Anonymen Besuchern
 */
switch (true) {
	case PMX_CACHE_INAKTIVE:
    case !$mxJpCacheUse:
    case MX_IS_ADMIN:
    case MX_IS_USER:
    case isset($_REQUEST['noJpC']):
    case $_SERVER['REQUEST_METHOD'] === 'POST';
        break;
    default:
        include_once(PMX_SYSTEM_DIR . DS . 'jpcache' . DS . 'mx_jpcache.php');
}

/**
 * einen Modulnamen verpassen
 */
$name = mxGetMainModuleName();
define('MX_MODULE', $name);
define('PMX_MODULE', $name);

$modfile = 'modules/' . $name . '/index.php';
$op = 'modload';
$file = 'index';

if (!@file_exists($modfile)) {
    $index = 1;
    if (MX_IS_ADMIN) {
        return mxErrorScreen('<h2>' . _HOMEPROBLEM . '</h2><p>[&nbsp;<a href="' . adminUrl('modules') . '">' . _ADDAHOME . '</a>&nbsp;]</p>');
    }
    return mxErrorScreen(_HOMEPROBLEMUSER);
}

/**
 * Moduldatei includen
 */
include_once($modfile);

?>