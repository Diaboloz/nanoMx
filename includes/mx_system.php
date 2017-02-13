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
 * load_class()
 *
 * @param mixed $class
 * @param mixed $parameters
 * @return
 */
function &load_class($class, $parameters = null)
{
    require_once(PMX_SYSTEM_DIR . '/classes/' . $class . '.php');

    $name = 'pmx' . $class;

    switch (true) {
        case $parameters === false:
            /* wenn $parameters false ist, wird kein Objekt zurückgegeben */
            $return = true; // Variable wegen: Only variable references should be returned by reference
            return $return;

        case $parameters === null:
            $return = new $name();
            return $return;

        case func_num_args() > 2:
            $parameters = func_get_args();
            array_shift($parameters);

        default:
            $return = new $name($parameters);
            return $return;
    }
}

/**
 * pmx_get_hook()
 *
 * deprecated !!
 *
 * @param mixed $hook_name
 * @return array or false
 */
function pmx_get_hook($hook_name)
{
    trigger_error('Use of deprecated function ' . __FUNCTION__ . '(), use class "pmxHook" instead.', E_USER_NOTICE);
    return load_class('Hook', $hook_name)->get_files();
}

/**
 * pmx_run_hook()
 *
 * deprecated !!
 *
 * @param string $hook_name
 * @param mixed $hook_parameters
 * @param mixed $hook_result
 * @return bolean
 */
function pmx_run_hook($hook_name, &$hook_parameters = null, &$hook_result = null)
{
    $hook = load_class('Hook', $hook_name);

    if ($hook_parameters) {
        if (is_numeric($hook_parameters) && (strpos($hook_name, 'user.') === 0)) {
            /* TODO: dieser Sonderfall ist noch dringeblieben, weil viele Aufrufe
           * von user-hooks z.B. user.edit eine direkte $uid übergeben.
           * ->> das muss alles noch umgeschrieben werden
           */
            $hook_parameters = array('uid' => $hook_parameters);
        } else {
            trigger_error('Use of deprecated function ' . __FUNCTION__ . '(), use class "pmxHook" instead.', E_USER_NOTICE);
        }
        $hook->set((array)$hook_parameters);
    }
    // mxDebugFuncVars($hook_name, $hook_parameters, $hook_result);#exit;
    if ($hook_result) {
        $hook->run($hook_result);
    } else {
        $hook_result = $hook->run();
    }

    return true;
}

/**
 * mxCounter()
 *
 * @return
 */
