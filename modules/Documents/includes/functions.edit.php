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

function add_Rating($id, $rate)
{
    global $doc_cfg, $doc;
    $doc->addrating($id, $rate);
    return;
}

function get_access($id=0)
{
    global $doc_cfg, $doc;
//    $user = $doc->getUser();
//
//    $groupist = intval($user['user_ingroup']);
//    $groupsoll = $doc_cfg['group_access'];
//    $groupaccess = in_array($groupist, $groupsoll);
    return $doc->get_access($id);
}

function imgModul()
{
    global $module_name;
    $images = "book_open.png";
    return "<img src=\"modules/" . $module_name . "/style/images/" . $images . "\" alt=\"\" />";
}

function content_getPageConfigForm($tb, $config)
{
    $pageview2 = array(_DOCS_DEFAULT => -1, _DOCS_VIEW_INDEX => 0, _DOCS_VIEW_BLOG => 1, _DOCS_VIEW_LIST => 2);
    $tb->add("extended", "yesnodefault", "config[view_title]", $config['view_title'], _DOCS_VIEWTITLE, _DOCS_VIEWTITLE_TEXT);
    $tb->add("extended", "yesnodefault", "config[link_title]", $config['view_title'], _DOCS_LINKTITLE, _DOCS_LINKTITLE_TEXT);
    $tb->add("extended", "select", "config[viewblog]", $config['viewblog'], _DOCS_CONF_BLOGVIEW, _DOCS_CONF_BLOGVIEW_TEXT, 1, $pageview2);
    $tb->add("extended", "select", "config[tabscount]", $config['tabscount'], _DOCS_CONF_TABCOUNT, _DOCS_CONF_TABCOUNT_LIST_TEXT, 1, array(_DOCS_DEFAULT => -1, 1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6));
    $tb->add("extended", "yesnodefault", "config[breadcrump]", $config['breadcrump'], _DOCS_CONF_BREADCRUMP, _DOCS_CONF_BREADCRUMP_TEXT);
    $tb->add("extended", "yesnodefault", "config[alphaindex]", $config['alphaindex'], _DOCS_PAGE_ALPHA, _DOCS_PAGE_ALPHA_TEXT);
    $tb->add("extended", "yesnodefault", "config[viewindex]", $config['viewindex'], _DOCS_PAGE_INDEX, _DOCS_PAGE_INDEX_TEXT);
    $tb->add("extended", "yesnodefault", "config[pageindex]", $config['pageindex'], _DOCS_PAGE_INDEXFULL, _DOCS_PAGE_INDEXFULL_TEXT);
    $tb->add("extended", "yesnodefault", "config[viewindexnew]", $config['viewindexnew'], _DOCS_PAGE_INDEX_NEW, _DOCS_PAGE_INDEX_NEW_TEXT);
    $tb->add("extended", "yesnodefault", "config[viewsearch]", $config['viewsearch'], _DOCS_VIEWSEARCH, _DOCS_VIEWSEARCH_TEXT);
    $tb->add("extended", "yesnodefault", "config[viewcreator]", $config['viewcreator'], _DOCS_PAGE_CREATOR, _DOCS_PAGE_CREATOR_TEXT);
    $tb->add("extended", "yesnodefault", "config[vieweditor]", $config['vieweditor'], _DOCS_PAGE_LASTEDITOR, _DOCS_PAGE_LASTEDITOR_TEXT);
    $tb->add("extended", "yesnodefault", "config[viewkeywords]", $config['viewkeywords'], _DOCS_PAGE_VIEWKEYWORDS, _DOCS_PAGE_VIEWKEYWORDS_TEXT);
    $tb->add("extended", "yesnodefault", "config[navigation]", $config['navigation'], _DOCS_PAGE_VIEWNAVIGATION, _DOCS_PAGE_VIEWNAVIGATION_TEXT);
    $tb->add("extended", "yesnodefault", "config[viewsimilar]", $config['viewsimilar'], _DOCS_PAGE_VIEWSIMILAR, _DOCS_PAGE_VIEWSIMILAR_TEXT);
    $tb->add("extended", "yesnodefault", "config[pageprint]", $config['pageprint'], _DOCS_PAGE_PRINT, _DOCS_PAGE_PRINT_TEXT);
    $tb->add("extended", "yesnodefault", "config[sendfriend]", $config['sendfriend'], _DOCS_PAGE_SENDFRIEND, _DOCS_PAGE_SENDFRIEND_TEXT);
    $tb->add("extended", "yesnodefault", "config[viewsocial]", $config['viewsocial'], _DOCS_PAGE_VIEWSOCIAL, _DOCS_PAGE_VIEWSOCIAL_TEXT);
    $tb->add("extended", "yesnodefault", "config[viewrating]", $config['viewrating'], _DOCS_PAGE_VIEWRATING, _DOCS_PAGE_VIEWRATING_TEXT);
    /* links */
    $tb->add("extended", "yesnodefault", "config[viewbooklink]", $config['viewbooklink'], _DOCS_PAGE_VIEWBOOKLINK, _DOCS_PAGE_VIEWBOOKLINK_TEXT);
    $tb->add("extended", "yesnodefault", "config[viewbookbase]", $config['viewbookbase'], _DOCS_PAGE_VIEWBOOKBASE, _DOCS_PAGE_VIEWBOOKBASE_TEXT);
    $tb->add("extended", "yesnodefault", "config[linkmodules]", $config['linkmodules'], _DOCS_PAGE_VIEWMODULELINK, _DOCS_PAGE_VIEWMODULELINK_TEXT);
}

