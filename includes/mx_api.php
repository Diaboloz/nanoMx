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
 * $Revision: 338 $
 * $Author: PragmaMx $
 * $Date: 2017-05-15 14:46:58 +0200 (Mo, 15. Mai 2017) $
 *
 * @package pragmaMx
 */

defined('mxMainFileLoaded') or die('access denied');

/**
 * Bindet die aktuelle Sprachdatei ein
 *
 * @since pragmaMx 0.1.0
 * @param string $module Name oder Pfad des Modules, von welchem die
 * Sprachdatei eingebunden werde soll
 * @return mixed Gibt false oder den Namen der aktuellen Sprachdatei zurueck.
 */
function mxGetLangfile($module = '', $filepatern = 'lang-*.php')
{
    switch (true) {
        case preg_match("#(://)|(\.\.)#", $module):
            // hackversuch?
            return false;

        case !$module:
            $langpath = PMX_LANGUAGE_DIR;
            $module = 'System';
            break;

        case $module == 'admin':
            $langpath = PMX_ADMIN_DIR . DS . 'language';
            break;

        case strpos($module, PMX_ADMINMODULES_DIR) === 0:
            $langpath = $module . DS . 'language';
            $module = 'admin.' . basename($module);
            break;

        case strpos($module, PMX_MODULES_DIR) === 0:
            $langpath = $module . DS . 'language';
            $module = basename($module);
            break;

        case strpos($module, PMX_THEMES_DIR) === 0:
            $parts = explode(DS, $module);
            $mod = array_pop($parts);
            if ($mod == 'language') {
                // manche Themes rufen das mit 'language' hintendran auf
                $langpath = $module;
                $module = array_pop($parts);
            } else {
                $langpath = $module . DS . 'language';
                $module = basename($module);
            }
            break;

        case glob(rtrim($module, DS . '/') . DS . $filepatern):
            $langpath = rtrim($module, DS . '/');
            break;

        default:
            $langpath = PMX_MODULES_DIR . DS . $module . DS . 'language';
    }

    $lang = $GLOBALS['currentlang'];
    $filename = str_replace('*', $lang, $filepatern);

    /* zuerst Benutzerdatei einbinden */
    $custom = false;
    $langfile = $langpath . DS . 'custom' . DS . $filename;
    if (file_exists($langfile)) {
        $custom = include_once($langfile);
    }
    /* dann die normale.. */
    $langfile = $langpath . DS . $filename;
    if (file_exists($langfile)) {
        $ok = false;
        if ($custom) {
            /* wenn bereits custom includet, Fehler abschalten! */
            pmxDebug::pause();
            $ok = include_once($langfile);
            pmxDebug::restore();
        } else {
            $ok = include_once($langfile);
        }
        if ($ok) {
            /* nur Temporär, das muss wieder raus !! > 3x */
            // include(PMX_LANGUAGE_DIR . DS . 'to-do.php');
            return $lang;
        }
    }

    /* deutsch_du noch beachten und ggf. includen */
    if (strpos($lang, 'german') !== false) {
        $xlang = ($lang == 'german') ? 'german_du' : 'german';
        $filename = str_replace('*', $xlang, $filepatern);

        /* zuerst Benutzerdatei einbinden */
        $custom = false;
        $langfile = $langpath . DS . 'custom' . DS . $filename;
        if (file_exists($langfile)) {
            $custom = include_once($langfile);
        }
        /* dann die normale.. */
        $langfile = $langpath . DS . $filename;
        if (file_exists($langfile)) {
            $ok = false;
            if ($custom) {
                /* wenn bereits custom includet, Fehler abschalten! */
                pmxDebug::pause();
                $ok = include_once($langfile);
                pmxDebug::restore();
            } else {
                $ok = include_once($langfile);
            }
            if ($ok) {
                /* nur Temporär, das muss wieder raus !! > 3x */
                // include(PMX_LANGUAGE_DIR . DS . 'to-do.php');
				pmxTranslate::init();
                return $xlang;
            }
        }
    }

    /* absoluten Serverpfad aus Meldung entfernen */
    $module = str_replace(DS, '/', mx_strip_sysdirs($module));

    trigger_error("language-file '" . $lang . "' for module '" . $module . "' is missing");
    /* Admin erhält auf jeden Fall eine Meldung */
    if (MX_IS_ADMIN) {
        echo mxSiteServiceMessage("<b>Error:</b> language-file '" . $lang . "' for module '" . $module . "' is missing!<br />[<a href=\"" . adminUrl('settings') . "\">edit</a>]");
    }

    /* versuchen alle sonst verfügbaren Sprachdateien zu includen */
    pmxDebug::pause();
    foreach ((array)glob($langpath . DS . $filepatern) as $langfile) {
        $ok = ($langfile && include_once($langfile));
        /* alle verfügbaren includen */
        /* um dies zu vermeiden, die nächste Zeile einkommentieren */
        // break;
    }
    pmxDebug::restore();

    /* Meldung für User nur ausgeben, wenn gar keine Sprachdatei includet */
    if (!MX_IS_ADMIN && empty($ok)) {
        echo mxSiteServiceMessage("<b>Error:</b> language-file '" . $lang . "' for module '" . $module . "' is missing!");
    }
    /* nur Temporär, das muss wieder raus !! > 3x */
    // include(PMX_LANGUAGE_DIR . DS . 'to-do.php');
    return false;
}



/**
 * Reinigen von Usereingaben / Erwzingen double oder integer.
 * Nimmt eine globale Variable und modifiziert den Inhalt, erzwingt double bzw. integer.
 *
 * @since pragmaMx 0.1.0
 * @param mixed $var Name der Variablen, es können mehrere Variablen,
 * mittels Komma getrennt, übergeben werden.
 * @return mixed Gibt die modifizierte Variable zurück, wenn nur eine Variable
 * übergeben wurde, ansonsten ein Array mit den modifizierten Werten.
 *
 * deprecated !!
 * diese Funktion wird seit pragmaMx 1.12 nicht mehr verwendet
 */
function mxForceInteger()
{
    $arr = func_get_args();
    foreach($arr as $i => $v) {
        if (empty($arr[$i])) {
            $arr[$i] = 0;
            continue;
        }
        if (is_array($arr[$i])) {
            foreach($arr[$i] as $ia => $va) {
                if (empty($arr[$i][$ia])) {
                    $arr[$i][$ia] = 0;
                    continue;
                }
                $arr[$i][$ia] = mxForceInteger($arr[$i][$ia]);
            }
        } else {
            $arr[$i] = (gettype($arr[$i]) == "double") ? (double)$arr[$i] : (int)$arr[$i];
        }
    }
    $out = (func_num_args() == 1) ? $arr[0] : $arr;
    return $out;
}

/**
 * Modifiziert den Variableninhalt zum Speichern in der Datenbank (Escapen von Zeichen).
 * Umkehrfunktion von mxStripSlashes().
 *
 * Nimmt eine Variable und modifiziert den Inhalt, so dass dieser ohne Probleme
 * in der Datenbank gespeichert werden kann.
 *
 * @since pragmaMx 0.1.0
 * @param mixed $what Name der Variablen, deren Inhalt modifiziert werden soll.
 * @return mixed Gibt die modifizierte Variable zurück, wenn nur eine Variable
 * übergeben wurde, ansonsten ein Array mit den modifizierten Werten.
 */
function mxAddSlashesForSQL($what)
{
    /* wenn $what numerisch oder leer ist, unverändert zurückgeben */
    if (is_numeric($what) || empty($what)) return $what;
    static $entity;
    if (empty($entity)) {
        /* eindeutige Kennung erstellen */
        $entity = md5(mt_rand());
    }
    /* wenn $what ein Array, das Array durchlaufen
     * und die Funktion rekursiv aufrufen */
    if (is_array($what)) {
        foreach ($what as $key => $value) {
            $what[$key] = mxAddSlashesForSQL($value);
        }
    }
    /* wenn String */
    else {
        /* erwünschte Backslashes in eindeutigen String umwandeln */
        $what = str_replace("\\\\", $entity, $what);
        /* alle anderen Backslashes entfernen */
        $what = stripslashes($what);
        /* erwünschte Backslashes wieder zurückwandeln */
        $what = str_replace($entity, "\\", $what);
        /* sicherstellen, dass die richtige Anzahl von Backslashes wieder eingefügt werden */
        $what = addslashes($what);
    }
    return $what;
}

/**
 * Modifiziert eine Datenbankausgabe (Escapen von Zeichen rückgängig machen).
 * Umkehrfunktion von mxAddSlashesForSQL().
 *
 * Nimmt eine Variable und modifiziert den Inhalt, so dass dieser ohne Probleme
 * angezeigt werden kann.
 *
 * @since pragmaMx 0.1.0
 * @param mixed $what Name der Variablen, deren Inhalt modifiziert werden soll.
 * @return mixed Gibt die modifizierte Variable zurück, wenn nur eine Variable
 * übergeben wurde, ansonsten ein Array mit den modifizierten Werten.
 */
function mxStripSlashes(&$what)
{
    /* wenn $what leer ist, Leerstring zurückgeben */
    if (empty($what)) return "";
    /* eindeutige Kennung erstellen */
    static $entity;
    if (empty($entity)) {
        $entity = md5(mt_rand());
    }
    if (is_numeric($what) || empty($what)) {
        // numerische und leere Werte nicht behandeln
        return $what;
    } else if (is_array($what)) {
        /* wenn Array, rekursiv aufrufen */
        array_walk($what, 'mxStripSlashes');
    } else if (is_string($what)) {
        /* nur wenn String...
         * erwünschte Backslashes in eindeutigen String umwandeln */
        $what = str_replace("\\\\", $entity, $what);
        /* alle anderen Backslashes entfernen */
        $what = stripslashes($what);
        /* erwünschte Backslashes wieder zurückwandeln */
        $what = str_replace($entity, "\\", $what);
    }
    return $what;
}

/**
 * Konvertiere ein "newline" (\n) in einen HTML <br /> - Tag.
 * Umkehrfunktion von mxUndoNltobr()
 *
 * @since pragmaMx 0.1.0
 * @param string $value Name der Variablen, deren Inhalt konvertiert werden soll.
 * @return string Gibt den konvertierten String zurück.
 */
function mxNL2BR($value)
{
    if (strip_tags($value) === $value) {
        return nl2br($value);
    }
    return $value;
}

/**
 * Konvertiere einen HTML <br /> - Tag in ein "newline" (\n)
 * Umkehrfunktion von mxNL2BR()
 *
 * @since pragmaMx 0.1.0
 * @param string $text Name der Variablen, deren Inhalt konvertiert werden soll
 * @return string Gibt den konvertierten String zurück
 */
function mxUndoNltobr($text)
{
    return preg_replace('/<br[ \/]*>(\r\n|\r|\n)?/i', PHP_EOL, $text);
}

/**
 * Rückgängigmachen des SQL-Injection Fixes um die Originalzeichen zurück zu erhalten.
 *
 * @since pragmaMx 0.1.0
 * @deprecated Funktion wird seit pragmaMx 0.1.6 nicht mehr verwendet.
 * Existiert nur noch aus Kompatibilitätsgründen und ist bei neuen
 * Entwicklungen nicht mehr einzusetzen.
 * @param string $ &$value Name der Variablen, deren Inhalt konvertiert werden soll.
 * @return string Gibt den konvertierten String zurück.
 */
function mxUndoSqlInjectFix(&$value)
{
    if (!empty($value)) {
        if (!is_long($value) && !is_int($value) && !is_double($value) && !is_object($value) && !is_array($value)) {
            $value = str_replace(array('&#41;', '&#124;', '&#45;&#45;', '&#35;'), array(')', '|', '--', '#'), $value);
            $value = str_replace('&amp;#', '&#xxxxxxxx', $value);
        }
    }
    return $value;
}

/**
 * Variablenwert für die Ausgabe aufbereiten / Schutz vor E-Mail-Sammlern.
 * Sucht den Text 'x@y' und ersetzt ihn durch die entsprechenden HTML-Entitäten,
 * was einen gewissen Schutz vor E-Mail-Sammlern bietet.
 *
 * @since pragmaMx 0.1.0
 * @param mixed $var Variable oder Array, deren Wert(e) modifiziert werden soll(en).
 * @return mixed Gibt die modifizierte Variable zurück, wenn nur eine Variable
 * übergeben wurde, ansonsten ein Array mit den modifizierten Werten.
 * Barrierefrei
 
 */
