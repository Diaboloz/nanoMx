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

if (!mxGetAdminPref('radminarticle')) {
    vkpNewsNotAuthorized();
    exit;
}

$module_name = basename(dirname(dirname(__FILE__)));
mxGetLangfile($module_name);
// mxSessionSetVar('panel', MX_ADMINPANEL_CONTENT);
/**
 */
function vkpStoriesHeader($title)
{
    // TODO: Current-Klasse anfügen !!
    $item[] = '<a class="nav-link" href="' . adminUrl(PMX_MODULE, 'adminStory') . '" title="' . _ADDSTORY . '">' . _ADDSTORY . '</a>';
    $item[] = '<a class="nav-link" href="' . adminUrl(PMX_MODULE) . '" title="' . _ADMIN_NEWSNEW . '">' . _ADMIN_NEWSNEW . '</a>';
    $item[] = '<a class="nav-link" href="' . adminUrl(PMX_MODULE, 'currentStories') . '" title="' . _ADMIN_NEWSCURRENT . '">' . _ADMIN_NEWSCURRENT . '</a>';
    $item[] = '<a class="nav-link" href="' . adminUrl(PMX_MODULE, 'timedStories') . '" title="' . _ADMIN_NEWSTIMED . '">' . _ADMIN_NEWSTIMED . '</a>';
    $item[] = '<a class="nav-link" href="' . adminUrl(PMX_MODULE, 'Categories') . '" title="' . _CATEGORIESADMIN . '">' . _ADMIN_NEWSCATEGORIES . '</a>';
    $menu = implode('</li><li>', $item);
    title($title);
    echo '
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    	<div class="collapse navbar-collapse" id="navbarNav">
			<ul class="navbar-nav">
				<li class="nav-item">' . $menu . '</li>
			</ul>
		</div>
	</nav>';
}

/**
 */
function vkpNewsNotAuthorized()
{
    include('header.php');
    vkpStoriesHeader(_ARTICLEADMIN);
    echo '
		<div class="alert alert-warning text-center">
			<p>
			<strong>' . _NOTAUTHORIZED1 . '</strong>
			<br />
			' . _NOTAUTHORIZED2 . '<br /><br />' . _GOBACK . '
			<p>
		</div>';
    include('footer.php');
}

/**
 */
function vkpPutInHome($story)
{
    // // Achtung!!! ihome: 0 = Ja , 1 = Nein
    $sel1 = (empty($story['ihome'])) ? ' checked="checked"' : "";
    $sel2 = (empty($story['ihome'])) ? "" : ' checked="checked"';
    echo '
		<div class="form-group row">
    		<label class="col-md-4 form-control-label"">' . _PUBLISHINHOME . '</label>
    		<div class="col-md-8">
    			<label class="radio-inline">
    				<input type="radio" name="ihome" value="0" ' . $sel1 . ' />' . _YES . '
                </label>
    			<label class="radio-inline">
    				<input type="radio" name="ihome" value="1" ' . $sel2 . ' />' . _NO . '
                </label>
                <p class="help-block">(' . _ONLYIFCATSELECTED . ')</p>
			</div>          
        </div>';
}

/**
 * vkpNewsSelectPoll()
 *
 * @param integer $sid
 * @return
 */
