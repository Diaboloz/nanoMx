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
 * $Revision: 96 $
 * $Author: PragmaMx $
 * $Date: 2015-11-16 11:24:35 +0100 (Mo, 16. Nov 2015) $
 */

defined('mxMainFileLoaded') or die('access denied');

/**
 * mxGetAllBlocks()
 *
 * @param mixed $side
 * @return
 */
function mxGetAllBlocks($side = false)
{
    global $prefix;
    static $allblocks; // statisches Array
    if (!isset($allblocks)) { // wenn statisches Array noch nicht initialisiert
        $allblocks = array(); // statisches Array initialisieren

        /* Bedingung für sql-Abfrage initialisieren */
        $where = "(active=1) ";

        /* Spracheinstellung für Abfrage */
        $where .= pmx_multilang_query('blanguage', 'AND');

        /* rechte Blöcke von der Abfrage ausschliessen, wenn $index = 0 */
        $where .= ($GLOBALS['index']) ? '' : " AND (position <> 'r')";

        /* linke Blöcke von der Abfrage ausschliessen, wenn $hide_left = 0 */
        // TODO: das ist Themespezifisch und funzt nicht überall...
        $where .= (empty($GLOBALS['hide_left'])) ? '' : " AND (position <> 'l')";


          if (!defined('MX_HOME_FILE')) {
            /* Centerblöcke von der Abfrage ausschliessen, wenn nicht auf Startseite */
              $where .= " AND (position NOT IN('c','d'))";
         }

        /* die rechten News-Artikel Blöcke in der Abfrage extra behandeln */
        if (empty($GLOBALS['story_blocks'])) {
            $where .= " AND NOT (position='r' AND blockfile LIKE('" . MX_NEWSBLOCK_PREFIX . "%') AND module='News')";
        } else {
            $where .= " AND NOT (position='r' AND (blockfile NOT LIKE('" . MX_NEWSBLOCK_PREFIX . "%')) AND module<>'News')";
        }

        /* die Blockberechtigungen feststellen und in ein array für die Abfrage stellen */
        // für alle Besucher
        $view[] = "(view = 0)";
        // Nur Administratoren
        if (MX_IS_ADMIN) {
            $view[] = "(view = 2)";
            // Nur System-Administratoren
            if (mxGetAdminPref('radminsuper')) {
                $view[] = "(view = 4)";
            }
        }
        // nur für eingeloggte User
        if (MX_IS_USER) {
            $userinfo = mxGetUserData(); // gesamte Userdaten in Array lesen
            $qry = "SELECT block_id FROM ${prefix}_groups_blocks WHERE (group_id=" . intval($userinfo['user_ingroup']) . ")";
            $result = sql_system_query($qry);
            $bids = array();
            while (list($bid) = sql_fetch_row($result)) {
                $bids[] = $bid;
            }
            if ($bids) {
                $view[] = "(view = 1 AND bid in(" . implode(",", $bids) . "))";
            }
        } else {
            // Nur Anonyme Benutzer
            $view[] = "(view = 3)";
        }

        /* aus dem array mit Berechtigungen eine Abfragebedingung konstruieren */
        $where .= " AND (" . implode(" OR ", $view) . ")";

        /* die aus den Bedingungen zusammengesetzte SQL-Abfrage */
        $qry = "SELECT * FROM ${prefix}_blocks
                  WHERE $where
                  ORDER BY position, weight ASC";

        $result = sql_system_query($qry);

        $lastpos = "";
        $i = 0;

        /* Schleife, alle Blockdaten in array lesen */
        while ($block = sql_fetch_assoc($result)) {
            // if ($block['position'] == 'r') {
            // mxDebugFuncVars($block['module'] . ' > ' . $block['blockfile']);
            // }
            // den Inhalt und weitere Daten aus dem includeten blockfile bzw. cache  holen
            $block = mxGetBlockData($block);
            // wenn kein Blockinhalt, Schleife fortsetzen
            if (empty($block['content'])) {
                continue;
            }
            // wenn eine andere Position, den Zähler der Reihenfolge zurücksetzen
            if ($block['position'] != $lastpos) {
                $lastpos = $block['position'];
                // den Zähler für diese Position zurücksetzen
                if ($block['position'] == "c") {
                    // die Mitteilungen bei den oberen Centerblöcken mitzählen
                    $i = 0; //rem nanomx     $i = count($allmessages);
                } 
            }
            // Arrayschlüssel 'order' mit diesem Wert belegen
            $block['order'] = $i;
            // Wenn der Titel als Sprachkonstante angegeben ist, diese verwenden
            $block['title'] = mxTranslate($block['title']);
            // jetzt endlich das statische Array mit den Blockdaten füllen
            $allblocks[$block['position']][$i] = $block;
            // den Zähler der Reihenfolge erhöhen
            $i++;
        }
        // die Messages in die CenterBlöcke einschleifen
        
// rem nanomx        if (count($allmessages)) {
//            $allblocks['c'] = (isset($allblocks['c'])) ? array_merge($allmessages, $allblocks['c']) : $allmessages;
//        }
    }

    switch (true) {
        case $side === false && isset($allblocks):
            // keine Seite angegeben, alle Blöcke zurückgeben
            return $allblocks;
            break;
        case isset($allblocks[$side]):
            // wenn Blöcke für diese Seite vorhanden, diese als Array zurückgeben
            return $allblocks[$side];
            break;
        default:
            // ansonsten ein leeres Array zurückgeben
            return array();
    }
}

