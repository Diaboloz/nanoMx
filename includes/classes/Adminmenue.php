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
 * $Revision: 23 $
 * $Author: PragmaMx $
 * $Date: 2015-07-13 18:57:06 +0200 (Mo, 13. Jul 2015) $
 */

defined('mxMainFileLoaded') or die('access denied');

/* Jeder Adminmenü-Kategorie einen Namen verpassen... */
define('MX_ADMINPANEL_SYSTEM', 1);
define('MX_ADMINPANEL_USERS', 2);
define('MX_ADMINPANEL_TOOLS', 3);
define('MX_ADMINPANEL_SECURITY', 4);
define('MX_ADMINPANEL_CONTENT', 5);
define('MX_ADMINPANEL_ADDON', 6);
/**
 * adminmenu()
 * Hilfsfunktion zum generieren der Links im Adminmenue
 *
 * @param string $url
 * @param string $title
 * @param string $image
 * @param integer $panel
 * @param string $description
 * @return nothing
 */
function adminmenu($url, $title, $image, $panel = MX_ADMINPANEL_ADDON, $description = '')
{
    return pmxAdminmenue::add($url, $title, $image, $panel, $description);
}

/**
 * pmxAdminmenue
 * Klasse zum generieren der Links im Adminmenue
 *
 * @package pragmaMx
 * @author tora60
 * @copyright Copyright (c) 2008
 * @version $Id: Adminmenue.php 23 2015-07-13 16:57:06Z PragmaMx $
 * @access public
 */
class pmxAdminmenue {
    private static $__links = array();
    private static $__admindata = array();
    private static $_imagepath = 'images/admin/';

    private static $__defaults = array(/* Standardwerte der Menünkte */
        'url' => '',
        'title' => '',
        'image' => '',
        'panel' => 0,
        'module' => '',
        'target' => '',
        'expanded' => false,
        'description' => '',
        'current' => false,
        );

    public function __construct()
    {
        /* die Daten von $admindata werden in den .link-Dateien benoetigt */
        self::$__admindata = mxGetAdminData();
    }

    /**
     * pmxAdminmenue::add()
     * Setzt die Daten der alten Funktion adminmenue() in
     * das benötigte Format um
     *
     * @param mixed $url
     * @param mixed $title
     * @param mixed $image
     * @param mixed $panel
     * @return
     */
    public static function add($url, $title, $image, $panel = MX_ADMINPANEL_ADDON, $description = '')
    {
        pmxDebug::pause();
        $query = parse_url($url, PHP_URL_QUERY);
        $query = str_ireplace('&amp;', '&', $query);
        parse_str($query, $para);
        pmxDebug::restore();

        $module = (isset($para['op'])) ? $para['op'] : '';

        /* wenn kein Pfad (/) im Bildnamen enthalten ist, den adminbildchen ordner als Pfad verwenden */
        $image = (strpos($image, '/') !== false) ? $image : self::$_imagepath . $image;

        $values = compact('url', 'title', 'image', 'panel', 'module', 'description');

        /* $key dient zur alfabetischen Sortierung des Arrays */
        $key = self::_arrkey($title, $url);
        self::$__links[$panel][$key] = array_merge(self::$__defaults, $values);
    }

    /**
     * pmxAdminmenue::fetch()
     *
     * @return
     */
    public function fetch()
    {
        if (!MX_IS_ADMIN) {
            return array();
        }

        extract(self::$__admindata);

        $cacheid = __CLASS__ . $aid . $GLOBALS['currentlang'];
        $cache = load_class('Cache');
        if (($headings = $cache->read($cacheid)) !== false) {
            return $headings;
        }

        $headings = $this->_paneldata();

        /* alle .link Dateien einlesen */
        foreach ($this->_linkdata() as $key => $value) {
            ksort($value);
            if ($key && isset($headings[$key])) {
                $headings[$key]['links'] = $value;
            } else {
                // unbekannte panels zu addons zufügen
                $headings[MX_ADMINPANEL_ADDON]['links'] = array_merge($headings[MX_ADMINPANEL_ADDON]['links'], $value);
            }
        }

        $cache->write($headings, $cacheid);

        return $headings;
    }

