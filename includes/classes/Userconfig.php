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
 * pmxUserconfig
 *
 * @package
 * @author tora60
 * @copyright Copyright (c) 2010
 * @version $Id: Userconfig.php 6 2015-07-08 07:07:06Z PragmaMx $
 * @access public
 */
class pmxUserconfig {
    protected $_configfile = '';
    protected static $_config = array();
    private static $_defaults = array();

    /**
     * pmxUserconfig::__construct()
     */
    public function __construct()
    {
        $this->_configfile = PMX_MODULES_DIR . DS . 'Your_Account' . DS . 'config.php';
		$this->_check_configfile($this->_configfile);
        /* Konfiguration einlesen */
        return $this->_init();
    }

    /**
     * pmxUserconfig::get_config()
     *
     * @return array
     */
    public function get_config()
    {
        if (!self::$_config) {
            return $this->_init();
        }
        return self::$_config;
    }

    /**
     * pmxUserconfig::get_defaults()
     *
     * @return
     */
    public function get_defaults()
    {
        if (!self::$_defaults) {
            /* Standardwerte einlesen */
            $this->_init_defaults();
        }
        return self::$_defaults;
    }

    /**
     * pmxUserconfig::get()
     *
     * @param mixed $name
     * @return
     */
    public function get($name)
    {
        return $this->__get($name);
    }

    /**
     * pmxUserconfig::__get()
     *
     * @param mixed $name
     * @return
     */
    public function __get($name)
    {
        if (array_key_exists($name, self::$_config)) {
            return self::$_config[$name];
        }
        $trace = debug_backtrace();
        trigger_error('undefined property \'' . $name . '\' in ' . mx_strip_sysdirs($trace[0]['file']) . ' line ' . $trace[0]['line'], E_USER_NOTICE);
        return null;
    }

    /**
     * pmxUserconfig::get_config()
     *
     * @return
     */
    private function _init()
    {
        if (self::$_config) {
            return self::$_config;
        }

        if (!defined('mxYALoaded')) {
            define('mxYALoaded', true);
        }
        $conf = array();
        include($this->_configfile);
        $conf = get_defined_vars();

        if (!isset($conf['upoints_entries'])) {
            $this->_update_points($conf);
        }

        if (!isset($conf['agb_agree'])) {
            $this->_update_agb_agree($conf);
        }

        self::$_config = array_merge($this->get_defaults(), $conf);
    }

    /**
     * pmxUserconfig::_init_defaults()
     *
     * @return
     */
    private function _init_defaults()
    {
        self::$_defaults = array(/* Standardwerte */
            'senddeletemail' => 1,
            'sendaddmail' => 1,
            'allowchangetheme' => 0,
            'yastartpage' => 'modules.php?name=Your_Account',
            'yaproofdate' => 0,

            'sendnewusermsg' => 0,
            'msgadminid' => intval(mxSessionGetVar('user_uid')),
            'msgicon' => '',
            'msgdefaultlang' => $GLOBALS['language'],

            'useuserpoints' => 0,
            'excludedusers' => '',
            'upoints_entries' => 4,
            'upoints_pics' => 3,
            'upoints_comments' => 1,
            'upoints_votes' => 1,
            'upoints_posts' => 2,
            'upoints_threads' => 3,

            'agb_agree' => 1,
            'agb_agree_link' => 'modules.php?name=legal',
            'pp_link' => 'modules.php?name=legal&amp;file=privacy',
            'minpass' => 4,
            'showusergroup' => 0,
            'uname_min_chars' => 4,
            'uname_space_chars' => 0,
            'uname_special_chars' => 0,
            'uname_caseinsensitive' => 0,
            /* 0 = Send password direct. 1 = Send first a confirmation - code */
            'passlost_codeoption' => 1,

            );

        /* alte Werte aus config.php einlesen */
        $oldvals = array(/* Standardwerte */
            'vkpUserregoption' => array('register_option', 2),
            'default_group' => array('default_group', 1),
            'pm_poptime' => array('pm_poptime', 0),
            );
        foreach ($oldvals as $key => $value) {
            if (isset($GLOBALS[$key])) {
                self::$_defaults[$value[0]] = $GLOBALS[$key];
            } else {
                self::$_defaults[$value[0]] = $value[1];
            }
        }

        /* Standardwerte der Userpic-Klasse einlesen */
        $pici = load_class('Userpic');
        self::$_defaults = array_merge(self::$_defaults, $pici->get_defaults());
    }