/**
 * Stellt die Blockdaten zusammen
 *
 * @param array $block
 * @param bool $nocache optional, default value false
 * @return array
 */
function mxGetBlockData($block, $nocache = false)
{
    global $prefix;

    /* Wenn der Blocktitel als Sprachkonstante angegeben ist, diese verwenden */
    $block['title'] = mxTranslate($block['title']);

    switch (true) {
        case !empty($block['url']):
            // RSS-Block
            $past = time() - $block['refresh'];
            if ($block['time'] < $past) {
                $block['content'] = mxGetRssContent($block['url']);
                $result = sql_system_query("update ${prefix}_blocks set content='" . mxAddSlashesForSQL($block['content']) . "', time='" . time() . "' where bid='" . $block['bid'] . "'");
                // TODO: stimmt das so, mit dem Admin?
                if (MX_IS_ADMIN && (empty($block['content']) || !$result)) {
                    $block['content'] = '<div class="warning">' . _RSSPROBLEM . '<br /><a href="' . $block['url'] . '" target="_blank">' . mxCutString($block['url'], 40, '..', '/') . '</a></div>';
                }
            }
            return $block;

        case $block['module'] && $block['blockfile'] && !mxModuleAllowed($block['module']):
            $block['content'] = '';
            $block['active'] = false;
            return $block;

        case $block['blockfile'] && $block['refresh'] && !$nocache:
            // Datei-Block, cacheable
            $block = mxCacheBlocks($block);
            return $block;

        case $block['blockfile']:
            // Datei-Block, not cacheable
            $block = mxIncludeBlock($block);
            return $block;

        default:
            // sonst HTML-Block
            return $block;
    }
}

/**
 * mxCacheBlocks()
 *
 * @param array $block
 * @return array $block
 */
function mxCacheBlocks($block)
{
    global $prefix;
    $lang = $GLOBALS['currentlang'];

    if (!$block['refresh']) {
        return $block;
    }

    $data = array();
    if ($block['content']) {
        pmxDebug::pause();
        $data = (array)unserialize(base64_decode($block['content']));
        pmxDebug::restore();
    }

    $past = time() - $block['refresh'];
    $must_cache = ($block['time'] < $past) || !isset($data[$lang]);
    // $must_cache = true;
    if ($must_cache) {
        $block['cacheable'] = true; // wird in mxIncludeBlock() evtl. wieder geändert
        // blockfile includen
        $block = mxIncludeBlock($block);

        if (!$block['cacheable']) {
            // wenn der Block nicht gecached werden darf oder caching nicht aktiviert
            // den Cache für die aktuelle Sprache löschen, falls vorhanden
            $qry = "UPDATE ${prefix}_blocks SET content='', refresh=0, time=0 WHERE bid=" . intval($block['bid']);
            sql_system_query($qry);
            // das Blockarray mit den Variablen des includeten Blocks direkt ausgeben
            return $block;
        }

        $data[$lang] = array('title' => $block['title'], 'content' => $block['content']);

        $qry = "UPDATE ${prefix}_blocks SET content='" . base64_encode(serialize($data)) . "', time=" . time() . " WHERE bid=" . intval($block['bid']);
        $result = sql_system_query($qry);
    }

    if (isset($data[$lang])) {
        return array_merge($block, $data[$lang]);
    }

    $block['content'] = '';
    // das neu erstellte Blockarray zurückgeben....
    return $block;
}

