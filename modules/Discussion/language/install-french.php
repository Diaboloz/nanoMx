<?php
/**
 * mxBoard, pragmaMx Module
 * Copyright by pragmaMx Developer Team - http://www.pragmamx.org
 *
 * $Author: PragmaMx $
 * $Revision: 6 $
 * $Date: 2015-07-08 09:07:06 +0200 (mer. 08 juil. 2015) $
 *
 * based on eBoard v1.1, rewrite and modified by
 * vkpMx-Developer-Team (http://www.maax-design.de)
 * Original source-code made by the XMB-team
 * (XMB-Forum, http://www.xmbforum.com), modified for nukestyle-systems
 * by Trollix (XForum, http://www.trollix.com).
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 */

defined('mxMainFileLoaded') or die('access denied');

define('_TITRE', 'Installation du module de forum mxBoard <br/> pour pragmaMx 1.x');
define('_INSTFAILED', 'Echec');
// define('_FILE_NOT_WRITEN', 'Attention, ce fichier suivant situé dans le répertoire \'modules/' . MXB_MODNAME . '/ n\'est pas accessible en écriture.<br/><br/>Tentative de la mise à jour de ces droits: ');
define('_FILE_NOT_WRITEN', 'Attention, un fichier important ne peut pas être écrit lors de l\'installation. <br /> Des persmmissions d\'écriture sont nécessaires pour: ');
define('_MANUAL_RIGHTS', 'Vous devrez mettre ces droits manuellement avant de continuer.');
define('_INSTALL_PARAM', 'Les paramètres de votre installation');
define('_TXT_XF_PREFIX', 'Le préfixe de mxBoard forum');
// define('_TXT_XF_PREFIX_EXPL', 'C\'est la valeur par defaut que l\'on met pour le Forum. Il est conseillé de laisser cette valeur par défaut sauf si vous savez ce que vous faites, par exemple installer plusieurs forums sur le même site->pas encore possible');
define('_TXT_XF_PREFIX_EXPL', 'Ceci est votre préfixe mxBoard pour les tables de la base de données. Si vous mettez à niveau, voici ci-après les valeurs de la version précédente. Veuillez ne rien changer si vous n\'êtes pas sûr.');
define('_TXT_XMB_LANG', 'La langue du forum par défaut');
define('_TXT_XMB_LANG_EXPL', 'Correspond à la langue qui sera affectée par défaut à tous les utilisateurs. Note: ils pourront changer cette valeur ensuite');
define('_TXT_XMB_THEME', 'Le thème du forum par défaut');
define('_TXT_XMB_THEME_EXPL', 'Correspond au thème du forum qui sera appliqué par défaut à tous les utilisateurs lors de leur inscription. Il est fortement conseillé de laisser le défaut à gray pour une première installation. Nota: Cette valeur pourra être modifiée ensuite par l\'utilisateur');
define('_TEXTDEFAULT', 'Défaut');
define('_NEXT2', 'Suivant');
define('_ERRPREFIX', 'Le préfixe ne peut contenir que des lettres minuscules, des chiffres et le trait souligner (_) et il doit commencer par une lettre minuscule.');
define('_ERRDEFAULT', 'Une erreur non définie a eu lieu.');
// define('_PRERR11', 'Les deux préfixes doivent être écrit en minuscule et doivent commencer par une lettre, ils peuvent contenir des nombres, des lettres et le signe souligné (_) mais ceux-ci ne doivent pas avoir une longueur totale de plus de ' . PREFIX_MAXLENGTH . ' caractères.');
define('_SETUPHAPPY1', 'Félicitations,');
define('_SETUPHAPPY2', 'Votre système est désormais complètement installé, au prochain clic vous serez automatiquement redirigé vers le panneau d\'administration.');
define('_SETUPHAPPY3', 'Ainsi, vous pourrez à  nouveau vérifier vos réglages initiaux puis validez par la sauvegarde.');
define('_GET_SQLHINTS', 'Ci-dessous, la liste de toutes les requêtes SQL qui ont été importées durant le processus de conversion/intégration');
define('_DATABASEISCURRENT', 'La structure de la base de données était déjà présente, les modifications étaient inutiles.');
define('_DB_UPDATEREADY', 'La conversion/intégration des tables est terminée.');
// define('_DB_UPDATEFAIL', 'La conversion/intégration des tables n\'a pas pu être effectuée complètement.');

?>
