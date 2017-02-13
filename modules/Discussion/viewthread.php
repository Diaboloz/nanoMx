<?php
/**
 * mxBoard, pragmaMx Module
 * Copyright by pragmaMx Developer Team - http://www.pragmamx.org
 *
 * $Author: PragmaMx $
 * $Revision: 251 $
 * $Date: 2016-11-08 22:17:36 +0100 (mar. 08 nov. 2016) $
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

if (empty($tid) || !is_numeric($tid)) {
    include(dirname(__file__)) . '/index.php';
    return;
}

include_once(dirname(__FILE__) . DS . 'includes' . DS . 'header.php');

$query = sql_query("SELECT tid, fid, 0 AS pid, subject, lastpost, author, message, dateline, icon, usesig, closed, topped, useip, bbcodeoff, smileyoff
      FROM $table_threads
      WHERE tid=" . intval($tid));
$thread = sql_fetch_object($query);

if (!is_object($thread) || $thread->tid != $tid) {
    return mxbExitMessage(_TEXTNOTHREAD, true);
}

$thread->subject = stripslashes($thread->subject);

$fid = intval($thread->fid);
$query = sql_query("SELECT name, private, userlist, moderator, type, fup, fid, postperm, allowhtml, allowsmilies, allowbbcode, guestposting, allowimgcode
      FROM $table_forums
      WHERE fid=" . intval($fid) . " AND type IN('forum','sub')");
$forums = sql_fetch_object($query);

if (!is_object($forums)) {
    return mxbExitMessage(_TEXTNOFORUM, true);
}
if (!mxbPrivateCheck($forums)) {
    return mxbExitMessage(_PRIVFORUMMSG, true);
}

if (empty($orderdate) || $orderdate != 'DESC') {
    $orderdate = 'ASC';
}

$jumper = mxbGetJumplink();

$query = sql_query("SELECT count(pid) as nbsites FROM $table_posts WHERE tid=" . intval($tid));
$row = sql_fetch_object($query);
$num = $row->nbsites;
$pages = ceil(($num + 1) / $ppp);

if ((empty($page) || $page < 1)) {
    $page = 1;
} else if ($page > $pages) {
    $page = $pages;
}

$start_limit = 0;
$postcounter = 0;
if ($page == 1 && $orderdate == 'ASC') {
    // Seite 1 aufsteigend, mit Thread
    $start_limit = 0;
    $postcounter = $start_limit; // Start des Postcounters
    $ppp--; // 1 post weniger, weil thread mit dabei
} else if ($page > 1 && $orderdate == 'ASC') {
    // weitere Seite aufsteigend, ohne Thread
    $start_limit = (($page-1) * $ppp) -1;
    $postcounter = $start_limit; // Start des Postcounters
} else if ($page == 1 && $orderdate == 'DESC') {
    // Seite 1 abfsteigend, ohne Thread
    $start_limit = 0;
    $postcounter = $num + 1; // Start des Postcounters
} else if ($page > 1 && $orderdate == 'DESC') {
    // weitere Seite aufsteigend, ohne Thread
    $start_limit = (($page-1) * $ppp);
    $postcounter = $num - $start_limit + 1; // Start des Postcounters
}
// multipage Anzeige
if ($num >= $ppp) {
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
    $multipage = array();
    if ($page == 1) {
        $multipage[] = '<span>&laquo;</span>';
    } else {
        $multipage[] = '<a href="' . MXB_BM_VIEWTHREAD1 . 'fid=' . $fid . '&amp;tid=' . $tid . '&amp;page=1&amp;orderdate=' . $orderdate . '' . $jumper . '" title="' . _GOTOPAGEFIRST . '"><span class="arrows">&laquo;</span></a>';
    }
    foreach ($range1 as $i) {
        if ($i == $page) {
            $multipage[] = '<a href="' . MXB_BM_VIEWTHREAD1 . 'fid=' . $fid . '&amp;tid=' . $tid . '&amp;page=' . $i . '&amp;orderdate=' . $orderdate . '' . $jumper . '" title="' . _PAGE . '&nbsp;' . $i . ' ' . _MXB_OF . ' ' . $pages . '" class="current">' . $i . '</a>';
        } else {
            $multipage[] = '<a href="' . MXB_BM_VIEWTHREAD1 . 'fid=' . $fid . '&amp;tid=' . $tid . '&amp;page=' . $i . '&amp;orderdate=' . $orderdate . '' . $jumper . '" title="' . _GOTOPAGE . '&nbsp;' . $i . ' ' . _MXB_OF . ' ' . $pages . '">' . $i . '</a>';
        }
    }
    if ($page == $pages) {
        $multipage[] = '<span class="arrows">&raquo;</span>';
    } else {
        $multipage[] = '<a href="' . MXB_BM_VIEWTHREAD1 . 'fid=' . $fid . '&amp;tid=' . $tid . '&amp;page=' . $pages . '&amp;orderdate=' . $orderdate . '' . $jumper . '" title="' . _GOTOPAGELAST . ' (' . $pages . ' ' . _MXB_OF . ' ' . $pages . ')"><span class="arrows">&raquo;</span></a>';
    }
    $multipage = '<span class="counter">' . _PAGE . ' <b>' . $page . '</b> ' . _MXB_OF . ' ' . $pages . '</span> ' . implode($multipage);
} else {
    $multipage = '';
}
// --------------------------------------------
// querypost->display post from sql request
$querypost = sql_query("SELECT fid, tid, pid, author, message, dateline, icon, usesig, useip, bbcodeoff, smileyoff
          FROM $table_posts
          WHERE tid=" . intval($tid) . "
          ORDER BY dateline $orderdate
          LIMIT " . intval($start_limit) . ", " . intval($ppp));

$thisbg = 'alternate-1';
// alle Posts, des Threads in ein Array lesen
$post_array = array();

while ($post = sql_fetch_object($querypost)) {
    // $postcounter wird bereits weiter oben mit einem Startwert initialisiert
    if ($orderdate == "ASC") {
        $postcounter++;
    } else {
        $postcounter--;
    }
    $post->number = $postcounter;
    $post_array[] = $post;
}

if (empty($pages) || $pages == 1) {
    if ($orderdate == "ASC") {
        // eine Seite und absteigend >> thread davor
        $post_array = array_merge(array($thread), $post_array);
    } else {
        // eine Seite und aufsteigend >> thread ans Ende
        $post_array = array_merge($post_array, array($thread));
    }
} else {
    if ($page == 1 && $orderdate == "ASC") {
        // seite 1 und absteigend >> thread davor
        $post_array = array_merge(array($thread), $post_array);
    } else if ($page == $pages && $orderdate == "DESC") {
        // letzte Seite und aufsteigend >> thread ans Ende
        $post_array = array_merge($post_array, array($thread));
    }
}
//  Link-Ergänzung
if ($linkstatus == 'on') {
    $queryjumplink = sql_query("SELECT name, fid FROM $table_forums WHERE fid=" . intval($jumplink));
    $fupjumplink = sql_fetch_object($queryjumplink);
    $mxbnavigator->add(MXB_BM_FORUMDISPLAY1 . "fid=" . $fupjumplink->fid, $fupjumplink->name);
    if ($linktype != "thread") {
        $mxbnavigator->add(MXB_BM_FORUMDISPLAY1 . "fid=$fid&amp;jumplink=$jumplink&amp;linkstatus=on", $forums->name);
    }
} else {
    if ($forums->type != 'forum') {
        $query = sql_query("SELECT name, fid FROM $table_forums WHERE fid=" . intval($forums->fup));
        $fup = sql_fetch_object($query);
        $mxbnavigator->add(MXB_BM_FORUMDISPLAY1 . "fid=" . $fup->fid, $fup->name);
    }
    $mxbnavigator->add(MXB_BM_FORUMDISPLAY1 . "fid=$fid", $forums->name);
}
$mxbnavigator->add(false, $thread->subject);
//  Ende Link-Ergänzung
$replylink = '';
if ($thread->closed == 'yes') {
    $closeopen = "<a href=\"" . MXB_BM_TOPICADMIN1 . "action=close&amp;fid=$fid&amp;tid=$tid$jumper\">" . _TEXTOPENTHREAD . "</a>";
} else {
    $closeopen = "<a href=\"" . MXB_BM_TOPICADMIN1 . "action=close&amp;fid=$fid&amp;tid=$tid$jumper\">" . _TEXTCLOSETHREAD . "</a>";
    if (mxbPostingAllowed($forums, 'reply')) {
        $replylink = "<a href=\"" . MXB_BM_POSTREPLY1 . "fid=$fid&amp;tid=$tid$jumper\" class=\"mx-button mx-button-primary\"><i class=\"fa fa-reply fa-fw\"></i> " . mxbGetImage('reply.png', _TEXTPOSTREPLY, true) . "</a>";
    }
}

$querylast = sql_query("SELECT tid FROM $table_threads WHERE lastpost < '" . $thread->lastpost . "' AND fid=" . intval($fid) . " ORDER BY lastpost DESC");
$goto2 = sql_fetch_object($querylast);
if (!empty($goto2->tid)) {
    $prev = " <a class=\"navinthread\" href=\"" . MXB_BM_VIEWTHREAD1 . "fid=$fid&amp;tid=" . $goto2->tid . "&amp;orderdate=" . $orderdate . "$jumper\" style=\"white-space: nowrap;\">&laquo;&nbsp;" . _LASTTHREAD . "</a>&nbsp;";
} else {
    $prev = " <span class=\"navinthread\" style=\"white-space: nowrap;\">&laquo;&nbsp;" . _LASTTHREAD . "</span>&nbsp;";
}
$querynext = sql_query("SELECT tid FROM $table_threads WHERE lastpost > '" . $thread->lastpost . "' AND fid=" . intval($fid) . " ORDER BY lastpost");
$gotothread = sql_fetch_object($querynext);
if (!empty($gotothread->tid)) {
    $next = "&nbsp;<a class=\"navinthread\" href=\"" . MXB_BM_VIEWTHREAD1 . "fid=$fid&amp;tid=" . $gotothread->tid . "&amp;orderdate=" . $orderdate . "$jumper\" style=\"white-space: nowrap;\">" . _NEXTTHREAD . "&nbsp;&raquo;</a> ";
} else {
    $next = "&nbsp;<span class=\"navinthread\" style=\"white-space: nowrap;\">" . _NEXTTHREAD . "&nbsp;&raquo;</span> ";
}
//  Link-Ergänzungen hier
$newtopiclink = '';
//  wenn man über einen Thread-Link kommt, dann posted man neue Topics aus dem Thread in das gelinkte Forum
if ($linktype != "thread") {
    if (mxbPostingAllowed($forums, 'newpost')) {
        $newtopiclink = "<a href=\"" . MXB_BM_POSTNEWTOPIC1 . "fid=$fid$jumper\">" . mxbGetImage('newtopic.png', _TEXTNEWTOPIC, true) . "</a>";
    }
} else {
    // wenn Thread-Link, dann hier
    $prev = '';
    $next = '';
    if (mxbPostingAllowed($forums, 'newpost')) {
        $newtopiclink = "<a href=\"" . MXB_BM_POSTNEWTOPIC1 . "fid=$jumplink\">" . mxbGetImage('newtopic.png', _TEXTNEWTOPIC, true) . "</a>";
    }
}

sql_query("UPDATE $table_threads SET views=views+1 WHERE tid=" . intval($tid));

?>
<h2 class="h4-like">
	<?php echo _TEXTSUBJECT, " ", $thread->subject ?>
</h2>
<div class="topic-actions">
		<?php echo trim($replylink) ?>
		<?php echo $multipage ?>
<?php
	if ($showsort == 'on') { ?>
    <span class="reorder">
		<a href="<?php echo MXB_BM_VIEWTHREAD1 ?>fid=<?php echo $fid ?>&amp;tid=<?php echo $tid, $jumper ?>&amp;orderdate=ASC"><?php echo mxbGetImage('haut.png', _TRI_ASC, false) ?></a>
		<a href="<?php echo MXB_BM_VIEWTHREAD1 ?>fid=<?php echo $fid ?>&amp;tid=<?php echo $tid, $jumper ?>&amp;orderdate=DESC"><?php echo mxbGetImage('bas.png', _TRI_DESC, false) ?></a>
	</span>
	<?php
	}
?>
</div>

<?php
// Schleife durch Posts
foreach ($post_array as $post) {
    $postnumber = (empty($post->number)) ? 'class="first h6-like">' : 'class="h6-like">Re: ';
    // Standardvorgaben
    $ip = '';
    $showtitle = _UNREG;
    $miscinfo = '';
    $profile = '';
    $onlinestatus = '';
    $stars = '';
    $search = '';
    $avatar = '';
    $geschlecht = '';
    $edit = '';
    $email = '';
    $user2user = '';
    $site = '';
    $repquote = '';
    $reportlink = '';
    $userstuff = '';

    $date = gmdate($dateformat, (int)$post->dateline + ($timeoffset * 3600));
    $time = gmdate($timecode, (int)$post->dateline + ($timeoffset * 3600));

    $poston = _TEXTPOSTON . " $date " . _TEXTAT . " $time";

    if ($post->dateline >= $lastvisitdate) {
        $newpostimage = mxbGetImage('new.png', 'New', true) . '&nbsp;';
    } else {
        $newpostimage = '';
    }

    if (!empty($post->icon)) {
        $post->icon = '<img src="' . MXB_BASEMODIMG . '/' . $post->icon . '" alt="' . $post->icon . '" style="float:left; margin-right: 6px;"/>';
    }
    
    $postmember = false;
    if (isset($info[$post->author])) {
        $postmember = $info[$post->author];
    } else {
        $query = sql_query("SELECT fm.username, u.user_regtime as regdate, fm.postnum, u.email, u.url, fm.status, u.user_sig, fm.customstatus, fm.u2u, u.uname, u.user_lastvisit, u.user_lastmod, u.user_stat, u.user_sexus, u.user_viewemail
                        FROM $table_members AS fm
                        LEFT JOIN {$user_prefix}_users AS u
                        ON fm.username = u.uname
                        WHERE username='" . mxAddSlashesForSQL(substr($post->author, 0, 25)) . "'");
        $postmember = sql_fetch_object($query);
        // pruefen ob User noch im System aktiviert ist
        if ($postmember && $postmember->status != 'Administrator' && (empty($postmember->uname) || $postmember->user_stat != 1)) {
            // wenn nicht vorhanden, alle Datenbestände dieses Users angleichen
            if (empty($postmember->uname)) {
                mxbCleanUserdata($post->author);
            }
            $postmember = false;
        }
        $info[$post->author] = $postmember;
    }
    $post->message = mxbPostify(stripslashes($post->message), $forums->allowhtml, $forums->allowsmilies, $forums->allowbbcode, $forums->allowimgcode, $post->smileyoff, $post->bbcodeoff);

    if (is_object($postmember)) {
        if (!empty($postmember->user_sexus) && $postmember->user_sexus == 1) {
            $geschlecht = '<dd><strong>' . _EBF_GESCHLECHT . '</strong> ' . mxbGetImage('female.gif', _YA_FEMALE, false) . '</dd>';
        } else if (!empty($postmember->user_sexus) && $postmember->user_sexus == 2) {
            $geschlecht = '<dd><strong>' . _EBF_GESCHLECHT . '</strong> ' . mxbGetImage('male.gif', _YA_MALE, false) . '</dd>';
        } else {
            $geschlecht = '';     
        }

        if (!empty($postmember->email) && ($postmember->user_viewemail || $eBoardUser['isadmin'])) {
            $email = '<li class="email-icon"><a href="mailto:' . mxPrepareToDisplay($postmember->email) . '" title="' . _EB_EMAILUSER . '"><span><i class="fa fa-envelope-o"></i></span></a></li>';            
        } else {
            $email = '';
        }

        if ($allowu2u == 'on' && ($postmember->u2u == 'yes' || isOnStaff($eBoardUser['status'])) && mxModuleAllowed('Private_Messages') && !mxbIsAnonymous() && !empty($postmember->uname)) {
            $user2user = "<li class=\"pm-icon\"><a href=\"javascript:IM('" . $postmember->uname . "')\" title=\"" . _EB_PMTOAUTHOR . "\"><span><i class=\"fa fa-envelope-o\"></i></span></a></li>";
        }

        $postmember->url = mxCutHTTP($postmember->url);
        if ($postmember->url) {
            $site = '<li class="web-icon"><a href="' . $postmember->url . '" title="' . _EB_VISITSITE . '" target="_blank"><span><i class="fa fa-globe"></i></span></a></li>';
        } else {
            $site = '';
        }

        $search = "<a href=\"" . MXB_BM_SEARCH1 . "member=" . $post->author . "\">" . mxbGetImage('find.png', _SEARCHUSERMSG, true) . "</a> ";
        //$profile = mxb_link2profile($post->author, mxbGetImage('profile.png', _TEXTVIEWPRO, true)) . " ";

        $tharegdate = gmdate($dateformat, (int)$postmember->regdate + ($timeoffset * 3600));
        $miscinfo = '
        	<dd><strong>' . _TEXTPOSTS . '</strong> ' . $postmember->postnum . '</dd>
        	<dd><strong>' . _TEXTREGISTERED . '</strong> ' . $tharegdate . '</dd>';


        if ($post->usesig == 'yes') {
            $membersig = mxbPostify($postmember->user_sig, $sightml, $sigbbcode, $sigbbcode, $sigbbcode);
            if ($sigimgXxXauth == 'on') {
                $membersig = preg_replace("/<img[^>]*src=([^>]*)>/i", "<img width=" . $sigimgwidth . " height=" . $sigimgheight . " src=\\1>", $membersig);
            }
            $membersig = preg_replace('#&lt;br\s*/?&gt;#i', '<br/>', $membersig);
            $post->message .= (empty($membersig)) ? '' : '<div class="signature">' . $membersig . '</div>';
        }

        extract (vtGetUserInfo($postmember, $post));
    } /// Ende, nur wenn Member...
    if (isOnStaff($eBoardUser['status'])) {
        $ip = '<li class="info-icon"><a href="' . MXB_BM_TOPICADMIN1 . 'action=getip&amp;fid=' . $fid . '&amp;tid=' . $tid . '&amp;pid=' . $post->pid . '"><span><i class="fa fa-exclamation-triangle"></i> Get IP</span></a></li>';
    }

    if (mxbIsPostOwner($post->author, $eBoardUser['username']) || mxbIsModeratorInForum($forums)) {
        $edit = '<li class="edit-icon"><a title="' . _EB_EDITPOST . '" href="' . MXB_BM_POSTEDIT1 . 'fid=' . $fid . '&amp;tid=' . $tid . '&amp;pid=' . $post->pid . '' . $jumper . '"><span><i class="fa fa-pencil-square-o"></i></span></a></li>';
    }

    if ($thread->closed != "yes" && (mxbPostingAllowed($forums, 'reply'))) {
        // $repquote = "<a href=\"" . MXB_BM_POSTREPLY1 . "fid=$fid&amp;tid=$tid$jumper&amp;repquote=r&amp;repquoteid=" . $post->pid . "\">" . mxbGetImage('quote.png', _EB_QUOTEREPLY, true) . "</a> ";
        $repquote = '<li class="quote-icon"><a title="' . _EB_QUOTEREPLY . '" href="' . MXB_BM_POSTREPLY1 . 'fid=' . $fid . '&amp;tid=' . $tid . '' . $jumper . '&amp;repquoteid=' . $post->pid . '"><i class="fa fa-quote-left"></i></a></li>';
    }
    // TODO ??
    if (!mxbIsAnonymous() && $reportpost != 'off') {
        $reportlink = '<li class="report-icon"><a title="' . _TEXTREPORTPOST . ' "href="' . MXB_BM_MISC1 . 'action=report&amp;fid=' . $fid . '&amp;tid=' . $tid . '&amp;pid=' . $post->pid . '"><span>' . _TEXTREPORTPOST . '</span></a></li>';
    }

    ?>
		<div class="post <?php echo $thisbg ?> <?php echo $onlinestatus ?>">
			<div class="inner">
				<div class="postbody">
					<ul class="profile-icons">
						<?php echo $repquote ?>
						<?php echo $reportlink ?>
						<?php echo $edit ?>
					</ul>
					<?php echo $post->icon ?> 
					<h3 <?php echo $postnumber ?><?php echo $thread->subject ?></h3>
					<p class="author"><?php echo $newpostimage ?><?php echo $poston ?> <?php echo _TEXTBY ?> <?php echo $post->author ?><a name="pid<?php echo $post->pid ?>"></a></p>
					<div class="content">
						<?php echo $post->message ?>					
					</div>
				</div>
				<dl class="postprofile">
					<dt>
						<?php echo $avatar ?>
						<br />
						<a href="<?php echo MXB_BM_MEMBER1 ?>action=viewpro&amp;member=<?php echo rawurlencode($post->author) ?>"><?php echo $post->author ?></a>
					</dt>		
						<dd><?php echo $showtitle ?></dd>
						<dd><?php echo $stars ?></dd>
						<dd>&nbsp;</dd>
						<?php echo $miscinfo ?>
						<?php echo $geschlecht ?>
						<dd>
							<ul class="profile-icons">
								<?php if (mxbIsAnonymous()) {
									echo '<li></li>';    
								} ?>
								<?php echo $email ?>
								<?php echo $site ?>		
								<?php echo $user2user ?>
							</ul>
						</dd>
				</dl>
				<div class="back2top">
					<a href="#haut" class="top" title="<?php echo _MXBPAGEUP ?>"><i class="fa fa-arrow-circle-up"></i></a>
				</div>
			</div>
		</div>
	<hr class="divider" />


        <?php
    $thisbg = ($thisbg == 'alternate-1') ? 'alternate-2' : 'alternate-1';
}
// ENDE Schleife durch Posts

