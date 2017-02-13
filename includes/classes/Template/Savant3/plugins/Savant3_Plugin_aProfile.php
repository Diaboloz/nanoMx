<?php

/**
 * Generates an <a href="">...</a> tag.
 *
 * @package Savant3
 * @author Paul M. Jones <pmjones@ciaweb.net>
 * @license http://www.gnu.org/copyleft/lesser.html LGPL
 * @version $Id: Savant3_Plugin_aProfile.php 6 2015-07-08 07:07:06Z PragmaMx $
 */

/**
 * Generates an <a href="">...</a> tag.
 *
 * @package Savant3
 * @author Paul M. Jones <pmjones@ciaweb.net>
 */

class Savant3_Plugin_aProfile extends Savant3_Plugin {
    /* zwischenspeichern, ob Userinfo Modul erlaubt */
    private static $__uinfallowed = null;

    /**
     * Generate an HTML <a href="">...</a> tag.
     *
     * @access public
     * @param string $ |array $href A string URL for the resulting tag.  May
     * also be an array with any combination of the keys 'scheme',
     * 'host', 'path', 'query', and 'fragment' (c.f. PHP's native
     * parse_url() function).
     * @param string $text The displayed text of the link.
     * @param string $ |array $attr Any extra attributes for the <a> tag.
     * @return string The <a href="">...</a> tag.
     */

    public function aProfile($username, $text = null, $attr = null)
    {
        if (is_null(self::$__uinfallowed)) {
            self::$__uinfallowed = (mxModuleAllowed('Userinfo') || MX_IS_ADMIN);
        }

        $html = '<a';

        if (self::$__uinfallowed) {
            $html .= ' href="modules.php?name=Userinfo&amp;uname=' . urlencode($username) . '"';
        }
        // add attributes
        if (is_array($attr)) {
            // from array
            foreach ($attr as $key => $val) {
                $key = htmlspecialchars($key, ENT_COMPAT | ENT_HTML5, 'UTF-8', false);
                $val = htmlspecialchars($val, ENT_COMPAT | ENT_HTML5, 'UTF-8', false);
                $html .= " $key=\"$val\"";
            }
        } elseif (! is_null($attr)) {
            // from scalar
            $html .= htmlspecialchars(" $attr", ENT_COMPAT | ENT_HTML5, 'UTF-8', false);
        }

        if (!strpos($html, 'title=') && defined('_USERINFO')) {
            $html .= ' title="' . _USERINFO . '"';
        }

        if (is_null($text)) {
            $text = $username;
        }
        // set the link text, close the tag, and return
        $html .= '>' . $text . '</a>';

        return $html;
    }
}

?>