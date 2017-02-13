<?php
/**
 * mxBoard, pragmaMx Module
 * Copyright by pragmaMx Developer Team - http://www.pragmamx.org
 * write with: $Id: helper.php 6 2015-07-08 07:07:06Z PragmaMx $
 */

defined('mxMainFileLoaded') or die('access denied');

// Database settings
$tablepre = $GLOBALS['prefix'] . '_mxboard_';

$table_banned     = $tablepre . 'banned';
$table_forums     = $tablepre . 'forums';
$table_links      = $tablepre . 'links';
$table_members    = $tablepre . 'members';
$table_posts      = $tablepre . 'posts';
$table_ranks      = $tablepre . 'ranks';
$table_smilies    = $tablepre . 'smilies';
$table_themes     = $tablepre . 'themes';
$table_threads    = $tablepre . 'threads';
$table_whosonline = $tablepre . 'whosonline';
$table_words      = $tablepre . 'words';

// general settings
$bbname = 'Forum nanoMx';
$avastatus = 'on';
$langfile = 'french';
$XFtheme = 'mxsilver';
$lastpostsubj = 'off';
$lastpostsubjchars = '25';
$linkforumstatus = 'on';
$linkthreadstatus = 'on';
$postperpage = '25';
$topicperpage = '30';
$memberperpage = '45';
$hottopic = '20';
$floodctrl = '10';
$bbstatus = 'on';
$bboffreason = '';
$whosonlinestatus = 'off';
$advancedonlinestatus = 'off';
$regviewonly = 'off';
$hideprivate = 'off';
$showsort = 'on';
$searchstatus = 'off';
$messotdstatus = 'on';
$dureemessotd = '1';
$faqstatus = 'off';
$memliststatus = 'off';
$memlistanonymousstatus = 'off';
$statspage = 'off';
$nbitemsinstats = '10';
$indexstats = 'off';
$affjumper = 'off';
$affjumperdynamic = 'off';
$piconstatus = 'on';
$smilieslinenumber = '5';
$smiliesrownumber = '3';
$colorsubject = 'off';
$allowu2u = 'off';
$reportpost = 'off';
$showtotaltime = 'off';
$adminstars = 'maxusersp3';

// Notify-settings
$adminemail = 'webmaster@nanomx.pro';
$emailalltoadmin = 'on';
$mailonthread = 'on';
$mailonpost = 'on';
$mailonedit = 'on';
$mailondele = 'on';
$emailalltomoderator = 'on';
$moderatormailonthread = 'on';
$moderatormailonpost = 'on';
$moderatormailonedit = 'on';
$moderatormailondelete = 'on';

// Text-settings
$textarearows = '10';
$textareacols = '100';
$sigbbcode = 'on';
$sightml = 'off';
$sigimgXxXauth = 'off';
$sigimgwidth = '100';
$sigimgheight = '50';

// Time-settings
$timeformat = '24';
$dateformat = 'dd/mm/yyyy';
$globaltimestatus = 'on';
$globaltimeoffset = '1';

// Default values
$eb_defhtml = 'no';
$eb_defsmilies = 'yes';
$eb_defbbcode = 'yes';
$eb_defanoposts = 'no';
$eb_defimgcode = 'yes';
$eb_defstaff = '';
$eb_defmain2sub = 'yes';
$eb_defmods = '';
$eb_defmessslv = '1';
$eb_defmembernews = 'no';
$eb_defmemberu2u = 'no';

// Boardrules-settings
$bbrules = 'off';
$bbrulestxt = 'Here is the text for the bb-rules popup-window';

// Captcha
$captcha = 'on';
$captchauser = 'off';

?>