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
 * $Revision: 7 $
 * $Author: PragmaMx $
 * $Date: 2015-07-08 12:52:42 +0200 (Mi, 08. Jul 2015) $
 */

defined('mxMainFileLoaded') or die('access denied');

if (defined('DONT_INIT_THEME')) {
    return;
}

/**
 * alles beginnt, mit dem Aufruf der Funktion theme_get_template() am Ende dieser Datei
 */
$GLOBALS['index'] = (empty($GLOBALS['index'])) ? 0 : 1;

/* hier diese Variable generieren, weil die oft in den Themes verwendet wird */
$GLOBALS['backend_active'] = mxModuleAllowed('rss');

global $VKPTheme;
$VKPTheme = true;
global $theme_template;
$theme_template = array();

/**
 * info
 */
function theme_get_template()
{
    global $theme_template;

    $allblocks = theme_define_blocks();

    $cache_id = '';
    $cache = null;
    /* wennn das Theme cacheable ist, versuchen aus dem Cache zu lesen */
    if ($GLOBALS['mxUseThemecache'] && defined('MX_THEME_CACHABLE') && MX_THEME_CACHABLE === true && !MX_MOBILE_DEVICE) {
        switch (true) {
            case MX_IS_ADMIN:
                $userdata = mxGetAdminData();
                $cache_id .= $userdata['aid'];
                break;
            case MX_IS_USER:
                $userdata = mxGetUserData();
                $cache_id .= implode('.', $userdata['groups']);
        }

        $cache_id = implode('.', array_keys($allblocks)) . $_SERVER['HTTP_HOST'] . MX_THEME . $cache_id;
        $cache = load_class('Cache');

        if (!isset($_GET['themerefresh']) && ($theme_template = $cache->read($cache_id)) !== false) {
            return $theme_template;
        }
    }

    /* template einlesen */
    $template = file_get_contents(MX_THEME_DIR . "/" . MX_THIS_THEMEFILE);
    // alle template-Kommentare entfernen
    theme_extract_comments($template);
    // Die Funktion theme_replace_start enthaelt die Definitionen und muss in der theme.php oder functions.php vorhanden sein
    // bestimmte Texte, vor allem Image-Pfade, die ersetzt werden sollen, definieren (suche/ersetze)
    $template = theme_replace_start($template);
    // alle Bloecke auslesen und den entsprechenden Positionen zuordnen
    foreach ($allblocks as $type => $var) {
        $theme_template[$type] = theme_extract_part($template, $type);
        if (isset($var['container']) && isset($var['position'])) {
            $theme_template['blockcontainers'][$var['container']]['posi'] = $var['position'];
            $theme_template['blockcontainers'][$var['container']]['function'] = $var['function'];
        }
    }
    $contentvars = theme_define_content();
    // die Teile fuer das Newsmodul aus dem Template extrahieren
    // mxDebugFuncVars($contentvars);
    if (is_array($contentvars['themeindex'])) {
        foreach ($contentvars['themeindex'] as $name) {
            $theme_template[$name] = theme_extract_part($template, $name);
        }
    } else {
        $theme_template['themeindex'] = theme_extract_part($template, $contentvars['themeindex']);
    }
    $theme_template['themearticle'] = theme_extract_part($template, $contentvars['themearticle']);
    foreach ($contentvars['opentabs'] as $function => $arr) {
        $theme_template[$function] = theme_extract_part($template, $arr['templatevar']);
    }
    // zusaetzliche Header-Tags des Themes auslesen
    $theme_template['more_header'] = theme_extract_optional_part($template, $contentvars['add_header']);
    $theme_template['more_header'] .= "\n<!-- pmx-theme-engine v." . mx_theme_engineversion() . " -->\n";
    // den eigentlichen Output (body) extrahieren <body></body> muss komplett enthalten sein)
    $pattern = '#(<body[^>]*>)(.*)</body>#si';
    preg_match($pattern, $template, $matches);
    // wenn der body-Tag nicht gefunden werden konnte ist das template unbrauchbar
    if (empty($matches[2])) {
        die('<h4>Template error</h4><p>Failed to find valid theme body-tag (' . htmlspecialchars($pattern) . ')</p><hr /><p>in generated html-source:</p><pre>' . htmlspecialchars($template) . '</pre>');
    }
    // das template enthaelt jetzt nur noch den Inhalt zwischen <body> und </body>
    $theme_template['template'] = trim($matches[2]);
    /**
     * seit pragmaMx 0.1.9 koennen die Platzhalter in einer zentralen Funktion
     * zusammengestellt und gemeinsam am Ende ersetzt werden, diese Platzhalter
     * werden hier durch einen eindeutigen String ersetzt, der verhindern soll,
     * dass diese Platzhalter durch Usereingaben beeinflusst werden koennen
     */
    if (function_exists('theme_define_placeholders')) {
        $theme_template['template'] = str_replace('{', '-:_' . md5($GLOBALS['mxSecureKey']) . '_:-', $theme_template['template']);
    }
    // body-Tag gesondert in das template-Array speichern damit er spaeter weiterbehandelt werden kann
    $theme_template['body_tag'] = $matches[1];
    // und Sprachparameter im bodytag durch lokale Einstellung ersetzen
    $pattern = '#lang.*?=.*?["|\'](.*?)["|\']#si';
    $theme_template['body_tag'] = preg_replace($pattern, ' lang="' . _DOC_LANGUAGE . '" ', $theme_template['body_tag']);

    /* wennn das Theme cacheable ist, den Cache schreiben */
    if ($cache_id && is_object($cache)) {
        $cache->write($theme_template, $cache_id, 18000); // 5 Stunden Cachezeit
    }

    return $theme_template;
    // die naechsten Ausgaben sind dann die hardgecodeten <head> Teile in der header.php
}

