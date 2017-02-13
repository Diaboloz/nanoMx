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
// $expire = 45;  // Lebensdauer der Seite im Cache in Sekunden
// header("Expires: " . gmdate("D, d M Y H:i:s", time()+$expire) ." GMT");
// header("Last-Modified: " . gmdate("D, d M Y H:i:s", getlastmod()) ." GMT");
// HTTP 1.1
// header("Cache-Control: private, max-age=" . $expire);
// MSIE 5.x special
// header("Cache-Control: pre-check=" . $expire, FALSE);
// unset($expire);
include_once(dirname(__FILE__) . DS . 'includes' . DS . 'header.php');

function cmp ($a, $b)
{
    return ($a["dateline"] < $b["dateline"]);
}

if ($searchstatus != 'on') {
    return mxbExitMessage(_SEARCHOFF, true);
}

global $threadmainfid;

$tabtemp = array();

if (!empty($_GET['member']) && mxCheckNickname($_GET['member']) === true) {
    define('MXB_IS_USERPAGE', $_GET['member']);
    $_REQUEST['searchname'] = $_GET['member'];
    $_POST['searchsubmit'] = 'yxz';
    $mxbnavigator->add(false, _SEARCHUSERMSG . ': ' . $_GET['member']);
} else {
    $mxbnavigator->add(false, _TEXTSEARCH);
}

$searchtext = (empty($_REQUEST['searchtext'])) ? '' : $_REQUEST['searchtext'];
$searchname = (empty($_REQUEST['searchname'])) ? '' : $_REQUEST['searchname'];
$srchfid = (empty($_REQUEST['srchfid'])) ? 'all' : $_REQUEST['srchfid'];
$searchfrom = (empty($_REQUEST['searchfrom'])) ? MX_TIME : $_REQUEST['searchfrom'];
$searchin = (empty($_REQUEST['searchin'])) ? 'both' : $_REQUEST['searchin'];

if (!defined('MXB_IS_USERPAGE')) {
    // --  Display without search - first call to search script

    ?>

<form method="post" action="<?php echo MXB_BM_SEARCH0 ?>">
	<h2><?php echo _TEXTSEARCH ?></h2>
	<div class="panel">
		<div class="inner">
			<fieldset>
				<dl>
					<dt><label><?php echo _TEXTSEARCHFOR ?></label></dt>
					<dd><input type="text" name="searchtext" value="<?php echo stripslashes($searchtext) ?>" size="30" maxlength="40" /></dd>
				</dl>
				<dl>
					<dt><label><?php echo _TEXTSRCHUNAME ?></label></dt>
					<dd><input type="text" name="searchname" value="<?php echo stripslashes($searchname) ?>" size="30" maxlength="40" /></dd>
				</dl>
				<dl>
					<dt><label><?php echo _SRCHBYFORUM ?></label></dt>
					<dd><select name="srchfid"><?php echo mxbLargeSelectWithLinks($srchfid) ?></select></dd>
				</dl>
				<dl>
					<dt><label><?php echo _TEXTLFROM ?></label></dt>
					<dd>
						<select name="searchfrom">
							<option <?php echo (($searchfrom == 3600) ? 'selected="selected" class="current"' : '') ?> value="3600"><?php echo _LASTONEHOUR?></option>
                            <option <?php echo (($searchfrom == 43200) ? 'selected="selected" class="current"' : '') ?> value="43200"><?php echo _LAST12HOURS?></option>
                            <option <?php echo (($searchfrom == 86400) ? 'selected="selected" class="current"' : '') ?> value="86400"><?php echo _LAST24HOURS?></option>
                            <option <?php echo (($searchfrom == 604800) ? 'selected="selected" class="current"' : '') ?> value="604800"><?php echo _AWEEK?></option>
                            <option <?php echo (($searchfrom == 2592000) ? 'selected="selected" class="current"' : '') ?> value="2592000"><?php echo _MONTH1?></option>
                            <option <?php echo (($searchfrom == 7948800) ? 'selected="selected" class="current"' : '') ?> value="7948800"><?php echo _MONTH3?></option>
                            <option <?php echo (($searchfrom == 15897600) ? 'selected="selected" class="current"' : '') ?> value="15897600"><?php echo _MONTH6?></option>
                            <option <?php echo (($searchfrom == 31536000) ? 'selected="selected" class="current"' : '') ?> value="31536000"><?php echo _LASTYEAR?></option>
                            <option <?php echo (($searchfrom == MX_TIME) ? 'selected="selected" class="current"' : '') ?> value="0"><?php echo _BEGINNING?></option>
						</select>
					</dd>
				</dl>
				<dl>
					<dt><label><?php echo _TEXTSEARCHIN ?></label></dt>
					<dd>
<label><input <?php echo (($searchin == "reply") ? 'checked="checked"' : '') ?> type="radio" name="searchin" value="reply"/><?php echo _REPLIESL?></label>
<label><input <?php echo (($searchin == "topic") ? 'checked="checked"' : '') ?> type="radio" name="searchin" value="topic"/><?php echo _TOPICSL?></label>
<label><input <?php echo (($searchin == "both") ? 'checked="checked"' : '') ?> type="radio" name="searchin" value="both"/><?php echo _TEXTBOTH?></label>
<label><input <?php echo (($searchin == "topicnoreply") ? 'checked="checked"' : '') ?> type="radio" name="searchin" value="topicnoreply"/><?php echo _TEXTTHREADSWITHOUTREPLY?></label>
												
					</dd>
				</dl>
			</fieldset>
		</div>
	</div>
	
	<div class="panel bgcolor2">
		<div class="inner">
			<fieldset class="submit-buttons">
				<input type="submit" name="searchsubmit" value="<?php echo _TEXTSEARCH ?>" class="button1" />
			</fieldset>
		</div>
	</div>

</form>

<?php
}

