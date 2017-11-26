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

defined('mxMainFileLoaded') or die('access denied');

if (!mxGetAdminPref('radminsuper')) {
    return mxRedirect(adminUrl(), 'Access Denied');
}

define('MXBLOCKFILENAMEDELIMITER', '~~');
define('MXBLOCKFILENAMEFORMATER', '%s' . MXBLOCKFILENAMEDELIMITER . '%s');

/* Sprachdatei auswählen */
mxGetLangfile(__DIR__);

function BlocksAdmin()
{
    $template = load_class('Template');
    $template->init_path(__FILE__);

    include('header.php');
    // title(_BLOCKSADMIN);
    $template->display('navTabs.html');
    include('footer.php');
}

function blockslistall()
{
    global $prefix;

    $multilingual = $GLOBALS['multilingual'];

    if (isset($_GET['filter'])) {
        if ($_GET['filter'] == -1) {
            mxSessionDelVar('blockfilter');
        } else {
            mxSessionSetVar('blockfilter', $_GET['filter']);
        }
    }
    $filterlink[] = (mxSessionGetVar('blockfilter') == 1) ? '<a class="nav-link active">' . _BLOCKSHOWACTIVE . '</a>' : '<a class="nav-link" href="' . adminUrl(PMX_MODULE, '', 'filter=1') . '">' . _BLOCKSHOWACTIVE . '</a>';
    $filterlink[] = (!mxSessionGetVar('blockfilter')) ? '<a class="nav-link active">' . _ALL . '</a>' : '<a class="nav-link" href="' . adminUrl(PMX_MODULE, '', 'filter=-1') . '">' . _ALL . '</a>';
    $filterlink[] = (mxSessionGetVar('blockfilter') == 2) ? '<a class="nav-link active">' . _BLOCKSHOWDEACTIVE . '</a>' : '<a class="nav-link" href="' . adminUrl(PMX_MODULE, '', 'filter=2') . '">' . _BLOCKSHOWDEACTIVE . '</a>';

    switch (mxSessionGetVar('blockfilter')) {
        case 1:
            $qryfilter = ' WHERE active = 1 ';
            break;
        case 2:
            $qryfilter = ' WHERE active = 0 ';
            break;
        default:
            $qryfilter = '';
    }

    $qry = "SELECT bid, bkey, title, url, position, weight, active, blanguage, module, blockfile, view, refresh
            FROM ${prefix}_blocks " . $qryfilter . "
            ORDER BY position, weight";
    $result = sql_query($qry);

    $countblocks = sql_num_rows($result);
    $blocks = array();

    if ($countblocks) {

        // Icons Bootstrap 4
        $img_activate   = '<i class="fa fa-eye fa-lg"></i>&nbsp;'  . _ACTIVATE;
        $img_deactivate = '<i class="fa fa-eye-slash fa-lg"></i>&nbsp;'  . _DEACTIVATE;
        $img_edit       = '<i class="fa fa-edit fa-lg"></i>&nbsp;'  . _EDIT;
        $img_delete     = '<i class="fa fa-trash fa-lg"></i>&nbsp;'  . _DELETE;
        $img_view       = '<i class="fa fa-search fa-lg"></i>&nbsp;'  . _SHOW;
        // Icons Bootstrap 4 - End

        while ($block = sql_fetch_object($result)) {
            if (mxIsNewsBlockfile($block->module, $block->blockfile)) {
                $block->position = 'z';
            }

            $block->errfile = false;
            $block->cachetime = getRefreshTimeString($block->refresh);

            switch ($block->position) {
                case 'c':
                    $block->pos = _CENTERUP;
                    break;
                case 'd':
                    $block->pos = _CENTERDOWN;
                    break;
                case 'r':
                    $block->pos = _RIGHT;
                    break;
                case 'z':
                    $block->pos = _RIGHTNEWS;
                    break;
                default:
                    $block->pos = _LEFT;
            }
            // ########################
            // $block['typecaption'] = "??";
            // if ($block['bkey'] == SYS_BLOCKTYPE_MESSAGE) $block['typecaption'] = _BLK_MESSAGE;
            // else if ($block['bkey'] == SYS_BLOCKTYPE_RDF) $block['typecaption'] = _RSSCONTENT;
            // else if ($block['bkey'] == SYS_BLOCKTYPE_FILE) $block['typecaption'] = str_replace(".php", "", str_replace("block-", "", $block['blockfile'])) . " (" . _BLOCKFILE2 . ")";
            // else if ($block['bkey'] == SYS_BLOCKTYPE_HTML) $block['typecaption'] = "HTML";
            // else {
            // // falls kein gültiger bkey vorhanden, den Wert aktualisieren
            // $block = blockrepairbkey($block);
            // }
            // ##########################
            switch (true) {
                case !empty($block->url):
                    $block->type = 'RSS/RDF';
                    break;
                case !empty($block->blockfile) && !empty($block->module):
                    if (!is_file(PMX_MODULES_DIR . DS . $block->module . DS . 'blocks' . DS . $block->blockfile)) {
                        $block->errfile = true;
                    }
                    $block->type = _BLOCKFILE2 . ' &gt; ' . $block->module . ' &gt; ' . str_replace('.php', '', str_replace('block-', '', $block->blockfile));
                    break;
                case !empty($block->blockfile):
                    if (!is_file(PMX_BLOCKS_DIR . DS . $block->blockfile)) {
                        $block->errfile = true;
                    }
                    $block->type = _BLOCKFILE2 . ' &gt;&gt; ' . str_replace('.php', '', str_replace('block-', '', $block->blockfile));
                    break;
                default:
                    $block->type = 'HTML';
            }

            switch ($block->view) {
                case 1:
                    $block->who_view = _MVGROUPS2;
                    break;
                case 2:
                    $block->who_view = _MVADMIN;
                    break;
                case 3:
                    $block->who_view = _MVANON;
                    break;
                case 4:
                    $block->who_view = _MVSYSADMIN;
                    break;
                case 0:
                default:
                    $block->who_view = _MVALL;
                    break;
            }

            $block->title = (empty($block->active)) ? '<span class="font-italic">' . $block->title . '</span>' : $block->title;

            $block->functions = array();
            $block->functions[] = '<a class="btn btn-primary btn-sm" title="' . _EDIT . '" href="' . adminUrl(PMX_MODULE, 'Edit', 'bid=' . $block->bid) . '">' . $img_edit . '</a>';
            $block->functions[] = '<a class="btn btn-' . ((empty($block->active)) ? 'success' : 'secondary') . ' btn-sm" href="' . adminUrl(PMX_MODULE, 'ChangeStatus', 'bid=' . $block->bid) . '">' . ((empty($block->active)) ? $img_activate : $img_deactivate) . '</a>';
            if ($block->position !== 'z') {
                $block->functions[] = '<a class="btn btn-danger btn-sm" title="' . _DELETE . '" href="' . adminUrl(PMX_MODULE, 'Delete', 'bid=' . $block->bid) . '">' . $img_delete . '</a>';
                $block->functions[] = '<a class="btn btn-info btn-sm" title="' . _SHOW . '" href="' . adminUrl(PMX_MODULE, 'show', 'bid=' . $block->bid) . '">' . $img_view . '</a>';
            }

            if ($multilingual) {
                if (empty($block->blanguage)) {
                    $block->blanguage = _ALL;
                } else {
                    $block->blanguage = ucfirst($block->blanguage);
                }
            }
            $blocks[$block->pos][] = $block;
        }
    }

    $template = load_class('Template');
    $template->init_path(__FILE__);

    $template->assign(compact('blocks', 'countblocks', 'multilingual', 'filterlink'));

    return $template->fetch('blockslistall.html');
}