/**
 * info
 */
if (!function_exists('themeheader')) {
    function themeheader()
    {
        global $theme_template;
        // die Ausgaben der header.php zwischenspeichern und die Ausgabe verwerfen
        $header = ob_get_clean();
        // Puffer muesste danach Level 1 oder Level 2 bei obgzhandler sein, test:
        // mxDebugFuncVars('ob_get_status:',ob_get_status());
        // optionalen Header aus template file an die richtige stelle setzen, direkt vor bzw. nach <head>
        $header = str_ireplace('</head>', $theme_template['more_header'], $header);
        // die Siteservice und Debugfuncvars vorher extrahieren, falls vorhanden
        $siteservice = trim(theme_extract_optional_part($header, 'mx_site_message'));
        $debugservice = trim(theme_extract_optional_part($header, 'mx_debug_message'));
        // weitere Aenderungen am Header konfigurierbar direkt im theme
        $header = theme_header($header, $siteservice, $debugservice);
        // unnoetige spaces entfernen
        $header = trim(preg_replace('#\s*\n\s+#', "\n", $header));
        // die aus dem header extrahierten Nachrichten hier wieder anfügen,
        // dies ist dann nach dem body-Tag, die Nachrichten koennen im Theme auch
        // anderweitig verwendet bzw. entfernt werden
        // - dann Header bereits vorher abschicken
        echo trim($header), "\n\n", $debugservice, $siteservice, "\n";
        // den Puffer wieder neu starten fuer die weiteren Ausgaben des Moduls etc.
        ob_start();
    }
}

/**
 * Seite parsen
 */
