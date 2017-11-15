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

/* Direktaufruf möglich, da nicht nur innerhalb von pragmaMx genutzt !! */

class pmxTextarea {
    protected $area = array(/* Standardwerte initialisieren */
        'name' => 'editor',
        'value' => '',
        'mode' => '',
        'height' => '350',
        'width' => '100%',
        'pagebreak' => false,
        'infotext' => '',
        'wysiwyg' => true,
        'lang' => 'en',
        'langdirection' => 'ltr',
        'stylesheet' => '',
        );

    private static $_config = array();
    private static $_users_config = array();

    private static $_systemdir, $_editordir, $_classdir;

    protected $editor;

    /* Fallback, falls wysiwyg deaktiviert, oder nicht verfügbar ist */
    const fallback = '_textarea';

    /**
     * pmxTextarea::__construct()
     * Der Constructor der Klasse
     *
     * @param string $name
     * @param string $value
     * @param string $mode
     * @param string $height
     * @param string $width
     * @param bol $pagebreak
     * @param string $infotext
     * @param bol $wysiwyg
     */
    public function __construct($name = '', $value = '', $mode = '', $height = '', $width = '', $pagebreak = false, $infotext = '', $wysiwyg = true)
    {
        $this->area['lang'] = (defined('_DOC_LANGUAGE')) ? _DOC_LANGUAGE : 'en';
        $this->area['langdirection'] = (defined('_DOC_DIRECTION')) ? _DOC_DIRECTION : 'ltr';

        switch (true) {
            case is_array($name) && isset($name[0]):
                // numerisches Array
                $argnames = array('name', 'value', 'mode', 'height', 'width', 'pagebreak', 'infotext', 'wysiwyg');
                foreach ($name as $key => $value) {
                    $settings[$argnames[$key]] = $value;
                }
                break;

            case is_array($name):
                // nicht numerisches Array
                $settings = $name;
                break;

            default:
                // ganz normale Funktionsparameter
                $settings = compact('name', 'value', 'mode', 'height', 'width', 'pagebreak', 'infotext', 'wysiwyg');
        }

        /* mit den übergebenen Werten (leere entfernen) die Standardwerte überschreiben */
        $this->area = array_merge($this->area, array_filter($settings));
    }

    /**
     * pmxTextarea::get()
     * gibt aktuellen Einstellwerte zurück
     *
     * @return
     */
    public function get()
    {
        return $this->area;
    }

    /**
     * pmxTextarea::getHtml()
     *
     * Der komplett generierte HTML-Output der aufgerufenen Klasse
     * Es wird nicht nur die eigentliche Textarea erstellt, sondern auch die
     * zugehörigen Einstellungen und Erweiterungen im HTML-Headbereich der
     * Seite erzeugt.
     *
     * @return
     */
    public function getHtml($settings = array())
    {
        /* mit den übergebenen Parametern die aktuellen Werte überschreiben */
        $this->area = array_merge($this->area, array_intersect_key($settings, $this->area));

        if ($this->pagebreak) {
            $this->infotext .= PHP_EOL . PHP_EOL . _PAGEBREAK;
        }

        /* Toolbar (mode) einstellen, ggf. auf Usereinstellung zurücksetzen */
        $this->setMode($this->mode);

        $this->set_editor();

        $tmp = 'pmx_editor_' . $this->editor;
        $editor_object = new $tmp($this->area);

        $out = trim($editor_object->getHtml());

        /* der Zielseite mitteilen ob wysiwyg oder _textarea... */
        $out .= "\n" . '<input type="hidden" name="wysiwyg_' . $this->name . '" value="' . intval($this->editor != self::fallback) . '" />' . "\n";

        /* diese Werte nach jeder Ausgabe zurücksetzen */
        $this->pagebreak = false;
        $this->infotext = '';

        return $out;
    }

    /**
     * pmxTextarea::show()
     * outputs wysiwyg control
     *
     * @return string (html)
     */
    public function show($settings = array())
    {
        echo $this->getHtml($settings);
    }

