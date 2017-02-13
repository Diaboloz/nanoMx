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

$hook = function($module_name, $options, &$entries)
{
    global $prefix;

    switch (true) {
        case $options['type'] === 'modules':
            return $entries[$module_name] = _NEWS;

        case $options['type'] === 'items':

            extract($options);

            $result = sql_query("
              SELECT sid, title, hometext, bodytext, UNIX_TIMESTAMP(time) AS  date , informant
              FROM {$prefix}_stories
              WHERE (UNIX_TIMESTAMP(time) <= " . time() . ") ORDER BY `time` DESC, sid DESC " . $limit);
            if ($result) {
                while ($item = sql_fetch_assoc($result)) {
                    $item['module'] = $module_name;
                    $item['link'] = 'modules.php?name=' . $module_name . '&file=article&sid=' . $item['sid'];
                    $item['description'] = $item['hometext'] . '<br /><br />' . $item['bodytext'];

                    $entries[] = $item;
                }
            }
    }
} ;

?>