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

define("_CAPTCHASAMPLE", "Sample");
define("_CAPTCHATITLE", "Captcha  -  settings");
define("_CAPTCHAIMAGEWIDTH", "Image width");
define("_CAPTCHAIMAGEHEIGHT", "Image height");
define("_CAPTCHAFONTSIZE", "Fontsize (foreground)");
define("_CAPTCHABGINTENSITY", "Background intensity");
define("_CAPTCHABGFONTTYPE", "Fontsize (background)");
define("_CAPTCHASCRATCHAMOUNT", "Scratches amount");
define("_CAPTCHAPASSPHRASELENGHT", "Text lengh");
define("_CAPTCHAFILTER", "Use deformation filter");
define("_CAPTCHASCRATCHES", "Use scratches");
define("_CAPTCHASAVESETTINGS", "Save settings");
define("_CAPTCHAFILTERTYPE", "Deformation filter type");
define("_CAPTCHAADDHORLINES", "All color lines");
define("_CAPTCHAADDAGRID", "Add bars");
define("_CAPTCHARANDOMCOLOR", "Use random colors");
define("_CAPTCHAANGLE", "Font angle");
define("_CAPTCHAMINSIZE", "Minimal bar distance");
// define("_CAPTCHAFEEDBACKON", "Active in Feedback modul");
// define("_CAPTCHAFAQON", "Active in FAQ-Modul");
// define("_CAPTCHAWEBLINKSON", "Active in Weblinks modul");
// define("_CAPTCHADOWNLOADSON", "Active in Downloads modul");
// define("_CAPTCHADOCUMENTSON", "Aktivieren in den Documents");
// define("_CAPTCHANEWSON", "Active in News modul");
// define("_CAPTCHANEWSLETTERON", "Active in Newsletter modul");
// define("_CAPTCHAGUESTBOOKON", "Active in Guestbook modul");
// define("_CAPTCHAREVIEWSON", "Active in Reviews modul");
define("_CAPTCHAUSERON", "Activate Captcha for registered users as well");
define("_CAPTCHAREGISTRATIONON", "Userregistration");
// define("_CAPTCHARECOMMENDON", "Active in '" . _RECOMMEND . "' modul");
define("_CAPTCHACOMMENTSON", "comments");
define("_CAPTCHAANSWERSUSE", "Show answers");
define("_CAPTCHAANSWERSCOUNT", "Number of given answers");
define("_CAPTCHADIGITSRANGE1", "Range of numbers from");
define("_CAPTCHADIGITSRANGE2", "to");
define("_CAPTCHACALCSTEPS", "Numbers of calculation steps");
define("_CAPTCHAERRORINGD", "Image captchas cannot be generated, because your PHP installation's GD library has no JPEG support.");
define("_CAPTCHACHARSTOUSE", "Characters to use in the code");
define("_CAPTCHACHARCASESEN", "Inputs are case sensitive");
define("_CAPTCHAERR", "The Captcha picture cannot be indicated possibly correctly, because the following problem exists:");
define("_CAPTCHAERR_MISSINGGD", "The GD-library is not installed probably, or in the wrong version, at least version 2.0 is needed. (<a href=\"http://www.php.net/manual/ref.image.php\">info</a>)");
define("_CAPTCHAERR_FALSEFT", "The GD-library either is not installed, or those FreeType support is not configured.");
define("_CAPTCHAERR_FALSEGD", "It is probably an incompatible version of the GD-library, at least version 2.0 is needed. (<a href=\"http://www.php.net/manual/ref.image.php\">info</a>)");
define("_CAPTCHAERR_NOJPG", "The JPG support of the GD-library probably is not available.");
define("_CAPTCHAERR_MISSINGFT", "The FreeType support of the GD-library probably is not available.");
define("_CAPTCHASETTINGS", "Settings");
define("_CAPTCHASESSION", "Check spelling only once per session");

define("_CAPTCHAMODSET", "Enable the following modules and sections:");
define("_CAPTCHAMODHAVEOWN", "The following modules use their own settings to enable Captcha's:");
define("_CAPTCHASETTINGS2", "Activation");
define("_CAPTCHASETRESET", "Ignore all and reset to system defaults");

?>