    /**
     * pmxTextarea::setMode()
     * This property holds the toolbar mode of the editor.
     *
     * @param string $mode
     * @return nothing
     */
    public function setMode($mode = '')
    {
        $modes = self::get_mode_levels();
        $config = self::get_users_config();

        switch ($mode) {
            case 'normal':
            case 'standard':
            case '2':
                $level = 2;
                break;
            case 'full':
            case 'complete':
            case 'all':
            case '3':
                $level = 3;
                break;
            case 'reduced':
            case 'small':
            case 'mini':
            case '1':
                $level = 1;
                break;
            case 'default':
            default:
                $level = $config['mode'];
                break;
        }

        /* jetzt guggen ob modus > als erlaubt und ggf zurückstellen... */
        if ($level > $config['mode']) {
            $level = $config['mode'];
        }

        $this->mode = $modes[$level];
    }

    /**
     * pmxTextarea::getMode()
     * This property holds the toolbar mode of the editor.
     *
     * @return string
     */
    public function getMode()
    {
        return($this->mode);
    }

    /**
     * pmxTextarea::set_editor()
     *
     * @param string $editor
     * @return nothing
     */
    protected function set_editor($editor = '')
    {
        $config = self::get_users_config();

        if ($editor) {
            $this->editor = $editor;
        } else {
            $this->editor = $config['editor'];
        }

        switch (true) {
            case !$this->wysiwyg:
            case !$this->editor:
            case $this->editor === self::fallback:
                $this->editor = self::fallback;
                break;
            case !($file = realpath(dirname(__DIR__) . DS . 'wysiwyg' . DS . $this->editor . '/editor.class.php')):
            case !include_once($file):
            case !class_exists('pmx_editor_' . $this->editor):
                trigger_error('Editor "' . $this->editor . '" is not available!', E_USER_WARNING);
                $this->editor = self::fallback;
                break;
        }

        if ($this->editor === self::fallback) {
            $file = realpath(dirname(__DIR__) . DS . 'wysiwyg' . DS . self::fallback . '/editor.class.php');
            include_once($file);
            $this->editor = self::fallback;
            $this->wysiwyg = false;
        }
    }

    /**
     * pmxTextarea::setName()
     * This property holds the name and ID of the editor.
     *
     * @param mixed $value
     * @return
     */
    public function setName($value)
    {
        $this->name = $value;
    }

    /**
     * pmxTextarea::getName()
     * This property holds the name and ID of the editor.
     *
     * @return
     */
    public function getName()
    {
        return($this->name);
    }

    /**
     * pmxTextarea::setValue()
     * This property holds the content of the editor.
     *
     * @param mixed $value
     * @return
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * pmxTextarea::getValue()
     * This property holds the content of the editor.
     *
     * @return
     */
    public function getValue()
    {
        return($this->value);
    }

    /**
     * pmxTextarea::setHeight()
     * set height/get height
     *
     * @param mixed $value
     * @return
     */
    public function setHeight($value)
    {
        $this->height = $value;
    }

    /**
     * pmxTextarea::getHeight()
     * set height/get height
     *
     * @return
     */
    public function getHeight()
    {
        return($this->height);
    }

    /**
     * pmxTextarea::setWidth()
     * set/get width
     *
     * @param mixed $value
     * @return
     */
    public function setWidth($value)
    {
        $this->width = $value;
    }

    /**
     * pmxTextarea::getWidth()
     * set/get width
     *
     * @return
     */
    public function getWidth()
    {
        return($this->width);
    }

    /**
     * pmxTextarea::setDimensions()
     * Sets editor dimensions
     *
     * @param mixed $width
     * @param mixed $height
     * @return
     */
    public function setDimensions($width, $height)
    {
        if ($width != null && $width != '') {
            $this->setWidth($width);
        }
        if ($height != null && $height != '') {
            $this->setHeight($height);
        }
    }

    /**
     * pmxTextarea::setPagebreak()
     * set/get pagebreak
     *
     * @param mixed $value
     * @return
     */
    public function setPagebreak($value)
    {
        $this->pagebreak = ($value) ? true : false;
    }

    /**
     * pmxTextarea::getPagebreak()
     * set/get pagebreak
     *
     * @return
     */
    public function getPagebreak()
    {
        return($this->pagebreak);
    }

    /**
     * pmxTextarea::setInfotext()
     * set/get infotext
     *
     * @param mixed $value
     * @return
     */
    public function setInfotext($value)
    {
        $this->infotext = $value;
    }

    /**
     * pmxTextarea::getInfotext()
     * set/get infotext
     *
     * @return
     */
    public function getInfotext()
    {
        return($this->infotext);
    }

