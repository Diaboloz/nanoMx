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
 *
 * translation by: Wilhelm Moellering, http://www.humanmedia.dk
 */

defined('mxMainFileLoaded') or die('access denied');

define("_MX_MENUS", "Menuer");
define("_MX_MENU_ADMIN", "Administrere Menuer");
define("_MX_MENU_INPUTREQUIRED_NOTE", "Dette felt er nødvendig");
define("_MX_MENU_INPUTREQUIRED", "Feltet <i>%s</i> er nødvendig");
define("_MX_MENU_PATH", "Sti");
define("_MX_MENU_EDIT", "Bearbejd");
define("_MX_MENU_DELETE", "Slet");
define("_MX_MENU_DELETE_AREYOUSURE", "Er du sikker på, at menuen '%s' sal slettes ? [ <a href='%s'>Ja</a> | <a href='" . adminUrl('menu') . "'>Nej</a> ]");
define("_MX_MENU_DELETED", "Menuen '%s' blev slettet.");
define("_MX_MENU", "Menu");
define("_MX_MENU_ITEM", "Menupunkt");
define("_MX_MENU_OPERATIONS", "Operationer");
define("_MX_MENU_SHOWALL", "Vis alle");
define("_MX_MENU_SHOWALL_NO_MENUS", "Ingen menuer defineret");
define("_MX_MENU_ADDMENU", "Tilføj menu");
define("_MX_MENU_ADDMENU_INTRO", "Navn for det nye menu. Det er nødvendigt at aktivere Blokken senere.");
define("_MX_MENU_ADDMENU_EDIT", "Bearbejd menu");
define("_MX_MENU_ADDMENU_NAME_DESCR", "Navn af manuen.");
define("_MX_MENU_ADDMENU_ADDED", "Menuen '%s' blev oprettet.");
define("_MX_MENU_ADDMENU_UPDATED", "Menuen '%s' blev aktualiseret.");
define("_MX_MENU_ADDMENU_EXISTEDALREADY", "Menuen '%s' eksisterer allerede.");
define("_MX_MENU_ADDMENU_BLOCKEDIT", "<a href=\"%s\">Administrer Menublok</a>");
define("_MX_MENU_ADDITEM", "Tilføj Menupunkt");
define("_MX_MENU_ADDITEM_EDIT", "Bearbejd Menupunkt");
define("_MX_MENU_ADDITEM_NOTDEF", "Ingen Menupunkter defineret.");
define("_MX_MENU_ADDITEM_NAME_DESCR", "Navnet af Menupunkt.");
define("_MX_MENU_ADDITEM_TITLE_DESCR", "Beskrivelsen der skal vises, når musen kører over en menupunktet.");
define("_MX_MENU_ADDITEM_PATH_DESCR", "Sti til den menupunktet pejer. Dette kan være en pragmamx-sti eller en ekstern adresse, som http://www.pragmamx.org.");
define("_MX_MENU_ADDITEM_ASSOCIATEDMENU", "Overordnet menu");
define("_MX_MENU_ADDITEM_WEIGHT", "Rækkefølge");
define("_MX_MENU_ADDITEM_ADDED", "Menupunktet blev tilføjet.");
define("_MX_MENU_ADDITEM_DELETE_AREYOUSURE_1", "Er du sikker på, at menupunktet '%s' skal slettes ? [ <a href='%s'>Ja</a> | <a href='" . adminUrl('menu') . "'>Nej</a> ]");
define("_MX_MENU_ADDITEM_DELETE_AREYOUSURE_2", "Er du sikker på, at menupunktet '%s' skal slettes ? [ <a href='%s'>Ja</a> | <a href='" . adminUrl('menu') . "'>Nej</a> ]<br /><b>Achtung</b>: vorhandene Unterpunkte werden in der darüberliegenden Menüebene eingeordnet.");
define("_MX_MENU_ADDITEM_DELETED", "Menupunktet '%s' blev slettet.");
define("_MX_MENU_ADDITEM_UPDATED", "Menupunktet '%s' blev aktualiseret.");
define("_MX_MENU_MODULE_IMPORT", "Importer");
define("_MX_MENU_ITEM_EXP_OPEN", "åbnet");
define("_MX_MENU_ITEM_EXP_DESCR", "Hvis dette menupunkt har underkategorier, bliver disse åbnet vist.");
define("_MX_MENU_ITEM_ISDISABLED", "(deaktiveret)");
define("_MX_MENU_MODULE_ADMIN", "Administrer Moduler");
define("_MX_MENU_MODULE_NAME", "Navn");
define("_MX_MENU_MODULE_TITLE", "Titel");
define("_MX_MENU_MODULE_BLOCK", "Blok-Fil");
define("_MX_MENU_MODULE_OUTYET", "Modullinks");
define("_MX_MENU_ITEM_FORBIDDEN", "%s gemmte Modullinks");
define("_MX_MENU_MENUOPTIONS", "Menuoptioner");
define("_MX_MENU_SETTINGS_UPDATED", "Menuindstillingen blev aktualiseret.");
define("_MX_MENU_SETTINGS_DYN_EXP", "Muliggør dynamisk Rul ud/ind af Menupunkter");
define("_MX_MENU_SETTINGS_DYN_EXP_DESCR", "Hvis denne option er aktiveret har brugeren mulighed for at klikke på Rul ud/ind symbol, for at gør eksisterende undermenupunkter synlig.");
define("_MX_MENU_ENABLED", "Menuen '%s' blev aktiveret.");
define("_MX_MENU_DISABLED", "Menuen '%s' blev deaktiveret.");
define("_MX_MENU_MODULLINK", "Modullink");
define("_MX_MENU_FURTHERSETTINGS", "og andre indstillinger");
define("_MX_MENU_TARGET", "Vinduet");
define("_MX_MENU_TARGET2", "Destinationsvindue");
define("_MX_MENU_ADDITEM_TARGET_DESCR", "Navn for vinduet hvor i hyperlinket skal åbnes. Hvis tomt, skal linket åbnes i samme vindue.");
define("_MX_MENU_POS_BEFORE", "før");
define("_MX_MENU_POS_BEGIN", "til starten");
define("_MX_MENU_POS_LAST", "til slutningen");
define("_MX_MENU_SAVEERROR", "Menupunktet '%s' kunne ikke gemmes.");
define("_MX_MENU_NOTACTIVE", "inaktiv");
define("_MX_MENU_BLOCKSADMIN", "BLOK INDSTILLINGER");

?>