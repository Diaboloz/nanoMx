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

$result = sql_query("SELECT type, name, private, allowhtml, allowsmilies, allowbbcode, guestposting, fup, postperm, allowimgcode, userlist, moderator FROM $table_forums WHERE fid=" . intval($fid) . " AND type IN('forum','sub')");
$forums = sql_fetch_object($result);

if (!is_object($forums)) {
    return mxbExitMessage(_TEXTNOFORUM, true);
}

if (!mxbPrivateCheck($forums)) {
    return mxbExitMessage(_PRIVFORUMMSG, true);
}

if ($forums->guestposting != 'yes' && mxbIsAnonymous()) {
    return mxbExitMessage(sprintf(_TEXTFORUMNOGUESTPOSTING, MXB_URLREGISTER, MXB_URLLOGIN), true);
}

if (!mxbPostingAllowed($forums, 'reply')) {
    return mxbExitMessage(_POSTPERMERR, true);
}

$result = sql_query("SELECT subject, replies FROM $table_threads WHERE tid=" . intval($tid));
$threadrow = sql_fetch_object($result);

if (!is_object($threadrow)) {
    return mxbExitMessage(_TEXTNOTHREAD, true);
}

$threadname = stripslashes($threadrow->subject);

$jumper = mxbGetJumplink();

if (!mxbIsAnonymous()) {
    $username = $eBoardUser['username'];
} else {
    $username = MXB_ANONYMOUS;
}
//  Link-ergänzungen
if ($linkstatus == 'on') {
    $result = sql_query("SELECT name, fid FROM $table_forums WHERE fid=" . intval($jumplink));
    $fupjumplink = sql_fetch_object($result);
    if ($linktype == "thread") {
        $postaction = "<a href=\"" . MXB_BM_FORUMDISPLAY1 . "fid=" . $fupjumplink->fid . "\">" . $fupjumplink->name . "</a> &gt; <a href=\"" . MXB_BM_VIEWTHREAD1 . "tid=$tid$jumper\">$threadname</a> &gt; ";
    } else {
        $postaction = "<a href=\"" . MXB_BM_FORUMDISPLAY1 . "fid=" . $fupjumplink->fid . "\">" . $fupjumplink->name . "</a> &gt; <a href=\"" . MXB_BM_FORUMDISPLAY1 . "fid=$fid$jumper\">" . $forums->name . "</a> &gt; <a href=\"" . MXB_BM_VIEWTHREAD1 . "tid=$tid$jumper\">$threadname</a> &gt; ";
    }
} else {
    if ($forums->type == 'forum') {
        $postaction = "<a href=\"" . MXB_BM_FORUMDISPLAY1 . "fid=$fid\">" . $forums->name . "</a> &gt; <a href=\"" . MXB_BM_VIEWTHREAD1 . "tid=$tid\">$threadname</a> &gt; ";
    } else {
        $result = sql_query("SELECT name, fid FROM $table_forums WHERE fid=" . intval($forums->fup));
        $fup = sql_fetch_object($result);
        $postaction = "<a href=\"" . MXB_BM_FORUMDISPLAY1 . "fid=" . $fup->fid . "\">" . $fup->name . "</a> &gt; <a href=\"" . MXB_BM_FORUMDISPLAY1 . "fid=$fid\">" . $forums->name . "</a> &gt; <a href=\"" . MXB_BM_VIEWTHREAD1 . "tid=$tid\">$threadname</a> &gt; ";
    }
}

if (empty($previewpost)) {
    $postaction .= _TEXTPOSTREPLY;
} else {
    $postaction .= _TEXTPREVIEW;
}

$mxbnavigator->add(false, $postaction);

$posticon = (isset($posticon)) ? $posticon : '';

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

