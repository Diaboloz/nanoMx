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
 * $Revision: 228 $
 * $Author: module-factory $
 * $Date: 2016-09-29 07:25:39 +0200 (Do, 29. Sep 2016) $
 */

defined('mxMainFileLoaded') or die('access denied');

/**
 * includetheme()
 * die theme.php des eingestellten Themes includen
 *
 * @param mixed $dont_init
 * @return
 */
function includetheme($dont_init = false)
{
    static $themevars;
    if (!isset($themevars)) {
        if ($dont_init && !defined('DONT_INIT_THEME')) {
            // theme-Engine nicht laden
            define('DONT_INIT_THEME', true);
        }
        ob_start();
        $themeok = include_once(MX_THEME_DIR . "/theme.php");
        ob_end_clean();
        if (!$themeok) @die("<br /><b>Error</b>: the theme '" . MX_THEME . "' is corrupted!<br /><br />");
        $themevars = get_defined_vars();
        unset($themevars['GLOBALS'], $themevars['themevars'], $themevars['theme_template'], $themevars['themeok']);
        // Standardfarben die im System und den Modulen verwendet werden
        // falls nicht vorhanden, versch. Graustufen verwenden
        /**
         * Define colors for your web site. $bgcolor2 is generaly
         * used for the tables border as you can see on OpenTable()
         * function, $bgcolor1 is for the table background and the
         * other two bgcolor variables follows the same criteria.
         * $texcolor1 and 2 are for tables internal texts
         */
        $requred = array('textcolor1' => '#2F2F2F',
            'textcolor2' => '#5F5F5F',
            'bgcolor1' => '#F0F0F0',
            'bgcolor2' => '#D0D0D0',
            'bgcolor3' => '#E0E0E0',
            'bgcolor4' => '#C0C0C0',
            );
        foreach ($requred as $key => $value) {
            // unset($themevars[$key]);
            if (!isset($themevars[$key])) {
                $themevars[$key] = $value;
            }
        }
        // mxDebugFuncVars($themevars);
    }
    return $themevars;
}

if (!function_exists('printOverlibDiv')) {
    /**
     * printOverlibDiv()
     * den benoetigten Code fuer die Klasse overlib nur einmal ausgeben
     *
     * @return
     */
    function printOverlibDiv()
    {
        if (!defined('_Overlib_Div')) {
            define('_Overlib_Div', 1);
            pmxHeader::add_script(PMX_JAVASCRIPT_PATH . 'overlib.js');
            pmxHeader::add_script(PMX_JAVASCRIPT_PATH . 'overlib_hideform.js');
            include_once(PMX_JAVASCRIPT_DIR . DS . 'class.overlib.php');
            echo '<div id="overDiv" style="position: absolute; visibility: hidden; z-index: 1000; background: transparent;"></div>', "\n", '<!-- overLIB (c) Erik Bosrup -->', "\n";
        }
        return true;
    }
}

/**
 * mxErrorScreen()
 * Standard-Fehlerseite
 *
 * @param string $msg
 * @param string $title
 * @param integer $goback
 * @return
 */
function mxErrorScreen($msg = '', $title = 'Error', $goback = 1)
{
    if (!defined('MX_MODULE')) {
        // falls die Funktion bereits aufgerufen wird, bevor die Konstante
        // definiert ist, z.B. bei Fehlern in der modules.php
        define('MX_MODULE', '');
    }
    if ($title == 'Error' && defined('_ERROROCCURS')) {
        $title = _ERROROCCURS;
    }
    if ($title) {
        $GLOBALS['pagetitle'] = "- $title";
    }

    $goback = ($goback) ? _GOBACK : '';

    $template = load_class('Template');
    $template->init_path(__FILE__);
    $template->assign(compact('title', 'msg', 'goback'));

    include_once('header.php');
    $template->display('errorscreen.html');
    include_once('footer.php');
    die();
}

/**
 * mxErrorScreen()
 * Standard-Fehlerseite
 *
 * @param string $msg
 * @param string $title
 * @param integer $goback
 * @return
 */
function mxError($msg = '',$errcode=NULL , $title = 'Error', $goback = 1)
{
    if (!defined('MX_MODULE')) {
        // falls die Funktion bereits aufgerufen wird, bevor die Konstante
        // definiert ist, z.B. bei Fehlern in der modules.php
        define('MX_MODULE', '');
    }
    if ($title == 'Error' && defined('_ERROROCCURS')) {
        $title = _ERROROCCURS;
    }
    if ($title) {
        $GLOBALS['pagetitle'] = "- $title";
    }

    $goback = ($goback) ? _GOBACK : '';

    $template = load_class('Template');
    $template->init_path(__FILE__);
    $template->assign(compact('title', 'msg', 'goback','errcode'));

	pmxHeader::Status($errcode);
    include_once('header.php');
    $template->display('error.html');
    include_once('footer.php');
    die();
}


/**
 * mxMessageScreen()
 * Standard-Meldungsseite
 *
 * @param mixed $msg
 * @param string $title
 * @param integer $goback
 * @return
 */
function mxMessageScreen($msg, $title = '', $goback = 1)
{
    if ($title) {
        $GLOBALS['pagetitle'] = "- $title";
    }

    $goback = ($goback) ? _GOBACK : '';

    $template = load_class('Template');
    $template->init_path(__FILE__);
    $template->assign(compact('title', 'msg', 'goback'));

    include_once('header.php');
    $template->display('messagescreen.html');
    include_once('footer.php');
    die();
}