function BlocksListWeigth($pvs)
{
    global $prefix;
    $i = 0;
    foreach ($pvs['weight'] as $bid => $weight) {
        $qry = "UPDATE ${prefix}_blocks SET weight=" . $weight . " WHERE bid=" . $bid . " AND weight<>" . $weight;
        $result = sql_query($qry);
        if ($result) $i++;
    }
    if ($i) {
        repairweigth();
    }
    mxRedirect(adminUrl(PMX_MODULE), _ADMIN_SETTINGSAVED);
}

function blockaddform ($mode)
{
    static $groupselect;
    if (!isset($groupselect)) {
        $groupselect = getGroupSelect(0);
    }

    switch ($mode) {
        case 'file':
            $type = _BLOCKFILE;
            break;
        case 'rss':
            $type = _RSSCONTENT;
            break;
        case 'html':
        default:
            $type = '(HTML)';
            break;
    }

    $content = '';
    $multilingual = $GLOBALS['multilingual'];

    $template = load_class('Template');
    $template->init_path(__FILE__);
    $template->assign(compact('mode', 'type', 'content', 'groupselect', 'multilingual'));
    return $template->fetch('blockaddform.html');
}

function BlocksEdit()
{
    global $prefix;
    $bid = (empty($_GET['bid'])) ? 0 : intval($_GET['bid']);
    $menu = (empty($_GET['menu'])) ? 0 : 1;
    $result = sql_query("SELECT * FROM ${prefix}_blocks WHERE bid=" . $bid);
    $block = sql_fetch_assoc($result);

    $type = (empty($block['url'])) ? 'HTML' : _RSSCONTENT;
    $type = (empty($block['blockfile'])) ? $type : _BLOCKFILE;

    $oldposition = $block['position'];
    $multilingual = $GLOBALS['multilingual'];

    $template = load_class('Template');
    $template->init_path(__FILE__);
    $template->assign(compact('multilingual', 'type', 'menu', 'oldposition'));
    $template->assign($block);
    $template->assign('block', $block); // für das Menu...

    include('header.php');
    // title(_EDITBLOCK);
    $template->display('blocksedit.html');
    include('footer.php');
}

