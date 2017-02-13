<?php
/**
 * mxBoard, pragmaMx Module
 * Copyright by pragmaMx Developer Team - http://www.pragmamx.org
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * $Revision: 6 $
 * $Author: PragmaMx $
 * $Date: 2015-07-08 09:07:06 +0200 (mer. 08 juil. 2015) $
 */

/*
// TODO:
// - Board Berechtigungen richtig beachten
 */

$hook = function($module_name, $options, &$entries)
{
    global $prefix;
    // global $table_smilies, $table_words, $dateformat, $timecode, $timeoffset, $max_w, $max_h, $showeditedby;
    switch (true) {
        case !($file = realpath(dirname(__dir__) . '/settings.php')):
        case !include($file):
        case !isset($table_forums):
        case !isset($table_threads):
        case !isset($table_posts):
            return false;

        case $options['type'] === 'modules':
            return $entries[$module_name] = _BOARD;

        case !($file = realpath(dirname(__dir__) . '/includes/functions.php')):
        case !include($file):
            return false;

        case $options['type'] === 'items':

            extract($options);

            /* Threads */
            $qry = "
              SELECT a.tid, a.subject AS title, a.message AS text, a.dateline AS `date`, a.views, b.fid, b.name AS ftitle
              FROM " . $table_threads . " AS a
                LEFT JOIN " . $table_forums . " AS b
                  ON a.fid =b.fid
              WHERE private=''
                AND userlist=''
                AND b.name <> ''
              ORDER BY dateline DESC
              LIMIT 0," . intval($limit) ;
            $result = sql_query($qry);
            if ($result) {
                while ($entry = sql_fetch_assoc($result)) {
                    $entries[] = array(/* Moduldaten */
                        'loc' => "modules.php?name=$module_name&amp;file=viewthread&amp;tid=$entry[tid]",
                        'lastmod' => $entry['date'],
                        'text' => $entry['title'] . ' ' . $entry['text'],
                        );
                }
            }

            /* Antworten */
            $qry = "
              SELECT p.tid, t.subject AS title, p.message AS text, p.dateline AS `date`, t.views, b.fid, b.name AS ftitle
              FROM " . $table_posts . " AS p
                LEFT JOIN " . $table_threads . " AS t
                  ON p.tid = t.tid
                LEFT JOIN " . $table_forums . " AS b
                  ON t.fid =b.fid
              WHERE private=''
                AND userlist=''
                AND b.name <> ''
              ORDER BY p.dateline DESC
              LIMIT 0," . intval($limit) ;
            $result = sql_query($qry);

            if ($result) {
                while ($entry = sql_fetch_assoc($result)) {
                    $entries[] = array(/* Moduldaten */
                        'loc' => "modules.php?name=" . $module_name . "&amp;file=viewthread&amp;tid=" . $entry['tid'],
                        'lastmod' => $entry['date'],
                        'text' => $entry['title'] . ' ' . $entry['text'],
                        );
                }
            }
    }
} ;

?>