function content_getPageMetaForm($tb, $config, $content, $info)
{
	if (!array_key_exists('title',$info)) $info['title']="";
    $tb->add("metapage", "input", "info[title]", $info['title'], _DOCS_PAGE_TITLE, _DOCS_PAGE_TITLE_TEXT, 30);
    $tb->add("metapage", "textbox", "keywords", $content['keywords'], _DOCS_KEYWORDS, _DOCS_KEYWORDS_TEXT, 40, array('rows' => 3));
    $tb->add("metapage", "textarea", "info[description]", $info['description'], _DOCS_SHORTDESC, "", 40, array('rows' => 3));
    $tb->add("metapage", "input", "info[canonical]", $info['canonical'], _DOCS_META_CANONICAL, _DOCS_META_CANONICAL_TEXT, 30);
    $robots = array("index,follow" => "index,follow", "index,nofollow" => "index,nofollow", "noindex,follow" => "noindex,follow", "noindex,nofollow" => "noindex,nofollow");
    $tb->add("metapage", "select", "info[robots]", $info['robots'], _DOCS_META_ROBOTS, _DOCS_META_ROBOTS_TEXT, 1, $robots);
    $tb->add("metapage", "input", "info[revisit]", $info['revisit'], _DOCS_META_REVISIT, _DOCS_META_REVISIT_TEXT, 5);
    $tb->add("metapage", "input", "info[author]", $info['author'], _DOCS_META_AUTHOR, _DOCS_META_AUTHOR_TEXT, 30);
    $tb->add("metapage", "textbox", "info[alternate]", $info['alternate'], _DOCS_META_ALTERNATE, _DOCS_META_ALTERNATE_TEXT, 40, array('rows' => 3));
}

function printFooter()
{
    global $mxbook_version;
    $year = (date("Y") > "2012")?"2012-" . date("Y"):date("Y");
    //echo '<div class="clear"></div><div class="tiny">mxDocument V ' . $mxbook_version . ' &copy; ' . $year . ' </div><div class="clear"></div>';
    include ("footer.php");
}

function quoteAttachmentFilename($filename)
{
    $replace = array('/\s/' => '_', '/[^0-9a-zA-Z_\.]/' => '', '/_+/' => '_', '/(^_)|(_$)/' => '');

    return preg_replace(array_keys($replace), $replace, $filename);
}

function page_mail_sendfriend ($eid, $sname, $semail, $stext, $ename, $eemail, $ok = 0)
{
    global $prefix, $sitename, $currentlang, $module_name, $bgcolor1, $bgcolor2, $bgcolor3, $doc;

    $mailto = "";
    $mailsubject = "";
    $mailmsg = "";
    $mailsender = "";

    $node = $doc->getRecord($eid);

    $mailsubject .= _MAIL_EVENTFRIEND_SUBJECT . ":" . $mxConf['sitename'] . " - " . $title;
    $mailto = $eemail; // an Schedule-Admin
    $mailsender = $semail;

    $mailmsg .= "Hallo " . $ename . ", \n\n";

    $mailmsg .= $sname . _MAIL_EVENTFRIEND_TEXT1 . "\n \n";
    $mailmsg .= _TITLE . " : " . $title . "\n\n";
    $mailmsg .= _DOCS_FROM . " " . ws_langDate($startdate) . " - " . ws_langDate($enddate) . "\n\n";
    $mailmsg .= _MAIL_EVENTFRIEND_TEXT2 . " \n\n";
    $mailmsg .= pmxRootURL() . "modules.php?name=$module_name&act=show&eid=$eid   \n\n";

    $mailmsg .= _MAIL_EVENTFRIEND_TEXT3 . "\n";
    $mailmsg .= pmxRootURL() . " \n\n";
    $mailmsg .= $sname . _MAIL_EVENTFRIEND_TEXT4 . "\n\n";
    $mailmsg .= $stext . "\n\n";
    $mailmsg .= _MAIL_EVENTFRIEND_REGARDS . " \n" . $GLOBALS['sitename'] . "\n\n";
    $mailmsg .= _MAIL_SECURITY_TEXT_2 . $sname . " " . $semail . "  IP: " . $_SERVER['REMOTE_ADDR'] . "\n\n";
    $mailmsg .= _MAIL_SECURITY_TEXT_1;

    $mailmsg = strip_tags($mailmsg);
    mxMail($mailto, $mailsubject, $mailmsg, $mailsender);

    return;
}

?>