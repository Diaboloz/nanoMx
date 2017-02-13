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
 * Author: Olaf Herfurth / TerraProject  http://www.tecmu.de
 *
 * $Revision: 147 $
 * $Author: PragmaMx $
 * $Date: 2016-05-06 10:27:20 +0200 (Fr, 06. Mai 2016) $
 */

defined('mxMainFileLoaded') or die('access denied');
// Variablen definieren
$module_name = basename(__DIR__);

$wysiwyg = 0;
$wysiwyg_content = 0;
define("_MX_DOCS_MAIN", true);

mxGetLangfile($module_name);
$GLOBALS['plugins'] = 0;
global $prefix, $user_prefix, $doc, $doc_cfg, $module_name;

include_once(PMX_MODULES_DIR . DS . $module_name . DS . "includes/functions.php");
include_once(PMX_MODULES_DIR . DS . $module_name . DS . "includes/functions.edit.php");

if (empty($ok)) $ok = 0;
if (empty($id)) $id = 0;
if (empty($pic)) $pic = 0;
if (empty($size)) $size = 0;
if (empty($page)) $page = 0;

if (!isset($highlight)) $highlight = "";

if (!isset($act)) $act = "" ;
if (!isset($list)) $list = "" ;
if (!isset($keywords)) $keywords = "";
if (!isset($char)) $char = "" ;

if ($id == 0 && ($act != "search" and $act != "pageedit" and $act != "pagenew" and $act != "alphaindex" and $act != "newestpages")) $act = "";

$index = $doc_cfg['rightblocks'];
$plugins = 1;
switch ($act) {
    case "page":
        page($id, $page, $highlight);
        break;
    case "index":
        index($id, $page, $highlight);
        break;
    case "pageedit":
        page_edit($id);
        break;
    case "pagenew":
        page_new($id);
        break;
    case "pageprint":
        page_print($id);
        break;
    case "sendfriend":
        page_sendfriend($id);
        break;
    case "search":
        page_search($keywords);
        break;
    case "download":
        page_download($id);
        break;
    case "rate":
        add_rating($id, $size);
        exit;
        break;
    case "newestpages":
        page_newest();
        break;
    case "alphaindex":
        page_alphalist($char, $id);
		
    default:
        main($id);
        break;
}

function main($id = 0)
{
    global $prefix, $currentlang, $module_name, $doc_cfg, $doc;

    $cat = $doc;
    $doc_cfg = $cat->getConfig();
    $adminlink = page_adminlink($id, 0);

    pmxHeader::add_style("modules/$module_name/style/style.css");
    if (file_exists("modules/$module_name/style/custom.style.css")) pmxHeader::add_style("modules/$module_name/style/custom.style.css");
    // Template initialisieren
    $template = load_class('Template');
    $template->init_path(__FILE__);

    /* hier die Ausgabefelder angeben */
    $rootid = $cat->getBookRootID();
    $id = ($id == 0)?$rootid:intval($id);
    $output = array("id", "parent_id", "title", "date_created", "date_edit", "owner_id", "publish", "access", "language", 'text1', 'text2', 'info', 'link');
    $cat->setFilter("indextree", "publish", "=", "1");
    $cat->setFilter("indextree", "language", " IN ", "('ALL','" . $currentlang . "')");
	$cat->setFilter("indextree", "date_created", "<", time());
    $filter = $cat->getFilter("indextree");
    $liste = $cat->getRecordList(0, $output, "indextree");
    $count = $cat->contentcount();
    $breadcrump = $cat->getBreadcrump($id, false);
    $node = $cat->getRoot();

    $news = $cat->getRecords_New($doc_cfg['newscount'], $doc_cfg['changescount'], 0);
    $newscount = count($news);
    $lastchange = $cat->getRecords_LastChange($doc_cfg['newscount'], $doc_cfg['changescount'], $rootid);
    $lastchangecount = count($lastchange);

    $bloglist = $cat->getRecords($filter, "ORDER BY date_created DESC", $doc_cfg['changescount']);
    $blogcount = count($bloglist);

    $title = imgModul() . " " . $doc->getModuleTitle();
    $text = $doc->getModuleText();
    $pagetitle = $title;
    $maxlevel = $doc_cfg['indexwidth'];
    $x = $doc_cfg['tabscount'];
    $width = intval((100 - $x * 3) / $x);
    $alphaindex = $cat->getAlphaIndexString($id);
    /* Variablen an das Template uebergeben */
    $template->assign(compact('node',
            'title',
            'liste',
            'count',
            'module_name',
            "breadcrump",
            'rootid',
            'maxlevel',
            'adminlink',
            'doc_cfg',
            "news",
            'newscount',
            'lastchange',
            'lastchangecount',
            'width',
            'bloglist',
            'blogcount',
            'text',
            'doc',
            'alphaindex'
            ));

    pmxHeader::add_keywords($node['keywords']);
    pmxHeader::set_title($title);
   // pmxHeader::add(' <script>$(function() {$( "#indexmenu" ).menu();});</script>
	//				<style>.ui-menu { width: 150px; }</style>');

    /* Template ausgeben (echo) */
    include("header.php");
    switch ($doc_cfg['viewblog']) {
        case 1:
            $template->display('view.blogview.html');
            break;
        case 2:
            $template->display('view.listview.html');
            break;
        default:
            $template->display('view.overview.html');
            break;
    }
    // var_dump($cat->getAlphaIndex($id));
    include("footer.php");
}