function Blocksave()
{
    global $prefix;

    $defaults = array(/* Standardwerte für nicht gesetzte Feldwerte */
        'bid' => 0,
        'active' => 0,
        'weight' => 0,
        'view' => 0,
        'refresh' => 0,
        'url' => '',
        'blockfile' => '',
        'blanguage' => '',
        'b_content' => '',
        'title' => '',
        'position' => 0,
        'groups' => array(),
        'headline' => 0,
        'module' => '',
        );

    $block = array_merge($defaults, $_POST);
    $block['content'] = $block['b_content']; // Feld ist wegen #id umbenannt

    switch (true) {
        case $block['op'] == PMX_MODULE . '/EditSave' && empty($block['menu']):
            $location = adminUrl(PMX_MODULE, 'Edit', array('bid' => $block['bid']));
            break;
        case $block['op'] == PMX_MODULE . '/EditSave':
            $location = adminUrl('menu');
            break;
        default:
            $location = adminUrl(PMX_MODULE);
    }

    if (!empty($block['headline'])) {
        $result = sql_query("SELECT sitename, headlinesurl FROM " . $prefix . "_headlines WHERE hid='" . $block['headline'] . "'");
        list ($xtitle, $block['url']) = sql_fetch_row($result);
        if (empty($block['title'])) $block['title'] = $xtitle;
    }

    if (!empty($block['url']) && empty($block['blockfile'])) {
        $block['content'] = mxGetRssContent($block['url']);
        switch (true) {
            case strpos($block['content'], _XMLERROROCCURED) === 0:
                return mxRedirect($location, '<b>' . _RSSFAIL . '</b><br /><br />' . $block['content']);
            case empty($block['content']):
                return mxRedirect($location, '<b>' . _RSSFAIL . '</b><br /><br />' . _RSSTRYAGAIN);
        }
    }

    /* Den Modulnamen vom Dateinamen trennen */
    if ($block['blockfile']) {
        list($block['module'], $block['blockfile']) = explode(MXBLOCKFILENAMEDELIMITER, $block['blockfile']);
    }

    $block = mxAddSlashesForSQL($block);

    if ($block['op'] == PMX_MODULE . '/EditSave') {
        $qry = "UPDATE ${prefix}_blocks SET
                        title='" . $block['title'] . "',
                        module='" . $block['module'] . "',
                        blockfile='" . $block['blockfile'] . "',
                        content='" . $block['content'] . "',
                        url='" . mx_urltohtml($block['url']) . "',
                        position='" . $block['position'] . "',
                        weight=" . $block['weight'] . ",
                        active=" . $block['active'] . ",
                        refresh=" . $block['refresh'] . ",
                        blanguage='" . $block['blanguage'] . "',
                        view=" . $block['view'] . "
                        WHERE bid=" . $block['bid'];
        if (!sql_query($qry)) {
            return mxRedirect($location, _ADMIN_SETTINGNOSAVED);
        }
        include_once(PMX_SYSTEM_DIR . DS . 'mx_reset.php');
        resetBlockCache($block['bid']);
    } else {
        $qry = "INSERT INTO ${prefix}_blocks
        (title, content, url, position, weight, active, refresh, time, blanguage, blockfile, module, view) VALUES
        ('" . $block['title'] . "','" . $block['content'] . "','" . $block['url'] . "','" . $block['position'] . "'," . $block['weight'] . "," . $block['active'] . "," . $block['refresh'] . ",0,'" . $block['blanguage'] . "','" . $block['blockfile'] . "','" . $block['module'] . "'," . $block['view'] . ")";
        if (!sql_query($qry)) {
            return mxRedirect($location, _ADMIN_SETTINGNOSAVED);
        }
        $block['bid'] = sql_insert_id();
    }

    /* Reihenfolge korrigieren */
    repairweigth();

    /* Gruppen aktualisieren */
    $userconfig = load_class('Userconfig');
    sql_query("DELETE FROM " . $prefix . "_groups_blocks WHERE block_id IN(0," . intval($block['bid']) . ")");
    if ($block['view'] == 1) {
        // Sicherstellen, dass wirklich eine Gruppe selektiert wurde
        if (empty($block['groups'])) {
            $block['groups'] = array(intval($userconfig->default_group));
        } elseif (!is_array($block['groups'])) {
            $block['groups'] = array(intval($block['groups']));
        }
        if (empty($block['groups'][0])) {
            $block['groups'] = array(intval($userconfig->default_group));
        }
        foreach ($block['groups'] as $groupid) {
            sql_query("INSERT INTO " . $prefix . "_groups_blocks (group_id, block_id) VALUES (" . intval($groupid) . ", " . intval($block['bid']) . ")");
        }
    }
    /* RSS-Daten an Headlines anfuegen */
    if (empty($block['headline']) && !empty($block['url']) && !empty($block['content']) && !empty($block['title'])) {
        $result2 = sql_query("SELECT headlinesurl FROM " . $prefix . "_headlines WHERE headlinesurl='" . $block['url'] . "' OR sitename='" . $block['title'] . "'");
        list ($xurl) = sql_fetch_row($result2);
        if (empty($xurl)) {
            sql_query("INSERT INTO " . $prefix . "_headlines (sitename, headlinesurl) VALUES ('" . $block['title'] . "', '" . $block['url'] . "')");
        }
    }

    mxRedirect($location, _ADMIN_SETTINGSAVED);
}

