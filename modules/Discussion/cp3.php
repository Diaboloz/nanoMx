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

include_once(dirname(__FILE__) . DS . 'includes' . DS . 'header.php');
set_time_limit(0);

if (!$eBoardUser['isadmin']) {
    return mxbExitMessage(_NOTADMIN, true);
}
$action = (isset($_REQUEST['action'])) ? $_REQUEST['action'] : '';

if ($action == 'prunesubmit' && !empty($prunecancel)) {
    header('Location: ' . htmlspecialchars_decode(MXB_BM_CP30) . '&action=prune');
}
if ($action == 'countforum' && !empty($countcancel)) {
    header('Location: ' . htmlspecialchars_decode(MXB_BM_CP0));
}
if ($action == 'resetforum' && !empty($resetcancel)) {
    header('Location: ' . htmlspecialchars_decode(MXB_BM_CP0));
}

$mxbnavigator->add(false, _TEXTCP);

?>
<table cellspacing="0" cellpadding="0" border="0" width="<?php echo $tablewidth ?>" align="center">
<tr><td class="bordercolor">

<table border="0" cellspacing="<?php echo $borderwidth ?>" cellpadding="<?php echo $tablespace ?>" width="100%">
<?php
mxbAdminMenu();

/*
//
// Prune Forum
//
*/
if ($action == "prune") {
    if (empty($prunesubmit)) {
        $forumselect = "<select name=\"forumprune\">\n";
        $forumselect .= "<option value=\"0\"></option>\n";

        if ($eBoardUser['superuser']) {
            $forumselect .= "<option value=\"" . _TEXTALL . "\">" . _TEXTALL . "</option>\n";
        }

        $queryfor = sql_query("SELECT fid, name FROM $table_forums WHERE fup='' AND type='forum' ORDER BY displayorder") ;
        while ($forum = sql_fetch_assoc($queryfor)) {
            $forumselect .= "<option value=\"$forum[fid]\"> &nbsp; &gt; $forum[name]</option>";

            $querysub = sql_query("SELECT fid, name FROM $table_forums WHERE fup='$forum[fid]' AND type='sub' ORDER BY displayorder") ;
            while ($sub = sql_fetch_assoc($querysub)) {
                $forumselect .= "<option value=\"$sub[fid]\">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &gt; $sub[name]</option>";
            }

            $forumselect .= "<option value=\"0\">&nbsp;</option>";
        }

        $querygrp = sql_query("SELECT fid, name FROM $table_forums WHERE type='group' ORDER BY displayorder") ;
        while ($group = sql_fetch_assoc($querygrp)) {
            $forumselect .= "<option value=\"0\">$group[name]</option>";
            $forumselect .= "<option value=\"0\">--------------------</option>";

            $queryfor = sql_query("SELECT fid, name FROM $table_forums WHERE fup='$group[fid]' AND type='forum' ORDER BY displayorder") ;
            while ($forum = sql_fetch_assoc($queryfor)) {
                $forumselect .= "<option value=\"$forum[fid]\"> &nbsp; &gt; $forum[name]</option>";

                $querysub = sql_query("SELECT fid, name FROM $table_forums WHERE fup='$forum[fid]' AND type='sub' ORDER BY displayorder") ;
                while ($sub = sql_fetch_assoc($querysub)) {
                    $forumselect .= "<option value=\"$sub[fid]\">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &gt; $sub[name]</option>";
                }
            }

            $forumselect .= "<option value=\"0\">&nbsp;</option>";
        }
        $forumselect .= "</select>";

        ?>

    <tr class="altbg2">
    <td align="center">
    <br />
    <form method="post" action="<?php echo MXB_BM_CP31 ?>action=prunesubmit ">
    <table cellspacing="0" cellpadding="0" border="0" width="100%" align="center">
    <tr><td class="bordercolor">

    <table border="0" cellspacing="<?php echo $borderwidth ?>" cellpadding="<?php echo $tablespace ?>" width="100%">

    <tr class="mxb-header">
    <td colspan="2"><?php echo _TEXTPRUNE ?></td>
    </tr>

    <tr class="altbg1">
    <td class="tablerow"><?php echo _PRUNEWHERE ?></td>
    <td><input type="text" name="prunedays" size="4" /><?php echo _MXB_DAYS ?></td>
    </tr>

    <tr class="altbg1">
    <td class="tablerow"><?php echo _PRUNEIN ?></td>
    <td><?php echo $forumselect ?></td>
    </tr>

    </table>
    </td></tr></table>
    <center><br/><input type="submit" name="prunesubmit" value="<?php echo _TEXTSUBMITCHANGES ?>" /></center>
    </form>

    </td>
    </tr>

    <?php
    }
}

