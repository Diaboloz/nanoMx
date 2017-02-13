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
 * Author: Olaf Herfurth / TerraProject  http://www.tecmu.de
 *
 * $Revision: 6 $
 * $Author: PragmaMx $
 * $Date: 2015-07-08 09:07:06 +0200 (Mi, 08. Jul 2015) $
 */

$hook = function($module_name, $parameter, &$replaces)
{
    if (!isset($parameter[$module_name])) {
        return;
    }
    global $prefix;

    $book = load_class('Book', $module_name);

    $cat = array();
    $page = array();
    $pageedit = array();
    $pagenew = array();
    $pageprint = array();
    $sendfriend = array();

    foreach ($parameter[$module_name] as $key => $para) {
        switch (true) {
            case (isset($para['id'], $para['act']) && intval($para['id']) && $para['act'] == 'page'):
                $cat[0][] = $para['id'];
                $cat[$key] = $para;
                break;
            case (isset($para['id'], $para['act']) && intval($para['id']) && $para['act'] == 'index'):
                $page[0][] = $para['id'];
                $page[$key] = $para;
                break;
            case (isset($para['id'], $para['act']) && intval($para['id']) && $para['act'] == 'pageedit'):
                $pageedit[0][] = $para['id'];
                $pageedit[$key] = $para;
                break;
            case (isset($para['id'], $para['act']) && intval($para['id']) && $para['act'] == 'pagenew'):
                $pagenew[0][] = $para['id'];
                $pagenew[$key] = $para;
                break;
            case (isset($para['id'], $para['act']) && intval($para['id']) && $para['act'] == 'pageprint'):
                $pageprint[0][] = $para['id'];
                $pageprint[$key] = $para;
                break;
            case (isset($para['id'], $para['act']) && intval($para['id']) && $para['act'] == 'sendfriend'):
                $sendfriend[0][] = $para['id'];
                $sendfriend[$key] = $para;
                break;
            case (isset($para['act']) && isset($para['keywords']) && $para['act'] == 'search'):
                $search[0][] = $para['keywords'];
                $search[$key] = $para;
                // $new_url = pmxModrewrite::title_parameters($link, 'keywords', 'act');
                $new_url = $module_name . '-Search-' . $para['keywords'] . ".html";
                $replaces[$key] = $para['prefix'] . $new_url . $para['suffix'];
                break;
            case (isset($para['act']) && isset($para['char']) && $para['act'] == 'alphaindex'):
                $search[0][] = $para['char'];
                $linkid = "";
                if (isset($para['id'])) $linkid = "-id-" . intval($para['id']);
                $search[$key] = $para;
                // $new_url = pmxModrewrite::title_parameters($link, 'keywords', 'act');
                $new_url = $module_name . '-Alphaindex-' . $para['char'] . $linkid . ".html";
                $replaces[$key] = $para['prefix'] . $new_url . $para['suffix'];
                break;
            case (isset($para['act']) && $para['act'] == 'newestpages'):
                // $new_url = pmxModrewrite::title_parameters($link, 'keywords', 'act');
                $new_url = $module_name . '-Newest-Pages' . ".html";
                $replaces[$key] = $para['prefix'] . $new_url . $para['suffix'];
                break;
        }
    }

    /**
     */
    if ($cat) {
        $inquery = implode(',', array_unique(array_shift($cat)));
        $ids = preg_split('/,/', $inquery);
        $result = $book->getRecordsFromId($ids);
        $titles = array();
        foreach ($result as $key => $value) {
            $bookt = $book->getBookRoot($value['id']);
            $booktitle = $bookt['title'];
            if ($bookt['id'] == true) {
                $titles[$value['id']] = pmxModrewrite::title_entities($booktitle . "-Page-" . $value['title'], '-');
            } else {
                $titles[$value['id']] = pmxModrewrite::title_entities($value['title'], '-');
            }
        }

        foreach ($cat as $key => $link) {
            $new_url = pmxModrewrite::title_parameters($link, 'id', 'act');
            $new_url = $module_name . '-' . $titles[$link['id']] . '-Id-' . $link['id'] . $new_url;
            $replaces[$key] = $link['prefix'] . $new_url . $link['suffix'];
        }
    }

    /*   */
    if ($page) {
        $inquery = implode(',', array_unique(array_shift($page)));
        $ids = preg_split('/,/', $inquery);
        $result = $book->getRecordsFromId($ids);
        $titles = array();
        foreach ($result as $key => $value) {
            $bookt = $book->getBookRoot($value['id']);
            $booktitle = $bookt['title'];
            if ($bookt['id'] == true) {
                $titles[$value['id']] = pmxModrewrite::title_entities($booktitle . "-Contents-" . $value['title'], '-');
            } else {
                $titles[$value['id']] = pmxModrewrite::title_entities($value['title'] . "-Contents-", '-');
            }
        }

        foreach ($page as $key => $link) {
            $new_url = pmxModrewrite::title_parameters($link, 'id', 'act');
            $new_url = $module_name . '-' . $titles[$link['id']] . '-' . $link['id'] . $new_url;
            $replaces[$key] = $link['prefix'] . $new_url . $link['suffix'];
        }
    }
    if ($pageedit) {
        $inquery = implode(',', array_unique(array_shift($pageedit)));
        $ids = preg_split('/,/', $inquery);
        $result = $book->getRecordsFromId($ids);
        $titles = array();
        foreach ($result as $key => $value) {
            $titles[$value['id']] = pmxModrewrite::title_entities($value['title'] . "-Edit", '-');
        }

        foreach ($pageedit as $key => $link) {
            $new_url = pmxModrewrite::title_parameters($link, 'id', 'act');
            $new_url = $module_name . '-' . $titles[$link['id']] . '-' . $link['id'] . $new_url;
            $replaces[$key] = $link['prefix'] . $new_url . $link['suffix'];
        }
    }
    if ($pagenew) {
        $inquery = implode(',', array_unique(array_shift($pagenew)));
        $ids = preg_split('/,/', $inquery);
        $result = $book->getRecordsFromId($ids);
        $titles = array();
        foreach ($result as $key => $value) {
            $titles[$value['id']] = pmxModrewrite::title_entities($value['title'] . "-New", '-');
        }

        foreach ($pagenew as $key => $link) {
            $new_url = pmxModrewrite::title_parameters($link, 'id', 'act');
            $new_url = $module_name . '-' . $titles[$link['id']] . '-' . $link['id'] . $new_url;
            $replaces[$key] = $link['prefix'] . $new_url . $link['suffix'];
        }
    }
    if ($pageprint) {
        $inquery = implode(',', array_unique(array_shift($pageprint)));
        $ids = preg_split('/,/', $inquery);
        $result = $book->getRecordsFromId($ids);
        $titles = array();
        foreach ($result as $key => $value) {
            $bookt = $book->getBookRoot($value['id']);
            $booktitle = $bookt['title'];
            if ($bookt['id'] == true) {
                $titles[$value['id']] = pmxModrewrite::title_entities($booktitle . "-Print-" . $value['title'], '-');
            } else {
                $titles[$value['id']] = pmxModrewrite::title_entities($value['title'], '-');
            }
        }

        foreach ($pageprint as $key => $link) {
            $new_url = pmxModrewrite::title_parameters($link, 'id', 'act');
            $new_url = $module_name . '-' . $titles[$link['id']] . '-' . $link['id'] . $new_url;
            $replaces[$key] = $link['prefix'] . $new_url . $link['suffix'];
        }
    }
    if ($sendfriend) {
        $inquery = implode(',', array_unique(array_shift($sendfriend)));
        $ids = preg_split('/,/', $inquery);
        $result = $book->getRecordsFromId($ids);
        $titles = array();
        foreach ($result as $key => $value) {
            $bookt = $book->getBookRoot($value['id']);
            $booktitle = $bookt['title'];
            if ($bookt['id'] == true) {
                $titles[$value['id']] = pmxModrewrite::title_entities($booktitle . "-Friend-" . $value['title'], '-');
            } else {
                $titles[$value['id']] = pmxModrewrite::title_entities($value['title'], '-');
            }
        }

        foreach ($sendfriend as $key => $link) {
            $new_url = pmxModrewrite::title_parameters($link, 'id', 'act');
            $new_url = $module_name . '-' . $titles[$link['id']] . '-' . $link['id'] . $new_url;
            $replaces[$key] = $link['prefix'] . $new_url . $link['suffix'];
        }
    }
    unset($book);
}
// include_once("dynamic_mode_rewrite.php");

?>