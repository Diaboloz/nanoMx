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

/**
 * pmxComments
 *
 * Beschreibung:
 * Verwaltet alle Kommentare
 * die Funktionen können sowohl Public aufegrufen werden, werden aber auch intern verwendet
 *
 * @package pragmaMx
 * @author terraproject
 * @copyright Copyright (c) 2012
 * @version $Id: Comments.php 6 2015-07-08 07:07:06Z PragmaMx $
 * @access public
 */
global $prefix;

load_class("Content", false);
load_class("AdminForm", false);

class pmxComments extends pmxContent {

    /* Speichert die Instanz der Klasse */
    private static $_initialized = false;
	private $comment=array();
    /* Configuration */
    private static $__set = array(); // Konfigurtion

    /**
     * pmxComments::__construct()
     *
     * @param 
     * @return nothing
     */
    public function __construct($module_name='')
    {
		global $prefix;
        global $language_avalaible, $currentlang;

        if (self::$_initialized) {
            return;
        }
			$module_name=($module_name=="")?"comments":$module_name;
		
		$this->__set('admin',false);
		
		if (PMX_MODULE == "admin") {
			$this->__set('admin',true);
		} else {
			$this->__set('com_modul',PMX_MODULE);
		}
		
		
		// muss hier definiert werden !!!
        $this->__set('modulname',$module_name);
        $this->__set('module_name', $module_name);
        $this->__set("language_avalaible", $language_avalaible);
        $this->__set("language", $currentlang);
	    $this->__set('dbtable', $prefix . "_comment");
        
        // muss Immer als erstes aufgerufen werden !!!!
		parent::__construct($module_name);
		/* Root-ID einlesen */
        $this->getRoot();

        /* Modulname festlegen */

		
        mxGetLangfile(dirname(__FILE__) . DS . 'Comments' . DS . 'language');
        self::$_initialized = true;
        
	}
	
    /**
     * pmxComments::is_admin_module()
     *
	 * true, wenn Adminbereich geladen
     * @param 
     * @return boolean
     */
	
	public function is_admin_module() {
		return $self::admin;
	}
	
	/**
	 * pmxComments::writeThread()
	 * 
	 * speichert einen Basisthread für die Kommentare ab
	 *
	 * @param   $mid int   //zugeordnete ID vom Modul
	 * @return bool
	*/
	public function writeThread ($mid,$thread=array())
	{
		return;
	}
	
	
	/**
	 * pmxComments::readThread()
	 * 
	 * liest alle Kommentare zu einem Basisthread in ein rekursives Array
	 *
	 * @param   $mid int   //zugeordnete ID vom Modul
	 * @return array
	*/
	public function readThread ($mid)
	{
		$thread=array();
		$thisnode=$this->getNodeFromMID($mid);
		
		return $thread;
	}
	

    /**
     * pmxComments::setComment()
     *
     * @param array $comment // inhalte für DB
     * 
     * @return insert_id
     */
	public function writeComment($comment=array())
	{
		$com=$this->_def_Comment(); // defaultwerte eintragen
		$com=array_merge($com,$comment); // zusammenführen
		
		return;
	}
	
    /**
     * pmxComments::getCommentFromId()
     *
     * @param $id
     * 
     * @return $array Comment
     */
	public function readCommentFromId($id)
	{
		$com=array();
		$com=$this->_getNodeFromID($id);
		
		return $com;
	}
	
    /**
     * pmxComments::getComment()
     *
     * @param string $module_name
     * @param string $sid
     * @param string $pid
     * 
     * @return $array Comment
     */
	public function readComment($pid,$sid)
	{
		$com=array();
		
		return $com;
	}
	
	
    /**
     * pmxComments::getCommentsHTML()
     *
     * gibt den kompletten Thread der Comments für die Kennung $mid in einem String zurück
     * @param string $mid
     * @param 
     * 
     * @return string
     */
	public function getCommentsHTML($mid) 
	{
		$clist="";
		$this->readThread ($mid);
		
		$clist .="<div class='comments'>Hallo";
		$clist .="</div>";
		return $clist;
	}

    /**
     * pmxComments::getCommentsHTML()
     *
     * gibt den kompletten Thread der Comments für die Kennung $mid in einem String zurück
     * @param string $mid
     * @param 
     * 
     * @return string
     */
	public function getCommentsForm($mid) 
	{
		$cform=new pmxAdminForm("inputcomment");
		$cform->__set('target_url', "");
		$cform->__set("tb_text", "komma");
		$cform->__set("tb_direction", 'right');
		$cform->__set("infobutton", false);
		$cform->__set("tb_pic_heigth", 15);
		$cform->__set("csstoolbar", "toolbar1");
		$cform->__set('buttontext', false);
		$cform->__set("homelink", false);
		$cform->__set('fieldhomebutton', false);
		
		$cform->addFieldSet("comment","Geben Sie hier ihren Kommentar ein","",true);
		$cform->add("comment","input","cuser","","USERNAME","",25);
		$cform->add("comment","editor","ctext","Text");
		$cform->add("comment","hidden","mid",$mid);
		
		$form=$cform->FormOpen();
		$form.=$cform->getFieldSet('comment');
		$form.=$cform->FormClose();
		
		
		return $form;
	}

	private function _db_writeComment($mid,$comment=array())
	{
		$insert_id=false;
		
		switch (true) {
			case !array_key_exists('title',$comment):
			case !array_key_exists('text',$comment):
			return false;
			break;
		}
		$com=$this->getCommentDefault();
		
		$com['mid']=intval($mid);
		$com['title']=mxAddSlashesForSQL(strip_tags($comment['title']));
		$com['text1']=mxAddSlashesForSQL($comment['text']);
		$com['alias']=mxAddSlashesForSQL(strip_tags($comment['module_name']));
		$com['link']=mxAddSlashesForSQL(strip_tags($comment['link']));
		
		
		
		return $insert_id;
	}
	
	private function _db_readComment($tid)
	{
		$comment=array();
		return $comment;
	}
	
	private function _db_readCommentList($filter = array())
	{
		$comments=array();
		return $comments;
	}
	private function _db_updateComment($id,$comment=array())
	{
		$com=array();
		
		return $com;
	}

	private function _db_deleteComment($tid)
	{
		return ;
	}

	private function _db_deleteCommentList($pid)
	{
		return ;
	}
	
    public function getCommentDefault()
    {
        $user = $this->GetUser();
        $record = array('id' => 0,
            'title' => _COMMENT,
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
            'module_name' => $this->module_name,
            'attachment' => serialize(array()),
            'info' => MX_REMOTE_ADDR." - ".MX_REMOTE_HOST." - ".MX_USER_AGENT,
            'config' => serialize(array()),
            );
        return $record;
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
  
  
}

/*
CREATE TABLE IF NOT EXISTS `${prefix}_comments` (
  `tid` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL DEFAULT '0',
  `sid` int(11) NOT NULL DEFAULT '0',
  `modul_name` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `reply_date` int(11) NOT NULL DEFAULT '0',
  `name` varchar(60) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `uid` int(11) NOT NULL DEFAULT '0',
  `host_name` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL,
  `subject` varchar(85) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `comment` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`tid`),
  KEY `modul_name` (`modul_name`),
  KEY `sid` (`sid`),
  KEY `reply_date` (`reply_date`),
  KEY `uid` (`uid`),
  KEY `host_name` (`host_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
*/
 ?>