function mxCounter()
{
    global $prefix;

    if (MX_MODULE == 'admin' || MX_IS_ADMIN) {
       return true;
    }

    /* Get the Browser data */
    $browser = load_class('Browser');

    $where[] = "(`type`='total' AND `var`='hits')";
    switch (true) {
        case $browser->browser:
            /* ein Browser */
            $where[] = "(`var`='$browser->browser' AND `type`='browser')";
            $isbot = false;
            break;
        case $browser->robot:
            /* ein Bot */
            $where[] = "(`var`='$browser->robot' AND `type`='robot')";
            $isbot = true;
            break;
        default:
            $isbot = false;
    }
    /* das Betriebssystem */
    $where[] = "(`var`='$browser->os' AND `type`='os')";
    $where = implode(" OR ", $where);

    $qry = "UPDATE `${prefix}_counter` SET `count`=count+1 WHERE " . $where;
    $result = sql_system_query($qry);

    /* es müssen genau 3 Datensätze geändert werden !! */
    $affected = sql_affected_rows();
    if ($affected != 3) {
        $types = array('total' => false, 'browser' => false, 'robot' => false, 'os' => false);
        $qry = "SELECT `type` FROM `${prefix}_counter` WHERE " . $where . "";
        $result = sql_system_query($qry);
        while (list($type) = sql_fetch_row($result)) {
            if (isset($types[$type])) {
                $types[$type] = true;
            }
        }

        $vals = array();
        /* fehlende anfügen */
        if (!$types['total']) {
            $vals[] = "('total', 'hits', NULL, 1)";
        }
        if (!$isbot && !$types['browser']) {
            $vals[] = "('browser', '$browser->browser', '$browser->browser_icon', 1)";
        } else if ($isbot && !$types['robot']) {
            $vals[] = "('robot', '$browser->robot', '$browser->robot_icon', 1)";
        }
        if ($browser->os && !$types['os']) {
            $vals[] = "('os', '$browser->os', '$browser->os_icon', 1)";
        }
        if ($vals) {
            $qry = "REPLACE INTO `${prefix}_counter` (`type`, `var`, `icon`, `count`) VALUES " . implode(',', $vals);
            $result = sql_system_query($qry);
        }
    }

    $now = getdate();
    $stmnt = sql_system_query("UPDATE `${prefix}_stats` SET `hits`=hits+1
              WHERE (`year`='$now[year]')
                AND (`month`='$now[mon]')
                AND (`date`='$now[mday]')
                AND (`hour`='$now[hours]')");

    $affected = sql_affected_rows();
    if ($affected==0) {
        $vals = array();
        for ($i = 0; $i <= 23; $i++) {
            $cnt = intval($i == $now['hours']);
            $vals[$i] = " ($now[year], $now[mon], $now[mday] ,$i, $cnt)";
        }
        $qry = "REPLACE INTO `${prefix}_stats` (`year`, `month`, `date`, `hour`,`hits`) VALUES " . implode(',', $vals);
        $result = sql_system_query($qry);
    }
}

/**
 * online()
 * Online aktualisieren
 *
 * @return
 */
function online()
{
    global $prefix, $user_prefix;
    static $done;
    if (isset($done)) {
        return true;
    }
    $done = true;
    $past = time() - MX_SETINACTIVE_MINS;
    $lasttime = mxSessionGetVar('lasttime');
    $lastday = (time() - 86400);
    $url = basename($_SERVER['REQUEST_URI']);

    if ($lasttime < $lastday && $lasttime > 0) {
        sql_system_query("DELETE FROM " . $prefix . "_visitors
            WHERE (time < " . ($lastday * 10) . " AND uid <> 0)
            OR (time < " . ($lastday) . " AND uid=0)
            OR (time > " . (time() + 60) . ")
            ");
    }

    if (defined('mxModFileLoaded') && defined('MX_MODULE')) {
        if (mxModFileLoaded == 2) {
            // die index.php geladen
            $module = 'Home';
            $url = 'index.php';
        } else {
            // die modules.php geladen
            $module = MX_MODULE;
        }
    } else {
        $module = $url;
        if (defined('mxAdminFileLoaded')) {
            // die admin.php geladen
            $module = 'Admin';
        } else if (preg_match('#^(.+)\.php#i', $url, $matches)) {
            $module = $matches[1] . '.php';
        }
        if (empty($module) || $module == 'index' || $url == '/') {
            $module = 'Home';
            $url = 'index.php';
        }
    }
    // falls gerade ein Formular gesendet wurde und evtl. nicht alle Parameter in der url stehen..
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (defined('mxModFileLoaded') && defined('MX_MODULE')) {
            $url = 'modules.php?name=' . MX_MODULE;
        }
    }

    if ($lasttime < $past || mxSessionGetVar("lastmodu") != $module) {
        mxSessionSetVar("lasttime", time());
        mxSessionSetVar("lastmodu", $module);
        $url = mxAddSlashesForSQL(mx_urltohtml($url));
        $url = substr($url, 0, 255);
        if (MX_IS_USER) {
            $usersession = mxGetUserSession();
            $uid = intval($usersession[0]);
            $qry = "UPDATE {$user_prefix}_users set user_lastvisit=" . time() . ", user_lastmod='" . $module . "', user_lasturl='" . $url . "', user_lastip='" . MX_REMOTE_ADDR . "' where uid=" . intval($uid);
            $result = sql_system_query($qry);
        } else {
            $uid = 0;
        }
        // visitor-tabelle aktualisieren
        $lnk=sql_system_query("UPDATE ${prefix}_visitors set uid=" . intval($uid) . ", time=" . time() . ", module='" . $module . "', url='" . $url . "', ip='" . MX_REMOTE_ADDR . "' where ip='" . MX_REMOTE_ADDR . "'");
        		
		if (!MX_IS_USER && sql_affected_rows($lnk)<1) {
				sql_system_query("REPLACE INTO ${prefix}_visitors (time,ip,module,url,uid) VALUES (" . time() . ",'" . MX_REMOTE_ADDR . "','" . $module . "','" . $url . "'," . intval($uid) . ")");
			} else {
				sql_system_query("UPDATE ${prefix}_visitors set uid=" . intval($uid) . ", time=" . time() . ", module='" . $module . "', url='" . $url . "', ip='" . MX_REMOTE_ADDR . "' where ip='" . MX_REMOTE_ADDR . "'");
		}
    }
}

/**
 * mxReferer()
 * HTTP-Referer Funktion
 *
 * @return
 */
function mxReferer()
{
    global $prefix;
    if (empty($GLOBALS['httprefmax'])) {
        // referer abgeschaltet
        return;
    }
    if (empty($_SERVER['HTTP_REFERER'])) {
        // kein referer
        return;
    }
    $ref = parse_url($_SERVER['HTTP_REFERER']);
    if (empty($ref['host']) || empty($ref['scheme'])) {
        // gefakter referer
        return;
    }
    if (empty($ref['path'])) {
        $ref['path'] = '';
    } else {
        // nur den Pfad der URL extrahieren und gleich den letzten slash entfernen, Ergebnis in $x[1]
        preg_match('#(.*)/[^/]*#', $ref['path'], $x);
        if (isset($x[1])) {
            $ref['path'] = $x[1];
        }
    }

    $self = parse_url(PMX_HOME_URL . '/index.php');

    if (empty($self['path'])) {
        $self['path'] = '';
    } else {
        preg_match('#(.*)/[^/]*#', $self['path'], $x);
        if (isset($x[1])) {
            $self['path'] = $x[1];
        }
    }
    // referer von eigener Seite
    if (preg_replace('#^www\.#i', '', $ref['host']) == preg_replace('#^www\.#i', '', $self['host'])) {
        /**
         * Pfade noch ueberpruefen
         * - wenn Serverpfad leer
         * - wenn Refererpfad leer
         * - wenn die Pfade gleich sind
         * - wenn Serverpfad in Refererpfad am Anfang enthalten ist
         * - wenn Refererpfad in Serverpfad am Anfang enthalten ist
         * dann diesen Referer nicht notieren
         */
        switch (true) {
            case !$self['path']:
            case !$ref['path']:
            case $ref['path'] === $self['path']:
            case strpos($ref['path'], $self['path']) === 0:
            case strpos($self['path'], $ref['path']) === 0:
                return;
        }
    }
    // referer die unerwuenscht sind, ignorieren
    if (@file_exists('admin/.ignore_referer')) {
        $ignore = file_get_contents('admin/.ignore_referer');
        $ignore = preg_split('#\s*,\s*#m', trim($ignore));
        if (in_array($_SERVER['HTTP_REFERER'], $ignore) || in_array($ref['host'], $ignore)) {
            return;
        }
    }

    $result = sql_system_query("select min(rid), avg(rid), count(rid) from " . $prefix . "_referer");
    list ($min, $avg, $count) = sql_fetch_row($result);

    if ($count == $GLOBALS['httprefmax']) {
        sql_system_query("DELETE FROM " . $prefix . "_referer WHERE rid = " . $min);
    } else if ($count > $GLOBALS['httprefmax']) {
        sql_system_query("DELETE FROM " . $prefix . "_referer WHERE rid < " . $avg);
    }
    pmxDebug::pause();
    sql_query("REPLACE INTO " . $prefix . "_referer (rid, url) values (" . intval(MX_TIME) . ",'" . mxAddSlashesForSQL($_SERVER['HTTP_REFERER']) . "')");
    pmxDebug::restore();
}

/**
 * mxSecureLog()
 *
 * @param mixed $log_eventid
 * @param mixed $log_event
 * @param string $account
 * @param integer $withdata
 * @return
 */
function mxSecureLog($log_eventid, $log_event, $account = '', $withdata = false)
{
    if (empty($GLOBALS["vkpsec_logging"])) {
        return;
    }
    global $prefix;
    $aid = "";
    $uname = "";
    if (MX_IS_USER) {
        $usersession = mxGetUserSession();
        @ $uname = $usersession[1];
    }
    if (MX_IS_ADMIN) {
        extract(mxGetAdminSession()); ;
    }
    $data = (empty($withdata)) ? '' : serialize($_REQUEST);
    $qry = "INSERT INTO ${prefix}_securelog
        (log_ip, log_time, log_eventid, log_event, uname, aid, request) VALUES
        ('" . MX_REMOTE_ADDR . "', " . time() . ", '" . mxAddSlashesForSQL($log_eventid) . "', '" . mxAddSlashesForSQL($log_event) . "', '" . mxAddSlashesForSQL($uname) . "', '" . mxAddSlashesForSQL($aid) . "', '" . mxAddSlashesForSQL($data) . "')";
    sql_system_query($qry);
}

/**
 * mxUserSecureLog()
 *
 * @param mixed $eventid
 * @param mixed $event
 * @param string $account
 * @param mixed $withdata
 * @return
 */
function mxUserSecureLog($eventid, $event, $account = '', $withdata = false)
{
    if (pmxDebug::is_debugmode()) {
        return mxSecureLog($eventid, $event, $account, true);
    }
}

/**
 * mxSessionSafeCookie()
 * sets a cookie to test the session-ID
 *
 * @param mixed $CookieName
 * @param mixed $inout
 * @return
 */
function mxSessionSafeCookie($CookieName, $inout)
{
    $type = ($CookieName == MX_SAFECOOKIE_NAME_ADMIN) ? 2 : 1;
    if ($GLOBALS["vkpSafeCookie" . $type]) {
        mt_srand((double) microtime() * 1000000);
        $checkId = mt_rand();
        $check = ($inout == 1) ? md5($checkId) : "";
        $CookieTime = ($inout == 1) ? 1 : -1;
        mxSetCookie($CookieName, $check, $CookieTime);
        mxSessionSetVar("ck" . $type, $checkId);
        $_COOKIE[$CookieName] = $check;
    }
    return true;
}

/**
 * mxInfo()
 * Gibt Copyright-Informationen aus
 *
 * @return
 */
function mxInfo()
{
    require_once(PMX_SYSTEM_DIR . '/mx_credits.php');
    $out = mxcredit_getinfo();

    if (isset($_REQUEST['name']) && $_REQUEST['name'] == 'mxcredit') {
        echo implode('<br />', $out);
        return '';
    } else {
        return $out;
    }
}

/**
 * mxGetOutputHandler()
 * hier kann ein anderer OutputHandler zugefuegt werden, als Alternative zu ob_gzhandler
 *
 * @return
 */
function mxGetOutputHandler()
{
    global $mxUseGzipCompression;

    if (empty($mxUseGzipCompression)) {
        return false;
    }

    /* Kompression auf jeden Fall abschalten */
    $mxUseGzipCompression = false;

    if (headers_sent()) {
        return false;
    }

    if (!isset($_SERVER['HTTP_ACCEPT_ENCODING'])) {
        return false;
    }

    if (!extension_loaded('zlib')) {
        return false;
    }

    if (mxIniGet('zlib.output_compression')) {
        return false;
    }

    if (mxIniGet('output_handler') == 'ob_gzhandler') {
        return false;
    }

    if (false !== strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) {
        /* Kompression einschalten */
        $mxUseGzipCompression = true;
        /* Handler zurueckgeben */
        return 'ob_gzhandler';
    }

    return false;
}

/**
 * mxSiteService()
 * Displays SiteService and DebugService
 *
 * @return
 */
function mxSiteService()
{
    /* Display SiteService */
    if ($GLOBALS['mxSiteService'] && !empty($GLOBALS['mxSiteServiceText'])) {
        echo '<!-- START mx_site_message --><div class="message-site alert alert-info txtcenter">', mxNL2BR($GLOBALS['mxSiteServiceText']), '</div><!-- END mx_site_message -->';
    }

    /* Display DebugService */
    $out = '';
    if (MX_IS_ADMIN && pmxDebug::is_debugmode()) {
        $out .= '<h3>' . _MSGDEBUGMODE . '</h3>';
    }
    if ($out) {
        echo '<!-- START mx_debug_message --><div class="message-debug">', $out, '</div><!-- END mx_debug_message -->';
    }
}

/**
 * pmx_get_adminnews()
 *
 * @return array
 */
function pmx_get_adminnews()
{
    if (!MX_IS_ADMIN) {
        return array();
    }

    $admindata = mxGetAdminData();
    $cacheid = __FUNCTION__ . $admindata['aid'];
    $cache = load_class('Cache');

    //if (($entries = $cache->read($cacheid)) === false) {
        $hook = load_class('Hook', 'admin.newentries');
        $hook->set($admindata);
        $hook->set('only_active', true);
        $hook->set('only_allowed', false);
        $entries = $hook->get();

        //$cache->write($entries, $cacheid, 600); // 10 Minuten Cachezeit
    //}

    if ($entries && is_array($entries)) {
        return $entries;
    }

    return array();
}

/**
 * pmx_get_usernews()
 *
 * @return array
 */
function pmx_get_usernews()
{
    if (!MX_IS_USER) {
        return array();
    }

    $userdata = mxGetUserData();
    $hook = load_class('Hook', 'user.newentries');
    $hook->set($userdata);

    $entries = $hook->get();
    if ($entries && is_array($entries)) {
        return $entries;
    }

    return array();
}

function pmxGetMobileDevice()
{
	/* find mobile devices */
	if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od|ad)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|ios|xda|xiino/i',MX_USER_AGENT)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr(MX_USER_AGENT,0,4))) return true;
	
	return false;

}

