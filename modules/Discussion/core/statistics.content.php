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
 * $Revision: 92 $
 * $Author: PragmaMx $
 * $Date: 2015-11-16 11:20:19 +0100 (lun. 16 nov. 2015) $
 */

$hook = function($module_name, $dummy, &$items)
{
    /* $dummy wird hier nicht übergeben, ist also immer leer */
    /* $items ergänzt die bestehende Liste */

    global $prefix, $user_prefix;
	include_once("modules/".$module_name."/settings.php");
    $result = sql_num_rows(sql_query("select fid from {$tablepre}forums"));
    if ($result) {
        $items[] = array(/* Atttribute */
            'module' => $module_name,
            'url' => "modules.php?name=$module_name",
            'icon' => "modules/$module_name/images/agt_forum.png",
            'caption' => _BOARD . " - " . _STAT_FORACT,
            'value' => $result,
            );
    }
    $result = sql_num_rows(sql_query("select tid from {$tablepre}threads"));
    $result += sql_num_rows(sql_query("select pid from {$tablepre}posts"));
    if ($result) {
        $items[] = array(/* Atttribute */
            'module' => $module_name,
            'url' => "modules.php?name=$module_name",
            'icon' => "modules/$module_name/images/agt_forum.png",
            'caption' => _BOARD . " - " . _TEXTPOSTS,
            'value' => $result,
            );
    }
}

?>