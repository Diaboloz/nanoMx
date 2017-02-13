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

$query = sql_query("SELECT type, name, fup, private, userlist, moderator, postperm FROM $table_forums WHERE fid=" . intval($fid) . " AND type IN('forum','sub')");
$forums = sql_fetch_object($query);

if (!is_object($forums)) {
    return mxbExitMessage(_TEXTNOFORUM, true);
}

if (mxbIsAnonymous()) {
    return mxbExitMessage(_BADNAME, true);
}

if (!mxbIsModeratorInForum($forums)) {
    return mxbExitMessage(_TEXTNOACTION, true);
}

if (!mxbPrivateCheck($forums)) {
    return mxbExitMessage(_PRIVFORUMMSG, true);
}
// Hier wird gekuckt ob wir ueber einen Link kommen und jump ggf. definiert
$jumper = mxbGetJumplink();

if ($tid && $fid) {
    $query = sql_query("SELECT subject FROM $table_threads WHERE tid=" . intval($tid));
    $row = sql_fetch_object($query);
    $threadname = $row->subject;
    $threadname = stripslashes($threadname);
}
//  Link-ergänzungen
if ($linkstatus == 'on') {
    $queryjumplink = sql_query("SELECT name, fid FROM $table_forums WHERE fid=" . intval($jumplink));
    $fupjumplink = sql_fetch_object($queryjumplink);
    if ($linktype == "thread") {
        $postaction = "<a href=\"" . MXB_BM_FORUMDISPLAY1 . "fid=" . $fupjumplink->fid . "\">" . $fupjumplink->name . "</a> &gt; <a href=\"" . MXB_BM_VIEWTHREAD1 . "tid=$tid$jumper\">$threadname</a> &gt;";
    } else {
        $postaction = "<a href=\"" . MXB_BM_FORUMDISPLAY1 . "fid=" . $fupjumplink->fid . "\">" . $fupjumplink->name . "</a> &gt; <a href=\"" . MXB_BM_FORUMDISPLAY1 . "fid=$fid$jumper\">" . $forums->name . "</a> &gt; <a href=\"" . MXB_BM_VIEWTHREAD1 . "tid=$tid$jumper\">$threadname</a> &gt;";
    }
} else {
    if ($forums->type == 'forum') {
        $postaction = "<a href=\"" . MXB_BM_FORUMDISPLAY1 . "fid=$fid\">" . $forums->name . "</a> &gt; <a href=\"" . MXB_BM_VIEWTHREAD1 . "tid=$tid\">$threadname</a> &gt; ";
    } else {
        $query = sql_query("SELECT name, fid FROM $table_forums WHERE fid=" . intval($forums->fup));
        $fup = sql_fetch_object($query);
        $postaction = "<a href=\"" . MXB_BM_FORUMDISPLAY1 . "fid=" . $fup->fid . "\">" . $fup->name . "</a> &gt; <a href=\"" . MXB_BM_FORUMDISPLAY1 . "fid=$fid\">" . $forums->name . "</a> &gt; <a href=\"" . MXB_BM_VIEWTHREAD1 . "tid=$tid\">$threadname</a> &gt; ";
    }
}

if ($action == "delete") {
    $postaction .= _TEXTDELETETHREAD;
} elseif ($action == "top") {
    $postaction .= _TEXTTOPTHREAD;
} elseif ($action == "close") {
    $postaction .= _TEXTCLOSETHREAD;
} elseif ($action == "move") {
    $postaction .= _TEXTMOVETHREAD;
} elseif ($action == "getip") {
    $postaction .= _TEXTGETIP;
} elseif ($action == "bump") {
    $postaction .= _TEXTBUMPTHREAD;
} elseif ($action == "linkthread") {
    $postaction .= _TEXTNEWTHREADLINK;
}

