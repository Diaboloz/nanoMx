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

define("_MX_MENUS", "Menus");
define("_MX_MENU_ADMIN", "Administrate Menus");
define("_MX_MENU_INPUTREQUIRED_NOTE", "Input for this field is required");
define("_MX_MENU_INPUTREQUIRED", "An entry in the dialog box <i>%s</i> is required");
define("_MX_MENU_PATH", "Path");
define("_MX_MENU_EDIT", "Edit");
define("_MX_MENU_DELETE", "Delete");
define("_MX_MENU_DELETE_AREYOUSURE", "Are you sure that you want to delete Menu '%s' ? [ <a href='%s'>Yes</a> | <a href='" . adminUrl('menu') . "'>No</a> ]");
define("_MX_MENU_DELETED", "Menu '%s' has been deleted.");
define("_MX_MENU", "Menu");
define("_MX_MENU_ITEM", "Menu item");
define("_MX_MENU_OPERATIONS", "Operations");
define("_MX_MENU_SHOWALL", "Show all");
define("_MX_MENU_SHOWALL_NO_MENUS", "No Menus defined");
define("_MX_MENU_ADDMENU", "Add a Menu");
define("_MX_MENU_ADDMENU_INTRO", "Name for the new Menu. After a new block has been created, it needs to be activated.");
define("_MX_MENU_ADDMENU_EDIT", "Edit Menu");
define("_MX_MENU_ADDMENU_NAME_DESCR", "Name of the Menu.");
define("_MX_MENU_ADDMENU_ADDED", "New Menu was created.");
define("_MX_MENU_ADDMENU_UPDATED", "Menu '%s' was updated.");
define("_MX_MENU_ADDMENU_EXISTEDALREADY", "Menu '%s' already exists.");
define("_MX_MENU_ADDMENU_BLOCKEDIT", "<a href=\"%s\">Administration of the Menu block</a>");
define("_MX_MENU_ADDITEM", "Add a Menu item");
define("_MX_MENU_ADDITEM_EDIT", "Edit Menu item");
define("_MX_MENU_ADDITEM_NOTDEF", "No Menu items defined.");
define("_MX_MENU_ADDITEM_NAME_DESCR", "The name of the Menu item.");
define("_MX_MENU_ADDITEM_TITLE_DESCR", "Describes a Menu item, when a mouse pointer hovers over that Menu item.");
define("_MX_MENU_ADDITEM_PATH_DESCR", "The  path to which the Menu item points to. This could be a pragmaMx path or an external address like http://www.pragmamx.org .");
define("_MX_MENU_ADDITEM_ASSOCIATEDMENU", "Parent Menu");
define("_MX_MENU_ADDITEM_WEIGHT", "Weight");
define("_MX_MENU_ADDITEM_ADDED", "Menu item has been added.");
define("_MX_MENU_ADDITEM_DELETE_AREYOUSURE_1", "Are you sure that you want to delete Menu item '%s' ? [ <a href='%s'>Yes</a> | <a href='" . adminUrl('menu') . "'>No</a> ]");
define("_MX_MENU_ADDITEM_DELETE_AREYOUSURE_2", "Are you sure that you want to delete Menu item '%s' ? [ <a href='%s'>Yes</a> | <a href='" . adminUrl('menu') . "'>No</a> ]<br /><b>Note:</b> existing sub-entries are arranged in the above menu level.");
define("_MX_MENU_ADDITEM_DELETED", "Menu item '%s' has been deleted.");
define("_MX_MENU_ADDITEM_UPDATED", "Menu item '%s' has been updated.");
define("_MX_MENU_MODULE_IMPORT", "Import");
define("_MX_MENU_ITEM_EXP_OPEN", "open");
define("_MX_MENU_ITEM_EXP_DESCR", "If this menu item has subitems, they are always open.");
define("_MX_MENU_ITEM_ISDISABLED", "(Disabled)");
define("_MX_MENU_MODULE_ADMIN", "Administrate Modules");
define("_MX_MENU_MODULE_NAME", "Name");
define("_MX_MENU_MODULE_TITLE", "Title");
define("_MX_MENU_MODULE_BLOCK", "Blockfile");
define("_MX_MENU_MODULE_OUTYET", "Module Links");
define("_MX_MENU_ITEM_FORBIDDEN", "%s hidden Modul Links");
define("_MX_MENU_MENUOPTIONS", "Menuoptions");
define("_MX_MENU_SETTINGS_UPDATED", "Settings of mxMenu have been updated.");
define("_MX_MENU_SETTINGS_DYN_EXP", "Enable dynamic open/close for menu items");
define("_MX_MENU_SETTINGS_DYN_EXP_DESCR", "If this option is enabled, users are able to view or hide submenus by clicking the open/close symbols.");
define("_MX_MENU_ENABLED", "Menu '%s' has been enabled.");
define("_MX_MENU_DISABLED", "Menu '%s' has been disabled.");
define("_MX_MENU_MODULLINK", "Module link");
define("_MX_MENU_FURTHERSETTINGS", "and further settings");
define("_MX_MENU_TARGET", "target");
define("_MX_MENU_TARGET2", "target");
define("_MX_MENU_ADDITEM_TARGET_DESCR", "Name of the window in which the link should be opened. Leave blank to open in the same window.");
define("_MX_MENU_POS_BEFORE", "before");
define("_MX_MENU_POS_BEGIN", "to the beginning");
define("_MX_MENU_POS_LAST", "at the end");
define("_MX_MENU_SAVEERROR", "The menu item '%s' could not be saved.");
define("_MX_MENU_NOTACTIVE", "not active");
define("_MX_MENU_BLOCKSADMIN", "Administration block");

?>