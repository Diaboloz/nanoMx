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

if (!mxGetAdminPref('radmintopic')) {
    mxErrorScreen("Access Denied");
    die();
}

$module_name = basename(dirname(dirname(__FILE__)));
mxGetLangfile($module_name);

function topicsmanager($xtopictext = '', $xtopicimage = '', $t_err = 0)
{
    global $prefix, $tipath;

    include("header.php");
    title(_TOPICSMANAGER);

    OpenTable();
    echo '<fieldset><legend>' . _CURRENTTOPICS . '</legend>';
    echo '<p class="align-center">' . _CLICK2EDIT . '</p>'
     . '<table width="100%" cellpadding="5" cellspacing="0"><tr>';
    $count = 0;
    $result = sql_query("SELECT topicid, topicimage, topictext FROM {$prefix}_topics ORDER BY topictext");
    while (list($topicid, $topicimage, $topictext) = sql_fetch_row($result)) {
        echo '<td align="center"><font class="content">'
         . '<a href="' . adminUrl(PMX_MODULE, 'edit', 'topicid=' . $topicid) . '">' . mxCreateImage($tipath . '/' . $topicimage, $topictext) . '<br />'
         . $topictext . '</a></font></td>';
        $count++;
        if ($count == 5) {
            echo '</tr><tr>';
            $count = 0;
        }
    }
    echo '</tr></table></fieldset>';
    CloseTable();

    echo '<br /><a name="Add"></a>';

    OpenTable();
    echo '<fieldset><legend>' . _ADDATOPIC . '</legend>';
    if ($t_err == 1) {
        echo '<center class="warning">' . _TOPICALLFIELDS1 . '</center><br />';
    }
    echo '<form action="' . adminUrl(PMX_MODULE) . '" method="post" name ="newTopic">'
     . sec_subform($topictext, $topicimage)
     . '<input type="hidden" name="op" value="' . PMX_MODULE . '/make" />'
     . '<input type="submit" value="' . _ADDTOPIC . '" />'
     . '</form></fieldset>';
    CloseTable();
    include("footer.php");
}

function topicedit($topicid, $topictext = '', $topicimage = '', $name = '', $url = '', $t_err = 0)
{
    global $prefix, $tipath;
    $img_delete = mxCreateImage("images/delete.gif", _DELETE, 0, 'title="' . _DELETE . '"');
    $img_edit = mxCreateImage("images/edit.gif", _EDIT, 0, 'title="' . _EDIT . '"');
    $name = (empty($name)) ? "" : mxEntityQuotes($name);
    $url = (empty($url)) ? "http://" : mxEntityQuotes($url);

    if ($t_err == 0) {
        $result = sql_query("SELECT topicid, topicimage, topictext FROM {$prefix}_topics WHERE topicid=$topicid");
        list($topicid, $topicimage, $topictext) = sql_fetch_row($result);
    }

    include("header.php");
    title(_TOPICSMANAGER);

    OpenTable();
    echo '<fieldset><legend>' . _EDITTOPIC . ': ' . $topictext . '</legend>';
    if ($t_err == 1) {
        echo '<center class="warning">' . _TOPICALLFIELDS1 . '</center><br />';
    } elseif ($t_err == 2) {
        echo '<center class="warning">' . _TOPICALLFIELDS2 . '</center><br />';
    }
    echo '<form action="' . adminUrl(PMX_MODULE) . '" method="post" name ="newTopic">'
     . sec_subform($topictext, $topicimage)
     . '<b>' . _ADDRELATED . ':</b><br />'
     . _SITENAME . ': <input type="text" name="name" value="' . $name . '" size="30" maxlength="30" /><br />'
     . _URL . ': <input type="text" name="url" value="' . $url . '" size="50" maxlength="200" /><br /><br />'
     . '<b>' . _ACTIVERELATEDLINKS . ':</b><br />'
     . '<table width="100%" border="0">';
    $res = sql_query("SELECT rid, name, url FROM {$prefix}_related WHERE tid=$topicid");
    $num = sql_num_rows($res);
    if ($num == 0) {
        echo '<tr><td><font class="tiny">' . _NORELATED . '</font></td></tr>';
    } while (list($rid, $name, $url) = sql_fetch_row($res)) {
        echo '<tr><td align="left"><font class="content"><strong><big>&middot;</big></strong>&nbsp;&nbsp;<a href="' . $url . '">' . $name . '</a></font></td>'
         . '<td align="center"><font class="content">'
         . '<a href="' . $url . '">' . $url . '</a></font></td>'
         . '<td align="right">&nbsp;<a href="' . adminUrl(PMX_MODULE, 'relatededit', 'tid=' . $topicid . '&amp;rid=' . $rid) . '">' . $img_edit . '</a>&nbsp;<a href="' . adminUrl(PMX_MODULE, 'relateddelete', 'tid=' . $topicid . '&amp;rid=' . $rid) . '">' . $img_delete . '</a></td></tr>';
    }
    echo '</table><br /><br />'
     . '<input type="hidden" name="topicid" value="' . $topicid . '" />'
     . '<input type="hidden" name="op" value="' . PMX_MODULE . '/change" />'
     . '<input type="submit" value="' . _SAVECHANGES . '" />'
     . '<font class="content">[&nbsp;<a href="' . adminUrl(PMX_MODULE, 'delete', 'topicid=' . $topicid) . '">' . _DELETE . '</a>&nbsp;]</font>'
     . '</form></fieldset>';
    CloseTable();
    include("footer.php");
}