$mxbnavigator->add(false, $postaction);
// ------------------------------------------------------------------------------
// Action = delete
// ------------------------------------------------------------------------------
if ($action == "delete") {
    if (empty($deletesubmit)) {

        ?>

<form method="post" action="<?php echo MXB_BM_TOPICADMIN1 ?>action=delete">

<input type="hidden" name="fid" value="<?php echo $fid?>" />
<input type="hidden" name="tid" value="<?php echo $tid?>" />
<input type="hidden" name="username" value="<?php echo $eBoardUser['username']?>">
<?php
        if ($linkstatus == 'on') {

            ?>
<input type="hidden" name="jumplink" value="<?php echo $jumplink?>" />
<input type="hidden" name="lid" value="<?php echo $lid?>" />
<input type="hidden" name="linktype" value="thread" />
<input type="hidden" name="linkstatus" value="on" />
<?php
        }
        echo mxbMessageScreen('<center>' . _MXBSHUREDELETETHREAD . '<br/><br/><br/><br/><input type="submit" name="deletesubmit" value="' . _TEXTDELETETHREAD . '" /><br/><br/>' . _GOBACK . '</center>');

        ?>
</form>

<?php
    }

    if (!empty($deletesubmit)) {
        $scount = sql_query("SELECT COUNT(pid) as nbsites FROM $table_posts WHERE tid=" . intval($tid));
        $row = sql_fetch_object($scount);
        $subtract = $row->nbsites;

        $subtract++;

        $query = sql_query("SELECT type, fup, lastpost FROM $table_forums WHERE fid=" . intval($fid));
        $for = sql_fetch_object($query);
        $postingtime = $for->lastpost;

        $query = sql_query("SELECT author FROM $table_posts WHERE tid=" . intval($tid));
        while ($result = sql_fetch_object($query)) {
            sql_query("UPDATE $table_members SET postnum=postnum-1 WHERE username='" . substr($result->author, 0, 25) . "'");
            // array zum anpassen der Userpostings
            $check_authors[$result->author] = $result->author;
        }

        $query = sql_query("SELECT author, subject FROM $table_threads WHERE tid=" . intval($tid));
        $row = sql_fetch_object($query);
        $origauthor = $row->author;
        $subject = $row->subject;

        sql_query("DELETE FROM $table_threads WHERE tid=" . intval($tid));
        sql_query("DELETE FROM $table_posts WHERE tid=" . intval($tid));
        sql_query("DELETE FROM $table_links WHERE type='thread' AND fromid=" . intval($tid));
        sql_query("UPDATE $table_forums SET threads=threads-1, posts=posts-'$subtract' WHERE fid=" . intval($fid));
        // array zum anpassen der Userpostings
        $check_authors[$origauthor] = $origauthor;
        mxbLastPostForum($fid, $postingtime);

        if ($for->type == 'sub') {
            sql_query("UPDATE $table_forums SET threads=threads-1, posts=posts-'$subtract' WHERE fid='" . intval($for->fup) . "'");
            mxbLastPostForum($for->fup, $postingtime);
        }
        // 
        if (($emailalltomoderator == 'on') || ($emailalltoadmin == 'on')) {
            $theurl = trim(MX_HOME_URL, '/') . "/" . MXB_BM_FORUMDISPLAY1 . 'fid=' . $fid;
            $theurl = preg_replace("/\/{1,}modules/", "/modules", $theurl);
            $mailsubject = '[' . $bbname . '] ' . _EMAILSUPPRNOTIFYSUBJECT . " $subject";
            $mailmessage = _EMAILSUPPRNOTIFYINTRO . "$subject\n\n" . _EMAILNOTIFYINTRO2 . "\n\n$theurl\n\n" . _EMAILNOTIFYEND;

            if ($mailondele == 'on' && $emailalltoadmin == 'on') {
                mxbNotifyAdmin($fid, $mailsubject, $mailmessage, "notifydelete");
            }

            if ($moderatormailondelete == 'on' && $emailalltomoderator == 'on') {
                mxbNotifyModerator($fid, $mailsubject, $mailmessage, "notifydelete");
            }
        }
        // 
        if (isset($check_authors)) {
            foreach($check_authors as $check_autho) {
                mxbRepairUserPostNum($check_autho);
            }
            unset($check_authors, $check_autho);
        }
        echo mxbMessageScreen(_DELETETHREADMSG);
        echo mxbRedirectScript(MXB_BM_FORUMDISPLAY1 . 'fid=' . $fid . $jumper, 1250);
    }
}
// ------------------------------------------------------------------------------
// Action = close
// ------------------------------------------------------------------------------
if ($action == "close") {
    $query = sql_query("SELECT closed FROM $table_threads WHERE tid=" . intval($tid));
    $row = sql_fetch_object($query);

    $closetext = (empty($row->closed) || $row->closed != "yes") ? _TEXTCLOSETHREAD : _TEXTOPENTHREAD;
    $titleCloseText = $closetext;

    if (empty($closesubmit)) {
        $closetext .= '<br /><br /><input class="button2" type="submit" name="closesubmit" value="' . $closetext . '" />';

        ?>
<h2><?php echo $titleCloseText ?></h2>   
<div class="panel bgcolor3">            
        <form method="post" action="<?php echo MXB_BM_TOPICADMIN1 ?>action=close">
        <?php echo mxbMessageScreen($closetext)?>   
<input type="hidden" name="username" value="<?php echo $eBoardUser['username']?>" />
<input type="hidden" name="fid" value="<?php echo $fid?>" />
<input type="hidden" name="tid" value="<?php echo $tid?>" />
<?php
        if ($linkstatus == 'on') {

            ?>
<input type="hidden" name="jumplink" value="<?php echo $jumplink?>" />
<input type="hidden" name="lid" value="<?php echo $lid?>" />
<input type="hidden" name="linktype" value="thread" />
<input type="hidden" name="linkstatus" value="on" />
<?php
        }

        ?>
        </form>
  </div>
</div>
<?php
    }

    if (!empty($closesubmit)) {
        if (empty($row->closed) || $row->closed != "yes") {
            sql_query("UPDATE $table_threads SET closed='yes' WHERE tid=" . intval($tid));
        } else {
            sql_query("UPDATE $table_threads SET closed='' WHERE tid=" . intval($tid));
        }

        echo mxbMessageScreen(_CLOSETHREADMSG);
        echo mxbRedirectScript(MXB_BM_VIEWTHREAD1 . 'tid=' . $tid . $jumper, 1250);
    }
}
// ----------------------------------------------------------------------------//
// Action == move  : On deplace le message
// ----------------------------------------------------------------------------//
if ($action == "move") {
    if (empty($movesubmit)) {
        $forumselect = "<select name=\"moveto\">\n";
        $queryfor = sql_query("SELECT * FROM $table_forums WHERE fup='' AND type='forum' ORDER BY displayorder");
        while ($forum = sql_fetch_object($queryfor)) {
            $forumselect .= "<option value=\"" . $forum->fid . "\"> &nbsp; &gt; " . $forum->name . "</option>";

            $querysub = sql_query("SELECT * FROM $table_forums WHERE fup='" . intval($forum->fid) . "' AND type='sub' ORDER BY displayorder");
            while ($sub = sql_fetch_object($querysub)) {
                $forumselect .= '<option value="' . $sub->fid . '">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &gt; ' . $sub->name . '</option>';
            }
            $forumselect .= '<option value="0">&nbsp;</option>';
        }

        $querygrp = sql_query("SELECT * FROM $table_forums WHERE type='group' ORDER BY displayorder");
        while ($group = sql_fetch_object($querygrp)) {
            $forumselect .= '<option value="0">' . $group->name . '</option>';
            $forumselect .= '<option value="0">--------------------</option>';

            $queryfor = sql_query("SELECT * FROM $table_forums WHERE fup='" . intval($group->fid) . "' AND type='forum' ORDER BY displayorder");
            while ($forum = sql_fetch_object($queryfor)) {
                $forumselect .= "<option value=\"" . $forum->fid . "\"> &nbsp; &gt; " . $forum->name . "</option>";

                $querysub = sql_query("SELECT * FROM $table_forums WHERE fup='" . intval($forum->fid) . "' AND type='sub' ORDER BY displayorder");
                while ($sub = sql_fetch_object($querysub)) {
                    $forumselect .= '<option value="' . $sub->fid . '">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &gt; ' . $sub->name . '</option>';
                }
            }
            $forumselect .= "<option value=\"0\">&nbsp;</option>";
        }
        $forumselect .= "</select>";

        ?>

<h2><?php echo _TEXTMOVETHREAD?></h2>   
<div class="panel bgcolor3">
        <form method="post" action="<?php echo MXB_BM_TOPICADMIN1 ?>action=move">
            <fieldset>
                <dl>
                    <dt>
                        <label><?php echo _TEXTMOVETO?></label>
                    </dt>
                    <dd><?php echo $forumselect?></dd>
                </dl>
            <input type="hidden" name="fid" value="<?php echo $fid?>" />
            <input type="hidden" name="tid" value="<?php echo $tid?>" />
            <input type="hidden" name="username" value="<?php echo $eBoardUser['username']?>">        
<?php
        if ($linkstatus == 'on') {

            ?>
<input type="hidden" name="jumplink" value="<?php echo $jumplink?>" />
<input type="hidden" name="lid" value="<?php echo $lid?>" />
<input type="hidden" name="linktype" value="thread" />
<input type="hidden" name="linkstatus" value="on" />
<?php
        }

        ?>                                        
            <input class="button2" type="submit" name="movesubmit" value="<?php echo _TEXTMOVETHREAD?>" />
            </fieldset>
        </form>
  </div>
</div>
<?php
    }

    if (!empty($movesubmit)) {
        $query = sql_query("SELECT type, fid, fup, lastpost FROM $table_forums WHERE fid='" . intval($moveto) . "'");
        $move = sql_fetch_object($query);

        $query = sql_query("SELECT type, fid, fup, lastpost FROM $table_forums WHERE fid=" . intval($fid));
        $thisx = sql_fetch_object($query);
        $query = sql_query("SELECT COUNT(pid) as nbsites FROM $table_posts WHERE tid=" . intval($tid));
        $row = sql_fetch_object($query);
        $subtract = $row->nbsites;
        $subtract++;

        if ($moveto) {
            sql_query("UPDATE $table_threads SET fid='$moveto' WHERE tid=" . intval($tid));
            sql_query("UPDATE $table_posts SET fid='$moveto' WHERE tid=" . intval($tid));

            sql_query("UPDATE $table_forums SET threads=threads+1, posts=posts+'$subtract' WHERE fid='" . intval($move->fid) . "'");
            sql_query("UPDATE $table_forums SET threads=threads-1, posts=posts-'$subtract' WHERE fid='" . intval($thisx->fid) . "'");

            mxbLastPostForum($move->fid, $move->lastpost);
            mxbLastPostForum($thisx->fid, $thisx->lastpost);

            if ($thisx->type == 'sub') {
                sql_query("UPDATE $table_forums SET threads=threads-1, posts=posts-'$subtract' WHERE fid='" . intval($thisx->fup) . "'");
                mxbLastPostForum($thisx->fup, "checkforum");
            }

            if ($move->type == 'sub') {
                sql_query("UPDATE $table_forums SET threads=threads+1, posts=posts+'$subtract' WHERE fid='" . intval($move->fup) . "'");
                mxbLastPostForum($move->fup, "checkforum");
            }

            echo mxbMessageScreen(_MOVETHREADMSG);
            echo mxbRedirectScript(MXB_BM_VIEWTHREAD1 . 'tid=' . $tid . $jumper, 1250);
        } else {
            echo mxbMessageScreen(_MOVETHREADMSGERR);
            echo mxbRedirectScript(MXB_BM_VIEWTHREAD1 . 'tid=' . $tid . $jumper, 3000);
        }
    }
}