function getActiveSelect($active = 1)
{
    $active = (int)$active;
    $out = '<input type="radio" name="active" value="1"' . (($active == 1) ? ' checked="checked"' : '') . ' />' . _YES . ' &nbsp;&nbsp;';
    $out .= '<input type="radio" name="active" value="0"' . (($active == 0) ? ' checked="checked"' : '') . ' />' . _NO . "\n";
    return $out;
}

function getRefreshSelect($refresh = 0, $shownever = 0)
{
    $refresh = (int)$refresh;
    $out = '<select id="refresh" class="form-control" name="refresh">';
    if ($shownever) {
        $out .= '<option value="0"' . (($refresh == 0) ? ' selected="selected" class="current"' : '') . '>- ' . _NEVER . '</option>';
    }
    $out .= '<option value="300"' . (($refresh == 300) ? ' selected="selected" class="current"' : '') . '>5&nbsp;&nbsp; ' . _MINUTES . '</option>'
     . '<option value="600"' . (($refresh == 600) ? ' selected="selected" class="current"' : '') . '>10 ' . _MINUTES . '</option>'
     . '<option value="1200"' . (($refresh == 1200) ? ' selected="selected" class="current"' : '') . '>20 ' . _MINUTES . '</option>'
     . '<option value="1800"' . (($refresh == 1800) ? ' selected="selected" class="current"' : '') . '>30 ' . _MINUTES . '</option>'
     . '<option value="3600"' . (($refresh == 3600) ? ' selected="selected" class="current"' : '') . '>1&nbsp;&nbsp; ' . _HOUR . '</option>'
     . '<option value="18000"' . (($refresh == 18000) ? ' selected="selected" class="current"' : '') . '>5&nbsp;&nbsp; ' . _HOURS . '</option>'
     . '<option value="36000"' . (($refresh == 36000) ? ' selected="selected" class="current"' : '') . '>10&nbsp; ' . _HOURS . '</option>'
     . '<option value="86400"' . (($refresh == 86400) ? ' selected="selected" class="current"' : '') . '>24&nbsp; ' . _HOURS . '</option>'
     . '<option value="172800"' . (($refresh == 172800) ? ' selected="selected" class="current"' : '') . '>48&nbsp; ' . _HOURS . '</option>'
     . '</select>';
    return $out;
}

function getRefreshTimeString($refresh)
{
    switch (intval($refresh)) {
        case 300 :
            return '5 ' . _MINUTES;
        case 600 :
            return '10 ' . _MINUTES;
        case 1200 :
            return '20 ' . _MINUTES;
        case 1800 :
            return '30 ' . _MINUTES;
        case 3600 :
            return '1 ' . _HOUR;
        case 18000 :
            return '5 ' . _HOURS;
        case 36000 :
            return '10 ' . _HOURS;
        case 86400 :
            return '24 ' . _HOURS;
        case 172800 :
            return '48 ' . _HOURS;
        default :
            return '';
    }
}

function getViewSelect($view = 0)
{
    $view = (int)$view;
    $out = '<select id="view" class="form-control" name="view">'
     . '<option value="0"' . (($view == 0) ? ' selected="selected" class="current"' : '') . '>' . _MVALL . '</option>'
     . '<option value="1"' . (($view == 1) ? ' selected="selected" class="current"' : '') . '>' . _MVGROUPS . '</option>'
     . '<option value="2"' . (($view == 2) ? ' selected="selected" class="current"' : '') . '>' . _MVADMIN . '</option>'
     . '<option value="4"' . (($view == 4) ? ' selected="selected" class="current"' : '') . '>' . _MVSYSADMIN . '</option>'
     . '<option value="3"' . (($view == 3) ? ' selected="selected" class="current"' : '') . '>' . _MVANON . '</option>'
     . '</select>';
    return $out;
}

function getGroupSelect($blockid = 0)
{
    global $prefix;

    $userconfig = load_class('Userconfig');

    $blockid = (int)$blockid;
    if (!empty($blockid)) {
        $qry = "SELECT group_id FROM ${prefix}_groups_blocks WHERE block_id=" . $blockid;
        $result = sql_query($qry);
        while (list($group_id) = sql_fetch_row($result)) {
            $groups[] = $group_id;
        }
    }
    $groups = (empty($groups)) ? $userconfig->default_group : $groups;
    $groupoptions = getAllAccessLevelSelectOptions($groups);
    $cnt = substr_count($groupoptions, '<option') + 1;
    $out = '<select class="form-control" name="groups[]" size="' . $cnt . '" multiple="multiple">' . $groupoptions . '</select>';
    return $out;
}

function getPositionSelect($pos = '')
{
    $out = '<select id="position" class="form-control" name="position">'
     . '<option value="l"' . (($pos == 'l') ? ' selected="selected" class="current"' : '') . '>' . _LEFT . '</option>'
     . '<option value="c"' . (($pos == 'c') ? ' selected="selected" class="current"' : '') . '>' . _CENTERUP . '</option>'
     . '<option value="d"' . (($pos == 'd') ? ' selected="selected" class="current"' : '') . '>' . _CENTERDOWN . '</option>'
     . '<option value="r"' . (($pos == 'r') ? ' selected="selected" class="current"' : '') . '>' . _RIGHT . '</option>'
     . '</select>';
    return $out;
}

