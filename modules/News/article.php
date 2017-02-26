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
 * $Revision: 39 $
 * $Author: PragmaMx $
 * $Date: 2015-07-28 15:00:02 +0200 (Di, 28. Jul 2015) $
 *
 *
 */

defined('mxMainFileLoaded') or die('access denied');

$index = 1;
$plugins=1;

$module_name = basename(dirname(__FILE__));
mxGetLangfile($module_name);
include_once(PMX_SYSTEM_DIR . '/mxNewsFunctions.php');

if (empty($op)) $op = '';
if (empty($mode)) $mode = '';
if (isset($sid)) $sid = intval($sid);

if (empty($sid)) { // # && !isset($tid)
    mxRedirect("modules.php?name=" . $module_name);
}

if ($op == 'Reply') {
    mxRedirect("modules.php?name=" . $module_name . "&file=comments&op=Reply&pid=0&sid=$sid");
}

$qry = "SELECT s.*,
				s.sid AS s_sid,
				c.title AS cattitle,
				t.topicname,
				t.topicimage,
				t.topictext
				FROM (${prefix}_stories AS s
				LEFT JOIN ${prefix}_stories_cat AS c ON s.catid = c.catid)
				LEFT JOIN ${prefix}_topics AS t ON s.topic = t.topicid
				WHERE s.sid=" . $sid . " AND s.time <= now();";

$result = sql_query($qry);
$story = sql_fetch_assoc($result);

if (empty($story["aid"])) {
    mxRedirect("modules.php?name=" . $module_name);
}

sql_query("UPDATE ${prefix}_stories SET counter=counter+1 where sid=" . $sid);

$story = vkpGetStoryDetails($story);
$story["completetext"] = (empty($story["bodytext"])) ? $story["hometext"] . $story["notes"] : $story["hometext"] . '<br />' . $story["bodytext"] . $story["notes"];
// das Array, welches in den News-Bloecken abgefragt wird !!
$GLOBALS['story_blocks'] = $story;

$artpage = 1;
$pagetitle = $story["title"];
include('header.php');
themearticle($story["aid"], $story["informant"], $story["datetime"], $story["title_formated"], $story["completetext"], $story["topic"], $story["topicname"], $story["topicimage"], $story["topictext"], $story);
if ((($mode != "nocomments") && ($story["acomm"] == 0)) && $GLOBALS['articlecomm']) { // // Achtung!!! acomm: 0 = Ja , 1 = Nein
    include_once(PMX_MODULES_DIR . DS . $module_name . "/comments.php");
    DisplayTopic();
}
include('footer.php');

?>
