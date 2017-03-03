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
 * pmxCaptcha
 * Vereinfachung der Handhabung des Captcha Systems
 *
 * @package pragmaMx
 * @author tora60
 * @copyright Copyright (c) 2008
 * @version $Id: Captcha.php 6 2015-07-08 07:07:06Z PragmaMx $
 * @access public
 */
class pmxCaptcha {
    /* Grundeinstellungen */
    protected $_config = array();

    /* Pfad zu den Captcha Dateien */
    protected $_lib_dir = '';
    protected $_lib_pat = '';
    protected $_settingsfile = '';

    /* die Einstell-Werte aus den Modulen */
    private static $_hook_vars = array();

    private $__section = false;

    /**
     * pmxCaptcha::__construct()
     * Konstruktor initialisiert die KLasse
     */
    public function __construct($section = false)
    {
        /* Pfad zu den Captcha Dateien */
        $this->_lib_dir = PMX_SYSTEM_DIR . DS . 'classes' . DS . 'Captcha' . DS;
        $this->_lib_pat = PMX_SYSTEM_PATH . 'classes/Captcha/';
        $this->_settingsfile = $this->_lib_dir . 'settings.php';

        /* Konfiguration abrufen */
        include($this->_settingsfile);

        /* Konfigurationsfallback... */
        $this->_config = get_defined_vars();
        unset($this->_config['GLOBALS'], $this->_config['_FILES'], $this->_config['_COOKIE'], $this->_config['_POST'], $this->_config['_GET'], $this->_config['tmp']);
        $this->_config = array_merge($this->_defaultvalues(), $this->_config);

        $this->__section = $section;
        $this->_config['active'] = $this->get_active($section);

        /* zufaellige ID erzeugen */
        $this->_config['id'] = 'c' . substr(md5(uniqid(rand())), 0, 6);
    }

    /**
     * pmxCaptcha::__get()
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
     * pmxCaptcha::__set()
     *
     * @param string $name
     * @param mixed $value
     * @return
     */
    public function __set($name, $value)
    {
        $this->_config[$name] = $value;
    }

    /**
     * pmxCaptcha::check()
     *
     * @param mixed $array
     * @param string $array_key
     * @return boolean
     */
    public function check($array, $array_key = 'captcha')
    {
        switch (true) {
            case MX_IS_ADMIN:
            case MX_IS_USER && !$this->_config['captchauseron']:
            case $this->captchasession && $this->_session_get();
            case !$this->active:
                return true;
            case !$array:
            case !is_array($array):
            case empty($array[$array_key]):
                return false;
            default:
                require_once($this->_lib_dir . 'phrasefactory.php');
                $result = PhraseFactory::get(false, $this->passphraselenght, $array[$array_key]);
                $result = ($array[$array_key] === $result);
                if ($this->captchasession) {
                    $this->_session_set($result);
                }
                return $result;
        }
    }

    /**
     * pmxCaptcha::posted()
     *
     * @param mixed $array
     * @param string $array_key
     * @return boolean
     */
    public function posted($array, $array_key = 'captcha')
    {
        switch (true) {
            case !$array:
            case !is_array($array):
            case !isset($array[$array_key]):
                return false;
            default:
                return true;
        }
    }

    /**
     * pmxCaptcha::set_active()
     *
     * @param mixed $mode
     * @return
     */
    public function set_active($mode = true)
    {
        $this->active = $mode;
    }

    /**
     * pmxCaptcha::get_active()
     *
     * @return
     */
    public function get_active($section = false)
    {
        /* wenn section bereits dem Konstruktur übergeben wurde, diese verwenden */
        if ($section === false && $this->__section) {
            $section = $this->__section;
        }

        switch (true) {
            case MX_IS_ADMIN:
            case MX_IS_USER && !$this->_config['captchauseron']:
            case $this->captchasession && $this->_session_get();
                return false;
                break;
            case $section === false:
                return true;
                break;
            case isset($this->_config[$section]):
                return $this->_config[$section];
                break;
            default:
                return true;
        }
    }