if (!function_exists('themefooter')) {
    function themefooter()
    {
        global $theme_template;
        $part = array();

        /* Im Puffer stehen hier die Ausgaben des Moduls */
        $theme_template['script_output'] = ob_get_clean();

        /* hier ist das Modul komplett durchgelaufen und die Variablen koennen jetzt im Template ersetzt werden */
        $theme_template['template'] = theme_replace_vars($theme_template['template']);
        $contentvars = theme_define_content();
        theme_extract_part($theme_template['template'], $contentvars['output_container'], $theme_template['script_output']);
        unset($theme_template['script_output']);
        foreach ($theme_template['blockcontainers'] as $container => $sidex) {
            $allblocks = '';
            $blockcount[$container] = 0;
            $blocks = mxGetAllBlocks($sidex['posi']);
            foreach ($blocks as $block) {
                $func = $sidex['function'];
                $created = $func($block["title"], $block["content"], $block, 'noecho');
                // Inhalte der Bloecke bei Bedarf ersetzen
                if (function_exists('theme_replace_blocks')) {
                    $created = theme_replace_blocks($created, $block);
                }
                // und den fertigen Block anfuegen
                $allblocks .= $created;
                $blockcount[$container]++;
            }
            /* hier steht der Blockinhalt komplett formatiert zur Verfuegung */
            theme_extract_part($theme_template['template'], $container, $allblocks);
        }
        unset($allblocks, $block, $container, $sidex, $theme_template['blockcontainers']);
        // pruefen ob ueberhaupt rechte Bloecke aktiv sind, wenn nicht $index auf 0 stellen
        // dies verhindert, dass eine leere rechte Spalte angezeigt wird
        $GLOBALS['index'] = ($blockcount[$contentvars['index_on_block_container']] > 0) ? $GLOBALS['index'] : 0;
        // je nach $index, die Tabellenspalten der rechten Bloecke extrahieren oder entfernen
        if (empty($GLOBALS['index'])) {
            $contentvars = theme_define_content();
            theme_extract_part($theme_template['template'], $contentvars['index_on_container']);
        }
        // ersetzen von eigenen Theme-Elementen, kann veraendert und ergaenzt werden
        $theme_template['template'] = theme_replace_end($theme_template['template']);

        /* sys_images ersetzen */
        $theme_template['template'] = theme_replace_sysimages($theme_template['template']);

        /**
         * hier wird der eindeutige String fuer die Platzhalter durch die
         * eigentlichen Platzhalter, bzw. dessen Werte ersetzt (ab 0.1.9)
         */
        if (function_exists('theme_define_placeholders')) {
            $parts = theme_define_placeholders();
            $key = '-:_' . md5($GLOBALS['mxSecureKey']) . '_:-';
            foreach ($parts as $uups) {
                $searches[] = $key . trim($uups[0], '{');
                $replaces[] = $uups[1];
            }
            $theme_template['template'] = str_replace($searches, $replaces, $theme_template['template']);
        }

        /* Debug-Info ausgeben */
        $theme_template['template'] = theme_get_debuginfo($theme_template['template']);

        /* Puffer wieder starten fuer evtl. mod_rewrite */
        if (!ob_get_level()) {
            // Falls keine Pufferebene mehr vorhanden, obwohl in mxBaseconfig explizit gestartet...
            ob_start();
        }

        /* Inhalt abschicken */
        echo $theme_template['template'];
    }
}

/**
 * sys_images ersetzen
 */
function theme_replace_sysimages($template)
{
    $folder = MX_THEME_DIR . '/sys_images/';

    if (!is_dir($folder)) {
        return $template;
    }

    $cache_id = MX_THEME . 'sysimages' . PMX_HOME_URL;
    $cache = load_class('Cache');

    if (($sys_images = $cache->read($cache_id)) === false) {
        /* liest rekursiv, alle Bilder aus dem sys-images Ordner */
        $items = (array)glob($folder . '/*');
        for ($i = 0; $i < count($items); $i++) {
            if (is_dir($items[$i])) {
                $add = (array)glob($items[$i] . '/*');
                $items = array_merge($items, $add);
            }
        }

        $sys_images = array();
        foreach ($items as $key => $filename) {
            if (preg_match("#\.(gif|jpe?g|png)$#i", $filename)) {
                $search = str_replace($folder . '/', '', $filename);
                $sys_images[] = array('"' . PMX_HOME_URL . '/' . $search . '"', '"' . $filename . '"');
                $sys_images[] = array('"' . PMX_BASE_PATH . $search . '"', '"' . $filename . '"');
                $sys_images[] = array('"' . $search . '"', '"' . $filename . '"');
                $sys_images[] = array("'" . $search . "'", '"' . $filename . '"');
            }
        }

        $cache->write($sys_images, $cache_id, 18000); // 5 Stunden Cachezeit
    }

    if ($sys_images) {
        // images innerhalb von TextArea's 'entwerten', damit diese nicht ersetzt werden
        $original = array();
        $modified = array();
        $parts = preg_split('#</textarea>#i', $template);
        foreach ($parts as $key => $value) {
            if (preg_match('#<textarea[^>]*>(.*)$#is', $value, $matches)) {
                if (trim($matches[1])) {
                    $original[$key] = $matches[1];
                    $modified[$key] = preg_replace('#(gif|jpe?g|png)#i', '~~\\1~~', $matches[1]);
                }
            }
        }
        if ($original && $modified) {
            $template = str_replace($original, $modified, $template);
        }
        // Struktur des Ordners sys_images analysieren
        // ersetzen und Ausgabe in das template-Array speichern
        $template = theme_replace_parts($template, $sys_images);
        // images innerhalb von TextArea's wieder herstellen
        if ($original && $modified) {
            $template = str_replace($modified, $original, $template);
        }
        // falls der sys_images-Pfad doppelt ersetzt wurde
        $template = preg_replace("#($folder){2,}#", '$1', $template);
    }

    return $template;
}