    /**
     * pmxAdminmenue::get_current()
     * ermittelt die Menü-Daten des aktuellen Adminmoduls
     *
     * @return
     */
    public function get_current()
    {
        $curentmodule = $this->_get_current_module();
        $items = $this->fetch();

        foreach ($items as $tab => $tabitems) {
            if ($tabitems['links']) {
                foreach ($tabitems['links'] as $key => $item) {
                    if ($curentmodule === $item['module']) {
                        return $item;
                    }
                }
            }
        }

        /* falls Fehler, Standardarray zurückgeben */
        $uups = self::$__defaults;
        $uups['image'] = self::$_imagepath . 'unknown.png';
        $uups['url'] = adminUrl($curentmodule);
        return $uups;
    }

    /**
     * pmxAdminmenue::graphicadmin()
     *
     * @return
     */
    public function graphicadmin()
    {
        if (!MX_IS_ADMIN) {
            return '';
        }

        $items = $this->fetch();

        if (!$items) {
            return '';
        }

        $curentmodule = $this->_get_current_module();
        $currenttab = 0;
        $currentitem = array();
        $countitems = 0;
        $tabindex = 0;

        foreach ($items as $tab => $tabitems) {
            if ($tabitems['links']) {
                foreach ($tabitems['links'] as $key => $item) {
                    $countitems++;
                    if ($curentmodule == $item['module']) {
                        $items[$tab]['current'] = true;
                        $items[$tab]['links'][$key]['current'] = true;
                        $currenttab = $tabindex;
                        $currentitem = $item;
                    }
                }
                $tabindex++;
            }
        }

        if (!$currenttab) {
            $items[MX_ADMINPANEL_SYSTEM]['current'] = true;
        }

        if (!$currentitem) {
            $currentitem = self::$__defaults;
            $currentitem['image'] = self::$_imagepath . 'unknown.png';
            $currentitem['url'] = adminUrl($curentmodule);
        }

        /* ermitteln ob es ein normales Modul ist, oder ein Systemmodul */
        $module = (defined('PMX_MODULE') && is_file(PMX_MODULES_DIR . DS . PMX_MODULE . DS . 'index.php')) ? 'modules.php?name=' . PMX_MODULE : '';

        $template = load_class('Template');
        $template->init_path(__FILE__);

        $template->assign(compact('items', 'currenttab', 'currentitem', 'module'));

        $oldstyle = (defined('GRAPHICADMINOLDSTYLE') && GRAPHICADMINOLDSTYLE);

        switch (true) {
            case !$oldstyle:
                return $template->fetch('graphicadmin_menu.html');
            case $oldstyle && $countitems > 10:
                /* Admintabs im alten Stil */
                return $template->fetch('graphicadmin.html');
            case $oldstyle:
                /* Admintabs abschalten, z.B. wenn kein God-Admin, um zu verhindern dass unnötig leere Tabs angezeigt werden */
                return $template->fetch('graphicadmin_notabs.html');
        }
    }

    /**
     * pmxAdminmenue::_modulname()
     * Modulname ermitteln, fuer den Pfad des Bildes
     *
     * @param mixed $filename
     * @return
     */
    private static function _modulname($filename)
    {
        return basename(dirname(dirname($filename)));
    }

    /**
     * pmxAdminmenue::_get_current_module()
     * ermittelt den Namen des aktuellen Adminmodul
     *
     * @return
     */
    private function _get_current_module()
    {
        switch (true) {
            case defined('PMX_MODULE'):
                /* wenn Modulname eindeutig erkennbar, diesen verwenden */
                return PMX_MODULE;
            case defined('PMX_ADMIN_OP'):
                return PMX_ADMIN_OP;
            case !empty($_REQUEST['op']):
                return $_REQUEST['op'];
            default:
                return 'main';
        }
    }

    /**
     * pmxAdminmenue::_linkdata()
     *
     * @return
     */
    private function _linkdata()
    {
        global $prefix;

        /* die Daten von $admindata werden in den .link-Dateien benoetigt */
        extract(self::$__admindata);

        self::$__links = array();

        /* erst alte Module und Systemmodule abfragen */
        $files = glob(PMX_ADMIN_DIR . DS . 'links' . DS . 'links.*.php', GLOB_NOSORT);
        if (is_array($files)) {
            /* Dateien die zu ignorieren sind */
            $outdated = self::_outdated();

            foreach ($files as $filename) {
                /* Datei einlesen, hier wird jeweils die funktion adminmenu() ausgefuehrt */
                if ($filename && !in_array(basename($filename), $outdated)) {
                    include_once($filename);
                }
            }
        }

        /* jetzt neue Module auslesen, eventuell doppelte Einträge werden durch die neuen Module überschrieben*/
        $hook = load_class('Hook', 'admin.menu');
        $hook->set(self::$__admindata);
        $hook->set('only_active', false);
        $hook->set('only_allowed', false);

        $hookitems = $hook->get();
        if ($hookitems && is_array($hookitems)) {
            foreach ($hookitems as $key => $array) {
                if (!$radminsuper && empty($array['case'])) {
                    /* keine Admin-Berechtigung > ignorieren */
                    continue;
                }
                unset($array['case']);
                $array = array_merge(self::$__defaults, $array);
                /* $key dient zur alfabetischen Sortierung des Arrays */
                $key = self::_arrkey($array['title'], $key, $array['url']);
                self::$__links[$array['panel']][$key] = $array;
            }
        }

        /* Die Systemlinks noch anfügen */
        self::_add_systemlinks();

        return (array)self::$__links;
    }