if ($action == "top") {
    $query = sql_query("SELECT topped FROM $table_threads WHERE tid=" . intval($tid));
    $row = sql_fetch_object($query);

    $toppedtext = (empty($row->topped)) ? _TEXTTOPTHREAD : _TEXTUNTOPTHREAD;

    if (empty($topsubmit)) {

        ?>
<h2><?php echo $toppedtext?></h2>   
<div class="panel bgcolor3">
    <div class="inner">
        <form method="post" action="<?php echo MXB_BM_TOPICADMIN1 ?>action=top">
        
<input type="hidden" name="fid" value="<?php echo $fid?>" />
<input type="hidden" name="tid" value="<?php echo $tid?>" />
<input type="hidden" name="username" value="<?php echo $eBoardUser['username']?>">
<?php
        if ($linkstatus == 'on') {

            ?>
<input type="hidden" name="jumplink" value="<?php echo $jumplink?>" />
<input type="hidden" name="lid" value="<?php echo $lid?>" />
<input type="hidden" name="linktype" value="thread" />
<input type="hidden" name="linkstatus" value="on" />
<?php
        }

        ?>
        <input class="button2" type="submit" name="topsubmit" value="<?php echo $toppedtext?>" />
        </form>
  </div>
</div>
<?php
    }
    if (!empty($topsubmit)) {
        if (empty($row->topped)) {
            sql_query("UPDATE $table_threads SET topped='1' WHERE tid=" . intval($tid));
        } else {
            sql_query("UPDATE $table_threads SET topped='0' WHERE tid=" . intval($tid));
        }

        echo mxbMessageScreen(_TOPTHREADMSG);
        echo mxbRedirectScript(MXB_BM_VIEWTHREAD1 . 'tid=' . $tid . $jumper, 1250);
    }
}

