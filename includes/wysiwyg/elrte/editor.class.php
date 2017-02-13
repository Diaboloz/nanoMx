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
 * elRTE Klasse
 *
 * Konfiguration, siehe hier:
 * http://elrte.org/redmine/projects/elrte/wiki/,
 * - oder besser: in der mitgelieferten elRTE.options.js
 *
 * @package pragmaMx
 * @author tora60
 * @copyright Copyright (c) 2011
 * @version $Id: editor.class.php 6 2015-07-08 07:07:06Z PragmaMx $
 * @access public
 */
class pmx_editor_elrte {
    /* Standardwerte initialisieren */

    protected $name, $height, $width, $lang, $stylesheet, $mode, $value, $pagebreak, $infotext, $langdirection;

    protected $cols, $rows, $editor_path, $templatedir, $doctype;

    protected static $instances = 0;

    /**
     * pmx_editor_elrte::__construct()
     * Der Constructor der Klasse
     *
     * @param mixed $parent_area
     */
    public function __construct($parent_area)
    {
        self::$instances++;

        $class_vars = get_class_vars(__CLASS__);

        $intersect = array_intersect_key($parent_area, $class_vars);
        foreach ($intersect as $key => $value) {
            $this->$key = $value;
        }

        $this->editor_path = PMX_BASE_PATH . PMX_SYSTEM_PATH . 'wysiwyg/' . basename(__DIR__) . '/editor/';
        $this->templatedir = __DIR__ . DS . 'templates';

        /* TODO: Fallback für fehlende Sprachdatei !!! */
        $this->lang = _DOC_LANGUAGE;
        $this->langdirection = _DOC_DIRECTION;
    }

    /**
     * pmx_editor_elrte::getHtml()
     * Der komplett generierte HTML-Output dieser Klasse
     * Es wird nicht nur die eigentliche Textarea erstellt, sondern auch die
     * zugehoerigen Einstellungen und Erweiterungen im HTML-Headbereich der
     * Seite erzeugt.
     *
     * @return string (html)
     */
    public function getHtml()
    {
        $this->set_dimensions();

        $template = $this->mode . '.template.html';

        $templatevars = array();
        foreach ($this as $key => $value) {
            $templatevars[$key] = $value;
        }

        $templatevars['value'] = htmlspecialchars($this->value);

        /* TODO: Cols und Rows noch berechnen? */

        /* Template initialisieren */
        $template_object = load_class('Template');
        $template_object->set_path($this->templatedir);
        $template_object->assign($templatevars);
        $out = $template_object->fetch($template);

        if (self::$instances === 1) {
            /* ab hier den kompletten Eintrag für den Headbereich generieren */

            $fb = load_class('Filebrowse');
            $filebrowse_active = $fb->is_active();
            $template_object->assign('filebrowse_active', $filebrowse_active);

            $doctype = mxDoctypeArray($GLOBALS['DOCTYPE']);
            $this->doctype = preg_replace('#\s+#', ' ', $doctype['value']);

            pmxHeader::add_jquery(/* jQuery-UI */
                'jquery.migrate.js', // wegen .browser
                'ui/jquery.ui.draggable.js',
                'ui/jquery.ui.droppable.js',
                'ui/jquery.ui.resizable.js',
                'ui/jquery.ui.selectable.js',
                'ui/jquery.ui.button.js',
                'ui/jquery.ui.dialog.js',
                'ui/jquery.ui.effect.js'
                );

            /* Tabs ohne ui */
            pmxHeader::add_tabs();

            pmxHeader::add_script($this->editor_path . 'js/elrte.min.js');
            pmxHeader::add_script($this->editor_path . 'js/i18n/elrte.' . $this->lang . '.js');

            pmxHeader::add_style($this->editor_path . 'css/elrte.min.css');
            pmxHeader::add_style($this->templatedir . '/style.pmx.css');

            $head_options = $template_object->fetch('options.js.php');
            pmxHeader::add_script_code($head_options);

            if ($filebrowse_active) {
                // Achtung! hier wird evtl. der Templatepfad geändert!!
                $out .= $fb->elrte();
            }
        }

        /**
         * nur das erzeugte Textfeld zurueckgeben, damit dieses als
         * eigentlichen Editor an der aufgerufenen Stelle erscheint.
         * der Rest aus dieser Funktion landet ja im Headbereich der Seite
         */
        return $out;
    }

    /**
     * pmx_editor_elrte::set_dimensions()
     *
     * @return nothing
     */
    protected function set_dimensions()
    {
        // der elRTE akzeptiert nur Zahlen in Pixelwerten
        // TODO: andere Einheiten umrechnen ?
        switch (true) {
            case !$this->width:
                $this->cols = 80;
                break;
            case substr($this->width, -1) === '%':
                $width = intval($this->width);
                if (!$width || $width > 97) {
                    $width = 97;
                }
                $this->cols = round(80 / 100 * $width);
                $this->width = false;
                break;
            default:
                $this->width = intval($this->width);
                $this->cols = round($this->width / 6.5);
                if ($this->cols > 80) {
                    $this->cols = 80;
                }
        }

        $this->height = intval($this->height);
        switch (true) {
            // der elRTE akzeptiert nur Zahlen in Pixelwerten
            case !$this->height:
                $this->rows = 12;
                break;
            default:
                $this->rows = round($this->height / 14.5);
                if ($this->rows > 20) {
                    $this->rows = 20;
                }
        }
    }
}

?>
