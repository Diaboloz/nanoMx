<?php

/**
 * Plugin to generate an 'image' element.
 *
 * @package Savant3
 * @subpackage Savant3_Plugin_Form
 * @author Paul M. Jones <pmjones@ciaweb.net>
 * @license http://www.gnu.org/copyleft/lesser.html LGPL
 * @version $Id: Savant3_Plugin_formImage.php 6 2015-07-08 07:07:06Z PragmaMx $
 */

require_once 'Savant3_Plugin_form_element.php';

/**
 * Plugin to generate an 'image' element.
 *
 * @package Savant3
 * @subpackage Savant3_Plugin_Form
 * @author Paul M. Jones <pmjones@ciaweb.net>
 */

class Savant3_Plugin_formImage extends Savant3_Plugin_form_element {
    /**
     * Generates an 'image' element.
     *
     * @access public
     * @param string $ |array $name If a string, the element name.  If an
     * array, all other parameters are ignored, and the array elements
     * are extracted in place of added parameters.
     * @param mixed $image The source ('src="..."') for the image.
     * @param array $attribs Attributes for the element tag.
     * @return string The element XHTML.
     */

    public function formImage($name, $image = null, $attribs = null)
    {
        $info = $this->getInfo($name, $image, $attribs);
        extract($info); // name, value, attribs, options, listsep, disable

        if (!$name) {
            $name = 'btn_' . rand();
        }
        // unset any 'src' attrib
        if (isset($attribs['src'])) {
            unset($attribs['src']);
        }
        // unset any 'alt' attrib
        if (isset($attribs['alt'])) {
            $alt = $attribs['alt'];
            unset($attribs['alt']);
        } else {
            $alt = $name;
        }

        /* disabled, just an image tag */
        if ($disable) {
            return $this->Savant->image($image, $alt, $attribs);
        }

        /* enabled, build the element */
        $path = null;
        $info = null;
        $xhtml = '';

        if (strpos($image, '://') === false) {
            $path = $this->Savant->findimage($image); // plugin findimage
            $image = PMX_BASE_PATH . substr($path, strlen(PMX_REAL_BASE_DIR) + 1);
        }

        if (!isset($attribs['style'])) {
            if ($path) {
                $info = @getimagesize($path);
            }

            /* did we find the file info? */
            if (is_array($info)) {
                $attribs['style'] = 'border: none; background: transparent; width: ' . $info[0] . 'px; height: ' . $info[1] . 'px;';
            } else {
                $attribs['style'] = 'border: none; background: transparent;';
            }
        }

        $xhtml = '<input type="image"';
        $xhtml .= ' name="' . htmlspecialchars($name, ENT_COMPAT | ENT_HTML5, 'UTF-8', false) . '"';
        $xhtml .= ' src="' . htmlspecialchars($image, ENT_COMPAT | ENT_HTML5, 'UTF-8', false) . '"';
        $xhtml .= $this->Savant->htmlAttribs($attribs);
        $xhtml .= ' />';

        return $xhtml;
    }
}

?>