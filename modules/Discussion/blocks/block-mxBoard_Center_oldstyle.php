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

/* --------- Konfiguration fuer den Block ----------------------------------- */
// here you have to set you Module-Name
$ModName = basename(dirname(__DIR__));
// Here you can specify the name of a forum (only one!) not been showed in the center-block
// e.g. "Testforum" or something else. Leave blank if you don't want to use it.
$dontshowthisforum = '';
// the same as an array with forum ID's
// / example: $hideforums = array(22,31,45,1);
$hideforums = array();
// how many new-posts should be shown in this block?
$posts = 5;
// show these columns in the list:
// the thread-icon
$showicon = true;
// the author of the thread
$showauthor = true;
// views
$showviews = true;
// replies
$showreplies = true;
// last post, date & author
$showlastpost = true;
// Should the "Who is online" been shown also into the center-block?
// Set this value to true or false
$showonlinestatus = true;
// "Who is online" shows only users in forum, or on the entire web page.
// true = complete site, false = only in forum
$mxbshowonlineentiresite = true;
// show priority level/color
$showpriority = false;
// a small grafic behind the subject, which shows the new-post status
$shownewgrafic = true;
// default thread-icon, if empty...
$default_icon = 'folder.gif';
// the new-post status grafic
$newimage = 'new.png';

/* --------- Ende der Konfiguration ----------------------------------------- */

/* check ob die verwendete pragmaMx Version korrekt ist */
if (!isset($block['settings'])) {
    return $content = 'Sorry, pragmaMx-Version >= 2.0 is required for this mxBoard-Block.';
}

extract($block['settings'], EXTR_OVERWRITE);

global $prefix, $user_prefix;

if (!defined('MXB_INIT')) define('MXB_INIT', true);

$path = PMX_MODULES_DIR . DS . $ModName . DS;

