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
 * $Revision: 6 $
 * $Author: PragmaMx $
 * $Date: 2015-07-08 09:07:06 +0200 (Mi, 08. Jul 2015) $
 */

defined('mxMainFileLoaded') or die('access denied');

if (!defined("_DOCS_NEW_CONTENT")) define("_DOCS_NEW_CONTENT", "New Document");

global $prefix;

load_class("Content", false);
load_class("AdminForm", false);


/**
 * pmxBook
 * beinhaltet alle funktionen zum verwalten der Contentcategories und Datensätze
 *
 * @author terraproject
 * @copyright Copyright (c) 2011
 * @access public
 */
class pmxBook extends pmxContent {
    public function __construct($parameter = null)
    {
        global $prefix;
        global $language_avalaible, $currentlang;
        $this->__set("language_avalaible", $language_avalaible);
        $this->__set("language", $currentlang);
        // parent::__construct(func_get_args());
        // muss Immer als erstes aufgerufen werden !!!!
        $this->_setStandard($parameter);

        /* Modulname festlegen */
        $this->__set('modulname', $parameter);
        $this->__set('module_name', $parameter);

        /* hier Standard-DB-Tabelle festlegen*/
        $this->__set('dbtable', $prefix . "_content");
        $this->__set('logtable', $prefix . "_content_log");

        /* hier Standard-Einfügerichtung festlegen*/
        $this->__set('insertfirst', false);

        /* set standard-publish */
        $this->__set('waiting', -1);
        $this->__set('unpublish', 0);
        $this->__set('publish', 1);
        $this->__set('archive', 2);
        $this->__set('access', array('-1'));
        $this->__set('logging', 0);
        //$this->__set('config', array('-1'));
        /* Root-ID einlesen */
        $this->getRootID();
    }

    public function getModuleName()
    {
        return $this->module_name;
    }

    public function getConfig()
    {
		//if (count($this->config)>1 ) return $this->config;
        
		$temp = $this->getRoot();

        if (!array_key_exists('config', $temp)) $temp = $this->setConfigDefaults();

        $root = $this->getRecordDefault();
        $config1 = unserialize($root['config']);
        $config2 = unserialize($temp['config']);
        $config = $this->_getDefaultValues();
        $config = array_merge($config, $config1, $config2);

        $config['title'] = $temp['title'];
        $config['text1'] = $temp['text1'];
        $config['text2'] = $temp['text2'];
        $config['text3'] = $temp['text3'];
        $this->logging = $config['logging'];
        // $this->access=implode (",",$config['group_access']);
        unset($temp);
		$this->__set('config',$config1);
        return $config;
    }

    /* erstellt die Konfiguration mit Defaultwerten */
    public function setConfigDefaults()
    {
        $root = array();
        $config = $this->_getDefaultValues();
        $root['config'] = serialize($config);

        $this->setRootRecord($root);
        $temp = $this->getRoot();

        return $temp;
    }

    protected function _getDefaultValues()
    {
        $config = array();

        $config['insertfirst'] = 0;
        $config['rightblocks'] = 0;
        $config['viewblog'] = 0;
        $config['breadcrump'] = 1;
        $config['cuttext'] = 1;
        $config['cutlen'] = 100;
        $config['logging'] = 0;
        $config['group_access'] = array('-1');

        $config['link_other'] = 0;
        $config['view_title'] = 1;
        $config['link_title'] = 1;
        $config['link_count'] = 0;
        $config['linkmodules'] = 0;
        $config['indexwidth'] = 1;
        $config['viewsearch'] = 1;
        $config['tabscount'] = 1;
        $config['changescount'] = 5;
        $config['viewnews'] = 0;
        $config['newscount'] = 5;
        $config['viewchanges'] = 0;
        $config['searchcount'] = 10;
        $config['language'] = 0;

        $config['viewindex'] = 1;
        $config['pageindex'] = 1;
        $config['alphaindex'] = 0;
        $config['viewindexnew'] = 1;
        $config['viewcreator'] = 1;
        $config['vieweditor'] = 1;
        $config['viewviews'] = 1;
        $config['viewkeywords'] = 1;
        $config['navigation'] = 1;
        $config['viewsimilar'] = 1;
        $config['similarcount'] = 5;
        $config['editusergroup'] = -1;
        $config['editorrights'] = 0;
        /* links */
        $config['viewbooklink'] = 0;
        $config['viewbookbase'] = 0;
        $config['viewencylink'] = 0;
        $config['viewrating'] = 0;
        $config['viewsocial'] = 0;
        $config['pageprint'] = 1;
        $config['sendfriend'] = 1;
        $config['blockmenuwidth'] = 2;
        $config['blockmenucontent'] = array();

        $config['attpath'] = "media/files/";

        $config['att_on'] = 1;
        $config['attcount'] = 5;
        $config['attmaxsize'] = 100;
        $config['attmedia'] = 1;
        $config['attmaxwidth'] = 300;
        $config['attmaxheight'] = 200;
        $config['attmaxwidththumb'] = 150;
        $config['baserating'] = 5;
        return $config;
    }

