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
 * die richtige Klasse fuer die php-Version laden
 */
include_once(dirname(__FILE__) . '/editor/fckeditor_php5.php');

/**
 * FCK-Editor Klasse
 *
 * Konfiguration, siehe hier:
 * http://wiki.fckeditor.net/,
 * - oder besser: in der mitgelieferten fckconfig.js
 *
 * @package pragmaMx
 * @author tora60
 * @copyright Copyright (c) 2011
 * @version $Id: editor.class.php 6 2015-07-08 07:07:06Z PragmaMx $
 * @access public
 */
class pmx_editor_fck extends FCKeditor {
    /* Standardwerte initialisieren */

    protected $name, $height, $width, $lang, $stylesheet, $mode, $value, $pagebreak, $infotext, $langdirection, $uicolor;

    protected $cols, $rows, $editor_path, $templatedir;

    /**
     * pmx_editor_fck::__construct()
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

        $this->BasePath = PMX_BASE_PATH . PMX_SYSTEM_PATH . 'wysiwyg/' . basename(dirname(__FILE__)) . '/editor/';
    }

    /**
     * Der komplett generierte HTML-Output dieser Klasse
     * Es wird nicht nur die eigentliche Textarea erstellt, sondern auch die
     * zugehoerigen Einstellungen und Erweiterungen im HTML-Headbereich der
     * Seite erzeugt.
     */
    public function getHtml()
    {
        // $this->setMode($this->mode);
        $this->setWidth($this->width);
        $this->setHeight($this->height);

        $this->ToolbarSet = 'Default' ;
        // Achtung, im FCK, vorne gross
        $this->Width = $this->width;
        $this->Height = $this->height;
        $this->Value = $this->value;
        $this->Config = array('AutoDetectLanguage' => true,
            'DefaultLanguage' => _DOC_LANGUAGE,
            'ContentLangDirection' => _DOC_DIRECTION,
            ) ;

        return $this->CreateHtml();
    }
    // /**
    // * This property holds the toolbar mode of the editor.
    // */
    // public function setMode($mode = '')
    // {
    // // TODO: reduced, normal, full
    // }
    /**
     * This property holds the name and ID of the editor.
     */
    public function setName($value)
    {
        // Achtung, im FCK, spezieller Name
        $this->InstanceName = $value;
    }

    /**
     * This property holds the content of the editor.
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * This property controls the height of the editor.
     */
    public function setHeight($value)
    {
        // TODO: der fck macht Probleme bei kleinen Hoehen, da wird der
        // Eingabebereich durch die Toolbar verdeckt, evtl. hier den Wert
        // dynamisch vergroesssern
        if (substr($value, -1) === '%') {
            $value = intval($value) . '%';
        } else {
            $value = intval($value) . 'px';
        }
        // Achtung, im FCK, vorne gross
        $this->height = $value;
    }

    /**
     * This property controls the width of the editor.
     */
    public function setWidth($value)
    {
        if (substr($value, -1) === '%') {
            $value = intval($value) . '%';
        } else {
            $value = intval($value) . 'px';
        }
        // Achtung, im FCK, vorne gross
        $this->width = $value;
    }
}

?>
