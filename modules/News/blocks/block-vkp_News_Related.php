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

switch (true) {
    case MX_MODULE != $module_name:
    case empty($GLOBALS['story_blocks']):
    case !mxModuleAllowed($module_name):
        /* Block darf nicht gecached werden und weg... */
        $mxblockcache = false;
        return;
}

/* Block kann gecached werden? */
$mxblockcache = true;

$blockfiletitle = _RELATED;

$thestory = $GLOBALS['story_blocks'];

$content = '';

/* Relation zum Thema selbst */
$relatedlist['modules.php?name=' . $module_name . '&amp;topic=' . intval($thestory['topic'])] = $thestory["topictext"];

/* Relationen zu den zugehörigen Links zum Thema */
/*
switch (true) {
    case !$thestory['relatedlinks']:
        $relatedlinks = array();
        break;
    case is_array($thestory['relatedlinks']):
        $relatedlinks = $thestory['relatedlinks'];
        break;
    default:
        $relatedlinks = (array)unserialize($thestory['relatedlinks']);
        break;
}
foreach ($relatedlinks as $text => $url) {
    $relatedlist[$url] = $text;
}
*/

/* Relationen aus Array */
include(PMX_SYSTEM_DIR . '/mxRelatedArray.php');
$relatedarray = array_change_key_case($relatedarray);
$search = join('|', array_keys($relatedarray));
$search = '#(?:[^[:alnum:]])(' . $search . ')(?:[^[:alnum:]])#i';
if (preg_match_all($search , strip_tags($thestory["completetext"]), $matches)) {
    $matches = array_change_key_case(array_flip(array_unique($matches[1])), CASE_LOWER);
    foreach ($matches as $key => $dummy) {
        $text = array_keys($relatedarray[$key]);
        $url = array_values($relatedarray[$key]);
        $relatedlist[$url[0]] = $text[0];
    }
}

/* der meistgelesene Artikel im Thema */
$result2 = sql_query("SELECT sid, title
            FROM ${prefix}_stories
            WHERE topic=" . intval($thestory['topic']) . " AND  time  <= now() " . pmx_multilang_query('alanguage', 'AND') . "
            ORDER BY counter desc
            LIMIT 1");
list($sid, $title) = sql_fetch_row($result2);
if ($sid) {
    $mostread = array(/* Der meistgelesene Artikel zu dem Thema ... */
        'url' => 'modules.php?name=' . $module_name . '&amp;file=article&amp;sid=' . $sid,
        'text' => $title,
        'caption' => _MOSTREAD . ' ' . $thestory["topictext"]);
} else {
    $mostread = array();
}

/* Templateausgabe erstellen */
$tpl = load_class('Template');
$tpl->init_path(__FILE__);
$tpl->init_template(__FILE__);
$tpl->assign('relatedlist', $relatedlist);
$tpl->assign('mostread', $mostread);
$content = $tpl->fetch();

?>