/**
 * ersetzt die php-Variablen und Konstanten innerhalb des Templates
 * Beispiele:  {$sitename} {_HOME}
 */
function theme_replace_vars($content, $searcharray = array())
{
    static $pattern;
    if (!$pattern) {
        if (function_exists('theme_define_placeholders')) {
            $key = preg_quote('-:_' . md5($GLOBALS['mxSecureKey']) . '_:-');
            $pattern = "#(?:\{|" . $key . ")([\$]?)([[:alpha:]0-9_]*)}#s";
        } else {
            $pattern = "#\{([\$]?)([[:alpha:]0-9_]*)}#s";
        }
    }
    if (preg_match_all($pattern, $content, $matches)) {
        if (isset($matches[2])) {
            if (!count($searcharray)) {
                $searcharray = $GLOBALS;
            }
            foreach($matches[2] as $key => $vari) {
                if ($matches[1][$key] == '$') {
                    if (isset($searcharray[$vari])) {
                        $search[$key] = $matches[0][$key];
                        $replace[$key] = $searcharray[$vari];
                    }
                } else if (defined($vari)) {
                    $search[$key] = $matches[0][$key];
                    $replace[$key] = constant($vari);
                }
            }
            if (isset($search)) {
                $content = str_replace($search, $replace, $content);
            }
        }
    }
    return $content;
}

/**
 * info
 */
if (!function_exists('OpenTable')) {
    function OpenTable()
    {
        ob_start();
    }
}

/**
 * info
 */
if (!function_exists('CloseTable')) {
    function CloseTable()
    {
        global $theme_template;
        $alltables = theme_define_content();
        if (isset($theme_template['OpenTable'])) {
            $out = str_replace($alltables['opentabs']['OpenTable']['innerreplace'], trim(ob_get_clean()), $theme_template['OpenTable']);
        } else {
            die('<h4>Template error</h4><p>Failed to find theme part \'OpenTable\'</p>');
        }
        echo $out;
    }
}

/**
 * info
 */
if (!function_exists('OpenTable2')) {
    function OpenTable2()
    {
        ob_start();
    }
}

/**
 * info
 */
if (!function_exists('CloseTable2')) {
    function CloseTable2()
    {
        global $theme_template;
        $alltables = theme_define_content();
        if (isset($theme_template['OpenTable2'])) {
            $out = str_replace($alltables['opentabs']['OpenTable2']['innerreplace'], trim(ob_get_clean()), $theme_template['OpenTable2']);
        } else {
            die('<h4>Template error</h4><p>Failed to find theme part \'OpenTable2\'</p>');
        }
        echo $out;
    }
}

/**
 * info
 */
if (!function_exists('OpenTableAl')) {
    function OpenTableAl()
    {
        ob_start();
    }
}

/**
 * info
 */
if (!function_exists('CloseTableAl')) {
    function CloseTableAl()
    {
        global $theme_template;
        $alltables = theme_define_content();
        if (isset($theme_template['OpenTableAl'])) {
            $out = str_replace($alltables['opentabs']['OpenTableAl']['innerreplace'], trim(ob_get_clean()), $theme_template['OpenTableAl']);
        } else {
            die('<h4>Template error</h4><p>Failed to find theme part \'OpenTableAl\'</p>');
        }
        echo $out;
    }
}

/**
 * Extract and return block '$part_name' from the template, the part is replaced by $subst
 */
function theme_extract_part(&$template, $part_name, $subst = '')
{
    $pattern = "#(<!-- START $part_name -->)(.*?)(<!-- END $part_name -->)#s";
    if (!preg_match($pattern, $template, $matches)) {
        die('<h4>Template error</h4><p>Failed to find theme part \'' . $part_name . '\'<br />(' . htmlspecialchars($pattern) . ')</p><hr /><p>in generated html-source:</p><pre>' . htmlspecialchars($template) . '</pre>');
    }
    $template = str_replace($matches[1] . $matches[2] . $matches[3], $subst, $template);
    return $matches[2];
}

/**
 * Extract and return optional block '$part_name' from the template, the part is replaced by $subst
 */
