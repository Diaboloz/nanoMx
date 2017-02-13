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

/* Die benötigten Dateien includen: */
load_class('Upload', false);

/**
 * pmxUserpic
 *
 * @package
 * @author tora60
 * @copyright Copyright (c) 2009
 * @version $Id: Userpic.php 6 2015-07-08 07:07:06Z PragmaMx $
 * @access public
 */
class pmxUserpic {
    protected $userdata = array();

    protected $config = array();
    protected $db = null;
    protected $view = null;

    public $errors = array();

    private $_type = '';

    /**
     * pmxUserpic::__construct()
     *
     * @param mixed $userdata
     */
    public function __construct($userdata = null)
    {
        switch (true) {
            case is_null($userdata) && MX_IS_USER:
                $this->userdata = mxGetUserData();
                $this->userdata['is_current'] = true;
                break;
            case is_null($userdata):
            case $userdata === false:
                $this->userdata = false;
                $this->userdata['is_current'] = false;
                break;
            case is_array($userdata) && !isset($userdata['groups']) && isset($userdata['uid']):
                $userdata = $userdata['uid'];
            case is_numeric($userdata):
                $this->userdata = mxGetUserDataFromUid($userdata);
                $this->userdata['is_current'] = $this->_init_currentuser();
                break;
            case is_array($userdata) && !isset($userdata['groups']) && isset($userdata['uname']):
                $userdata = $userdata['uname'];
            case is_string($userdata):
                $this->userdata = mxGetUserDataFromUsername($userdata);
                $this->userdata['is_current'] = $this->_init_currentuser();
                break;
            case is_array($userdata):
                $this->userdata = $userdata;
                $this->userdata['is_current'] = $this->_init_currentuser();
                break;
        }

        /* Konfiguration einlesen */
        $this->config = $this->get_config();

        /* Datenbankfunktionen initialisieren */
        $this->db = new pmxUserpic_model($this->config);
    }

    /**
     * pmxUserpic::get_config()
     *
     * @return
     */
    public function get_config()
    {
        if ($this->config) {
            return $this->config;
        }

        $tmp = load_class('Userconfig');

        $config = array_intersect_key($tmp->get_config(), $this->get_defaults());

        return $config;
    }

    /**
     * pmxUserpic::get()
     *
     * @param string $size
     * @return
     */
    public function get($size = 'small')
    {
        if (empty($this->userdata['user_avatar'])) {
            return false;
        }

        switch ($this->gettype()) {
            case 'nopic':
                return false;
            case 'uploaded':
                $parts = pathinfo($this->userdata['user_avatar']);
                $filename = $this->createfilename($size, $parts['extension']);
                return $parts['dirname'] . '/' . $filename;
            case 'avatar':
            default:
                return $this->userdata['user_avatar'];
        }
    }

    /**
     * pmxUserpic::set_from_file()
     *
     * @param mixed $sourcefile
     * @param mixed $deletesource
     * @return
     */
    public function set_from_file($sourcefile, $deletesource = false)
    {
        // Uploadberechtigung abfragen
        if (!permission_granted($this->access_upload, $this->userdata['groups'])) {
            return false;
        }

        $db_success = false;
        $handle = new pmxUserpic_upload($sourcefile);

        /* falls keine Dateiendung vorhanden */
        $copied = false;
        if (empty($handle->file_src_name_ext) && $handle->file_is_image) {
            $copied = $handle->file_src_pathname . '.' . $handle->image_src_type;
            if (copy($handle->file_src_pathname, $copied)) {
                $handle = new pmxUserpic_upload($copied);
            } else {
                $copied = false;
            }
        }

        $handle->set($this->get_config());
        $handle->handle($this->userdata['uid']);

        if ($handle->errors) {
            $this->errors += $handle->errors;
        }

        /* Wenn keine Fehler aufgetreten sind: */
        if ($handle->success) {
            /* Userbild in Usertabelle einragen */
            $db_success = $this->db->set_foto($this->userdata['uid'], $handle->success);
            if ($db_success) {
                $this->userdata['user_avatar'] = $handle->success;
                /* E-Mail Funktion! */
                $this->adminmail();
            } ;
        }

        if ($copied && file_exists($copied)) {
            unlink($copied);
        }

        if ($deletesource && file_exists($sourcefile)) {
            unlink($sourcefile);
        }

        return $handle->success && $db_success;
    }