function relatededit($tid, $rid, $name = '', $url = '', $t_err = 0)
{
    global $prefix, $tipath;
    include("header.php");
    title(_TOPICSMANAGER);

    if ($t_err == 0) {
        $result = sql_query("SELECT name, url FROM {$prefix}_related WHERE rid=$rid");
        list($name, $url) = sql_fetch_row($result);
    }
    $result2 = sql_query("SELECT topictext, topicimage FROM {$prefix}_topics WHERE topicid=$tid");
    list($topictext, $topicimage) = sql_fetch_row($result2);
    OpenTable();
    echo '<center>'
     . mxCreateImage($tipath . '/' . $topicimage, $topictext, 0, 'align="right"')
     . '<font class="option"><b>' . _EDITRELATED . '</b></font><br /><br />';
    if ($t_err == 2) {
        echo '<center class="warning">' . _TOPICALLFIELDS3 . '</center><br /><br />';
    }
    echo '<b>' . _TOPIC . ':</b> ' . $topictext . '</center><br /><br />'
     . '<form action="' . adminUrl(PMX_MODULE) . '" method="post">'
     . _SITENAME . ': <input type="text" name="name" value="' . mxEntityQuotes($name) . '" size="30" maxlength="30" /><br /><br />'
     . _URL . ': <input type="text" name="url" value="' . mxEntityQuotes($url) . '" size="60" maxlength="200" /><br /><br />'
     . '<input type="hidden" name="op" value="' . PMX_MODULE . '/relatedsave" />'
     . '<input type="hidden" name="tid" value="' . $tid . '" />'
     . '<input type="hidden" name="rid" value="' . $rid . '" />'
     . '<input type="submit" value="' . _SAVECHANGES . '" /> ' . _GOBACK
     . '</form>';
    CloseTable();
    include("footer.php");
}

function relatedsave($tid, $rid, $name, $url)
{
    global $prefix;

    if ((!empty($name) && ($url == 'http://' || $url == '')) || (empty($name) && $url != '' && (strlen($url) > 7))) {
        return relatededit($tid, $rid, $name, $url, 2);
    }
    sql_query("UPDATE {$prefix}_related SET name='$name', url='" . mx_urltohtml($url) . "' WHERE rid=$rid");
    return mxRedirect(adminUrl(PMX_MODULE, 'edit', "topicid=$tid"));
}

