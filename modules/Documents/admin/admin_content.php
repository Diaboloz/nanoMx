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
 * $Revision: 214 $
 * $Author: PragmaMx $
 * $Date: 2016-09-15 15:51:34 +0200 (Do, 15. Sep 2016) $
 */

defined('mxMainFileLoaded') or die('access denied');

if (!mxGetAdminPref('radmincontent')) {
    mxErrorScreen('Access Denied');
    die();
}

switch ($act) {
    case "startpage":
        content_startpage($id);
        exit;
        break;
    case "spsetstartpage":
        content_setstartpage($id);
        content_startpage();
        break;
    case "spunsetstartpage":
        content_unsetstartpage($id);
        content_startpage();
        break;
    case "startpagesave":
        content_publish($id);
        content_startpage();
        break;
    case "startpagepublish":
        content_publish($id);
        content_startpage();
        break;
    case "startpageunpublish":
        content_unpublish($id);
        content_startpage();
        break;
    case "setstartpage":
        content_setstartpage($id);
        content_main();
        break;
    case "unsetstartpage":
        content_unsetstartpage($id);
        content_main();
        break;
    case "contentpublish":
        content_publish($id);
        content_main();
        break;
    case "contentunpublish":
        content_unpublish($id);
        content_main();
        break;
    case "contentmoveup":
        content_moveup($id);
        content_main();
        break;
    case "contentmovedn":
        content_movedn($id);
        content_main();
        break;
    case "contentmove":
        content_move($cid);
        exit;
        break;
    case "contentdelete":
        content_delete($cid);
        exit;
        break;
    case "contentedit":
        content_edit($id);
        exit;
        break;
    case "contentnew":
        content_edit(0,true);
        exit;
        break;
//    case "getmenu":
//        content_menu($id);
//        exit;
//        break;
    default:
        content_main($book, $page);
        break;
}

