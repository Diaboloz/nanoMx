<?php

/**
 * Plugin to generate a 'text' element.
 *
 * @package Savant3
 * @subpackage Savant3_Plugin_Form
 * @author Paul M. Jones <pmjones@ciaweb.net>
 * @license http://www.gnu.org/copyleft/lesser.html LGPL
 * @version $Id: Savant3_Plugin_formColorpicker.php 6 2015-07-08 07:07:06Z PragmaMx $
 */

require_once 'Savant3_Plugin_form_element.php';

/**
 * Plugin to generate a 'text' element.
 *
 * @package Savant3
 * @subpackage Savant3_Plugin_Form
 * @author Paul M. Jones <pmjones@ciaweb.net>
 */

class Savant3_Plugin_formColorpicker extends Savant3_Plugin_form_element {
    static $done = false;

    /**
     * Generates a 'text' element.
     *
     * @access public
     * @param string $ |array $name If a string, the element name.  If an
     * array, all other parameters are ignored, and the array elements
     * are used in place of added parameters.
     * @param mixed $value The element value.
     * @param array $attribs Attributes for the element tag.
     * @return string The element XHTML.
     */

    public function formColorpicker($name, $value = null, $attribs = null)
    {
        $info = $this->getInfo($name, $value, $attribs);
        extract($info); // name, value, attribs, options, listsep, disable
        $value = strtolower(trim($value, '# '));

        $xhtml = '';
        // build the element
        if ($disable) {
            // disabled
            $xhtml .= $this->Savant->formHidden($name, $value);
            $xhtml .= htmlspecialchars($value, ENT_COMPAT | ENT_HTML5, 'UTF-8', false);
            return $xhtml;
        }

        $attribs['size'] = (isset($attribs['size'])) ? intval($attribs['size']) : 8;
        $attribs['maxlength'] = (isset($attribs['maxlength'])) ? intval($attribs['maxlength']) : 6;

        $defcol = ($value) ? '#' . $value : 'transparent';

        $xhtml .= '<span class="colpick-color-box" style="border-right-color:' . $defcol . '">';
        $xhtml .= '<input type="text"';
        $xhtml .= ' name="' . htmlspecialchars($name, ENT_COMPAT | ENT_HTML5, 'UTF-8', false) . '"';
        $xhtml .= ' value="' . htmlspecialchars($value, ENT_COMPAT | ENT_HTML5, 'UTF-8', false) . '"';
        $xhtml .= $this->Savant->htmlAttribs($attribs);
        $xhtml .= ' />';
        $xhtml .= '</span>';

        if (self::$done) {
            return $xhtml;
        }

        ob_start();

        ?>

<script type="text/javascript">
  $(function() {
    $("span.colpick-color-box input").colpick( {
      colorScheme:'light',
      onSubmit: function(hsb, hex, rgb, el) {
        $(el).val(hex);
        $(el).parent().css('border-right-color', '#'+hex);
        $(el).colpickHide();
      },
      onBeforeShow: function () {
        $(this).colpickSetColor(this.value);
        return false;
      }
    });
    $("span.colpick-color-box input").parent().click(
      function () {
        $(this).children().click();
      });
  });
</script>

<?php
        $script = ob_get_clean();

        pmxHeader::add_script_code($script);
        pmxHeader::add_jquery('color/js/colpick.js');
        pmxHeader::add_style('includes/javascript/jquery/color/css/colpick.css');

        self::$done = true;
        return $xhtml;
    }
}

?>