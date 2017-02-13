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
 * $Revision: 1.35 $
 * $Author: tora60 $
 * $Date: 2014-02-09 10:07:30 $
 *
 * ACHTUNG WICHTIG !!
 * Bitte ändern sie nicht diese Original Systemdatei!
 * Um das Standard Stylesheet anzupassen, erstellen sie in diesem
 * Ordner eine zusätzliche css Datei die ihre Änderungen enthält.
 * Der Dateiname muss mit "custom" beginnen und die Dateiendung
 * muss ".css" sein.
 * z.B.: custom.irgendetwas.css
 */

define('mxMainFileLoaded', true);

/**
 * default_css
 *
 * @package
 * @author tora60
 * @copyright Copyright (c) 2009
 * @version $Id: default.css.php,v 1.35 2014-02-09 10:07:30 tora60 Exp $
 * @access public
 */
class default_css {
    private $pmxroot = '';
    private $theme = '';
    private $themecolors = array('bgcolor1' => '', 'bgcolor2' => '', 'bgcolor3' => '', 'bgcolor4' => '', 'textcolor1' => '', 'textcolor2' => '');
    private $cachefile = '';
    private $dynasheet = '';
    private $browser = null;
    private $comment = array('start' => '~<<<~~~<~', 'end' => '~>~~~>>>~');
    private $skipcache = false;

    /**
     * default_css::__construct()
     */
    public function __construct()
    {
        /* Puffer starten, um spaeter alle Ausgaben zu verwerfen */
        ob_start();

        $this->pmxroot = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR;

        /* guggen was für ein Browser */
        include_once($this->pmxroot . 'includes/classes/Browser.php');
        $this->browser = new pmxBrowser();

        /* aktuelles Theme ermitteln */
        $theme = $this->gettheme();

        /* Cache ignorieren? */
        $this->skipcache = isset($_GET['skipcache']);

        /* Browser ermitteln, abgleichen mit __destruct !! */
        switch (true) {
            case $this->browser->is_gecko():
                $agent = '.gecko';
                break;
            case $this->browser->is_msie() && $this->browser->version < 8:
                $agent = '.msie' . $this->browser->version;
                break;
            default:
                $agent = '';
        }

        /* cachefile-Name erstellen (hier, weil in __destruct die Pfade nicht mehr stimmen) */
        $this->cachefile = $this->pmxroot . 'dynadata' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'global-default-' . $theme . $agent . '.css';
        /* Dateiname mit header.php abgleichen !! */
        $this->dynasheet = $this->pmxroot . 'layout' . DIRECTORY_SEPARATOR . 'style' . DIRECTORY_SEPARATOR . '_theme.' . $theme . $agent . '.css';

        /* Farben aus dem Theme ermitteln */
        $this->_initthemecolors($theme);
    }

