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

if (mxbIsAnonymous()) {
    return mxbExitMessage(_BADNAME, true);
}

$result = sql_query("SELECT type, name, private, allowhtml, allowsmilies, allowbbcode, guestposting, fup, postperm, allowimgcode, userlist, moderator FROM $table_forums WHERE fid=" . intval($fid) . " AND type IN('forum','sub')");
$forums = sql_fetch_object($result);

if (!is_object($forums)) {
    return mxbExitMessage(_TEXTNOFORUM, true);
}

if (!mxbPrivateCheck($forums)) {
    return mxbExitMessage(_PRIVFORUMMSG, true);
}
// if ($noreg != 'on' && mxbIsAnonymous()) {
// mxbExitMessage(sprintf(_TEXTNOGUESTPOSTING, MXB_URLREGISTER, MXB_URLLOGIN), true);
// }
if ($forums->guestposting != 'yes' && mxbIsAnonymous()) {
    return mxbExitMessage(sprintf(_TEXTFORUMNOGUESTPOSTING, MXB_URLREGISTER, MXB_URLLOGIN));
}

if ($pid) {
    $query = sql_query("SELECT p.*, t.subject, t.replies FROM $table_posts as p LEFT JOIN $table_threads AS t ON p.tid = t.tid  WHERE p.pid=" . intval($pid));
    $postinfo = sql_fetch_object($query);
} else {
    $query = sql_query("SELECT * FROM $table_threads WHERE tid=" . intval($tid));
    $postinfo = sql_fetch_object($query);
}

if (!is_object($postinfo)) {
    return mxbExitMessage(_TEXTNOTHREAD, true);
}
// Hier wird gekuckt ob wir ueber einen Link kommen und jump ggf. definiert
$jumper = mxbGetJumplink();

if ($linkstatus == 'on') {
    $result = sql_query("SELECT name, fid FROM $table_forums WHERE fid=" . intval($jumplink));
    $fupjumplink = sql_fetch_object($result);
    if ($linktype == "thread") {
        $postaction = "<a href=\"" . MXB_BM_FORUMDISPLAY1 . "fid=" . $fupjumplink->fid . "\">" . $fupjumplink->name . "</a> &gt; <a href=\"" . MXB_BM_VIEWTHREAD1 . "tid=$tid$jumper\">" . $postinfo->subject . "</a> &gt; ";
    } else {
        $postaction = "<a href=\"" . MXB_BM_FORUMDISPLAY1 . "fid=" . $fupjumplink->fid . "\">" . $fupjumplink->name . "</a> &gt; <a href=\"" . MXB_BM_FORUMDISPLAY1 . "fid=$fid$jumper\">" . $forums->name . "</a> &gt; <a href=\"" . MXB_BM_VIEWTHREAD1 . "tid=$tid$jumper\">" . $postinfo->subject . "</a> &gt; ";
    }
} else {
    if ($forums->type == 'forum') {
        $postaction = "<a href=\"" . MXB_BM_FORUMDISPLAY1 . "fid=$fid\">" . $forums->name . "</a> &gt; <a href=\"" . MXB_BM_VIEWTHREAD1 . "tid=$tid\">" . $postinfo->subject . "</a> &gt; ";
    } else {
        $result = sql_query("SELECT name, fid FROM $table_forums WHERE fid=" . intval($forums->fup));
        $fup = sql_fetch_object($result);
        $postaction = "<a href=\"" . MXB_BM_FORUMDISPLAY1 . "fid=" . $fup->fid . "\">" . $fup->name . "</a> &gt; <a href=\"" . MXB_BM_FORUMDISPLAY1 . "fid=$fid\">" . $forums->name . "</a> &gt; <a href=\"" . MXB_BM_VIEWTHREAD1 . "tid=$tid\">" . $postinfo->subject . "</a> &gt; ";
    }
}

