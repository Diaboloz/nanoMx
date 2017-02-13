<?php
/**
 * mxBoard, pragmaMx Module
 * Copyright by pragmaMx Developer Team - http://www.pragmamx.org
 *
 * $Author: PragmaMx $
 * $Revision: 248 $
 * $Date: 2016-11-01 13:59:37 +0100 (mar. 01 nov. 2016) $
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

if ($memliststatus != 'on') {
    return mxbExitMessage(_MEMLISTOFF, true);
}

$mxbnavigator->add(false, _TEXTMEMBERLIST);

switch (true) {
    case empty($_REQUEST['order']):
        $order = 'username';
        break;
    case $_REQUEST['order'] == 'regdate':
    case $_REQUEST['order'] == 'username':
    case $_REQUEST['order'] == 'postnum':
        $order = $_REQUEST['order'];
        break;
    default:
        $order = 'regdate';
}

$desc = (empty($_REQUEST['desc']) || $_REQUEST['desc'] == 'asc') ? 'asc' : 'desc';

if ((empty($page) || $page < 1)) {
    $start_limit = 0;
    $page = 1;
} else {
    $start_limit = ($page-1) * $memberperpage;
}

if (empty($srchmem)) {
    $query = sql_query("SELECT count(fm.uid) as number FROM $table_members as fm, ${user_prefix}_users AS u WHERE fm.username = u.uname and u.user_level=1 and u.user_regtime>0");
} else {
    $query = sql_query("SELECT count(uid) as number FROM $table_members WHERE username LIKE '%" . mxAddSlashesForSQL(substr($srchmem, 0, 25)) . "%'");
}

$row = sql_fetch_object($query);
$num = $row->number;
if ($num > $memberperpage) {
    $pages = ceil($num / $memberperpage);

    if ($page == $pages) {
        $to = $pages;
    } elseif ($page == $pages-1) {
        $to = $page + 1;
    } elseif ($page == $pages-2) {
        $to = $page + 2;
    } else {
        $to = $page + 3;
    }

    if ($page == 1 || $page == 2 || $page == 3) {
        $from = 1;
    } else {
        $from = $page-3;
    }

    $sort = ($desc == 'desc') ? '&amp;desc=desc' : '';
    $multipage = array();

    if ($page == 1) {
        $multipage[] = '<span class="arrows">&laquo;</span>';
    } else {
        $multipage[] = "<a href=\"" . MXB_BM_MEMBERSLIST1 . "page=1&amp;order={$order}{$sort}\"><span class=\"arrows\">&laquo;</span></a>";
    }
    for ($i = $from; $i <= $to; $i++) {
        if ($i == $page) {
            $multipage[] = "<span class=\"current\">$i</span>";
        } else {
            $multipage[] = "<a href=\"" . MXB_BM_MEMBERSLIST1 . "page=$i&amp;order={$order}{$sort}\">$i</a>";
        }
    }
    if ($page == $pages) {
        $multipage[] = '<span class="arrows">&raquo;</span>';
    } else {
        $multipage[] = "<a href=\"" . MXB_BM_MEMBERSLIST1 . "page=$pages&amp;order={$order}{$sort}\"><span class=\"arrows\">&raquo;</span></a>";
    }

    $multipage = '<span class="counter">' . _PAGE . ' <b>' . $page . '</b> ' . _MXB_OF . ' ' . $pages . '</span>' . implode($multipage);
} else {
    $multipage = '';
}

switch (true) {
    case $order == 'postnum' && $desc != 'desc':
        $sort1 = '&amp;desc=desc';
        $stit1 = _TRI_DESC;
        $simg1 = mxbGetImage('haut.png', _TRI_ASC, false, false, 'margin-left:.5em;');
        break;
    case $order == 'postnum':
        $sort1 = '';
        $stit1 = _TRI_ASC;
        $simg1 = mxbGetImage('bas.png', _TRI_DESC, false, false, 'margin-left:.5em;');
        break;
    default:
        $sort1 = '';
        $stit1 = _TRI_ASC;
        $simg1 = '';
}

switch (true) {
    case $order == 'regdate' && $desc != 'desc':
        $sort3 = '&amp;desc=desc';
        $stit3 = _TRI_DESC;
        $simg3 = mxbGetImage('haut.png', _TRI_ASC, false, false, 'margin-left:.5em;');
        break;
    case $order == 'regdate':
        $sort3 = '';
        $stit3 = _TRI_ASC;
        $simg3 = mxbGetImage('bas.png', _TRI_DESC, false, false, 'margin-left:.5em;');
        break;
    default:
        $sort3 = '';
        $stit3 = _TRI_ASC;
        $simg3 = '';
}

switch (true) {
    case $order == 'username' && $desc != 'desc':
        $sort2 = '&amp;desc=desc';
        $stit2 = _TRI_DESC;
        $simg2 = mxbGetImage('haut.png', _TRI_ASC, false, false, 'margin-left:.5em;');
        break;
    case $order == 'username':
        $sort2 = '';
        $stit2 = _TRI_ASC;
        $simg2 = mxbGetImage('bas.png', _TRI_DESC, false, false, 'margin-left:.5em;');
        break;
    default:
        $sort2 = '';
        $stit2 = _TRI_ASC;
        $simg2 = '';
}

?>
<div class="forumbg">
	<div class="inner"> 
  	<table class="table1">
				<thead>
					<tr>
						<th class="name"><a href="<?php echo MXB_BM_MEMBERSLIST1 ?>order=username<?php echo $sort2 ?>" title="<?php echo $stit2 ?>"><?php echo _TEXTUSERNAME ?></a><?php echo $simg2 ?></th>						
						<th class="infos"><a href="<?php echo MXB_BM_MEMBERSLIST1 ?>order=regdate<?php echo $sort3 ?>" title="<?php echo $stit3 ?>"><?php echo _TEXTREGISTERED ?></a><?php echo $simg3 ?></th>
						<th class="infos"><a href="<?php echo MXB_BM_MEMBERSLIST1 ?>order=postnum<?php echo $sort1 ?>" title="<?php echo $stit1 ?>"><?php echo _TEXTPOSTS ?></a><?php echo $simg1 ?></th>
						<th class="active" style="width:30%"><?php echo _TEXTLOCATION ?></th>
						<th class="infos"><?php echo _TEXTEMAIL ?></th>
						<th class="infos"><?php echo _TEXTSITE ?></th>
					</tr>
				</thead>						
			<tbody>



<?php

$past = time() - MX_SETINACTIVE_MINS ;

if (empty($srchmem)) {
    $where = 'WHERE u.user_level=1 and u.user_regtime>0';
} else {
    $where = "WHERE u.uname LIKE '%" . mxAddSlashesForSQL(substr($srchmem, 0, 25)) . "%' and u.user_level=1 and u.user_regtime>0";
}

$querymem = sql_query("SELECT fm.username, u.user_regtime as regdate, fm.postnum, u.email, u.url, fm.status, fm.customstatus, fm.u2u, u.uname, u.user_lastvisit, u.user_lastmod, u.user_stat, u.user_sexus, u.user_viewemail
                              FROM $table_members AS fm
                              LEFT JOIN ${user_prefix}_users AS u
                              ON fm.username = u.uname
                              " . $where . "
                              ORDER BY $order $desc
                              LIMIT " . intval($start_limit) . ", " . intval($memberperpage));
$i = 0;
$class = 'alternate-0';
while ($member = sql_fetch_object($querymem)) {
    if ($member->status != 'Administrator' && (empty($member->uname) || $member->user_stat != 1 || $member->regdate<1)) {
        // wenn nicht vorhanden, alle Datenbestände dieses Users angleichen
        if (empty($member->uname)) {
            mxbCleanUserdata($member->username);
        }
        continue;
    }

    if ($member->regdate) {
        $member->regdate = gmdate($dateformat, (int)$member->regdate + ($timeoffset * 3600));
    } else {
        $member->regdate = '&nbsp;';
    }

    if ($member->email && ($member->user_viewemail || $eBoardUser['isadmin'])) {
        $member->email = "<a href=\"mailto:" . mxPrepareToDisplay($member->email) . "\">" . mxbGetImage('email.png', _EB_EMAILUSER, false) . "</a>";
    } else {
        $member->email = '&nbsp;';
    }

    $member->url = mxCutHTTP($member->url);
    if ($member->url) {
        $member->url = "<a href=\"" . $member->url . "\" target=\"_blank\">" . mxbGetImage('site.png', _EB_VISITSITE, false) . "</a>";
    } else {
        $member->url = '&nbsp;';
    }

    if ((!empty($member->user_stat)) && ($member->user_lastvisit >= $past) && ($member->user_lastmod != 'logout') && ($member->user_lastmod != MXB_MODNAME)) {
        $onlinelocation = "<b>" . _TEXTONLINE . "</b>";
    } else if ((!empty($member->user_stat)) && ($member->user_lastvisit < $past || $member->user_lastmod == 'logout') && ($member->user_lastmod != MXB_MODNAME)) {
        $onlinelocation = "<i>" . _TEXTOFFLINE . "</i>";
    } else {
        $queryonline = sql_query("SELECT location FROM $table_whosonline WHERE username='" . substr($member->username, 0, 25) . "'");
        if ($isonline = sql_fetch_assoc($queryonline)) {
            $onlinelocation = "<b>" . _TEXTONLINE . "</b>";
            if ($advancedonlinestatus == 'on' || $eBoardUser['isadmin']) {
                $onlinelocation .= "&nbsp;(" . $isonline['location'] . ")";
            }
        } else {
            $onlinelocation = "<i>" . _TEXTOFFLINE . "</i>";
        }
    }

    if (empty($member->location)) {
        $member->location = "&nbsp;";
    }

 echo '
    		<tr class="' . $class . '">
     			<td class="name">' . mxb_link2profile($member->username) . '</td>
     			<td class="infos">' . $member->regdate . '</td>
     			<td class="infos">' . $member->postnum . '</td>
     			<td class="active">' . $onlinelocation . '</td>
     			<td class="infos">' . $member->email . '</td>
     			<td class="infos">' . $member->url . '</td>
    		</tr>';
    $class = ($class == 'alternate-0') ? 'alternate-1' : 'alternate-0';
}

?>
			</tbody>
		</table>
	</div>
</div>

<form method="post" action="<?php echo MXB_BM_MEMBERSLIST0;?>">
<span class="f12pix"><?php echo _TEXTSRCHUSR?></span>
<input type="text" size="15" name="srchmem">
<input type="submit" value="<?php echo _TEXTGO?>" />

<?php
echo
"<br/><b>" . _TEXTOR . "</b>&nbsp;" . _TEXTSORTBY . " "
 . "<a href=\"" . MXB_BM_MEMBERSLIST1 . "order=postnum&amp;desc=desc\">" . _TEXTPOSTNUM . "</a> - "
 . "<a href=\"" . MXB_BM_MEMBERSLIST1 . "order=username\">" . _TEXTALPHA . "</a> - "
 . "<a href=\"" . MXB_BM_MEMBERSLIST0 . "\">" . _TEXTREGDATE . "</a>
 </form>";

?>

<table cellspacing="0" cellpadding="0" border="0" align="center">
	<tr>
		<td class="multi" width="2%"><?php echo $multipage?></td>
		<td>&nbsp;</td>
	</tr>
</table>
<?php
include_once(MXB_BASEMODINCLUDE . 'footer.php');

?>