function getheadlineselect ($url = '')
{
    global $prefix;
    $res = sql_query("SELECT hid, sitename, headlinesurl FROM " . $prefix . "_headlines ORDER BY sitename");

    /* zur Sicherheit, die Entities richtigstellen */
    $url = mx_urltohtml($url);

    $options[] = '<option value="0" selected="selected">' . _CUSTOM . '</option>';
    while (list($hid, $htitle, $hurl) = sql_fetch_row($res)) {
        $options[] = '<option value="' . $hid . '"' . (($url == $hurl) ? ' selected="selected" class="current"' : '') . '>' . $htitle . '</option>';
    }

    $template = load_class('Template');
    $template->init_path(__FILE__);
    $template->assign(compact('url', 'options'));

    return $template->fetch('getheadlineselect.html');
}

function block_editmenue($block, $optitle = '', $menu = 0)
{
    $menu = (empty($menu)) ? 0 : 1;
    $optitle = (empty($optitle)) ? _BLOCKSADMIN : $optitle;
    $active = ($block['active']) ?_DEACTIVATE : _ACTIVATE;

    switch ($menu) {
        case 1:
            $link[] = '<a class="btn btn-sm btn-primary" href="' . adminUrl(PMX_MODULE, 'ChangeStatus', 'bid=' . $block['bid'] . '&amp;menu=1') . '">' . $active . '</a>';
            $link[] = '<a class="btn btn-sm btn-warning" href="' . adminUrl(PMX_MODULE, 'Edit', 'bid=' . $block['bid'] . '&amp;menu=1') . '">' . _EDIT . '</a>';

            if (mxIsNewsBlockfile($block['module'], $block['blockfile'])) {
                $link[] = _DELETE;
            } else {
                $link[] = '<a class="btn btn-sm btn-danger" href="' . adminUrl(PMX_MODULE, 'Delete', 'bid=' . $block['bid'] . '&amp;menu=1') . '">' . _DELETE . '</a>';
            }
            break;

        default:
            $link[] = '<a class="btn btn-sm btn-primary" href="' . adminUrl(PMX_MODULE, 'ChangeStatus', 'bid=' . $block['bid']) . '">' . $active . '</a>';
            $link[] = '<a class="btn btn-sm btn-warning" href="' . adminUrl(PMX_MODULE, 'Edit', 'bid=' . $block['bid']) . '">' . _EDIT . '</a>';

            if (mxIsNewsBlockfile($block['module'], $block['blockfile'])) {
                $link[] = _DELETE;
            } else {
                $link[] = '<a class="btn btn-sm btn-danger" href="' . adminUrl(PMX_MODULE, 'Delete', 'bid=' . $block['bid']) . '">' . _DELETE . '</a>';
            }
            break;
    }

    $link[] = '<a class="btn btn-sm btn-info" href="' . adminUrl(PMX_MODULE) . '">' . _BLOCKSADMIN . '</a>';

    $template = load_class('Template');
    $template->init_path(__FILE__);
    $template->assign('items', $link);

    return $template->fetch('blockeditmenue.html');
}

function block_show()
{
    global $prefix;
    $bid = (empty($_GET['bid'])) ? 0 : intval($_GET['bid']);
    $result = sql_query("SELECT * FROM ${prefix}_blocks WHERE bid=" . $bid);
    $block = sql_fetch_assoc($result);

    $template = load_class('Template');
    $template->init_path(__FILE__);
    $template->assign('block', $block);

    include('header.php');
    // title(_BLOCKSADMIN);
    $template->display('blockshow.html');
    include('footer.php');
}

function showpreview($block)
{
    if (mxIsNewsBlockfile($block['module'], $block['blockfile'])) {
        global $prefix;
        $result = sql_query("SELECT * FROM ${prefix}_stories LIMIT 0,1");
        $GLOBALS['story_blocks'] = sql_fetch_assoc($result);
        $GLOBALS['name'] = 'News';
        @mxGetLangfile('News');
    }

    $block = mxGetBlockData($block, 1); // ohne cache

    $template = load_class('Template');
    $template->init_path(__FILE__);
    $template->assign('block', $block);

    return $template->fetch('showpreview.html');
}

function fixweight()
{
    repairweigth();
    mxRedirect(adminUrl(PMX_MODULE), _ADMIN_SETTINGSAVED);
    exit;
}

function repairweigth()
{
    global $prefix;
    $newweight = 0;
    $lastpos = 'xyz';
    $qry = "SELECT position, bid, weight
            FROM ${prefix}_blocks
            ORDER BY position, weight, bid";
    $result = sql_query($qry);
    while (list($pos, $bid, $weight) = sql_fetch_row($result)) {
        if ($lastpos != $pos) {
            $lastpos = $pos;
            $newweight = 0;
        }
        $newweight++;
        if ($newweight != $weight) {
            $qry = "UPDATE ${prefix}_blocks SET weight = $newweight WHERE bid = $bid";
            sql_query($qry);
        }
    }
}

