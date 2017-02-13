<?php
/**
 * mxBoard, pragmaMx Module
 * Copyright by pragmaMx Developer Team - http://www.pragmamx.org
 *
 * $Author: PragmaMx $
 * $Revision: 171 $
 * $Date: 2016-06-29 13:59:03 +0200 (mer. 29 juin 2016) $
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
defined('MXB_INIT') or die('Not in mxBoard...');

define('MXB_VERSION', '2.3.');

define('MXB_MODNAME', basename(dirname(dirname(__FILE__))));
define('MXB_SETTINGSFILE', dirname(dirname(__FILE__)) . DS . 'settings.php');
define('MXB_ANONYMOUS', ((empty($GLOBALS['anonymous'])) ? 'Anonymous' : $GLOBALS['anonymous']));

/* Pfade */
define('MXB_ROOTMOD', 'modules/' . MXB_MODNAME . '/');
define('MXB_BASEMODIMG', 'modules/' . MXB_MODNAME . '/images');
define('MXB_BASEMODJS', 'modules/' . MXB_MODNAME . '/js');
define('MXB_BASEMODTEMPLATE', 'modules/' . MXB_MODNAME . '/templates');
define('MXB_BASEMODTHEME', 'modules/' . MXB_MODNAME . '/themes');
define('MXB_BASEMODINCLUDE', dirname(__FILE__) . DS);

/* Pfade und Dateiaufrufe */
define('MXB_URLLOGIN', 'modules.php?name=Your_Account');
define('MXB_URLREGISTER', 'modules.php?name=User_Registration');

define('MXB_BASEMOD', 'modules.php?name=' . MXB_MODNAME . '&amp;file=');
define('MXB_BM_INDEX0', 'modules.php?name=' . MXB_MODNAME);
define('MXB_BM_INDEX1', 'modules.php?name=' . MXB_MODNAME . '&amp;');
define('MXB_BM_VIEW1', MXB_BASEMOD . 'view&amp;');
define('MXB_BM_VIEW0', MXB_BASEMOD . 'view');
define('MXB_BM_MEMBER1', MXB_BASEMOD . 'member&amp;');
define('MXB_BM_MEMBER0', MXB_BASEMOD . 'member');
define('MXB_BM_MISC1', MXB_BASEMOD . 'misc&amp;');
define('MXB_BM_MISC0', MXB_BASEMOD . 'misc');
define('MXB_BM_SEARCH1', MXB_BASEMOD . 'search&amp;');
define('MXB_BM_SEARCH0', MXB_BASEMOD . 'search');
define('MXB_BM_STATS1', MXB_BASEMOD . 'stats&amp;');
define('MXB_BM_STATS0', MXB_BASEMOD . 'stats');
define('MXB_BM_MESSOTD1', MXB_BASEMOD . 'messslv&amp;view=4&amp;');
define('MXB_BM_MESSOTD0', MXB_BASEMOD . 'messslv&amp;view=4');
define('MXB_BM_MESSLV1', MXB_BASEMOD . 'messslv&amp;');
define('MXB_BM_MESSLV0', MXB_BASEMOD . 'messslv');
define('MXB_BM_MEMBERSLIST1', MXB_BASEMOD . 'memberslist&amp;');
define('MXB_BM_MEMBERSLIST0', MXB_BASEMOD . 'memberslist');
define('MXB_BM_VIEWTHREAD1', MXB_BASEMOD . 'viewthread&amp;');
define('MXB_BM_VIEWTHREAD0', MXB_BASEMOD . 'viewthread');
define('MXB_BM_FORUMDISPLAY1', MXB_BASEMOD . 'forumdisplay&amp;');
define('MXB_BM_FORUMDISPLAY0', MXB_BASEMOD . 'forumdisplay');
define('MXB_BM_TOPICADMIN1', MXB_BASEMOD . 'topicadmin&amp;');
define('MXB_BM_TOPICADMIN0', MXB_BASEMOD . 'topicadmin');
define('MXB_BM_PRINT1', MXB_BASEMOD . 'print&amp;');
define('MXB_BM_PRINT0', MXB_BASEMOD . 'print');
define('MXB_BM_CP1', MXB_BASEMOD . 'cp&amp;');
define('MXB_BM_CP0', MXB_BASEMOD . 'cp');
define('MXB_BM_CP21', MXB_BASEMOD . 'cp2&amp;');
define('MXB_BM_CP20', MXB_BASEMOD . 'cp2');
define('MXB_BM_CP31', MXB_BASEMOD . 'cp3&amp;');
define('MXB_BM_CP30', MXB_BASEMOD . 'cp3');
define('MXB_BM_SETTINGS0', MXB_BASEMOD . 'cp.settings');
define('MXB_BM_SETTINGS1', MXB_BASEMOD . 'cp.settings&amp;');
define('MXB_BM_POSTREPLY1', MXB_BASEMOD . 'post.reply&amp;');
define('MXB_BM_POSTREPLY0', MXB_BASEMOD . 'post.reply');
define('MXB_BM_POSTNEWTOPIC1', MXB_BASEMOD . 'post.newtopic&amp;');
define('MXB_BM_POSTNEWTOPIC0', MXB_BASEMOD . 'post.newtopic');
define('MXB_BM_POSTEDIT1', MXB_BASEMOD . 'post.edit&amp;');
define('MXB_BM_POSTEDIT0', MXB_BASEMOD . 'post.edit');

?>