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
 * $Revision: 206 $
 * $Author: PragmaMx $
 * $Date: 2016-09-12 13:33:26 +0200 (Mo, 12. Sep 2016) $
 */

defined('mxMainFileLoaded') or die('access denied');

/**
 * pmxFilebrowse
 *
 * @package
 * @author tora60
 * @copyright Copyright (c) 2009
 * @version $Id: Filebrowse.php 206 2016-09-12 11:33:26Z PragmaMx $
 * @access public
 */
class pmxFilebrowse {
    /* Grundkonfiguration und Speicher für getter / setter... */
    protected $_config = array(/* Standardwerte */
        'manager' => 'elfinder',
        'title' => 'pragmaMx filemanager',
        'type' => '',
        'getback' => 'path',
        );

    protected $_getback_allowed = array(/* erlaubte Werte für $_config::getback, der erste ist Standard*/
        'path',
        'name',
        'url',
        );

    protected $_filemandir = '';
    protected $_filemanpath = '';

    protected $_defaults = array();

    /**
     * pmxFilebrowse::__construct()
     *
     * @param string $file
     */
    public function __construct($options = array())
    {
        $this->_filemandir = PMX_SYSTEM_DIR . DS . 'filemanager';
        $this->_filemanpath = PMX_SYSTEM_PATH . 'filemanager';
		
        /* userspezifische Config der Textarea-Config einlesen */
        load_class('Textarea', false);
        $userconfig = pmxTextarea::get_users_config();
		
        /* Standard-Config Daten zusammenfügen */
        $this->_defaults = array_merge($this->_config, $userconfig);

        /* Standard-Config Daten mit Übergabe-Parametern überschreiben */
        $this->_config = array_merge($this->_defaults, $options);
		
    }

    /**
     * pmxFilebrowse::set_type()
     * Dateityp setzen
     *
     * @param string $value
     * @return
     */
    public function set_type($value)
    {
        $this->set('type', $value);
    }

    /**
     * pmxFilebrowse::set_getback()
     * Art der Rückgabe z.B. Pfad oder nur Dateiname setzen
     *
     * @param string $value
     * @return
     */
    public function set_getback($value)
    {
        $this->set('getback', $value);
    }

    /**
     * pmxFilebrowse::add_root()
     * roots setzen
     *
     * @param mixed $root
     * @param string $alias
     * @param boolean $append
     * @return
     */
    public function add_root($root, $alias = '', $append = false)
    {
        /* bisherige roots zwischenspeichern */
        $roots = $this->_config['roots'];

        /* neue roots setzen */
        $this->set_root($root, $alias);

        if ($append) {
            /* neue roots HINTER die bisherigen setzen */
            $this->_config['roots'] = array_merge($roots, $this->_config['roots']);
        } else {
            /* neue roots VOR die bisherigen setzen STANDARD!! */
            $this->_config['roots'] = array_merge($this->_config['roots'], $roots);
        }
    }

    /**
     * pmxFilebrowse::set_root()
     * roots setzen
     *
     * @param mixed $root
     * @param string $alias
     * @return
     */
    public function set_root($root, $alias = '')
    {
        switch (true) {
            /* Ordner als String mit Alias */
            case $alias && is_string($root):
                // nicht über set() weil _check2set() sonst meckert..
                $this->_config['roots'] = array($alias => $root);
                break;

            /* Ordner nur als String */
            case is_string($root):
                $this->_config['roots'] = array(basename($root) => $root);
                break;

            /* Ordner (auch mehrere) als Array */
            case is_array($root):
                $roots = array();
                foreach ($root as $key => $value) {
                    if (is_numeric($key)) {
                        // z.B.: array('ordner/1', 'ordner/2')
                        $roots[basename($value)] = $value;
                    } else {
                        // z.B.: array('alias-1'=>'ordner/1', 'alias-2'=>'ordner/2')
                        $roots[$key] = $value;
                    }
                }

                if ($roots) {
                    $this->_config['roots'] = $roots;
                }
                break;
        }
    }

    /**
     * pmxFilebrowse::_get_html()
     *
     * @param string $editor
     * @return
     */
    protected function _get_html($editor = '', $arguments = array())
    {
        // TODO: Session chech
        $classname = $this->manager . '_browse';

        if (!class_exists($classname, false)) {
            $filename = 'browse.php';
            if ($incfile = realpath($this->_filemandir . DS . $this->manager . DS . $filename)) {
                include_once($incfile);
            } else {
                trigger_error($filename . ' for filemanager "' . $this->manager . '" is missing...', E_USER_WARNING);
                return false;
            }
        }
        if (!$editor) {
            // kann auch aus Config von textarea übernommen werden
            $editor = $this->editor;
        }

        /* schädliche Zeichen in $editor killen */
        $editor = preg_replace('#[^a-zA-Z0-9._-]#', '', $editor);

        $browse = new $classname($this);
        return $browse->getHtml($editor);
    }

    /**
     * pmxFilebrowse::connector()
     *
     * @return
     */
    public function connector()
    {
        $classname = $this->manager . '_connector';

        if (!class_exists($classname, false)) {
            $filename = 'connector.php';
            if ($incfile = realpath($this->_filemandir . DS . $this->manager . DS . $filename)) {
                include_once($incfile);
            } else {
                trigger_error($filename . ' for filemanager "' . $this->manager . '" is missing...' , E_USER_WARNING);
                return false;
            }
        }

        /* Falls Konfiguration in Session gespeichert, diese mit verwenden */
        if (isset($_GET['hash'])) {
            $session = mxSessionGetVar($_GET['hash']);
            if (is_array($session)) {
                $this->_config = array_merge($this->_config, $session);
            }
        }

        $connector = new $classname($this);
        return $connector->get();
    }