    /**
     * pmxAdminmenue::_paneldata()
     *
     * @return
     */
    private function _paneldata()
    {
         $nav_item = array(
            MX_ADMINPANEL_SYSTEM => array(/*  */
                // erster Menüpunkt verlinkt zur Startseite
                'url' => adminUrl(),
                'image' => 'fa-gears',
                'title' => _PANELSYSTEM,
                'description' => _PANELSYSTEM_DESCR,
                'panelname' => 'system',
                'current' => false,
                'links' => array(),
                ),
            MX_ADMINPANEL_CONTENT => array(/*  */
                'image' => 'fa-file-text',
                'title' => _PANELCONTENT,
                'description' => _PANELCONTENT_DESCR,
                'panelname' => 'content',
                'current' => false,
                'links' => array(),
                ),
            MX_ADMINPANEL_USERS => array(/*  */
                'image' => 'fa-group',
                'title' => _PANELUSER,
                'description' => _PANELUSER_DESCR,
                'panelname' => 'users',
                'current' => false,
                'links' => array(),
                ),
            MX_ADMINPANEL_TOOLS => array(/*  */
                'image' => 'fa-tachometer',
                'title' => _PANELTOOLS,
                'description' => _PANELTOOLS_DESCR,
                'panelname' => 'tools',
                'current' => false,
                'links' => array(),
                ),
            MX_ADMINPANEL_SECURITY => array(/*  */
                'image' => 'fa-lock',
                'title' => _PANELSECURITY,
                'description' => _PANELSECURITY_DESCR,
                'panelname' => 'security',
                'current' => false,
                'links' => array(),
                ),
            MX_ADMINPANEL_ADDON => array(/*  */
                'image' => 'fa-inbox',
                'title' => _PANELADDON,
                'description' => _PANELADDON_DESCR,
                'panelname' => 'addon',
                'current' => false,
                'links' => array(),
                ),
            );
        return $nav_item;
    }

