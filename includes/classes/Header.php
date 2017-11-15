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
 * $Revision: 344 $
 * $Author: PragmaMx $
 * $Date: 2017-06-07 16:26:06 +0200 (Mi, 07. Jun 2017) $
 */

/**
 * pmxHeader
 *
 * @package pragmaMx
 * @author tora60
 * @copyright Copyright (c) 2008
 * @version $Id: Header.php 344 2017-06-07 14:26:06Z PragmaMx $
 * @access public
 */
class pmxHeader {
    private static $__adds = array();

    private static $__urls = array();

    private static $__meta = array(/* Meta Informationen */
        'title' => "",
        'description' => "",
        'robots' => "index,follow",
        'canonical' => "",
        'revisit' => "8",
        'copyright' => "",
        'resource-type' => "document",
        'distribution' => "global",
        'rating' => "general",
        'author' => "",
        'alternate' => "",
		'viewport' =>"",
        );

    private static $__keywords = array();

    private static $__jquery = array(/* jQuery Details */
        'required' => false,
        'loaded' => false,
        'main' => 'jquery.js',
        'path' => 'includes/javascript/jquery/',
        'files' => array(),
        'ui_required' => false,
        'ui_loaded' => false,
        'ui_theme' => 'base',
        'ui_theme_path' => 'layout/jquery/ui/',
        );
		
	private static $__status = NULL ;
	private static $__statustext = "" ;

    /**
     * pmxHeader::__construct()
     * Ein private Konstruktor; verhindert die direkte Erzeugung des Objektes
     */
    private function __construct()
    {
    }

    /**
     * Returns content of the output
     *
     * @returns array
     * @static
     */
    public static function get()
    {
        $buf = pmxHeader::fetch();
        $buf = trim(implode("\n", $buf));
        /* unnötige spaces entfernen */
        $buf = trim(preg_replace('#\s*\n\s+#', "\n", $buf));
        return $buf;
    }

    /**
     * Outputs content of the buffer and delete the buffer
     *
     * @returns string
     * @static
     */
    public static function show()
    {
        // Puffer ausgeben
        echo '<!-- start~header~' . MX_TIME . '~pmx -->', self::get(), '<!-- end~header~' . MX_TIME . '~pmx -->';
    }

    /**
     * pmxHeader::move()
     *
     * @param mixed $showed
     * @return
     */
    public static function move($showed)
    {
        $pattern = '#<!-- start~header~' . MX_TIME . '~pmx -->.*?<!-- end~header~' . MX_TIME . '~pmx -->#s';
        if (preg_match($pattern, $showed, $matches)) {
            $showed = str_replace($matches[0], self::get(), $showed);
        }
        return $showed;
    }

    /**
     * pmxHeader::fetch()
     * Einstellungen prüfen und komplettieren
     *
     * @param mixed $defaults
     * @return
     */
    public static function fetch($defaults = false)
    {
        $more = self::get_more(); // als erstes abfragen, weil in my_header noch Zuweisungen stehen könnten
        $export['style_code'] = self::get_style_code();
        $export['style'] = "\n" . '<link rel="stylesheet" type="text/css" href="' . MX_THEME_DIR . '/style/style.css" />' . "\n";
        $export['style'] .= self::get_style();
        $export['jquery'] = self::get_jquery();
        $export['script_code'] = self::get_script_code();
        $export['script'] = self::get_script();

        $export['more'] = $more;
        return $export;
    }

    /**
     * pmxHeader::add()
     * Adds code to output buffer
     *
     * @param string $code code for output
     * @param bool $extract
     * @return
     */
    public static function add($code, $extract = false)
    {
        if (is_array($code)) {
            foreach ($code as $item) {
                if ($extract) {
                    $item = self::_extract($item);
                }
                self::$__adds['more'][] = $item;
            }
        } else {
            if ($extract) {
                $code = self::_extract($code);
            }
            self::$__adds['more'][] = $code;
        }
    }