    /**
     * default_css::__destruct()
     */
    public function __destruct()
    {
        $changetime = array();
        if (!$this->skipcache && is_file($this->cachefile)) {
            /* Wenn Cache-Datei vorhanden, diese verwenden */
            $out = trim(file_get_contents($this->cachefile)) . "\n/* << cached >> */";
            $changetime[] = filemtime($this->cachefile);
        } else {
            /* Pfad zu dieser Datei */
            $absolute = __DIR__ . DIRECTORY_SEPARATOR;

            /* Dateien, die gesondert einsortiert werden */
            $fixed = array(// kompletter Pfad!
                $absolute . 'default.reset.css',
                $absolute . 'default.css',
                $absolute . 'default.msie6.css',
                $absolute . 'default.msie7.css',
                // $absolute . 'default.msie8.css',
                $absolute . 'default.gecko.css',
                );

            $key = 0;

            /* Standarddateien ermitteln */
            $files = (array)glob($absolute . 'default*.css');
            foreach ($files as $file) {
                if ($file && (!in_array($file, $fixed))) {
                    $arr[$file] = $key++;
                }
            }

            /* Inhalte, die immer ganz oben stehen sollten */
            $arr[$absolute . 'default.reset.css'] = -2;
            $arr[$absolute . 'default.css'] = -1;
            /* Inhalte, die immer ganz unten stehen sollten */
            switch (true) {
                /* Fixes fuer Firefox u.Co. */
                case $this->browser->is_gecko():
                    $arr[$absolute . 'default.gecko.css'] = $key++;
                    break;
                /* Fixes fuer alte IE < 9 */
                case $this->browser->msie && $this->browser->version >= 8:
                    // $arr[$absolute . 'default.msie8.css'] = $key++;
                    break;
                case $this->browser->msie && $this->browser->version >= 7:
                    $arr[$absolute . 'default.msie7.css'] = $key++;
                    break;
                case $this->browser->msie && $this->browser->version < 7:
                    $arr[$absolute . 'default.msie6.css'] = $key++;
                    break;
            }

            /* Benutzerdateien ermitteln */
            $files = (array)glob($absolute . 'custom*.css');
            foreach ($files as $file) {
                if ($file) {
                    $arr[$file] = $key++;
                }
            }

            /* sortieren, damit auch alles an den rechten Platz kommt ;-) */
            asort($arr);

            /* eigentliche Inhalte einlesen */
            $out = '';
            foreach ($arr as $file => $dummy) {
                $out .= $this->comment['start'] . basename($file) . $this->comment['end'];
                $out .= file_get_contents($file);
                $changetime[] = filemtime($file);
            }

            /* Platzhalter, Farben, etc. umschreiben */
            $out = $this->prepare($out);

            if (!$this->skipcache) {
                /* Cache schreiben */

                file_put_contents($this->cachefile, $out);
                $this->_chmod_read($this->cachefile);

                if (is_writeable(dirname($this->dynasheet)) || is_writeable($this->dynasheet)) {
                    file_put_contents($this->dynasheet, $out);
                    $this->_chmod_read($this->dynasheet);
                }
            }
        }

        /* alle eventuelle Ausgaben verwerfen */
        for ($i = 1; ($i <= 10 && ob_get_contents()); $i++) {
            ob_end_clean();
        }

        /* CSS Header senden */
        sort($changetime);
        header('Content-Type: text/css; charset=utf-8');
        header('X-Powered-By: pragmaMx-cms');
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 3600 * 24) . ' GMT'); // 1 day
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', array_pop($changetime)) . ' GMT');
        header('Etag: ' . md5($out));

        /* raus damit... */
        echo $this->compress_output($out, 5);
    }

    /**
     * default_css::prepare()
     * Platzhalter, Farben, etc. umschreiben
     *
     * @param mixed $out
     * @param mixed $this ->theme
     * @return
     */
    public function prepare($out)
    {
        ob_start();

        $colors = $this->getcolors();

        $out .= $this->comment['start'] . 'theme colors' . $this->comment['end'];
        foreach ($colors as $key => $value) {
            /* die bgColor-Klassen erstellen */
            switch (substr($key, 0, 2)) {
                case 'al':
                case 'bg':
                    /* und anfuegen... */
                    $out .= "\n*.{$key}{background-color:$value;color:inherit;}";
                    break;
            }
            /* suche/ersetze Array erstellen */
            $search[$key] = '___' . $key . '___';
            $replace[$key] = $value;
        }

        /* erstelltes suche/ersetze Array anwenden */
        $out = str_replace($search, $replace, $out);

        $header = '/**' . "\n"
         . ' * pragmaMx - Web Content Management System' . "\n"
         . ' * Copyright by pragmaMx Developer Team - http://www.pragmamx.org' . "\n"
         . ' * $Id: default.css.php,v 1.35 2014-02-09 10:07:30 tora60 Exp $' . "\n"
         . ' */';

        /* die html-Bestandteile entfernen (die kommen von topstyle) */
        $out = str_replace(array("\t", "\r", '<sty' . 'le>', '</st' . 'yle>', '<!--', '-->'), '', $out); // style getrennt wegen css-Formater ;)
        /* Kommentare und unnoetige Leerzeichen entfernen */
        $out = preg_replace('#(\/\*[^{}]*\*\/)|[[:cntrl:]]#', ' ', $out);
        $out = preg_replace('#\s{2,}#', ' ', $out);
        $s = array($this->comment['start'], $this->comment['end'], '}' , "\n ", ": ", "; ", "{ ", " {", "} ", " }", ", ", ";@", "}\n}");
        $r = array("\n/* " , " */\n" , "}\n", "\n", ":" , ";" , "{" , "{" , "}" , "}" , "," , ";\n@", "}}");
        $out = str_replace($s, $r, trim($out));
        /* doppelte charsetdefinitionen entfernen und die erste an den Anfang setzen */
        preg_match_all('#\s*\@charset\s*["\'].+["\'];#i', $out, $matches);
        $out = trim($matches[0][0]) . "\n" . trim($header) . "\n" . str_replace($matches[0], '', $out) . "\n";

        /* eventuelle Fehlermeldungen und Ausgaben einfach loeschen */
        ob_end_clean();
        return trim($out);
    }

    /**
     * default_css::gettheme()
     *
     * @return
     */
    public function gettheme()
    {
        if (!$this->theme) {
            switch (true) {
                case !empty($_GET['t']):
                    $theme = preg_replace('#[^A-Za-z0-9_-]#', '_', $_GET['t']);
                    break;
                case include(realpath($this->pmxroot . 'config.php')):
                    $theme = $Default_Theme;
                    break;
                default:
                    $theme = 'mx-default';
            }

            /* aktuelles Theme ermitteln u. pruefen, ob verwendbar */
            switch (true) {
                case is_file($this->pmxroot . 'themes/' . $theme . '/theme.php'):
                    break;
                case $theme = glob($this->pmxroot . 'themes/*/theme.php'):
                    /* irgend ein Theme holen... */
                    $theme = basename(dirname($theme[0]));
                    break;
                default:
                    die('/* no pragmaMx themes available */');
            }

            $this->theme = $theme;
        }

        return $this->theme;
    }

    /**
     * default_css::getcolors()
     * Farben aus Theme ermitteln
     *
     * @return array
     */
    public function getcolors()
    {
        extract($this->themecolors);

        /* dunkles (-1) oder helles (1) theme, falls bereits erkennbar? */
        $darklight = 127.5;
        if ($this->themecolors && isset($bgcolor1)) {
            if ($bgcolor1[0] == '#') {
                $bgcolor1 = helper_colors::correctlen($bgcolor1);
            } else {
                $bgcolor1 = helper_colors::getnamedcolor($bgcolor1);
            }
            $faktor = (helper_colors::hexavrg($bgcolor1) < $darklight) ? -1 : 1;
        } else {
            $faktor = 1;
        }

        /**
         * Standardfarben die im System und den Modulen verwendet werden
         * falls nicht vorhanden, versch. Graustufen verwenden
         *
         * $bgcolor2 is generaly used for the tables border as you can
         * see on OpenTable() function,
         * $bgcolor1 is for the table background and the
         * other two bgcolor variables follows the same criteria.
         * $texcolor1 and 2 are for tables internal texts
         */

        /* Standardwerte, fuer die bgColor-Klassen, alles in Graustufen */
        if ($faktor == 1) {
            $colors = array(// helles Theme
                'bgcolor1' => '#fbfbfb',
                'bgcolor2' => '#f7f7f7',/* Rahmenfarbe */
                'bgcolor3' => '#f5f5f5',
                'bgcolor4' => '#e7e7e7',/* Rahmenfarbe */
                'textcolor1' => '#2F2F2F',
                'textcolor2' => '#5F5F5F',
                );
        } else {
            $colors = array(// dunkel
                'bgcolor1' => '#313131',
                'bgcolor2' => '#494949',/* Rahmenfarbe */
                'bgcolor3' => '#222222',
                'bgcolor4' => '#3a3a3a',/* Rahmenfarbe */
                'textcolor1' => '#fbfbfb',
                'textcolor2' => '#ebebeb',
                );
        }
        // print_r($colors);
        $part = array();
        foreach ($colors as $key => $value) {
            /* Wenn passender Theme-Key vorhanden, diesen anstatt dem Standard verwenden */
            if (isset($$key)) {
                $value = htmlspecialchars($$key);
            }
            if ($value[0] == '#') {
                $value = helper_colors::correctlen($value);
            } else {
                $value = helper_colors::getnamedcolor($value);
            }
            $colors[$key] = $value;
        }

        /* hell(1)/dunkel(-1) Faktor nochmals gegenpruefen u. ggf. korrigieren */
        $faktor = (helper_colors::hexavrg($colors['bgcolor1']) < $darklight) ? -1 : 1;

        /* gleich definierte Farben korrigieren um Kontraste zu bewahren */
        if ($colors['bgcolor2'] == $colors['bgcolor1']) {
            $colors['bgcolor2'] = helper_colors::darker($colors['bgcolor2'], + 25 * $faktor);
        }
        if ($colors['bgcolor4'] == $colors['bgcolor3']) {
            $colors['bgcolor4'] = helper_colors::darker($colors['bgcolor4'], + 25 * $faktor);
        }
        if ($colors['bgcolor1'] == $colors['bgcolor3']) {
            $colors['bgcolor3'] = helper_colors::darker($colors['bgcolor3'], -15 * $faktor);
        }
        if ($colors['bgcolor2'] == $colors['bgcolor4']) {
            $colors['bgcolor4'] = helper_colors::darker($colors['bgcolor4'], -15 * $faktor);
        }

        /* Wenn die definierten textfarben extrem hell oder dunkel sind,
         * diese nicht verwenden
         */
        $check = helper_colors::hexavrg($colors['textcolor1']);
        if ($faktor == -1 && $check <= 10) {
            /* dunkles Theme braucht helle Farbe */
            $colors['textcolor1'] = '#F0F0F0';
        } else if ($faktor == 1 && $check >= 245) {
            /* helles Theme braucht dunkle Farbe */
            $colors['textcolor1'] = '#E0E0E0';
        }

        $check = helper_colors::hexavrg($colors['textcolor2']);
        if ($faktor == -1 && $check <= 10) {
            /* dunkles Theme braucht helle Farbe */
            $colors['textcolor1'] = '#2F2F2F';
        } else if ($faktor == 1 && $check >= 245) {
            /* helles Theme braucht dunkle Farbe */
            $colors['textcolor2'] = '#5F5F5F';
        }

        /* Rahmenfarbe und 2 Alternativen */
        $colors['bordercolor'] = $colors['bgcolor2'];
        $colors['bordercolor-dark'] = helper_colors::darker($colors['bordercolor'], + 20 * $faktor);
        $colors['bordercolor-light'] = helper_colors::darker($colors['bordercolor'], -20 * $faktor);

        /* Alternativfarben, basierend auf bgcolor1, z.B. fuer Tabellen */
        $increment = 10;
        $color = $colors['bgcolor1'];
        $check = helper_colors::hexavrg(helper_colors::darker($color, + ($increment * 3) * $faktor));
        for ($i = 0; $i <= 4; $i++) {
            $new = helper_colors::darker($color, + $increment * $faktor * $i);
            $check = helper_colors::hexavrg($new);
            if (($i != 0 && $i != 4) && ($check == 255 || $check == 0)) {
                /* wird eine Farbstufe schwarz, oder weiss, die Intensitaet umkehren */
                $new = helper_colors::darker($color, + $increment * $faktor * $i * -1);
            }
            $colors['alternate-' . $i] = $new;
        }

        return $colors;
    }

    /**
     * default_css::_initthemecolors()
     * die theme.php des eingestellten Themes includen, um die Farbvariablen zu ermitteln
     *
     * @return
     */
    private function _initthemecolors($theme)
    {

        /* Themeordner ermitteln */
        $themepath = $this->pmxroot . 'themes' . DIRECTORY_SEPARATOR . $theme;

        /* versch. Konstanten definieren, die evtl. im Theme gebraucht werden */
        define('MX_THEME', $theme); // Konstante wird in manchen themes verwendet
        define('MX_THEME_DIR', $themepath); // Konstante wird in manchen themes verwendet
        define('DONT_INIT_THEME', true); // theme-Engine nicht laden
        define('PMX_VERSION', '0.1.11'); // Konstante wird in manchen themes verwendet
        define('PMX_SYSTEM_DIR', $this->pmxroot . DIRECTORY_SEPARATOR . 'includes'); // Konstante wird in manchen themes verwendet

        /* aktuelles Arbeitsverzeichnis zwischenspeichern */
        $cwd = getcwd();

        /* versuchen das Arbeitsverzeichnis ins mx-root zu wechseln */
        $changed = (chdir($this->pmxroot)) ? true : false;

        /* die Theme-Datei includen */
        $files = array('theme.colors.php', 'colors.php', 'settings.php', 'theme.php',);
        foreach ($files as $file) {
            if (is_file($themepath . DIRECTORY_SEPARATOR . $file)) {
                if (include($themepath . DIRECTORY_SEPARATOR . $file)) {
                    if ($colors = compact('bgcolor1', 'bgcolor2', 'bgcolor3', 'bgcolor4', 'textcolor1', 'textcolor2')) {
                        /* Farbarray initialisieren */
                        $this->themecolors = array_merge($this->themecolors, $colors);
                        break;
                    }
                }
            }
        }

        /* wenn Arbeitsverzeichnis geaendert wurde, wieder zurueck wechseln */
        if ($changed) {
            chdir($cwd);
        }
    }

    /**
     * default_css::compress_output()
     * try to compress output
     *
     * @param mixed $contents
     * @param integer $level
     * @return
     */
    public function compress_output($contents, $level = 0)
    {
        $encoding = (empty($_SERVER['HTTP_ACCEPT_ENCODING'])) ? false : $_SERVER['HTTP_ACCEPT_ENCODING'];

        $size = strlen($contents);
        switch (true) {
            case !$level:
            case !$encoding;
            case $size < 2048:
            case headers_sent():
            case !function_exists('gzcompress'):
            case !is_callable('gzcompress'):
            case ini_get('zlib.output_compression'):
            case ini_get('output_handler') == 'ob_gzhandler':
                return $contents;

            case strpos($encoding, 'gzip') !== false:
                header('Content-Encoding: gzip');
                break;

            case strpos($encoding, 'x-gzip') !== false:
                header('Content-Encoding: x-gzip');
                break;

            default:
                return $contents;
        }

        $crc = crc32($contents);

        $out = "\x1f\x8b\x08\x00\x00\x00\x00\x00";
        $out .= substr(gzcompress($contents, $level), 0, - 4);
        $out .= pack("V", $crc) . pack("V", $size);

        return $out;
    }

    /**
     * default_css::_chmod_read()
     *
     * @param mixed $path
     * @param mixed $umask
     * @return bolean
     */
    private function _chmod_read($path, $umask = true)
    {
        if (!function_exists('chmod')) {
            return false;
        }

        if ($umask && !function_exists('umask')) {
            $umask = false;
        }

        if ($umask) {
            $old = umask(0);
        }

        $olderr = error_reporting(0);
        $result = chmod($path, octdec('0666'));
        error_reporting($olderr);

        if ($umask) {
            umask($old);
        }

        return $result;
    }
}

