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
 * pmxHook
 *
 * @package pragmaMx
 * @author tora60
 * @copyright Copyright (c) 2014
 * @version $Id: Hook.php 6 2015-07-08 07:07:06Z PragmaMx $
 * @access public
 */
class pmxHook {
    /* der alles beherrschende Name des Hooks :-)
     * Musss im Konstruktor der Klasse übergeben werden!!
     */
    private $_hookname = 'undefined';

    private $_props = array(/* Array wird über setter dynamisch verändert */

        /* Dient zum Einschränken des Hooks auf bestimmte Module.
         * Es werden nur die Module berücksichtigt, die in diesem Array aufgeführt sind.
         */
        'modlist' => array(),

        /* Dient zum Einschränken des Hooks auf für den aktuellen User erlaubte Module. */
        'only_allowed' => true,

        /* Dient zum Einschränken des Hooks auf im System aktivierte Module. */
        'only_active' => true,
        );

    /* Kennzeichnung der für die Hooks generierten Cache Dateien */
    const cache_extension = '.hooks.php';

    /**
     * pmxHook::__Construct()
     */
    public function __construct($name)
    {
        if (preg_match('#[^a-z0-9.-_]#', $name)) {
            trigger_error('\'' . $name . '\' is not a valid name for a hook', E_USER_NOTICE);
            return false;
        }

        $this->_hookname = $name;
    }

    /**
     * pmxHook::get_files()
     *
     * Stellt die Dateien für run() zur Verfügung
     * kann aber auch standallone verwendet werden um die Dateien
     * direkt zu verwenden
     *
     * @return bolean
     */
    public function get_files()
    {
        if ($this->_props['modlist']) {
            /* nur die in $this->_props['modlist'] vorhandenen Module beachten */
            $files = $this->_get_files_fromlist();
        } else {
            /* alle hook-Dateien aus allen Modulen einlesen */
            $files = $this->_get_files();
        }
        return $files;
    }

    /**
     * pmxHook::get()
     *
     * @return mixed
     */
    public function get()
    {
        /* schauen ob Cache eingeschaltet und ob was drin ist */
        /* der Rückgabewert, kann in der Funktion $hook verändert werden,
         * wenn er per Referenz in der Funktion übergeben wird
         */
        $outcome = false;

        /* Dateien für diesen Hook auslesen */
        $files = $this->get_files();

        if ($files) {
            /* hier wird gleich der Modulname an die hook-Datei übergeben !! */
            foreach ($files as $module_name => $filename) {
                $hook = null;
                switch (true) {
                    case $this->_props['only_active'] && !mxModuleActive($module_name):
                    case $this->_props['only_allowed'] && !mxModuleAllowed($module_name):
                        break;
                    case !include($filename):
                    case !$hook:
                    case !is_callable($hook):
                        trigger_error('Invalid hook-file: ' . mx_strip_sysdirs($filename), E_USER_NOTICE);
                        break;
                    default:
                        /* anonyme Funktion $hook in der Hook-Datei ausführen */
                        $hook($module_name, $this->_props, $outcome);
                }
            }
        }

        return $outcome;
    }

    /**
     * pmxHook::run()
     *
     * @param mixed $outcome
     * @return bolean
     */
    public function run(&$outcome = null)
    {
        /* Dateien für diesen Hook auslesen */
        $files = $this->get_files();

        if ($files) {
            /* hier wird gleich der Modulname an die hook-Datei übergeben !! */
            foreach ($files as $module_name => $filename) {
                $hook = null;
                switch (true) {
                    case $this->_props['only_active'] && !mxModuleActive($module_name):
                    case $this->_props['only_allowed'] && !mxModuleAllowed($module_name):
                        break;
                    case !include($filename):
                    case !$hook:
                    case !is_callable($hook):
                        trigger_error('Invalid hook-file: ' . mx_strip_sysdirs($filename), E_USER_NOTICE);
                        break;
                    default:
                        /* anonyme Funktion $hook in der Hook-Datei ausführen */
                        $hook($module_name, $this->_props, $outcome);
                }
            }
        } else {
            return false;
        }

        /* Immer true/false ! Die Funktion hat keinen Rückgabewert,
         * nur der Übergabeparameter $outcome wird verändert/ergänzt
         */
        return true;
    }

    /**
     * pmxHook::_get_files()
     *
     * @return array or false
     */
    protected function _get_files()
    {
        /* alle hook-Dateien einlesen */
        $cache = load_class('Cache');
        $cache->sFileExtension = self::cache_extension;
        if (($files = $cache->read(__METHOD__)) === false) {
            $excludes = array('index', 'htaccess');

            $config = load_class('Config', 'pmx.hooks');
            $checked = $config->deactivated;

            $files = array();
            foreach ((array)glob(PMX_MODULES_DIR . DS . '*' . DS . 'core' . DS . '*.php', GLOB_NOSORT) as $filename) {
                if ($filename && $info = pathinfo($filename)) {
                    $module_name = basename(dirname($info['dirname']));
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
            $cache->write($files, __METHOD__, 18000); // 5 Stunden Cachezeit
        }

        if (!isset($files[$this->_hookname])) {
            return false;
        }

        return $files[$this->_hookname];
    }

    /**
     * pmxHook::_get_files_fromlist()
     *
     * @return
     */
    protected function _get_files_fromlist()
    {
        settype($this->_props['modlist'], 'array');

        $allfiles = $this->_get_files($this->_hookname);
        if (!$allfiles) {
            return false;
        }

        $files = array();
        foreach ($this->_props['modlist'] as $modname) {
            if (array_key_exists($modname, $allfiles)) {
                $files[$modname] = $allfiles[$modname];
            }
        }

        return $files;
    }

    /**
     * pmxHook::__get()
     *
     * @param mixed $name
     * @return
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->_props)) {
            return $this->_props[$name];
        }
        $trace = debug_backtrace();
        trigger_error('undefined property \'' . $name . '\' in ' . mx_strip_sysdirs($trace[0]['file']) . ' line ' . $trace[0]['line'], E_USER_NOTICE);
        return null;
    }

    /**
     * pmxHook::__set()
     *
     * @param string $name
     * @param mixed $value
     * @return
     */
    public function __set($name, $value)
    {
        $this->_props[$name] = $value;
    }

    /**
     * pmxHook::set()
     *
     * @param mixed $name
     * @param mixed $value
     * @return
     */
    public function set($name, $value = null)
    {
        if (is_array($name)) {
            foreach ($name as $key => $value) {
                $this->_props[$key] = $value;
            }
        } else {
            $this->_props[$name] = $value;
        }
    }

    /**
     * pmxHook::id()
     *
     * @param string $module_name
     * @param string $hook_name
     * @return string
     */
    public static function id($module_name, $hook_name)
    {
        return preg_replace('#\W#', '_', $module_name . '_' . $hook_name);
    }
}

?>