?>
<div class="topic-actions">	
		<?php echo trim($replylink) ?>
		<?php echo $multipage ?>
</div>
 <?php 
    if (!empty($prev) || !empty($next)) { 
    echo '<p>' . $prev, $next . '</p>';
    } 
 ?>

<?php
if (mxbIsModeratorInForum($forums)) {
    echo '
    	<hr />
    	<div class="topic-actions">';
    if (!empty($thread->topped)) {
        $topuntop = '<a class="boutton sticky-icon" href="' . MXB_BM_TOPICADMIN1 . 'action=top&amp;fid=' . $fid . '&amp;tid=' . $tid . $jumper .'">' . _TEXTUNTOPTHREAD . '</a>';
    } else {
        $topuntop = '<a class="boutton sticky-icon" href="' . MXB_BM_TOPICADMIN1 . 'action=top&amp;fid=' . $fid . '&amp;tid=' . $tid . $jumper . '">' . _TEXTTOPTHREAD . '</a>';
    }
    echo '
    				<a class="boutton delete-icon" href="' . MXB_BM_TOPICADMIN1 . 'action=delete&amp;fid=' . $fid . '&amp;tid=' . $tid . $jumper . '">' . _TEXTDELETETHREAD . '</a>
    				' . $closeopen . '
     				<a class="boutton move-icon" href="' . MXB_BM_TOPICADMIN1 . 'action=move&amp;fid=' . $fid . '&amp;tid=' . $tid . $jumper . '">' . _TEXTMOVETHREAD . '</a>
     				' . $topuntop ;
    if ($linkthreadstatus == 'on') {
        echo '
        		<a class="boutton up-icon" href="' . MXB_BM_TOPICADMIN1 . 'action=bump&amp;fid=' . $fid . '&amp;tid=' . $tid . $jumper . '">' . _TEXTBUMPTHREAD . '</a>
         		<a class="boutton link-icon" href="' . MXB_BM_TOPICADMIN1 . 'action=linkthread&amp;fid=' . $fid . '&amp;tid=' . $tid . $jumper . '">' . _TEXTNEWTHREADLINK . '</a>';
    } else {
        echo '
        		<a class="boutton up-icon" href="' . MXB_BM_TOPICADMIN1 . 'action=bump&amp;fid=' . $fid . '&amp;tid=' . $tid . $jumper . '">' . _TEXTBUMPTHREAD . '</a>';
    }
    echo '   		
    	</div>';
}



