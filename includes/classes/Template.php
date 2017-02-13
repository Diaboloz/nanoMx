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

define('SAVANT', dirname(__file__) . '/Template/Savant3/');
require_once(SAVANT . 'Savant3.php');

/**
 * pmxTemplate
 *
 * Erweiterung der Savant Template Engine,
 * setzt die pragmaMx spezifische Umgebung
 *
 * @extends : Savant
 * @package pragmaMx
 * @author tora60
 * @copyright Copyright (c) 2008
 * @version $Id: Template.php 6 2015-07-08 07:07:06Z PragmaMx $
 * @access public
 */
class pmxTemplate extends Savant3 {
    /**
     * Array of configuration parameters.
     *
     * @access protected
     * @var array
     */
    protected $_config = array(
        // 'template_path' => array(),
        // 'resource_path' => array(),
        // 'error_text' => "\n\ntemplate error, examine fetch() result\n\n",
        // 'exceptions' => false,
        'autoload' => false,
        // 'compiler' => null,
        // 'filters' => array('Savant3_Filter_trimwhitespace','filter'),
        // 'plugins' => array(),
        // 'template' => null,
        // 'plugin_conf' => array(),
        // 'extract' => false,
        // 'fetch' => null,
        // 'escape' => array('htmlspecialchars'),
        );

    /**
     * Zwischenspeicher zum wiederherstellen der vorherigen Konfiguration.
     * Bei verschachtelten Templates benoetigt.
     *
     * @access private
     * @var array
     */
    // private $__configstack = array();
    /**
     * Konstruktor, Inititalisiert die Templates fuer
     * die jeweilige Anwendung!
     * setzt die globalen Grundeinstellungen fuer Savant
     *
     * @author Andi
     */
    function __construct($file = null)
    {
        /**
         * das Array, mit den Ordnern, in denen das abgerufene Template gesucht wird
         */
        if ($file) {
            /* falls Uebergabe als Array, muss der erste Paraeter $file sein! */
            if (is_array($file)) {
                list($file) = $file;
            }
            $this->_config['template_path'] = $this->_build_template_chain($file);
        }

        /* zusaetzliche Plugins */
        $this->_config['resource_path'][] = SAVANT . 'plugins';

        /* Konfiguration der Plugins */
        $this->_config['plugin_conf']['image'] = array('documentRoot' => PMX_REAL_BASE_DIR,
            'imageDir' => '.',
            );

        if (defined('MX_IS_ADMIN') && MX_IS_ADMIN) {
            $this->_config['exceptions'] = true;
        }

        /* der Pfad in dem ein fehlendes Template automatisch erstellt werden soll */
        $this->__config['create_template_path'] = './';

        /* das aktuell verwendete Template */
        $this->__config['current_template'] = null;

        /*  Aufruf von Filtern:
         - Filter Funktion in Klasse, per Array aufrufen
            array(__CLASS__, '__rel2abs'), // Makes all links absolute
         - dynamischen Savant-Filter, als Array aufrufen, mit kompletten Namen
            array('Savant3_Filter_rel2abs', 'filter'),
         - Filter in normaler Funktion, als String aufrufen
            'trim'
        */
        // $this->addFilters(array('Savant3_Filter_trimwhitespace','filter'));
        // $this->addFilters(array('Savant3_Filter_translate','filter'));
        /**
         * Constructor von Savant aufrufen um die Initialisierung abzuschliessen
         */
        parent::__construct($this->_config);
    }

    /**
     * pmxTemplate::template()
     * die normale Funktion aus der Elternklasse
     * allerdings wird hier der Name (mit Pfad) des ermittelten Templates in
     * die config-Variable current_template eingelesen, die dann auch in
     * anderen Funktionen, z.B. Filtern etc verwendet werden kann
     *
     * @param mixed $tpl
     * @return
     */
    protected function template($tpl = null)
    {
        // unter Windows, die backslashes durch normale slashes ersetzen
        $this->__config['current_template'] = str_replace(DS, '/', parent::template($tpl));
        return $this->__config['current_template'];
    }

    /**
     * pmxTemplate::fetch()
     * die normale Funktion aus der Elternklasse
     * -
     *
     * @param mixed $tpl
     * @return
     */
    public function fetch($tpl = null)
    {
        $out = parent::fetch($tpl);
        // $this->__config = $this->__configstack;
        return $out;
    }

    /**
     * pmxTemplate::display()
     * Displays a template directly (equivalent to <code>echo $tpl</code>).
     *
     * @param string $tpl The template source to compile and display.
     * @return
     */
    public function display($tpl = null)
    {
        $output = $this->fetch($tpl);
        if ($this->isError($output)) {
            $text = $this->__config['error_text'];
            echo $this->escape($text);
        } else {
            echo $output;
        }
    }

    /**
     * pmxTemplate::error()
     * die normale Funktion aus der Elternklasse
     * - insofern erweitert, dass fehlende Templatedateien automatisch
     * generiert werden koennen
     *
     * @param mixed $code
     * @param array $info
     * @param mixed $level
     * @param mixed $trace
     * @return
     */
    public function error($code, $info = array(), $level = E_USER_ERROR, $trace = true)
    {
        if ($code == 'ERR_TEMPLATE' && !empty($info['template'])) {
            include_once(dirname(__file__) . '/Template/make_template.php');
            pmx_make_template($this->__config['create_template_path'], $info['template'], $this);
        }

        return parent::error($code, $info, $level, $trace);
    }

    /**
     * pmxTemplate::init_path()
     *
     * @param mixed $file
     * @return
     */
    public function init_path($file = null)
    {
        $pathes = $this->_build_template_chain($file);
        $this->addPath('template', $pathes);
        return $pathes;
    }