    /**
     * pmxCaptcha::show_image()
     *
     * @param array $attr
     * @return string HTML-Tag
     */
    public function show_image($attr = array())
    {
        if ($this->active) {
            $defaults = array(// Standardwerte die durch $attr überschrieben werden könnten
                'src' => 'nur zur Sortierung ;-)',
                'id' => 'nur zur Sortierung ;-)',
                //'name' => 'captcha' . $this->id,
                'height' => $this->imageheight,
                'width' => $this->imagewidth,
                //'align' => 'middle',
                //'border' => 0,
                'alt' => 'captcha',
                'class' => 'captcha-image',
                );
            $values = array(// wichtige unveränderliche Werte
                'src' => $this->_lib_pat . 'captchaimg.php?' . md5(crypt(uniqid(rand()),rand())),
                'id' => 'captcha' . $this->id,
                );
            $attr = array_merge($defaults, (array)$attr, $values);
            $attr = $this->_htmlattribs($attr);

            return '<img' . $attr . ' />';
        }
        return '';
    }

    /**
     * pmxCaptcha::show_reloadbutton()
     *
     * @return
     */
    public function show_reloadbutton($attr = array())
    {
        $out = '';
        if ($this->active) {
            pmxHeader::add_script($this->_lib_pat . 'reload.js');

            $defaults = array(// Standardwerte die durch $attr überschrieben werden könnten
                'value' => _CAPTCHARELOAD,
                'class' => 'captcha-reload',
                );
            $values = array(// wichtige unveränderliche Werte
                'type' => 'button',
                'onclick' => 'captcha_reload(\'' . $this->id . '\'); return false;',
                );
            $attr = array_merge($defaults, (array)$attr, $values);
            $attr = $this->_htmlattribs($attr);

            $out = '<input' . $attr . ' />';
        }
        return $out;
    }

    /**
     * pmxCaptcha::show_inputfield()
     *
     * @return
     */
    public function show_inputfield($attr = array())
    {
        $out = '';
        if ($this->active) {
            $defaults = array(// Standardwerte die durch $attr überschrieben werden könnten
                'value' => '',
                'size' => '20',
                'maxlength' => ($this->passphraselenght >= 10) ? intval($this->passphraselenght * 1.5) : 10 ,
                'class' => 'captcha-input',
                );
            $values = array(// wichtige unveränderliche Werte
                'type' => 'text',
                'name' => 'captcha',
                'id' => 'captchainput' . $this->id,
                );
            $attr = array_merge($defaults, (array)$attr, $values);
            $attr = $this->_htmlattribs($attr);
            // todo: maxlength?
            $out = '<input' . $attr . ' />';
        }
        return $out;
    }

    /**
     * pmxCaptcha::show_caption()
     *
     * @return
     */
    public function show_caption($attr = array(), $caption = _CAPTCHAINSERT)
    {
        $out = '';
        if ($this->active) {
            $defaults = array(// Standardwerte die durch $attr überschrieben werden könnten
                'class' => 'captcha-caption',
                );
            $values = array(// wichtige unveränderliche Werte
                'for' => 'captchainput' . $this->id,
                );
            $attr = array_merge($defaults, (array)$attr, $values);
            $attr = $this->_htmlattribs($attr);
            $out = '<label' . $attr . '>' . $caption . '</label>';
        }
        return $out;
    }

    /**
     * pmxCaptcha::show_complete()
     *
     * @return
     */
    public function show_complete($attr = array())
    {
        $out = '';
        if ($this->active) {
            $defaults = array(// Standardwerte die durch $attr überschrieben werden könnten
                'class' => 'captcha-area',
                );
            $attr = array_merge($defaults, (array)$attr);
            $attr = $this->_htmlattribs($attr);
            $out .= '<div' . $attr . '>';
            $out .= $this->show_image();
            // $out .= '&nbsp;';
            $out .= $this->show_reloadbutton();
            // $out .= '</div><div>';
            $out .= $this->show_caption();
            // $out .= '</div><div>';
            $out .= $this->show_inputfield();
            $out .= '</div>'; #</div>
        }
        return $out;
    }

