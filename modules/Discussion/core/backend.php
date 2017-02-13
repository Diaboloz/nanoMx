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
// - Threads und Posts ???
 */

$hook = function($module_name, $options, &$entries)
{
    global $prefix;
    global $table_smilies, $table_words, $dateformat, $timecode, $timeoffset, $max_w, $max_h, $showeditedby;

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

            /* Konstanten, die z.B. im [quote] verwendet werden */
            defined('MXB_BASEMODIMG') OR define('MXB_BASEMODIMG', 'modules/' . $module_name . '/images');
            defined('MXB_BM_VIEWTHREAD1') OR define('MXB_BM_VIEWTHREAD1', '');
            defined('_TEXTBY') OR define('_TEXTBY', '');
            defined('_LASTREPLY1') OR define('_LASTREPLY1', '');
            defined('_TEXTAT') OR define('_TEXTAT', '');
            /* die beiden leeren Variablen verhindern die falsche Anzeige des Zitat-Datums */
            $dateformat = '';
            $timecode = '';

            /* Threads */
            $result = sql_query("
              SELECT a.tid, a.subject AS title, a.message AS description, a.lastpost, a.dateline, b.private, b.userlist
              FROM " . $table_threads . " AS a
                LEFT JOIN " . $table_forums . " AS b
                  ON a.fid =b.fid
              WHERE private=''
                AND userlist=''
              ORDER BY a.lastpost DESC" . $limit);
            if ($result) {
                while ($item = sql_fetch_assoc($result)) {
                    // mxbPostify($message, $allowhtml, $allowsmilies, $allowbbcode, $allowimgcode, $smileyoff = '', $bbcodeoff = '')
                    $item['module'] = $module_name;
                    $item['description'] = mxbPostify($item['description'], false, true, true, true);
                    $item['link'] = 'modules.php?name=' . $module_name . '&file=viewthread&tid=' . $item['tid'];
                    $item['date'] = intval(substr($item['lastpost'], 0, (strpos($item['lastpost'], '|'))));

                    $entries[] = $item;
                }
            }
    } ;
} ;

?>