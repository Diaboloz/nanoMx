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

if (!mxbPostingAllowed($forums, 'newpost')) {
    return mxbExitMessage(_POSTPERMERR, true);
}

$jumper = mxbGetJumplink();
if ($linkstatus == 'on') {
    $result = sql_query("SELECT name, fid FROM $table_forums WHERE fid=" . intval($jumplink));
    $fupjumplink = sql_fetch_object($result);
    if ($linktype == "thread") {
        $postaction = "<a href=\"" . MXB_BM_FORUMDISPLAY1 . "fid=" . $fupjumplink->fid . "\">" . $fupjumplink->name . "</a> &gt; ";
    } else {
        $postaction = "<a href=\"" . MXB_BM_FORUMDISPLAY1 . "fid=" . $fupjumplink->fid . "\">" . $fupjumplink->name . "</a> &gt; <a href=\"" . MXB_BM_FORUMDISPLAY1 . "fid=$fid$jumper\">" . $forums->name . "</a> &gt; ";
    }
} else {
    if ($forums->type == 'forum') {
        $postaction = "<a href=\"" . MXB_BM_FORUMDISPLAY1 . "fid=$fid\">" . $forums->name . "</a> &gt; ";
    } else {
        $result = sql_query("SELECT name, fid FROM $table_forums WHERE fid=" . intval($forums->fup));
        $fup = sql_fetch_object($result);
        $postaction = "<a href=\"" . MXB_BM_FORUMDISPLAY1 . "fid=" . $fup->fid . "\">" . $fup->name . "</a> &gt; <a href=\"" . MXB_BM_FORUMDISPLAY1 . "fid=$fid\">" . $forums->name . "</a> &gt; ";
    }
}

if (!mxbIsAnonymous()) {
    $username = $eBoardUser['username'];
} else {
    $username = MXB_ANONYMOUS;
}

if (empty($previewpost)) {
    $postaction .= _TEXTPOSTNEW;
} else {
    $postaction .= _TEXTPREVIEW;
}

$mxbnavigator->add(false, $postaction);

$posticon = (isset($posticon)) ? $posticon : '';

if ($forums->allowimgcode == 'yes') {
    $allowimgcode = '<span style="color:green">' . _TEXTON . '</span>';
} else {
    $allowimgcode = '<span style="color:red">' . _TEXTOFF . '</span>';
}

if ($forums->allowhtml == 'yes') {
    $allowhtml = '<span style="color:green">' . _TEXTON . '</span>';
} else {
    $allowhtml = '<span style="color:red">' . _TEXTOFF . '</span>';
}

if ($forums->allowsmilies == 'yes') {
    $allowsmilies = '<span style="color:green">' . _TEXTON . '</span>';
} else {
    $allowsmilies = '<span style="color:red">' . _TEXTOFF . '</span>';
}

if ($forums->allowbbcode == 'yes') {
    $allowbbcode = '<span style="color:green">' . _TEXTON . '</span>';
} else {
    $allowbbcode = '<span style="color:red">' . _TEXTOFF . '</span>';
}

$topoption = '';
if (mxbIsModeratorInForum($forums)) {
    $topoption = (isset($toptopic) && $toptopic == 'yes') ? ' checked="checked"' : '';
    $topoption = '<div><label for="toptopic"><input type="checkbox" name="toptopic" id="toptopic" value="yes"' . $topoption . ' />' . _TOPMSGQUES . '</label></div>';
}

