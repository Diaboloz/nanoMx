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

/**
 * zur Kontrollausgabe, um zu sehen, welche Regeln importiert wurden und was
 * in $content steht kann man die Variable "mod_rewrite_check" per GET uebergeben
 */

/**
 * pmxModrewrite
 * Ersetzen der Links in ein suchmaschinenfreundliches Format.
 *
 * @package
 * @author tora60
 * @copyright Copyright (c) 2009
 * @version $Id: Modrewrite.php 6 2015-07-08 07:07:06Z PragmaMx $
 * @access public
 */
class pmxModrewrite {
    /**
     * durch was sollen bei automatischer Ersetzung, die erwuenschten
     * Bindestriche in der URL ersetzt werden?
     */
    public static $alternate = '~';

    /* Trennzeichen zwischen Modulnamen und restlichem Querystring */
    private static $_indicator = '________________________';

    private static $_maxlen_url = 255;

    private static $_maxlen_suhosin = null;

    private static $_supported = false;

    private static $_obj_number2word = null;

    /**
     * pmxModrewrite::__construct()
     *
     * @param mixed $params
     */
    protected function __construct($params = null)
    {
    }

    /**
     * pmxModrewrite::_modules()
     * alle vorhandenen Module ermitteln
     *
     * @return array
     */
    private static function _modules()
    {
        static $modules = array();

        if (!$modules) {
            $cache = load_class('Cache');
            if (($modules = $cache->read('modrewrite_moduleslist')) === false) {
                $modules = array();
                foreach ((array)glob(PMX_MODULES_DIR . DS . '*' . DS . 'index.php', GLOB_NOSORT) as $filename) {
                    if ($filename) {
                        $filename = basename(dirname($filename));
                        $modules[strtolower($filename)] = $filename;
                    }
                }
                $cache->write($modules, 'modrewrite_moduleslist', 18000); // 5 Stunden Cachezeit
            }
        }

        return $modules;
    }

    /**
     * pmxModrewrite::supported()
     * pruefen ob mod_rewrite aktiviert und unterstützt
     *
     * @return
     */
    public static function supported()
    {
        $seo = load_class('Config', 'pmx.seo');
        switch (true) {
            case !$seo:
            case !$seo->modrewrite:
            case !is_array($seo->modrewrite):
                return false;
            default:
                $config = $seo->modrewrite;
        }

        switch (true) {
            case !array_sum($config):
                return false;
            case $config['anony'] && !MX_IS_USER && !MX_IS_ADMIN:
            case $config['users'] && MX_IS_USER && !MX_IS_ADMIN:
            case $config['admin'] && MX_IS_ADMIN:
            case self::$_supported;
                return self::check_htaccess();
            default:
                return false;
        }
    }

    /**
     * pmxModrewrite::enforce()
     * Erzwingt die mod_rewrite Umschreibung
     *
     * @param mixed $status
     * @return
     */
    public static function enforce($status = true)
    {
        self::$_supported = (boolean)$status;
    }

    /**
     * pmxModrewrite::check_htaccess()
     * .htaccess auf Gueltigkeit pruefen
     *
     * @return
     */
    public static function check_htaccess()
    {
        /* PMXMODREWRITE wird in der mod.php definiert */
        if (is_file(PMX_REAL_BASE_DIR . DS . '.htaccess') || defined('PMXMODREWRITE')) {
            // TODO: Inhalt pruefen
            return true;
        }
        return false;
    }

