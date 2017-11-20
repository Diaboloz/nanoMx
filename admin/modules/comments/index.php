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

/* Sprachdatei auswählen */
mxGetLangfile(dirname(__FILE__));

$limit = 20;
$cpgname = 'Gallery';

include_once(PMX_SYSTEM_DIR . DS . 'mxNewsFunctions.php');
// mxSessionSetVar('panel', MX_ADMINPANEL_CONTENT);
function ListComments()
{
    global $prefix, $cpgname, $limit;

    $offset = (empty($_GET['s'])) ? 0 : intval($_GET['s']);
    $class = '';

    $result = sql_query("SHOW TABLES;"); #LIKE '{$prefix}%'
    while (list($tablename) = sql_fetch_row($result)) {
        $tables[$tablename] = $tablename;
        // print $tablename . "<br />\n";
    }

    /* TODO: hook für die Module !!! */

    if (isset($tables["{$prefix}_downloads_votedata"])) {
        $qry[] = "
    SELECT 'downloads' as ctype, ratingdbid as cid, ratinglid as parent, ratingcomments as `comment`, ratingtimestamp as `cdate`, ratinguser as `user`, ratinghostname as host, '' as subject
    FROM `{$prefix}_downloads_votedata`
    WHERE `ratingcomments` <> ''
    ";
    }
    if (isset($tables["{$prefix}_links_votedata"])) {
        $qry[] = "
    SELECT 'links' as ctype, ratingdbid as cid, ratinglid as parent, ratingcomments as `comment`, ratingtimestamp as `cdate`, ratinguser as `user`, ratinghostname as host, '' as subject
    FROM `{$prefix}_links_votedata`
    WHERE `ratingcomments` <> ''
    ";
    }
    if (isset($tables["{$prefix}_reviews_comments"])) {
        $qry[] = "
    SELECT 'reviews' as ctype, cid, rid as parent, comments as `comment`, FROM_UNIXTIME(`date`) as `cdate`, username as `user`, '' as host, '' as subject
    FROM `{$prefix}_reviews_comments`
    WHERE `comments` <> ''
    ";
    }
    if (isset($tables["{$prefix}_comments"])) {
        $qry[] = "
    SELECT 'news' as ctype, tid as cid, sid as parent, comment, FROM_UNIXTIME(`reply_date`) as `cdate`, name as `user`, host_name as host, subject
    FROM `{$prefix}_comments`
    WHERE `comment` <> ''
    ";
    }
    if (isset($tables["{$prefix}_pollcomments"])) {
        $qry[] = "
    SELECT 'polls' as ctype, tid as cid, pollID as parent, comment, `date` as `cdate`, name as `user`, host_name as host, subject
    FROM `{$prefix}_pollcomments`
    WHERE `comment` <> ''
    ";
    }
    if (isset($tables["{$prefix}_gallery_comments"])) {
        $qry[] = "
    SELECT 'egallery' as ctype, cid, pid as parent, comment, `date` as `cdate`, name as `user`, '' as host, '' as subject
    FROM `{$prefix}_gallery_comments`
    WHERE `comment` <> ''
    ";
    }

    if (file_exists(PMX_MODULES_DIR . DS . $cpgname . '/include/config.inc.php')) {
        // das global wird benoetigt, falls gleichzeitig ein Coppermine Block
        // angezeigt wird, ab Coppermine 1.4.10.1 nicht mehr...
        global $CONFIG;
        if (!defined('IN_COPPERMINE')) {
            define('IN_COPPERMINE', true);
        }
        include(PMX_MODULES_DIR . DS . '' . $cpgname . '/include/config.inc.php');
        if (isset($tables["{$CONFIG['TABLE_PREFIX']}comments"])) {
            $qry[] = "
            SELECT 'copper' as ctype, msg_id as cid, pid as parent, msg_body as `comment`, msg_date as `cdate`, msg_author as `user`, msg_raw_ip as host, msg_hdr_ip as subject
            FROM `{$CONFIG['TABLE_PREFIX']}comments`
            WHERE `msg_body` <> ''
            ";
        }
    }

    if (!isset($qry)) {
        mxErrorScreen('no comments available...');
        return;
    }
    $qry = implode("\nUNION\n", $qry);

    $qry .= "
    ORDER BY `cdate` DESC
    LIMIT " . $offset . ", " . ($limit + 1) . ";
    ";
    $result = sql_query($qry);
    // $allcomments = sql_num_rows($result);
    $i = 0;
    $showmore = false;

    include("header.php");
    while ($row = sql_fetch_assoc($result)) {
        $i++;
        // wenn das Limit überschritten, dann "mehr" anzeigen und schleife beenden
        if ($i > $limit) {
            $showmore = true;
            break;
        }
        switch ($row['ctype']) {
            case 'downloads':
                $modname = _DOWNLOADS;
                $link = 'modules.php?name=Downloads&amp;op=comments&amp;lid=' . $row['parent'];
                $delete = adminUrl('Downloads', 'DelComment', 'lid=' . $row['parent'] . '&amp;rid=' . $row['cid'] . '');
                $edit = '';
                break;
            case 'links':
                $modname = _WEBLINKS;
                $link = 'modules.php?name=Web_Links&amp;op=comments&amp;lid=' . $row['parent'];
                $delete = adminUrl('Web_Links', 'DelComment', 'lid=' . $row['parent'] . '&amp;rid=' . $row['cid'] . '');
                $edit = '';
                break;
            case 'reviews':
                $modname = _REVIEWS;
                $link = 'modules.php?name=Reviews&amp;rop=showcontent&amp;id=' . $row['parent'];
                $delete = 'modules.php?name=Reviews&amp;rop=del_comment&amp;cid=' . $row['cid'] . '&amp;id=' . $row['parent'];
                $edit = '';
                break;
            case 'news':
                $modname = _NEWSARTICLES;
                $link = 'modules.php?name=News&amp;file=article&amp;sid=' . $row['parent'] . '#comments';
                $delete = adminUrl('News', 'RemoveComment', 'tid=' . $row['cid'] . '&amp;sid=' . $row['parent'] . '&amp;ok=0');
                $edit = '';
                break;
            case 'polls':
                $modname = _SURVEYS;
                $link = 'modules.php?name=Surveys&amp;pollID=' . $row['parent'];
                $delete = adminUrl('Surveys', 'RemoveComment', 'tid=' . $row['cid'] . '&amp;pollID=' . $row['parent'] . '&amp;ok=0');
                $edit = '';
                break;
            case 'egallery':
                $modname = _GALLERY;
                $link = 'modules.php?name=My_eGallery&amp;do=showpic&amp;pid=' . $row['parent'] . '&amp;orderby=dateD';
                $delete = 'modules.php?name=My_eGallery&do=deletecomment&amp;cid=' . $row['cid'] . '&amp;pid=' . $row['parent'] . '&amp;orderby=dateD';
                $edit = '';
                break;
            case 'copper':
                $modname = 'Coppermine';
                $link = ''; #'modules.php?name=Gallery&amp;act=displayimage&amp;pos=-4';
                $delete = 'modules.php?name=' . $cpgname . '&amp;act=delete&amp;msg_id=' . $row['cid'] . '&amp;what=comment';
                $edit = '';
                break;
            default:
                $modname = $row['ctype'];
                $link = '';
                $delete = '';
                $edit = '';
                break;
        }

        $func = array();
        if ($link) {
            $func[] = '<a class="btn btn-outline-secondary btn-sm" href="' . $link . '"><i class="fa fa-eye fa-lg m-t-2"></i></a>';
        }
        if ($delete) {
            $func[] = '<a class="btn btn-outline-secondary btn-sm" href="' . $delete . '"><i class="fa fa-trash fa-lg m-t-2"></i></a>';
        }
        if ($edit) {
            $func[] = '<a class="btn btn-outline-secondary btn-sm" href="' . $edit . '"><i class="fa fa-edit fa-lg m-t-2"></i></a>';
        }
        $func = implode(' ', $func);
        $class = ($class == '') ? ' class="alternate-a"' : '';
        $entry[] = '
        	<tr' . $class . ' style="vertical-align: top;">
       			<td>' . ($i + $offset) . '<br/></td>
       			<td><em>' . $row['cdate'] . '</em><br/><strong>' . $row['user'] . '</strong><br/><em>' . wordwrap($row['host'], 28, "\n", true) . '</em></td>
       			<td>' . $row['comment'] . '</td>
                <td><span class="badge badge-pill badge-primary">' . $modname . '</span></td>
       			<td>' . $func . '</td>
       		</tr>';
    }
    GraphicAdmin();
    title(_COMMENTSMOD);
    if (isset($entry)) {
        if ($offset >= $limit) {
            $pager[] = '<a href="' . adminUrl(PMX_MODULE, '', 's=' . ($offset - $limit), '') . '" title="' . ($offset - $limit) . ' - ' . ($offset) . '">' . mxCreateImage('images/previous.png', _GOPREV, 0, 'style="vertical-align: bottom;"') . ' ' . _GOPREV . '</a>';
        }
        if ($showmore) {
            $pager[] = '<a href="' . adminUrl(PMX_MODULE, '', 's=' . ($offset + $limit), '') . '" title="' . ($offset + $limit + 1) . ' - ' . ($offset + $limit + $limit) . '">' . _GONEXT . ' ' . mxCreateImage('images/next.png', _GONEXT, 0, 'style="vertical-align: bottom;"') . '</a>';
        }
        $pager = (isset($pager)) ? '' . implode('&nbsp;|&nbsp;', $pager) . '' : '';

        $entries = implode("\n", $entry);

        echo '
        <div class="card">
        <div class="card-header">'. _COMMENTSMOD .'</div>
        <div class="card-body">
        <div class="pagination align-right">
            ' . $pager . '
        </div>
        <table class="table">
        	<thead>
        		<tr>
        			<th>#</th>
        			<th>' . _COMMENTS . ' ' . _BY . '</th>
        			<th>' . _TEXT . '</th>
                    <th></th>
        			<th>' . _FUNCTIONS . '</th>
        		</tr>
        	</thead>
        	<tbody>
        	' . $entries . '
        	</tbody>
        </table>
        <div class="pagination align-right">
             ' . $pager . '
        </div>
        </div>
        </div>
';
    }
    include('footer.php');
}

switch ($op) {
    default:
        ListComments ();
        break;
}

?>