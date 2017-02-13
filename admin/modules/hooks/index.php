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
 * hooks_admin
 *
 * @package pragmaMx
 * @author tora60
 * @copyright Copyright (c) 2014
 * @version $Id: index.php 6 2015-07-08 07:07:06Z PragmaMx $
 * @access public
 */
class hooks_admin {
    private $_messages = array();
    private $_errors = array();
    private $_template;
    private $_cache;
    private $_config = array();

    /**
     * hooks_admin::__construct()
     *
     * @param mixed $op
     */
    public function __construct($op)
    {
        /* versch. Funktionen werden davon benötigt */
        load_class('Hook', false);

        /* Sprachdatei auswählen */
        mxGetLangfile(__DIR__);

        /* Template initialisieren */
        $this->_template = load_class('Template');
        $this->_template->init_path(__FILE__);

        $this->_config = load_class('Config', 'pmx.hooks');

        $this->_cache = load_class('Cache');
        $this->_cache->sFileExtension = pmxHook::cache_extension;

        /* was tun? */
        switch ($op) {
            case PMX_MODULE . '/save':
                return $this->save();
            default:
                return $this->hookslist();
        }
    }

    /**
     * hooks_admin::hookslist()
     *
     * @return
     */
    private function hookslist()
    {
        $checked = $this->_config->deactivated;

        $cacheid = __METHOD__;
        if (($files = $this->_cache->read($cacheid)) === false) {
            $files[1] = self::_get_files('hook');
            $files[2] = self::_get_lang_files('hook');
            $files[3] = self::_get_files('mod');
            $files[4] = self::_get_lang_files('mod');

            $this->_cache->write($files, $cacheid, 18000); // 18000  5 Stunden Cachezeit
        }

        $zus1 = array();
        foreach ($files[1] as $hook_name => $list) {
            foreach ($list as $id => $module_name) {
                if (in_array($id, $checked)) {
                    $zus1[$hook_name][$id] = $module_name;
                }
            }
        }

        $zus2 = array();
        foreach ($files[2] as $hook_name => $list) {
            foreach ($list as $id => $module_name) {
                if (in_array($id, $checked)) {
                    $zus2[$hook_name][$id] = $module_name;
                }
            }
        }

        $zus3 = array();
        foreach ($files[3] as $hook_name => $list) {
            foreach ($list as $id => $module_name) {
                if (in_array($id, $checked)) {
                    $zus3[$hook_name][$id] = $module_name;
                }
            }
        }

        $zus4 = array();
        foreach ($files[4] as $hook_name => $list) {
            foreach ($list as $id => $module_name) {
                if (in_array($id, $checked)) {
                    $zus4[$hook_name][$id] = $module_name;
                }
            }
        }

        $tabs['nhg']['title'] = _HOOKS_GROUPH;
        $tabs['nhg']['data']['nhgh']['title'] = _HOOKS_HOOKS;
        $tabs['nhg']['data']['nhgh']['data'] = $files[1];
        $tabs['nhg']['data']['nhgs']['title'] = _HOOKS_LANGUAGES;
        $tabs['nhg']['data']['nhgs']['data'] = $files[2];

        $tabs['nhz']['title'] = _HOOKS_GROUPZ;
        $tabs['nhz']['data']['nhzh']['title'] = _HOOKS_HOOKS;
        $tabs['nhz']['data']['nhzh']['data'] = $zus1;
        $tabs['nhz']['data']['nhzs']['title'] = _HOOKS_LANGUAGES;
        $tabs['nhz']['data']['nhzs']['data'] = $zus2;

        $tabs['nmg']['title'] = _HOOKS_GROUPM;
        $tabs['nmg']['data']['nmgh']['title'] = _HOOKS_HOOKS;
        $tabs['nmg']['data']['nmgh']['data'] = $files[3];
        $tabs['nmg']['data']['nmgs']['title'] = _HOOKS_LANGUAGES;
        $tabs['nmg']['data']['nmgs']['data'] = $files[4];

        $tabs['nmz']['title'] = _HOOKS_GROUPZ;
        $tabs['nmz']['data']['nmzh']['title'] = _HOOKS_HOOKS;
        $tabs['nmz']['data']['nmzh']['data'] = $zus3;
        $tabs['nmz']['data']['nmzs']['title'] = _HOOKS_LANGUAGES;
        $tabs['nmz']['data']['nmzs']['data'] = $zus4;

        $this->_template->assign('tabs', $tabs);
        $this->_template->assign('checked', $checked);

        include('header.php');
        $this->_template->display('hookslist.html');
        include('footer.php');
    }

    /**
     * hooks_admin::save()
     *
     * @return
     */
    private function save()
    {
        $set = array();
        if (isset($_POST['id']) && is_array($_POST['id'])) {
            $set = array_keys($_POST['id']);
        }
        $this->_config->deactivated = $set;

        /* den Hook Cache zurücksetzen, hat sich ja was geändert ;-) */
        $this->_cache->truncate();

        /* und einfach wieder zur Liste... */
        return $this->hookslist();
    }

    /**
     * hooks_admin::_get_files()
     *
     * @return array or false
     */
    private function _get_files($type = 'hook')
    {
        $excludes = array(/* diese Dateien ignorieren */
            'index',
            'htaccess',
            'install.delfiles',
            'install.tabledef',
            );

        $files = array();

        /* alle hook-Dateien einlesen */

        foreach ((array)glob(PMX_MODULES_DIR . DS . '*' . DS . 'core' . DS . '*.php', GLOB_NOSORT) as $filename) {
            if ($filename && $info = pathinfo($filename)) {
                $module_name = basename(dirname($info['dirname']));
                $hook_name = $info['filename'];
                $id = pmxHook::id($module_name, $hook_name);
                switch (true) {
                    case in_array($hook_name, $excludes) :
                        /* diese Dateien ignorieren */
                        break;
                    case $type == 'mod':
                        $files[$module_name][$id] = $hook_name;
                        break;
                    default:
                        $files[$hook_name][$id] = $module_name;
                }
            }
        }
        ksort($files);
        foreach ($files as $key => $value) {
            natcasesort($files[$key]);
        }

        return $files;
    }

    /**
     * hooks_admin::_get_lang_files()
     *
     * @return array or false
     */
    private function _get_lang_files($type = 'hook')
    {
        $excludes = array(/* diese Dateien ignorieren */
            'index',
            'htaccess',
            // 'install.delfiles',
            // 'install.tabledef',
            );

        $files = array();

        /* alle hook-Dateien einlesen */

        $langfiles = (array)glob(PMX_MODULES_DIR . '/*/language/lang-*.core.php', GLOB_NOSORT);
        // $langfiles += (array)glob(PMX_ADMINMODULES_DIR . '/*/language/lang-*.core.php', GLOB_NOSORT);
        $langfiles = array_filter($langfiles);
        foreach ($langfiles as $filename) {
            if ($filename && $info = pathinfo($filename)) {
                $module_name = basename(dirname($info['dirname']));
                $hook_name = $info['filename'];
                $id = pmxHook::id($module_name, $hook_name);
                switch (true) {
                    case in_array($hook_name, $excludes) :
                        /* diese Dateien ignorieren */
                        break;
                    case $type == 'mod':
                        $files[$module_name][$id] = $hook_name;
                        break;
                    default:
                        $files[$hook_name][$id] = $module_name;
                }
            }
        }
        ksort($files);
        foreach ($files as $key => $value) {
            natcasesort($files[$key]);
        }

        return $files;
    }
}

$tmp = new hooks_admin($op);
$tmp = null;

?>