    /**
     * pmxModrewrite::prepare()
     * Alle in dem uebergebenen String enthaltenen Links umschreiben
     *
     * @param mixed $content
     * @param string $homeurl
     * @return string $content
     */
    public static function prepare($content, $homeurl = '')
    {
        /* wenn kein Content, raus hier */
        if (!$content || !self::supported()) {
            return $content;
        }

        /**
         * Array mit allen Schluesseln und Werten zum Suchen & ersetzen zurueckgeben
         * der zu suchende regulaere Ausdruck ist der Schluessel des Arrays der
         * ersetzende Wert ist der Wert des Arrays
         */
        $replace_arr = array();
        $filepath = __DIR__ . DS . 'Modrewrite' . DS;

        /* die Regeln importieren */
        $file = realpath($filepath . 'global.php');
        if ($file && is_file($file)) {
            include($file);
        }
        $file = realpath($filepath . 'custom.php');
        if ($file && is_file($file)) {
            include($file);
        }

        if ($replace_arr) {
            $content = preg_replace(array_keys($replace_arr), array_values($replace_arr), $content);
        }

        /* ab hier werden die Ersetzungen automatisch generiert */
        $pattern = '!(<a(?:[^>]+)href=["\']|<link>)(?:' . preg_quote(PMX_HOME_URL, '!') . '/+|' . preg_quote(PMX_BASE_PATH, '!') . ')?modules\.php\?((?:[[:alnum:]\=&_.;]|&amp;)+)(#[^"\']+)?(["\']|</link>)!i';
        if (!preg_match_all($pattern, $content, $matches)) {
            return $content;
        }

        /* prüfen ob Suhosin installiert und die Parameterlängen limitiert sind */
        $maxlen_suhosin = self::max_parastring_length();

        /* wenn url als Prefix angegeben, diese korrigieren */
        if ($homeurl) {
            $homeurl = trim($homeurl, ' /' . DS) . '/';
        }

        $is_extended = self::_is_extended();

        $replaces = array();

        $modules_array = array();

        /* alle Module ermitteln für Gross-Kleinschreibung */
        $mods = self::_modules();

        foreach($matches[2] as $i => $value) {
            /* gewuenschte Bindestriche ersetzen */
            $value = str_replace(array('-', '&amp;'), array(self::$alternate, '&'), $value);

            /* den alten Wert zwischenspeichern, der wird später ersetzt ;) */
            $old[$i] = $matches[0][$i];

            /* den gefundenen Querystring aufsplitten */
            parse_str($value, $para);

            /* den Modulnamen gesondert behandeln */
            if (array_key_exists('name', $para)) {
                $name = strtolower($para['name']); // klein für Gross-Klein-test
                unset($para['name']);
            } else {
                /* wenn kein Modulname vorhanden, weiter... */
                continue;
            }

            /* Gross-Kleinschreibung fixen */
            if (array_key_exists($name, $mods)) {
                /* immer den in der DB gespeicherten Namen verwenden */
                $name = $mods[$name];
            } else {
                /* wenn Modulname nicht vorhanden, weiter... */
                continue;
            }

            /* unnötige Parameter entfernen */
            if (array_key_exists('op', $para) && $para['op'] === 'modload') {
                unset($para['op']);
            }
            if (array_key_exists('file', $para) && $para['file'] === 'index') {
                unset($para['file']);
            }

            /* prettyPhoto Parameter extra behandeln */
            $add = array();
            if (array_key_exists('iframe', $para) && $para['iframe'] === 'true') {
                $add['iframe'] = 'true';
                unset($para['iframe']);
            }
            if (array_key_exists('ajax', $para) && $para['ajax'] === 'true') {
                $add['ajax'] = 'true';
                unset($para['ajax']);
            }
            if ($add) {
                if (array_key_exists('width', $para)) {
                    $add['width'] = intval($para['width']);
                    unset($para['width']);
                }
                if (array_key_exists('height', $para)) {
                    $add['height'] = intval($para['height']);
                    unset($para['height']);
                }
            }

            /* URL neu zusammensetzen, Modulnamen zuerst */
            $parts = array(0 => $name);
            $str = '';

            /* wenn das array jetzt schon leer ist, sind keine weiteren Parameter vorhanden, also gleich weiter */
            if ($para) {
                foreach ($para as $key => $value) {
                    $str .= "-{$key}-{$value}";
                    if (strlen($str) > $maxlen_suhosin) {
                        $add[$key] = $value;
                    } else {
                        $parts[] = $key . '-' . $value;
                    }
                }

                if ($is_extended) {
                    /* das Array für die individuelle Modulersetzung generieren */
                    $modules_array[$name][$old[$i]] = $para;
                    $modules_array[$name][$old[$i]]['prefix'] = $matches[1][$i] . $homeurl;
                    $modules_array[$name][$old[$i]]['suffix'] = $matches[3][$i] . $matches[4][$i];
                }
                $newurl = implode('-', $parts) . '.html';
            } else {
                $newurl = $parts[0] . '.html';
            }

            /* prettyPhoto Parameter extra behandeln */
            if ($add) {
                $newurl .= '?' . http_build_query($add);
            }

            /* Extralange Parameter ignorieren */
            if (strlen($newurl) <= self::$_maxlen_url) {
                // durch das verwenden von $old als Schlüssel, wird das Array gleich unique
                $replaces[$old[$i]] = $matches[1][$i] . $homeurl . $newurl . $matches[3][$i] . $matches[4][$i];
            }
        }

        /* modulspezifische Ersetzungen auslesen u. tätigen.. */
        if ($modules_array) {
            /* Hook einschränken auf die gefundenen Module */
            $modlist = array_keys($modules_array);
            $hook = load_class('Hook', 'mod_rewrite');
            $hook->set($modules_array);
            $hook->set('modlist', $modlist);
            $hook->set('only_active', false);
            $hook->set('only_allowed', false);

            $hook->run($replaces);
        }

        if ($replaces) {
            $content = str_replace(array_keys($replaces), array_values($replaces), $content);
        }

        /* zur Kontrollausgabe, um zu sehen, welche Regeln importiert wurden und was in $content steht */
        if (isset($_GET['mod_rewrite_check']) && MX_IS_ADMIN) {
            die(mxDebugFuncVars($replace_arr, $replaces));
        }

        return $content;
    }