    /**
     * pmxTextarea::setWysiwyg()
     * set/get wysiwyg
     *
     * @param mixed $value
     * @return
     */
    public function setWysiwyg($value)
    {
        $this->wysiwyg = $value;
        if ($value) {
            $config = self::get_users_config();
            $this->set_editor($config['editor']);
        }
    }

    /**
     * pmxTextarea::getWysiwyg()
     * set/get wysiwyg
     *
     * @return
     */
    public function getWysiwyg()
    {
        if (!$this->editor) {
            $this->set_editor();
        }

        return($this->wysiwyg);
    }

    /**
     * pmxTextarea::is_wysiwyg()
     * alias from getWysiwyg()
     *
     * @return
     */
    public function is_wysiwyg()
    {
        return $this->getWysiwyg();
    }

    /**
     * pmxTextarea::__get()
     *
     * @param mixed $name
     * @return
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->area)) {
            return $this->area[$name];
        }
        $trace = debug_backtrace();
        trigger_error('undefined property \'' . $name . '\' in ' . mx_strip_sysdirs($trace[0]['file']) . ' line ' . $trace[0]['line'], E_USER_NOTICE);
        return null;
    }

    /**
     * pmxTextarea::__set()
     *
     * @param mixed $name
     * @param mixed $value
     * @return
     */
    public function __set($name, $value)
    {
        $this->area[$name] = $value;
    }

    /**
     * pmxTextarea::get_mode_levels()
     *
     * @return array
     */
    public static function get_mode_levels()
    {
        return array(/*all core toolbars*/
            1 => 'reduced',
            2 => 'normal',
            3 => 'full',
            );
    }

    /**
     * pmxTextarea::get_filetypegroups()
     * Dateityp-Gruppen, analog zu den Sprachdateien des Spaw-Editors
     *
     * @return
     */
    public static function get_filetypegroups()
    {
        // TODO: Auslagern in eine konfigurierbare ini Datei
        $filetypegroups = array(// Dateitypen...
            'images' => array(/* images */
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'gif' => 'image/gif',
                'png' => 'image/png',
                // 'tif' => 'image/tiff',
                // 'tiff' => 'image/tiff',
                // 'tga' => 'image/x-targa',
                // 'psd' => 'image/vnd.adobe.photoshop',
                'bmp' => 'image/x-ms-bmp',
                ),
            'video' => array(/* video */
                'avi' => 'video/x-msvideo',
                'mp4' => 'video/mp4',
                'mpeg' => 'video/mpeg',
                'mpg' => 'video/mpeg',
                'mov' => 'video/quicktime',
                'wmv' => 'video/x-ms-wmv',
                'swf' => 'application/x-shockwave-flash',
                'mkv' => 'video/x-matroska',
                // 'flv' => 'video/x-flv',
                // 'dv' => 'video/x-dv',
                ),
            'audio' => array(/* audio */
                'mp3' => 'audio/mpeg',
                'mp4' => 'audio/mp4',
                'mid' => 'audio/midi',
                'wav' => 'audio/wav',
                'ogg' => 'audio/ogg',
                'wma' => 'audio/x-ms-wma',
                ),
            'documents' => array(/* documents */
                'txt' => 'text/plain',
                'pdf' => 'application/pdf',
                'rtf' => 'text/rtf',
                // 'rtfd' => 'text/rtfd',
                // 'sxw'=>'application/vnd.sun.xml.writer',
                // 'sxc'=>'application/vnd.sun.xml.calc',
                'doc' => 'application/vnd.ms-word',
                'xls' => 'application/vnd.ms-excel',
                'ppt' => 'application/vnd.ms-powerpoint',
                'pps' => 'application/vnd.ms-powerpoint',
                'odt' => 'application/vnd.oasis.opendocument.text',
                'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
                ),
            'archives' => array(/* archives */
                'gz' => 'application/x-gzip',
                'tgz' => 'application/x-gzip',
                'bz' => 'application/x-bzip2',
                // 'bz2' => 'application/x-bzip2',
                // 'tbz' => 'application/x-bzip2',
                'zip' => 'application/zip',
                'rar' => 'application/x-rar',
                'tar' => 'application/x-tar',
                '7z' => 'application/x-7z-compressed',
                ),
            );

        return $filetypegroups;
    }