    /**
     * pmxUserpic::_createpic()
     *
     * @param mixed $attr
     * @param bolean $pretty
     * @return
     */
    protected function _createpic($attr, $pretty = false)
    {
        if (!isset($attr['src'])) {
            return '';
        }

        if (!isset($attr['alt'])) {
            // alt auf jeden Fall...
            $attr['alt'] = 'userpic';
        }

        if (isset($attr['border'])) {
            $attr['border'] = floatval($attr['border']);
        } else {
            $attr['border'] = 0;
        }

        if (isset($attr['style'])) {
            $styles = explode(';', $attr['style']);
        } else {
            $styles = array();
        }

        /* get the file information */
        if (strpos($attr['src'], '://') === false && is_file($attr['src'])) {
            /* no "://" in the file, so it's local */
            $info = getimagesize($attr['src']);
            /* gegen den Browsercache einen eindeutigen Suffix dranhängen */
            $cachesuffix = '?u' . substr(filemtime($attr['src']), -7, -1);
            $attr['src'] .= $cachesuffix;
        } else {
            /* don't attempt to get file info from streams, it takes way too long. */
            $info = false;
            $cachesuffix = '';
        }

        switch (true) {
            /* TODO: Masseinheit beachten ? */
            case is_array($info) && isset($attr['scale-width']):
                $styles[] = 'width:' . round($attr['scale-width'], 2) . 'px';
                $styles[] = 'height:' . round($info[1] * ($attr['scale-width'] / $info[0]), 2) . 'px';
                break;
            case is_array($info) && isset($attr['scale-height']):
                $styles[] = 'height:' . round($attr['scale-height'], 2) . 'px';
                $styles[] = 'width:' . round($info[0] * ($attr['scale-height'] / $info[1]), 2) . 'px';
                break;
            case is_array($info) && isset($attr['shrink-width']) && ($info[0] > $attr['shrink-width']):
                $styles[] = 'width:' . round($attr['shrink-width'], 2) . 'px';
                $styles[] = 'height:' . round($info[1] * ($attr['shrink-width'] / $info[0]), 2) . 'px';
                break;
            case is_array($info) && isset($attr['shrink-height']) && ($info[1] > $attr['shrink-height']):
                $styles[] = 'height:' . round($attr['shrink-height'], 2) . 'px';
                $styles[] = 'width:' . round($info[0] * ($attr['shrink-height'] / $info[1]), 2) . 'px';
                break;
            default:
                if (isset($attr['width'])) {
                    $styles[] = 'width:' . floatval($attr['width']) . 'px';
                } else {
                    $styles[] = 'width:' . $info[0] . 'px';
                }
                if (isset($attr['height'])) {
                    $styles[] = 'height:' . floatval($attr['height']) . 'px';
                } else {
                    $styles[] = 'height:' . $info[1] . 'px';
                }
        }

        if ($styles) {
            $attr['style'] = implode(';', $styles);
        } else {
            unset($attr['style']);
        }

        $class = '';
        if (!empty($attr['size'])) {
            $class = 'userpic userpic-' . $attr['size'];
        }
        if (!isset($attr['class'])) {
            $attr['class'] = $class;
        } else {
            $attr['class'] .= ' ' . $class;
        }
        $attr['class'] = trim($attr['class']);
        if (!$attr['class']) {
            unset($attr['class']);
        }

        if ($pretty) {
            $full = self::get('full');
            if (is_file($full) && ($fullsize = getimagesize($full)) !== false) {
                if ($fullsize[0] <= $info[0]) {
                    $pretty = false;
                }
            }

            /* Title Attribut für Pretty-Photo zufügen */
            if ($pretty && empty($attr['title'])) {
                $attr['title'] = _CLICKFORFULLSIZE;
            }
        }

        /* unnoetige Attribute entfernen */
        unset($attr['scale-width'], $attr['scale-height'], $attr['shrink-width'], $attr['shrink-height'], $attr['width'], $attr['height'], $attr['size']);

        /* img-Tag generieren */
        $out = '<img';
        foreach ($attr as $key => $value) {
            if (!is_numeric($key)) { // is_numeric, falls ungültige Arrays übergeben werden
                $out .= ' ' . htmlspecialchars($key, ENT_COMPAT | ENT_HTML5, 'UTF-8', false) . '="' . htmlspecialchars($value, ENT_COMPAT | ENT_HTML5, 'UTF-8', false) . '"';
            }
        }
        $out .= ' />';

/*nanomx remove pretty
        if ($pretty) {
            pmxHeader::add_lightbox();
            $out = '<a href="' . $full . $cachesuffix . '" rel="pretty" style="text-decoration: none">' . $out . '</a>';
        }*/

        return $out;
    }