    /**
     * pmxAdminmenue::_add_systemlinks()
     *
     * @return array
     */
    private function _add_systemlinks()
    {
        extract(self::$__admindata);

        if ($radminsuper) {
            $link['settings'] = array(/* Linkdaten */
                'panel' => MX_ADMINPANEL_SYSTEM,
                'title' => _PREFERENCES,
                'image' => 'preferences.png' ,
                );

            $link['blocks'] = array(/* Linkdaten */
                'panel' => MX_ADMINPANEL_SYSTEM,
                'title' => _BLOCKS,
                'image' => 'blocks.png' ,
                );

            $link['textarea'] = array(/* Linkdaten */
                'panel' => MX_ADMINPANEL_SYSTEM,
                'title' => _EDITOR,
                'image' => 'editor.png' ,
                );

            $link['modules'] = array(/* Linkdaten */
                'panel' => MX_ADMINPANEL_SYSTEM,
                'title' => _MODULES,
                'image' => 'modules.png' ,
                );

            $link['menu'] = array(/* Linkdaten */
                'panel' => MX_ADMINPANEL_SYSTEM,
                'title' => _MX_MENU_MANAGER,
                'image' => 'menumanager.png' ,
                );

            $link['optimize'] = array(/* Linkdaten */
                'panel' => MX_ADMINPANEL_TOOLS,
                'title' => _OPTIMIZE,
                'image' => 'db_status.png' ,
                );

            $link['backup'] = array(/* Linkdaten */
                'panel' => MX_ADMINPANEL_TOOLS,
                'title' => _SAVEDATABASE,
                'image' => 'backup.png' ,
                );
/*
            $link['reset/cache'] = array(
                'panel' => MX_ADMINPANEL_TOOLS,
                'title' => _RESETPMXCACHE,
                'image' => 'reactivateuser.png' ,
                );
*/
            $link['lightbox'] = array(/* Linkdaten */
                'panel' => MX_ADMINPANEL_SYSTEM,
                'title' => _LIGHTBOXSETTINGS,
                'image' => 'kview.png' ,
                );

            $link['captcha'] = array(/* Linkdaten */
                'panel' => MX_ADMINPANEL_SECURITY,
                'title' => 'Captcha',
                'image' => 'captcha.png' ,
                );

            $link['referers'] = array(/* Linkdaten */
                'panel' => MX_ADMINPANEL_SECURITY,
                'title' => _HTTPREFERERS,
                'image' => 'referer.png' ,
                );

            $link['securelog'] = array(/* Linkdaten */
                'panel' => MX_ADMINPANEL_SECURITY,
                'title' => _SECLOGTITLE,
                'image' => 'seclog.png' ,
                );

            $link['setban'] = array(/* Linkdaten */
                'panel' => MX_ADMINPANEL_SECURITY,
                'title' => _SETBAN,
                'image' => 'ban.png' ,
                );

            $link['messages'] = array(/* Linkdaten */
                'panel' => MX_ADMINPANEL_SYSTEM,
                'title' => _MESSAGES,
                'image' => 'messages.png' ,
                );

            $link['authors'] = array(/* Linkdaten */
                'panel' => MX_ADMINPANEL_USERS,
                'title' => _EDITADMINS,
                'image' => 'authors.png' ,
                );

            $link['usersconfig'] = array(/* Linkdaten */
                'panel' => MX_ADMINPANEL_USERS,
                'title' => _BENUTZERCONFIG,
                'image' => 'userconfig.png' ,
                );

            $link['versioncheck'] = array(/* Linkdaten */
                'panel' => MX_ADMINPANEL_TOOLS,
                'title' => _VERSIONCHECK,
                'image' => 'daemons.png' ,
                );

            $link['seo'] = array(/* Linkdaten */
                'panel' => MX_ADMINPANEL_SYSTEM,
                'title' => _SEO,
                'image' => 'log.png' ,
                );

            $link['plugins'] = array(/* Linkdaten */
                'panel' => MX_ADMINPANEL_SYSTEM,
                'title' => _PLUGINS,
                'image' => 'plugins.png' ,
                );

            $link['logfiler'] = array(/* Linkdaten */
                'panel' => MX_ADMINPANEL_TOOLS,
                'title' => _LOGFILER,
                'image' => 'bug.png' ,
                );

            $link['hooks'] = array(/* Linkdaten */
                'panel' => MX_ADMINPANEL_SYSTEM,
                'title' => _HOOKS,
                'image' => 'tab_remove.png' ,
                );
            $link['themes'] = array(/* Linkdaten */
				'panel' => MX_ADMINPANEL_SYSTEM,
				'title' => _THEMES,
				'image' => 'themes.png' ,
			);
        }

        if ($radminsuper || $radminuser || $radmingroups) {
            $userconfig = load_class ('Userconfig') ;

            $link['userlist'] = array(/* Linkdaten */
                'panel' => MX_ADMINPANEL_USERS,
                'title' => _EDITUSERS,
                'image' => 'user.png' ,
                );

            $link['users'] = array(/* Linkdaten */
                'panel' => MX_ADMINPANEL_USERS,
                'title' => _EDITUSERS,
                'image' => 'user.png' ,
                );

            $link['who'] = array(/* Linkdaten */
                'panel' => MX_ADMINPANEL_USERS,
                'title' => _WHO_IS_ONLINE,
                'image' => 'who.png' ,
                );

            $link['groups'] = array(/* Linkdaten */
                'panel' => MX_ADMINPANEL_USERS,
                'title' => _USERGROUPS,
                'image' => 'groups.png' ,
                );

            $link['user'] = array(/* Linkdaten */
                'panel' => MX_ADMINPANEL_USERS,
                'title' => _EDITUSERS,
                'image' => 'user.png' ,
                );				
            if ($userconfig->register_option === 3 || $userconfig->register_option === 4) {
                $link['usernewbies'] = array(/* Linkdaten */
                    'panel' => MX_ADMINPANEL_USERS,
                    'title' => _YADELETER,
                    'image' => 'yadeleter.png' ,
                    );
            }
        }

        $link['main'] = array(/* Linkdaten */
            'panel' => MX_ADMINPANEL_SYSTEM,
            'title' => _ADMINDASHBOARD,
            'image' => 'gohome.png' ,
            );

        $link['selfadmin'] = array(/* Linkdaten */
            'panel' => MX_ADMINPANEL_USERS,
            'title' => _OWNDATA,
            'image' => 'theuser.png' ,
            );

        $link['comments'] = array(/* Linkdaten */
            'panel' => MX_ADMINPANEL_CONTENT,
            'title' => _COMMENTSMOD,
            'image' => 'comments.png' ,
            );

        $link['iupload'] = array(/* Linkdaten */
            'panel' => MX_ADMINPANEL_SYSTEM,
            'title' => _MANAGEMEDIA,
            'image' => 'images.png' ,
            );

        $link['comments2'] = array(/* Linkdaten */
            'panel' => MX_ADMINPANEL_CONTENT,
            'title' => _COMMENTSMOD,
            'image' => 'comments.png' ,
            );

        foreach ($link as $module => $array) {
            list($mod_name, $op) = preg_split('#[./]#', $module . '/');
            if (realpath(PMX_ADMINMODULES_DIR . DS . $mod_name . '/index.php')) {
                $array['module'] = $module;
                $array['url'] = adminUrl($mod_name, $op);
                $array['image'] = self::$_imagepath . $array['image'];

                /* $key dient zur alfabetischen Sortierung des Arrays */
                // Die Adminstartseite an den Anfang sortieren
                $key = ('main' == $module) ? 'aaa' . $array['title'] : $array['title'];
                $key = self::_arrkey($key, $array['url']);

                self::$__links[$array['panel']][$key] = array_merge(self::$__defaults, $array);
            }
        }
    }

