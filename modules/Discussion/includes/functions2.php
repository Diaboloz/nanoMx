<?php
/**
 * mxBoard, pragmaMx Module
 * Copyright by pragmaMx Developer Team - http://www.pragmamx.org
 *
 * $Author: PragmaMx $
 * $Revision: 120 $
 * $Date: 2016-03-31 12:35:00 +0200 (jeu. 31 mars 2016) $
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
defined('MXB_INIT') or die('Not in mxBoard...');

function mxbLastPostForumTid($fid, $type)
{
    global $table_links, $table_threads, $table_forums, $linkthreadstatus, $linkforumstatus, $lastpostsubj;

    $querythreads = sql_query("SELECT tid, lastpost, subject FROM $table_threads WHERE fid=" . intval($fid) . " ORDER BY lastpost DESC limit 0,1") ;
    $thread = sql_fetch_assoc($querythreads);

    if ($type != 'sub') {
        $querysubforums = sql_query("SELECT fid, lastpost FROM $table_forums WHERE type='sub' AND fup='" . intval($fid) . "' ORDER BY lastpost DESC limit 0,1") ;
        $subforum = sql_fetch_assoc($querysubforums);
    }
    $subforum['lastpost'] = (empty($subforum['lastpost'])) ? 0 : $subforum['lastpost'];
    $subforum['fid'] = (empty($subforum['fid'])) ? 0 : $subforum['fid'];

    if ($thread['lastpost'] > $subforum['lastpost']) {
        $lastpost = $thread['lastpost'];
        $lasttid = $thread['tid'];
        $lastsubject = $thread['subject'];
    } else { // wenn der Thread nicht der neuste Beitrag ist
        $querysubthreads = sql_query("SELECT tid, lastpost, subject FROM $table_threads WHERE fid='" . intval($subforum['fid']) . "' ORDER BY lastpost DESC limit 0,1") ;
        $subthread = sql_fetch_assoc($querysubthreads);
        $lastpost = $subthread['lastpost'];
        $lasttid = $subthread['tid'];
        $lastsubject = $subthread['subject'];

        if ($linkforumstatus == 'on' || $linkthreadstatus == 'on') {
            $querysubforumlinks = sql_query("SELECT * FROM $table_links WHERE toid='" . intval($subforum['fid']) . "' AND status='on' ORDER BY lastpost DESC limit 0,1") ;
            $subforumlink = sql_fetch_assoc($querysubforumlinks);

            if ($subforumlink['lastpost'] > $lastpost) {
                $lastpost = $subforumlink['lastpost'];

                if ($subforumlink['type'] == 'forum' && $linkforumstatus == 'on') {
                    $querylinkedthreads = sql_query("SELECT tid, lastpost, subject FROM $table_threads WHERE fid='" . intval($subforumlink['fromid']) . "' ORDER BY lastpost DESC limit 0,1") ;
                    $linkedthread = sql_fetch_assoc($querylinkedthreads);

                    if ($linkthreadstatus == 'on') {
                        $querylinkedthreadlinks = sql_query("SELECT fromid, lastpost FROM $table_links WHERE type='thread' AND toid='" . intval($subforumlink['fromid']) . "' AND status='on' ORDER BY lastpost DESC limit 0,1") ;
                        $linkedthreadlink = sql_fetch_assoc($querylinkedthreadlinks);
                        if ($linkedthreadlink['lastpost'] > $linkedthread['lastpost']) {
                            if ($lastpostsubj == 'on') {
                                $query = sql_query("SELECT subject FROM $table_threads WHERE tid='" . intval($linkedthreadlink['fromid']) . "'");
                                list($lastsubject) = sql_fetch_row($query);
                            }
                            $lasttid = $linkedthreadlink['fromid'] . "&amp;linkstatus=on&amp;jumplink=" . $subforumlink['toid'] . "&amp;linktype=" . $subforumlink['type'] . "&amp;lid=" . $subforumlink['lid'];
                        } else {
                            $lastsubject = $linkedthread['subject'];
                            $lasttid = $linkedthread['tid'] . "&amp;linkstatus=on&amp;jumplink=" . $subforumlink['toid'] . "&amp;linktype=" . $subforumlink['type'] . "&amp;lid=" . $subforumlink['lid'];
                        }
                    } else {
                        $lastsubject = $linkedthread['subject'];
                        $lasttid = $linkedthread['tid'] . "&amp;linkstatus=on&amp;jumplink=" . $subforumlink['toid'] . "&amp;linktype=" . $subforumlink['type'] . "&amp;lid=" . $subforumlink['lid'];
                    }
                } else {
                    // Wenn thread gelinkt ist
                    if ($lastpostsubj == 'on') {
                        $query = sql_query("SELECT subject FROM $table_threads WHERE tid='" . intval($subforumlink['fromid']) . "'");
                        list($lastsubject) = sql_fetch_row($query);
                    }
                    $lasttid = $subforumlink['fromid'] . "&amp;linkstatus=on&amp;jumplink=" . $subforumlink['toid'] . "&amp;linktype=" . $subforumlink['type'] . "&amp;lid=" . $subforumlink['lid'];
                }
            }
        }
    } // END Else von Thread ist nicht der neuste Beitrag
    // bis hierhin werden alle Threads und Subforen in dem Forum berücksichtigt. In den Subforen werden auch noch gelinkte
    // Threads und Foren berücksichtigt
    // hier werden jetzt alle Links im Mainforum überprüft
    if ($linkthreadstatus == 'on' || $linkforumstatus == 'on') {
        $querylinks = sql_query("SELECT * FROM $table_links WHERE toid=" . intval($fid) . " AND status='on' ORDER BY lastpost DESC limit 0,1") ;
        $link = sql_fetch_assoc($querylinks);
        if ($link['lastpost'] > $lastpost) {
            $lastpost = $link['lastpost'];

            if ($link['type'] == 'forum' && $linkforumstatus == 'on') {
                $querylinkedthreadsmain = sql_query("SELECT tid, lastpost, subject FROM $table_threads WHERE fid='" . intval($link['fromid']) . "' ORDER BY lastpost DESC limit 0,1") ;
                $linkedthreadmain = sql_fetch_assoc($querylinkedthreadsmain);

                if ($linkthreadstatus == 'on') {
                    $querylinkedthreadmainlinks = sql_query("SELECT fromid, lastpost FROM $table_links WHERE type='thread' AND toid='" . intval($link['fromid']) . "' AND status='on' ORDER BY lastpost DESC limit 0,1") ;
                    $linkedthreadmainlink = sql_fetch_assoc($querylinkedthreadmainlinks);
                    if ($linkedthreadmainlink['lastpost'] > $linkedthreadmain['lastpost']) {
                        if ($lastpostsubj == 'on') {
                            $query = sql_query("SELECT subject FROM $table_threads WHERE tid='" . intval($linkedthreadmainlink['fromid']) . "'");
                            list($lastsubject) = sql_fetch_row($query);
                        }
                        $lasttid = $linkedthreadmainlink['fromid'] . "&amp;linkstatus=on&amp;jumplink=" . $link['toid'] . "&amp;linktype=" . $link['type'] . "&amp;lid=" . $link['lid'];
                    } else {
                        $lastsubject = $linkedthreadmain['subject'];
                        $lasttid = $linkedthreadmain['tid'] . "&amp;linkstatus=on&amp;jumplink=" . $link['toid'] . "&amp;linktype=" . $link['type'] . "&amp;lid=" . $link['lid'];
                    }
                } else {
                    $lastsubject = $linkedthreadmain['subject'];
                    $lasttid = $linkedthreadmain['tid'] . "&amp;linkstatus=on&amp;jumplink=" . $link['toid'] . "&amp;linktype=" . $link['type'] . "&amp;lid=" . $link['lid'];
                }
            } else {
                // Wenn thread gelinkt ist
                if ($lastpostsubj == 'on') {
                    $query = sql_query("SELECT subject FROM $table_threads WHERE tid='" . intval($link['fromid']) . "'");
                    list($lastsubject) = sql_fetch_row($query);
                }
                $lasttid = $link['fromid'] . "&amp;linkstatus=on&amp;jumplink=" . $link['toid'] . "&amp;linktype=" . $link['type'] . "&amp;lid=" . $link['lid'];
            }
        } // END if Link ist größer
    } // END if LInk ist aktiviert
    $thisthread = new stdClass;
    $thisthread->subject = $lastsubject;
    $thisthread->tid = $lasttid;

    return $thisthread;
}

function mxbColorSubject($subject, $color, $bold, $italic)
{
    if ($color) {
        $subject = preg_replace("#&lt;font\s+color=(.*&gt;)(.*&lt;)#", "\\2", $subject);
        $subject = str_replace("&lt;/font&gt;", "", $subject);
        $subject = "<font color=\"$color\">" . $subject . "</font>";
    } else {
        $subject = preg_replace("#&lt;font\s+color=(.*&gt;)(.*&lt;)#", "\\2", $subject);
        $subject = str_replace("&lt;/font&gt;", "", $subject);
    }

    if ($bold) {
        $subject = str_replace("&lt;b&gt;", "", $subject);
        $subject = str_replace("&lt;/b&gt;", "", $subject);
        $subject = "<b>" . $subject . "</b>";
    } else {
        $subject = str_replace("&lt;b&gt;", "", $subject);
        $subject = str_replace("&lt;/b&gt;", "", $subject);
    }

    if ($italic) {
        $subject = str_replace("&lt;i&gt;", "", $subject);
        $subject = str_replace("&lt;/i&gt;", "", $subject);
        $subject = "<i>" . $subject . "</i>";
    } else {
        $subject = str_replace("&lt;i&gt;", "", $subject);
        $subject = str_replace("&lt;/i&gt;", "", $subject);
    }

    return $subject;
}

function mxbLargeSelect()
{
    global $table_forums, $hideprivate;
    static $forumselect;
    if (isset($forumselect)) {
        return $forumselect;
    }

    $forumselect = "<option value=\"all\">" . _TEXTALL . "</option>\n";

    $queryfor = sql_query("SELECT fid, name, moderator, private, userlist FROM $table_forums WHERE type='forum' AND fup='' AND status='on' ORDER BY displayorder");
    while ($forum = sql_fetch_object($queryfor)) {
        $authorization = (mxbPrivateCheck($forum));

        if ($authorization) {
            $forumselect .= "<option value=\"" . $forum->fid . "\"> &nbsp; &gt;" . htmlspecialchars($forum->name) . "</option>\n";
        }

        $querysub = sql_query("SELECT fid, name, moderator, private, userlist FROM $table_forums WHERE type='sub' AND fup='$forum->fid' AND status='on' ORDER BY displayorder");
        while ($sub = sql_fetch_object($querysub)) {
            $authorization = mxbPrivateCheck($sub);
            if ($authorization) {
                $forumselect .= "<option value=\"" . $sub->fid . "\">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &gt; " . htmlspecialchars($sub->name) . "</option>\n";
            }
        }
    }

    $forumselect .= "<option value=\"0\" disabled=\"disabled\">&nbsp;</option>";

    $querygrp = sql_query("SELECT fid, name FROM $table_forums WHERE type='group' AND status='on' ORDER BY displayorder");
    while ($group = sql_fetch_object($querygrp)) {
        $forumselect .= "<option value=\"0\">" . htmlspecialchars($group->name) . "</option>\n";
        $forumselect .= "<option value=\"0\" disabled=\"disabled\">--------------------</option>\n";

        $queryfor = sql_query("SELECT fid, name, moderator, private, userlist FROM $table_forums WHERE fup='$group->fid' AND type='forum' AND status='on' ORDER BY displayorder");
        while ($forum = sql_fetch_object($queryfor)) {
            $authorization = mxbPrivateCheck($forum);

            if ($authorization) {
                $forumselect .= "<option value=\"" . $forum->fid . "\"> &nbsp; &gt;" . htmlspecialchars($forum->name) . "</option>\n";
            }

            $querysub = sql_query("SELECT fid, name, moderator, private, userlist FROM $table_forums WHERE type='sub' AND fup='$forum->fid' AND status='on' ORDER BY displayorder");
            while ($sub = sql_fetch_object($querysub)) {
                $authorization = mxbPrivateCheck($sub);

                if ($authorization) {
                    $forumselect .= "<option value=\"" . $sub->fid . "\">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &gt; " . htmlspecialchars($sub->name) . "</option>\n";
                }
            }
        }
        $forumselect .= "<option value=\"0\" disabled=\"disabled\">&nbsp;</option>";
    }

    return $forumselect;
}

function mxbLargeSelectWithLinks($current = 0)
{
    global $table_forums, $table_links, $linkforumstatus, $hideprivate;

    $sel = ' selected="selected" class="current"';
    $forumselect = "<option value=\"all\"" . (($current == 'all') ? $sel : '') . ">" . _TEXTALL . "</option>\n";
    $forumselect .= "<option value=\"0\"" . (($current == 0) ? $sel : '') . ">&nbsp;</option>\n";

    $queryfor = sql_query("SELECT fid, name, moderator, private, userlist FROM $table_forums WHERE type='forum' AND fup='' AND status='on' ORDER BY displayorder");
    while ($forum = sql_fetch_object($queryfor)) {
        $linkfromid = $forum->fid;

        $authorization = mxbPrivateCheck($forum);

        if ($authorization) {
            $forumselect .= "<option value=\"" . $forum->fid . "\"" . (($current == $forum->fid) ? $sel : '') . "> &nbsp; &gt; " . htmlspecialchars($forum->name) . "</option>\n";
        }

        $querysub = sql_query("SELECT fid, name, moderator, private, userlist FROM $table_forums WHERE type='sub' AND fup='$forum->fid' AND status='on' ORDER BY displayorder");
        while ($sub = sql_fetch_object($querysub)) {
            $authorization = mxbPrivateCheck($sub);

            if ($authorization) {
                $forumselect .= "<option value=\"" . $sub->fid . "\"" . (($current == $sub->fid) ? $sel : '') . ">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &gt; " . htmlspecialchars($sub->name) . "</option>\n";
            }

            if ($linkforumstatus == 'on') {
                $querylink = sql_query("SELECT lid, fromid, toid FROM $table_links WHERE type='forum' AND toid='$sub->fid' AND status='on'");
                while ($link = sql_fetch_object($querylink)) {
                    $querylinkforum = sql_query("SELECT fid, name, moderator, private, userlist FROM $table_forums WHERE fid='$link->fromid' AND status='on'");
                    $linkforum = sql_fetch_object($querylinkforum);

                    $authorization = mxbPrivateCheck($linkforum);

                    if ($authorization) {
                        $jumperlink = $linkforum->fid . "&amp;linkstatus=on&amp;linktype=forum&amp;lid=" . $link->lid;
                        $forumselect .= "<option value=\"" . $jumperlink . "\">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &gt;&gt; " . htmlspecialchars($linkforum->name) . "</option>\n";
                    }
                }
            }
        }

        if ($linkforumstatus == 'on') {
            $querylink = sql_query("SELECT lid, fromid, toid FROM $table_links WHERE type='forum' AND toid='$forum->fid' AND status='on'");
            while ($link = sql_fetch_object($querylink)) {
                $querylinkforum = sql_query("SELECT fid, name, moderator, private, userlist FROM $table_forums WHERE fid='$link->fromid' AND status='on'");
                $linkforum = sql_fetch_object($querylinkforum);

                $authorization = mxbPrivateCheck($linkforum);

                if ($authorization) {
                    $jumperlink = $linkforum->fid . "&amp;linkstatus=on&amp;linktype=forum&amp;lid=" . $link->lid;
                    $forumselect .= "<option value=\"" . $jumperlink . "\">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &gt;&gt; " . htmlspecialchars($linkforum->name) . "</option>\n";
                }
            }
        }
    }

    $forumselect .= "<option value=\"0\" disabled=\"disabled\">&nbsp;</option>";

    $querygrp = sql_query("SELECT fid, name FROM $table_forums WHERE type='group' AND status='on' ORDER BY displayorder");
    while ($group = sql_fetch_object($querygrp)) {
        $forumselect .= "<option value=\"0\">" . htmlspecialchars($group->name) . "</option>\n";
        $forumselect .= "<option value=\"0\" disabled=\"disabled\">--------------------</option>\n";

        $queryfor = sql_query("SELECT fid, name, moderator, private, userlist FROM $table_forums WHERE fup='$group->fid' AND type='forum' AND status='on' ORDER BY displayorder");
        while ($forum = sql_fetch_object($queryfor)) {
            $authorization = mxbPrivateCheck($forum);

            if ($authorization) {
                $forumselect .= "<option value=\"" . $forum->fid . "\"" . (($current == $forum->fid) ? $sel : '') . "> &nbsp; &gt; " . htmlspecialchars($forum->name) . "</option>\n";
            }

            $querysub = sql_query("SELECT fid, name, moderator, private, userlist FROM $table_forums WHERE type='sub' AND fup='$forum->fid' AND status='on' ORDER BY displayorder");
            while ($sub = sql_fetch_object($querysub)) {
                $authorization = mxbPrivateCheck($sub);

                if ($authorization) {
                    $forumselect .= "<option value=\"" . $sub->fid . "\"" . (($current == $sub->fid) ? $sel : '') . ">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &gt; " . htmlspecialchars($sub->name) . "</option>\n";
                }

                if ($linkforumstatus == 'on') {
                    $querylink = sql_query("SELECT lid, fromid, toid FROM $table_links WHERE type='forum' AND toid='$sub->fid' AND status='on'");
                    while ($link = sql_fetch_object($querylink)) {
                        $querylinkforum = sql_query("SELECT fid, name, moderator, private, userlist FROM $table_forums WHERE fid='$link->fromid' AND status='on'");
                        $linkforum = sql_fetch_object($querylinkforum);

                        $authorization = mxbPrivateCheck($linkforum);

                        if ($authorization) {
                            $jumperlink = $linkforum->fid . "&amp;linkstatus=on&amp;linktype=forum&amp;lid=" . $link->lid;
                            $forumselect .= "<option value=\"" . $jumperlink . "\">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &gt;&gt; " . htmlspecialchars($linkforum->name) . "</option>\n";
                        }
                    }
                }
            }

            if ($linkforumstatus == 'on') {
                $querylink = sql_query("SELECT lid, fromid, toid FROM $table_links WHERE type='forum' AND toid='$forum->fid' AND status='on'");
                while ($link = sql_fetch_object($querylink)) {
                    $querylinkforum = sql_query("SELECT fid, name, moderator, private, userlist FROM $table_forums WHERE fid='$link->fromid' AND status='on'");
                    $linkforum = sql_fetch_object($querylinkforum);

                    $authorization = mxbPrivateCheck($linkforum);

                    if ($authorization) {
                        $jumperlink = $linkforum->fid . "&amp;linkstatus=on&amp;linktype=forum&amp;lid=" . $link->lid;
                        $forumselect .= "<option value=\"" . $jumperlink . "\">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &gt;&gt; " . htmlspecialchars($linkforum->name) . "</option>\n";
                    }
                }
            }
        }
        $forumselect .= "<option value=\"0\" disabled=\"disabled\">&nbsp;</option>";
    }
    return $forumselect;
}

function mxbBuildQuickJump($reset = false)
{
    global $table_forums, $table_links, $linkforumstatus, $hideprivate, $affjumper, $affjumperdynamic, $eBoardUser, $currentlang;

    if ($affjumper != 'on') {
        return;
    }

    if ($affjumperdynamic != 'on') {
        $reset = true;
    }

    $cache = load_class('Cache');
    $cacheid = 'mxb_jumper_' . $table_forums . $eBoardUser['status'] . $currentlang;
    if (!$reset && (($forumselect = $cache->read($cacheid)) !== false)) {
        return $forumselect;
    }

    $forumselect = "<form method=\"post\" name=\"quickjump\" action=\"" . MXB_BM_FORUMDISPLAY0 . "\">\n";
    $forumselect .= "<fieldset class=\"jumpbox\">\n";
    $forumselect .= "<select name=\"fid\" onchange=\"javascript:if(document.quickjump.fid.value!=''){document.quickjump.submit()}\">\n";

    $forumselect .= "<option value=\"0\">Quick-Jumper</option>\n";
    $queryfor = sql_query("SELECT fid, name, status, moderator, private, userlist FROM $table_forums WHERE type='forum' AND fup='' AND status='on' ORDER BY displayorder");
    while ($forum = sql_fetch_object($queryfor)) {
        $linkfromid = $forum->fid;

        $authorization = mxbPrivateCheck($forum);
        if ($authorization) {
            $forumselect .= "<option value=\"" . $forum->fid . "\"> &nbsp; &gt; " . htmlspecialchars($forum->name) . "</option>\n";
        }

        $querysub = sql_query("SELECT fid, name, moderator, private, userlist FROM $table_forums WHERE type='sub' AND fup='$forum->fid' AND status='on' ORDER BY displayorder");
        while ($sub = sql_fetch_object($querysub)) {
            $authorization = mxbPrivateCheck($sub);
            if ($authorization) {
                $forumselect .= "<option value=\"" . $sub->fid . "\">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &gt; " . htmlspecialchars($sub->name) . "</option>\n";
            }

            if ($linkforumstatus == 'on') {
                $querylink = sql_query("SELECT lid, fromid, toid FROM $table_links WHERE type='forum' AND toid='$sub->fid' AND status='on'");
                while ($link = sql_fetch_object($querylink)) {
                    $querylinkforum = sql_query("SELECT fid, name, moderator, private, userlist FROM $table_forums WHERE fid='$link->fromid' AND status='on'");
                    $linkforum = sql_fetch_object($querylinkforum);

                    $authorization = mxbPrivateCheck($linkforum);
                    if ($authorization) {
                        $jumperlink = $linkforum->fid . "&amp;linkstatus=on&amp;linktype=forum&amp;lid=" . $link->lid;
                        $forumselect .= "<option value=\"" . $jumperlink . "\">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &gt;&gt; " . htmlspecialchars($linkforum->name) . "</option>\n";
                    }
                }
            }
        }

        if ($linkforumstatus == 'on') {
            $querylink = sql_query("SELECT lid, fromid, toid FROM $table_links WHERE type='forum' AND toid='$forum->fid' AND status='on'");
            while ($link = sql_fetch_object($querylink)) {
                $querylinkforum = sql_query("SELECT fid, name, moderator, private, userlist FROM $table_forums WHERE fid='$link->fromid' AND status='on'");
                $linkforum = sql_fetch_object($querylinkforum);

                $authorization = mxbPrivateCheck($linkforum);
                if ($authorization) {
                    $jumperlink = $linkforum->fid . "&amp;linkstatus=on&amp;linktype=forum&amp;lid=" . $link->lid;
                    $forumselect .= "<option value=\"" . $jumperlink . "\">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &gt;&gt; " . htmlspecialchars($linkforum->name) . "</option>\n";
                }
            }
        }
    }

    $forumselect .= "<option value=\"0\" disabled=\"disabled\">&nbsp;</option>";

    $querygrp = sql_query("SELECT fid, name FROM $table_forums WHERE type='group' AND status='on' ORDER BY displayorder");
    while ($group = sql_fetch_object($querygrp)) {
        $forumselect .= "<option value=\"" . $group->fid . "\">" . htmlspecialchars($group->name) . "</option>\n";
        $forumselect .= "<option value=\"" . $group->fid . "\" disabled=\"disabled\">--------------------</option>\n";

        $queryfor = sql_query("SELECT fid, name, moderator, private, userlist FROM $table_forums WHERE fup='$group->fid' AND type='forum' AND status='on' ORDER BY displayorder");
        while ($forum = sql_fetch_object($queryfor)) {
            $authorization = mxbPrivateCheck($forum);
            if ($authorization) {
                $forumselect .= "<option value=\"" . $forum->fid . "\"> &nbsp; &gt; " . htmlspecialchars($forum->name) . "</option>\n";
            }

            $querysub = sql_query("SELECT fid, name, moderator, private, userlist FROM $table_forums WHERE type='sub' AND fup='$forum->fid' AND status='on' ORDER BY displayorder");
            while ($sub = sql_fetch_object($querysub)) {
                $authorization = mxbPrivateCheck($sub);
                if ($authorization) {
                    $forumselect .= "<option value=\"" . $sub->fid . "\">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &gt; " . htmlspecialchars($sub->name) . "</option>\n";
                }

                if ($linkforumstatus == 'on') {
                    $querylink = sql_query("SELECT lid, fromid, toid FROM $table_links WHERE type='forum' AND toid='$sub->fid' AND status='on'");
                    while ($link = sql_fetch_object($querylink)) {
                        $querylinkforum = sql_query("SELECT fid, name, moderator, private, userlist FROM $table_forums WHERE fid='$link->fromid' AND status='on'");
                        $linkforum = sql_fetch_object($querylinkforum);

                        $authorization = mxbPrivateCheck($linkforum);
                        if ($authorization) {
                            $jumperlink = $linkforum->fid . "&amp;linkstatus=on&amp;linktype=forum&amp;lid=" . $link->lid;
                            $forumselect .= "<option value=\"" . $jumperlink . "\">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &gt;&gt; " . htmlspecialchars($linkforum->name) . "</option>\n";
                        }
                    }
                }
            }
            if ($linkforumstatus == 'on') {
                $querylink = sql_query("SELECT lid, fromid, toid FROM $table_links WHERE type='forum' AND toid='$forum->fid' AND status='on'");
                while ($link = sql_fetch_object($querylink)) {
                    $querylinkforum = sql_query("SELECT fid, name, moderator, private, userlist FROM $table_forums WHERE fid='" . intval($link->fromid) . "' AND status='on'");
                    $linkforum = sql_fetch_object($querylinkforum);

                    $authorization = mxbPrivateCheck($linkforum);
                    if ($authorization) {
                        $jumperlink = $linkforum->fid . "&amp;linkstatus=on&amp;linktype=forum&amp;lid=" . $link->lid;
                        $forumselect .= "<option value=\"" . $jumperlink . "\">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &gt;&gt; " . htmlspecialchars($linkforum->name) . "</option>\n";
                    }
                }
            }
        }
        $forumselect .= "<option value=\"0\" disabled=\"disabled\">&nbsp;</option>";
    }
    $forumselect .= "</select>
        </fieldset>
    </form>";

    $cache->write($forumselect, $cacheid, 18000); // 5 Stunden Cachezeit

    return $forumselect;
}

function mxbLastPostThread($tid, $postingtime)
{
    global $table_posts, $table_links, $table_threads, $linkthreadstatus;

    $querythread = sql_query("SELECT dateline, author, lastpost, fid FROM $table_threads WHERE tid=" . intval($tid) . "") ;
    $thread = sql_fetch_assoc($querythread);

    if ($postingtime == $thread['lastpost']) {
        $queryposts = sql_query("SELECT dateline, author FROM $table_posts WHERE tid=" . intval($tid) . " ORDER BY dateline DESC limit 0,1") ;
        $post = sql_fetch_assoc($queryposts);
        $lastpost = $post['dateline'] . "|" . $post['author'];
        if ($lastpost == "|") {
            $lastpost = $thread['dateline'] . "|" . $thread['author'];
        }

        sql_query("UPDATE $table_threads SET lastpost='$lastpost' WHERE tid=" . intval($tid));
        if ($linkthreadstatus == 'on') {
            $querylinks = sql_query("SELECT lid FROM $table_links WHERE type='thread' AND fromid=" . intval($tid));
            while ($links = sql_fetch_assoc($querylinks)) {
                sql_query("UPDATE $table_links SET lastpost='$lastpost' WHERE lid='" . intval($links['lid']) . "'");
            }
        }

        mxbLastPostForum($thread['fid'], $postingtime);
    }
}

function mxbLastPostForum($fid, $postingtime)
{
    global $table_links, $table_threads, $table_forums, $linkthreadstatus, $linkforumstatus;

    $queryforums = sql_query("SELECT fup, lastpost FROM $table_forums WHERE fid=" . intval($fid) . "") ;
    $forum = sql_fetch_assoc($queryforums);

    if ($postingtime == $forum['lastpost'] || $postingtime == "checkforum") {
        $querythreads = sql_query("SELECT lastpost FROM $table_threads WHERE fid=" . intval($fid) . " ORDER BY lastpost DESC limit 0,1") ;
        $thread = sql_fetch_assoc($querythreads);
        $querysubforums = sql_query("SELECT lastpost FROM $table_forums WHERE type='sub' AND fup='" . intval($fid) . "' ORDER BY lastpost DESC limit 0,1") ;
        $subforum = sql_fetch_assoc($querysubforums);

        if ($thread['lastpost'] > $subforum['lastpost']) {
            $lastpost = $thread['lastpost'];
        } else {
            $lastpost = $subforum['lastpost'];
        }

        if ($linkthreadstatus == 'on' || $linkforumstatus == 'on') {
            $querylinks = sql_query("SELECT lastpost FROM $table_links WHERE toid=" . intval($fid) . " AND status='on' ORDER BY lastpost DESC limit 0,1") ;
            $link = sql_fetch_assoc($querylinks);
            if ($link['lastpost'] > $lastpost) {
                $lastpost = $link['lastpost'];
            }
        }

        sql_query("UPDATE $table_forums SET lastpost='$lastpost' WHERE fid=" . intval($fid));
        if ($linkforumstatus == 'on') {
            $querylinks = sql_query("SELECT lid, toid FROM $table_links WHERE type='forum' AND fromid=" . intval($fid));
            while ($links = sql_fetch_assoc($querylinks)) {
                sql_query("UPDATE $table_links SET lastpost='$lastpost' WHERE lid='" . intval($links['lid']) . "'");
                mxbLastPostForum($links['toid'], $postingtime);
            }
        }
    } // End-if main
}

function mxbNotifyModerator($fid, $subject, $message, $case)
{
    global $table_forums, $table_members, $user_prefix, $adminemail;
    $time = date("Y-m-d H:i");
    $query = sql_query("SELECT moderator FROM $table_forums WHERE fid=" . intval($fid));
    $modsearch = sql_fetch_object($query);
    if (!is_object($modsearch) || empty($modsearch->moderator)) {
        return false;
    }
    $moderators = preg_split('#\s*,\s*#', trim($modsearch->moderator, ', '));
    if (empty($moderators)) {
        return false;
    }
    $querymods = sql_query("SELECT fm.username, fm.status, u.email, fm.notifyme, fm.notifythread, fm.notifypost, fm.notifyedit, fm.notifydelete, u.uname,  u.user_stat
                        FROM $table_members AS fm
                        LEFT JOIN {$user_prefix}_users AS u
                        ON fm.username = u.uname
                        WHERE fm.username IN ('" . implode("','", $moderators) . "')");
    while ($moderator = sql_fetch_object($querymods)) {
        if ($moderator->notifyme == "ymd" && $moderator->$case == 'yes') {
            if ($moderator->status != 'Administrator' && (empty($moderator->uname) || $moderator->user_stat != 1)) {
                // falls es den Moderator nicht mehr als User gibt >> einfach weiter...
                continue;
            }
            if (!empty($moderator->email)) {
                mxMail($moderator->email, strip_tags($subject), $message);
            }
        }
    }
}

function mxbNotifyAdmin($fid, $subject, $message, $case)
{
    global $table_members, $user_prefix, $adminemail, $sitename;

    $queryadmins = sql_query("SELECT fm.username, u.email, fm.notifythread, fm.notifypost, fm.notifyedit, fm.notifydelete
                    FROM $table_members AS fm
                    LEFT JOIN {$user_prefix}_users AS u
                    ON fm.username = u.uname
                    WHERE status='Administrator' AND notifyme='yad'");

    while ($admins = sql_fetch_object($queryadmins)) {
        if ($admins->$case == 'yes') {
            if ($admins->email) {
                mxMail($admins->email, strip_tags($subject), $message);
            }
        }
    }
}

function mxbColorTime($time)
{
    global $color1, $color2;

    if ($time < 10) {
        $colortime = '<span style="color: ' . $color1 . '">' . $time . '</span>';
    } elseif ($time < 100) {
        $colortime = $time;
    } else {
        $colortime = '<span style="color: ' . $color2 . '"><b>' . $time . '</b></span>';
    }

    return $colortime;
}

function mxbIsOnline($username, $member = false)
{
    global $table_whosonline, $eBoardUser;
    static $users;
    if (isset($users[$username])) {
        return $users[$username];
    }
    if ($username == $eBoardUser['username']) {
        $users[$username] = true;
        return true;
    }
    if (is_object($member)) {
        $past = time() - MX_SETINACTIVE_MINS ;
        if ((!empty($member->user_stat)) && ($member->user_lastvisit >= $past) && ($member->user_lastmod != 'logout') && ($member->user_lastmod != MXB_MODNAME)) {
            $users[$username] = true;
            return true;
        }
        if ((!empty($member->user_stat)) && ($member->user_lastvisit < $past || $member->user_lastmod == 'logout') && ($member->user_lastmod != MXB_MODNAME)) {
            $users[$username] = false;
            return false;
        }
    }

    $queryonline = sql_query("SELECT username FROM $table_whosonline WHERE username='" . substr($username, 0, 25) . "'");
    if ($online = sql_fetch_assoc($queryonline)) {
        $users[$username] = true;
        return true;
    } else {
        $users[$username] = false;
        return false;
    }
}

function mxbStrFilePerms($filename)
{
    if (is_string($filename)) {
        clearstatcache();
        if (is_dir($filename)) {
            $str = "d";
        } else {
            $str = "-";
        }

        $perms = fileperms($filename);
        $perms = $perms &0777;

        $mask = 0700;

        for ($i = 0;$i < 3;$i++) {
            $droits = $perms &$mask;
            if ($i == 0) {
                $droits = $droits >> 6;
            } elseif ($i == 1) {
                $droits = $droits >> 3;
            }

            if ($droits &04) {
                $str .= "r";
            } else {
                $str .= "-";
            }

            if ($droits &02) {
                $str .= "w";
            } else {
                $str .= "-";
            }

            if ($droits &01) {
                $str .= "x";
            } else {
                $str .= "-";
            }

            $mask = $mask >> 3;
        }
    }
    return $str;
}

function mxbChangeFilePerm($file, $mode)
{
    $fichconfigperms = mxbStrFilePerms($file);
    if (!preg_match("/-rw[-|x]rw[-|x]rw[-|x]/i", $fichconfigperms, $parts) && $mode == "unlock") {
        $chmod = @chmod($file, 0666);
        if ($chmod && @file_exists($file)) {
            return true;
        } else {
            return false;
        }
    } elseif (!preg_match("/-rw[-|x]r-[-|x]r-[-|x]/i", $fichconfigperms, $parts) && $mode == "lock") {
        $chmod = @chmod($file, 0644);
        if ($chmod && @file_exists($file)) {
            return true;
        } else {
            return false;
        }
    } else {
        return true;
    }
}

function mxbGetThreadPage($tid, $pid)
{
    global $table_posts, $ppp;

    $counter = 0;
    $replynumber = 0;
    $lastpostquery = sql_query("SELECT pid FROM $table_posts WHERE tid=" . intval($tid) . " ORDER BY dateline");
    while ($searchlastpost = sql_fetch_object($lastpostquery)) {
        $counter++;
        if ($searchlastpost->pid == $pid) {
            $replynumber = $counter;
        }
    }
    return "&amp;page=" . ceil(($replynumber + 1) / $ppp);
}
// // Liste der PostIcons für Post-Formular
function mxbShowPostIcons($posticon)
{
    global $table_smilies, $piconstatus, $maxposticons;
    if (empty($smiles)) {
        $smiles = array();
        $querysmilie = sql_query("SELECT url FROM $table_smilies WHERE type='picon' ORDER BY id");
        while ($smilie = sql_fetch_object($querysmilie)) {
            $smiles[] = $smilie->url;
        }
    }
    foreach($smiles as $i => $url) {
        if ($i >= ($maxposticons)) {
            break;
        }
        $checked = ($url == $posticon) ? ' checked="checked"' : '';
        if ($size = @getimagesize(MXB_BASEMODIMG . '/' . $url)) {
            $icons[] = '<input type="radio" name="posticon" value="' . $url . '"' . $checked . '/><img src="' . MXB_BASEMODIMG . '/' . $url . '" alt="' . $url . '" border="0" ' . $size[3] . '/>';
        }
    }
    if (isset($icons)) {
        $allicons = count($icons);
        if ($allicons > $maxposticons / 2) {
            array_splice($icons, ceil($allicons / 2), 0, '<br/>');
        }
        $icons = implode("\n", $icons);
    } else {
        $piconstatus = 'off';
        $icons = '';
    }
    return $icons;
}

function mxbExitMessage($message, $ebExitMessageHeader = false, $location = '')
{
    global $mxbnavigator;

    pmxDebug::pause();

    if ($ebExitMessageHeader) {
        extract($GLOBALS, EXTR_SKIP);
        $mxbnavigator->reset();
    }
    echo '
    	<div class="alert alert-warning">
    		<p>' . $message . '
            <br />
    		' . _GOBACK . '</p>
    	</div>';
    echo mxbRedirectScript($location);
    include_once(MXB_BASEMODINCLUDE . 'footer.php');
    exit;
}

function mxbMessageScreen($message)
{
    $out = '
        <div class="note align-center">
    		' . $message . '
    	</div>';
    return $out;
}

function mxbRedirectScript($location, $timeout = 2500)
{
    global $mxbnavigator;
    if (empty($location)) {
        return '';
    }
    $mxbnavigator->reset();
    $timeout = intval($timeout);
    if (empty($timeout) || $timeout < 0) {
        $timeout = 2500;
    }
    $location = str_replace('&amp;', '&', $location);
    return '
    <script type="text/javascript">
        /*<![CDATA[*/
        function eboard_redirect() {
            window.location.replace("' . $location . '");
        }
        setTimeout("eboard_redirect();", ' . $timeout . ');
        /*]]>*/
    </script>
    <meta http-equiv="refresh" content="' . ceil($timeout / 1000) . ';url=' . str_replace('&', '&amp;', $location) . '">
    ';
}

