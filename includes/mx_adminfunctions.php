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

/**
 * GraphicAdmin()
 *
 * @return
 */
function GraphicAdmin()
{
    /* Anzeige verhindern, z.B. wenn Theme eigenes Menü mitbringt */
    if (defined('GRAPHICADMIN')) {
        return;
    }

    $menu = load_class('Adminmenue');
    $oldstyle = (defined('GRAPHICADMINOLDSTYLE') && GRAPHICADMINOLDSTYLE);

    if ($oldstyle) {
        OpenTable();
        echo $menu->graphicadmin();
        CloseTable();
        echo '<br />';
    } else {
        echo $menu->graphicadmin();
    }

    define('GRAPHICADMIN', true);
}

/**
 * getAllUsersSelectOptions2()
 *
 * @param mixed $currentgroup
 * @return
 */
function getAllUsersSelectOptions2($currentgroup)
{
    global $user_prefix, $prefix;
    $useroptions = "";
    $qry = "SELECT uname
            FROM {$user_prefix}_users
                    WHERE user_ingroup=" . $currentgroup . " AND user_stat=1
                    ORDER BY uname";
    $result = sql_query($qry);
    if ($result) {
        while (list($uname) = sql_fetch_row($result)) {
            $useroptions .= "<option value=\"" . $uname . "\">" . $uname . "</option>";
        }
    }
    $useroptions = (empty($useroptions)) ? "<option value=0>ERROR!!! No Users available</option>" : $useroptions;
    return $useroptions;
}

/**
 * getAllAccessLevelSelectOptions()
 *
 * @param mixed $selectedgroup
 * @return
 */
function getAllAccessLevelSelectOptions($selectedgroup)
{
    global $prefix;

    $userconfig = load_class('Userconfig');
    // Sicherstellen, dass wirklich eine Gruppe vorselektiert werden kann
    if (empty($selectedgroup)) {
        $selectedgroup = array(intval($userconfig->default_group));
    } else if (!is_array($selectedgroup)) {
        $selectedgroup = array(intval($selectedgroup));
    }
    if (empty($selectedgroup[0])) {
        $selectedgroup = array(intval($userconfig->default_group));
    }
    // alle Gruppen auslesen
    $groups = getAllAccessGroups(true);
    // die Optionen zusammenstellen
    foreach ($groups as $key => $row) {
        if ($userconfig->default_group == $row['access_id']) {
            $row['access_title'] .= '&nbsp;*';
        }
        $sel = (in_array($row['access_id'], $selectedgroup)) ? ' selected="selected" class="current"' : '';
        $groupoptions[] = '<option value="' . $row['access_id'] . '"' . $sel . '>' . $row['access_title'] . '</option>';
    }
    // wenn Optionen vorhanden, ansonsten Fehler
    if (isset($groupoptions)) {
        return "\n" . implode("\n", $groupoptions) . "\n";
    } else {
        return '<option value="0">ERROR!!! No Groups available</option>';
    }
}

/**
 * pmx_admin_exist_god()
 * pruefen ob ein Super-Administrator-Account existiert
 *
 * @return boolean
 */
function pmx_admin_exist_god()
{
    global $prefix;
    $result = sql_query("SELECT aid FROM {$prefix}_authors WHERE isgod=1 LIMIT 1");
    return sql_num_rows($result);
}

/**
 * pmx_admin_setlogin()
 * Das schreiben der Logindaten in Session und Cookies
 *
 * @param string $aid
 * @param bin $ är/string $pwd
 * @param string $lang >> unnötig
 * @return boolean
 */
function pmx_admin_setlogin($aid, $pwd, $lang = '')
{
    if (!($aid && $pwd)) {
        return false;
    }

    $info = base64_encode($aid . ':' . $pwd . ':' . $lang); // dritter Parameter leer, war admlanguage

    /* das login ;-) */
    mxSessionSetVar('admin', $info);
    //mxSetNukeCookie('admin', $info, 1);
    mxSessionSafeCookie(MX_SAFECOOKIE_NAME_ADMIN, 1);

    return true;
}

/**
 * pmx_admin_casefiles()
 *
 * @return array ()
 */