    public function checkConfig($config = array())
    {
        $default = $this->getRecordDefault();
        $default = unserialize($default['config']);
        return array_merge($default, $config);
    }

    public function getUser()
    {
        $user = array();
        switch (true) {
            case MX_IS_USER:
                $user = mxGetUserData();
                break;
            case MX_IS_ADMIN:
                $admin = mxGetAdminData();
                if ($admin['user_uid'] == null or $admin['user_uid'] == 0) {
                    $user['uid'] = 0;
                    $user['uname'] = $admin['aid'];
                    $user['user_ingroup'] = 0;
                } else {
                    $user = mxGetUserDataFromUid($admin['user_uid']);
                }
                break;
            default:
                $user['uid'] = 0;
                $user['uname'] = $GLOBALS['anonymous'];
                $user['user_ingroup'] = 0;
                break;
        }
        return $user;
    }

    public function getRecordDefault()
    {
        $user = $this->GetUser();
        $record = array('id' => 0,
            'title' => _DOCS_NEW_CONTENT,
            'text1' => "",
            'text2' => "",
            'text3' => "",
            'keywords' => "",
            'alias' => "",
            'date_start' => 0,
            'date_created' => time(),
            'date_end' => 0,
            "owner_name" => $user['uname'],
            "owner_id" => $user['uid'],
            'publish' => 0,
            'position' => 0,
            'hash' => "",
            'access' => 0,
            'group_access' => serialize(array('-1')),
            'status' => 0,
            'module_name' => $this->modulname,
            'attachment' => serialize(array()),
            'info' => serialize(
                array('robots' => '',
                    'canonical' => '',
                    'alternate' => '',
                    'revisit' => '10',
                    'author' => '',
                    'description' => '',
					'title'=>'',
                    )),
            'config' => serialize(
                array('view_title' => -1,
                    'viewindex' => -1,
                    'viewblog' => -1,
                    'viewsearch' => -1,
                    'breadcrump' => -1,
                    'alphaindex' => -1,
                    'link_title' => -1,
                    'linkmodules' => -1,
                    'pageindex' => -1,
                    'viewindexnew' => -1,
                    'viewcreator' => -1,
                    'vieweditor' => -1,
                    'viewkeywords' => -1,
                    'viewviews' => -1,
                    'navigation' => -1,
                    'viewsimilar' => -1,
                    'pageprint' => -1,
                    'sendfriend' => -1,
                    'viewsocial' => -1,
                    'viewrating' => -1,
                    'viewbooklink' => -1,
                    'viewbookbase' => -1,
                    'viewencylink' => -1,
					'blockmenuwidth'=>-1,
                    'tabscount' => -1,
                    'logging' => -1,
                    'group_access' => array('-1'),
                    'blockmenucontent' => array(),
                    )),
            );
        return $record;
    }

    public function getConfigPage($id)
    {
        $config = $this->getConfig();
        $configarray = $this->getBreadcrumpAll($id, true);
        foreach ($configarray as $field) {
            $temp = array();
            $temp = unserialize($field['config']);
            foreach($temp as $key => $value) {
                if ($value != -1) $config[$key] = $value;
            }
            unset($temp);
        }
        return $config;
    }

    public function setRootRecord ($node)
    {
        return $this->_setRootRecord ($node);
    }

    public function getRootRecord ()
    {
        return $this->getRoot();
    }


