<?php
/**
 * mxBoard, pragmaMx Module
 * Copyright by pragmaMx Developer Team - http://www.pragmamx.org
 *
 * $Author: PragmaMx $
 * $Revision: 171 $
 * $Date: 2016-06-29 13:59:03 +0200 (mer. 29 juin 2016) $
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

/* check ob die verwendete pragmaMx Version korrekt ist */
(defined('PMX_VERSION') && version_compare(PMX_VERSION, '2.2.', '>=')) or
die('Sorry, pragmaMx-Version >= 2.2 is required for mxBoard.');

include_once(dirname(__FILE__) . DS . 'includes' . DS . 'header.php');

if (empty($gid)) {
    $gid = 0;
} else {
    $gid = intval($gid);
    $query = sql_query("SELECT name 
						FROM $table_forums 
						WHERE fid='" . $gid . "' AND type='group'");
    $cat = sql_fetch_object($query);
    $mxbnavigator->add(false, $cat->name);
    unset($cat, $query);
}

if (!$gid) {
    $query = sql_query("SELECT f.fid, f.name, f.moderator, f.private, f.description, f.userlist, f.posts, f.threads, f.type,
        CONVERT(f.lastpost, UNSIGNED) AS lastpost_at,
        SUBSTRING_INDEX(f.lastpost, '|',-1) AS lastpost_by,
        l.username AS lastpost_by_true
        FROM $table_forums AS f
          LEFT JOIN $table_members as l ON l.username = SUBSTRING_INDEX(f.lastpost, '|',-1)
        WHERE f.type='forum' AND f.status='on' AND f.fup=''
        ORDER BY f.displayorder");
    // Foren ohne Kategorie
    $strtemp = '';
    while ($forum = sql_fetch_object($query)) {
        if (isset($announcestatus) && $announcestatus == 'on' && !$gid) {
            $strtemp .= '
				<tr>
					<td>
						<a href="#"><strong>' . _TEXTTOPLEVEL . '</strong></a>
					</td>
				</tr>';
        }
        if ($hideprivate != 'on' || mxbPrivateCheck($forum)) {
            $selectedres = mxbLastPostForumTid($forum->fid, $forum->type);
            $strtemp .= mxbListForum($forum, $selectedres->tid, $selectedres->subject);
        }
    }
    if ($strtemp) {
        echo $strtemp . '
			<tr>
				<td></td>
			</tr>';
    }
    $queryg = sql_query("SELECT fid, name FROM $table_forums WHERE type='group' AND status='on' ORDER BY displayorder");
} else {
    $queryg = sql_query("SELECT fid,name FROM $table_forums WHERE type='group' AND fid='" . intval($gid) . "' AND status='on' ORDER BY displayorder");
} // end !gid
while ($group = sql_fetch_object($queryg)) {
    $query = sql_query("SELECT f.fid, f.name, f.moderator, f.private, f.description, f.userlist, f.posts, f.threads, f.type,
        CONVERT(f.lastpost, UNSIGNED) AS lastpost_at,
        SUBSTRING_INDEX(f.lastpost, '|',-1) AS lastpost_by,
        l.username AS lastpost_by_true
        FROM $table_forums AS f
          LEFT JOIN $table_members as l ON l.username = SUBSTRING_INDEX(f.lastpost, '|',-1)
        WHERE f.type='forum' AND f.status='on' AND f.fup='" . intval($group->fid) . "'
        ORDER BY f.displayorder");

    $Ligneforum = array();
    while ($forum = sql_fetch_object($query)) {
        if ($hideprivate != 'on' || mxbPrivateCheck($forum)) {
            $selectedres = mxbLastPostForumTid($forum->fid, $forum->type);
            $Ligneforum[] = mxbListForum($forum, $selectedres->tid, $selectedres->subject);
        }
    }
    if ($Ligneforum) {
        if (!$gid) {
               ?>
			<div class="forabg">
				<div class="inner">
					<ul class="topiclist">
						<li class="header">
							<dl class="icon">
								<dt><?php echo '<a href="' . MXB_BM_INDEX1 . 'gid=' . $group->fid . '">' . $group->name . '</a>' ?></dt>								
   								<dd class="lastpost"><span><?php echo _TEXTLASTPOST ?></span></dd>
								<dd class="topics"><?php echo _TEXTTOPICS ?></dd>
								<dd class="posts"><?php echo _TEXTPOSTS ?></dd>								
							</dl>
						</li>
					</ul>
				</div>	
			</div> 			 
  	<?php 
        }
        echo "\n" . implode("\n", $Ligneforum) . "\n";
    
    }
}

if (empty($gid)) {
    if ($whosonlinestatus == 'on') {
        $past = intval(time() - MX_SETINACTIVE_MINS);

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
                        WHERE time>" . $past . " AND uid=0 AND module='" . mxAddSlashesForSQL(MXB_MODNAME) . "'");
            list($guestcount) = sql_fetch_row($result);
            // Alle Mitglieder ermitteln
            $result = sql_query("SELECT uname FROM ${user_prefix}_users
                        WHERE ((user_lastvisit >= " . $past . ") AND (user_stat=1) AND (user_lastmod='" . mxAddSlashesForSQL(MXB_MODNAME) . "'))");
        } while (list($uname2) = sql_fetch_row($result)) {
            $memtally[$uname2] = mxb_link2profile($uname2);
        }
        if (isset($memtally)) {
            $membercount = count($memtally);
            $memtally = implode(', ', $memtally);
        }
        $memonmsg = _WHOSONLINE1 . ' <strong>' . $guestcount . '</strong> ' . _WHOSONLINE2 . ' <strong>' . $membercount . '</strong> ' . _WHOSONLINE3 . '';
        echo '
        	<h3 class="h6-like"><a href="' . MXB_BM_MISC1 . 'action=online" title="' . _WHOSONLINE . '">' . _WHOSONLINE . '</a></h3>
        	<p>' . $memonmsg . '';
        if (isset($memtally)) {
            echo '
            	<br />
              ' . $memtally;
        }
        echo '</p>';
    }

    if ($indexstats == 'on') {
        $query = sql_query("SELECT fm.username, u.user_regtime
                            FROM $table_members AS fm
                            INNER JOIN {$user_prefix}_users AS u
                            ON fm.username = u.uname
                            ORDER BY u.user_regtime DESC LIMIT 0, 1");
        $lastmem = sql_fetch_object($query);
        $lastmember = $lastmem->username;
        $members = sql_num_rows($query);
        $query = sql_query("SELECT COUNT(tid) as nbsites FROM $table_threads");
        $row = sql_fetch_object($query);
        $threads = $row->nbsites;
        $query = sql_query("SELECT COUNT(pid) as nbsites FROM $table_posts");
        $row = sql_fetch_object($query);
        $posts = $row->nbsites;
        $posts = $threads + $posts;
        $memhtml = mxb_link2profile($lastmember);
        eval(_EVALINDEXSTATS);
        if ($members == "0") {
	        	$memhtml = "<strong>" . _TEXTNOONE . "</strong>";
        }
        $memhtml = '' . _INDEXSTATS . ' ' . _STATS41 . ' ' . $memhtml . '';
        $txtstats = ($statspage == 'on') ? "<a href=\"" . MXB_BM_STATS0 . "\">" . _TEXTSTATS . "</a>" : _TEXTSTATS;
        echo '
        	<h3 class="h6-like">' . $txtstats . '</h3>
        	<p>' . $memhtml . '</p>';
    }
}

include_once(MXB_BASEMODINCLUDE . 'footer.php');

?>