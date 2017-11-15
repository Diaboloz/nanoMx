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

/**
 * pmxPlugin
 *
 * @package pragmaMx
 * @author Olaf
 * @copyright Copyright (c) 2014
 * @version $Id: Plugin.php 6 2015-07-08 07:07:06Z PragmaMx $
 * @access public
 */
class pmxPlugin {
 
	private static 	$defaultRecord = array(
											/*  für Pluginliste */
						'name' => '',			/* angezeigter Name */
						'description' => '',	/* Beschreibung */
						'image' => "",			/* Bild */
						'hook' => '',			/* betroffener Hook */
						'admin' => false,		/* Adminbereich vorhanden ? */
						'backend'=>false,		/* auch im Backend aktiv ? */
						'path'=> __FILE__,		/* pfad zur Datei (relativ zur root) */
						'settings' =>array(),	/* Einstellungen des Plugins */
						'url' =>'',
						);	

    /**
     * pmxPlugin::__Construct()
     */
    function __construct()
    {
    }
	
	/**
	 *   Plugin.php:init
	 *  
	 *  @return 
	 *  
	 */
	 static public function init()
	{
		
	}

	/**
	 *   Plugin.php:_get_files
	 *  
	 *  @return 
	 *  
	 */
	 protected function _get_plugins()
	{
		/* alle Plugin-Dateien einlesen */

		$excludes = array('index', 'htaccess');

		$files = array();
		foreach ((array)glob(PMX_PLUGIN_PATH . DS . '*' . DS . 'core' . DS . 'plugin.menu.php', GLOB_NOSORT) as $filename) {
			if ($filename && $info = pathinfo($filename)) {
				$plugin_name = basename(dirname($info['dirname']));
				$hook_name = $info['filename'];
				$id = self::id($module_name, $hook_name);
				switch (true) {
					case in_array($hook_name, $excludes) :
					case in_array($id, $checked) :
						/* diese Dateien ignorieren */
						break;
					default:
						$files[$hook_name][$module_name] = $filename;
				}
			}
		}


		if (!isset($files[$this->_hookname])) {
			return false;
		}

		return $files[$this->_hookname];
	}
	
	/**
	 *   Plugin.php:_addPlugin
	 *  
	 *  @param [in] array Description
	 *  @return 
	 *  
	 */
	 static function addPlugin ($plugin=false)
	{
		if (!$plugin) return false;
		$prefix=pmxBase::prefix();
		$plugin= self::_checkrecord($plugin);
		if (trim($plugin['name'])=="") return false;
		
		$qryarr=array(
			"name"=>$plugin['name'],
			"description"=>mxAddSlashesForSQL($plugin['description']),
			"path"=>mxAddSlashesForSQL(normalizePath($plugin['path'])),
			"hook"=>mxAddSlashesForSQL($plugin['hook']),
			"image"=>mxAddSlashesForSQL($plugin['image']),
			"url"=>mxAddSlashesForSQL($plugin['url']),
			"settings"=>mxAddSlashesForSQL(serialize($plugin['settings'])),
			);
			$qry ="INSERT IGNORE INTO ${prefix}_plugins SET ";
			$qryset=array();
			foreach ($qryarr as $key => $value) {
				$qryset[]=$key."='".$value."'";
			}
			$qry .=implode(",",$qryset);
			
			if ($qry) pmxDatabase::query($qry);
			
		
		return pmxDatabase::insert_id();
	}
	
	/**
	 *   Plugin.php:_checkRecord
	 *  
	 *  @param [in] $record Description
	 *  @return 
	 *  
	 */
	private static function _checkRecord($record) {
		
		return array_merge (self::$defaultRecord,$record);
	}
}
?>