/**
 * mxSiteServiceMessage()
 * Formatierung fuer die Site-Service Message
 *
 * @param mixed $message
 * @param integer $width
 * @return
 */
function mxSiteServiceMessage($message, $width = 60)
{
    /* Achtung! immer Prozentangabe */
    $width = (intval($width)) ? intval($width) : 60;

    $template = load_class('Template');
    $template->init_path(__FILE__);
    $template->assign(compact('message', 'width'));
    return $template->fetch('siteservicemessage.html');
}

/**
 * mxDebugInfo()
 * Debug-Info ausgeben
 *
 * @return
 */
function mxDebugInfo()
{
    /* verhindern dass die Funktion mehrfach ausgeführt werden kann */
    static $shown = false;
    if ($shown) {
        return '';
    }
    $shown = true;

    $out = '';

    if (MX_IS_ADMIN && $mode = pmxDebug::is_debugmode()) {
        $template = load_class('Template');
        $template->init_path(__FILE__);
        $template->view_all = ($mode == VIEW_ALL);
        $out .= $template->fetch('debuginfo1.html');
    }

    if (pmxDebug::is_mode('enhanced')) {
        $notices = '';
        if (pmxDebug::is_error() && pmxDebug::is_mode('screen') && $cont = pmxDebug::format_errors()) {
            $notices = $cont;
        }
        $request = ($cont = pmxDebug::format_request()) ? $cont : '';
        $queries = ($cont = pmxDebug::format_queries()) ? $cont : '';

        if (!isset($template)) {
            $template = load_class('Template');
            $template->init_path(__FILE__);
        }

        $template->assign(compact('notices', 'request', 'queries'));
        $out .= $template->fetch('debuginfo2.html');
    }

    return $out;
}

/**
 * title()
 *
 * @param mixed $content
 * @return
 */
function title($title)
{
    $template = load_class('Template');
    $template->init_path(__FILE__);
    $template->title = $title;
    $template->display('title.html');
}

/**
 * vkpIpBanning()
 *
 * @return
 */
function vkpIpBanning()
{
    global $prefix;
    $ban_ip_found = false;
    $result = sql_system_query("SELECT ban_val FROM " . $prefix . "_user_ban WHERE ban_type='ban_ip' AND ban_val='" . MX_REMOTE_ADDR . "'");
    list($ban_val) = sql_fetch_row($result);
    if ($ban_val == MX_REMOTE_ADDR || mxSessionGetVar("blocked")) {
        mxSessionDelVar("user");
        //mxSetNukeCookie("user");
        mxSessionDelVar("admin");
       //mxSetNukeCookie("admin");

        pmx_show_banned(_BANNED);
        die();
    }
}

/**
 * pmx_show_banned()
 *
 * @param string $message
 * @return
 */
function pmx_show_banned($message = '')
{
    global $sitename, $adminmail;

    if (!$message) {
        $message = _BANNED;
    }

    if (!defined('MX_THEME')) {
        $themes = load_class("Config","pmx.themes");
        define('MX_THEME', $themes->defaulttheme);
    }

    defined('_DOC_LANGUAGE') OR define('_DOC_LANGUAGE', 'de');
    defined('_DOC_DIRECTION') OR define('_DOC_DIRECTION', 'ltr');

    header('Content-type: text/html; charset=utf-8');
    header('Content-Language: ' . _DOC_LANGUAGE);
    header('X-Powered-By: pragmaMx ' . PMX_VERSION);

    $template = load_class('Template');
    $template->sitename = $sitename;
    $template->adminmail = mxPrepareToDisplay($adminmail);
    $template->message = $message;
    $template->init_path(__FILE__);
    $template->display('banned.html');
    die();
}

/**
 * mxViewBench()
 *
 * @return
 */
function mxViewBench()
{
    $vkpendtime = microtime();
    $vkpendtime = explode(" ", $vkpendtime);
    $vkpendtime = $vkpendtime[1] + $vkpendtime[0];
    $difftime = round($vkpendtime - MX_TIME, 4);
    return _VKPBENCH1 . " " . $difftime . " " . _VKPBENCH2 . " " . pmxDebug::querycount() . " " . _VKPBENCH3;
}

/**
 * mxThemeShowDate()
 *
 * @return
 */
function mxThemeShowDate()
{
    $months = array(// array of month names
        _JANUARY,
        _FEBRUARY,
        _MARCH,
        _APRIL,
        _MAY,
        _JUNE,
        _JULY,
        _AUGUST,
        _SEPTEMBER,
        _OCTOBER,
        _NOVEMBER,
        _DECEMBER,
        );

    $template = load_class('Template');
    $template->init_path(__FILE__);
    $template->months = $months;
    return $template->fetch('themeshowdate.html');
}

/**
 * footmsg()
 * sollte im Theme aufgerufen werden
 *
 * @param integer $as_variable
 * @param integer $as_table
 * @return
 */
function footmsg($as_variable = 0, $as_table = 0)
{
    $entries = array();
    for($i = 1; $i <= 4; $i++) {
        if ($GLOBALS['foot' . $i]) {
            if (defined($GLOBALS['foot' . $i])) {
                $entries[$i] = constant($GLOBALS['foot' . $i]);
            } else {
                $entries[$i] = $GLOBALS['foot' . $i];
            }
        }
    }
    $out = '';
    if ($entries) {
        $template = load_class('Template');
        $template->init_path(__FILE__);
        $template->as_table = $as_table;
        $template->entries = $entries;
        $out = $template->fetch('footmsg.html');
    }

    if ($as_variable) {
        return $out;
    }
    echo $out;
}