    /**
     * pmxModrewrite::prepare_url()
     * Die in dem String uebergebene einzelne URL umschreiben
     *
     * @param string $url
     * @param string $homeurl
     * @return string $url
     */
    public static function prepare_url($url, $homeurl = '')
    {
        /* einfach <link> drumlegen, damit es von der normalen Funktion erkannt wird*/
        $url = '<link>' . $url . '</link>';
        $url = self::prepare($url, $homeurl);
        $url = str_replace(array('<link>', '</link>'), '', $url);
        return $url;
    }

    /**
     * pmxModrewrite::undo()
     * Die per umgeschriebener URL dem Script uebergebene Request Parameter
     * wieder in eine verwendbare Form bringen
     *
     * @return string URL
     */
    public static function undo()
    {
        $name = '';
        $get = array();

        $decodeutf8 = (!isset($_SERVER['PMX_REWRITE_DECODE_UTF8']) || 'on' == $_SERVER['PMX_REWRITE_DECODE_UTF8']) ? true : false;
        $_SERVER['QUERY_STRING'] = urldecode($_SERVER['QUERY_STRING']);

        switch (true) {
            case isset($_GET['name']) && empty($_GET['_MORE_']):
                $name = $_GET['name'];
                $querystring = '';
                break;

            case isset($_GET['name']):
                $name = $_GET['name'];
                $querystring = '';
                $che = explode('-', ltrim($_GET['_MORE_'], ' -'));
                $che = array_chunk($che, 2);
                foreach($che as $part) {
                    if ($part[0]) {
                        $key = str_replace(self::$alternate, '-', $part[0]);
                        $value = str_replace(self::$alternate, '-', $part[1]);
                        if ($decodeutf8) {
                            $get[$key] = utf8_decode($value);
                        } else {
                            $get[$key] = $value;
                        }
                    }
                }
                break;

            case strpos($_SERVER['QUERY_STRING'], '&' . self::$_indicator) === false:
                $name = str_replace('..', '', $_SERVER['QUERY_STRING']);
                $querystring = '';
                break;

            default:
                list($name, $querystring) = explode('&' . self::$_indicator, $_SERVER['QUERY_STRING']);
                $che = explode('-', $querystring);
                $che = array_chunk($che, 2);
                foreach($che as $part) {
                    if ($part[0]) {
                        $key = str_replace(self::$alternate, '-', $part[0]);
                        $value = str_replace(self::$alternate, '-', $part[1]);
                        if ($decodeutf8) {
                            $get[$key] = utf8_decode($value);
                        } else {
                            $get[$key] = $value;
                        }
                    }
                }
        }

        if ($name != 'mxcredit' && !file_exists(PMX_MODULES_DIR . DS . $name . DS . 'index.php')) {
            $mods = self::_modules();

            /* Gross-Kleinschreibung fixen */
            if (array_key_exists(strtolower($name), $mods)) {
                /* immer den tatsächlichen Ordner-Namen verwenden */
                $name = $mods[$name];
            } else {
                header('HTTP/1.1 301 Moved Permanently');
            }
        }

        $tmp = str_replace('.', '_', $querystring); // Punkte werden im Arrayschluessel als Unterstrich umgewandelt, deswegen hier ersetzen (z.B. $file in mxBoard)
        unset($_GET['name'], $_REQUEST['name'], $get['name'],
            $_GET[$name], $_REQUEST[$name], $get[$name],
            $_GET[self::$_indicator . $tmp], $_REQUEST[self::$_indicator . $tmp],
            $_GET['_MORE_'], $_REQUEST['_MORE_']
            );

        $_GET = array_merge(array('name' => $name), $get, $_GET);

        $parts = array();
        foreach ($_GET as $key => $value) {
            if (is_scalar($value)) {
                $parts[$key] = "{$key}={$value}";
            }
        }
        $querystring = implode('&', $parts);

        $_SERVER['QUERY_STRING'] = $querystring;
        $_SERVER['SCRIPT_FILENAME'] = str_replace('/mod.php', '/modules.php', $_SERVER['SCRIPT_FILENAME']);
        $_SERVER['PHP_SELF'] = str_replace('/mod.php', '/modules.php', $_SERVER['PHP_SELF']);
        $_SERVER['SCRIPT_NAME'] = str_replace('/mod.php', '/modules.php', $_SERVER['SCRIPT_NAME']);
        if (isset($PHP_SELF)) {
            $PHP_SELF = $_SERVER['PHP_SELF'];
        }
        $_SERVER['REQUEST_URI'] = $_SERVER['PHP_SELF'] . '?' . $querystring;
    }

