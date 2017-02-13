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

/* Direktaufruf möglich, da nicht nur innerhalb von pragmaMx genutzt !! */

/**
 * pmxBrowser
 *
 * @package pragmaMx
 * @author tora60
 * @copyright Copyright (c) 2011
 * @version $Id: Browser.php 6 2015-07-08 07:07:06Z PragmaMx $
 * @access public
 */
class pmxBrowser {
    private static $_initialized = false;

    private static $_props = array(/* Standardwerte */
        'agent' => 'unknown',
        'name' => 'unknown',
        'version' => 0,

        'browser' => false,
        'browser_version' => false,
        'browser_icon' => false,
        'os' => false,
        'os_version' => false,
        'os_icon' => false,
        'robot' => false,
        'robot_version' => false,
        'robot_icon' => false,
        ) ;

    /**
     * pmxBrowser::__Construct()
     */
    public function __construct()
    {
        if (self::$_initialized) {
            return;
        }
        self::$_initialized = true;

        self::$_props['agent'] = strtolower($_SERVER['HTTP_USER_AGENT']);

        $this->_analyse($_SERVER['HTTP_USER_AGENT']);

        self::$_props['agent'] = strtolower($_SERVER['HTTP_USER_AGENT']);
        self::$_props['name'] = str_replace(' ', '_', strtolower(self::$_props['browser']));
        self::$_props['version'] = floatval(self::$_props['browser_version']) ;
    }

    /**
     * pmxBrowser::_analyse()
     *
     * based on:
     * project:	expCounter
     * file:  includes/functions.inc.php [last modified: 2010-06-27]
     * version:	1.2
     * copyright:	since	2009 © by Volker S. Latainski
     * license:	GPL 2.0 or higher(see docs/license.txt)
     * based on chCounter 3.1.3 by Christoph Bachner and Bert Körn
     *
     * @param mixed $useragent
     * @return
     */
    private function _analyse($useragent)
    {
        include(dirname(__FILE__) . '/Browser/user_agents.lib.php');

        $chC_ualib_browser = &$chC_ualib_browsers;
        $chC_ualib_robot = &$chC_ualib_robots;
        $libs = array('browser', 'os', 'robot');

        foreach($libs as $libname) {
            // Wenn Browser und OS schon durchgelaufen und Browser erfolgreich ermittelt,
            // ist es kein Robot, und das Durchsuchen der Arrays kann beendet werden
            if ((isset($browser) && isset($os)) && $browser != 'unknown') {
                break;
            }
            // jeweiliges Library-Array durchlaufen
            foreach(${'chC_ualib_'.$libname} as $name => $array) {
                if ($array['use_PCRE'] == 1) {
                    if (preg_match($array['pattern'], $useragent, $match)) {
                        if (!empty ($array['anti_pattern']) && preg_match($array['anti_pattern'], $useragent)) {
                            continue;
                        }
                        ${$libname} = $name;
                    }
                } elseif (is_int(strpos($useragent, $array['pattern']))) {
                    if (!empty($array['anti_pattern']) && is_int(strpos ($useragent, $array['anti_pattern']))) {
                        continue;
                    }
                    ${$libname} = $name;
                }
                // Wenn kein Treffer, Loop fortsetzen -> weitersuchen:
                if (!isset(${$libname})) {
                    continue;
                }
                // Ansonsten: Treffer
                // Icon
                ${$libname.'_icon'} = $array['icon'];
                // nach Version suchen?
                if ($array['use_PCRE'] == 1 && ${'chC_ualib_'.$libname}[${$libname}]['version'] != false) { // mit preg_match bereits Version ermittelt
                    if (!empty($match[ (int) ${'chC_ualib_'.$libname}[${$libname}]['version'] ])) {
                        ${$libname.'_version'} = $match[ (int) ${'chC_ualib_'.$libname}[${$libname}]['version'] ];
                    } else {
                        ${$libname.'_version'} = 'unknown';
                    }
                } elseif (is_array (${'chC_ualib_'.$libname}[${$libname}]['version'])) {
                    foreach(${'chC_ualib_'.$libname}[${$libname}]['version'] as $pattern => $version) {
                        if (is_int(strpos($useragent, (string) $pattern))) {
                            ${$libname.'_version'} = $version;
                            break;
                        }
                    }
                    if (!isset(${$libname.'_version'})) {
                        ${$libname.'_version'} = 'unknown';
                    }
                }
                // mit dem nächsten lib-Typ weiter
                continue 2;
            }
        }
        if (isset($robot)) {
            unset($browser);
        }

        self::$_props['browser'] = isset($browser) ? $browser : false;
        self::$_props['browser_version'] = isset($browser_version) ? $browser_version : false;
        self::$_props['browser_icon'] = isset($browser) ? $browser_icon : false;
        self::$_props['os'] = $os;
        self::$_props['os_version'] = isset($os_version) ? $os_version : false;
        self::$_props['os_icon'] = $os_icon;
        self::$_props['robot'] = isset($robot) ? $robot : false;
        self::$_props['robot_version'] = isset($robot_version) ? $robot_version : false;
        self::$_props['robot_icon'] = isset($robot_icon) ? $robot_icon : false;
    }

    public function get()
    {
    return self::$_props;
    }


    /**
     * pmxBrowser::__get()
     *
     * @param mixed $name
     * @return boolean is it the browser $name
     * @return boolean the Value of $_props->name
     */
    public function __get($name)
    {
        /* Direktabfrage des Browsernamens: $browser->browsername */
        if (!array_key_exists($name, self::$_props)) {
            $func = 'is_' . $name;
            return $this->$func(); // __call
        }

        /* Abfrage von Eigenschaften: $browser->version */
        return self::$_props[$name] ;
    }

    /**
     * pmxBrowser::__set()
     *
     * @param mixed $name
     * @param mixed $val
     * @return nothing
     */
    public function __set($name, $val)
    {
        self::$_props[$name] = $val ;
    }

    /**
     * pmxBrowser::__call()
     *
     * @param mixed $name
     * @param mixed $arguments
     * @return integer Browser-Version or false
     */
    public function __call($name, $arguments)
    {
        $name = explode('_', strtolower($name), 2);
        if ($name[0] == 'is' && isset($name[1]) && (self::$_props['name'] === $name[1])) {
            return self::$_props['version'];
        }
        return false;
    }

    /**
     * pmxBrowser::is_gecko()
     *
     * @return integer gecko-Version
     */
    public function is_gecko()
    {
        switch (true) {
            case !self::$_props['name']:
            case !self::$_props['version']:
            case !self::$_props['agent']:
                return false;
            case strpos(self::$_props['agent'], 'gecko/') !== false:
                return self::$_props['version'];
            default:
                return false;
        }
    }

    /**
     * pmxBrowser::is_ie()
     *
     * @return integer msie-Version
     */
    public function is_ie()
    {
        switch (true) {
            case !self::$_props['name']:
            case !self::$_props['version']:
            case !self::$_props['agent']:
                return false;
            case self::$_props['name'] === 'internet explorer':
            case self::$_props['name'] === 'avant browser':
            case strpos(self::$_props['agent'], ' msie ') !== false:
                return self::$_props['version'];
            default:
                return false;
        }
    }

    /**
     * pmxBrowser::is_msie()
     *
     * @return integer msie-Version
     */
    public function is_msie()
    {
        return $this->is_ie();
    }
}

?>