function content_main($book = 0, $page = 1)
{
    global $doc_cfg, $module_name, $credits, $setup_result, $prefix, $doc;

    $info = "";

    $cat = $doc;

    $user = $doc->getUser();
	$catchange=(isset($_POST['catfilter']))?(($_POST['catfilter']==mxSessionGetVar("bookfilter"))?false:true):false;
    /* ausgangswerte festlegen */
    $catfilter = ($book == 0)?((isset($_POST['catfilter']))?$_POST['catfilter']:((mxSessionGetVar("bookfilter"))?mxSessionGetVar("bookfilter"):0)):$book;
    $docfilter = (!$catchange)?((isset($_POST['docfilter']))?$_POST['docfilter']:((mxSessionGetVar("docfilter"))?mxSessionGetVar("docfilter"):0)):0;
    $pagelimit = (isset($_POST['pagelimit']))?$_POST['pagelimit']:((mxSessionGetVar("bookpagelimit"))?mxSessionGetVar("bookpagelimit"):25);
    $pagestart = (isset($_POST['page']))?$_POST['page']:((mxSessionGetVar("bookpagestart"))?mxSessionGetVar("bookpagestart"):1);
    $pagecount = (isset($_POST['pagecount']))?$_POST['pagecount']:((mxSessionGetVar("bookpagecount"))?mxSessionGetVar("bookpagecount"):0);
    $pagestart = ($pagelimit != mxSessionGetVar("bookpagelimit"))?1:$pagestart;
	//$pagestart = ($catchange)?0:$pagestart;
	
    switch (pmxAdminForm::CheckButton()) {
        case "cpanel":
            mxRedirect("admin.php?op=$module_name", "", 0);
            break;
        case "add":
            $cid = intval((isset($_POST['cid']))?$_POST['cid'][0]:$catfilter);
            //$cat->addRecord($cid, $cat->getRecordDefault());
			content_edit($cid,true);
			exit;
            break;
        case "delete":
            $cid = $_POST['cid'];
            content_delete($cid);
            exit;
            break;
        case "copy":
            foreach($_POST['cid'] as $dummy => $id) {
                $cat->copyRecord(intval($id));
            }
            break;
        case "publish":
            foreach($_POST['cid'] as $dummy => $id) {
                $cat->publish(intval($id));
            }
            break;
        case "unpublish":
            foreach($_POST['cid'] as $dummy => $id) {
                $cat->unpublish(intval($id));
            }
            break;
        case "archive":
            foreach($_POST['cid'] as $dummy => $id) {
                $cat->archive(intval($id));
            }
            break;
        case "move":
            $cid = $_POST['cid'];
            content_move($cid);
            exit;
            break;
        case "nextpage":
            $pagestart++;
            break;
        case "prepage":
            $pagestart--;
            break;
        case "firstpage":
            $pagestart = 1;
            break;
        case "lastpage":
            $pagestart = intval($_POST['pagecount']);
            break;
        case "gotopage":
            $pagestart = intval($_POST['page']);
            break;
        case "edit":
            if (isset($_POST['cid']))content_edit(intval($_POST['cid'][0]));
            exit;
            break;
        default:
            // $info=pmxAdminForm::CheckButton(); // Test
            break;
    }
    /* Form starten */
    $tb = load_class('AdminForm');;
    $tb->__set('target_url', "admin.php?op=" . $module_name . "&amp;act=content");

    $tb->__set("tb_text", $info);
    $tb->__set("tb_direction", 'right');
    $tb->__set("infobutton", true);
    $tb->__set("tb_pic_heigth", 25);
    $tb->__set('homelink', false);

    $tb->addToolbar("editx");
    $tb->addToolbar("publishX");
    $tb->addToolbar("unpublishX");
    $tb->addToolbar("add");
    $tb->addToolbar("movex", "", "images/adminform/move_to_folder.png");
	//$tb->addToolbar("exportX");
	//$tb->addToolbar("importX");
    $tb->addToolbar("copyx");
    $tb->addToolbar("deleteX");
    $tb->addToolbarLink("startpage", "admin.php?op=$module_name&act=startpage", _DOCS_STARTPAGE, "images/rating/star-on.png");
    $tb->addToolbarLink("cpanel", "admin.php?op=$module_name");

    /*
     * Template
     */
    // Template initialisieren
    $template = load_class('Template');
    $template->init_path(__FILE__);

    include('header.php');

    $formOpen = $tb->FormOpen();

    $toolbar = $tb->getToolbar();

    /* filter aufbauen */
    //$filter = "s.parent_id=" . $cat->getRootID();

		/* Filter Books */
		$output = array("id", "title");
		$cat->setFilter("booklist", "parent_id", "=", $cat->getRootID());
		$catfilter2 = $cat->getRecordList($cat->getRootID(), $output, "booklist");
		$selectfilter = "<select class=\"form\" name=\"catfilter\" size=\"1\" onchange=\"document[adminForm].submit();\" >";
	
		foreach($catfilter2 as $node) {
		  if ($node['level']<3) {
			$selected = ($node['id'] == $catfilter)?"selected=\"selected\"":"";
			$selectfilter .= "<option value=\"" . $node['id'] . "\" " . $selected . " >" . str_repeat("-",intval($node['level']-1)). " ".$node['title'] . "&nbsp;&nbsp;</option>";
		  }
		}
		$selectfilter .= "</select>";

	/* filter Documente*/
	
		
		$cat->setFilter("contentlist", "parent_id", "=", $catfilter);
		$catfilter3 = $cat->getRecordList($cat->getRootID(), $output, "contentlist");
		$selectfilter2 = "<select class=\"form\" name=\"docfilter\" size=\"1\" onchange=\"document[adminForm].submit();\" >";
			$selectfilter2 .= "<option value=\"0\" >" . _ALL . "&nbsp;&nbsp;</option>";

		foreach($catfilter3 as $node) {
		  if ($node['childs']>0) {
			$selected = ($node['id'] == $docfilter)?"selected=\"selected\"":"";
			if ($selected != "") $docfilter=$node['id'];
			$selectfilter2 .= "<option value=\"" . $node['id'] . "\" " . $selected . " >" . str_repeat("-",intval($node['level']-1)). " ".$node['title'] . "&nbsp;&nbsp;</option>";
		  }
		}
		$selectfilter2 .= "</select>";
		
		$selecteddoc=($docfilter==0)?$catfilter:$docfilter;
		$docfilterroot=$cat->getRecord($selecteddoc);
		$docfilterroot =($docfilterroot['id']==0 or $docfilterroot==NULL)?$cat->getRecord($catfilter):$docfilterroot;
		
    /* hier die Ausgabefelder angeben */
    $output = array("id", "parent_id", "title", "date_created", "owner_name", "publish", "access", "date_edit", "edit_uname", "views", "position");

    $cat->setFilter("contentroot", "id", " NOT IN", "('" . $selecteddoc . "')");
    if ($selecteddoc > 0 && !$catchange ) { 
			$cat->setFilter("contentroot", "leftID", ">",  $docfilterroot['leftID'] );
			$cat->setFilter("contentroot", "rightID", "<",  $docfilterroot['rightID'] );
	}
	$catlist = $cat->getRecordList($selecteddoc, $output, "contentroot", max(($pagestart-1) * $pagelimit, 0),0);// $pagelimit
    $count = $cat->contentcount();
    $breadcrump = $cat->getBreadcrump($catfilter, false);
    //$cat->delFilter("contentroot");
	
    /* Seitenzahlen berechnen */
    $pagecount = (intval($count / $pagelimit) == ($count / $pagelimit))? intval($count / $pagelimit):intval($count / $pagelimit) + 1;
    $pagestart = min($pagestart, $pagecount);
    $pagestart = max($pagestart, 1);
    $pagestart = ($pagestart * $pagelimit > $count)?intval($count / $pagelimit) + 1 :$pagestart;
    $prepage = ($pagestart > 1)?$pagestart-1:0;
    $nextpage = (($pagestart * $pagelimit) < $count)?$pagestart + 1:0;
    $pageview = true;
    $catlist = $cat->getRecordList($catfilter, $output, "contentroot", max(($pagestart-1) * $pagelimit, 0), $pagelimit);
    //$count = $cat->contentcount();

	
    /* Form schliessen */
    $formClose = $tb->FormClose();

    /* benötigte Werte in der Session speichern */
    mxSessionSetVar("bookfilter", $catfilter);
    mxSessionSetVar("bookpagelimit", $pagelimit);
    mxSessionSetVar("bookpagestart", $pagestart);
    mxSessionSetVar("bookpagecount", $pagelimit);
    mxSessionSetVar("docfilter", $docfilter);
    // Template initialisieren
    $template = load_class('Template');
    $template->init_path(__FILE__);

    /* Variablen an das Template uebergeben */
    $template->assign(compact('toolbar',
            'catlist',
            'formOpen',
            'formClose',
            'credits',
            'module_name',
            'count',
            'selectfilter',
            'selectfilter2',
            'prepage',
            'nextpage',
            'pageview',
            'pagestart',
            'pagelimit',
            'pagecount',
            'breadcrump'
            ));

    /* Template ausgeben (echo) */
    include('header.php');

    $template->display('admin/admin.content.html');

    include('footer.php');
}

