<?php
/**
 * mxBoard, pragmaMx Module
 * Copyright by pragmaMx Developer Team - http://www.pragmamx.org
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * $Revision: 30 $
 * $Author: PragmaMx $
 * $Date: 2015-07-23 13:11:26 +0200 (jeu. 23 juil. 2015) $
 */

/*

# mxBoard
RewriteRule ^mxBoard-forum-.*-view-([0-9]+)(-.*)?\.html$ mod.php?name=mxBoard&file=forumdisplay&fid=$1&_MORE_=$2 [L]
RewriteRule ^mxBoard-thread-.*-view-([0-9]+)(-.*)?\.html$ mod.php?name=mxBoard&file=viewthread&tid=$1&_MORE_=$2 [L]
RewriteRule ^mxBoard-forum-.*-newthread-([0-9]+)(-.*)?\.html$ mod.php?name=mxBoard&file=post.newtopic&fid=$1&_MORE_=$2 [L]
RewriteRule ^mxBoard-thread-.*-edit-([0-9]+)(-.*)?\.html$ mod.php?name=mxBoard&file=post.edit&tid=$1&_MORE_=$2 [L]
RewriteRule ^mxBoard-thread-.*-reply-([0-9]+)(-.*)?\.html$ mod.php?name=mxBoard&file=post.reply&tid=$1&_MORE_=$2 [L]
RewriteRule ^mxBoard-member-view-([^/]+)?\.html$ mod.php?name=mxBoard&file=member&action=viewpro&member=$1 [L]
RewriteRule ^mxBoard-member-online\.html$ mod.php?name=mxBoard&file=misc&action=online [L]
RewriteRule ^mxBoard-memberlist\.html$ mod.php?name=mxBoard&file=memberslist [L]
RewriteRule ^mxBoard-search\.html$ mod.php?name=mxBoard&file=search [L]
RewriteRule ^mxBoard-faq\.html$ mod.php?name=mxBoard&file=misc&action=faq [L]
RewriteRule ^mxBoard-newposts\.html$ mod.php?name=mxBoard&file=messslv [L]
RewriteRule ^mxBoard-postoftheday-([0-9]+)(-.*)?\.html$ mod.php?name=mxBoard&file=messslv&view=$1 [L]
RewriteRule ^mxBoard-statistics\.html$ mod.php?name=mxBoard&file=stats [L]
RewriteRule ^mxBoard-boardruless\.html$ mod.php?name=mxBoard&file=bbrules [L]
RewriteRule ^mxBoard-memberlist-numberofposts-([^/]+)?\.html$ mod.php?name=mxBoard&file=memberslist&order=postnum&_MORE_=$1 [L]
RewriteRule ^mxBoard-memberlist-user\.html$ mod.php?name=mxBoard&file=memberslist&order=username [L]

*/