    /**
     * pmxTemplate::init_template()
     * creates and sets the template name to use.
     * entspricht der Funktion setTemplate(), zusaetzlich wird aber der
     * Templatename automatisch ermittelt, wenn __FILE__ uebergeben wurde
     *
     * @param string $template The template name.
     * @return void
     */
    public function init_template($template)
    {
        /* wenn __FILE__ uebergeben wurde, den Dateinamen automatisch ermitteln */
        if (strpos($template, PMX_REAL_BASE_DIR) === 0) {
            $pathinfo = pathinfo($template);
            $template = $pathinfo['filename'] . '.html';
        }
        parent::setTemplate($template);
    }

    /**
     * pmxTemplate::find_language_template()
     *
     * @param mixed $language
     * @param mixed $prefix
     * @param string $suffix
     * @return
     */
    public function find_language_template($language, $prefix, $suffix = '.html')
    {
        $search = array(/* mögliche Sprachen im Template Namen */
            $language,
            $GLOBALS['language'],
            $GLOBALS['currentlang'],
            'german',
            'english',
            );
        $search = array_unique($search);

        foreach ($search as $file) {
            if ($found = $this->findFile('template', $prefix . $file . $suffix)) {
                return $found;
            }
        }
        return false;
    }

    /**
     * pmxTemplate::_build_template_chain()
     *
     * @param mixed $file
     * @return
     */
    private function _build_template_chain($file)
    {
        $file .= DS; // Trick um auch Verzeichnisse zu verarbeiten
        $pathes = array();
        // $this->__configstack = $this->__config;
        switch (true) {
            case (strpos($file, PMX_MODULES_DIR) === 0):
                /* Module */
                $namepos[0] = strlen(PMX_MODULES_DIR) + 1 ;
                $namepos[1] = strpos($file, DS, $namepos[0]);
                $pathes[4] = substr($file, 0, $namepos[1] + 1) . 'templates';
                $pathes[3] = realpath($pathes[4] . DS . 'custom');
                if (defined('MX_THEME')) {
                    if ($pathes[2] = realpath(PMX_THEMES_DIR . DS . MX_THEME . DS . 'templates' . substr(substr($file, 0, $namepos[1]), strlen(PMX_REAL_BASE_DIR)))) {
                        $pathes[1] = realpath($pathes[2] . DS . 'custom');
                    }
                }
                break;

            case (strpos($file, PMX_BLOCKS_DIR) === 0):
                /* System-Bloecke */
                $pathes[4] = PMX_LAYOUT_DIR . DS . 'templates' . DS . 'blocks';
                $pathes[3] = realpath($pathes[4] . DS . 'custom');
                if (defined('MX_THEME')) {
                    if ($pathes[2] = realpath(substr_replace($pathes[4], PMX_THEMES_DIR . DS . MX_THEME, 0, strlen(PMX_LAYOUT_DIR)))) {
                        $pathes[1] = realpath($pathes[2] . DS . 'custom');
                    }
                }
                break;

            case preg_match('#^' . preg_quote(PMX_ADMINMODULES_DIR . DS) . '([^.' . preg_quote(DS) . ']+)+#', $file, $matches):
                /* Admin Module, findet:
                  - admin/modules/(***).php
                  - admin/modules/(***)/xy.php
                 */
                $pathes[4] = $matches[0] . DS . 'templates';
                $pathes[3] = realpath($pathes[4] . DS . 'custom');
                if (defined('MX_THEME')) {
                    if ($pathes[2] = realpath(PMX_THEMES_DIR . DS . MX_THEME . DS . 'templates' . substr($matches[0], strlen(PMX_REAL_BASE_DIR)))) {
                        $pathes[1] = realpath($pathes[2] . DS . 'custom');
                    }
                }
                break;

            default:
                /* alle anderen */
                $pathes[4] = dirname(substr_replace($file, PMX_LAYOUT_DIR . DS . 'templates', 0, strlen(PMX_REAL_BASE_DIR))) . DS . basename($file, '.php');
                $pathes[3] = realpath($pathes[4] . DS . 'custom');
                if (defined('MX_THEME')) {
                    if ($pathes[2] = realpath(substr_replace($pathes[4], PMX_THEMES_DIR . DS . MX_THEME, 0, strlen(PMX_LAYOUT_DIR)))) {
                        $pathes[1] = realpath($pathes[2] . DS . 'custom');
                    }
                }
        }

        if ($pathes) {
            // der Pfad in dem ein fehlendes Template automatisch erstellt werden soll
            $this->__config['create_template_path'] = $pathes[4];
            // leere Variablen entfernen
            $pathes = array_filter($pathes);
            // Reihenfolge umkehren
            krsort($pathes);
        }

        return $pathes;
    }

    /**
     * Set the path to the template files.
     *
     * @param string $path path to template files
     * @return void
     */
    public function set_path($path)
    {
        $this->add_path($path);
    }

    /**
     * Adds a search directory for templates.
     *
     * @access public
     * @param string $path , The directory to search.
     * @return void
     */
    public function add_path($path)
    {
        $path = rtrim($path, '/\\');
        $this->addPath('template', $path);
        if ($custom = realpath($path . DS . 'custom')) {
            $this->addPath('template', $custom);
        }
    }

    /**
     * pmxTemplate::add_filter()
     * Adds a filter with default names to filters
     *
     * @param mixed $filtername
     * @return
     */
    public function add_filter($filtername)
    {
        $this->addFilters(array("Savant3_Filter_$filtername", 'filter'));
    }

    public function translate($value)
    {
        return mxTranslate($value);
        // return pmx::$lang->translate($value);
    }

    /* Alias fuer translate() */
    public function _($value)
    {
        return mxTranslate($value);
        // return pmx::$lang->translate($value);
    }
}

?>