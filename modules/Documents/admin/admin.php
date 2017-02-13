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
 * $Revision: 158 $
 * $Author: PragmaMx $
 * $Date: 2016-05-14 19:07:02 +0200 (Sa, 14. Mai 2016) $
 */

defined('mxMainFileLoaded') or die('access denied');

if (!mxGetAdminPref('radmincontent')) {
    mxErrorScreen('Access Denied');
    die();
}

$wysiwyg = 1;
$wysiwyg_content = 1;

global $module_name, $prefix, $doc, $doc_cfg;
mxGetLangfile($module_name);
// mxGetLangfile($module_name,"core.lang-*.php");
include_once(PMX_MODULES_DIR . DS . $module_name . DS . "includes/functions.php");
include_once(PMX_MODULES_DIR . DS . $module_name . DS . "includes/functions.edit.php");
include_once(PMX_MODULES_DIR . DS . $module_name . DS . "setup.pmx.php");

global $bookmodule,$mxbook_version;
$docconfig = load_class('Config',$module_name);

if (empty($ok)) $ok = 0;
if (!isset($act)) $act = "";
if (!isset($id)) $id = 0;
if (!isset($cid)) $cid = 0;
if (!isset($page)) $page = 1;
if (!isset($book)) $book = 0;

if (version_compare($mxbook_version, $docconfig->getValue('version',$module_name,0), '>')) $act="update";

switch ($act) {
    case "update":
        book_docupdate();
        break;
    case "content":
    case "startpage":
    case "startpagesave":
    case "startpagepublish":
    case "startpageunpublish":
    case "setstartpage":
    case "unsetstartpage":
    case "spsetstartpage":
    case "spunsetstartpage":
    case "contentmoveup":
    case "contentmovedn":
    case "contentpublish":
    case "contentunpublish":
    case "contentedit":
    case "contentnew":
    case "contentmove":
	case "getmenu":
    case "contentdelete":
        include_once("modules/$module_name/admin/admin_content.php");
        break;
    case "bookmoveup":
        content_moveup($id);
        book_main();
        break;
    case "bookmovedn":
        content_movedn($id);
        book_main();
        break;
    case "bookpublish":
        content_publish($id);
        book_main();
        break;
    case "bookunpublish":
        content_unpublish($id);
        book_main();
        break;
    case "bookedit":
        book_edit($id);
        break;
    case "bookdelete":
        book_delete($cid);
        break;
    case "bookconfig":
        book_config();
        exit;
        break;
    case "booktools":
        book_tools();
        exit;
        break;
    case "contentdeletedirect":
        $doc->deleteRecord ($id);
        book_main();
        break;
    case "getlog":
        book_viewlog($id);
        break;
    case "dellog":
        book_dellog($id);
        break;
    case "activate":
        content_setmoduleactive();
        book_main();
        break;
    default:
        book_main();
        break;
}

function book_main()
{
    global $doc_cfg, $module_name, $mxbook_version, $credits, $setup_result, $doc;

    include_once ("modules/" . $module_name . "/setup.pmx.php");
    $mod_err = (!mxModuleActive($module_name))?"<div class=\"error\">" . _MODULENOTACTIVE . " <a href=\"admin.php?op=".$module_name."&amp;act=activate\" class=\"button\" >"._ACTIVATE."</a></div>":"";

    $cat = $doc;
    $doc_cfg = $cat->getConfig();
    $user = $doc->getUser();
	

    $info2 = imgModul() . " " . $doc->getModuleTitle();;

    switch (pmxAdminForm::CheckButton()) {
        case "add":
            $cat->addRecord(0, $cat->getRecordDefault());

            break;
        case "edit":
            $id = $_POST['cid'][0];
            book_edit($id);
            exit;
            break;
        case "config":
            book_config();
            exit;
            break;
        case "tools":
            book_tools();
            exit;
            break;
        case "settings":
            break;
        case "delete":
            $cid = $_POST['cid'];
            book_delete($cid);
            exit;
            break;
        case "publish":
            foreach($_POST['cid'] as $dummy => $id) {
                $cat->publish($id);
            }
            break;
        case "unpublish":
            foreach($_POST['cid'] as $dummy => $id) {
                $cat->unpublish($id);
            }
            break;
        case "dbrepair":
            $cat->_renumNestedSet();
            break;
        default:
            // $info=pmxAdminForm::CheckButton(); // Test
            break;
    }

    $error = "";
    if (intval($cat->_CheckNestedSets()) != 0 ) {
        $error = "<span class=\"error\">" . _DOCS_NESTEDSET_ERROR . "&nbsp;</span><br />";
        $error .= "";
    } else {
        $error = "<span class=\"highlight\">" . _DOCS_NESTEDSET_IO . "</span>";
    }
    $tb = load_class('AdminForm', "adminFormMain");
    $tb->__set("tb_text", "");
    $tb->__set("tb_direction", 'right');
    $tb->__set("infobutton", true);
    $tb->__set("tb_pic_heigth", 22);
    $tb->__set("cssclass", "toolbar1");
    $tb->__set('homelink', false);

    $tb->addToolbar("editX");
    $tb->addToolbar("publishX");
    $tb->addToolbar("unpublishX");
    $tb->addToolbar("add");
    $tb->addToolbar("deleteX");
    $tb->addToolbarLink("startpage", "admin.php?op=$module_name&act=startpage", _DOCS_STARTPAGE, "images/rating/star-on.png");
    $tb->addToolbar("tools");
    $tb->addToolbar("config");

    $formOpen = $tb->FormOpen();
    $toolbar = $tb->getToolbar();
    $formClose = $tb->FormClose();

    /*
     * Template
     */
    // Template initialisieren
    $template = load_class('Template');
    $template->init_path(__FILE__);

    /* hier die Ausgabefelder angeben */
    $output = array("id", "parent_id", "title", "date_created", "owner_id", "owner_name", "publish", "access", "language");

    $cat->setFilter("adminroot", "parent_id", "=", $cat->getRootID());
    $catlist = $cat->getRecordList($cat->getRootID(), $output, "adminroot");
    $count = $cat->contentcount();
    $newcontent = $doc->getRecords_AdminNews();
    //$error .= implode(',', $cat->_CheckNestesSetsMore());

    /* Variablen an das Template uebergeben */
    $template->assign(compact('credits', 'toolbar', 'info2', 'catlist', 'count', 'formOpen', 'formClose', 'module_name', 'access', 'owner_id', 'error', 'newcontent', 'cat', 'mod_err'));

    include('header.php');
    
    /* Template ausgeben (echo) */
    $template->display('admin/admin.html');
    printFooter();
}

