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


    echo '<div class="card"><div class="card-header">' . _CURRENTTOPICS . '</div>';
    echo '<div class="card-body"><p class="alert alert-info">' . _CLICK2EDIT . '</p>'
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
    echo '</table></div></div>';


    echo '<br /><a name="Add"></a>';

    echo '<div class="card"><div class="card-header">' . _ADDATOPIC . '</div>';
    if ($t_err == 1) {
        echo '<center class="warning">' . _TOPICALLFIELDS1 . '</center><br />';
    }
    echo '<div class="card-body">
          <form action="' . adminUrl(PMX_MODULE) . '" method="post" name ="newTopic">'
     . sec_subform($topictext, $topicimage)
     . '<input type="hidden" name="op" value="' . PMX_MODULE . '/make" />'
     . '<div class="form-group"><br/><button class="btn btn-primary" type="submit"><i class="fa fa-plus"></i>  ' . _ADDTOPIC . '</button></div>'
     . '</form></div></div>';

    include("footer.php");
}

function topicedit($topicid, $topictext = '', $topicimage = '', $name = '', $url = '', $t_err = 0)
{
    global $prefix, $tipath;
    $name = (empty($name)) ? "" : mxEntityQuotes($name);
    $url = (empty($url)) ? "http://" : mxEntityQuotes($url);

    if ($t_err == 0) {
        $result = sql_query("SELECT topicid, topicimage, topictext FROM {$prefix}_topics WHERE topicid=$topicid");
        list($topicid, $topicimage, $topictext) = sql_fetch_row($result);
    }

    include("header.php");
    title(_TOPICSMANAGER);

    echo '<div class="card"><div class="card-header">' . _EDITTOPIC . ': ' . $topictext . '</div>';
    if ($t_err == 1) {
        echo '<div class="alert alert-warning>' . _TOPICALLFIELDS1 . '</div>';
    } elseif ($t_err == 2) {
        echo '<div class="alert alert-warning>' . _TOPICALLFIELDS2 . '</div>';
    }
    echo '<div class="card-body"><form action="' . adminUrl(PMX_MODULE) . '" method="post" name ="newTopic">'
     . sec_subform($topictext, $topicimage)
     . '<strong>' . _ADDRELATED . ':</strong><br />'
     . '<div class="form-group row"><label for="name" class="col-sm-2 col-form-label">' . _SITENAME . '</label><div class="col-sm-10"><input type="text" class="form-control" name="name" value="' . $name . '" maxlength="30" /></div></div>'
     . '<div class="form-group row"><label for="url" class="col-sm-2 col-form-label">' . _URL . '</label><div class="col-sm-10"><input type="text" class="form-control" name="url" value="' . $url . '" maxlength="200" /></div></div>'
     . '<b>' . _ACTIVERELATEDLINKS . ':</b><br />'
     . '<table class="table">';
    $res = sql_query("SELECT rid, name, url FROM {$prefix}_related WHERE tid=$topicid");
    $num = sql_num_rows($res);
    if ($num == 0) {
        echo '<tr><td><i>' . _NORELATED . '</i></td></tr>';
    } while (list($rid, $name, $url) = sql_fetch_row($res)) {
        echo '<tr><td align="left"><i class="fa fa-caret-right"></i> <a href="' . $url . '">' . $name . '</a></td>'
         . '<td class="text-center">'
         . '<a href="' . $url . '">' . $url . '</a></td>'
         . '<td class="text-right"><a class="btn btn-outline-secondary btn-sm" href="' . adminUrl(PMX_MODULE, 'relatededit', 'tid=' . $topicid . '&amp;rid=' . $rid) . '"><i class="fa fa-edit"></i></a>&nbsp;<a class="btn btn-outline-secondary btn-sm" href="' . adminUrl(PMX_MODULE, 'relateddelete', 'tid=' . $topicid . '&amp;rid=' . $rid) . '"><i class="fa fa-trash"></i></a></td></tr>';
    }
    echo '</table><br /><br />'
     . '<input type="hidden" name="topicid" value="' . $topicid . '" />'
     . '<input type="hidden" name="op" value="' . PMX_MODULE . '/change" />'
     . '<button class="btn btn-primary" type="submit"><i class="fa fa-check"></i> ' . _SAVECHANGES . '</button>'
     . '<a class="btn btn-danger" href="' . adminUrl(PMX_MODULE, 'delete', 'topicid=' . $topicid) . '"><i class="fa fa-trash"></i> ' . _DELETE . '</a>'
     . '</form></div></div>';
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
    echo '<div class="card"><div class="card-header">' . _EDITRELATED . ' : <strong>' . _TOPIC . ':</strong> ' . $topictext . '</div><div class="card-body">';
    if ($t_err == 2) {
        echo '<div class="alert alert-warning>' . _TOPICALLFIELDS3 . '</div>';
    }
    echo '<div class="text-center">' . mxCreateImage($tipath . '/' . $topicimage, $topictext, 0, 'align="right"') . '</div>'
     . '<form action="' . adminUrl(PMX_MODULE) . '" method="post">'
     . '<div class="form-group row"><label for="name" class="col-sm-2 col-form-label">' . _SITENAME . '</label><div class="col-sm-10"><input class="form-control" type="text" name="name" value="' . mxEntityQuotes($name) . '" size="30" maxlength="30" /></div></div>'
     . '<div class="form-group row"><label for="url" class="col-sm-2 col-form-label">' . _URL . '</label><div class="col-sm-10"><input class="form-control" type="text" name="url" value="' . mxEntityQuotes($url) . '" size="60" maxlength="200" /></div></div>'
     . '<input type="hidden" name="op" value="' . PMX_MODULE . '/relatedsave" />'
     . '<input type="hidden" name="tid" value="' . $tid . '" />'
     . '<input type="hidden" name="rid" value="' . $rid . '" />'
     . '<button class="btn btn-primary" type="submit"><i class="fa fa-check"></i> ' . _SAVECHANGES . '</button>  ' . _GOBACK
     . '</form></div></div>';
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

<div class="form-group">
<label for="topictext"><?php echo _TOPICTEXT1 ?></label>
<input class="form-control" type="text" name="topictext" id="topictext" size="40" maxlength="40" value="<?php echo mxEntityQuotes($topictext) ?>" required="required" />
</div>

<div class="form-row align-items-center">
    <div class="col-auto">
<input class="form-control mb-2 mb-sm-0" type="text" name="topicimage" id="topicimagefield" value="<?php echo $topicimage ?>" size="25" maxlength="100" required="required" placeholder="<?php echo _TOPICIMAGE ?>" /> 
</div>
<?php if($fb->is_active()){ ?>
  <button id="rtvkhs" class="btn btn-primary"><?php echo _BROWSE ?></button> &nbsp;
<?php } //endif ?>
<img align="top" alt="topicimage" id="topicimagepic" src="<?php echo $view_image ?>" style="max-height:100px;max-width:100px;" />
</div>

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