function page($id = 0, $page = 0, $highlight = "")
{
    global $currentlang, $module_name, $doc_cfg, $pagetitle, $doc;

    $cat = $doc;
    $doc_cfg = $cat->getConfigPage($id);
    $user = $doc->getUser();
    $GLOBALS['plugins'] = $doc_cfg['linkmodules'];
    pmxHeader::add_jquery();
    pmxHeader::add_style("modules/$module_name/style/style.css");
    pmxHeader::add_script("modules/$module_name/includes/indexslide.js");

    $rootid = $cat->getRootID();
    $id = ($id == 0)?$cat->getRootID():intval($id);

    $cat->addview($id);

    $node = $cat->getRecord($id);
    if (!$cat->get_access_from_node($node) and $node['publish'] == 0) mxErrorScreen(_GROUPRESTRICTEDAREA);
    // if (!get_access($id) and $node['publish'] == 0 ) mxErrorScreen(_GROUPRESTRICTEDAREA);
    if ($node['id'] == 0) mxErrorScreen(_ERR_YOUBAD);

    $breadcrump = $cat->getBreadcrump($node['parent_id'], false);
    reset($breadcrump);
    $book = each($breadcrump);
    $bookid = $cat->getBookRootID($node['id']);
    $booktitle = ($node['level'] < 3)?$node['title']:$breadcrump[0]['title'];

    $title = (($doc_cfg['view_title'] == 1)?(($doc_cfg['link_title'] == 0)?imgModul() . " " . $doc->getModuleTitle():imgModul() . " " . "<a href=\"modules.php?name=$module_name\" title=\"\" >" . $doc->getModuleTitle() . "</a>"):"");

    $nodeupper = $cat->getRecordUpper($id, $bookid);
    $nodelower = $cat->getRecordLower($id, $bookid);
    /* hier die Ausgabefelder angeben */
    $output = array("id", "parent_id", "title", "date_created", "owner_id", "publish", "access", "language", 'text1', 'keywords', 'date_edit', 'info');

    if (!get_access($id)) {
        $cat->setFilter("tree", "publish", "=", "1");
        $filter = $cat->getFilter("tree");
    }

    $tid = ($doc_cfg['pageindex'] == 1)?$bookid:$id;
    $liste = $cat->content_get_tree($tid, $output, "tree");
    $count = $cat->contentcount();

    $pagetitle = $title . " - " . (($node['level'] < 3)?$node['title']:$breadcrump[0]['title'] . " - " . $node['title']);

    $node['keywords'] = str_replace(" ", ",", $node['keywords']);
    $node['keywords'] = (str_replace(",,", ",", $node['keywords'])); //htmlentities
    $highlight = str_replace(" ", "", $highlight);
    $highlights = explode(",", $highlight);
    $node['text1'] = (str_replace("&nbsp;", " ", $node['text1']));
    $text = mxPrepareToDisplay(getHighlightedString($node['text1'], $highlights, '<span class="highlight">', '</span>'));

    /* Attachments regeln */
    $node['attachments'] = unserialize($node['attachment']);
    if (!is_array($node['attachments']))$node['attachments'] = array();

    $node['attachment'] = array();
    $node['media'] = array();
    $fnr = 0;

    $attachments = (is_array($node['attachments']))?$node['attachments']:array();
    $attcount = count($attachments);
    if ($attcount) {
        foreach ($attachments as $file) {
            if (!array_key_exists('id', $file)) $file['id'] = $fnr;
            switch (strtolower($file['type'])) {
                case "image/png":
                case "image/jpg":
                case "image/jpeg":
                case "image/gif":
                case "audio/mpeg":
                case "audio/mpg":
                case "audio/mp3":
                case "video/mpeg":
                case "video/mpg":
                case "video/mp4":
                case "video/quicktime":
                case "application/x-shockwave-flash":
                    $file['filename'] = content_getAttachmentLink($id . "-" . $file['id'], $file);
                    $node['media'][] = $file;
                    break;
                default:
                    $file['filelen'] = $file['filesize']; //floatval(intval(filesize($file['filename']) / 1024));
                    $file['typeimg'] = content_getMimeImage($file['filename']);
                    $file['link'] = content_getAttachmentLink($id . "-" . $file['id'], $file);
                    $file['download'] = content_getAttachmentLink($id . "-" . $file['hash'] , $file, true);
                    $node['attachment'][] = $file;
                    break;
            }

            $fnr++;
        }
    }

    /* end attachments */

    /* links */

    if ($doc_cfg['viewbooklink'] == 1) $text = $cat->book_link ($text, (($doc_cfg['viewbookbase'] == 1)?0:$node['id']), $node['id'], $doc_cfg['link_count']);

    $newid = ($node['id'] == $bookid)?$node['id']:$node['id'];
    $adminlink = page_adminlink($id, $newid);
    $config = $doc_cfg;
    $config = $cat->getConfigPage($id);

    /* Ähnliche Artikel */
    $similar = array();
    $similarcount = 0;
    if ($doc_cfg['viewsimilar'] == 1) {
        if (trim($node['keywords']) != "") {
            $similar_filter = "";
            // $cat->setFilter("similar", "publish", "=", "1");
            $cat->setFilter("similar", "id", "<>", $node['id']);
            $cat->setFilter("similar", "language", " IN ", "('ALL','" . $cat->language . "')");
            $filter = $cat->getFilter("similar");
            $cat->setFilter("similar", "publish=1 AND match(s.title,s.keywords)", "AGAINST", "('" . str_replace(",", " ", $node['keywords']) . "' IN BOOLEAN MODE)");
            $extra = " AND match(s.title,s.keywords) AGAINST ('" . str_replace(",", " ", $node['keywords']) . "' IN BOOLEAN MODE)";
            $similar = $cat->getRecordList((($doc_cfg['viewbookbase'] == 1)?0:$bookid), "", "similar", 0, $doc_cfg['similarcount'], $extra);
            $similarcount = count($similar);
        }
    }

    $socialshare = "";

    $moduleinfo = array();
    $moduleinfo['link'] = "modules.php?name=$module_name&act=page&id=" . $node['id'];
    $moduleinfo['title'] = $node['title'];
    if ($doc_cfg['viewsocial'] == 1) $socialshare = add_Social_share("socialshareprivacy", $moduleinfo);

    $rating = "";
    if ($doc_cfg['viewrating'] == 1) $rating = output_Rating("rating", $node['id'], $node['rating']);

    /* kein blogview, wenn keine childnodes vorhanden */
    if ($node['childs'] == 0)$doc_cfg['viewblog'] = 0;

    $childs = array();
    $cat->setFilter("childs", "publish", "=", "1");
    $cat->setFilter("childs", "parent_id", "=", $node['id']);
    $childs = $cat->getRecordList($node['id'], $output, "childs");

    $width = intval((100 - max(1, $doc_cfg['tabscount']) * 3) / max(1, $doc_cfg['tabscount']));
    $doc_cfg['viewblog'] = (count($childs) > 0)?$doc_cfg['viewblog']:0;

    $atid = ($config['viewbookbase'] == 1)?$bookid:$tid;
    $alphaindex = $cat->getAlphaIndexString($atid);

    /* jetzt noch alle Meta-Tags generieren */
    $info = unserialize($node['info']);

    foreach($info as $key => $value) {
        $info[$key] = htmlspecialchars(strip_tags($value), ENT_QUOTES, 'utf-8', false);
    }

    pmxHeader::add_keywords($node['keywords']);
    pmxHeader::set_title($pagetitle);
    pmxHeader::set_meta('canonical', $info['canonical']);
    pmxHeader::set_meta('robots', $info['robots']);
    pmxHeader::set_meta('alternate', $info['alternate']);
    pmxHeader::set_meta('revisit', $info['revisit']);
    pmxHeader::set_meta('author', $info['author']);
    pmxHeader::set_meta('description', $info['description']);

    if (array_key_exists('title', $info)) {
        if (trim($info['title']) != "") pmxHeader::set_meta('title', $info['title']);
    }
    $ullist = "";
    if ($config['viewindex'] == 1) {
        pmxHeader::add_jquery("jquery.treeview.js");
        pmxHeader::add_script_code("$(document).ready(function(){
				$('#navigation_" . $module_name . "').treeview({
					persist: \"location\",
					collapsed: true,
					unique: true
				});
			});");

        $ullist = "<div id=\"navigation_" . $module_name . "\" class=\"treeview\">" . $cat->content_get_html($tid, $id, "", false) . "</div>";
    }

    $com = load_class('Comments');
    $commentslist = $com->getCommentsHTML($id);
    $commentsform = $com->getCommentsForm($id);
    /*
     * Template
     */
    // Template initialisieren
    $template = load_class('Template');
    $template->init_path(__FILE__);
    /* Variablen an das Template uebergeben */
    $template->assign(
        compact('node',
            'title',
            'text',
            'liste',
            'count',
            'module_name',
            "breadcrump",
            'rootid',
            'maxlevel',
            'nodeupper',
            'nodelower',
            'adminlink',
            'doc_cfg',
            'config',
            'booktitle',
            'bookid',
            'cat',
            'newrecords',
            'similar',
            'similarcount',
            'width',
            'socialshare',
            'rating',
            'childs',
            'width',
            'doc',
            'alphaindex',
            'ullist',
            'commentslist',
            'commentsform'

            ));

    /* Template ausgeben (echo) */

    include("header.php");

    switch ($doc_cfg['viewblog']) {
        case 1:
            $template->display('view.pageblog.html');
            break;
        case 2:
            $template->display('view.pagelist.html');
            break;
        default:
            $template->display('view.page.html');
            // $template->display('view.comments.html');
            break;
    }
    include("footer.php");
}
function index($id, $page = 0, $highlight = "")
{
    global $currentlang, $module_name, $doc_cfg, $pagetitle, $doc;

    $cat = $doc;
    $doc_cfg = $cat->getConfig();
    $user = $doc->getUser();
    $ullist = "";

    pmxHeader::add_style("modules/$module_name/style/style.css");

    $rootid = $cat->getRootID();
    $id = ($id == 0)?$cat->getRootID():intval($id);

    $node = $cat->getRecord($id);

    if ($node['id'] == 0) mxErrorScreen(_ERR_YOUBAD);

    $title = imgModul() . " " . (($doc_cfg['view_title'] == 1)?(($doc_cfg['link_title'] == 0)?$doc->getModuleTitle():"<a href=\"modules.php?name=$module_name\" title=\"\" >" . $doc->getModuleTitle() . "</a>"):"");

    $breadcrump = $cat->getBreadcrump($node['parent_id'], false);
    reset($breadcrump);
    $book = each($breadcrump);
    $bookid = ($node['level'] < 3)?$node['id']:$breadcrump[0]['id'];
    $booktitle = ($node['level'] < 3)?$node['title']:$breadcrump[0]['title'];

    $nodeupper = $cat->getRecordUpper($id, $bookid);
    $nodelower = $cat->getRecordLower($id, $bookid);
    /* hier die Ausgabefelder angeben */
    $output = array("id", "parent_id", "title", "date_created", "owner_id", "publish", "access", "language", 'text1', 'keywords', 'date_edit', 'info');
    $cat->setFilter("tree", "publish", "=", "1");
    $filter = $cat->getFilter("tree");

    $tid = ($doc_cfg['pageindex'] == 1)?$bookid:$id;
    $liste = $cat->content_get_tree($tid, $output, "tree");
    $count = $cat->contentcount();

    $pagetitle = $title . " - " . (($node['level'] < 3)?$node['title']:$breadcrump[0]['title'] . " - " . $node['title']);

    $node['keywords'] = str_replace(" ", ",", $node['keywords']);
    $node['keywords'] = htmlentities(str_replace(",,", ",", $node['keywords']));

    $highlight = str_replace(" ", "", $highlight);
    $highlights = explode(",", $highlight);
    $node['text1'] = getHighlightedString($node['text1'], $highlights, '<span class="highlight">', '</span>');
    pmxHeader::add_keywords($node['keywords']);
    $alphaindex = $cat->getAlphaIndexString($id);

    if ($doc_cfg['viewindex'] == 1) {
        pmxHeader::add_jquery("jquery.treeview.js");
        pmxHeader::add_script_code("$(document).ready(function(){
				$('#navigation_" . $module_name . "').treeview({
					persist: \"location\",
					collapsed: true,
					unique: true
				});
			});");
        // $ullist=$cat->content_get_html($id,0,"id=\"navigation\"");
        $ullist = "<div id=\"navigation_" . $module_name . "\" class=\"treeview-famfamfam\">" . $cat->content_get_html($id, 0, "", true, true) . "</div>";
    }

    $adminlink = page_adminlink($id);

    $maxlevel = 99;
    $config = $doc_cfg;
    /*
     * Template
     */
    // Template initialisieren
    $template = load_class('Template');
    $template->init_path(__FILE__);
    /* Variablen an das Template uebergeben */
    $template->assign(compact('node',
            'title',
            'liste',
            'count',
            'module_name',
            "breadcrump",
            'rootid',
            'maxlevel',
            'nodeupper',
            'nodelower',
            'adminlink',
            'doc_cfg',
            'config',
            'booktitle',
            'bookid',
            'cat',
            'alphaindex',
            'ullist'
            ));

    /* Template ausgeben (echo) */
    include("header.php");
    $template->display('view.index.html');
    include("footer.php");
}