function theme_extract_optional_part(&$template, $part_name, $subst = '')
{
    $pattern = "#(<!-- START $part_name -->)(.*?)(<!-- END $part_name -->)#s";
    if (!preg_match($pattern, $template, $matches)) {
        return '';
    }
    $template = str_replace($matches[1] . $matches[2] . $matches[3], $subst, $template);
    return $matches[2];
}

/**
 * Extract and return block '$part_name' from the template, the part is replaced by $subst
 */
function theme_extract_comments(&$template)
{
    $pattern = "#(<!-- START comment -->)(.*?)(<!-- END comment -->)#s";
    if (preg_match_all($pattern, $template, $matches)) {
        $template = str_replace($matches[0], '', $template);
    }
    return $matches;
}

/**
 * bestimmte Texte, vor allem Image-Pfade, die ersetzt werden sollen, definieren (suche/ersetze).
 */
function theme_replace_parts($string, $part)
{
    // wenn nix zu ersetzen, wieder zurueck
    if (!count($part)) return $string;
    // array fuer ersetzungsvorgang fuellen
    foreach ($part as $key => $item) {
        if ('preg' !== $key) { // preg rausnehmen
            $searches[$key] = $item[0];
            $replaces[$key] = $item[1];
        }
    }
    // ersetzen und das Ergebnis in das template-Array speichern
    if (isset($searches)) {
        $string = str_replace($searches, $replaces, $string);
    }
    // die oben definierten Elemente per preg_replace ersetzen
    if (isset($part['preg'])) {
        // alte Arrays wieder loeschen
        $searches = array();
        $replaces = array();
        // array fuer ersetzungsvorgang fuellen
        foreach ($part['preg'] as $i => $item) {
            $searches[$i] = $item[0];
            $replaces[$i] = $item[1];
        }
        // ersetzen und Ausgabe in das template-Array speichern
        if (count($searches)) {
            $string = preg_replace($searches, $replaces, $string);
        }
    }
    return $string;
}

/**
 * ermittelt die Version der aktuellen Theme-Engine
 */
function mx_theme_engineversion()
{
    // ACHTUNG!! wird automatisch aus dem CVS-Header generiert !
	$regs=array("0","0","0","0","0","0");
    $eng = "\$Id: mx_themes.php 7 2015-07-08 10:52:42Z PragmaMx $";
	preg_match('#([^a-z]{1,}) ([0-9\]{1,2})\s([0-9]{4})[-/]([0-9]{1,2})[-/]([0-9]{1,2})#', $eng, $regs);
    //preg_match("#([^a-z]{1,}) ([0-9]{1,2})\s([0-9]{4})[-/]([0-9]{1,2})[-/]([0-9]{1,2})#", $eng, $regs);
    $eng = "2.$regs[1]/$regs[2]-$regs[3]-$regs[4]";
    return $eng;
}

if (!function_exists('theme_show_date')) {
    function theme_show_date()
    {
        return mx_strftime(_DATESTRING);
    }
}

if (!function_exists('theme_show_time')) {
    function theme_show_time()
    {
        if (_SYS_TIME24HOUR) {
            /* Zeit im 24 Stunden Format 21:20 */
            return strftime('%H:%M');
        } else {
            /* Zeit im 12 Stunden Format 09:20 PM */
            $out = trim(strftime('%I:%M %p'));
            if (strlen($out) == 5) {
                /* falls kein am/pm angehaengt wurde, dies manuell machen */
                $hour = intval(strftime('%H'));
                $out .= ($hour < 12) ? ' AM' : ' PM';
            }
            return $out;
        }
    }
}

if (!function_exists('theme_total_user_online')) {
    function theme_total_user_online()
    {
        // mx_total_user_online
        global $prefix, $user_prefix;
        $past = time() - MX_SETINACTIVE_MINS ;
        // Alle Gaeste ermitteln
        $guest_online_num = 0;
        $result = sql_system_query("SELECT Count(ip) FROM ${prefix}_visitors WHERE time>" . $past . " AND uid=0;");
        list($guest_online_num) = sql_fetch_row($result);
        // alle Online-User ermitteln
        $member_online_num = 0;
        $result = sql_system_query("SELECT COUNT(uid) FROM {$user_prefix}_users WHERE (user_lastvisit >= " . $past . " AND user_stat=1 AND user_lastmod<>'logout')");
        list($member_online_num) = sql_fetch_row($result);
        return $guest_online_num + $member_online_num;
    }
}