    /**
     * pmxModrewrite::can_extend()
     * feststellen ob modulspezifische Erweiterungen vorhanden sind
     *
     * @return bool
     */
    public static function can_extend()
    {
        $modules = self::_modules();
        foreach ($modules as $name) {
            $modulefile = PMX_MODULES_DIR . DS . $name . DS . 'core' . DS . 'mod_rewrite.php';
            if (is_file($modulefile)) {
                return true;
            }
        }
        return false;
    }

    /**
     * pmxModrewrite::can_rewrite()
     * feststellen ob die .htaccess Datei geeignet ist
     *
     * @param string $message
     * @return bool
     */
    public static function can_rewrite(&$message = '')
    {
        switch (true) {
            case !file_exists('.htaccess'):
                $message = _PROMODREWERROR1;
                return false;
            case !($htaccess = file_get_contents('.htaccess')):
            case !$htaccess;
            case !preg_match('!(RewriteEngine\s+on).*(\sRewriteRule\s)!si', $htaccess);
                $message = _PROMODREWERROR2;
                return false;
            default:
                return true;
        }
    }

    /**
     * pmxModrewrite::max_parastring_length()
     * prüfen ob Suhosin installiert und die Parameterlängen limitiert sind
     * entsprechend die Maximale Länge zurückgeben
     * http://bugs.pragmamx.de/view.php?id=1464
     *
     * @return integer
     */
    public static function max_parastring_length()
    {
        if (self::$_maxlen_suhosin !== null) {
            return self::$_maxlen_suhosin;
        }

        $sgl = intval(mxIniGet('suhosin.get.max_name_length'));
        $srl = intval(mxIniGet('suhosin.request.max_varname_length'));

        switch (true) {
            case !$sgl && !$srl:
                self::$_maxlen_suhosin = intval(self::$_maxlen_url);
                break;
            case $sgl > $srl:
                self::$_maxlen_suhosin = $srl;
                break;
            case $sgl < $srl:
            case $sgl == $srl:
                self::$_maxlen_suhosin = $sgl;
                break;
        }
        /* Die Länge des Indikators abziehen */
        self::$_maxlen_suhosin -= strlen(self::$_indicator);

        return self::$_maxlen_suhosin;
    }