    public function getModulRootID ()
    {
        return $this->getRootID();
    }
    public function getBookAccess()
    {
        // $user = mxGetUserData();
        // $groupist = intval($user['user_ingroup']);
        // $isaccess=(stristr($this->access,$groupist))?true:false;
        // $isaccess=(MX_IS_ADMIN)?true:$isaccess;
        return true;
    }

    /**
     * Gibt den Pfad des Node zurück
     *
     * @parameter  $ : id intval
     * @return :    array
     */
    public function getBookRootID($id = 0)
    {
        // if ($id == 0) $id=$this->getRootID();
        $a = array();
        $a = $this->getBookRoot($id);
        $b = $this->_getNode($id);

        $output = ($a['id'] == 0)?$b['id']:$a['id'];
        unset($a, $b);
        return $output;
    }

    public function getBookRoot($id)
    {
        $root = $this->getRootNode($id);
        return $root;
    }

    function getModuleTitle()
    {
        $root = $this->getRoot();
        $roottitle = unserialize($root['text2']);
        $moduletitle = stripslashes((trim($roottitle[$this->language]) == "")?$this->getModuleName():trim($roottitle[$this->language]));
        return $moduletitle;
    }
    function getModuleText()
    {
        $root = $this->getRoot();
        $roottitle = unserialize($root['text1']);
        return stripslashes($roottitle[$this->language]);
    }

    public function getRecords_AdminNews()
    {
        $this->setFilter("adminnews", "publish", "=", "0");
        $this->setFilter("adminnews", "status", "=", "1");
        $news = $this->_getNodes("adminnews");
        return $news;
    }

    public function getRecords_StartPage($all = false)
    {
        if (!$all) $this->setFilter("startpage", "publish", "=", "1");
        $this->setFilter("startpage", "position", ">", "0");
        $news = $this->_getNodes("startpage", " ORDER BY position asc ");
        return $news;
    }


    public function getRecords_StartPagePositions($all = false)
    {
        if (!$all) $this->setFilter("startpage", "publish", "=", "1");
        $this->setFilter("startpage", "position", ">", "0");
        $news = $this->_getNodes("startpage", " ORDER BY position asc ");
        $pages = array();
        foreach($news as $page) {
            $pages[$page['id']] = $page['position'];
        }
        unset($news);
        return $pages;
    }

    public function getRecords_RootDocuments($all=false)
    {
        if (!$all) $this->setFilter("statistics", "publish", "=", "1");
        $this->setFilter("statistics", "parent_id", "=", $this->getRootID());
        $news = $this->_getNodes("statistics", "");
        return $news;
    }

    public function getRecords_Documents($all=false)
    {
        if (!$all) $this->setFilter("statistics2", "publish", "=", "1");
        $this->setFilter("statistics2", "parent_id", ">", $this->getRootID());
        $news = $this->_getNodes("statistics2", "");
        return $news;
    }

    public function getRecords_New($days, $count, $base = 0)
    {
        return $this->_getNodes_New($days, $count, $base, "language IN ('ALL','" . $this->language . "')");
    }

    public function getRecords_LastChange($days, $count, $base = 0)
    {
        return $this->_getNodes_LastChange($days, $count, $base, "language IN ('ALL','" . $this->language . "')");
    }

    public function getRecord_LastEdit()
    {
		
        $this->setFilter("newpage", "publish", "=", "1");
        $record=$this->_getNodes("newpage", "ORDER by date_edit desc", "1");
		
		return array_shift($record);
    }

    public function getRecords_Best( $count)
    {
        $this->setFilter("rating", "rating", ">", "0");
        $this->setFilter("rating", "language", " IN ", "('ALL','" . $this->language . "')");
        return $this->_getNodes("rating", "ORDER by rating desc",$count);
    }

    public function getRecords_MostViewed( $count)
    {
        $this->setFilter("views", "views", ">", "0");
        $this->setFilter("views", "language", " IN ", "('ALL','" . $this->language . "')");
        return $this->_getNodes("views", "ORDER by views desc",$count);
    }
    public function publish($id)
    {
        $catarray = array('publish' => 1, "status" => "0");
        $record = $this->_updateNode($id, $catarray, false);
        if ($record['publish'] == 1) $this->writelog($id, $record, "PUBLISH");
        return;
    }