    /**
     * pmxCaptcha::__call()
     *
     * @param mixed $function
     * @param mixed $arguments
     * @return mixed , result of called function
     */
    public function __call($function, $arguments)
    {
        if (method_exists($this, 'show_' . $function)) {
            switch (count($arguments)) {
                case 0:
                    return call_user_func(array(&$this, 'show_' . $function));
                case 1:
                    return call_user_func(array(&$this, 'show_' . $function), $arguments[0]);
                case 2:
                    return call_user_func(array(&$this, 'show_' . $function), $arguments[0], $arguments[1]);
                case 3:
                    return call_user_func(array(&$this, 'show_' . $function), $arguments[0], $arguments[1], $arguments[2]);
                case 4:
                    return call_user_func(array(&$this, 'show_' . $function), $arguments[0], $arguments[1], $arguments[2], $arguments[3]);
                default:
                    return call_user_func(array(&$this, 'show_' . $function), $arguments);
            }
        } else {
            return false;
        }
    }

    /**
     * pmxCaptcha::_defaultvalues()
     *
     * @return
     */
    protected function _defaultvalues()
    {
        $set = array(// Konfigurationsfallback
            'passphraselenght' => 4,
            'charstouse' => '23456789abcdfghjkmnpqrstvwxABCDEFGHJKLMNPRSTUVWXYZ',
            'casesensitive' => 0,
            'imagewidth' => 140,
            'imageheight' => 40,
            'fontsize' => 24,
            'bgintensity' => 40,
            'bgfonttype' => 3,
            'scratchamount' => 30,
            'filter' => 0,
            'filtertype' => 'Wavy',
            'scratches' => 1,
            'addagrid' => 1,
            'addhorizontallines' => 1,
            'useRandomColors' => 1,
            'minsize' => 24,
            'angle' => 15,
            'captchasession' => 0,
            'captchauseron' => 0,
            'commentson' => 1,
            // old deprecated vars  << pragmaMx 2.0 :
            'feedbackon' => 1,
            'faqon' => 1,
            'downloadson' => 1,
            'weblinkson' => 1,
            'guestbookon' => 1,
            'newson' => 1,
            'newsletteron' => 1,
            'reviewson' => 1,
            'recommendon' => 1,
            'registrationon' => 1,
            'documentson' => 1,
            );

        /* die Daten aus den Modul-Hooks zufügen */
        $hookvalues = $this->_get_hook_vars();
        foreach ($hookvalues as $hook) {
            if (!$hook['hidden']) {
                $set[$hook['varname']] = $hook['default'];
            }
        }

        return $set;
    }

    /**
     * pmxCaptcha::_get_hook_vars()
     *
     * @return
     */
    protected function _get_hook_vars()
    {
        if (self::$_hook_vars) {
            return self::$_hook_vars;
        }

        $cache = load_class('Cache');
        if ((self::$_hook_vars = $cache->read(__METHOD__)) !== false) {
            return self::$_hook_vars;
        }

        $hook = load_class('Hook', 'captcha');
        $hook->set('only_active', false);
        $hook->set('only_allowed', false);

        self::$_hook_vars = (array)$hook->get();
        if (!self::$_hook_vars || !is_array(self::$_hook_vars)) {
            self::$_hook_vars = array();
        }

        $cache->write(self::$_hook_vars, __METHOD__);

        return self::$_hook_vars;
    }

    /**
     * pmxCaptcha::_htmlattribs()
     *
     * @param array $attr
     * @return string
     */
    private function _htmlattribs($attr)
    {
        $para = '';
        foreach ($attr as $key => $val) {
            if ($val === null) {
                continue;
            }

            if (is_array($val)) {
                $val = implode(' ', $val);
            }

            $key = htmlspecialchars($key, ENT_COMPAT | ENT_HTML5, 'UTF-8', false);
            $val = htmlspecialchars($val, ENT_COMPAT | ENT_HTML5, 'UTF-8', false);

            $para .= " $key=\"$val\"";
        }
        return $para;
    }

    /**
     * pmxCaptcha::_session_get()
     *
     * @return
     */
    private function _session_get()
    {
        $expire = mxSessionGetVar('valcapt');
        return intval($expire) > 0 && $expire > time();
    }

    /**
     * pmxCaptcha::_session_set()
     *
     * @param mixed $value
     * @return
     */
    private function _session_set($value)
    {
        if ($value) {
            $value = time() + MX_COOKIE_LIFETIME;
        } else {
            $value = false;
        }
        mxSessionSetVar('valcapt', $value);
        return $value;
    }
}

?>