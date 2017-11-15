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
 * $Revision: 1 $
 * $Author: PragmaMx $
 * $ID: 2016-06-09 10:27:55 +0200 (Do, 09. Jun 2016) $
 */

defined('mxMainFileLoaded') or die('access denied');

/* Modulfunktionen */

/**
 * ermittelt ob der aktuelle User die Berechtigung hat, ein Modul zu nutzen
 *
 * @param string $modulname Modulname des Modules für das die Berechtigung geprüft werden soll
 * @staticvar mixed $allmodules Statische Variable in der beim ersten Aufruf der Funktion
 * mxModuleAllowed()
 * die Berechtigungen gespeichert werden
 * @return bool Liefert das Ergebnis (true/false)
 */
function mxModuleAllowed($modulename)
{
    global $prefix;
    static $allmodules = array(); // statisches Array
    if ($modulename == 'admin' && MX_IS_ADMIN) {
        return true;
    }
    if ($modulename == mxGetMainModuleName()) {
        return true;
    }
    if (!$allmodules) { // wenn statisches Array noch nicht initialisiert
        if (mxGetAdminPref('radminsuper')) {
            $qry = "SELECT m.title
                 FROM `{$prefix}_modules` AS m
                 ORDER BY m.title ASC";
        } else {
            $query_view[] = "(title = '" . mxGetMainModuleName() . "')"; // Home-Modul
            $query_view[] = "(view = 0 AND active=1)"; // Alle Besucher
            if (MX_IS_USER) {
                $userinfo = pmxUserStored::current_userdata(); // gesamte Userdaten in Array lesen
                $query_view[] = "(view = 1 AND g.group_id=" . intval($userinfo['user_ingroup']) . " AND active=1)"; // Nur angemeldete Benutzer
            } else {
                $query_view[] = "(view = 3 AND active=1)"; // Anonyme Besucher
            }
            if (MX_IS_ADMIN) {
                $query_view[] = "(view = 2)"; // Nur Administratoren
            }

            $qry = "SELECT m.title
                 FROM `{$prefix}_modules` AS m LEFT JOIN {$prefix}_groups_modules AS g
                 ON m.mid = g.module_id
                 WHERE (" . implode(" OR ", $query_view) . ")
                 ORDER BY m.title ASC";
        }

        $result = sql_system_query($qry); // sql ausfuehren

        if ($result) { // wenn erfolgreich
            // Schleife, alle Modulnamen in array lesen
            while (list($title) = sql_fetch_row($result)) {
                // jetzt endlich das statische Array mit den Moduldaten füllen
                $allmodules[$title] = 1;
            }
        }
    }

    /* Modulname im Array und index.php des Moduls vorhanden */
    if (isset($allmodules[$modulename]) && file_exists(PMX_MODULES_DIR . DS . $modulename . DS . 'index.php')) {
        return true;
    }

    /* wenn nicht, Modulname aus Array entfernen  */
    unset($allmodules[$modulename]);
    return false;
}

/**
 * ermittelt ob ein Modul aktiviert ist
 *
 * @param string $modulname : Name des Modules das geprüft werden soll
 * @return bool Liefert das Ergebnis (true/false)
 */
function mxModuleActive($modulename)
{
    static $allmodules = array();
    // wenn statisches Array noch nicht initialisiert
    if (!$allmodules) {
        global $prefix;
        $qry = "SELECT title FROM `{$prefix}_modules` WHERE active=1";

        $result = sql_system_query($qry);
        while (list($title) = sql_fetch_row($result)) {
            $allmodules[] = $title;
        }
    }

    /* Modulname im Array und index.php des Moduls vorhanden */
    return in_array($modulename, $allmodules);
}

/**
 * aktiviert oder deaktiviert ein Modul
 *
 * @param string $modulname : Name des Modules das ge#ndert werden soll
 * @return bool Liefert das Ergebnis (true/false)
 */
function mxSetModuleActive($modulename, $active = 1, $view=2)
{
    global $prefix;
	$active = intval($active);
	$active = ($active!=0)?1:0;

	/* views auswerten */
	/* 0=alle, 1 = user, 2 = Admins */
	$view = intval($view);
	$view = ($view !=0)?(($view !=1)?2:1):0;	//damit werden auch negative angaben rausgefiltert

    $result = sql_query("SELECT `mid` FROM `{$prefix}_modules` WHERE `title`='" . mxAddSlashesForSQL($modulename) . "'");
    list($mid) = sql_fetch_row($result);
    if ($mid) {
        return sql_query("UPDATE `{$prefix}_modules` SET
          `active`=" . intval($active) . ",
          `view` = ".$view . "
          WHERE `title`='" . mxAddSlashesForSQL($modulename) . "'");
    } else {
        return sql_query("INSERT INTO `{$prefix}_modules` SET
          `title`='" . mxAddSlashesForSQL($modulename) . "',
          `custom_title`='" . mxAddSlashesForSQL(ucwords(str_replace(array('_', '-'), ' ', $modulename))) . "',
          `active`=" . intval($active) . ",
          `view` = ".$view);
    }
}

/**
 * Ermitteln des Modulnamens für die Startseite
 * wenn keines angegeben wurde, News als Startseite eintragen
 *
 * @static string $main Statische Variable in der der name des Startseitenmodules gespeichert
 * wird
 * @return bool Liefert den Namen des Startseitenmodules zurück
 */
function mxGetMainModuleName()
{
    global $prefix;
    static $main;
    if (!$main) {
        $result = sql_system_query("select main_module from {$prefix}_main WHERE main_module <> ''");
        list($main) = sql_fetch_row($result);
        sql_free_result($result);
        $main = (empty($main)) ? 'blank_Home' : $main;
        /* falls das Modul nicht existiert, das erste verfügbare verwenden */
        if (!file_exists(PMX_MODULES_DIR . DS . $main . DS . 'index.php')) {
            $main = '';
            foreach ((array)glob(PMX_MODULES_DIR . DS . '*' . DS . 'index.php', GLOB_NOSORT) as $modulename) {
                $modulename = basename(dirname($modulename));
                if ($modulename && strpos($modulename, '.') === false) {
                    $main = $modulename;
                    break;
                }
            }
            if (!empty($main)) {
                sql_system_query("DELETE FROM {$prefix}_main");
                sql_system_query("INSERT INTO {$prefix}_main (main_module) VALUES ('" . mxAddSlashesForSQL($main) . "')");
            }
        }
    }
    return $main;
}

/**
 *   mxGetModuleInfo
 *  TODO: noch nicht für Admin-Module möglich
 *  @param $module_name - Modulename
 *  @return
 *
 */function mxGetModuleInfo ($module_name=false)
{
	$modul=($module_name)?$module_name:MX_MODULE;

}
?>