function page_new($id = 0)
{
    global $doc_cfg, $module_name, $doc;
    $GLOBALS['plugins'] = 0;

    $doc_cfg = $doc->getConfig();
    if ($id == 0) {
        $node = $doc->getRecord_LastEdit();
        $id = $node['parent_id'];
    }
    page_edit($id , true);
}

function page_edit($id = 0, $new = false)
{
    global $doc_cfg, $module_name, $doc;
    $GLOBALS['plugins'] = 0;

    $user = $doc->getUser();

    $returnflag = false;
    $err = false;
    $cat = $doc;

    /* unerlaubte zugriffe blockieren */
    if ($new == false and $id != 0) {
        $doc_cfg = $cat->getConfigPage($id);
        if (!get_access($id)) mxErrorScreen(_GROUPRESTRICTEDAREA, _ACCESSDENIED);
    }
    /* jetzt configuration wieder zurücksetzen */
    $doc_cfg = $cat->getConfig();

    /* titel generieren */
    $info2 = imgModul() . " " . $doc->getModuleTitle() . " - " . _EDIT;

    switch (pmxAdminForm::CheckButton()) {
        case "save":
            $returnflag = true;
        case "accept":
            $content = array();
            $new = intval($_POST['new']);
            $sid = intval($_POST['sectionid']);
            if ($new == 1) {
                $content = $cat->getRecordDefault();
                $id = $cat->addRecord($sid, $content);
            }

            $_POST['info']['alternate'] = mxAddSlashesForSQL(trim(htmlspecialchars(strip_tags($_POST['info']['alternate']))));
            foreach($_POST as $key => $value) {
                if (is_array($value)) {
                    $content[$key] = serialize(mxAddSlashesForSQL($value));
                } else {
                    $content[$key] = mxAddSlashesForSQL($value);
                }
            }
            /* fehler-check */
            $content['title'] = mxAddSlashesForSQL(trim(strip_tags($content['title'])));
            $content['alias'] = mxAddSlashesForSQL($cat->check_alias($content['alias']));
            $content['keywords'] = mxAddSlashesForSQL(trim(strip_tags($content['keywords'])));

            if ($content['title'] == "") {
                $err = true;
            }
            /* uploads*/
            $uploadfiles = array();
            if (isset($_POST['attachments'])) {
                foreach ($_POST['attachments'] as $cid) {
                    if ($cid['delete'] == 1) {
                        if (file_exists($cid['filename'])) unlink($cid['filename']);
                    } else {
                        $uploadfiles[] = $cid;
                    }
                }
            }
            if (isset($_FILES['attachment'])) {
                $maxfilesupload = count($_FILES['attachment']['name']);
                $temppath = $doc_cfg['attpath'];

                foreach ($_FILES['attachment']['error'] as $cid => $error) {
                    $ok = $_FILES['attachment']['error'][$cid];
                    $file = array();
                    $file['error'] = false;
                    if ($error == UPLOAD_ERR_OK) {
                        $uploadsize = $_FILES['attachment']['size'][$cid];
                        $fname = $_FILES['attachment']['name'][$cid];
                        $extension2 = explode('.', $_FILES['attachment']['name'][$cid]);
                        $extension = strtolower($extension2[count($extension2)-1]);
                        $type = $_FILES['attachment']['type'][$cid];
                        $newname = $id . "-" . string_to_filename($_FILES['attachment']['name'][$cid]); //hash("haval160,4", time() . $_FILES['attachment']['name'][$cid]) . "." . $extension;
                        move_uploaded_file($_FILES['attachment']['tmp_name'][$cid], $temppath . "/" . $newname);
                        $file['error'] = "";
                        $file['filename'] = trim($temppath . "/" . $newname);
                        $file['type'] = $type;
                        $file['hash'] = md5($newname);
                        $file['name'] = $_FILES['attachment']['name'][$cid];
                        $file['title'] = strip_tags($_FILES['attachment']['name'][$cid]);
                        $file['filesize'] = intval(filesize($temppath . "/" . $newname) / 10.24) / 100;
                        $file['id'] = count($uploadfiles) + 1;
                        if ($file['filesize'] > $doc_cfg['attmaxsize']) {
                            $file['error'] = _DOCS_ERR_FILESIZE;
                            unlink($file['filename']);
                        }
                        $uploadfiles[] = $file;
                    } else {
                        $file['error'] = $error;
                        $file['filename'] = $_FILES['attachment']['name'][$cid];
                        $file['filesize'] = intval($_FILES['attachment']['size'][$cid] / 10.24) / 100;
                        // unlink($file['filename']);
                    }
                }
            }
            /* uploads ende */

            $content['attachment'] = serialize($uploadfiles);

            $content['publish'] = (MX_IS_ADMIN)?$_POST['publish']:(min($doc_cfg['editorrights'], $_POST['publish']));
            if (!$err) {
                if ($new == 1) {
                    $content['owner_id'] = intval($_POST['edit_uid']);
                    $content['owner_name'] = mxAddSlashesForSQL($_POST['edit_uname']);
                    $content['status'] = ($content['publish'] == 0 && !MX_IS_ADMIN)?1:0; //
                    $content['edit_uid'] = "";
                    $content['edit_uname'] = "";
                    $cat->updateRecord ($id, $content);
                    $new = false;
                } else {
                    $cat->updateRecord ($id, $content);
                }
                unset($content);
                if ($returnflag) mxRedirect("modules.php?name=$module_name&act=page&id=$id", _CHANGESAREOK, 1);
            }
            if ($err) mxRedirect("modules.php?name=$module_name&act=page" . (($new == 1)?"new":"edit") . "&id=$id", _CHANGESNOTOK . " <br> " . _ERROROCCURS . "<br>" . _ERRNOTITLE, 3);
            break;
        default:
            break;
    }
    // id=0 then New
    /* hier die Ausgabefelder angeben */
    $output = array("id", "parent_id", "title", "date_created", "owner_id", "publish", "access", "text1", 'info');

    if ($new == true) {
        $content = array();
        $content['id'] = 0;
        $book = $cat->getBookRoot($id);
        if ($book['id'] == 0)$book = $cat->getRecord($id);
        $section = $cat->getRecord($id);
        if ($section['id'] == 0) mxErrorScreen(_ERR_YOUBAD);
        $content = $cat->getRecordDefault();
        $config = array_merge($doc_cfg, unserialize($content['config']));
        $content['attachment'] = array();
        $info = unserialize($content['info']);
        $id = 0;
        $snew = 1;
    } else {
        $snew = 0;
        $content = $cat->getRecord($id);
        $book = $cat->getBookRoot($id);
        $section = $cat->getParentRecord($id) ;
        if ($content['id'] == 0) mxErrorScreen(_ERR_YOUBAD);
        $content['attachment'] = unserialize($content['attachment']);
        $config = array_merge($doc_cfg, unserialize($content['config']));
        $info = unserialize($content['info']);
    }

    /* hier die Ausgabefelder angeben */
    $sqlfilter = "";
    $output = array("id", "parent_id", "title", "date_created", "owner_id", "publish", "access", "text1");
    $sqlfilter .= " AND s.leftID NOT BETWEEN " . $book['leftID'] . " AND " . $book['rightID'];
    $targetcontent = $cat->getRecordList(0, $output, " " . $sqlfilter . " ");
    foreach($targetcontent as $node) {
        $key = str_repeat("&nbsp;&nbsp;&nbsp;", $node['level']-1) . $node['title'];
        if ($node['parent_id'] == $book['id']) {
            $key = "<b>" . $key . "</b>";
        }
        $sectionselect[$key] = $node['id'];
        unset($key);
    }

    $sectionarraytemp = $cat->getRecordList($book['id']);
    foreach ($sectionarraytemp as $value) {
        $tabs = str_repeat("&nbsp;", ($value['level']-1) * 2);
        $sectionarray[$tabs . "&raquo;" . $value['title']] = $value['id'];
    }
    $tb = load_class('AdminForm');;
    $tb->__set('target_url', "modules.php?name=" . $module_name . "&amp;act=pageedit");
    $tb->__set("tb_text", "");
    $tb->__set("tb_direction", 'right');
    $tb->__set("infobutton", true);
    $tb->__set("tb_pic_heigth", 25);

    $tb->addToolbar("accept");
    $tb->addToolbar("save");
    $tb->addToolbarLink("cancel", "modules.php?name=$module_name&amp;act=page&amp;id=" . $id);
    /* Form elements */
    $tb->addFieldSet("head", _CONTENT, "", false);
    $tb->addFieldSet("content", _DOCS_CONTENT_EDIT, "", false);
    $tb->addFieldSet("attachments", _DOCS_ATTACHMENTS, _DOCS_INFO_FILESIZE . " " . $doc_cfg['attmaxsize'], false);
    $tb->addFieldSet("extended", _DOCS_CONF_PAGE, "", true, array("width" => "50%"));
    $tb->addFieldSet("metapage", _DOCS_META_PAGE, "", true, array("width" => "49%"));

    $tb->add("", "html", "<span class=\"tiny\">" . _DOCS_LASTCHANGE . " : " . mx_strftime(_DATESTRING . " %H:%M:%S", $content['date_created']) . "</span>"
         . "<span style=\"float:right;margin:5px;\" ><a href=\"admin.php?op="
         . $module_name . "&amp;act=getlog&amp;id=" . $content['id']
         . "&amp;iframe=true&amp;width=80%&amp;height=80%\" title=\"" . _DOCS_VIEW_LOG . "\" rel=\"pretty\">"
         . "<img src=\"images/adminform/page.png\" width=\"26\" height=\"26\" alt=\"" . _DOCS_HISTORY . "\" /></a>"
         . "<a href=\"modules.php?name=" . $module_name . "&amp;act=page&amp;id=" . $content['id'] . "&amp;iframe=true&amp;width=80%&amp;height=80%\" rel=\"pretty\" title=\"" . _PREVIEW . "\" >"
         . "<img src=\"images/adminform/preview.png\" width=\"26\" height=\"26\" alt=\"" . _PREVIEW . "\" /></a>"
         . "</span>");
    $tb->add("", "input", "lastchange", mx_strftime(_DATESTRING . " %H:%M:%S", $content['date_created']), _DOCS_LASTCHANGE, "", 40, "readonly=\"readonly\"");
    // $tb->add("", "input", "booktitle", $book['title'], _DOCU, "", 60, "readonly=\"readonly\"");
    // $tb->add("", "input", "booksection", $section['title'], _DOCS_SECTION, "", 60, "readonly=\"readonly\"");
    $tb->add("", "hidden", "new", $snew);

    $tb->add("", "hidden", "id", $content['id']);
    // $tb->add("", "hidden", "bookid", $book['id']);
    // $tb->add("", "hidden", "sectionid", $section['id']);
    $tb->add("", "select", "sectionid", $section['id'], _DOCS_SECTION, "", 1, $sectionselect);
    $tb->add("", "hidden", "edit_uid", $user['uid']);
    $tb->add("", "hidden", "edit_uname", $user['uname']);
    $tb->add("", "hidden", "act", "pageedit");
    $tb->add("head", "input", "title", $content['title'], _DOCS_CONTENT_TITLE . " " . _REQUIRED, _DOCS_CONTENT_TITLE_TEXT, 50, "required=\"required\"");
    $tb->add("head", "input", "alias", $content['alias'], _DOCS_ALIAS, _DOCS_ALIAS_TEXT, 50);
    $tb->add("head", "input", "owner_name", $content['owner_name'], _DOCS_OWNER, _DOCS_OWNER, 50);
    $tb->add("", "hidden", "publish", 0);
    if ($new == 0 and $content['parent_id'] <> $cat->getRootID($id)) {
        if ($doc_cfg['editorrights'] == 1 or MX_IS_ADMIN) $tb->add("head", "yesno", "publish", $content['publish'], _DOCS_PUBLISHED);
    } else {
        if (MX_IS_ADMIN) $tb->add("head", "yesno", "publish", $content['publish'], _DOCS_PUBLISHED);
    }
    $tb->add("head", "yesno", "position", $content['position'], _DOCS_STARTPAGE_ON, _DOCS_STARTPAGE_TEXT);

    $tb->add("content", "editor", "text1", $content['text1'], "", "", 300, true, true);

    $attachments = (is_array($content['attachment']))?$content['attachment']:array();
    $attcount = count($attachments);
    $attmaxcount = $doc_cfg['attcount'];
    if ($attcount) {
        $i = 0;
        $alist = "";
        foreach ($attachments as $file) {
            if ($file['error']) {
                $tb->add("attachments", "output", "<span class=\"warning\">" . $file['name'] . " - " . $file['error'] . " = " . $file['filesize'] . " kByte </span>");
                $i++;
            } else {
                $tb->add("attachments", "hidden", "attachments[$i][delete]", "0", _DELETE, _DOCS_ATTACH_DELETE);
                $tb->add("attachments", "hidden", "attachments[$i][id]", $file['id']);
                $tb->add("attachments", "hidden", "attachments[$i][filename]", $file['filename']);
                $tb->add("attachments", "hidden", "attachments[$i][type]", $file['type']);
                $tb->add("attachments", "hidden", "attachments[$i][hash]", $file['hash']);
                $tb->add("attachments", "hidden", "attachments[$i][name]", $file['name']);
                $tb->add("attachments", "hidden", "attachments[$i][filesize]", $file['filesize']);
                $tb->add("attachments", "hidden", "attachments[$i][title]", $file['title']);
                $tb->add("attachments", "hidden", "attachments[$i][error]", 0);
                $alist .= "<tr><td >" . substr($file['name'], 0, 20) . "</td>";
                $alist .= "<td>" . substr($file['type'], 0, 20) . "</td>";
                $alist .= "<td ><input type=\"text\" name=\"attachments[$i][title]\" value=\"" . $file['title'] . "\" size=\"40\" /></td>";
                $alist .= "<td >" . $file['filesize'] . " kByte</td>";
                $alist .= "<td><input type=\"checkbox\" name=\"attachments[$i][delete]\" value=\"1\" /></td></tr>";
                $i++;
            }
        }
        $tlist = "<table class=\"list\" width=\"100%\" ><thead><tr><th width=\"150\">" . _DOCS_FILENAME . "</th>";
        $tlist .= "<th>" . _DOCS_FILETYPE . "</th>";
        $tlist .= "<th>" . _DOCS_FILETITLE . "</th>";
        $tlist .= "<th width=\"100\">" . _DOCS_FILESIZE . "</th>";
        $tlist .= "<th width=\"60\">" . _DELETE . "</th></tr></thead><tbody>";
        $tlist .= $alist . "</tbody></table>";
        $tb->add("attachments", "output", $tlist);
    }
    for ($i = 0;$i < ($attmaxcount - $attcount);$i++) {
        $tb->add("attachments", "file", "attachment", "", _DOCS_ATTACHMENTS);
    }
    content_getPageConfigForm($tb, $config);
    content_getPageMetaForm($tb, $config, $content, $info) ;

    /* Form schliessen*/

    $form = $tb->Show();
    /*
     * Template
     */
    $template = load_class('Template');
    $template->init_path(__FILE__);

    /* Variablen an das Template uebergeben */
    $template->assign(compact('toolbar', 'info2', 'form', 'module_name'));

    include('header.php');
    /* Template ausgeben (echo) */
    $template->display('admin/admin.contentedit.html');
    // printFooter();
    include('footer.php');
}