function relateddelete($tid, $rid, $ok = 0)
{
    global $prefix, $tipath;
    if ($ok == 1) {
        sql_query("DELETE FROM {$prefix}_related WHERE rid='$rid'");
        return mxRedirect(adminUrl(PMX_MODULE, 'edit', "topicid=$tid"));
    }
    include("header.php");
    title(_TOPICSMANAGER);

    $result = sql_query("SELECT name, url FROM {$prefix}_related WHERE rid=$rid");
    list($name, $url) = sql_fetch_row($result);
    $result2 = sql_query("SELECT topicimage, topictext FROM {$prefix}_topics WHERE topicid='$tid'");
    list($topicimage, $topictext) = sql_fetch_row($result2);
    OpenTable();
    echo '<center>' . mxCreateImage($tipath . '/' . $topicimage, $topictext) . '<br /><br />'
     . '<b>' . _DELETELINK . ': <i>' . $topictext . '</i></b><br /><br />'
     . '<b>' . _SITENAME . ':</b> ' . $name . '<br />'
     . '<b>' . _URL . ':</b> ' . $url . '<br /><br />'
     . _LINKDELSURE . '<br /><br />'
     . '[&nbsp;<a href="' . adminUrl(PMX_MODULE, 'edit', 'topicid=' . $tid) . '">' . _NO . '</a> | <a href="' . adminUrl(PMX_MODULE, 'relateddelete', 'tid=' . $tid . '&amp;rid=' . $rid . '&amp;ok=1') . '">' . _YES . '</a>&nbsp;]</center><br /><br />';
    CloseTable();
    include("footer.php");
}

