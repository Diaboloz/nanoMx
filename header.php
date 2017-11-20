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
 * $Revision: 171 $
 * $Author: PragmaMx $
 * $Date: 2016-06-29 13:59:03 +0200 (Mi, 29. Jun 2016) $
 */

defined('mxMainFileLoaded') or die('access denied');

/**
 * definiert, dass diese Datei bereits includet wurde
 * nicht verändern!
 */
if (defined('PMX_HEADER')) {
    return;
} else {
    define('PMX_HEADER', true);
    /* TODO: $GLOBALS["header"] >> deprecated */
    //$GLOBALS['header'] = 1;
}

if (isset($pagetitle)) {
    // $pagetitle wird in älteren Modulen direkt vor include der header.php definiert
    $GLOBALS['pagetitle'] = $pagetitle;
	pmxBase::pagetitle($pagetitle);
} else {
    // TODO: raus !! nur für nuke-Module noch drin!
    //global $pagetitle;
}

/* aktuelle URL versuchen zu speichern */
pmxDebug::pause();

$currequest = parse_url($_SERVER['REQUEST_URI']);
$lasturl=(empty($currequest['query'])) ? '': basename($currequest['path']) . '?' . $currequest['query'];
mxSessionSetVar('lasturl',$lasturl);

pmxDebug::restore();

/* rechte Blöcke ausblenden oder nicht */
switch (true) {
    case defined('mxAdminFileLoaded'):
        // in der Administration, die Blöcke NIE anzeigen
        $GLOBALS['index'] = 0;
        break;
    case empty($GLOBALS['vkpBlocksRight']):
        // moduldefiniert, nix machen
        break;
    case $GLOBALS['vkpBlocksRight'] == 3:
        // Blöcke nur auf der Startseite anzeigen
        $GLOBALS['index'] = defined('MX_HOME_FILE');
        break;
    case $GLOBALS['vkpBlocksRight'] == 2:
        // Blöcke NIE anzeigen
        $GLOBALS['index'] = 0;
        break;
    case $GLOBALS['vkpBlocksRight'] == 1:
        // Blöcke immer anzeigen
        $GLOBALS['index'] = 1;
        break;
}

if (!defined('MX_MODULE')) {
    // falls die header.php in eine Dazei im pmx-root includet wird,
    // ist diese Konstante nicht definiert und verursacht notices z.B. in den Blöcken
    define('MX_MODULE', '');
}

/* Statistik aktualisieren */
mxCounter();

/* Onlineliste aktualisieren */
online();

/**
 * das theme includen und die dort deklarierten Variablen in den
 * globalen Scope importieren
 */
$themevars = includetheme();
foreach ($themevars as $key => $value) {
    global $$key;
    $$key = $value;
}

/* Kompatibilität mit vkp-Themes, wichtig für blocks() */
/* $VKPTheme = (empty($VKPTheme)) ? false : true;*/ 


/* versch. HTTP Header senden */
if (!headers_sent()) {
	header(pmxHeader::Status()); 
    header('Content-type: text/html; charset=utf-8');
    header('Content-Language: ' . _DOC_LANGUAGE);
    header('X-Powered-By: pragmaMx ' . PMX_VERSION);
    header('X-UA-Compatible: IE=edge;FF=5;chrome=1');
}

/**
 * Die HTML-Ausgabe beginnen
 * Info zum Doctype:
 * - http://carsten-protsch.de/zwischennetz/doctype/einleitung.html
 */
$GLOBALS['DOCTYPE'] = (empty($GLOBALS['DOCTYPE'])) ? 0 : intval($GLOBALS['DOCTYPE']);
$doctype_arr = mxDoctypeArray($GLOBALS['DOCTYPE']);
echo $doctype_arr['value'], "\n";
if ($doctype_arr['xhtml'] && !$doctype_arr['html']) {
    echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="', _DOC_LANGUAGE, '" lang="', _DOC_LANGUAGE, '" dir="', _DOC_DIRECTION, '" >';
} else {
    echo '<html lang="', _DOC_LANGUAGE, '" dir="', _DOC_DIRECTION, '" >';
}

/* guggen, was f?r ein Browser am werkeln ist */
$browser = load_class('Browser');
/* remove nanomx no support ie7 PNG-Fix für alten IE < 7 
if ($browser->msie && $browser->version < 7) {
    pmxHeader::add_script(PMX_JAVASCRIPT_PATH . 'iepngfix/iepngfix_tilebg.js', 'lt IE 7');
}
*/

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