function page_search ($keywords = "")
{
    global $doc_cfg, $module_name, $doc;

    $user = $doc->getUser();
    $cat = $doc;

    $doc_cfg = $cat->getConfig();
    $GLOBALS['plugins'] = 0;

    $info2 = imgModul() . " " . $doc->getModuleTitle() . " - " . _SEARCH;

    $title = $info2;

    $nodes = $cat->getPages($keywords, $doc_cfg['searchcount']);

    /*
     * Template
     */
    $template = load_class('Template');
    $template->init_path(__FILE__);

    /* Variablen an das Template uebergeben */
    $template->assign(compact('title', 'nodes', 'module_name', 'doc_cfg', 'keywords', 'doc'));

    include('header.php');
    /* Template ausgeben (echo) */
    $template->display('view.search.html');
    include("footer.php");
}

function page_newest ()
{
    global $doc_cfg, $module_name, $doc;

    $user = $doc->getUser();
    $cat = $doc;

    $doc_cfg = $cat->getConfig();
    $GLOBALS['plugins'] = 0;

    $info2 = $doc->getModuleTitle() . " - " . _DOCS_LASTCHANGES;

    $title = (($doc_cfg['view_title'] == 1)?(($doc_cfg['link_title'] == 0)?imgModul() . " " . $doc->getModuleTitle():imgModul() . " " . "<a href=\"modules.php?name=$module_name\" title=\"\" >" . $info2 . "</a>"):"");

    $nodes = $cat->getRecords_LastChange(999, $doc_cfg['searchcount']);

    /*
     * Template
     */
    $template = load_class('Template');
    $template->init_path(__FILE__);

    /* Variablen an das Template uebergeben */
    $template->assign(compact('title', 'nodes', 'module_name', 'doc_cfg', 'doc'));

    include('header.php');
    /* Template ausgeben (echo) */
    $template->display('view.lastchanged.html');
    include("footer.php");
}