/**
 * mxLanguageSelect()
 * erstellt ein <select> Feld zur Auswahl der Sprache
 *
 * @param mixed $selectname
 * @param string $selectlang
 * @param string $folder
 * @param integer $withempty
 * @param string $morepara
 * @return
 */
function mxLanguageSelect($selectname, $selectlang = '', $folder = 'language', $withempty = 0, $morepara = '')
{
    $languageslist = mxGetAvailableLanguages();
    if ($withempty) {
        $languageslist['- ' . _ALL] = '';
        ksort($languageslist);
    }
    $options = array();
    foreach($languageslist as $alt => $value) {
        $options[] = '<option value="' . $value . '"' . (($value == $selectlang) ? ' selected="selected" class="current"' : '') . ' >' . $alt . '</option>';
    }
    return '<select name="' . $selectname . '" ' . $morepara . '>' . implode("\n", $options) . '</select>';
}

/**
 * pmxTimezoneSelect()
 * erstellt ein <select> Feld zur Auswahl der Zeitzonen
 *
 * @param mixed $selectname , Name des Feldes
 * @param string $default , Vorgabewert
 * @param string $morepara , mehr HTML-Parameter für das Feld
 * @return string
 */
function pmxTimezoneSelect($selectname, $default = '', $morepara = '')
{
    if (!$default) {
        $default = date_default_timezone_get();
    }

    $zones = timezone_identifiers_list();
    sort($zones);

    $continents = array('Africa', 'America', 'Antarctica', 'Arctic', 'Asia', 'Atlantic', 'Australia', 'Europe', 'Indian', 'Pacific');
    $options = array();

    foreach ($zones as $thiszone) {
        $zone = explode('/', $thiszone); // 0 => Continent, 1 => City
        if (in_array($zone[0], $continents) && isset($zone[1])) {
            $options[$zone[0]][] = '<option value="' . $thiszone . '"' . (($thiszone == $default) ? ' selected="selected" class="current"' : '') . '>' . str_replace('_', ' ', $thiszone) . '</option>';
        }
    }

    $out = '';
    foreach ($options as $continent => $values) {
        $out .= '<optgroup label="' . $continent . '">' . implode('', $values) . '</optgroup>';
    }

    if ($out) {
        $out = '<select name="' . $selectname . '" ' . $morepara . '>' . $out . '</select>';
    }

    return $out;
}

/**
 * mxRedirectMessage()
 * header redirect
 *
 * @param mixed $url
 * @param mixed $message
 * @param string $debug
 * @param integer $delay
 * @return
 */
function mxRedirectMessage($url, $message, $debug = '', $delay = 0)
{
    $url_html = mx_urltohtml($url);
    $url_js = strtr($url, array('&amp;' => '&', ' ' => '%20', ';' => '%3b'));

    if (!headers_sent()) {
        header('Content-type: text/html; charset=utf-8');
        header('Content-Language: ' . _DOC_LANGUAGE);
        header('X-Powered-By: pragmaMx ' . PMX_VERSION);
    }

    pmxDebug::pause();
    if ($delay > 3) {
        pmxHeader::add_jquery('jquery.epiclock.js');
        pmxHeader::add_style_code('.epiclock{cursor:wait;} .epiclock-digit{font-weight:bold;}');
    } else {
        header("Refresh: $delay; URL=" . strtr($url_js, array('&amp;' => '&', ' ' => '%20', ';' => '%3b')));
        header("Cache-Control: no-cache");
    }
    pmxDebug::restore();

    $template = load_class('Template');
    $template->init_path(__FILE__);
    $template->assign(compact('url', 'message', 'debug', 'delay', 'url_html', 'url_js'));
    $template->display('redirectpage.html');
}

/**
 * mxDoctypeArray()
 * stellt ein Array mit den verfügbaren DOCTYPE's zur Verfügung
 *
 * @param mixed $doctype
 * @return
 */
function mxDoctypeArray($doctype = null)
{
    // http://tidy.sourceforge.net/docs/quickref.html#doctype
    $types = array(/* versch. DocTypes... */
        0 => array('name' => 'HTML 4.01 Transitional, Quirks Mode',
            'value' => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">',
            'tidy_doctype' => 'transitional',
            'html' => true,
            'xhtml' => false,
            ),
        1 => array('name' => 'HTML 4.01 Transitional, Almost Standards Mode',
            'value' => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"' . "\n  " . '"http://www.w3.org/TR/html4/loose.dtd">',
            'tidy_doctype' => 'transitional',
            'html' => true,
            'xhtml' => false,
            ),
        2 => array('name' => 'HTML 4.01 Strict, Full Standards Mode',
            'value' => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"' . "\n  " . '"http://www.w3.org/TR/html4/strict.dtd">',
            'tidy_doctype' => 'strict',
            'html' => true,
            'xhtml' => false,
            ),
        3 => array('name' => 'XHTML 1.0 Transitional, Almost Standards Mode',
            'value' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"' . "\n  " . '"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">',
            'tidy_doctype' => 'transitional',
            'html' => false,
            'xhtml' => true,
            ),
        4 => array('name' => 'XHTML 1.0 Strict, Full Standards Mode',
            'value' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"' . "\n  " . '"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">',
            'tidy_doctype' => 'strict',
            'html' => false,
            'xhtml' => true,
            ),
        5 => array('name' => 'XHTML 1.1, Full Standards Mode',
            'value' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"' . "\n  " . '"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">',
            'tidy_doctype' => 'strict',
            'html' => false,
            'xhtml' => true,
            ),
        6 => array('name' => 'HTML 5.0 (experimental, only for development)',
            'value' => '<!DOCTYPE html>',
            'tidy_doctype' => 'strict',
            'html' => true,
            'xhtml' => true,
            ),
        );
    if ($doctype === null) {
        return $types;
    } else {
        return $types[intval($doctype)];
    }
}