function pmx_admin_casefiles()
{
    global $aid; // aus: extract(mxGetAdminData());

    $cache = load_class('Cache');
    $cache_id = __FUNCTION__ . $aid;

    $casefiles = array();

    if (($casefiles = $cache->read($cache_id)) !== false) {
        return (array)$casefiles;
    }

    $outdated = array(/* Dateien die zu ignorieren sind */
        // auch die Systemdatei ausschliessen
        'case._system.php',
        'case.adminselffaq.php',
        'case.alllinks.php',
        'case.authors.php',
        'case.authors_access.php',
        'case.avatar.php',
        'case.backup.php',
        'case.banners.php',
        'case.bannersfsz.php',
        'case.blockmaker.php',
        'case.blocks.php',
        'case.blocks_manager.php',
        'case.captcha.php',
        'case.comments.php',
        'case.content.php',
        'case.contentmetakeys.php',
        'case.coppermine.php',
        'case.cportalnews.php',
        'case.download.php',
        'case.downloadconfig.php',
        'case.downloadsmetakeys.php',
        'case.editgroups.php',
        'case.editor.php',
        'case.egallery.php',
        'case.encyclopedia.php',
        'case.ephemerids.php',
        'case.error.php',
        'case.forum.php',
        'case.forums.php',
        'case.groups.php',
        'case.helpsystem.php',
        'case.hip.php',
        'case.images.php',
        'case.intruders.php',
        'case.ipban.php',
        'case.lightbox.php',
        'case.links.php',
        'case.linksconfig.php',
        'case.log.php',
        'case.media.php',
        'case.messages.php',
        'case.metakeys.php',
        'case.moderation.php',
        'case.modules.php',
        'case.mxfeedback.php',
        'case.mx_menu.php',
        'case.ns_contact_plus.php',
        'case.nukebook.php',
        'case.optimize.php',
        'case.phpinfo.php',
        'case.polls.php',
        'case.powernuke.php',
        'case.prettyphoto.php',
        'case.privmsg.php',
        'case.reactivateuser.php',
        'case.recommend.php',
        'case.referenzen.php',
        'case.referers.php',
        'case.reviews.php',
        'case.seclog.php',
        'case.SecLog.php',
        'case.sections.php',
        'case.securelog.php',
        'case.selfadmin.php',
        'case.setban.php',
        'case.settings.php',
        'case.smilies.php',
        'case.sommaire2.php',
        'case.splattforums.php',
        'case.stories.php',
        'case.topics.php',
        'case.tracking.php',
        'case.update.php',
        'case.userguest.php',
        'case.users.php',
        'case.usersconfig.php',
        'case.who.php',
        'case.ya_deleter.php',
        );

    /* alte Module abfragen */
    foreach ((array)glob(PMX_ADMIN_DIR . DS . 'case' . DS . 'case.*.php', GLOB_NOSORT) as $filename) {
        if ($filename && !in_array(basename($filename), $outdated)) {
            $casefiles[substr(basename($filename), 0, -4)] = $filename;
        }
    }

    $cache->write($casefiles, $cache_id, 18000); // 5 Stunden Cachezeit

    return (array)$casefiles;
}

/**
 * pmx_admin_get_infonews()
 *
 * @return
 */
function pmx_admin_get_infonews()
{
    global $show_pragmamx_news_url;

    $url = $show_pragmamx_news_url . '&plain';

    settype($content, 'string');;
    settype($comment, 'string');;

    switch (true) {
        case mxIniGet('allow_url_fopen') && $content = file_get_contents($url):
            $comment = 'file_get_contents';
            break;

        case function_exists('curl_init'):
            // wenn möglich die curl-Biblithek verwenden
            $ch = curl_init();
            $timeout = 20; // set to zero for no timeout
            curl_setopt ($ch, CURLOPT_URL, $url);
            curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $content = curl_exec($ch);
            curl_close($ch);
            $comment = 'curl';
            break;

        case mxIniGet('allow_url_include') && $content = include($url):
            $comment = 'include';
            break;

        case function_exists('fsockopen'):
            $comment = 'fsockopen';
            // ansonsten fsockopen() verwenden
            $errno = '';
            $errstr = '';
            $v_file = parse_url($url);
            $v_file['host'] = strtolower($v_file['host']);
            $fp = fsockopen($v_file['host'], 80, $errno, $errstr, 15);
            if ($fp) {
                if (!isset($v_file['query'])) {
                    $v_file['query'] = "";
                }
                fputs($fp, "GET " . $v_file['path'] . "?" . $v_file['query'] . " HTTP/1.0\r\n");
                fputs($fp, "HOST: " . $v_file['host'] . "\r\n\r\n");

                $go = false;
                while (!feof($fp)) {
                    $pagetext = trim(fgets($fp, 16777216));
                    if (!$pagetext) {
                        $go = true;
                    }
                    if ($go) {
                        $content .= trim($pagetext);
                    }
                }
                fputs($fp, "Connection: close\r\n\r\n");
                fclose($fp);
            }
            break;
    }

    if ($content) {
        $content .= "\n\n<p class='hide'>$comment</p>";
    } else {
        $splits = preg_split('#[?]#', $show_pragmamx_news_url);
        $content = '<p class="warning">no url-wrapper found for: <a href="' . $show_pragmamx_news_url . '" target="_blank" rel="pretty">' . $splits[0] . '</a></p>';
    }

    die($content);
}

