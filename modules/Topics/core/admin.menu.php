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

$hook = function($module_name, $options, &$menu)
{
    /* $options enthält die kompletten Daten des aktuellen Admins */
    extract($options, EXTR_SKIP);

    $menu[] = array(/* Menüpunkt */
        'case' => ($radmintopic),
        'module' => $module_name,
        'url' => adminUrl($module_name),
        'title' => _TOPICSMANAGER,
        'description' => '',
        'image' => "modules/$module_name/images/topics.png",
        'panel' => MX_ADMINPANEL_CONTENT,
        );
} ;

?>