<?php

/**
 * Plugin to convert an associative array to a string of tag attributes.
 *
 * @package Savant3
 * @author Paul M. Jones <pmjones@ciaweb.net>
 * @license http://www.gnu.org/copyleft/lesser.html LGPL
 * @version $Id: Savant3_Plugin_htmlAttribs.php 6 2015-07-08 07:07:06Z PragmaMx $
 */

/**
 * Plugin to convert an associative array to a string of tag attributes.
 *
 * @package Savant3
 * @author Paul M. Jones <pmjones@ciaweb.net>
 */

class Savant3_Plugin_htmlAttribs extends Savant3_Plugin {
    /**
     * Converts an associative array to a string of tag attributes.
     *
     * @access public
     * @param array $attribs From this array, each key-value pair is
     * converted to an attribute name and value.
     * @return string The XHTML for the attributes.
     */

    public function htmlAttribs($attribs)
    {
        $xhtml = '';
        foreach ((array) $attribs as $key => $val) {
            if ($val === null) {
                continue;
            }

            if (is_array($val)) {
                $val = implode(' ', $val);
            }

            $key = htmlspecialchars($key, ENT_COMPAT | ENT_HTML5, 'UTF-8', false);
            $val = htmlspecialchars($val, ENT_COMPAT | ENT_HTML5, 'UTF-8', false);

            $xhtml .= " $key=\"$val\"";
        }
        return $xhtml;
    }
}

?>