    /**
     * pmxUserconfig::_update_points()
     * Userpunkte aus alter Konfigurationsdatei konvertieren
     *
     * @param mixed $config
     * @return
     */
    private function _update_points(&$config)
    {
        extract($config);
        // $config['upoints'] = array('entries','pics','comments','votes','posts','threads');
        /* Beiträge berechnen */
        $entries = compact('points_kalender', 'points_reviews1', 'points_links1', 'points_downloads1', 'points_artikel');
        if ($entries) {
            $config['upoints_entries'] = ceil(array_sum($entries) / count($entries));
        }

        /* Bilder berechnen */
        $entries = compact('points_bilder1', 'coppermine_bilder');
        if ($entries) {
            $config['upoints_pics'] = ceil(array_sum($entries) / count($entries));
        }

        /* Kommentare berechnen */
        $entries = compact('points_kommentare', 'points_umfragen', 'points_reviews2', 'points_bilder2', 'coppermine_comments');
        if ($entries) {
            $config['upoints_comments'] = ceil(array_sum($entries) / count($entries));
        }

        /* Bewertungen berechnen */
        $entries = compact('points_downloads2', 'points_links2', 'coppermine_votes');
        if ($entries) {
            $config['upoints_votes'] = ceil(array_sum($entries) / count($entries));
        }

        /* Forum Posts berechnen */
        $entries = compact('points_fposts', 'points_smf_posts');
        if ($entries) {
            $config['upoints_posts'] = ceil(array_sum($entries) / count($entries));
        }

        /* Forum Threads berechnen */
        $entries = compact('points_fthreads', 'points_smf_topics', 'points_smf_polls');
        if ($entries) {
            $config['upoints_threads'] = ceil(array_sum($entries) / count($entries));
        }
    }

    /**
     * pmxUserconfig::_update_agb_agree()
     * ab pragmaMx 2.0
     * AGB-Zustimmoption aus alter Konfigurationsdatei konvertieren
     *
     * @param mixed $config
     * @return
     */
    private function _update_agb_agree(&$config)
    {
        $thelink = '';

        switch (true) {
            // Option garnicht angewählt
            case empty($config['agb_content']):
                break;
            // wenn bereits ein Link existiert, diesen auch verwenden (unwahrscheinlich)
            case !empty($config['agb_agree_link']):
                $thelink = $config['agb_agree_link'];
                break;
            // Sections
            case !empty($config['agb_content_sub1']):
                $thelink = 'modules.php?name=Sections&amp;op=viewarticle&amp;artid=' . intval($config['agb_content_sub1']);
                break;
            // Content
            case !empty($config['agb_content_sub2']):
                $thelink = 'modules.php?name=Content&amp;pid=' . intval($config['agb_content_sub2']);
                break;
            // Module
            case !empty($config['agb_content_sub3']):
                global $prefix;
                $result = sql_query("SELECT `title` FROM `{$prefix}_modules` WHERE `active`=1 AND `mid`=" . intval($config['agb_content_sub3']));
                list($modname) = sql_fetch_row($result);
                if (!empty($modname)) {
                    $thelink = 'modules.php?name=' . $modname;
                }
                break;
        }

        $config['agb_agree'] = ($thelink) ? 1 : 0;
        $config['agb_agree_link'] = $thelink;

        unset($config['agb_content'], $config['agb_content_sub1'], $config['agb_content_sub2'], $config['agb_content_sub3']);
    }
	
	private function _check_configfile($configfile)
	{
		/* check ob configfile vorhanden */
		if (file_exists($configfile)) return; // ja, raus hier
		
		$defaultfile=PMX_MODULES_DIR . DS . 'Your_Account' . DS . 'config.default.php';
		// ist ein defaultfile vorhanden ?
		if (file_exists($defaultfile)) {
			// in die config copieren
			if (copy($defaultfile,$configfile)) return;
		}
		// wenn alles fehlgeschlagen dann fehlermeldung
		die ("Configuration could not be loaded. Configuration file is missing.");
	}
}

?>