    /**
     * pmxUserpic::getImagesize()
     *
     * @param string $size
     * @param array $attr
     * @return
     */
    public function getImagesize($size = 'small', $attr = array())
    {
        $src_img = self::get($size);

        switch (true) {
            case !$src_img:
            case strpos($src_img, '://') !== false:
            case !is_file($src_img):
                return false;
        }

        /* get the file information */
        $info = getimagesize($src_img);

        switch (true) {
            /* TODO: Masseinheit beachten ? */
            case is_array($info) && isset($attr['scale-width']):
                $info['width'] = round($attr['scale-width'], 2);
                $info['height'] = round($info[1] * ($attr['scale-width'] / $info[0]), 2);
                break;
            case is_array($info) && isset($attr['scale-height']):
                $info['height'] = round($attr['scale-height'], 2);
                $info['width'] = round($info[0] * ($attr['scale-height'] / $info[1]), 2);
                break;
            case is_array($info) && isset($attr['shrink-width']) && ($info[0] > $attr['shrink-width']):
                $info['width'] = round($attr['shrink-width'], 2);
                $info['height'] = round($info[1] * ($attr['shrink-width'] / $info[0]), 2);
                break;
            case is_array($info) && isset($attr['shrink-height']) && ($info[1] > $attr['shrink-height']):
                $info['height'] = round($attr['shrink-height'], 2);
                $info['width'] = round($info[0] * ($attr['shrink-height'] / $info[1]), 2);
                break;
            default:
                if (isset($attr['width'])) {
                    $info['width'] = floatval($attr['width']);
                } else {
                    $info['width'] = $info[0];
                }
                if (isset($attr['height'])) {
                    $info['height'] = floatval($attr['height']);
                } else {
                    $info['height'] = $info[1];
                }
        }
        return $info;
    }