function mxbGetJumplink()
{
    global $linkstatus, $linktype, $jumplink, $lid;
    $linkstatus = (!isset($linkstatus) || $linkstatus != 'on') ? 'off' : 'on';
    $jumper = '';
    if ($linkstatus == 'on') {
        if ($linktype == "thread") {
            $jumper = "&amp;linkstatus=on&amp;jumplink=$jumplink&amp;linktype=thread&amp;lid=$lid";
        } else {
            $jumper = "&amp;linkstatus=on&amp;jumplink=$jumplink&amp;linktype=forum&amp;lid=$lid";
        }
    }
    return $jumper;
}

/**
 * der Bildname genügt, dann guggt er:
 * - im eingestellten imageset in der eingestellten Sprache
 * - im eingestellten imageset in der Standard-Sprache
 * - im Standardimageset (grau) in der eingestellten Sprache
 * - im Standardimageset (grau) in der Standard-Sprache
 * - im eingestellten imageset, ohne Sprache
 * - im Standardimageset (grau), ohne Sprache
 * - im (alten) imageordner
 * - wenn alles erfolglos, gibt es nur den alt-Text aus
 *
 * $imagename = wie der name sagt, ohne Pfade
 * $alternate = der Alternativtext
 * $search_in_language = suche in den Sprachen übergehen
 * $only_source = nicht den kompletten imagetag ausgeben, sondern nur den Pfad
 */
