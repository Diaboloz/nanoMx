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

if (empty($tid) || !is_numeric($tid)) {
    include(dirname(__file__)) . '/index.php';
    return;
}
// / alle Ausgabe zwischenspeichern, wird wieder verworfen, wenn kein Fehler auftritt
ob_start();
include_once(dirname(__FILE__) . DS . 'includes' . DS . 'header.php');

$query = sql_query("SELECT tid, fid, subject, lastpost, author, message, dateline, icon, usesig, closed, topped, useip, bbcodeoff, smileyoff
                            FROM $table_threads
                            WHERE tid=" . intval($tid));
$thread = sql_fetch_object($query);

if (!is_object($thread) || $thread->tid != $tid) {
    return mxbExitMessage(_TEXTNOTHREAD, true);
}

$thread->subject = stripslashes($thread->subject);

$fid = intval($thread->fid);
$query = sql_query("SELECT name, private, userlist, moderator, type, fup, fid, postperm, allowhtml, allowsmilies, allowbbcode, guestposting, allowimgcode
                            FROM $table_forums
                            WHERE fid=" . intval($fid) . " AND type IN('forum','sub')");
$forums = sql_fetch_object($query);

if (!is_object($forums)) {
    return mxbExitMessage(_TEXTNOFORUM, true);
}
if (!mxbPrivateCheck($forums)) {
    return mxbExitMessage(_PRIVFORUMMSG, true);
}

if (empty($orderdate) || $orderdate != 'DESC') {
    $orderdate = 'ASC';
}
// --------------------------------------------
// querypost->display post from sql request
$querypost = sql_query("SELECT fid, tid, pid, author, message, dateline, icon, usesig, useip, bbcodeoff, smileyoff
                                FROM $table_posts
                                WHERE tid=" . intval($tid) . "
                                ORDER BY dateline $orderdate ");
// alle Posts, des Threads in ein Array lesen
$post_array = array();
while ($post = sql_fetch_object($querypost)) {
    $post_array[] = $post;
}

$thread_array[] = $thread;
if ($orderdate == "ASC") {
    // eine Seite und absteigend >> thread davor
    $post_array = array_merge(array($thread), $post_array);
} else {
    // eine Seite und aufsteigend >> thread ans Ende
    $post_array = array_merge($post_array, array($thread));
}

sql_query("UPDATE $table_threads SET views=views+1 WHERE tid=" . intval($tid));
// Ausgabepuffer komplett leeren
while (ob_get_length()) {
    ob_end_clean();
}
ob_start();

/* versch. HTTP Header senden */
if (!headers_sent()) {
    header('Content-type: text/html; charset=utf-8');
    header('Content-Language: ' . _DOC_LANGUAGE);
    header('X-Powered-By: pragmaMx ' . PMX_VERSION);
}

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head><title><?php echo $sitename ?></title>
<style type="text/css">
<!--
body {
   background-color: #ffffff;
   color: #000000;
}

.largetext {
   background-color: transparent;
   color: #000000;
   font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;
   font-size: 13px;
}

.link {
   font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;
   font-size: 11px;
}

.text {
   background-color: transparent;
   color: #000000;
   font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;
   font-size: 11px;
}
-->
</style>
</head>
<body>
<center>
<table border="0" width="640" cellpadding="0" cellspacing="1" bgcolor="#d3d3d3">
<tr><td>
<table border="0" width="640" cellpadding="20" cellspacing="1" bgcolor="#FFFFFF">
<tr><td align="left">
<img src="images/<?php echo $site_logo ?>" border="0" alt=""/>
<br/><br/>
<p class="text"><b><?php echo _THREADNAME ?> <span class="largetext"><?php echo $thread->subject ?></span></b></p>
<p class="text"><b><?php echo _TEXTFORUM ?></b> <?php echo $forums->name ?></p>
<p class="text"><b><?php echo _TEXTAUTHOR ?></b> <?php echo $thread->author ?></p>
</td></tr>
<tr><td>

<?php
// Schleife durch Posts
foreach ($post_array as $post) {
    // thread fixen:
    $post->pid = (empty($post->pid)) ? 0 : $post->pid;

    $date = gmdate($dateformat, (int)$post->dateline + ($timeoffset * 3600));
    $time = gmdate($timecode, (int)$post->dateline + ($timeoffset * 3600));

    $poston = "$date " . _TEXTAT . " $time";

    $post->message = stripslashes($post->message);
    $post->message = mxbPostify($post->message, $forums->allowhtml, $forums->allowsmilies, $forums->allowbbcode, $forums->allowimgcode, $post->smileyoff, $post->bbcodeoff);

    echo '
    <hr/>
    <p class="text"><b>' . $post->author . '</b> - ' . $poston . '</p>
    <div class="text">' . $post->message . '</div>
    ';
}
// ENDE Schleife durch Posts
echo '
</td></tr>
<tr><td align="center">
<hr/>
<font class="text">
' . _COMEFROM . ' ' . $sitename . '<br/>
<a href="' . MX_HOME_URL . '" class="link">' . MX_HOME_URL . '</a>
<br/><br/>
' . _URLOFTHISSITE . '
<br/>
<a href="' . MX_HOME_URL . '/' . MXB_BM_VIEWTHREAD1 . 'fid=' . $fid . '&amp;tid=' . $tid . '" class="link">' . MX_HOME_URL . '/' . MXB_BM_VIEWTHREAD1 . 'fid=' . $fid . '&amp;tid=' . $tid . '</a>
</font>
</td></tr>
</table></td></tr></table>
</center>
</body>
</html>';

$ob_contents = ob_get_contents();
ob_end_clean();

echo $ob_contents;
// include_once(MXB_BASEMODINCLUDE . 'footer.php');

?>