function mxPrepareToDisplay()
{
    $resarray = array();
    foreach (func_get_args() as $var) {
        /* Add to array */
        $resarray[] = preg_replace_callback('/(.)@(.)/s',
            create_function('$match',
                'return "&#".sprintf("%03d", ord($match[1])).";&#064;&#".sprintf("%03d", ord($match[2])).";";'
                ),
            $var
            );
    }
    if (func_num_args() == 1) {
        /* Return vars */
        return $resarray[0];
    } else {
        return $resarray;
    }
}

/**
 * Variablenwert für die Ausgabe aufbereiten / Schutz vor E-Mail-Sammlern.
 * Sucht den Text 'x@y' und ersetzt ihn durch einen reversen String, verpackt in einer CSS-Klasse, 
 * welche die emailadresse wieder richtig dreht.
 * Das bietet einen gewissen Schutz vor E-Mail-Sammlern.
 *
 * @since pragmaMx 2.2.4
 * @param mixed $var Variable oder Array, deren Wert(e) modifiziert werden soll(en).
 * @return mixed Gibt die modifizierte Variable zurück, wenn nur eine Variable
 * übergeben wurde, ansonsten ein Array mit den modifizierten Werten.
 * nicht barrierefrei !!!
 */
function mxPrepareToDisplay_af(){
	   $resarray = array();
    foreach (func_get_args() as $var) {
          /* Add to array */
		  $results = preg_match_all('#[a-z0-9\-_]?[a-z0-9.\-_]+[a-z0-9\-_]?@[a-z.-]+\.[a-z]{2,}#i', $var, $subpattern);
		  //
		  // Neues Array initialisieren
		  //
		  $emails = array();
		  //
		  // Rückgabe durchlaufen und ersetzung definieren
		  //
		  foreach($subpattern[0] as $email) {
			$emails[$email] = "<span class=\"codedirection\">".str_replace("@","&#064;",utf8_strrev($email))."</span>";
		  }
		  //
		  // Duplikate entfernen
		  //
		  $emails = array_unique($emails);
		  
		  // jetzt alles, was nicht in Tags steht austauschen
		  $var = mxChangeContent($var, $emails,0);
		  
		  // jetzt noch den Rest nach der alten Methode behandeln.
		  $resarray[]= preg_replace_callback('/(.)@(.)/s',
            create_function('$match',
                'return "&#".sprintf("%03d", ord($match[1])).";&#064;&#".sprintf("%03d", ord($match[2])).";";'),$var);
    }
    if (func_num_args() == 1) {
        /* Return vars */
        return $resarray[0];
    } else {
        return $resarray;
    }
}	
/**
 * String wird komplett umgedreht
 */
function utf8_strrev($str){
	if (!is_string( $str)) return $str;
	
    preg_match_all('/./us', $str, $ar);
    return join('',array_reverse($ar[0]));
}


/**
 * Gibt ein Array mit den erlaubten HTML-Tags oder PCRE zurück
 *
 * Die erlaubten HTML-Tags werden aus der Konfiguration ausgelesen und in einem
 * Array zurückgegeben, um diese in weiteren Berarbeitungsschritten
 * (z.B. preg_match etc.) verwenden zu können.
 *
 * @since pragmaMx 0.1.6
 * @staticvar mixed $allowedhtmlpreg Statische Variable um die PCRE zu speichern.
 * @staticvar mixed $allowedtags Statische Variable um die HTML-Tags zu speichern.
 * @param mixed $as_preg Variable um zwischen Rückgabe HTML-Tags oder PCRE umzuschalten.
 * @return mixed Gibt ein Array mit HTML-Tags oder PCRE zurück.
 */
function mxGetAllowedHtml($as_preg = '')
{
    static $allowedhtmlpreg;
    static $allowedtags;
    if (!isset($allowedhtmlpreg)) {
        $allowedhtmlpreg = array();
        foreach($GLOBALS['AllowableHTML'] as $tag => $para) {
            switch ($para) {
                case 0:
                    continue;
                    break;
                case 1:
                    $allowedhtmlpreg[] = "|<(/?$tag)\s*/?>|i";
                    $allowedtags[] = $tag;
                    break;
                case 2:
                    $allowedhtmlpreg[] = "|<(/?$tag(\s+.*?)?)>|i";
                    $allowedtags[] = $tag;
                    break;
            }
        }
    }
    if ($as_preg) {
        return $allowedhtmlpreg;
    }
    return $allowedtags;
}

/**
 * Variablenwert für die Ausgabe aufbereiten / Schutz vor E-Mail-Sammlern.
 * Sucht den Text 'x@y' und ersetzt ihn durch die entsprechenden HTML-Entitäten,
 * was einen gewissen Schutz vor E-Mail-Sammlern bietet.
 *
 * @since pragmaMx 0.1.0
 * @deprecated Funktion wird seit pragmaMx 0.1.6 nicht mehr verwendet.
 * Macht aus Kompatibilitätsgründen dasselbe wie mxPrepareToDisplay()
 * @param mixed $var Variable oder Array, deren Wert(e) modifiziert werden soll(en).
 * @return mixed Gibt die modifizierte Variable zurück, wenn nur eine Variable
 * übergeben wurde, ansonsten ein Array mit den modifizierten Werten.
 */
function mxPrepareToHTMLDisplay()
{
    return call_user_func_array("mxPrepareToDisplay", func_get_args());
}

/**
 * Zensierte Wörter entfernen
 *
 * @param mixed $value Variable deren Inhalt Zensiert werden soll
 * @return mixed Liefert die modifizierte Variable zurück
 */
function mxPrepareCensored($value)
{
    switch (true) {
        case empty($value);
        case empty($GLOBALS['CensorMode']);
            return $value;
        case is_array($value):
            /* wenn $value ein Array, das Array durchlaufen und die Funktion rekursiv aufrufen */
            foreach ($value as $key => $var) {
                $value[$key] = mxPrepareCensored($var);
            }
            return $value;
        default:
            static $search = array();
            if (empty($search)) {
                $repsearch = array('/o/i', '/e/i', '/a/i', '/i/i');
                $repreplace = array('0', '3', '@', '1');
                $badwords = $GLOBALS['CensorList'];
                foreach ($badwords as $badword) {
                    $search[] = "#\b" . preg_quote($badword, '#') . "\b#i"; // Simple word
                    $mungedword = preg_replace($repsearch, $repreplace, $badword); // Common replacements
                    if ($mungedword != $badword) {
                        $search[] = "#\b" . preg_quote($mungedword, '#') . "\b#i";
                    }
                }
            }

            $value = preg_replace($search, $GLOBALS['CensorReplace'], $value); // Parse out nasty words
            return $value;
    }
}

/**
 * mxGetAvailableThemes()
 * ermittelt die verfügbaren Themes
 * mit dem Parameter $admintheme kann die Auswahl auf Adminbereichs-Themes
 * eingeschränkt werden
 *
 * @param boolean $admintheme
 * @return array
 */
function mxGetAvailableThemes($admintheme = false)
{
    $cache = load_class('Cache');
    if (!(($themelist = $cache->read(__function__ . strval($admintheme))) === false)) {
        return $themelist;
    }

    $themelist = array();
    foreach ((array)glob(PMX_THEMES_DIR . DS . '*' . DS . 'theme.php', GLOB_NOSORT) as $theme) {
        $theme = basename(dirname($theme));
        if ($theme && !preg_match('#[^A-Za-z0-9_-]#', $theme)) {
            $is_admintheme = (preg_match('#(^admin[-_.])|([-_.]admin$)#i', $theme));
            switch (true) {
                case $admintheme && $is_admintheme:
                    $themelist['*' . strtolower($theme)] = $theme;
                    break;
                case $admintheme:
                case !$admintheme && !$is_admintheme:
                    $themelist[strtolower($theme)] = $theme;
                    break;
            }
        }
    }
    ksort($themelist);

    $cache->write($themelist, __function__ . strval($admintheme), 18000); // 5 Stunden Cachezeit
    return $themelist;
}

/**
 * mxGetAvailableLanguages()
 * ermittelt die verfügbaren Sprachen
 *
 * @param boolean $getallfromfolder
 * @return array
 */
function mxGetAvailableLanguages($getallfromfolder = false)
{
    switch (true) {
        case $getallfromfolder:
            foreach ((array)glob(PMX_LANGUAGE_DIR . DS . 'lang-*.php', GLOB_NOSORT) as $filename) {
                if ($filename && preg_match('#^lang-(.+)\.php$#', basename($filename), $matches)) {
                    $key = mxGetLanguageString($matches[1]);
                    $langlist[$key] = $matches[1];
                }
            }
            break;
        case empty($GLOBALS['language_avalaible']) || !is_array($GLOBALS['language_avalaible']):
            return mxGetAvailableLanguages(true);
            break;
        default:
            $langlist = array();
            foreach ($GLOBALS['language_avalaible'] as $value) {
                $key = mxGetLanguageString($value);
                $langlist[$key] = $value;
            }
    }
    ksort($langlist);
    return $langlist;
}

/**
 * mxGetLanguageString()
 * ermittelt die Sprachkonstante einer verfügbaren Sprache
 *
 * @param mixed $language
 * @return
 */
function mxGetLanguageString($language)
{
    if ($language == 'german_du') {
        $language = _LANGGERMAN . ' (Du)';
    } else {
        $language = strtoupper($language);
        if (defined('_LANG' . $language)) {
            $language = constant('_LANG' . $language);
        }
    }
    return ucwords($language);
}

/**
 * Namen des verwendeten Themes abfragen
 *
 * @static string $usertheme Statische Variable um den Namen des verwendeten Themes zu speichern
 * @return returns Liefert den namen des verwendeten Themes zurück
 */
function mxGetTheme()
{
    static $usertheme;
    if (isset($usertheme)) {
        return $usertheme;
    }
	/* einstellungen laden */
	$themes = load_class("Config","pmx.themes");
	
	/* nur noch abwärtskompatibilität */
	$GLOBALS['admintheme']=$themes->admintheme;
	$GLOBALS['Default_Theme']=$themes->defaulttheme;
	$GLOBALS['mobiletheme']=$themes->getValue('mobiletheme',"pmx.themes",$GLOBALS['Default_Theme']);

	/* userdaten laden */
    $usertheme = '';
    if (MX_IS_USER) {
        $userdata = pmxUserStored::current_userdata();
        $usertheme = $userdata['theme'];
    }

    switch (true) {
        case MX_IS_ADMIN && defined('mxAdminFileLoaded') && !empty($GLOBALS['admintheme']):
            $usertheme = $GLOBALS['admintheme'];
			break;
		case MX_MOBILE_DEVICE:/* mobile theme einstellen */
			$usertheme = $GLOBALS['mobiletheme'];
			//break;
        case (!MX_IS_USER && !MX_IS_ADMIN && isset($_COOKIE['theme'])):
            $usertheme = $_COOKIE['theme'];
            break;
        case ((MX_IS_USER || MX_IS_ADMIN) && mxSessionGetVar('theme')):
            $usertheme = mxSessionGetVar('theme');
            break;
        case (MX_IS_USER && $usertheme):
            break;
        default:
            $usertheme = $GLOBALS['Default_Theme'];
    }
	
	
	
	
    /* auf Sonderzeichen im themenamen prüfen und ggf. entfernen */
    if (preg_match('#[^A-Za-z0-9_-]#', $usertheme)) {
        if (MX_IS_ADMIN) {
            echo mxSiteServiceMessage("<b>Error:</b> Special chars are not permitted in the theme-name (" . $usertheme . ")!<br />[&nbsp;<a href=\"" . adminUrl('settings') . "\">edit</a>&nbsp;]");
        }
        $usertheme = preg_replace('#[^A-Za-z0-9_-]#', '_', $usertheme);
    }

    /* wenn theme nicht vorhanden, das Standardtheme verwenden */
    if (!is_file(PMX_THEMES_DIR . DS . $usertheme . DS . 'theme.php')) {
        $usertheme = $GLOBALS['Default_Theme'];
        /* wenn das auch fehlt, das erste verfügbare theme suchen */
        if (!is_file(PMX_THEMES_DIR . DS . $usertheme . DS . 'theme.php')) {
            $themelist = mxGetAvailableThemes();
            $usertheme = array_shift($themelist);
            if (MX_IS_ADMIN) {
                echo mxSiteServiceMessage("<b>Error:</b> the default-theme '" . $GLOBALS["Default_Theme"] . "' is missing!<br />[&nbsp;<a href=\"" . adminUrl('settings') . "\">edit</a>&nbsp;]");
            }
        }
    }

    return $usertheme;
}