    /**
     * pmxUserpic::getHtml()
     *
     * @param string $size
     * @param array $attr
     * @return
     */
    public function getHtml($size = 'small', $attr = array(), $showdummy = false)
    {
        $dimensions['width'] = $this->config['width_' . $size];
        $dimensions['height'] = $this->config['height_' . $size];

        $src_img = self::get($size);
        if (!$src_img) {
            $type = 'nopic';
        } else {
            $type = $this->gettype();
            switch ($type) {
                case 'uploaded':
                    if (!permission_granted($this->access_upload, $this->userdata['groups'])) {
                        $this->delete_uploaded();
                        $type = 'nopic';
                    }
                    break;
                case 'avatar':
                    if (!permission_granted($this->access_avatars, $this->userdata['groups'])) {
                        if ($this->db->set_foto($this->userdata['uid'], '')) {
                            $this->userdata['user_avatar'] = '';
                        } ;
                        $type = 'nopic';
                    }
                    break;
                default:

            }
        }

        switch ($type) {
            case 'uploaded':
                switch (true) {
                    case $size === 'full':
                        $attr += array(/* HTML-Attribute */
                            'src' => $src_img,
                            'alt' => $this->userdata['uname'],
                            'size' => $size,
                            );
                        return $this->_createpic($attr);
                    case $size === 'mini':
                    case $size === 'small':
                    case $size === 'normal':
                    default:
                        // class="pic border bgcolor3"
                        $attr += array(/* HTML-Attribute */
                            'src' => $src_img,
                            'alt' => $this->userdata['uname'],
                            'size' => $size,
                            );
                        return $this->_createpic($attr, true);
                }
                break;

            case 'avatar':
                $attr += array(/* HTML-Attribute */
                    'src' => $src_img,
                    'alt' => $this->userdata['uname'],
                    'size' => 'avatar',
                    );
                return $this->_createpic($attr);

            case 'nopic':
            default:

                switch (true) {
                    case !$showdummy:
                        $img = PMX_IMAGE_PATH . 'pixel.gif';
                        $attr += array(/* HTML-Attribute */
                            'height' => $dimensions['height'],
                            'width' => $dimensions['width'],
                            );
                        break;

                    case empty($this->userdata['user_sexus']):
                    default:
                        $img = PMX_IMAGE_PATH . 'dummies/dummy.png';
                        break;

                    case 1 === $this->userdata['user_sexus']:
                        $img = PMX_IMAGE_PATH . 'dummies/dummy_female.png';
                        break;

                    case 2 === $this->userdata['user_sexus']:
                        $img = PMX_IMAGE_PATH . 'dummies/dummy_male.png';
                        break;
                }

                $attr += array(/* HTML-Attribute */
                    'src' => $img,
                    'alt' => 'nopic',
                    'size' => 'nopic',
                    );
                return $this->_createpic($attr);
        }
    }

    /**
     * pmxUserpic::__get()
     *
     * @param mixed $name
     * @return
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->config)) {
            return $this->config[$name];
        }
        $trace = debug_backtrace();
        trigger_error('undefined property \'' . $name . '\' in ' . mx_strip_sysdirs($trace[0]['file']) . ' line ' . $trace[0]['line'], E_USER_NOTICE);
        return null;
    }

    /**
     * pmxUserpic::createfilename()
     *
     * @param string $size
     * @param string $extension
     * @return string $filename or boolean false
     */
    public function createfilename($size = 'small', $extension = '')
    {
        switch ($size) {
            case 'all_per_user':
                /* ohne Suffix und Endung, zur Suche aller Dateien eines Users */
                $size = '';
                $extension = '';
                break;
            case 'normal':
            case 'medium':/* optional */
                $size = $this->suffix_normal;
                break;
            case 'full':
            case 'big':/* optional */
                $size = $this->suffix_big;
                break;
            case 'mini':
                $size = $this->suffix_mini;
                break;
            case 'small':
            case 'thumb':/* optional */
            default:
                $size = $this->suffix_small;
                break;
        }

        $filename = intval($this->userdata['uid']) . '_' . $size;
        if ($extension) {
            $filename .= '.' . $extension;
        }
        return $filename;
    }

    /**
     * pmxUserpic::exist()
     *
     * @param string $size
     * @return
     */
    public function exist($size = 'small')
    {
        /* Dateiname erstellen, ohne Endung */
        $filename = $this->path_upload . '/' . $this->createfilename($size) . '.';
        /* Verzeichnis auslesen, nach dem Dateinamen */
        $find = (array)glob($this->path_upload . DS . $this->createfilename($size) . '.*', GLOB_NOSORT);

        /* das zuletzt gefundene Bild verwenden */
        $filename = str_replace(DS, '/', array_pop($find));
        /* wenn noch weitere Bilder des gleichen Benutzers vorhanden */
        if ($find) {
            /* diese löschen.*/
            foreach ($find as $xfile) {
                if (is_file($xfile)) {
                    unlink($xfile);
                }
            }
        }
        /* wenn Bild vorhanden, dies zurückgeben */
        if ($filename) {
            return $filename;
        }
        return false;
    }