function mxbGetImage($imagename, $alternate = '', $search_in_language = false, $only_source = false, $style = '')
{
    static $allimages;
    if (isset($allimages[$imagename]) && !$only_source) {
        return $allimages[$imagename];
    }

    global $langfile, $imageset, $imageset_default;
    $search = array();
    if ($search_in_language) {
        $lang = (preg_match('#^german#i', $langfile)) ? 'german' : $langfile;
        $lang_alternate = ($lang == 'german') ? 'english' :'german' ;
        // vollständig vorhanden?
        $search[] = 'imagesets/' . $imageset . '/' . $lang;
        // im Standardimageset vorhanden?
        $search[] = 'imagesets/' . $imageset_default . '/' . $lang;
        // nur in Standardsprache vorhanden?
        $search[] = 'imagesets/' . $imageset . '/' . $lang_alternate;
        // nur im Standardimageset und in Standardsprache vorhanden?
        $search[] = 'imagesets/' . $imageset_default . '/' . $lang_alternate;
    }
    // vollständig vorhanden?
    $search[] = 'imagesets/' . $imageset;
    // im Standardimageset vorhanden?
    $search[] = 'imagesets/' . $imageset_default;
    // dann halt nur im image-Ordner?
    $search[] = 'images';

    foreach ($search as $path) {
        $img = MXB_ROOTMOD . $path . '/' . $imagename;
        if ($only_source && file_exists($img)) {
            return $img;
        }
        if (file_exists($img) && ($size = @getimagesize($img))) {
            if (empty($style)) {
                $style .= ' border: none;';
            }
            $allimages[$imagename] = mxbCreateImage($img, $size[0], $size[1], $style, $alternate);

            return $allimages[$imagename];
        }
    }
    return $alternate;
}