/**
 * erzeugt ein verstecktes Feld um die Gültigkeit der Session
 * beim Userlogin anzuzeigen, zusätzlich die benötigten Hidden-Felder
 * Diese Funktion muss in Themes eingesetzt werden, die ein Userlogin haben
 * Aufruf: echo mxGetUserLoginCheckField();
 *
 * @return string Liefert die benötigten Felder für ein HTML-Formular zurück
 */
function mxGetUserLoginCheckField()
{
    if (isset($GLOBALS['JPCACHE_ON']) && $GLOBALS['JPCACHE_ON'] && !func_num_args()) {
        // Platzhalter wenn jp-Cache eingeschaltet,
        // dies wird beim Aufruf in jp-Cache Funktion jpcache_flush() durch den
        // korrekten Inhalt ersetzt
        // Achtung für künftige Entwicklung:
        // in dieser Funktion darf, wegen dem jp-Cache, kein
        // Ausgabepuffer gestartet werden !!
        return '<!-- mxgetuserlogincheckfield_' . md5($GLOBALS['mxSecureKey']) . ' -->';
    }
    // if, falls bereits im YA-Modul oder Loginblock definiert
    if (!mxSessionGetVar('uloginreqcheck')) {
        mxSessionSetVar('uloginreqcheck', mt_rand());
    }
    $out = "<input type=\"hidden\" name=\"check\" value=\"" . MD5(mxSessionGetVar('uloginreqcheck')) . "\" />\n"
     . "<input type=\"hidden\" name=\"check_cookie\" value=\"" . MD5(session_id()) . "\" />\n"
     . "<input type=\"hidden\" name=\"name\" value=\"Your_Account\" />\n"
     . "<input type=\"hidden\" name=\"op\" value=\"login\" />\n";
    return $out;
}

/**
 * überprüft, ob der Nutzer als Admin eingeloggt ist
 *
 * @static bool $isadmin Statische Variable um den Status zu speichern
 * @return boolean
 */
function mxIsAdmin()
{
    return pmxUserStored::isadmin();
}

/**
 * Überprüft, ob der Nutzer als Admin eingeloggt ist
 *
 * @static bool $isuser Statische Variable um den Status zu speichern
 * @return boolean
 */
function mxIsUser()
{
    return pmxUserStored::isuser();
}

/**
 * Abfragen der User-Session
 *
 * @static mixed $stat_cookie Variable zum Speichern der Ergebnisse
 * @return mixed Liefert die globale Variable
 */
function mxGetUserSession()
{
    return pmxUserStored::current_usersession();
}

/**
 * Abfragen der User-Daten des aktuellen Users.
 *
 * @staticvar mixed $myuserinfo Variable zum Speichern der Ergebnisse
 * @param bool $dummy_forceDB is deprecated and unused
 * @return mixed Liefert ein Array mit den Userdaten des aktuellen Users zurück
 */
function mxGetUserData($dummy_forceDB = true)
{
    return pmxUserStored::current_userdata();
}

/**
 * Abfragen der User-Daten von einem bestimmten User mit Usernamen als Abfrageparameter
 *
 * @static mixed $myuserinfo Variable zum Speichern der Ergebnisse
 * @param string $username Name des Users dessen Daten gebraucht werden
 * @return mixed Liefert ein Array mit den Userdaten des Users zurück
 */
function mxGetUserDataFromUsername($username)
{
    static $userinfo;

    $username = substr(trim($username), 0, 25);

    if (!isset($userinfo[$username])) {
        $userinfo[$username] = pmxUserStored::getuserdata("uname='" . mxAddSlashesForSQL($username) . "'");
    }
    return $userinfo[$username];
}

/**
 * Abfragen der User-Daten von einem bestimmten User mit Userid als Abfrageparameter
 *
 * @static mixed $myuserinfo Variable zum Speichern der Ergebnisse
 * @param int $uid Userid des Users dessen Daten gebraucht werden
 * ansonsten werden die Daten aus dem statischen Array zurückgegeben
 * @return mixed Liefert ein Array mit den Userdaten des Users zurück
 */
function mxGetUserDataFromUid($uid)
{
    static $userinfo;

    $uid = intval($uid);

    if (!isset($userinfo[$uid])) {
        $userinfo[$uid] = pmxUserStored::getuserdata('uid=' . $uid);
    }
    return $userinfo[$uid];
}

/**
 * Dekodiert den "Admincookie" des aktuell angemeldeten Admins
 * Beispiel Aufruf: extract(mxGetAdminSession());
 *
 * @static mixed $data Variable zum Speichern der Ergebnisse
 * @return mixed Liefert ein Array mit den Ergebnissen
 */
function mxGetAdminSession()
{
    return pmxUserStored::current_adminsession();
}

/**
 * Liest alle Administratoreigenschaften des
 * aktuell angemeldeten Admins aus der Tabelle
 * Aufrufbeispiel: $arrayadmin = mxGetAdminData();
 *
 * @return mixed Liefert ein Array mit den Ergebnissen
 */
function mxGetAdminData()
{
    return pmxUserStored::current_admindata();
}

/**
 * Prüft ob der aktuell angemeldete
 * Admin eine bestimmte Berechtigung hat
 * Aufrufbeispiel: $xx = mxGetAdminPref('radminarticle', 'radminuser');
 *
 * @param string $preference Berechtigung, die abgefragt werden soll
 * @return bool Liefert das Ergebnis (true/false)
 */
function mxGetAdminPref()
{
    if (!MX_IS_ADMIN) {
        return false;
    }

    $allprefs = pmxUserStored::current_admindata();

    if ($allprefs['radminsuper']) {
        return true;
    }

    $args = func_get_args();
    foreach ($args as $preference) {
        if ($allprefs[$preference]) {
            return true;
        }
    }
    return false;
}

/**
 * Ermittelt ob ein User online ist
 *
 * @param string $username Username des Users für den der Onlinestatus abgefragt werden soll
 * @return bool Liefert das Ergebnis (true/false)
 */
function mxIsUserOnline($username)
{
    $data = mxGetUserDataFromUsername($username);
    return !empty($data['user_online']);
}

/**
 * permission_granted()
 * Ermittelt die Zugriffsrechte von Benutzergruppen auf eine bestimmte Berechtigung
 *
 * @param mixed $permission
 * @param mixed $usergroups
 * @return boolean
 */
function permission_granted($permission, $usergroups)
{
    $access = 0;
    foreach ($usergroups as $group) {
        /* wenn Usergruppe nicht in den Berechtigungen */
        if (!isset($permission[$group])) {
            if (isset($permission[0])) {
                /* aber Standardwert für Berechtigung vorhanden  */
                $permission[$group] = $permission[0];
            } else {
                /* ansonsten, abschalten */
                $permission[$group] = 'off';
            }
        }

        switch (true) {
            case $permission[$group] === 'deny':
                /* verboten, direkt raus... */
                return false;
            case $permission[$group] === 'on':
                $access++;
        }
    }

    return ($access > 0);
}



/**
 * Beschreibung
 *
 * @param string $action optional, default value ""
 * @return
 */
function mxYoubad($action, $doblock = false, $account = '')
{
    if ($doblock) {
        // $doblock wird in vkpIpBanning() ausgewertet
        mxSessionSetVar("blocked", true);
    }
    $subject = "Bad work on my site";

    $action = (empty($action)) ? $subject : $action;

    $message = "Aktion: " . $action . "
    IP : " . MX_REMOTE_ADDR . "
    port: " . $_SERVER['REMOTE_PORT'] . "
    hostname : " . gethostbyaddr(MX_REMOTE_ADDR) . "
    time: " . date("Y-m-d h:i:s") . "
    browser: " . MX_USER_AGENT . "
    data: " . serialize($_REQUEST);

    mxMail($GLOBALS['adminmail'], $subject, $message);
    mxSecureLog("bad Request", $action, $account, true);

    return mxErrorScreen(_ERR_YOUBAD);
}

/**
 * Zum Erstellen der Bildinformationen
 * mxCreateImage('themes/' . $thename . '/images/logo.gif', 'alternate Text',
 * 'border', 'moreparameter', 'xhtml', 'captcha');
 *
 * @param string $image
 * @param string $alt optional, default value ''
 * @param mixed $border_or_attribs optional, default value 0
 * - as integer is deprecated
 * - border should be styled with CSS using this param as array
 * @param string $more optional, default value ''
 * @param boolean $xhtml optional, default value true
 * @param boolean $captcha deprecated! optional, default value false
 * @param boolean $dimensions optional, default value true
 * @return string
 */
function mxCreateImage($image, $alt = '', $border_or_attribs = 0, $more = '', $xhtml = true, $captcha = false, $dimensions = true)
{
    /* Systempfade am Anfang entfernen */
    $image = str_replace(DS, '/', mx_strip_sysdirs($image));

    if (!is_file($image)) {
        $pathinfo = parse_url($image);
        if (!isset($pathinfo['scheme'])) {
            if (MX_IS_ADMIN) {
                trigger_error('[' . __function__ . '] missing image: ' . $image, E_USER_NOTICE);
                return '<img src="' . PMX_IMAGE_PATH . 'noimg.png" alt="no image" title="missing image: ' . $image . '" />';
            }
            return $alt;
        }
    }

    if (is_array($border_or_attribs)) {
        /* wenn als Array angegeben, alle weitern Parameter ignorieren */
        $more = '';
        $xhtml = true;
        $captcha = false;
        $dimensions = true;
        $attr = $border_or_attribs;
    } else {
        settype($border_or_attribs, 'float');
        if ($border_or_attribs) {
            $attr['border'] = $border_or_attribs;
        } else {
            $attr = array();
        }
    }

    $type = 0;
    switch (true) {
        case !$dimensions:
        case stristr($more, 'width'):
        case stristr($more, 'height'):
        case isset($attr['width']);
        case isset($attr['height']);
            // Alternativ auch "externe" Grössenangaben über den
            // more-Parameter oder gar keine Grössenangaben zulassen
            break;

        default:
            pmxDebug::pause();
            if ($size = GetImageSize($image)) {
                $attr['width'] = $size[0];
                $attr['height'] = $size[1];
                $type = $size[2];
            }
            pmxDebug::restore();
    }

    switch (true) {
        case isset($attr['alt']):
        case $more && stripos('alt=', $more) !== false:
            break;

        case !$alt:
            $pathinfo = pathinfo($image);
            $attr['alt'] = $pathinfo['filename'];
            if (strcasecmp($pathinfo['extension'], 'png') == 0) {
                $type = IMAGETYPE_PNG;
            }
            break;

        default:
            $attr['alt'] = $alt;
    }

    if (!$type && stripos($image, '.png') !== false) {
        $type = IMAGETYPE_PNG;
    }

    /* is the file a PNG? if so, check user agent, we will need to make special allowances for Microsoft IE. */
    if ($type === IMAGETYPE_PNG) {
        $browser = load_class('Browser');
        if ($browser->msie && $browser->version < 7) {
            /* in der default.css.php, wird die Klasse .png mit dem Filter definiert */
            switch (true) {
                case isset($attr['class']) && $attr['class'] != 'png':
                    $attr['class'] .= ' png';
                    break;
                case stristr($more, 'class="'):
                    $more = str_replace('class="', 'class="png ', $more);
                    break;
                default:
                    $attr['class'] = 'png';
            }
        }
    }

    $para = '';
    foreach ((array) $attr as $key => $val) {
        if ($val === null) {
            continue;
        }

        if (is_array($val)) {
            $val = implode(' ', $val);
        }

        $key = htmlspecialchars($key);
        $val = htmlspecialchars($val);

        $para .= " $key=\"$val\"";
    }

    if ($more) {
        $para .= ' ' . $more;
    }
    if ($xhtml) {
        $para .= ' /';
    }
    return '<img src="' . $image . '"' . $para . '>';
}

/**
 * Kürzt einen String ($string) auf eine bestimmte Länge ($len)
 * und fügt einen beliebigen String ($add) an das Ende
 *
 * @param string $string Variable dessen Inhalt gekürzt werden soll
 * @param int $len Anzahl der Zeichen ab denen gekürzt
 * @param string $add Zeichen welche an den gekürzten String angehängt werden sollen
 * @param string $cutter Trennzeichen#
 * @return string Liefert den gekürzten String zurück
 */