    /**
     * pmxTextarea::get_config()
     *
     * @return
     */
    public static function get_config()
    {
        if (self::$_config !== array()) {
            return self::$_config;
        }

        $conf_file = __DIR__ . DS . 'Textarea' . DS . 'config.inc.php';
        if (file_exists($conf_file)) include($conf_file);
        $wyscnf['file'] = $conf_file;

        /* Konfiguration mit Standardwerten zusammenführen / ergänzen */
        $def = self::_defaults();
        foreach ($def as $key => $value) {
            if (isset($wyscnf[$key])) {
                $wyscnf[$key] = array_merge($value, $wyscnf[$key]);
            } else {
                $wyscnf[$key] = $value;
            }
        }

        /* Sicherstellen, dass das richtig belegt ist */
        $wyscnf['globals']['chmod_to'] = (empty($wyscnf['globals']['chmod_to']) || !is_numeric($wyscnf['globals']['chmod_to'])) ? '' : intval($wyscnf['globals']['chmod_to']);
        $wyscnf['globals']['chmod_dir_to'] = (empty($wyscnf['globals']['chmod_dir_to']) || !is_numeric($wyscnf['globals']['chmod_dir_to'])) ? '' : intval($wyscnf['globals']['chmod_dir_to']);

        self::$_config = $wyscnf;
        return self::$_config;
    }

    /**
     * pmxTextarea::get_users_config()
     * Die für die entsprechende Usergruppe festgelegte Konfiguration ermitteln
     *
     * @return
     */
    public static function get_users_config()
    {
		
        if (self::$_users_config !== array()) {
            return self::$_users_config;
        }
		
        $wyscnf = self::get_config();

        switch (true) {
            case !defined('PMX'):
                // irgendwas faul, mainfile fehlt...
                return false;

            case MX_IS_ADMIN:
                $userconf = $wyscnf['admin'];
                break;

            case MX_IS_USER;
                $userdata = mxGetUserData();

                /* Feststellen, ob der User, in einer der speziell angegebenen Gruppen ist */
                if (empty($wyscnf['globals']['pmxgroups'][$userdata['user_ingroup']])) {
                    $userconf = $wyscnf['other'];
                    break;
                }

                $userconf = $wyscnf['user'];

                /* Benutzerordner initialisieren, mit uid hintendran */
                $userconf['roots'] = array_slice($userconf['roots'], 0, 1);
                // nur der erste, falls mehrere
                foreach ($userconf['roots'] as $alias => $root) {
                    $root = rtrim($root, ' /' . DS) . '/' . $userdata['uid'];
                    $ok = file_exists($root);
                    if (!$ok) {
                        if ($wyscnf['globals']['chmod_dir_to']) {
                            $ok = mkdir($root, octdec($wyscnf['globals']['chmod_dir_to']), true);
                        } else {
                            $ok = mkdir($root, PMX_CHMOD_FULLUNOCK , true);
                        }
                    }
                    if ($ok) {
                        $userconf['roots'][$alias] = $root;
                    }
                    $userconf['roots'][$alias] = $root;
                }
                break;

            default:
                // Anonyme und User, die nicht in der Gruppe sind
                $userconf = $wyscnf['other'];
        }

        /* die globalen Einstellungen anfügen */
        $userconf = array_merge($userconf, $wyscnf['globals']);

        settype($userconf['allow_upload'], 'integer');
        settype($userconf['upload_max_size'], 'integer');
        settype($userconf['allow_modify'], 'integer');
        settype($userconf['max_img_width'], 'integer');
        settype($userconf['max_img_height'], 'integer');

        switch (true) {
            case MX_IS_ADMIN :	//&& mxGetAdminPref('radminsuper')
                $userconf['allow_upload'] = 1;
                $userconf['filetype'] = array('any');
                $userconf['allow_modify'] = 1;
                $userconf['upload_max_size'] = -1;
                $userconf['max_img_width'] = -1;
                $userconf['max_img_height'] = -1;
				$userconf['manager'] = "elfinder";
                break;

            default:
                /* feststellen, ob upload allowed, das ist der Fall, wenn min. 1 Dateityp erlaubt ist */
                $types = array();
                foreach ($userconf['filetype'] as $key => $value) {
                    if ($value) {
                        $types[] = $key;
                        $userconf['allow_upload'] = 1;
                    }
                }

                switch (true) {
                    case !$userconf['allow_upload']:
                    case $userconf['upload_max_size'] === 0:
                        // nicht erlaubt, allow_upload abschalten !!
                        $userconf['allow_upload'] = 0;
                        $userconf['filetype'] = array();
                        $userconf['allow_modify'] = 0;
                        $userconf['upload_max_size'] = 0;
                        $userconf['max_img_width'] = 0;
                        $userconf['max_img_height'] = 0;
                        break;
                    default:
                        $userconf['allow_upload'] = 1;
                        $userconf['filetype'] = $types;
                        $userconf['max_img_width'] = ($userconf['max_img_width'] < 0) ? -1 : intval($userconf['max_img_width']);
                        $userconf['max_img_height'] = ($userconf['max_img_height'] < 0) ? -1 : intval($userconf['max_img_height']);
                }
        }

        $maxsize = Textarea::get_max_uploadsize();
        if (($userconf['upload_max_size'] * 1024) > $maxsize || $userconf['upload_max_size'] < 0) {
            // in Bytes !!
            $userconf['upload_max_size'] = $maxsize;
        }

        self::$_users_config = $userconf;
		
        return self::$_users_config;
    }