/* rem nanomx
if (!function_exists('theme_show_banner')) {
    function theme_show_banner($pos)
    {
        if ($GLOBALS['banners']) {
            if (!function_exists('viewbanner')) {
                include_once(PMX_SYSTEM_DIR . DS . 'mx_bannerfunctions.php');
            }
            $pos = intval($pos);
            if (empty($pos)) $pos = 1;
            return viewbanner($pos);
        }
        return '&nbsp;';
    }
}
*/

if (!function_exists('theme_adminname')) {
    /**
     * erstellt einen Link mit den Daten des Admins (Author), der den
     * Arikel veroeffentlicht hat (nuke-Erbe)
     */
    function theme_adminname($story)
    {
        global $theme_template;
        if (!empty($story['url'])) {
            $story['aid'] = "<a href='" . $story['url'] . "' target='new'>" . $story['aid'] . "</a>";
        } else if (!empty($story['email'])) {
            $story['aid'] = "<a href='mailto:" . mxPrepareToDisplay($story['email']) . "'>" . $story['aid'] . "</a>";
        }
        return $story['aid'];
    }
}

if (!function_exists('theme_show_currentpath')) {
    /**
     * erstellt den Navigationspfad zum aktuellen Modul
     */
    function theme_show_currentpath($spacer = '&nbsp;»&nbsp;')
    {
        global $prefix;
        $maxwordlen = 18;

        $link[] = '<a href="./" title="' . _HOME . '">' . _HOME . '</a>';
        if (MX_MODULE == 'admin') {
            // Administration
            $link[] = '<a href="' . adminUrl() . '" title="' . _ADMINMENU . '">' . _ADMINMENU . '</a>';
        } else if (isset($_REQUEST['name']) && mxModuleAllowed(MX_MODULE)) {
            // Module
            $result = sql_query("SELECT custom_title FROM ${prefix}_modules WHERE title='" . mxAddSlashesForSQL(MX_MODULE) . "'");
            list($custom_title) = sql_fetch_row($result);
            $custom_title = str_replace("_", " ", ((empty($custom_title)) ? MX_MODULE : $custom_title));
            $short_title = mxCutString($custom_title, $maxwordlen, "..", "");
            $link[] = '<a href="modules.php?name=' . urlencode(MX_MODULE) . '" title="' . $custom_title . '">' . $short_title . '</a>';
        }
        return implode($spacer, $link);
    }
}

/**
 * feststellen, ob der IE am werkeln ist...
 */
function theme_is_agent_ie()
{
    $browser = load_class('Browser');
    return $browser->msie;
}

/**
 * nur im IE, die haesslichen Rahmen um Checkboxen und Radio-Feldern entfernen,
 * ueber style-sheet Klasse
 */
function theme_fix_formtags($template)
{
    $browser = load_class('Browser');
    if (!$browser->msie || $browser->version > 7) {
        return $template;
    }
    $pattern = '#(<input[^>]*type\s*=\s*[\"\'](?:radio|checkbox)[\"\']*?[^>]*?)\s*/*>#iU';
    preg_match_all($pattern, $template, $matches);
    if (isset($matches[1])) {
        foreach($matches[1] as $search) {
            if (!preg_match('#class\s*=\s*["\'][^"\']*["\']#i', $search)) {
                $part[] = array($search, $search . ' class="formcheckbox"');
            }
        }
    }
    // die haesslichen Rahmen um Checkboxen und Radio-Feldern entfernen, über style-sheet Klasse
    $pattern = '#(<input.*[^>]type\s*=\s*[\"\'](?:submit|button|reset)[\"\']*?[^>]*?)\s*/*>#iU';
    preg_match_all($pattern, $template, $matches);
    if (isset($matches[1])) {
        foreach($matches[1] as $search) {
            if (!preg_match('#class\s*=\s*["\'][^"\']*["\']#i', $search)) {
                $part[] = array($search, $search . ' class="formbutton"');
            }
        }
    }
    // falls xHTML-Tags zerstoert wurden, diese hier wieder reparieren
    $part['preg'][] = array('#/[[:space:]]?class="(formcheckbox|formbutton)"\s*/*>#', ' class="$1" />');
    // falls die eben hinzugefügten Tageigenschaften doppelt gesetzt wurden, diese wieder vereinzeln
    $part['preg'][] = array('#( class="(?:formbutton|formcheckbox)"){2,}#si', '$1');

    return theme_replace_parts($template, $part);
}