function mxbCreateImage($image, $width, $heigth, $style = '', $alt = '')
{
    return '<img src="' . $image . '" style="width: ' . $width . 'px; height: ' . $heigth . 'px; ' . $style . '"' . ((empty($alt)) ? ' alt="' . pathinfo($image, PATHINFO_BASENAME) . '"' : ' title="' . $alt . '" alt="' . $alt . '"') . '/>';
}

function mxb_add_signatur($signatur)
{
    global $sigimgXxXauth, $sightml, $sigbbcode, $sigbbcode, $sigbbcode, $sigimgwidth, $sigimgheight;
    // mxDebugFuncVars($sigimgXxXauth, $sightml, $sigbbcode, $sigbbcode, $sigbbcode, $sigimgwidth, $sigimgheight);
    $signatur = mxbPostify($signatur, $sightml, $sigbbcode, $sigbbcode, $sigbbcode);
    $signatur = preg_replace('#&lt;br\s*/?&gt;#i', '<br/>', $signatur);

    if ($sigimgXxXauth == 'on') {
        // Bildgrösse eingeschränkt
        return '<div class="mxb-signatur imgshrink">' . $signatur . '</div>';
    }
    return '<div class="mxb-signatur">' . $signatur . '</div>';
}

function mxbThemeFallback($var = array())
{
    $defaults = array('name' => 'anewtheme1',
        'bgcolor' => '#ffffff',
        'altbg1' => '#dededf',
        'altbg2' => '#eeeeee',
        'link' => '#333399',
        'bordercolor' => '#9999ff',
        'header' => '',
        'bgcolheader' => '#9999ff',
        'headertext' => '',
        'bgcolheadertext' => '#ffffff',
        'top' => '#eeeeee',
        'catcolor' => '#dcdcde',
        'tabletext' => '#000000',
        'text' => '#000000',
        'borderwidth' => '1',
        'tablewidth' => '97%',
        'tablespace' => '6',
        'font' => 'Verdana',
        'fontsize' => '12px',
        'altfont' => 'sans-serif',
        'altfontsize' => '10px',
        'replyimg' => '',
        'newtopicimg' => '',
        'boardimg' => '',
        'postscol' => '',
        'color1' => 'red',
        'color2' => 'blue',
        'imageset' => 'grau',
        );
	//if (!is_array($var)) $var=array();
    $var = array_merge($defaults, $var);

    if (empty($var['header'])) {
        $var['header'] = $var['bgcolheader'];
    } else if (empty($var['bgcolheader'])) {
        $var['bgcolheader'] = $var['header'];
    }

    if (empty($var['headertext'])) {
        $var['headertext'] = $var['bgcolheadertext'];
    } else if (empty($var['bgcolheadertext'])) {
        $var['bgcolheadertext'] = $var['headertext'];
    }

    foreach ($defaults as $key => $value) {
        // wenn leer oder nicht vorhanden, Standardwert einsetzen
        if (empty($var[$key])) {
            $var[$key] = $value;
        }
    }

    if (intval($var['borderwidth']) < 0) {
        $var['borderwidth'] = '1';
    }
    if (intval($var['tablespace']) < 0) {
        $var['tablespace'] = '6';
    }

    return $var;
}