    /**
     * pmxHeader::add_style()
     *
     * @param mixed $src
     * @param string $media
     * @param string $if_for_ie
     * @return
     */
    public static function add_style($src, $media = '', $if_for_ie = '')
    {
        switch (true) {
            case !$src:
            case $if_for_ie && !self::_is_ie():
                return false;
            case $media:
                $media = ' media="' . $media . '"';
        }

        /* Systempfade am Anfang entfernen */
        $src = self::_cleanpath($src);

        /* verhindern, dass url doppelt eingefügt wird */
        if (in_array($src, self::$__urls)) {
            return true;
        }
        self::$__urls[] = $src;

        $src = '<link rel="stylesheet" type="text/css" href="' . trim($src) . '"' . $media . ' />';
        if ($if_for_ie) {
            $src = "<!--[if " . $if_for_ie . "]>\n" . $src . "\n<![endif]-->";
        }

        self::$__adds['style'][] = $src;
    }

    /**
     * pmxHeader::add_style_code()
     *
     * @param mixed $code
     * @return
     */
    public static function add_style_code($code)
    {
        self::$__adds['style_code'][] = preg_replace(array('#</?style[^>]*>#i', '#[[:cntrl:]]#', '#\s+#'), array('', '', ' '), $code);
    }

    /**
     * pmxHeader::add_script()
     *
     * @param mixed $src
     * @param string $if_for_ie
     * @return
     */
    public static function add_script($src, $if_for_ie = '')
    {
        switch (true) {
            case !$src:
            case $if_for_ie && !self::_is_ie():
                return false;
        }

        /* Systempfade am Anfang entfernen */
        $src = trim(self::_cleanpath($src));

        /* verhindern, dass url doppelt eingefügt wird */
        if (in_array($src, self::$__urls)) {
            return true;
        }

        $src = self::_getminfilename($src); // min Version vorhanden?

        self::$__urls[] = $src;

        if (strrpos($src, '/' . self::$__jquery['main']) !== false && !$if_for_ie) {
            self::add_jquery();
            return;
        }

        $src = '<script type="text/javascript" src="' . $src . '"></script>';
        if ($if_for_ie) {
            $src = "<!--[if " . $if_for_ie . "]>\n" . $src . "\n<![endif]-->";
        }

        self::$__adds['script'][] = $src;
    }

    /**
     * pmxHeader::add_script_code()
     *
     * @param mixed $code
     * @return
     */
    public static function add_script_code($code)
    {
        self::$__adds['script_code'][] = preg_replace('#</?script[^>]*>#i', '', $code);
    }

	/**
     * pmxHeader::add_script_body()
     *
     * @param mixed $code
     * @return
     */
    public static function add_body_script_code($code)
    {
        self::$__adds['body_code'][] = preg_replace('#</?script[^>]*>#i', '', $code);
    }
	