    public function unpublish($id)
    {
        $catarray = array('publish' => 0, "status" => 0);
        $record = $this->_updateNode($id, $catarray, false);
        if ($record['publish'] == 0)$this->writelog($id, $record, "UNPUBLISH");
        return;
    }
    public function archive($id)
    {
        $catarray = array('publish' => ($this->__get('archive')));
        $this->_updateNode($id, $catarray, false);
        return;
    }
    public function waiting($id)
    {
        $catarray = array('publish' => -1);
        $this->_updateNode($id, $catarray, false);
        $this->writelog($id, "", "SETWAITING");
        return;
    }

    public function setstartpage($id)
    {
        $startpage = $this->getRecords_StartPagePositions(true);
        $startpage_count = count($startpage) + 1 ;
        unset($startpage);
        $catarray = array('position' => $startpage_count);
        $record = $this->_updateNode($id, $catarray, false);
        $this->renumstartpage();
        if ($record['position'] == 1) $this->writelog($id, $record, "SET STARTPAGE");
        return;
    }
    public function unsetstartpage($id)
    {
        $catarray = array('position' => 0);
        $record = $this->_updateNode($id, $catarray, false);
        $this->renumstartpage();
        if ($record['position'] == 0) $this->writelog($id, $record, "DEL FROM STARTPAGE");
        return;
    }

    public function renumstartpage($positions = array())
    {
        // Position[id]=position
        $sp_records = $this->getRecords_StartPagePositions(true);
        foreach ($positions as $id => $value) {
            $sp_records[$id] = intval($value);
        }
        asort($sp_records, SORT_NUMERIC);
        $i = 0;
        foreach ($sp_records as $id => $value) {
            $i++;
            $catarray = array('position' => $i);
            $record = $this->_updateNode($id, $catarray, false);
            if ($record['position'] != $value) $this->writelog($id, $record, "STARTPAGE CHANGE POSITION");
        }
        return;
    }

    public function deleteRecord($id)
    {
        /* erstmal Daten holen */
        $cat = $this->_getNode($id);

        /* Attachments raussuchen */
        $attach = unserialize($cat['attachment']);
        if (is_array($attach)) {
            foreach ($attach as $file) {
                /* attachments löschen */
                if (is_writable($file['filename'])) @unlink($file['filename']);
            }
        }
        /* jetzt erst Record löschen */
        $this->_deleteNode($id);
        /* auch aus den Logs löschen */
        $this->_deleteLogFromId($id);
        return;
    }

    public function getRecordList($cat = 0, $output = "", $dbfilter = "", $start = 0, $limit = 0)
    {
        return $this->_get_list($cat, $output, $dbfilter, $start, $limit);
    }

    public function addRecord($id, $record = array())
    {
        $record['hash'] = $this->_getCRC($record);
        $new_id = $this->_addNode($id, $record);
        $this->writelog($new_id, $record, "ADD");

        return $new_id;
    }

    public function updateRecord($id, $record = array(), $log_action = "UPDATE")
    {
        if (array_key_exists('alias', $record)) {
            $record['alias'] = stristr($record['alias'], "-");
            if (trim($record['alias']) == "") $record['alias'] = $record['title'];

            $record['alias'] = $record['id'] . "-" . $this->check_alias($record['alias']);
        }

        $this->writelog($id, $record, $log_action);

        return $this->_updateNode($id, $record);
    }

    public function updateRecords($ids = array(), $record = array())
    {
        return $this->_updateNodes($ids, $record);
    }

    public function moveRecord ($source_id, $parentid)
    {
        if ($this->logging == 1) {
            $source = $this->getPage($source_id);
            $from = $this->getPage($source['parent_id']);
            $dest = $this->getPage($parentid);
            $this->writelog($source_id, $source, "MOVE", "move from " . $from['title'] . " to " . $dest['title'] . "");
        }
        $imove = $this->_move ($source_id, $parentid);
        return $imove;
    }

    public function check_alias($text)
    {
        $text = strip_tags(trim($text));
        $text = $this->remove_accent(utf8_encode(strtolower($text)));
        $replace = array('/\s/' => '_', '/[^0-9a-zA-Z_\.]/' => '', '/_+/' => '_', '/(^_)|(_$)/' => '');
        return preg_replace(array_keys($replace), $replace, $text);
        // return string_to_filename($text);
    }