function page_download($id)
{
    global $doc_cfg, $module_name, $doc;
    $file_id = explode("-", $id);
    $cid = $file_id[0];
    $fnr = trim($file_id[1]);
    $file = null;
    // $cat = $doc;
    // $page = $cat->getPage($cid);
    $fileinfo = $doc->getPageAttachments($cid); //unserialize($page['attachment']);
    foreach($fileinfo as $dfile) {
        if ($dfile['hash'] == $fnr) {
            content_getAttachmentDownload($dfile); //$file = $dfile;
            die();
        }
        $file = $dfile['filename'];
    }
    // if (is_array($file)) content_getAttachmentDownload($file);
    mxErrorScreen(_MODULEFILENOTFOUND . " - " . $file);
    return;
}
function page_adminlink($id, $parentid = 0)
{
    global $doc_cfg, $module_name, $doc;
    $bookid = intval($parentid);
    $cat = $doc;
    $adminlink = "";
    $access = get_access($id);

    if ($parentid == 0) $access = false;

    /* graphiclinks */
    $img_edit = "<img src=\"" . PMX_MODULES_PATH . $module_name . "/style/images/edit.png\" title=\"" . _DOCS_PAGE_EDIT . "\" style=\"margin-right:5px;\" />";
    $img_new = "<img src=\"" . PMX_MODULES_PATH . $module_name . "/style/images/new.png\" title=\"" . _DOCS_PAGE_NEW . "\" style=\"margin-right:5px;\" />";
    $img_admin = "<img src=\"" . PMX_MODULES_PATH . $module_name . "/style/images/administration.png\" title=\"" . _DOCS_ADMIN_PANEEL . "\" style=\"margin-right:5px;\" />";

    if ($access) {
        $adminlink .= (($id == 0 or ($cat->getBookRootID($id) == $id)))?"":"<a href=\"modules.php?name=$module_name&amp;act=pageedit&amp;id=$id\" title=\"" . _DOCS_PAGE_EDIT . "\">" . $img_edit . "</a>";
        $adminlink .= ($parentid > 0)?"<a href=\"modules.php?name=$module_name&amp;act=pagenew&amp;id=" . $bookid . "\" title=\"" . _DOCS_PAGE_NEW . "\">" . $img_new . "</a>":"";
    }
    $adminlink .= (MX_IS_ADMIN)?"<a href=\"admin.php?op=$module_name\" title=\"" . _DOCS_ADMIN_PANEEL . "\">" . $img_admin . "</a>":"";
    unset($cat);
    return $adminlink;
}