    /**
     * pmxHeader::add_script()
     *
     * @param mixed $src
     * @param string $if_for_ie
     * @return
     */
    public static function add_body_script($src, $if_for_ie = '')
    {
        switch (true) {
            case !$src:
            case $if_for_ie && !self::_is_ie():
                return false;
        }

        /* Systempfade am Anfang entfernen */
        $src = trim(self::_cleanpath($src));

        /* verhindern, dass url doppelt eingefügt wird */
        if (in_array($src, self::$__urls)) {
            return true;
        }

        $src = self::_getminfilename($src); // min Version vorhanden?

        self::$__urls[] = $src;

        if (strrpos($src, '/' . self::$__jquery['main']) !== false && !$if_for_ie) {
            self::add_jquery();
            return;
        }

        $src = '<script type="text/javascript" src="' . $src . '"></script>';
        if ($if_for_ie) {
            $src = "<!--[if " . $if_for_ie . "]>\n" . $src . "\n<![endif]-->";
        }

        self::$__adds['body'][] = $src;
    }
    /**
     * pmxHeader::add_jquery()
     *
     * @return
     */
    public static function add_jquery()
    {
        self::$__jquery['required'] = true;

        $args = func_get_args();

        if ($args) {
            foreach ($args as $src) {
                /* ermitteln ob jQuery-UI geladen werden muss wird */
                if (strpos(basename($src), 'jquery.ui.') === 0) {
                    self::$__jquery['ui_required'] = true;
                }

                $src = self::$__jquery['path'] . $src;

                $src = self::_getminfilename($src); // min Version vorhanden?
                /* verhindern, dass url doppelt eingefuegt wird */
                if (in_array($src, self::$__urls)) {
                    continue;
                }
                // TODO: hier noch check ob MIN Version vorhanden ??? hmmmmm...
                self::$__urls[] = $src;
                self::$__jquery['files'][] = $src;
            }
        }

        if (self::$__jquery['ui_required'] && !self::$__jquery['ui_loaded']) {
            // TODO: flexibler machen, so dass es über das Theme gesteuert werden könnte **********************
            $path = trim(self::$__jquery['ui_theme_path'], ' /') . '/' . pmxBase::juitheme();
            if (file_exists($path)) {
                self::$__jquery['ui_theme'] = pmxBase::juitheme();
            } else {
                // Fallback für falsch eingestelltes UI-Theme
                self::$__jquery['ui_theme'] = 'default';
                $path = trim(self::$__jquery['ui_theme_path'], ' /') . '/' . self::$__jquery['ui_theme'];
            }

            /* Stylesheet für jQuery-UI anfügen */
            $src = self::_getminfilename($path . '/jquery-ui.css', 'css'); // min Version vorhanden?
            self::add_style($src);

            /* Core-Datei für jQuery-UI am Anfang anfügen (immer min Version)*/
            $src = self::$__jquery['path'] . 'ui/jquery-ui-pmx-core.min.js';
            array_unshift(self::$__jquery['files'], $src);

            /* verhindern, dass url nicht doppelt eingefügt werden kann */
            self::$__urls[] = $src;
            self::$__jquery['ui_loaded'] = true;

            /* sicherstellen, dass jQuery Core-Datei ganz an den Anfang gestellt wird */
            self::$__jquery['loaded'] = false;
        }

        /* Core-Datei für jQuery am Anfang einfügen */
        if (!self::$__jquery['loaded']) {
            self::$__jquery['loaded'] = true;

            $src = self::$__jquery['path'] . self::$__jquery['main'];
            $src = self::_getminfilename($src); // min Version vorhanden?
            array_unshift(self::$__jquery['files'], $src);

            /* verhindern, dass url nicht doppelt eingefügt werden kann */
            self::$__urls[] = $src;
        }
    }

    /**
     * pmxHeader::add_jquery_ui()
     *
     * @param string $theme
     * @return nothing
     */	
	
	public static function add_jquery_ui($theme="base") {
		pmxBase::set_system("juitheme",preg_replace ( '/[^a-z0-9\-]/i', '', $theme));  // alles unnötige aus dem Namen entfernen
		
	}
    /**
     * pmxHeader::add_lightbox()
     *
     * @param mixed $options
     * @return nothing
     */
    public static function add_lightbox($options = false)
    {
        switch (true) {
            case is_array($options):
            case is_object($options):
                $options = '?' . http_build_query($options, 'x', '&amp;');
                break;
            case $options:
                // nur reset
                $options = '?t=' . intval(MX_TIME);
                break;
            default:
                $options = '';
        }

        self::add_style(PMX_LAYOUT_PATH . 'jquery/css/prettyPhoto.css');
        self::add_jquery('jquery.prettyPhoto.js', 'lightbox.js.php' . $options);
    }

    /**
     * pmxHeader::add_prettyphoto()
     * clone von add_lightbox()
     *
     * @param mixed $options
     * @return nothing
     */
    public static function add_prettyphoto($options = false)
    {
        self::add_lightbox($options);
    }

    /**
     * pmxHeader::add_tabs()
     *
     * @param mixed $cookie
     * @return nothing
     */
    public static function add_tabs($cookie = true)
    {
        if ($cookie) {
            self::add_jquery('jquery.cookie.js');
        }
        self::add_jquery('ui/jquery.ui.tabs.noui.js');
    }

    /**
     * pmxHeader::get_style()
     *
     * @return
     */
    public static function get_style()
    {
        if (array_key_exists('style', self::$__adds)) {
            return implode("\n", array_unique(self::$__adds['style'])) . "\n";
        }
    }

    /**
     * pmxHeader::get_style_code()
     *
     * @return
     */
    public static function get_style_code()
    {
        if (array_key_exists('style_code', self::$__adds)) {
            return self::_preparelines(self::$__adds['style_code'], 'style') . "\n";
        }
    }