function content_startpage($book = 0, $page = 1)
{
    global $doc_cfg, $module_name, $credits, $setup_result, $prefix, $doc;

    $info = "";

    $cat = $doc;

    $user = $doc->getUser();

    /* ausgangswerte festlegen */
    // $catfilter=($book==0)?((isset($_POST['catfilter']))?$_POST['catfilter']:((mxSessionGetVar("bookfilter"))?mxSessionGetVar("bookfilter"):0)):$book;
    $pagelimit = (isset($_POST['pagelimit']))?$_POST['pagelimit']:((mxSessionGetVar("bookpagelimit"))?mxSessionGetVar("bookpagelimit"):25);
    $pagestart = (isset($_POST['page']))?$_POST['page']:((mxSessionGetVar("bookpagestart"))?mxSessionGetVar("bookpagestart"):1);
    $pagecount = (isset($_POST['pagecount']))?$_POST['pagecount']:((mxSessionGetVar("bookpagecount"))?mxSessionGetVar("bookpagecount"):0);
    $pagestart = ($pagelimit != mxSessionGetVar("bookpagelimit"))?1:$pagestart;

    switch (pmxAdminForm::CheckButton()) {
        case "cpanel":
            mxRedirect("admin.php?op=$module_name", "", 0);
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
        case "save":
            $cat->renumstartpage($_POST['position']);
            break;
        case "nextpage":
            $pagestart++;
            break;
        case "prepage":
            $pagestart--;
            break;
        case "firstpage":
            $pagestart = 1;
            break;
        case "lastpage":
            $pagestart = $_POST['pagecount'];
            break;
        default:
            // $info=pmxAdminForm::CheckButton(); // Test
            break;
    }
    /* Form starten */
    $tb = load_class('AdminForm');;
    $tb->__set('target_url', "admin.php?op=" . $module_name . "&amp;act=startpage");

    $tb->__set("tb_text", $info);
    $tb->__set("tb_direction", 'right');
    $tb->__set("infobutton", true);
    $tb->__set("tb_pic_heigth", 25);
    $tb->__set('homelink', false);

    $tb->addToolbar("save", "", "", _DOCS_POSITION . " " . _SAVE);
    $tb->addToolbar("publishX");
    $tb->addToolbar("unpublishX");
    $tb->addToolbarLink("cpanel", "admin.php?op=$module_name");

    /*
     * Template
     */
    // Template initialisieren
    $template = load_class('Template');
    $template->init_path(__FILE__);

    include('header.php');

    $formOpen = $tb->FormOpen();

    $toolbar = $tb->getToolbar();

    $catlist = $cat->getRecords_StartPage(true);
    $count = $cat->contentcount();

    /* Seitenzahlen berechnen */
    $pagecount = (intval($count / $pagelimit) == ($count / $pagelimit))?intval($count / $pagelimit):intval($count / $pagelimit) + 1;
    $pagestart = min($pagestart, $pagecount);
    $pagestart = max($pagestart, 1);
    $pagestart = ($pagestart * $pagelimit > $count)?intval($count / $pagelimit) + 1 :$pagestart;
    $prepage = ($pagestart > 1)?$pagestart-1:0;
    $nextpage = (($pagestart * $pagelimit) < $count)?$pagestart + 1:0;
    $pageview = true;

    /* Form schliessen */
    $formClose = $tb->FormClose();

    /* benötigte Werte in der Session speichern */
    mxSessionSetVar("bookpagelimit", $pagelimit);
    mxSessionSetVar("bookpagestart", $pagestart);
    mxSessionSetVar("bookpagecount", $pagelimit);
    // Template initialisieren
    $template = load_class('Template');
    $template->init_path(__FILE__);

    /* Variablen an das Template uebergeben */
    $template->assign(compact('toolbar',
            'catlist',
            'formOpen',
            'formClose',
            'credits',
            'module_name',
            'count',
            'prepage',
            'nextpage',
            'pageview',
            'pagestart',
            'pagelimit',
            'pagecount',
            'breadcrump',
            'doc'
            ));

    /* Template ausgeben (echo) */
    include('header.php');

    $template->display('admin/admin.startpage.html');

    include('footer.php');
}