    /**
     * pmxModrewrite::title_entities()
     *
     * @param mixed $title
     * @param string $delimiter (deprecated)
     * @return
     */
    public static function title_entities($title, $delimiter = '// deprecated')
    {
        $title = html_entity_decode($title, ENT_COMPAT | ENT_HTML5, 'UTF-8');

        $and = (defined('_AND')) ? _AND : 'and';
        $rewriteentities = array(/* Sonderbehandlung */
            '&amp;' => '-' . $and . '-',
            '&' => '-' . $and . '-',
            '+' => '-' . $and . '-',
            '@' => '-' . 'at' . '-',
            '.' => '-' . 'dot' . '-',
            );
        $title = strtr($title, $rewriteentities);

        $title = trim(preg_replace('#[^\w\d_ -]#u', '', $title)); //remove all illegal chars

        $matches = array();
        if (preg_match_all('#[0-9]+#', $title, $matches)) {
            // Nummer in String umwandeln
            rsort($matches[0], SORT_NUMERIC);
            $nw = self::_number2word_object();
            foreach ($matches[0] as $key => $value) {
                $vasl = '-' . $nw->Numbers2Words($value, _DOC_LANGUAGE) . '-';
                $title = str_replace($value, $vasl, $title);
            }
        }

        $matches = array();
        if (preg_match_all('#[^a-zA-Z_ -]#u', $title, $matches)) {
            $rewriteentities = self::_get_replace_entities();

            if (defined('_REWRITEENTITIES')) {
                $rewriteentities = (array)unserialize(_REWRITEENTITIES) + (array)$rewriteentities;
            }

            if (!function_exists('utf8_ord')) {
                include_once(UTF8 . DS . 'ord.php');
            }

            $matches = array_unique($matches[0]);
            foreach ($matches as $search) {
                $ord = utf8_ord($search);
                if (isset($rewriteentities[$ord])) {
                    $title = str_replace($search, $rewriteentities[$ord], $title);
                }
            }
        }

        $title = preg_replace('#[^a-zA-Z_-]#u', '-', $title);
        $title = preg_replace('#-+#u', '-', $title);
        $title = trim($title, '-');

        return $title;
    }

    /**
     * pmxModrewrite::title_parameters()
     *
     * @param mixed $array
     * @return
     */
    public static function title_parameters($array)
    {
        $func_get_args = func_get_args();
        $parameter = array_shift($func_get_args);
        $func_get_args = array_merge($func_get_args, array('old', 'prefix', 'suffix'));
        if ($parameter) {
            $parts = array();
            foreach ($parameter as $key => $value) {
                if (!in_array($key, $func_get_args)) {
                    $parts[] = $key . '-' . $value;
                }
            }
            if ($parts) {
                $more = '-' . implode('-', $parts) . '.html';
                return $more;
            }
        }
        return '.html';
    }

    /**
     * pmxModrewrite::_is_extended()
     * prüfen ob erweiterte mod_rewrite Regeln aktiviert sind
     *
     * @return bol
     */
    protected static function _is_extended()
    {
        $seo = load_class('Config', 'pmx.seo');
        return $seo->modrewriteextend;
    }

    /**
     * pmxModrewrite::_number2word_object()
     * The Numbers_Words class provides method to convert arabic numerals to words.
     *
     * @return object
     */
    protected static function _number2word_object()
    {
        if (is_object(self::$_obj_number2word)) {
            return self::$_obj_number2word;
        }
        // Klasse einbinden
        include_once(__DIR__ . '/_misc/Numbers_Words.php');
        // Objekt erzeugen
        self::$_obj_number2word = new Numbers_Words();
        return self::$_obj_number2word;
    }