    public function copyRecord($id)
    {
        $new_id = $this->copyNode($id);
        $record = $this->_getNode($new_id);
        $this->writelog($new_id, $record, "ADD");
        return $new_id;
    }

    public function getRecord($id)
    {
        return $this->_getNode($id);
    }

    public function getRecord_mid($id)
    {
        return $this->getNodeFromMID($id);
    }

    public function getRecordUpper($id, $bookid)
    {
        return $this->_getNodeUpper($id, $bookid);
    }

    public function getRecordLower($id, $bookid)
    {
        return $this->_getNodeLower($id, $bookid);
    }

    public function getRecords($filter = array(), $orderby = "", $limit = "")
    {
        return $this->_getNodes($filter, $orderby, $limit);
    }

    public function addview($id)
    {
        if (mxSessionGetVar($this->getModuleName(). "-view-" . intval($id))) return;

        $this->update_field_1($id, "views");
        mxSessionSetVar($this->getModuleName() . "-view-" . intval($id), true);
        return;
    }

    public function addrating($id, $rate)
    {
        // $base=$this->config['baserating'];
        $rating = $this->get_field($id, "rating");
        if (mxSessionGetVar($this->getModuleName() . "-rate-" . intval($id))) {
            echo number_format ($rating, 1);
            return;
        }
        $votes = $this->get_field($id, "votes");
        $ratenew = ($rating == 0)?$rate:($rating * $votes + $rate) / ($votes + 1);
        echo number_format ($ratenew, 1);
        $this->update_field_1($id, "votes");
        $this->update_field($id, "rating", $ratenew);

        mxSessionSetVar($this->getModuleName() . "-rate-" . intval($id), true);
        exit;
    }

    public function getRecordsFromId($ids = array())
    {
        return $this->_getNodesFromId($ids);
    }

    public function selectBook($id = 0)
    {
    }

    public function isNew($days, $record)
    {
        $Cdays = intval($days);
        if ($Cdays <1) return false;
        $cstart = time() - (86400 * $Cdays);
        if ($record['date_created'] > $cstart) return true;
        return false;
    }

    public function isChanged($days, $record)
    {
        if ($this->isNew($days, $record)) return false;
        $Cdays = intval($days);
        if ($Cdays <1) return false;
        $cstart = time() - (86400 * $Cdays);
        if ($record['date_edit'] > $cstart and $record['date_edit'] > $record['date_created'] + 86400) return true;
        return false;
    }

    public function getLastRecordId()
    {
        $output = array();
        $result1 = sql_query("SELECT id FROM " . $this->__get('dbtable') . " where module_name='" . $this->getModuleName() . "' order by id desc limit 1");
        list($maxid) = sql_fetch_row($result1);
        return $maxid;
    }

	public function get_access($id=0)
	{
		
		$user = $this->getUser();
		//$doc_cfg=$this->getConfig();
		//$groupsoll = $doc_cfg['group_access'];
		
		$config=$this->getConfigPage($id);
		$groupsoll = $config['group_access'];
		$groupist = intval($user['user_ingroup']);
		$groupaccess = in_array($groupist, $groupsoll);
		return (MX_IS_ADMIN or $groupaccess or (MX_IS_USER && in_array(1, $groupsoll)));
	}

	public function get_access_from_node($node=array())
	{
		
		$user = $this->getUser();
		
		$config=$this->getConfigPage($node['id']);
		$ifuseraccess= ($user['uid']==$node['owner_id'] or $user['uid']==$node['edit_uid']) ;
		
		$groupist = intval($user['user_ingroup']);
		$groupsoll = $config['group_access'];
		$groupaccess = in_array($groupist, $groupsoll);
		return (MX_IS_ADMIN or  (MX_IS_USER && in_array(1, $groupsoll) and $ifuseraccess) and$groupaccess);
	}
    /**
     *
     * @parameter  $ :
     * @return :
     */
    public function content_get_ul($cat = 0)
    {
        $getout = array("title", "id", "parent_id");
        $rootid = ($cat == 0)?$this->getRootID():$cat;
        $this->setFilter("liste_a", "publish", "=", "1");
        $this->setFilter("liste_a", "parent_id", "=", $rootid);
        $filter = $this->getFilter("liste_a");
        $result = $this->getRecordList ($cat, $getout, 'liste_a');

        return $result;
    }