function mxCutString($string, $len, $add = "..", $cutter = " ")
{
    if (utf8_strlen($string) > $len) {
        $string = utf8_substr ($string, 0, $len + utf8_strlen($add));
        $last = (empty($cutter)) ? $len : utf8_strrpos ($string, $cutter);
        $last = (empty($last)) ? $len : $last;
        $string = trim(utf8_substr($string, 0, $last)) . $add;
    }
    return $string;
}

/**
 * kann verwendet werden um Variablen und Arrays in der HTML-Seite auszugeben
 * nur zum debuggen !!
 */
function mxDebugFuncvars()
{
    echo "<pre style='font-size: 11px; text-align: left; background: #FCFCFC; color: Black;'>\n";
    $args = func_get_args();
    foreach ($args as $i => $value) {
        echo 'Argument <b>', ($i + 1) , '</b>: ', trim(wordwrap(htmlspecialchars(print_r($value, true)), 200)), PHP_EOL;
    }
    echo "</pre>";
}

/**
 * Debug-Funktion
 * by dany dot dylan at gmail dot com
 * found: http://de3.php.net/manual/de/function.debug-print-backtrace.php
 *
 * @param bool $print optional, default value true
 * @return string
 */
function mxDebugBacktrace($print = true)
{
    ob_start();
    debug_print_backtrace();
    $trace = ob_get_contents();
    ob_end_clean();
    // Remove first item from backtrace as it's this function which
    // is redundant.
    $trace = preg_replace ('/^#0\s+' . __FUNCTION__ . "[^\n]*\n/", '', $trace, 1);
    // Renumber backtrace items.
    $trace = preg_replace_callback('/^#(\d+)/m', function ($m)
        {
            return '#' . ($m[0] - 1);
        } , $trace);

    if ($print) {
        echo $trace;
    }

    return $trace;
}
/**
 * Beschreibung
 *
 * @param string $url
 * @return string
 */
function mxCheckURL($uri="")
{
	if (!preg_match("/\b(?:(?:http|https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",$uri)) return "1-false";
    //if (!preg_match( '/(?:(?:http|https?|ftp):\/\/|www\.)[a-z0-9_]+([\\-\\.]{1}[a-z_0-9]+)*\\.[_a-z]{2,5}((:[0-9]{1,5})?\\/.*)?$/i' ,$uri)) return"2-false";
	return "true";
}

/**
 * Beschreibung
 *
 * @param string $url
 * @return string
 */
function mxCutHTTP($url)
{
    $url = trim($url);
    if ($url == "http://" || $url == "https://" || $url == "") return "";
    return (preg_match('#https?://#i', $url)) ? $url : "http://$url";
}

/**
 * Wertet RSS-Feeds aus und bereitet die Daten zur Anzeige vor
 *
 * @uses RSS_feed
 * @param string $url URL von dem RSS-Feed der angezeigt werden soll
 * @return string Liefert die aufbereiteten daten zurück
 */
function mxGetRssContent($url)
{
    $url = mxCutHTTP($url);
    // zur Sicherheit, die entities richtigstellen
    $url = mx_urltohtml($url);
    include_once(PMX_SYSTEM_DIR . '/class_RSS_feed.php');
    $feed_parser = new RSS_feed();
    $feed_parser->Set_URL($url);
    // die Konfigwerte die an die Klasse übermittelt werden, sollte auch noch im Konfig-File eingebunden werden
    $feed_parser->Set_Limit(0); // 0 = Default = Alle. Anzahl der Einträge in der Liste
    $feed_parser->Show_Image(false); // Default = false. Zeige Bilddaten (bei höheren Versionen als 0.91)
    $feed_parser->Show_Description(false); // Default = false. Zeige Description-Data (bei höheren Versionen als 0.91)
    $feed_parser->Set_Encoding('utf-8');
    // Ende der Konfiguration
    $content = $feed_parser->Get_Results();

    switch (true) {
        case preg_match('#<ul>(.*)</ul>#is', $content, $parts):
            // Ausgabe aufbereiten
            $content = '<ul class="list">' . strip_tags($parts[1], '<li><a>') . '</ul>';
            $content .= '<p class="align-right"><a href="' . $url . '" target="_blank" title="' . strip_tags($parts[1]) . '"><b>' . _READMORE . '</b></a></p>';
            return $content;
        case strpos($content, _XMLERROROCCURED) === 0:
            return $content;
        default:
            return '';
    }
}

/**
 * Setzt oder löscht einen Cookie mit korrekten Parametern
 * entsprechend den Einstellungen des Session-Cookies
 *
 * @param string $cookiename Name für den zu erstellenden Cookie
 * @param string $cookievalue Wert für den Cookie
 * @param string $cookietime Gültigkeitsdauer des Cookie's
 * Wenn $cookietime nicht gesetzt ist, wird der cookie beim beenden des scripts gelöscht
 * Wenn $cookietime < 0 wird der Cookie sofort gelöscht und wenn $cookietime == 1 wird die
 * $cookietime auf die Session-Dauer gesetzt . Ansonsten ird die übergebene Zeit verwendet
 */
function mxSetCookie($cookiename, $cookievalue, $cookietime)
{
    $CookieInfo = session_get_cookie_params();

    switch (true) {
        case $cookievalue === null:
        case $cookietime === null:
        case $cookietime < 0:
            // sofort löschen
            $cookietime = MX_TIME - $CookieInfo['lifetime'];
            $cookievalue = '';
            break;
        case empty($cookietime):
        case empty($CookieInfo['lifetime']):
            // beim beenden löschen
            $cookietime = 0;
            break;
        case $cookietime == 1:
            // Dauer der session-lifetime
            $cookietime = MX_TIME + $CookieInfo['lifetime'];
            break;
        default:
            // ansonsten wird Dauer der angegebenen Zeit angenommen
            $cookietime = intval($cookietime);
    }

    switch (true) {
        case empty($CookieInfo['domain']) && empty($CookieInfo['secure']):
            return setcookie($cookiename, $cookievalue, $cookietime, $CookieInfo['path']);
        case empty($CookieInfo['secure']):
            return setcookie($cookiename, $cookievalue, $cookietime, $CookieInfo['path'], $CookieInfo['domain']);
        default:
            return setcookie($cookiename, $cookievalue, $cookietime, $CookieInfo['path'], $CookieInfo['domain'], $CookieInfo['secure']);
    }
}

/**
 * Setzt oder löscht die alten Cookies von phpNuke
 * wenn in config.php entsprechend eingestellt
 * Dies kann für bestimmte phpNuke-Module nötig sein
 *
 * @param string $CookieName Name für den zu erstellenden Cookie
 * @uses mxSetCookie()
 * @param string $CookieValue Wert für den Cookie
 * @param string $inout Gültigkeitsdauer des Cookie's
 * Wenn $inout nicht gesetzt ist, wird der Cookie sofort gelöscht
 * Wenn $inout == 1 wird die Lebensdauer des Cookie auf die Session-Dauer gesetzt.
 *
 * deprecated !!
 */
function mxSetNukeCookie($CookieName, $CookieValue = "", $inout = 0)
{
   trigger_error('Use of deprecated phpnuke-function ' . __FUNCTION__ . '()', E_USER_WARNING);
    if (empty($GLOBALS['mxCreateNukeCookie'])) return;
    $inout = (empty($CookieValue)) ? 0 : $inout;
    $CookieTime = ($inout == 1) ? 1 : - 1;
    mxSetCookie($CookieName, $CookieValue, $CookieTime);
}

/**
 * Rekonvertierung von html_entities in Umlaute
 * Funktion ist ab PHP 4.3.0 in PHP enthalten ABER macht Probleme!
 *
 * @param string $string Text, welcher HTML-Entitäten enthalten kann
 * @return string konvertierter Text, in dem die Enttitäten wieder in normale Umlaute zurückgewandelt
 * sind
 */
function mxHtmlEntityDecode($string)
{
    return html_entity_decode($string, ENT_COMPAT, 'UTF-8');
}

/**
 * Aktivieren und Deaktivieren des Bulk-Mail Modus für die Funktion mx_mail()
 *
 * @param bool $what Zum Einschalten des Bulkmailings ==1, zum Ausschalten ==0
 * @param string $mode - Bulmail Versandmodus (nur mit Sendmail & EXIM kompatibelen MTA
 * bei allen anderen MTA wird ein anderer Wert als 'q' einen Fehler provozieren)
 * Mögliche Werte für $modus sind :
 * SENDMAIL_DELIVERY_DEFAULT = ''
 * Does not override the default mode.
 *
 * SENDMAIL_DELIVERY_INTERACTIVE - 'i'
 * Attempt to send the messages synchronously to the recipient's SMTP server and only returns when
 * it succeeds or fails. This is usually the default mode. It stalls the delivery of messages but it may be
 * safer to preserve disk space because the successfully delivered messages are not stored.
 * SENDMAIL_DELIVERY_BACKGROUND - 'b'
 * Creates a background process that attempts to deliver the message and returns immediately. This
 * mode is recommended when you want to send a few messages as soon as possible. It is not recommended for
 * sending messages to many recipients as it may consume too much memory and CPU that result from creating
 * excessive background processes.
 * SENDMAIL_DELIVERY_QUEUE - 'q'
 * Just drop the message in the queue and leave it there until next time the queue is run. It is
 * recommended for deliverying messages to many recipients as long as there is enough disk space to store
 * all the messages in the queue.
 * SENDMAIL_DELIVERY_DEFERRED - 'd'
 * The same as the queue mode except for a few verifications that are skipped.
 */
function mxBulkMail($what, $mode = 'q')
{
    require_once(PMX_SYSTEM_DIR . '/mailclasses/email_message.php');
    require_once(PMX_SYSTEM_DIR . '/mailclasses/smtp_message.php');
    require_once(PMX_SYSTEM_DIR . '/mailclasses/smtp.php');
    require_once(PMX_SYSTEM_DIR . '/mailclasses/sasl.php');
    $email_message = new smtp_message_class;
    $email_message->SetBulkMail(intval($what));
    $email_message->bulk_mail_delivery_mode = $mode;
}

/**
 * Versenden von Text und HTML-Mails mit und ohne Datei-Anhänge
 *
 * @uses smtp_message_class
 * @param string $to - Empfängeradresse der Email
 * @param string $subject - Subject der Email
 * @param string $message - Mitteilung /Message (text oder Html)
 * @param string $mxsender - Absenderadresse der Mail (setzt Admin-mailadresse wenn nicht angegeben)
 * @param string $type - Type der Email (text or html)
 * @param string $seclogid - Bezeichner für das Security-Log
 * @param string $sendername - Absendername der Mail
 * @param int $wrap - Textumbruch nach dieser Anzahl von Zeichen
 * @param mixed $bcc_array - Array für BCC-Empfänger das Array folgendermassen füllen :
 * $bcc_array=array('peter@gabriel.org' => 'Peter Gabriel'...)
 * @param mixed $the_attachment - Array : Dieses Array für File-Anhänge übergeben. Array muss
 * enthalten:
 * Inhalt für $the_attachment: "Name" = Dateiname in der Mitteilung,
 * "FileName" = URL zumr Datei, "Disposition" = "attachment", "Content-
 * Type" = "automatic/name" (Dies sollte den Filetype automatisch feststellen)
 */