    /**
     * pmxHeader::get_script()
     *
     * @return
     */
    public static function get_script()
    {
        if (array_key_exists('script', self::$__adds)) {
            return implode("\n", array_unique(self::$__adds['script'])) . "\n";
        }
    }

    /**
     * pmxHeader::get_script_code()
     *
     * @return
     */
    public static function get_script_code()
    {
        if (array_key_exists('script_code', self::$__adds)) {
            return self::_preparelines(self::$__adds['script_code'], 'script') . "\n";
        }
    }

    /**
     * pmxHeader::get_jquery()
     *
     * @return
     */
    public static function get_jquery()
    {
        if (self::$__jquery['required']) {
            self::$__jquery['files'] = array_unique(self::$__jquery['files']);
            foreach (self::$__jquery['files'] as $addon) {
                $srcs[] = '<script type="text/javascript" src="' . $addon . '"></script>';
            }
            return implode("\n", $srcs) . "\n";
        }
        return;
    }

    /**
     * pmxHeader::get_more()
     *
     * @return
     */
    public static function get_more()
    {
        /* die Sammlung zurückgeben... */
        if (array_key_exists('more', self::$__adds)) {
            $more = array_unique(self::$__adds['more']);
            foreach ($more as $key => $value) {
                $more[$key] = preg_replace('#\s*\n\s+#', "\n", $value);
            }
            return implode("\n", $more) . "\n";
        }
    }
   /**
     * pmxHeader::get_body()
     * setzt zusätzliche JScrips ans ende des HTML-Body's
     * @return
     */
    public static function get_body($body_content)
    {
		$body_js="\n";
        /* die Sammlung zurückgeben... */

        if (array_key_exists('body', self::$__adds)) {
            $body_js .= implode("\n", array_unique(self::$__adds['body'])) . "\n";
        }
		
        if (array_key_exists('body_code', self::$__adds)) {
            $body = array_unique(self::$__adds['body_code']);
			$body_js .= self::_preparelines(self::$__adds['body_code'], 'script') . "\n";
            /* foreach ($body as $key => $value) {
                $body[$key] = preg_replace('#\s*\n\s+#', "\n", $value);
            }
            $body_js = implode("\n", $body) . "\n"; */
        }
		return $body_content . "\n".$body_js;
    }
    /**
     * pmxHeader::get_user()
     * nur noch zur Abwärtskompatibilität
     * my_header.php gibt es nicht mehr, wurde ersetzt durch hook 'prepare.header'
     *
     * @return
     */
    public static function get_user()
    {
        return '';
    }