/**
 * mxIncludeBlock()
 *
 * @param array $block
 * @return array $block
 */
function mxIncludeBlock($block)
{
    /* globals, die im Block auf jeden Fall vorhanden sein müssen */
    global $prefix, $user_prefix;

    $file = '';
    switch (true) {
        case !$block:
        case empty($block['blockfile']):
            // falls falscher Funktionsaufruf...
            return false;
        case (!$block['module']) && ($file = realpath(PMX_BLOCKS_DIR . DS . $block['blockfile']));
            $exist = is_file($file);
            break;
        case !$block['module']:
            $exist = false;
            break;
        case $file = realpath(PMX_MODULES_DIR . DS . $block['module'] . '/blocks/' . $block['blockfile']);
            $exist = is_file($file);
            break;
        default:
            $exist = false;
    }

    $block['cacheable'] = true;
    $block['settings'] = array('before' => '', 'after' => '', 'class' => '');
    $message = '';

    switch (true) {
        case MX_IS_ADMIN && !$exist && $block['module']:
            // wenn blockfile fehlt, bei Admin Fehlermeldung zeigen
            $block['content'] = _BLOCKPROBLEM . '<br />' . PMX_MODULES_PATH . $block['module'] . '/blocks/' . $block['blockfile'];
            return $block;

        case MX_IS_ADMIN && !$exist:
            // wenn blockfile fehlt, bei Admin Fehlermeldung zeigen
            $block['content'] = _BLOCKPROBLEM . '<br />' . $block['blockfile'];
            return $block;

        case !$exist:
            $block['content'] = '';
            return $block;

        case MX_IS_ADMIN && $block['module'] && $block['blockfile'] && !mxModuleActive($block['module']):
            if ('Ephemerids' != $block['module']) {
                // Ephemerids ist kein richtiges Modul, deswegen hier nicht..
                $message = '<div class="warning">' . sprintf(_BLK_MODULENOTACTIVE, $block['module']) . '</div>';
            }
            break;
    }

    $content = '';

    /* eventuelle Fehl-Ausgaben abfangen und zwischenspeichern */
    ob_start();

    /* benutzerdefinierte Konfiguration auswerten */
    $inifile = pathinfo($file);
    $inifile = $inifile['dirname'] . DS . $inifile['filename'] . '.ini';
    if (is_file($inifile)) {
        $block['settings'] = array_merge($block['settings'], parse_ini_file($inifile, true));
    }
	// TODO : Blockeinstellungen aus der DB lesen 
	if (isset($block['config']) and $block['config']!=NULL) {
		$block['config']=unserialize($block['config']);
		$block['settings'] = array_merge($block['settings'],$block['config']);
	}
	/* hier nun blockfile includen */
    include($file);

    /* Ausgabe zusammensetzen */
    $block['content'] = trim(ob_get_clean()) . $message . $block['settings']['before'] . $content . $block['settings']['after'];

    /* Wenn im Blockfile der Titel angegeben war, diesen verwenden */
    if (isset($blockfiletitle)) {
        $block['title'] = $blockfiletitle;
    }

    /* die Variable $mxblockcache (aus dem blockfile) entscheidet, ob der Block gecached werden darf */
    if (isset($mxblockcache) && !$mxblockcache) {
        $block['cacheable'] = false;
    }

    return $block;
}

/**
 * render_blocks()
 *
 * @param mixed $block
 * @return
 */
