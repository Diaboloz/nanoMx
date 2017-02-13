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
 *
 * This is version 1.11 of class.overlib for php (http://www.php.net)
 * written 1999, 2000, 2001 Patrick Hess <hess@dland.de>
 * overLib is from Eric Bosrup (http://www.bosrup.com/web/overlib/)
 * This class is just a driver/container, so most of this wonderful
 * work is done by Eric Bosrup! Keep this in mind...
 * Small changes by A.Ellsel (http://www.pragmaMx.org), to better works with pragmaMx.
 */

defined('mxMainFileLoaded') or die('access denied');
global $bgcolor1, $textcolor1, $bgcolor2, $textcolor2;

if (!defined("_ol_path")) define("_ol_path", dirname(__FILE__));
if (!defined("_ol_fgcolor")) define("_ol_fgcolor", $bgcolor1);
if (!defined("_ol_textcolor")) define("_ol_textcolor", $textcolor1);
if (!defined("_ol_bgcolor")) define("_ol_bgcolor", $bgcolor2);
if (!defined("_ol_capcolor")) define("_ol_capcolor", $textcolor2);
if (!defined("_ol_closecolor")) define("_ol_closecolor", $textcolor2);

class Overlib {
    var $ol_path = _ol_path;
    var $ol_align = 0;
    var $ol_valign = 0;
    // Main background color (the large area). Usually a bright color (white, yellow etc)
    var $ol_fgcolor = _ol_fgcolor; # "#fcfcfc";
    // Border color and color of caption. Usually a dark color (black, brown etc)
    var $ol_bgcolor = _ol_bgcolor; # "#0080C0";
    // Text color. Usually a dark color
    var $ol_textcolor = _ol_textcolor; # "#000000";	# var $ol_textcolor = "";
    // Color of the caption text. Usually a bright color
    var $ol_capcolor = _ol_capcolor; # "#FFFFFF";
    // Color of "Close" when using Sticky. Usually a semi-bright color
    var $ol_closecolor = _ol_closecolor; # var $ol_closecolor = "";
    // Font face for the main text
    var $ol_textfont = "";
    // Font face for the caption
    var $ol_captionfont = "";
    // Font face for the close text
    var $ol_closefont = "";
    // Font size for the main text. When using CSS this will be very small.
    var $ol_textsize = 0;
    // Font size for the caption. When using CSS this will be very small.
    var $ol_captionsize = 0;
    // Font size for the close text. When using CSS this will be very small.
    var $ol_closesize = 0;
    // Width of the popups in pixels. 100-300 pixels is typical
    var $ol_width = 0;
    // How thick the ol_border should be in pixels. 1-3 pixels is typical
    var $ol_border = "2";
    // How many pixels to the right/left of the cursor to show the popup. Values between 3 and 12 are best
    var $ol_offsetx = 0;
    // How many pixels to the below the cursor to show the popup. Values between 3 and 12 are best
    var $ol_offsety = 0;
    // Default text for popups. Should you forget to pass something to overLIB this will be displayed.
    var $ol_text = "Default Text";
    // Default caption. You should leave this blank or you will have problems making non caps popups.
    var $ol_cap = "";
    // Decides if sticky popups are default. 0 for non, 1 for stickies.
    var $ol_sticky = false;
    // Default background image. Better left empty unless you always want one.
    var $ol_background = "";
    // Text for the closing sticky popups. Normal is "Close".
    var $ol_closetext = "Close"; #var $ol_close = "Close";	# true;
    // Default vertical alignment for popups. It's best to leave RIGHT here. Other options are LEFT and CENTER.
    var $ol_hpos = "RIGHT";
    // Default status bar text when a popup is invoked.
    var $ol_status = "";
    // If the status bar automatically should load either text or caption. 0=nothing, 1=text, 2=caption
    var $ol_autostatus = false;
    var $ol_autostatuscap = false;
    // Default height for popup. Often best left alone.
    var $ol_height = 0;
    // Horizontal grid spacing that popups will snap to. 0 makes no grid, anything else will cause a snap to that grid spacing.
    var $ol_snapx = 0;
    // Vertical grid spacing that popups will snap to. 0 makes no grid, andthing else will cause a snap to that grid spacing.
    var $ol_snapy = 0;
    // Sets the popups horizontal position to a fixed column. Anything above -1 will cause fixed position.
    var $ol_fixx = -1;
    // Sets the popups vertical position to a fixed row. Anything above -1 will cause fixed position.
    var $ol_fixy = 0;
    // Background image for the popups inside.
    var $ol_fgbackground = "";
    // Background image for the popups frame.
    var $ol_bgbackground = "";
    // How much horizontal left padding text should get by default when BACKGROUND is used.
    var $ol_padxl = 0;
    // How much horizontal right padding text should get by default when BACKGROUND is used.
    var $ol_padxr = 0;
    // How much vertical top padding text should get by default when BACKGROUND is used.
    var $ol_padyt = 0;
    // How much vertical bottom padding text should get by default when BACKGROUND is used.
    var $ol_padyb = 0;
    // If the user by default must supply all html for complete popup control. Set to 1 to activate, 0 otherwise.
    var $ol_fullhtml = false;
    // Default vertical position of the popup. Default should normally be BELOW. ABOVE only works when HEIGHT is defined.
    var $ol_vpos = "BELOW";
    // Default height of popup to use when placing the popup above the cursor.
    var $ol_aboveheight = 0;
    // Default icon to place next to the popups caption.
    var $ol_capicon = "images/arrow.gif";
    // Default frame. We default to current frame if there is no frame defined.
    var $ol_frame = "self";
    // Default timeout. By default there is no timeout.
    var $ol_timeout = 0;
    // Default timeout. By default there is no timeout.
    var $ol_delay = 0;
    // Default javascript funktion. By default there is none.
    var $ol_function = "Function()";
    // If overLIB should decide the horizontal placement.
    var $ol_hauto = false;
    // If overLIB should decide the vertical placement.
    var $ol_vauto = false;
    // If the user has to click to close stickies.
    var $ol_closeclick = 0;
    // This variable determines if you want to use CSS or inline definitions. CSSOFF=no CSS CSSSTYLE=use CSS inline styles CSSCLASS=use classes
    var $ol_css = "CSSOFF";
    // Main background class (eqv of fgcolor). This is only used if CSS is set to use classes (ol_css = CSSCLASS)
    var $ol_fgclass = "";
    // Frame background class (eqv of bgcolor). This is only used if CSS is set to use classes (ol_css = CSSCLASS)
    var $ol_bgclass = "";
    // Main font class. This is only used if CSS is set to use classes (ol_css = CSSCLASS)
    var $ol_textfontclass = "";
    // Caption font class. This is only used if CSS is set to use classes (ol_css = CSSCLASS)
    var $ol_captionfontclass = "";
    // Close font class. This is only used if CSS is set to use classes (ol_css = CSSCLASS)
    var $ol_closefontclass = "";
    // Unit to be used for the text padding above. Only used if CSS inline styles are being used (ol_css = CSSSTYLE). Options include "px", "%", "in", "cm" and more
    var $ol_padunit = "px";
    // Unit to be used for height of popup. Only used if CSS inline styles are being used (ol_css = CSSSTYLE). Options include "px", "%", "in", "cm" and more
    var $ol_heightunit = "px";
    // Unit to be used for width of popup. Only used if CSS inline styles are being used (ol_css = CSSSTYLE). Options include "px", "%", "in", "cm" and more
    var $ol_widthunit = "px";
    // Font size unit for the main text. Only used if CSS inline styles are being used (ol_css = CSSSTYLE)
    var $ol_textsizeunit = "px";
    // Decoration of the main text ("none", "underline", "line-through" or "blink"). Only used if CSS inline styles are being used (ol_css = CSSSTYLE)
    var $ol_textdecoration = "none";
    // Font style of the main text ("normal" or "italic"). Only used if CSS inline styles are being used (ol_css = CSSSTYLE)
    var $ol_textstyle = "normal";
    // Font weight of the main text ("normal", "bold", "bolder", "lighter", ect.). Only used if CSS inline styles are being used (ol_css = CSSSTYLE)
    var $ol_textweight = "normal";
    // Font size unit for the caption. Only used if CSS inline styles are being used (ol_css = CSSSTYLE)
    var $ol_captionsizeunit = "px";
    // Decoration of the caption ("none", "underline", "line-through" or "blink"). Only used if CSS inline styles are being used (ol_css = CSSSTYLE)
    var $ol_captiondecoration = "none";
    // Font style of the caption ("normal" or "italic"). Only used if CSS inline styles are being used (ol_css = CSSSTYLE)
    var $ol_captionstyle = "normal";
    // Font weight of the caption ("normal", "bold", "bolder", "lighter", ect.). Only used if CSS inline styles are being used (ol_css = CSSSTYLE)
    var $ol_captionweight = "bold";
    // Font size unit for the close text. Only used if CSS inline styles are being used (ol_css = CSSSTYLE)
    var $ol_closesizeunit = "px";
    // Decoration of the close text ("none", "underline", "line-through" or "blink"). Only used if CSS inline styles are being used (ol_css = CSSSTYLE)
    var $ol_closedecoration = "none";
    // Font style of the close text ("normal" or "italic"). Only used if CSS inline styles are being used (ol_css = CSSSTYLE)
    var $ol_closestyle = "normal";
    // Font weight of the close text ("normal", "bold", "bolder", "lighter", ect.). Only used if CSS inline styles are being used (ol_css = CSSSTYLE)
    var $ol_closeweight = "normal";

    function xoverLib($path = "")
    {
        if (strlen($path)) $this->ol_path = $path ?>
<nolink rel="stylesheet" href="<?php echo "$this->ol_path/overlib.css" ?>" type="text/css">
<div id="overDiv" style="position: absolute; visibility: hidden; z-index: 1000;"></div>
<script src="<?php echo "$this->ol_path/overlib.js" ?>" type="text/javascript"></script>
<script src="<?php echo "$this->ol_path/overlib_hideform.js" ?>" type="text/javascript"></script>
<?php
    }

    function set($var, $value)
    {
        $v = "ol_$var";
        $this->$v = $value;
    }

    function get($var)
    {
        $v = "ol_$var";
        return $this->$v;
    }

    function over($text, $title = "", $status = "", $outtype = "over")
    {
        $cmd = (empty($text)) ? "" : "'$text'";
        if (strlen($status)) $cmd .= ", STATUS, '$status'";
        if (strlen($title)) {
            $cmd .= ", CAPTION, '$title'";
            if (strlen($this->ol_capcolor)) $cmd .= ", CAPCOLOR, '$this->ol_capcolor'";
            if (strlen($this->ol_captionfont)) $cmd .= ", CAPTIONFONT, '$this->ol_captionfont'";
            if ($this->ol_captionsize) $cmd .= ", CAPTIONSIZE, $this->ol_captionsize";
            if (strlen($this->ol_capicon)) $cmd .= ", CAPICON, '$this->ol_capicon'";
        }
        if ($this->ol_sticky) {
            $cmd .= ", STICKY";
            if (strlen($this->ol_closecolor)) $cmd .= ", CLOSECOLOR, '$this->ol_closecolor'";
            if ($this->ol_closeclick) $cmd .= ", CLOSECLICK";
            if ($this->ol_closesize) $cmd .= ", CLOSESIZE, $this->ol_closesize";
            if (strlen($this->ol_closetext)) $cmd .= ", CLOSETEXT, '$this->ol_closetext'";
            if (strlen($this->ol_closefont)) $cmd .= ", CLOSEFONT, '$this->ol_closefont'";
            // if ($this->ol_noclose) $cmd .= ", NOCLOSETEXT";
        }
        if ($this->ol_align) {
            $this->ol_align = strtoupper($this->ol_align);
            $cmd .= ", " . $this->ol_align . "";
        }
        if ($this->ol_valign) { // ABOVE , BELOW
            $this->ol_valign = strtoupper($this->ol_valign);
            $cmd .= ", " . $this->ol_valign . "";
        }
        if (strlen($this->ol_fgbackground)) {
            $cmd .= ", FGCOLOR, '', FGBACKGROUND, '$this->ol_fgbackground'";
        } else {
            if (strlen($this->ol_fgcolor)) {
                $cmd .= ", FGCOLOR, '$this->ol_fgcolor'";
            }
        }
        if (strlen($this->ol_bgbackground)) {
            $cmd .= ", BGCOLOR, '', BGBACKGROUND, '$this->ol_bgbackground'";
        } else {
            if (strlen($this->ol_bgcolor)) {
                $cmd .= ", BGCOLOR, '$this->ol_bgcolor'";
            }
        }

        if (strlen($this->ol_textcolor)) $cmd .= ", TEXTCOLOR, '$this->ol_textcolor'";
        if (strlen($this->ol_textfont)) $cmd .= ", TEXTFONT, '$this->ol_textfont'";
        if (strlen($this->ol_background)) $cmd .= ", BACKGROUND, '$this->ol_background'";
        if ($this->ol_textsize) $cmd .= ", TEXTSIZE, $this->ol_textsize";
        if ($this->ol_width) $cmd .= ", WIDTH, $this->ol_width";
        if ($this->ol_height) $cmd .= ", HEIGHT, $this->ol_height";
        if ($this->ol_border >= 0) $cmd .= ", BORDER, $this->ol_border";
        if ($this->ol_offsetx) $cmd .= ", OFFSETX, $this->ol_offsetx";
        if ($this->ol_offsety) $cmd .= ", OFFSETY, $this->ol_offsety";
        if ($this->ol_autostatus) $cmd .= ", AUTOSTATUS";
        if ($this->ol_autostatuscap) $cmd .= ", AUTOSTATUSCAP";
        if ($this->ol_snapx) $cmd .= ", SNAPX, $this->ol_snapx";
        if ($this->ol_snapy) $cmd .= ", SNAPY, $this->ol_snapy";
        if ($this->ol_fixy) $cmd .= ", FIXY, $this->ol_fixy";
        if ($this->ol_padxl || $this->ol_padxr) $cmd .= ", PADX, $this->ol_padxl, $this->ol_padxr";
        if ($this->ol_padyt || $this->ol_padyb) $cmd .= ", PADY, $this->ol_padyt, $this->ol_padyb";
        if ($this->ol_fullhtml) $cmd .= ", FULLHTML";
        if ($this->ol_timeout > 0) $cmd .= ", TIMEOUT, $this->ol_timeout";
        if ($this->ol_delay > 0) $cmd .= ", DELAY, $this->ol_delay";
        if ($this->ol_hauto) {
            $cmd .= ", HAUTO";
            $this->ol_hauto = false;
        }
        if ($this->ol_vauto) {
            $cmd .= ", VAUTO";
            $this->ol_hauto = false;
        }
        $cmd = trim($cmd, ', ');
        if ($outtype == "click") { // kompletter onClick Aufruf
            $output = " onclick=\"return overlib($cmd); return false;\" ";
        }
        if ($outtype == "cmd") { // nur die Uebergabeparameter
            $output = $cmd;
        } else { // kompletter onMouseOver Aufruf mit onMouseOut
            $output = " onmouseover=\"return overlib($cmd);\" ";
            $output .= " onmouseout=\"nd();\" ";
        }
        return $output;
    }

    function jscompatible($tstring) // add by AE
    {
        if (empty($tstring)) return '';
        $tstring = str_replace("\"", "'", $tstring);
        $tstring = mxNL2BR(addslashes($tstring));
        $tstring = preg_replace("#[[:cntrl:]]{1,}#", " ", $tstring);
        return $tstring;
    }

    function pover ($text, $title = "", $status = "") // changed by AE
    {
        echo $this->over($this->jscompatible($text), $this->jscompatible($title), $this->jscompatible($status), "over");
    }

    function vover ($text, $title = "", $status = "") // changed by AE
    {
        return $this->over($this->jscompatible($text), $this->jscompatible($title), $this->jscompatible($status), "over");
    }
    // aus pragmaMx
    function mxclick ($text, $title = "", $status = "") // changed by AE
    {
        return $this->over($this->jscompatible($text) , $this->jscompatible($title), $this->jscompatible($status), "click");
    }

    function mxover ($text, $title = "", $status = "") // changed by AE
    {
        return $this->over($this->jscompatible($text), $this->jscompatible($title), $this->jscompatible($status), "over");
    }

    function mxcommand ($text, $title = "", $status = "") // changed by AE
    {
        return $this->over($this->jscompatible($text), $this->jscompatible($title), $this->jscompatible($status), "cmd");
    }
} # end class

?>