/**
 * pmxUserStored
 * Die Funktionen dieser Klasse bitte nicht in eigenen Projekten verwenden!!
 * Mit der nächsten pragmaMx Version wird diese Klasse wieder ersatzlos entfernt!!
 *
 * @package
 * @author tora60
 * @copyright Copyright (c) 2010
 * @version $Id: mx_system.php 206 2016-09-12 11:33:26Z PragmaMx $
 * @access public
 */
class pmxUserStored {
    private static $user = array();
    private static $isuser = false;
    private static $user_session = array();
    private static $admin = array();
    private static $isadmin = false;
    private static $admin_session = array();

    /**
     * pmxUserStored::init()
     *
     * @return
     */
    private function __construct()
    {
		       
    }

	public static function init()
	{
		
        self::_init_admin();
        self::_init_admin_session();
        self::_init_user();

        define('MX_IS_USER' , self::$isuser); // Userberechtigung  initialisieren
        define('MX_IS_ADMIN', self::$isadmin); // Adminberechtigung initialisieren
        define('MX_IS_SYSADMIN', MX_IS_ADMIN && isset(self::$admin['radminsuper']) && self::$admin['radminsuper']); // SYS-Adminberechtigung initialisieren		
		
	}
	
    /**
     * pmxUserStored::current_admindata()
     *
     * @return
     */
    public static function current_admindata()
    {
        return self::$admin;
    }