$hook = function($name, $parameter, &$replaces)
{
    switch (true) {
        case !isset($parameter[$name]):
        case !include(PMX_MODULES_DIR . DS . $name . DS . 'settings.php'):
        case !isset($table_forums):
        case !isset($table_threads):
            return false;
    }

    $forum = array();
    $thread = array();

    foreach ($parameter[$name] as $key => $para) {
        if (isset($para['file'])) {
            switch (true) {
                case $para['file'] == 'forumdisplay':
                case $para['file'] == 'post.newtopic':
                    if (isset($para['fid']) && intval($para['fid'])) {
                        $forum[0][] = $para['fid'];
                        $forum[$key] = $para;
                    }
                    break;
                case $para['file'] == 'viewthread':
                case $para['file'] == 'post.edit':
                case $para['file'] == 'post.reply':
                    if (isset($para['tid']) && intval($para['tid'])) {
                        $thread[0][] = $para['tid'];
                        $thread[$key] = $para;
                    }
                    break;
                case (($para['file'] == 'member') &&
                        (isset($para['action']) == 'viewpro') && (isset($para['member']))):
                    $new_url = $name . '-member-view-' . $para['member'] . '.html';
                    /* die globalen Ersetzungsparameter mit den neuen Werten überschreiben */
                    $replaces[$key] = $para['prefix'] . $new_url . $para['suffix'];
                    break;
                case (($para['file'] == 'misc') && ($para['action'] == 'online')):
                    $new_url = $name . '-member-online.html';
                    /* die globalen Ersetzungsparameter mit den neuen Werten überschreiben */
                    $replaces[$key] = $para['prefix'] . $new_url . $para['suffix'];
                    break;
                case (($para['file'] == 'search')):
                    $new_url = $name . '-search.html';
                    /* die globalen Ersetzungsparameter mit den neuen Werten überschreiben */
                    $replaces[$key] = $para['prefix'] . $new_url . $para['suffix'];
                    break;
                case (($para['file'] == 'misc') && ($para['action'] == 'faq')):
                    $new_url = $name . '-faq.html';
                    /* die globalen Ersetzungsparameter mit den neuen Werten überschreiben */
                    $replaces[$key] = $para['prefix'] . $new_url . $para['suffix'];
                    break;
                case (($para['file'] == 'messslv') && (!isset($para['view']))):
                    $new_url = $name . '-newposts.html';
                    /* die globalen Ersetzungsparameter mit den neuen Werten überschreiben */
                    $replaces[$key] = $para['prefix'] . $new_url . $para['suffix'];
                    break;
                case (($para['file'] == 'messslv') && (isset($para['view']))):
                    $new_url = $name . '-postoftheday-' . $para['view'] . '.html';
                    /* die globalen Ersetzungsparameter mit den neuen Werten überschreiben */
                    $replaces[$key] = $para['prefix'] . $new_url . $para['suffix'];
                    break;
                case (($para['file'] == 'stats')):
                    $new_url = $name . '-statistics.html';
                    /* die globalen Ersetzungsparameter mit den neuen Werten überschreiben */
                    $replaces[$key] = $para['prefix'] . $new_url . $para['suffix'];
                    break;
                case (($para['file'] == 'bbrules')):
                    $new_url = $name . '-boardrules.html';
                    /* die globalen Ersetzungsparameter mit den neuen Werten überschreiben */
                    $replaces[$key] = $para['prefix'] . $new_url . $para['suffix'];
                    break;
            }
        }
    }

    /**
     */
    if ($forum) {
        $inquery = implode(',', array_unique(array_shift($forum)));
        $qry = "SELECT fid, name FROM $table_forums WHERE fid in ($inquery)";
        $result = sql_query($qry);
        while (list($id, $title) = sql_fetch_row($result)) {
            /**
             * den aus der DB ausgelesenen Titel als URL tauglich machen
             * - Umlaute werden umgeschrieben
             * - Sonderzeichen werden durch den angegebenen 2ten Parameter ersetzt
             * Einstellung:
             * der zweite Parameter der Funktion, als Sonderzeichenersatz
             */
            $titles[$id] = pmxModrewrite::title_entities(strip_tags($title), '-');
        }

        foreach ($forum as $key => $link) {
            /**
             * schreibt alle Parameter um in das normale pragmaMx mod_reweite Format,
             * also getrennt mit den Bindestrichen
             * Einstellung:
             * alle Parameter, die nicht benötigt werden, weil sie z.B. anderweitig
             * in der URL verwendet werden, als zusätzlichen Parameter der Funktion
             * übergeben
             */
            $new_url = pmxModrewrite::title_parameters($link, 'file', 'fid');
            /**
             * den neuen URL zusammensetzen
             * Einstellung:
             * nach belieben zusammensetzen und entspr. die .htaccess anpassen
             */
            switch ($link['file']) {
                case 'post.newtopic':
                    $new_url = $name . '-forum-' . $titles[$link['fid']] . '-newthread-' . $link['fid'] . $new_url;
                    break;
                case 'forumdisplay':
                default:
                    // $title = $name . '-lid-' . $title . '-' . $lid . $end; // Original m-t
                    $new_url = $name . '-forum-' . $titles[$link['fid']] . '-view-' . $link['fid'] . $new_url;
                    break;
            }

            /**
             * die globalen Ersetzungsparameter mit den neuen Werten überschreiben
             * Einstellung: keine, muss exakt so bleiben!
             */
            $replaces[$key] = $link['prefix'] . $new_url . $link['suffix'];
        }
    }

    /**
     */
    if ($thread) {
        $inquery = implode(',', array_unique(array_shift($thread)));
        $qry = "SELECT tid, subject FROM $table_threads WHERE tid in ($inquery)";
        $result = sql_query($qry);
        while (list($id, $title) = sql_fetch_row($result)) {
            /* den aus der DB ausgelesenen Titel als URL tauglich machen */
            $titles[$id] = pmxModrewrite::title_entities(strip_tags($title), '-');
        }
        foreach ($thread as $key => $link) {
            /* schreibt alle Parameter um in das normale pragmaMx mod_reweite Format */
            $new_url = pmxModrewrite::title_parameters($link, 'file', 'tid');
            /* den neuen URL zusammensetzen */
            switch ($link['file']) {
                case 'post.reply':
                    $new_url = $name . '-thread-' . $titles[$link['tid']] . '-reply-' . $link['tid'] . $new_url;
                    break;
                case 'post.edit':
                    $new_url = $name . '-thread-' . $titles[$link['tid']] . '-edit-' . $link['tid'] . $new_url;
                    break;
                case 'viewthread':
                default:
                    $new_url = $name . '-thread-' . $titles[$link['tid']] . '-view-' . $link['tid'] . $new_url;
            }
            /* die globalen Ersetzungsparameter mit den neuen Werten überschreiben */
            $replaces[$key] = $link['prefix'] . $new_url . $link['suffix'];
        }
    }
}

?>