    /**
     * pmxUserpic::gettype()
     *
     * @return
     */
    public function gettype()
    {
        if ($this->_type) {
            return $this->_type;
        }

        if (empty($this->userdata['user_avatar'])) {
            $this->_type = 'nopic';
            return $this->_type;
        }

        $path_parts = pathinfo($this->userdata['user_avatar']);

        switch (true) {
            case empty($path_parts['filename']):
            case empty($path_parts['dirname']):
            case strpos($path_parts['dirname'], ':') !== false:
                $this->_type = 'nopic';
                break;
            case is_file($this->path_avatars . DS . $path_parts['basename']):
                if ('.' == $path_parts['dirname']) {
                    // alte Schreibweise, ohne Pfad, vor 1.12
                    $this->userdata['user_avatar'] = $this->path_avatars . '/' . $path_parts['basename'];
                    // konvertieren ;-)
                    $this->db->set_foto($this->userdata['uid'], $this->userdata['user_avatar']);
                }
                $this->_type = 'avatar';
                break;
            case $this->exist():
                // case $this->exist() && is_file($this->path_upload . DS . $path_parts['basename']):
                $this->_type = 'uploaded';
                break;
            default:
                $this->_type = 'nopic';
                break;
        }

        return $this->_type;
    }

    /**
     * pmxUserpic::is_nopic()
     *
     * @return
     */
    public function is_nopic()
    {
        return $this->gettype() === 'nopic';
    }

    /**
     * pmxUserpic::is_avatar()
     *
     * @return
     */
    public function is_avatar()
    {
        return $this->gettype() === 'avatar';
    }

    /**
     * pmxUserpic::is_uploaded()
     *
     * @return
     */
    public function is_uploaded()
    {
        return $this->gettype() === 'uploaded';
    }

    /**
     * pmxUserpic::get_defaults()
     *
     * @return
     */
    public function get_defaults()
    {
        return array(// Standardwerte
            'file_maxsize' => 1024,

            'path_avatars' => PMX_IMAGE_PATH . 'forum/avatar',
            'access_avatars' => array('-3' => 'on', '-2' => 'on', '-1' => 'deny', '0' => 'on'),
            'path_upload' => 'media/userpics',
            'access_upload' => array('-3' => 'on', '-2' => 'on', '-1' => 'deny', '0' => 'off'),
            'width_mini' => 40,
            'height_mini' => 30,
            'width_small' => 100,
            'height_small' => 80,
            'width_normal' => 170,
            'height_normal' => 220,
            'width_full' => 640,
            'height_full' => 480,

            'mail_notice' => 0,
            'mail_address' => $GLOBALS['adminmail'],

            'endings' => array('jpg', 'jpeg', 'gif', 'png'),
            // 'sessionvar' => 'avm', // Name der Sessionvariable
            'suffix_normal' => 'normal', // suffix fuer Thumbnail Dateinamen
            'suffix_mini' => 'mini', // suffix fuer Thumbnail Dateinamen
            'suffix_small' => 'small', // suffix fuer Thumbnail Dateinamen
            'suffix_big' => 'full', // suffix fuer Thumbnail Dateinamen
            // 'url_avatars' => PMX_HOME_URL . '/images/forum/avatar',
            // 'url_upload' => PMX_HOME_URL . '/media/userpics',
            );
    }

