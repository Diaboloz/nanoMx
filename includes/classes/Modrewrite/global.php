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

/* falls die Datei direkt aufgerufen, das include einfach beenden */
if (!defined('PMX_HOME_URL')) return;

$home = preg_quote(PMX_HOME_URL, '!');

/*
der zu suchende reguläre Ausdruck ist der Schlüssel des Arrays
der ersetzende Wert ist der Wert des Arrays
*/

/* index.php mit oder ohne Parameter => index.html */
$replace_arr['!(<a[^>]+href=["\'](?:' . $home . ')?)(?:index\.php|\./)([^"\'>]*)(["\'])!'] = "\\1home.html\\2\\3";

/* die verschiedenen User-Module */
if (MX_IS_USER) {
    $replace_arr['!(<a[^>]+href=["\'](?:' . $home . ')?)modules\.php\?name=Your_Account(["\'])!i'] = "\\1myaccount.html\\2";
    $replace_arr['!(<a[^>]+href=["\'](?:' . $home . ')?)modules\.php\?name=Your_Account(?:[^"\'>]*)op=logout(["\'])!i'] = "\\1log-me-out.html\\2";
    $replace_arr['!(<a[^>]+href=["\'](?:' . $home . ')?)modules\.php\?name=User_Registration(?:[^"\'>]*)(["\'])!i'] = "\\1myaccount.html\\2";
    $replace_arr['!(<a[^>]+href=["\'](?:' . $home . ')?)modules\.php\?name=Your_Account(?:[^"\'>]*)op=edituser(["\'])!i'] = "\\1mydata.html\\2";
    $replace_arr['!(<a[^>]+href=["\'](?:' . $home . ')?)modules\.php\?name=Your_Account(?:[^"\'>]*)op=edithome(["\'])!i'] = "\\1mysettings.html\\2";
} else {
    $replace_arr['!(<a[^>]+href=["\'](?:' . $home . ')?)modules\.php\?name=Your_Account(["\'])!i'] = "\\1log-me-in.html\\2\\3";
    $replace_arr['!(<a[^>]+href=["\'](?:' . $home . ')?)modules\.php\?name=User_Registration([^>"\'%\s]*)(["\'])!i'] = "\\1register-me.html\\3";
    $replace_arr['!(<a[^>]+href=["\'](?:' . $home . ')?)modules\.php\?name=Your_Account&(?:amp;)?op=edit(?:user|home)(["\'])!i'] = "\\1log-me-in.html\\2\\3";
}

?>