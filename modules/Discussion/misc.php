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
// nur diese Aktionen erlauben
if (empty($action)) {
    include_once(dirname(__FILE__) . '/index.php');
    return;
} else if ($action == 'faq') {
    $miscaction = _TEXTFAQ;
} else if ($action == 'online') {
    $miscaction = _WHOSONLINE;
} else if ($action == 'report') {
    $miscaction = _TEXTREPORTPOST;
} else {
    include_once(dirname(__FILE__) . '/index.php');
    return;
}

$mxbnavigator->add(false, $miscaction);

if ($action == 'faq') {
    if ($faqstatus != 'on') {
        return mxbExitMessage(_FAQOFF, true);
    }

    $stars = '';
    $allranks = '';
    $query = sql_query("SELECT * FROM $table_ranks ORDER BY posts");
    while ($ranks = sql_fetch_object($query)) {
        for($i = 0; $i < $ranks->stars; $i++) {
            $stars .= "<img src=\"" . MXB_BASEMODIMG . "/star.gif\" alt=\"\"/>";
        }
        $allranks .= '
            <dt>' . $ranks->title . '</dt>
            <dd>' . $stars . '&nbsp;' . $ranks->posts . '&nbsp;' . _MEMPOSTS . '</dd>';
        $stars = '';
    }

?>
<h2><?php echo _TEXTFAQ ?></h2>
<div class="panel bgcolor3">
	<div class="inner"> 
			<a name="bbcode"></a>
			<div class="column1">
			    <h3><?php echo _TEXTBBCODE ?></h3>
				<dl class="faq">				
					<dd><?php echo _BBCODEINFO ?></dd>			
				</dl>
			</div>
			<div class="column2">
				<h3><?php echo _TEXTUSERRANKS ?></h3>				
					<dl><?php echo $allranks ?></dl>
			</div>			
	</div>
</div>
<?php
}

if ($action == 'online') {
?>
<h2><?php echo _WHOSONLINE ?></h2>
<div class="forumbg">
	<div class="inner">  
  	<table class="table1">
				<thead>
					<tr>
						<th class="name"><?php echo _TEXTUSERNAME?></th>
		            <?php	if ($eBoardUser['isadmin']) {
                        echo '
        		        <th class="infos">' . _TEXTIPADDRESS . '</th>';
                    }	?>						
						<th class="infos"><?php echo _TEXTTIME?></th>
						<th class="active"><?php echo _TEXTLOCATION?></th>
					</tr>
				</thead>
				<tbody>  
<?php
		$class = 'alternate-0';
    $query = sql_query("SELECT * FROM $table_whosonline ORDER BY `time` DESC");
    while ($online = sql_fetch_object($query)) {
        $onlinetime = date($timecode, (int)$online->time + ($timeoffset * 3600));

        $onlineusername = str_replace("xguest123", _TEXTGUEST1, $online->username);

        if ($online->username != "xguest123") {
            $online->username = mxb_link2profile($online->username, $onlineusername);
        } else {
            $online->username = $onlineusername;
        }

        ?>
        	<tr class="<?php echo $class ?>">
						<td class="name"><?php echo $online->username ?></td>
					<?php  if ($eBoardUser['isadmin']) {
                        echo '
                        <td class="infos">' . $online->ip . '</td>';
                    }	?>				
						<td class="infos"><?php echo $onlinetime ?></td>
						<td class="infos"><?php echo $online->location ?></td>
        				<?php $class = ($class == 'alternate-0') ? 'alternate-1' : 'alternate-0'; ?>
					</tr>
				<?php
    }
    ?>
				</tbody>    
   		</table>
	</div>
</div>
<?php
}

if ($action == "report") {
    if (mxbIsAnonymous()) {
        return mxbExitMessage(_TEXTNOACTION, false);
    }

    if ($reportpost != 'on') {
        return mxbExitMessage(_REPORTPOSTDISABLED, false);
    }
    // Hier wird gekuckt ob wir ueber einen Link kommen und jump ggf. definiert
    $jumper = mxbGetJumplink();

    if (empty($reportsubmit)) {

        ?>
        <h2><?php echo _TEXTREPORTPOST?></h2>        
<div class="panel">
	<div class="inner">
			<form method="post" name="input" action="<?php echo MXB_BM_MISC1 ?>action=report">
    		<input type="hidden" name="tid" value="<?php echo $tid?>" />
	    	<input type="hidden" name="fid" value="<?php echo $fid?>" />
<?php if ($pid) { ?>
				<input type="hidden" name="pid" value="<?php echo $pid?>">
<?php } ?>
<?php if ($linkstatus == 'on') { ?>
				<input type="hidden" name="jumplink" value="<?php echo $jumplink?>" />
				<input type="hidden" name="lid" value="<?php echo $lid?>" />
				<input type="hidden" name="linktype" value="thread" />
				<input type="hidden" name="linkstatus" value="on" />
<?php } ?>
			<dl>
				<dt><label for="reason"><?php echo _TEXTREASON?></label></dt>
				<dd><textarea id="reason" rows="15" cols="70" name="reason" class="inputbox"></textarea></dd>
				<fieldset class="submit-buttons">
					<input class="button1" type="submit" name="reportsubmit" value="<?php echo _TEXTREPORTPOST?>" />
				</fieldset>
			</dl>
			</form>
  </div>
</div>
<?php
    }

    if (!empty($reportsubmit)) {
        $posturl = trim(MX_HOME_URL, '/') . "/" . MXB_BM_VIEWTHREAD1 . "tid=$tid";
        if ($pid) {
            $posturl .= "#pid$pid";
        }
        $posturl = str_replace('&amp;', '&', $posturl);

        $message = _REPORTMESSAGE . "\n   $posturl \n\n" . _TEXTUSERNAME . " " . $eBoardUser['username'] . "\n" . _TEXTREASON . "\n $reason ";
        $message = stripslashes($message);

        $query = sql_query("SELECT moderator FROM $table_forums WHERE fid=" . intval($fid));
        $forum = sql_fetch_object($query);
        $mods = '';
        if (!empty($forum->moderator)) {
            $mods = preg_split('#\s*,\s*#', trim($forum->moderator, ', '));
            $mods = "OR username in ('" . implode('', '', $mods) . "')";
        }

        $query = sql_query("SELECT fm.username, u.email, u.uname,  u.user_stat
                FROM $table_members AS fm
                LEFT JOIN {$user_prefix}_users AS u
                ON fm.username = u.uname
                WHERE status='Administrator' OR  status='Super Moderator' $mods");
        while ($whilemember = sql_fetch_object($query)) {
            if (is_object($whilemember) && !empty($whilemember->username) && !empty($whilemember->email)) {
                if ($whilemember->status != 'Administrator' && (empty($whilemember->uname) || $whilemember->user_stat != 1)) {
                    // falls es den Moderator nicht mehr als User gibt >> einfach weiter...
                    continue;
                }
                $sendmess = _HELLO . ' ' . $whilemember->username . ",\n" . $message;
                mxMail($whilemember->email, _REPORTSUBJECT, $sendmess);
            }
        }
        echo mxbMessageScreen(_REPORTMSG);
        echo mxbRedirectScript(MXB_BM_VIEWTHREAD1 . 'tid=' . $tid . $jumper, 1250);
    }
}
include_once(MXB_BASEMODINCLUDE . 'footer.php');

?>