    /**
     * pmxUserpic::check_config()
     *
     * @param mixed $conf
     * @return
     */
    public function check_config(&$conf)
    {
        $defaults = self::get_defaults();

        /* Werte die nicht in $conf enthalten sind, aus der vorhandenen Konfiguration lesen
         * dies ermöglicht nicht zugängliche Optionen in der Konfiguration zu ändern
         * z.B. $endings
         */
        $tmp = array_diff_key($defaults, $conf);
        if ($tmp) {
            foreach ($tmp as $key => $value) {
                if (isset($this->config[$key])) {
                    $conf[$key] = $this->config[$key];
                }
            }
        }

        $conf = array_merge($defaults, $conf);
        $perms = array('access_avatars', 'access_upload');
        foreach ($perms as $key => $perm) {
            ksort($conf[$perm]);
        }

        if ($conf['file_maxsize'] < 0) {
            $conf['file_maxsize'] = $conf['file_maxsize'] * -1;
        }

        $max = self::get_upload_max_filesize();
        if ($conf['file_maxsize'] > $max) {
            $conf['file_maxsize'] = $max;
        }

        /* Dimensionen checken */
        $chk = array('width_full', 'height_full', 'width_normal', 'height_normal', 'width_small', 'height_small', 'width_mini', 'height_mini');
        foreach ($chk as $key) {
            if ($conf[$key] < 1) {
                $conf[$key] = $defaults[$key];
            }
        }

        /* pruefen der mailadresse, bei fehler deaktivieren */
        switch (true) {
            case $conf['mail_notice'] && empty($conf['mail_address']):
                $conf['mail_address'] = $defaults['mail_address'];
                break;
            case $conf['mail_notice'] && !mxCheckEmail($conf['mail_address']):
            case $conf['mail_notice'] && pmx_is_mail_banned($conf['mail_address']):
                $conf['mail_notice'] = 0;
                $conf['mail_address'] = '';
                break;
        }

        /* gültiger Avatarpfad */
        $conf['path_avatars'] = str_replace(array(DS . DS, DS), '/', trim($conf['path_avatars'], DS . '/ .'));
        if (!$conf['path_avatars'] || !self::get_available_avatars($conf['path_avatars'])) {
            $conf['path_avatars'] = $defaults['path_avatars'];
        }

        /* gültiger Uploadpfad */
        $conf['path_upload'] = str_replace(array(DS . DS, DS), '/', trim($conf['path_upload'], DS . '/ .'));
        if (!$conf['path_upload'] || !file_exists($conf['path_upload'])) {
            $conf['path_upload'] = $defaults['path_upload'];
        }
    }

    /**
     * pmxUserpic::get_available_avatars()
     *
     * @param mixed $path
     * @return
     */
    public function get_available_avatars($path = false)
    {
        if ($path === false) {
            $path = $this->path_avatars;
        }
        $endings = array('gif', 'png', 'jpg', 'jpeg');
        $filelist = array();
        $tmp = (array)glob(str_replace(DS, '/', $path . '/*'));
        foreach ($tmp as $image) {
            $info = pathinfo($image);
            if (isset($info['extension']) && in_array(strtolower($info['extension']), $endings)) {
                $filelist[$info['basename']] = $image;
            }
        }
        natcasesort($filelist);
        return $filelist;
    }

    /**
     * pmxUserpic::get_upload_max_filesize()
     * Versuchen die upload_max_filesize aus der php.ini auszuwerten
     *
     * @return integer
     */
    public function get_upload_max_filesize()
    {
        $upload_max_filesize = 0;
        $upload_max = ini_get('upload_max_filesize');
        if (!is_double($upload_max)) {
            preg_match('#([0-9]+)([A-Z])#i', $upload_max, $matches);
            switch (strtoupper($matches[2])) {
                case 'G':/* Gigabyte */
                    $upload_max_filesize = $matches[1] * 1024 * 1024 * 1024;
                    break;
                case 'M':/* Megabyte */
                    $upload_max_filesize = $matches[1] * 1024 * 1024;
                    break;
                case 'K':/* Kilobyte */
                    $upload_max_filesize = $matches[1] * 1024;
                    break;
                case 'B':/* Byte */
                    $upload_max_filesize = $matches[1];
                    break;
            }
        }
        return $upload_max_filesize;
    }