function mxMail($to, $subject, $message, $mxsender = '', $type = 'text', $seclogid = '', $sendername = '', $wrap = 0, $bcc_array = '', $the_attachment = '')
{
    if (empty($to)) {
        return false;
    }
    if (empty($message) && empty($subject)) {
        return false;
    }
    if (empty($type)) {
        $type = 'text';
    }

    $message = preg_replace("/(?<!\r)\n/si", "\r\n", $message);
    $to = preg_replace('#[[:cntrl:]|" "]#', ' ', $to);
    $charset = 'utf-8';
    $mxsender = (empty($mxsender)) ? trim($GLOBALS['adminmail']) : trim($mxsender);
    $sendername = (empty($sendername)) ? trim(preg_replace("#[:!\"$%&=]#", "", $GLOBALS['sitename'])) : trim(preg_replace("#[:!\"$%&=]#", "", $sendername));

    if ($GLOBALS['mailauth'] != 'smtp') {
        $subject = strip_tags(mxHtmlEntityDecode($subject));
        $header = "From: " . $sendername . " <" . $mxsender . ">" . PHP_EOL;
        $header .= "Reply-To: " . $sendername . " <" . $mxsender . ">" . PHP_EOL;
        $header .= "X-Mailer: pragmaMx " . PMX_VERSION . PHP_EOL;
        if ($type != 'text') {
            $header .= "MIME-Version: 1.0" . PHP_EOL;
            $header .= 'Content-type: text/html; charset=' . $charset . PHP_EOL;
        } else {
            $header .= 'Content-type: text/plain; charset=' . $charset . PHP_EOL;
            $message = str_replace(array("\r\n", "\r", "\n"), PHP_EOL, $message);
            $message = strip_tags(mxHtmlEntityDecode($message));
        }

        $result = @mail($to, $subject, $message, $header);
        return $result;
    }

    ob_start();
    if (!class_exists('smtp_message_class')) {
        require_once(PMX_SYSTEM_DIR . '/mailclasses/email_message.php');
        require_once(PMX_SYSTEM_DIR . '/mailclasses/smtp_message.php');
        require_once(PMX_SYSTEM_DIR . '/mailclasses/smtp.php');
        require_once(PMX_SYSTEM_DIR . '/mailclasses/sasl.php');
    }
    $email_message = new smtp_message_class;

    $email_message->ResetMessage(); //reset komplete
    /**
     * This computer address
     */
    $email_message->localhost = "localhost";
    /**
     * SMTP server address, probably your ISP address
     */
    $email_message->smtp_host = $GLOBALS['mailhost'];
    /**
     * authentication user name
     */
    $email_message->smtp_user = $GLOBALS['mailuname'];
    /**
     * authentication password
     */
    $email_message->smtp_password = $GLOBALS['mailpass'];
    /**
     * if you need POP3 authentication before SMTP delivery,
     * specify the host name here. The smtp_user and smtp_password above
     * should set to the POP3 user and password
     */
    $email_message->smtp_pop3_auth_host = $GLOBALS['popauth'];
    /**
     * Specify whether it should use secure connections with SSL to connect to the SMTP server.
     * Certain e-mail services like Gmail require SSL connections.
     */
    // TODO: Hier muss noch eine Einstellmglichkeit in das Admin-Men bei den Maileinstellungen $GLOBALS['USE_MAIL_SSL']
    $email_message->smtp_ssl = 0;
    /**
     * Specify the default character set to be assumed for the message headers and body text.
     * Change this variable to the correct character set name if it is different than the default.
     */
    $email_message->default_charset = $charset;
    if (MX_IS_ADMIN && pmxDebug::is_debugmode()) {
        /* Output dialog with SMTP server */
        $email_message->smtp_debug = 1; //Debug-Mode on/off
    } else {
        $email_message->smtp_debug = 0; //Debug-Mode on/off
    }
    if ($bcc_array && is_array($bcc_array)) {
        $email_message->SetMultipleEncodedEmailHeader('Bcc', $bcc_array);
    }

    $email_message->mailer = 'pragmaMx ' . PMX_VERSION;
    $email_message->SetEncodedEmailHeader("To", $to, '');
    $email_message->SetEncodedEmailHeader("From", $mxsender, $sendername);
    $email_message->SetEncodedEmailHeader("Reply-To", $mxsender, $sendername);
    $email_message->SetHeader("Return-Path", $mxsender);
    $email_message->SetEncodedEmailHeader("Errors-To", $mxsender, $sendername);
    $email_message->SetEncodedHeader("Subject", $subject);
    if ($wrap) {
        $message = $email_message->WrapText($message, $wrap, "");
    }
    if ($type == "text") {
        $subject = strip_tags(mxHtmlEntityDecode($subject));
        $message = strip_tags(mxHtmlEntityDecode($message));
        $email_message->AddQuotedPrintableTextPart($message);
    } else {
        $email_message->CreateQuotedPrintableHTMLPart($message, "", $html_part);
        // $text_message = "This is an HTML message. Please use an HTML capable mail program to read this message.";
        $email_message->CreateQuotedPrintableTextPart($message, "", $text_part);
        $alternative_parts = array($text_part, $html_part);
        $email_message->AddAlternativeMultipart($alternative_parts);
    }
    if ($the_attachment && is_array($the_attachment)) {
        $email_message->AddFilePart($the_attachment);
    }
    $error = $email_message->Send();

    /* Debugausgaben der Mailklasse abfangen */
    $output = ob_get_clean();
    if ($output) {
        $output = "\n-----------------------------------------\n" . trim($output) . "\n-----------------------------------------\n";
    }

    for($recipient = 0, Reset($email_message->invalid_recipients);$recipient < count($email_message->invalid_recipients);Next($email_message->invalid_recipients), $recipient++)
//rem nanomx    mxSecureLog("Mailsystem-Invalid recipient: ", Key($email_message->invalid_recipients) . " Error: " . $email_message->invalid_recipients[Key($email_message->invalid_recipients)] . PHP_EOL);
    if (!empty($seclogid)) {
        $securelog = $seclogid . PHP_EOL;
//        mxSecureLog("Mailsystem-Log: ", $securelog . " " . $subject . " \n" . $to);
    }
    if (strcmp($error, '')) {
        if (MX_IS_ADMIN && mxGetAdminPref('radminsuper') && pmxDebug::is_mode('screen')) {
            trigger_error($error . $output, E_USER_ERROR);
        } else {
            trigger_error($error, E_USER_NOTICE);
        }
  //rem nanomx      mxSecureLog("Mailsystem-Error: ", $subject . " \n" . $to . " \n" . $error);
        return false;
    }

    return true;
}

/**
 * Funktion zum Auslesen des Geschlechts der angegebenen Person
 *
 * @param string $usernamen Name des Users dessen Geschlecht festgestellt werden soll
 * @return string Liefert das Geschlecht des Users zurück
 */
function mxGetUserGeschlecht($username)
{
    $data = mxGetUserDataFromUsername($username);
    if (isset($data['user_sexus'])) {
        return $data['user_sexus'];
    }
}

/**
 * Funktion zum Auslesen des Gruppennamens anhand der Gruppen-ID
 *
 * @param  $groupid
 * @return string
 */
function mxGetGroupTitle($groupid)
{
    global $user_prefix, $prefix;
    $groupid = (empty($groupid)) ? 1 : $groupid;
    $result = sql_system_query("SELECT access_title FROM {$prefix}_groups_access WHERE access_id ='" . intval($groupid) . "'");
    list($grouptitle) = sql_fetch_row($result);
    return $grouptitle;
}



/**
 * mxDemoMode()
 *
 * @deprecated Funktion wird seit pragmaMx 2.0 nicht mehr verwendet.
 * @return
 */
function mxDemoMode()
{
    //trigger_error('Use of deprecated function ' . __FUNCTION__ . '()', E_USER_WARNING);
    return false;
}

/**
 * Weiterleitung zu einer anderen URL
 *
 * @param string $url URL zu der weitergeleitet werden soll
 * @param string $message Mitteilung die zur Weiterleitung angezeigt werden soll
 * @param int $delay Wartezeit bis zur Weiterleitung
 */
function mxRedirect($url = '', $message = '', $delay = 3)
{
    /* eventuelle Ausgaben entfernen, und zwischenspeichern */
    if (ob_get_level()) {
        // TODO: was damit machen?
        $content = ob_get_clean();
    }

    /* Debugmodus ermitteln u. ggf. die Infos anfügen */
    switch (true) {
        case pmxDebug::is_mode('enhanced'):
            $debug = mxDebugInfo();
            $delay = 59; // Sekunden sind das !!
            break;
        case pmxDebug::is_error() && pmxDebug::is_mode('screen'):
            ob_start() ?>
            <div id="pmx-debug-area">
                <h4><?php echo _ERROROCCURS ?></h4>
                <div><?php echo pmxDebug::format_errors() ?></div>
            </div> <?php
            $debug = ob_get_clean();
            $delay = 30; // Sekunden sind das !!
            break;
        default:
            $debug = '';
    }

    /* ggf. url korrigieren */
    $rewrite = true;
    switch (true) {
        case !$url:
            $url = './';
            $rewrite = false;
            break;
        case strpos($url, PMX_BASE_PATH) === 0:
            break;
        case !preg_match('!^(f|ht)tps?!i', $url):
            $url = PMX_HOME_URL . '/' . ltrim($url, '/\\ ');
            break;
    }

    if ($debug) {
        /* im Debugmodus wird, wenn Anker # in der url enthalten sind,
         * nicht korrekt weitergeleitet, deswegen hier die Weiterleitungs-Url
         * verändern (bug-id: 1113)
         */
        $pos2 = strrpos($url, '#');
        if ($pos2 !== false) {
            $pos1 = strrpos($url, '?');
            $rewrite = false;
            if ($pos1 !== false) {
                $url = substr_replace($url, 'debugredirect&', $pos1 + 1, 0);
            } else {
                $url = substr_replace($url, '?debugredirect', $pos2, 0);
            }
        }
    }
    if ($rewrite) {
        $seo = load_class('Config', 'pmx.seo');
        if (array_sum((array)$seo->modrewrite)) {
            load_class('Modrewrite', false);
            $url = pmxModrewrite::prepare_url($url);
            $message = pmxModrewrite::prepare($message);
        }
    }

    $url = preg_replace('#[[:space:][:cntrl:]]+#', '%20', $url);

    /* wie weiterleiten? */
    switch (true) {
        case $debug:
        case $message:
        case headers_sent():
            mxRedirectMessage($url, $message, $debug, $delay);
            //session_write_close();
            die();
        default:
            //session_write_close();
            header('Location: ' . strtr($url, array('&amp;' => '&')));
            die();
    }
}

/**
 * Wandelt Anführungszeichen in deren Entitäten um
 *
 * @param string $string Variable deren Inhalt umgewandelt werden soll
 * @param string $quote_style Umwandlungsmodus
 * 2 = ENT_COMPAT = behandelt nur die doppelten, nicht aber die einfachen Anführungszeichen
 * 3 = ENT_QUOTES = werden einfache und doppelte Anführungszeichen umgewandelt
 * 0 = ENT_NOQUOTES = sowohl einfache als auch doppelte Anführungszeichen bleiben unberührt
 * @return string Liefert umgewandelten String zurück
 */
function mxEntityQuotes($string, $quote_style = ENT_QUOTES)
{
    if ($quote_style == ENT_COMPAT) {
        return str_replace('"', '&quot;', $string);
    } else if ($quote_style == ENT_QUOTES) {
        return str_replace("'", '&#039;', str_replace('"', '&quot;', $string));
    }
    return $string;
}

/**
 * Email Address Verification
 * found at: http://www.devshed.com/c/a/PHP/Email-Address-Verification-with-PHP/1/
 *
 * @param string $email Emailadresse, die überprüft werden soll
 * @return boolean Liefert true bzw. im Fehlerfall false zurück
 */
function mxCheckEmail($email)
{
    /* check Mailadresse ab PHP 5.2 möglich */
    if (filter_id ("validate_email")) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) return true;
    } else {
        /* fallback */
        if (preg_match("/^([a-zA-Z0-9-])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", trim($email))) {
            return true;
        }
    }
    return false;
}

/**
 * auf gesperrte eMail prüfen
 *
 * @param string $email Emailadresse, die überprüft werden soll
 * @return boolean Liefert true bzw. im Fehlerfall false zurück
 */
function pmx_is_mail_banned($email)
{
    global $prefix;
    $qry = "SELECT ban_val
            FROM " . $prefix . "_user_ban
            WHERE ban_type='ban_mail'
            AND (ban_val='" . mxAddSlashesForSQL($email) . "' OR '" . mxAddSlashesForSQL($email) . "' RLIKE ban_val)";
    $result = sql_query($qry);

    return (bool) sql_fetch_row($result);
}

/**
 * Wandelt bestimmte Sonderzeichen in den entsprechenden HTML-Code um.
 * Quelle: http://phpdiscovery.com/dangers-of-remote-execution/
 *
 * Gegenüber der Funktion cleanpreg() werden zusätzliche Sonderzeichen umgewandelt.
 *
 * @since pragmaMx 0.1.10
 * @param string $specialchars String, in welchem vorhandene Sonderzeichen
 * umgewandelt werden sollen.
 * @return string Gibt den bereinigten String zurück
 */
function cleaneval($specialchars)
{
    $bad = array('{', '}', '$', ']', '[', '`', ';');
    $good = array('&#123', '&#125', '&#36', '&#93', '&#91', '&#96', '&#59');
    $specialchars = htmlspecialchars($specialchars);
    $specialchars = str_replace($bad, $good, $specialchars);
    return $specialchars;
}

/**
 * Wandelt bestimmte Sonderzeichen in den entsprechenden HTML-Code um.
 * Quelle: http://phpdiscovery.com/dangers-of-remote-execution/
 *
 * @since pragmaMx 0.1.10
 * @param string $specialchars String, in welchem vorhandene Sonderzeichen
 * umgewandelt werden sollen.
 * @return string Gibt den bereinigten String zurück
 */
