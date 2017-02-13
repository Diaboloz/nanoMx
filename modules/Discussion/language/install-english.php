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

define('_TITRE', 'mxBoard forums modul for pragmaMx 1.x<br/> Installation');
define('_INSTFAILED', 'Failed');
define('_FILE_NOT_WRITEN', 'Attention, an important file can not be overwritten during the installation.<br/>Write permissions required for: ');
define('_MANUAL_RIGHTS', 'You must give this rights manually before going on.');
define('_INSTALL_PARAM', 'Your Installation Parameters:');
define('_TXT_XF_PREFIX', 'mxBoard prefix');
define('_TXT_XF_PREFIX_EXPL', 'This is your mxBoard prefix for the database tables. If you upgrade, here are the values ​​of the previous version. Please do not change if you are not sure.');
define('_TXT_XMB_LANG', 'Default language for the Forum');
define('_TXT_XMB_LANG_EXPL', 'It is the default language value affected to a new user connected to your pragmaMx. Nota: it could be changed later');
define('_TXT_XMB_THEME', 'Default theme of your forum');
define('_TXT_XMB_THEME_EXPL', 'It is the default theme value affected to a new user connected to your pragmaMx. Nota: it could be changed later');
define('_TEXTDEFAULT', 'Default');
define('_NEXT2', 'Next');
define('_ERRPREFIX', 'The prefix may only contain small letters, numbers, and the underscore (_), and must include with a lowercase letter.');
define('_ERRDEFAULT', 'There has been an undefined error.');
define('_SETUPHAPPY1', 'Congratulations');
define('_SETUPHAPPY2', 'Your system from now on is completely installed, with the next click you will be automatically redirected towards the panel of administration.');
define('_SETUPHAPPY3', 'Here you should first examine the basic adjustments and store them again.');
define('_GET_SQLHINTS', 'Below, the list of all the requests SQL which were imported during the process of conversion/integration');
define('_DATABASEISCURRENT', 'The structure of the database was already present, modifications were useless.');
define('_DB_UPDATEREADY', 'Conversion/integration of the tables is finished.');

?>