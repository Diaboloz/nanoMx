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

$hook = function($module_name, $dummy, &$items)
{
    /* $dummy wird hier nicht übergeben, ist also immer leer */
    /* $items ergänzt die bestehende Liste */

    global $prefix, $user_prefix;

    $result = sql_num_rows(sql_query("select * from {$prefix}_topics "));

    if ($result) {
        $items[] = array(/* Atttribute */
            'module' => $module_name,
            'url' => "modules.php?name=$module_name",
            'icon' => "modules/$module_name/images/topics.png",
            'caption' => _STAT_SACTIVETOPICS,
            'value' => $result,
            );
    }
}

?>