function cleanpreg($specialchars)
{
    $bad = array('{', '}', '$');
    $good = array('&#123', '&#125', '&#36;');
    $specialchars = htmlspecialchars($specialchars);
    $specialchars = str_replace($bad, $good, $specialchars);
    return $specialchars;
}

/**
 * Wandelt eine Integer- oder Fliesskommazahl in einen formatierten String um,
 * damit die Ausgabe in einer lesbareren Form geschieht.
 *
 * Die Formatierung stützt sich auf die ISO 31 (ab 2008: ISO 80000).
 * Info: http://de.wikipedia.org/wiki/Schreibweise_von_Zahlen#Internationale_Standards
 *
 * Wenn die Anzahl der auszugebenden Nachkommastellen kleiner ist als die
 * Anzahlzahl der Nachkommastellen des umzuwandelnden Wertes, wird beim Umwandeln
 * kaufmännisch gerundet.
 *
 * Die Funktion dient ausschliesslich dazu, Zahlen in einer formatierten Form
 * zur Anzeige zu bringen. Alle mathematischen Berechnungen müssen vor
 * Verwendung der Funktion ausgeführt werden.
 *
 * @param mixed $value Zahl, die als formatierter String ausgegeben werden soll
 * @param integer $decimal_precision Anzahl der auszugebenden Nachkommastellen,
 * optional, default value 2
 * @param boolean $nobreakspace , Ausgabe mit oder ohne geschützten Leerzeichen
 * @return string $new_value Rückgabe des Wertes als formatierter String.
 */
function mxValueToString($value, $decimals = 2)
{
    switch (true) {
        case !is_numeric($value): // Wert muss eine Zahl sein
        case $value === 0: // Null muss nicht formatiert werden
            return 0;
        case !defined('_DECIMAL_SEPARATOR');
            return number_format($value, $decimals);
        default:
            return number_format($value, $decimals, _DECIMAL_SEPARATOR, _THOUSANDS_SEPARATOR);
    }
}

function pmx_number_format($value, $decimals = 0)
{
    return mxValueToString($value, $decimals);
}

/**
 * Generiert einen Link um Userprofil
 *
 * Es wird nur dann ein Link zum Userprofil ausgegeben, wenn das Modul aktiv
 * und für den jeweiligen Besucher erlaubt ist. Ein Admin bekommt immer
 * einen Link angezeigt.
 *
 * @param string $uname Name des Users, dessen Profil verlinkt werden soll
 * @param string $text optional, enthält den mit dem Link auszustattenden Text
 * @param string $title optional, enthält den Titel für den Link
 * @param bool $news optional, wenn der Parameter TRUE ist, dann handelt es sich um den
 * Morelink-Button für das Newsmodul und der Link muss entsprechend anders aufgebaut sein
 * @param string $option optional, enthält Optionen für an <a>-Tag, z.B. Style-Angaben
 * @return string $out Gibt entweder den kompletten HTML-Code für den Link oder
 * nur den Text ohne Link zurück
 */
function mxCreateUserprofileLink($uname, $text = '', $title = '', $news = false, $option = '')
{
    global $anonymous;

    if (!$uname || $uname == $anonymous) {
        return $anonymous;
    }

    $linktagclose = '';

    static $uinfallowed;
    if (!isset($uinfallowed)) {
        $uinfallowed = (MX_IS_ADMIN || mxModuleAllowed('Userinfo'));
    }

    if ($news) {
        $text = '';
    } else {
        if (!$text) {
            $text = $uname;
        }
        $linktagclose = '</a>';
    }
    if ($title) {
        $title = ' title="' . $title . '"';
    }
    if ($option) {
        $option = ' ' . $option;
    }
    if ($uinfallowed) {
        return '<a href="modules.php?name=Userinfo&amp;uname=' . urlencode($uname) . '"' . $title . ' rel="nofollow"' . $option . '>' . $text . $linktagclose;
    }
    if ($news) {
        $text = '<a>' . $text;
    }
    return $text;
}

/**
 * Konvertiert ein Array zu einem Standardobjekt
 *
 * @since pragmaMx 0.1.11
 * @param array $array Array, das zu einem Objekt konvertiert werden soll.
 * Das Array kann sowohl ein- als auch mehrdimensionale sein.
 * @return mixed Gibt die Daten aus dem übergebenen Array in einem Objekt zurück.
 * Falls $array kein Array ist, wird der Wert von $array zurückgegeben.
 * @param array $array
 * @return object
 */
function array2object($array)
{
    if (is_array($array)) {
        $object = new stdclass;
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $object->$key = array2object($value);
            } else {
                $object->$key = $value;
            }
        }
        return $object;
    }
    return $array;
}

/**
 * object2array()
 * Konvertiert ein Standardobjekt zu einem Array
 *
 * @param object $object
 * @return array
 */
function object2array($object)
{
    if (is_object($object)) {
        foreach ($object as $key => $value) {
            if (is_object($value)) {
                $array[$key] = object2array($value);
            } else {
                $array[$key] = $value;
            }
        }
        return $array;
    }
    return $object;
}

/**
 * wordwrap2()
 *
 * @param mixed $str
 * @param mixed $width
 * @param string $break
 * @param mixed $cut
 * @return
 */
function wordwrap2($str, $width = 75, $break = PHP_EOL, $cut = false)
{
    switch (true) {
        /* Array übergeben, dann alles rekursiv durchlaufen */
        case is_array($str):
            foreach ($str as $key => $value) {
                $str[$key] = wordwrap2 ($value, $width, $break, $cut);
            }
            return $str;

        case !is_string($str):
            /* kein String > abbrechen */
            return $str;

        case strpos($str, '<') === false:
            /* keine Tags vorhanden, normale Funktion ausführen */
            return wordwrap($str, $width , $break, $cut);

        case preg_match_all('#(?:^|>)([^<>[:space:]]{' . $width . ',})(?:<|$)#m', $str, $matches):
            /* die eigentliche Funktion */
            $i = 1;
            foreach ($matches[1] as $match) {
                $key = (utf8_strlen($match) * 1000) + $i++;
                $search[$key] = $match;
                $replace[$key] = wordwrap($match, $width , $break, $cut);
            }
            krsort($search);
            krsort($replace);
            return str_replace($search, $replace, $str);

        default:
            /* alles andere, normale Funktion ausführen... */
            return wordwrap($str, $width , $break, $cut);
    }
}

/**
 * getAllAccessGroups()
 *
 * @param mixed $autoinsert
 * @return
 */
function getAllAccessGroups($autoinsert = true)
{
    global $prefix;

    $userconfig = load_class('Userconfig');

    $result = sql_query("select access_id, access_title from " . $prefix . "_groups_access where access_title != 'Deleted' order by access_title");
    while ($row = sql_fetch_assoc($result)) {
        $groupoptions[$row['access_id']] = $row;
    }
    if ($autoinsert && !isset($groupoptions[$userconfig->default_group])) {
        sql_query("INSERT INTO " . $prefix . "_groups_access SET access_title = '" . MX_FIRSTGROUPNAME . "', access_id=" . intval($userconfig->default_group));
        $groupoptions = getAllAccessGroups(false);
    }
    return $groupoptions;
}

/**
 * stringmarker()
 *
 * @param mixed $source
 * @param mixed $words
 * @return
 */
function stringmarker($source, $words)
{
    if (!trim($source)) {
        return $source;
    }

    if (is_string($words)) {
        $words = preg_split('#\W+#u', $words, - 1, PREG_SPLIT_NO_EMPTY);
    }
    if (!$words) {
        return $source;
    }
    $pattern = '#(' . implode('|', $words) . ')#';

    $source_array = preg_split('#(<[^>]*>)#', $source, - 1, PREG_SPLIT_DELIM_CAPTURE);

    foreach ($source_array as $key => $value) {
        if ($value && $value[0] != '<') {
            $source_array[$key] = preg_replace($pattern, '<span class="highlight" style="padding:0">$1</span>', $value);
        }
    }
    return implode('', $source_array);
}

/**
 * mxIniGet()
 * Fallback, falls ini_get nicht möglich
 * ist aber nur ne Krücke!
 *
 * @param mixed $cfgname
 * @return
 */
function mxIniGet($cfgname)
{
    if (function_exists('ini_get')) {
        return ini_get($cfgname);
    } else {
        return get_cfg_var($cfgname);
    }
}

/**
 * Prüft ob der User Fotos bzw. Avatare hochgeladen hat.
 * Aufrufbeispiele:
 * $foo = mxIsAvatarUploaded($bar);->false oder den aktuellen Avatar des Users als String
 * $foo = mxIsAvatarUploaded($bar, true);->false oder den aktuellen Avatar des Users als mxCreateImage.
 * $foo = mxIsAvatarUploaded($bar,false,true);->false oder $row als Array mit Zugriff auf alle
 * Spalten (z.B $foo['werist'])
 *
 * @param string $username Username von dem User desses Avatar gesucht wird
 * @param bool $imagereturn Wenn imareturn==true wird der komplette HTML-Tag zum Anzeigen des
 * Avatars zurückgegeben, ansonsten nur der Avatar Dateiname, optional, default value false
 * @param bool $new Wenn $new==true wird ein Array mit den gesamten Daten zum Useravatar
 * zurückgegeben, optional, default value false
 * @return mixed Liefert Image, Avatar-Dateinamen oder Array mit Informationen zum Avatar je nach
 * übergebenen Parametern
 *
 * deprecated !!
 */
function mxIsAvatarUploaded($username, $imagereturn = false, $new = false)
{
    $pici = load_class('Userpic', $username);
    $userdata = mxGetUserDataFromUsername($username);

    switch (true) {
        case !permission_granted($pici->access_upload, $userdata['groups']):
        case !$pici->is_uploaded():
            return false;
        case $imagereturn:
            return $pici->getHtml('small');
        default:
            $img = $pici->get('small');
            if (!$img) {
                return false;
            }
            $info = pathinfo($img);
            if (!defined('MX_PATH_MEMBERAVATAR')) {
                define('MX_PATH_MEMBERAVATAR', $info['dirname']); // path_avatars
            }
            if ($new) {
                return array(// det olle array von 2.4.3...
                    'aid' => $username,
                    'typ' => $info['basename'],
                    'anzahl' => 1,
                    'werist' => $info['basename'],
                    'fake_zahl' => 1,
                    'foto' => '',
                    'date' => time(),
                    'id' => 0,
                    );
            } else {
                return $info['basename'];
            }
    }
}

/**
 * Funktion zum löschen aller Avatare und Einstellungen eines Users.
 *
 * @since pragmaMx 0.1.0
 * @deprecated Diese Funktion wird seit pragmaMx 0.1.11 SP1 nicht mehr verwendet.
 * @param string $xx Username des User, desses Avatar gelöscht werden soll.
 * @return bool Gibt false zurück.
 */
function mxDeleteAvatar($xx = null)
{
    return false;
}

/**
 * Ueberprüft einen Usernamen auf Zulässigkeit
 *
 * @since pragmaMx 1.12
 * @param string $username Username der überprüft werden soll
 * @return mixed Gibt im Erfolgsfall true, im Fehlerfall einen Fehlertext zurück
 */
function mxCheckNickname($username)
{
    $username = trim($username);

    $userconfig = load_class('Userconfig');
    $error = '';
    $add = '';

    switch (true) {
        case !$username:
        case $userconfig->uname_min_chars && strlen($username) < $userconfig->uname_min_chars:
            $add = $userconfig->uname_min_chars;
            $error = '_NICK2SHORT';
            break;

        case strlen($username) > 25:
            $add = '25';
            $error = '_NICK2LONG';
            break;

        case is_numeric($username):
            $error = '_NICKNOTNUMERIC';
            break;

        case (!$userconfig->uname_space_chars) && preg_match('#\s#', $username);
            $error = '_NICKNOSPACES';
            break;

        case $userconfig->uname_special_chars && preg_match_all('#(\W)#', (($userconfig->uname_space_chars) ? str_replace(' ', '_', $username) : $username), $matches);
        case (!$userconfig->uname_special_chars) && preg_match_all('#(' . (($userconfig->uname_space_chars) ? '[^a-zA-Z0-9 _-]' : '[^a-zA-Z0-9_-]') . ')#', $username, $matches);
            $add = implode('', array_unique($matches[1]));
            $error = '_NICKNOSPECIALCHARACTERS';
            break;

        default:
            return true;
    }

    if ($error && !defined($error)) {
        mxGetLangfile('Your_Account');
    }

    return sprintf(constant($error), $add);
}