function topicmake()
{
    global $prefix;

    if (empty($_POST['topictext'])) {
        return topicsmanager($_POST['topictext'], $_POST['topicimage'], 1);
    }

    $topicimage = mxAddSlashesForSQL($_POST['topicimage']);
    $topictext = mxAddSlashesForSQL($_POST['topictext']);
    $topicname = topic_create_dummyname($topictext);

    sql_query("INSERT INTO {$prefix}_topics
              (topicname, topicimage, topictext)
              VALUES ('$topicname', '$topicimage', '$topictext')");
    return mxRedirect(adminUrl(PMX_MODULE, '', '', '#Add'));
}

function topicchange()
{
    global $prefix;

    extract($_POST);

    switch (true) {
        case empty($topictext):
            return topicedit($topicid, $topictext, $topicimage, $name, $url, 1);
        case $name && (!$url || $url == "http://"):
        case !$name && $url && $url != "http://":
            return topicedit($topicid, $topictext, $topicimage, $name, $url, 2);
    }

    $topicimage = mxAddSlashesForSQL(($topicimage));
    $topictext = mxAddSlashesForSQL(($topictext));
    $topicname = topic_create_dummyname($topictext);
    $name = mxAddSlashesForSQL(($name));
    $url = mxAddSlashesForSQL(mx_urltohtml($url));
    sql_query("UPDATE {$prefix}_topics SET topicname='$topicname', topicimage='$topicimage', topictext='$topictext' WHERE topicid=$topicid");
    if ($name && $url) {
        sql_query("INSERT INTO {$prefix}_related VALUES (NULL, '$topicid','$name','$url')");
    }
    return mxRedirect(adminUrl(PMX_MODULE, 'edit', "topicid=$topicid"));
}

function topicdelete($topicid, $ok = 0)
{
    global $prefix, $tipath;
    if ($ok == 1) {
        sql_query("DELETE FROM {$prefix}_topics WHERE topicid=" . intval($topicid));
        sql_query("DELETE FROM {$prefix}_related WHERE tid=" . intval($topicid));

        $result = sql_query("SELECT sid FROM ${prefix}_stories WHERE topic=" . intval($topicid));
        $stories = array();
        while (list($sid) = sql_fetch_row($result)) {
            $stories[] = $sid;
        }

        if ($stories) {
            sql_query("DELETE FROM {$prefix}_comments WHERE sid IN (" . implode(',', $stories) . ")");
            sql_query("DELETE FROM ${prefix}_stories WHERE topic=" . intval($topicid));
        }

        return mxRedirect(adminUrl(PMX_MODULE));
    } else {
        // global $topicimage; // ToDo: Ist ueberfluessig !?!
        include("header.php");
        title(_TOPICSMANAGER);

        $result2 = sql_query("SELECT topicimage, topictext FROM {$prefix}_topics WHERE topicid='$topicid'");
        list($topicimage, $topictext) = sql_fetch_row($result2);
        OpenTableAl();
        echo '<center>' . mxCreateImage($tipath . '/' . $topicimage, $topictext) . '<br /><br />'
         . '<b>' . _DELETETOPIC . ': <i>' . $topictext . '</i></b><br /><br />'
         . _TOPICDELSURE . '<br />'
         . _TOPICDELSURE1 . '<br /><br />'
         . '[&nbsp;<a href="' . adminUrl(PMX_MODULE) . '">' . _NO . '</a> | <a href="' . adminUrl(PMX_MODULE, 'delete', 'topicid=' . $topicid . '&amp;ok=1') . '">' . _YES . '</a>&nbsp;]</center><br /><br />';
        CloseTableAl();
        include("footer.php");
    }
}

function topic_create_dummyname($topictext)
{
    $topicname = substr(strtolower(preg_replace('#\W#', '', $topictext)), 0, 20);
    return $topicname;
}

function topic_get_images()
{
    global $tipath;

    static $tlist;

    if (!isset($tlist)) {
        $tlist = array();
        $path = trim($tipath, ' ;,:./\\');
        $handle = opendir($path);
        while ($file = readdir($handle)) {
            if ($file == 'AllTopics.gif') {
                continue;
            }
            if (preg_match('#\.(gif|jpe?g|png)$#i', $file)) {
                $tlist[] = $file;
            }
        }
        closedir($handle);
        if ($tlist) {
            sort($tlist);
        }
    }

    return $tlist;
}

function topic_get_viewimage($topicimage = '')
{
    global $tipath;
    $path = trim($tipath, ' ;,:./\\');

    $allimages = topic_get_images();

    switch (true) {
        case !$allimages:
            $view_image = PMX_IMAGE_PATH . 'noimg.png';
            break;
        case !$topicimage:
        default:
            $view_image = $path . '/' . $allimages[0];
            break;
        case in_array($topicimage, $allimages):
            $view_image = $path . '/' . $topicimage;
            break;
    }
    return $view_image;
}

function sec_subform($topictext, $topicimage)
{
    global $tipath;

    $path = trim($tipath, ' ;,:./\\') . '/';

    $fb = load_class('Filebrowse');
    if ($fb->is_active()) {
        $fb->set_getback('name');
        $fb->set_root($path, _TOPICIMAGE);
        $fb->set_type('images');
        $fb->dialog();
    }

    $view_image = topic_get_viewimage($topicimage);
    ob_start();

    ?>

<label><?php echo _TOPICNAME ?> &nbsp;
<span class="tiny"> <?php echo _TOPICTEXT1 ?> </span></label><br />
<input type="text" name="topictext" size="40" maxlength="40" value="<?php echo mxEntityQuotes($topictext) ?>" required="required" /><br /><br />
<label><?php echo _TOPICIMAGE ?></label><br />
<input type="text" name="topicimage" id="topicimagefield" value="<?php echo $topicimage ?>" size="25" maxlength="100" required="required" /> &nbsp;
<?php if($fb->is_active()){ ?>
  <button id="rtvkhs"><?php echo _BROWSE ?></button> &nbsp;
<?php } //endif ?>
<img align="top" alt="topicimage" id="topicimagepic" src="<?php echo $view_image ?>" style="max-height:100px;max-width:100px;" />
<br /><br />

<script type="text/javascript">
  /*<![CDATA[*/

  $('#rtvkhs').click(function() {
    pmxfilemanager('topicimagefield', true);
    return false;
  });

  $('#topicimagefield').change(function() {
    $('#topicimagepic').attr('src','<?php echo $path ?>' + this.value);
  });

  /*]]>*/
</script>

    <?php
    $ret = ob_get_clean();
    return $ret;
}

$ok = !(empty($ok));

switch ($op) {
    case PMX_MODULE . "/edit":
        topicedit($topicid);
        break;

    case PMX_MODULE . "/make":
        topicmake();
        break;

    case PMX_MODULE . "/delete":
        topicdelete($topicid, $ok);
        break;

    case PMX_MODULE . "/change":
        topicchange();
        break;

    case PMX_MODULE . "/relatedsave":
        relatedsave($tid, $rid, $name, $url);
        break;

    case PMX_MODULE . "/relatededit":
        relatededit($tid, $rid);
        break;

    case PMX_MODULE . "/relateddelete":
        relateddelete($tid, $rid, $ok);
        break;

    default:
        topicsmanager();
        break;
}

?>