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
 * pmxLanguage
 *
 * @package pragmaMx
 * @author tora60
 * @copyright Copyright (c) 2008
 * @version $Id: Language.php 6 2015-07-08 07:07:06Z PragmaMx $
 * @access public
 */
class pmxLanguage {
    /* Speichert die Instanz der Klasse */
    private static $__instance;

    /* alle Sprachvariablen */
    private static $__words = array();

    /* die Spracheinstellungen */
    private static $__set = array(/* global settings */
        'id' => '',
        'current' => '',
        'default' => 'german',
        'available' => array('german'),
        'multi' => false,
        'flags' => false,
        );

    /**
     * pmxLanguage::__construct()
     * Ein private Konstruktor; verhindert die direkte Erzeugung des Objektes
     */
    private function __construct()
    {
        $arr_lang = array();
        if (isset($_REQUEST['newlang'])) {
            $arr_lang['request'] = preg_replace('#[^a-zA-Z0-9_]#', '_', $_REQUEST['newlang']);
        }
        $arr_lang['session'] = mxSessionGetVar('lang');
        if (MX_IS_USER) {
            $uinf = pmxUserStored::current_userdata();
            $arr_lang['userdata'] = $uinf['user_lang'];
        }
        // TODO: aus Browser ermitteln
        $arr_lang['config'] = $GLOBALS['language'];
        $arr_lang = array_merge($arr_lang, $GLOBALS['language_avalaible']);
        $arr_lang = array_intersect($arr_lang, $GLOBALS['language_avalaible']);
        $arr_lang = array_unique($arr_lang);

        $path = PMX_LANGUAGE_DIR;
        $key = '';
        foreach ($arr_lang as $key => $current) {
            if ($current) {
                // zuerst Benutzerdatei einbinden
                $file = realpath($path . DS . 'custom' . DS . 'lang-' . $current . '.php');
                if ($file) {
                    include_once($file);
                }
                // dann die normale..
                $file = realpath($path . DS . 'lang-' . $current . '.php');
                if ($file && include_once($file)) {
                    // Schleife verlassen
                    break;
                }
            }
        }

        self::_get_hooks($current);

        if ($key === 'request') {
            // TODO: evtl. global in Session Klasse verankern?
            /* Session nur aendern, wenn Script im root ausgeführt wird (problem mit default.css) */
            mxSessionSetVar('lang', $current);
        }

        $id = _DOC_LANGUAGE;
        if ($current === 'german') {
            $id .= '.sie';
        } else if ($current === 'german_du') {
            $id .= '.du';
        }

        self::$__set = array(/* global settings */
            'id' => $id,
            'current' => $current,
            'default' => $GLOBALS['language'],
            'available' => $GLOBALS['language_avalaible'],
            'multi' => (count($GLOBALS['language_avalaible']) > 1) ? $GLOBALS['multilingual'] : 0,
            'flags' => $GLOBALS['useflags'],
            );
    }

    /**
     * pmxLanguage::instance()
     * Die Singleton Funktion
     *
     * @return
     */
    public static function instance()
    {
        if (!isset(self::$__instance)) {
            $self = __CLASS__;
            self::$__instance = new $self;
        }
        return self::$__instance;
    }

    /**
     * pmxLanguage::__clone()
     * Halte Benutzer vom Klonen der Instanz ab
     *
     * @return
     */
    public function __clone()
    {
        trigger_error('Klonen ist nicht erlaubt.', E_USER_ERROR);
    }

    /**
     * pmxLanguage::get()
     *
     * @param mixed $value_name
     * @return
     */
    public function get($value)
    {
        if (array_key_exists($value, self::$__words)) {
            return self::$__words[$value];
        }
        return $value;
    }

    /* Alias fuer get() */
    public function translate($value)
    {
        return self::get($value);
    }

    /* Alias fuer get() */
    public function _($value)
    {
        return self::get($value);
    }

    /**
     * Sets global config item
     *
     * @param string $name Config item's name
     * @param mixed $value Config item's value
     * @param string $section
     * @static
     */
    public function set($name, $value)
    {
        self::$__words[$name] = $value;
    }

