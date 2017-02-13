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
define("_MX_MENU_ADMIN", "Administration des menus");
define("_MX_MENU_INPUTREQUIRED_NOTE", "Champs requis");
define("_MX_MENU_INPUTREQUIRED", "Le champs dans la boite de dialogue <i>%s</i> est requis");
define("_MX_MENU_PATH", "Chemin");
define("_MX_MENU_EDIT", "Modifier");
define("_MX_MENU_DELETE", "Supprimer");
define("_MX_MENU_DELETE_AREYOUSURE", "Etes vous sûr de vouloir supprimer le menu '%s' ? [ <a href='%s'>Oui</a> | <a href='" . adminUrl('menu') . "'>Non</a> ]");
define("_MX_MENU_DELETED", "Le menu '%s' a été supprimé.");
define("_MX_MENU", "Menu");
define("_MX_MENU_ITEM", "Items du menu");
define("_MX_MENU_OPERATIONS", "Opérations");
define("_MX_MENU_SHOWALL", "Tout afficher");
define("_MX_MENU_SHOWALL_NO_MENUS", "Aucun menu défini");
define("_MX_MENU_ADDMENU", "Ajouter un menu");
define("_MX_MENU_ADDMENU_INTRO", "Indiquez le nom du nouveau menu. Après avoir créé le nouveau bloc, vous devez l'activer");
define("_MX_MENU_ADDMENU_EDIT", "Modifier le menu");
define("_MX_MENU_ADDMENU_NAME_DESCR", "Nom du menu.");
define("_MX_MENU_ADDMENU_ADDED", "Le nouveau menu a été créé.");
define("_MX_MENU_ADDMENU_UPDATED", "Le menu '%s' a été mis à jour.");
define("_MX_MENU_ADDMENU_EXISTEDALREADY", "Le menu '%s' éxiste déjà.");
define("_MX_MENU_ADDMENU_BLOCKEDIT", "<a href=\"%s\">Administration du bloc menu</a>");
define("_MX_MENU_ADDITEM", "Ajouter un item au menu");
define("_MX_MENU_ADDITEM_EDIT", "Modifier l'item du menu");
define("_MX_MENU_ADDITEM_NOTDEF", "Aucun item défini pour ce menu.");
define("_MX_MENU_ADDITEM_NAME_DESCR", "Nom de l'item pour ce menu.");
define("_MX_MENU_ADDITEM_TITLE_DESCR", "Description de l'item du menu lors du survol dela souris.");
define("_MX_MENU_ADDITEM_PATH_DESCR", "Chemin vers lequel pointe l'item du menu. Celui-ci peut être un chemin interne à pragmaMx ou une adresse externe comme http://www.pragmamx.org .");
define("_MX_MENU_ADDITEM_ASSOCIATEDMENU", "Menu parent");
define("_MX_MENU_ADDITEM_WEIGHT", "Ordre");
define("_MX_MENU_ADDITEM_ADDED", "L'item du menu a été ajouté.");
define("_MX_MENU_ADDITEM_DELETE_AREYOUSURE_1", "Etes vous sûr de vouloir supprimer l'item '%s' du menu ? [ <a href='%s'>Oui</a> | <a href='" . adminUrl('menu') . "'>Non</a> ]");
define("_MX_MENU_ADDITEM_DELETE_AREYOUSURE_2", "Etes vous sûr de vouloir supprimer l'item '%s' du menu ? [ <a href='%s'>Oui</a> | <a href='" . adminUrl('menu') . "'>Non</a> ]<br /><b>Note:</b> existing sub-entries are arranged in the above menu level.");
define("_MX_MENU_ADDITEM_DELETED", "L'item '%s' du menu a été supprimé.");
define("_MX_MENU_ADDITEM_UPDATED", "L'item '%s' du menu a été mis à jour.");
define("_MX_MENU_MODULE_IMPORT", "Importation");
define("_MX_MENU_ITEM_EXP_OPEN", "ouvrir");
define("_MX_MENU_ITEM_EXP_DESCR", "Si les items de ce menu ont des sous-items, ces derniers seront toujours ouverts.");
define("_MX_MENU_ITEM_ISDISABLED", "(Désactivé)");
define("_MX_MENU_MODULE_ADMIN", "Administration des modules");
define("_MX_MENU_MODULE_NAME", "Nom");
define("_MX_MENU_MODULE_TITLE", "Titre");
define("_MX_MENU_MODULE_BLOCK", "Fichier bloc");
define("_MX_MENU_MODULE_OUTYET", "Liens vers les modules");
define("_MX_MENU_ITEM_FORBIDDEN", "%s liens cachés du module");
define("_MX_MENU_MENUOPTIONS", "Options du menu");
define("_MX_MENU_SETTINGS_UPDATED", "Les  réglages de mxMenu ont été mis à jour.");
define("_MX_MENU_SETTINGS_DYN_EXP", "Activer la fonction d'ouverture et fermeture dynamiques pour les items du menu.");
define("_MX_MENU_SETTINGS_DYN_EXP_DESCR", "Si cette option est activée, les utilisateurs peuvent afficher ou cacher les sous-menus en cliquant sur le symbole ouvrir/fermer.");
define("_MX_MENU_ENABLED", "Le menu '%s' a été activé.");
define("_MX_MENU_DISABLED", "Le menu '%s' a été désactivé.");
define("_MX_MENU_MODULLINK", "Module lien");
define("_MX_MENU_FURTHERSETTINGS", "et réglages supplémentaires");
define("_MX_MENU_TARGET", "Fenêtre");
define("_MX_MENU_TARGET2", "Fenêtre cible");
define("_MX_MENU_ADDITEM_TARGET_DESCR", "Nom de la fenêtre dans laquelle le lien doit être ouvert. Laisser vide pour la même fenêtre à ouvrir.");
define("_MX_MENU_POS_BEFORE", "Avant");
define("_MX_MENU_POS_BEGIN", "Au début");
define("_MX_MENU_POS_LAST", "A la fin");
define("_MX_MENU_SAVEERROR", "L'élément du menu '%s' n'a pas pu être sauvé.");
define("_MX_MENU_NOTACTIVE", "Inactif");
define("_MX_MENU_BLOCKSADMIN", "Administration des blocs");

?>