function page_print($id)
{
    global $doc_cfg, $module_name, $site_logo, $sitename, $doc;

    $GLOBALS['plugins'] = 0;

    $target = 'modules.php?name=' . $module_name . '&act=page&id=' . intval($id);
    if (!MX_IS_ADMIN && (empty($_SERVER['HTTP_REFERER']) || strpos($_SERVER['HTTP_REFERER'], PMX_HOME_URL) === false)) {
        header('HTTP/1.1 301 Moved Permanently');
        header('Status: 301 Moved Permanently');
        return mxRedirect($target);
    }

    /* versch. HTTP Header senden */
    if (!headers_sent()) {
        header('Content-type: text/html; charset=utf-8');
        header('Content-Language: ' . _DOC_LANGUAGE);
        header('X-Powered-By: pragmaMx ' . PMX_VERSION);
    }

    $cat = $doc;
    $record = $cat->getRecord($id);
    $book = $cat->getBookRoot($id);
    $config = $cat->getConfigPage($id);

    if ($config['pageprint'] == 0) mxErrorScreen(_GROUPRESTRICTEDAREA);

    $title = strip_tags(str_replace('&nbsp;', ' ', $record['title']));
    $logo = (file_exists($site_logo)) ? '<p>' . mxCreateImage($site_logo, $sitename) . '</p>' : '';
    $topictext = $book['title'];
    $content = $record['text1'];
    $time = mx_strftime(_XDATESTRING, $record['date_created']);
    ob_start();

    ?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
      <meta http-equiv="content-type" content="text/html; charset=utf-8" />
      <meta http-equiv="content-language" content="<?php echo _DOC_LANGUAGE ?>" />
      <meta name="robots" content="NOINDEX, NOFOLLOW" />
      <link rel="stylesheet" type="text/css" href="layout/style/style.css.php?t=<?php echo MX_THEME ?>" />
      <link rel="stylesheet" type="text/css" href="themes/<?php echo MX_THEME ?>/style/style.css" />
      <link rel="stylesheet" type="text/css" href="layout/style/printpage.css" />
      <title><?php echo $title ?></title>
    </head>

    <body class="printpage">
      <div id="p-page">
        <div id="p-head">
          <?php echo $logo ?>
          <h1><?php echo $title ?></h1>
        </div>
		<hr />
        <div id="p-main" class="content">
          <h1>Content</h1><br /><?php echo $content ?>
        </div>

        <div id="p-foot">
          <p><b><?php echo _DOCS_CREATED ?></b> <?php echo $time ?> <?php echo _DOCS_FROM ?> <?php echo $record['owner_name'] ?></p>
          <p><b><?php echo _DOCU ?>:</b> <?php echo $topictext ?></p><br />

          <p><?php echo _DOCS_URL_TEXT ?> <br />
          <a href="<?php echo PMX_HOME_URL ?>"><?php echo $sitename ?></a> </p>
          <p><a href="<?php echo PMX_HOME_URL . "/" . htmlspecialchars($target) ?>" ><?php echo _DOCS_URL ?> </a><br />
          <br /></p>
        </div>
      </div>
    </body>
    </html>

    <?php
	/*
    // echo trim(ob_get_clean());
    $content = ob_get_clean();
    // convert to PDF
    load_class('PDF', false);

    try {
        $html2pdf = new HTML2PDF('P', 'A4', 'de', true, 'UTF-8', array(20, 20, 20, 20));
        $html2pdf->setDefaultFont('times');
        // $html2pdf->pdf->SetFont('times',"",18);
        // $html2pdf->pdf->SetDisplayMode('fullpage');
        // $html2pdf->pdf->setHeaderData('',0,$title,htmlspecialchars($target));
        // $html2pdf->pdf->SetTitle($title);
        $html2pdf->writeHTML($content, false);
        // $html2pdf->_INDEX_NewPage('1');
        //$html2pdf->createIndex('Index', 30, 12, true, true, null, 'times') ;
        $html2pdf->Output('exemple04.pdf', 'I');
    }
    catch(HTML2PDF_exception $e) {
        echo $e;
        exit;
    }
	*/
    return;
}

/*
        gib die Datei als Download aus
 */

function content_getAttachmentDownload($fileinfo = array())
{
    $filesize = filesize($fileinfo['filename']);
    if (is_readable($fileinfo['filename'])) {
        header("Content-Type: " . $fileinfo['type'] . "");
        header("Content-disposition: attachment; filename=\"" . $fileinfo['name'] . "\"");
        header("Content-Length: " . $fileinfo['filesize'] * 1024);
        header("Pragma: no-cache");
        header("Expires: 0");
        readfile($fileinfo['filename']);
        exit;
    }
    return;
}

function content_getMimeImage($filename)
{
    global $module_name;
    $extension = strtolower(substr(strrchr($filename, '.'), 1));
    $img_path = PMX_IMAGE_PATH . "filetypes/";
    $img = "file_extension_" . $extension . ".png";
    if (file_exists($img_path . $img)) {
        $img = $img_path . $img;
    } else {
        $img = $img_path . "file_extension_blank.png";
    }
    return $img;
}

/*
        erstellt einen Downloadlink für eine Datei
 */

function content_getAttachmentLink($id, $fileinfo = array(), $download = false)
{
    global $module_name, $doc_cfg;
    $link = "";
    if (is_readable($fileinfo['filename'])) {
        $fileinfo['title'] = addslashes(strip_tags($fileinfo['title']));
        $fileinfo['name'] = addslashes(strip_tags($fileinfo['name']));
        $filesize = " [" . floatval(intval(filesize($fileinfo['filename']) / 1024)) . " kB]";

        $img = ($download)?"modules/" . $module_name . "/style/images/download_big.jpg":content_getMimeImage($fileinfo['filename']);
        switch (strtolower($fileinfo['type'])) {
            case "audio/mpeg":
            case "audio/mpg":
            case "audio/mp3":
                $link .= "<object data=\"" . $fileinfo['filename'] . "\" type=\"" . $fileinfo['type'] . "\" width=\"200\" height=\"20\">
                  <param name=\"movie\" value=\"" . $fileinfo['filename'] . "\">
                  <param name=\"quality\" value=\"high\">
                  <param name=\"scale\" value=\"exactfit\">
                  <param name=\"autoplay\" value=\"false\">
                  <param name=\"menu\" value=\"true\">
                </object><span style=\"vertical-align:top;\">&nbsp;&nbsp;" . $fileinfo['title'] . "</span><br />";
                break;
            case "video/mp4":
            case "video/quicktime":
            case "application/x-shockwave-flash":
                $link .= "<div class\"block align-center\"><object data=\"" . $fileinfo['filename'] . "\" type=\"" . $fileinfo['type'] . "\" width=\"" . $doc_cfg['attmaxwidth'] . "\"height=\"" . $doc_cfg['attmaxheight'] . "\">
                  <param name=\"movie\" value=\"" . $fileinfo['filename'] . "\">
                  <param name=\"quality\" value=\"high\">
                  <param name=\"scale\" value=\"exactfit\">
                  <param name=\"autoplay\" value=\"true\">
                  <param name=\"menu\" value=\"true\">
                </object><br /></div>";
                break;
            case "video/mpeg":
            case "video/mpg":
                $link .= "<object classid=\"clsid:22D6F312-B0F6-11D0-94AB-0080C74C7E95\">
                   <param name=\"type\" value=\"video/x-ms-mpg\">
                   <param name=\"filename\" value=\"" . $fileinfo['filename'] . "\">
                   <param name=\"autostart\" value=\"1\">
                   <param name=\"data\" value=\"" . $fileinfo['filename'] . "\">
                   <param name=\"showcontrols\" value=\"1\">
                   <param name=\"showdisplay\" value=\"1\">
                   <param name=\"showstatusBar\" value=\"1\">
                   <param name=\"autosize\" value=\"1\">
                </object>";
                break;

            case "image/jpeg":
            case "image/png":
            case "image/gif":
                $link .= "<a href=\"" . $fileinfo['filename'] . "\" title=\"" . $fileinfo['title'] . "\"
                 rel=\"pretty[doc]\" ><img src=\"" . $fileinfo['filename'] . "\" alt=\"" . $fileinfo['title'] . "\" class=\"image img\"
                 style=\"max-width:" . $doc_cfg['attmaxwidththumb'] . "px; max-height:" . $doc_cfg['attmaxwidththumb'] . "px;\"/></a>";
                break;
            default:
                $link .= "<a href=\"modules.php?name=$module_name&amp;act=download&amp;id=$id\"
                title=\"" . _DOCS_DOWNLOAD . "-" . $fileinfo['name'] . "\" ><img src=\"" . $img . "\" alt=\"" . _DOCS_DOWNLOAD . "-" . $fileinfo['name'] . "\" />" . "</a>";
                break;
        }
    }
    return $link;
}