$errormessage = array();
// -----------------------------------------------------------------------------
// Gültigkeits-Check
// -----------------------------------------------------------------------------
if (!empty($topicsubmit) || !empty($previewpost)) {
    if (empty($subject)) {
        $errormessage[] = _TEXTNOSUBJECT;
    }
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
if (!isOnStaff($eBoardUser['status']) && !empty($topicsubmit)) {
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
if (!empty($topicsubmit) && !empty($errormessage)) {
    unset($topicsubmit);
    $previewpost = _TEXTPREVIEW;
}
// -----------------------------------------------------------------------------
// Topic eingeben
// -----------------------------------------------------------------------------
if (empty($topicsubmit) && empty($previewpost)) {
    $sigcheck = '';
    if (!mxbIsAnonymous() && !empty($eBoardUser['user_sig'])) {
        $sigcheck = ' checked="checked"';
    }
    pmxHeader::add_script(MXB_BASEMODJS . '/unb_lib.js');

    ?>

<h2><?php echo _TEXTPOSTNEW ?></h2>  
<form method="post" name="input" class="mx-form" action="<?php echo MXB_BM_POSTNEWTOPIC1 ?>fid=<?php echo $fid, $jumper?>#preview">
	<div id="postingbox" class="panel bgcolor2">
		<div class="inner mas">
			<h3><?php echo _TEXTMESSAGE ?></h3>
			<fieldset class="fields1">
				<dl style="clear: left;">
					<dt><label><?php echo _TEXTSUBJECT ?></label></dt>
					<dd><input type="text" name="subject" size="45" /></dd>
				</dl>
<?php if (isOnStaff($eBoardUser['status']) || $colorsubject == 'on') { ?>
				<dl style="clear: left;">
					<dt><label><?php echo _TEXTPRIORITY ?></label></dt>
						<dd>
							<input type="radio" name="priority" value="high" /><span style="color: <?php echo $color1 ?>;"><?php echo _TEXTPRIORITYHIGH ?></span>
							<input type="radio" name="priority" value="normal" checked="checked" /><?php echo _TEXTPRIORITYNORMAL ?>
							<input type="radio" name="priority" value="low" /><span style="color: <?php echo $color2 ?>;"><?php echo _TEXTPRIORITYLOW ?></span>
							<input type="checkbox" name="bold" value="true" /><b>&nbsp;<?php echo _TEXTPRIORITYBOLD ?></b>
							<input type="checkbox" name="italic" value="true" />&nbsp;<i><?php echo _TEXTPRIORITYITALIC ?></i>
						</dd>
				</dl>
<?php } ?>
<?php if (!empty($posticon) AND $piconstatus == 'on') { ?>
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
				<h4 class="h6-like"><?php echo _TEXTRIGHTS ?></h4>
 				<?php echo _TEXTHTMLIS ?> <?php echo $allowhtml ?><br />
    		    <?php echo _TEXTSMILIESARE ?>  <?php echo $allowsmilies ?><br />
    		    <?php echo _TEXTBBCODEIS ?> <?php echo $allowbbcode ?><br />
    		    <?php echo _TEXTIMGCODEIS ?> <?php echo $allowimgcode ?>
			</div>

			
				<div id="message-box">
  				<textarea name="message" id="message" rows="15" cols="70" onkeydown="return UnbTextKeydownHandler(this)"></textarea>
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
		
<?php if (mxbUseCaptcha($eBoardUser)) { echo mxbPrintCaptcha(); }  ?>
    
	<div class="panel bgcolor1">
		<div class="inner">
			<fieldset class="submit-buttons">
				<input type="submit" accesskey="s" tabindex="6" name="topicsubmit" value="<?php echo _TEXTPOSTNEW ?>" class="mx-button mx-button-primary" />&nbsp;
                <input type="submit" tabindex="5" name="previewpost" value="<?php echo _TEXTPREVIEW ?>" class="mx-button"/>
			</fieldset>
		</div>
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
  				<?php echo $topoption ?>
<?php } ?>    			 					
			
					</fieldset>
	</div>
</div>


</form>

<?php
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

    $boldcheck = '';
    $italiccheck = '';
    $priorityhighcheck = '';
    $prioritynormalcheck = '';
    $prioritylowcheck = '';
    $subject = (isset($subject)) ? stripslashes($subject) : '';
    $previewsubject = $subject;

    if (isOnStaff($eBoardUser['status']) || $colorsubject == 'on') {
        if (!empty($bold)) {
            $previewsubject = "<b>" . $previewsubject . "</b>";
            $boldcheck = ' checked="checked"';
        }
        if (!empty($italic)) {
            $previewsubject = "<i>" . $previewsubject . "</i>";
            $italiccheck = ' checked="checked"';
        }
        if (empty($priority) || $priority == "normal") {
            $prioritynormalcheck = ' checked="checked"';
        } else if ($priority == "high") {
            $previewsubject = '<span style="color: ' . $color1 . '">' . $previewsubject . "</span>";
            $priorityhighcheck = ' checked="checked"';
        } else if ($priority == "low") {
            $previewsubject = '<span style="color: ' . $color2 . '">' . $previewsubject . "</span>";
            $prioritylowcheck = ' checked="checked"';
        }
    }

    $posticon = (empty($posticon)) ? '' : '<img src="' . MXB_BASEMODIMG . '/' . $posticon . '" alt="' . $posticon . '" border="0" align="middle">';

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
  		<ul class="align-center" style="list-style-type:none;">
  			<li><?php echo $errormessage ?></li>
  		</ul>
		</div>
		<?php
    } // END if (isset($errormessage))
    pmxHeader::add_script(MXB_BASEMODJS . '/unb_lib.js');

    ?>
<table cellspacing="0" cellpadding="0" border="0" width="<?php echo $tablewidth?>" align="center">
<tr>
  <td class="bordercolor">

<table border="0" cellspacing="<?php echo $borderwidth?>" cellpadding="<?php echo $tablespace?>" width="100%">
<tr class="mxb-header">
  <td colspan="2"><?php echo _TEXTPREVIEW?></td>
</tr>

<tr valign="top" class="tablerow">
<td class="altbg1"><div class="f11pix"><?php echo _TEXTSUBJECT ?></div></td>
<td class="altbg1"><div class="f12pix"><?php echo $previewsubject ?></div></td>
</tr>

<tr class="tablerow altbg1">
  <td rowspan="2" valign="top" width="18%"><span class="postauthor"><?php echo $username?></span><br /><br /></td>
  <td width="82%"><?php echo $posticon?>  <?php echo $poston?></td></tr>
  <tr class="tablerow altbg1">
  <td height="120" valign="top" width="82%">
    <div class="ebpostchild f12pix"><?php echo $message1 ?></div>
  </td></tr>
</table>

  </td>
</tr>
</table>
<br />
<form method="post" name="input" action="<?php echo MXB_BM_POSTNEWTOPIC1 ?>fid=<?php echo $fid, $jumper?>#preview">
<table cellspacing="0" cellpadding="0" border="0" width="<?php echo $tablewidth?>" align="center">
<tr><td class="bordercolor">

<table border="0" cellspacing="<?php echo $borderwidth?>" cellpadding="<?php echo $tablespace?>" width="100%">
<tr class="mxb-header">
<td colspan="2"><?php echo _TEXTPOSTNEW?></td>
</tr>

<tr valign="top" class="tablerow">
<td class="altbg1"><b><?php echo _TEXTSUBJECT?></b></td>
<td class="altbg2"><input type="text" name="subject" size="45" value="<?php echo $subject?>" style="width: 99%;" /></td>
</tr>
<?php
    if (isOnStaff($eBoardUser['status']) || $colorsubject == 'on') {

        ?>
<tr valign="top" class="tablerow">
<td class="altbg1"><b><?php echo _TEXTPRIORITY?></b></td>
<td class="altbg2">
<input type="radio" name="priority" value="high" <?php echo $priorityhighcheck?>/><span style="color: <?php echo $color1?>;"><?php echo _TEXTPRIORITYHIGH?></span>
&nbsp;<input type="radio" name="priority" value="normal" <?php echo $prioritynormalcheck?>/><?php echo _TEXTPRIORITYNORMAL?>
&nbsp;<input type="radio" name="priority" value="low" <?php echo $prioritylowcheck?>/><span style="color: <?php echo $color2?>;"><?php echo _TEXTPRIORITYLOW?></span>
&nbsp;<input type="checkbox" name="bold" value="true" <?php echo $boldcheck?>><b>&nbsp;<?php echo _TEXTPRIORITYBOLD?></b>&nbsp;
&nbsp;<input type="checkbox" name="italic" value="true" <?php echo $italiccheck?>>&nbsp;<i><?php echo _TEXTPRIORITYITALIC?></i>
</td>
</tr>
<?php }

    ?>
<tr valign="top" class="tablerow">
<td class="altbg1" valign="top"><b><?php echo _TEXTMESSAGE?></b>
<br/><br/>

<?php

    if ($forums->allowsmilies == 'yes') {
        echo mxbShowTableSmilies();
    }

    ?>
</td>

<h2><?php echo _TEXTPOSTNEW ?></h2>  
<form method="post" name="input" action="<?php echo MXB_BM_POSTNEWTOPIC1 ?>fid=<?php echo $fid, $jumper?>#preview">
	<div id="postingbox" class="panel bgcolor2">
		<div class="inner">
			<h3><?php echo _TEXTMESSAGE ?></h3>
			<fieldset class="fields1">
				<dl style="clear: left;">
					<dt><label><?php echo _TEXTSUBJECT ?></label></dt>
					<dd><input type="text" name="subject" size="45" value="<?php echo $subject ?>" /></dd>
				</dl>

<?php if (isOnStaff($eBoardUser['status']) || $colorsubject == 'on') { ?>
				<dl style="clear: left;">
					<dt><label><?php echo _TEXTPRIORITY ?></label></dt>
						<dd>
							<input type="radio" name="priority" value="high" <?php echo $priorityhighcheck ?>/><span style="color: <?php echo $color1?>;"><?php echo _TEXTPRIORITYHIGH ?></span>
							<input type="radio" name="priority" value="normal" <?php echo $prioritynormalcheck ?>/><?php echo _TEXTPRIORITYNORMAL ?>
							<input type="radio" name="priority" value="low" <?php echo $prioritylowcheck ?>/><span style="color: <?php echo $color2 ?>;"><?php echo _TEXTPRIORITYLOW ?></span>
							<input type="checkbox" name="bold" value="true" <?php echo $boldcheck ?>><b>&nbsp;<?php echo _TEXTPRIORITYBOLD ?></b>&nbsp;
							<input type="checkbox" name="italic" value="true" <?php echo $italiccheck ?>>&nbsp;<i><?php echo _TEXTPRIORITYITALIC ?></i>
						</dd>
				</dl>
<?php } ?>

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
    
	<div class="panel bgcolor1">
		<div class="inner">
			<fieldset class="submit-buttons">
				<input type="submit" accesskey="s" tabindex="6" name="topicsubmit" value="<?php echo _TEXTPOSTNEW ?>" class="button2" />&nbsp;
        <input type="submit" tabindex="5" name="previewpost" value="<?php echo _TEXTPREVIEW ?>" class="button1"/>
			</fieldset>
		<span class="corners-bottom"><span><!-- --></span></span></div>
	</div>

	<div class="panel bgcolor4" id="options-panel">
		<div class="inner"><span class="corners-top"><span><!-- --></span></span>
		<fieldset class="fields1">
<?php if ($forums->allowsmilies == 'yes') { ?>
  			<div><label for="smileyoff"><input type="checkbox" name="smileyoff" value='yes' <?php echo $smileoffcheck ?> /> <?php echo _TEXTDISSMILEYS ?></label></div>
<?php } if ($forums->allowbbcode == 'yes') { ?>
  			<div><label for="bbcodeoff"><input type="checkbox" name="bbcodeoff" value='yes' <?php echo $codeoffcheck ?> /> <?php echo _BBCODEOFF ?></label></div>
<?php } if (!mxbIsAnonymous()) { ?>
  				<div><label for="usesig"><input type="checkbox" name="usesig" value='yes' <?php echo $usesigcheck ?> /> <?php echo _TEXTUSESIG ?></label></div>
  				<div><label for="emailnotify"><input type="checkbox" name="emailnotify" value='yes' <?php echo $notifycheck ?> /> <?php echo _EMAILNOTIFYTOREPLIES ?></label></div>
  				<?php echo $topoption ?>
<?php } ?>    			 						
		</fieldset>
	</div>
	</div>
	<input type="hidden" name="username" value="<?php echo $username?>"/>
	<input type="hidden" name="postauthor" value="<?php echo (isset($postauthor)) ? $postauthor : '' ?>"/>
	<input type="hidden" name="postdateline" value="<?php echo (isset($postdateline)) ? $postdateline : '' ?>"/>
</form>

<?php
}
// -----------------------------------------------------------------------------
// Topic in Datenbank schreiben
// -----------------------------------------------------------------------------
if (!empty($topicsubmit)) {
    $bold = (isset($bold)) ? $bold : '';
    $italic = (isset($italic)) ? $italic : '';

    $subject = str_replace("<", "&lt;", $subject);
    $subject = str_replace(">", "&gt;", $subject);
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

    $thatime = time();
    if (!empty($postauthor)) {
        $username = $postauthor;
    }
    if (!empty($postdateline)) {
        $thatime = $postdateline;
    }
    sql_query("INSERT INTO $table_threads (fid, subject, lastpost, views, replies, author, message, dateline, icon, usesig, closed, topped, useip, bbcodeoff, smileyoff, emailnotify) VALUES ($fid, '$subject', '$thatime|$username', '0', '0', '$username', '$message', '$thatime', '$posticon', '$usesig', '', '', '$onlineip', '$bbcodeoff', '$smileyoff', '$emailnotify')");

    $tid = sql_insert_id();
    $thatime = time();
    sql_query("UPDATE $table_forums SET lastpost='$thatime|$username', threads=threads+1, posts=posts+1 WHERE fid=" . intval($fid));

    if (($emailalltomoderator == 'on') || ($emailalltoadmin == 'on')) {
        $theurl = trim(MX_HOME_URL, '/') . "/" . MXB_BM_VIEWTHREAD1 . "tid=$tid";
        $theurl = preg_replace("/\/{1,}modules/", "/modules", $theurl);
        $mailsubject = '[' . $bbname . '] ' . _EMAILTHREADNOTIFYSUBJECT . " " . strip_tags($subject);
        $mailmessage = _EMAILTHREADNOTIFYINTRO . "\n\n" . _TEXTFORUM . " \"" . strip_tags($forums->name) . "\"\n" . _TEXTSUBJECT . " \"" . strip_tags($subject) . "\"\n" . _TEXTAUTHOR . " \"$username\"\n\n" . _TEXTMESSAGE . "\n\"$message\"\n\n\n" . _EMAILNOTIFYINTRO2 . "\n\n$theurl\n\n" . _EMAILNOTIFYEND;

        if ($mailonthread == 'on' && $emailalltoadmin == 'on') {
            mxbNotifyAdmin($fid, $mailsubject, $mailmessage, "notifythread");
        }

        if ($moderatormailonthread == 'on' && $emailalltomoderator == 'on') {
            mxbNotifyModerator($fid, $mailsubject, $mailmessage, "notifythread");
        }
    }

    if ($forums->type == 'sub') {
        sql_query("UPDATE $table_forums SET lastpost='$thatime|$username', threads=threads+1, posts=posts+1 WHERE fid=" . intval($forums->fup));
    }

    if (!mxbIsAnonymous()) {
        mxbRepairUserPostNum($eBoardUser['username']);
    }

    if (isset($toptopic) && $toptopic == 'yes' && mxbIsModeratorInForum($forums)) {
        sql_query("UPDATE $table_threads SET topped='1' WHERE tid=" . intval($tid));
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
    } // Ende Linkstatus
    // 
    echo mxbMessageScreen(_POSTMSG);
    echo mxbRedirectScript(MXB_BM_VIEWTHREAD1 . 'tid=' . $tid . $jumper, 1250);
}
include_once(MXB_BASEMODINCLUDE . 'footer.php');

?>