function BlockOrder ()
{
    global $prefix;
    extract($_GET);
    $result = sql_query("UPDATE ${prefix}_blocks SET weight = " . intval($weight) . " WHERE bid = " . intval($bidrep));
    $result2 = sql_query("UPDATE ${prefix}_blocks SET weight = " . intval($weightrep) . " WHERE bid = " . intval($bidori));
    mxRedirect(adminUrl(PMX_MODULE), _ADMIN_SETTINGSAVED);
}

function ChangeStatus()
{
    global $prefix;
    $ok = (empty($_GET['ok'])) ? 0 : 1;
    $bid = (empty($_GET['bid'])) ? 0 : intval($_GET['bid']);
    $menu = (empty($_GET['menu'])) ? 0 : 1;
    $result = sql_query("SELECT * FROM ${prefix}_blocks WHERE bid = $bid");
    $block = sql_fetch_assoc($result);
    extract ($block);
    if (($ok) OR ($block['active'] == 1)) {
        $active = (empty($block['active'])) ? 1 : 0;
        $result = sql_query("UPDATE ${prefix}_blocks SET active = '$active' WHERE bid = $bid");
        switch ($menu) {
            case 1:
                mxRedirect(adminUrl('menu'), _ADMIN_SETTINGSAVED);
                break;

            default:
                mxRedirect(adminUrl(PMX_MODULE), _ADMIN_SETTINGSAVED);
                break;
        }
    } else {
        switch ($menu) {
            case 1:
                $links[] = '<a class="btn btn-primary" href="' . adminUrl('menu') . '"><i class="fa fa-ban fa-lg"></i> ' . _NO . '</a>';
                $links[] = '<a class="btn btn-primary" href="' . adminUrl(PMX_MODULE, 'ChangeStatus', 'bid=' . $bid . '&amp;ok=1&amp;menu=1') . '"><i class="fa fa-check fa-lg"></i> ' . _YES . '</a>';
                break;

            default:
                $links[] = '<a class="btn btn-primary" href="' . adminUrl(PMX_MODULE) . '"><i class="fa fa-ban fa-lg"></i> ' . _NO . '</a>';
                $links[] = '<a class="btn btn-primary" href="' . adminUrl(PMX_MODULE, 'ChangeStatus', 'bid=' . $bid . '&amp;ok=1') . '"><i class="fa fa-check fa-lg"></i> ' . _YES . '</a>';
                break;
        }
        $template = load_class('Template');
        $template->init_path(__FILE__);
        $template->assign(compact('menu', 'links', 'block'));

        include('header.php');
        // title(_BLOCKACTIVATION);
        $template->display('changestatus.html');
        include('footer.php');
    }
}

function BlocksDelete()
{
    global $prefix;
    $ok = (empty($_GET['ok'])) ? 0 : 1;
    $bid = (empty($_GET['bid'])) ? 0 : intval($_GET['bid']);
    $menu = (empty($_GET['menu'])) ? 0 : 1;
    if ($ok) {
        sql_query("DELETE FROM ${prefix}_blocks WHERE bid = $bid");
        repairweigth();
        switch ($menu) {
            case 1:
                mxRedirect(adminUrl('menu'), _ADMIN_SETTINGSAVED);
                break;

            default:
                mxRedirect(adminUrl(PMX_MODULE), _ADMIN_SETTINGSAVED);
                break;
        }
    }
    $result = sql_query("SELECT * FROM ${prefix}_blocks WHERE bid = $bid");
    $block = sql_fetch_assoc($result);

    switch ($menu) {
        case 1:
            $links[] = '<a class="btn btn-primary" href="' . adminUrl('menu') . '"><i class="fa fa-ban fa-lg"></i> ' . _NO . '</a>';
            $links[] = '<a class="btn btn-primary" href="' . adminUrl(PMX_MODULE, 'Delete', 'bid=' . $bid . '&amp;ok=1&amp;menu=1') . '"><i class="fa fa-check fa-lg"></i> ' . _YES . '</a>';
            break;
        default:
            $links[] = '<a class="btn btn-primary" href="' . adminUrl(PMX_MODULE) . '"><i class="fa fa-ban fa-lg"></i> ' . _NO . '</a>';
            $links[] = '<a class="btn btn-primary" href="' . adminUrl(PMX_MODULE, 'Delete', 'bid=' . $bid . '&amp;ok=1') . '"><i class="fa fa-check fa-lg"></i> ' . _YES . '</a>';
            break;
    }

    $template = load_class('Template');
    $template->init_path(__FILE__);
    $template->assign(compact('menu', 'links', 'block'));

    include('header.php');
    // title(_BLOCKSADMIN);
    $template->display('blocksdelete.html');
    include('footer.php');
}

function HeadlinesAdmin()
{
    global $prefix;

    $result = sql_query("SELECT hid, sitename, headlinesurl FROM " . $prefix . "_headlines ORDER BY sitename");
    while ($row = sql_fetch_assoc($result)) {
        $row['headlinesurl'] = mx_urltohtml($row['headlinesurl']);
        $items[] = $row;
    }
    $template = load_class('Template');
    $template->init_path(__FILE__);
    $template->assign('items', $items);

    include('header.php');
    // title(_HEADLINESADMIN);
    $template->display('headlinesadmin.html');
    include('footer.php');
}