/**
 * admin_chmods
 *
 * @package pragmaMx
 * @author tora60
 * @copyright Copyright (c) 2011
 * @version $Id: mx_adminfunctions.php 6 2015-07-08 07:07:06Z PragmaMx $
 * @access public
 */
class admin_chmods {
    /**
     * admin_chmods::check()
     * > http://www.pragmamx.org/doku.php?id=handbuch:installation_und_upgrade_des_systems
     *
     * @param mixed $err_dir
     * @param mixed $err_file
     * @return
     */
    public static function check(&$err_dir, &$err_file)
    {
        $dyndirs = array();
        $root = dirname(__FILE__);

        $checkfiles = array(/* zu testende Dateien */
            'config.php',
            'includes/classes/Textarea/config.inc.php',
            'includes/classes/Captcha/settings.php',
            'includes/prettyPhoto/config.php',
            'Downloads' => 'modules/Downloads/d_config.php',
            'Guestbook' => 'modules/Guestbook/include/config.inc.php',
            'My_eGallery' => 'modules/My_eGallery/settings.php',
            'UserGuest' => 'modules/UserGuest/settings.php',
            'Web_Links' => 'modules/Web_Links/l_config.php',
            'Your_Account' => 'modules/Your_Account/config.php',
            );

        foreach ($checkfiles as $modname => $value) {
            if (!is_numeric($modname) && !mxModuleActive($modname)) {
                // inaktive Module ignorieren
                continue;
            }
            if ($file = realpath(PMX_REAL_BASE_DIR . DS . $value)) {
                if (!self::_isit_writable($file)) {
                    $err_file[] = $file;
                    $dir = dirname($file);
                    if ($root != $dir) {
                        $dyndirs[] = $dir;
                    }
                }
            }
        }

        $dyndirs = array_merge($dyndirs, self::_scandir(PMX_DYNADATA_DIR, GLOB_ONLYDIR | GLOB_NOSORT));
        $dyndirs = array_merge($dyndirs, self::_scandir(PMX_MEDIA_DIR, GLOB_ONLYDIR | GLOB_NOSORT));
        $dyndirs[] = PMX_LAYOUT_DIR . DS . 'style';

        foreach ($dyndirs as $file) {
            if (!is_writable($file)) {
                $err_dir[] = $file;
            }
        }
    }

    /**
     * admin_chmods::_scandir()
     *
     * @param mixed $dir
     * @param integer $flags
     * @return
     */
    private static function _scandir($dir, $flags = 0)
    {
        $items = glob($dir . '/*', $flags);

        for ($i = 0; $i < count($items); $i++) {
            if (is_dir($items[$i])) {
                $add = glob($items[$i] . '/*', $flags);
                if ($add) {
                    $items = array_merge($items, $add);
                }
            }
        }

        return $items;
    }

    /**
     * admin_chmods::_isit_writable()
     *
     * @param mixed $filename
     * @return
     */
    private static function _isit_writable($filename)
    {
        if (!file_exists($filename)) {
            return true;
        }

        $oldmode = false;

        if (!is_writable($filename)) {
            /* aktuellen chmod der Datei zwischenspeichern */
            $oldmode = fileperms($filename);
            /* versuchen beschreibbar zu machen */
            mx_chmod($filename, PMX_CHMOD_UNLOCK);
            clearstatcache();
        }

        $result = is_writable($filename);

        if ($oldmode !== false) {
            mx_chmod($filename, $oldmode);
        }

        return $result;
    }
}

?>