function mxbThemeCompressCss($contents)
{
    /* Kommentare und unnoetige Leerzeichen entfernen */
    $contents = preg_replace('#(\/\*[^{}]*\*\/)|[[:cntrl:]]#', ' ', $contents);
    $contents = preg_replace('#\s{2,}#', ' ', $contents);
    $s = array('}' , "\n ", ": ", "; ", "{ ", " {", "} ", " }", ", ", ";@", "}\n}");
    $r = array("}\n", "\n" , ":" , ";" , "{" , "{" , "}" , "}" , "," , ";\n@", "}}");
    $contents = str_replace($s, $r, $contents);
    return $contents;
}

class mxb_navigation {
    private $_items = array();

    public function __construct()
    {
        $this->add(MXB_BM_INDEX0, _TEXTINDEX);
    }

    public function add($link, $text)
    {
        $this->_items[$link] = strip_tags($text);
    }

    public function get()
    {
        $out = array();
        foreach ($this->_items as $link => $text) {
            if ((!$link) || is_numeric($link)) {
                $out[] = $text;
            } else {
                $out[] = '<a href="' . $link . '">' . htmlspecialchars($text) . '</a>';
            }
        }
        return implode(' &raquo; ', $out);
    }

    public function reset()
    {
        $this->__construct();
    }

    public function get_pagetitle()
    {
        global $bbname;
        $out = array();
        $tmp = $this->_items;
        array_shift($tmp); // das erste abschneiden...
        foreach ($tmp as $link => $text) {
            $out[] = $text;
        }

        if ($out) {
            return $bbname . ' - ' . htmlspecialchars(implode(' > ', $out));
        }

        return $bbname;
    }

    public function __toString()
    {
        return $this->get();
    }
}

?>
