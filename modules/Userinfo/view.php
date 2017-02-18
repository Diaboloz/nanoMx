<?php
/**
 * This file is part of
 * pragmaMx - Web Content Management System.
 * Copyright by pragmaMx Developer Team - http://www.pragmamx.org
 *
 * pragmaMx is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * $Revision: 6 $
 * $Author: PragmaMx $
 * $Date: 2015-07-08 09:07:06 +0200 (Mi, 08. Jul 2015) $
 */

defined('mxMainFileLoaded') or die('access denied');
defined('mxYALoaded') or die('access denied');

/**
 * zeigt die Daten eines angemeldetet Users an
 */
function viewuserinfo($uinfo)
{
    global $prefix, $user_prefix;
    global $istheuser, $privmsgactive, $gbactiv, $showall; // für Unterfunktionen in mx_userfunctions_options.php

    mxGetLangfile('Your_Account');
    $userconfig = load_class('Userconfig');

    $istheuser = $uinfo['current'];
    $admin = mxGetAdminPref('radminuser');
    $showall = ($istheuser || $admin) ? true : false;
    $privmsgactive = (mxModuleAllowed('Private_Messages')) ? true : false; # feststellen ob pm-modul aktiv ist
    $gbactiv = (mxModuleAllowed('UserGuest')) ? true : false; # feststellen ob gaestebuch vorhanden
    $uinfo['url'] = mxCutHTTP($uinfo['url']);

    /* Signatur */
    if (!empty($uinfo['user_sig'])) {
        if (file_exists(PMX_MODULES_DIR . DS . 'Private_Messages' . DS . 'bbfunctions.php')) {
            include_once(PMX_MODULES_DIR . DS . 'Private_Messages' . DS . 'bbfunctions.php');
            $uinfo['user_sig'] = msg_smile(msg_bbencode(make_clickable(pmxSigBbCode($uinfo['user_sig']))));
        }
        $uinfo['user_sig'] = mxNL2BR(mxPrepareToDisplay($uinfo['user_sig']));
    }

    /* aktuelles Foto ermitteln */
    $pici = load_class('Userpic', $uinfo);
    $photo = $pici->getHtml('normal', array('class' => 'align-center'));
    $photo_uploaded = $pici->is_uploaded();

    /* Private Nachrichten */
    if ($privmsgactive && $istheuser) { // falls pm-modul aktiv ist
        $uinfo['countpm'] = 0;
        $uinfo['countpmread'] = 0;
        $uinfo['countpmunread'] = 0;
        $qry = "SELECT read_msg, Count(msg_id)
                FROM ${prefix}_priv_msgs
                WHERE to_userid='" . intval($uinfo['uid']) . "'
                GROUP BY read_msg;";
        $result = sql_query($qry);
        while (list($read_msg, $nums) = sql_fetch_row($result)) {
            if ($read_msg == 0) { // wenn angemeldeter User
                $uinfo['countpmunread'] = $nums; # Anzahl ungelesene ermitteln
            } else {
                $uinfo['countpmread'] = $nums; # Anzahl gelesener pm's ermitteln
            }
        }
        $uinfo['countpm'] = $uinfo['countpmread'] + $uinfo['countpmunread'];
        $uinfo['contpm'] = '
			<table cellspacing="0" cellpadding="0">
				<tr>
					<td nowrap="nowrap">
						<a href="modules.php?name=Private_Messages"><b>' . $uinfo['countpm'] . '</b></a>&nbsp;
						<a href="modules.php?name=Private_Messages">' . _YA_BWOPMSGALL . '</a>';
        if ($uinfo['countpmunread']) {
            $uinfo['contpm'] .= '
					</td>
					<td>,&nbsp;</td>
					<td nowrap="nowrap">
						<a href="modules.php?name=Private_Messages">
							<marquee behavior="alternate" direction="left" width="100" hspace="0" vspace="0" loop="">
								<b>' . $uinfo['countpmunread'] . '/b>&nbsp;<b>' . _YA_BWOPMSGUNREAD . '</b>
							</marquee>
						</a>';
        }
        $uinfo['contpm'] .= '
					</td>
					<td>&nbsp;&nbsp;*</td>
				</tr>
			</table>';
    }

    /* Online oder Offline */
    if ($uinfo['user_online']) {
        $uinfo['online'] = '<span style="color:green;">' . _ONLINE . '</span>';
        $uinfo['lastonline'] = '';
    } else {
        $uinfo['online'] = '<span style="color:red;">' . _OFFLINE . '</span>';
        $uinfo['lastonline'] = mx_strftime(_XDATESTRING, $uinfo['user_lastvisit']);
    }

    /* Userpunkte */
    $uinfo['hasuserpoints'] = '';
    if ($userconfig->useuserpoints) {
        include_once(PMX_MODULES_DIR . DS . 'Your_Account' . DS . 'userpoints.php');
        $points = new ya_userpoints($uinfo);
        $uinfo['hasuserpoints'] = $points->get();
    }
    /* Geburtstag */
    if ($uinfo['user_bday'] && $timestamp = strtotime($uinfo['user_bday'])) {
        $uinfo['user_bday'] = mx_strftime(_SHORTDATESTRING, $timestamp);
    }

    /* Usergaestebuch */
    $uinfo['gbnewentries'] = 0;
    if ($gbactiv) { // falls gaestebuch vorhanden
        $qry = "SELECT Count(gid)
                FROM ${prefix}_userguest
                WHERE touserid='" . intval($uinfo['uid']) . "' and dummy=0;";
        $result = sql_query($qry);
        list($uinfo['gbnewentries']) = sql_fetch_row($result);
    }

    /* Benutzergruppe */
    switch (true) {
        case $admin:
        case $userconfig->showusergroup == 1 && $istheuser:
        case $userconfig->showusergroup == 2 && MX_IS_USER:
        case $userconfig->showusergroup == 3:
            $result = sql_query("SELECT access_title
                            FROM ${prefix}_groups_access
                            WHERE access_id = " . intval($uinfo['user_ingroup']));
            list($uinfo['usergroup']) = sql_fetch_row($result);
    }

    /* ////////////////// Ausgabe der Daten ////////////////// */

    ob_start();

    ?>

<script type="text/javascript" charset="utf-8">
  $(document).ready(function() {

    $('#upic-upload-delete').click(function() {
        if (!confirm('<?php echo addslashes(_UPIC_SUREDELETE) ?>')) {
            return false;
        }
        $.get('modules.php?name=Your_Account&file=upicajaxupload&uid=<?php echo $uinfo['uid'] ?>&delete', function(text){
          if ('<?php echo addslashes(_UPIC_DELETED) ?>' == text) {
            $('.view-narrow a').html(text);
            $('#upic-upload-delete').hide();
          } else {
            alert(text);
          }
        });
        return false;
    });

<?php if ($photo_uploaded) {

        ?>
    $('#upic-upload-delete').show();
<?php }

    ?>

  });

  function avatarlist() {
    window.open('modules.php?name=Your_Account&op=avatarlist', 'avatarlist', 'width=640, height=480, left=10, top=10, scrollbars=yes, resizable=yes, toolbar=no, location=no, status=no, menubar=no');
    return false;
  }

</script>
<?php
    pmxHeader::add_script_code(ob_get_clean());
    ob_start();

    if (function_exists('viewuserinfo_option_1')) {
        viewuserinfo_option_1($uinfo);
    } else {
        if (!empty($uinfo['name'])) {
            $items[] = array(_UREALNAME, mxPrepareToDisplay($uinfo['name']));
        }
        if (!empty($uinfo['user_sexus'])) {
            $items[] = array(_YA_USEXUS, vkpGetSexusString($uinfo['user_sexus']));
        }
        if (!empty($uinfo['user_bday'])) {
            $items[] = array(_YA_UBDAY, $uinfo['user_bday'] . ' (' . $uinfo['user_age'] . '&nbsp;' . _YEARS . ')');
        }
        if ($uinfo['user_viewemail']) {
            $items[] = array(_UREALEMAIL, '<a href="mailto:' . mxPrepareToDisplay($uinfo['email']) . '">' . mxPrepareToDisplay($uinfo['email']) . '</a>');
        } else if ($showall) {
            $items[] = array(_UREALEMAIL, '<a href="mailto:' . mxPrepareToDisplay($uinfo['email']) . '">' . mxPrepareToDisplay($uinfo['email']) . '</a>', 1);
        }
        if (!empty($uinfo['url'])) {
            $items[] = array(_WEBSITE, '<a href="' . mxPrepareToDisplay($uinfo['url']) . '" target="_blank">' . mxPrepareToDisplay($uinfo['url']) . '</a>');
        }
        if (!empty($uinfo['user_icq'])) {
            $items[] = array(_ICQ, mxPrepareToDisplay($uinfo['user_icq']));
        }
        if (!empty($uinfo['user_aim'])) {
            $items[] = array(_AIM, mxPrepareToDisplay($uinfo['user_aim']));
        }
        if (!empty($uinfo['user_yim'])) {
            $items[] = array(_YIM, mxPrepareToDisplay($uinfo['user_yim']));
        }
        if (!empty($uinfo['user_msnm'])) {
            $items[] = array(_MSNM, mxPrepareToDisplay($uinfo['user_msnm']));
        }
        if (!empty($uinfo['user_from'])) {
            $items[] = array(_LOCATION, mxPrepareToDisplay($uinfo['user_from']));
        }
        if (!empty($uinfo['user_occ'])) {
            $items[] = array(_OCCUPATION, mxPrepareToDisplay($uinfo['user_occ']));
        }
        if (!empty($uinfo['user_intrest'])) {
            $items[] = array(_INTERESTS, mxPrepareToDisplay($uinfo['user_intrest']));
        }
 /*       if (!empty($uinfo['user_sig'])) {
            $items[] = array(_SIGNATURE, $uinfo['user_sig']);
        }
        if (!empty($uinfo['bio'])) {
            $items[] = array(_EXTRAINFO, mxNL2BR(mxPrepareToDisplay($uinfo['bio'])));
        }*/
        if ($uinfo['hasuserpoints']) {
            $items[] = array(_GRANKS, $uinfo['hasuserpoints']);
        }
        if ($privmsgactive && $istheuser) {
            $items[] = array(_YA_BWOPMSG, $uinfo['contpm']);
        }
        if ($gbactiv && $uinfo['gbnewentries'] && $istheuser) { // falls gaestebuch vorhanden und der User selbst
            $items[] = array(_GUESTBOOKVIEW, '<b> ' . $uinfo['gbnewentries'] . '</b> ' . _YA_BWOPMSGUNREAD, 1);
        }
        $items[] = array(_USERSTATUS, $uinfo['online']);
        if (!empty($uinfo['lastonline'])) {
            $items[] = array(_YA_LASTONLINE, $uinfo['lastonline']);
        }
        if (!empty($uinfo['usergroup'])) {
            $items[] = array(_YA_INGROUP, $uinfo['usergroup']);
        }
        if (!empty($uinfo['user_regtime'])) {
            $items[] = array(_DATEREGISTERED, mx_strftime(_SHORTDATESTRING, $uinfo['user_regtime']));
        }

        ?>
<div class="tabs-panel">
<div class="mx-g">
    <div class="mx-u-6 mx-u-md-6-24">
        <div class="profil-photo">
    	   <h2 class="mbn"><?php if (!empty($uinfo['uname'])) { echo mxPrepareToDisplay($uinfo['uname']); } ?></h2>
        <?php if ($photo_uploaded) { ?>
            <div class="view-narrow">
                <?php echo $photo ?>
            </div>
        <?php } ?>
        <?php if ($photo_uploaded) { ?>
            <div class="clear"></div>
        <?php } ?>

                    <div class="social clearfix">
                        <ul>
                            <li><a href=""><?php echo mxCreateImage("modules/Your_Account/images/facebook.png", '' , 0, 'title="facebook"'); ?></a></li>
                            <li><a href=""><?php echo mxCreateImage("modules/Your_Account/images/twitter.png", '' , 0, 'title="twitter"'); ?></a></li>
                            <li><a href=""><?php echo mxCreateImage("modules/Your_Account/images/linkedin.png", '' , 0, 'title="linkedin"'); ?></a></li>
                            <li><a href=""><?php echo mxCreateImage("modules/Your_Account/images/google-plus.png", '' , 0, 'title="google-plus"'); ?></a></li>
                            <li><a href=""><?php echo mxCreateImage("modules/Your_Account/images/rss.png", '' , 0, 'title="rss"'); ?></a></li>
                        </ul>
                    </div>
        <?php if ($istheuser) {
            echo '       
                <p><a href="modules.php?name=Your_Account&amp;op=logout" class="mx-button mx-button-primary">' . _LOGOUTEXIT . '</a></p>';
        }
             if ($admin) {
            echo '
                <p><a href="' . adminUrl('users', 'modify', 'chng_uid=' . $uinfo["uid"]) . '" class="mx-button mx-button-primary">' . _YA_EDITUSER . '</a></p>';
        } ?>
        </div>
    </div>    	
    <?php
    if (!empty($uinfo['bio']) OR !empty($uinfo['user_sig'])) {
        echo '
         <div class="mx-u-10 mx-u-md-10-24">';
    }

    	if (!empty($uinfo['bio'])) {
    	 	echo '
    	 		<h4 class="resume">' . _EXTRAINFO . '</h4>
            	<p class="pls prs"> '. mxNL2BR(mxPrepareToDisplay($uinfo['bio'])) .'</p>';
        }?>
     <?php
    	 if (!empty($uinfo['user_sig'])) {
    	 	echo '
    	 		<h4 class="resume">' . _SIGNATURE . ':</h4>
            	<p class="pls prs">' . $uinfo['user_sig'] . '</p>';
        }
     if (!empty($uinfo['bio']) OR !empty($uinfo['user_sig'])) {
        echo '
         </div>';
    } ?>      
    <div class="mx-u-8 mx-u-md-8-24">
        <ul class="profil-items">
        <?php foreach ($items as $item) { ?>
            <li>
                <h4><?php echo $item[0] ?></h4>
                <div><?php echo $item[1] . ((empty($item[2])) ? '' : ' *') ?></div>
            </li>
        <?php } ?>
        </ul>
        <?php if ($istheuser) {
            echo '       
                <p class="small fr">* ' . _YA_ONLYYOUSEE . '!</p>';
        } ?>

    </div>
</div>
</div>



<?php    }

    if (function_exists('viewuserinfo_option_2')) {
        viewuserinfo_option_2($uinfo);
    } else {
        // private Nachrichten
        if ($privmsgactive) {
            if (!$istheuser) {
                $xuname = (MX_IS_USER || $admin) ? $uinfo['uname'] : "";
                echo '
				<div align="center"><br />
					<form action="modules.php?name=Private_Messages" method="post">
						<input type="hidden" name="name" value="Private_Messages" />
						'. _USENDPRIVATEMSG . '
						<input type="text" name="to_user" size="20" maxlength="25" value="' . $xuname . '" />
						<input type="hidden" name="op" value="send_to" />
						<input type="submit" name="submita" value="' . _SUBMIT . '" />
					</form>
				</div>';
            }
        }
        // Listings
        $result1 = sql_query("SELECT c.tid, c.sid, c.subject, c.comment
            FROM ${prefix}_comments AS c
            INNER JOIN ${prefix}_stories AS s
            ON c.sid = s.sid
            WHERE c.uid='" . intval($uinfo['uid']) . "'
            ORDER BY c.tid DESC
            LIMIT 0,10");
        $view1 = sql_num_rows($result1);

        $result2 = sql_query("SELECT sid, title
            FROM ${prefix}_stories
            WHERE informant='" . mxAddSlashesForSQL($uinfo['uname']) . "'
            AND `time` <= now()
            ORDER BY time DESC
            LIMIT 0,10");
        $view2 = sql_num_rows($result2);

        if ($view1 || $view2) {
            echo '
                <div class="mx-g">';
            if ($view2) {
                echo '  
                    <div class="mx-u-1-2 pam">
						<h4>' . _LAST10SUBMISSIONS . ' ' . $uinfo['uname'] . '</h4>
						<ul>';
                while (list($sid, $title) = sql_fetch_row($result2)) {
                    echo '
						  <li>
						      	<a href="modules.php?name=News&amp;file=article&amp;sid=' . $sid . '">' . $title . '</a>
						  </li>';
                }
                echo '
						</ul>
					</div>';
            }
            if ($view1) {
                echo '
                    <div class="mx-u-1-2 pam">
					   <h4>' . _LAST10COMMENTS . ' ' . $uinfo['uname'] . '</h4>
				        <ul>';
                while (list($tid, $sid, $subject, $comment) = sql_fetch_row($result1)) {
                    $subject = strip_tags($subject);
                    $subject = (empty($subject)) ? mxCutString(strip_tags($comment), 50) : $subject;
                    if ($subject) {
                        echo '
							<li><a href="modules.php?name=News&amp;file=article&amp;sid=' . $sid . '#$tid">' . $subject . '</a></li>';
                    }
                }
                echo '
					   </ul>
				    </div>';
            }
            echo '
			     </div>';
        }
    }

    $content = ob_get_clean();
    define('PMXPROFVIEWLOADED', true);
    require_once(PMX_SYSTEM_DIR . DS . 'mx_userfunctions.php');
    $view = new pmxUserPage($uinfo);
    //$view->subtitle = _YAUSERINFO . ' - ' . $uinfo['uname'];
/*
   if ($istheuser) {
        $view->subtitle = '<span class="title">'. _YAOVERVIEW . '</span>';
    } else {
        $view->subtitle = _YAUSERINFO . ' - ' . $uinfo['uname'];
    }*/
    $view->tabname = 'overview';
    $view->show($content);
}

?>