function HeadlinesEdit()
{
    global $prefix;
    $hid = (empty($_GET['hid'])) ? 0 : intval($_GET['hid']);
    $result = sql_query("SELECT hid, sitename, headlinesurl FROM " . $prefix . "_headlines WHERE hid=" . $hid);
    $row = sql_fetch_assoc($result);

    $template = load_class('Template');
    $template->init_path(__FILE__);
    $template->assign($row);

    include('header.php');
    // title(_HEADLINESADMIN);
    $template->display('headlinesedit.html');
    include('footer.php');
}

function HeadlinesSave($pvs)
{
    global $prefix;
    $hid = $pvs['hid'];
    // $xsitename = str_replace(" ", "", $xsitename);
    sql_query("UPDATE " . $prefix . "_headlines SET sitename='" . mxAddSlashesForSQL($pvs['xsitename']) . "', headlinesurl='" . mxAddSlashesForSQL(mx_urltohtml($pvs['headlinesurl'])) . "' WHERE hid='" . $hid . "'");
    mxRedirect(adminUrl(PMX_MODULE, 'HeadlinesAdmin'), _ADMIN_SETTINGSAVED);
}

function HeadlinesAdd($pvs)
{
    global $prefix;
    sql_query("INSERT INTO " . $prefix . "_headlines (sitename, headlinesurl) VALUES ('" . mxAddSlashesForSQL($pvs['xsitename']) . "', '" . mxAddSlashesForSQL($pvs['headlinesurl']) . "')");
    mxRedirect(adminUrl(PMX_MODULE, 'HeadlinesAdmin'), _ADMIN_SETTINGSAVED);
}

function HeadlinesDel()
{
    global $prefix;
    $ok = (empty($_GET['ok'])) ? 0 : 1;
    $hid = (empty($_GET['hid'])) ? 0 : intval($_GET['hid']);
    if ($ok) {
        sql_query("DELETE FROM " . $prefix . "_headlines WHERE hid=" . $hid);
        mxRedirect(adminUrl(PMX_MODULE, 'HeadlinesAdmin'), _ADMIN_SETTINGSAVED);
    } else {
        $template = load_class('Template');
        $template->init_path(__FILE__);
        $template->assign('hid', $hid);

        include('header.php');
        // title(_HEADLINESADMIN);
        $template->display('headlinesdel.html');
        include('footer.php');
    }
}