/**
 * feststellen, ob korrekter Doctype eingestellt ist...
 */
function theme_check_xhtmldoctype()
{
    // zwischenspeichern, damit der Doctype korrigiert werden kann und
    // trotzdem die Originaleinstellung abgefragt wird
    static $return;
    if (isset($return)) {
        return $return;
    }
    if (!function_exists('mxDoctypeArray')) {
        // falls mx_themes ueber css includet wird, ist diese Funktion nicht vorhanden
        $return = true;
    } else {
        $doctype_arr = mxDoctypeArray($GLOBALS['DOCTYPE']);
        $return = $doctype_arr['xhtml'];
    }
    return $return;
}

if (!function_exists('theme_show_chcounter')) {
    /**
     * chCounter aktivieren
     */
    function theme_show_chcounter($path = 'chcounter')
    {
        if (!file_exists($path . '/counter.php')) {
            return '<!-- (ch)counter.php not found -->';
        }
        $chCounter_visible = 1;
        $chCounter_force_new_db_connection = false;
        $chCounter_status = 'active';
        $chCounter_page_title = $GLOBALS['pagetitle'];
        ob_start();
        include($path . '/counter.php');
        $out = ob_get_clean();
        return $out;
    }
}

if (!function_exists('theme_get_servicetext')) {
    function theme_get_servicetext($type)
    {
        global $mxSiteServiceText, $mxSiteService;

        $out = '';
        switch ($type) {
            case 'siteservice':
                if ($mxSiteService) {
                    $out .= $mxSiteServiceText;
                }
                break;

            case 'debugservice':
                if (MX_IS_ADMIN && pmxDebug::is_debugmode()) {
                    $out .= '<h3>' . _MSGDEBUGMODE . '</h3>';
                }
        }

        return $out;
    }
}

if (!function_exists('theme_get_debuginfo')) {
    function theme_get_debuginfo($template = false)
    {
        if (!pmxDebug::is_debugmode()) {
            return $template;
        }

        $key = '{DEBUGINFO-:_' . md5($GLOBALS['mxSecureKey']) . '_:-}';

        /* beim ersten Aufruf nur den neuen Platzhalter zurückgeben */
        if ($template === false) {
            return $key;
        }

        /* erst beim zweiten Aufruf den wirklichen Funktionswert abrufen*/
        /* Das geschieht dann erst in der Funktion themefooter() !!! */
        if (strpos($template, $key) !== false) {
            return str_replace($key, mxDebugInfo(), $template);
        }
        return $template;
    }
}

if (!function_exists('theme_show_languageflags')) {
    function theme_show_languageflags($languagelist = array(), $path = 'images/language', $extension = 'png')
    {
        $query = $_SERVER['QUERY_STRING'];
        if (isset($_GET['newlang'])) {
            $query = preg_replace('#[&?]?newlang=[a-zA-Z_]*#', '', $query);
        }
        $to = basename($_SERVER['PHP_SELF']);
        // index.php ist auch php_self=modules.php, deswegen hier index.php verwenden, falls $name leer ist
        if ($to == 'modules.php' && empty($_GET['name'])) {
            $to = './';
        }
        if ($query) {
            $to .= '?' . mx_urltohtml($query) . '&amp;newlang=';
        } else {
            $to .= '?newlang=';
        }

        $languages = array_flip(mxGetAvailableLanguages());
        // $languagelist = array('german', 'english');
        // $languagelist = '';
        $linklist = array();

        switch (true) {
            case $tmp = array_intersect_key((array)$languagelist, $languages):
                // 'german_du' => 'Sprache f&uuml;r das Interface ausw&auml;hlen: Deutsch (Du-Form)'
                foreach ($tmp as $language => $title) {
                    $linklist[] = '<a href="' . $to . $language . '" title="' . $title . '" rel="nofollow">' . mxCreateImage($path . '/flag-' . $language . '.' . $extension) . '</a>';
                }
                break;

            case $tmp = array_intersect_key($languages, array_flip((array)$languagelist)):
                // array('german','english')
                foreach ($tmp as $language => $title) {
                    $linklist[] = '<a href="' . $to . $language . '" title="' . _SELECTGUILANG . ': ' . $title . '" rel="nofollow">' . mxCreateImage($path . '/flag-' . $language . '.' . $extension) . '</a>';
                }
                break;

            default:
                foreach ($languages as $language => $title) {
                    $linklist[] = '<a href="' . $to . $language . '" title="' . _SELECTGUILANG . ': ' . $title . '" rel="nofollow">' . mxCreateImage($path . '/flag-' . $language . '.' . $extension) . '</a>';
                }
                break;
        }

        if (count($linklist) < 2) {
            return false;
        }

        return implode("\n", $linklist);
    }
}