    /**
     * pmxUserpic::delete_uploaded()
     *
     * @return
     */
    public function delete_uploaded()
    {
        /* wenn das aktuelle Userbild ist, dieses aus Usertabelle entfernen */
        if ($this->is_uploaded()) {
            if ($this->db->set_foto($this->userdata['uid'], '')) {
                $this->userdata['user_avatar'] = '';
            } ;
        }

        /* Dateiname erstellen, ohne Endung und suffix */
        $filenames = $this->path_upload . DS . $this->createfilename('all_per_user') . '*.*';

        /* Verzeichnis auslesen, nach dem Dateinamen */
        $allfiles = (array)glob($filenames, GLOB_NOSORT);

        /* wenn Bilder des Benutzers vorhanden, diese löschen.*/
        foreach ($allfiles as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        /* Verzeichnis nochmal auslesen, nach dem Dateinamen */
        $allfiles = (array)glob($filenames, GLOB_NOSORT);

        /* keine mehr vorhanden, bzw. array leer >> true */
        return count(array_filter($allfiles)) === 0;
    }

    /**
     * pmxUserpic::is_own_image()
     *
     * @return
     */
    public function is_own_image()
    {
        return (bool)$this->userdata['is_current'];
    }

    /**
     * pmxUserpic::_init_currentuser()
     *
     * @return
     */
    private function _init_currentuser()
    {
        if (empty($this->userdata['uid'])) {
            return false;
        }

        $current = mxGetUserData();

        if (empty($current['uid'])) {
            return false;
        }

        return $current['uid'] == $this->userdata['uid'];
    }

    /**
     * pmxUserpic::adminmail()
     *
     * @return
     */
    protected function adminmail()
    {
        /* E-Mail Funktion! */
        if ($this->mail_notice) {
            /* Sprachdatei laden */
            mxGetLangfile('Your_Account');
            $message = sprintf(_UPIC_MAILMESSAGE, $this->userdata['uname'], PMX_HOME_URL . '/modules.php?name=Userinfo&uid=' . $this->userdata['uid']);
            if (!mxMail($this->mail_address, _UPIC_MAILSUBJECT, $message)) {
                $this->errors[] = _UPIC_UPLOAD5;
                return false;
            }
        }
        return true;
    }
}

/**
 * pmxUserpic_model
 *
 * @package
 * @author tora60
 * @copyright Copyright (c) 2009
 * @version $Id: Userpic.php 6 2015-07-08 07:07:06Z PragmaMx $
 * @access public
 */
class pmxUserpic_model extends pmxUserpic {
    /* die pragmaMx Userabelle */
    protected $_table_user;

    /* die importierte Konfiguration des übergeordneten Objektes */
    protected $config = array();

    /**
     * pmxUserpic_model::__construct()
     *
     * @param mixed $config
     */
    public function __construct($config)
    {
        global $user_prefix;
        $this->_table_user = $user_prefix . '_users';
    }

    /**
     * pmxUserpic_model::set_foto()
     * Usertabelle aktualisieren
     *
     * @param mixed $uid
     * @param mixed $filename
     * @return
     */
    public function set_foto($uid, $filename)
    {
        $qry = "UPDATE `{$this->_table_user}`
            SET `user_avatar`='" . mxAddSlashesForSQL($filename) . "'
            WHERE `uid`=" . intval($uid);
        return sql_query($qry);
    }
}

/**
 * avmod_upload
 *
 * @package
 * @author tora60
 * @copyright Copyright (c) 2009
 * @version $Id: Userpic.php 6 2015-07-08 07:07:06Z PragmaMx $
 * @access public
 */
class pmxUserpic_upload extends pmxUpload {
    public $success = false;
    public $errors = array();
    public $logs = array();
    /**
     * avmod_upload::__construct()
     *
     * @param mixed $file
     */
    public function __construct($file)
    {
        /* Konstruktor der Elternklasse aufrufen */
        parent::__construct($file);
    }