    /**
     *	Erstellt eine verschachtelte HTML-Liste 
     * @parameter  $ :
     * @return :
     */
    public function content_get_html($cat = 0, $id=0, $coption="", $newmark=false, $level=99, $collapse=false )
    {
		$config=$this->getConfigPage($id);
        $getout = array("title", "id", "parent_id",'date_edit','date_created','publish','owner_id','edit_uid');
        $rootid = ($cat == 0)?$this->getRootID():$cat;
        //if (!MX_IS_ADMIN) $this->setFilter("liste_a", "publish", "=", "1");
		$user = $this->getUser();
        //$this->setFilter("liste_a", "parent_id", "=", $rootid);
        $filter = $this->getFilter("liste_a");
        $result = $this->getRecordList ($cat, $getout, 'liste_a');
		$base=$this->getRecord($cat);
		$current=($id==0)?$this->getRecord($cat):$this->getRecord($id);
		$html="";
		$i=-1;
		//$html =" <div ".$coption." >";
		foreach($result as $node) {
			if ($level<$node['level']) continue 1;
			
			if ($node['publish']==0 and !MX_IS_ADMIN) {
				//$config=$this->getConfigPage($node['id']);
				//if ($user['uid']!=$node['owner_id'] or $user['uid']!=$node['edit_uid'] or !$this->get_access($config['group_access']))  continue 1;
				if (!$this->get_access_from_node($node)) continue 1;
			}
			switch (true) {
				case ($node['level']==1 && $base):
					$i=0;
					continue 2;
					break;
				case ($i<$node['level']):
					$html .="<ul><li>";
					break;
				case ($i==$node['level']):
					$html .="</li><li>";
					break;
				case ($i>$node['level']):
					$html .=str_repeat( "</li></ul>",$i-$node['level'])."<li>";
					break;
			}
			$new="";
			$update=""	;
			if ($newmark) {
				$new=($this->isNew($config['newscount'],$node))?"<span class=\"contentnew\">". _DOCS_PAGE_NEW ."</span>":"";
				$update=($this->isChanged($config['newscount'],$node))?"<span class=\"contentupdate\">". _DOCS_UPDATE ."</span>":""; 
			}
			$i=$node['level'];
			$curmark= ($node['id']==$current['id'])?"collapsable":""; 
			if ($node['publish']==0) $curmark .=" inactiv";
			$curmark=trim($curmark);
			$html .= "<a class=\"".$curmark."\" href=\"modules.php?name=".$this->module_name."&amp;act=page&amp;id=".$node['id']."\" title=\"".$node['title']."\" >".$node['title']." $new $update</a>";
				
		}
		$html .=str_repeat("</li></ul>",max(0,($i-$base['level']+1)));
		//$html .="</div>";
        return $html;
    }

    public function getPageAttachments($id)
    {
        $record = $this->_getNode($id);
        $record = unserialize($record['attachment']);
        $record = (is_array($record))?$record:array();
        return $record;
    }

    public function getPage($id)
    {
        $record = $this->_getNode($id);
        return $record;
    }

    public function getPages($keywords, $limit)
    {
        $records = array();
        $keywords = trim(strip_tags($keywords));
        if (empty($keywords)) return $records;

        $filter = $this->setFilter("search", "publish", "=", "1");
        $filter = $this->setFilter("search", "language", " IN ", "('ALL','" . $this->language . "')");
        $filter = $this->setFilter("search", "MATCH(text1,keywords,title,text2)", "AGAINST", "('" . $keywords . "' IN BOOLEAN MODE) ");
        $records = $this->_getNodes("search", "", $limit);
        foreach($records as $value) {
            $value['text1'] = strip_tags($value['text1']);
            $records[$value['id']]['text1'] = preg_replace("/{(.*?)}/", "", $value['text1']);
        }
        return $records;
    }

    public function getTitle($keywords)
    {
        $records = array();
        $keywords = trim(strip_tags($keywords));
        if (empty($keywords)) return $records;
        $search = $this->setFilter("search", "publish", "=", "1");
        $search = $this->setFilter("search", "language", " IN ", "('ALL','" . $this->language . "')");
        $search = $this->setFilter("MATCH(text1,keywords,title,text2) AGAINST ('" . $keywords . "' IN BOOLEAN MODE) ");
        $records = $this->_getNodes("search", "", 1);
        return $records[0]['title'];
    }

