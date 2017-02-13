<?php
/**
 * mxBoard, pragmaMx Module
 * Copyright by pragmaMx Developer Team - http://www.pragmamx.org
 *
 * $Author: tora60 $
 * $Revision: 1.27 $
 * $Date: 2011/10/13 22:46:54 $
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

/* --------- Konfiguration fuer den Block ----------------------------------- */

/* here you have to set you Module-Name */
$module_name = 'Discussion';

/* Here you can specify the name of a forum (only one!) not been showed in the center-block
   e.g. "Testforum" or something else. Leave blank if you don´t want to use it. */
$dontshowthisforum = '';

/* the same as an array with forum ID's
   example: $hideforums = array(22,31,45,1); */
$hideforums = array();

/* how many new-posts should be shown in this block? */
$posts = 8;

/* show these columns in the list: */
// the thread-icon
$showicon = false;
// the author of the thread */
$showauthor = false;
// views
$showviews = true;
// replies
$showreplies = true;
// last post, date & author
$showlastpost = true;

/* more usefull links to parts of your board */
$showusefulllinks = false;

/* some statistics of your board (only if stats are enabled)*/
$showboardstats = false;

/* Should the "Who is online" been shown also into the center-block?
   Set this value to true or false */
$showonline = false;

/* "Who is online" shows only users in forum, or on the entire web page.
   true = complete site, false = only in forum */
$showonline_entiresite = true;

/* show priority level/color */
$showpriority = false;

/* a small grafic behind the subject, which shows the new-post status */
$shownewgrafic = true;

/* default thread-icon, if empty... */
$default_icon = 'folder.gif';

/* the new-post status grafic */
$newimage = 'new.png';

/* --------- Ende der Konfiguration ----------------------------------------- */

/* check ob die verwendete pragmaMx Version korrekt ist */
if (!isset($block['settings'])) {
    return $content = 'Sorry, pragmaMx-Version >= 1.12 is required for this mxBoard-Block.';
}

extract($block['settings'], EXTR_OVERWRITE);

global $prefix, $user_prefix;

if (!defined('MXB_INIT')) define('MXB_INIT', true);

$path = PMX_MODULES_DIR . DS . $module_name . DS;

/* Load the $module_name-settings */
if (!file_exists($path . 'settings.php')) {
    if (MX_IS_ADMIN) {
        $content = 'ERROR: <br />mxBoard-settings not found in ' . basename(__file__);
    }
    return;
}
include($path . 'settings.php');
// Here the language is set for the center-block
if (!defined('MXB_LANGFILE_INCLUDED')) {
    mxGetLangfile($module_name);
}

if ($shownewgrafic) {
    $newimage = mxCreateImage('modules/' . $module_name . '/images/' . $newimage, 'new', array('align' => 'right'));
}

$newpostnum = 0;
$status = '';
$ppp = intval($postperpage);
$lastvisitdate = time();
$thisuser = '';

if (MX_IS_USER || MX_IS_ADMIN) {
    if (MX_IS_USER) {
        $userarray = mxGetUserData();
        $thisuser = $userarray['uname'];
    }

    $memberquery = sql_query("SELECT status, lastvisit, lastvisitdate, lastvisitstore, keeplastvisit, ppp FROM $table_members WHERE username='$thisuser'");
    if ($member = sql_fetch_object($memberquery)) {
        $status = $member->status;
        if ($member->lastvisitstore < (time() - (3600 * $member->keeplastvisit))) {
            // für den Fall das wir in das Forum kommen und "date" und "store" ungleich sind
            // wollen wir natürlich nicht, das das "date" auf time gesetzt wird. Daher Bed. if
            if ($member->lastvisit < (time() - 900)) {
                $lastvisitdate = $member->lastvisit;
                // wenn wir im Forum rumkrauchen sollen die Umschläge ja auch
                // irgendwann verschwinden, daher also diese Bedingung
            } else {
                $lastvisitdate = time();
            }
        } else {
            $lastvisitdate = $member->lastvisitdate;
        }

        $querythreads = sql_query("SELECT COUNT(tid) as posts FROM $table_threads WHERE dateline >= '$lastvisitdate'");
        $threadcounter = sql_fetch_object($querythreads);
        $queryposts = sql_query("SELECT COUNT(pid) as replies FROM $table_posts WHERE dateline >= '$lastvisitdate'");
        $postcounter = sql_fetch_object($queryposts);

        $newpostnum = $threadcounter->posts + $postcounter->replies;
        $ppp = $member->ppp;
    }
}

