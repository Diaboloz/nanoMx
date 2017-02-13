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
 * $Revision: 206 $
 * $Author: PragmaMx $
 * $Date: 2016-09-12 13:33:26 +0200 (Mo, 12. Sep 2016) $
 */

defined('mxMainFileLoaded') or die('access denied');

require_once(dirname(__FILE__) . DS . 'Menu' . DS . 'model.php');

/**
 * pmxMenu
 *
 * @package
 * @author tora60
 * @copyright Copyright (c) 2010
 * @version $Id: Menu.php 206 2016-09-12 11:33:26Z PragmaMx $
 * @access public
 */
class pmxMenu extends pmxMenu_model {
    protected $_menu_id = 0;
    protected $_menu_tree = array();

    /* Javascripte welche am Ende des Menues ausgeführt werden */
    protected $js_end = array();

    /* jQuery und Menü-Javascripte einbinden? */
    protected $_load_script = false;

    protected $_config = array(/* Standardwerte */
        'menuname' => '',
        'homelink' => './',
        'class_home' => 'home',
        'class_first' => 'first',
        'class_last' => 'last',
        'class_current' => 'current',
        'class_disabled' => 'disabled',
        'class_nolink' => 'nolink',
        'class_sublevel' => 'sub',
        'class_parent' => 'mx-menu-has-children mx-menu-allow-hover',
        'class_additional' => '',
        'stylesheet' => '', // in __construct
        'template_path' => '', // in __construct
        'template' => 'block.html', // in __construct
        'normal_events' => true, // normale Klappfunktion, also nicht im CSS-menü etc.
        'adminmenu' => false,
        );

    protected static $_block_id_ = 0;

    protected $_template_object;

    /**
     * pmxMenu::get_block_instance()
     * ein pmxMenu Objekt für den Menüblock zurueckgeben,
     * abhaengig vom Theme
     *
     * @return
     */
    public static function get_block_instance($bid)
    {
        switch (true) {
            case (($file = realpath(MX_THEME_DIR . '/theme.mxmenu.php')) && is_file($file)):
            case (($file = realpath(MX_THEME_DIR . '/menu.class.php')) && is_file($file)):
                self::$_block_id_ = $bid;
                /* das Theme erweitert die Klasse selbst */
                include_once($file);
                if (class_exists('Theme_Mx_Menu', false)) {
                    /* in Theme_Mx_Menu() wird dann die 'alte' Klasse Mx_Menu
                     * erweitert, deswegen unten, in dieser Datei, der dummy ;-)
                     */
                    $menu = new Theme_Mx_Menu($bid);
                    if ($bid && $bid == 'adminmenu') {
                        $menu->_menu_id = 0;
                        $menu->adminmenu = true;
                        // hässlicher Trick, weil die Klasse im theme
                        // die $bid nicht an den Konstruktor übergibt
                        $menu->__construct($bid);
                    }
                    return $menu;
                }
                /* kein break, dass default offen bleibt! */
            default:
                /* Standardmethoden für Blockmenüs, unabhängig vom Theme */
                return new pmxMenu($bid);
        }
    }

    /**
     * pmxMenu::get_menu_instance()
     *
     * @param mixed $params
     * @return
     */
    public static function get_menu_instance($params = false)
    {
        include_once(dirname(__FILE__) . DS . 'Menu' . DS . 'menu.php');
        /* Achtung!! in versch. Themes sind die css-Dateien nur Dummies, die müssen dann gelöscht werden !! */
        return new pmxMenu_menu($params);
    }

    /**
     * pmxMenu::__construct()
     *
     * @param integer $bid
     */
    public function __construct($bid = 0)
    {
        /* Konstruktor der Model-Klasse ausfuehren */
        parent::__construct();

        /* Sprachdatei auswaehlen */
        if (!defined('_MX_MENU_ADMIN')) {
            mxGetLangfile(dirname(__FILE__) . DS . 'Menu' . DS . 'language');
        }

        switch (true) {
            case $bid && $bid == 'adminmenu':
                $this->_menu_id = 0;
                $this->adminmenu = true;
                break;
            case self::$_block_id_:
                $this->_menu_id = intval(self::$_block_id_);
                self::$_block_id_ = 0;
                break;
            case $bid:
                $this->_menu_id = intval($bid);
                break;
            default:
                $this->_menu_id = 0;
        }

        if ($this->adminmenu) {
            $this->_menu_tree = $this->read_items_4admin_menu();
        } else if ($this->_menu_id) {
            $this->_menu_tree = $this->read_items_4menu_db($this->_menu_id);
        }

        /* Template initialisieren */
        $this->_template_object = load_class('Template');
    }

