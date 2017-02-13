<?php
/**
 * mxBoard, pragmaMx Module
 * Copyright by pragmaMx Developer Team - http://www.pragmamx.org
 *
 * $Author: PragmaMx $
 * $Revision: 245 $
 * $Date: 2016-10-31 20:28:34 +0100 (lun. 31 oct. 2016) $
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

if (empty($fid) || !is_numeric($fid)) {
    include(dirname(__file__)) . '/index.php';
    return;
}

include_once(dirname(__FILE__) . DS . 'includes' . DS . 'header.php');
// if more than 10 Pages would be schown, shorten the diplay?
// true or false
$eblimitsites = true;

$query = sql_query("SELECT name, private, fid, userlist, moderator, threads, type, fup, postperm, guestposting FROM $table_forums WHERE fid=" . intval($fid));
$forums = sql_fetch_object($query);

if (!is_object($forums)) {
    return mxbExitMessage(_TEXTNOFORUM, true);
}

if ($forums->type == 'group') {
    // wenn das Forum über den Quickjumper aufgerufen wird, aber das Forum eine Gruppe ist,
    // $gid richtig setzen, die index.php includen und beenden
    $gid = intval($fid);
    include(dirname(__file__)) . '/index.php';
    return;
} else if ($forums->type == 'forum' && $forums->type == 'sub') {
    return mxbExitMessage(_TEXTNOFORUM, true);
}

if (!mxbPrivateCheck($forums)) {
    return mxbExitMessage(_PRIVFORUMMSG, true);
}

$jumper = mxbGetJumplink();

switch (true) {
    case $linkstatus == 'on':
        $query = sql_query("SELECT name, fid FROM $table_forums WHERE fid=" . intval($jumplink));
        $fup = sql_fetch_object($query);
        $mxbnavigator->add(MXB_BM_FORUMDISPLAY1 . "fid=" . $fup->fid, $fup->name);
        $mxbnavigator->add(false, $forums->name);
        break;
    case $forums->type == 'forum':
        $mxbnavigator->add(false, $forums->name);
        break;
    default:
        $forums->fup = (isset($forums->fup)) ? $forums->fup : 0;
        $query = sql_query("SELECT name, fid FROM $table_forums WHERE fid=" . intval($forums->fup));
        $fup = sql_fetch_object($query);
        if (is_object($fup)) {
            $mxbnavigator->add(MXB_BM_FORUMDISPLAY1 . "fid=" . $fup->fid, $fup->name);
            $mxbnavigator->add(false, $forums->name);
        }
        break;
}

$query = sql_query("SELECT name 
					FROM $table_forums 
					WHERE type='sub' 
					AND fup='" . intval($fid) . "'");
$sub = sql_fetch_object($query);
if (empty($sub->name)) {
    $sub = new stdClass;
    $sub->name = '';
}

if ($linkforumstatus == 'on') {
    $querylink = sql_query("SELECT lid 
							FROM $table_links 
							WHERE type='forum' 
							AND toid=" . intval($fid) . " AND status='on'");
    $sub2 = sql_fetch_object($querylink);
} else {
    $sub2 = new stdClass;
    $sub2->lid = $sub->name;
}

if (!empty($sub->name) || !empty($sub2->lid)) {

    ?>

			<div class="forabg">
				<div class="inner">
					<ul class="topiclist">
						<li class="header">
							<dl class="icon">
								<dt><?php echo _TEXTFORUM ?></dt>
								<dd class="lastpost"><span><?php echo _TEXTLASTPOST ?></span></dd>
								<dd class="topics"><?php echo _TEXTTOPICS ?></dd>
								<dd class="posts"><?php echo _TEXTPOSTS ?></dd>
							</dl>
						</li>
					</ul>
				</div>	
			</div> 			

<?php
    $querys = sql_query("SELECT f.fid, f.name, f.moderator, f.private, f.description, f.userlist, f.posts, f.threads,
        CONVERT(f.lastpost, UNSIGNED) AS lastpost_at,
        SUBSTRING_INDEX(f.lastpost, '|',-1) AS lastpost_by,
        l.username AS lastpost_by_true
        FROM $table_forums AS f
          LEFT JOIN $table_members as l ON l.username = SUBSTRING_INDEX(f.lastpost, '|',-1)
        WHERE f.type='sub' AND f.fup='" . intval($fid) . "' AND f.status='on'
        ORDER BY f.displayorder");
    while ($whileforum = sql_fetch_object($querys)) {
        if ($hideprivate != 'on' || mxbPrivateCheck($whileforum)) {
            $selectedres = mxbLastPostForumTid($whileforum->fid, 'sub');
            if ($linkstatus == 'on') {
                $whileforum->fid = $whileforum->fid . $jumper;
            }
            echo mxbListForum($whileforum, $selectedres->tid, $selectedres->subject);
        }
    }
    // #################
    // # LINKERGÄNZUNG
    // # Wird benötigt, um gelinkte Foren anzuzeigen
    if ($linkforumstatus == 'on') {
        $queryslink = sql_query("SELECT lid, fromid, toid 
								FROM $table_links 
								WHERE type='forum' 
								AND toid=" . intval($fid) . " AND status='on'");

        while ($forumlink = sql_fetch_object($queryslink)) {
            $queryslinks = sql_query("SELECT f.fid, f.name, f.moderator, f.private, f.description, f.userlist, f.posts, f.threads,
                CONVERT(f.lastpost, UNSIGNED) AS lastpost_at,
                SUBSTRING_INDEX(f.lastpost, '|',-1) AS lastpost_by,
                l.username AS lastpost_by_true
                FROM $table_forums AS f
                  LEFT JOIN $table_members as l ON l.username = SUBSTRING_INDEX(f.lastpost, '|',-1)
                WHERE fid='" . intval($forumlink->fromid) . "'");
            $forumlinks = sql_fetch_object($queryslinks);

            if ($hideprivate != 'on' || mxbPrivateCheck($forumlinks)) {
                $sql1 = sql_query("SELECT IF(LENGTH(LEFT(lastpost, INSTR(lastpost, '|')-1))<10, CONCAT('0',lastpost), lastpost) as  lastpost, tid, subject 
									FROM $table_threads where fid='" . intval($forumlinks->fid) . "' 
									ORDER BY lastpost DESC limit 0,1");
                $res1 = sql_fetch_assoc($sql1);
                $selectedlastpost['time'] = $res1['lastpost'];
                $lastsubject = $res1['subject'];
                $selectedres = $res1['tid'] . "&amp;linkstatus=on&amp;jumplink=" . $forumlink->toid . "&amp;linktype=thread&amp;lid=" . $forumlink->lid;

                $sql3 = sql_query("SELECT IF(LENGTH(LEFT(lastpost, INSTR(lastpost, '|')-1))<10, CONCAT('0',lastpost), lastpost) as  lastpost, type, fromid, toid, lid 
									FROM $table_links 
									WHERE type='thread' AND toid='" . $forumlinks->fid . "' AND status='on' 
									ORDER BY lastpost DESC limit 0,1");
                $res3 = sql_fetch_assoc($sql3);

                $sql4 = sql_query("SELECT IF(LENGTH(LEFT(lastpost, INSTR(lastpost, '|')-1))<10, CONCAT('0',lastpost), lastpost) as  lastpost, type, fromid, toid, lid 
									FROM $table_links 
									WHERE type='forum' AND toid='" . intval($forumlinks->fid) . "' AND status='on' 
									ORDER BY lastpost DESC limit 0,1");
                $res4 = sql_fetch_assoc($sql4);
                if ($res3['lastpost'] > $res4['lastpost']) {
                    if ($lastpostsubj == 'on') {
                        $query = sql_query("SELECT subject 
											FROM $table_threads 
											WHERE tid='" . intval($res3['fromid']) . "'");
                        list($lastsubject) = sql_fetch_row($query);
                    }
                    $selectedreslink = $res3['fromid'] . "&amp;linkstatus=on&amp;jumplink=" . $res3['toid'] . "&amp;linktype=" . $res3['type'] . "&amp;lid=" . $res3['lid'];
                    $selectedlastpostlink['time'] = $res3['lastpost'];
                } else {
                    $query = sql_query("SELECT tid, subject 
										FROM $table_threads 
										WHERE fid='" . intval($res4['fromid']) . "' 
										ORDER BY lastpost DESC limit 0,1");
                    list($linkforumtid, $lastsubject) = sql_fetch_row($query);
                    $selectedreslink = $linkforumtid . "&amp;linkstatus=on&amp;jumplink=" . $res4['toid'] . "&amp;linktype=" . $res4['type'] . "&amp;lid=" . $res4['lid'];
                    $selectedlastpostlink['time'] = $res4['lastpost'];
                }

                if ($selectedlastpostlink['time'] > $selectedlastpost['time']) {
                    $selectedres = $selectedreslink;
                }

                $forumlinks->fid = $forumlinks->fid . "&amp;linkstatus=on&amp;jumplink=" . $forumlink->toid . "&amp;linktype=forum&amp;lid=" . $forumlink->lid;
                $forumlinks->name .= '&nbsp;' . mxbGetImage('linked.png', 'linked', false);
                echo mxbListForum($forumlinks, $selectedres, $lastsubject);
            }
        }
    }
    // # LINKTEST ENDE
    // ################

}
$newtopiclink = '';
if (mxbPostingAllowed($forums, 'newpost')) {
	 $newtopiclink = '<a class="mx-button mx-button-primary" href="' . MXB_BM_POSTNEWTOPIC1 . 'fid=' . $fid . $jumper . '"><i class="fa fa-plus fa-fw"></i> ' . _TEXTNEWTOPIC . '</a>';
}

if ($piconstatus == 'on') {
    $picon1 = "<td>&nbsp;</td>";
} else {
    $picon1 = '';
}

if (!empty($page) && $page > 1) {
    $start_limit = ($page-1) * $tpp;
} else {
    $start_limit = 0;
    $page = 1;
}

if (!empty($cusdate) && $cusdate > 0) {
    $cusdate = intval($cusdate);
    $sql_cusdate = "AND t.lastpost >= '" . (time() - intval($cusdate)) . "'";
} else {
    $cusdate = 0;
    $sql_cusdate = '';
}

if (empty($ascdesc) || $ascdesc != 'ASC') {
    $ascdesc = "DESC";
} else {
    $ascdesc = "ASC";
}

$querytop = sql_query("
		SELECT t.tid, t.views, t.subject, t.dateline, t.replies, t.author, t.icon, t.fid , f.name, f.private, f.userlist, closed, topped,
      CONVERT(t.lastpost, UNSIGNED) AS lastpost_at,
      SUBSTRING_INDEX(t.lastpost, '|',-1) AS lastpost_by,
      l.username AS lastpost_by_true,
      u.username AS author_true
		FROM $table_threads as t
		INNER JOIN $table_forums AS f ON t.fid = f.fid
      LEFT JOIN $table_members as l ON l.username = SUBSTRING_INDEX(t.lastpost, '|',-1)
      LEFT JOIN $table_members as u ON u.username = t.author
    WHERE t.fid=" . intval($fid) . " $sql_cusdate
    ORDER BY t.topped $ascdesc, lastpost_at $ascdesc
    LIMIT " . intval($start_limit) . ", " . intval($tpp));

$query = sql_query("SELECT count(tid) as nbsites FROM $table_threads WHERE fid=" . intval($fid));
$row = sql_fetch_object($query);
// links werden gezählt und ggf. die Seitenanzahl erhoeht
$querylinks = sql_query("SELECT count(lid) as nbsites 
						FROM $table_links 
						WHERE type='thread' AND toid=" . intval($fid) . " AND status='on'");
$rowlinks = sql_fetch_object($querylinks);
$topicsnum = $row->nbsites + $rowlinks->nbsites;
// multipage Anzeige
if ($topicsnum >= $tpp) {
    $pages = ceil($topicsnum / $tpp);
    if ($page > $pages) {
        $page = $pages;
    }
    $sortadd = '';
    if ($cusdate > 0) {
        $sortadd .= '&amp;cusdate=' . $cusdate;
    }
    if ($ascdesc == 'ASC') {
        $sortadd .= '&amp;ascdesc=ASC';
    }
    $multipage = array();
    switch (true) {
        case $pages <= 5:
            $range1 = range(1, $pages);
            break;
        case $page <= 3:
            $range1 = range(1, 5);
            break;
        case $page >= ($pages - 2):
            $range1 = range($pages - 4, $pages);
            break;
        default:
            $range1 = range($page-2, $page + 2);
    }
    if ($page == 1) {
        $multipage[] = '<span class="arrows">&laquo;</span>';
    } else {
        $multipage[] = '<a href="' . MXB_BM_FORUMDISPLAY1 . 'fid=' . $fid . '&amp;page=1' . $sortadd . $jumper . '" title="' . _GOTOPAGEFIRST . '"><span class="arrows">&laquo;</span></a>';
    }
    foreach ($range1 as $i) {
        if ($i == $page) {
            $multipage[] = '<a href="' . MXB_BM_FORUMDISPLAY1 . 'fid=' . $fid . '&amp;page=' . $i . $sortadd . $jumper . '" title="' . _PAGE . '&nbsp;' . $i . ' ' . _OF . ' ' . $pages . '" class="current">' . $i . '</a>';
        } else {
            $multipage[] = '<a href="' . MXB_BM_FORUMDISPLAY1 . 'fid=' . $fid . '&amp;page=' . $i . $sortadd . $jumper . '" title="' . _GOTOPAGE . '&nbsp;' . $i . ' ' . _OF . ' ' . $pages . '">' . $i . '</a>';
        }
    }
    if ($page == $pages) {
        $multipage[] = '<span class="arrows">&raquo;</span>';
    } else {
        $multipage[] = '<a href="' . MXB_BM_FORUMDISPLAY1 . 'fid=' . $fid . '&amp;page=' . $pages . $sortadd . $jumper . '" title="' . _GOTOPAGELAST . ' (' . $pages . ' ' . _OF . ' ' . $pages . ')"><span class="arrows">&raquo;</span></a>';
    }
    $multipage = '<span class="counter">' . _PAGE . ' <b>' . $page . '</b> ' . _OF . ' ' . $pages . '</span>' . implode($multipage);
} else {
    $multipage = '';
}

?>
<div class="topic-actions">
	<?php echo $newtopiclink?>
	<div class="mxb-pagination">
			 <?php echo $multipage?>
	</div>	
</div>

<div class="forumbg">
	<div class="inner">
		<ul class="topiclist">
			<li class="header">
				<dl class="icon">
					<dt><?php echo _TEXTSUBJECT ?></dt>
					<dd class="posts"><?php echo _TEXTREPLIES ?></dd>
					<dd class="views"><?php echo _TEXTVIEWS ?></dd>
					<dd class="lastpost"><span><?php echo _TEXTLASTPOST ?></span></dd>
				</dl>
			</li>
		</ul>
		<ul class="topiclist topics">	
<?php

while ($thread = sql_fetch_object($querytop)) {
    $lastreplydate = gmdate($dateformat, $thread->lastpost_at + ($timeoffset * 3600));
    $lastreplytime = gmdate($timecode, $thread->lastpost_at + ($timeoffset * 3600));
    $lastpost = $lastreplydate . " " . _TEXTAT . " " . $lastreplytime . "<br/>" . _TEXTBY . " " . mxb_link2profile($thread->lastpost_by_true, $thread->lastpost_by);

    if ($thread->icon) {
        $thread->icon = mxCreateImage(MXB_BASEMODIMG . "/" . $thread->icon);
    } else {
        $thread->icon = '';
    }

    switch (true) {
        case $thread->closed == 'yes':
            $folder = 'lock_folder.gif';
            break;
        case $thread->replies >= $hottopic && $lastvisitdate < $thread->lastpost_at:
            $folder = 'hot_red_folder.gif';
            break;
        case $lastvisitdate < $thread->lastpost_at:
            $folder = 'red_folder.gif';
            break;
        case $thread->replies >= $hottopic:
            $folder = 'hot_folder.gif';
            break;
        default:
            $folder = 'folder.gif';
    }
    $folder = mxbGetImage($folder, false, false, true);

    if ($thread->topped == 1) {
        $XFprefix = 'alternate-4';
    } else {
        $XFprefix = '';
    }

    $thread->subject = stripslashes($thread->subject);
    //  Link-Ergänzung für die Threads in einem gelinkten Forum
    $threadlinkto = "<a href=\"" . MXB_BM_VIEWTHREAD1 . "tid=" . $thread->tid . $jumper . "\">" . $thread->subject . "</a>";
    $threadlinkto2 = MXB_BM_VIEWTHREAD1 . "tid=" . $thread->tid . $jumper;
    //  Ende Link-Ergänzung
    $authorlink = mxb_link2profile($thread->author_true, $thread->author);

    if ($thread->replies > $ppp) {
        $posts = $thread->replies;
        $posts++;
        $topicpages = ceil($posts / $ppp);
        $pagelinks = '';
        if ($topicpages <= 7 || !$eblimitsites) {
            for ($i = 1; $i <= $topicpages; $i++) {
                $pagelinks .= ' <a href="' . MXB_BM_VIEWTHREAD1 . 'tid=' . $thread->tid . '&amp;page=' . $i . $jumper . '" title="' . _GOTOPAGE . '&nbsp;' . $i . ' ' . _MXB_OF . ' ' . $topicpages . '">' . $i . '</a> ';
            }
        } else {
            for ($i = 1; $i <= 3; $i++) {
                $pagelinks .= ' <a href="' . MXB_BM_VIEWTHREAD1 . 'tid=' . $thread->tid . '&amp;page=' . $i . $jumper . '" title="' . _GOTOPAGE . '&nbsp;' . $i . ' ' . _MXB_OF . ' ' . $topicpages . '">' . $i . '</a> ';
            }
            $pagelinks .= ' ... ';
            for ($i = $topicpages - 2; $i <= $topicpages; $i++) {
                $pagelinks .= ' <a href="' . MXB_BM_VIEWTHREAD1 . 'tid=' . $thread->tid . '&amp;page=' . $i . $jumper . '" title="' . _GOTOPAGE . '&nbsp;' . $i . ' ' . _MXB_OF . ' ' . $topicpages . '">' . $i . '</a> ';
            }
        }
        $multipage2 = '(<small>' . _PAGES . ': <span style="white-space: nowrap;">' . $pagelinks . '</span></small>)';
        $pagelinks = '';
    } else {
        $multipage2 = '';
    }

    if ($piconstatus == 'on') {
        $picon2 = '       
        <dt>  
            ' . $thread->icon . '
            ' . $threadlinkto . '
			<br />
			' . $multipage2 . '
			de ' . $authorlink . '
		</dt>';
        $ratecol2 = '
       	<dd class="posts">' . $thread->replies . '<dfn>' . _TEXTREPLIES . '</dfn></dd>
			<dd class="views">' . $thread->views . '<dfn>' . _TEXTVIEWS . '</dfn></dd>
			<dd class="lastpost">
				<span>
					<dfn>' . _TEXTLASTPOST . ' </dfn>
					' . _TEXTBY . ' ' . $lastpost . '
				</span>					
			</dd>';
    } else {
        $picon2 = '
        	<dt>
        		' . $threadlinkto . '
						<br />
						' . $multipage2 . '
						de ' . $authorlink . '
					</dt>'; 
        $ratecol2 = '
        	<dd class="posts">' . $thread->replies . '<dfn>' . _TEXTREPLIES . '</dfn></dd>
					<dd class="views">' . $thread->views . '<dfn>' . _TEXTVIEWS . '</dfn></dd>
					<dd class="lastpost">
						<span>
							<dfn>' . _TEXTLASTPOST . ' </dfn>
							' . _TEXTBY . ' ' . $lastpost . '
						</span>					
				</dd>';
    }

    $allthreads[$thread->topped][($thread->lastpost_at . '.' . $thread->tid)] = '
    	<li class="ligne bgcolor3 ' . $XFprefix .'">
				<dl class="icon" style="background-image: url(' . $folder . '); background-repeat: no-repeat;">
					' . $picon2 . '
					' . $ratecol2 . '
				</dl>
			</li>';
}
// Threads links Anfang
// 
if ($linkthreadstatus == 'on') {
    $querytoplinks = sql_query("SELECT lid, fromid, toid,
    CONVERT(lastpost, UNSIGNED) AS lastpost_at
    FROM $table_links as t
    WHERE type='thread' AND t.toid=" . intval($fid) . " AND t.status='on' $sql_cusdate
    ORDER BY lastpost_at $ascdesc
    LIMIT " . intval($start_limit) . ", " . intval($tpp));

    while ($threadlink = sql_fetch_object($querytoplinks)) {
        $querytopthread = sql_query("SELECT t.tid, t.subject, t.lastpost, t.views, t.replies, t.author, t.icon, t.closed, t.topped,
          CONVERT(t.lastpost, UNSIGNED) AS lastpost_at,
          SUBSTRING_INDEX(t.lastpost, '|',-1) AS lastpost_by,
          l.username AS lastpost_by_true,
          u.username AS author_true
          FROM $table_threads AS t
      		INNER JOIN $table_forums AS f ON t.fid = f.fid
            LEFT JOIN $table_members as l ON l.username = SUBSTRING_INDEX(t.lastpost, '|',-1)
            LEFT JOIN $table_members as u ON u.username = t.author
          WHERE t.tid='" . intval($threadlink->fromid) . "'");

        $thread = sql_fetch_object($querytopthread);

        $lastreplydate = gmdate($dateformat, $thread->lastpost_at + ($timeoffset * 3600));
        $lastreplytime = gmdate($timecode, $thread->lastpost_at + ($timeoffset * 3600));
        $lastpost = "$lastreplydate " . _TEXTAT . " $lastreplytime<br/>" . _TEXTBY . " " . mxb_link2profile($thread->lastpost_by_true, $thread->lastpost_by);

        if ($thread->icon) {
            $thread->icon = mxCreateImage(MXB_BASEMODIMG . "/" . $thread->icon);
        } else {
            $thread->icon = "&nbsp;";
        }

        switch (true) {
            case $thread->closed == 'yes':
                $folder = 'lock_folder.gif';
                break;
            case $thread->replies >= $hottopic && $lastvisitdate < $thread->lastpost_at:
                $folder = 'hot_red_folder.gif';
                break;
            case $lastvisitdate < $thread->lastpost_at:
                $folder = 'red_folder.gif';
                break;
            case $thread->replies >= $hottopic:
                $folder = 'hot_folder.gif';
                break;
            default:
                $folder = 'folder.gif';
        }
        $folder = mxbGetImage($folder, false);

        if ($thread->topped == 1) {
            $XFprefix = 'alternate';
        } else {
            $XFprefix = '';
        }

        $thread->subject = stripslashes($thread->subject);
        //  Link-Ergänzung für die Threads in einem gelinkten Forum
        $threadlinkto = "<a href=\"" . MXB_BM_VIEWTHREAD1 . "tid=" . $thread->tid . "&amp;linkstatus=on&amp;jumplink=" . $threadlink->toid . "&amp;linktype=thread&amp;lid=" . $threadlink->lid . "\">" . $thread->subject . "</a>";
        $threadlinkto2 = MXB_BM_VIEWTHREAD1 . "tid=" . $thread->tid . "&amp;linkstatus=on&amp;jumplink=" . $threadlink->toid . "&amp;linktype=thread&amp;lid=" . $threadlink->lid;
        //  Ende Link-Ergänzung
        $authorlink = mxb_link2profile($thread->author_true, $thread->author);

        if ($thread->replies > $ppp) {
            $posts = $thread->replies;
            $posts++;
            $topicpages = ceil($posts / $ppp);
            $pagelinks = '';
            if ($topicpages <= 7 || !$eblimitsites) {
                for ($i = 1; $i <= $topicpages; $i++) {
                    $pagelinks .= ' <a href="' . MXB_BM_VIEWTHREAD1 . 'tid=' . $thread->tid . '&amp;page=' . $i . '&amp;linkstatus=on&amp;jumplink=' . $threadlink->toid . '&amp;linktype=thread&amp;lid=' . $threadlink->toid . '" title="' . _GOTOPAGE . '&nbsp;' . $i . ' ' . _MXB_OF . ' ' . $topicpages . '">' . $i . '</a> ';
                }
            } else {
                for ($i = 1; $i <= 3; $i++) {
                    $pagelinks .= ' <a href="' . MXB_BM_VIEWTHREAD1 . 'tid=' . $thread->tid . '&amp;page=' . $i . '&amp;linkstatus=on&amp;jumplink=' . $threadlink->toid . '&amp;linktype=thread&amp;lid=' . $threadlink->toid . '" title="' . _GOTOPAGE . '&nbsp;' . $i . ' ' . _MXB_OF . ' ' . $topicpages . '">' . $i . '</a> ';
                }
                $pagelinks .= ' ... ';
                for ($i = $topicpages - 2; $i <= $topicpages; $i++) {
                    $pagelinks .= ' <a href="' . MXB_BM_VIEWTHREAD1 . 'tid=' . $thread->tid . '&amp;page=' . $i . '&amp;linkstatus=on&amp;jumplink=' . $threadlink->toid . '&amp;linktype=thread&amp;lid=' . $threadlink->toid . '" title="' . _GOTOPAGE . '&nbsp;' . $i . ' ' . _MXB_OF . ' ' . $topicpages . '">' . $i . '</a> ';
                }
            }
            $multipage2 = '<strong class="mxb-pagination"><span>' . $pagelinks . '</span></strong>';
            $pagelinks = '';
        } else {
            $multipage2 = '';
        }

        if ($piconstatus == 'on') {
            $picon2 = "<td>" . $thread->icon . "</td>"
             . "<td onmouseover=\"this.style.backgroundColor='" . $altbg1 . "'; this.style.cursor='pointer';\" onmouseout=\"this.style.backgroundColor='" . $altbg2 . "'\" onclick=\"window.location.href='" . $threadlinkto2 . "'\"><font class=\"f12pix\">$threadlinkto &nbsp;" . mxbGetImage('linked.png', 'linked', false) . " $XFprefix $multipage2</font></td>"
             . "<td>$authorlink</td>";

            $ratecol2 = "<td>" . $thread->replies . "</td>"
             . "<td>" . $thread->views . "</td>"
             . "<td>" . $lastpost . "</td>"
             . "<td><a href=\"" . MXB_BM_PRINT1 . "fid=$fid&amp;tid=" . $thread->tid . "\" target=\"_blank\">" . mxbGetImage('print.png', 'print', false) . "</a></td>";
        } else {
            $picon2 = "<td>$threadlinkto &nbsp;" . mxbGetImage('linked.png', 'linked', false) . " $XFprefix $multipage2</td>"
             . "<td>$authorlink</td>";

            $ratecol2 = "<td>" . $thread->replies . "</td>"
             . "<td>" . $thread->views . "</td>"
             . "<td>$lastpost</td>"
             . "<td><a href=\"" . MXB_BM_PRINT1 . "fid=$fid&amp;tid=" . $thread->tid . "\" target=\"_blank\">" . mxbGetImage('folder.gif', false) . "</a></td>";
        }

        $allthreads[$thread->topped][($thread->lastpost_at . '.' . $thread->tid)] = '
            <tr>
				<td>' . $folder . '</td>
				' . $picon2 . '
				' . $ratecol2 . '
            </tr>
        ';
    }
}

if ($ascdesc == "DESC") {
    $sortfunc = 'krsort';
} else {
    $sortfunc = 'ksort';
}

if (isset($allthreads[1]) && $ascdesc == "DESC") {
    $sortfunc($allthreads[1]);
    echo(implode("\n", $allthreads[1]));
}
if (isset($allthreads[0])) {
    $sortfunc($allthreads[0]);
    echo(implode("\n", $allthreads[0]));
}
if (isset($allthreads[1]) && $ascdesc != "DESC") {
    $sortfunc($allthreads[1]);
    echo(implode("\n", $allthreads[1]));
}
//  Thread-Link ende
// 
if ($topicsnum == 0) {
    echo '<li>' . mxbMessageScreen(_NOPOSTS) . '</li>';
}

?>
	</ul>
</div>
</div><!--/ boite principale -->
<?php

if ($showsort == 'on' && $topicsnum > 0) {

   ?>
<form method="post" action="<?php echo MXB_BM_FORUMDISPLAY1 . "fid=" . $fid . $jumper ?>">
	<fieldset class="display-options">
		<label id="cusdate" for="cusdate" accesskey="c"><?php echo _SHOWTOPICS?></label>
 		<select name="cusdate"> 
			<option value="86400" <?php echo (($cusdate == 86400) ? ' selected="selected" class="current"' : '')?>><?php echo _DAY1?></option>
			<option value="432000" <?php echo (($cusdate == 432000) ? ' selected="selected" class="current"' : '')?>><?php echo _DAY5?></option>
			<option value="1296000" <?php echo (($cusdate == 1296000) ? ' selected="selected" class="current"' : '')?>><?php echo _DAY15?></option>
			<option value="2592000" <?php echo (($cusdate == 2592000) ? ' selected="selected" class="current"' : '')?>><?php echo _DAY30?></option>
			<option value="5184000" <?php echo (($cusdate == 5184000) ? ' selected="selected" class="current"' : '')?>><?php echo _DAY60?></option>
			<option value="8640000" <?php echo (($cusdate == 8640000) ? ' selected="selected" class="current"' : '')?>><?php echo _DAY100?></option>
			<option value="31536000" <?php echo (($cusdate == 31536000) ? ' selected="selected" class="current"' : '')?>><?php echo _LASTYEAR?></option>
			<option value="0" <?php echo ((empty($cusdate) || $cusdate < 0) ? ' selected="selected" class="current"' : '')?>><?php echo _BEGINNING?></option>
		</select>
		<label id="ascdesc" for="ascdesc"><?php echo _SORTBY?></label>
		<select name="ascdesc">
			<option value="ASC"<?php echo (($ascdesc == 'ASC') ? ' selected="selected" class="current"' : '') ?>><?php echo _ASC?></option>
			<option value="DESC"<?php echo (($ascdesc == 'DESC') ? ' selected="selected" class="current"' : '') ?>><?php echo _DESC?></option>
		</select>
		<input type="hidden" name="page" value="<?php echo $page ?>"/>
		<input type="submit" class="button2" value="<?php echo _TEXTGO?>"/>
	</fieldset>
</form>

<?php
}
?>
<hr />
<div class="topic-actions">
	<div class="buttons">
		<?php echo $newtopiclink?>
	</div>
	<div class="mxb-pagination">
			 <?php echo $multipage?>
	</div>	
</div>
<?php
//$foldernote = mxbGetImage('red_folder.gif', false) . " " . _OPENNEW . " (" . mxbGetImage('hot_red_folder.gif', false) . " " . _HOTTOPIC . ")<br/>" . mxbGetImage('folder.gif', false) . " " . _OPENTOPIC . " (" . mxbGetImage('hot_folder.gif', false) . " " . _HOTTOPIC . ")<br/>" . mxbGetImage('lock_folder.gif', false) . "  " . _LOCKTOPIC;
include_once(MXB_BASEMODINCLUDE . 'footer.php');

?>