    /**
     * pmxFilebrowse::getConnectorUrl()
     *
     * @param array $arguments
     * @return
     */
    public function getConnectorUrl($arguments = array())
    {
        /* Parameter, die sich von der normalen Config unterscheiden,
         * in der Session speichern
         */
        $options = array_merge($this->_config, $arguments);
        $options = self::_diff_config($options, $this->_defaults);

        /* die _GET Werte auch übergeben, falls irgendwo gebraucht... */
        $options['GET'] = $_GET;

        /* aus den Parametern einen hash erstellen, der den Sessionkey bildet und auch der URL übergeben wird */
        $hash = 'q' . md5(serialize($options));
        mxSessionSetVar($hash, $options);

        $connectorurl = PMX_BASE_PATH . PMX_SYSTEM_PATH . 'classes/Filebrowse/connector.php?hash=' . $hash;
        return $connectorurl;
    }

    /**
     * pmxFilebrowse::getBrowseUrl()
     *
     * @param array $arguments
     * @return
     */
    public function getBrowseUrl($arguments = array())
    {
        // TODO: Session chech
        /* Parameter, die sich von der normalen Config unterscheiden,
         * in der Session speichern
         */

        if (!isset($arguments['editor'])) {
            $arguments['editor'] = 'standallone';
        }
        $options = self::_diff_config($this->_config, $this->_defaults);
        $options = array_merge($options, $arguments);

        $qry = http_build_query($options, 'x', '&');
        $browseurl = PMX_BASE_PATH . PMX_SYSTEM_PATH . 'classes/Filebrowse/browse.php?' . $qry;

        return $browseurl;
    }

    /**
     * pmxFilebrowse::get_config()
     *
     * @return array
     */
    public function get_config()
    {
        return $this->_config;
    }

    /**
     * pmxFilebrowse::get_roots()
     *
     * @return array
     */
    public function get_root()
    {
        return $this->_config['roots'];
    }

    /**
     * pmxFilebrowse::is_active()
     *
     * @return boolean
     */
    public function is_active()
    {
        return ($this->_config['manager']) ? true : false;
    }

    /**
     * pmxFilebrowse::set()
     *
     * @param string $name
     * @param mixed $value
     * @return
     */
    public function set($name, $value = null)
    {
        if (is_array($name) && $value === null) {
            foreach ($name as $key => $value) {
                $this->_check2set($key, $value);
                $this->_config[$key] = $value;
            }
        } else {
            $this->_check2set($name, $value);
            $this->_config[$name] = $value;
        }
    }

    /**
     * pmxFilebrowse::_check2set()
     *
     * @param mixed $key
     * @param mixed $value
     * @return
     */
    protected function _check2set(&$key, &$value)
    {
        switch ($key) {
            case 'getback':
                if (!$value || !in_array($value, $this->_getback_allowed)) {
                    $value = $this->_getback_allowed[0];
                }
                return;
            case 'roots':
            case 'root':
            case 'manager':
                // Wert darf nicht über set() gesetzt werden
                trigger_error('Can\'t set "' . $key . '" with ' . __CLASS__ . '::set() method', E_USER_ERROR);
                return;
        }
    }

    /**
     * pmxFilebrowse::__get()
     *
     * @param string $name
     * @return
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->_config)) {
            return $this->_config[$name];
        }
        $trace = debug_backtrace();
        trigger_error('undefined property \'' . $name . '\' in ' . mx_strip_sysdirs($trace[0]['file']) . ' line ' . $trace[0]['line'], E_USER_NOTICE);
        return null;
    }

    /**
     * pmxFilebrowse::__set()
     *
     * @param string $name
     * @param mixed $value
     * @return
     */
    public function __set($name, $value)
    {
        $this->set($name, $value);
    }

    /**
     * pmxFilebrowse::__call()
     * overload für _get_html(), z.B. $fm->dialog()
     *
     * @param mixed $editor
     * @param mixed $arguments
     * @return
     */
    public function __call($editor, $arguments)
    {
        return $this->_get_html($editor, $arguments);
    }

    /**
     * pmxFilebrowse::_diff_config()
     *
     * @param mixed $array1
     * @param mixed $array2
     * @return array
     */
    protected static function _diff_config($array1, $array2)
    {
        $options = array();
        foreach ($array2 as $key => $value) {
            if (isset($array1[$key]) && $array1[$key] != $value) {
                $options[$key] = $array1[$key];
            }
        }
        foreach ($array1 as $key => $value) {
            if (!isset($array2[$key])) {
                $options[$key] = $value;
            }
        }

        return $options;
    }

    /**
     * pmxFilebrowse::get_available_managers()
     *
     * @return array
     */
    public static function get_available_managers()
    {
        static $managers = array();
        if (!$managers) {
            $files = (array)glob(dirname(__DIR__) . DS . 'filemanager' . DS . '*' . DS . 'manager');
            foreach ($files as $filename) {
                if ($filename) {
                    $subfiles = (array)glob($filename . DS . '*');
                    // mxDebugFuncVars($filename,$subfiles);
                    // manager nur auflisten wenn auch die Dateien vorhanden sind
                    if (count($subfiles) > 4) { // 17
                        $managers[] = basename(dirname($filename));
                    }
                }
            }
        }
        return $managers;
    }
}

?>