    /**
     * pmxMenu::_level()
     *
     * @param integer $level
     * @return
     */
    protected function _level($level = 0)
    {
        switch (true) {
            case $level !== 0:
                break;

            case !$this->_menu_tree && MX_IS_ADMIN:
                return '<div class="important">' . _MX_MENU_ADDITEM_NOTDEF . ' [<a href="' . adminUrl('menu', 'add_item/' . $this->_menu_id) . '">' . _MX_MENU_ADDMENU_EDIT . '</a>]</div>';

            case !$this->_menu_tree:
                return '<!-- ' . _MX_MENU_ADDITEM_NOTDEF . ' -->';

            case $this->template_path && ($path = realpath($this->template_path)):
                /* Template initialisieren */
                $this->_template_object->set_path($path);
                break;

            case $this->template_path && !$path:
                trigger_error("template path '$this->template_path' doesn't exit!", E_USER_NOTICE);
                break;

            default:
                /* Template initialisieren */
                // ergibt: layout/templates/includes/classes/Menu/
                $this->_template_object->init_path(__FILE__);
        }

        $parent = $this->_menu_tree[$level];

        $items = array();
        $i = 0;
        $counts = count($parent['sub']);

        foreach ($parent['sub'] as $value) {
            $i++;
            $class = array('item' => array(),
                'actor' => array(),
                'link' => array(),
                'isparent' => array(),
                );

            $row = $this->_menu_tree[$value];

            $subtree = '';
            if ($row['sub']) {
                $subtree = $this->_level($value);

                if ($level !== 0 && $subtree) {
                    $class['isparent'][] = $this->class_sublevel;
                }
            }

            if (!$row['allowed']) {
                if ($subtree) {
                    // wenn nicht erlaubt, aber Unterpunkte vorhanden, den URL aendern
                    $row['url'] = '';
                    $row['description'] = '';
                    $row['disabled'] = true;
                } else {
                    // wenn nicht erlaubt und keine Unterpunkte vorhanden, einfach ignorieren
                    continue;
                }
            }

            if ($row['current']) {
                $class['item'][] = $this->class_current;
            }

            if ($this->_current_link_parents && in_array($row['id'], $this->_current_link_parents)) {
                $class['citem'][] = $this->class_current;
            }

            $class['actor'][] = 'd';
            if ($row['sub']) {
                $class['actor'][] = 'ac-' . $row['item'];
            }

            if ($row['disabled']) {
                $class['link'][] = $this->class_disabled;
                $class['actor'][] = $this->class_disabled;
            }

            if ($row['target']) {
                $tggt = 'mtgt-' . str_replace('_', '-', $row['target']);
                $class['link'][] = $tggt;
                $this->js_end_add("$('a." . $tggt . ", ." . $tggt . " > a').attr('target','" . $row['target'] . "');");
            }

            if (!$row['url']) {
                $class['item'][] = $this->class_nolink;
                /* wenn kein Link vorhanden, wird auch die Beschreibung nicht angezeigt, das hier per Javascript fixen */
                if ($row['description'] && $row['description'] != $row['title']) {
                    $this->js_end_add('men_titles["' . $row['item'] . '"] = "' . $row['description'] . '";');
                }
            }

            if ($this->normal_events && $row['sub'] && $row['expanded']) {
                /* bei den bereits ausgeklappten Links, die richtigen icons und Klassen zuweisen */
                $this->js_end_add("mxmenu_toggle('" . $row['item'] . "', true);");
            }

            $class['citem'] = (empty($class['citem'])) ? '' : ' class="' . implode(' ', array_unique($class['citem'])) . '"';
            $class['actor'] = (empty($class['actor'])) ? '' : ' class="' . implode(' ', array_unique($class['actor'])) . '"';
            $class['link'] = (empty($class['link'])) ? '' : ' class="' . implode(' ', array_unique($class['link'])) . '"';
            $class['isparent'] = (empty($class['isparent'])) ? '' : ' class="' . implode(' ', array_unique($class['isparent'])) . '"';

            $tmp = array(/*  */
                'href' => ($row['url']) ? ' href="' . $row['url'] . '"' : '',
                'title' => ($row['description']) ? ' title="' . $row['description'] . '"' : '',
                'caption' => $row['title'],
                'subtree' => $subtree,
                'class' => $class,
				'target'=>$row['target'],
                );

            $items[] = array_merge($row, $tmp);
        }

        switch (true) {
            case $level === 0 && !$items && MX_IS_ADMIN:
                return '<div class="important">' . _MX_MENU_ADDITEM_NOTDEF . ' [<a href="' . adminUrl('menu', 'add_item/' . $this->_menu_id) . '">' . _MX_MENU_ADDMENU_EDIT . '</a>]</div>';
            case $level === 0 && !$items:
                return '<!-- ' . _MX_MENU_ADDITEM_NOTDEF . ' -->';
            case $level !== 0 && !$items:
                return '';
            case $level !== 0:
                $id = $parent['item'];
                $containerclass = " class=\"ul-{$id}\"";

                if ($this->normal_events) {
                    /* die Klappfunktionen für diesen Link einbinden */
                    $this->js_end_add("\$('.mx-menu .ac-{$id}').click(function() {mxmenu_slide('{$id}');});");
                    if (!$parent['expanded']) {
                        /* alle zusammenklappen */
                        $this->js_end_add("mxmenu_toggle('{$id}', false);");
                    }
                }
                break;
            default:
                $containerclass = '';
        }

        $counts = count($items)-1;
        foreach ($items as $i => $item) {
            $class = $item['class']['item'];
            if ($i === 0) {
                $class[] = $this->class_first;
            }
            if ($i === $counts) {
                $class[] = $this->class_last;
            }
            if ($item['url'] === $this->homelink) {
                $class[] = $this->class_home;
            }
            if ($this->class_additional) {
                $class[] = $this->class_additional;
            }
            if ($item['sub']) {
                $class[] = $this->class_parent;
            }
            $items[$i]['class']['item'] = ($class) ? ' class="' . implode(' ', array_unique($class)) . '"' : '';
        }

        $js_end = implode("\n", array_unique($this->js_end));

        $this->_template_object->assign(compact('level', 'items', 'js_end', 'containerclass'));
        $this->_template_object->assign('menu_id', $this->_menu_id);
        $this->_template_object->assign('is_ie', $this->detect_ie());
        $out = $this->_template_object->fetch($this->template);

        $out = str_replace(array('  ', ' >'), array(' ', '>'), $out);
        $out = preg_replace('#\s*\n\s*#', "\n", $out);

        return $out;
    }