if (MX_IS_ADMIN && defined('mxAdminFileLoaded')) {
	 /*nanomx add */
	 $stylesheet = '';
     //nanomx  remove $stylesheet = 'style/style.css.php?t=' . MX_THEME;
    } else {
        $stylesheet = 'style/_theme.' . MX_THEME . $agent . '.css';
    }
	
if (!empty($GLOBALS['mxUseThemecache']) && file_exists(PMX_LAYOUT_DIR . DS . $stylesheet)) {
    /* Achtung: Dateinamensabgleich mit default.css.php  __construct */
    $stylesheet = PMX_LAYOUT_PATH . $stylesheet;
} else {
    $stylesheet = PMX_LAYOUT_PATH . 'style/style.css.php?t=' . MX_THEME;
}

/**
 * der HTML-Beginn und Seitentitel
 * die Metatags
 */
?>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<?php if (pmxHeader::get_meta("canonical")) { echo "<link rel=\"canonical\" href=\"" . pmxHeader::get_meta("canonical") . "\" />\n"; } ?>
<title><?php echo pmxHeader::get_meta('title') ?></title>
<?php if (defined('MX_HOME_FILE')) { echo "<meta name=\"description\" content=\"" . pmxHeader::get_meta("description") . "\" />\n"; } ?>
<?php if (pmxHeader::get_meta("viewport")) { echo "<meta name=\"viewport\" content=\"" . pmxHeader::get_meta("viewport") . "\" />\n"; } ?>
<?php if (pmxHeader::get_meta("alternate")) { echo pmxHeader::get_meta("alternate"); } ?>

<link rel="stylesheet" href="<?php echo $stylesheet ?>" type="text/css" />

<?php if (file_exists("favicon.ico")) { echo "<link rel=\"shortcut icon\" href=\"" . PMX_HOME_URL . "/favicon.ico\" type=\"image/x-icon\" />\n"; }

/* zusätzliche head-Tags aus den Modulen anfügen */
$hook = load_class('Hook', 'prepare.header');
$hook->run();

/**
 * Zusätze aus den Modulen und Themes
 * Blöcke werden evtl. erst in der footer.php berücksichtigt,
 * dort wird die Funktion nochmals aufgerufen, falls etwas dazu gekommen ist.
 *
 * die globale CSS-Datei des Themes wird hierbei auch eingebunden
 */
pmxHeader::show();

/**
 * HTML-Header Bereich beenden
 * - weiter geht es mit dem body-Tag in der Funktion themeheader()
 */
echo "\n</head>\n";

/**
 * den Siteservice als erstes anzeigen
 */
mxSiteService();

/**
 * der Seitenkopf des Themes,
 * evtl. mit Ausgabe der linken Blöcke in nuke-styled Themes
 */
themeheader();

/* Adminmenü anzeigen, falls benötigt */
if (defined('mxAdminFileLoaded')) {
    GraphicAdmin();
}

/* bei theme-Enine Themes kann hier beendet werden */
if (function_exists('mx_theme_engineversion')) {
    return true;
}

/**
 * in nicht Theme-Engine Themes, den body-Tag korrigieren,
 * dabei auch den Siteservice etc. an die richtige Stelle setzen
 */
$header = ob_get_contents();
if ($header && preg_match('#<body([^>]*)>#i', $header, $matches)) {
    ob_end_clean();
    ob_start();
	
    $adds = $matches[1];
    if ($adds && preg_match('#(class\s*=\s*)(["\'])([^"\']*)\2#i', $adds, $class)) {
        $adds = str_replace($class[0], 'class="bodymain ' . $class[3] . '"', $adds);
    } else {
        $adds = 'class="bodymain"' . $adds;
    }
    $header = str_replace($matches[0], '', $header);
    $header = str_replace('</head>', '</head><body ' . $adds . ' >', $header);
    echo $header;
}

/**
 * Ausgabe der oberen Center-Blöcke in nuke-styled Themes
 */
if (defined('MX_HOME_FILE') && !$VKPTheme) {
    blocks('center');
}

/* __________________________________________________________________________ */

/**
 * kompatibilität zu < VKP-Maxi-themes
 * In pragmaMx Themes existiert normalerweise diese Funktion und wird stattdessen verwendet
 */
if (!function_exists('OpenTableAl')) {
    function OpenTableAl()
    {
        echo '<div class="alert alert-warning">';
    }
}
/**
 * kompatibilität zu < VKP-Maxi-themes
 * In pragmaMx Themes existiert normalerweise diese Funktion und wird stattdessen verwendet
 */
if (!function_exists('CloseTableAl')) {
    function CloseTableAl()
    {
        echo '</div>';
    }
}
$plugins=1;
?>