function render_blocks($block)
{
    if (!isset($block['order'])) {
        $block['order'] = $block['weight'];
    }
    switch (true) {
        case $block['position'] == 'c' && function_exists('thememiddlebox'):
        case $block['position'] == 'd' && function_exists('thememiddlebox'):
            thememiddlebox($block['title'], $block['content'], $block);
            break;
        case $block['position'] == 'c' && function_exists('themecenterbox'):
        case $block['position'] == 'd' && function_exists('themecenterbox'):
            /* kompatibilität zu < nuke-themes */
            themecenterbox($block['title'], $block['content'], $block);
            break;
        case function_exists('themesidebox'):
            themesidebox($block['title'], $block['content'], $block);
            break;
        default:
            if (!empty($block['title'])) {
			 echo "<h2>". $block['title'] ."</h2>";
			}
			if (!empty($block['content'])) {
			  echo "<div class=\"content\">". $block['content'] ."</div><br />";
			}
            break;
    }
}

/**
 * blocks()
 *
 * @param mixed $side
 * @return
 */
function blocks($side)
{
    // exit;
    $side = strtolower($side[0]); // Uebergabeparameter in den ersten Kleinbuchstaben umwandeln
    // mxDebugFuncVars(basename(__FILE__), __FUNCTION__, $side);
    $blocks = mxGetAllBlocks($side); // statisches Blockarray für diese Seite abholen
    // wenn array leer, Funktion beenden
    if (empty($blocks)) {
        return;
    }
    // in Schleife alle Blöcke für diese Seite ausgeben
    foreach ($blocks as $block) {
        render_blocks($block);
    }
}

/**
 * Beschreibung
 *
 * @param string $blockname
 * @return string deprecated !!
 */
function mxIsNewsBlockfile($module, $blockname)
{
    return $module == 'News' && strstr($blockname, MX_NEWSBLOCK_PREFIX);
}

/**
 * mxGetMessages() /// remove for nanomx
 *
 * @return
 */
function mxGetMessages()
{
    global $prefix;
    $qry = "SELECT mid, title, content, `date`, expire, view
        FROM {$prefix}_message
        WHERE active=1 AND content <> '' " . pmx_multilang_query('mlanguage', 'AND') . "
        ORDER BY `date` DESC";
    $result = sql_system_query($qry);

    $i = 0;
    $allmessages = array();
    while (list ($mid, $title, $content, $mdate, $expire, $view) = sql_fetch_row($result)) {
        if (empty($expire)) {
            $remain = _UNLIMITED;
        } else {
            $etime = (($mdate + $expire) - time()) / 3600;
            $etime = (int) $etime;
            if ($etime < 1) {
                $remain = _EXPIRELESSHOUR;
            } else {
                $remain = _EXPIREIN . " $etime " . _HOURS;
            }
        }

        switch ($view) {
            case 4: // nur Admins
                $viewfor = _MVIEWADMIN;
                $showit = MX_IS_ADMIN;
                break;
            case 3: // nur anonyme
                $viewfor = _MVIEWANON;
                $showit = (!MX_IS_USER);
                break;
            case 2: // nur angemeldete User
                $viewfor = _MVIEWUSERS;
                $showit = MX_IS_USER;
                break;
            case 1: // alle
            default:
                $viewfor = _MVIEWALL;
                $showit = true;
                break;
        }

        if ($showit) {
            if ($viewfor && MX_IS_ADMIN) {
                $content .= '<p class="align-right tiny nowrap">[ ' . $viewfor . ' - ' . $remain . ' - <a href="' . adminUrl('messages', 'edit', 'mid=' . $mid) . '">' . _EDIT . '</a> ]</p>';
            }
            $allmessages[$i] = array(/* Daten für Nachricht */
                'bid' => 'm' . $mid,
                'title' => $title,
                'content' => $content,
                'position' => 'c',
                'weight' => $i,
                'order' => $i,
                );
            $i ++;
        }

        if ($expire != 0) {
            $past = time() - $expire;
            if ($mdate < $past) {
                sql_system_query("UPDATE " . $prefix . "_message SET active=0 WHERE mid=" . intval($mid));
            }
        }
    }
    return $allmessages;
}

?>