/**
 * helper_colors
 *
 * @package
 * @author tora60
 * @copyright Copyright (c) 2009
 * @version $Id: default.css.php,v 1.35 2014-02-09 10:07:30 tora60 Exp $
 * @access public
 */
class helper_colors {
    /**
     * helper_colors::darker()
     *
     * @param mixed $color
     * @param integer $c
     * @return
     */
    public static function darker($color, $c = 10)
    {
        $rgb = str_split(trim($color, '#'), 2);
        foreach ($rgb as $i => $value) {
            $hex = hexdec($rgb[$i]) - $c;
            if ($hex > 255) {
                $rgb[$i] = 'ff';
            } else if ($hex < 0) {
                $rgb[$i] = '00';
            } else {
                $rgb[$i] = substr('00' . dechex($hex), -2);
            }
        }
        return '#' . $rgb[0] . $rgb[1] . $rgb[2];
    }

    /**
     * helper_colors::oppositehex()
     *
     * @param mixed $color
     * @return
     */
    public static function oppositehex($color)
    {
        $color = trim($color, '#');

        $r = dechex(255 - hexdec(substr($color, 0, 2)));
        $r = (strlen($r) > 1) ? $r : '0' . $r;
        $g = dechex(255 - hexdec(substr($color, 2, 2)));
        $g = (strlen($g) > 1) ? $g : '0' . $g;
        $b = dechex(255 - hexdec(substr($color, 4, 2)));
        $b = (strlen($b) > 1) ? $b : '0' . $b;
        return '#' . $r . $g . $b;
    }