/*
//
// Prune Forum
//
*/
if ($action == "prunesubmit") {
    if (!empty($prunesubmit)) {
        if (!$prunedays) {
            $prunedays = 0;
        }
        // if no forum was selected, exit here
        if (!$forumprune) {

            ?>
<tr class="altbg2">
<td align="center">
<br />
<form method="post" action="<?php echo MXB_BM_CP31 ?>action=prune ">
<table cellspacing="0" cellpadding="0" border="0" width="100%" align="center">
<tr><td class="bordercolor">

<table border="0" cellspacing="<?php echo $borderwidth ?>" cellpadding="<?php echo $tablespace ?>" width="100%">

<tr class="mxb-header">
<td colspan="2"><?php echo _TEXTPRUNE ?></td>
</tr>

<tr class="altbg1">
<td class="tablerow"><?php echo _SELECTEDFORUMERROR ?></td>
</tr>

</table>
</td></tr></table>

<center><br/><input type="submit" name="cancel" value="<?php echo _TEXTPREVIOUS ?>" /></center>
</form>

</td>
</tr>

<?php
            echo "</table></td></tr></table>";
            include_once(MXB_BASEMODINCLUDE . 'footer.php');
            exit;
        }

        if ($forumprune == _TEXTALL) {
            $forumprunename['name'] = _SELECTEDFORUMALL;
        } else {
            $queryfor = sql_query("SELECT fid, name FROM $table_forums WHERE fid='$forumprune'") ;
            $forumprunename = sql_fetch_assoc($queryfor);
        }
        // here we call the confirming-screen
        if (empty($pruneok)) {

            ?>

<tr class="altbg2">
<td align="center">
<br />
<form method="post" action="<?php echo MXB_BM_CP31 ?>action=prunesubmit ">
<table cellspacing="0" cellpadding="0" border="0" width="100%" align="center">
<tr><td class="bordercolor">

<table border="0" cellspacing="<?php echo $borderwidth ?>" cellpadding="<?php echo $tablespace ?>" width="100%">

<tr class="mxb-header">
<td colspan="2"><?php echo _TEXTPRUNE ?></td>
</tr>

<tr class="altbg1">
<td class="tablerow"><?php echo _SELECTEDTIME ?></td>
<td><?php echo $prunedays, '&nbsp;', _MXB_DAYS ?></td>
</tr>

<tr class="altbg1">
<td class="tablerow"><?php echo _SELECTEDFORUM ?></td>
<td><?php echo $forumprunename['name'] ?></td>
</tr>

</table>
</td></tr></table>
<input type="hidden" name="forumprune" value="<?php echo $forumprune ?>"/>
<input type="hidden" name="prunesubmit" value="$prunesubmit"/>
<input type="hidden" name="prunedays" value="<?php echo $prunedays ?>"/>
<center><br/><input type="submit" name="pruneok" value="<?php echo _YES ?>" />&nbsp;&nbsp;<input type="submit" name="prunecancel" value="<?php echo _NO ?>" /></center>
</form>

</td>
</tr>

<?php
        }
        // if user confirmed prune-action, we carry on
        if (!empty($pruneok)) {
            $pruneall = false;

            if (empty($prunedays)) {
                $prunedate = time();
                $pruneall = true;
            } else {
                $prunedate = time() - (86400 * $prunedays);
            }
            // IF(LENGTH(LEFT(lastpost, INSTR(lastpost, '|')-1))<10, CONCAT('0',lastpost), lastpost) as  lastpost
            // only a second exit point, if something went wrong with the datatransfair. We don't want
            // to delete something we are not sure about it.
            if ($forumprune == "") {
                echo "</table></td></tr></table>";
                include_once(MXB_BASEMODINCLUDE . 'footer.php');
                exit;
            } elseif ($forumprune == _TEXTALL) {
                $querythread = sql_query("SELECT IF(LENGTH(LEFT(lastpost, INSTR(lastpost, '|')-1))<10, CONCAT('0',lastpost), lastpost) as  lastpost, tid, fid, author FROM $table_threads WHERE dateline < '$prunedate'") ;
            } else {
                $querythread = sql_query("SELECT IF(LENGTH(LEFT(lastpost, INSTR(lastpost, '|')-1))<10, CONCAT('0',lastpost), lastpost) as  lastpost, tid, fid, author FROM $table_threads WHERE fid='$forumprune' AND dateline < '$prunedate'") ;
            } while ($thread = sql_fetch_assoc($querythread)) {
                if ($thread['lastpost'] < $prunedate) {
                    // here we count how many posts to delete
                    $querycounter = sql_query("SELECT COUNT(pid) as nbsites FROM $table_posts WHERE tid='$thread[tid]'") ;
                    $countrow = sql_fetch_assoc($querycounter);
                    $subtract = $countrow['nbsites'];
                    // and increase it by one for the thread
                    $subtract++;

                    $querypost = sql_query("SELECT pid, tid, fid, author FROM $table_posts WHERE tid='$thread[tid]'") ;
                    while ($post = sql_fetch_assoc($querypost)) {
                        sql_query("DELETE FROM $table_posts WHERE pid='$post[pid]'") ;
                        // array zum anpassen der Userpostings
                        $check_authors[$post['author']] = $post['author'];
                    }

                    $count = sql_query("SELECT IF(LENGTH(LEFT(lastpost, INSTR(lastpost, '|')-1))<10, CONCAT('0',lastpost), lastpost) as  lastpost, type, fup FROM $table_forums WHERE fid='$thread[fid]'") ;
                    $deletefrom = sql_fetch_assoc($count);

                    if ($deletefrom['type'] == 'sub') {
                        sql_query("UPDATE $table_forums SET posts=posts-'$subtract', threads=threads-1 WHERE fid='$deletefrom[fup]'") ;

                        $countmain = sql_query("SELECT IF(LENGTH(LEFT(lastpost, INSTR(lastpost, '|')-1))<10, CONCAT('0',lastpost), lastpost) as  lastpost, fid FROM $table_forums WHERE fid='$deletefrom[fup]'") ;
                        $deletefrommain = sql_fetch_assoc($countmain);

                        if (($deletefrommain['lastpost'] <= $prunedate) && (!$pruneall || $forumprune == _TEXTALL || $forumprune == $deletefrommain['fid'])) {
                            sql_query("UPDATE $table_forums SET lastpost='' WHERE fid='$deletefrom[fup]'");
                        }
                    } else {
                        $nosub = false;
                        $howmany = 0;
                        $countforsub = sql_query("SELECT fid FROM $table_forums WHERE fup='$thread[fid]'") ;
                        while ($subcounter = sql_fetch_assoc($countforsub)) {
                            $howmany++;
                        }
                        if ($howmany == 0) {
                            $nosub = true;
                        }
                    }

                    if (($deletefrom['lastpost'] <= $prunedate) && (!$pruneall || $forumprune == _TEXTALL || ($forumprune == $thread['fid'] && $nosub))) {
                        sql_query("UPDATE $table_forums SET lastpost='' WHERE fid='$thread[fid]'") ;
                    }

                    $querythreadinfo = sql_query("SELECT lastpost FROM $table_threads WHERE tid='$thread[tid]'");
                    $threadinfo = sql_fetch_assoc($querythreadinfo);
                    $postingtime = $threadinfo['lastpost'];

                    sql_query("DELETE FROM $table_threads WHERE tid='$thread[tid]'") ;

                    $querylinks = sql_query("SELECT lid, toid FROM $table_links WHERE type='thread' AND fromid='$thread[tid]'") ;
                    while ($link = sql_fetch_assoc($querylinks)) {
                        sql_query("DELETE FROM $table_links WHERE lid='$link[lid]'") ;
                        if ($forumprune != _TEXTALL) {
                            mxbLastPostForum($link['toid'], $postingtime);
                        }
                    }

                    sql_query("UPDATE $table_forums SET posts=posts-'$subtract', threads=threads-1 WHERE fid='$thread[fid]'") ;
                    // array zum anpassen der Userpostings
                    $check_authors[$thread['author']] = $thread['author'];
                }
            }
            if (isset($check_authors)) {
                foreach($check_authors as $check_autho) {
                    mxbRepairUserPostNum($check_autho);
                }
                unset($check_authors, $check_autho);
            }
            echo mxbRedirectScript(MXB_BM_CP31 . "action=prune", 2000);
        } // End if-pruneok
    } // End if-prunesubmit
} // End case-prunesubmit
/*
//
// Forum-Counter
//
*/
if ($action == "countforum") {
    if (empty($countok)) {

        ?>

<tr class="altbg2">
<td align="center">
<br />
<form method="post" action="<?php echo MXB_BM_CP31 ?>action=countforum ">
<table cellspacing="0" cellpadding="0" border="0" width="100%" align="center">
<tr><td class="bordercolor">

<table border="0" cellspacing="<?php echo $borderwidth ?>" cellpadding="<?php echo $tablespace ?>" width="100%">

<tr class="mxb-header">
<td colspan="2"><?php echo _TEXTCOUNTFORUM ?></td>
</tr>

<tr class="altbg1">
<td class="tablerow"><?php echo _TEXTCOUNTFORUMEXPLAIN ?></td>
</tr>


</table>
</td></tr></table>
<center><br/><input type="submit" name="countok" value="<?php echo _YES ?>" />&nbsp;&nbsp;<input type="submit" name="countcancel" value="<?php echo _NO ?>" /></center>
</form>

</td>
</tr>

<?php
    } else {
        // Here is the part that counts all threads and posts, subforums and forums to
        // adjust:
        // 1. countersetting-problems in threads, subforums and forums depending on the
        // bugs XForum had
        // 2. "Last Post" entry in Threads, Subforums and Forums
        // THREAD
        // First we count our threads.
        // If the number of replies is not correct, we update it
        $countfix = 0;

        $querymember = sql_query("SELECT uid, username, postnum FROM $table_members") ;
        while ($membertocount = sql_fetch_assoc($querymember)) {
            $total = 0;

            $querythreads = sql_query("SELECT COUNT(tid) as posts FROM $table_threads WHERE author='$membertocount[username]'") ;
            $threadcounter = sql_fetch_assoc($querythreads);

            $queryposts = sql_query("SELECT COUNT(pid) as replies FROM $table_posts WHERE author='$membertocount[username]'") ;
            $postcounter = sql_fetch_assoc($queryposts);

            $total = intval($threadcounter['posts']) + intval($postcounter['replies']);

            if ($total != $membertocount['postnum']) {
                sql_query("UPDATE $table_members SET postnum='$total' WHERE uid='$membertocount[uid]'") ;
                $countfix++;
            }
        }

        $querythread = sql_query("SELECT lastpost, tid, replies FROM $table_threads") ;
        while ($threadtocount = sql_fetch_assoc($querythread)) {
            $queryposts = sql_query("SELECT COUNT(pid) as replies FROM $table_posts WHERE tid='$threadtocount[tid]'") ;
            $postcounter = sql_fetch_assoc($queryposts);

            if ($threadtocount['replies'] != $postcounter['replies']) {
                sql_query("UPDATE $table_threads SET replies='$postcounter[replies]' WHERE tid='$threadtocount[tid]'") ;
                $countfix++;
            }
            // here we fake our function to count thread new after deleting a post to count the whole thread
            // and also update all links to this thread with the newes lastpost-date
            mxbLastPostThread($threadtocount['tid'], $threadtocount['lastpost']);
        }
        // SUBFORUMS
        // Now we count our subforums
        // If the number of threads or posts is not correct, we update it
        $querysubforum = sql_query("SELECT lastpost, fid, posts, threads FROM $table_forums WHERE type='sub'") ;
        while ($subforumtocount = sql_fetch_assoc($querysubforum)) {
            $subreplies = 0;
            $subthreads = 0;
            $querythreads = sql_query("SELECT replies FROM $table_threads WHERE fid='$subforumtocount[fid]'") ;
            while ($threadcounter = sql_fetch_assoc($querythreads)) {
                $subreplies = $subreplies + $threadcounter['replies'] + 1;
                $subthreads++;
            }

            if ($subforumtocount['threads'] != $subthreads) {
                sql_query("UPDATE $table_forums SET threads='$subthreads' WHERE fid='$subforumtocount[fid]'") ;
                $countfix++;
            }

            if ($subforumtocount['posts'] != $subreplies) {
                sql_query("UPDATE $table_forums SET posts='$subreplies' WHERE fid='$subforumtocount[fid]'") ;
                $countfix++;
            }
            // here we fake our function to count forums new after deleting a thread to count the whole forum
            // new and also update all links to this forum with the newest lastpost-date
            mxbLastPostForum($subforumtocount['fid'], $subforumtocount['lastpost']);
        }
        // MAINFORUM
        // Now we have everything to update our mainforums.
        // If the number of threads or posts is not correct, we update it
        $querymainforum = sql_query("SELECT lastpost, fid, posts, threads FROM $table_forums WHERE type='forum'") ;
        while ($mainforumtocount = sql_fetch_assoc($querymainforum)) {
            $mainreplies = 0;
            $mainthreads = 0;
            $lastposttemp = '';
            $querythreads = sql_query("SELECT replies FROM $table_threads WHERE fid='$mainforumtocount[fid]'") ;
            while ($threadcounter = sql_fetch_assoc($querythreads)) {
                $mainreplies = $mainreplies + $threadcounter['replies'] + 1;
                $mainthreads++;
            }
            // here we include subforums of the mainforum if available
            $querymainsubforum = sql_query("SELECT posts, threads FROM $table_forums WHERE fup='$mainforumtocount[fid]'") ;
            while ($mainsubforumtocount = sql_fetch_assoc($querymainsubforum)) {
                $mainreplies = $mainreplies + $mainsubforumtocount['posts'];
                $mainthreads = $mainthreads + $mainsubforumtocount['threads'];
            }

            if ($mainforumtocount['threads'] != $mainthreads) {
                sql_query("UPDATE $table_forums SET threads='$mainthreads' WHERE fid='$mainforumtocount[fid]'") ;
                $countfix++;
            }

            if ($mainforumtocount['posts'] != $mainreplies) {
                sql_query("UPDATE $table_forums SET posts='$mainreplies' WHERE fid='$mainforumtocount[fid]'") ;
                $countfix++;
            }
            // here we fake our function to count our whole mainforum new and also set lastpost-date to all
            // links pointing onto this forum correct
            mxbLastPostForum($mainforumtocount['fid'], $mainforumtocount['lastpost']);
        }

        ?>

<tr class="altbg2">
<td align="center">
<br />
<form method="post" action="<?php echo MXB_BM_CP31 ?>action=countforum ">
<table cellspacing="0" cellpadding="0" border="0" width="100%" align="center">
<tr><td class="bordercolor">

<table border="0" cellspacing="<?php echo $borderwidth ?>" cellpadding="<?php echo $tablespace ?>" width="100%">

<tr class="mxb-header">
<td colspan="2"><?php echo _TEXTCOUNTFORUM ?></td>
</tr>

<tr class="altbg1">
<td class="tablerow"><?php echo _TEXTCOUNTFORUMRESOULT1, '&nbsp;', $countfix, '&nbsp;', _TEXTCOUNTFORUMRESOULT2 ?></td>
</tr>


</table>
</td></tr></table>

</form>

</td>
</tr>

<?php

    } // End if-$countok
} //End if-countforum
/*
 ###########################
*/

