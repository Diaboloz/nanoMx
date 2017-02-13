<?php
/**
 * Plugin to generate a 'password' element.
 *
 * @package Savant3
 * @subpackage Savant3_Plugin_Form
 * @author Paul M. Jones <pmjones@ciaweb.net>
 * @license http://www.gnu.org/copyleft/lesser.html LGPL
 * @version $Id: Savant3_Plugin_formPassword.php 6 2015-07-08 07:07:06Z PragmaMx $
 */

require_once 'Savant3_Plugin_form_element.php';

/**
 * Plugin to generate a 'password' element.
 *
 * @package Savant3
 * @subpackage Savant3_Plugin_Form
 * @author Paul M. Jones <pmjones@ciaweb.net>
 */

class Savant3_Plugin_formPassword extends Savant3_Plugin_form_element {
    private static $_validator = false;

    /**
     * Generates a 'password' element.
     *
     * @access public
     * @param string $ |array $name If a string, the element name.  If an
     * array, all other parameters are ignored, and the array elements
     * are extracted in place of added parameters.
     * @param mixed $value The element value.
     * @param array $attribs Attributes for the element tag.
     * @return string The element XHTML.
     */

    public function formPassword($name, $value = null, $attribs = null, $validate = true)
    {
        $info = $this->getInfo($name, $value, $attribs);
        extract($info); // name, value, attribs, options, listsep, disable

        if (isset($options['validate'])) {
            $validate = (bool)$options['validate'];
        }

        if ($validate && !$disable) {
            if (!self::$_validator) {
                self::$_validator = true;

                pmx_html_passwordchecker();
            }

            if (isset($attribs['class'])) {
                $attribs['class'] .= ' password-checker-input';
            } else {
                $attribs['class'] = 'password-checker-input';
            }
        }

        /* build the element */
        $xhtml = '';
        if ($disable) {
            // disabled
            $xhtml .= $this->Savant->formHidden($name, $value);
            $xhtml .= 'xxxxxxxx';
        } else {
            // enabled
            $xhtml .= '<input type="password"';
            $xhtml .= ' name="' . htmlspecialchars($name, ENT_COMPAT | ENT_HTML5, 'UTF-8', false) . '"';
            $xhtml .= ' value="' . htmlspecialchars($value, ENT_COMPAT | ENT_HTML5, 'UTF-8', false) . '"';
            $xhtml .= $this->Savant->htmlAttribs($attribs);
            $xhtml .= ' />';
        }

        return $xhtml;
    }
}

?>