/* Load the $ModName-settings */
if (!file_exists($path . 'settings.php')) {
    if (MX_IS_ADMIN) {
        $content = 'ERROR: <br />mxBoard-settings not found in ' . basename(__file__);
    }
    return;
}
include($path . 'settings.php');
// Here the language is set for the center-block
if (!defined('MXB_LANGFILE_INCLUDED')) {
    // mxGetLangfile($ModName);
}
// theme-stuff
if (!function_exists('mxbThemeFallback')) {
    include_once($path . 'includes' . DS . 'functions2.php');
}
$query = sql_query("SELECT * FROM $table_themes WHERE name='" . substr($XFtheme, 0, 30) . "'");
if ($themevars = sql_fetch_assoc($query)) {
    $themevars['bgcolheader'] = $themevars['header'];
    $themevars['bgcolheadertext'] = $themevars['headertext'];
    $themevars['imageset'] = $themevars['replyimg'];
    unset($themevars['name'], $themevars['header'], $themevars['headertext'], $themevars['replyimg']);
    extract(mxbThemeFallback($themevars), EXTR_OVERWRITE);
    // mxDebugFuncVars(dirname(__FILE__) . '/templates/' . $XFtheme . '/theme.php');
    // if (@file_exists(dirname(__FILE__) . '/templates/' . $XFtheme . '/theme.php')) {
    // define('MXBOARD_HTML_THEME', $XFtheme);
    // }
} else {
    // wenn das theme nicht in der db existiert
    // ggf. bestehende Userdaten ändern
    sql_query("UPDATE $table_members SET theme='' WHERE theme='" . mxAddSlashesForSQL($XFtheme) . "'");
    sql_query("UPDATE $table_forums SET theme='' WHERE theme='" . mxAddSlashesForSQL($XFtheme) . "'");
    // fallback anwenden
    extract(mxbThemeFallback(), EXTR_OVERWRITE);
    // und Standardtheme verwenden
    $XFtheme = 'default';
}
unset($themevars);
// wenn default eingestellt, mit der defaulttheme.php die Farbwerte überschreiben
if ($XFtheme == "default") {
    //include($path . 'includes' . DS . 'defaulttheme.php');
}
// end theme-stuff
if ($shownewgrafic) {
    $newimage = mxCreateImage('modules/' . $ModName . '/images/' . $newimage, 'new', array('align' => 'right'));
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

$query = sql_query("SELECT COUNT(tid) as nbsites, SUM(views) as totalus FROM $table_threads");
$row = sql_fetch_object($query);
$topicspop = $row->nbsites;
$threadsall = $row->totalus;
$query = sql_query("SELECT COUNT(pid) as nbposts FROM $table_posts");
$row = sql_fetch_object($query);
$postpop = $row->nbposts;
$postpop = $postpop + $topicspop;
$query = sql_query("SELECT COUNT(uid) as xusers FROM $table_members");
$row = sql_fetch_object($query);
$member = $row->xusers;

$show = "<br><center>" . _TEXTTOPICS . "&nbsp;<strong>$topicspop</strong>&nbsp;<strong>|</strong>&nbsp;" . _TEXTPOSTS . "&nbsp;<strong>$postpop</strong>&nbsp;<strong>|</strong>&nbsp;" . _TEXTVIEWS . "&nbsp;<strong>$threadsall</strong>&nbsp;<strong>|</strong>&nbsp;";

if ($memliststatus == 'on' && $memlistanonymousstatus == 'on') {
    $show .= "<a href=\"modules.php?name=" . $ModName . "&amp;file=members.list\">";
} else {
    $show .= "<a>";
}

$show .= _TEXTMEMBERS . "</a>&nbsp;<strong>$member</strong></center>";
// versteckte Foren, versteckt fuer alle User
if (!empty($hideforums)) {
    $hide[1] = " t.fid NOT IN ('" . implode("','", $hideforums) . "')";
}
// versteckte Foren, versteckt fuer alle User
if (!empty($dontshowthisforum)) {
    $hide[2] = " f.name <> '" . mxAddSlashesForSQL($dontshowthisforum) . "'";
}
// private Foren, von Anonymen verstecken
if ($hideprivate == 'on' && !MX_IS_USER && !MX_IS_ADMIN && $status != 'Super Moderator' && $status != 'Administrator') {
    // Forum fuer Alle
    $hide[3] = "(f.private IS NULL OR f.private = '')";
} else
    // verstecken fuer User
    if ($hideprivate == 'on' && MX_IS_USER && !MX_IS_ADMIN && $status != 'Super Moderator' && $status != 'Administrator') {
        // Forum nur fuer User
        // oder der User in der Accessliste
        // oder der User ist Moderator im Form
        $hide[4] = "(
			(f.private IS NULL OR f.private = '' OR f.private = 'user')
			OR ( INSTR(REPLACE(CONCAT(',',LCASE(f.userlist),','), ', ', ',') , '," . mxAddSlashesForSQL(strtolower($thisuser)) . ",') )
			OR ( INSTR(REPLACE(CONCAT(',',LCASE(f.moderator),','), ', ', ',') , '," . mxAddSlashesForSQL(strtolower($thisuser)) . ",') )
			)";
        // leere Userliste
        // oder der User in der Accessliste
        // oder der User ist Moderator im Form
        $hide[5] = "(
			( f.userlist IS NULL OR f.userlist = '' )
			OR ( INSTR(REPLACE(CONCAT(',',LCASE(f.userlist),','), ', ', ',') , '," . mxAddSlashesForSQL(strtolower($thisuser)) . ",') )
			OR ( INSTR(REPLACE(CONCAT(',',LCASE(f.moderator),','), ', ', ',') , '," . mxAddSlashesForSQL(strtolower($thisuser)) . ",') )
			)";
    }
    if (isset($hide)) {
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
    $content = '<div id="block_' . $ModName . '">';
    $content .= "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\" align=\"center\">";
    $content .= "<tr><td bgcolor=\"$bordercolor\">";
    $content .= "<table cellspacing=\"$borderwidth\" cellpadding=\"$tablespace\" border=\"0\" width=\"100%\" align=\"center\">";
    $viewlast = '';
    $i = 0;
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
            $viewthreadpage = "&amp;page=" . ceil(($event['replies'] + 1) / $ppp) . "#pid" . $lasttpost->pid;
        }

        $dateA = mx_strftime(_SHORTDATESTRING . ' %H:%M', $event['lastpost_at']);

        if ($event['lastpost_by_true']) {
            $lastauthor = "<a href=\"modules.php?name=" . $ModName . "&amp;file=member&amp;action=viewpro&amp;member=" . $event['lastpost_by_true'] . "\">" . $event['lastpost_by_true'] . "</a>";
        } else {
            $lastauthor = $event['lastpost_by'];
        }

        if ($event['author_true']) {
            $author1 = "<a href=\"modules.php?name=" . $ModName . "&amp;file=member&amp;action=viewpro&amp;member=" . $event['author_true'] . "\">" . $event['author_true'] . "</a>";
        } else {
            $author1 = $event['author'];
        }

        if ($shownewgrafic && $event['lastpost_at'] >= $lastvisitdate) {
            $newpost = $newimage;
        } else {
            $newpost = '';
        }

        $viewlast .= "<tr>";
        if ($showicon) {
            if (empty($event['icon'])) {
                $event['icon'] = $default_icon;
            }
            $viewlast .= "<td style=\"background-color: $altbg1; text-align: center;\">" . mxCreateImage('modules/' . $ModName . '/images/' . $event['icon'], '') . "</td>";
        }
        $subject = ($showpriority) ? stripslashes($event['subject']) : strip_tags(stripslashes($event['subject']));
        $viewlast .= "<td style=\"background-color: $altbg2;\">" . $newpost . "<a href=\"modules.php?name=" . $ModName . "&amp;file=viewthread&amp;tid=" . $event['tid'] . "$viewthreadpage\"><strong>" . $subject . "</strong></a>";
        $viewlast .= "<br><a href=\"modules.php?name=" . $ModName . "&amp;file=forumdisplay&amp;fid=" . $event['fid'] . "\"><font size=\"0\">" . $event['name'] . "</font></a></td>";

        if ($showauthor) {
            $viewlast .= "<td style=\"background-color: $altbg1; text-align: center;\"><strong>" . $author1 . "</strong></td>";
        }
        if ($showviews) {
            $viewlast .= "<td style=\"background-color: $altbg2; text-align: center;\"><strong>" . $event['views'] . "</strong></td>";
        }
        if ($showreplies) {
            $viewlast .= "<td style=\"background-color: $altbg1; text-align: center;\"><strong>" . $event['replies'] . "</strong></td>";
        }
        if ($showlastpost) {
            $viewlast .= "<td  style=\"background-color: $altbg2; text-align: center;\"><strong>" . $lastauthor . "</strong><br><span class=\"tiny\" style=\"white-space: nowrap;\"><i>(" . $dateA . ")</i></span></td>";
        }
        $viewlast .= "</tr>";
    }

    $colspan = ($showicon) ? 2 : 1;
    $content .= "<tr style=\"background-color: $bgcolheader; color: $bgcolheadertext; text-align: center; font-weight: bold;\">";
    $content .= "<td colspan=\"" . $colspan . "\">" . _TEXTNEWTOPIC . "</td>";
    if ($showauthor) {
        $content .= "<td>" . _TEXTAUTHOR . "</td>";
    }
    if ($showviews) {
        $content .= "<td>" . _TEXTVIEWS . "</td>";
    }
    if ($showreplies) {
        $content .= "<td>" . _TEXTREPLIES . "</td>";
    }
    if ($showlastpost) {
        $content .= "<td>" . _LPOSTSTATS . "</td>";
    }
    $content .= "</tr>";

    $content .= "$viewlast";

    $content .= "</table></td></tr></table>";
    $content .= "$show";

    if ($whosonlinestatus == 'on' && $showonlinestatus) {
        $past = intval(time() - (MX_SETINACTIVE_MINS));
        if (!mxSessionGetVar('mxbPast') || mxSessionGetVar('mxbPast') < $past) {
            sql_query("DELETE FROM $table_whosonline WHERE time<'$past' AND username <> 'xguest123' AND ip <> '" . MX_REMOTE_ADDR . "'");
            mxSessionSetVar('mxbPast', time());
        }

        $guestcount = 0;
        $membercount = 0;
        if ($mxbshowonlineentiresite) {
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
                        WHERE time>" . $past . " AND uid=0 AND module='" . mxAddSlashesForSQL($ModName) . "'");
            list($guestcount) = sql_fetch_row($result);
            // Alle Mitglieder ermitteln
            $result = sql_query("SELECT uname FROM ${user_prefix}_users
                        WHERE ((user_lastvisit >= " . $past . ") AND (user_stat=1) AND (user_lastmod='" . mxAddSlashesForSQL($ModName) . "'))");
        } while (list($uname2) = sql_fetch_row($result)) {
            $memtally[$uname2] = '<a href="modules.php?name=' . $ModName . '&amp;file=member&amp;action=viewpro&amp;member=' . $uname2 . '">' . $uname2 . '</a>';
        }
        if (isset($memtally)) {
            $membercount = count($memtally);
            $memtally = implode(', ', $memtally);
        }

        $memonmsg = '<span class="f11pix">' . _WHOSONLINE1 . ' ' . $guestcount . ' ' . _WHOSONLINE2 . ' ' . $membercount . ' ' . _WHOSONLINE3 . '</span>';
        $content .= "<br><br><table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\" align=\"center\">";
        $content .= "<tr><td bgcolor=\"$bordercolor\">";
        $content .= "<table cellspacing=\"$borderwidth\" cellpadding=\"$tablespace\" border=\"0\" width=\"100%\" align=\"center\">";
        $content .= "<tr><td bgcolor=\"$bgcolheader\"><font color=\"$bgcolheadertext\"><a href=\"modules.php?name=" . $ModName . "&amp;file=misc&amp;action=online\" title=\"" . _WHOSONLINE . "\">" . _WHOSONLINE . "</a> - $memonmsg</font></td></tr>";
        if (!empty($memtally)) {
            $content .= "<tr bgcolor=\"$altbg2\"><td>$memtally</td></tr>";
        }
        $content .= "</table></td></tr></table>";
    }

    $content .= "<br><center>[ <a href=\"modules.php?name=" . $ModName . "\">" . _TEXTINDEX . "</a> ]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

    if ($newpostnum > 0) {
        $content .= "[ <a href=\"modules.php?name=" . $ModName . "&amp;file=messslv\"><font color=\"$color2\"><strong>$newpostnum " . _TEXTMESSSLV . "</strong></font></a> ]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
    }

    if ($statspage == 'on') {
        $content .= "[ <a href=\"modules.php?name=" . $ModName . "&amp;file=stats\">" . _TEXTSTATS . "</a> ]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
    }

    if ($searchstatus == 'on') {
        $content .= "[ <a href=\"modules.php?name=" . $ModName . "&amp;file=search\">" . _TEXTSEARCH . "</a> ]";
    }

    $content .= "</center>";
    $content .= "</div>";

    ?>