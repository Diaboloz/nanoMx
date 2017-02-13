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
 *
 */

defined('mxMainFileLoaded') or die('access denied');

/**
 * Die Variable $conf dient zur individuellen Konfiguration und kann
 * beliebig angepasst werden
 */
function config_trackhack()
{
    $conf['debug'] = 0; # default 0 / Debug information  1=YES  /  0=NO
    $conf['session'] = 1; # default 1 / use Session to identify users?   1=YES  /  0=NO
    // Exclude IPs
    $conf['exclude_ip'] = array("127.0.0.1", "255.255.255.0");
    // Exclude nuke usernames
    $conf['exclude_user'] = array("Testname_ztutzi");
    // Exclude url containing strings
    $conf['exclude_url'] = array(/*  */
        "/backend.php",
        "/includes/captcha/captchaimg.php",
        "/includes/classes/Captcha/captchaimg.php",
        "/modules.php?name=Private_Messages&file=buddy",
        "/modules.php?name=Your_Account&op=login",
        // SMF Krams...
        '&action=dlattach;attach=',
        '&file=style&st=',
        '&action=keepalive;',
        );
    return $conf;
}

/**
 * Here starts the update, normaly no changes neccessarry
 */
function start_trackhack()
{
    global $prefix;

    $conf = config_trackhack();

    if (!mxSessionGetVar("anno")) {
        mxSessionSetVar("anno", "0:0");
    }

    if (MX_IS_USER) {
        $userinfo = mxGetUserSession();
    } else {
        $userinfo[0] = 0;
        $userinfo[1] = '';
    }
    if ($conf['debug']) {
        echo "here is trackhack.php for uid=" . $userinfo[0] . " uname=" . $userinfo[1] . '<br />';
    }

    if ($conf['session'] == 1) {
        if ($conf['debug']) {
            echo "we are working with cookies<br />";
        }
        if (!mxSessionGetVar("anno")) {
            $trackid = date("ymdHis", time());
            $cookie_encode = $trackid . ":" . intval($userinfo[0]);
            mxSessionSetVar("anno", $cookie_encode);
            if ($conf['debug']) {
                echo "No cookie anno found. I've set one with trackid=" . $trackid . " and uid=" . $userinfo[0] . '<br />';
            }
        } else {
            $anno = mxSessionGetVar("anno");
            $conf['session'] = explode(":", $anno);

            $trackid = $conf['session'][0];
            $track_uid = $conf['session'][1];
            if ($conf['debug']) {
                echo "cookie anno found with trackid=" . $trackid . " track_uid=" . $track_uid . '<br />';
            }

            if (empty($conf['session'][0])) {
                $trackid = date("ymdHis", time());
                $cookie_encode = $trackid . ":" . intval($userinfo[0]);
                mxSessionSetVar("anno", $cookie_encode);
                if ($conf['debug']) {
                    echo "Trackid in cookie was empthy. Create new one with trackid=" . $trackid . " and uid=" . $userinfo[0] . '<br />';
                }
            }

            if ((intval($userinfo[0]) != 0) and ($conf['session'][1] != intval($userinfo[0]))) {
                $cookie_encode = $conf['session'][1] . ":" . intval($userinfo[0]);
                mxSessionSetVar("anno", $cookie_encode);
                if ($conf['debug']) {
                    echo "Uid in cookie was different. Create new one with trackid=" . $trackid . " and uid=" . $userinfo[0] . '<br />';
                }
            }
        }
    }

    if (in_array(MX_REMOTE_ADDR, $conf['exclude_ip'])) {
        if ($conf['debug']) {
            echo "exclude from tracking remote_addr=" . MX_REMOTE_ADDR . '<br />';
        }
        return;
    }
    if (!empty($userinfo[1])) {
        if (in_array($userinfo[1], $conf['exclude_user'])) {
            if ($conf['debug']) {
                echo "exclude from tracking uname=" . $userinfo[1] . '<br />';
            }
            return;
        }
    } while (list($key, $value) = each($conf['exclude_url'])) {
        if (strpos($_SERVER['REQUEST_URI'], $value) !== false) {
            if ($conf['debug']) {
                echo "exclude from tracking requri=" . $_SERVER['REQUEST_URI'] . '<br />';
            }
            return;
        }
    }
    switch ($_SERVER['REQUEST_URI']) {
        case ("/"):
            $_SERVER['REQUEST_URI'] = "/index.php";
            break;
            // case ("/index.php3"):
            // $_SERVER['REQUEST_URI'] = "/index.php";
            // break;
    }

    $qry = "REPLACE INTO " . $prefix . "_tracking (tracktime,ip,uid,server,referer,requrl,trackid) VALUES (NOW(), '" . MX_REMOTE_ADDR . "', '" . intval($userinfo[0]) . "', '" . mxAddSlashesForSQL($_SERVER['SERVER_NAME']) . "', '" . mxAddSlashesForSQL(strip_tags($_SERVER['HTTP_REFERER'])) . "', '" . mxAddSlashesForSQL($_SERVER['REQUEST_URI']) . "', '" . mxAddSlashesForSQL($trackid) . "')";
    // mxDebugFuncVars($qry);
    sql_system_query($qry);
    if ($conf['debug']) {
        echo "tracking write to database, affected rows:" . sql_affected_rows() . " Errno:" . sql_errno() . " Errortext:" . sql_error() . '<br />';
        echo "values tracktime=" . date("Y-m-d H:i:s", time()) . " remote_addr=" . MX_REMOTE_ADDR . " uid=" . $userinfo[0] . " server=" . $_SERVER['SERVER_NAME'] . " referer=" . strip_tags($_SERVER['HTTP_REFERER']) . " requri=" . $_SERVER['REQUEST_URI'] . " trackid=" . $trackid . '<br />';
    }
}

/**
 * Tracking starten
 */
start_trackhack();

?>
