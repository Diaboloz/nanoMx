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
 * elfinder_browse
 *
 * @package pragmaMx
 * @author tora60
 * @copyright Copyright (c) 2013
 * @version $Id: browse.php 206 2016-09-12 11:33:26Z PragmaMx $
 * @access public
 */
class elfinder_browse {
    private $_config = array();
    private $tpl;

    /**
     * elfinder_browse::__construct()
     *
     * @param mixed $fmconfig
     * @param string $editor
     */
    public function __construct($parent_object)
    {
        $this->_config = $parent_object->get_config();
        $connector = $parent_object->getConnectorUrl();

        $path = __DIR__ . '/manager/';//DS . 'manager' . DS;

        if (_DOC_LANGUAGE != 'de' && !file_exists($path . 'js/i18n/elfinder.' . _DOC_LANGUAGE . '.js')) {
            /* Fallback für fehlende Sprachdatei, deutsch müsste immer da sein */
            $lang = 'de';
            $langdirection = 'ltr';
        } else {
            $lang = _DOC_LANGUAGE;
            $langdirection = _DOC_DIRECTION;
        }

        /* Dateityp bestimmen */
        $typefolders = 'false';
        switch (true) {
            case empty($this->_config['type']): // not set, z.B. elrte
            default:
                $onlymimes = false;
                $rememberlastdir = 'true';
                break;
            case $this->_config['type'] == 'image': // tiny
            case $this->_config['type'] == 'images': // ck
                $onlymimes[] = 'image';
                $rememberlastdir = 'true';
                break;
            case $this->_config['type'] == 'file': // tiny
            case $this->_config['type'] == 'files': // ck
                $onlymimes = false;
                $rememberlastdir = 'false';
                break;
            case $this->_config['type'] == 'media': // tiny
                $onlymimes[] = 'video';
                $onlymimes[] = 'audio';
                $rememberlastdir = 'false';
                break;
            case $this->_config['type'] == 'flash': // ck
                $onlymimes[] = 'application/x-shockwave-flash';
                $rememberlastdir = 'false';
                break;
            case $this->_config['type'] == 'folder':
            case $this->_config['type'] == 'folders':
                $onlymimes = false;
                $rememberlastdir = 'false';
                $typefolders = 'true';
                break;
        }

        switch (true) {
            // wenn anonym oder root angegeben, Ordner nicht merken
            case !(MX_IS_ADMIN || MX_IS_USER):
            case isset($_GET['root']):
                $rememberlastdir = 'false';
        }

        if ($onlymimes) {
            /* in js-array wandeln */
            $onlymimes = '"' . implode('","', $onlymimes) . '"';
        }

        $dateformat = (_DOC_LANGUAGE == 'de') ? 'd.m.Y H:i' : '';

        /* Ordner in root wechseln, damit jQuery Dateien (min version) korrekt gefunden werden  */
        $old = getcwd();
        chdir(PMX_REAL_BASE_DIR);

        /* jQuery Core einbinden */
        pmxHeader::add_jquery(/* jQuery */
            'ui/jquery.ui.draggable.min.js',
            'ui/jquery.ui.droppable.min.js',
            'ui/jquery.ui.resizable.min.js',
            'ui/jquery.ui.selectable.min.js',
            'ui/jquery.ui.sortable.min.js',
            'ui/jquery.ui.button.min.js',
            'ui/jquery.ui.dialog.min.js'
            );

        /* Tabs ohne ui */
        pmxHeader::add_tabs();

        /* elfinder core */
        pmxHeader::add_script($path . 'js/elfinder.min.js');

        /* Sprachdatei */
        pmxHeader::add_script($path . 'js/i18n/elfinder.' . $lang . '.js');

        /* elfinder stylesheet */
        pmxHeader::add_style($path . 'css/elfinder.min.css');

        /* pragmaMx spezifische Anpassungen im elfinder stylesheet */
        pmxHeader::add_style($path . '../templates/style.css');

        /* Ordner wieder zurück */
        chdir($old);

        $this->tpl = load_class('Template');
        $this->tpl->set_path(__DIR__ . DS . 'templates');
        $this->tpl->assign(compact('head', 'lang', 'langdirection', 'connector', 'rememberlastdir', 'onlymimes', 'typefolders', 'manager', 'path', 'dateformat'));
    }

    /**
     * elfinder_browse::getHtml()
     *
     * @param mixed $editor
     * @return
     */
    public function getHtml($editor)
    {
        $this->tpl->assign($this->_config);

        /* browse */
        switch ($editor) {
            case 'ckeditor':
            case 'tinymce':
            case 'standallone':
                /* Header auslesen und an Template übergeben */
                $this->tpl->assign('head', $this->_header());
                return $this->tpl->fetch($editor . '.html');
        }

        /* integrator */
        switch ($editor) {
            case 'manager':
                return $this->tpl->fetch('manager.html');
            case 'elrte':
                $head = $this->tpl->fetch('elrte.html');
                break;
            case 'dialog':
                $head = $this->tpl->fetch('dialog.html');
                break;
            default:
                return 'Error: not accepted value <i>' . $editor . '</i> for $editor!';
        }

        pmxHeader::add_script_code($head);

        return '';
    }

    /**
     * elfinder_browse::_header()
     *
     * @return
     */
    private function _header()
    {
        /* Header auslesen */
        $head = pmxHeader::get();

        /* die unnötigen head-Tags ausfiltern */
        $head = preg_replace('#<[^>]+(backend|lightbox\.js)\.php[^>]+>(</script>)?#i', '', $head);

        /* Pfade anpassen */
        $head = preg_replace('#\s(?:href|src)\s*=\s*["\']#', '$0' . PMX_BASE_PATH, $head);

        return $head;
    }

    /**
     * elfinder_browse::__get()
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
     * elfinder_browse::__set()
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
     * elfinder_browse::set()
     *
     * @param string $name
     * @param mixed $value
     * @return
     */
    public function set($name, $value = null)
    {
        if (is_array($name) && $value === null) {
            foreach ($name as $key => $value) {
                $this->_config[$key] = $value;
            }
        } else {
            $this->_config[$name] = $value;
        }
    }
}

?>