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

/**
 * pmxMenu_menu
 *
 * @package
 * @author tora60
 * @copyright Copyright (c) 2010
 * @version $Id: menu.php 206 2016-09-12 11:33:26Z PragmaMx $
 * @access public
 */
class pmxMenu_menu extends pmxMenu {
    // private $_template_object;
    /**
     * pmxMenu_menu::__construct()
     *
     * @param mixed $params
     */
    public function __construct($params = false)
    {
        /* Konstruktor der Menü-Klasse ausfuehren */
        parent::__construct();

        switch (true) {
            case is_array($params):
                $this->_config = array_merge($this->_config, $params);
                break;
            case is_numeric($params):
                $this->_menu_id = $params;
                $this->_config['menuname'] = get_menu_title_db($params);
                break;
            case is_string($params):
                $this->_menu_id = $this->getmenuid_by_name_db($params);
                $this->_config['menuname'] = $params;
                break;
        }

        /* normale Klappfunktion abschalten */
        $this->normal_events = false;

        /* Menüpunkte auslesen */

        if ($this->adminmenu) {
            return $this->_menu_tree = $this->read_items_4admin_menu();
            // return;
        }

        if (!$this->_menu_id) {
            $this->_menu_id = $this->getmenuid_by_name_db($this->_config['menuname']);
        }

        if ($this->_menu_id) {
            $this->_menu_tree = $this->read_items_4menu_db($this->_menu_id);
        }

        /* Template initialisieren */
        // $this->_template_object = load_class('Template');
    }

    /**
     * pmxMenu_menu::_level()
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
                $this->_template_object->init_path(__DIR__);
        }

        $parent = $this->_menu_tree[$level];

        $items = array();
        $i = 0;
        $counts = count($parent['sub']);

        foreach ($parent['sub'] as $value) {
            $i++;
            $class = array();

            $row = $this->_menu_tree[$value];

            $subtree = '';
            if ($row['sub']) {
                $subtree = $this->_level($value);

                if ($level !== 0 && $subtree) {
                    $class[] = $this->class_sublevel;
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

            if ($this->_current_link_parents && in_array($row['id'], $this->_current_link_parents)) {
                $class[] = $this->class_current;
            }

            if ($row['target']) {
                $tggt = 'mtgt-' . str_replace('_', '-', $row['target']);
                $class[] = $tggt;
                $this->js_end_add("$('a." . $tggt . ", ." . $tggt . " > a').attr('target','" . $row['target'] . "');");
            }

            if (!$row['url']) {
                $class[] = $this->class_nolink;
            }

            if (!empty($row['class']) && is_scalar($row['class'])) {
                $class[] = $row['class'];
            }

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
        }

        $counts = count($items)-1;
        foreach ($items as $i => $item) {
            $class = $item['class'];
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
            $items[$i]['class'] = ($class) ? ' class="' . implode(' ', array_unique($class)) . '"' : '';
        }

        $js_end = implode("\n", array_unique($this->js_end));

        $this->_template_object->assign(compact('level', 'items', 'js_end'));
        $this->_template_object->assign('is_ie', $this->detect_ie());
        $out = $this->_template_object->fetch($this->template);

        $out = str_replace(array('  ', ' >'), array(' ', '>'), $out);
        $out = preg_replace('#\s*\n\s*#', "\n", $out);

        return $out;
    }
}

?>