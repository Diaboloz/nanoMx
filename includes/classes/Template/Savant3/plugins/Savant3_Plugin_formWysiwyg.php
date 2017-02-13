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

/**
 * TODO: die Konfiguration des Javascript noch in die
 * optionalen Parameter $attribs einarbeiten!
 * TODO: Dokumentation !!
 */

require_once 'Savant3_Plugin_form_element.php';

/**
 * Plugin to generate a 'Wysiwyg' element.
 *
 * @package Savant3
 * @subpackage Savant3_Plugin_Form
 * @author Paul M. Jones <pmjones@ciaweb.net>
 */

class Savant3_Plugin_formWysiwyg extends Savant3_Plugin_form_element {
    /**
     * The default number of height for a textarea.
     *
     * @access public
     * @var int
     */

    public $height = '450';

    /**
     * The default number of columns for a textarea.
     *
     * @access public
     * @var int
     */

    public $width = '100%';

    /**
     * The default mode of the toolbar for a textarea.
     *
     * @access public
     * @var string
     */

    public $mode = '';

    /**
     * Generates a 'Wysiwyg' element.
     *
     * @access public
     * @param string $ |array $name If a string, the element name.  If an
     * array, all other parameters are ignored, and the array elements
     * are extracted in place of added parameters.
     * @param mixed $value The element value.
     * @param array $attribs Attributes for the element tag.
     * @return string The element XHTML.
     */

    public function formWysiwyg($name, $value = null, $attribs = null)
    {
        $info = $this->getInfo($name, $value, $attribs);
        extract($info); // name, value, attribs, options, listsep, disable
        $xhtml = '';
        // build the element
        if ($disable) {
            // disabled.
            $xhtml .= $this->Savant->formHidden($name, $value);
            $xhtml .= nl2br(htmlspecialchars($value, ENT_COMPAT | ENT_HTML5, 'UTF-8', false));
        } else {
            // enabled.
            // first, make sure that there are 'height' and 'width' values
            // as required by the spec.
            if (empty($attribs['height'])) {
                $attribs['height'] = $this->height;
            }

            if (empty($attribs['width'])) {
                $attribs['width'] = $this->width;
            }

            if (empty($attribs['mode'])) {
                $attribs['mode'] = $this->mode;
            }

            $attribs['name'] = $name;

            $attribs['value'] = $value;
            // now build the element.
            // $sw = load_class('Textarea', $name, $value);
            $sw = load_class('Textarea', $attribs);
            // $sw = new pmxTextarea($name, $value);
            // $sw->setWidth($attribs['width']);
            // $sw->setHeight($attribs['height']);
            // TODO: hier noch den Toolbar-Mode beruecksichtigen
            $xhtml = $sw->getHtml();
        }

        return $xhtml;
    }
}

?>