if (!empty($_POST['searchsubmit'])) {
    $array_1 = array("AND", "and", "+", " ", "%%");
    $array_2 = array("%", "%", "%", "%", "%");
    $searchtext = str_replace($array_1, $array_2, $searchtext);
    $searchtext = mxAddSlashesForSQL($searchtext);

    $searchname = substr($searchname, 0, 25);
    $searchname = mxAddSlashesForSQL($searchname);

    $srchfid = intval($srchfid);

    $searchfrom = intval(MX_TIME - $searchfrom);

    $sql1 = "SELECT dateline, subject, fid, tid, author, icon FROM $table_threads";
    $sql2 = "SELECT dateline, pid, fid, tid, author, icon FROM $table_posts";

    if ($searchtext) {
        $sql1 .= " WHERE (message LIKE '%$searchtext%' OR subject LIKE '%$searchtext%')";
        $sql2 .= " WHERE message LIKE '%$searchtext%'";
    } elseif (!$searchtext && !$searchname && ($srchfid != "all" || !$srchfid)) {
        $sql1 .= " WHERE fid=$srchfid";
        $sql2 .= " WHERE fid=$srchfid";
    } elseif (!$searchtext && $searchname) {
        if ($srchfid == "all") {
            $sql1 .= " WHERE author='$searchname'";
            $sql2 .= " WHERE author='$searchname'";
        } elseif ($srchfid) {
            $sql1 .= " WHERE author='$searchname' AND fid=$srchfid";
            $sql2 .= " WHERE author='$searchname' AND fid=$srchfid";
        }
    }

    if ($srchfid != "all" && $searchtext && !$searchname) {
        $sql1 .= " AND fid=$srchfid";
        $sql2 .= " AND fid=$srchfid AND dateline >= $searchfrom";
    } elseif ($searchname && $srchfid != "all" && $searchtext) {
        $sql1 .= " AND fid=$srchfid";
        $sql2 .= " AND fid=$srchfid";
    }

    if ($searchtext && $searchname) {
        $sql1 .= " AND author='$searchname'";
        $sql2 .= " AND author='$searchname'";
    }

    if (strstr($sql1, "WHERE")) {
        $sql1 .= " AND dateline >= $searchfrom";
    } else {
        $sql1 .= " WHERE dateline >= $searchfrom";
    }

    if (strstr($sql2, "WHERE")) {
        $sql2 .= " AND dateline >= $searchfrom";
    } else {
        $sql2 .= " WHERE dateline >= $searchfrom";
    }

    if ($searchin == "both" || $searchin == "topic" || $searchin == "topicnoreply") {
        $query1 = sql_query($sql1 . ' LIMIT 0, ' . $max_searchreults);

        while ($thread = sql_fetch_object($query1)) {
            // Query-Cache für Foren
            if (empty($forumcache[$thread->fid])) {
                $forumquery = sql_query("SELECT type, name, moderator, private, userlist, fup FROM $table_forums WHERE fid=" . intval($thread->fid) . " AND status='on'");
                $forum = sql_fetch_object($forumquery);
                $forumcache[$thread->fid] = $forum;
            } else {
                $forum = $forumcache[$thread->fid];
            }

            if (!empty($forum)) {
                $howmany = 0;
                $date = date($dateformat, (int)$thread->dateline);
                $time = date($timecode, (int)$thread->dateline);
                $poston = "$date " . _TEXTAT . " $time";
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
                if ($authToDisplay && $forum->type == 'sub' && !empty($forum->fup)) {
                    $mainforumquery = sql_query("SELECT moderator, private, userlist FROM $table_forums WHERE fid=" . intval($forum->fup) . " AND status='on'");
                    $mainforum = sql_fetch_object($mainforumquery);
                    if (isTrusted($mainforum)) {
                        $authToDisplay = true;
                    } else {
                        $authToDisplay = false;
                    }
                }
                // Wenn nur nach Beiträgen ohne Antwort gesucht werden soll
                if ($authToDisplay && $searchin == "topicnoreply") {
                    $noreplyquery = sql_query("SELECT COUNT(pid) as nbsites FROM $table_posts WHERE tid=" . intval($thread->tid));
                    $howmany = sql_fetch_object($noreplyquery);
                    if ($howmany->nbsites != 0) {
                        $authToDisplay = false;
                    } else {
                        $authToDisplay = true;
                    }
                }

                if ($authToDisplay) {
                    $tabtemp[] = array("dateline" => $thread->dateline, "fid" => $thread->fid, "icon" => $thread->icon, "forumname" => $forum->name, "subject" => $thread->subject, "tid" => $thread->tid, "author" => $thread->author, "poston" => $poston, "textpost" => _TEXTTOPIC, "pid" => '');
                }
            } # Ende if $forum
            $forum = '';
        } # Ende while $thread
    }

    if ($searchin == "both" || $searchin == "reply") {
        $query2 = sql_query($sql2 . ' LIMIT 0, ' . $max_searchreults);
        // $postcount = sql_num_rows($query2);
        while ($post = sql_fetch_object($query2)) {
            // Query-Cache für Foren
            if (empty($forumcache[$post->fid])) {
                $forumquery = sql_query("SELECT type, name, moderator, private, userlist, fup FROM $table_forums WHERE fid=" . intval($post->fid) . " AND status='on'");
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
                        $infoquery = sql_query("SELECT subject, tid FROM $table_threads WHERE tid=" . intval($post->tid));
                        $threadinfo = sql_fetch_object($infoquery);
                        $threadinfo->subject = stripslashes($threadinfo->subject);
                        $threadcache[$post->tid] = $threadinfo;
                    } else {
                        $threadinfo = $threadcache[$post->tid];
                    }
                    // $sqlname = sql_query("SELECT name FROM $table_forums WHERE fid= '".$post->fid."'");
                    // $fidname = sql_fetch_object($sqlname);
                    $tabtemp[] = array("dateline" => $post->dateline, "fid" => $post->fid, "icon" => $post->icon, "forumname" => $forum->name, "subject" => $threadinfo->subject, "pid" => $post->pid, "tid" => $threadinfo->tid, "author" => $post->author, "poston" => $poston, "textpost" => _TEXTREPLY);
                    // $tabtemp[]=array("dateline"=>$post->dateline, "subject"=>$threadinfo->subject, "pid"=> $post->pid, "tid"=>$threadinfo->tid, "author"=>$post->author, "poston"=>$poston, "textpost"=>_TEXTPOSTREPLYNOTE);
                } # Ende if $authToDisplay
            } # Ende if $forum
            $forum = '';
        } # Ende while $post
    }

    $resultnumber = count($tabtemp);
    if ($resultnumber) {
        if ($resultnumber >= $max_searchreults) {
            $resultnumber = $max_searchreults;
        }
        // usort($tabtemp, "cmp");
    }

    ?>
    