/**
 * make_clickable()
 * URL's im Text finden und klickbar machen
 *
 * @param mixed $text
 * @return
 */
function make_clickable($text)
{
    $replacer = array(/* replaces */
        '#(^|[>[:space:]\n])([[:alnum:]]+)://([^\'"\s<]*)([[:alnum:]\#?/&=])([<[:space:]\n]|$)#mi' => '$1<a href="$2://$3$4" target="_blank">$2://$3$4</a>$5',
        '#(^|[\s])([\w]+?://[^\'"\s<]*)#is' => '$1<a href="$2" target="_blank">$2</a>',
        '#(^|[\s])((www|ftp)\.[^\'"\s<]*)#is' => '$1<a href="http://$2" target="_blank">$2</a>',
        "#(^|[\s])([a-z0-9&\-_.]+?)@([\w\-]+\.([\w\-\.]+\.)*[\w]+)#i" => '$1<a href="mailto:$2@$3">$2@$3</a>',
        );
    $text = preg_replace(array_keys($replacer), array_values($replacer), $text);
    return $text;
}

/**
 * pmxSigBbCodeGetsize()
 * nur Hilfsfunktion zu pmxSigBbCode()
 *
 * @param array $args
 * @return string
 */
function pmxSigBbCodeGetsize($args)
{
    list($tmp, $size, $text) = $args;

    $sizes = array(1 => 'xx-small', // winzig
        2 => 'x-small', // sehr klein
        3 => 'small', // klein
        4 => 'medium', // mittel
        5 => 'large', // groß
        6 => 'x-large', // sehr groß
        7 => 'xx-large', // riesig
        );
    if (!$text || !$size || !isset($sizes[$size])) {
        return '';
    }
    return '<span style="font-size:' . $sizes[$size] . '">' . $text . '</span>';
}

/**
 */
/**
 * pmxSigBbCode()
 * bbCode in Usersignaturen interpretieren.
 * diese Funktion stammt aus dem mxBoard und ergaenzt das alte Teil aus den PM's ;)
 *
 * @param mixed $message
 * @return
 */
function pmxSigBbCode($message)
{
    // eventuelle Zeilenumbrüche bei bestimmten bbCode-Tags entfernen, damit nl2br keine zusätzlichen anfügt...
    $message = preg_replace('#[[:cntrl:]]?(\[/?(?:p|li|center|blink|strike|h[3-7]|marquee|quote|list(?:=[1Aa])?)\])[[:cntrl:]]?#is', '\1', $message);

    $message = preg_replace_callback('#\[size=([1-7])\](.*)(\[\/size\]|$)#isU', 'pmxSigBbCodeGetsize', $message);
    $message = preg_replace("/\[color=([^\[]*)\](.*)(\[\/color\]|$)/isU", '<span style="color:\1;background-color:transparent;">\2</span>', $message);
    $message = preg_replace("/\[font=([^\[]*)\](.*)(\[\/font\]|$)/isU", '<span style="font-family:\1">\2</span>', $message);
    $message = preg_replace("/\[align=([^\[]*)\](.*)(\[\/align\]|$)/isU", '<p align="\1">\2</p>', $message);
    // unterstrichen extra behandeln
    $message = preg_replace('#\[([u])\]#is', '<u style="text-decoration: underline;">', $message);
    // Formatierungen, fett, unterstrichen, kursiv, absatz
    $message = preg_replace('#\[(/?[abiup])\]#is', '<\1>', $message);
    $message = preg_replace('#\[(/?(?:center|blink|strike|li|strong|h[1-7]|sup|marquee))\]#is', '<\1>', $message);
    $message = preg_replace("/(^|[>[:space:]\n])([[:alnum:]]+):\/\/([^'\"\s<]*)([[:alnum:]#?\/&=])([<[:space:]\n]|$)/mi", "\\1<a rel=\"nofollow\" href=\"\\2://\\3\\4\" target=\"_blank\">\\2://\\3\\4</a>\\5", $message);
    $message = str_replace("[list]", "<ul type=\"square\">", $message);
    $message = str_replace("[/list]", "</ul>", $message);
    $message = preg_replace('#\[list=([1Aa])\]#i', '<ol type="\1">', $message);
    $message = preg_replace('#\[/?list=([1Aa])\]#i', '</ol>', $message);
    $message = str_replace("[*]", "<li>", $message);
    $message = str_replace("[line]", "<hr width=\"95%\" size=\"1\" noshade=\"noshade\"/>", $message);
    $message = str_replace("[hr]", "<hr width=\"95%\" size=\"1\"  noshade=\"noshade\"/>", $message);
    $message = preg_replace("/\[img(?:[^\]]*)\]([^\[]*)\[\/img\]/i", "<img src=\"\\1\" border=\"0\" alt=\"\"/>", $message);
    // # Img-Link fix
    $patterns = array();
    $replacements = array();
    $patterns[0] = "/\[url\]www.([^\[]*)\[\/url\]/i";
    $replacements[0] = "<a href=\"http://www.\\1\" target=\"_blank\">\\1</a>";
    $patterns[1] = "/\[url\]([^\[]*)\[\/url\]/i";
    $replacements[1] = "<a href=\"\\1\" target=\"_blank\">\\1</a>";
    $patterns[2] = "/\[url=([^\[]*)\]([^\[]*)\[\/url\]/i";
    $replacements[2] = "<a href=\"\\1\" target=\"_blank\">\\2</a>";
    $patterns[3] = "/\[email\]([^\[]*)\[\/email\]/i";
    $replacements[3] = "<a href=\"mailto:\\1\">\\1</a>";
    $patterns[4] = "/\[email=([^\[]*)\]([^\[]*)\[\/email\]/i";
    $replacements[4] = "<a href=\"mailto:\\1\">\\2</a>";
    $message = preg_replace($patterns, $replacements, $message);

    return $message;
}