$boardstats = '';
if ($showboardstats) {
    $query = sql_query("SELECT COUNT(tid) as nbsites, SUM(views) as totalus FROM $table_threads");
    $row = sql_fetch_object($query);
    $topicspop = $row->nbsites;
    $threadsall = $row->totalus;
    $query = sql_query("SELECT COUNT(pid) as nbposts FROM $table_posts");
    $row = sql_fetch_object($query);
    $postpop = $row->nbposts + $topicspop;
    $query = sql_query("SELECT COUNT(uid) as xusers FROM $table_members");
    $row = sql_fetch_object($query);
    $members = $row->xusers;

    $mt = ($memliststatus == 'on' && $memlistanonymousstatus == 'on') ? '<a href="modules.php?name=' . $module_name . '&amp;file=members.list">' . _TEXTMEMBERS . '</a>' : _TEXTMEMBERS;
    $boardstats = '
<p class="txtcenter clear">
' . _TEXTTOPICS . ' <strong>' . $topicspop . '</strong> |
' . _TEXTPOSTS . ' <strong>' . $postpop . '</strong> |
' . _TEXTVIEWS . ' <strong>' . $threadsall . '</strong> |
' . $mt . ': <strong>' . $members . '</strong>
</p>';
}

$hide = array();
// versteckte Foren, versteckt fuer alle User
if (!empty($hideforums)) {
    $hide[] = " t.fid NOT IN ('" . implode("','", $hideforums) . "')";
}
// versteckte Foren, versteckt fuer alle User
if (!empty($dontshowthisforum)) {
    $hide[] = " f.name <> '" . mxAddSlashesForSQL($dontshowthisforum) . "'";
}
// private Foren, von Anonymen verstecken
if ($hideprivate == 'on' && !MX_IS_USER && !MX_IS_ADMIN && $status != 'Super Moderator' && $status != 'Administrator') {
    // Forum fuer Alle
    $hide[] = "(f.private IS NULL OR f.private = '')";
} else
    // verstecken fuer User
    if ($hideprivate == 'on' && MX_IS_USER && !MX_IS_ADMIN && $status != 'Super Moderator' && $status != 'Administrator') {
        // Forum nur fuer User
        // oder der User in der Accessliste
        // oder der User ist Moderator im Form
        $hide[] = "(
			(f.private IS NULL OR f.private = '' OR f.private = 'user')
			OR ( INSTR(REPLACE(CONCAT(',',LCASE(f.userlist),','), ', ', ',') , '," . mxAddSlashesForSQL(strtolower($thisuser)) . ",') )
			OR ( INSTR(REPLACE(CONCAT(',',LCASE(f.moderator),','), ', ', ',') , '," . mxAddSlashesForSQL(strtolower($thisuser)) . ",') )
			)";
        // leere Userliste
        // oder der User in der Accessliste
        // oder der User ist Moderator im Form
        $hide[] = "(
			( f.userlist IS NULL OR f.userlist = '' )
			OR ( INSTR(REPLACE(CONCAT(',',LCASE(f.userlist),','), ', ', ',') , '," . mxAddSlashesForSQL(strtolower($thisuser)) . ",') )
			OR ( INSTR(REPLACE(CONCAT(',',LCASE(f.moderator),','), ', ', ',') , '," . mxAddSlashesForSQL(strtolower($thisuser)) . ",') )
			)";
    }
    if ($hide) {
        $hideforums = ' WHERE ' . implode(' AND ', $hide);
    } else {
        $hideforums = '';
    }
    $query = sql_query("
		SELECT t.tid, t.views, t.subject, t.lastpost, t.dateline, t.replies, t.author, t.icon, t.fid , f.name, f.private, f.userlist,
      CONVERT(t.lastpost, UNSIGNED) AS lastpost_at,
      SUBSTRING_INDEX(t.lastpost, '|',-1) AS lastpost_by,
      l.username AS lastpost_by_true,
      u.username AS author_true
		FROM $table_threads as t
		INNER JOIN $table_forums AS f ON t.fid = f.fid
      LEFT JOIN $table_members as l ON l.username = SUBSTRING_INDEX(t.lastpost, '|',-1)
      LEFT JOIN $table_members as u ON u.username = t.author
		$hideforums
		ORDER BY t.lastpost Desc
		LIMIT 0, $posts");

    $content = '';
    $content .= '<ul class="unstyled pa0 w100">';
    $viewlast = '';
    $i = 0;
    $class == '';
    while ($event = sql_fetch_assoc($query)) {
        if (empty($event['replies'])) {
            $viewthreadpage = '#pid0';
        } else {
            $searchquery = sql_query("SELECT pid FROM $table_posts WHERE tid='" . $event['tid'] . "' AND dateline >= '$lastvisitdate' ORDER BY dateline LIMIT 0,1");
            $lasttpost = sql_fetch_object($searchquery);
            if (empty($lasttpost->pid)) {
                $searchquery = sql_query("SELECT pid FROM $table_posts WHERE tid='" . $event['tid'] . "' ORDER BY dateline DESC LIMIT 0,1");
                $lasttpost = sql_fetch_object($searchquery);
            }
            $viewthreadpage = '&amp;page=' . ceil(($event['replies'] + 1) / $ppp) . '#pid' . $lasttpost->pid;
        }

        $dateA = mx_strftime(_SHORTDATESTRING . ' %H:%M', $event['lastpost_at']);

        if ($event['lastpost_by_true']) {
            $lastauthor = '<a href="modules.php?name=' . $module_name . '&amp;file=member&amp;action=viewpro&amp;member=' . $event['lastpost_by_true'] . '">' . $event['lastpost_by_true'] . '</a>';
        } else {
            $lastauthor = $event['lastpost_by'];
        }

        if ($event['author_true']) {
            $author1 = '<a href="modules.php?name=' . $module_name . '&amp;file=member&amp;action=viewpro&amp;member=' . $event['author_true'] . '">' . $event['author_true'] . '</a>';
        } else {
            $author1 = $event['author'];
        }

        if ($shownewgrafic && $event['lastpost_at'] >= $lastvisitdate) {
            $newpost = $newimage;
        } else {
            $newpost = '';
        }

        $subject = ($showpriority) ? stripslashes($event['subject']) : strip_tags(stripslashes($event['subject']));
        $class = ($class == '') ? 'bgcolor3' : '';

        $viewlast .= '
            <li class="ma0 ' . $class . ' clearfix" style="padding:5px">';
        $viewlast .= '
            <span class="topic fl w60 tiny-w100">';
            $icon ='';
            if ($showicon) {
                $icon = mxCreateImage('modules/' . $module_name . '/images/' . ((empty($event['icon'])) ? $default_icon : $event['icon']), '');
            }
            
             $viewlast .= $icon . $newpost . '<a class="stronger" href="modules.php?name=' . $module_name . '&amp;file=viewthread&amp;tid=' . $event['tid'] . $viewthreadpage . '">' . $subject . '</a>
                <br />
                <em class="small"><a href="modules.php?name=' . $module_name . '&amp;file=forumdisplay&amp;fid=' . $event['fid'] . '">' . $event['name'] . '</a></em>
            </span>';

        if ($showauthor) {
            $viewlast .= $author1;
        }
        if ($showreplies) {
            $viewlast .= '
                <span class="fl w10 txtcenter bigger">
                    ' . $event['replies'] . '
                    <br />
                    <em class="smaller">' . trim(_TEXTREPLIES, ':') . '</em>                   
                </span>';
        }
        if ($showviews) {
            $viewlast .= '
                <span class="fl w10 txtcenter bigger">
                    ' . $event['views'] . '
                    <br />
                    <em class="smaller">' . trim(_TEXTVIEWS, ':') . '</em>
                </span>';
        }
        if ($showlastpost) {
            $viewlast .= '
                <span class="fr w20 small txtright">
                    Le ' . $dateA . '
                    <br />                   
                    <em>' . $lastauthor . '</em>
                </span>';
        }
        $viewlast .= '
            </li>';
    }

    if ($viewlast) {
        $content .= $viewlast;
        $content .= '</ul>';
    }

    $content .= $boardstats;

    if ($whosonlinestatus == 'on' && $showonline) {
        $past = intval(time() - (MX_SETINACTIVE_MINS));
        if (!mxSessionGetVar('mxbPast') || mxSessionGetVar('mxbPast') < $past) {
            sql_query("DELETE FROM $table_whosonline WHERE time<'$past' AND username <> 'xguest123' AND ip <> '" . MX_REMOTE_ADDR . "'");
            mxSessionSetVar('mxbPast', time());
        }

        $guestcount = 0;
        $membercount = 0;
        if ($showonline_entiresite) {
            // Alle Gaeste ermitteln
            $result = sql_query("SELECT Count(ip) FROM ${prefix}_visitors
                        WHERE time>" . $past . " AND uid=0;");
            list($guestcount) = sql_fetch_row($result);
            // Alle Mitglieder ermitteln
            $result = sql_query("SELECT uname FROM ${user_prefix}_users
                        WHERE ((user_lastvisit >= " . $past . ") AND (user_stat=1) AND (user_lastmod<>'logout'))");
        } else {
            // Alle Gaeste ermitteln
            $result = sql_query("SELECT Count(ip) FROM ${prefix}_visitors
                        WHERE time>" . $past . " AND uid=0 AND module='" . mxAddSlashesForSQL($module_name) . "'");
            list($guestcount) = sql_fetch_row($result);
            // Alle Mitglieder ermitteln
            $result = sql_query("SELECT uname FROM ${user_prefix}_users
                        WHERE ((user_lastvisit >= " . $past . ") AND (user_stat=1) AND (user_lastmod='" . mxAddSlashesForSQL($module_name) . "'))");
        } while (list($uname2) = sql_fetch_row($result)) {
            $memtally[$uname2] = '<a href="modules.php?name=' . $module_name . '&amp;file=member&amp;action=viewpro&amp;member=' . $uname2 . '">' . $uname2 . '</a>';
        }
        if (isset($memtally)) {
            $membercount = count($memtally);
            $memtally = implode(', ', $memtally);
        }

        $content .= '<p class="txtcenter">';
        $content .= '<a href="modules.php?name=' . $module_name . '&amp;file=misc&amp;action=online" title="' . _WHOSONLINE . '">' . _WHOSONLINE . '</a>';
        $content .= ': ' . _WHOSONLINE1 . ' ' . $guestcount . ' ' . _WHOSONLINE2 . ' ' . $membercount . ' ' . _WHOSONLINE3 . '';
        $content .= '</p>';
        if (!empty($memtally)) {
            $content .= '<p>' . $memtally . '</p>';
        }
    }

    if ($showusefulllinks) {
        $links = array('<a href="modules.php?name=' . $module_name . '">' . _TEXTINDEX . '</a>');

        if ($newpostnum >= 0) {
            $links[] = '<a href="modules.php?name=' . $module_name . '&amp;file=messslv"><b>' . $newpostnum . '</b> ' . _TEXTMESSSLV . '</a>';
        }

        if ($statspage == 'on') {
            $links[] = '<a href="modules.php?name=' . $module_name . '&amp;file=stats">' . _TEXTSTATS . '</a>';
        }

        if ($searchstatus == 'on') {
            $links[] = '<a href="modules.php?name=' . $module_name . '&amp;file=search">' . _TEXTSEARCH . '</a>';
        }

        $content .= '<p class="align-center nowrap">' . implode('&nbsp; | &nbsp;', $links) . '</p>';
    }

    ?>