function getblockfileselect($module = '', $blockfile = '')
{
    global $prefix;

    $current = ($module || $blockfile) ? sprintf(MXBLOCKFILENAMEFORMATER, $module, $blockfile) : '';

    $db_blocks = array();
    $mod_blocks = array();
    $options = array();

    $result = sql_query("SELECT module, blockfile FROM ${prefix}_blocks WHERE blockfile<>''");
    while (list($module, $blockfile) = sql_fetch_row($result)) {
        $db_blocks[] = sprintf(MXBLOCKFILENAMEFORMATER, $module, $blockfile);
    }

    if (!$current) {
        $options[] = '<option value="" selected="selected">&nbsp;</option>';
    }

    $files = (array)glob(PMX_BLOCKS_DIR . DS . 'block-*.php', GLOB_NOSORT);
    foreach ($files as $filename) {
        $blockfile = basename($filename);
        $key = sprintf(MXBLOCKFILENAMEFORMATER, '', $blockfile);
        $blockname = substr(substr($blockfile, 6), 0, -4);
        $sel = ($current == $key) ? ' selected="selected"' : '';
        $inst = (in_array($key, $db_blocks)) ? ' *' : '';
        $options[] = '<option value="' . $key . '"' . $sel . '>' . $blockname . $inst . '</option>';
        // if (!(in_array($key, $db_blocks))) {
        // /* Falls ein neuer Block gefunden wurde, diesen automatisch in die Tabelle einfuegen */
        // $blockname = substr(str_replace(MX_NEWSBLOCK_PREFIX, '', $blockfile), 0, -4);
        // $module = '';
        // $qry = "INSERT INTO `${prefix}_blocks` (`bid`, `bkey`, `title`, `content`, `url`, `position`, `weight`, `active`, `refresh`, `time`, `blanguage`, `blockfile`, `module`, `view`) VALUES (NULL, '', '" . $blockname . "', NULL, '', 'c', 0, 1, 0, 0, '', '" . $blockfile . "', '" . $module . "', 0);";
        // sql_query($qry);
        // }
    }
    natcasesort($options);

    $files = (array)glob(PMX_MODULES_DIR . DS . '*' . DS . 'blocks' . DS . 'block-*.php', GLOB_NOSORT);
    foreach ($files as $filename) {
        $module = basename(dirname(dirname($filename)));
        $blockfile = basename($filename);
        $key = sprintf(MXBLOCKFILENAMEFORMATER, $module, $blockfile);
        // if (!(in_array($key, $db_blocks))) {
        // /* Falls ein neuer Block gefunden wurde, diesen automatisch in die Tabelle einfuegen */
        // $blockname = substr(str_replace(MX_NEWSBLOCK_PREFIX, '', $blockfile), 0, -4);
        // $qry = "INSERT INTO `${prefix}_blocks` (`bid`, `bkey`, `title`, `content`, `url`, `position`, `weight`, `active`, `refresh`, `time`, `blanguage`, `blockfile`, `module`, `view`) VALUES (NULL, '', '" . $blockname . "', NULL, '', 'c', 0, 1, 0, 0, '', '" . $blockfile . "', '" . $module . "', 0);";
        // sql_query($qry);
        // $qry = "DELETE FROM `${prefix}_blocks` WHERE module IN ('Contest','Forum','Gallery','MailNews','SiriusGallery','Schedule')";
        // sql_query($qry);
        // }
        if (mxIsNewsBlockfile($module, $blockfile)) {
            if (!(in_array($key, $db_blocks))) {
                /* Falls ein neuer News-Block gefunden wurde, diesen automatisch in die Tabelle einfuegen */
                $blockname = substr(str_replace(MX_NEWSBLOCK_PREFIX, '', $blockfile), 0, -4);
                $qry = "INSERT INTO `${prefix}_blocks` (`bid`, `bkey`, `title`, `content`, `url`, `position`, `weight`, `active`, `refresh`, `time`, `blanguage`, `blockfile`, `module`, `view`) VALUES (NULL, '', '" . $blockname . "', NULL, '', 'r', 0, 0, 0, 0, '', '" . $blockfile . "', '" . $module . "', 0);";
                sql_query($qry);
            }
        } else {
            $mod_blocks[$module][$key] = substr(substr($blockfile, 6), 0, -4);
        }
    }

    ksort($mod_blocks);
    foreach ($mod_blocks as $module => $blocks) {
        natcasesort($blocks);
        $options[] = '<optgroup label="' . $module . '">'; # disabled="disabled"
        foreach ($blocks as $key => $blockname) {
            $sel = ($current == $key) ? ' selected="selected"' : '';
            $inst = (in_array($key, $db_blocks)) ? ' *' : '';
            $options[] = '<option value="' . $key . '"' . $sel . '>' . $module . ' &gt; ' . $blockname . $inst . '</option>';
        }
        $options[] = '</optgroup>';
    }

    $template = load_class('Template');
    $template->init_path(__FILE__);
    $template->assign('options', $options);

    return $template->fetch('blockfileselect.html');
}
// ##################################################
/**
 * Kurzbeschreibung
 *
 * diese Funktion versucht den bKey zu fixen, falls nicht vorhanden
 * z.B. durch Installationsroutinen von Fremdmodulen
 *
 * @param string $username
 * @param string $password
 * @tables users, permissions
 * @author Andi
 */
// function blockrepairbkey($block)
// {
// global $prefix;
// if (!empty($block['blockfile'])) {
// $block['bkey'] = SYS_BLOCKTYPE_FILE;
// $block['typecaption'] = str_replace(".php", "", str_replace("block-", "", $block['blockfile'])) . " (" . _BLOCKFILE2 . ")";
// } else if (!empty($block['url'])) {
// $block['bkey'] = SYS_BLOCKTYPE_RDF;
// $block['typecaption'] = $block['typecaption'] = _RSSCONTENT;
// } else {
// $block['bkey'] = SYS_BLOCKTYPE_HTML;
// $block['typecaption'] = $block['typecaption'] = "HTML";
// }
// sql_query("UPDATE ${prefix}_blocks SET bkey='" . $block['bkey'] . "' WHERE bid=" . $block['bid']);
// return $block;
// }
// ##############################################
switch ($op) {
    case PMX_MODULE . '/Add':
        Blocksave();
        break;

    case PMX_MODULE . '/Edit':
        BlocksEdit();
        break;

    case PMX_MODULE . '/EditSave':
        Blocksave();
        break;

    case PMX_MODULE . '/ChangeStatus':
        ChangeStatus();
        break;

    case PMX_MODULE . '/Delete':
        BlocksDelete();
        break;

    case PMX_MODULE . '/BlockOrder':
        BlockOrder ();
        break;

    case PMX_MODULE . '/show':
        block_show();
        break;

    case PMX_MODULE . '/fixweight':
        fixweight();
        break;

    case PMX_MODULE . '/ListWeigth':
        BlocksListWeigth($_POST);
        break;

    case PMX_MODULE . '/HeadlinesDel':
        HeadlinesDel();
        break;

    case PMX_MODULE . '/HeadlinesAdd':
        HeadlinesAdd($_POST);
        break;

    case PMX_MODULE . '/HeadlinesSave':
        HeadlinesSave($_POST);
        break;

    case PMX_MODULE . '/HeadlinesAdmin':
        HeadlinesAdmin();
        break;

    case PMX_MODULE . '/HeadlinesEdit':
        HeadlinesEdit();
        break;

    default:
        BlocksAdmin();
        break;
}

?>