    /**
     * helper_colors::correctlen()
     *
     * @param mixed $color
     * @return
     */
    public static function correctlen($color)
    {
        $color = trim($color, '#');
        if (strlen($color) < 6) {
            return '#' . str_repeat($color[0], 2) . str_repeat($color[1], 2) . str_repeat($color[2], 2);
        }
        return '#' . $color;
    }

    /**
     * helper_colors::getrgbvalues()
     *
     * @param mixed $color
     * @return
     */
    public static function getrgbvalues($color)
    {
        return array_map('hexDec', str_split(trim($color, '#'), 2));
    }

    /**
     * helper_colors::hexavrg()
     *
     * @param mixed $color
     * @return
     */
    public static function hexavrg($color)
    {
        return array_sum(self::getrgbvalues($color)) / 3;
    }

    /**
     * helper_colors::getnamedcolor()
     *
     * @param mixed $color
     * @return
     */
    public static function getnamedcolor($color)
    {
        $colors = array(// Farbname => Hexwert
            'aliceblue' => 'F0F8FF',
            'antiquewhite' => 'FAEBD7',
            'aqua' => '00FFFF',
            'aquamarine' => '7FFFD4',
            'azure' => 'F0FFFF',
            'beige' => 'F5F5DC',
            'bisque' => 'FFE4C4',
            'black' => '000000',
            'blanchedalmond' => 'FFEBCD',
            'blue' => '0000FF',
            'blueviolet' => '8A2BE2',
            'brown' => 'A52A2A',
            'burlywood' => 'DEB887',
            'cadetblue' => '5F9EA0',
            'chartreuse' => '7FFF00',
            'chocolate' => 'D2691E',
            'coral' => 'FF7F50',
            'cornflowerblue' => '6495ED',
            'cornsilk' => 'FFF8DC',
            'crimson' => 'DC143C',
            'cyan' => '00FFFF',
            'darkblue' => '00008B',
            'darkcyan' => '008B8B',
            'darkgoldenrod' => 'B8860B',
            'darkgray' => 'A9A9A9',
            'darkgreen' => '006400',
            'darkkhaki' => 'BDB76B',
            'darkmagenta' => '8B008B',
            'darkolivegreen' => '556B2F',
            'darkorange' => 'FF8C00',
            'darkorchid' => '9932CC',
            'darkred' => '8B0000',
            'darksalmon' => 'E9967A',
            'darkseagreen' => '8FBC8B',
            'darkslateblue' => '483D8B',
            'darkslategray' => '2F4F4F',
            'darkturquoise' => '00CED1',
            'darkviolet' => '9400D3',
            'deeppink' => 'FF1493',
            'deepskyblue' => '00BFFF',
            'dimgray' => '696969',
            'dodgerblue' => '1E90FF',
            'firebrick' => 'B22222',
            'floralwhite' => 'FFFAF0',
            'forestgreen' => '228B22',
            'fuchsia' => 'FF00FF',
            'gainsboro' => 'DCDCDC',
            'ghostwhite' => 'F8F8FF',
            'gold' => 'FFD700',
            'goldenrod' => 'DAA520',
            'gray' => '808080',
            'green' => '008000',
            'greenyellow' => 'ADFF2F',
            'honeydew' => 'F0FFF0',
            'hotpink' => 'FF69B4',
            'indianred' => 'CD5C5C',
            'indigo' => '4B0082',
            'ivory' => 'FFFFF0',
            'khaki' => 'F0E68C',
            'lavender' => 'E6E6FA',
            'lavenderblush' => 'FFF0F5',
            'lawngreen' => '7CFC00',
            'lemonchiffon' => 'FFFACD',
            'lightblue' => 'ADD8E6',
            'lightcoral' => 'F08080',
            'lightcyan' => 'E0FFFF',
            'lightgoldenrodyellow' => 'FAFAD2',
            'lightgray' => 'D3D3D3',
            'lightgreen' => '90EE90',
            'lightpink' => 'FFB6C1',
            'lightsalmon' => 'FFA07A',
            'lightseagreen' => '20B2AA',
            'lightskyblue' => '87CEFA',
            'lightslategray' => '778899',
            'lightsteelblue' => 'B0C4DE',
            'lightyellow' => 'FFFFE0',
            'lime' => '00FF00',
            'limegreen' => '32CD32',
            'linen' => 'FAF0E6',
            'magenta' => 'FF00FF',
            'maroon' => '800000',
            'mediumaquamarine' => '66CDAA',
            'mediumblue' => '0000CD',
            'mediumorchid' => 'BA55D3',
            'mediumpurple' => '9370DB',
            'mediumseagreen' => '3CB371',
            'mediumslateblue' => '7B68EE',
            'mediumspringgreen' => '00FA9A',
            'mediumturquoise' => '48D1CC',
            'mediumvioletred' => 'C71585',
            'midnightblue' => '191970',
            'mintcream' => 'F5FFFA',
            'mistyrose' => 'FFE4E1',
            'moccasin' => 'FFE4B5',
            'navajowhite' => 'FFDEAD',
            'navy' => '000080',
            'oldlace' => 'FDF5E6',
            'olive' => '808000',
            'olivedrab' => '6B8E23',
            'orange' => 'FFA500',
            'orangered' => 'FF4500',
            'orchid' => 'DA70D6',
            'palegoldenrod' => 'EEE8AA',
            'palegreen' => '98FB98',
            'paleturquoise' => 'AFEEEE',
            'palevioletred' => 'DB7093',
            'papayawhip' => 'FFEFD5',
            'peachpuff' => 'FFDAB9',
            'peru' => 'CD853F',
            'pink' => 'FFC0CB',
            'plum' => 'DDA0DD',
            'powderblue' => 'B0E0E6',
            'purple' => '800080',
            'red' => 'FF0000',
            'rosybrown' => 'BC8F8F',
            'royalblue' => '4169E1',
            'saddlebrown' => '8B4513',
            'salmon' => 'FA8072',
            'sandybrown' => 'F4A460',
            'seagreen' => '2E8B57',
            'seashell' => 'FFF5EE',
            'sienna' => 'A0522D',
            'silver' => 'C0C0C0',
            'skyblue' => '87CEEB',
            'slateblue' => '6A5ACD',
            'slategray' => '708090',
            'snow' => 'FFFAFA',
            'springgreen' => '00FF7F',
            'steelblue' => '4682B4',
            'tan' => 'D2B48C',
            'teal' => '008080',
            'thistle' => 'D8BFD8',
            'tomato' => 'FF6347',
            'turquoise' => '40E0D0',
            'violet' => 'EE82EE',
            'wheat' => 'F5DEB3',
            'white' => 'FFFFFF',
            'whitesmoke' => 'F5F5F5',
            'yellow' => 'FFFF00',
            'yellowgreen' => '9ACD32',
            );
        if (isset($colors[strtolower($color)])) {
            return '#' . strtolower($colors[$color]);
        }
        return $color;
    }
}

if (!isset($skip_init)) {
    /* Melde alle Fehler außer E_NOTICE */
    error_reporting(E_ALL ^ E_NOTICE);

    $action = new default_css();
    $action = null;
}

?>