function book_edit($id)
{
    global $doc_cfg, $module_name, $credits, $setup_result, $doc;

    include ("modules/" . $module_name . "/setup.pmx.php");
    $returnflag = false;
    $cat = $doc;
    $doc_cfg = $cat->getConfig();

    $info2 = imgModul() . " " . $doc->getModuleTitle() . " - " . _EDIT;

    switch (pmxAdminForm::CheckButton()) {
        case "save":
            $returnflag = true;
        case "accept":
            $content = array();
            $_POST['info']['alternate'] = mxAddSlashesForSQL(trim(htmlspecialchars($_POST['info']['alternate'])));
            foreach($_POST as $key => $value) {
                if (is_array($value)) {
                    $content[$key] = serialize(mxAddSlashesForSQL($value));
                } else {
                    $content[$key] = mxAddSlashesForSQL($value);
                }
            }
            $id = intval($_POST['id']);

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
                        move_uploaded_file($_FILES['attachment']['tmp_name'][$cid], $temppath . DS . $newname);
                        $file['filename'] = $temppath . DS . $newname;
                        $file['type'] = $type;
                        $file['hash'] = md5($newname);
                        $file['name'] = $_FILES['attachment']['name'][$cid];
                        $file['title'] = strip_tags($_FILES['attachment']['name'][$cid]);
                        $file['filesize'] = intval(filesize($temppath . DS . $newname) / 10.24) / 100;
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
                    }
                }
            }
            /* uploads ende */
            $content['attachment'] = serialize($uploadfiles);

            $cat->updateRecord ($id, $content);
            unset($content);
            if ($returnflag) mxRedirect("admin.php?op=$module_name", _CHANGESAREOK, 0);
            break;
        default:
            break;
    }

    /* hier die Ausgabefelder angeben */
    $output = array("id");

    $book = $cat->getRecord($id);

    $config = array_merge($doc_cfg, (array)unserialize($book['config']));
    $info = (array)unserialize($book['info']);
    $tb = load_class('AdminForm');;
    $tb->__set("tb_text", "");
    $tb->__set("tb_direction", 'right');
    $tb->__set("infobutton", true);
    $tb->__set("tb_pic_heigth", 25);
    $tb->__set('target_url', "admin.php?op=" . $module_name . "&amp;act=bookedit");
    $tb->__set('homelink', false);
    $tb->__set('fieldhomebutton', true);
	$tb->__set("cssform", "a306010");
	
    $tb->addToolbar("accept");
    $tb->addToolbar("save");
    $tb->addToolbarLink("cancel", "admin.php?op=$module_name");
    $tb->addToolbarLink("cpanel", "admin.php?op=$module_name");

    /* Form elements */
    $tb->addFieldSet("head", _DOCS_EDIT, _DOCS_EDIT_TEXT, false);
    $tb->addFieldSet("attachments", _DOCS_ATTACHMENTS, "", true);
    $tb->addFieldSet("extended", _DOCS_EXTENDET_SETTINGS, "", true, array("style" => "width:49%;"));
    $tb->addFieldSet("metapage", _DOCS_META_PAGE, "", true, array("style" => "width:49%;float:right;"));

    $tb->add("", "input", "lastchange", mx_strftime(_DATESTRING . " %H:%M:%S", $book['date_created']), _DOCS_LASTCHANGE, "", 50, "readonly=\"readonly\"");
    $tb->add("head", "hidden", "id", $book['id']);
    $tb->add("head", "input", "title", $book['title'], _DOCS_TITLE, _DOCS_TITLE_TEXT, 50);
    $tb->add("head", "input", "alias", $book['alias'], _DOCS_ALIAS, _DOCS_ALIAS_TEXT, 50);
    $tb->add("head", "input", "keywords", $book['keywords'], _DOCS_KEYWORDS, _DOCS_KEYWORDS_TEXT, 80);
    $tb->add("head", "selectlanguage", "language", $book['language'], _DOCS_LANGUAGE, _DOCS_LANGUAGE_TEXT);
    $tb->add("head", "yesno", "publish", $book['publish'], _DOCS_PUBLISHED);

    $tb->add("head", "selectusergroup", "config[group_access][]", $config['group_access'], _DOCS_PAGE_EDITORS, _DOCS_PAGE_EDITORS_TEXT , 4, 0, 'multiple="multiple"');

    $tb->add("head", "selectuser", "owner_id", $book['owner_id'], _DOCS_OWNER, "");

    $tb->add("head", "editor", "text2", $book['text2'], _DOCS_SHORTDESC, _DOCS_SHORTDESC_TEXT, 180);
    $tb->add("head", "editor", "text1", $book['text1'], _DOCS_PREAMBLE, _DOCS_PREAMBLE_TEXT, 180);
    $tb->add("head", "editor", "text3", $book['text3'], _DOCS_COPYRIGHT, _DOCS_COPYRIGHT_TEXT, 180);

    $book['attachment'] = unserialize($book['attachment']);
    $attachments = (is_array($book['attachment']))?$book['attachment']:array();
    $attcount = count($attachments);
    $attmaxcount = $doc_cfg['attcount'];
    if ($attcount) {
        $i = 0;
        $alist = "";
        foreach ($attachments as $file) {
            if ($file['error']) {
                $tb->add("attachments", "output", "<span class=\"warning\">" . $file['name'] . " - " . $file['error'] . " = " . $file['filesize'] . " kByte </span>");
                $attcount--;
                //  @unlink ($file['name']);
                // $tb->add("attachments", "hidden", "attachments[$i][delete]", "1", _DELETE, _DOCS_ATTACH_DELETE);
                // $i++;
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
    if ($doc_cfg['att_on'] == 1) {
        for ($i = 0;$i < ($attmaxcount - $attcount);$i++) {
            $tb->add("attachments", "file", "attachment", "", _DOCS_ATTACHMENTS);
        }
    }

    /* icons */
    $files = (array)glob(PMX_REAL_BASE_DIR . DS . "media" . DS . 'images' . DS . '*.{png,jpg,jpeg,gif}', GLOB_BRACE);
    $auswahl = "<hr />" . _DOCS_SELECT_ICON . "<br />";
    $sel = ($book['link'] == 0) ? "checked='checked'":"";
    $auswahl .= "<div style=\"display:inline-block; margin:5px; text-align:top;width:70px;\">";
    $auswahl .= "<input type='radio' name='link' value='' $sel />" . _NONE . "<br /></div>";

    foreach ($files as $icon) {
        $icon2 = "media/images/" . basename($icon);
        $sel = ($book['link'] == $icon2) ? "checked='checked'":"";
        $auswahl .= "<div style=\"display:inline-block; margin:5px; text-align:top;width:70px;\">";
        $auswahl .= "<input type='radio' name='link' value='$icon2' $sel /><img src=\"" . $icon2 . "\" style=\"max-width:48px;max-height:48px;\" />";
        $auswahl .= "</div>";
    }
    $tb->add("attachments", "output", $auswahl);

    content_getPageConfigForm($tb, $config);
    content_getPageMetaForm($tb, $config, $book, $info) ;

    /* Form schliessen*/

    $form = $tb->Show();
    /*
     * Template
     */
    // Template initialisieren
    $template = load_class('Template');
    $template->init_path(__FILE__);

    /* Variablen an das Template uebergeben */
    $template->assign(compact('toolbar', 'info2', 'form', 'module_name'));

    include('header.php');
    /* Template ausgeben (echo) */
    $template->display('admin/admin.booksedit.html');
    include('footer.php');
}

function book_update ($id =0)
{
    global $doc_cfg, $module_name, $credits, $setup_result, $doc;

    $returnflag = false;
    $cat = $doc;
    $doc_cfg = $cat->getConfig();
    $book = $cat->getRecord($id);

    $config = array_merge($doc_cfg, (array)unserialize($book['config']));
    $info = (array)unserialize($book['info']);
	$book['config']=serialize($config);
	$cat->updateRecord($id, $book);
	return;
}

function book_delete($ids = array())
{
    global $doc_cfg, $module_name, $doc;

    $returnflag = false;
    $cat = $doc;

    $info2 = imgModul() . " " . $doc->getModuleTitle() . " - " . _DELETE;
    $doc_cfg = $cat->getConfig();

    switch (pmxAdminForm::CheckButton()) {
        case "bookdelete":
            foreach($_POST['cid'] as $id) {
                $cat->delete_all ($id);
            }
            mxRedirect("admin.php?op=$module_name", _CHANGESAREOK, 2);
            break;
        default:
            break;
    }

    /* hier die Ausgabefelder angeben */
    $output = array("id", "parent_id", "title", "date_created", "owner_id", "publish", "access", "text1");

    $catlist = $cat->getRecordsFromID($ids);

    $count = count($ids);

    $tb = load_class('AdminForm');;
    $tb->__set('target_url', "admin.php?op=" . $module_name . "&amp;act=bookdelete");
    $tb->__set("tb_text", "");
    $tb->__set("tb_direction", 'right');
    $tb->__set("infobutton", true);
    $tb->__set("tb_pic_heigth", 25);
    $tb->addToolbarLink("cancel", "admin.php?op=$module_name");

    $formOpen = $tb->FormOpen();
    $toolbar = $tb->getToolbar();
    $formClose = $tb->FormClose();

    /*
     * Template
     */
    // Template initialisieren
    $template = load_class('Template');
    $template->init_path(__FILE__);

    /* Variablen an das Template uebergeben */
    $template->assign(compact('toolbar', 'info2', 'formOpen', 'formClose', 'count', 'catlist', 'module_name'));

    include('header.php');
    /* Template ausgeben (echo) */
    $template->display('admin/admin.bookdelete.html');
    include('footer.php');
}

function book_docupdate()
{

    global $prefix,$doc_cfg, $module_name, $doc,$mxbook_version, $docconfig;

    $returnflag = false;
    $cat = $doc;
    include("modules/$module_name/core/install.tabledef.php");

    include('header.php');
    /* Template ausgeben (echo) */
   // echo $setup_result;

    /* hier die Ausgabefelder angeben */
    $output = array("id", "parent_id", "title", "date_created", "owner_id", "owner_name", "publish", "access", "language");

    //$cat->setFilter("adminroot", "true", "=", "true");
    $catlist = $cat->getRecordList($cat->getRootID());
	$i=0;
	foreach ($catlist as $node) {
		book_update($node['id']);
		$i++;
	}
	$docconfig->setValue('version',$mxbook_version,$module_name);
	mxRedirect("admin.php?op=$module_name", _DOCS_UPDATE_DB."<br />". $i." "._DOCS_UPDATE_DB_TXT , 1);
    include('footer.php');
	exit;
    return;
}

function book_create_modRewrite()
{
     global $doc_cfg, $module_name, $currentlang, $doc;
	 		
		/**/
		$rew=array( "RewriteRule ^".$module_name."-Alphaindex-(.)(-.*)?\.html$ mod.php?name=".$module_name."&act=alphaindex&char=$1&_MORE_=$2 [L]",
					"RewriteRule ^".$module_name."-Search-(.*)?\.html$ mod.php?name=".$module_name."&act=search&keywords=$1 [L]",
					"RewriteRule ^".$module_name."-.*-Page-.*-([0-9]+)-highlight-(.*)?\.html$ mod.php?name=".$module_name."&act=page&id=$1&highlight=$2 [L]",
					"RewriteRule ^".$module_name."-.*-Page-.*-([0-9]+)?\.html$ mod.php?name=".$module_name."&act=page&id=$1 [L]",
					"RewriteRule ^".$module_name."-.*-Contents-Id-([0-9]+)\.html$ mod.php?name=".$module_name."&act=index&id=$1 [L]",
					"RewriteRule ^".$module_name."-.*-Edit-([0-9]+)\.html$ mod.php?name=".$module_name."&act=pageedit&id=$1 [L]",
					"RewriteRule ^".$module_name."-.*-New-([0-9]+)\.html$ mod.php?name=".$module_name."&act=pagenew&id=$1 [L]",
					"RewriteRule ^".$module_name."-.*-Print-.*-([0-9]+)\.html$ mod.php?name=".$module_name."&act=pageprint&id=$1 [L]",
					"RewriteRule ^".$module_name."-.*-Friend-.*-([0-9]+)\.html$ mod.php?name=".$module_name."&act=sendfriend&id=$1 [L]",
					"RewriteRule ^".$module_name."-.*-([0-9]+)-highlight-(.*)?\.html$ mod.php?name=".$module_name."&act=page&id=$1&highlight=$2 [L]",
					"RewriteRule ^".$module_name."-.*-([0-9]+)?\.html$ mod.php?name=".$module_name."&act=page&id=$1 [L]");

		
		$htaccess=PMX_REAL_BASE_DIR . DS . ".htaccess";
		if (file_exists($htaccess) && is_writable($htaccess)) {
		 $content=file_get_contents($htaccess);
		 if (strpos($content,$rew[0]) ==0 ) {
		 	$start=strpos($content,"# Downloads");
			$anfang=substr($content,0,$start);
			$ende=substr($content,$start);
			$anfang .="\n\n# ".$module_name."\n";
			foreach ($rew as $dummy=>$value) {
				$anfang .=$value."\n";
			}
			$anfang .= "\n".$ende;
			@file_put_contents($htaccess, $anfang);
		 }
		}
		
	unset($content);
    return ;
}

function book_config()
{
    global $doc_cfg, $module_name, $currentlang, $doc;

    $returnflag = false;
    $conf = $doc;
	
    $info2 = imgModul() . " " . $doc->getModuleTitle() . " - " . _DOCS_CONFIG;

    switch (pmxAdminForm::CheckButton()) {
        case "save":
            $returnflag = true;
        case "accept":
            $root['text1'] = "";
            $root['text2'] = "";
            $text = array();
            $title = array();
            foreach ($_POST["text"] as $key => $value) {
                $text[$key] = mxAddSlashesForSQL($value);
            }
            foreach ($_POST["title"] as $key => $value) {
                $title[$key] = $value;
            }
            $root['text1'] = addslashes(serialize($text));
            $root['text2'] = addslashes(serialize($title));
            $config2 = $_POST['config'];
            $root['config'] = serialize($config2);
            $conf->setRootRecord($root);
            unset($config2);
			book_create_modRewrite();
            if ($returnflag) mxRedirect("admin.php?op=$module_name", _CHANGESAREOK, 1);
            break;
        default:
            break;
    }
    $root = $conf->getRoot();
    $config = (array)unserialize($root['config']);
	$config = $doc->getConfig();
    /* defaultwerte einstellen */

    $config['tabscount'] = ($config['tabscount'] == 0)?1:$config['tabscount'];
    $root['title'] = ($root['title'] == "")?$module_name:$root['title'];

    $tb = load_class('AdminForm', "config2");
    $tb->__set('target_url', "admin.php?op=" . $module_name . "&amp;act=bookconfig");
    $tb->__set("tb_text", "");
    $tb->__set("tb_direction", 'right');
    $tb->__set("infobutton", false);
    $tb->__set("tb_pic_heigth", 25);
    $tb->__set("csstoolbar", "toolbar1");
    $tb->__set('buttontext', false);
    $tb->__set('homelink', false);
    $tb->__set('fieldhomebutton', true);
    $tb->addToolbar("accept");
    $tb->addToolbar("save");
    $tb->addToolbarLink("cancel", "admin.php?op=$module_name");
    $tb->addToolbarLink("cpanel", "admin.php?op=$module_name");

    $tb->addFieldset("language", _DOCS_CONF_INTRO, _DOCS_CONF_INTRO_TEXT, true);
    $tb->addFieldset("head", _DOCS_CONF_STARTPAGE, _DOCS_CONF_STARTPAGE_TEXT, true);
    $tb->addFieldset("index", _DOCS_CONF_INDEXPAGE, _DOCS_CONF_INDEXPAGE_TEXT, true);
    $tb->addFieldset("page", _DOCS_CONF_PAGE, _DOCS_CONF_PAGE_TEXT, true);
    $tb->addFieldset("rights", _DOCS_CONF_RIGHTS, _DOCS_CONF_RIGHTS_TEXT, true);
    $tb->addFieldset("links", _DOCS_CONF_LINK, _DOCS_CONF_LINK_TEXT, true);
    $tb->addFieldset("blocks", _DOCS_CONF_BLOCKS, _DOCS_CONF_BLOCKS_TEXT, true);
    $tb->addFieldset("attachments", _DOCS_CONF_ATTACH, _DOCS_CONF_ATTACH_TEXT, true);

    $config['language'] = ($config['language'] == "")?"ALL":$config['language'];

    $pageview2 = array(_DOCS_VIEW_INDEX => 0, _DOCS_VIEW_BLOG => 1, _DOCS_VIEW_LIST => 2);
    $tb->add("head", "yesno", "config[rightblocks]", $config['rightblocks'], _DOCS_CONF_RIGHTBLOCKS);
    $tb->add("head", "yesno", "config[logging]", $config['logging'], _DOCS_CONF_LOGGING, _DOCS_CONF_LOGGING_TEXT);
    $tb->add("head", "select", "config[viewblog]", $config['viewblog'], _DOCS_CONF_BLOGVIEW, _DOCS_CONF_BLOGVIEW_TEXT, 2, $pageview2);
    $tb->add("head", "yesno", "config[breadcrump]", $config['breadcrump'], _DOCS_CONF_BREADCRUMP, _DOCS_CONF_BREADCRUMP_TEXT);
    $tb->add("head", "yesno", "config[alphaindex]", $config['alphaindex'], _DOCS_PAGE_ALPHA, _DOCS_PAGE_ALPHA_TEXT);
    $tb->add("head", "yesno", "config[cuttext]", $config['cuttext'], _DOCS_CONF_PREAMBLE, _DOCS_CONF_PREAMBLE_TEXT);
    $tb->add("head", "number", "config[cutlen]", $config['cutlen'], _DOCS_CONF_CHARCOUNT, _DOCS_CONF_CHARCOUNT_TEXT);
    $indexwidth = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9);
    $tb->add("index", "select", "config[indexwidth]", $config['indexwidth'], _DOCS_CONF_INDEXCOUNT, _DOCS_CONF_INDEXCOUNT_TEXT, 1, $indexwidth);
    $tb->add("head", "select", "config[tabscount]", $config['tabscount'], _DOCS_CONF_TABCOUNT, _DOCS_CONF_TABCOUNT_TEXT, 1, array(1 => 1, 2 => 2, 3 => 3, 4, 5, 6));
    $tb->add("head", "input", "config[changescount]", $config['changescount'], _DOCS_PAGE_CHANGESCOUNT, _DOCS_PAGE_CHANGESCOUNT_TEXT, 2);
    $tb->add("index", "yesno", "config[viewnews]", $config['viewnews'], _DOCS_PAGE_NEWS, _DOCS_PAGE_NEWS_TEXT);
    $tb->add("index", "input", "config[newscount]", $config['newscount'], _DOCS_PAGE_NEWSCOUNT, _DOCS_PAGE_NEWSCOUNT_TEXT, 2);
    $tb->add("index", "yesno", "config[viewchanges]", $config['viewchanges'], _DOCS_PAGE_CHANGES, _DOCS_PAGE_CHANGES_TEXT);
    $tb->add("head", "input", "config[searchcount]", $config['searchcount'], _DOCS_CONF_SEARCHCOUNT, _DOCS_CONF_SEARCHCOUNT_TEXT, 5);
    $tb->add("head", "selectlanguage", "config[language]", $config['language'], _DOCS_CONF_LANGUAGE, _DOCS_CONF_LANGUAGE_TEXT);

    $insertfirst = array(_DOCS_INSERTFIRST => 1, _DOCS_INSERTLAST => 0);
    $tb->add("head", "select", "config[insertfirst]", $config['insertfirst'], _DOCS_CONF_INSERTFIRST, _DOCS_CONF_INSERTFIRST_TEXT, 1, $insertfirst);

    $tb->add("page", "yesno", "config[view_title]", $config['view_title'], _DOCS_VIEWTITLE, _DOCS_VIEWTITLE_TEXT);
    $tb->add("page", "yesno", "config[link_title]", $config['view_title'], _DOCS_LINKTITLE, _DOCS_LINKTITLE_TEXT);
    $tb->add("page", "yesno", "config[viewindex]", $config['viewindex'], _DOCS_PAGE_INDEX, _DOCS_PAGE_INDEX_TEXT);
    $tb->add("page", "yesno", "config[pageindex]", $config['pageindex'], _DOCS_PAGE_INDEXFULL, _DOCS_PAGE_INDEXFULL_TEXT);
    $tb->add("page", "yesno", "config[viewindexnew]", $config['viewindexnew'], _DOCS_PAGE_INDEX_NEW, _DOCS_PAGE_INDEX_NEW_TEXT);
    $tb->add("page", "yesno", "config[viewsearch]", $config['viewsearch'], _DOCS_VIEWSEARCH, _DOCS_VIEWSEARCH_TEXT);
    $tb->add("page", "yesno", "config[viewcreator]", $config['viewcreator'], _DOCS_PAGE_CREATOR, _DOCS_PAGE_CREATOR_TEXT);
    $tb->add("page", "yesno", "config[vieweditor]", $config['vieweditor'], _DOCS_PAGE_LASTEDITOR, _DOCS_PAGE_LASTEDITOR_TEXT);
    $tb->add("page", "yesno", "config[viewviews]", $config['viewcreator'], _DOCS_VIEWVIEWS, _DOCS_VIEWVIEWS_TEXT);
    $tb->add("page", "yesno", "config[viewkeywords]", $config['viewkeywords'], _DOCS_PAGE_VIEWKEYWORDS, _DOCS_PAGE_VIEWKEYWORDS_TEXT);
    $tb->add("page", "yesno", "config[navigation]", $config['navigation'], _DOCS_PAGE_VIEWNAVIGATION, _DOCS_PAGE_VIEWNAVIGATION_TEXT);
    $tb->add("page", "yesno", "config[viewsimilar]", $config['viewsimilar'], _DOCS_PAGE_VIEWSIMILAR, _DOCS_PAGE_VIEWSIMILAR_TEXT);
    $tb->add("page", "input", "config[similarcount]", $config['similarcount'], _DOCS_PAGE_SIMILARCOUNT, _DOCS_PAGE_SIMILARCOUNT_TEXT, 2);
    $tb->add("page", "yesno", "config[pageprint]", $config['pageprint'], _DOCS_PAGE_PRINT, _DOCS_PAGE_PRINT_TEXT);
    $tb->add("page", "yesno", "config[sendfriend]", $config['sendfriend'], _DOCS_PAGE_SENDFRIEND, _DOCS_PAGE_SENDFRIEND_TEXT);
    $tb->add("page", "yesno", "config[viewsocial]", $config['viewsocial'], _DOCS_PAGE_VIEWSOCIAL, _DOCS_PAGE_VIEWSOCIAL_TEXT);
    $tb->add("page", "yesno", "config[viewrating]", $config['viewrating'], _DOCS_PAGE_VIEWRATING, _DOCS_PAGE_VIEWRATING_TEXT);

    /* rights */
    $tb->add("rights", "yesno", "config[editorrights]", $config['editorrights'], _DOCS_PAGE_EDITOR_RIGHTS, _DOCS_PAGE_EDITOR_RIGHTS_TEXT);

    /* links */
    $tb->add("links", "yesno", "config[viewbooklink]", $config['viewbooklink'], _DOCS_PAGE_VIEWBOOKLINK, _DOCS_PAGE_VIEWBOOKLINK_TEXT);
    $tb->add("links", "yesno", "config[viewbookbase]", $config['viewbookbase'], _DOCS_PAGE_VIEWBOOKBASE, _DOCS_PAGE_VIEWBOOKBASE_TEXT);
    $tb->add("links", "yesno", "config[link_other]", $config['link_other'], _DOCS_CONF_LINKOTHER, _DOCS_CONF_LINKOTHER_TEXT);
    $tb->add("links", "yesno", "config[linkmodules]", $config['linkmodules'], _DOCS_PAGE_VIEWMODULELINK, _DOCS_PAGE_VIEWMODULELINK_TEXT);
    $tb->add("links", "select", "config[link_count]", $config['link_count'], _DOCS_CONF_LINKCOUNT, _DOCS_CONF_LINKCOUNT_TEXT, 1, array(_DOCS_LINK_ALL => 0, _DOCS_LINK_FIRST => 1,));


	/* block menu */
	$rootid = strval($doc->getModulRootID());
	$filter=$doc->setFilter("docmenu","parent_id","=","'".$rootid."'");
	$filter=$doc->setFilter("docmenu","publish","=","1");
	$baselist=$doc->getRecordList(0,"","docmenu");
    $indexwidth2 = array(1, 2, 3, 4, 5, 6, 7, 8, 9,10);
    $tb->add("blocks", "select", "config[blockmenuwidth]", $config['blockmenuwidth'], _DOCS_CONF_MENUWIDTH, _DOCS_CONF_MENUWIDTH_TEXT, 1, $indexwidth2);

	//$tb->add("blocks","highlight",_DOCS_CONF_MENUCONTENT);
	$tb->add("blocks","note",_DOCS_CONF_MENUCONTENT_TEXT);
	
	$tb->add("blocks","html","<div class=\"table\" style=\"border:1px solid;padding:2px;\">");
	foreach ($baselist as $base) {
		if (!array_key_exists($base['id'],$config['blockmenucontent'])) $config['blockmenucontent'][$base['id']]=0;
		$tb->add("blocks","checkbox","config[blockmenucontent][".$base['id']."]",$config['blockmenucontent'][$base['id']],$base['title'],"",$config['blockmenucontent'][$base['id']]);
	}
	$tb->add("blocks","html","</div>");
	
	/* attachments */
    if (empty($config['attpath']))$config['attpath'] = "modules/$module_name/attachments/";

    $tb->add("attachments", "yesno", "config[att_on]", $config['att_on'], _DOCS_CONF_ATTACH_ON, _DOCS_CONF_ATTACH_ON_TEXT);
    $tb->add("attachments", "input", "config[attcount]", $config['attcount'], _DOCS_ATTACH_MAX, _DOCS_ATTACH_MAX_TEXT, 2);
    $tb->add("attachments", "input", "config[attmaxsize]", $config['attmaxsize'], _DOCS_CONF_ATTACH_MAXSIZE, _DOCS_CONF_ATTACH_MAXSIZE_TEXT, 4);
    $tb->add("attachments", "input", "config[attpath]", $config['attpath'], _DOCS_CONF_ATTACH_PATH, _DOCS_CONF_ATTACH_PATH_TEXT, 40);
    $tb->add("attachments", "yesno", "config[attmedia]", $config['attmedia'], _DOCS_CONF_ATTACH_MEDIA, _DOCS_CONF_ATTACH_MEDIA_TEXT);
    $tb->add("attachments", "input", "config[attmaxwidth]", $config['attmaxwidth'], _DOCS_CONF_ATTACH_MAXWIDTH, _DOCS_CONF_ATTACH_MAXWIDTH_TEXT, 4);
    $tb->add("attachments", "input", "config[attmaxheight]", $config['attmaxheight'], _DOCS_CONF_ATTACH_MAXHEIGHT, _DOCS_CONF_ATTACH_MAXHEIGHT_TEXT, 4);
    $tb->add("attachments", "input", "config[attmaxwidththumb]", $config['attmaxwidththumb'], _DOCS_CONF_ATTACH_MAXWIDTHTHUMB, _DOCS_CONF_ATTACH_MAXWIDTHTHUMB_TEXT, 4);

    /* editors*/

    pmxHeader::add_tabs(true);

    $tabsscript = '<script type="text/javascript">/* <![CDATA[ */
          $(document).ready(function()
            {
                $(\'ul.tabs-nav\').each(function(){

                // Fuer jeden Satz Tabs wollen wir verfolgen welcher
                // Tab aktiv ist und der ihm zugeordnete Inhalt
                var $active, $content, $links = $(this).find(\'a\');

                // Der erste Link ist der zu Anfang akitve Tab
                $active = $links.first().addClass(\'active\');
                $content = $($active.attr(\'href\'));

                // Verstecke den restlichen Inhalt
                $links.not(\':first\').each(function () {
                    $($(this).attr(\'href\')).hide();
                });

                // Binde den click event handler ein
                $(this).on(\'click\', \'a\', function(e){

                    // Mache den alten Tab inaktiv
                    $active.removeClass(\'active\');
                    $content.hide();

                    // Aktualisiere die Variablen mit dem neuen Link und Inhalt
                    $active = $(this);
                    $content = $($(this).attr(\'href\'));

                    // Setze den Tab aktiv
                    $active.addClass(\'active\');
                    $content.show();

                    // Verhindere die Anker standard click Aktion
                    e.preventDefault();
                });
            });
         });
        /* ]]> */</script>';

    $langlist = mxGetAvailableLanguages(true);
    $text1 = array();
    $titel = array();
    $text1 = unserialize($root['text1']);
    $titel = unserialize($root['text2']);

    /* tabs generieren */
    $tb->add("language", "html", "<ul class='tabs-nav' >");
    foreach ((array)$langlist as $caption => $value) {
        $img = mxCreateImage("images/language/flag-" . $value . ".png");
        $tb->add("language", "html", "<li class='tabs-nav'><a href='#tab-" . $value . "' class='item'>" . $img . " " . $caption . "</a></li>");
    }
    $tb->add("language", "html", "</ul>");

    $tb->add("language", "html", "<div class='tabs-panel' >");
    $tb->add("language", "html", "<div>");
    foreach ((array)$langlist as $caption => $value) {
        $title = stripslashes($titel[$value]);
        $text = stripslashes($text1[$value]);
        $tb->add("language", "html", "<div id='tab-" . $value . "'>");
        $tb->add("language", "output", "<h4>" . $caption . "</h4>");
        $tb->add("language", "input", "title[" . $value . "]", $title, _DOCS_CONF_TITLE, _DOCS_CONF_TITLE_TEXT);
        $tb->add("language", "editor", "text[" . $value . "]", $text, "", "", 200);
        $tb->add("language", "html", "</div><div class=\"clear\"></div>");
    }
    $tb->add("language", "html", "</div>");
    $tb->add("language", "html", "</div>");
    $tb->add("language", "html", $tabsscript);

    /* formular abrufen */
    $form = $tb->Show();
    /*
     * Template
     */
    // Template initialisieren
    $template = load_class('Template');
    $template->init_path(__FILE__);

    /* hier die Ausgabefelder angeben */

    /* Variablen an das Template uebergeben */
    $template->assign(compact('form', 'info2', 'catlist', 'count', 'module_name'));

    include('header.php');
    /* Template ausgeben (echo) */
    $template->display('admin/admin.booksconfig.html');

    include('footer.php');
}

function book_tools()
{
    global $doc_cfg, $module_name, $currentlang, $doc;
    $info = "";
	set_time_limit (0);
    switch (pmxAdminForm::CheckButton()) {
        case "start-import":
            $importmodul = $_POST["import-modul"];
            $importbook = $_POST["import-book"];
            $info = $importmodul . " : " . book_import($importmodul, $importbook) . _DOCS_IMPORT_ACTION;
            break;
        case "start-dbcheck":
            $doc->_renumNestedSet();
            break;
        case "logdelete":
            $doc->_deleteLog();
            $info = _DOCS_DB_DELLOG_ACTION;
            break;
        default:
            break;
    }
    $error = "";
    if (intval($doc->_CheckNestedSets()) != 0 or count($doc->_CheckNestesSetsMore()) > 0) {
        $error = "<span class=\"error\">" . _DOCS_NESTEDSET_ERROR . "&nbsp;</span><br />";
        $error .= "";
    } else {
        $error = "<span class=\"highlight\">" . _DOCS_NESTEDSET_IO . "</span>";
    }

    $tb = load_class('AdminForm', "tools");
    $tb->__set('target_url', "admin.php?op=" . $module_name . "&amp;act=booktools");
    $tb->__set("tb_text", $info);
    $tb->__set("tb_direction", 'right');
    $tb->__set("infobutton", false);
    $tb->__set("tb_pic_heigth", 25);
    $tb->__set("csstoolbar", "toolbar1");
    $tb->__set('buttontext', false);

    $tb->addToolbarLink("cpanel", "admin.php?op=$module_name");

    $tb->addFieldset("head", _DOCS_TOOLS_DB, _DOCS_TOOLS_DB_TEXT, false);
    $tb->addFieldset("import", _DOCS_TOOLS_IMPORT, _DOCS_TOOLS_IMPORT_TEXT, false);
    $tb->add("head", "output", $error);
    $tb->add("head", "submitbutton", "start-dbcheck", _DOCS_DB_REPAIR, "", _DOCS_DB_REPAIR_TEXT);
    $tb->add("head", "submitbutton", "logdelete", _DOCS_DB_DELLOG, "", _DOCS_DB_DELLOG_TEXT);

    $modules = array(_DOCS_NONE => _DOCS_NONE, "Content" => "Content", "Stories" => "Stories", "Sections" => "Sections" , "Reviews" => "Reviews" , "Encyclopedia" => "Encyclopedia");
    $tb->add("import", "select", "import-modul", _NONE, _DOCS_TOOLS_IMPORT_SELECT, _DOCS_TOOLS_IMPORT_SELECT_TEXT, 1, $modules);
    $tb->add("import", "input", "import-book", "", _DOCS_TOOLS_IMPORT_DOC, _DOCS_TOOLS_IMPORT_DOC_TEXT, 20);
    $tb->add("import", "submitbutton", "start-import", _DOCS_START, "", "");

    /* formular abrufen */
    $info2 = imgModul() . " " . $doc->getModuleTitle() . " - " . _DOCS_TOOLS;
    $form = $tb->Show();
    /*
     * Template
     */
    // Template initialisieren
    $template = load_class('Template');
    $template->init_path(__FILE__);

    /* hier die Ausgabefelder angeben */

    /* Variablen an das Template uebergeben */
    $template->assign(compact('form', 'info2', 'catlist', 'count', 'module_name'));

    include('header.php');
    /* Template ausgeben (echo) */
    $template->display('admin/admin.bookstools.html');

    include('footer.php');
}

function book_import($module, $bookname = "")
{
    global $doc_cfg, $module_name, $currentlang, $doc, $prefix;
    $irecord = 0;
	$modul_root_id=$doc->getRootID();
	
	set_time_limit (0);
	$contentbook = $doc->getRecordDefault();
	$content_id = (trim($bookname) == "")?$modul_root_id:0;
	$booklist = $doc->getRecordList($content_id);
	$contentbook['title'] = (trim($bookname) == "")?$contentbook['title'] = $module:$bookname;
	$contentbook['language'] = "ALL";

    switch ($module) {
        case "Content":
            foreach($booklist as $books) {
                $content_id = ($books['title'] == $contentbook['title'])?$books['id']:$content_id;
            }
            unset($booklist);

            if ($content_id == 0) {
                $content_id = $doc->addRecord(0, $contentbook);
                $irecord++;
            }
            $catarray = array();
            $resultcat = sql_query("SELECT * FROM ${prefix}_pages_categories");

            while ($cat = sql_fetch_assoc($resultcat)) {
                $iscats = array();
                $xcat = $doc->getRecordDefault();
                $xcat['title'] = $cat['title']." (Content)";
                $xcat['text1'] = $cat['description'];
                $xcat['mid'] = $cat['cid'];
                $xcat['import'] = "" . $module . "-" . $cat['cid'] . "";
                $xcat['language'] = 'ALL';

                $doc->delFilter('dbfilter');
                $output = array("id", "parent_id", "title", "date_created", "owner_id", "publish", "access", "text1", "import");
                $doc->setFilter("dbfilter", "import", "=", "'" . $xcat['import'] . "'");
                $dbfilter = $doc->getFilter("dbfilter");
                $iscats = $doc->getRecords($dbfilter);
                $iscat = array('import' => "");
                foreach($iscats as $iat) {
                    $iscat = $iat;
                }
                if ($iscat['import'] == $xcat['import']) {
                    $cat_id = $iscat['id'];
                } else {
                    $cat_id = $doc->addRecord($content_id, $xcat);
                    $irecord++;
                }
                $catarray[$cat['cid']] = $cat_id;
            }
            $xcat = array();
            $resultcontent = sql_query("SELECT * FROM ${prefix}_pages");
            while ($cat = sql_fetch_assoc($resultcontent)) {
                $doc->delFilter('dbfilter');
                $iscats = array();
                $xcat = $doc->getRecordDefault();
                $xcat['title'] = $cat['title'];
                $xcat['text1'] = $cat['page_header'] . "<br /><br />" . $cat['text'] . "<br /><br />" . $cat['page_footer'] . "<br /><br /><div class=\"align-right\"><i>" . $cat['signature'] . "</i></div>";
                $xcat['mid'] = $cat['pid'];
                $xcat['language'] = $cat['clanguage'];
                $xcat['views'] = $cat['counter'];
                $xcat['import'] = "" . $module . "-" . $cat['cid'] . "-" . $cat['pid'] . "";
                $xcat['date_created'] = strtotime($cat['date']);

                $output = array("id", "parent_id", "title", "date_created", "owner_id", "publish", "access", "text1", "import");
                $doc->setFilter("dbfilter", "import", "=", "'" . $xcat['import'] . "'");
                $dbfilter = $doc->getFilter("dbfilter");
                $iscats = $doc->getRecords($dbfilter);
                $iscat = array('import' => "");
                foreach($iscats as $iat) {
                    $iscat = $iat;
                }
                if ($iscat['import'] == $xcat['import']) {
                    // $cat_id=$doc->updateRecord($catarray[$cat['cid']],$xcat);
                } else {
                    $cat['cid'] = ($cat['cid'] == 0)?$content_id:$catarray[$cat['cid']];
                    $cat_id = $doc->addRecord($cat['cid'], $xcat);
                    $irecord++;
                }
            }
            break;
        case "Reviews":
//            $contentbook = $doc->getRecordDefault();
//            $content_id = 0;
//
//            $booklist = $doc->getRecordList(0);
//            $contentbook['title'] = (trim($bookname) == "")?$contentbook['title'] = $module:$bookname;
//            $contentbook['language'] = "ALL";
            foreach($booklist as $books) {
                $content_id = ($books['title'] == $contentbook['title'])?$books['id']:$content_id;
            }
            unset($booklist);

            if ($content_id == 0) {
                $content_id = $doc->addRecord(0, $contentbook);
                $irecord++;
            }
            $catarray = array();
            $resultcat = sql_query("SELECT * FROM ${prefix}_reviews");

            while ($cat = sql_fetch_assoc($resultcat)) {
                $iscats = array();
                $xcat = $doc->getRecordDefault();
                $xcat['title'] = $cat['title']." (Reviews)";
                $xcat['text1'] = $cat['text'];
                $xcat['mid'] = $cat['id'];
                $xcat['import'] = "" . $module . "-" . $cat['id'] . "";
                $xcat['uname'] = $cat['reviewer'];
                $img = (is_file("images/reviews/" . $cat['cover']))?"<img class=\"image align-left border\" align=\"left\" src=\"images/reviews/" . $cat['cover'] . "\" title=\"" . $cat['title'] . "\" alt=\"" . $cat['title'] . "\" />":"";
                $xcat['text1'] = $img . $cat['text'] . "<br /><hr /><a href=\"" . $cat['url'] . "\" title=\"" . $cat['url_title'] . "\" target=\"_blank\" rel=\"nofollow\">" . $cat['url_title'] . "</a>";
                $xcat['rating'] = $cat['score'] / 2;
                $xcat['language'] = $cat['rlanguage'];
                $xcat['views'] = $cat['hits'];
                $xcat['date_created'] = $cat['date'];

                $doc->delFilter('dbfilter');
                $output = array("id", "parent_id", "title", "date_created", "owner_id", "publish", "access", "text1", "import");
                $doc->setFilter("dbfilter", "import", "=", "'" . $xcat['import'] . "'");
                $dbfilter = $doc->getFilter("dbfilter");
                $iscats = $doc->getRecords($dbfilter);
                $iscat = array('import' => "");
                foreach($iscats as $iat) {
                    $iscat = $iat;
                }
                if ($iscat['import'] == $xcat['import']) {
                    $cat_id = $iscat['id'];
                } else {
                    $cat_id = $doc->addRecord($content_id, $xcat);
                    $irecord++;
                }
            }
            break;
        case "Sections":
//            $contentbook = $doc->getRecordDefault();
//            $content_id = 0;
//
//            $booklist = $doc->getRecordList(0);
//            $contentbook['title'] = (trim($bookname) == "")?$contentbook['title'] = $module:$bookname;
//            $contentbook['language'] = "ALL";
            foreach($booklist as $books) {
                $content_id = ($books['title'] == $contentbook['title'])?$books['id']:$content_id;
            }
            unset($booklist);

            if ($content_id == 0) {
                $content_id = $doc->addRecord(0, $contentbook);
                $irecord++;
            }
            $catarray = array();
            $resultcat = sql_query("SELECT * FROM ${prefix}_sections");
            $resulttopics = sql_query("SELECT * FROM ${prefix}_topics");
            while ($result = sql_fetch_assoc($resulttopics)) {
                $topics[$result['topicid']] = $result['topictext'];
            } while ($cat = sql_fetch_assoc($resultcat)) {
                $iscats = array();
                $xcat = $doc->getRecordDefault();
                $xcat['title'] = $cat['secname'];
                $xcat['text1'] = $cat['secname'];
                $xcat['mid'] = $cat['secid'];
                $xcat['import'] = "" . $module . "-" . $cat['secid'] . "";

                $doc->delFilter('dbfilter');
                $output = array("id", "parent_id", "title", "date_created", "owner_id", "publish", "access", "text1", "import");
                $doc->setFilter("dbfilter", "import", "=", "'" . $xcat['import'] . "'");
                $dbfilter = $doc->getFilter("dbfilter");
                $iscats = $doc->getRecords($dbfilter);
                $iscat = array('import' => "");
                foreach($iscats as $iat) {
                    $iscat = $iat;
                }
                if ($iscat['import'] == $xcat['import']) {
                    $cat_id = $iscat['id'];
                } else {
                    $cat_id = $doc->addRecord($content_id, $xcat);
                    $irecord++;
                }
                $catarray[$cat['secid']] = $cat_id;
            }
            $xcat = array();
            $resultcontent = sql_query("SELECT * FROM ${prefix}_seccont");
            while ($cat = sql_fetch_assoc($resultcontent)) {
                $doc->delFilter('dbfilter');
                $iscats = array();
                $xcat = $doc->getRecordDefault();
                $xcat['title'] = $cat['title'];
                $xcat['text1'] = $cat['content'];
                $xcat['mid'] = $cat['artid'];
                $xcat['language'] = $cat['slanguage'];
                $xcat['views'] = $cat['counter'];
                $xcat['import'] = "" . $module . "-" . $cat['secid'] . "-" . $cat['artid'] . "";

                $output = array("id", "parent_id", "title", "date_created", "owner_id", "publish", "access", "text1", "import");
                $doc->setFilter("dbfilter", "import", "=", "'" . $xcat['import'] . "'");
                $dbfilter = $doc->getFilter("dbfilter");
                $iscats = $doc->getRecords($dbfilter);
                $iscat = array('import' => "");
                foreach($iscats as $iat) {
                    $iscat = $iat;
                }
                if ($iscat['import'] == $xcat['import']) {
                    // $cat_id=$doc->updateRecord($catarray[$cat['cid']],$xcat);
                } else {
                    $cat_id = $doc->addRecord($catarray[$cat['secid']], $xcat);
                    $irecord++;
                }
            }
            break;
        case "Stories":
//            $contentbook = $doc->getRecordDefault();
//            $content_id = 0;
//
//            $booklist = $doc->getRecordList(0);
//            $contentbook['title'] = (trim($bookname) == "")?$contentbook['title'] = $module:$bookname;
//            $contentbook['language'] = "ALL";
            foreach($booklist as $books) {
                $content_id = ($books['title'] == $contentbook['title'])?$books['id']:$content_id;
            }
            unset($booklist);

            if ($content_id == 0) {
                $content_id = $doc->addRecord(0, $contentbook);
                $irecord++;
            }
            $catarray = array();
            $resultcat = sql_query("SELECT * FROM ${prefix}_stories_cat");
            $resulttopics = sql_query("SELECT * FROM ${prefix}_topics");
            while ($result = sql_fetch_assoc($resulttopics)) {
                $topics[$result['topicid']] = $result['topictext'];
            } while ($cat = sql_fetch_assoc($resultcat)) {
                $iscats = array();
                $xcat = $doc->getRecordDefault();
                $xcat['title'] = $cat['title'];
                $xcat['text1'] = sql_real_escape_string($cat['title']);
                $xcat['mid'] = $cat['catid'];
                $xcat['import'] = "" . $module . "-" . $cat['catid'] . "";

                $doc->delFilter('dbfilter');
                $output = array("id", "parent_id", "title", "date_created", "owner_id", "publish", "access", "text1", "import");
                $doc->setFilter("dbfilter", "import", "=", "'" . $xcat['import'] . "'");
                $dbfilter = $doc->getFilter("dbfilter");
                $iscats = $doc->getRecords($dbfilter);
                $iscat = array('import' => "");
                foreach($iscats as $iat) {
                    $iscat = $iat;
                }
                if ($iscat['import'] == $xcat['import']) {
                    $cat_id = $iscat['id'];
                } else {
                    $cat_id = $doc->addRecord($content_id, $xcat);
                    $irecord++;
                }
                $catarray['0'] = $content_id;
                $catarray[$cat['catid']] = $cat_id;
            }
            $xcat = array();
            $resultcontent = sql_query("SELECT * FROM ${prefix}_stories");
            while ($cat = sql_fetch_assoc($resultcontent)) {
                $doc->delFilter('dbfilter');
                $iscats = array();
                $xcat = $doc->getRecordDefault();
                $xcat['title'] = sql_real_escape_string($cat['title']);
                $xcat['text1'] = sql_real_escape_string($cat['hometext'] . "<br /><br />" . $cat['bodytext']);
                $xcat['mid'] = $cat['sid'];
                $xcat['language'] = $cat['alanguage'];
                $xcat['views'] = $cat['counter'];
                $xcat['import'] = "" . $module . "-" . $cat['catid'] . "-" . $cat['sid'] . "";
                $xcat['keywords'] = "";
                $xcat['rating'] = $cat['ratings'];
                $xcat['uname'] = $cat['aid'];
                $xcat['date_created'] = strtotime($cat['time']);
                //$xcat['owner_id'] = $cat['aid'];

                $output = array("id", "parent_id", "title", "date_created", "owner_id", "publish", "access", "text1", "import");
                $doc->setFilter("dbfilter", "import", "=", "'" . $xcat['import'] . "'");
                $dbfilter = $doc->getFilter("dbfilter");
                $iscats = $doc->getRecords($dbfilter);
                $iscat = array('import' => "");
                foreach($iscats as $iat) {
                    $iscat = $iat;
                }
                if ($iscat['import'] == $xcat['import']) {
                    // $cat_id=$doc->updateRecord($catarray[$cat['cid']],$xcat);
                } else {
                    $cat_id = $doc->addRecord($catarray[$cat['catid']], $xcat);
                    $irecord++;
                }
            }
            break;
        case "Encyclopedia":
//            $contentbook = $doc->getRecordDefault();
//            $content_id = 0;
//
//            $booklist = $doc->getRecordList(0);
//            $contentbook['title'] = (trim($bookname) == "")?$contentbook['title'] = $module:$bookname;
//            $contentbook['language'] = "ALL";
            foreach($booklist as $books) {
                $content_id = ($books['title'] == $contentbook['title'])?$books['id']:$content_id;
            }
            unset($booklist);

            if ($content_id == 0) {
                $content_id = $doc->addRecord(0, $contentbook);
                $irecord++;
            }
            $catarray = array();
            $resultcat = sql_query("SELECT * FROM ${prefix}_encyclopedia");

            while ($cat = sql_fetch_assoc($resultcat)) {
                $iscats = array();
                $xcat = $doc->getRecordDefault();
                $xcat['title'] = $cat['title'];
                $xcat['text1'] = $cat['description'];
                $xcat['mid'] = $cat['eid'];
                $xcat['import'] = "" . $module . "-" . $cat['eid'] . "";
                $xcat['language'] = $cat['elanguage'];

                $doc->delFilter('dbfilter');
                $output = array("id", "parent_id", "title", "date_created", "owner_id", "publish", "access", "text1", "import");
                $doc->setFilter("dbfilter", "import", "=", "'" . $xcat['import'] . "'");
                $dbfilter = $doc->getFilter("dbfilter");
                $iscats = $doc->getRecords($dbfilter);
                $iscat = array('import' => "");
                foreach($iscats as $iat) {
                    $iscat = $iat;
                }
                if ($iscat['import'] == $xcat['import']) {
                    $cat_id = $iscat['id'];
                } else {
                    $cat_id = $doc->addRecord($content_id, $xcat);
                    $irecord++;
                }
                $catarray[$cat['eid']] = $cat_id;
            }
            $xcat = array();
            $resultcontent = sql_query("SELECT * FROM ${prefix}_encyclopedia_text");
            while ($cat = sql_fetch_assoc($resultcontent)) {
                $doc->delFilter('dbfilter');
                $iscats = array();
                $xcat = $doc->getRecordDefault();
                $xcat['title'] = $cat['title'];
                $xcat['text1'] = $cat['text'] . "";
                $xcat['mid'] = $cat['tid'];
                $xcat['views'] = $cat['counter'];
                $xcat['import'] = "" . $module . "-" . $cat['eid'] . "-" . $cat['tid'] . "";

                $output = array("id", "parent_id", "title", "date_created", "owner_id", "publish", "access", "text1", "import");
                $doc->setFilter("dbfilter", "import", "=", "'" . $xcat['import'] . "'");
                $dbfilter = $doc->getFilter("dbfilter");
                $iscats = $doc->getRecords($dbfilter);
                $iscat = array('import' => "");
                foreach($iscats as $iat) {
                    $iscat = $iat;
                }
                if ($iscat['import'] == $xcat['import']) {
                    // $cat_id=$doc->updateRecord($catarray[$cat['cid']],$xcat);
                } else {
                    $cat_id = $doc->addRecord($catarray[$cat['eid']], $xcat);
                    $irecord++;
                }
            }
            break;
    }
	unset($xcat,$iscat,$iscats,$dbfilter,$cat,$catarray,$resultcontent,$resultcat,$resulttopics);
    return $irecord;
}

function book_viewlog($id)
{
    global $doc_cfg, $module_name, $currentlang, $doc, $prefix;

    echo $doc->getLogHTML($id);

    exit;
}

function book_dellog($id)
{
    global $doc_cfg, $module_name, $currentlang, $doc, $prefix;

    $doc->_deleteLogFromId($id);
    echo _DOCS_DB_DELLOG_ACTION;
    exit;
}
function content_moveup($cid)
{
    global $doc_cfg, $module_name, $credits, $setup_result, $prefix, $doc;
    $doc->move_up($cid);
    return;
}
function content_movedn($cid)
{
    global $doc_cfg, $module_name, $credits, $setup_result, $prefix, $doc;
    $doc->move_dn($cid);
    return;
}
function content_publish($cid)
{
    global $doc_cfg, $module_name, $credits, $setup_result, $prefix, $doc;
    $doc->publish($cid);
    return;
}
function content_unpublish($cid)
{
    global $doc_cfg, $module_name, $credits, $setup_result, $prefix, $doc;
    $doc->unpublish($cid);
    return;
}
function content_setstartpage($cid)
{
    global $doc_cfg, $module_name, $credits, $setup_result, $prefix, $doc;
    $doc->setstartpage($cid);
    return;
}
function content_unsetstartpage($cid)
{
    global $doc_cfg, $module_name, $credits, $setup_result, $prefix, $doc;
    $doc->unsetstartpage($cid);
    return;
}

function content_setmoduleactive($active=1) {
    global $doc_cfg, $module_name, $credits, $setup_result, $prefix, $doc;
	mxSetModuleActive($module_name,$active);
	return;
}
?>