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

include_once(dirname(__FILE__) . DS . 'includes' . DS . 'header.php');

if (isset($_REQUEST['qv_submit']) && !mxbIsAnonymous()) {
    $lastvisitdate = time();
    $lastvisitstore = $lastvisitdate;
    sql_query("UPDATE $table_members SET lastvisitdate='$lastvisitdate', lastvisitstore='$lastvisitstore' WHERE username='" . substr($eBoardUser['username'], 0, 25) . "'");
}

$searchfrom = intval($lastvisitdate);

$view = (empty($_REQUEST['view'])) ? 0 : intval($_REQUEST['view']);
switch ($view) {
    case 1:
        $queryroulethread = "ORDER BY CONVERT(lastpost, UNSIGNED) Desc LIMIT 0,50";
        $queryroulepost = "LIMIT 0";
        $miscaction = _TEXTLAST50THREADS;
        break;
    case 2:
        $queryroulethread = "LIMIT 0";
        $queryroulepost = "ORDER BY dateline Desc LIMIT 0,50";
        $miscaction = _TEXTLAST50POSTS;
        break;
    case 3:
        $queryroulethread = "WHERE CONVERT(lastpost, UNSIGNED) >= " . $searchfrom;
        $queryroulepost = "LIMIT 0";
        $miscaction = _TEXTLASTTHREADS;
        break;
    case 4:
        $searchfrom = 86400;
        $searchfrom = time() - ($searchfrom * $dureemessotd);
        $queryroulethread = "WHERE dateline >= '" . $searchfrom . "' LIMIT 0, 200";
        $queryroulepost = "WHERE dateline >= '" . $searchfrom . "' ORDER BY dateline Desc LIMIT 0, 200";
        $miscaction = _TEXTMESSOTD;
        break;
    default:
        $queryrouleboard = (empty($_REQUEST['board'])) ? '' : ' AND fid=' . intval($_REQUEST['board']);
        $queryroulethread = "WHERE dateline >= '" . $searchfrom . "'" . $queryrouleboard;
        $queryroulepost = "WHERE dateline >= '" . $searchfrom . "'" . $queryrouleboard;
        $miscaction = _TEXTMESSSLVBIG;
}

$mxbnavigator->add(false, $miscaction);

$sql1 = "SELECT dateline, subject, fid, tid, replies, author, m.username AS author_true, icon, CONVERT(lastpost, UNSIGNED) AS lastpost_at, SUBSTRING_INDEX(lastpost, '|',-1) AS lastpost_by, l.username AS lastpost_by_true
          FROM $table_threads AS t
            LEFT JOIN $table_members as l ON l.username = SUBSTRING_INDEX(t.lastpost, '|',-1)
            LEFT JOIN $table_members as m ON m.username = t.author
          $queryroulethread";
$query1 = sql_query($sql1);

$sql2 = "SELECT dateline, pid, fid, tid, author, m.username AS author_true, icon
          FROM $table_posts AS p
            LEFT JOIN $table_members as m ON m.username = p.author
          $queryroulepost";
$query2 = sql_query($sql2);