    /**
     * pmxUserpic_upload::handle()
     *
     * @param mixed $filename
     * @return
     */
    public function handle($filename)
    {
        // $filename ist die user-ID (uid)
        /* then we check if the file has been uploaded properly
            in its *temporary* location in the server (often, it is /tmp) */
        if (!$this->uploaded) {
            // if we're here, the upload file failed for some reasons
            // i.e. the server didn't receive the file
            $this->errors[] = $this->error;
            return false;
        }
        // now, we start the upload 'process'. That is, to copy the uploaded file
        // from its temporary location to the wanted location
        // It could be something like $this->process('/home/www/my_uploads/');
        $this->_init_process('small', $filename);
        $this->process($this->path_upload);
        // $this->logs[] = $this->log;
        // we check if everything went OK
        if (!$this->processed) {
            // one error occured
            $this->errors[] = $this->error;
            return false;
        }
        $hold[] = $this->file_dst_pathname;
        // everything was fine !
        $this->success = str_replace(DS, '/', $this->file_dst_pathname);

        /* andere Grössen fuer Fotos generieren */
        $this->_init_process('mini', $filename);
        $this->process($this->path_upload);
        // $this->logs[] = $this->log;
        $hold[] = $this->file_dst_pathname;
        if ($this->error) {
            $this->errors[] = $this->error;
        }

        $this->_init_process('normal', $filename);
        $this->process($this->path_upload);
        // $this->logs[] = $this->log;
        $hold[] = $this->file_dst_pathname;
        if ($this->error) {
            $this->errors[] = $this->error;
        }

        $this->_init_process('full', $filename);
        $this->process($this->path_upload);
        // $this->logs[] = $this->log;
        $hold[] = $this->file_dst_pathname;
        if ($this->error) {
            $this->errors[] = $this->error;
        }

        /* evtl. verbliebene Dateien mit anderer Dateiendung > löschen */
        $find = (array)glob($this->path_upload . DS . $filename . '_*.*');
        $result = array_diff($find, $hold);
        foreach ($result as $value) {
            unlink($value);
        }

        return true;
    }

    /**
     * avmod_upload::_init_process()
     *
     * @param mixed $size
     * @param mixed $filename
     * @return
     */
    private function _init_process($size, $filename)
    {
        /* Seitenverhältnis beibehalten */
        $this->image_ratio = true;
        $this->image_ratio_no_zoom_in = true;
        /* wenn max-size == 0, Standardwert der Klasse (php.ini) verwenden */
        if ($this->file_maxsize) {
            $this->file_max_size = intval($this->file_maxsize); // byte
        }
        /* nur Bilder zulassen */
        $this->allowed = array('image/*');
        /* Punkte im Dateinamen ermoeglichen */
        $this->file_safe_name = true;
        /* automatisches umbenennen unterdruecken */
        $this->file_auto_rename = false;
        /* existierende Dateien einfach ueberschreiben */
        $this->file_overwrite = true;
        /* Dateiendung ergibt sich aus Originaldateinamen, nur Kleinbuchstaben in der Endung */
        $ending = strtolower($this->file_src_name_ext);
        /* keine Dateiendung vorhanden */
        if (!$ending && $this->file_src_mime && $this->image_src_type) {
            /* versuchen die Endung aus dem mime-Typ zu ermitteln */
            $ending = strtolower($this->image_src_type);
        }
        if (!in_array($ending, $this->endings)) {
            /* Dateiendung und Konvertierung, alle nur gif, jpg oder png */
            $ending = strtolower($this->endings[0]);
            $this->image_convert = $ending;
        }

        /* Bilder erstmal nicht automatisch in der Grösse anpassen */
        $this->image_resize = false;

        switch ($size) {
            case 'full':
                $filename .= '_' . $this->suffix_big;
                if ($this->image_src_x > $this->height_full || $this->image_src_y > $this->width_full) {
                    $this->image_resize = true;
                    $this->image_y = intval($this->height_full);
                    $this->image_x = intval($this->width_full);
                }
                break;
            case 'normal':
                $filename .= '_' . $this->suffix_normal;
                if ($this->image_src_x > $this->height_normal || $this->image_src_y > $this->width_normal) {
                    $this->image_resize = true;
                    $this->image_y = intval($this->height_normal);
                    $this->image_x = intval($this->width_normal);
                }
                break;
            case 'mini':
                $filename .= '_' . $this->suffix_mini;
                if ($this->image_src_x > $this->height_mini || $this->image_src_y > $this->width_mini) {
                    $this->image_resize = true;
                    $this->image_y = intval($this->height_mini);
                    $this->image_x = intval($this->width_mini);
                }
                break;
            case 'small':
            default:
                $filename .= '_' . $this->suffix_small;
                if ($this->image_src_x > $this->height_small || $this->image_src_y > $this->width_small) {
                    $this->image_resize = true;
                    $this->image_y = intval($this->height_small);
                    $this->image_x = intval($this->width_small);
                }
                break;
        }

        $this->file_new_name_body = $filename;
        $this->file_new_name_ext = $ending;
    }
}

?>