/**
 * is_tidy_available()
 * ist php-Tidy aktiv und verwendbar
 *
 * @return boolean
 */
function is_tidy_available()
{
    if (!extension_loaded('tidy') && function_exists('dl') && !mxIniGet('safe_mode')) {
        pmxDebug::pause();
        dl('tidy');
        pmxDebug::restore();
    }

    /* tidy aber nur mit tidy 2.0 */
    if (class_exists('tidy', false) && !function_exists('tidy_set_encoding')) {
        return true;
    }
    return false;
}

if (!function_exists('array_map_recursive')) {
    /**
     * array_map_recursive()
     * A recursive way to handle multidimensional arrays.
     * found: http://de3.php.net/manual/de/function.array-map.php
     * 24-Oct-2006 09:14, andref dot dias at pronus dot eng dot br
     *
     * @param mixed $func
     * @param mixed $arr
     * @return array
     */
    function array_map_recursive($func, $arr)
    {
        $newArr = array();
        foreach($arr as $key => $value) {
            $newArr[ $key ] = (is_array($value) ? array_map_recursive($func, $value) : $func($value));
        }
        return $newArr;
    }
}

/**
 * Systempfade am Anfang eines Datei oder Ordnernamens entfernen
 *
 * @since pragmaMx 1.12
 * @param string $path
 * @return string
 */
function mx_strip_sysdirs($path)
{
    switch (true) {
        case strpos($path, PMX_REAL_BASE_DIR) === 0:
            /* PMX_REAL_BASE_DIR am Anfang entfernen */
            return ltrim(substr_replace($path, '' , 0 , strlen(PMX_REAL_BASE_DIR)), ' \/');
        case strpos($path, PMX_BASE_PATH) === 0:
            /* PMX_BASE_PATH am Anfang entfernen */
            return ltrim(substr_replace($path, '' , 0 , strlen(PMX_BASE_PATH)), ' \/');
        default:
            return $path;
    }
}

/**
 * mx_urltohtml()
 * macht eine URL mit Sonderzeichen, HTML tauglich
 *
 * @param mixed $url
 * @return
 */
function mx_urltohtml($url)
{
    // return str_replace(array('&', '&amp;amp;'), '&amp;', $url);
    return htmlspecialchars($url, ENT_QUOTES, 'utf-8', false);
}

/**
 * mxSecureValue()
 * dient nur zur Filterung von $_GET, $_POST, $_COOKIE
 *
 * @param mixed $value
 * @param mixed $is_get
 * @return mixed $value
 */
function mxSecureValue($value, $is_get = true)
{
    switch (true) {
        case empty($value):
            return $value;
        case is_array($value):
            // wenn array, dieses in Schleife rekursiv behandeln und zurückgeben
            foreach ($value as $key => $val) {
                $value[$key] = mxSecureValue($val, $is_get);
            }
            return $value;
        case is_numeric($value):
        case !is_string($value):
        case $value === session_id():
            // leere, numerische und nicht String Werte, sowie die session_id nicht behandeln
            return $value;
    }

    /* Remove single non-printable code character excl. tab and CRLF */
    $value = preg_replace('#[\x00\x08\x0B\x0C\x0E-\x1F]#', ' ', $value);

    if (!get_magic_quotes_gpc()) {
        // This will reproduce the option magic_quotes_gpc=1 // nur wenn String
        $value = addslashes($value);
    }

    /* der Rest nur bei Nicht-Admins */
    if (!$is_get && MX_IS_ADMIN) {
        return $value;
    }

    if ($is_get) {
        $value = str_replace(array('\\"', "\'"), array('&quot;', '&#39;'), $value);
    }

    if (strpos($value, '<') !== false) {
        /* javascript events aus Tags entfernen */
        $value = preg_replace('#(<[^>]*)(on[a-z]+)\s*=#is', '\1notallowed*', $value);
        $value = preg_replace('#(<[^>]*[a-z]+\s*[\x3D\x28]\s*[\\\\]?["\']?)((?:(?:javascript|vbscript|mocha|livescript|behavior)[\x3A])|(?:[^"\'>]*ression\s*[\x28]))#is', '\1notallowed*', $value);

        /* Tags die immer verboten sind, entfernen */
        $value = preg_replace('#(</?\s*)(applet|body|bgsound|base|basefont|embed|frame|frameset|head|html|id|iframe|ilayer|layer|link|meta|name|object|script|style|title|xml)([^<]*>)#is', '\1notallowed*\3', $value);

        if ($is_get) {
            /* Tags die über GET verboten sind, entfernen */
            $value = preg_replace('#(</?\s*)(img|form|input|textarea|button|source)([^<]*>)#is', '\1notallowed*\3', $value);
            /* sonstige "böse" Parameter aus Tags entfernen */
            $value = preg_replace('#(<[^>]*)(style)\s*(\=[^<]*>)#is', '\1notallowed*\3', $value);
        }
    }

    if (!empty($GLOBALS['mxEntitieLevel'])) {
        // wenn level 2, dann alle unerlaubten html-Tags entfernen
        // die, die nur ohne Parameter verwendet werden dürfen, werden weiter unten trotzdem behandelt
        if ($GLOBALS['mxEntitieLevel'] === 2) {
            $allowedtags = '<' . join('><', mxGetAllowedHtml()) . '>';
            $value = strip_tags($value, $allowedtags);
        }
        // erlaubte HTML-Tags auslesen zum ersetzen (preg)
        $allowedhtml = mxGetAllowedHtml('preg');
        // Preparse var to mark the HTML that we want
        $value = preg_replace($allowedhtml, "\022\\1\024", $value);
        // Prepare var
        $value = str_replace(array ('<', '>'), array ('&lt;', '&gt;'), $value);
        // Fix the HTML that we want
        $value = preg_replace_callback('/\022([^\024]*)\024/',
            create_function('$match',
                'return "<".stripslashes(strtr($match[1], array("&gt;" => ">","&lt;" => "<"))).">";'
                ), $value);
    }

    /* Die Wortzensur anwenden */
    if (!MX_IS_ADMIN) {
        $value = mxPrepareCensored($value);
    }
    /* und weg damit ;-)) */
    return $value;
}

/**
 * idna_to_ascii()
 * Encode a given UTF-8 domain name
 *
 * @param string $ Domain name (UTF-8 or UCS-4)
 * @return string Encoded Domain name (ACE string)
 */
function idna_to_ascii($domain)
{
    include_once('includes/classes/_misc/idna_convert.class.php');
    // The input string, if input is not UTF-8 or UCS-4, it must be converted before
    $domain = utf8_encode($domain);
    // Instantiate it
    $IDN = new idna_convert(array('idn_version' => 2008));
    // Encode it to its punycode presentation
    return $IDN->encode($domain);
}

/**
 * idna_from_ascii()
 * Decode a given ACE domain name
 *
 * @param string $ Domain name (ACE string)
 * @return string Decoded Domain name (UTF-8 or UCS-4)
 */
function idna_from_ascii($domain)
{
    include_once('includes/classes/_misc/idna_convert.class.php');
    // Instantiate it
    $IDN = new idna_convert();
    // Encode it to its punycode presentation
    $domain = $IDN->decode($domain);
    // Output, what we got now, if output should be in a format different to UTF-8
    // or UCS-4, you will have to convert it before outputting it
    return utf8_decode($domain);
}

/**
 * pmx_get_startdate()
 *
 * @return
 */
function pmx_get_startdate()
{
    static $startdate = 0;
    if ($startdate) {
        return intval($startdate);
    }
    global $prefix;

    $tmpdate = strtotime($GLOBALS['startdate']);
    if ($tmpdate) {
        /* wenn in config gültiges Datum angegeben, dieses verwenden */
        $startdate = $tmpdate;
        return intval($startdate);
    }

    /* ansonsten den ersten Eintrag der Statistik ermitteln */
    $result = sql_query("SELECT year, month, `date` AS day FROM `{$prefix}_stats` WHERE hits>0 ORDER BY year, month, `date`, hour LIMIT 1");
    list($year, $month, $day) = sql_fetch_row($result);
    $startdate = mktime(0, 0, 0, intval($month), intval($day), intval($year));
    return intval($startdate);
}

/**
 * pmx_get_totalhits()
 *
 * @return
 */
