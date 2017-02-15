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

if (!($mainfile = realpath(dirname(__file__) . '/../../../mainfile.php'))) {
    die('mainfile missing...');
}
include_once($mainfile);

/* pragmaMx Version unterscheiden */
$evolution = false; // nö, hier noch nicht..., class_exists('pmx', false);

/* ermitteln ob Theme angegeben wurde */
if (isset($_GET['t'])) {
    $theme = htmlspecialchars($_GET['t']);
} else {
    $theme = MX_THEME;
}

/* Cache ignorieren? */
switch (true) {
    // case $evolution && !pmx::$conf->themecache:
    case (!$evolution) && (!$GLOBALS['mxUseThemecache']):
        $skipcache = '&skipcache';
        break;
    default:
        $skipcache = '';
        // header("HTTP/1.1 304 Not Modified");
}

/* CSS Header senden */
header('Content-Type: text/css; charset=utf-8');
header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 3600 * 24) . ' GMT'); // 1 day
header('Etag: ' . md5($theme . $skipcache . 'irgendwas'));

load_class('Textarea', false);
$wyscnf = pmxTextarea::get_config();

/* guggen, was für ein Browser am werkeln ist */
$browser = load_class('Browser');

/* dynamisches Stylesheet festlegen */
switch (true) {
    case $browser->is_msie() && $browser->version < 8:
        $agent = '.msie' . $browser->version;
        break;
    case $browser->is_gecko():
        $agent = '.gecko';
        break;
    default:
        $agent = '';
}
$stylesheet = 'style/_theme.' . MX_THEME . $agent . '.css';
if (!$skipcache && file_exists(PMX_LAYOUT_DIR . DS . $stylesheet)) {
    /* Achtung: Dateinamensabgleich mit default.css.php  __construct */
    $stylesheet = PMX_BASE_PATH . PMX_LAYOUT_PATH . $stylesheet;
} else {
    $stylesheet = PMX_BASE_PATH . PMX_LAYOUT_PATH . 'style/style.css.php?t=' . MX_THEME . $skipcache;
}

/* Charset fue Stylesheet */
echo "@charset \"utf-8\";\n";

/* das Default-Stylesheet laden, da nur 't' uebergeben wird */
echo "@import url('", $stylesheet, "');\n";

/* das normale stylesheet des aktuellen themes laden */
if ($theme) {
    echo "@import url('", PMX_BASE_PATH, "themes/", $theme, "/style/style.css');\n";
}

if ($evolution) {
    $area_background = $wyscnf['area_background'];
    $area_foreground = $wyscnf['area_foreground'];
} else {
    $area_background = $wyscnf['globals']['area_background'];
    $area_foreground = $wyscnf['globals']['area_foreground'];
}

/* check, ob spezielle Farben fuer den wysiwyg Bereich angegeben wurden */
if (!$area_background || !$area_foreground) {
    if ($evolution) {
        load_class('Theme', false);
        $themevars = pmxTheme::get_colors(MX_THEME);
        $bgcolor = $themevars['alternate-0'];
        $textcolor = $themevars['textcolor2'];
    } else {
        $olddir = getcwd();
        // zum root wechseln, damit MX_THEME_DIR richtig erkannt wird
        chdir(PMX_REAL_BASE_DIR);
        $themevars = includetheme(true);
        $bgcolor = $themevars['bgcolor3'];
        $textcolor = $themevars['textcolor2'];
        chdir($olddir);
    }
    // die Klasse .cke_panel_block gehört zu den Select-Listen des ckEditor
    // hier wird normalerweise die Hintergrundfarbe des <bod> Tags verwendet, was
    // zu Fehlanzeigen kommen kann, deswegen hier überschreiben
    ?>

#htmlarea, #htmlarea body,
body, body.body-allone, body#htmlarea {
   height: auto!important;
   text-align: left;
   background-color: #FEFEFE;
}

<?php
    return;
}
// falls Ja, die bisherigen Einstellungen ueberschreiben
?>

#htmlarea, #htmlarea body,
body, body.body-allone {
   color: <?php echo $area_foreground ?>!important;
   background-color: <?php echo $area_background ?>!important;
   background-image: none;
   height: auto!important;
   text-align: left;
}

