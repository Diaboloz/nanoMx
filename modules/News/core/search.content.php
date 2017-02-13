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

            $qry = "
                SELECT s.sid, s.title, UNIX_TIMESTAMP(s.time) AS  date , s.hometext, s.bodytext, s.counter AS  views , s.notes, s.topic, t.topictext
                FROM ${prefix}_stories AS s, ${prefix}_topics AS t
                WHERE (s.topic=t.topicid)
                  AND s.time <= now() " . pmx_multilang_query('alanguage', 'AND') . "
                  AND MATCH( s.title, s.hometext, s.bodytext, s.notes)
                    AGAINST('{$search}' IN BOOLEAN MODE)
                ORDER BY s.time DESC, s.sid DESC
                LIMIT 0," . intval($limit);

            $result = sql_query($qry);

            if ($result) {
                while ($entry = sql_fetch_assoc($result)) {
                    $entry['link'] = "modules.php?name=" . $module_name . "&amp;file=article&amp;sid=" . $entry['sid'];
                    $entry['moduletitle'] = _NEWS;
                    $entry['text'] = $entry['hometext'] . ' ' . $entry['bodytext'] . ' ' . $entry['notes'];
                    $entry['module'] = $module_name;
                    unset($entry['sid'], $entry['topic'], $entry['topictext'], $entry['hometext'], $entry['bodytext'], $entry['notes']);
                    $entries[] = $entry;
                }
            }
    }
} ;

?>