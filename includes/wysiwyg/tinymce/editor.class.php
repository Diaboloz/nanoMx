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
 * tinymce Klasse
 *
 * Konfiguration, siehe hier:
 * - http://wiki.moxiecode.com/index.php/TinyMCE:Index
 *
 * @package pragmaMx
 * @author tora60
 * @copyright Copyright (c) 2011
 * @version $Id: editor.class.php 6 2015-07-08 07:07:06Z PragmaMx $
 * @access public
 */
class pmx_editor_tinymce {
    /* Standardwerte initialisieren */

    protected $name, $height, $width, $lang, $stylesheet, $mode, $value, $pagebreak, $infotext, $langdirection;

    protected $cols, $rows, $editor_path, $templatedir, $script_url, $filebrowser, $contentcss;

    /**
     * pmx_editor_tinymce::__construct()
     * Der Constructor der Klasse
     *
     * @param mixed $parent_area
     */
    public function __construct($parent_area)
    {
        $class_vars = get_class_vars(__CLASS__);

        $intersect = array_intersect_key($parent_area, $class_vars);
        foreach ($intersect as $key => $value) {
            $this->$key = $value;
        }

        $editor = basename(__DIR__);
        $this->templatedir = __DIR__ . DS . 'templates';
        $this->editor_path = PMX_BASE_PATH . PMX_SYSTEM_PATH . 'wysiwyg/' . $editor . '/';
        $this->script_url = $this->editor_path . 'editor/tinymce.min.js';
        $this->contentcss = $this->editor_path . 'htmlarea.css.php?t=' . MX_THEME . '';

        $fb = load_class('Filebrowse');
        $this->filebrowser['active'] = $fb->is_active();
        $this->filebrowser['url'] = $fb->getBrowseUrl(array('editor' => $editor));
        $this->filebrowser['title'] = $fb->title;

        if (_DOC_LANGUAGE != 'en' && !file_exists(__DIR__ . DS . 'editor' . DS . 'langs' . DS . _DOC_LANGUAGE . '.js')) {
            /* Fallback fuer fehlende Sprachdatei, english müsste immer da sein */
            $this->lang = 'en';
            $this->langdirection = 'ltr';
        } else {
            $this->lang = _DOC_LANGUAGE;
            $this->langdirection = _DOC_DIRECTION;
        }
    }

    /**
     * pmx_editor_tinymce::getHtml()
     * Der komplett generierte HTML-Output dieser Klasse
     * Es wird nicht nur die eigentliche Textarea erstellt, sondern auch die
     * zugehörigen Einstellungen und Erweiterungen im HTML-Headbereich der
     * Seite erzeugt.
     *
     * @return string
     */
    public function getHtml()
    {
        $this->set_dimensions();

        /* TODO: Toolbar einstellen !! setMode() */
        $template = $this->mode . '.template.html';

        $templatevars = array();
        foreach ($this as $key => $value) {
            $templatevars[$key] = $value;
        }

        $templatevars['value'] = htmlspecialchars($this->value);

        /* TODO: Cols und Rows noch berechnen? */

        /**
         * hier den kompletten Eintrag fuer den Headbereich generieren
         */

        /* Load jQuery */
        pmxHeader::add_jquery();
        /* Load jQuery build */
        // pmxHeader::add_script($this->editor_path . 'editor/jquery.tinymce.js');
        pmxHeader::add_script($this->editor_path . 'editor/jquery.tinymce.min.js');

        /* Template initialisieren */
        $template_object = load_class('Template');
        $template_object->set_path($this->templatedir);
        $template_object->assign($templatevars);
        $out = $template_object->fetch($template);

        /**
         * nur das erzeugte Textfeld zurueckgeben, damit dieses als
         * eigentlichen Editor an der aufgerufenen Stelle erscheint.
         * der Rest aus dieser Funktion landet ja im Headbereich der Seite
         */
        return $out;
    }

    /**
     * pmx_editor_ckeditor::set_dimensions()
     *
     * @return nothing
     */
    protected function set_dimensions()
    {
        // TODO: andere Einheiten umrechnen ?
        switch (true) {
            case !$this->width:
            case substr($this->width, -1) === '%':
                $width = intval($this->width);
                if (!$width || $width > 97) {
                    $width = 97;
                }
                $this->cols = round(80 / 100 * $width);
                $this->width = $width . '%';
                break;
            default:
                $this->width = intval($this->width);
                $this->cols = round($this->width / 6.5);
                if ($this->cols > 80) {
                    $this->cols = 80;
                }
                $this->width .= 'px';
        }

        $this->height = intval($this->height);
        switch (true) {
            // Hoehe nur in Pixel, oder leer
            case !$this->height:
                $this->rows = 12;
                $this->height = 'auto';
                break;
            default:
                $this->rows = round($this->height / 14.5);
                if ($this->rows > 20) {
                    $this->rows = 20;
                }
                $this->height .= 'px';
        }
    }
}

?>