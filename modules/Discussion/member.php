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
// nur diese Aktionen erlauben
empty($action) AND $action = 'viewpro';

switch (true) {
    case empty($action):
    case $action == 'member':
        $action = 'viewpro';
    case $action == 'viewpro':
        $caption = _TEXTVIEWPRO;
        break;
    case $action == 'editpro':
        $caption = _TEXTEDITPRO;
        break;
    default:
        include_once(dirname(__FILE__) . '/index.php');
        return;
}

switch (true) {
    case empty($_REQUEST['member']) && mxbIsAnonymous():
    case $action == 'editpro' && mxbIsAnonymous():
        return mxbExitMessage(_TEXTNOACTION, false);
    case empty($_REQUEST['member']):
        $member = $eBoardUser['username'];
        break;
    case isset($_GET['member']) && mxCheckNickname($_GET['member']) !== true:
        return mxbExitMessage(mxCheckNickname($_GET['member']), false);
    default:
        $member = substr($_REQUEST['member'], 0, 25);
        break;
}

$query = sql_query("SELECT fm.*, u.email as email, u.user_regtime as regdate, u.uname, u.user_lastvisit, u.user_lastmod, u.user_stat, u.user_sexus
                FROM $table_members AS fm
                LEFT JOIN {$user_prefix}_users AS u
                ON fm.username = u.uname
                WHERE username='" . mxAddSlashesForSQL($member) . "'");
$userinfo = sql_fetch_object($query);

if (!($userinfo)) {
    return mxbExitMessage(_BADNAME, false);
}

if ((empty($userinfo->uname) || $userinfo->user_stat != 1) && $userinfo->status != 'Administrator') {
    // wenn nicht vorhanden, alle Datenbestände dieses Users angleichen
    if (empty($userinfo->uname)) {
        mxbCleanUserdata($member);
    }
    $userinfo->username = '';
}

if (empty($userinfo->username)) {
    return mxbExitMessage(_BADNAME, false);
}

$mxbnavigator->add(false, $caption . ': ' . $userinfo->username);

switch (true) {
    case $action == 'viewpro': {
            // ***************************************************************************//
            // Voir le profil    (action=viewpro)                                         //
            // ***************************************************************************//
            $user_is_online = false;
            $user_lastmod = MXB_MODNAME;
            // den Status überprüfen, nur wenn nicht der User selbst,
            // denn dann ist das schon in der header.php geprüft
            if ($eBoardUser['username'] != $userinfo->username) {
                $userinfo->status = mxbGetRepairedStatus ($userinfo);
            }

            $past = time() - MX_SETINACTIVE_MINS ;
            $uinfo = mxGetUserDataFromUsername($member);

            $daysreg = (time() - $userinfo->regdate) / (24 * 60 * 60);
            $ppd = $userinfo->postnum / $daysreg;
            $ppd = round($ppd, 2);

            $userinfo->regdate = gmdate($dateformat, (int)$userinfo->regdate + ($timeoffset * 3600));

            if (!$user_is_online && $user_lastmod != MXB_MODNAME) {
                $onlinestatus = '<span style="color:maroon">' . _TEXTOFFLINE . '</span>';
            } else if ($user_is_online && $user_lastmod != MXB_MODNAME) {
                $onlinestatus = '<strong style="color:green">' . _TEXTONLINE . '</strong>';
            } else if ($whosonlinestatus == 'on') { // si le qui whosonline est activé
                $query = sql_query("SELECT * FROM $table_whosonline WHERE username='" . mxAddSlashesForSQL($member) . "'");
                $onlineinfo = sql_fetch_object($query);
                if (is_object($onlineinfo) && $onlineinfo->username == $member) {
                    $onlinestatus = '<strong style="color:darkolivegreen">' . _TEXTONLINE . '</strong>';
                    if ($advancedonlinestatus == 'on' || $eBoardUser['isadmin']) {
                        $onlinestatus .= "&nbsp;(&nbsp;" . $onlineinfo->location . "&nbsp;)";
                    }
                } else {
                    $onlinestatus = _TEXTOFFLINE;
                }
            }

            $lastvisitdate = gmdate($dateformat, (int)$userinfo->lastvisit + ($timeoffset * 3600));
            $lastvisittime = gmdate($timecode, (int)$userinfo->lastvisit + ($timeoffset * 3600));
            $lastmembervisittext = "$lastvisitdate " . _TEXTAT . " $lastvisittime";
            $query = sql_query("SELECT COUNT(pid) as nbsites FROM $table_posts");
            $row = sql_fetch_object($query);
            $posts = $row->nbsites;
            $query = sql_query("SELECT COUNT(tid) as nbsites FROM $table_threads");
            $row = sql_fetch_object($query);
            $threads = $row->nbsites;
            $posttot = $threads + $posts;
            if ($posttot == 0) {
                $percent = "0";
            } else {
                $percent = $userinfo->postnum * 100 / $posttot;
                $percent = round($percent, 2);
            }

            $totalmembertime = 0;
            if ($userinfo->totaltime != 0) {
                $totalmembertime = $userinfo->totaltime / 3600;
                $totalmembertime = round($totalmembertime, 1);
            }

            $query = sql_query("SELECT dateline, tid FROM $table_posts WHERE author='" . mxAddSlashesForSQL(substr($userinfo->username, 0, 25)) . "' ORDER BY dateline DESC LIMIT 0, 1");
            $lastrep = sql_fetch_object($query);

            $query = sql_query("SELECT dateline, subject, tid, fid FROM $table_threads WHERE author='" . mxAddSlashesForSQL(substr($userinfo->username, 0, 25)) . "' ORDER BY dateline DESC LIMIT 0, 1");
            $lasttop = sql_fetch_object($query);

            if (isset($lastrep->dateline) && isset($lasttop->dateline) && $lastrep->dateline > $lasttop->dateline) {
                $ltoptime = $lastrep->dateline;
                $query = sql_query("SELECT subject, fid FROM $table_threads WHERE tid='" . intval($lastrep->tid) . "'");
                $ltop = sql_fetch_object($query);
                $lasttopsub = $ltop->subject;
                $lttid = $lastrep->tid;
                $ltfid = $ltop->fid;
            } else {
                $ltoptime = (!empty($lasttop->dateline)) ? $lasttop->dateline : '';
                $lasttopsub = (!empty($lasttop->subject)) ? $lasttop->subject : '';
                $lttid = (!empty($lasttop->tid)) ? $lasttop->tid : '';
                $ltfid = (!empty($lasttop->fid)) ? $lasttop->fid : '';
            }

            $queryprivate = sql_query("SELECT private, userlist FROM $table_forums WHERE fid='" . intval($ltfid) . "'");
            $isprivate = sql_fetch_object($queryprivate);

            if (empty($isprivate->private) && empty($isprivate->userlist)) {
                $lasttopsub = stripslashes($lasttopsub);
                $lasttopdate = gmdate($dateformat, (int)$ltoptime + ($timeoffset * 3600));
                $lasttoptime = gmdate($timecode, (int)$ltoptime + ($timeoffset * 3600));
                $lasttopic = "<a href=\"" . MXB_BASEMOD . "viewthread&amp;tid=$lttid\">$lasttopsub</a><br />" . _TEXTLE . " $lasttopdate " . _TEXTAT . " $lasttoptime";
            } else {
                $lasttopic = _TEXTPRIV;
            }
            // Gestion des ranks (beta2)
            $showtitle = $userinfo->status;
            $stars = '';

            $rank = false;
            if (!empty($userinfo->postnum)) {
                $queryrank = sql_query("SELECT posts, title, stars FROM $table_ranks WHERE posts<='" . intval($userinfo->postnum) . "' ORDER BY posts DESC LIMIT 0,1");
                $rank = sql_fetch_object($queryrank);

                $showtitle = (empty($rank->title)) ? '' : $rank->title;
                $stars = "<img src=\"" . MXB_BASEMODIMG . "/star" . ((empty($rank->stars)) ? '' : $rank->stars) . ".gif\" alt=\"" . $rank->title . "\"><br/>";
            }
            if (!is_object($rank)) {
                $rank = new stdClass;
            }
            if (isOnStaff($userinfo->status)) {
                if ($adminstars == 'maxusersp3') {
                    $query = sql_query("SELECT MAX(stars) as maxstars FROM $table_ranks");
                    $row = sql_fetch_object($query);
                    $maxstars = $row->maxstars;

                    $staffstar3 = $maxstars + 3;
                    $staffstar2 = $maxstars + 2;
                    $staffstar1 = $maxstars + 1;

                    if (!isset($rank->title)) {
                        $rank->title = '';
                    }

                    if ($userinfo->status == 'Administrator') {
                        $showtitle = _TEXTADMIN;
                        $stars = "<img src=\"" . MXB_BASEMODIMG . "/star" . $staffstar3 . ".gif\" alt=\"" . $rank->title . "\"><br/>";
                    } elseif ($userinfo->status == 'Super Moderator') {
                        $showtitle = _TEXTSUPERMOD;
                        $stars = "<img src=\"" . MXB_BASEMODIMG . "/star" . $staffstar2 . ".gif\" alt=\"" . $rank->title . "\"><br/>";
                    } elseif ($userinfo->status == 'Moderator') {
                        $showtitle = _TEXTMOD;
                        $stars = "<img src=\"" . MXB_BASEMODIMG . "/star" . $staffstar1 . ".gif\" alt=\"" . $rank->title . "\"><br/>";
                    }
                } else { // sameasusers
                    if ($userinfo->status == 'Administrator') {
                        $showtitle = _TEXTADMIN;
                    } elseif ($userinfo->status == 'Super Moderator') {
                        $showtitle = _TEXTSUPERMOD;
                    } elseif ($userinfo->status == 'Moderator') {
                        $showtitle = _TEXTMOD;
                    }
                }
            }

            define('MXB_IS_USERPAGE', $userinfo->uname);

            ?>

<h2><?php echo _TEXTPROFOR ?> <?php echo $member ?></h2>   
<div class="panel bgcolor3">
 <div class="inner">
    <div class="column1">	
        <dl class="details">
		    <dt><?php echo _TEXTUSERNAME ?></dt>
    		<dd>
	    		<span style="color:brown; font-weight: bold;"><?php echo $userinfo->username ?></span>			
	    	</dd>
    		<dt><?php echo _TEXTSTATUS ?></dt>
            <dd><?php echo $userinfo->status ?></dd>
    		<dt><?php echo _TEXTSTATUSXF ?></dt>
            <dd><?php echo $showtitle, '&nbsp;', $stars ?></dd>
<?php
            if ($whosonlinestatus == 'on') {

                ?>
    		<dt><?php echo _ONSTATUS ?></dt>
            <dd><?php echo $onlinestatus ?></dd>

<?php
            }   ?>         
            
        </dl>
    </div>
    <div class="column2">
	    <dl class="left-box details">
            <dt><?php echo _TEXTREGISTERED ?></dt>
            <dd><?php echo $userinfo->regdate ?> (<?php echo $ppd ?> <?php echo _TEXTMESPERDAY ?>)</dd>
    		<dt><?php echo _TEXTPOSTS ?></dt>
            <dd><?php echo $userinfo->postnum ?> (<?php echo $percent ?>% <?php echo _TEXTOFTOTPOSTS ?>.)</dd>
    		<dt><?php echo _TEXTTOTALMEMBERTIME ?></dt>
            <dd><?php echo $totalmembertime, '&nbsp;', _TEXTHOURS ?></dd>
<?php
            if ($userinfo->lastvisit) {
                ?>
    		<dt><?php echo _LASTACTIVE ?></dt>
            <dd><?php echo $lastmembervisittext ?></dd>
<?php
            }

            if ($userinfo->postnum != 0) {

                ?>
    		<dt><?php echo _LASTPOSTIN ?></dt>
            <dd><?php echo $lasttopic ?></dd>
        <?php
            }

            ?>
    	</dl>
	</div>
  </div>
</div>
<?php

            break;
        }

    case $action == 'editpro' && empty($_POST['editsubmit']) : {
            // ***************************************************************************//
            // Editer le profil    (action=editpro)                                         //
            // ***************************************************************************//
            if (!$eBoardUser['isadmin'] && $member != $eBoardUser['username']) {
                return mxbExitMessage(_TEXTNOACTION, false);
            }

            $checked = '';
            $newschecked = '';
            $notifydeletechecked = '';
            $notifyeditchecked = '';
            $notifymechecked = '';
            $notifymecheckedadmin = '';
            $notifymecheckedmoderator = '';
            $notifypostchecked = '';
            $notifythreadchecked = '';
            $u2uchecked = '';

            if ($userinfo->newsletter == 'yes') {
                $newschecked = ' checked="checked"';
            }
            if ($userinfo->u2u == 'yes') {
                $u2uchecked = ' checked="checked"';
            }
            if ($userinfo->notifyme == "ymd" || $userinfo->notifyme == "yad" || $userinfo->notifyme == 'yes') {
                $notifymechecked = ' checked="checked"';
            }
            if ($userinfo->notifyme == "yad") {
                $notifymecheckedadmin = ' checked="checked"';
            } else {
                $notifymecheckedmoderator = ' checked="checked"';
            }
            if ($userinfo->notifythread == 'yes') {
                $notifythreadchecked = ' checked="checked"';
            }
            if ($userinfo->notifypost == 'yes') {
                $notifypostchecked = ' checked="checked"';
            }
            if ($userinfo->notifyedit == 'yes') {
                $notifyeditchecked = ' checked="checked"';
            }
            if ($userinfo->notifydelete == 'yes') {
                $notifydeletechecked = ' checked="checked"';
            }

            $x = array();
            for ($i = 10; $i <= 90; $i = $i + 10) {
                $x['k' . sprintf("%02d", $i)] = '';
            }
            extract($x);
            unset($x);
            if ($userinfo->keeplastvisit == 1) {
                $k10 = 'selected="selected" class="current"';
            } elseif ($userinfo->keeplastvisit == 2) {
                $k20 = 'selected="selected" class="current"';
            } elseif ($userinfo->keeplastvisit == 3) {
                $k30 = 'selected="selected" class="current"';
            } elseif ($userinfo->keeplastvisit == 4) {
                $k40 = 'selected="selected" class="current"';
            } elseif ($userinfo->keeplastvisit == 5) {
                $k50 = 'selected="selected" class="current"';
            } elseif ($userinfo->keeplastvisit == 6) {
                $k60 = 'selected="selected" class="current"';
            } elseif ($userinfo->keeplastvisit == 7) {
                $k60 = 'selected="selected" class="current"';
            } elseif ($userinfo->keeplastvisit == 8) {
                $k60 = 'selected="selected" class="current"';
            } elseif ($userinfo->keeplastvisit == 9) {
                $k60 = 'selected="selected" class="current"';
            } elseif ($userinfo->keeplastvisit == 10) {
                $k60 = 'selected="selected" class="current"';
            }

            if ($globaltimestatus != 'on') {
                if ($userinfo->timeoffset == "-12") {
                    $sn12 = 'selected="selected" class="current"';
                } elseif ($userinfo->timeoffset == "-11") {
                    $sn11 = 'selected="selected" class="current"';
                } elseif ($userinfo->timeoffset == "-10") {
                    $sn10 = 'selected="selected" class="current"';
                } elseif ($userinfo->timeoffset == "-9") {
                    $sn9 = 'selected="selected" class="current"';
                } elseif ($userinfo->timeoffset == "-8") {
                    $sn8 = 'selected="selected" class="current"';
                } elseif ($userinfo->timeoffset == "-7") {
                    $sn7 = 'selected="selected" class="current"';
                } elseif ($userinfo->timeoffset == "-6") {
                    $sn6 = 'selected="selected" class="current"';
                } elseif ($userinfo->timeoffset == "-5") {
                    $sn5 = 'selected="selected" class="current"';
                } elseif ($userinfo->timeoffset == "-4") {
                    $sn8 = 'selected="selected" class="current"';
                } elseif ($userinfo->timeoffset == "-3.5") {
                    $sn35 = 'selected="selected" class="current"';
                } elseif ($userinfo->timeoffset == "-3") {
                    $sn3 = 'selected="selected" class="current"';
                } elseif ($userinfo->timeoffset == "-2") {
                    $sn2 = 'selected="selected" class="current"';
                } elseif ($userinfo->timeoffset == "-1") {
                    $sn1 = 'selected="selected" class="current"';
                } elseif ($userinfo->timeoffset == "0") {
                    $s0 = 'selected="selected" class="current"';
                } elseif ($userinfo->timeoffset == "1") {
                    $sp1 = 'selected="selected" class="current"';
                } elseif ($userinfo->timeoffset == "2") {
                    $sp2 = 'selected="selected" class="current"';
                } elseif ($userinfo->timeoffset == "3") {
                    $sp3 = 'selected="selected" class="current"';
                } elseif ($userinfo->timeoffset == "3.5") {
                    $sp35 = 'selected="selected" class="current"';
                } elseif ($userinfo->timeoffset == "4") {
                    $sp4 = 'selected="selected" class="current"';
                } elseif ($userinfo->timeoffset == "4.5") {
                    $sp45 = 'selected="selected" class="current"';
                } elseif ($userinfo->timeoffset == "5") {
                    $sp5 = 'selected="selected" class="current"';
                } elseif ($userinfo->timeoffset == "5.5") {
                    $sp55 = 'selected="selected" class="current"';
                } elseif ($userinfo->timeoffset == "6") {
                    $sp6 = 'selected="selected" class="current"';
                } elseif ($userinfo->timeoffset == "7") {
                    $sp7 = 'selected="selected" class="current"';
                } elseif ($userinfo->timeoffset == "8") {
                    $sp8 = 'selected="selected" class="current"';
                } elseif ($userinfo->timeoffset == "9") {
                    $sp9 = 'selected="selected" class="current"';
                } elseif ($userinfo->timeoffset == "9.5") {
                    $sp95 = 'selected="selected" class="current"';
                } elseif ($userinfo->timeoffset == "10") {
                    $sp10 = 'selected="selected" class="current"';
                } elseif ($userinfo->timeoffset == "11") {
                    $sp11 = 'selected="selected" class="current"';
                } elseif ($userinfo->timeoffset == "12") {
                    $sp12 = 'selected="selected" class="current"';
                }
            }

            $query = sql_query("SELECT name FROM $table_themes ORDER BY name");
            $themeselect = "<select name=\"XFthememem\">\n";
            $themeselect .= '<option value=""' . ((empty($userinfo->theme)) ? ' selected="selected" class="current"' : '') . '>- ' . _TEXTDEFAULT . "</option>\n";
            while ($obj_theme = sql_fetch_object($query)) {
                $themeselect .= '<option value="' . $obj_theme->name . '"' . (($obj_theme->name == $userinfo->theme) ? ' selected="selected" class="current"' : '') . '>' . $obj_theme->name . "</option>\n";
            }
            $themeselect .= "</select>";

            $langlist = mxbGetAvailableLanguages();
            $langlist = array_merge(array('- ' . _TEXTDEFAULT => ''), $langlist);
            $list = '';
            foreach ($langlist as $key => $value) {
                $sel = ($value == $userinfo->langfile) ? ' selected="selected" class="current"' : ''; // Standard vorselektieren
                $list .= '<option value="' . $value . '"' . $sel . '>' . $key . '</option>';
            }

            $langfileselect = '<select name="langfilenew">' . $list . '</select>';

            $check24 = '';
            $check12 = '';
            if ($userinfo->timeformat == '24') {
                $check24 = ' checked="checked"';
            } else {
                $check12 = ' checked="checked"';
            }

            define('MXB_IS_USERPAGE', $userinfo->uname);

            ?>

<h2><?php echo _TEXTOPTIONS ?>&nbsp;<?php echo $member ?></h2>
	<div class="panel bgcolor3">
		<div class="inner">
			<div style="width: 100%;"> 
			
				<form method="post" name="reg" action="<?php echo MXB_BM_MEMBER1 ?>action=editpro">															
					<fieldset>
					<!--
						<dl>
							<dt>
								<label><?php echo _TEXTTHEME ?></label>
							</dt>
							<dd>
								<label><?php echo $themeselect ?></label>
							</dd>
						</dl>
						-->
						<dl>
							<dt>
								<label><?php echo _TEXTLANGUAGE ?></label>
							</dt>
							<dd>
								<label><?php echo $langfileselect ?></label>
							</dd>
						</dl>						
						<dl>
							<dt>
								<label><?php echo _TEXTTPP ?></label>
							</dt>
							<dd>
								<label><input type="text" name="tppnew" size="4" value="<?php echo $userinfo->tpp ?>" /></label>
							</dd>
						</dl>							
						<dl>
							<dt>
								<label><?php echo _TEXTPPP ?></label>
							</dt>
							<dd>
								<label><input type="text" name="pppnew" size="4" value="<?php echo $userinfo->ppp ?>" /></label>
							</dd>
						</dl>		
						<dl>
							<dt>
								<label><?php echo _TEXTTIMEFORMAT ?></label>
							</dt>
							<dd>
								<label><input type="radio" value="24" name="timeformatnew" <?php echo $check24 ?> /><?php echo _TEXT24HOUR ?></label>
								<label><input type="radio" value="12" name="timeformatnew" <?php echo $check12?> /><?php echo _TEXT12HOUR ?></label>
<?php
            if ($globaltimestatus != 'on') {
                $currdate = gmdate($timecode);
                eval(_EVALOFFSET);

                ?>
<label>
<select name="timeoffset1" size="1">
    <option value="-12" <?php echo (($userinfo->timeoffset == -12) ? ' selected="selected" class="current"' : '') ?>>-12:00</option>
    <option value="-11" <?php echo (($userinfo->timeoffset == -11) ? ' selected="selected" class="current"' : '') ?>>-11:00</option>
    <option value="-10" <?php echo (($userinfo->timeoffset == -10) ? ' selected="selected" class="current"' : '') ?>>-10:00</option>
    <option value="-9" <?php echo (($userinfo->timeoffset == -9) ? ' selected="selected" class="current"' : '') ?>>-9:00</option>
    <option value="-8" <?php echo (($userinfo->timeoffset == -8) ? ' selected="selected" class="current"' : '') ?>>-8:00</option>
    <option value="-7" <?php echo (($userinfo->timeoffset == -7) ? ' selected="selected" class="current"' : '') ?>>-7:00</option>
    <option value="-6" <?php echo (($userinfo->timeoffset == -6) ? ' selected="selected" class="current"' : '') ?>>-6:00</option>
    <option value="-5" <?php echo (($userinfo->timeoffset == -5) ? ' selected="selected" class="current"' : '') ?>>-5:00</option>
    <option value="-4" <?php echo (($userinfo->timeoffset == -4) ? ' selected="selected" class="current"' : '') ?>>-4:00</option>
    <option value="-3.5" <?php echo (($userinfo->timeoffset == -3.5) ? ' selected="selected" class="current"' : '') ?>>-3:30</option>
    <option value="-3" <?php echo (($userinfo->timeoffset == -3) ? ' selected="selected" class="current"' : '') ?>>-3:00</option>
    <option value="-2" <?php echo (($userinfo->timeoffset == -2) ? ' selected="selected" class="current"' : '') ?>>-2:00</option>
    <option value="-1" <?php echo (($userinfo->timeoffset == -1) ? ' selected="selected" class="current"' : '') ?>>-1:00</option>
    <option value="0" <?php echo ((empty($userinfo->timeoffset)) ? ' selected="selected" class="current"' : '') ?>>0</option>
    <option value="1" <?php echo (($userinfo->timeoffset == 1) ? ' selected="selected" class="current"' : '') ?>>+1:00</option>
    <option value="2" <?php echo (($userinfo->timeoffset == 2) ? ' selected="selected" class="current"' : '') ?>>+2:00</option>
    <option value="3" <?php echo (($userinfo->timeoffset == 3) ? ' selected="selected" class="current"' : '') ?>>+3:00</option>
    <option value="3.5" <?php echo (($userinfo->timeoffset == 3.5) ? ' selected="selected" class="current"' : '') ?>>+3:30</option>
    <option value="4" <?php echo (($userinfo->timeoffset == 4) ? ' selected="selected" class="current"' : '') ?>>+4:00</option>
    <option value="4.5" <?php echo (($userinfo->timeoffset == 4.5) ? ' selected="selected" class="current"' : '') ?>>+4:30</option>
    <option value="5" <?php echo (($userinfo->timeoffset == 5) ? ' selected="selected" class="current"' : '') ?>>+5:00</option>
    <option value="5.5" <?php echo (($userinfo->timeoffset == 5.5) ? ' selected="selected" class="current"' : '') ?>>+5:30</option>
    <option value="6" <?php echo (($userinfo->timeoffset == 6) ? ' selected="selected" class="current"' : '') ?>>+6:00</option>
    <option value="7" <?php echo (($userinfo->timeoffset == 7) ? ' selected="selected" class="current"' : '') ?>>+7:00</option>
    <option value="8" <?php echo (($userinfo->timeoffset == 8) ? ' selected="selected" class="current"' : '') ?>>+8:00</option>
    <option value="9" <?php echo (($userinfo->timeoffset == 9) ? ' selected="selected" class="current"' : '') ?>>+9:00</option>
    <option value="9.5" <?php echo (($userinfo->timeoffset == 9.5) ? ' selected="selected" class="current"' : '') ?>>+9:30</option>
    <option value="10" <?php echo (($userinfo->timeoffset == 10) ? ' selected="selected" class="current"' : '') ?>>+10:00</option>
    <option value="11" <?php echo (($userinfo->timeoffset == 11) ? ' selected="selected" class="current"' : '') ?>>+11:00</option>
    <option value="12" <?php echo (($userinfo->timeoffset == 12) ? ' selected="selected" class="current"' : '') ?>>+12:00</option>
</select>
</label>
<?php
                echo _TEXTOFFSET;
            } else {

                ?>
    <input type="hidden" name="timeoffset1" value="0" />
<?php
            }

            ?>
							</dd>
						</dl>	

						<dl>
							<dt>
								<label><?php echo _DATEFORMAT ?></label>
							</dt>
							<dd>
								<label><input type="text" name="dateformatnew" size="30" value="<?php echo $userinfo->dateformat ?>" /></label>
							</dd>
						</dl>
						<dl>
							<dt>
								<label><?php echo _TEXTOPTIONS ?></label>
							</dt>
							<dd>
							  <label><input type="checkbox" name="newsletter" value="yes" <?php echo $newschecked ?> /> <?php echo _TEXTGETNEWS ?></label>
<?php
            if ($allowu2u == 'on') {
                if (mxModuleAllowed('Private_Messages')) {

                    ?>
    							<br /><label><input type="checkbox" name="u2u" value="yes" <?php echo $u2uchecked ?> /> <?php echo _TEXTU2U ?></label>
    <?php
                } else {

                    ?>
							    <input type="hidden" name="u2u" value="no" />
    <?php
                }
            }

            ?>
<br /><label>
<select name="keeplastvisit" size="1">
<option value="1" <?php echo $k10 ?>>1:00</option>
<option value="2" <?php echo $k20 ?>>2:00</option>
<option value="3" <?php echo $k30 ?>>3:00</option>
<option value="4" <?php echo $k40 ?>>4:00</option>
<option value="5" <?php echo $k50 ?>>5:00</option>
<option value="6" <?php echo $k60 ?>>6:00</option>
<option value="7" <?php echo $k70 ?>>7:00</option>
<option value="8" <?php echo $k80 ?>>8:00</option>
<option value="9" <?php echo $k90 ?>>9:00</option>
</select>
<?php echo _TEXTKEEPLASTVISIT ?>
</label>
							
<?php

            if (isOnStaff($userinfo->status)) {

                ?>

								<br /><label><input type="checkbox" name="notifyme" value="ymd" <?php echo $notifymechecked ?>/> <?php echo _TEXTNOTIFYME ?></label>
						</dl>

<?php

                if ($notifymechecked) {
                    if ($userinfo->status == 'Administrator') {

                        ?>
						<dl>
							<dt>
								<label><?php echo _TEXTNOTIFYTYPE ?></label>
							</dt>
							<dd>
								<label><input type="radio" value="yad" name="notifymeadmin" <?php echo $notifymecheckedadmin ?>/><?php echo _TEXTADMINISTRATORMAIL ?></label>
								<label><input type="radio" value="" name="notifymeadmin" <?php echo $notifymecheckedmoderator ?>/><?php echo _TEXTMODERATORMAIL ?></label>
                                
<?php

                    }

                    if ($moderatormailonthread = "on" || $eBoardUser['isadmin']) {

                        ?>
<br /><label><input type="checkbox" name="notifythread" value="yes" <?php echo $notifythreadchecked ?> > <?php echo _TEXTNOTIFYTHREAD ?></label>
<?php
                    }

                    if ($moderatormailonpost = "on" || $eBoardUser['isadmin']) {

                        ?>
<br /><label><input type="checkbox" name="notifypost" value="yes" <?php echo $notifypostchecked ?> /> <?php echo _TEXTNOTIFYPOST ?></label>
<?php
                    }

                    if ($moderatormailonedit = "on" || $eBoardUser['isadmin']) {

                        ?>
<br /><label><input type="checkbox" name="notifyedit" value="yes" <?php echo $notifyeditchecked ?> > <?php echo _TEXTNOTIFYEDIT ?></label>
<?php
                    }

                    if ($moderatormailondelete = "on" || $eBoardUser['isadmin']) {

                        ?>
<br /><label><input type="checkbox" name="notifydelete" value="yes" <?php echo $notifydeletechecked ?> /> <?php echo _TEXTNOTIFYDELETE ?></label>
<?php
                    }

                    ?>
 							</dd>                   
						</dl>
<?php
                }
            }

            ?>


<input type="hidden" name="member" value="<?php echo $member ?>"/>
<input type="submit" class="button2" name="editsubmit" value="<?php echo _TEXTSUBMITCHANGES ?>" />
</fieldset>
				</form>
			</div>
	</div>
</div>

<?php

            break;
        }

    case $action == 'editpro' && !empty($_POST['editsubmit']) : {
            // ***************************************************************************//
            // Submit Edit du profil    (action=editsubmit)                                 //
            // ***************************************************************************//
            if (empty($newsletter) || $newsletter != "yes") {
                $newsletter = "no";
            }
            if (empty($u2u) || $u2u != "yes") {
                $u2u = "no";
            }
            if (empty($keeplastvisit) || $keeplastvisit < 1) {
                $keeplastvisit = 1;
            }

            if (empty($notifyme) || ($notifyme != "ymd" && $notifyme != "yad")) {
                $notifyme = "no";
            }
            if (empty($notifythread) || $notifythread != "yes") {
                $notifythread = "no";
            }
            if (empty($notifypost) || $notifypost != "yes") {
                $notifypost = "no";
            }
            if (empty($notifyedit) || $notifyedit != "yes") {
                $notifyedit = "no";
            }
            if (empty($notifydelete) || $notifydelete != "yes") {
                $notifydelete = "no";
            }

            if (isset($notifymeadmin) && $notifymeadmin == "yad" && $notifyme == "ymd") {
                $notifyme = "yad";
            }

            $tppnew = (!is_numeric($tppnew) || $tppnew > $max_ppp || $tppnew < 0) ? $topicperpage : $tppnew;
            $pppnew = (!is_numeric($pppnew) || $pppnew > $max_ppp || $pppnew < 0) ? $postperpage : $pppnew;
            sql_query("UPDATE $table_members
            SET
                timeoffset='" . mxAddSlashesForSQL($timeoffset1) . "',
                theme='" . mxAddSlashesForSQL($XFthememem) . "',
                langfile='" . mxAddSlashesForSQL($langfilenew) . "',
                tpp='" . intval($tppnew) . "',
                ppp='" . intval($pppnew) . "',
                newsletter='" . mxAddSlashesForSQL($newsletter) . "',
                timeformat='" . mxAddSlashesForSQL($timeformatnew) . "',
                dateformat='" . mxAddSlashesForSQL($dateformatnew) . "',
                u2u='" . mxAddSlashesForSQL($u2u) . "',
                notifyme='" . mxAddSlashesForSQL($notifyme) . "',
                notifythread='" . mxAddSlashesForSQL($notifythread) . "',
                notifypost='" . mxAddSlashesForSQL($notifypost) . "',
                notifyedit='" . mxAddSlashesForSQL($notifyedit) . "',
                notifydelete='" . mxAddSlashesForSQL($notifydelete) . "',
                keeplastvisit='" . $keeplastvisit . "'
             WHERE username='" . mxAddSlashesForSQL($member) . "'");

            define('MXB_IS_USERPAGE', $userinfo->uname);

            echo mxbMessageScreen(_EDITEDPRO);
            echo mxbRedirectScript(MXB_BM_MEMBER1 . 'action=editpro&member=' . $member, 1250);

            break;
        }
}

include_once(MXB_BASEMODINCLUDE . 'footer.php');

?>