    /*  Uploads */

    public function uploadfile($tempfile, $i, $uploaddir)
    {
        $file = array();
        $uploadsize = $tempfile['size'][$i];
        $fname = $_FILES['attachment']['name'][$i];
        $extension2 = explode('.', $_FILES['attachment']['name'][$i]);
        $extension = strtolower($extension2[count($extension2)-1]);
        $type = $_FILES['attachment']['type'][$i];
        $newname = $_FILES['attachment']['name'][$i] . "";
        move_uploaded_file($_FILES['attachment']['tmp_name'][$i], $uploaddir . DS . $newname);
        $file[$i]['filename'] = $uploaddir . DS . $newname;
        $file[$i]['size'] = $uploadsize;
        $file[$i]['type'] = $type;
        $file[$i]['extension'] = $extension;

        return $file;
    }

    /*-----------------------------------------------------------------------------------------*/
    public function getBookSections($bookid)
    {
        $result = array();
        $bookid = $this->getBookRootID($bookid);
        // $book = ($bookid == 0)?$this->getRoot():$this->getBookRoot($bookid);
        $output = array("id", "title");
        $this->setFilter("contentlist", "publish", "=", "1");
        $this->setFilter("contentlist", "language", " IN ", "('ALL','" . $this->language . "')");
        $result = $this->content_get_tree($bookid, $output, "contentlist");

        return $result;
    }

    public function book_link ($string, $bookid = 0, $notid = 0, $count = 0)
    {
        global $prefix ;
        $hook_parameters = $string;
		$module_name=$this->getModuleName();
        /* Content Tabelle auslesen */
        $search = array();
        $search2 = $this->getBookSections($bookid);
        foreach ($search2 as $key => $value) {
            if ($value['id'] != $notid) {
                $title = htmlentities(trim($value['title']));
                $search[$title] = "<a class=\"doculink\" href=\"modules.php?name=$module_name&amp;act=page&amp;id=" . $value['id'] . "\" title=\"" . $value['title'] . "\" >" . $value['title'] . "</a>";
            }
        }
        /* Content umschreiben */
        $string = mxChangeContent($string, $search, $count);
        unset ($search, $search2);
        return $string;
    }

    /* checkt das veröffentlichungsdatum */

    public function check_time()
    {
        return;
    }

    /*
     *   schreibt die angegebene Änderung in das Log
     *
    */