/**
 * Generiert einen DIV-Container mit scrollendem Inhalt
 *
 * Um das Scrollen zu erzeugen wird die JavaScript-Library jscroller2
 * von http://jscroller.markusbordihn.de/
 * Copyright (c) 2008 Markus Bordihn (markusbordihn.de) genutzt
 *
 * @param string $content Inhalt, der gescrolled ausgegeben werden soll
 * @param string $direction optional, Richtung des Scrollens
 * - up = aufwaerts, down = abwaerts, left = von rechts nach links, right = von links nach rechts, default up
 * @param string $speed optional, Geschwindigkeit fuer das Scrolling, default 10
 * @param int $height optional, Hoehe in Pixel des scrollenden DIV-Containers, default 100px
 * @param int $width optional, Breite in Pixel des scrollenden DIV-Containers, default 0px (= 100%)
 * @param string $mousestop optional, Scrolling stoppt bei Mouseover
 * - true = ja, false = nein, default true
 * @param bool $focusstop optional, Scrolling stoppt, wenn das Fenster keinen Focus hat
 * - true = ja, false = nein, default true
 * @return string $out  gibt den kompletten Quelltext fuer die Ausgabe zurueck
 */
function mxScrollContent($content, $direction = 'up', $speed = 4, $height = 60, $width = 0, $mousestop = true, $focusstop = true)
{
    static $i = 1;

    $divname = strval($i++ . $i++);

    switch ($direction) {
        case 'left':
        case 'right':
        case 'down':
        case 'up':
            break;
        default:
            $direction = 'up';
            break;
    }

    switch (true) {
        case !is_numeric($speed):
        case $speed < 1:
            $speed = 4;
            break;
        case $speed > 100:
            $speed = 100;
            break;
    }

    $found = preg_match('#([0-9.]+)([^0-9.]*)#', strval($height), $matches);
    switch (true) {
        case $height == 'auto':
        case $found && $matches[1] && $matches[2]:
            // Zahl mit Maßeinheit, oder auto, direkt übernehmen
            break;
        case $found && $matches[1]:
            // Zahl ohne Maßeinheit, Standard Pixel dranhängen
            $height .= 'px';
            break;
        default:
            // irgendwas anderes, Standardwert verwenden
            $height = '60px';
            break;
    }

    $found = preg_match('#([0-9.]+)([^0-9.]*)#', strval($width), $matches);
    switch (true) {
        case $width == 0:
        case $width == 'auto':
        default:
            // irgendwas anderes, Standardwert verwenden
            $width = 'auto';
            break;
        case $found && $matches[1] && $matches[2]:
            // Zahl mit Maßeinheit, oder auto, direkt übernehmen
            break;
        case $found && $matches[1]:
            // Zahl ohne Maßeinheit, Standard Pixel dranhängen
            $width .= 'px';
            break;
    }

    $mousestop = intval($mousestop);
    $focusstop = (!$focusstop) ? 'true' : 'false';

    $template = load_class('Template');
    $template->init_path(__FILE__);
    $template->assign(compact('divname', 'direction', 'speed', 'mousestop', 'focusstop', 'content', 'width', 'height'));

    return $template->fetch('scrollcontent.html');
}

/**
 * pmxInlinePermissions
 *
 * @package
 * @author tora60
 * @copyright Copyright (c) 2010
 * @version $Id: mx_api_2.php 228 2016-09-29 05:25:39Z module-factory $
 * @access public
 */
class pmxInlinePermissions {
    private $_fieldname = '';
    private $_permissions = array();
    private $_sysgroups = array();
    private $_config = array(/* Einstellungen */
        'collapsible' => true,
        'defaultval' => 'on',
        'show_anonymous' => true,
        'show_admin' => true,
        'show_default' => true,
        // 'check_allow' => true,
        // 'check_disallow' => true,
        'check_deny' => true,
        );

    /**
     * pmxInlinePermissions::__construct()
     *
     * @param mixed $fieldname
     * @param mixed $permissions
     * @param array $config
     */
    public function __construct($fieldname, $permissions, $config = array())
    {
        $this->_fieldname = $fieldname;
        $this->_permissions = $permissions;
        $this->_config = array_merge($this->_config, $config);
        $this->_sysgroups = $this->_get_groups();
    }