    /**
     * pmxTextarea::get_max_uploadsize()
     * Versuchen die upload_max_filesize aus der php.ini auszuwerten
     *
     * @return integer > Bytes
     */
    public static function get_max_uploadsize()
    {
        $upload_max_filesize = 0;
        $upload_max = ini_get('upload_max_filesize');
        if (!($upload_max) && !is_null($upload_max) && !is_double($upload_max)) {
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
     * pmxTextarea::get_available_editors()
     *
     * @return array
     */
    public static function get_available_editors()
    {
        static $editors = array();
        if (!$editors) {
            $files = (array)glob(dirname(__DIR__) . DS . 'wysiwyg' . DS . '*' . DS . 'editor');
            foreach ($files as $filename) {
                if ($filename) {
                    $subfiles = (array)glob($filename . DS . '*');
                    // Editor nur auflisten wenn auch die Dateien vorhanden sind
                    if (count($subfiles) > 4) {
                        $editors[] = basename(dirname($filename));
                    }
                }
            }
        }
        return $editors;
    }

    private static function _defaults()
    {
        return array(/*  */

            'globals' => array(/*  */
                'pmxgroups' => array('1' => true,),
                'max_img_width' => 350,
                'max_img_height' => 300,
                'chmod_dir_to' => '',
                'chmod_to' => '',
                'area_foreground' => '',
                'area_background' => '',
                ),

            'admin' => array(/*  */
                'upload_max_size' => 1024,
                'editor' => 'ckeditor',
                'mode' => 3,
                'allow_modify' => true,
                'filetype' => array(/*  */
                    'images' => true,
                    'video' => true,
                    'audio' => false,
                    'documents' => false,
                    'archives' => false,
                    ),
                'manager' => 'elfinder',
                'roots' => array(/*  */
                    'media' => 'media',
                    'iupload' => 'images/iupload',
                    ),
                ),

            'user' => array(/*  */
                'upload_max_size' => 50,
                'editor' => 'ckeditor',
                'mode' => 2,
                'allow_modify' => false,
                'filetype' => array(/*  */
                    'images' => true,
                    'video' => false,
                    'audio' => false,
                    'documents' => false,
                    'archives' => false,
                    ),
                'manager' => 'elfinder',
                'roots' => array(/*  */
                    '_FILES' => 'media/userfiles',),
                ),

            'other' => array(/*  */
                'upload_max_size' => false,
                'editor' => 'ckeditor',
                'mode' => 1,
                'allow_modify' => false,
                'filetype' => array(/*  */
                    'images' => false,
                    'video' => false,
                    'audio' => false,
                    'documents' => false,
                    'archives' => false,),
                'manager' => false,
                'roots' => array(/*  */
                    '_FILES' => 'media/userfiles/anonymous',),
                ),
            );
    }
}

/**
 * Textarea
 *
 * @package
 * @author tora60
 * @copyright Copyright (c) 2011
 * @version $Id: Textarea.php 318 2017-02-05 21:09:24Z pragmamx $
 * @access public
 */
class Textarea extends pmxTextarea {
    /**
     * Textarea::__construct()
     */
    public function __construct()
    {
        $args = func_get_args();
        parent::__construct($args);
    }
}

?>