if ($action == "getip") {
    if (!$pid) {
        $query = sql_query("SELECT * FROM $table_threads WHERE tid=" . intval($tid));
    } else {
        $query = sql_query("SELECT * FROM $table_posts WHERE pid=" . intval($pid));
    }

    $ipinfo = sql_fetch_object($query);

    ?>
<h2><?php echo _TEXTGETIP?></h2>   
<div class="panel bgcolor3">
    <div class="inner">
        <p><?php echo _TEXTYESIP?> <strong><?php echo $ipinfo->useip ?></strong></p>
        <form method="post" action="<?php echo MXB_BM_CP1 ?>action=ipban">
<?php

    if ($eBoardUser['isadmin']) {
        $ip = explode(".", $ipinfo->useip);
        $query = sql_query("SELECT * FROM $table_banned WHERE (ip1='$ip[0]' OR ip1='-1') AND (ip2='$ip[1]' OR ip2='-1') AND (ip3='$ip[2]' OR ip3='-1') AND (ip4='$ip[3]' OR ip4='-1')");
        $result = sql_fetch_object($query);

        if ($result) {
            $buttontext = _TEXTUNBANIP;

            for($i = 1; $i <= 4; ++$i) {
                $j = 'ip' . $i;
                if ($result->$j == -1) { // potential error ??: $result->$i
                    $result->$j = "*";
                    $foundmask = 1;
                }
            }
            if ($foundmask) {
                $ipmask = "<b>" . $result->ip1 . $result->ip2 . $result->ip3 . $result->ip4 . "</b>";
                eval(_evalipmask);
                echo _BANNEDIPMASK;
            } else {
                echo _TEXTBANNEDIP;
            }

            echo "<input type=\"hidden\" name=\"delete" . $result->id . "\" value=\"" . $result->id . "\" />";
        } else {
            $buttontext = _TEXTBANIP;
            for($i = 1; $i <= 4; ++$i) {
                $j = $i - 1;
                echo "<input type=\"hidden\" name=\"newip$i\" value=\"$ip[$j]\" />";
            }
        }

        ?>
        <input class="button2" type="submit" name="ipbansubmit" value="<?php echo $buttontext?>" />

<?php
    }

    echo '
        </form>
  </div>
</div>';
}

