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

defined('mxMainFileLoaded') or die('access denied');

global $prefix;

/* der passende Modulname */
$module_name = basename(dirname(dirname(__FILE__)));

switch (false) {
    case mxModuleAllowed($module_name):
    case mxModuleAllowed('Topics'):
    case $classfile = realpath(PMX_MODULES_DIR . '/Topics/includes/class.topics.php');
        $mxblockcache = false; // Block darf nicht gecached werden
        return;
}

/* Block kann gecached werden? */
$mxblockcache = true;

if (!class_exists('pmxTopics', false)) {
    include($classfile);
}

$topclass = new pmxTopics();

$qry = "SELECT t.topicid, t.topicimage, t.topictext, t.topicdesc
        FROM ${prefix}_topics AS t
        INNER JOIN ${prefix}_stories AS s
          ON t.topicid = s.topic
        WHERE t.topictext<>''
          AND t.topicimage<>''
          AND s.time <= now()" . pmx_multilang_query('s.alanguage', 'AND') . "
        ORDER BY RAND() LIMIT 1";
$result = sql_query($qry);
$topic = sql_fetch_assoc($result);

if (!$topic) {
    return;
}

$result = sql_query("SELECT sid, title FROM ${prefix}_stories
          WHERE topic=" . intval($topic['topicid']) . "
            AND  time  <= now() " . pmx_multilang_query('alanguage', 'AND') . "
          ORDER BY  time  DESC,  sid  DESC
          LIMIT 10");
while ($row = sql_fetch_assoc($result)) {
    $row['link'] = 'modules.php?name=' . $module_name . '&amp;file=article&amp;sid=' . $row['sid'];
    $topic['stories'][] = $row;
}

$topic['link'] = 'modules.php?name=Topics&amp;topic=' . $topic['topicid'];
$topic['topicimage'] = $topclass->topicimage($topic['topicimage']);

/* Templateausgabe erstellen */
$tpl = load_class('Template');
$tpl->init_path(__FILE__);
$tpl->init_template(__FILE__);
$tpl->assign($topic);
$content = $tpl->fetch();

$blockfiletitle = $topic['topictext'];

?>