function vkpNewsSelectPoll($artid, $pollID)
{
    global $prefix;

    switch (true) {
        case $artid && !$pollID:
            // bestehender Datensatz editieren
            $options[0] = '<option value="0">- ' . _NO . '</option>';
            /* die aktuell zugewiesene Umfrage auf jeden Fall mit anzeigen  */
            $result = sql_query("SELECT pollID, pollTitle, pollactive
          FROM ${prefix}_poll_desc
          WHERE artid = " . intval($artid));
            list($pollID, $pollTitle, $pollactive) = sql_fetch_row($result);
            if ($pollID) {
                $activ = ($pollactive) ? ' (' . _ACTIVE . ')' : '';
                $options[$pollID] = "<option value=\"$pollID\" selected=\"selected\" class=\"current\">" . mxEntityQuotes($pollTitle . $activ) . "</option>";
            }
            break;
        case !$artid && $pollID:
            // neuer Datensatz Vorschau
            $options[0] = '<option value="0">- ' . _NO . '</option>';
            /* die aktuell zugewiesene Umfrage auf jeden Fall mit anzeigen  */
            $result = sql_query("SELECT pollID, pollTitle, pollactive
          FROM ${prefix}_poll_desc
          WHERE artid = 0
            AND pollID = " . intval($pollID));
            list($pollID, $pollTitle, $pollactive) = sql_fetch_row($result);
            if ($pollID) {
                $activ = ($pollactive) ? ' (' . _ACTIVE . ')' : '';
                $options[$pollID] = "<option value=\"$pollID\" selected=\"selected\" class=\"current\">" . mxEntityQuotes($pollTitle . $activ) . "</option>";
            }
            break;
        case !$artid && !$pollID:
        default:
            // neuer Datensatz eingeben
            $pollID = 0; // wird als Bedingung unten gebraucht
            /* die NEIN Option */
            $options[0] = '<option value="0" selected="selected" class="current">- ' . _NO . '</option>';
            break;
    }

    /* ansonsten nur die nicht zugewiesenen Umfragen anzeigen  */
    $result = sql_query("SELECT pollID, pollTitle, pollactive
        FROM ${prefix}_poll_desc
        WHERE artid = 0 AND pollID<>" . intval($pollID) . "
        ORDER BY timeStamp DESC
        LIMIT 0,35");
    while (list($plID, $plTitle, $plactive) = sql_fetch_row($result)) {
        $activ = ($plactive) ? ' (' . _ACTIVE . ')' : '';
        $options[$plID] = "<option value=\"$plID\">" . mxEntityQuotes($plTitle . $activ) . "</option>";
    }

    echo '
		<div class="form-group row">
        	<label class="col-md-4 form-control-label" for="pollID">' . _ATTACHAPOLL . '</label>
            <div class="col-md-5">
				<select id="pollID" name="pollID" class="form-control">
					' . implode(PHP_EOL, $options) . '
				</select>
            </div>
        </div>';

    if ($artid && $pollTitle) {
        echo '- ' . sprintf(_ANNOUNCEEDIT, '<a href="' . adminUrl('Surveys', 'edit', array('pollID' => $pollID)) . '">' . $pollTitle . '</a>') . '<br />';
    }
}

/**
 */
function DeleteStory($qid, $ok = 1)
{
    global $prefix;
    if (empty($ok)) {
        include('header.php');
        vkpStoriesHeader(_SUBMISSIONSADMIN);
        echo '
			<div class="alert alert-danger text-center">
				<p>' . _SURETODELSTORY . '</p>
				<p>
					<a class="btn btn-danger" href="' . adminUrl(PMX_MODULE, 'DeleteStory', "qid=$qid&amp;ok=1") . '\">' . _YES . '</a>
					<a class="btn btn-primary" href="' . adminUrl(PMX_MODULE) . '">' . _NO . '</a>
				</p>
			</div>';
        include('footer.php');
    } else {
        $result = sql_query("DELETE FROM ${prefix}_queue where qid=" . intval($qid));
        if (!$result) {
            mxErrorScreen("Sorry, the Story can't be deleted.");
            return;
        }
        mxRedirect(adminUrl(PMX_MODULE));
    }
}

/**
 */
function SelectCategory($cat)
{
    global $prefix;
    $selcat = sql_query("SELECT catid, title 
						FROM ${prefix}_stories_cat 
						ORDER BY title");
    $a = 1;
    echo '
    	<label class="col-md-4 form-control-label" for="' . catid . '">' . _CATEGORY . '</label>
    		<div class="col-md-5">
        		<select id="' . catid . '" name="' . catid . '" class="form-control">';
    $sel = (empty($cat)) ? 'selected="selected" class="current"' : '';
    echo "<option value=\"0\" $sel> (" . _ARTICLES . ")</option>";
    while (list($catid, $title) = sql_fetch_row($selcat)) {
        $sel = ($catid == $cat) ? 'selected="selected" class="current"' : '';
        echo "<option value=\"$catid\" $sel>" . htmlspecialchars($title) . "</option>";
        $a++;
    }
    echo '
				</select>
		</div>';
    // [&nbsp;<a href=\"" . adminUrl(PMX_MODULE, 'AddCategory') . "\">"._ADD."</a> | <a href=\"" . adminUrl(PMX_MODULE, 'EditCategory') . "\">"._EDIT."</a> | <a href=\"" . adminUrl(PMX_MODULE, 'DelCategory') . "\">"._DELETE."</a>&nbsp;]";
}

/**
 * neu eingegangene Story - bearbeiten
 */
function DisplayStory($qid)
{
    global $prefix, $user_prefix;
    $result = sql_query("SELECT qid, uid, uname as informant, subject as title, story as hometext, storyext as bodytext, topic, alanguage
    					FROM ${prefix}_queue
    					WHERE qid=" . intval($qid));
    $story = sql_fetch_assoc($result);
    $story['op'] = PMX_MODULE . '/DisplayStory';
    include('header.php');
    vkpStoriesHeader(_SUBMISSIONSADMIN);
    myform($story, 1, _ADDARTICLE);
    include('footer.php');
}

/**
 * neu eingegangene Story - Voransicht
 */
function previewStory($story, $title = _PREVIEWSTORY)
{
    include('header.php');
    vkpStoriesHeader(_ARTICLEADMIN);
    myform(mxStripSlashes($story), 1, $title);
    include('footer.php');
}

/**
 * ueber Submit_News eingegangene Story speichern
 */
function PostStory($story)
{
    global $prefix, $user_prefix;

    $story = mxStoryDefaults($story);
    // versch. Daten pruefen
    // hier nicht umleiten, sondern Vorschau mit Meldung zeigen
    if (empty($story['topic'])) $story['errmsg'][] = _ERRNOTOPIC;
    if (empty($story['title']) || $story['title'] == _NOSUBJECT) $story['errmsg'][] = _ERRNOTITLE;
    if (empty($story['hometext'])) $story['errmsg'][] = _ERRNOTEXT;
    if (isset($story['errmsg'])) {
        if (empty($story['qid'])) {
            $story['op'] = PMX_MODULE . '/adminStory';
        } else {
            $story['op'] = PMX_MODULE . '/DisplayStory';
        }
        previewStory($story, _ADDARTICLE);
        return;
    }
    // die Daten des aktuellen Admins
    $admindata = mxGetAdminData();
    // Erstellungsdatum ermitteln
    $year = (empty($story['year'])) ? date('Y') : $story['year'];
    $updDate = sprintf("%04d-%02d-%02d %02d:%02d:00", $year, $story['month'], $story['day'], $story['hour'], $story['min']);
    // Datenbank anfuegen
    $qry = "INSERT INTO ${prefix}_stories SET
        catid     = " . intval($story['catid']) . ",
        title     ='" . mxAddSlashesForSQL($story['title']) . "',
        hometext  ='" . mxAddSlashesForSQL($story['hometext']) . "',
        bodytext  ='" . mxAddSlashesForSQL($story['bodytext']) . "',
        topic     = " . intval($story['topic']) . ",
        informant ='" . mxAddSlashesForSQL($story['informant']) . "',
        notes     ='" . mxAddSlashesForSQL($story['notes']) . "',
        ihome     = " . intval($story['ihome']) . ",
        alanguage ='" . mxAddSlashesForSQL($story['alanguage']) . "',
        acomm     = " . intval($story['acomm']) . ",
        time      ='" . $updDate . "',
        aid       ='" . mxAddSlashesForSQL($admindata['aid']) . "'
        ";

    $resultinsert = sql_query($qry);
    // Wenn erfolgreich eingefuegt..
    if ($resultinsert) {
        // Umfrage eintragen
        $story['sid'] = sql_insert_id();
        mxInsertPoll($story);
        // Userzähler erhöhen
        $userinfo = mxGetUserDataFromUsername($story['informant']);
        if (!empty($userinfo['uid'])) {
            sql_query("update {$user_prefix}_users set counter=(counter+1) where uid=" . intval($userinfo['uid']));
        }
        // Adminzähler erhöhen
        sql_query("update ${prefix}_authors set counter=(counter+1) where aid='" . mxAddSlashesForSQL($admindata['aid']) . "'");
        // wenn ueber Submit_News gemeldet, den Queue Datensatz löschen
        if (!empty($story['qid'])) {
            sql_query("DELETE FROM ${prefix}_queue where qid=" . intval($story['qid']));
        }
        mxRedirect(adminUrl(PMX_MODULE, 'EditStory', 'sid=' . $story['sid']), _CHANGESAREOK, 1);
    } else {
        if (!empty($story['qid'])) {
            mxRedirect(adminUrl(PMX_MODULE, 'DisplayStory', 'qid=' . $story['qid']), _ERRNOSAVED, 3);
        } else {
            mxRedirect(adminUrl(PMX_MODULE, 'currentStories'), _ERRNOSAVED, 3);
        }
    }
}

/**
 * vorhandene bearbeitete Story speichern
 */
function ChangeStory($story)
{
    global $prefix, $user_prefix;

    $story = mxStoryDefaults($story);
    // versch. Daten pruefen
    // hier nicht umleiten, sondern Vorschau mit Meldung zeigen
    if (empty($story['topic'])) $story['errmsg'][] = _ERRNOTOPIC;
    if (empty($story['title']) || $story['title'] == _NOSUBJECT) $story['errmsg'][] = _ERRNOTITLE;
    if (empty($story['hometext'])) $story['errmsg'][] = _ERRNOTEXT;
    if (isset($story['errmsg'])) {
        $story['op'] = PMX_MODULE . '/EditStory';
        previewStory($story, _EDITARTICLE);
        return;
    }
    // die Daten des aktuellen Admins
    $admindata = mxGetAdminData();
    // Erstellungsdatum ermitteln
    $year = (empty($story['year'])) ? date('Y') : $story['year'];
    $updDate = sprintf("%04d-%02d-%02d %02d:%02d:00", $year, $story['month'], $story['day'], $story['hour'], $story['min']);
    // Datenbank aktualisieren
    $qry = "UPDATE ${prefix}_stories SET
        catid='" . intval($story['catid']) . "',
        title='" . mxAddSlashesForSQL($story['title']) . "',
        hometext='" . mxAddSlashesForSQL($story['hometext']) . "',
        bodytext='" . mxAddSlashesForSQL($story['bodytext']) . "',
        topic='" . intval($story['topic']) . "',
        informant='" . mxAddSlashesForSQL($story['informant']) . "',
        notes='" . mxAddSlashesForSQL($story['notes']) . "',
        ihome='" . intval($story['ihome']) . "',
        alanguage='" . mxAddSlashesForSQL($story['alanguage']) . "',
        acomm='" . intval($story['acomm']) . "',
        time='" . $updDate . "'
        WHERE sid=" . intval($story['sid']) . "";
    $result = sql_query($qry);

    if ($result) {
        // Umfrage eintragen / aktualisieren
        mxInsertPoll($story);
        // Userzähler aktualisieren, falls anderer User angegeben wurde
        $userinfo = mxGetUserDataFromUsername($story['informant']);
        if ($userinfo['uname'] != $story['informant_before']) {
            if (!empty($userinfo['uid'])) {
                sql_query("update {$user_prefix}_users set counter=(counter+1) where uid=" . intval($userinfo['uid']));
            }
            sql_query("update {$user_prefix}_users set counter=(counter-1) where uname='" . mxAddSlashesForSQL(substr($story['informant_before'], 0, 25)) . "' AND counter > 0");
        }
        mxRedirect(adminUrl(PMX_MODULE, 'EditStory', 'sid=' . $story['sid']), _CHANGESAREOK, 1);
    } else {
        mxRedirect(adminUrl(PMX_MODULE, 'EditStory', 'sid=' . $story['sid']), _ERRNOSAVED, 3);
    }
}

/**
 * vorhandene Story bearbeiten
 */
function EditStory($sid)
{
    global $prefix;
    $qry = "SELECT * FROM ${prefix}_stories where sid=" . intval($sid);
    $result = sql_query($qry);
    $story = sql_fetch_assoc($result);
    $story['op'] = PMX_MODULE . '/EditStory';
    $story += (array)date_parse($story['time']);
    include('header.php');
    vkpStoriesHeader(_ARTICLEADMIN);
    myform($story, 1, _EDITARTICLE);
    include('footer.php');
}

/**
 */
function RemoveStory($sid, $ok = 0)
{
    global $prefix, $user_prefix;
    if ($ok) {
        $ok = mxDelStories(array(intval($sid)));
        mxRedirect(adminUrl(PMX_MODULE));
    } else {
        include('header.php');
        vkpStoriesHeader(_ARTICLEADMIN);
        echo '
        	<div class="alert alert-danger text-center">
				<p>' . _REMOVESTORY . '&nbsp;' . $sid . '&nbsp;' . _ANDCOMMENTS . '
    			<p>
    				<a class="btn btn-danger" href="' . adminUrl(PMX_MODULE, 'RemoveStory', 'sid=' . $sid . '&amp;ok=1') . '\">' . _YES . '</a>
					<a class="btn btn-primary" href="' . adminUrl(PMX_MODULE) . '">' . _NO . '</a>
    			</p>
			</div>';
        include('footer.php');
    }
}

/**
 * neue Story erstellen
 */
function adminStory()
{
    $story = array();
    $story['op'] = PMX_MODULE . '/adminStory';
    include('header.php');
    vkpStoriesHeader(_ARTICLEADMIN);
    myform($story, 0, _ADDARTICLE);
    include('footer.php');
}

function mxStoryDefaults($story = array())
{
    $admindata = mxGetAdminData();

    /* Datum */
    $today = getdate();

    $defaults = array(/*  */
        'sid' => 0,
        'catid' => 0,
        'aid' => $admindata['aid'],
        'title' => _NOSUBJECT,
        'hometext' => '',
        'bodytext' => '',
        'topic' => vkpGetFirstTopic(),
        'informant' => mxSessionGetVar('user_uname'),
        'notes' => '',
        'ihome' => 0,
        'alanguage' => '',
        'acomm' => 0, // // Achtung!!! acomm: 0 = Ja , 1 = Nein
        'qid' => 0, // von der queue-Tabelle
        'pollID' => 0,
        'year' => $today['year'],
        'month' => $today['mon'],
        'day' => $today['mday'],
        'hour' => $today['hours'],
        'min' => $today['minutes'],
        );

    $story = array_merge($defaults, $story);

    return $story;
}

/**
 * das Eingabeformular
 * Die Daten muessen ohne Backslashes kommen
 */
function myform($story, $preview = 0, $caption = '')
{
    global $prefix;

    $story = mxStoryDefaults($story);

    echo '
    <form name="snews" action="' . adminUrl(PMX_MODULE) . '" method="post">
    <div class="row">
    	
    		<div class="col-md-8">
    			<div class="card">';
    if ($caption) {
        echo '
        			<div class="card-header">
          				<i class="fa fa-file-text-o"></i>&nbsp' . $caption . '
		        	</div>';
    }

    if (isset($story['errmsg'])) {
        echo '
			<div class="alert alert-danger text-center">
				<p class="h3">' . _ERROROCCURS . '</p>
				<ul class="list-unstyled">
					<li>' . implode('</li><li>', $story['errmsg']) . '</li>
				</ul>
			</div>';
    } else if ($preview) {
        vkpStoryPreview($story);
    }
    echo '
					<div class="card-body">';
    				addNewsTextFields($story);
    echo '
				</div>
				</div>
			</div>
	<div class="col-md-4">
		<div class="card">
		<div class="card-header">
          				<i class="fa fa-wrench"></i>&nbsp' . _CONFIGURE . '
		        	</div>
			<div class="card-body">';
    vkpNewsSelectTopicCat($story);
    vkpPutInHome($story); //// Achtung!!! ihome: 0 = Ja , 1 = Nein

    vkpAutomatedSelect($story['year'], $story['day'], $story['month'], $story['hour'], $story['min']);
    vkpNewsSelectActComments($story['acomm']); //// Achtung!!! acomm: 0 = Ja , 1 = Nein
    vkpNewsSelectPoll($story['sid'], $story['pollID']);
    vkpNewsSelectLanguage($story['alanguage']);
    vkpUsernameField ($story);
    echo '
    </div><!-- card-body -->
    <div class="card-footer">
    <div class="row">
    	<div class="col-md-6">
    		<input type="hidden" name="sid" value="' . $story['sid'] . '" />';
    echo '<input type="hidden" name="qid" value="' . $story['qid'] . '" />';
    echo '<input type="hidden" name="aid" value="' . $story['aid'] . '" />';
    echo '<select class=" form-control" name="op">';
    if ($story['op'] == PMX_MODULE . '/DisplayStory' || $story['op'] == PMX_MODULE . '/PreviewAgain') {
        echo '<option value="' . PMX_MODULE . '/PreviewAgain">' . _PREVIEWSTORY . '</option>';
    }
    if ($story['op'] == PMX_MODULE . '/DisplayStory' || $story['op'] == PMX_MODULE . '/PreviewAgain' || $story['op'] == PMX_MODULE . '/adminStory' || $story['op'] == PMX_MODULE . '/PreviewAdminStory') {
        echo '<option value="' . PMX_MODULE . '/PostStory">' . _POSTSTORY . '</option>';
    }
    if ($story['op'] == PMX_MODULE . '/adminStory' || $story['op'] == PMX_MODULE . '/PreviewAdminStory') {
        echo '<option value="' . PMX_MODULE . '/PreviewAdminStory">' . _PREVIEWSTORY . '</option>';
    }
    if ($story['op'] == PMX_MODULE . '/EditStory') {
        echo '<option value="' . PMX_MODULE . '/ChangeStory">' . _SAVECHANGES . '</option>';
    }
    echo '</select>
    	</div>';
    echo '

    	<div class="col-md-6">
    	<button type="submit" class="btn btn-primary"><i class="fa fa-check fa-lg"></i>&nbsp;' . _OK . '</button>';
    if ($story['op'] == PMX_MODULE . '/DisplayStory' || $story['op'] == PMX_MODULE . '/PreviewAgain') {
        echo '&nbsp; <a class="btn btn-danger" href="' . adminUrl(PMX_MODULE, 'DeleteStory', 'qid=' . $story['qid'] . '&amp;ok=0') . '"><i class="fa fa-trash fa-lg"></i>&nbsp;' . _DELETE . '</a>';
    } else if ($story['op'] == PMX_MODULE . '/EditStory') {
        echo '&nbsp; <a class="btn btn-danger" href="' . adminUrl(PMX_MODULE, 'RemoveStory', 'sid=' . $story['sid'] . '&amp;ok=0') . '"><i class="fa fa-trash fa-lg"></i>&nbsp;' . _DELETE . '</a>';
    }
    echo '</div>
    	</div>
    </div>


		</div>
    </div>
    </div>
    </form>
    ';
}

/**
 */
function submissions()
{
    global $bgcolor1, $bgcolor2, $prefix;

    $img_delete = '<i class="fa fa-trash fa-lg"></i>&nbsp;'  . _DELETE;
    $img_edit = '<i class="fa fa-edit fa-lg"></i>&nbsp;'  . _EDIT;
    $img_view = '<i class="fa fa-search fa-lg"></i>&nbsp;'  . _SHOW;

    include('header.php');
    $result = sql_query("SELECT qid, subject, timestamp, alanguage
    					FROM ${prefix}_queue
    					ORDER BY timestamp");
    $dummy = 0;
    $out = '';
    while (list($qid, $title, $time, $alanguage) = sql_fetch_row($result)) {
        if ($qid) $xqid = $qid;
        $title = (empty($title)) ? _NOSUBJECT : $title;
        $alanguage = (empty($alanguage)) ? _ALL : $alanguage;
        $out .= "<tr>\n";
        $out .= "<td>&nbsp;" . ($dummy + 1) . "</td>\n";
        $out .= "<td>&nbsp;" . formatTimestamp($time, _SHORTDATESTRING) . "</td>\n";
        $out .= "<td>&nbsp;<a href=\"" . adminUrl(PMX_MODULE, 'DisplayStory', "qid=" . $qid) . "\">" . $title . "</a></td>\n";
        $out .= "<td>" . $alanguage . "</td>\n";
        $out .= "<td><a class=\"btn btn-info\" href=\"" . adminUrl(PMX_MODULE, 'DisplayStory', "qid=" . $qid) . "\">" . $img_view . "</a> <a class=\"btn btn-danger\" href=\"" . adminUrl(PMX_MODULE, "DeleteStory", "qid=" . $qid . "&amp;ok=0") . "\">" . $img_delete . "</a></td>\n";
        $out .= "</tr>";
        $dummy++;
    }
    if ($dummy == 1 && isset($xqid)) {
        mxRedirect(adminUrl(PMX_MODULE, 'DisplayStory', "qid=$xqid"));
        exit;
    }

    vkpStoriesHeader(_SUBMISSIONSADMIN);
    if (empty($dummy)) {
        echo '
        	<div class="alert alert-info text-center">
				<p class="h3">' . _NOSUBMISSIONS . '</p>
			</div>';
    } else {
        echo '
        <div class="card">
        	<div class="card-header">
        	  <i class="fa fa-pause"></i>&nbsp;' . _NEWSUBMISSIONS . '
        	</div>
        	<div class="card-body">
				<table class="table">
					<thead>
						<tr>
							<th>&nbsp;</th>
							<th>' . _DATE . '</th>
							<th>' . _TITLE . '</th>
							<th>' . _UALANGUAGE . '</th>
							<th>' . _FUNCTIONS . '</th>
						</tr>
					</thead>';
        echo $out;
        echo '
				</table>
			</div>
		</div>';
    }
    include('footer.php');
}

/**
 */
function currentStories()
{
    global $bgcolor1, $bgcolor2, $prefix;
    $countview = 50;
    extract(mxGetAdminData());
    include("header.php");

    $img_delete = '<i class="fa fa-trash fa-lg"></i>&nbsp;'  . _DELETE;
    $img_edit = '<i class="fa fa-edit fa-lg"></i>&nbsp;'  . _EDIT;
    $img_view = '<i class="fa fa-search fa-lg"></i>&nbsp;'  . _SHOW;

    $result = sql_query("SELECT sid, aid as s_aid, title, time, topic, informant, alanguage, counter
    		FROM ${prefix}_stories WHERE  time  <= now()
    		ORDER BY time desc");
    $dummy = 0;
    $out = '';
    $out2 = '';
    while ($story = sql_fetch_assoc($result)) {
        extract($story);
        $title = (empty($title)) ? _NOSUBJECT : $title;
        if ($dummy < $countview) {
            $alanguage = (empty($alanguage)) ? _ALL : $alanguage;
            $out .= "<tr>\n";
            $out .= "<td>" . ($dummy + 1) . "</td>\n";
            $out .= "<td>" . formatTimestamp($time, _SHORTDATESTRING) . "</td>\n";
            $out .= "<td>" . $title . "</td>\n";
            $out .= "<td>" . mxGetLanguageString($alanguage) . "</td>\n";
            $out .= "<td>" . $counter . "</td>\n";
            $out .= "<td><a class=\"btn btn-primary btn-sm\" href=\"" . adminUrl(PMX_MODULE, 'EditStory', "sid=" . $sid) . "\">" . $img_edit . "</a> <a class=\"btn btn-danger btn-sm\" href=\"" . adminUrl(PMX_MODULE, 'RemoveStory', 'sid=' . $sid) . "\">" . $img_delete . "</a></td>\n";
            $out .= "</tr>";
        } else {
            $out2 .= "<option value=\"" . $sid . "\">" . formatTimestamp($time, _SHORTDATESTRING) . " - " . htmlspecialchars(mxCutString($title, 75)) . "</option>\n";
        }
        $dummy++;
    }
    if ($dummy == 1 && isset($xsid)) {
        mxRedirect(adminUrl(PMX_MODULE, 'DisplayStory', "sid=$xsid"));
        exit;
    }

    vkpStoriesHeader(_ARTICLEADMIN);
    if (empty($dummy)) {
        echo '
			<div class="alert alert-info text-center">
				<p class="h3">
					' . _ADMIN_NEWSNOARTICLES . '
				</p>
			</div>';
    } else {
        echo '
        <div class="card">
        	<div class="card-header">
        	  <i class="fa fa-newspaper-o"></i>&nbsp;' . $countview . '&nbsp;' . _LAST . '&nbsp;' . _ARTICLES . '
        	</div>
        	<div class="card-body">
				<table class="table">
					<thead>
						<tr>
							<th>&nbsp;</th>
							<th>' . _DATE . '</th>
							<th>' . _TITLE . '</th>
							<th>' . _UALANGUAGE . '</th>
							<th>' . _READS . '</th>
							<th>' . _FUNCTIONS . '</th>
						</tr>
					</thead>';
        echo '
        			<tbody>
        				' .$out . '
	        
    		    	</tbody>
        		</table>
        	</div>
        </div>';
        if ($out2) {
            echo '
				<center><b>' . _ADMIN_NEWSOLD . '</b> (' . ($dummy - $countview) . ')</center>
				<br />';
            echo "
<form action=\"" . adminUrl(PMX_MODULE) . "\" method=\"get\">
<select name=\"sid\" size=\"15\">" . $out2 . "</select><br /><br />
<select name=\"op\">
<option value=\"" . PMX_MODULE . "/EditStory\" selected=\"selected\">" . _EDIT . "</option>
<option value=\"" . PMX_MODULE . "/RemoveStory\">" . _DELETE . "</option>
</select>
<input type=\"submit\" value=\"" . _GO . "\" />
</form>";
        }
    }
    include('footer.php');
}

/**
 */
function timedStories()
{
    global $bgcolor1, $bgcolor2, $prefix;
    $countview = 20;
    extract(mxGetAdminData());
    include('header.php');

    $img_delete = '<i class="fa fa-trash fa-lg"></i>&nbsp;'  . _DELETE;
    $img_edit = '<i class="fa fa-edit fa-lg"></i>&nbsp;'  . _EDIT;
    $img_view = '<i class="fa fa-search fa-lg"></i>&nbsp;'  . _SHOW;

    $result = sql_query("SELECT sid, aid as s_aid, title, time, topic, informant, alanguage
    					FROM ${prefix}_stories WHERE  time  > now()
    					ORDER BY time desc");
    $dummy = 0;
    $out = '';
    $out2 = '';
    while ($story = sql_fetch_assoc($result)) {
        extract($story);
        $title = (empty($title)) ? _NOSUBJECT : $title;
        if ($dummy < $countview) {
            $alanguage = (empty($alanguage)) ? _ALL : $alanguage;
            $out .= "<tr>\n";
            $out .= "<td>" . ($dummy + 1) . "</td>\n";
            $out .= "<td>" . formatTimestamp($time, _SHORTDATESTRING) . "</td>\n";
            $out .= "<td>" . $title . "</td>\n";
            $out .= "<td>" . mxGetLanguageString($alanguage) . "</td>\n";
            $out .= "<td nowrap=\"nowrap\"><a class=\"btn btn-primary\" href=\"" . adminUrl(PMX_MODULE, 'EditStory', "sid=" . $sid) . "\">" . $img_edit . "</a> <a class=\"btn btn-danger\" href=\"" . adminUrl(PMX_MODULE, 'RemoveStory', 'sid=' . $sid) . "\">" . $img_delete . "</a></td>\n";
            $out .= "</tr>";
        } else {
            $out2 .= "<option value=\"" . $sid . "\">" . $time . " - " . htmlspecialchars($title) . "</option>\n";
        }
        $dummy++;
    }
    if ($dummy == 1 && isset($xsid)) {
        mxRedirect(adminUrl(PMX_MODULE, 'DisplayStory', "sid=$xsid"));
        exit;
    }

    vkpStoriesHeader(_ARTICLEADMIN);
    if (empty($dummy)) {
        echo '
        	<div class="alert alert-info text-center">
				<p class="h3">
					' . _NOAUTOARTICLES . '
				</p>
			</div>';
    } else {
        echo '
       <div class="card">
        	<div class="card-header">
        	  <i class="fa fa-newspaper-o"></i>&nbsp;' . _ADMIN_NEWSTIMED . '
        	</div>
        	<div class="card-body">
				<table class="table">
					<thead>
						<tr>
							<th>&nbsp;</th>
							<th>' . _ADM_MESS_DATESTART . '</th>
							<th>' . _TITLE . '</th>
							<th>' . _UALANGUAGE . '</th>
							<th>' . _FUNCTIONS . '</th>
						</tr>
					</thead>';
        echo $out;
        echo '
			</table>
		</div>';
        if ($out2) {
            echo '
				<center><b>' . _ADMIN_NEWSOLD . '</b> (' . ($dummy - $countview) . ')</center>';
            echo "
<form action=\"" . adminUrl(PMX_MODULE) . "\" method=\"get\">
<select name=\"sid\" size=\"15\">" . $out2 . "</select><br /><br />
<select name=\"op\">
<option value=\"" . PMX_MODULE . "/EditStory\" selected=\"selected\">" . _EDIT . "</option>
<option value=\"" . PMX_MODULE . "/RemoveStory\">" . _DELETE . "</option>
</select>
<input type=\"submit\" value=\"" . _GO . "\" />
</form>";
        }
    }
    include('footer.php');
}

/**
 */
function vkpUsernameField ($story)
{
    if (empty($story['informant'])) {
        if ($GLOBALS['op'] == 'adminStory') {
            if (MX_IS_USER) {
                $userinfo = mxGetUserData();
                $story['informant'] = $userinfo['uname'];
            } else {
                $userinfo = mxGetAdminSession();
                $story['informant'] = $userinfo['aid'];
            }
        } else {
            $story['informant'] = '';
        }
    }
    if (empty($userinfo) && !empty($story['informant'])) {
        $userinfo = mxGetUserDataFromUsername($story['informant']);
    }
    $informant_before = (isset($story['informant_before'])) ? $story['informant_before'] : $story['informant'];
    echo '
		<div class="form-group row">
    		<label class="col-md-4 form-control-label" for="informant">' . _SUBMITTER . '</label>
    		<div class="col-md-5">
    		    <input type="text" class="form-control" id="informant" name="informant" size="28" maxlength="25" value="' . mxEntityQuotes($story['informant']) . '" />
    			<input type="hidden" name="informant_before" value="' . mxEntityQuotes($informant_before) . '" />';

    if (!empty($userinfo['uid'])) {
        // thx to diabolo...
        echo '
        	<p>
        	<a href="mailto:' . $userinfo['email'] . '">' . _SENDEMAILUSER . '</a>';
        if(mxModuleAllowed('Private_Messages')){
        	echo ' | <a href="modules.php?name=Private_Messages&amp;op=send&amp;uname=' . $story['informant'] . '" target="_blank">' . _SENDPMUSER . '</a>';
        }
        echo '
        	</p>';
    }
    echo '
    		</div>          
        </div>';

}

/**
 * #####   Categorie-Functions   #################
 */
function CategoriesMenu()
{
    global $prefix;
    $result = sql_query("SELECT ${prefix}_stories_cat.catid, ${prefix}_stories_cat.title, Count(${prefix}_stories.sid) AS anz
        FROM ${prefix}_stories_cat LEFT JOIN ${prefix}_stories
        ON ${prefix}_stories_cat.catid = ${prefix}_stories.catid
        GROUP BY ${prefix}_stories_cat.catid, ${prefix}_stories_cat.title
        ORDER BY ${prefix}_stories_cat.title");
    while (list($catid, $title, $anz) = sql_fetch_row($result)) {
        $tit = base64_encode($title);
        $fields[] = '
        	<tr>
        		<td>
					<strong>' . htmlspecialchars($title) . '</strong>
				</td>
				<td>' . $anz . '&nbsp;' . _ARTICLES . '</td>
				<td>
					<form action="' . adminUrl(PMX_MODULE) . '" method="post">
						<input type="text" class="form-control" name="title" value="' . htmlspecialchars($title) . '" size="22" maxlength="40" />
						<input type="hidden" name="op" value="' . PMX_MODULE . '/SaveEditCategory" />
						<input type="hidden" name="catid" value="' . $catid . '" />
						<input type="hidden" name="anz" value="' . $anz . '" />
						<button type="submit" class="btn btn-primary"><i class="fa fa-check fa-lg"></i>&nbsp;' . _SAVECHANGES . '</button>						
					</form>
				</td>
				<td>
					<a class="btn btn-danger" href="' . adminUrl(PMX_MODULE, 'DelCategory', 'catid=' . $catid . '&amp;anz=' . $anz . '&amp;tit=' . $tit) . '">
						<i class="fa fa-trash fa-lg"></i>&nbsp;' . _DELETE . '
					</a>
				</td>
			</tr>';
    }

    include('header.php');
    vkpStoriesHeader(_CATEGORIESADMIN);
    echo '
    	<div class="card">
    		<div class="card-header">
        	  <i class="fa fa-bars"></i>&nbsp;' . _CATEGORIESADMIN . '
        	</div>
    		<table class="table">
    			<tbody>';
    echo implode("\n", $fields);
    echo '
    			</tbody>
    		</table>
    		<div class="card-footer">
				<form action="' . adminUrl(PMX_MODULE) . '" method="post">
                	<input type="hidden" name="op" value="' . PMX_MODULE . '/SaveCategory" /> 
					<div class="row">    
	                    <div class="col-md-3 h5">                      
                            ' . _CATEGORYADD . '                    
    	                </div>           
                    	<div class="form-group col-md-3">
							<input class="form-control" type="text" name="title" value="" size="22" maxlength="40" />
                    	</div>
                    	<div class="form-group col-sm-2">
                        	<button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-plus fa-lg"></i>&nbsp;' . _ADD . '</button>    
                    	</div>                                   
                	</div>
                </form>  
        	</div>
    	</div>';
    include('footer.php');
}

/**
 */
function SaveCategory($pvs)
{
    global $prefix;
    $pvs = mxAddSlashesForSQL($pvs);
    extract($pvs);
    $result = sql_query("SELECT catid
    					FROM ${prefix}_stories_cat
    					WHERE title='" . $title . "'");
    list($check) = sql_fetch_row($result);
    if ($check) {
        $what1 = _CATEXISTS;
        $what2 = _GOBACK;
    } else {
        $what1 = _CATADDED;
        $what2 = "[&nbsp;<a href='" . adminUrl(PMX_MODULE, 'Categories') . "'>" . _CATEGORIESADMIN . "</a>&nbsp;]";
        $result = sql_query("INSERT INTO ${prefix}_stories_cat (title, counter) values ('" . $title . "', 0)");
        if (!$result) {
            $what1 = _ERRNOSAVED;
        } else {
            $ok = true;
        }
    }
    if (isset($ok)) {
        mxRedirect(adminUrl(PMX_MODULE, 'Categories'));
    } else {
        include('header.php');
        vkpStoriesHeader(_CATEGORIESADMIN);
        echo '
			<center><b>' . $what1 . '</b>
			<br />
			<br />';
        echo $what2 . '</center>';
        include('footer.php');
    }
}

/**
 */
function SaveEditCategory($pvs)
{
    global $prefix;
    $pvs = mxAddSlashesForSQL($pvs);

    extract($pvs);
    $catid = (int)$catid;
    $result = sql_query("SELECT catid
    					FROM ${prefix}_stories_cat
    					WHERE title='$title'");
    list($check) = sql_fetch_row($result);
    if ($check) {
        $what1 = _CATEXISTS;
        $what2 = _GOBACK;
    } else {
        $what1 = _CATSAVED . " (" . $title . ")";
        $what2 = "[&nbsp;<a href='" . adminUrl(PMX_MODULE, 'Categories') . "'>" . _CATEGORIESADMIN . "</a>&nbsp;]";
        $result = sql_query("update ${prefix}_stories_cat set title='" . ($title) . "' where catid=$catid");
        if (!$result) {
            $what1 = _ERRNOSAVED;
        } else {
            $ok = true;
        }
    }
    if (isset($ok)) {
        mxRedirect(adminUrl(PMX_MODULE, 'Categories'));
    } else {
        include('header.php');
        vkpStoriesHeader(_CATEGORIESADMIN);
        echo '
			<center><b>' . $what1 . '</b>
			<br />
			<br />';
        echo $what2 . "</center>";
        include('footer.php');
    }
}

/**
 */
function DelCategory($gvs)
{
    global $prefix;
    extract($gvs);
    $title = mxSecureValue(base64_decode($tit), true);
    $catid = (int)$catid;
    include('header.php');
    vkpStoriesHeader(_CATEGORIESADMIN);
    echo '
		<center>
			<font class="option"><b>';
    if (empty($anz)) {
        sql_query("DELETE FROM ${prefix}_stories_cat where catid=" . intval($catid));
        echo _CATDELETED . "</b> (" . $title . ")</font><br /><br /><a href='" . adminUrl(PMX_MODULE, 'Categories') . "'>" . _CATEGORIESADMIN . "</a>";
    } else {
        echo _DELETECATEGORY . "</b></font><br />";
        echo "<br /><br /><b>" . _WARNING . ":</b> " . _THECATEGORY . " <b>" . htmlspecialchars($title) . "</b> " . _HAS . " <b>" . $anz . "</b> " . _STORIESINSIDE . '<br /><br />'
         . _DELCATWARNING1 . '<br />' . _DELCATWARNING2 . '<br /><br />' . _DELCATWARNING3 . '<br /><br />'
         . "[&nbsp;<a href=\"" . adminUrl(PMX_MODULE, 'YesDelCategory', "catid=$catid") . "\">" . _YESDEL . "</a> | "
         . "<a href=\"" . adminUrl(PMX_MODULE, 'NoMoveCategory', "catid=$catid&tit=$tit") . "\">" . _NOMOVE . "</a>]<br /><br />" . _GOBACK . " ";
    }
    echo '
		</center>';
    include('footer.php');
}

/**
 */
function YesDelCategory()
{
    global $prefix;
    $result1 = sql_query("DELETE FROM ${prefix}_stories_cat where catid=" . intval($_GET['catid']));
    if ($result1) {
        $result2 = sql_query("SELECT sid
        					FROM ${prefix}_stories
        					WHERE catid=" . intval($_GET['catid']));
        while (list($sid) = sql_fetch_row($result2)) {
            $allsid[] = $sid;
        }
        if (isset($allsid)) {
            $ok = mxDelStories($allsid);
        }
    }
    mxRedirect(adminUrl(PMX_MODULE, 'Categories'));
}

/**
 */
function NoMoveCategory($request)
{
    global $prefix;

    extract($request);
    $catid = (int)$catid;
    $title = mxSecureValue(base64_decode($tit), true);
    include('header.php');
    vkpStoriesHeader(_CATEGORIESADMIN);
    echo '
		<center><font class="option">
			<b>' . _MOVESTORIES . '</b>
			<br />
			<br />';
    if (empty($newcat)) {
        echo _ALLSTORIES . " <b>$title</b> " . _WILLBEMOVED . '<br /><br />';
        echo "<form action=\"" . adminUrl(PMX_MODULE) . "\" method=\"post\">";
        echo "<b>" . _SELECTNEWCAT . ":</b> ";
        echo "<select name=\"newcat\">";
        echo "<option value=\"-1\"> (" . _ARTICLES . ")</option>";
        $selcat = sql_query("select catid, title from ${prefix}_stories_cat WHERE catid<>" . intval($catid) . " order by title");
        while (list($newcat, $title) = sql_fetch_row($selcat)) {
            echo "<option value=\"$newcat\">" . htmlspecialchars($title) . "</option>";
        }
        echo "</select>";
        echo "<input type=\"hidden\" name=\"catid\" value=\"$catid\" />";
        echo "<input type=\"hidden\" name=\"op\" value=\"" . PMX_MODULE . "/NoMoveCategory\" />";
        echo "<input type=\"hidden\" name=\"tit\" value=\"$tit\" />";
        echo "<input type=\"submit\" value=\"" . _OK . "\" />";
        echo "</form>";
    } else {
        $newcat = (int)$newcat;
        if ($newcat == -1) $newcat = 0;
        sql_query("update ${prefix}_stories set catid=" . intval($newcat) . " where catid=" . intval($catid));
        sql_query("DELETE FROM ${prefix}_stories_cat where catid=" . intval($catid));
        echo _MOVEDONE . "<br /><br /><a href='" . adminUrl(PMX_MODULE, 'Categories') . "'>" . _CATEGORIESADMIN . "</a>";
    }
    echo '
		</center>';

    include('footer.php');
}

/**
 */
function mxDelStories($arrsid = array())
{
    global $prefix, $user_prefix;
    $insids = implode(',', $arrsid);
    $result = sql_query("SELECT sid, catid, aid, informant
    					FROM ${prefix}_stories
    					WHERE sid IN (" . $insids . ")");
    if ($result) {
        $result22 = sql_query("DELETE FROM ${prefix}_stories WHERE sid IN (" . $insids . ")");
        if ($result22) {
            sql_query("DELETE FROM ${prefix}_comments WHERE sid IN (" . $insids . ")");
            sql_query("UPDATE ${prefix}_poll_desc SET artid='0' WHERE artid IN (" . $insids . ")");
            while ($story = sql_fetch_assoc($result)) {
                if ($story['aid']) {
                    sql_query("UPDATE ${prefix}_authors SET counter=(counter-1) WHERE aid='" . mxAddSlashesForSQL($story['aid']) . "' AND counter > 0");
                }
                if ($story['informant']) {
                    sql_query("UPDATE {$user_prefix}_users SET counter=(counter-1) WHERE uname='" . mxAddSlashesForSQL(substr($story['informant'], 0, 25)) . "' AND counter > 0");
                }
            }
            return true;
        }
    }
}

/**
 */
function mxInsertPoll($story)
{
    global $prefix;

    sql_query("UPDATE ${prefix}_poll_desc SET artid=0
            WHERE pollID <> " . intval($story['pollID']) . "
              AND artid = " . intval($story['sid']));

    sql_query("UPDATE ${prefix}_poll_desc SET artid=" . intval($story['sid']) . "
            WHERE pollID = " . intval($story['pollID']) . "
              AND artid <> " . intval($story['sid']));
}

function removeSubComments($tid)
{
    global $prefix;
    $result = sql_query("select tid from " . $prefix . "_comments where pid='$tid'");
    $numrows = sql_num_rows($result);
    if ($numrows > 0) {
        while (list($stid) = sql_fetch_row($result)) {
            removeSubComments($stid);
            sql_query("delete from " . $prefix . "_comments where tid=$stid");
        }
    }
    sql_query("DELETE from " . $prefix . "_comments WHERE tid=$tid");
}

function removeComment($tid, $sid, $ok = 0)
{
    global $prefix;
    if ($ok) {
        /**
         * Call recursive delete function to delete the comment and all its childs
         */
        removeSubComments($tid);
        $result = sql_query("SELECT count(tid) from " . $prefix . "_comments where sid=" . intval($sid));
        list($numresults) = sql_fetch_row($result);
        sql_query("update " . $prefix . "_stories set comments=" . intval($numresults) . " where sid=" . intval($sid));
        mxRedirect("modules.php?name=" . PMX_MODULE . "&file=article&sid=$sid");
    } else {
        include('header.php');
        // GraphicAdmin();
        title(_REMOVECOMMENTS);
        echo '
        	<p class="align-center">
       			' . _SURETODELCOMMENTS . '<br /><br />
       			[&nbsp;<a href="modules.php?name=' . PMX_MODULE . '&amp;file=article&amp;sid=' . $sid . '" onclick="history.go(-1); return false;">' . _NO . '</a> | <a href="' . adminUrl(PMX_MODULE, 'RemoveComment', 'tid=' . $tid . '&amp;sid=' . $sid . '&amp;ok=1') . '">' . _YES . '</a>&nbsp;]
       		</p>';
        include('footer.php');
    }
}
/**
 * #####   main   #################
 */
include_once(PMX_SYSTEM_DIR . DS . 'mxNewsFunctions.php');

if (empty($sid)) $sid = 0;
if (empty($ok)) $ok = 0;

switch ($op) {
    case PMX_MODULE . '/Categories':
    case PMX_MODULE . '/EditCategory':
        CategoriesMenu();
        break;

    case PMX_MODULE . '/DelCategory':
        DelCategory($_GET);
        break;

    case PMX_MODULE . '/YesDelCategory':
        YesDelCategory();
        break;

    case PMX_MODULE . '/NoMoveCategory':
        NoMoveCategory($_REQUEST);
        break;

    case PMX_MODULE . '/SaveEditCategory':
        SaveEditCategory($_POST);
        break;

    case PMX_MODULE . '/SelectCategory':
        SelectCategory($cat);
        break;

    case PMX_MODULE . '/AddCategory':
        AddCategory();
        break;

    case PMX_MODULE . '/SaveCategory':
        SaveCategory($_POST);
        break;

    case PMX_MODULE . '/DisplayStory':
        DisplayStory($qid);
        break;

    case PMX_MODULE . '/adminStory':
        adminStory($sid);
        break;

    case PMX_MODULE . '/PreviewAgain':
    case PMX_MODULE . '/PreviewAdminStory':
        previewStory($_POST);
        break;

    case PMX_MODULE . '/PostAdminStory':
    case PMX_MODULE . '/PostStory':
        PostStory($_POST);
        break;

    case PMX_MODULE . '/EditStory':
        EditStory($sid);
        break;

    case PMX_MODULE . '/RemoveStory':
        RemoveStory($sid, $ok);
        break;

    case PMX_MODULE . '/ChangeStory':
        ChangeStory($_POST);
        break;

    case PMX_MODULE . '/DeleteStory':
        DeleteStory($qid, $ok);
        break;

    case PMX_MODULE . '/timedStories':
        timedStories();
        break;

    case PMX_MODULE . '/currentStories':
        currentStories();
        break;

    case PMX_MODULE . '/RemoveComment':
        removeComment($tid, $sid, $ok);
        break;

    default:
        submissions();
        break;
} // end switch

?>