    /**
     * pmxHeader::get_meta()
     * gibt die Meta-Informationen zurück
     *
     * @return
     */
    public static function get_meta($tag, $default = '')
    {
        /* SEO Konfiguration laden */
        $seo = load_class('Config', 'pmx.seo');

        $temp = "";
        switch ($tag) {
            case "keywords":
                $limit = 25;
                $keywords = $seo->metakeywords;
                $keywords = array_merge(self::$__keywords, $keywords);
                $keywords[] = PMX_VERSION;
                $keywords = array_chunk(array_unique($keywords), $limit);
                $temp = self::_escape(implode(', ', $keywords[0]));
                break;

            case "title":
                global $pagetitle;
                $temp = $GLOBALS['sitename'];
                switch (true) {
                    case defined('MX_HOME_FILE'):
                        /* wenn Startseite, nur den Seitenname als Seitentitel anzeigen. */
                        break;
                    case trim(self::$__meta['title']):
                        $temp .= ' - ' . self::_escape(self::$__meta['title']);
                        $pagetitle = self::_escape(self::$__meta['title']); // wird in manchen Modulen weiter verwendet
                        break;
                    case trim($pagetitle):
                        $temp .= ' - ' . $pagetitle;
                        break;
                }

                /* sicherstellen, dass der Seitentitel keine Tags enthält und Sonderzeichen nicht zerstückelt werden */
                $temp = trim(str_replace('&nbsp;', ' ', self::_escape($temp)), '- ');

                break;

            case "author":
                if (!trim(self::$__meta['author'])) {
                    self::$__meta['author'] = $GLOBALS['sitename'];
                }
                $temp = self::_escape(self::$__meta['author']);
                break;

            case "rating":
                $oks = array('general', 'mature', 'restricted', '14 years', 'safe for kids');
                $rate = trim(self::$__meta['rating']);
                if (!($rate && in_array($rate, $oks))) {
                    self::$__meta['rating'] = "general";
                }
                $temp = self::$__meta['rating'];
                break;

            case "description":
                if (!trim(self::$__meta['description'])) {
                    self::$__meta['description'] = $GLOBALS['slogan'];
                }
                $temp = self::_escape(self::$__meta['description']);
                break;

            case "robots":
                $temp = trim(strtolower(self::$__meta['robots']));
                if (!($temp && preg_match('#^(noindex|index|nofollow|follow)\s*,\s*(noindex|index|nofollow|follow)$#', $temp))) {
                    $temp = "index, follow";
                }
                break;

            case "canonical":
                if (trim(self::$__meta['canonical'])) {
                    $temp = self::_escape(self::$__meta['canonical']);
                }
                break;

            case "revisit":
                if (intval(self::$__meta['revisit']) == 0) {
                    self::$__meta['revisit'] = 10;
                }
                $temp = intval(self::$__meta['revisit']) . " days";
                break;

            case "copyright":
                if (!trim(self::$__meta['copyright'])) {
                    self::$__meta['copyright'] = date('Y') . ' by ' . $GLOBALS['sitename'];
                }
                $temp = self::_escape(self::$__meta['copyright']);
                break;

            case "alternate":
                // self::$__meta['alternate'] = 'meta name="revisit-after" content="1 month"';
                $temp = trim(stripslashes(strip_tags(self::$__meta['alternate'])));
                if ($temp) {
                    $temp = preg_split('#\s*[,]\s*#', $temp);
                    $temp = "<" . implode(" />\n<", $temp) . " />\n";
                }

                break;
            case "viewport":
                if (trim(self::$__meta['viewport'])) {
                    $temp = trim(self::$__meta['viewport']);
                }
                break;
            default:
                if (array_key_exists($tag, self::$__meta)) {
                    $temp = self::$__meta[$tag];
                }
                break;
        }

        return $temp;
    }

    /**
     * pmxHeader::set_meta()
     * set meta description
     *
     * @param mixed $description
     * @return
     */
    public static function set_meta($tag, $content)
    {
        self::$__meta[$tag] = strip_tags($content);

        return;
    }
	
	/**
     * pmxHeader::status()
     * set meta description
     *
     * @param mixed $heder response code
     * @return
     */
    public static function Status($code=NULL)
    {
            if ($code === NULL) {
			
				if (self::$__status==NULL) self::$__status=(isset($GLOBALS['http_response_code'])) ? $GLOBALS['http_response_code'] :http_response_code();


            } else { 
				http_response_code(intval($code));
				self::$__status=intval($code);
			}
			
            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
			
			switch (self::$__status) {
				case 100: $text = 'Continue'; break;
				case 101: $text = 'Switching Protocols'; break;
				case 200: $text = 'OK'; break;
				case 201: $text = 'Created'; break;
				case 202: $text = 'Accepted'; break;
				case 203: $text = 'Non-Authoritative Information'; break;
				case 204: $text = 'No Content'; break;
				case 205: $text = 'Reset Content'; break;
				case 206: $text = 'Partial Content'; break;
				case 300: $text = 'Multiple Choices'; break;
				case 301: $text = 'Moved Permanently'; break;
				case 302: $text = 'Moved Temporarily'; break;
				case 303: $text = 'See Other'; break;
				case 304: $text = 'Not Modified'; break;
				case 305: $text = 'Use Proxy'; break;
				case 400: $text = 'Bad Request'; break;
				case 401: $text = 'Unauthorized'; break;
				case 402: $text = 'Payment Required'; break;
				case 403: $text = 'Forbidden'; break;
				case 404: $text = 'Not Found'; break;
				case 405: $text = 'Method Not Allowed'; break;
				case 406: $text = 'Not Acceptable'; break;
				case 407: $text = 'Proxy Authentication Required'; break;
				case 408: $text = 'Request Time-out'; break;
				case 409: $text = 'Conflict'; break;
				case 410: $text = 'Gone'; break;
				case 411: $text = 'Length Required'; break;
				case 412: $text = 'Precondition Failed'; break;
				case 413: $text = 'Request Entity Too Large'; break;
				case 414: $text = 'Request-URI Too Large'; break;
				case 415: $text = 'Unsupported Media Type'; break;
				case 423: $text = 'Locked'; break;
				case 500: $text = 'Internal Server Error'; break;
				case 501: $text = 'Not Implemented'; break;
				case 502: $text = 'Bad Gateway'; break;
				case 503: $text = 'Service Unavailable'; break;
				case 504: $text = 'Gateway Time-out'; break;
				case 505: $text = 'HTTP Version not supported'; break;
				default:
					exit('Unknown http status code "' . htmlentities($code) . '"');
				break;
			}
			
			http_response_code(self::$__status);
			$code = $protocol . ' ' . self::$__status . ' ' . $text;
			self::$__statustext=$text;
			$GLOBALS['http_response_code'] = self::$__status;

            return $code;        
    }