    public function writelog($id, &$record, $log_action = "ADD", $text = "", $uid = 0)
    {
        if ($this->logging == 0) return;

        $user = ($uid == 0)?$this->getUser():mxGetUserDataFromUid($uid);
        $check = false;

        switch (strtolower($log_action)) {
            case "update":
                if (!is_array($record) or !array_key_exists('hash', $record)) {
                    $record2 = $this->_getNode($id);
                    $record['hash'] = $record2['hash'];
                    $check = $this->_checkChange($record);
                }
                break;
            default:
                $check = true;
                break;
        }
        if ($check) {
            sql_query("INSERT INTO " . $this->logtable . " SET
                    module_name='" . $this->getModuleName() . "',
                    id=" . $id . ",
                    date_action=" . time() . ",
                    action='" . $log_action . "',
                    text_action='" . mxAddSlashesForSQL($text) . "',
                    edit_uid=" . $user['uid'] . ",
                    edit_uname='" . $user['uname'] . "',
                    title='" . $record['title'] . "'
                    ");
        }

        return;
    }

    private function _checkChange(&$record = array())
    {
        $newhash = $this->_getCRC($record);
        if (intval($record['hash']) == $newhash) {
            return false;
        } else {
            $record['hash'] = $newhash;
        }
        return true;
    }

    private function _getCRC($record = array())
    {
        $hash = crc32($record['title']
             . $record['text1']
             . $record['publish']
             . $record['config']
            );

        return $hash;
    }

    public function _deleteLogFromId($id)
    {
        if ($this->logging == 0) return;
        sql_query("DELETE FROM " . $this->logtable . " WHERE id=" . $id . " and  module_name='" . $this->modulname . "'");
        return;
    }

    public function _deleteLog()
    {
        if ($this->logging == 0) return;
        sql_query("DELETE FROM " . $this->logtable . " WHERE module_name='" . $this->modulname . "'");
        return;
    }

    public function getlog($id)
    {
        $result = sql_query("SELECT * FROM " . $this->logtable . " WHERE id=" . $id . " and  module_name='" . $this->modulname . "' ORDER BY date_action desc LIMIT 0,100");
        $log = array();
        while ($logvalues = sql_fetch_assoc($result)) {
            $log[] = $logvalues;
        }
        return $log;
    }

    public function getLogHTML($id)
    {
        $logs = $this->getlog($id);
        $text = "";
        $text .= "<a href=\"admin.php?op=" . $this->getModuleName() . "&amp;act=dellog&amp;id=$id\">" . _DOCS_DB_DELLOG . "</a><br>";

        $text .= "<table style=\"width:100%;border:1px solid;\">";
        $text .= "<thead><tr>";
        $text .= "<th style=\"width:20%;border:1px solid;\">" . _DATE . "</th>";
        $text .= "<th style=\"width:20%;border:1px solid;\">" . _FROM . "</th>";
        $text .= "<th style=\"border:1px solid;\">" . _DOCS_ACTION . "</th>";
        $text .= "<th style=\"border:1px solid;\">" . _TITLE . "</th>";
        $text .= "<th style=\"border:1px solid;\">" . _DOCS_ACTION . "</th>";
        $text .= "</tr></thead>";
        $text .= "<tbody>";
        foreach($logs as $log) {
            $text .= "<tr>";
            $text .= "<td style=\"border:1px solid;\">" . date("d.m.Y H:i", $log['date_action']) . "</td>";
            $text .= "<td style=\"border:1px solid;\">" . $log['edit_uname'] . "</td>";
            $text .= "<td style=\"border:1px solid;\">" . $log['action'] . "</td>";
            $text .= "<td style=\"border:1px solid;\">" . $log['title'] . "</td>";
            $text .= "<td style=\"border:1px solid;\">" . $log['text_action'] . "&nbsp;</td>";
            $text .= "</tr>";
        }
        $text .= "</tbody>";
        $text .= "</table>";

        return $text;
    }
	
	public function getAlphaIndex ($id=0)
	{
		$filter="";
		$orderby="order by title asc";
		$limit="";
		$output=array("id","title");
		$records=$this->_get_list($id, $output, "",$orderby, $limit);
		$alphaindex=array();
		$alphaindexnum=NULL;
		$index=NULL;
		if (count($records) == 0 ) return NULL;
		foreach($records as $node) {
			$index2= $this->strtoupper_utf8($node['title']);
			$index2=mb_substr($index2,0,1);
			//$index2=$this->remove_accent($index2);
			//echo $index2." - ";
		   if (ord($index2)<58 and strlen($index2)==1) {
				$alphaindexnum[]=$node;
			} else {
				//$index2=iconv('UTF-8', 'ASCII//TRANSLIT',$index2);
				$alphaindex[$index2][]=$node;
			}
		}
		ksort ($alphaindex);
		if ($alphaindexnum) $alphaindex['0']=$alphaindexnum;
		
		return $alphaindex;
	}
	public function getAlphaIndexString($id=0)
	{
		$alphaindex=$this->getAlphaIndex($id);
		$temp="";
		if (count($alphaindex)>0) {
			foreach ($alphaindex as $key=>$item) {
				if ($key=='0'){
				$temp .="<a href=\"modules.php?name=".$this->module_name."&amp;act=alphaindex&amp;char=0&amp;id=$id\">0-9</a> ";
				} else {
				$temp .="<a href=\"modules.php?name=".$this->module_name."&amp;act=alphaindex&amp;char=". ($key)."&amp;id=$id\">".($key)."</a> ";
				}
				
			}
		}
		return $temp;
	}
	function remove_accent($str="")
	{
	  $a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ','Ç','ç','µ',"€");
	  $b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o','C','c','m','E');
	  return str_replace($a, $b, $str);
	} 	
	function strtoupper_utf8($string){
		$string=utf8_decode($string);
		$string=strtoupper($string);
		$string=utf8_encode($string);
    return $string;
	}
	
}


?>