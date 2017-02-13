<?php
/**
 * pragmaMx - Web Content Management System
 * Copyright by pragmaMx Developer Team - http://www.pragmamx.org
 * written with: $Id: config.default.php 6 2015-07-08 07:07:06Z PragmaMx $
 */

defined('mxMainFileLoaded') or die('access denied');

$senddeletemail = 1;
$sendaddmail = 1;
$allowchangetheme = 1;
$yastartpage = 'modules.php?name=Your_Account';
$yaproofdate = 0;
$sendnewusermsg = 0;
$msgadminid = 2;
$msgicon = 't_envelope.gif';
$msgdefaultlang = 'german';
$useuserpoints = 1;
$excludedusers = 'beispiel_hshshsee,beispiel_hhhh';
$upoints_entries = 4;
$upoints_pics = 3;
$upoints_comments = 1;
$upoints_votes = 1;
$upoints_posts = 2;
$upoints_threads = 3;
$agb_agree = 1;
$agb_agree_link = 'modules.php?name=legal';
$pp_link = 'modules.php?name=legal&file=privacy';
$minpass = 0;
$showusergroup = 0;
$uname_min_chars = 4;
$uname_space_chars = 0;
$uname_special_chars = 1;
$uname_caseinsensitive = 1;
$passlost_codeoption = 1;
$register_option = 1;
$default_group = 1;
$pm_poptime = 0;
$file_maxsize = 102400;
$path_avatars = 'images/forum/avatar';
$access_avatars = array('-3'=>'on','-2'=>'on','0'=>'on','1'=>'on');
$path_upload = 'media/userpics';
$access_upload = array('-3'=>'on','-2'=>'on','0'=>'off','1'=>'off');
$width_mini = 40;
$height_mini = 30;
$width_small = 100;
$height_small = 80;
$width_normal = 170;
$height_normal = 220;
$width_full = 640;
$height_full = 480;
$mail_notice = 0;
$mail_address = '';
$endings = array('jpg','jpeg','gif','png');
$suffix_normal = 'normal';
$suffix_mini = 'mini';
$suffix_small = 'small';
$suffix_big = 'full';
$conf = array();

?>