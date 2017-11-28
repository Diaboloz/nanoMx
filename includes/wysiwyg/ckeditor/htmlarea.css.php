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
 * $Revision: 142 $
 * $Author: PragmaMx $
 * $Date: 2016-05-01 17:57:18 +0200 (So, 01. Mai 2016) $
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
body, body.body-allone,
div.cke_panel_block {
   height: auto!important;
   text-align: left;
}

div.cke_panel_block {
  color: <?php echo $textcolor ?>;
  background-color: <?php echo $bgcolor ?>;
  background-image: none;
}

<?php } else {
    // falls Ja, die bisherigen Einstellungen ueberschreiben
    ?>

#htmlarea, #htmlarea body,
body, body.body-allone,
div.cke_panel_block {
   color: <?php echo $area_foreground ?>!important;
   background-color: <?php echo $area_background ?>!important;
   background-image: none;
   height: auto!important;
   text-align: left;
}

<?php }
// Nachfolgend die Dummy-Styles für das stylesheetparser-Plugin.
// Diese Klassen werden dann in den Selectfeldern angezeigt, aber vom
// globalen Stylesheet formatiert ;)
?>

big.bigger,
del.del,

div.alternate-0,
div.alternate-1,
div.alternate-2,
div.alternate-3,
div.alternate-4,
div.bgcolor1,
div.bgcolor2,
div.bgcolor3,
div.bgcolor4,
div.border,
div.box,
div.code,
div.error,
div.important,
div.indent,
div.info,
div.middot,
div.note,
div.quote,
div.success,
div.warning,
p.content,

img.align-center,
img.align-left,
img.align-right,
img.border,
img.float-left,
img.float-right,
img.margin,

ol.infolist,
ol.list,
ul.infolist,
ul.list,

span.highlight,
span.nowrap,
span.required,
span.smaller,

table.align-center,
table.blind,
table.fixed,
table.full,
table.list,

td.alternate-a,
td.alternate-b,
td.alternate-c,
td.alternate,
td.head,
tr.alternate-a,
tr.alternate-b,
tr.alternate-c,
tr.alternate,
tr.head {  }
