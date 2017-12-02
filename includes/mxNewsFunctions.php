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
 *
 *
 */

defined('mxMainFileLoaded') or die('access denied');

/**
 * nur noch dummy, fuer aeltere Themes
 */
function convertNewsForView($msg)
{
    return $msg;
}

/**
 * vkpGetStoryDetails()
 *
 * @param mixed $story
 * @return
 */
function vkpGetStoryDetails($story)
{
    global $prefix;
    if (!isset($story['sid'])) {
        $story['sid'] = 0;
    }
    if (!isset($story['aid'])) {
        $story['aid'] = '';
    }
    if (!isset($story['acomm'])) {
        $story['acomm'] = 0;
    }
    if (!isset($story['time'])) {
        $story['time'] = sprintf("%04d-%02d-%02d", date('Y'), date('m'), date('d'));
    }
    if (empty($story['counter']) || intval($story['counter']) < 0) {
        $story['counter'] = 0;
    }
    if (empty($story['comments']) || intval($story['comments']) < 0) {
        $story['comments'] = 0;
    }
    if (!isset($story['cattitle']) && !empty($story['catid'])) {
        $result = sql_query("SELECT title as cattitle 
							FROM {$prefix}_stories_cat 
							WHERE catid=" . intval($story['catid']) . "");
        $cat = sql_fetch_assoc($result);
        $story['cattitle'] = (empty($cat['cattitle'])) ? '' : $cat['cattitle'];
    }
    if (!isset($story['cattitle'])) {
        $story['cattitle'] = '';
        $story['catid'] = 0;
    }
    if (!isset($story['topicimage']) && !empty($story['topic'])) {
        $result = sql_query("SELECT topicimage, topictext 
							FROM {$prefix}_topics 
							WHERE topicid=" . intval($story['topic']) . "");
        $topic = sql_fetch_assoc($result);
        $story['topicimage'] = (empty($topic['topicimage'])) ? '' : $topic['topicimage'];
        $story['topictext'] = (empty($topic['topictext'])) ? '' : $topic['topictext'];
    }
    if (!isset($story['topicimage'])) {
        $story['topicimage'] = '';
        $story['topic'] = 0;
    }
    if (!isset($story['topictext'])) {
        $story['topictext'] = '';
    }
    if (!isset($story['datetime'])) {
        $story['datetime'] = formatTimestamp($story['time']);
    }

    $tmp = array('hometext', 'bodytext', 'notes');
    foreach ($tmp as $key) {
        // Texte bereinigen, die evtl. nur leere Tags enthalten und gleich evtl. fehlende Variablen erstellen
        if (!isset($story[$key]) || !strip_tags($story[$key], '<img><embed><object>')) {
            $story[$key] = '';
        }
    }

    if (!isset($story['allmorelink'])) {
        $story['allmorelink'] = vkpGetMoreLink($story);
    }
    $story['morelink'] = $story['allmorelink']['formated'];
    if (empty($story['title'])) {
        $story['title'] = $story['topictext'];
    }
    $story['title_formated'] = $story['title'];
    if (!empty($story['catid'])) {
        $story['title_formated'] = "<a href=\"modules.php?name=News&amp;file=categories&amp;catid=" . $story['catid'] . "\"><span class=\"smaller storycat\">" . $story['cattitle'] . "</span></a>: " . $story['title'];
    }
    return $story;
}

/**
 * pmx_news_navigation()
 *
 * @param string $link // The page we are linking to
 * @param integer $count_rows // Total number of items (database results)
 * @param integer $per_page // Max number of items you want shown per page
 * @param integer $currentpage // The current page being viewed
 * @param integer $items // Number of "digit" links to show before/after the currently viewed page
 * @return string
 */
function pmx_news_navigation($link, $count_rows, $per_page, $currentpage, $items = 0)
{
    if (!intval($count_rows) || !intval($per_page)) {
        return '';
    }

    $pages = ceil($count_rows / $per_page);
    if ($pages <= 1) {
        return '';
    }

    if (!intval($items)) {
        $items = ($GLOBALS['index']) ? 4 : 6;
    }

    $start = 1;
    $end = $pages + 1;

    switch (true) {
        case !$currentpage:
        case intval($currentpage) < 1:
            $currentpage = 1;
            break;
        case $currentpage > $pages:
            $currentpage = $pages;
            break;
    }

    if ($pages > $items) {
        $start = $currentpage - intval(($items -1) / 2);
        if ($start < 1) {
            $start = 1;
        }
        $end = $start + $items;
        if ($end > $pages) {
            $end = $pages + 1;
        }
        if ($end == ($pages + 1)) {
            $start = $end - $items;
        }
    }
    $backlink = ($currentpage > 1) ? ' href="' . $link . '&amp;page=' . ($currentpage - 1) . '"' : '';
    $nextlink = ($currentpage < $pages) ? ' href="' . $link . '&amp;page=' . ($currentpage + 1) . '"' : '';

    $out = '
		<div class="pagination">
			<span class="counter">' . sprintf(_PAGEOFPAGES, $currentpage, $pages) . '</span>';
    // if ($currentpage == 1) {
    // $out .= '<span>&lt;</span>';
    // } else {
    // $out .= '<a' . $backlink . ' title="' . _GOTOPAGEPREVIOUS . ": " . ($currentpage - 1) . '">&lt;</a>';
    // }
    if ($start > 1) {
        $out .= '
			<a href="' . $link . '" title="' . _GOTOPAGEFIRST . '">
				1 <span class="arrows">&laquo;</span>
			</a>';
        if ($start > 2) {
            $out .= '
				<span class="points">..</span>';
        }
    }
    for ($i = $start; $i < $end; $i++) {
        switch ($i) {
            case $currentpage:
                $out .= '<span class="current">' . $i . '</span>';
                break;
            case 1:
                $out .= '<a href="' . $link . '" title="' . _GOTOPAGEFIRST . '">1</a>';
                break;
            case $pages:
                $out .= '<a href="' . $link . '&amp;page=' . $i . '" title="' . _GOTOPAGELAST . '">' . $i . '</a>';
                break;
            default:
                $out .= '<a href="' . $link . '&amp;page=' . $i . '" title="' . _GOTOPAGE . ": " . $i . '">' . $i . '</a>';
        }
    }
    if ($end < $pages + 1) {
        if ($end < $pages) {
            $out .= '
				<span class="points">..</span>';
        }
        $out .= '
			<a href="' . $link . '&amp;page=' . $pages . '" title="' . _GOTOPAGELAST . '">
				<span class="arrows">&raquo;</span>
					' . $pages . '
			</a>';
    }
    // if ($currentpage >= $pages) {
    // $out .= '<span>&gt;</span>';
    // } else {
    // $out .= '<a' . $nextlink . ' title="' . _GOTOPAGENEXT . ": " . ($currentpage + 1) . '">&gt;</a>';
    // }
    $out .= '
		</div>';

    return $out;
}

/**
 * vkpGetMoreLink()
 *
 * @param mixed $story
 * @return
 */
function vkpGetMoreLink($story)
{
    $formated['homecount'] = strlen(trim(strip_tags($story['hometext'])));

    $check = trim(strip_tags($story['bodytext']));
    if (!$check && trim($story['bodytext'])) {
        $formated['bodycount'] = strlen($story['bodytext']);
    } else {
        $formated['bodycount'] = strlen($check);
    }

    $formated['totalcount'] = $formated['homecount'] + $formated['bodycount'];
    $formated['storylink'] = "<a href=\"modules.php?name=News&amp;file=article&amp;sid=" . $story['sid'] . "\">";
    $formated['printerlink'] = "<a href=\"modules.php?name=News&amp;file=print&amp;sid=" . $story['sid'] . "\" title='" . _PRINTER . "' rel=\"nofollow\" target=\"_blank\">";
    $formated['friendlink'] = (mxModuleAllowed('Recommend_Us')) ? "<a href=\"modules.php?name=News&amp;file=friend&amp;sid=" . $story['sid'] . "\" title='" . _FRIEND . "' rel=\"nofollow\">" : "<a>";
    $formated['datetime'] = _ON . ' <span>' . $story['datetime'] . '</span>';
    $formated['counter'] = $story['counter'] . ' ' . _READS;
    $formated['informantlink'] = (empty($story['informant'])) ? '<a title="' . _BY . ': ' . $GLOBALS['anonymous'] . '">' : mxCreateUserprofileLink($story['informant'], '', _BY . ': ' . $story['informant'], true);
    $formated['comments'] = "0 " . _COMMENTSQ;
    if (!empty($story['url'])) {
        $formated['postedby'] = _POSTEDBY . ' <a href="' . $story['url'] . '" target="_blank">' . $story['aid'] . '</a>';
    } else if (!empty($story['email'])) {
        $formated['postedby'] = _POSTEDBY . ' <a href="mailto:' . mxPrepareToDisplay($story['email']) . '">' . $story['aid'] . '</a>';
    } else {
        $formated['postedby'] = _POSTEDBY . ' ' . $story['aid'];
    }

    if (($formated['bodycount'] > 0) || ($story['comments'] > 0)) {
        $morelink['more'] = $formated['storylink'] . "<b>" . _READMORE . "</b></a>";
    }
    if ($formated['bodycount'] > 0) {
        $morelink['bytemore'] = $formated['totalcount'] . ' ' . _BYTESMORE;
    }
    $morelink['comments'] = "";
    // Kommentare erlaubt?
    if ($GLOBALS['articlecomm'] == 1 && $story['acomm'] == 0) { // Achtung!!! acomm: 0 = Ja , 1 = Nein
        $story['comments'] = (empty($story['comments'])) ? 0 : (int)$story['comments'];
        if (empty($story['comments'])) $formated['comments'] = $story['comments'] . ' ' . _COMMENTSQ;
        else if ($story['comments'] == 1) $formated['comments'] = $story['comments'] . ' ' . _COMMENT;
        else $formated['comments'] = $story['comments'] . ' ' . _COMMENTS;
        $morelink['comments'] = $formated['storylink'] . $formated['comments'] . "</a>";
    }
    // Achtung!!! acomm: 0 = Ja , 1 = Nein
    $formated['commentslink'] = (!empty($story['acomm']) || empty($GLOBALS['articlecomm'])) ? "<a>" : "<a href=\"modules.php?name=News&amp;file=article&amp;sid=" . $story['sid'] . "#comments\" title=\"" . $formated['comments'] . "\">";
    if (empty($formated['bodycount']) && $story['acomm'] == 0) {
        $morelink['comments'] .= " " . $formated['storylink'] . "<b>" . _READMORE . "</b></a>";
    }

    $morelink['functions'] = $formated['printerlink'] . mxCreateImage('images/print.gif', _PRINTER, array('title' => _PRINTER)) . "</a>";
    $morelink['functions'] .= (mxModuleAllowed('Recommend_Us')) ? "&nbsp;&nbsp;" . $formated['friendlink'] . mxCreateImage('images/friend.gif', _FRIEND, array('title' => _FRIEND)) . "</a>" : "";
    if (!empty($story['catid'])) {
        $morelink['categorie'] = "<a href=\"modules.php?name=News&amp;file=categories&amp;catid=" . $story['catid'] . "\" title=\"" . _NEWSCATEGORY . ": " . $story['cattitle'] . "\">" . $story['cattitle'] . "</a>";
    }
    $rated = (empty($story['score'])) ? 0 : substr($story['score'] / $story['ratings'], 0, 4);
    $morelink['score'] = _SCORE . ' ' . $rated;

    $morelink['formated'] = "(" . implode(" | ", $morelink) . ")";

    $morelink = array_merge($morelink, $formated);

    return $morelink;
}

/**
 * vkpStoryPreview()
 *
 * @param mixed $story
 * @return
 */
function vkpStoryPreview($story)
{
    // die Daten muessen ohne Backslashes kommen
    if (empty($story['topic'])) {
        $story['topic'] = vkpGetFirstTopic();
    }
    /* change for nanomx
    if (function_exists('mx_theme_engineversion')) {
        @mxGetLangfile('News');
        if (MX_IS_USER) {
            $userdata = mxGetUserData();
            $story['informant'] = $userdata['uname'];
        } else {
            $story['informant'] = _YOURNAME;
        }
        $story = vkpGetStoryDetails($story);
        @themearticle(null, null, null, null, null, null, null, null, null, $story);
    } else {
        /* fuer veraltete Themes (nuke etc.) */
        //remove image topic echo vkpTopicImage($story['topic'], 1);
        echo '
            <div class="card m-2 p-2">
                <h4>' . mxPrepareToDisplay($story['title']) . '</h4>';
        echo mxPrepareToDisplay($story['hometext']);
        if (isset($story['bodytext']) && trim(strip_tags($story['bodytext']))) {
            echo '<hr noshade="noshade" size="1" />' . mxPrepareToDisplay($story['bodytext']);
        }
        if (isset($story['notes']) && trim(strip_tags($story['notes']))) {
            echo '
                <p><strong>' . _NOTE . '</strong></p>
                    ' . mxPrepareToDisplay($story['notes']);
        }
    //}
    echo '
		  </div>';
}

/**
 * addNewsTextFields()
 *
 * @param mixed $story
 * @return
 */
function addNewsTextFields($story)
{
    // die Daten muessen ohne Backslashes kommen
    $sw = load_class('Textarea');
    echo '
		<div class="row">
            <div class="col-md-8">
              <div class="form-group">
                <label for="title">' . _TITLE . '</label>
                <input type="text" class="form-control" id="title" name="title" size="80" maxlength="80" value="' . mxEntityQuotes($story['title']) . '" />
              </div>
            </div>
          </div>';

    echo '
        <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label for="hometext">' . _STORYTEXT . '</label>
                ' . $sw->getHtml(array('name' => 'hometext', 'value' => $story['hometext'], 'height' => '350')) . '
              </div>
            </div>
        </div>


        <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label for="bodytext">' . _EXTENDEDTEXT . '</label>
                ' . $sw->getHtml(array('name' => 'bodytext', 'value' => $story['bodytext'], 'height' => '400')) . '
              </div>
            </div>
        </div>';
    // Achtung: Notes funktionieren nur, wenn die Funktion über das Adminmodul aufgerufen wird
    if (MX_IS_ADMIN && defined("mxAdminFileLoaded")) {
        echo '
        <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label for="notes">' . _NOTES . '</label>
                    ' . $sw->getHtml(array('name' => 'notes', 'value' => $story['notes'], 'height' => '120')) . '
              </div>
            </div>
        </div>';
    }
}

/**
 * vkpNewsSelectLanguage()
 *
 * @param string $alanguage
 * @return
 */
function vkpNewsSelectLanguage($alanguage = '')
{
    if ($GLOBALS['multilingual']) {
        echo '
            <div class="form-group row">
                <label class="col-md-4 form-control-label" for="language">' . _LANGUAGE . '</label>
            <div class="col-md-5">
                ' . mxLanguageSelect('alanguage', $alanguage, 'language', 1) . '
            </div>
        </div>';
    } else {
        echo '
			<input type="hidden" name="alanguage" value="" />';
    }
}

/**
 * vkpSelectTopic()
 *
 * @param integer $topic
 * @return
 */
function vkpSelectTopic($topic = 0)
{
    global $prefix;
    echo '
        <label class="col-md-4 form-control-label" for="topic">' . _TOPIC . '</label>
            <div class="col-md-5">
		        <select id="topic" name="topic" class="form-control">';
    $toplist = sql_query("SELECT topicid, topictext 
						FROM " . $prefix . "_topics 
						ORDER BY topictext");
    while (list($topicid, $topictext) = sql_fetch_row($toplist)) {
        $sel = ($topicid == $topic) ? 'selected="selected" class="current"' : '';
        echo '
			<option ' . $sel . ' value="' . $topicid . '">'
				. mxEntityQuotes($topictext) . '
			</option>';
    }
    echo '
		        </select>
            </div>';
}

/**
 * vkpGetTopics()
 *
 * @param mixed $sid
 * @return
 */
function vkpGetTopics($sid)
{
    global $prefix;
    if (empty($sid)) return array();
    $qry = "SELECT topicid, topicname, topicimage, topictext
            FROM {$prefix}_stories
            LEFT JOIN {$prefix}_topics
            ON {$prefix}_stories.topic = {$prefix}_topics.topicid
            WHERE {$prefix}_stories.sid = $sid";
    $result = sql_query($qry);
    $row = sql_fetch_array($result);
    return $row;
}

/**
 * vkpGetDate()
 *
 * @return
 */
function vkpGetDate()
{
    $today = getdate();
    $thour = $today['hours'];
    if ($thour < 10) {
        $thour = "0$thour";
    }
    $tmin = $today['minutes'];
    if ($tmin < 10) {
        $tmin = "0$tmin";
    }
    $tsec = $today['seconds'];
    if ($tsec < 10) {
        $tsec = "0$tsec";
    }
    return mx_strftime(_DATESTRING, time()) . " $thour:$tmin:$tsec";
}

/**
 * vkpAutomatedSelect()
 *
 * @param mixed $year
 * @param mixed $day
 * @param mixed $month
 * @param mixed $hour
 * @param mixed $min
 * @return
 */
function vkpAutomatedSelect($year, $day, $month, $hour, $min)
{
    $title = _CHNGPROGRAMSTORY;
    $xday = 1;
    while ($xday <= 31) {
        $sel = ($xday == (int)$day) ? 'selected="selected" class="current"' : '';
        $d[] = "<option value=\"$xday\" $sel>$xday</option>";
        $xday++;
    }
    $xmonth = 1;
    while ($xmonth <= 12) {
        $sel = ($xmonth == (int)$month) ? 'selected="selected" class="current"' : '';
        $m[] = "<option value=\"$xmonth\" $sel>$xmonth</option>";
        $xmonth++;
    }
    $xhour = 0;
    while ($xhour <= 23) {
        $sel = ($xhour == (int)$hour) ? 'selected="selected" class="current"' : '';
        $yhour = ($xhour < 10) ? "0$xhour" : $xhour;
        $h[] = "<option value=\"$xhour\" $sel>$yhour</option>";
        $xhour++;
    }
    $xmin = 0;
    $min = floor($min / 5) * 5;
    while ($xmin <= 59) {
        $sel = ($xmin == (int)$min) ? 'selected="selected" class="current"' : '';
        $ymin = ($xmin < 10) ? "0$xmin" : $xmin;
        $mi[] = "<option value=\"$xmin\" $sel>$ymin</option>";
        $xmin = $xmin + 5;
    }
    echo '
        <p class="h5">' . $title . '</p>
        <p class="small">' . _NOWIS . ': ' . vkpGetDate() . '</p>
<div class="row">

            <div class="form-group col-sm-3">
              <label for="day">' . _DAY . '</label>
              <select class="form-control" name="day" id="day">
                ' . (implode("\n", $d)) . '
              </select>
            </div>

            <div class="form-group col-sm-3">
              <label for="month">' . _UMONTH . '</label>
              <select class="form-control" name="month" id="month">
                ' . (implode("\n", $m)) . '
              </select>
            </div>

            <div class="col-sm-3">
              <div class="form-group">
                <label for="year">' . _YEAR . '</label>
                <input class="form-control" type="text" name="year" id="year" value="' . $year . '" size="5" maxlength="4" />
              </div>
            </div>



            <div class="col-sm-3">
              <div class="form-group">
                <label>' . _HOUR . '</label>
                <select class="form-control" name="hour">
                    ' . (implode("\n", $h)) . '
                </select>
                <select class="form-control" name="min">
                    ' . (implode("\n", $mi)) . '
                </select>
              </div>
            </div>

          </div>';

}

/**
 * vkpNewsSelectTopicCat()
 *
 * @param mixed $story
 * @return
 */
function vkpNewsSelectTopicCat($story)
{
    echo '
        <div class="form-group row">';
            vkpSelectTopic($story['topic']);
   echo '
        </div>
        <div class="form-group row">';
            SelectCategory($story['catid']);
    echo'          
        </div>';
}

/**
 * vkpNewsSelectActComments()
 *
 * @param integer $acomm
 * @return
 */
function vkpNewsSelectActComments($acomm = 0)
{
    // Achtung!!! acomm: 0 = Ja , 1 = Nein
    $sel1 = (empty($acomm)) ? 'checked="checked"' : '';
    $sel2 = (empty($acomm)) ? '' : 'checked="checked"';
    echo '            
		<div class="form-group row">
			<label class="col-md-4 col-form-label">' . _ACTIVATECOMMENTS . '</label>
            <div class="col-md-8">
                <label class="radio-inline">
                    <input type="radio" name="acomm" value="0" ' . $sel1 . ' />' . _YES . '
                </label>
                <label class="radio-inline">
                    <input type="radio" name="acomm" value="1" ' . $sel2 . ' />' . _NO . '
                </label>               
            </div>
        </div>';
}

/**
 * vkpTopicImage()
 *
 * @param mixed $topic
 * @param integer $full
 * @param string $align
 * @return
 */
function vkpTopicImage($topic, $full = 0, $align = "right")
{
    global $prefix, $tipath;
    if (empty($topic)) {
        $topicimage = "AllTopics.gif";
    } else {
        $result = sql_query("SELECT topicimage 
							FROM " . $prefix . "_topics 
							WHERE topicid='" . intval($topic) . "'");
        list($topicimage) = sql_fetch_row($result);
        if (empty($topicimage)) {
            $topicimage = "AllTopics.gif";
        }
    }
    $path = trim($tipath, ' ;,:./\\');
    if ($full) {
        return mxCreateImage($path . '/' . $topicimage, '', 0, 'align="' . $align . '"');
    } else {
        return $path . '/' . $topicimage;
    }
}

/**
 * vkpGetFirstTopic()
 *
 * @return
 */
function vkpGetFirstTopic()
{
    global $prefix;
    $result = sql_query("SELECT min(topicid) 
						FROM " . $prefix . "_topics");
    list($tid) = sql_fetch_row($result);
    if (empty($tid)) $tid = 0;
    return $tid;
}

/**
 * rate_article()
 *
 * @param mixed $sid
 * @param mixed $score
 * @return
 */
function rate_article($sid, $score)
{
    global $prefix;
    settype($sid, 'int');
    settype($score, 'int');
    if ($score) {
        if ($score > 5) {
            $score = 5;
        }
        if ($score < 1) {
            $score = 1;
        }

        if (isset($_COOKIE['ratecookie'])) {
            $r_cookie = explode(':', trim(base64_decode($_COOKIE['ratecookie']), ': '));
            if (in_array($sid, $r_cookie)) {
                return mxRedirect("modules.php?name=News&op=rate_complete&sid=$sid&rated=1");
            }
        }

        $r_cookie[] = $sid;

        $result = sql_query("update ${prefix}_stories set score=score+$score, ratings=ratings+1 where sid=" . intval($sid));
        $info = base64_encode(implode(':', array_unique($r_cookie)));
        mxSetCookie('ratecookie', $info, time() + 3600);
        return mxRedirect("modules.php?name=News&op=rate_complete&sid=$sid");
    } else {
        return mxRedirect("modules.php?name=News&file=article&sid=$sid", _DIDNTRATE);
    }
}

/**
 * pmx_news_get_articles_resource()
 *
 * @param mixed $where
 * @param mixed $offset
 * @param mixed $storynum
 * @return
 */
function pmx_news_get_articles_resource($where, $offset, $storynum)
{
    global $prefix, $user_prefix;

    $qry = "SELECT s.sid, s.informant, s.title, s.hometext, s.bodytext, s.comments, s.counter, s.notes, s.time, s.alanguage, s.acomm, s.aid, s.score, s.ratings, s.topic, t.topicname, t.topicimage, t.topictext, c.catid, c.title AS cattitle, a.url, a.email, u.uid, u.uname, u.user_viewemail
			FROM (({$prefix}_stories AS s
			LEFT JOIN {$prefix}_stories_cat AS c ON s.catid = c.catid)
			LEFT JOIN {$prefix}_topics AS t ON s.topic = t.topicid)
			LEFT JOIN {$prefix}_authors AS a ON s.aid = a.aid
			LEFT JOIN {$user_prefix}_users AS u ON a.user_uid = u.uid
			WHERE " . $where . "
			ORDER BY s.time DESC, s.sid DESC
			LIMIT " . intval($offset) . "," . intval($storynum) . ";";
    return sql_query($qry);
}

?>
