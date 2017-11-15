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
 * $Revision: 277 $
 * $Author: PragmaMx $
 * $Date: 2016-12-05 14:19:59 +0100 (Mo, 05. Dez 2016) $
 */

define('mxAdminFileLoaded', true);

defined('MX_TIME') OR define('MX_TIME', microtime(true));

/* einen Modulnamen verpassen */
define('MX_MODULE', 'admin');

/* nur zum initialisieren der Variablen, wird durch das Modul ueberschrieben */
$GLOBALS['index'] = 0;

/**
 * PHP_SELF macht auf manchen Servern Probleme bei mod_rewrite,
 * es wird die umgeschriebene URL verwendet anstatt modules.php
 * deshalb hier umschreiben
 */
$_SERVER['PHP_SELF'] = preg_replace('#^[\\\\/]+#', '/', dirname($_SERVER['PHP_SELF']) . '/' . basename(__file__));

define('PMX_SYSADMIN_NAME', 'God');

/* pragmaMx Hauptdatei includen */
require_once('mainfile.php');

require_once(PMX_SYSTEM_DIR . DS . 'mx_adminfunctions.php');

/* Sprachdatei auswaehlen */
mxGetLangfile('admin');
pmxTranslate::init();

$pagetitle = _ADMINMENU;

switch (true) {
    case empty($_REQUEST['op']) && empty($_REQUEST['name']):
        $op = 'main';
        break;
    case empty($_REQUEST['op']) && !empty($_REQUEST['name']):
        $op = $_REQUEST['name'];
        $_REQUEST['op'] = $op;
        break;
    default:
        $op = $_REQUEST['op'];
        break;
}

/* $op Parameter cleanen */
settype($op, 'string');
$op = preg_replace('#[^a-zA-Z0-9/.-_]#', '_', $op);
/* und als Konstante speichern */
define('PMX_ADMIN_OP', $op);
$_REQUEST['op'] = $op; // für alte Module

switch (true) {
    case !MX_IS_ADMIN && !pmx_admin_exist_god():
        /* einen festen Modulnamen verpassen */
        define('PMX_MODULE', 'createfirst');
        return include_once(PMX_ADMINMODULES_DIR . DS .PMX_MODULE . DS . 'index.php');
    case !MX_IS_ADMIN:
        /* einen festen Modulnamen verpassen */
        define('PMX_MODULE', 'login');
        return include_once(PMX_ADMINMODULES_DIR . DS .PMX_MODULE . DS. 'index.php');
    default:
        mxSessionDelVar('abad');
        extract(mxGetAdminSession());
}

/* $op Parameter aufsplitten, kann mit Punkt oder Slash getrennt sein */
list($module_name) = preg_split('#[./]#', $op);

if ($module_name) {
    $casefiles = array(/* mögliche Dateipfade */
        /* der erste Teil des Splits kann ein Ordner im Adminmodulordner sein */
        PMX_ADMINMODULES_DIR . DS . $module_name . '/index.php',
        /* der erste Teil des Splits kann eine Admindatei im Adminmodulordner sein */
        PMX_ADMINMODULES_DIR . DS . $module_name . '.php',
        /* der erste Teil des Splits kann auch ein Modulname sein */
        PMX_MODULES_DIR . DS . $module_name . '/admin/admin.php',
        );

    foreach ($casefiles as $filename) {
        /* Datei gefunden */
        if (is_file($filename)) {
            /* einen festen Modulnamen verpassen */
            define('PMX_MODULE', $module_name);
            /* Datei einbinden und raus hier */
            return include_once($filename);
        }
    }
}

/* alte Methode, case-Files ermitteln */
$casefiles = pmx_admin_casefiles();

/* Ausgabepuffer aktivieren um zu pruefen ob die $op-Option vorhanden ist */
ob_start();

/* Schleife durch die Dateien, $module_name ist der zugehörige Modulname */
foreach ($casefiles as $module_name => $filename) {
    if (!$filename) {
        continue;
    }

    /* einen festen Modulnamen verpassen */
    define('PMX_MODULE', $module_name);

    /* Case Datei includen, hier wird entweder eine Ausgabe oder ein Redirect produziert */
    include_once($filename);

    /* manche Module verändern die Fehlerbehandlung */
    pmxDebug::restore();

    /* falls jetzt die header und footer.php geladen wurden, kann der Rest der */
    /* Schleife abgebrochen werden, weil die erwartende Ausgabe stattgefunden hat */
    if (defined('PMX_FOOTER') && defined('PMX_HEADER')) {
        break;
    }
}

if (ob_get_length()) {
    return ob_end_flush();
}

/* Falls Script hier noch nicht beendet und Ausgabepuffer leer, */
/* stimmt was nicht, also eine Fehlermeldung zu generieren */
include('header.php');
GraphicAdmin();
echo '<div class="warning">' . sprintf(_OPNOTAVAILABLE, htmlentities($op)) . '</div>';
include ('footer.php');

?>