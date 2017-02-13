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

$hook = function($module_name, $options, &$hook_result)
{
    /* $options enthält die kompletten Daten des aktuellen Admins */
    global $prefix, $user_prefix;

    if ($options['radminarticle']) {
        list($num) = sql_fetch_row(sql_query("select count(qid) from ${prefix}_queue"));
        if ($num) {
            $hook_result[] = array(/* Eintrag */
                'module' => $module_name,
                'count' => $num,
                'link' => adminUrl($module_name),
                'text' => (($num > 1) ? _AB_SUBMISSIONS : _AB_SUBMISSION));
        }
    }
} ;

?>