    /**
     * pmxModrewrite::_get_replace_entities()
     *
     * @return array
     */
    protected static function _get_replace_entities()
    {
        $rewriteentities = array( /* Zeichen-Nummer, Ersatz, Sonderzeichen, Beschreibung */
            181 => 'my',    // µ : Mikro-Zeichen
            192 => 'A',     // À : Großes A mit Grave (Accent grave)
            193 => 'A',     // Á : Großes A mit Akut
            194 => 'A',     // Â : Großes A mit Zirkumflex
            195 => 'A',     // Ã : Großes A mit Tilde
            196 => 'AE',    // Ä : Großes A mit Diaeresis (Umlaut)
            197 => 'A',     // Å : Großes A mit Ring (Krouzek)
            198 => 'Ae',    // Æ : Ligatur aus großem A und großem E
            199 => 'C',     // Ç : Großes C mit Cedilla
            200 => 'E',     // È : Großes E mit Grave
            201 => 'E',     // É : Großes E mit Akut
            202 => 'E',     // Ê : Großes E mit Zirkumflex
            203 => 'E',     // Ë : Großes E mit Diaeresis (Trema)
            204 => 'I',     // Ì : Großes I mit Grave
            205 => 'I',     // Í : Großes I mit Akut
            206 => 'I',     // Î : Großes I mit Zirkumflex
            207 => 'I',     // Ï : Großes I mit Diaeresis (Trema)
            208 => 'ETH',   // Ð : Großes Eth
            209 => 'N',     // Ñ : Großes N mit Tilde
            210 => 'O',     // Ò : Großes O mit Grave
            211 => 'O',     // Ó : Großes O mit Akut
            212 => 'O',     // Ô : Großes O mit Zirkumflex
            213 => 'O',     // Õ : Großes O mit Tilde
            214 => 'OE',    // Ö : Großes O mit Diaeresis (Umlaut)
            216 => 'O',     // Ø : Großes O mit Schrägstrich
            217 => 'U',     // Ù : Goßes U mit Grave
            218 => 'U',     // Ú : Großes U mit Akut
            219 => 'U',     // Û : Großes U mit Zirkumflex
            220 => 'UE',    // Ü : Großes U mit Diaeresis (Umlaut)
            221 => 'Y ',    // Ý : Großes Y mit Akut
            222 => 'THORN', // Þ : Großes Thorn
            223 => 'ss',    // ß : Esszett, Ligatur aus s und z
            224 => 'a',     // à : Kleines a mit Grave
            225 => 'a',     // á : Kleines a mit Akut
            226 => 'a',     // â : Kleines a mit Zirkumflex
            227 => 'a',     // ã : Kleines a mit Tilde
            228 => 'ae',    // ä : Kleines a mit Diaeresis (Umlaut)
            229 => 'a',     // å : Kleines a mit Ring (Krouzek)
            230 => 'ae',    // æ : Ligatur aus a und e
            231 => 'c',     // ç : Kleines c mit Cedilla
            232 => 'e',     // è : Kleines e mit Grave (Accent grave)
            233 => 'e',     // é : Kleines e mit Akut
            234 => 'e',     // ê : Kleines e mit Zirkumflex
            235 => 'e ',    // ë : Kleines e mit Diaeresis (Trema)
            236 => 'i',     // ì : Kleines i mit Grave
            237 => 'i',     // í : Kleines i mit Akut
            238 => 'i',     // î : Kleines i mit Zirkumflex
            239 => 'i',     // ï : Kleines i mit Diaeresis (Trema)
            240 => 'eth',   // ð : Kleines eth
            241 => 'n',     // ñ : Kleines n mit Tilde
            242 => 'o',     // ò : Kleines o mit Grave
            243 => 'o',     // ó : Kleines o mit Akut
            244 => 'o',     // ô : Kleines o mit Zirkumflex
            245 => 'o',     // õ : Kleines o mit Tilde
            246 => 'oe',    // ö : Kleines o mit Diaeresis (Umlaut)
            248 => 'oe',    // ø : Kleines o mit Schrägstrich
            249 => 'u',     // ù : Kleines u mit Grave
            250 => 'u',     // ú : Kleines u mit Akut
            251 => 'u',     // û : Kleines u mit Zirkumflex
            252 => 'ue',    // ü : Kleines u mit Diaeresis (Umlaut)
            253 => 'y',     // ý : Kleines y mit Akut
            254 => 'thorn', // þ : Kleines thorn
            255 => 'y',     // ÿ : Kleines y mit Diaeresis
            256 => 'A',     // Ā : Großes A mit Macron
            257 => 'a',     // ā : Kleines a mit Macron
            258 => 'A',     // Ă : Großes A mit Breve
            259 => 'a',     // ă : Kleines a mit Breve
            260 => 'A',     // Ą : Großes A mit Ogonek
            261 => 'a',     // ą : Kleines a mit Ogonek
            262 => 'C',     // Ć : Großes C mit Akut (accent aigu)
            263 => 'c',     // ć : Kleines c mit Akut (accent aigu)
            264 => 'C',     // Ĉ : Großes C mit Zirkumflex
            265 => 'c',     // ĉ : Kleines c mit Zirkumflex
            266 => 'C',     // Ċ : Großes C mit einem Punkt darüber
            267 => 'c',     // ċ : Kleines c mit einem Punkt darüber
            268 => 'C',     // Č : Großes C mit Caron (Hatschek)
            269 => 'c',     // č : Kleines c mit Caron (Hatschek)
            270 => 'D',     // Ď : Großes D mit Caron (Hatschek)
            271 => 'd',     // ď : Kleines d mit Caron (Hatschek)
            272 => 'D',     // Đ : Großes D mit Querstrich
            273 => 'd',     // đ : Kleines d mit Querstrich
            274 => 'E',     // Ē : Großes E mit Macron
            275 => 'e',     // ē : Kleines e mit Macron
            276 => 'E',     // Ĕ : Großes E mit Breve
            277 => 'e',     // ĕ : Kleines e mit Breve
            278 => 'E',     // Ė : Großes E mit einem Punkt darüber
            279 => 'e',     // ė : Kleines e mit einem Punkt darüber
            280 => 'E',     // Ę : Großes E mit Ogonek
            281 => 'e',     // ę : Kleines e mit Ogonek
            282 => 'E',     // Ě : Großes E mit Caron (Hatschek)
            283 => 'e',     // ě : Kleines e mit Caron (Hatschek)
            284 => 'G',     // Ĝ : Großes G mit Zirkumflex
            285 => 'g',     // ĝ : Kleines g mit Zirkumflex
            286 => 'G',     // Ğ : Großes G mit Breve
            287 => 'g',     // ğ : Kleines g mit Breve
            288 => 'G',     // Ġ : Großes G mit einem Punkt darüber
            289 => 'g',     // ġ : Kleines g mit einem Punkt darüber
            290 => 'G',     // Ģ : Großes G mit Cedilla
            291 => 'g',     // ģ : Kleines g mit Cedilla
            292 => 'H',     // Ĥ : Großes H mit Zirkumflex
            293 => 'h',     // ĥ : Kleines h mit Zirkumflex
            294 => 'H',     // Ħ : Großes H mit Querstrich
            295 => 'h',     // ħ : Kleines h mit Querstrich
            296 => 'I',     // Ĩ : Großes I mit Tilde
            297 => 'i',     // ĩ : Kleines i mit Tilde
            298 => 'I',     // Ī : Großes I mit Macron
            299 => 'i',     // ī : Kleines i mit Macron
            300 => 'I',     // Ĭ : Großes I mit Breve
            301 => 'i',     // ĭ : Kleines i mit Breve
            302 => 'I',     // Į : Großes I mit Ogonek
            303 => 'i',     // į : Kleines I mit Ogonek
            304 => 'I',     // İ : Großes I mit Punkt darüber
            305 => 'i',     // ı : Kleines i ohne Punkt
            306 => 'Ij',    // Ĳ : Ligatur aus I und J
            307 => 'ij',    // ĳ : Ligatur aus i und j
            308 => 'J',     // Ĵ : Großes J mit Zirkumflex
            309 => 'j',     // ĵ : Kleines j mit Zirkumflex
            310 => 'K',     // Ķ : Großes K mit Cedilla
            311 => 'k',     // ķ : Kleines k mit Cedilla
            312 => 'kra',   // ĸ : Kleines kra
            313 => 'L',     // Ĺ : Großes L mit Akut
            314 => 'l',     // ĺ : Kleines l mit Akut
            315 => 'L',     // Ļ : Großes L mit Cedilla
            316 => 'l',     // ļ : Kleines l mit Cedilla
            317 => 'L',     // Ľ : Großes L mit Caron (Hatschek)
            318 => 'l',     // ľ : Kleines l mit Caron (Hatschek)
            319 => 'L',     // Ŀ : Großes L mit Punkt in der Mitte
            320 => 'l',     // ŀ : Kleines l mit Punkt in der Mitte
            321 => 'L',     // Ł : Großes L mit Schrägstrich
            322 => 'l',     // ł : Kleines l mit Schrägstrich
            323 => 'N',     // Ń : Großes N mit Akut
            324 => 'n',     // ń : Kleines n mit Akut
            325 => 'N',     // Ņ : Großes N mit Cedilla
            326 => 'n',     // ņ : Kleines n mit Cedilla
            327 => 'N',     // Ň : Großes N mit Caron (Hatschek)
            328 => 'n',     // ň : Kleines n mit Caron (Hatschek)
            329 => 'n',     // ŉ : Kleines n, dem ein Apostroph vorausgeht
            330 => 'ENG',   // Ŋ : Großes Eng
            331 => 'eng',   // ŋ : Kleines eng
            332 => 'O',     // Ō : Großes O mit Macron
            333 => 'o',     // ō : Kleines o mit Macron
            334 => 'O',     // Ŏ : Großes O mit Breve
            335 => 'o',     // ŏ : Kleines o mit Breve
            336 => 'O',     // Ő : Großes O mit Doppel-Akut
            337 => 'o',     // ő : Kleines o mit Doppel-Akut
            338 => 'Oe',    // Œ : Ligatur aus O und E
            339 => 'oe',    // œ : Ligatur aus o und e
            340 => 'R',     // Ŕ : Großes R mit Akut
            341 => 'r',     // ŕ : Kleines r mit Akut
            342 => 'R',     // Ŗ : Großes R mit Cedilla
            343 => 'r',     // ŗ : Kleines r mit Cedilla
            344 => 'R',     // Ř : Großes R mit Caron (Hatschek)
            345 => 'r',     // ř : Kleines r mit Caron (Hatschek)
            346 => 'S',     // Ś : Großes S mit Akut
            347 => 's',     // ś : Kleines s mit Akut
            348 => 'S',     // Ŝ : Großes S mit Zirkumflex
            349 => 's',     // ŝ : Kleines s mit Zirkumflex
            350 => 'S',     // Ş : Großes S mit Cedilla
            351 => 's',     // ş : Kleines s mit Cedilla
            352 => 'S',     // Š : Großes S mit Caron (Hatschek)
            353 => 's',     // š : Kleines s mit Caron (Hatschek)
            354 => 'T',     // Ţ : Großes T mit Cedilla
            355 => 't',     // ţ : Kleines t mit Cedilla
            356 => 'T',     // Ť : Großes T mit Caron (Hatschek)
            357 => 't',     // ť : Kleines t mit Caron (Hatschek)
            358 => 'T',     // Ŧ : Großes T mit Querstrich
            359 => 't',     // ŧ : Kleines t mit Querstrich
            360 => 'U',     // Ũ : Großes U mit Tilde
            361 => 'u',     // ũ : Kleines u mit Tilde
            362 => 'U',     // Ū : Großes U mit Macron
            363 => 'u',     // ū : Kleines u mit Macron
            364 => 'U',     // Ŭ : Großes U mit Breve
            365 => 'u',     // ŭ : Kleines u mit Breve
            366 => 'U',     // Ů : Großes U mit Ring darüber (Krouzek)
            367 => 'u',     // ů : Kleines u mit Ring darüber (Krouzek)
            368 => 'U',     // Ű : Großes U mit Doppel-Akut
            369 => 'u',     // ű : Kleines u mit Doppel-Akut
            370 => 'U',     // Ų : Großes U mit Ogonek
            371 => 'u',     // ų : Kleines u mit Ogonek
            372 => 'W',     // Ŵ : Großes W mit Zirkumflex
            373 => 'w',     // ŵ : Kleines w mit Zirkumflex
            374 => 'Y',     // Ŷ : Großes Y mit Zirkumflex
            375 => 'y',     // ŷ : Kleines y mit Zirkumflex
            376 => 'Y',     // Ÿ : Großes Y mit Diaeresis
            377 => 'Z',     // Ź : Großes Z mit Akut
            378 => 'z',     // ź : Kleines z mit Akut
            379 => 'Z',     // Ż : Großes Z mit einem Punkt darüber
            380 => 'z',     // ż : Kleines z mit einem Punkt darüber
            381 => 'Z',     // Ž : Großes Z mit Caron (Hatschek)
            382 => 'z',     // ž : Kleines z mit Caron (Hatschek)
            383 => 's',     // ſ : Kleines Lang-s
        );
        return $rewriteentities;
    }
}

?>