function content_edit($id, $new=false)
{
    global $doc_cfg, $module_name, $doc;

    $user = $doc->getUser();
    
	$returnflag = false;
    $cat = $doc;
    $doc_cfg = $cat->getConfig();
    $err = "";
    $info2 = imgModul() . " " . _DOCS_TITLE . " - " . _EDIT;
    $bookid = $cat->getBreadcrump($id);

    switch (pmxAdminForm::CheckButton()) {
        case "save":
            $returnflag = true;
        case "accept":
            $new = intval($_POST['new']);
            $sid = intval($_POST['sectionid']);
            if ($new == 1) {
                $content = $cat->getRecordDefault();
                $id = $cat->addRecord($sid, $content);
				$bookid = $cat->getBreadcrump($id);
            }
            $content = array();
            $_POST['info']['alternate'] = mxAddSlashesForSQL(trim(htmlspecialchars($_POST['info']['alternate'])));
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
            $content['status'] = 0;
			$content['date_created']=string2timestamp($content['date_created'], _SHORTDATESTRING);
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
                        $file['filename'] = trim($temppath . "/" . $newname);
                        $file['type'] = $type;
                        $file['hash'] = md5($newname);
                        $file['name'] = $_FILES['attachment']['name'][$cid];
                        $file['title'] = strip_tags($_FILES['attachment']['name'][$cid]);
                        $file['filesize'] = intval(filesize($temppath . "/" . $newname) / 10.24) / 100;
                        $file['id'] = $id;//count($uploadfiles) + 1;
                        if ($file['filesize'] > $doc_cfg['attmaxsize']) {
                            $file['error'] = _DOCS_ERR_FILESIZE;
                            unlink($file['filename']);
                        }
                        $uploadfiles[] = $file;
                    } else {
                        $file['error'] = $error;
                        $file['filename'] = $_FILES['attachment']['name'][$cid];
                        $file['filesize'] = intval($_FILES['attachment']['size'][$cid] / 10.24) / 100;
                        $file['name'] = $_FILES['attachment']['name'][$cid];
						//$uploadfiles[] = $file;
                    }
                }
            }
            /* uploads ende */

            $content['attachment'] = serialize($uploadfiles);

            if (!$err) {
                $cat->updateRecord ($id, $content);
                unset($content);
                if ($returnflag) {
					 	mxRedirect("admin.php?op=$module_name&act=content&book=" . $bookid[0]['id'], _CHANGESAREOK, 1);
				} 
            }
            if ($err) mxRedirect("admin.php?op=$module_name&act=contentedit&id=" . $id, _CHANGESNOTOK . " <br> " . _ERROROCCURS . "<br>" . _ERRNOTITLE, 3);
			$new=false;
            break;
        default:
            break;
    }

    /* hier die Ausgabefelder angeben */
    $output = array("id", "parent_id", "title", "date_created", "owner_id", "publish", "access", "text1", "info");

	if ($id==0)$id=$doc->getBookRootID();
	
    if ($new == true) {
        $content = array();
        $content['id'] = 0;
        $book = $cat->getBookRoot($id);
        if ($book['id'] == 0)$book = $cat->getRecord($id);
        $section = $cat->getRecord($id);
        if ($section['id'] == 0) $section['id']=$book['id'];//mxErrorScreen(_ERR_YOUBAD);
        $content = $cat->getRecordDefault();
        $config = array_merge($doc_cfg, unserialize($content['config']));
        $content['attachment'] = unserialize($content['attachment']);
        $info = unserialize($section['info']);
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

/*   */

    /* hier die Ausgabefelder angeben */
	$sqlfilter="";
    $output = array("id", "parent_id", "title", "date_created", "owner_id", "publish", "access", "text1");
    $sqlfilter .= " AND s.leftID NOT BETWEEN " . $book['leftID'] . " AND " . $book['rightID'];
    $targetcontent = $cat->getRecordList(0, $output, " " . $sqlfilter . " ");
    foreach($targetcontent as $node) {
		$key=str_repeat("&nbsp;&nbsp;&nbsp;", $node['level']-1) . $node['title'];
        if ($node['parent_id'] == $book['id']) {
			$key="<b>".$key."</b>";
        }
		$sectionselect[$key]=$node['id'];
		unset($key);
    }
    



/*   */
	
    $tb = load_class('AdminForm');;
    $tb->__set('target_url', "admin.php?op=" . $module_name . "&amp;act=contentedit");
    $tb->__set("tb_text", "");
    $tb->__set("tb_direction", 'right');
    $tb->__set("infobutton", true);
    $tb->__set("tb_pic_heigth", 25);
    $tb->__set('homelink', false);
    $tb->__set('fieldhomebutton', true);
	$tb->__set("cssform", "a306010");
    $tb->addToolbar("accept");
    $tb->addToolbar("save");
    $tb->addToolbarLink("cancel", "admin.php?op=$module_name&amp;act=content&book=" . $bookid[0]['id']);
    $tb->addToolbarLink("cpanel", "admin.php?op=$module_name");

    /* Form elements */
    $tb->addFieldSet("head", _DOCS_CONTENT_TITLE, "", false);
    $tb->addFieldSet("content", _DOCS_CONTENT_EDIT, "", false);
    $tb->addFieldSet("attachments", _DOCS_ATTACHMENTS, "", true);
    $tb->addFieldSet("extended", _DOCS_EXTENDET_SETTINGS, "", true, array("style" => "width:49%;"));
    $tb->addFieldSet("metapage", _DOCS_META_PAGE, "", true, array("style" => "width:49%;float:right;"));
    $tb->add("", "html", "<span class=\"tiny\">" . _DOCS_LASTCHANGE . " : " . mx_strftime(_DATESTRING . " %H:%M:%S", $content['date_created']) 
		 . (($new)?"</span>":
		   " "._FROM." ".$content['edit_uname']."</span>"
         . "<span style=\"float:right;margin:5px;\"  ><a class=\"button\" href=\"admin.php?op="
         . $module_name . "&amp;act=getlog&amp;id=" . $content['id']
         . "&amp;iframe=true&amp;width=80%&amp;height=80%\" title=\"" . _DOCS_VIEW_LOG . "\" rel=\"pretty\">"
         . "<img src=\"images/adminform/page.png\" width=\"26\" height=\"26\" alt=\"" . _DOCS_HISTORY . "\" /></a>"
         . "<a class=\"button\" href=\"modules.php?name=" . $module_name . "&amp;act=page&amp;id=" . $content['id'] . "&amp;iframe=true&amp;width=80%&amp;height=80%\" rel=\"pretty\" title=\"" . _PREVIEW . "\" >"
         . "<img src=\"images/adminform/preview.png\" width=\"26\" height=\"26\" alt=\"" . _PREVIEW . "\" /></a>"
         . "</span>"));
    $tb->add("", "date", "date_created", mx_strftime(_SHORTDATESTRING , $content['date_created']), _DOCS_CREATED . " ["._SHORTDATESTRING."]", "", 10);
    $tb->add("", "html", "");
    //$tb->add("", "input", "booktitle", $book['title'], _DOCU, "", 60, "readonly=\"readonly\"");
    //$tb->add("", "input", "booksection", $section['title'], _DOCS_SECTION, "", 60, "readonly=\"readonly\"");
	$tb->add("", "select", "sectionid", $section['id'], _DOCS_SECTION, "", 1, $sectionselect);
	//$tb->add("", "html",$selectfilter);
    $tb->add("", "hidden", "new", $snew);
    $tb->add("", "hidden", "id", $content['id']);
    //$tb->add("", "hidden", "bookid", $book['id']);
    $tb->add("", "hidden", "edit_uid", $user['uid']);
    $tb->add("", "hidden", "edit_uname", $user['uname']);
    $tb->add("", "hidden", "act", "contentedit");
    $tb->add("", "input", "views", $content['views'], _DOCS_ACCESS, "", 8);
    //$tb->add("", "hidden", "sectionid", $section['id']);
    $tb->add("head", "input", "title", $content['title'], _DOCS_CONTENT_TITLE, _DOCS_CONTENT_TITLE_TEXT, 50);
    $tb->add("head", "input", "alias", $content['alias'], _DOCS_ALIAS, _DOCS_ALIAS_TEXT, 50);
    $tb->add("head", "input", "owner_name", $content['owner_name'], _DOCS_OWNER, _DOCS_OWNER, 50);
    $tb->add("head", "yesno", "publish", $content['publish'], _DOCS_PUBLISHED);
    $tb->add("head", "yesno", "position", $content['position'], _DOCS_STARTPAGE_ON, _DOCS_STARTPAGE_TEXT);
    $tb->add("content", "output", _DOCS_CONTENT_EDIT);
    $tb->add("content", "editor", "text1", $content['text1'], "", "", 300,true,true);

    //$content['attachment'] = unserialize($content['attachment']);
    $attachments = (is_array($content['attachment']))?$content['attachment']:array();
    $attcount = count($attachments);
    $attmaxcount = $doc_cfg['attcount'];
    if ($attcount) {
        $i = 0;
        $alist = "";
        foreach ($attachments as $file) {
            if ($file['error']) {
                $tb->add("attachments", "output", "<span class=\"warning\">" . $file['name'] . " - " . $file['error'] . " = " . $file['filesize'] . " kByte </span>");
                //  @unlink ($file['name']);
                // $tb->add("attachments", "hidden", "attachments[$i][delete]", "1", _DELETE, _DOCS_ATTACH_DELETE);
                // $i++;
                $attcount--;
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
            $tb->add("attachments", "file", "attachment", "", _DOCS_ATTACHMENTS,_DOCS_ATTACHMENT,30);
        }
    }
    content_getPageConfigForm($tb, $config);

    content_getPageMetaForm($tb, $config, $content, $info) ;

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
    $template->display('admin/admin.contentedit.html');
    include('footer.php');
}

function content_move($ids = array())
{
    global $doc_cfg, $module_name, $doc;

    $returnflag = false;
    $cat = $doc;

    $info2 = imgModul() . " " . _DOCS_TITLE . " - " . _DOCS_MOVE;

    switch (pmxAdminForm::CheckButton()) {
        case "save":
            foreach($_POST['cid'] as $id) {
                $cat->moveRecord ($id, $_POST['targetid']);
            }
            mxRedirect("admin.php?op=$module_name&act=content", _CHANGESAREOK, 2);
            break;
        default:
            break;
    }

    /* hier die Ausgabefelder angeben */
    $output = array("id", "parent_id", "title", "date_created", "owner_id", "publish", "access", "text1");

    $catlist = $cat->getRecordsFromID($ids);

    $sqlfilter = "";
    foreach ($catlist as $id) {
        $sqlfilter .= " AND s.leftID NOT BETWEEN " . $id['leftID'] . " AND " . $id['rightID'];
    }

    $targetcontent = $cat->getRecordList(0, $output, " " . $sqlfilter . " ");
    $count = count($ids);

    $tb = load_class('AdminForm');;
    $tb->__set('target_url', "admin.php?op=" . $module_name . "&amp;act=contentmove");
    $tb->__set("tb_text", "");
    $tb->__set("tb_direction", 'right');
    $tb->__set("infobutton", true);
    $tb->__set("tb_pic_heigth", 25);

    $tb->addToolbar("save");
    $tb->addToolbarLink("cancel", "admin.php?op=$module_name&amp;act=content");
    $tb->addToolbarLink("cpanel", "admin.php?op=$module_name");

    $formOpen = $tb->FormOpen();
    $toolbar = $tb->getToolbar();
    $formClose = $tb->FormClose();

    $selectfilter = "<select class=\"form\" name=\"targetid\" size=\"20\" >";
    $startflag = false;
    foreach($targetcontent as $node) {
        if ($node['parent_id'] == $cat->getRootID()) {
            $selectfilter .= ($startflag)?"</optgroup>":"";
            $selectfilter .= "<optgroup label=\"" . $node['title'] . "\">";
            $startflag = true;
        }
        $selectfilter .= "<option value=\"" . $node['id'] . "\" >" . str_repeat("&nbsp;&nbsp;&nbsp;", $node['level']-1) . $node['title'] . "&nbsp;&nbsp;</option>";
    }
    $selectfilter .= "</optgroup></select>";

    /*
     * Template
     */
    // Template initialisieren
    $template = load_class('Template');
    $template->init_path(__FILE__);

    /* Variablen an das Template uebergeben */
    $template->assign(compact('toolbar', 'info2', 'formOpen', 'formClose', 'count', 'catlist', 'module_name', 'selectfilter'));

    include('header.php');
    /* Template ausgeben (echo) */
    $template->display('admin/admin.contentmove.html');
    include('footer.php');
}

function content_delete($ids = array())
{
    global $doc_cfg, $module_name, $doc;

    $returnflag = false;
    $cat = $doc;

    $info2 = imgModul() . " " . _DOCS_TITLE . " - " . _DELETE;

    switch (pmxAdminForm::CheckButton()) {
        case "contentdelete":
            foreach($_POST['cid'] as $id) {
                $cat->deleteRecord ($id);
            }
            mxRedirect("admin.php?op=$module_name&act=content", _CHANGESAREOK, 2);
            break;
        default:
            break;
    }

    /* hier die Ausgabefelder angeben */
    $output = array("id", "parent_id", "title", "date_created", "owner_id", "publish", "access", "text1");

    $catlist = $cat->getRecordsFromID($ids);

    $count = count($ids);

    $tb = load_class('AdminForm');;
    $tb->__set('target_url', "admin.php?op=" . $module_name . "&amp;act=contentdelete");
    $tb->__set("tb_text", "");
    $tb->__set("tb_direction", 'right');
    $tb->__set("infobutton", true);
    $tb->__set("tb_pic_heigth", 25);
    // $tb->addToolbar("save");
    $tb->addToolbarLink("cancel", "admin.php?op=$module_name&amp;act=content");
    $tb->addToolbarLink("cpanel", "admin.php?op=$module_name");

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
    $template->display('admin/admin.delete.html');
    include('footer.php');
}

function content_menu($id)
{
    global $doc_cfg, $module_name, $doc,$prefix;

	$node=$doc->getRecord($id);
	$mid=intval($node['mid']);
	$parent=$doc->getRecord($node['parent_id']);
	
	$result=sql_query("SELECT id FROM ${prefix}_menu WHERE id=".$mid."");
	$result2=sql_query("SELECT bid FROM ${prefix}_menu ORDER BY bid DESC LIMIT 1");
	list($lastblock)=sql_fetch_row($result2);
	
	$parent_mid=(intval($parent['mid'])>0)?$parent['mid']:$lastblock;
	$result3=sql_query("SELECT pid FROM ${prefix}_menu WHERE id=".$parent_mid."");
	
	if (sql_num_rows($result)==0) {
		
		sql_query("INSERT INTO ${prefix}_menu SET 
			bid='".$lastblock."',
			pid='".$parent_mid."',
			title='".$node['title']."',
			description='".$node['title']."',
			target='',
			weight='1',
			expanded='0',
			url='modules.php?name=".$module_name."&act=page&id=".$node['id']."'");
			
		$node['mid']=sql_insert_id();
		$doc->updateRecord($node['id'],$node);
	}
		mxRedirect("admin.php?op=menu/add_item/edit/".$node['mid']);
		exit;
	return;
}
?>