if ($action == "bump") {
    if (empty($bumpsubmit)) {

        ?>
<h2><?php echo _TEXTBUMPTHREAD ?></h2>   
<div class="panel bgcolor3">
    <div class="inner">
        <p><?php echo _TEXTBUMPTHREAD ?></p>            
        <form method="post" action="<?php echo MXB_BM_TOPICADMIN1 ?>action=bump">
<input type="hidden" name="fid" value="<?php echo $fid?>" />
<input type="hidden" name="tid" value="<?php echo $tid?>" />
<input type="hidden" name="username" value="<?php echo $eBoardUser['username']?>">
<?php
        if ($linkstatus == 'on') {

            ?>
<input type="hidden" name="jumplink" value="<?php echo $jumplink?>" />
<input type="hidden" name="lid" value="<?php echo $lid?>" />
<input type="hidden" name="linktype" value="thread" />
<input type="hidden" name="linkstatus" value="on" />
<?php
        }

        ?>
        <input class="button2" type="submit" name="bumpsubmit" value="<?php echo _TEXTBUMPTHREAD?>" />
       </form>
  </div>
</div>

<?php
    }

    if (!empty($bumpsubmit)) {
        $queryforum = sql_query("SELECT type, fup FROM $table_forums WHERE fid=" . intval($fid));
        $typeofforum = sql_fetch_object($queryforum);

        sql_query("UPDATE $table_threads SET lastpost='" . time() . "|" . $eBoardUser['username'] . "' WHERE tid=" . intval($tid) . " AND fid=" . intval($fid));
        sql_query("UPDATE $table_forums SET lastpost='" . time() . "|" . $eBoardUser['username'] . "' WHERE fid=" . intval($fid));

        if ($typeofforum->type == 'sub') {
            sql_query("UPDATE $table_forums SET lastpost='" . time() . "|" . $eBoardUser['username'] . "' WHERE fid=" . intval($typeofforum->fup));
        }

        if ($linkforumstatus == 'on') {
            $queryforumlinks = sql_query("SELECT lid, toid FROM $table_links WHERE type='forum' AND fromid=" . intval($fid) . " AND status='on'");
            while ($forumlink = sql_fetch_object($queryforumlinks)) {
                sql_query("UPDATE $table_forums SET lastpost='" . time() . "|" . $eBoardUser['username'] . "' WHERE fid=" . intval($forumlink->toid));
                sql_query("UPDATE $table_links SET lastpost='" . time() . "|" . $eBoardUser['username'] . "' WHERE lid=" . intval($forumlink->lid));
            }
        }

        if ($linkthreadstatus == 'on') {
            $querythreadlinks = sql_query("SELECT lid, toid FROM $table_links WHERE type='thread' AND fromid=" . intval($tid) . " AND status='on'");
            while ($threadlink = sql_fetch_object($querythreadlinks)) {
                sql_query("UPDATE $table_forums SET lastpost='" . time() . "|" . $eBoardUser['username'] . "' WHERE fid=" . intval($threadlink->toid));
                sql_query("UPDATE $table_links SET lastpost='" . time() . "|" . $eBoardUser['username'] . "' WHERE lid=" . intval($threadlink->lid));
            }
        }

        echo mxbMessageScreen(_BUMPTHREADMSG);
        echo mxbRedirectScript(MXB_BM_VIEWTHREAD1 . 'tid=' . $tid . $jumper, 1250);
    }
}
//  Anfang Link-Thread
if ($action == "linkthread") {
    if ($linkthreadstatus != 'on') {
        return mxbExitMessage(_TEXTLINKTHREADSTATUSDISABLED, false);
    }

    if (empty($linkthreadsubmit)) {
        $countlinks = 0;

        $querythread = sql_query("SELECT subject FROM $table_threads WHERE tid=" . intval($tid));
        $thread = sql_fetch_object($querythread);

        $queryforum = sql_query("SELECT name FROM $table_forums WHERE fid=" . intval($fid));
        $threadforum = sql_fetch_object($queryforum);

        $activelinks = "<select name=\"deletelinkid\">\n";
        $activelinks .= "<option value=\"0\">&nbsp;</option>\n";
        $activelinks .= "<option value=\"all\">" . _TEXTALL . "</option>\n";

        $queryforlinks = sql_query("SELECT * FROM $table_links WHERE type='thread' AND fromid=" . intval($tid));
        while ($threadlinks = sql_fetch_object($queryforlinks)) {
            $countlinks++;
            $queryforumname = sql_query("SELECT name FROM $table_forums WHERE fid='" . intval($threadlinks->toid) . "'");
            $activelinkname = sql_fetch_object($queryforumname);

            $activelinks .= "<option value=\"" . $threadlinks->lid . "\"> &gt;$activelinkname->name </option>\n";
        }
        $activelinks .= "</select>";

        ?>
<h2><?php echo _TEXTNEWTHREADLINK ?></h2>   
<div class="panel bgcolor3">
    <div class="inner">
        <form method="post" action="<?php echo MXB_BM_TOPICADMIN1 ?>action=linkthread">
            <fieldset>
                <dl>
                    <dt><label><?php echo _TEXTNEWLINKFROMTHREAD?></label></dt>
                    <dd><label>"<?php echo $thread->subject?>"&nbsp;(<?php echo _TEXTFROMFORUM?>"<?php echo $threadforum->name?>")</label></dd>

                    <dt><label><?php echo _TEXTNEWLINKTOFORUM?></label></dt>
                    <dd><label><select name="tofid"><?php echo mxbLargeSelectWithLinks()?></select></label></dd>


                    <dt><label><?php echo _TEXTOLDLINKTOFORUM1, $countlinks, _TEXTOLDLINKTOFORUM2?></label></dt>
                    <dd><label><?php echo $activelinks?>&nbsp;&nbsp;<?php echo _TEXTDELETELINKTOFORUM?>&nbsp;<input type="checkbox" name="deletelink" value="yes"></label></dd>
                </dl>
            </fieldset>
<input type="hidden" name="fid" value="<?php echo $fid?>" />
<input type="hidden" name="tid" value="<?php echo $tid?>" />
<input type="hidden" name="username" value="<?php echo $eBoardUser['username']?>">

<?php
        if ($linkstatus == 'on') {

            ?>
<input type="hidden" name="jumplink" value="<?php echo $jumplink?>" />
<input type="hidden" name="lid" value="<?php echo $lid?>" />
<input type="hidden" name="linktype" value="thread" />
<input type="hidden" name="linkstatus" value="on" />
<?php
        }

        ?>
    <input class="button2" type="submit" name="linkthreadsubmit" value="<?php echo _TEXTSUBMITCHANGES ?>" />
    </form>
  </div>
</div>

<?php
    }

    if (!empty($linkthreadsubmit)) {
        // Links zu loeschen ?
        if (!empty($deletelink) && !empty($deletelinkid)) {
            if ($deletelinkid == "all") {
                $result = sql_query("SELECT * FROM $table_links WHERE type='thread' AND fromid=" . intval($tid));
                while ($threadlink = sql_fetch_object($result)) {
                    $linktofid = $threadlink->toid;
                    $linkpostingtime = $threadlink->lastpost;
                    sql_query("DELETE FROM $table_links WHERE lid='" . $threadlink->lid . "'");
                    mxbLastPostForum($linktofid, $linkpostingtime);
                }
            } else {
                $result = sql_query("SELECT * FROM $table_links WHERE lid='" . intval($deletelinkid) . "'");
                $threadlink = sql_fetch_object($result);
                $linktofid = $threadlink->toid;
                $linkpostingtime = $threadlink->lastpost;
                sql_query("DELETE FROM $table_links WHERE lid='" . intval($deletelinkid) . "'");
                mxbLastPostForum($linktofid, $linkpostingtime);
            }
        } else if (!empty($tofid) && !empty($tid)) {
            // hier werden Links für Threads erstellt
            unset($boards);
            if ($tofid == 'all') {
                // wenn alle in Foren verlinkt werden soll, alle ID's ermitteln, ausser dem gleichen Forum
                $result = sql_query("SELECT * FROM $table_forums WHERE fid<>'" . intval($fid) . "'");
                while ($board = sql_fetch_object($result)) {
                    $boards[] = intval($board->fid);
                }
            } else if ($tofid != $fid) {
                // ansonsten die ID des einzelnen Forums als Array fuer die spaetere Verwendung speichern
                $boards[] = intval($tofid);
            }
            if (isset($boards)) {
                // den Lastpost des entsprechenden Threads ermitteln
                $result = sql_query("SELECT lastpost FROM $table_threads WHERE tid=" . intval($tid));
                $thread = sql_fetch_object($result);
                // alle bereits vorhandenen Links einfach loeschen
                sql_query("DELETE FROM $table_links WHERE type='thread' AND fromid=" . intval($tid) . " AND toid IN(" . implode(',', $boards) . ")");
                // Schleife durch alle Foren, in die verlinkt werden soll
                foreach($boards as $tofid) {
                    // die Links in die Tabelle einfuegen
                    sql_query("INSERT INTO $table_links VALUES ('', 'thread', '" . intval($tid) . "', '" . intval($tofid) . "', 'on', '" . $thread->lastpost . "')");
                    // Forumsstat aktualisieren
                    mxbLastPostForum($tofid, "checkforum");
                }
            }
        }
        echo mxbMessageScreen(_LINKTHREADMSG);
        echo mxbRedirectScript(MXB_BM_VIEWTHREAD1 . 'tid=' . $tid . $jumper, 1250);
    }
}
//  Ende link-thread
include_once(MXB_BASEMODINCLUDE . 'footer.php');

?>