function vtGetUserInfo($postmember, $post)
{
    global $dateformat, $timeoffset, $avastatus, $adminstars, $table_ranks;

    static $maxstars;
    if (!isset($maxstars) && $adminstars == 'maxusersp3') {
        $query = sql_query("SELECT MAX(stars) as maxstars FROM $table_ranks");
        $row = sql_fetch_object($query);
        $maxstars = $row->maxstars;
    }

    if (!empty($post->author) && $post->author != MXB_ANONYMOUS) {
        if (!empty($ol[$postmember->username])) {
            $info['onlinestatus'] = $ol[$postmember->username];
        } elseif (mxbIsOnline($postmember->username, $postmember)) {
            $info['onlinestatus'] = 'online';
            $ol[$postmember->username] = $info['onlinestatus'];
        } else {
            $info['onlinestatus'] = 'offline';
            $ol[$postmember->username] = $info['onlinestatus'];
        }

        if (empty($postmember->postnum) || $postmember->postnum < 1) {
            $postmember->postnum = mxbRepairUserPostNum($postmember->username);
        }
        // aktuell gültigen Status ermitteln
        $postmember->status = mxbGetRepairedStatus($postmember);

        $info['showtitle'] = $postmember->status;
        if (!empty($rankstore[$postmember->username])) {
            $rank = $rankstore[$postmember->username];
        } else {
            $queryrank = sql_query("SELECT posts, title, stars 
                                    FROM $table_ranks 
                                    WHERE posts<=" . intval($postmember->postnum) . " 
                                    ORDER BY posts DESC LIMIT 0,1");
            $rank = sql_fetch_object($queryrank);
            $rankstore[$postmember->username] = $rank;
        }

        $info['showtitle'] = $rank->title;
        $info['stars'] = "<img src=\"" . MXB_BASEMODIMG . "/star" . $rank->stars . ".gif\" alt=\"" . $rank->title . "\"><br/>";

        if (isOnStaff($postmember->status)) {
            if ($adminstars == 'maxusersp3') {
                $staffstar3 = $maxstars + 3;
                $staffstar2 = $maxstars + 2;
                $staffstar1 = $maxstars + 1;

                if ($postmember->status == 'Administrator') {
                    $info['showtitle'] = '<span style="color:#AA0000">' . _TEXTADMIN . '</span>';
                    $info['stars'] = '<img src="' . MXB_BASEMODIMG . '/star' . $staffstar3 . '.gif" alt="' . $rank->title . '" />';
                } elseif ($postmember->status == 'Super Moderator') {
                    $info['showtitle'] = '<span style="color:#FF00CC">' . _TEXTSUPERMOD . '</span>';
                    $info['stars'] = '<img src="' . MXB_BASEMODIMG . '/star' . $staffstar2 . '.gif" alt="' . $rank->title . '" />';
                } elseif ($postmember->status == 'Moderator') {
                    $info['showtitle'] = '<span style="color:#105289">' . _TEXTMOD . '</span>';
                    $info['stars'] = '<img src="' . MXB_BASEMODIMG . '/star' . $staffstar1 . '.gif" alt="' . $rank->title . '" />';
                }
            } else { // sameasusers
                if ($postmember->status == 'Administrator') {
                    $info['showtitle'] = '<span style="color:#AA0000">' . _TEXTADMIN . '</span>';
                } elseif ($postmember->status == 'Super Moderator') {
                    $info['showtitle'] = '<span style="color:#FF00CC">' . _TEXTSUPERMOD . '</span>';
                } elseif ($postmember->status == 'Moderator') {
                    $info['showtitle'] = '<span style="color:#105289">' . _TEXTMOD . '</span>';
                }
            }
        }               
    } else {
        $info['stars'] = '';
        $info['showtitle'] = '';
    }

    if ($postmember->status == 'Banned') {
        $info['showtitle'] = _TEXTBANNED;
        $info['stars'] = '';
    }

    if ($postmember->customstatus != '') {
        $info['showtitle'] = $postmember->customstatus;
    }

    $info['showtitle'] .= "<br/>";
    $info['stars'] .= "<br/>";
    $info['onlinestatus'] .= "<br/>";
    // / Avatar
    static $infoavatar;
    $avatar = '';
    if ($avastatus == 'on' && !isset($infoavatar[$postmember->username])) {
        /* aktuelles Benutzerbild ermitteln */
        $pici = load_class('Userpic', $postmember->username);
        $avatar = $pici->getHtml('small');
        $infoavatar[$postmember->username] = $avatar;
    }
    $info['avatar'] = (empty($infoavatar[$postmember->username])) ? '' : trim($infoavatar[$postmember->username]);

    $info['member'] = $postmember;
    return $info;
}
include_once(MXB_BASEMODINCLUDE . 'footer.php');

?>
