<?php
/**
 * mxBoard, pragmaMx Module
 * © 2006-2010 by pragmaMx-Team, http://www.pragmaMx.org
 *
 * $Author: PragmaMx $
 * $Revision: 6 $
 * $Date: 2015-07-08 09:07:06 +0200 (mer. 08 juil. 2015) $
 *
 * based on eBoard v1.1, rewrite and modified by
 * vkpMx-Developer-Team (http://www.maax-design.de)
 * Original source-code made by the XMB-team
 * (XMB-Forum, http://www.xmbforum.com),
 * 'title' => modified for nukestyle-systems
 * by Trollix (XForum, http://www.trollix.com).
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 */

$hook = function($module_name, $options, &$menu)
{
    /* $options enthält die kompletten Daten des aktuellen Admins */
    extract($options, EXTR_SKIP);

    $menu[] = array(/* Menüpunkt */
        'case' => ($radminforum),
        'module' => $module_name,
        'url' => 'modules.php?name=' . $module_name . '&amp;file=cp',
        'title' => _BOARD,
        'description' => '',
        'image' => "modules/$module_name/images/agt_forum.png",
        'panel' => MX_ADMINPANEL_ADDON,
        );
} ;

?>