function add_Social_share($divcontainerid, $moduleinfo = array())
{
    // global $module_name;
    // pmxHeader::add_jquery("jquery.browser.js", "jquery.cookie.js", "social/jquery.socialshareprivacy.js");
    // pmxHeader::add_style("includes/javascript/jquery/social/socialshareprivacy.css");
    // pmxHeader::add_script_code("
    // $(document).ready(function($){
    // if($('#".$divcontainerid."').length > 0){
    // $('#".$divcontainerid."').socialSharePrivacy({
    // services : {
    // facebook : {
    // 'status': 'on',
    // 'dummy_img' : 'images/social/dummy_facebook.png',
    // 'txt_info' : '".addslashes(_DOCS_SOCIAL_INFO_FACEBOOK)."',
    // 'language' : '"._LOCALE."'
    // },
    // twitter : {
    // 'status' : 'on',
    // 'dummy_img' : 'images/social/dummy_twitter.png',
    // 'txt_info' : '".addslashes(_DOCS_SOCIAL_INFO_TWITTER)."',
    // 'language' : '"._DOC_LANGUAGE."'
    // },
    // gplus : {
    // 'status' : 'on',
    // 'dummy_img' : 'images/social/dummy_gplus.png',
    // 'txt_info' : '".addslashes(_DOCS_SOCIAL_INFO_GPLUS)."',
    // 'language' : '"._DOC_LANGUAGE."'
    // }
    // },
    // 'css_path' : '',
    // 'txt_help' : '".addslashes(_DOCS_SOCIAL_INFO_HELP)."',
    // 'settings_perma' : '".addslashes(_DOCS_SOCIAL_INFO_TOOLS)."'
    // });
    // }
    // });
    // ");
    /* --------- Konfiguration fuer den Block ----------------------------------- */

    /* Definition der anzuzeigenden Links */
    $medias = array(
        array('title' => 'Google+',
            'link' => 'https://plusone.google.com/_/+1/confirm?hl=de&amp;url=[1]&amp;title=[2]',
            'class' => 'gplus'
            ),
        array('title' => 'Twitter',
            'link' => 'https://twitter.com/intent/tweet?source=webclient&amp;text=[2]%20[1]',
            'class' => 'twitter'
            ),
        array('title' => 'Facebook',
            'link' => 'https://www.facebook.com/sharer/sharer.php?u=[1]&amp;t=[2]',
            'class' => 'facebook'
            ),
        );

    $link = $moduleinfo['link'];

    $replaces = array(/* Platzhalter definieren */
        '[1]' => urlencode(PMX_HOME_URL . '/' . $link),
        '[2]' => $moduleinfo['title'],
        '[3]' => $GLOBALS['sitename'],
        );

    ob_start();

    ?>
<style type="text/css">
  .socialb{ margin-left: auto; margin-right: auto; width:100%; margin-bottom:10px; }
  .socialb .social-facebook{ background: url(images/social/facebook.png) no-repeat }
  .socialb .social-gplus{ background: url(images/social/googleplus.png) no-repeat }
  .socialb .social-twitter{ background: url(images/social/twitter.png) no-repeat }
  .socialb a{ border-bottom: none !important; display: block; float: left; height: 40px; margin: 0  4px; width: 40px; }
  .socialb a:hover{ position: relative; top: -1px; }
  .socialb li{ display: inline; }
  .socialb p{ font-weight: bold; margin-bottom: 1em; }
  .socialb span{ display: none; }
  .socialb ul{ line-height: 40px; list-style: none; margin: 0; padding: 0; margin-left:auto;}
</style>

<?php
    pmxHeader::add_style_code(ob_get_clean());

    ob_start();

    ?>

<div class="align-center socialb">

  <ul>
    <?php foreach ($medias as $key => $l) {

        ?>
    <?php $link = str_replace(array_keys($replaces), array_values($replaces), $l['link']);

        ?>
    <li><a class="social-<?php echo $l['class'] ?>" title="<?php echo htmlentities ($l['title']) ?>" onclick="window.open('<?php echo $link ?>', 'sharer_<?php echo $key ?>', 'toolbar=0,status=0,width=626,height=436'); return false" rel="nofollow" target="_blank" href="<?php echo $link ?>"><span><?php echo htmlentities ($l['title']) ?></span></a></li>
    <?php } //endforeach

    ?>
  </ul>
  <div class="clear"></div>
</div>

<?php
    $content = ob_get_clean();

    $html = '';
    return $content;
}