if ($action == "resetforum") {
    if (empty($resetok)) {

        ?>

<tr class="altbg2">
<td align="center">
<br />
<form method="post" action="<?php echo MXB_BM_CP31 ?>action=resetforum ">
<table cellspacing="0" cellpadding="0" border="0" width="100%" align="center">
<tr><td class="bordercolor">

<table border="0" cellspacing="<?php echo $borderwidth ?>" cellpadding="<?php echo $tablespace ?>" width="100%">

<tr class="mxb-header">
<td colspan="2"><?php echo _TEXTRESETFORUM ?></td>
</tr>

<tr class="altbg1">
<td class="tablerow"><?php echo _TEXTRESETFORUMEXPLAIN ?><br/><br/>
<?php echo _TEXTRESETFORUMCHOOSE ?><br/><br/>

<input type="checkbox" name="resetftime" value="yes"> <?php echo _TEXTRESETFORUMFTIME ?><br/>
<input type="checkbox" name="resetmtime" value="yes"> <?php echo _TEXTRESETFORUMMTIME ?><br/><br/>
<input type="checkbox" name="resetforum" value="yes"> <?php echo _TEXTRESETFORUMFORUM ?><br/>
<input type="checkbox" name="resetmember" value="yes"> <?php echo _TEXTRESETFORUMMEMBER ?><br/><br/>
<input type="checkbox" name="resetall" value="yes"> <?php echo _TEXTRESETFORUMALL ?><br/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="resetallok" value="yes"> <?php echo _TEXTRESETFORUMALLOK ?><br/>
</td></tr>


</table>
</td></tr></table>
<center><br/><input type="submit" name="resetok" value="<?php echo _TEXTRESETFORUM ?>" />&nbsp;&nbsp;<input type="submit" name="resetcancel" value="<?php echo _NO ?>" /></center>
</form>

</td>
</tr>

<?php
    } else {
        // delete the whole forum
        if (isset($resetall) && $resetall == 'yes' && isset($resetallok) && $resetallok == 'yes') {
            $querylinks = sql_query("SELECT lid FROM $table_links") ;
            while ($link = sql_fetch_object($querylinks)) {
                sql_query("DELETE FROM $table_links WHERE lid='$link->lid'");
            }

            $queryposts = sql_query("SELECT pid FROM $table_posts") ;
            while ($post = sql_fetch_object($queryposts)) {
                sql_query("DELETE FROM $table_posts WHERE pid='$post->pid'");
            }

            $querythreads = sql_query("SELECT tid FROM $table_threads") ;
            while ($thread = sql_fetch_object($querythreads)) {
                sql_query("DELETE FROM $table_threads WHERE tid='$thread->tid'");
            }

            $queryforums = sql_query("SELECT fid FROM $table_forums") ;
            while ($forum = sql_fetch_object($queryforums)) {
                sql_query("DELETE FROM $table_forums WHERE fid='$forum->fid'");
            }

            $querymembers = sql_query("SELECT uid FROM $table_members") ;
            while ($member = sql_fetch_object($querymembers)) {
                sql_query("DELETE FROM $table_members WHERE uid='$member->uid'");
            }
        } // if delete all
        if ((isset($resetftime) && $resetftime == 'yes') || (isset($resetforum) && $resetforum == 'yes')) {
            $queryforum = sql_query("SELECT fid FROM $table_forums") ;
            while ($forum = sql_fetch_object($queryforum)) {
                if (isset($resetftime) && $resetftime == 'yes') {
                    sql_query("UPDATE $table_forums SET totaltime='0' WHERE fid='$forum->fid'") ;
                }

                if (isset($resetforum) && $resetforum == 'yes') {
                    if ($eb_defstaff == "staff") {
                        $privatestatus = "staff";
                    } elseif ($eb_defstaff == "user") {
                        $privatestatus = "user";
                    } else {
                        $privatestatus = '';
                    }
                    sql_query("UPDATE $table_forums SET status='on', moderator='$eb_defmods', private='$privatestatus', allowhtml='$eb_defhtml', allowsmilies='$eb_defsmilies', allowbbcode='$eb_defbbcode', guestposting='$eb_defanoposts', theme='', postperm='1|1', allowimgcode='$eb_defimgcode' WHERE fid='$forum->fid'") ;
                }
            }
        } // if reset forum
        if ((isset($resetmtime) && $resetmtime == 'yes') || (isset($resetmember) && $resetmember == 'yes')) {
            $querymember = sql_query("SELECT uid FROM $table_members") ;
            while ($member = sql_fetch_object($querymember)) {
                if ($resetmtime == 'yes') {
                    sql_query("UPDATE $table_members SET totaltime='0' WHERE uid='$member->uid'") ;
                }

                if ($resetmember == 'yes') {
                    sql_query("UPDATE $table_members SET u2u='$eb_defmemberu2u', keeplastvisit='$eb_defmessslv', tpp='$topicperpage', ppp='$postperpage', newsletter='$eb_defmembernews', timeformat='$timeformat', dateformat='$dateformat', timeoffset='$globaltimeoffset', theme='' WHERE uid='$member->uid'") ;
                }
            }
        } // if reset member

        ?>

<tr class="altbg2">
<td align="center">
<br />
<form method="post" action="<?php echo MXB_BM_CP31 ?>action=resetforum ">
<table cellspacing="0" cellpadding="0" border="0" width="100%" align="center">
<tr><td class="bordercolor">

<table border="0" cellspacing="<?php echo $borderwidth ?>" cellpadding="<?php echo $tablespace ?>" width="100%">

<tr class="mxb-header">
<td colspan="2"><?php echo _TEXTRESETFORUM ?></td>
</tr>

<tr class="altbg1">
<td class="tablerow"><?php echo _TEXTRESETFORUMEND ?></td>
</tr>


</table>
</td></tr></table>
<center><br/><input type="submit" name="resetcancel" value="<?php echo _YES ?>" /></center>
</form>

</td>
</tr>

<?php
    } // End if-$resetok
} //End if $action == "resetforum"
//
//
// Tracking
//
if ($action == "tracking") {

    ?>

<tr class="altbg2">
<td align="center">
<br />

<form method="post" action="<?php echo MXB_BM_CP31 ?>action=tracking">
<table cellspacing="0" cellpadding="0" border="0" width="100%" align="center">
<tr><td class="bordercolor">

<table border="0" cellspacing="<?php echo $borderwidth ?>" cellpadding="<?php echo $tablespace ?>" width="100%">

<tr>
<td class="mxb-header"><?php echo _TEXTTRACKING ?></td>
</tr>

<?php

    $queryf = sql_query("SELECT fid, name, totaltime FROM $table_forums WHERE type='forum' AND fup='' AND status='on' ORDER BY displayorder");
    while ($forum = sql_fetch_object($queryf)) {
        if ($forum->totaltime != 0) {
            $forum->totaltime = $forum->totaltime / 3600;
        }
        $forum->totaltime = round($forum->totaltime, 1);
        $totaltime = mxbColorTime($forum->totaltime) ?>
<tr class="tablerow altbg2">
<td class="f11pix"><a href="<?php echo MXB_BM_FORUMDISPLAY1 ?>fid=<?php echo $forum->fid ?>"><b>
<?php echo $forum->name ?></b></a>&nbsp;&nbsp;(<?php echo $totaltime, '&nbsp;', _TEXTHOURS ?>)

<?php
        $querymembers = sql_query("SELECT username, trackingtime FROM $table_members WHERE trackingfid='" . $forum->fid . "' ORDER BY trackingtime ASC");
        while ($forummember = sql_fetch_object($querymembers)) {
            if (mxbIsOnline($forummember->username)) {
                $timeinforum = (time() - $forummember->trackingtime) / 60;
                $timeinforum = round($timeinforum, 1) ?>
<br/>&nbsp; &gt;
<?php echo mxb_link2profile($forummember->username) ?>&nbsp;&nbsp;(<?php echo $timeinforum, '&nbsp;', _TEXTMINUTS ?>)
<?php
            }
        } //end-while members

        ?>
</tr>
<?php

        $querys = sql_query("SELECT fid, name, totaltime FROM $table_forums WHERE type='sub' AND fup='" . intval($forum->fid) . "' AND status='on' ORDER BY displayorder");
        while ($forum = sql_fetch_object($querys)) {
            if ($forum->totaltime != 0) {
                $forum->totaltime = $forum->totaltime / 3600;
            }
            $forum->totaltime = round($forum->totaltime, 1);
            $totaltime = mxbColorTime($forum->totaltime) ?>

<tr class="tablerow altbg2">
<td class="f11pix" style="padding-left: 2em">
<a href="<?php echo MXB_BM_FORUMDISPLAY1 ?>fid=<?php echo $forum->fid ?>"><b>
<?php echo $forum->name ?></b></a>&nbsp;&nbsp;(<?php echo $totaltime, '&nbsp;', _TEXTHOURS ?>)

<?php
            $querymembers = sql_query("SELECT username, trackingtime FROM $table_members WHERE trackingfid='" . $forum->fid . "' ORDER BY trackingtime ASC");
            while ($forummember = sql_fetch_object($querymembers)) {
                if (mxbIsOnline($forummember->username)) {
                    $timeinforum = (time() - $forummember->trackingtime) / 60;
                    $timeinforum = round($timeinforum, 1);

                    ?>
<br/>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &gt;
<?php echo mxb_link2profile($forummember->username) ?>&nbsp;&nbsp;(<?php echo $timeinforum, '&nbsp;', _TEXTMINUTS ?>)
<?php
                }
            } //end-while members

            ?>
</tr>
<?php
        }
    }
    // Bis hierhin Foren ohne Gruppen
    // hier Darstellung von  Gruppen!
    $queryg = sql_query("SELECT fid, name FROM $table_forums WHERE type='group' AND status='on' ORDER BY displayorder");
    while ($group = sql_fetch_object($queryg)) {

        ?>

<tr class="tablerow altbg1">
<td class="f12pix"><br/><i><b><?php echo $group->name ?></b></i>
</tr>

<?php
        $queryf = sql_query("SELECT fid, name, totaltime FROM $table_forums WHERE type='forum' AND fup='" . intval($group->fid) . "' AND status='on' ORDER BY displayorder");
        while ($forum = sql_fetch_object($queryf)) {
            if ($forum->totaltime != 0) {
                $forum->totaltime = $forum->totaltime / 3600;
            }
            $forum->totaltime = round($forum->totaltime, 1);
            $totaltime = mxbColorTime($forum->totaltime) ?>

<tr class="tablerow altbg2">
<td class="f11pix" style="padding-left: 2em">
<a href="<?php echo MXB_BM_FORUMDISPLAY1 ?>fid=<?php echo $forum->fid ?>"><b>
<?php echo $forum->name ?></b></a>&nbsp;&nbsp;(<?php echo $totaltime, '&nbsp;', _TEXTHOURS ?>)

<?php
            $querymembers = sql_query("SELECT username, trackingtime FROM $table_members WHERE trackingfid='" . $forum->fid . "' ORDER BY trackingtime ASC");
            while ($forummember = sql_fetch_object($querymembers)) {
                if (mxbIsOnline($forummember->username)) {
                    $timeinforum = (time() - $forummember->trackingtime) / 60;
                    $timeinforum = round($timeinforum, 1);

                    ?>
<br/>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &gt;
<?php echo mxb_link2profile($forummember->username) ?>&nbsp;&nbsp;(<?php echo $timeinforum, '&nbsp;', _TEXTMINUTS ?>)
<?php
                }
            } //end-while members

            ?>
</tr>
<?php

            $querys = sql_query("SELECT fid, name, totaltime FROM $table_forums WHERE type='sub' AND fup='" . intval($forum->fid) . "' AND status='on' ORDER BY displayorder");
            while ($forum = sql_fetch_object($querys)) {
                if ($forum->totaltime != 0) {
                    $forum->totaltime = $forum->totaltime / 3600;
                }
                $forum->totaltime = round($forum->totaltime, 1);
                $totaltime = mxbColorTime($forum->totaltime) ?>

<tr class="tablerow altbg2">
<td class="f11pix" style="padding-left: 4em">
<a href="<?php echo MXB_BM_FORUMDISPLAY1 ?>fid=<?php echo $forum->fid ?>"><b>
<?php echo $forum->name ?></b></a>&nbsp;&nbsp;(<?php echo $totaltime, '&nbsp;', _TEXTHOURS ?>)

<?php
                $querymembers = sql_query("SELECT username, trackingtime FROM $table_members WHERE trackingfid='" . $forum->fid . "' ORDER BY trackingtime ASC");
                while ($forummember = sql_fetch_object($querymembers)) {
                    if (mxbIsOnline($forummember->username)) {
                        $timeinforum = (time() - $forummember->trackingtime) / 60;
                        $timeinforum = round($timeinforum, 1);

                        ?>
<br/>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &gt;
<?php echo mxb_link2profile($forummember->username) ?>&nbsp;&nbsp;(<?php echo $timeinforum, '&nbsp;', _TEXTMINUTS ?>)
<?php
                    }
                } //end-while members

                ?>
</tr>
<?php
            }
        }
    }

    ?>
<tr class="tablerow altbg2">
 <td><center><br/><input type="submit" name="forumsubmit" value="<?php echo _TEXTREFRESH ?>"></center></td>
</tr>

</table>


</td></tr></table>
<br/>
</form>

</td>
</tr>

<?php
}
// End Trackingcase
// Ende Tracking
// ende Action-Feld
echo "</table></td></tr></table>";
include_once(MXB_BASEMODINCLUDE . 'footer.php');

?>
