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

            $result = sql_query("SELECT DISTINCT c.catid, c.title, MAX( UNIX_TIMESTAMP(s.time) ) AS datum
                FROM  {$prefix}_stories_cat  AS c
                LEFT JOIN {$prefix}_stories AS s ON ( c.catid = s.catid )
                WHERE s.catid IS NOT NULL AND  time  <= now()
                GROUP BY c.catid
                LIMIT " . $limit);
            if ($result) {
                while ($row = sql_fetch_object($result)) {
                    $text = $row->title;
                    $entries[] = array(/* Moduldaten */
                        'loc' => "modules.php?name=" . $module_name . "&file=categories&catid=" . $row->catid,
                        'lastmod' => $row->datum,
                        'text' => $text,
                        );
                }
            }

            $result = sql_query("SELECT sid, title, hometext, bodytext, UNIX_TIMESTAMP(time)AS datum FROM {$prefix}_stories WHERE  time  <= now() ORDER BY  `time`  DESC, sid DESC limit " . $limit);
            if ($result) {
                while ($row = sql_fetch_object($result)) {
                    $text = $row->title . ' ' . $row->hometext . ' ' . $row->bodytext;
                    $entries[] = array(/* Moduldaten */
                        'loc' => "modules.php?name=" . $module_name . "&file=article&sid=" . $row->sid,
                        'lastmod' => $row->datum,
                        'text' => $text,
                        );
                }
            }

            $result = sql_query("SELECT DISTINCT t.topicid, t.topictext, MAX( UNIX_TIMESTAMP(s.time) ) AS datum
                FROM  {$prefix}_topics  AS t
                LEFT JOIN {$prefix}_stories AS s ON ( t.topicid = s.topic )
                WHERE s.topic IS NOT NULL AND  `time`  <= now()
                GROUP BY t.topicid
                LIMIT " . $limit);
            if ($result) {
                while ($row = sql_fetch_object($result)) {
                    $text = $row->topictext;
                    $entries[] = array(/* Moduldaten */
                        'loc' => "modules.php?name=" . $module_name . "&topic=" . $row->topicid,
                        'lastmod' => $row->datum,
                        'text' => $text,
                        );
                }
            }
    }
}

?>