function output_Rating ($divcontainerid, $id, $rating = 0)
{
    global $module_name, $doc_cfg;
    pmxHeader::add_jquery();

    /* http://www.wbotelhos.com/raty/*/

    pmxHeader::add_jquery("jquery.raty.js");
    $rating = number_format ($rating, 1);
    $baserating = 5; //$doc_cfg['baserating'];
    $readOnly = "false";

    pmxHeader::add_script_code("
        var rateid = " . $id . ";
      $(document).ready(function($){
        $('#" . $divcontainerid . "').raty({
          readOnly : " . $readOnly . ",
          path        : 'images/rating/',
          cancel      : false,
          score       : " . $rating . " ,
          half        : false,
          cancelHint  : '" . _DOCS_RATE_CANCEL . "',
          number      : " . $baserating . ",
          cancelPlace : 'right',
          starOn      : 'star-on.png',
          starOff     : 'star-off.png',
          starHalf    : 'star-on.png',
          hints          : ['" . _DOCS_RATE_BAD . "', '" . _DOCS_RATE_POOR . "', '" . _DOCS_RATE_REGULAR . "', '" . _DOCS_RATE_GOOD . "', '" . _DOCS_RATE_GORGEOUS . "'],
/*          iconRange: [
            { range: 2, on: 'face-a.png', off: 'face-a-off.png' },
            { range: 3, on: 'face-b.png', off: 'face-b-off.png' },
            { range: 4, on: 'face-c.png', off: 'face-c-off.png' },
            { range: 5, on: 'face-d.png', off: 'face-d-off.png' }
          ],
*/            click       : function(score, evt) {

            $.post('modules.php?name=" . $module_name . "&act=rate',
                {
                    size: score,
                    id: rateid
                },
                function (data) {
                    $('#" . $divcontainerid . "-info').html(data);
                });
            }
         });
        })");

    $html = '
            <div id="' . $divcontainerid . '-info" style="display:inline-block;margin-right:5px;">
            ' . number_format ($rating, 1) . ' </div>
            <div id="' . $divcontainerid . '" style="display:inline-block;margin-right:20px;width:auto;">
            </div>
           ';
    return $html;
}

function page_sendfriend($eid, $ok = 0)
{
    global $doc_cfg, $module_name, $doc;
    $GLOBALS['plugins'] = 0;

    $user = $doc->getUser();
    $cat = $doc;
    $err = "";
    $doc_cfg = $cat->getConfig();
    $node = $cat->getPage($eid);
    $config = $cat->getConfigPage($eid);
    if ($config['sendfriend'] == 0) mxErrorScreen(_GROUPRESTRICTEDAREA);

    $title = $node['title'];

    $text = pmx_cutString($node['text1'], $doc_cfg['cutlen'], false);

    $event = array();
    $event['fname'] = "";
    $event['fmail'] = "";
    $event['ftext'] = "";

    switch (pmxAdminForm::CheckButton()) {
        case "back":
            mxRedirect('modules.php?name=' . $module_name . '&act=page&id=' . $eid, "", 0);
            break;
        case "save":
            $event = mxStripSlashes($_POST);

            $captcha = pmxAdminForm::checkCaptcha("friendcaptcha");
            if (!$captcha) $event['recerror'][] = _CAPTCHAWRONG;

            if (empty($event['yname'])) {
                $event['recerror'][] = _DOCS_RECERRORNAME;
            }

            switch (true) {
                case empty($event['ymail']):
                case !mxCheckEmail($event['ymail']):
                    $event['recerror'][] = _DOCS_RECERRORSENDER;
                    break;
                case pmx_is_mail_banned($event['ymail']):
                    $event['recerror'][] = _DOCS_RECERRORSENDER . ' (' . _MAILISBLOCKED . ')';
            }

            switch (true) {
                case empty($event['fmail']):
                case !mxCheckEmail($event['fmail']):
                    $event['recerror'][] = _DOCS_RECERRORRECEIVER;
                    break;
                case pmx_is_mail_banned($event['fmail']):
                    $event['recerror'][] = _DOCS_RECERRORRECEIVER . ' (' . _MAILISBLOCKED . ')';
            }

            if (!isset($event['recerror'])) {
                if (MX_IS_USER) {
                    $usersession = mxGetUserSession();
                } else {
                    $usersession[0] = 0;
                }
                $event['ftext'] = (empty($event['ftext'])) ? "" : "" . $event['ftext'] . "\n\n\n";
                $subject = _DOCS_RECINTSITE . " " . $GLOBALS['sitename'];
                $siteurl = PMX_HOME_URL . "/modules.php?name=" . $module_name . "&act=page&id=$eid";

                $message = _HELLO . " " . $event['fname'] . ":\n\n" . _DOCS_RECYOURFRIEND . " " . $event['yname'] . " " . _DOCS_RECOURSITE . " \n\"$siteurl\"\n " . _DOCS_RECINTSENT . "\n\n\n" . $event['ftext'] . _DOCS_RECSITENAME . " $title\n$text\n" . _DOCS_RECSITEURL . " " . PMX_HOME_URL . "\n";
                $message = strip_tags($message);
                mxMail($event['fmail'], $subject, $message, $event['ymail'], "text", "", $event['yname']);
                mxRedirect('modules.php?name=' . $module_name . '&act=page&id=' . $eid, _DOCS_RECTHANKS, 2);
            }

            break;
    }

    $event['yname'] = "";
    $event['ymail'] = "";

    if (MX_IS_USER) {
        $uinfo = mxGetUserData();
        $event['yname'] = $uinfo['uname'];
        $event['ymail'] = $uinfo['email'];
    }

    if (isset($event['recerror'])) {
        $err = '<div style="text-align: left;"><h2>' . _DOCS_RECERRORTITLE . '</h2><ul><li>' . implode('</li><li>', $event['recerror']) . '</li></ul></div><br />';
    }

    $tb = load_class('AdminForm');;
    $tb->__set('target_url', "modules.php?name=" . $module_name . "&amp;act=sendfriend");
    $tb->__set("tb_text", $err);
    $tb->__set("tb_direction", 'right');
    $tb->__set("infobutton", true);
    $tb->__set("tb_pic_heigth", 25);

    $tb->add("", "output", $err);
    $tb->add("", "input", "fname", $event['fname'], _DOCS_RECFRIENDNAME, "");
    $tb->add("", "input", "fmail", $event['fmail'], _DOCS_RECFRIENDEMAIL, "");
    $tb->add("", "textarea", "ftext", $event['ftext'], _DOCS_RECREMARKS, "");
    $tb->add("", "input", "yname", $event['yname'], _DOCS_RECYOURNAME, "");
    $tb->add("", "input", "ymail", $event['ymail'], _DOCS_RECYOUREMAIL, "");
    $tb->add("", "captcha", "friendcaptcha", "documentson", _CAPTCHAINSERT, _CAPTCHAINSERT);
    $tb->add("", "submitbutton", "save", _FORMSUBMIT, "&nbsp;", "");
    $tb->add("", "hidden", "id", $eid);
    $tb->add("", "hidden", "hidemainmenu", 1);
    $tb->add("", "hidden", "toolbarhide", "0");
    $tb->add("", "submitbutton", "back", _BACK, "&nbsp;", "");
    $form = $tb->Show();

    /*
     * Template
     */
    $template = load_class('Template');
    $template->init_path(__FILE__);

    /* Variablen an das Template uebergeben */
    $template->assign(compact('toolbar', 'title', 'text', 'form', 'module_name', 'doc_cfg', 'doc'));

    include('header.php');
    /* Template ausgeben (echo) */
    $template->display('view.friend.html');
    include("footer.php");
}

function page_alphalist($char = "", $id = 0)
{
    global $doc_cfg, $module_name, $doc;
    $GLOBALS['plugins'] = 0;
    $char = trim(strip_tags($char));
    $user = $doc->getUser();
    $cat = $doc;
    $doc_cfg = $cat->getConfig();
    $config = $cat->getConfigPage($id);
    $page = $cat->getRecord($id);
    $booktitle = $page['title'];

    //if ($config['alphaindex'] != 1) return;
    // $title="Alphaindex";
    $adminlink = page_adminlink(0, 0);
    $x = $doc_cfg['tabscount'];
    $width = intval((100 - $x * 3) / $x);
    $title = imgModul() . " " . (($doc_cfg['view_title'] == 1)?(($doc_cfg['link_title'] == 0)?$doc->getModuleTitle():"<a href=\"modules.php?name=$module_name\" title=\"\" >" . $doc->getModuleTitle() . "</a>"):"");
    $breadcrump = $cat->getBreadcrump(0, false);
    reset($breadcrump);

    $alphaindex = $cat->getAlphaIndexString($id);
    $indexlist = $cat->getAlphaIndex($id);
    if (!array_key_exists($char, $indexlist)) {
        $char = "";
        $ilist = "";
        $alphaindex = "";
        return;
    }
	if (array_key_exists($char,$indexlist)) {
		$ilist = $indexlist[$char];
		} else {
		$ilist[$char]=array('title'=>_DOCS_SEARCH_NORESULTS_TEXT,'id'=>$id);
	}

    pmxHeader::add_style("modules/$module_name/style/style.css");
    if (file_exists("modules/$module_name/style/custom.style.css")) pmxHeader::add_style("modules/$module_name/style/custom.style.css");
    // Template initialisieren
    $template = load_class('Template');
    $template->init_path(__FILE__);

    /* Variablen an das Template uebergeben */
    $template->assign(compact('adminlink', 'title', 'ilist', 'doc_cfg', 'doc', 'width', 'module_name', 'alphaindex', 'breadcrump', 'char', 'id', 'booktitle'));

    include('header.php');
    /* Template ausgeben (echo) */
    $template->display('view.alphaindex.html');
    include("footer.php");
    exit;
}

?>