    /**
     * pmxLanguage::__get()
     * gugge: http://www.php.net/manual/de/language.oop5.overloading.php
     *
     * @param mixed $value_name
     * @return
     */
    public function __get($value_name)
    {
        if (isset(self::$__set[$value_name])) {
            return self::$__set[$value_name];
        }
        return false;
    }

    /**
     * pmxLanguage::__set()
     *
     * @param mixed $key
     * @param mixed $value
     * @return
     */
    public function __set($key, $value)
    {
        return self::$__set[$key] = $value;
    }

    /**
     * pmxLanguage::words()
     * gugge: http://www.php.net/manual/de/language.oop5.overloading.php
     *
     * @param mixed $value_name
     * @return
     */
    public function words()
    {
        return self::$__words;
    }

    /**
     * pmxLanguage::append()
     *
     * @param mixed $path
     * @return
     */
    public function append($path)
    {
        $files = $this->_findfile($path);
        foreach ($files as $file) {
            $words = null;
            @include_once($file);
            if (is_array($words)) {
                self::$__words = array_merge(self::$__words, $words);
            }
        }
    }

    /**
     * pmxLanguage::_findfile()
     *
     * @param mixed $path
     * @return
     */
    private function _findfile($path)
    {
        $files = array();
        if ($this->current) {
            /* einen gueltigen Pfad erstellen */
            $path = rtrim($path, DS . '/');
            if (substr($path, -8) !== 'language') {
                $path .= DS . 'language';
            }

            /* einen gueltigen Dateinamen erstellen */
            $filename = 'lang-' . $this->current . '.php';

            /* zuerst die normale finden */
            $include = realpath($path . DS . $filename);
            if ($include) {
                $files[] = $include;
            }

            /* dann die Benutzerdatei.. */
            $include = realpath($path . DS . 'custom' . DS . $filename);
            if ($include) {
                $files[] = $include;
            }
        }

        return $files;
    }

    /**
     * pmxLanguage::_get_hooks()
     * stellt die Dateien für pmx_run_hook() zur Verfügung
     * kann aber auch standallone verwendet werden um die Dateien
     * direkt zu verwenden
     *
     * @param string $language (the current language)
     * @return nothing
     */
    protected function _get_hooks($language)
    {
        /* TODO: Admin berücksichtigen ? ?*/

        load_class('Hook', false);

        $cacheid = __METHOD__ . strval(MX_IS_ADMIN) . $language;
        $cache = load_class('Cache');
        $cache->sFileExtension = pmxHook::cache_extension;
        if (($constants = $cache->read($cacheid)) === false) {
            /* wenn Cache leer, alle hook-Dateien einlesen */

            $config = load_class('Config', 'pmx.hooks');
            $checked = $config->deactivated;

            $constants = array();
            $before = get_defined_constants(true);
            $before = $before['user'];
            foreach ((array)glob(PMX_MODULES_DIR . '/*/language/lang-' . $language . '.core.php', GLOB_NOSORT) as $filename) {
                if ($filename && $info = pathinfo($filename)) {
                    $module_name = basename(dirname($info['dirname']));
                    $hook_name = $info['filename'];
                    $id = pmxHook::id($module_name, $hook_name);
                    switch (true) {
                        case in_array($id, $checked) :
                            /* diese Dateien ignorieren */
                            break;
                        default:
                            include_once($filename);
                    }
                }
            }
            if (MX_IS_ADMIN) {
                foreach ((array)glob(PMX_ADMINMODULES_DIR . '/*/language/lang-' . $language . '.core.php', GLOB_NOSORT) as $filename) {
                    if ($filename) {
                        include_once($filename);
                    }
                }
            }
            $after = get_defined_constants(true);
            $after = $after['user'];
            $constants = array_diff_key($after, $before);
            $cache->write($constants, $cacheid, 18000); // 5 Stunden Cachezeit
        } else {
            foreach ($constants as $key => $value) {
                defined($key) OR define($key, $value);
            }
        }
        // mxDebugFuncVars($constants);exit;
    }
}

?>