    /**
     * pmxInlinePermissions::__toString()
     *
     * @return
     */
    public function __toString()
    {
        return $this->getHtml();
    }

    /**
     * pmxInlinePermissions::getHtml()
     *
     * @param string $fieldname
     * @return string HTML-Output
     */
    public function getHtml()
    {
        $template = load_class('Template');
        $template->init_path(__FILE__);
        $template->assign($this->_config);

        $template->assign('fieldname', $this->_fieldname);
        $template->assign('permissions', $this->_permissions);
        $template->assign('sysgroups', $this->_sysgroups);

        return $template->fetch('inline_permissions.html');
    }

    /**
     * pmxInlinePermissions::_get_groups()
     *
     * @return
     */
    private function _get_groups()
    {
        global $prefix;

        $userconfig = load_class('Userconfig');

        $default = (isset($this->_permissions['0'])) ? $this->_permissions['0'] : $this->defaultval;

        $groups = array();

        if ($this->show_anonymous) {
            $groups[PMX_GROUP_ID_ANONYMOUS] = _PERMISSIONS_ANONYMOUS;
        }

        $result = sql_query("SELECT access_id AS id, access_title AS title
                    FROM " . $prefix . "_groups_access
                    WHERE access_title != 'Deleted'
                    ORDER BY access_title");
        while ($row = sql_fetch_assoc($result)) {
            $groups[intval($row['id'])] = $row['title'];
        }
        if (!isset($groups[$userconfig->default_group])) {
            sql_query("INSERT INTO " . $prefix . "_groups_access
                SET access_title = '" . MX_FIRSTGROUPNAME . "', access_id=" . intval($userconfig->default_group));
            $groups[intval($userconfig->default_group)] = MX_FIRSTGROUPNAME;
        }

        if ($this->show_admin) {
            $groups[PMX_GROUP_ID_ADMIN] = _PERMISSIONS_ADMIN;
            $groups[PMX_GROUP_ID_SYSADMIN] = _PERMISSIONS_SYSADMIN;
        }

        if ($this->show_default) {
            $groups[PMX_GROUP_ID_USER] = _PERMISSIONS_DEFAULT;
        }

        foreach ($groups as $id => $title) {
            $groups[$id] = array('title' => $title, 'stat' => ((isset($this->_permissions[$id])) ? $this->_permissions[$id] : $default));
        }

        return $groups;
    }

    /**
     * pmxInlinePermissions::__get()
     *
     * @param string $name
     * @return
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->_config)) {
            return $this->_config[$name];
        }
        $trace = debug_backtrace();
        trigger_error('undefined property \'' . $name . '\' in ' . mx_strip_sysdirs($trace[0]['file']) . ' line ' . $trace[0]['line'], E_USER_NOTICE);
        return null;
    }

    /**
     * pmxInlinePermissions::__set()
     *
     * @param string $name
     * @param mixed $value
     * @return
     */
    public function __set($name, $value)
    {
        $this->_config[$name] = $value;
    }

    /**
     * pmxInlinePermissions::set()
     *
     * @param string $name
     * @param mixed $value
     * @return
     */
    public function set($name, $value = null)
    {
        if (is_array($name) && $value === null) {
            foreach ($name as $key => $value) {
                $this->_config[$key] = $value;
            }
        } else {
            $this->_config[$name] = $value;
        }
    }
}

/**
 * pmx_cutString()
 *
 * @param mixed $story
 * @param integer $textlen
 * @param string $link
 * @param mixed $showpics
 * @return
 */
function pmx_cutString($story, $textlen = 100, $link = "", $showpics = false)
{
    /* modifizierter originalcode aus 'block-News_short-more-columns */

    $introtext = '';
    if ($textlen) {
        // array zuruecksetzen
        $replaces = array();
        $prependtags = array();
        $textlentemp = $textlen;

        if ($showpics) {
            // Alle Tags ausser <br /> & <img> entfernen
            $introtext = trim(strip_tags($story, '<br /><img>'));
            // <br /> am Textbeginn entfernen
            $introtext = preg_replace('#^(?:<br\s*/*>\s*)*#is', '', $introtext);
            // alle img & br tags suchen und zwischenspeichern
            preg_match_all('#<(br|img)[^>]*>#si', $introtext, $prependtags);
        } else {
            // Alle Tags ausser <br /> entfernen
            $introtext = trim(strip_tags($story, '<br />'));
            // <br /> am Textbeginn entfernen
            $introtext = preg_replace('#^(?:<br\s*/*>\s*)*#is', '', $introtext);
            // alle br tags suchen und zwischenspeichern
            preg_match_all('#<br[^>]*>#si', $introtext, $prependtags);
        }

        foreach($prependtags[0] as $i => $img) {
            // einen alternativen String zum Ersetzen erstellen
            $alternate = md5($img);
            // die gewuenschte Textlaenge um die Textlaenge des alternativen String erweitern
            $textlentemp = $textlen + strlen($alternate);
            // den gefundenen Tag in das array stellen
            $replaces[$alternate] = $img;
            // den gefundenen Tag aus dem Text entfernen und dafuer den alternativen String einsetzen
            $introtext = trim(str_replace($img, $alternate, $introtext));
        }
        // Text auf die gewuenschte Laenge kuerzen
        $clicklink = (trim($link) != "")?"<a class='button' href=\"" . $link . "\" title=\"" . _READMORE . "\">" . _READMORE . "</a>":"";
        $introtext = mxCutString($introtext, $textlentemp, " ... " . $clicklink, " ");
        // wenn Tags gefunden wurden, die alternativen Textteile wieder
        // durch die im Array zwischengespeicherten Tags ersetzen
        if (count($replaces)) {
            $introtext = str_replace(array_keys($replaces), array_values($replaces), $introtext);
        }
    }
    return $introtext;
}

/**
 * get_mark()
 * Diese Funktion trennt die relevanten Bereiche aus dem Ausschnitt heraus $string ist dabei der zu durchsuchende Gesamtstring,
 * in $Mark sind durch "*" getrennt der Beginn des zu suchenden Strings und das Ende des zu suchende Abschnittes. Beispiel für den Text "<div>*</div></li>"
 *
 * @param mixed $string
 * @param mixed $mark
 * @return
 */
function get_mark($string, $mark)
{
    $ausgabe = array();
    $template = explode("*", $mark);
    $mark = $template[0];
    $end = $template[1];
    $string = strstr($string, $mark);

    $temp = explode($mark, $string);
    $a = 1;
    foreach ($temp as $tempx) {
        $tempx = explode($end, $tempx);
        $tempx = $tempx[0];
        if ($tempx) {
            array_push ($ausgabe, $tempx);
        }
    }
    return $ausgabe;
    /* alternativ */
    // if(preg_match('/-Anfangsmarke-(.+)-Endmarke-/isU', $text, $matches)) echo $matches[1];
}

/**
 * replace_mark()
 *
 * @param mixed $string
 * @param array $mark
 * @param array $strrep
 * @return
 */
function replace_mark($string, $mark = array(), $strrep = array())
{
    $string2 = str_replace($mark, $strrep, $string);
    return $string2;
}

/**
 * make_ency_link()
 *
 * @param mixed $string
 * @param integer $count
 * @return
 */
function make_ency_link($string, $count = 0)
{
    global $prefix;

    if (mxModuleAllowed("Encyclopedia") and mxModuleActive("Encyclopedia")) {
        // TODO: in der Datenbankabfrage beachten ob die entsprechenden Daten aktiviert und der aktuellen Sprache entsprechen !!!!
        $result = sql_query("SELECT content.* FROM ${prefix}_encyclopedia_text as content, ${prefix}_encyclopedia as category 
							WHERE content.eid = category.eid AND category.active=1 AND category.elanguage='".$GLOBALS['language']."'");
        $temp = array();
        while ($ency = sql_fetch_assoc($result)) {
            $text = (pmx_cutString(htmlspecialchars_decode (strip_tags($ency['text']))));
            $temp[$ency['title']] = "<a href=\"modules.php?name=Encyclopedia&amp;op=content&amp;tid=" . $ency['tid'] . "\" title=\"" . $text . "\" target=\"_blank\" >" . $ency['title'] . "</a>";
        }
        $string = mxChangeContent($string, $temp, $count);
    }
    return $string;
}

/**
 * getHighlightedString()
 *
 * @param mixed $story
 * @param array $highlights
 * @param string $pretext
 * @param string $posttext
 * @return
 */
function getHighlightedString($story, $highlights = array(), $pretext = "", $posttext = "")
{
    $text = $story;
    /* eigene Kennung rausfiltern*/
    $text = str_replace('###', '{{{', $text);
    // HTML-Tags  rausfischen
    preg_match_all('|</?[^>]*>|', $text, $tags);
    $tags = array_unique($tags[0]);
    // var_dump($tags);
    // Platzhalter setzen
    foreach($tags as $key => $tag) {
        $text = str_replace($tag, ' ###' . $key . '### ', $text);
    }
    // Normale Ersetzung
    foreach($highlights as $dummy => $highlight) {
        /* eigene Kennung rausfiltern*/
        $highlight = str_replace('###', '', strip_tags($highlight));
        /* jetzt ersetzen */
        $text = str_replace($highlight, $pretext . $highlight . $posttext, $text);
    }
    // Platzhalter wieder durch ursprüngliche Tags ersetzen
    foreach($tags as $key => $tag) {
        $text = str_replace(' ###' . $key . '### ', $tag, $text);
    }
    // Fertig
    return $text;
}

/**
 * string2timestamp()
 *
 * @param mixed $datetime
 * @param mixed $format
 * @return
 */
function string2timestamp($datetime, $format)
{
    $temp = strptime ($datetime , $format);

    return mktime($temp["tm_hour"], $temp["tm_min"], $temp["tm_sec"], $temp["tm_mon"] + 1, $temp["tm_mday"], $temp["tm_year"] + 1900);
}

/**
 * str_compare()
 *
 * @param mixed $text1
 * @param mixed $text2
 * @return
 */
function str_compare($text1, $text2)
{
    $array1 = explode(" ", str_replace(array("  ", "\r", "\n"), array(" ", "", ""), $text1));
    $array2 = explode(" ", str_replace(array("  ", "\r", "\n"), array(" ", "", ""), $text2));
    $max1 = count($array1);
    $max2 = count($array2);

    $start1 = $start2 = 0;
    $jump1 = $jump2 = 0;
    while ($start1 < $max1 && $start2 < $max2) {
        $pos11 = $pos12 = $start1;
        $pos21 = $pos22 = $start2;
        $diff2 = 0;
        // schaukel 1. Array hoch
        while ($pos11 < $max1 && $array1[$pos11] != $array2[$pos21]) {
            ++$pos11;
        }
        // Ende des 1 Arrays erreicht ?
        if ($pos11 == $max1) {
            $start2++;
            continue;
        }
        // Gegenschaukel wenn übersprunge Wörter
        if (($diff1 = $pos11 - $pos21) > 1) {
            while ($pos22 < $max2 && $array1[$pos12] != $array2[$pos22]) {
                ++$pos22;
            }
            $diff2 = $pos22 - $pos12 + $jump2;
        }
        // Ende des 2 Arrays erreicht ?
        if ($pos22 == $max2) {
            $start1++;
            continue;
        }
        $diff1 += $jump1;
        // Auswertung der Schaukel
        if ($diff1 >= $diff2 && $diff2) {
            unset($array1[$pos12], $array2[$pos22]);
            $start1 = $pos12 + 1;
            $start2 = $pos22 + 1;
            $jump2 = $diff2;
        } else {
            unset($array1[$pos11], $array2[$pos21]);
            $start1 = $pos11 + 1;
            $start2 = $pos21 + 1;
            $jump1 = $diff1;
        }
    }
    $safe1 = explode(" ", $text1);
    reset($array1);
    while (list($key1,) = each($array1)) {
        $safe1[$key1] = "<span style=\"color:green\">" . $safe1[$key1] . "</span>";
    }
    $safe2 = explode(" ", $text2);
    reset($array2);
    while (list($key2,) = each($array2)) {
        $safe2[$key2] = "<span style=\"color:red\">" . $safe2[$key2] . "</span>";
    }
    return implode(" ", $safe1) . "<br /><br /><br />" . implode(" ", $safe2) . "<br /><br />";
}

/**
 * pmx_html_passwordchecker()
 * Erstellt den benötigten Javascript Code im HTML-Header,
 * um die Stärke von Passwörtern zu visualisieren.
 *
 * based on: https://github.com/nicolaszhao/password-checker
 *
 * @return nothing
 */
function pmx_html_passwordchecker()
{
    /* nur einmal ausführen... */
    static $minpass;
    if (isset($minpass)) {
        return;
    }

    $userconfig = load_class('Userconfig');
    $minpass = (intval($userconfig->minpass)) ? $userconfig->minpass : 6;

    $strings = array(_PWD_TOOSHORT, _PWD_VERYWEAK, _PWD_WEAK, _PWD_GOOD, _PWD_STRONG);
    $strings = implode("', '", $strings);

    $template = load_class('Template');
    $template->init_path(__FILE__);
    $template->assign(compact('minpass', 'strings'));
    $out = $template->fetch('passwordchecker.html');

    pmxHeader::add_jquery('jquery.password-checker.js');
    pmxHeader::add_script_code($out);
}

function mxIncludeHeader(){
	include (PMX_REAL_BASE_DIR . DS . "header.php");
}

function mxIncludeFooter(){
	include (PMX_REAL_BASE_DIR . DS . "footer.php");
}

function pmxGetFileVersion($file){
	
	switch (true) {
		case (!file_exists($file)):
			$temp="";
			break;
		case (filesize($filename) === 0):
			$temp = '1.0';
			break;
		case (!preg_match('#\.(php|js|inc|htc|css|html)$#i', $file)):
			$temp="";
			break;
		default:
			$tmp = file_get_contents($file);
			// Versionsnummer aus dem Dateiinhalt, bzw. CVS-Header auslesen
			preg_match('#\$Revision\:[[:space:]]*([0-9\.]*)[[:space:]]*\$#i', $tmp, $matches);
			
			if (empty($matches[1])) {		// falls kein Standard-pmx-Header vorhanden
				preg_match('#\$Id\:.*\.(?:php|js|inc|htc|css|html)[[:space:]]*([0-9\.]*)[[:space:]]*.*\$#i', $tmp, $matches);
				if (empty($matches[1])) {
					// oder halt sonst was wie eine Versionsnummer aussieht
					preg_match('#((?:[0-9]+[:.-])+[0-9]+)#', $tmp, $matches);
				}
			}
			// falls keine Versionsinfo vorhanden, diese auf 0 setzen
			$tmp = (empty($matches[1])) ? '0' : $matches[1];
			$tmp = str_replace(array(':', '-'), '.', $tmp);
			// unnütze nullen am ende entfernen
			$tmp = preg_replace('#(.[0-9+])\.0+$#', '$1', $tmp);
			break;
	}
	return $tmp;
}
	
/**
 * pmxDevelLogo()
 *
 * @param mixed $modorg
 * @return string 
 */
function pmxDevelLogo($modorg=""){
	 	 
	 $modorg=strip_tags(trim($modorg));
	 // default Text
	 $title_text = 'by pragmaMx &copy;';
	 
	 if($modorg != MX_MODULE && $modorg!="" ){
	  $title_text = '' . MX_MODULE . ' based on ' . $modorg . ' ' . $title_text . ''; 
	 }
	 
	 $logoimg = '<img src="images/pragmamx.png" style="width:2em">';

	 $logo ='<div class="align-right tiny"><a href="http://www.pragmamx.org" target="_blank" title="' . $title_text . '"> ' . $logoimg . ' </a></div>';
	 
	 return $logo;
	 
}
?>