    /**
     * pmxUserStored::isadmin()
     *
     * @return
     */
    public static function isadmin()
    {
        return self::$isadmin;
    }

    /**
     * pmxUserStored::current_adminsession()
     *
     * @return
     */
    public static function current_adminsession()
    {
        return self::$admin_session;
    }

    /**
     * pmxUserStored::current_userdata()
     *
     * @return
     */
    public static function current_userdata()
    {
        return self::$user;
    }

    /**
     * pmxUserStored::isuser()
     *
     * @return
     */
    public static function isuser()
    {
        return self::$isuser;
    }

    /**
     * pmxUserStored::current_usersession()
     *
     * @return
     */
    public static function current_usersession()
    {
        return self::$user_session;
    }

    /**
     * pmxUserStored::getuserdata()
     *
     * @return
     */
    public static function getuserdata($where)
    {
        global $user_prefix, $prefix;

        if (!$where || !is_string($where)) {
            $where = 'FALSE';
        }

        $result = sql_query("SELECT u.*, IFNULL((YEAR(CURRENT_DATE) - YEAR(u.user_bday)) - ( RIGHT(CURRENT_DATE,5) < RIGHT(u.user_bday,5)), 0) AS user_age, a.aid AS admin_name, a.radminsuper AS admin_sys
                                FROM ".$user_prefix ."_users AS u
                                LEFT JOIN ".$prefix ."_authors AS a
                                ON a.user_uid = u.uid WHERE ".$where);

        if ($userinfo = sql_fetch_assoc($result)) {
            if (empty($userinfo['user_ingroup'])) {
                $userconfig = load_class('Userconfig');
                $userinfo['user_ingroup'] = $userconfig->default_group;
            }
            $userinfo['groups'][] = $userinfo['user_ingroup'];
            $userinfo['groups'][] = PMX_GROUP_ID_USER;
            if ($userinfo['admin_sys']) {
                $userinfo['groups'][] = PMX_GROUP_ID_SYSADMIN;
            }
            if ($userinfo['admin_name']) {
                $userinfo['groups'][] = PMX_GROUP_ID_ADMIN;
            }

            $userinfo['active'] = ($userinfo['user_stat'] == 1);

            if (self::$user_session && self::$user_session[0] == $userinfo['uid']) {
                $userinfo['current'] = true;
                $userinfo['user_online'] = true;
            } else {
                $userinfo['current'] = false;
                $userinfo['user_online'] = (($userinfo['user_lastvisit'] >= (time() - MX_SETINACTIVE_MINS)) && ($userinfo['user_lastmod'] != 'logout'));
            }

            return $userinfo;
        }
        return false;
    }

    /**
     * pmxUserStored::_init_user()
     *
     * @return
     */
    private static function _init_user()
    {
        if (!empty($GLOBALS['vkpSafeCookie1'])) { // nur wenn Variable gesetzt
            $checkId = md5(mxSessionGetVar('ck1'));
            $cookiechk = (empty($_COOKIE[MX_SAFECOOKIE_NAME_USER])) ? '' : $_COOKIE[MX_SAFECOOKIE_NAME_USER];
            if ($cookiechk != $checkId) {
                self::$isuser = false;
                return;
            }
        }

        $sess = mxSessionGetVar('user');
        if (empty($sess)) {
            self::$isuser = false;
            return;
        }

        $sess = base64_decode($sess);
        $sess = explode(':', $sess);
        if (empty($sess[2]) || !is_numeric($sess[0])) {
            self::$isuser = false;
            return;
        }

        $data = self::getuserdata('uid=' . intval($sess[0]) . ' AND user_stat=1');

        if ($data && $data['uname'] && $data['pass'] && $data['pass'] === $sess[2]) {
            self::$isuser = true;
            $data['user_online'] = true;
            $data['current'] = true;
            self::$user = $data;
            self::$user_session = $sess;
        }

        mxSessionDelVar('vkpnewuser'); // falls Neu-Registrierung...
    }

    /**
     * pmxUserStored::_init_admin()
     *
     * @return
     */
    private static function _init_admin()
    {
        global $prefix;

        if (!empty($GLOBALS['vkpSafeCookie2'])) { // nur wenn Variable gesetzt
            $checkId = md5(mxSessionGetVar('ck2'));
            $cookiechk = (empty($_COOKIE[MX_SAFECOOKIE_NAME_ADMIN])) ? '' : $_COOKIE[MX_SAFECOOKIE_NAME_ADMIN];
            if ($cookiechk != $checkId) {
                self::$isadmin = false;
                return;
            }
        }

        $sess = mxSessionGetVar('admin');
        if (empty($sess)) {
            self::$isadmin = false;
            return;
        }
        
		$sess = base64_decode($sess);
        $sess = explode(':', $sess);
        if (empty($sess[1])) {
            self::$isadmin = false;
            return;
        }

        $result = sql_system_query("select * from ".$prefix ."_authors where aid='" . mxAddSlashesForSQL(substr($sess[0], 0, 25)) . "'");
        $data = sql_fetch_assoc($result);

        if ($data && $data['radminsuper']) {
            foreach ($data as $key => $value) {
                if (strpos($key, 'radmin') === 0) {
                    $data[$key] = 1;
                }
            }
        }

        if ($data && $data['pwd'] && $data['pwd'] === $sess[1]) {
            self::$admin = $data;
            self::$isadmin = true;
        }
		
    }

    /**
     * pmxUserStored::_init_admin_session()
     *
     * @return
     */
    private static function _init_admin_session()
    {
        if (self::$isadmin) {
            self::$admin_session = array('aid' => self::$admin['aid'],
                'pwd' => self::$admin['pwd'],
                'admlanguage' => '', // admlanguage nur noch zur Kompatibilität
                );
        } else {
            self::$admin_session = array('aid' => '',
                'pwd' => '',
                'admlanguage' => '', // admlanguage nur noch zur Kompatibilität
                );
        }
    }
}

function includeHeader()
{
	/* jetzt in mx_api.php eingebaut */
	mxIncludeHeader();
}


?>