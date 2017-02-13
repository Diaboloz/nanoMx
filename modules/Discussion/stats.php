<?php
/**
 * mxBoard, pragmaMx Module
 * Copyright by pragmaMx Developer Team - http://www.pragmamx.org
 *
 * $Author: PragmaMx $
 * $Revision: 6 $
 * $Date: 2015-07-08 09:07:06 +0200 (mer. 08 juil. 2015) $
 *
 * based on eBoard v1.1, rewrite and modified by
 * vkpMx-Developer-Team (http://www.maax-design.de)
 * Original source-code made by the XMB-team
 * (XMB-Forum, http://www.xmbforum.com), modified for nukestyle-systems
 * by Trollix (XForum, http://www.trollix.com).
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 */

defined('mxMainFileLoaded') or die('access denied');

$mxbnavigator->add(false, _TEXTSTATS);
// alle Foren ermitteln, die der User sehen darf:
// error_reporting(E_ALL);
$query = sql_query("SELECT fid, moderator, userlist, private FROM $table_forums ORDER BY fid");
while ($priv = sql_fetch_object($query)) {
    if (isTrusted($priv)) {
        $allowedboards[] = $priv->fid;
    }
}
if (empty($allowedboards)) {
    $allowedboards[] = '0'; // dummy für implode
}
$allowedboards = ' fid IN(' . implode(',', $allowedboards) . ')';

$query = sql_query("SELECT COUNT(tid) as nbsites, SUM(views) as totalus FROM $table_threads WHERE $allowedboards");
$row = sql_fetch_object($query);
$threads = $row->nbsites;
$totalus = $row->totalus;

$query = sql_query("SELECT COUNT(pid) as nbsites FROM $table_posts WHERE $allowedboards");
$row = sql_fetch_object($query);
$posts = $row->nbsites;

$query = sql_query("SELECT COUNT(fid) as nbsites FROM $table_forums WHERE type='forum' AND $allowedboards");
$row = sql_fetch_object($query);
$forums = $row->nbsites;

$query = sql_query("SELECT COUNT(fid) as nbsites FROM $table_forums WHERE type='forum' AND status='on' AND $allowedboards");
$row = sql_fetch_object($query);
$forumsa = $row->nbsites;

$query = sql_query("SELECT COUNT(uid) as nbsites FROM $table_members");
$row = sql_fetch_object($query);
$members = $row->nbsites;

$query = sql_query("SELECT COUNT(uid) as nbsites FROM $table_members WHERE postnum!='0'");
$row = sql_fetch_object($query);
$membersact = $row->nbsites;

if ($members != 0) {
    $mapercent = $membersact * 100 / $members;
} else {
    $mapercent = 0;
}
$mapercent = number_format($mapercent, 2);
$mapercent .= "%";

$query = sql_query("SELECT views, tid, subject FROM $table_threads WHERE $allowedboards ORDER BY views DESC LIMIT 0, " . intval($nbitemsinstats));
$viewmost = '<ol>';
while ($views = sql_fetch_object($query)) {
    $viewmost .= "<li><a href=\"" . MXB_BM_VIEWTHREAD1 . "tid=" . $views->tid . "\">" . strip_tags($views->subject) . "</a> (" . $views->views . " " . _VIEWSL . ")</li>";
}
$viewmost .= "</ol>";

$query = sql_query("SELECT replies, tid, subject FROM $table_threads WHERE $allowedboards ORDER BY replies DESC LIMIT 0, $nbitemsinstats");
$replymost = '<ol>';
while ($reply = sql_fetch_object($query)) {
    $replymost .= "<li><a href=\"" . MXB_BM_VIEWTHREAD1 . "tid=" . $reply->tid . "\">" . strip_tags($reply->subject) . "</a> (" . $reply->replies . " " . _REPLIESL . ")</li>";
}
$replymost .= "</ol>";

$latest = '<ol>';
$query = sql_query("SELECT DATE_FORMAT( FROM_UNIXTIME( lastpost ), '%Y-%m-%d' ) as  lastpostfmt, lastpost, tid, subject FROM $table_threads WHERE $allowedboards ORDER BY lastpost DESC LIMIT 0, " . intval($nbitemsinstats));
while ($last = sql_fetch_object($query)) {
    $lpdate = date($dateformat, (int)$last->lastpost + ($timeoffset * 3600));
    $lptime = date($timecode, (int)$last->lastpost + ($timeoffset * 3600));
    $thislast = _LASTREPLY1 . " $lpdate " . _TEXTAT . " $lptime";
    $latest .= "<li><a href=\"" . MXB_BM_VIEWTHREAD1 . "tid=" . $last->tid . "\">" . strip_tags($last->subject) . "</a> ($thislast)</li>";
}
$latest .= "</ol>";