    /**
     * pmxAdminmenue::_arrkey()
     * ein eindeutiger String, ohne Sonderzeichen
     *
     * @return
     */
    private static function _arrkey()
    {
        $args = func_get_args();
        return strtolower(preg_replace('#[^a-zA-Z0-9]#', '', implode('', $args)));
    }

    /**
     * pmxAdminmenue::_outdated()
     *
     * @return array
     */
    private function _outdated()
    {
        return array(/* Dateien die zu ignorieren sind */
            'links._system.php',
            'links.addstory.php',
            'links.avatar.php',
            'links.backup.php',
            'links.banners.php',
            'links.bannersfsz.php',
            'links.blocks.php',
            'links.captcha.php',
            'links.comments.php',
            'links.content.php',
            'links.doku.php',
            'links.download.php',
            'links.downloadconfig.php',
            'links.editadmins.php',
            'links.editgroups.php',
            'links.editor.php',
            'links.editusers.php',
            'links.egallery.php',
            'links.encyclopedia.php',
            'links.ephemerids.php',
            'links.faq.php',
            'links.forum.php',
            'links.forums.php',
            'links.groups.php',
            'links.hip.php',
            'links.httpreferers.php',
            'links.images.php',
            'links.info.php',
            'links.intruder.php',
            'links.ipban.php',
            'links.lightbox.php',
            'links.linksconfig.php',
            'links.messages.php',
            'links.moderation.php',
            'links.modules.php',
            'links.mxfeedback.php',
            'links.mx_menu.php',
            'links.news-addstory.php',
            'links.news-submissions.php',
            'links.news-topics.php',
            'links.newsletter.php',
            'links.nukebook.php',
            'links.onlygod.php',
            'links.optimize.php',
            'links.preferences.php',
            'links.prettyphoto.php',
            'links.reactivateuser.php',
            'links.recommend.php',
            'links.reviews.php',
            'links.SecLog.php',
            'links.seclog.php',
            'links.sections.php',
            'links.securelog.php',
            'links.selfadmin.php',
            'links.setban.php',
            'links.settings.php',
            'links.siteupdate.php',
            'links.smilies.php',
            'links.submissions.php',
            'links.surveys.php',
            'links.system-backup.php',
            'links.system-banners.php',
            'links.system-blocks.php',
            'links.system-httpreferers.php',
            'links.system-log.php',
            'links.system-messages.php',
            'links.system-modules.php',
            'links.system-optimize.php',
            'links.system-preferences.php',
            'links.system-xphpinfo.php',
            'links.topics.php',
            'links.update.php',
            'links.userguest.php',
            'links.users-authors_access.php',
            'links.users-editadmins.php',
            'links.users-editusers.php',
            'links.users.php',
            'links.usersconfig.php',
            'links.weblinks.php',
            'links.who.php',
            'links.ya_deleter.php',
            );
    }
}

?>