if (!function_exists('FormatStory')) {
    /**
     * diese Funktion wird im pragmaMx nirgends verwendet !!!
     * bleibt aber drin, falls Fremd-Module das Ding benoetigen
     */
    function FormatStory($thetext, $notes, $aid, $informant)
    {
        global $theme_template;
        $notes = (empty($notes)) ? '' : "<br /><br /><b>" . _NOTE . "</b> <i>" . $notes . "</i>\n";
        if ($aid == $informant) {
            echo "<span class='content'>" . $thetext . ' ' . $notes . "</span>\n";
        } else {
            $boxstuff = (empty($informant)) ? '' : "<i>" . mxCreateUserprofileLink($informant) . " " . _WRITES . ":</i><br /><br />";
            $boxstuff .= $thetext . ' ' . $notes . "\n";
            echo "<span class='content'>" . $boxstuff . "</span>\n";
        }
    }
}

function theme_show_footmsg($asTable = false)
{
    $var = array();
    for($i = 1; $i <= 4; $i++) {
        if ($GLOBALS['foot' . $i]) {
            if (defined($GLOBALS['foot' . $i])) {
                $var[$i] = constant($GLOBALS['foot' . $i]);
            } else {
                $var[$i] = $GLOBALS['foot' . $i];
            }
        }
    }
    $out = '';
    if ($var) {
        if ($asTable) {
            $width = floor(100 / count($var));
            $out = '<table><tr><td style="width: ' . $width . '%">' . implode('</td><td style="width: ' . $width . '%">', $var) . '</td></tr></table>';
        } else {
            $out = '<p>' . implode("</p>\n<p>", $var) . '</p>';
        }
    }
    return $out;
}

/**
 * deprecated !!
 * nur noch dummy fuer aeltere Themes
 */
function theme_change_middot()
{
    trigger_error('Use of deprecated function theme_change_middot(). (' . MX_THEME . ')', E_USER_NOTICE);
    return false;
}

/**
 * deprecated !!
 * nur noch dummy fuer aeltere Themes
 */
function theme_unkillimages($template)
{
    trigger_error('Use of deprecated function theme_unkillimages(), use theme_replace_sysimages() instead. (' . MX_THEME . ')', E_USER_NOTICE);
    return preg_replace('#~~(gif|jpe?g|png)~~#i', '\\1', $template);
}

/**
 * deprecated !!
 * nur noch dummy fuer aeltere Themes
 */
function theme_get_morefiles($folder)
{
    trigger_error('Use of deprecated function theme_get_morefiles(). (' . MX_THEME . ')', E_USER_NOTICE);
    return array();
}

/**
 * deprecated !!
 * nur noch dummy fuer aeltere Themes
 */
function theme_getmore_parts()
{
    trigger_error('Use of deprecated function theme_getmore_parts(). (' . MX_THEME . ')', E_USER_NOTICE);
    return array();
}

/**
 * deprecated !!
 * nur noch dummy fuer aeltere Themes
 */
function theme_killimages($inputfield)
{
    trigger_error('Use of deprecated function theme_killimages(), use theme_replace_sysimages() instead. (' . MX_THEME . ')', E_USER_NOTICE);
    return $inputfield[1] . preg_replace('#(gif|jpe?g|png)#i', '~~\\1~~', $inputfield[2]) . $inputfield[3];
}

/**
 * /////////////////////////////////////////////////////////////////////////////////
 */

/* nur wenn theme.php normal verwendet wird, nicht fuer CSS */
if (defined('PMX_VERSION')) {
    /**
     * template einlesen, Funktion ist in der theme.php!
     */
    theme_get_template();

    /**
     * sicherstellen, dass die Ausgabe auf jeden Fall gepuffert wird
     * gzipTest: http://www.desilva.biz/gzip-test.php
     */
    ob_start();

    /**
     * die naechsten Ausgaben sind dann die hardgecodeten <head> Teile in der header.php
     * diese werden in den Puffer geschrieben. Danach geht es weiter mit der Funktion themeheader()
     */

}

?>