    /**
     * pmxHeader::set_keywords()
     * set and replace existing keywords
     *
     * @param mixed $keywords
     * @return
     */
    public static function set_keywords($keywords)
    {
        if (!is_array($keywords)) {
            $keywords = preg_split('#\s*[,;]\s*#', $keywords);
        }
        self::$__keywords = $keywords;
    }

    /**
     * pmxHeader::add_keywords()
     * add keywords to existing keywords
     *
     * @param mixed $keywords
     * @return
     */
    public static function add_keywords($keywords)
    {
        if (!is_array($keywords)) {
            $keywords = preg_split('#\s*[,;]\s*#', $keywords);
        }

        self::$__keywords = array_merge(self::$__keywords, $keywords);
    }

    /**
     * pmxHeader::set_title()
     * set meta pagetitle
     *
     * @param mixed $pagetitle
     * @return
     */
    public static function set_title($pagetitle)
    {
        if ($pagetitle) {
            /* Titel nur setzen, wenn auch wirklich angegeben, nicht dass fehlerhafte Module da Mist bauen */
            self::set_meta('title', $pagetitle, true);
        }
    }

    /**
     * pmxHeader::set_description()
     * set meta description
     *
     * @deprecated Funktion wird seit pragmaMx 2.0 nicht mehr verwendet.
     * @param mixed $description
     * @return
     */
    public static function set_description($description)
    {
        trigger_error('Use of deprecated method pmxHeader::set_description().', E_USER_NOTICE);

        self::set_meta('description', $description, true);
    }

    /**
     * pmxHeader::_preparelines()
     *
     * @param mixed $lines
     * @param mixed $type
     * @return
     */
    private static function _preparelines($lines, $type)
    {
        $lines = trim(implode("\n", array_unique($lines)));
        if (!$lines) {
            return '';
        }

        /* Kommentare entfernen */
        $lines = str_replace(array('<![CDATA[', ']]>'), '', $lines);
        $lines = preg_replace(array('#/\*[^\*]*\*/#sU', '#\<\!--.+--\>#siU', '#\n\s*//[^\n]*#'), '', $lines);

        if ($type === 'script') {
            $out = '<script type="text/javascript">';
        } else {
            $out = '<style type="text/css">';
        }

        $out .= "\n /* <![CDATA[ */\n" . $lines . "\n /* ]]> */\n";

        if ($type === 'script') {
            $out .= '</script>';
        } else {
            $out .= '</style>';
        }
        return $out;
    }

    /**
     * pmxHeader::_cleanpath()
     * schneidet Sytempfade am Anfang eines Pfades oder URL ab
     *
     * @param mixed $path
     * @return
     */
    private static function _cleanpath($path)
    {
        $path = mx_strip_sysdirs($path);
        return str_replace(DS, '/', $path);
    }