$topusers = '<ol>';
$query = sql_query("SELECT postnum, username, totaltime FROM $table_members ORDER BY postnum DESC LIMIT 0, " . intval($nbitemsinstats));
while ($last = sql_fetch_object($query)) {
    $timeinforum = round($last->totaltime / 3600, 1);
    $topusers .= "<li>" . mxb_link2profile($last->username) . " (" . $last->postnum . " " . _MEMPOSTS . ", " . $timeinforum . " " . _TEXTHOURS . ")</li>";
}
$topusers .= "</ol>";

$query = sql_query("SELECT posts, threads, fid, name FROM $table_forums WHERE $allowedboards ORDER BY posts DESC LIMIT 0, 1");
$pop = sql_fetch_object($query);
$popforum = '';
if (is_object($pop)) {
    $popforum = "<a href=\"" . MXB_BM_FORUMDISPLAY1 . "fid=" . $pop->fid . "\">" . $pop->name . "</a>";
    $pop->posts += $pop->threads;
}
$posts += $threads;

$querytime = sql_query("SELECT fid, name, totaltime FROM $table_forums WHERE $allowedboards ORDER BY totaltime DESC LIMIT 0, 1");
$poptime = sql_fetch_object($querytime);
$poptimeforum = '';
$poptimedisplay = '';
if (is_object($poptime)) {
    $poptimeforum = "<a href=\"" . MXB_BM_FORUMDISPLAY1 . "fid=" . $poptime->fid . "\">" . $poptime->name . "</a>";
    $poptimedisplay = round($poptime->totaltime / 3600, 1);
}

$mempost = 0;
$query = sql_query("SELECT SUM(postnum) FROM $table_members");
list($mempost) = sql_fetch_row($query);
if ($members != 0) {
    $mempost = $mempost / $members;
}
$mempost = number_format($mempost, 2);

$forumpost = 0;
if ($forums != 0) {
    $query = sql_query("SELECT SUM(posts) FROM $table_forums WHERE $allowedboards");
    list($forumpost) = sql_fetch_row($query);
    $forumpost = $forumpost / $forums;
}
$forumpost = number_format($forumpost, 2);

$threadreply = 0;
if ($threads != 0) {
    $query = sql_query("SELECT SUM(replies) FROM $table_threads WHERE $allowedboards");
    list($threadreply) = sql_fetch_row($query);
    $threadreply = $threadreply / $threads;
}
$threadreply = number_format($threadreply, 2);

$query = sql_query("SELECT dateline FROM $table_threads WHERE $allowedboards ORDER BY dateline LIMIT 0, 1");
$row = sql_fetch_object($query);
$postdays = '';
if (is_object($row)) {
    $postdays = $row->dateline;
}

$postsday = $posts / ((time() - $postdays) / 86400);
$postsday = number_format($postsday, 2);

$query = sql_query("SELECT u.user_regtime as regdate
                    FROM $table_members AS fm
                    LEFT JOIN {$user_prefix}_users AS u
                    ON fm.username = u.uname
                    ORDER BY regdate ASC LIMIT 0, 1");
$row = sql_fetch_object($query);
$memberdays = $row->regdate;

$query = sql_query("SELECT fm.username, u.user_regtime as regdate
                    FROM $table_members AS fm
                    LEFT JOIN {$user_prefix}_users AS u
                    ON fm.username = u.uname
                    ORDER BY regdate DESC LIMIT 0, 1");
$lastmem = sql_fetch_object($query);
$lastmember = $lastmem->username;
$memhtml = mxb_link2profile($lastmember);

$membersday = $members / ((time() - $memberdays) / 86400);
$membersday = number_format($membersday, 2);

eval(_EVALSTATS1);
eval(_EVALSTATS2);
eval(_EVALSTATS3);
eval(_EVALSTATS4);
eval(_EVALSTATS5);
eval(_EVALSTATS51);
eval(_EVALSTATS6);
eval(_EVALSTATS7);
eval(_EVALSTATS8);
eval(_EVALSTATS82);
eval(_EVALSTATS9);
eval(_EVALSTATS10);
eval(_EVALSTATS11);
eval(_EVALSTATS12);
eval(_EVALSTATS13);
eval(_EVALSTATS14);
eval(_EVALSTATS15);
eval(_EVALSTATS16);


/* Template initialisieren */
include_once(dirname(__FILE__) . DS . 'includes' . DS . 'header.php');
$template = load_class('Template');
$template->init_path(__FILE__);
$template->memhtml = $memhtml;
$template->display('stats.html');    
include_once(MXB_BASEMODINCLUDE . 'footer.php');

?>