$postaction .= _TEXTEDITPOST;
// if (($threadrow->replies + 1) > $ppp && $tid && $pid) {
if (($postinfo->replies + 1) > $ppp && $tid && $pid) {
    $viewthreadpage = mxbGetThreadPage($tid, $pid);
} else {
    $viewthreadpage = '';
}
$gotothisafteredit = MXB_BM_VIEWTHREAD1 . 'tid=' . $tid . $viewthreadpage . $jumper . '#pid' . $pid;
// Security - only admin or owner can edit post
if (!mxbIsModeratorInForum($forums) && !mxbIsPostOwner($postinfo->author, $eBoardUser['username'])) {
    return mxbExitMessage(_NOEDIT, true, $gotothisafteredit);
}

$mxbnavigator->add(false, $postaction);

if ($forums->allowimgcode == 'yes') {
    $allowimgcode = _TEXTON;
} else {
    $allowimgcode = _TEXTOFF;
}

if ($forums->allowhtml == 'yes') {
    $allowhtml = _TEXTON;
} else {
    $allowhtml = _TEXTOFF;
}

if ($forums->allowsmilies == 'yes') {
    $allowsmilies = _TEXTON;
} else {
    $allowsmilies = _TEXTOFF;
}

if ($forums->allowbbcode == 'yes') {
    $allowbbcode = _TEXTON;
} else {
    $allowbbcode = _TEXTOFF;
}
// -----------------------------------------------------------------------------
// Topic eingeben / √§ndern
// -----------------------------------------------------------------------------
if (empty($editsubmit)) {
    $posticon = (!empty($postinfo->icon)) ? $postinfo->icon : '';

    $postinfo->message = preg_replace('#<br\s*/?>#i', '', $postinfo->message);

    if ($forums->allowsmilies == 'yes') {
        $querysmilie = sql_query("SELECT * FROM $table_smilies WHERE type='smiley' ORDER BY id");
        while ($smilie = sql_fetch_object($querysmilie)) {
            $postinfo->message = str_replace("<img src=\"" . MXB_BASEMODIMG . "/" . $smilie->url . "\" border=0>", $smilie->code, $postinfo->message);
        }
    }

    if ($postinfo->usesig == 'yes') {
        $checked = ' checked="checked"';
    }

    $postinfo->message = str_replace('"', "&quot;", $postinfo->message);
    $postinfo->message = str_replace('<', "&lt;", $postinfo->message);
    $postinfo->message = str_replace('>', "&gt;", $postinfo->message);
    $postinfo->message = stripslashes($postinfo->message);

    if (preg_match_all('#\[\[editby=([^=]+)=([0-9]+)\]\]#i', $postinfo->message, $matches)) {
        foreach($matches[0] as $i => $match) {
            $postinfo->message = trim(str_replace($match, '', $postinfo->message));
        }
    }

    $offcheck1 = '';
    $offcheck2 = '';
    $offcheck3 = '';
    if ($postinfo->bbcodeoff == 'yes') {
        $offcheck1 = ' checked="checked"';
    }
    if ($postinfo->smileyoff == 'yes') {
        $offcheck2 = ' checked="checked"';
    }
    if ($postinfo->usesig == 'yes') {
        $offcheck3 = ' checked="checked"';
    }
    $notifycheck = '';
    if ($postinfo->emailnotify == 'yes') {
        $notifycheck = ' checked="checked"';
    }

    $postinfo->subject = stripslashes($postinfo->subject);
    if (empty($postinfo->subject)) {
        $postinfo->subject = '';
    } else {
        // / die Priorit√§ten vorselektieren und gleichzeitig die Tags vom Betreff entfernen
        if (preg_match('#(.*)<b>(.*)</b>(.*)#is', $postinfo->subject, $matches)) {
            $postinfo->subject = $matches[1] . $matches[2] . $matches[3];
            $prio_fett = true;
        }
        if (preg_match('#(.*)<i>(.*)</i>(.*)#is', $postinfo->subject, $matches)) {
            $postinfo->subject = $matches[1] . $matches[2] . $matches[3];
            $prio_kursiv = true;
        }
        if (preg_match('#(.*)<font color=[\'"]([^>\'"]+)[\'"]>(.*)</font>(.*)#is', $postinfo->subject, $matches)) {
            $postinfo->subject = $matches[1] . $matches[3] . $matches[4];
            if ($matches[2] == $color1) {
                $prio_high = true;
            } else if ($matches[2] == $color2) {
                $prio_low = true;
            }
        }
        $postinfo->subject = strip_tags(str_replace('"', "&quot;", $postinfo->subject));
    }

    pmxHeader::add_script(MXB_BASEMODJS . '/unb_lib.js');

    ?>
<h2><?php echo _TEXTEDITPOST ?></h2>  


<form method="post" name="input" action="<?php echo MXB_BM_POSTEDIT1, $jumper?>">
	<div id="posteditbox" class="panel bgcolor2">
		<div class="inner">
			<h3><?php echo _TEXTEDITPOST ?></h3>						
			<fieldset class="fields1">
<?php
    // wenn erster Post (thread)
    if (empty($pid)) {
        $str = '
 					<dl style="clear: left;">
						<dt><label>' . _TEXTSUBJECT . '</label></dt>
						<dd><input type="text" name="subject" size="45" value="' . $postinfo->subject . '" /></dd>
					</dl>';       
        if (isOnStaff($eBoardUser['status']) || $colorsubject == "on") {
            $str .= '
          <dl style="clear: left;">
						<dt><label><?php echo _TEXTPRIORITY ?></label></dt>
							<dd>
								<input type="radio" name="priority" value="high"' . ((isset($prio_high)) ? ' checked="checked"' : '') . ' /><span style="color: ' . $color1 . '">' . _TEXTPRIORITYHIGH . '</span>
								<input type="radio" name="priority" value="normal"' . ((!isset($prio_high) && !isset($prio_low)) ? ' checked="checked"' : '') . ' />' . _TEXTPRIORITYNORMAL . '
								<input type="radio" name="priority" value="low"' . ((isset($prio_low)) ? ' checked="checked"' : '') . ' /><span style="color: ' . $color2 . '">' . _TEXTPRIORITYLOW . '</span>
								<input type="checkbox" name="bold" value="true"' . ((isset($prio_fett)) ? ' checked="checked"' : '') . ' /><b>&nbsp;' . _TEXTPRIORITYBOLD . '</b>
								<input type="checkbox" name="italic" value="true"' . ((isset($prio_kursiv)) ? ' checked="checked"' : '') . ' />&nbsp;<i>' . _TEXTPRIORITYITALIC . '</i>
							</dd>
					</dl>';                   
        }
        echo $str;
    }

    ?>
<?php if ($piconstatus == 'on') { ?>
				<dl style="clear: left;">
					<dt><label><?php echo _TEXTICON ?></label></dt>
					<dd><?php echo mxbShowPostIcons($posticon)?></dd>
				</dl>
<?php }	?>	

							
<?php if ($forums->allowbbcode == 'yes') { ?>
				<div id="format-buttons">
					<?php echo mxbShowIconesBB() ?>
				</div>
<?php } ?>

			<div id="smiley-box">			
<?php if ($forums->allowsmilies == 'yes') { ?>				
        	<?php echo mxbShowTableSmilies() ?>
<?php } ?>
				<hr />
				<h4><?php echo _TEXTRIGHTS ?></h4>
 				<?php echo _TEXTHTMLIS ?> <?php echo $allowhtml ?><br />
    		<?php echo _TEXTSMILIESARE ?>  <?php echo $allowsmilies ?><br />
    		<?php echo _TEXTBBCODEIS ?> <?php echo $allowbbcode ?><br />
    		<?php echo _TEXTIMGCODEIS ?> <?php echo $allowimgcode ?>
			</div>

			
				<div id="message-box">   
  				<textarea name="message" id="message" rows="15" cols="70" onkeydown="return UnbTextKeydownHandler(this)" class="inputbox"><?php echo htmlspecialchars(mxbFixBbCodeQuote($postinfo->message)) ?></textarea>
    				<script type="text/javascript">
        		/*<![CDATA[*/
        		var textbox = getel("message");
        		UnbGlobalRegisterKeyHandler(13, 0, 2);
        		/*]]>*/
    				</script>					
				</div>
		
	
			</fieldset>
		</div>
	</div>
		
<?php if (mxbUseCaptcha($eBoardUser)) {
	    echo mxbPrintCaptcha();
    }  ?>
    
	<div class="panel bgcolor1">
		<div class="inner">
			<fieldset class="submit-buttons">
				<input type="hidden" name="fid" value="<?php echo $fid ?>" />
				<input type="hidden" name="tid" value="<?php echo $tid ?>" />
				<input type="hidden" name="pid" value="<?php echo $pid ?>" />
				<input type="hidden" name="origauthor" value="<?php echo $postinfo->author ?>" />
				<input type="submit" accesskey="p" tabindex="7" name="editsubmit" value="<?php echo _TEXTEDITPOST ?>" class="button2" />
			</fieldset>
		</div>
	</div>

	<div class="panel bgcolor4" id="options-panel">
		<div class="inner">
		<fieldset class="fields1">
<?php if ($forums->allowsmilies == 'yes') { ?>
  			<div><label for="smileyoff"><input type="checkbox" name="smileyoff" id="smileyoff" value="yes" <?php echo $offcheck2 ?> /><?php echo _TEXTDISSMILEYS ?></label></div>
<?php } if ($forums->allowbbcode == 'yes') { ?>
  			<div><label for="bbcodeoff"><input type="checkbox" name="bbcodeoff" id="bbcodeoff" value="yes" <?php echo $offcheck1 ?> /><?php echo _BBCODEOFF ?></label></div>
  				<div><label for="usesig"><input type="checkbox" name="usesig" id="usesig" value="yes" <?php echo $offcheck3 ?> /><?php echo _TEXTUSESIG ?></label></div>
  				<div><label for="emailnotify"><input type="checkbox" name="emailnotify" id="emailnotify" value="yes" <?php echo $notifycheck ?> /><?php echo _EMAILNOTIFYTOREPLIES ?></label></div>			 					
<?php }
    // nur lˆschen, wenn keine Antworten
    $threadposts = 0;
    if (empty($postinfo->pid)) {
        $query = sql_query("SELECT COUNT(pid) as threadposts FROM $table_posts WHERE tid=" . intval($tid));
        list($threadposts) = sql_fetch_row($query);
    }
    if (empty($threadposts)) { ?>
				<div><label for="delete"><input type="checkbox" name="delete" id="delete" value="yes" onclick="return confirm('<?php echo _MXBSHUREDELETEPOST ?>')" /><strong><?php echo _TEXTDELETE?></strong></label></div>
<?php } if ($pid && mxbIsModeratorInForum($forums)) { ?>
<p class="align-center">
[ <a href="<?php echo MXB_BM_POSTNEWTOPIC1 ?>previewpost=true&amp;subject=<?php echo urlencode($postinfo->subject) ?>&amp;message=<?php echo urlencode($postinfo->message) ?>&amp;posticon=<?php echo urlencode($postinfo->icon) ?>&amp;fid=<?php echo $fid, $jumper?>&amp;username=<?php echo urlencode($eBoardUser['username']) ?>&amp;postauthor=<?php echo urlencode($postinfo->author) ?>&amp;postdateline=<?php echo $postinfo->dateline?>">
<?php echo _TEXTPOSTTOTHREAD?></a> ]
</p>
<?php } ?>
		
					</fieldset>
    </div>
</div>
</form>

<?php
}
// -----------------------------------------------------------------------------
// Topic in Datenbank schreiben
// -----------------------------------------------------------------------------
if (!empty($editsubmit)) {
    if (empty($message)) {
        return mxbExitMessage(_ERRORNOMESSAGE, false);
    }

    if (empty($emailnotify) || $emailnotify != 'yes' || mxbIsAnonymous()) {
        $emailnotify = "no";
    }
    if (empty($usesig) || $usesig != 'yes') {
        $usesig = 'no';
    }
    if (empty($smileyoff) || $smileyoff != 'yes') {
        $smileyoff = 'off';
    }
    if (empty($bbcodeoff) || $bbcodeoff != 'yes') {
        $bbcodeoff = 'off';
    }

    $posticon = (isset($posticon)) ? $posticon : '';
    // $message = str_replace("&quot;", '"', $message);
    // $message = str_replace("&lt;", '<', $message);
    // $message = str_replace("&gt;", '>', $message);
    $message = nl2br($message);
    $message = preg_replace('#<br\s*/?>#i', '', $message);
    $message = mxAddSlashesForSQL($message);
    if (empty($message)) {
        return mxbExitMessage(_ERRORNOMESSAGE, false);
    }

    /* Captcha */
    if (!isOnStaff($eBoardUser['status'])) {
        $result = mxbCheckCaptcha($eBoardUser);
        if ($result !== true) {
            return mxbExitMessage($result, false); // _CAPTCHAWRONG
        }
    }
    // $date = @gmdate ($dateformat, time() + ($timeoffset * 3600));
    // $message .= "\n\n[" . _TEXTEDITON . " ".$date." " . _TEXTBY . " ".$eBoardUser['username']."]";
    if (preg_match_all('#\[\[editby=([^=]+)=([0-9]+)\]\]#i', $message, $matches)) {
        foreach($matches[0] as $i => $match) {
            $message = trim(str_replace($match, '', $message));
        }
    }
    $message .= "\n[[editby=" . urlencode($eBoardUser['username']) . "=" . time() . "]]";

    if (!empty($pid) && !empty($tid) && (empty($delete) || $delete != "yes")) { // edit post N∞pid=$pid
        $gotothisafteredit = MXB_BM_VIEWTHREAD1 . 'tid=' . $tid . $viewthreadpage . $jumper . '#pid' . $pid;

        sql_query("UPDATE $table_posts SET message='$message', usesig='$usesig', bbcodeoff='$bbcodeoff', smileyoff='$smileyoff', emailnotify='$emailnotify', icon='$posticon' WHERE pid=" . intval($pid));

        if (($emailalltomoderator == 'on') || ($emailalltoadmin == 'on')) {
            $threadquery = sql_query("SELECT subject FROM $table_threads WHERE tid=" . intval($tid));
            $threadsubject = sql_fetch_assoc($threadquery);
            $theurl = trim(MX_HOME_URL, '/') . "/" . MXB_BM_VIEWTHREAD1 . 'tid=' . $tid . '#pid' . $pid;
            $theurl = preg_replace("/\/{1,}modules/", "/modules", $theurl);
            $mailsubject = '[' . $bbname . '] ' . _EMAILMODIFIEDNOTIFYSUBJECT . " " . strip_tags($threadsubject['subject']);
            $mailmessage = _EMAILMODIFIEDNOTIFYINTRO . strip_tags($threadsubject['subject']) . "\n\n" . _EMAILNOTIFYINTRO2 . "\n\n$theurl\n\n" . _EMAILNOTIFYEND;

            if ($mailonedit == 'on' && $emailalltoadmin == 'on') {
                mxbNotifyAdmin($fid, $mailsubject, $mailmessage, "notifyedit");
            }

            if ($moderatormailonedit == 'on' && $emailalltomoderator == 'on') {
                mxbNotifyModerator($fid, $mailsubject, $mailmessage, "notifyedit");
            }
        }
    } elseif (empty($pid) && !empty($tid) && (empty($delete) || $delete != "yes")) { // edit thread N∞pid=$pid
        $gotothisafteredit = MXB_BM_VIEWTHREAD1 . 'tid=' . $tid . $jumper;

        if (empty($subject)) {
            return mxbExitMessage(_TEXTNOSUBJECT, false);
        }
        $subject = str_replace("<", "&lt;", $subject);
        $subject = str_replace(">", "&gt;", $subject);

        $bold = (isset($bold)) ? $bold : '';
        $italic = (isset($italic)) ? $italic : '';

        if (isOnStaff($eBoardUser['status']) || $colorsubject == 'on') {
            if (empty($priority) || $priority == "normal") {
                $color = '';
            } elseif ($priority == "high") {
                $color = $color1;
            } else {
                $color = $color2;
            }
            $subject = mxbColorSubject($subject, $color, $bold, $italic);
        } else {
            $subject = mxbColorSubject($subject, "", false, false);
        }
        $subject = mxAddSlashesForSQL($subject);
        if (empty($subject)) {
            return mxbExitMessage(_TEXTNOSUBJECT, false);
        }

        sql_query("UPDATE $table_threads SET message='$message', usesig='$usesig', subject='$subject', bbcodeoff='$bbcodeoff', smileyoff='$smileyoff', emailnotify='$emailnotify', icon='$posticon' WHERE tid=" . intval($tid));

        if (($emailalltomoderator == 'on') || ($emailalltoadmin == 'on')) {
            $theurl = trim(MX_HOME_URL, '/') . "/" . MXB_BM_VIEWTHREAD1 . "tid=$tid";
            $theurl = preg_replace("/\/{1,}modules/", "/modules", $theurl);
            $mailsubject = '[' . $bbname . '] ' . _EMAILMODIFIEDNOTIFYSUBJECT . " " . strip_tags($subject);
            $mailmessage = _EMAILMODIFIEDNOTIFYINTRO . strip_tags($subject) . "\n\n" . _EMAILNOTIFYINTRO2 . "\n\n$theurl\n\n" . _EMAILNOTIFYEND;

            if ($mailonedit == 'on' && $emailalltoadmin == 'on') {
                mxbNotifyAdmin($fid, $mailsubject, $mailmessage, "notifyedit");
            }

            if ($moderatormailonedit == 'on' && $emailalltomoderator == 'on') {
                mxbNotifyModerator($fid, $mailsubject, $mailmessage, "notifyedit");
            }
        }
    } elseif (!empty($pid) && (!empty($delete) && $delete == 'yes')) { // delete post N∞pid=$pid
        // here we check if we delete the newest post
        $querypost = sql_query("SELECT dateline, author FROM $table_posts WHERE pid=" . intval($pid) . "") ;
        $post = sql_fetch_assoc($querypost);
        $postingtime = $post['dateline'] . "|" . $post['author'];

        if (sql_query("DELETE FROM $table_posts WHERE pid=" . intval($pid) . "")) {
            sql_query("UPDATE $table_forums SET posts=posts-1 WHERE fid=" . intval($fid) . " AND posts>0");
            sql_query("UPDATE $table_threads SET replies=replies-1 WHERE tid=" . intval($tid) . " AND replies>0");
            $getmainforum = sql_query("SELECT type, fup FROM $table_forums WHERE fid=" . intval($fid));
            $for = sql_fetch_object($getmainforum);
            if ($for->type == 'sub') {
                sql_query("UPDATE $table_forums SET posts=posts-1 WHERE fid='" . intval($for->fup) . "' AND posts>0");
            }
            // array zum anpassen der Userpostings
            $check_authors[$postinfo->author] = $postinfo->author;

            mxbLastPostThread($tid, $postingtime);
            // end newest post
            if (($emailalltomoderator == 'on') || ($emailalltoadmin == 'on')) {
                $threadquery = sql_query("SELECT subject FROM $table_threads WHERE tid=" . intval($tid));
                $threadsubject = sql_fetch_assoc($threadquery);
                $theurl = trim(MX_HOME_URL, '/') . "/" . MXB_BM_VIEWTHREAD1 . 'tid=' . $tid;
                $theurl = preg_replace("/\/{1,}modules/", "/modules", $theurl);
                $mailsubject = '[' . $bbname . '] ' . _EMAILSUPPRNOTIFYSUBJECT . " " . strip_tags($threadsubject['subject']);
                $mailmessage = _EMAILSUPPRNOTIFYINTRO . strip_tags($threadsubject['subject']) . "\n\n" . _EMAILNOTIFYINTRO2 . "\n\n$theurl\n\n" . _EMAILNOTIFYEND;

                if ($mailondele == 'on' && $emailalltoadmin == 'on') {
                    mxbNotifyAdmin($fid, $mailsubject, $mailmessage, "notifydelete");
                }

                if ($moderatormailondelete == 'on' && $emailalltomoderator == 'on') {
                    mxbNotifyModerator($fid, $mailsubject, $mailmessage, "notifydelete");
                }
            }
        }
    } elseif (!empty($tid) && !empty($delete) && $delete == 'yes') {
        $query = sql_query("SELECT COUNT(pid) as threadposts FROM $table_posts WHERE tid=" . intval($tid));
        $row = sql_fetch_object($query);
        $threadposts = $row->threadposts;
        $threadposts++;

        $count = sql_query("SELECT type, fup FROM $table_forums WHERE fid=" . intval($fid));
        $for = sql_fetch_object($count);
        // nur l√∂schen, wenn keine Antworten
        if ($threadposts == 1) {
            if (sql_query("DELETE FROM $table_threads WHERE tid=" . intval($tid) . " AND fid=" . intval($fid) . "")) {
                // die queries einzeln, mit versch. Bedingungen, damit Anzahlen nicht ins Minus rutschen
                sql_query("UPDATE $table_forums SET threads=threads-1 WHERE fid=" . intval($fid) . " AND threads>0");
                sql_query("UPDATE $table_forums SET posts=posts-'$threadposts' WHERE fid=" . intval($fid) . " AND posts>0");
                if ($for->type == 'sub') {
                    sql_query("UPDATE $table_forums SET threads=threads-1 WHERE fid='" . intval($for->fup) . "' AND threads>0");
                    sql_query("UPDATE $table_forums SET posts=posts-'$threadposts' WHERE fid='" . intval($for->fup) . "' AND posts>0");
                }

                $query = sql_query("SELECT DISTINCT author FROM $table_posts WHERE tid=" . intval($tid));
                while ($result = sql_fetch_object($query)) {
                    // array zum anpassen der Userpostings
                    $check_authors[$result->author] = $result->author;
                }

                sql_query("DELETE FROM $table_posts WHERE tid=" . intval($tid));
                sql_query("DELETE FROM $table_links WHERE type='thread' AND fromid=" . intval($tid));
                // array zum anpassen der Userpostings
                $check_authors[$postinfo->author] = $postinfo->author;
                mxbLastPostForum($fid, 'checkforum');

                if (($emailalltomoderator == 'on') || ($emailalltoadmin == 'on')) {
                    $theurl = trim(MX_HOME_URL, '/') . "/" . MXB_BM_FORUMDISPLAY1 . 'fid=' . $fid;
                    $theurl = preg_replace("/\/{1,}modules/", "/modules", $theurl);
                    $mailsubject = '[' . $bbname . '] ' . _EMAILSUPPRNOTIFYSUBJECT . " " . strip_tags($subject);
                    $mailmessage = _EMAILSUPPRNOTIFYINTRO . strip_tags($subject) . "\n\n" . _EMAILNOTIFYINTRO2 . "\n\n$theurl\n\n" . _EMAILNOTIFYEND;

                    if ($mailondele == 'on' && $emailalltoadmin == 'on') {
                        mxbNotifyAdmin($fid, $mailsubject, $mailmessage, "notifydelete");
                    }

                    if ($moderatormailondelete == 'on' && $emailalltomoderator == 'on') {
                        mxbNotifyModerator($fid, $mailsubject, $mailmessage, "notifydelete");
                    }
                }
            }
        }
        $gotothisafteredit = MXB_BM_FORUMDISPLAY1 . 'fid=' . $fid . $jumper;
    }
    // hier wird f√ºr ein verlinktes Posting das Linkforum als Sprungziel nach dem editieren gesetzt
    if ($linkstatus == 'on' && $linktype == "thread") {
        $fid = $jumplink;
        $jumper = '';
    }

    if (isset($check_authors)) {
        foreach($check_authors as $check_autho) {
            mxbRepairUserPostNum($check_autho);
        }
        unset($check_authors, $check_autho);
    }
    echo mxbMessageScreen(_EDITPOSTMSG);
    echo mxbRedirectScript($gotothisafteredit, 1250);
}
include_once(MXB_BASEMODINCLUDE . 'footer.php');

?>