function pmx_get_totalhits()
{
    static $total = 0;
    if ($total) {
        return intval($total);
    }
    global $prefix;

    $start = pmx_get_startdate();
    $start = getdate($start);
    $result = sql_query("SELECT SUM(hits) FROM `{$prefix}_stats`
                WHERE hits > 0
                  AND STR_TO_DATE(CONCAT(year, '-', month, '-', `date`), '%Y-%c-%e') >= '$start[year]-$start[mon]-$start[mday]'");

    list($total) = sql_fetch_row($result);

    return intval($total);
}

/**
 * pmx_split_pages()
 *
 * @param mixed $content
 * @param string $mode
 * @return
 */
function pmx_split_pages($content, $mode = 'page')
{
    switch ($mode) {
        case 'print':
            $search = array(PMX_PAGE_DELIMITER, '<!--pagebreak-->');
            $replace = '<div style="page-break-after: always;"><span style="display: none;">&nbsp;</span></div>';
            return str_replace($search, $replace, $content);
            break;
        case 'preview':
            $search = array(PMX_PAGE_DELIMITER, '<!--pagebreak-->');
            $replace = '<div style="background-color: transparent; color: Red; text-align: center; page-break-after: always;">' . htmlspecialchars(PMX_PAGE_DELIMITER) . '</div>';
            return str_replace($search, $replace, $content);
            break;
        default:
            $content = str_replace('<!--pagebreak-->', PMX_PAGE_DELIMITER, $content);
            $pages = explode(PMX_PAGE_DELIMITER, $content);
            return array_filter($pages);
    }
}

/**
 * pmx_multilang_query()
 * Zur Abfrage von Sprachspezifischen Daten aus der Datenbank,
 * wenn Multilanguage aktiviert ist
 *
 * @param mixed $field ,  der Name des Datenbankfeldes
 * @param string $query_prefix , vorher anfügen, z.B. WHERE oder AND
 * @return string
 */
function pmx_multilang_query($field, $query_prefix = '')
{
    global $currentlang;
    switch (true) {
        case !$GLOBALS['multilingual']:
            return '';
        case ($pos = strpos($currentlang, '_')) !== false:
            $thislang = substr($currentlang, 0, $pos);
            break;
        default:
            $thislang = $currentlang;
    }

    return " " . trim($query_prefix . " (" . $field . " LIKE '" . $thislang . "%' OR " . $field . " = '') ") . " ";
}

/*
 * PBKDF2 Implementation (described in RFC 2898)
 * By Andrew Johnson Oct 3, 2009
 * http://www.itnewb.com/v/Encrypting-Passwords-with-PHP-for-Storage-Using-the-RSA-PBKDF2-Standard
 *
 * @param string p password
 * @param string s salt
 * @param int c iteration count (use 1000 or higher)
 * @param int kl derived key length
 * @param string a hash algorithm
 *
 * @return string derived key
 */
function pbkdf2($p, $s, $c = 5000, $kl = 64, $a = 'sha256')
{
    /* Fallback, falls hash-Funktionen nicht verfügbar */
    if (!(function_exists('hash') && function_exists('hash_hmac'))) {
        /* Perform block iterations */
        for ($i = 1; $i < $c * $kl; $i ++)
        return substr(str_repeat(crypt($s . $kl . $p . $a . $c, $s), $kl), - $kl);
    }

    $hl = strlen(hash($a, null, true)); # Hash length
    $kb = ceil($kl / $hl); # Key blocks to compute
    $dk = ''; # Derived key

    /* Create key */
    for ($block = 1; $block <= $kb; $block ++) {
        /* Initial hash for this block */
        $ib = $b = hash_hmac($a, $s . pack('N', $block), $p, true);

        /* Perform block iterations */
        for ($i = 1; $i < $c; $i ++)

        /* XOR each iterate */
        $ib ^= ($b = hash_hmac($a, $b, $p, true));

        $dk .= $ib; # Append iterated block
    }

    /* Return derived key of correct length */
    return substr($dk, 0, $kl);
}

/**
 * mkfromstrptime()
 * Gibt den Unix-Timestamp/Zeitstempel für ein Datum zurück welches mit.
 * Also eine Kombination von strptime() und mktime()
 * strftime() generiert/formatiert wurde
 *
 * @param string $date
 * @param string $format
 * @return integer timestamp
 */
function mkfromstrptime($date, $format)
{
    $tmp = strptime($date, $format);
    return mktime($tmp['tm_hour'], $tmp['tm_min'], $tmp['tm_sec'], $tmp['tm_mon'] + 1, $tmp['tm_mday'], $tmp['tm_year'] + 1900);
}

if (!function_exists('strptime')) {
    /**
     * strptime()
     * Parsed ein Datum welches mit strftime() generiert/formatiert wurde
     * Diese Funktion ist auf Windows-Plattformen nicht implementiert.
     * based on: http://de2.php.net/manual/de/function.strptime.php#103598
     *
     * @param string $str
     * @param string $fmt
     * @return array
     */
    function strptime($date, $format)
    {
        $masks = array('%d' => '(?P<d>[0-9]{2})',
            '%m' => '(?P<m>[0-9]{2})',
            '%Y' => '(?P<Y>[0-9]{4})',
            '%H' => '(?P<H>[0-9]{2})',
            '%M' => '(?P<M>[0-9]{2})',
            '%S' => '(?P<S>[0-9]{2})',
            // usw..
            );

        $rexep = "#" . strtr(preg_quote($format), $masks) . "#";
        if (!preg_match($rexep, $date, $out)) {
            return false;
        }

        $ret = array("tm_sec" => (isset($out['S'])) ? (int) $out['S'] : 0,
            "tm_min" => (isset($out['M'])) ? (int) $out['M'] : 0,
            "tm_hour" => (isset($out['H'])) ? (int) $out['H'] : 0,
            "tm_mday" => (isset($out['d'])) ? (int) $out['d'] : 0,
            "tm_mon" => (isset($out['m'])) ? ($out['m'] -1) : 0,
            "tm_year" => (isset($out['Y']) && $out['Y'] > 1900) ? ($out['Y'] - 1900) : 0,
            );

        return $ret;
    }
}

/**
 * string_to_filename($word)
 * konvertiert einen String in eine Zeichenkette, welche für einen dateinamen verwendet werden kann
 *
 * @param string $word
 * @return string
 */
function string_to_filename($word)
{
    $tmp = preg_replace('/^\W+|\W+$/', '', $word); // remove all non-alphanumeric chars at begin & end of string
    $tmp = preg_replace('/\s+/', '_', $tmp); // compress internal whitespace and replace with _
    $tmp = preg_replace('/[^a-z0-9\\040\\.\\-\\_]/i', "_", $tmp); // alow '-._'
    return strtolower(preg_replace('/\W-/', '', $tmp)); // remove all non-alphanumeric chars except _ and -
}

/**
 * adminUrl()
 *
 * @param string $module
 * @param string $op
 * @param string $more
 * @param string $anchor
 * @param string $arg_separator
 * @return
 */
function adminUrl($module = '', $op = '', $more = '', $anchor = '', $arg_separator = '&amp;')
{
    settype($para, 'array');

    switch (true) {
        case !$module:
            // Wenn Modul nicht angegeben, dann ist auch $op hinfällig.
            break;
        case $op:
            // Modul und $op angegeben, dann die beiden zusammenfügen
            $para[] = 'op=' . $module . '/' . $op;
            break;
        case $module:
            // nur Modul angegeben, dann ist das Modul auch $op
            $para[] = 'op=' . $module;
            break;
    }

    switch (true) {
        case !$more:
            break;
        case is_array($more):
            $para[] = http_build_query($more, '', $arg_separator);;
            break;
        case is_scalar($more):
            settype($more, 'string');
            $more = str_ireplace($arg_separator, '&', $more);
            parse_str($more, $new);
            $para[] = http_build_query($new, '', $arg_separator);;
            break;
    }

    settype($qry, 'string');
    if ($para) {
        $qry = '?' . implode($arg_separator, $para);
    }

    if ($anchor) {
        $anchor = '#' . trim($anchor, ' #');
    }

    return 'admin.php' . $qry . $anchor;
}


/**
 *  modulesUrl
 *  
 *  
 */
 function modulesUrl($module = '', $query = '',$anchor="", $arg_separator="&amp;")
{
    $para="";
	$qry="";
	
	if (is_array($query)) {
		$qry = http_build_query($query,"",$arg_separator);
	} else {
		$qry=$query;
	}
	
    switch (true) {
        case !$module:
            // Wenn Modul nicht angegeben, dann ist auch $query hinfällig.
			$para="";
            break;
        case $qry:
            // Modul und $query angegeben, dann die beiden zusammenfügen
            $para = '?name=' . $module . $arg_separator . $qry;
            break;
        case $module:
            // nur Modul angegeben, dann ist das Modul auch $query
            $para = '?name=' . $module;
            break;
    }    
	
	if ($anchor) {
        $anchor = '#' . trim($anchor, ' #');
    }
	
    return 'index.php' . $para . $anchor;
}

/**
 * mxChangeContent()
 * ersetzt Suchworte mit entsprechenden Ersetzungen in $content.
 *
 * @param  $seach array - key=Suchbegriff -value=Ersetzung
 * @param string $content
 * @param int $ count - Anzahl der Ersetzungen (0=alle)
 * @return $content
 */
function mxChangeContent($content, $search = array(), $count = 0)
{
    global $prefix, $module_name;

    /* Alle relevanten Tags aus dem zu ändernden Inhalt auslesen */
    // Tags in der Form <tag>text</tagende> z.B. <a>irgendwas</a>
    preg_match_all('#<(a|form|font|iframe|object|title|meta|script|noscript|code|header|footer|adress|label|thead|h1|h2|h3|h4|h5|h6)[^>]+>.+</\1>#isU', $content, $matches1);
    // Tags in der Form <tag />, z.B. <img />
    preg_match_all('#<(img)[^>]+>#isU', $content, $matches2);
    // preg_match_all("#<(h[1-9])[^>]+>(.*)</[^>]+>#U", $content, $matches3);
    $matches = array_merge($matches1[0], $matches2[0]);
    $temp = array();
    foreach ($matches as $value) {
        $key = '~|~' . base64_encode($value) . '~|~';
        /* Die Platzhalter als Schlüssel, der Originalwert als Wert */
        $temp[$key] = $value;
    }

    /* Die Tags durch einen eindeutigen Platzhalter ersetzen */
    if ($temp) {
        $content = str_replace(array_values($temp), array_keys($temp), $content);
    }
    //$content = html_entity_decode($content, ENT_COMPAT | ENT_HTML5, 'UTF-8');

    /* Sucharray sortiern um doppelte Einträge zu eleminieren */
    krsort($search);

    /* Autolink Funktion... */
    if ($search) {
        foreach ($search as $key => $value) {
            if (!$key || !$content) {
                // keine Werte, weiter...
                continue;
            }
            /* hier nochmal die Keys verschlüsseln, damit nicht Ersetzungen verschachtelt werden */
            $akey = '~|~' . base64_encode($value) . '~|~';
            if ($count > 0) {
                $c = strpos($content, $key, 0);
                if (!($c === false)) {
                    for ($i = 0; $i < $count;$i++) {
                        $c = strpos($content, $key, $c);
                        if (!($c === false)) {
                            $content = substr_replace($content, $akey, $c, strlen($key));
                            $c = $c + strlen($key) + 1;
                        }
                    }
                }
            } else {
                $content = str_replace($key, $akey, $content);
            }
            $temp[$akey] = $value;
        }
    }

    /* Die Platzhalter aller Tags jetzt wieder durch die Werte ersetzen */
    if ($temp) {
        $content = str_replace(array_keys($temp), array_values($temp), $content);
    }

    unset($temp);
    return $content;
}

/**
 * langdefine()
 * testet auf Vorhandensein einer Konstante .
 *
 * @param  $konstant $tring - Konstante
 * @param  $text string - Wert
 * @return none
 */

function langdefine($konstant, $text)
{
    defined($konstant) OR define($konstant, $text);
	pmxTranslate::add($konstant,$text);
}

/**
 * pmx_password_create()
 *
 * Ein einigermassen sicheres Passwort generieren.
 * based on: http://technologie4web.de/starke-automatische-passworter-php/
 * -- Darf frei verwendet werden!
 *
 * @return string , sichereres Passwort
 */
function pmx_password_create()
{
    $userconfig = load_class('Userconfig');
    $buchst = array_merge(range('a', 'z'), range('A', 'Z'));
    $zahlen = range(0, 9);
    $sonderzeichen = array('@', '?', '!', '$', '#', '-', '_', '+', '§', ':', ';'); //ggf. Erweitern
    $zeichen_pool = array_merge($buchst, $zahlen, $sonderzeichen);
    $zeichen_pool = array_flip($zeichen_pool); //Damit array-Rand den Array-Wert und nicht den Array-Index bekommt
    $laenge = rand($userconfig->minpass + 1, $userconfig->minpass + 5);
    $pass = '';
    for ($x = 1; $x < $laenge; $x++) {
        $pass .= array_rand($zeichen_pool);
    }
    return $pass;
}

/**
 * pmx_password_salt()
 *
 * Erstellt eine zufällige Zeichenfolge, die als Salt
 * für ein Passwort verwendet werden kann
 *
 * @param integer $laenge
 * @return
 */
function pmx_password_salt($laenge = 32)
{
    $buchst = array_merge(range('a', 'z'), range('A', 'Z'));
    $zahlen = range(0, 9);
    $sonderzeichen = array('@', '?', '!', '$', '-', '+', '', ':', ';');
    $zeichen_pool = array_merge($buchst, $zahlen, $sonderzeichen);
    $zeichen_pool = array_flip($zeichen_pool); //Damit array-Rand den Array-Wert und nicht den Array-Index bekommt
    $salt = '';
    for ($x = 1; $x < $laenge; $x++) {
        $salt .= array_rand($zeichen_pool);
    }
    return $salt;
    // return substr(md5($GLOBALS['mxSecureKey'] . strval(mt_rand() + time())), 0, $len);
}

/**
 * pmx_password_hash()
 *
 * Generiert einen Passwort-Hash mittels pbkdf2()
 *
 * @param mixed $password
 * @param string $salt
 * @return
 */
function pmx_password_hash($password, &$salt = '')
{
    if (!$salt) {
        $salt = pmx_password_salt(32);
    }
    return base64_encode(pbkdf2($password, $salt));
}

/**
 * pmx_password_verify()
 *
 * Überprüft, ob ein gegebenes Passwort einem Hash-Wert,
 * welcher per pmx_password_hash() generiert wude, entspricht.
 *
 * @param string $password , das gegebene Passwort
 * @param string $salt , der zum Hash gehörende Salt
 * @param binaer $hash , der Hash, welcher per pmx_password_hash() generiert wurde
 * @return
 */
function pmx_password_verify($password, $salt, $hash)
{
    return (base64_decode($hash) === pbkdf2($password, $salt));
}

function pmxGetBrowserName($user_agent="")
{
	$user_agent= ($user_agent="")?$_SERVER['HTTP_USER_AGENT']:$user_agert;
    if (strpos($user_agent, 'Opera') || strpos($user_agent, 'OPR/')) return 'Opera';
    elseif (strpos($user_agent, 'Edge')) return 'Edge';
    elseif (strpos($user_agent, 'Chrome')) return 'Chrome';
    elseif (strpos($user_agent, 'Safari')) return 'Safari';
    elseif (strpos($user_agent, 'Firefox')) return 'Firefox';
    elseif (strpos($user_agent, 'MSIE') || strpos($user_agent, 'Trident/7')) return 'Internet Explorer';
   
    return 'Other';
}





/**
 *  normalizePath
 *  
 *  @param $path Description
 *  @return 
 *  
 */function normalizePath($path)
{
    $parts = array();// Array to build a new path from the good parts
    $path = str_replace('\\', '/', $path);// Replace backslashes with forwardslashes
    $path = preg_replace('/\/+/', '/', $path);// Combine multiple slashes into a single slash
    $segments = explode('/', $path);// Collect path segments
    $test = '';// Initialize testing variable
    foreach($segments as $segment)
    {
        if($segment != '.')
        {
            $test = array_pop($parts);
            if(is_null($test))
                $parts[] = $segment;
            else if($segment == '..')
            {
                if($test == '..')
                    $parts[] = $test;

                if($test == '..' || $test == '')
                    $parts[] = $segment;
            }
            else
            {
                $parts[] = $test;
                $parts[] = $segment;
            }
        }
    }
    return implode('/', $parts);
}

?>