$errormessage = array();
// -----------------------------------------------------------------------------
// Gültigkeits-Check
// -----------------------------------------------------------------------------
if (!empty($replysubmit) || !empty($previewpost)) {
    if (empty($message)) {
        $errormessage[] = _ERRORNOMESSAGE;
    } else {
        $message = nl2br($message);
        $message = preg_replace('#<br\s*/?>#i', '', $message);
        if (!trim($message)) {
            $errormessage[] = _ERRORNOMESSAGE;
        }
    }
}
// -----------------------------------------------------------------------------
// Flood-Control und Captcha
// -----------------------------------------------------------------------------
if (!isOnStaff($eBoardUser['status']) && !empty($replysubmit)) {
    $result = sql_query("SELECT lastpost FROM $table_forums WHERE fid=" . intval($fid));
    $row = sql_fetch_object($result);
    if (!empty($row->lastpost)) {
        $lastpost = explode('|', $row->lastpost);
        $rightnow = time() - $floodctrl;
        if ($rightnow <= $lastpost[0] && $username == $lastpost[1]) {
            $errormessage[] = sprintf(_FLOODPROTECT, $floodctrl);
        }
    }

    $result = mxbCheckCaptcha($eBoardUser);
    if ($result !== true) {
        $errormessage[] = $result; // _CAPTCHAWRONG
    }
}
// -----------------------------------------------------------------------------
// wenn Fehler aufgetreten, zur Vorschau umschalten, anstatt abzuspeichern
// -----------------------------------------------------------------------------
if (!empty($replysubmit) && !empty($errormessage)) {
    unset($replysubmit);
    $previewpost = _TEXTPREVIEW;
}
// -----------------------------------------------------------------------------
// Topic eingeben
// -----------------------------------------------------------------------------
if (empty($replysubmit) && empty($previewpost)) {
    $sigcheck = '';
    if (!mxbIsAnonymous() && !empty($eBoardUser['user_sig'])) {
        $sigcheck = ' checked="checked"';
    }
    if (isset($repquoteid)) {
        if (empty($repquoteid) && !empty($tid)) {
            $result = sql_query("SELECT * FROM $table_threads WHERE tid=" . intval($tid));
            $thaquote = sql_fetch_object($result);
        } elseif (!empty($repquoteid)) {
            $result = sql_query("SELECT * FROM $table_posts WHERE pid='" . intval($repquoteid) . "'");
            $thaquote = sql_fetch_object($result);
        }
        if (!empty($thaquote->fid)) {
            $result = sql_query("SELECT type, name, private, allowhtml, allowsmilies, allowbbcode, guestposting, fup, postperm, allowimgcode, userlist, moderator FROM $table_forums WHERE fid='" . intval($thaquote->fid) . "'");
            $quoteforum = sql_fetch_object($result);
            // / TODO: rechte vom quote Forum besser überprüfen
            if (mxbPrivateCheck($quoteforum)) {
                // [quote author=Andi link=topic=16670.msg115098#msg115098 date=1147701559]
                $pidlnk = (empty($thaquote->pid)) ? '' : ".msg" . $thaquote->pid . "#msg" . $thaquote->pid . "";
                $thaquote->message = mxbFixBbCodeQuote("[quote author=" . urlencode($thaquote->author) . " link=topic=" . $thaquote->tid . "" . $pidlnk . " date=" . $thaquote->dateline . "]" . stripslashes(trim($thaquote->message)) . " [/quote]\n");
                if (preg_match_all('#\[\[editby=([^=]+)=([0-9]+)\]\]#i', $thaquote->message, $matches)) {
                    foreach($matches[0] as $i => $match) {
                        $thaquote->message = trim(str_replace($match, '', $thaquote->message));
                    }
                }
            } else {
                $thaquote->message = '';
            }
        }
    }
    pmxHeader::add_script(MXB_BASEMODJS . '/unb_lib.js');

    ?>
<h2><?php echo _TEXTPOSTREPLY ?></h2>    
<form method="post" name="input" action="<?php echo MXB_BM_POSTREPLY1 ?>fid=<?php echo $fid?>&amp;tid=<?php echo $tid, $jumper?>#preview">
	<div id="postreplybox" class="panel bgcolor2">
		<div class="inner">
			
			<h3><?php echo _TEXTMESSAGE ?></h3>
			<fieldset class="fields1">
<?php if ($piconstatus == 'on') { ?>
			<dl>
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
				<textarea name="message" id="message" rows="15" cols="70" onkeydown="return UnbTextKeydownHandler(this)"><?php echo ((empty($thaquote->message)) ? '' : $thaquote->message) ?></textarea>
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
    
	<div class="panel">
		<fieldset class="submit-buttons">
			<input type="submit" accesskey="k" tabindex="8" name="replysubmit" value="<?php echo _TEXTPOSTREPLY ?>" class="button1"/>&nbsp;
            <input type="submit" tabindex="5" name="previewpost" value="<?php echo _TEXTPREVIEW ?>" class="button2"/>
		</fieldset>
	</div>

	<div class="panel bgcolor4" id="options-panel">
		<div class="inner">
			<fieldset class="fields1">
<?php if ($forums->allowsmilies == 'yes') { ?>
  			<div><label for="smileyoff"><input type="checkbox" name="smileyoff" id="smileyoff" value='yes' /><?php echo _TEXTDISSMILEYS ?></label></div>
<?php } if ($forums->allowbbcode == 'yes') { ?>
  			<div><label for="bbcodeoff"><input type="checkbox" name="bbcodeoff" id="bbcodeoff" value='yes' /><?php echo _BBCODEOFF ?></label></div>
<?php } if (!mxbIsAnonymous()) { ?>
	  		<div><label for="usesig"><input type="checkbox" name="usesig" id="usesig" value='yes' <?php echo $sigcheck ?> /><?php echo _TEXTUSESIG ?></label></div>
  			<div><label for="emailnotify"><input type="checkbox" name="emailnotify" id="emailnotify" value='yes' /><?php echo _EMAILNOTIFYTOREPLIES ?></label></div>
<?php } ?>    			 						
			</fieldset>
		</div>
	</div>
</form>
<?php
    mxb_post_reply_preview();
}
// -----------------------------------------------------------------------------
// Vorschau
// -----------------------------------------------------------------------------
if (!empty($previewpost)) {
    $currtime = time();
    $date = gmdate("n/j/y", $currtime + ($timeoffset * 3600));
    $time = gmdate("H:i", $currtime + ($timeoffset * 3600));
    $poston = _TEXTPOSTON . " $date " . _TEXTAT . " $time";

    if (!empty($smileyoff) && $smileyoff == 'yes') {
        $smileoffcheck = ' checked="checked"';
    } else {
        $smileyoff = 'off';
        $smileoffcheck = '';
    }
    if (!empty($bbcodeoff) && $bbcodeoff == 'yes') {
        $codeoffcheck = ' checked="checked"';
    } else {
        $bbcodeoff = 'off';
        $codeoffcheck = '';
    }
    $usesigcheck = '';
    if (!empty($usesig) && $usesig == 'yes') {
        $usesigcheck = ' checked="checked"';
    }
    $notifycheck = '';
    if (!empty($emailnotify) && $emailnotify == 'yes') {
        $notifycheck = ' checked="checked"';
    }

    $message = (empty($message)) ? '' : stripslashes($message);
    $message1 = mxbPostify($message, $forums->allowhtml, $forums->allowsmilies, $forums->allowbbcode, $forums->allowimgcode, $smileyoff, $bbcodeoff);
    if (!empty($usesig) && $usesig == 'yes' && !empty($eBoardUser['user_sig'])) {
            $message1 .= mxb_add_signatur($eBoardUser['user_sig']);
    }

    ?>
<a name="preview" id="preview"></a>
<?php
    if (!empty($errormessage)) {
        $errormessage = implode('</li><li>', $errormessage);

        ?>
        <div class="warning align-center">
  				<h3 class="align-center"><?php echo _MXBERRORWHILESUBMIT ?></h3>
  				<p>
  					<?php echo $errormessage ?>
  				</p>
  			</div>
<?php
    } // END if (isset($errormessage))
    pmxHeader::add_script(MXB_BASEMODJS . '/unb_lib.js');

    ?>
<h2><?php echo _TEXTPREVIEW ?></h2>
<div class="post bgcolor2">
	<div class="inner">      
			<p class="author">
				<strong><?php echo $username ?></strong>
			<br />
			<?php echo $poston?>
			</p>
			<div class="content">
				<?php echo $message1 ?>
			</div>
	</div>
</div>    

<h2><?php echo _TEXTPOSTREPLY ?></h2>   
<form id="postform" method="post" name="postform" action="<?php echo MXB_BM_POSTREPLY1 ?>fid=<?php echo $fid?>&amp;tid=<?php echo $tid, $jumper?>#preview">
	<div id="postpreviewbox" class="panel bgcolor2">
		<div class="inner">
			<h3><?php echo _TEXTMESSAGE ?></h3>
			<fieldset class="fields1">
<?php if ($piconstatus == 'on') { ?>
				<dl>
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
				   <textarea name="message" id="message" rows="15" cols="70" onkeydown="return UnbTextKeydownHandler(this)"><?php echo htmlspecialchars(mxbFixBbCodeQuote($message)) ?></textarea>
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
    
	<div class="panel">
			<fieldset class="submit-buttons">			
				<input type="submit" accesskey="s" tabindex="6" name="replysubmit" value="<?php echo _TEXTPOSTREPLY ?>" class="button1" />&nbsp;
                <input type="submit" tabindex="5" name="previewpost" value="<?php echo _TEXTPREVIEW ?>" class="button2"/>
			</fieldset>
		</div>
	</div>

	<div class="panel bgcolor3" id="options-panel">
		<div class="inner">
		<fieldset class="fields1">
<?php if ($forums->allowsmilies == 'yes') { ?>
  			<div><label for="smileyoff"><input type="checkbox" name="smileyoff" id="smileyoff" value="yes" <?php echo $smileoffcheck ?> /><?php echo _TEXTDISSMILEYS ?></label></div>
<?php } if ($forums->allowbbcode == 'yes') { ?>
  			<div><label for="bbcodeoff"><input type="checkbox" name="bbcodeoff" id="bbcodeoff" value="yes" <?php echo $codeoffcheck ?> /><?php echo _BBCODEOFF ?></label></div>
<?php } if (!mxbIsAnonymous()) { ?>
  				<div><label for="usesig"><input type="checkbox" name="usesig" id="usesig" value="yes" <?php echo $usesigcheck ?> /><?php echo _TEXTUSESIG ?></label></div>
  				<div><label for="emailnotify"><input type="checkbox" name="emailnotify" id="emailnotify" value="yes" <?php echo $notifycheck ?> /><?php echo _EMAILNOTIFYTOREPLIES ?></label></div>
<?php } ?>    			 					
			
					</fieldset>
	</div>
</div>

</form>

<?php
    mxb_post_reply_preview();
}
// -----------------------------------------------------------------------------
// Topic in Datenbank schreiben
// -----------------------------------------------------------------------------
if (!empty($replysubmit)) {
    $message = mxAddSlashesForSQL($message);

    if (empty($smileyoff) || $smileyoff != 'yes') {
        $smileyoff = 'off';
    }
    if (empty($bbcodeoff) || $bbcodeoff != 'yes') {
        $bbcodeoff = 'off';
    }
    if (empty($usesig) || $usesig != 'yes') {
        $usesig = 'no';
    }
    if (empty($emailnotify) || $emailnotify != 'yes' || mxbIsAnonymous()) {
        $emailnotify = 'no';
    }

    $result = sql_query("SELECT closed FROM $table_threads WHERE fid=" . intval($fid) . " AND tid=" . intval($tid));
    $closed1 = sql_fetch_object($result);
    $closed = $closed1->closed;

    if ($closed != 'yes') {
        $thatime = time();

        $result = sql_query("SELECT author FROM $table_threads WHERE tid=" . intval($tid) . " AND emailnotify='yes'");
        $thread = sql_fetch_object($result);

        if ($thread) {
            $result_user = sql_query("SELECT fm.username, u.email, fm.status, u.uname, u.user_stat
                                    FROM $table_members AS fm
                                    LEFT JOIN {$user_prefix}_users AS u
                                    ON fm.username = u.uname
                                    WHERE fm.username='" . substr($thread->author, 0, 25) . "'");
            $objectuser = sql_fetch_object($result_user);
            if (!$objectuser || ($objectuser->status != 'Administrator' && (empty($objectuser->uname) || $objectuser->user_stat != 1))) {
                // wenn nicht vorhanden, einfach weiter
                // by Andi: doof gecodet, ich weiss, aber da war ich zu faul die bedingung umzuschreiben :-))
            } else {
                $theurl = trim(MX_HOME_URL, '/') . "/" . MXB_BM_VIEWTHREAD1 . "tid=$tid";
                $theurl = preg_replace("/\/{1,}modules/", "/modules", $theurl);
                if (($emailalltoadmin == 'on') && ($mailonpost == 'on')) {
                    if ($objectuser->email != $adminemail && $eBoardUser['username'] != $thread->author) {
                        $mailstore[$objectuser->email] = $objectuser->email;
                        mxMail($objectuser->email, '[' . $bbname . '] ' . _EMAILNOTIFYSUBJECT . " " . strip_tags($threadname), _EMAILNOTIFYINTRO . "\n\n" . _TEXTFORUM . " \"" . strip_tags($forums->name) . "\"\n" . _TEXTSUBJECT . " \"" . strip_tags($threadname) . "\"\n" . _TEXTAUTHOR . " \"" . $eBoardUser['username'] . "\"\n\n" . _TEXTMESSAGE . "\n\"$message\"\n\n\n" . _EMAILNOTIFYINTRO2 . "\n\n$theurl\n\n" . _EMAILNOTIFYEND);
                    }
                } elseif ($eBoardUser['username'] != $thread->author) {
                    $mailstore[$objectuser->email] = $objectuser->email;
                    mxMail($objectuser->email, '[' . $bbname . '] ' . _EMAILNOTIFYSUBJECT . " " . strip_tags($threadname), _EMAILNOTIFYINTRO . "\n\n" . _TEXTFORUM . " \"" . strip_tags($forums->name) . "\"\n" . _TEXTSUBJECT . " \"" . strip_tags($threadname) . "\"\n" . _TEXTAUTHOR . " \"" . $eBoardUser['username'] . "\"\n\n" . _TEXTMESSAGE . "\n\"$message\"\n\n\n" . _EMAILNOTIFYINTRO2 . "\n\n$theurl\n\n" . _EMAILNOTIFYEND);
                }
            }
        }

        if (($emailalltomoderator == 'on') || ($emailalltoadmin == 'on')) {
            $theurl = trim(MX_HOME_URL, '/') . "/" . MXB_BM_VIEWTHREAD1 . "tid=$tid";
            $theurl = preg_replace("/\/{1,}modules/", "/modules", $theurl);
            $mailsubject = '[' . $bbname . '] ' . _EMAILNOTIFYSUBJECT . " " . strip_tags($threadname);
            $mailmessage = _EMAILNOTIFYINTRO . "\n\n" . _TEXTFORUM . " \"" . strip_tags($forums->name) . "\"\n" . _TEXTSUBJECT . " \"" . strip_tags($threadname) . "\"\n" . _TEXTAUTHOR . " \"" . $eBoardUser['username'] . "\"\n\n" . _TEXTMESSAGE . "\n\"$message\"\n\n\n" . _EMAILNOTIFYINTRO2 . "\n\n$theurl\n\n" . _EMAILNOTIFYEND;

            if ($mailonpost == 'on' && $emailalltoadmin == 'on') {
                mxbNotifyAdmin($fid, $mailsubject, $mailmessage, "notifypost");
            }

            if ($moderatormailonpost == 'on' && $emailalltomoderator == 'on') {
                mxbNotifyModerator($fid, $mailsubject, $mailmessage, "notifypost");
            }
        }

        $result = sql_query("SELECT author FROM $table_posts WHERE tid=" . intval($tid) . " AND emailnotify='yes' AND author!='" . substr($eBoardUser['username'], 0, 25) . "'");
        while ($post = sql_fetch_object($result)) {
            if ($post) {
                $result_user = sql_query("SELECT fm.username, u.email, fm.status, u.uname, u.user_stat
                                    FROM $table_members AS fm
                                    LEFT JOIN {$user_prefix}_users AS u
                                    ON fm.username = u.uname
                                    WHERE fm.username='" . substr($post->author, 0, 25) . "'");
                $objectuser = sql_fetch_object($result_user);
                if (!$objectuser || ($objectuser->status != 'Administrator' && (empty($objectuser->uname) || $objectuser->user_stat != 1))) {
                    // wenn nicht vorhanden, einfach weiter
                    continue;
                }
                $theurl = trim(MX_HOME_URL, '/') . "/" . MXB_BM_VIEWTHREAD1 . "tid=$tid";
                $theurl = preg_replace("/\/{1,}modules/", "/modules", $theurl);
                if (empty($mailstore[$objectuser->email])) {
                    $mailstore[$objectuser->email] = $objectuser->email;
                    mxMail($objectuser->email, '[' . $bbname . '] ' . _EMAILNOTIFYSUBJECT . " " . strip_tags($threadname), _EMAILNOTIFYINTRO . "\n\n" . _TEXTFORUM . " \"" . strip_tags($forums->name) . "\"\n" . _TEXTSUBJECT . " \"" . strip_tags($threadname) . "\"\n" . _TEXTAUTHOR . " \"" . $eBoardUser['username'] . "\"\n\n" . _TEXTMESSAGE . "\n\"$message\"\n\n\n" . _EMAILNOTIFYINTRO2 . "\n\n$theurl\n\n" . _EMAILNOTIFYEND);
                }
            }
        }

        sql_query("INSERT INTO $table_posts VALUES ('$fid', '$tid', '', '" . $eBoardUser['username'] . "', '$message', '$thatime', '$posticon', '$usesig', '$onlineip', '$bbcodeoff', '$smileyoff', '$emailnotify')");

        $querypid = sql_query("SELECT pid FROM $table_posts WHERE tid=" . intval($tid) . " AND author='" . substr($eBoardUser['username'], 0, 25) . "' AND dateline='$thatime' AND useip='$onlineip'");
        list($pid) = sql_fetch_row($querypid);

        sql_query("UPDATE $table_threads SET lastpost='$thatime|$username', replies=replies+1 WHERE tid=" . intval($tid));
        sql_query("UPDATE $table_forums SET lastpost='$thatime|$username', posts=posts+1 WHERE fid=" . intval($fid));
        $getmainforum = sql_query("SELECT type, fup FROM $table_forums WHERE fid=" . intval($fid));
        $for = sql_fetch_object($getmainforum);
        if ($for->type == 'sub') {
            sql_query("UPDATE $table_forums SET lastpost='$thatime|$username', posts=posts+1 WHERE fid='" . intval($for->fup) . "'");
        }
        //  Link-Ergänzung
        // hier wird das Lastpost-Datum der Links und deren Ziele aktualisiert
        if ($linkforumstatus == 'on') {
            $getlink = sql_query("SELECT lid, toid, status FROM $table_links WHERE type='forum' AND fromid=" . intval($fid));
            while ($followlink = sql_fetch_object($getlink)) {
                sql_query("UPDATE $table_links SET lastpost='$thatime|$username' WHERE lid='" . intval($followlink->lid) . "'");
                if ($followlink->status == 'on') {
                    sql_query("UPDATE $table_forums SET lastpost='$thatime|$username' WHERE fid='" . intval($followlink->toid) . "'");
                    $getmainforumlink = sql_query("SELECT type, fup FROM $table_forums WHERE fid='" . intval($followlink->toid) . "'");
                    $forlink = sql_fetch_object($getmainforumlink);
                    if ($forlink->type == 'sub') {
                        sql_query("UPDATE $table_forums SET lastpost='$thatime|$username' WHERE fid='" . intval($forlink->fup) . "'");
                    }
                }
            } //while
        } // Ende Link-Forum-Status
        if ($linkthreadstatus == 'on') {
            $getlink = sql_query("SELECT lid, toid, status FROM $table_links WHERE type='thread' AND fromid=" . intval($tid));
            while ($followlink = sql_fetch_object($getlink)) {
                sql_query("UPDATE $table_links SET lastpost='$thatime|$username' WHERE lid='" . intval($followlink->lid) . "'");
                if ($followlink->status == 'on') {
                    sql_query("UPDATE $table_forums SET lastpost='$thatime|$username' WHERE fid='" . intval($followlink->toid) . "'");
                    $getmainforumlink = sql_query("SELECT type, fup FROM $table_forums WHERE fid='" . intval($followlink->toid) . "'");
                    $forlink = sql_fetch_object($getmainforumlink);
                    if ($forlink->type == 'sub') {
                        sql_query("UPDATE $table_forums SET lastpost='$thatime|$username' WHERE fid='" . intval($forlink->fup) . "'");
                    }
                }
            } //while
        } // Ende Link-Thread-Status
        //  Ende Link-Ergänzung
        if (!mxbIsAnonymous()) {
            mxbRepairUserPostNum($eBoardUser['username']);
        }
    } else {
        return mxbExitMessage(_CLOSEDMSG, false);
    }

    if (($threadrow->replies + 1) > $ppp) {
        $viewthreadpage = "&amp;page=" . ceil(($threadrow->replies + 1) / $ppp);
    } else {
        $viewthreadpage = '';
    }
    echo mxbMessageScreen(_REPLYMSG);
    echo mxbRedirectScript(MXB_BM_VIEWTHREAD1 . 'tid=' . $tid . $viewthreadpage . $jumper . '#pid' . $pid, 1250);
}

include_once(MXB_BASEMODINCLUDE . 'footer.php');
// ////////////////////////////// funktionen, nur fuer diese Datei ////////////////////////////
function mxb_post_reply_preview()
{
    extract($GLOBALS);

    ?>
<h3 id="review"><?php echo _TEXTTOPICREVIEW ?></h3>
<div id="topicreview">

<?php
    $result = sql_query("SELECT COUNT(pid) as nbsites FROM $table_posts WHERE tid=" . intval($tid));
    $row = sql_fetch_object($result);
    $replynum = (empty($row->nbsites)) ? 1 : $row->nbsites + 1;
    if ($replynum >= $ppp) {
        $threadlink = MXB_BM_VIEWTHREAD1 . "fid=$fid&amp;tid=$tid";
        eval(_EVALTREVLT);
				echo mxbMessageScreen(_TREVLTMSG);
    } else {
        $thisbg = 'bgcolor1';
        $result = sql_query("SELECT * FROM $table_posts WHERE tid=" . intval($tid) . " ORDER BY dateline DESC");
        while ($reply = sql_fetch_object($result)) {
            $date = gmdate($dateformat, (int)$reply->dateline + ($timeoffset * 3600));
            $time = gmdate($timecode, (int)$reply->dateline + ($timeoffset * 3600));

            $poston = _TEXTPOSTON . " $date " . _TEXTAT . " $time";
            if ($reply->icon) {
                $reply->icon = "<img src=\"" . MXB_BASEMODIMG . "/" . $reply->icon . "\" alt=\"Icon depicting mood of post\" />";
            }

            $reply->message = stripslashes($reply->message);
            $reply->message = mxbPostify($reply->message, $forums->allowhtml, $forums->allowsmilies, $forums->allowbbcode, $forums->allowimgcode, $reply->smileyoff, $reply->bbcodeoff);

            ?>
	<div class="post <?php echo $thisbg ?>">
		<div class="inner">      
				<p class="author">
					<?php echo $reply->icon ?> 
					<strong><?php echo $reply->author ?></strong>
					<br />
					<?php echo $poston ?>
				</p>
				<div class="content">
					<?php echo $reply->message ?>
				</div>
		</div>
	</div>
	<?php
  if ($thisbg == 'bgcolor2') { $thisbg = 'bgcolor1'; } else { $thisbg = 'bgcolor2'; } //alternate bgcolor
            
        }// end while
        $result = sql_query("SELECT * FROM $table_threads WHERE tid=" . intval($tid));
        $topic = sql_fetch_object($result);
        $date = gmdate($dateformat, (int)$topic->dateline + ($timeoffset * 3600));
        $time = gmdate($timecode, (int)$topic->dateline + ($timeoffset * 3600));

        $poston = _TEXTPOSTON . " $date " . _TEXTAT . " $time";
        if (!empty($topic->icon)) {
            $topic->icon = "<img src=\"" . MXB_BASEMODIMG . "/" . $topic->icon . "\" alt=\"Icon depicting mood of post\" />";
        }

        $topic->message = stripslashes($topic->message);
        $topic->message = mxbPostify($topic->message, $forums->allowhtml, $forums->allowsmilies, $forums->allowbbcode, $forums->allowimgcode, $topic->smileyoff, $topic->bbcodeoff);

        ?>
	<div class="post <?php echo $thisbg ?>">
		<div class="inner">
			<p class="author">
				<?php echo $topic->icon ?>
				<strong><?php echo $topic->author ?></strong>
				<br />
				<?php echo $poston ?>
			</p>
			<div class="content">
				<?php echo $topic->message?>
			</div>
		</div>
	</div>

<?php } ?>
    
	</div>    
<?php
}

?>
