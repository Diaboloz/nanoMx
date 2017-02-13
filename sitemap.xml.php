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
 *
 * Format der Ausgabe: http://www.sitemaps.org/
 */

/* System */
include_once('mainfile.php');

/**
 * pmxSitemap
 *
 * @package
 * @author tora60
 * @copyright Copyright (c) 2011
 * @version $Id: sitemap.xml.php 6 2015-07-08 07:07:06Z PragmaMx $
 * @access public
 */
class pmxSitemap {
    protected static $_config = array();
    protected static $_points1 = array();
    protected static $_points2 = array();

    /**
     * pmxSitemap::__construct()
     */
    public function __construct()
    {
        /*
         * eventuelle Ausgaben zwischenspeichern, um sie am Ende, evtl.
         * wieder als html-Kommentar anzufügen
         */
        ob_start();

        $this->_importconfig();

        /* Fehlerausgaben unterdrücken */
        if (pmxDebug::is_mode('enhanced')) {
            pmxDebug::level(pmxDebug::level_logfile);
        }
    }

    /**
     * pmxSitemap::create()
     *
     * @return
     */
    public function create()
    {
        if (!$this->sitemap) {
            die('this sitemap is not active...');
        }

        /* XML Header senden */
        header("Content-type: text/xml; charset=UTF-8");

        /* Cache initialisieren */
        $cacheid = __METHOD__;
        $cache = load_class('Cache');
        $cachetime = intval($this->sitemapcache);

        /* Ist was im Cache? */
        if (!($cachetime) || ($out = $cache->read($cacheid)) === false) {
            // Nö, nix drin...
            global $prefix;

            $result = array();

            /* alle Module */
            $modules = sql_query("SELECT title, custom_title FROM {$prefix}_modules WHERE active=1 AND view=0");
            while (list($modulename, $custom_title) = sql_fetch_row($modules)) {
                if (!in_array($modulename, $this->sitemapexmod)) {
                    $result[] = array(/* Moduldaten */
                        'loc' => "modules.php?name=" . $modulename,
                        'lastmod' => intval(MX_TIME),
                        'text' => $modulename . ' ' . $custom_title,
                        );
                }
            }

            $hook = load_class('Hook', 'seo_sitemap');
            $hook->type = 'items';
            $hook->limit = $this->sitemaplimit;
            $result2 = $hook->get();
            if ($result2 && is_array($result2)) {
                $result = array_merge($result, $result2);
            }
            unset($result2, $hook, $modules);

            /*
            <changefreq>
                always
                hourly
                daily
                weekly
                monthly
                yearly
                never
             */

            $rewrite = $this->_modrewrite();
            if ($rewrite) {
                load_class('Modrewrite', false);
            }

            /* Ausgabe zunächst in einem Array zwischenspeichern */
            $items = array();
            foreach($result as $i => $item) {
                if ($rewrite) {
                    $url = pmxModrewrite::prepare_url($item['loc'], PMX_HOME_URL);
                } else {
                    $url = PMX_HOME_URL . '/' . htmlspecialchars($item['loc']);
                }

                $priority = $this->_priority($item['lastmod'], $item['text']);

                /* der Schlüssel als Dezimalstring für spätere Sortierung */
                $items[($priority * 10) . '.' . (10000 - $i)] = array(/* Werte behandeln */
                    'loc' => $url,
                    'lastmod' => strftime('%Y-%m-%d', $item['lastmod']),
                    'priority' => sprintf('%.2f', $priority),
                    'changefreq' => (isset($item['changefreq'])) ? $item['changefreq'] : '',
                    );
            }

            /* das Array, nach der Wertigkeit (Schlüsselwerte abwärts sortieren */
            krsort($items);

            /* eventuelle Script oder Fehlerausgaben verwerfen */
            for ($i = 1; ($i <= 10 && ob_get_contents()); $i++) {
                ob_end_clean();
            }

            /* Ausgabe fertigstellen mit Kopf und Fuß */
            $template = load_class('Template');
            $template->init_path(__FILE__);
            $template->items = $items;

            $out = trim($template->fetch('sitemap.xml'));

            /* Cache schreiben */
            if ($cachetime) {
                $cache->write($out, $cacheid, ($cachetime * 3600)); // 5 Stunden Cachezeit
            }
        }
        return $out;
    }

    /**
     * pmxSitemap::_priority()
     *
     * @param mixed $date
     * @param mixed $text
     * @return
     */
    protected function _priority($date, $text)
    {
        $prio = ($this->_points1($text) + $this->_points2($date)) / 300;
        // ceil
        return $prio;
    }

    /**
     * pmxSitemap::_points1()
     *
     * @param mixed $text
     * @return
     */
    protected function _points1($text)
    {
        $points = 0;

        $text = html_entity_decode(strip_tags($text));
        $points = preg_match_all('#(?:' . $this->preg_keywords . ')#iu', $text, $matches);

        if ($points > 20) {
            $points = 20;
        }
        $points *= 10;
        self::$_points1[] = $points;

        return $points;
    }

    /**
     * pmxSitemap::_points2()
     *
     * @param mixed $date
     * @return
     */
    protected function _points2($date)
    {
        if (!$date || $date > time()) {
            return 0;
        }

        $daysold = intval((time() - $date) / 60 / 60 / 24); # // 50 muss wieder weg...
        if ($daysold > 100) {
            $daysold = 100;
        }
        $daysold = 100 - $daysold;

        self::$_points2[] = $daysold;

        return $daysold;
    }

    /**
     * pmxSitemap::_modrewrite()
     * ist mod_rewrite aktiviert?
     *
     * @return
     */
    protected function _modrewrite()
    {
        switch (true) {
            case empty($GLOBALS['mxUseModrewrite']['anony']):
            case !is_file('.htaccess'):
                return false;
            default:
                return true;
        }
    }

    /**
     * pmxSitemap::_importconfig()
     * Konfiguration einlesen
     *
     * @return
     */
    protected function _importconfig()
    {
        $seo = load_class('Config', 'pmx.seo');
        $config = $seo->get();
        $defaults = $seo->get_defaults();
        self::$_config = array_merge($defaults, $config);

        $keywords = array_merge(self::$_config['metakeywords'], self::$_config['sitemapkeywords']);
        $keywords = array_unique($keywords);
        $keywords = array_map('html_entity_decode', $keywords);
        $keywords = array_map('preg_quote', $keywords);
        self::$_config['preg_keywords'] = implode('|', $keywords);
    }

    /**
     * pmxSitemap::__get()
     *
     * @param mixed $name
     * @return
     */
    public function __get($name)
    {
        if (array_key_exists($name, self::$_config)) {
            return self::$_config[$name];
        }
        $trace = debug_backtrace();
        trigger_error('undefined property \'' . $name . '\' in ' . mx_strip_sysdirs($trace[0]['file']) . ' line ' . $trace[0]['line'], E_USER_NOTICE);
        return null;
    }

    /**
     * pmxSitemap::__set()
     *
     * @param mixed $name
     * @param mixed $value
     * @return
     */
    public function __set($name, $value)
    {
        self::$_config[$name] = $value;
    }
}

$action = new pmxSitemap();
echo $action->create();
$action = null;

?>