    /**
     * pmxHeader::_extract()
     * filtert bestimmte Bestandteile aus gemischten Headerdaten
     *
     * @param mixed $code
     * @return
     */
    private static function _extract($code)
    {
        /* conditional comments ignorieren <!--[if lte IE 6]><![endif]--> */
        preg_match_all('#<\!--\s*\[if[^\]]+\]>.+<\!\[endif\]\s*-->#siU', $code, $matches);
        $condition = array();
        if ($matches) {
            if (!self::_is_ie()) {
                $code = str_replace($matches[0], '', $code);
            } else {
                foreach ($matches[0] as $key => $src) {
                    $condition['~!~' . $key . '~!~'] = $src;
                }
            }
            if ($condition) {
                $code = str_replace(array_values($condition), array_keys($condition), $code);
            }
        }

        /* Scriptdateien rausfiltern und an Scriptarray anfügen */
        preg_match_all('#<script[^>]+src\s*=\s*["\']([^"\']+)["\'][^>]*>\s*</script>#siU', $code, $matches);
        if ($matches) {
            $code = str_replace($matches[0], '', $code);
            foreach ($matches[1] as $src) {
                self::add_script($src);
            }
        }

        /* Script- und Style-Codedateien rausfiltern und an entspr. Array anfügen */
        preg_match_all('#<(script|style)[^>]*>(.+)</\1>#siU', $code, $matches);
        if ($matches) {
            $code = str_replace($matches[0], '', $code);
            foreach ($matches[2] as $key => $src) {
                if ($matches[1][$key] == 'script') {
                    self::add_script_code($src);
                } else {
                    self::add_style_code($src);
                }
            }
        }

        /* Stylesheets rausfiltern und an Sheetarray anfügen */
        preg_match_all('#<link[^>]+href\s*=\s*["\']([^"\']+)["\'][^>]*/?>#siU', $code, $matches);
        if ($matches) {
            foreach ($matches[1] as $key => $src) {
                if (strpos($matches[0][$key], 'stylesheet') !== false) {
                    self::add_style($src);
                    $code = str_replace($matches[0][$key], '', $code);
                }
            }
        }

        /* conditional comments wieder zurückschreiben */
        if ($condition && $code) {
            $code = str_replace(array_keys($condition), array_values($condition), $code);
        }

        if ($code) {
            /* falls xhtml-Endung fehlt, diese anfügen */
            $code = preg_replace('#<((?:img|input|hr|br|link|meta|base)(?:[^>]*[^/])?)>#i', '<$1 />', $code);
        }

        /* raus damit */
        return $code;
    }

    /**
     * pmxHeader::_is_ie()
     * guggen, was für ein Browser am werkeln ist
     *
     * @return bool
     */
    private static function _is_ie()
    {
        $browser = load_class('Browser');
        return $browser->is_msie();
    }

    /**
     * pmxHeader::_getminfilename()
     *
     * @param string $src
     * @param string $ending
     * @return string $src
     */
    private static function _getminfilename($src, $ending = 'js')
    {
        if (pmxDebug::is_mode('enhanced') && is_file($src)) {
            // im erweiterten Debugmodus die Originale laden
            return $src;
        }
        $tmp = explode('.', $src);

        /* Endung wie gefordert? */
        if (array_pop($tmp) != $ending) {
            return $src;
        }

        /* bereits minimierte Version? */
        $last = array_pop($tmp);
        if ($last == 'min') {
            return $src;
        }

        /* Array ergänzen + min */
        array_push($tmp, $last, 'min', $ending);

        /* Dateiname aus Array wieder herstellen */
        $tmp = implode('.', $tmp);
        /* wenn minimierte Version vorhanden, diese verwenden */
        if (is_file($tmp)) {
            $src = $tmp;
        }
        return $src;
    }

    /**
     * pmxHeader::_escape()
     *
     * @param mixed $text
     * @return
     */
    private static function _escape($text)
    {
        if ($text) {
            $text = htmlspecialchars(strip_tags($text), ENT_QUOTES, 'utf-8', false);
        }
        return $text;
    }

    /**
     * pmxHeader::__callStatic()
     * einfach nur dummy um fehlende methoden zu simulieren...
     *
     * @param string $name
     * @param array $arguments
     * @return bol false
     */
    // public static function __callStatic ($name, $arguments = array() ){
    // trigger_error('Call to undefined method ' .__CLASS__. '::' . $name . '()', E_USER_WARNING);
    // return false;
    // }
}

?>