    /**
     * pmxMenu::get_tree()
     *
     * @return
     */
    public function get_tree()
    {
        return $this->_menu_tree;
    }

    /**
     * pmxMenu::fetch()
     *
     * @return
     */
    public function fetch()
    {
        $menu = $this->_level();

        if (!$this->_menu_tree) {
            return $menu;
        }
        /* ist menue aufklappbar? nur, wenn auch Submenues vorhanden sind */
  //nanomx not need js     $topcount = count($this->_menu_tree[0]['sub']);
  //      $allcount = count($this->_menu_tree)-1; // [0] abziehen
  //      $expandable = ($allcount > $topcount);

        /* Javascript fur Klappfunktion in Headbereich integrieren */
  //      if ($expandable) {
  //          pmxHeader::add_script_code("var lang_open = '" . _EXPAND2COLLAPSE_TITLE_E . "';\nvar lang_close = '" . _EXPAND2COLLAPSE_TITLE_C . "';");
  //      }

  //        /* jQuery Bibliothek fur Klapp, Target und Hoverfunktionen einbinden */
  //      switch (true) {
  //          case $this->detect_ie() === 6:
                /* oller IE? */
  //              pmxHeader::add_jquery();
  //              pmxHeader::add_script(PMX_JAVASCRIPT_PATH . 'mx_menu_ie6.js');
  //              break;
  //          case $expandable:
  //          case $this->_load_script;
  //              pmxHeader::add_jquery();
  //              pmxHeader::add_script(PMX_JAVASCRIPT_PATH . 'mx_menu.js');
  //              break;
 //       }

 //       if ($this->stylesheet) {
            pmxHeader::add_style($this->stylesheet);
  //      }

        return $menu;
    }

    /**
     * pmxMenu::js_end_add()
     *
     * @param mixed $add
     * @return
     */
    protected function js_end_add($add)
    {
        $this->js_end[] = trim($add);
        $this->_load_script = true;
    }

    /**
     * pmxMenu::detect_ie()
     * ist der Browser ein schrottiger Internetexplorer?
     *
     * @return int / bool
     */
    protected function detect_ie()
    {
        $browser = load_class('Browser');
        if ($browser->msie) {
            $ie = intval($browser->version);
        } else {
            $ie = false;
        }
        return $ie;
    }

    /**
     * pmxMenu::__get()
     *
     * @param mixed $name
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
     * pmxMenu::__set()
     *
     * @param mixed $name
     * @param mixed $value
     * @return
     */
    public function __set($name, $value)
    {
        $this->_config[$name] = $value;
    }
}

/**
 * Mx_Menu
 * zur Kompatibilität mit Themes, die für pragmaMx 0.1.11 geschrieben wurden
 * z.B. das element oder futuremag
 * wird in Methode pmxMenu::get_block_instance() referenziert !
 *
 * @package
 * @author tora60
 * @copyright Copyright (c) 2009
 * @version $Id: Menu.php 206 2016-09-12 11:33:26Z PragmaMx $
 * @access public
 */
class Mx_Menu extends pmxMenu {
    public function __construct()
    {
        parent::__construct();
    }
}

?>