<?php
/**
 * mxBoard, pragmaMx Module
 * Copyright by pragmaMx Developer Team - http://www.pragmamx.org
 *
 * $Author: PragmaMx $
 * $Revision: 6 $
 * $Date: 2015-07-08 09:07:06 +0200 (Mi, 08. Jul 2015) $
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

/* check ob die verwendete pragmaMx Version korrekt ist */
(defined('PMX_VERSION') && version_compare(PMX_VERSION, '2.2.', '>=')) or
die('Sorry, pragmaMx-Version >= 2.2 is required for mxBoard.');

include_once(dirname(__FILE__) . DS . 'includes' . DS . 'header.php');

if (MX_IS_ADMIN) {
	set_time_limit(0);
	$userres=sql_query("SELECT uid,uname,user_regtime FROM ${user_prefix}_users");
	while ($user=sql_fetch_assoc($userres)) {
		if ($user['user_regtime']==0) {
			$userquery = "SELECT * FROM $table_members WHERE username='".$user['uname']."'";
			$res = sql_query($userquery);
			$userdata = sql_fetch_assoc($res); 
			if ($userdata['regdate']>=0) {
				//sql_query("update from ${prefix}_users set user_regtime=".$userdata['regdate']." WHERE uid=".$user['uid']."");
				echo "update from ${user_prefix}_users set user_regtime=".$userdata['regdate']." WHERE uid=".$user['uid']."<br />";
			}
			
		} else {
			$userquery = "SELECT count(uid) FROM $table_members WHERE username='".$user['uname']."'";
			$res = sql_query($userquery);
			list($userdata) = sql_fetch_row($res); 			
			if ($userdata==0){
				echo "insert ".$user['uname']." <br>";
				mxb_insert_user($user['uname']);
			}
		}
	}
}

include_once(MXB_BASEMODINCLUDE . 'footer.php');
