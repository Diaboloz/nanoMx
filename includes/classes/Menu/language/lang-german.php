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

define("_MX_MENUS", "Menüs");
define("_MX_MENU_ADMIN", "Menüs verwalten");
define("_MX_MENU_INPUTREQUIRED_NOTE", "Dieses Feld wird benötigt");
define("_MX_MENU_INPUTREQUIRED", "Das Feld <i>%s</i> wird benötigt");
define("_MX_MENU_PATH", "Pfad");
define("_MX_MENU_EDIT", "Bearbeiten");
define("_MX_MENU_DELETE", "Löschen");
define("_MX_MENU_DELETE_AREYOUSURE", "Sicher, daß Menü '%s' gelöscht werden soll ? [ <a href='%s'>Ja</a> | <a href='" . adminUrl('menu') . "'>Nein</a> ]");
define("_MX_MENU_DELETED", "Das Menü '%s' wurde gelöscht.");
define("_MX_MENU", "Menü");
define("_MX_MENU_ITEM", "Menüpunkt");
define("_MX_MENU_OPERATIONS", "Operationen");
define("_MX_MENU_SHOWALL", "Alle anzeigen");
define("_MX_MENU_SHOWALL_NO_MENUS", "Keine Menüs definiert");
define("_MX_MENU_ADDMENU", "Menü hinzufügen");
define("_MX_MENU_ADDMENU_INTRO", "Name für das neue Menü. Der neu erstellte Block muss anschließend noch aktiviert werden.");
define("_MX_MENU_ADDMENU_EDIT", "Menü bearbeiten");
define("_MX_MENU_ADDMENU_NAME_DESCR", "Der Name des Menüs.");
define("_MX_MENU_ADDMENU_ADDED", "Das Menü '%s' wurde eingerichtet.");
define("_MX_MENU_ADDMENU_UPDATED", "Das Menü '%s' wurde aktualisiert.");
define("_MX_MENU_ADDMENU_EXISTEDALREADY", "Das Menü '%s' existiert bereits.");
define("_MX_MENU_ADDMENU_BLOCKEDIT", "<a href=\"%s\">Menüblock administrieren</a>");
define("_MX_MENU_ADDITEM", "Menüpunkt hinzufügen");
define("_MX_MENU_ADDITEM_EDIT", "Menüpunkt bearbeiten");
define("_MX_MENU_ADDITEM_NOTDEF", "Keine Menüpunkte definiert.");
define("_MX_MENU_ADDITEM_NAME_DESCR", "Der Name des Menüpunkts.");
define("_MX_MENU_ADDITEM_TITLE_DESCR", "Die Beschreibung, die angezeigt wird, wenn man mit der Maus über einen Menüpunkt fährt.");
define("_MX_MENU_ADDITEM_PATH_DESCR", "Der Pfad, auf den dieser Menüpunkt zeigt. Dies kann ein pragmaMx-Pfad oder eine externe Adresse, wie http://www.pragmamx.org, sein.");
define("_MX_MENU_ADDITEM_ASSOCIATEDMENU", "Übergeordnetes Menü");
define("_MX_MENU_ADDITEM_WEIGHT", "Reihenfolge");
define("_MX_MENU_ADDITEM_ADDED", "Der Menüpunkt '%s' wurde hinzugefügt.");
define("_MX_MENU_ADDITEM_DELETE_AREYOUSURE_1", "Sicher, daß Menüpunkt '%s' gelöscht werden soll? [ <a href='%s'>Ja</a> | <a href='" . adminUrl('menu') . "'>Nein</a> ]");
define("_MX_MENU_ADDITEM_DELETE_AREYOUSURE_2", "Sicher, daß Menüpunkt '%s' gelöscht werden soll? [ <a href='%s'>Ja</a> | <a href='" . adminUrl('menu') . "'>Nein</a> ]<br /><b>Achtung</b>: vorhandene Unterpunkte werden in der darüberliegenden Menüebene eingeordnet.");
define("_MX_MENU_ADDITEM_DELETED", "Der Menüpunkt '%s' wurde gelöscht.");
define("_MX_MENU_ADDITEM_UPDATED", "Der Menüpunkt '%s' wurde aktualisiert.");
define("_MX_MENU_MODULE_IMPORT", "Importieren");
define("_MX_MENU_ITEM_EXP_OPEN", "geöffnet");
define("_MX_MENU_ITEM_EXP_DESCR", "Falls dieser Menüpunkt Unterpunkte hat, werden diese immer geöffnet dargestellt.");
define("_MX_MENU_ITEM_ISDISABLED", "(deaktiviert)");
define("_MX_MENU_MODULE_ADMIN", "Module verwalten");
define("_MX_MENU_MODULE_NAME", "Name");
define("_MX_MENU_MODULE_TITLE", "Titel");
define("_MX_MENU_MODULE_BLOCK", "Block-Datei");
define("_MX_MENU_MODULE_OUTYET", "Modullinks");
define("_MX_MENU_ITEM_FORBIDDEN", "%s versteckte Modullinks");
define("_MX_MENU_MENUOPTIONS", "Menüoptionen");
define("_MX_MENU_SETTINGS_UPDATED", "Menüeinstellungen wurden aktualisiert.");
define("_MX_MENU_SETTINGS_DYN_EXP", "Dynamisches Auf-und Zuklappen der Menüpunkte ermöglichen");
define("_MX_MENU_SETTINGS_DYN_EXP_DESCR", "Falls diese Option aktiviert ist hat der User die Möglichkeit durch Anklicken des Ein/Ausklappen-Symbols vorhandene Untermenüpunkte sichtbar zu machen.");
define("_MX_MENU_ENABLED", "Das Menü '%s' wurde aktiviert.");
define("_MX_MENU_DISABLED", "Das Menü '%s' wurde deaktiviert.");
define("_MX_MENU_MODULLINK", "Modullink");
define("_MX_MENU_FURTHERSETTINGS", "und weitere Einstellungen");
define("_MX_MENU_TARGET", "Fenster");
define("_MX_MENU_TARGET2", "Zielfenster");
define("_MX_MENU_ADDITEM_TARGET_DESCR", "Name des Fensters in dem der Link geöffnet werden soll. Leer lassen um im selben Fenster zu öffnen.");
define("_MX_MENU_POS_BEFORE", "vor");
define("_MX_MENU_POS_BEGIN", "an den Anfang");
define("_MX_MENU_POS_LAST", "an das Ende");
define("_MX_MENU_SAVEERROR", "Der Menüpunkt '%s' konnte nicht gespeichert werden.");
define("_MX_MENU_NOTACTIVE", "inaktiv");
define("_MX_MENU_BLOCKSADMIN", "Block Einstellungen");

?>