while ($post = sql_fetch_object($query2)) {
    if (isset($tabtemp[$post->tid])) {
        continue;
    }
    // Query-Cache für Foren
    if (empty($forumcache[$post->fid])) {
        $forumquery = sql_query("SELECT type, name, moderator, private, userlist, fup
        												FROM $table_forums
        												WHERE fid=" . intval($post->fid) . "
        												AND status='on'");
        $forum = sql_fetch_object($forumquery);
        $forumcache[$post->fid] = $forum;
    } else {
        $forum = $forumcache[$post->fid];
    }

    if (!empty($forum)) {
        $date = date($dateformat, (int)$post->dateline);
        $time = date($timecode, (int)$post->dateline);
        $poston = "$date " . _TEXTAT . " $time";
        // Autorisations
        $authToDisplay = false;
        // Falls keine Zugangsbeschraenkung, alles ok
        if (empty($forum->private) && empty($forum->userlist)) {
            $authToDisplay = true;
        }
        // Falls Eintraege vorhanden, guck nach
        elseif (!empty($forum->private) || !empty($forum->userlist)) {
            if (isTrusted($forum)) {
                $authToDisplay = true;
            } else {
                $authToDisplay = false;
            }
        }
        // Falls Sub-Forum, guck ob Main-Forum Zugangsbeschraenkungen hat
        elseif ($authToDisplay && $forum->type == 'sub' && !empty($forum->fup)) {
            $mainforumquery = sql_query("SELECT moderator, private, userlist FROM $table_forums WHERE fid=" . intval($forum->fup) . " AND status='on'");
            $mainforum = sql_fetch_object($mainforumquery);
            if (isTrusted($mainforum)) {
                $authToDisplay = true;
            } else {
                $authToDisplay = false;
            }
        }

        if ($authToDisplay) {
            // Query-Cache fuer Threads
            if (empty($threadcache[$post->tid])) {
                $infoquery = sql_query("SELECT subject, tid, replies
                												FROM $table_threads
                												WHERE tid=" . intval($post->tid));
                $threadinfo = sql_fetch_object($infoquery);
                $threadinfo->subject = stripslashes($threadinfo->subject);
                $threadcache[$post->tid] = $threadinfo;
            } else {
                $threadinfo = $threadcache[$post->tid];
            }

            if ($ppp < ($threadinfo->replies + 1)) {
                $pid = (empty($post->pid)) ? 0 : $post->pid;
                $viewthreadpage = mxbGetThreadPage($post->tid, $pid);
            } else {
                $viewthreadpage = '';
            }

            $tabtemp[$threadinfo->tid] = array("dateline" => $post->dateline,
                "fid" => $post->fid,
                "icon" => $post->icon,
                "forumname" => $forum->name,
                "subject" => $threadinfo->subject,
                "pid" => $post->pid,
                "tid" => $threadinfo->tid . $viewthreadpage,
                "author" => mxb_link2profile($post->author_true, $post->author),
                "poston" => $poston,
                "textpost" => _TEXTREPLY);
        }
    } # Ende if $forum
    $forum = '';
}
// Ende while $post
while ($thread = sql_fetch_object($query1)) {
    if (isset($tabtemp[$thread->tid])) {
        continue;
    }
    // Query-Cache für Foren
    if (empty($forumcache[$thread->fid])) {
        $forumquery = sql_query("SELECT type, name, moderator, private, userlist, fup
        												FROM $table_forums
        												WHERE fid=" . intval($thread->fid) . "
        												AND status='on'");
        $forum = sql_fetch_object($forumquery);
        $forumcache[$thread->fid] = $forum;
    } else {
        $forum = $forumcache[$thread->fid];
    }

    if (!empty($forum)) {
        if ($view == 1 || $view == 3 || $view == 4) {
            $date = date($dateformat, $thread->lastpost_at);
            $time = date($timecode, $thread->lastpost_at);
            $postontime = $thread->lastpost_at;
            $lastposter = _TEXTBY . "&nbsp;" . mxb_link2profile($thread->lastpost_by_true, $thread->lastpost_by);
        } else {
            $date = date($dateformat, (int)$thread->dateline);
            $time = date($timecode, (int)$thread->dateline);
            $postontime = $thread->dateline;
            $lastposter = '';
        }
        $poston = $date . " " . _TEXTAT . " " . $time . " " . $lastposter;
        $thread->subject = stripslashes($thread->subject);
        // Autorisations
        $authToDisplay = false;
        // Falls keine Zugangsbeschraenkung, alles ok
        if (empty($forum->private) && empty($forum->userlist)) {
            $authToDisplay = true;
        }
        // Falls Eintraege vorhanden, guck nach
        elseif (!empty($forum->private) || !empty($forum->userlist)) {
            if (isTrusted($forum)) {
                $authToDisplay = true;
            } else {
                $authToDisplay = false;
            }
        }
        // Falls Sub-Forum, guck ob Main-Forum Zugangsbeschraenkungen hat
        elseif ($authToDisplay && $forum->type == 'sub' && !empty($forum->fup)) {
            $mainforumquery = sql_query("SELECT moderator, private, userlist
            														FROM $table_forums
            														WHERE fid=" . intval($forum->fup) . "
            														AND status='on'");
            $mainforum = sql_fetch_object($mainforumquery);
            if (isTrusted($mainforum)) {
                $authToDisplay = true;
            } else {
                $authToDisplay = false;
            }
        }

        if ($authToDisplay) {
            if ($view == 1 || $view == 3 || $view == 4) {
                if ($thread->dateline < $lastvisitdate) {
                    $query = sql_query("SELECT pid
                    										FROM $table_posts
                    										WHERE tid=" . intval($thread->tid) . "
                    										AND dateline >='" . $searchfrom . "'
                    										ORDER BY dateline LIMIT 0,1");
                    $lastpost = sql_fetch_object($query);

                    if ($ppp < $thread->replies) {
                        $pid = (empty($lastpost->pid)) ? 0 : $lastpost->pid;
                        $viewthreadpage = mxbGetThreadPage($thread->tid, $pid);
                    } else {
                        $viewthreadpage = '';
                    }
                } else {
                    $viewthreadpage = '';
                    $lastpost = '';
                }
            } else {
                $lastpost = '';
                $viewthreadpage = '';
            }

            $tabtemp[$thread->tid] = array("dateline" => $postontime,
                "fid" => $thread->fid,
                "icon" => $thread->icon,
                "forumname" => $forum->name,
                "subject" => $thread->subject,
                "tid" => $thread->tid . $viewthreadpage,
                "pid" => ((isset($lastpost->pid)) ? $lastpost->pid : 0),
                "author" => mxb_link2profile($thread->author_true, $thread->author),
                "poston" => $poston,
                "textpost" => _TEXTTOPIC
                );
        }
    } # Ende if $forum
    $forum = '';
}
// Ende while $thread
$tabtemp = (isset($tabtemp)) ? $tabtemp : array();
if ($view != "1" && $view != "2") {
    usort($tabtemp, "cmp");
}

$resultnumber = count($tabtemp);
// refresch

/* Template initialisieren */
$template = load_class('Template');
$template->init_path(__FILE__);

$template->miscaction = $miscaction;
$template->resultnumber = $resultnumber;
$template->tabtemp = $tabtemp;
$template->display('messslv.html');    

include_once(MXB_BASEMODINCLUDE . 'footer.php');

function changeMSLVview ()
{
    global $view;
    if (empty($view) || !is_numeric($view)) {
        $view = 0;
    }
     $forumselect = "
    	<fieldset class=\"display-options\">
				<label for=\"view\" accesskey=\"v\">" . _TEXTOPTIONS . "</label>
				    <select name=\"view\" id=\"view\" onchange=\"javascript:qvsubmit('view')\">";
				    $forumselect .= '<option value="4" ' . (($view == 4) ? ' selected="selected" class="current"' : '') . '>' . _TEXTMESSOTD . "</option>\n";
				    $forumselect .= '<option value="0" ' . (($view == 0) ? ' selected="selected" class="current"' : '') . '>' . _TEXTMESSSLVBIG . "</option>\n";
				    $forumselect .= '<option value="1" ' . (($view == 1) ? ' selected="selected" class="current"' : '') . '>' . _TEXTLAST50THREADS . "</option>\n";
				    $forumselect .= '<option value="2" ' . (($view == 2) ? ' selected="selected" class="current"' : '') . '>' . _TEXTLAST50POSTS . "</option>\n";
				    $forumselect .= '<option value="3" ' . (($view == 3) ? ' selected="selected" class="current"' : '') . '>' . _TEXTLASTTHREADS . "</option>\n";
				    $forumselect .= '</select>&nbsp;';
    				$forumselect .= '<input type="submit" name="qv_submit" class="button2" value="' . _TEXTREFRESH . '">
    	</fieldset>';
    return $forumselect;
}

function cmp ($a, $b)
{
    return ($a["dateline"] < $b["dateline"]);
}

?>