<h2><?php echo $resultnumber?>&nbsp;<?php echo _TEXTSEARCHRESULTS?></h2>   
<div class="panel bgcolor3">
    <div class="inner">
        <table class="table1">
<?php

    for($i = 0;$i <= count($tabtemp)-1;$i++) {
        if ($i >= $max_searchreults) {
            break;
        }
        $modt = '
            <tr class="alternate-0">';
        if ($tabtemp[$i]['icon']) {
            $modt .= "
                <td class=\"bgcolor2\" align=\"center\"><img src=\"" . MXB_BASEMODIMG . "/" . $tabtemp[$i]['icon'] . "\" alt=\"\"/></td>";
        } else {
            $modt .= "<td class=\"bgcolor3\">";
        }
        $modt .= "<td class=\"bgcolor3\"><a href=\"" . MXB_BM_VIEWTHREAD1 . "tid=" . $tabtemp[$i]['tid'] . "#pid" . $tabtemp[$i]['pid'] . "\">" . $tabtemp[$i]['subject'] . "</a>"
         . "<br/><a href=\"" . MXB_BM_FORUMDISPLAY1 . "fid=" . $tabtemp[$i]['fid'] . "\">" . $tabtemp[$i]['forumname'] . "</a></td>"
         . "<td class=\"bgcolor2\">" . $tabtemp[$i]['textpost'] . "</td>"
         . "<td class=\"bgcolor3\">" . mxb_link2profile($tabtemp[$i]['author']) . "</td>"
         . "<td class=\"bgcolor2\">" . $tabtemp[$i]['poston'] . "</td>"
         . "</tr>";

        echo $modt;
    }

    if (empty($tabtemp)) {
        echo '
            <tr>
                <td>' . mxbMessageScreen(_NORESULTS) . '</td>
            </tr>';
    } else if ($i >= $max_searchreults) {
        echo '
            <tr>
                <td>' . sprintf(_MORERESULTS, $max_searchreults) . '</td>
            </tr>';
    }

    echo '
        </table>
    </div>
</div>';
}

include_once(MXB_BASEMODINCLUDE . 'footer.php');

?>