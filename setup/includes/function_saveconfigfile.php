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
 * $Revision: 234 $
 * $Author: PragmaMx $
 * $Date: 2016-09-29 13:10:09 +0200 (Do, 29. Sep 2016) $
 */

defined('mxMainFileLoaded') or die('access denied');

/**
 * SetupSaveConfigFile()
 *
 * @param mixed $setvalues
 * @return
 */
function SetupSaveConfigFile($setvalues)
{
    // config.first.php entspricht der originalen config.php mit kleinen Änderungen
    // weitere Hinweise in dieser Datei !
    // FILE_CONFIG_BASE (config.first.php) entspricht einer originalen config.php ohne Änderungen
    include(FILE_CONFIG_BASE);
    // falls in dieser Datei DB-Einstellungen verblieben sind, diese zurücksetzen
    unset($dbhost, $dbname, $dbuname, $dbpass, $mxConf);
    // die Variablen aus der Original-Root config.php überschreiben die Basiskonfiguration
    if (@file_exists(FILE_CONFIG_ROOT)) {
        include(FILE_CONFIG_ROOT);
        $old_ansi = (isset($adminpanel) || isset($vkpUserregoption) || isset($nukeurl) || isset($sitekey) || !isset($admintheme) || !isset($mxConf));
    } else {
        $old_ansi = false;
    }


    unset($result, $dbstat);

    /* falls config.php noch nicht in UTF-8 Format ist, diese Werte konvertieren */
    if ($old_ansi) {
        $oldvars = get_defined_vars();
        unset($oldvars['mxConf'], $oldvars['setvalues']);
        $oldvars = setup_config_utf8encode($oldvars);
        extract($oldvars);
        unset($oldvars);
    }

    $vkpInactiveMins = (empty($vkpInactiveMins)) ? intval($vkpInactiveMins) : 5; # Standard 5 Minuten
    if (!defined("MX_SETINACTIVE_MINS")) define("MX_SETINACTIVE_MINS", $vkpInactiveMins * 60);
    // verschiedene Strings definieren, die in die config.php als Array geschrieben werden
    $allowedhtml = array();
    foreach ($AllowableHTML as $htmltag => $tagval) {
        if (($tagval == 1) || ($tagval == 2)) {
            $allowedhtml[] = "\"$htmltag\"=>$tagval";
        } else {
            $tagval = 0;
        }
    }
    $newAllowableHTML = 'array(' . implode(',', $allowedhtml) . ')';

	/* verschiedene texte zurück konvertieren */
	$mxSiteServiceText=utf8_decode(trim($mxSiteServiceText));
	$xmxOfflineModeText=utf8_decode(trim($mxOfflineModeText));
	$notify_message=utf8_decode(trim($notify_message));
	$notify_subject=utf8_decode(trim($notify_subject));
	$sitename=utf8_decode(trim($sitename));
	$slogan=utf8_decode(trim($slogan));
	$foot1=utf8_decode(trim($foot1));
	$foot2=utf8_decode(trim($foot2));
	$foot3=utf8_decode(trim($foot3));
	$foot4=utf8_decode(trim($foot4));

    $xmxSiteService = (empty($mxSiteServiceText)) ? 0 : $mxSiteService;
    $xmxOfflineMode = (empty($mxOfflineModeText)) ? 0 : $mxOfflineMode;

	
	/* Censor list */
    foreach ($CensorList as $word) {
        $xx = trim($word);
        if (!empty($xx)) {
            $words[] = '"' . utf8_decode(trim($word)) . '"';
        }
    }
    $newcensorlist = (isset($words)) ? 'array(' . implode(',', $words) . ')' : 'array()';

    /* debug */
    $debdef = array('log' => 16, 'screen' => 2, 'enhanced' => 0);
    foreach ($debdef as $key => $value) {
        if (isset($mxDebug[$key])) {
            $value = intval($mxDebug[$key]);
        }
        $newdebug[] = "'$key'=>$value";
    }
    $newdebug = 'array(' . implode(',', $newdebug) . ')';

    /* Standard Zeitzone */
    if (empty($default_timezone)) {
        $default_timezone = (defined('_SETTIMEZONE')) ? _SETTIMEZONE : date_default_timezone_get();
    }

    /* verfuegbare Sprachen */
    $language_avalaible_temp = setupGetLanguages();
    switch (true) {
        case !isset($language_avalaible):
        case !($language_avalaible):
            /* Update von < 1.12 */
            $language_avalaible = $language_avalaible_temp;
            break;
        case !in_array($language, $language_avalaible_temp);
            /* in config.php eingestellte Sprache existiert nicht */
            $language_avalaible[] = $language_avalaible_temp[0];
            $language = $language_avalaible_temp[0];
            break;
        default:
            $language_avalaible[] = $language;
    }
    $newlanguage_avalaible = 'array("' . implode('", "', array_unique($language_avalaible)) . '")';

    /* Sitelogo umschreiben, mit Pfad */
    if (!empty($site_logo) && strpos($site_logo, '/') === false) {
        if (file_exists('../images/' . $site_logo)) {
            $site_logo = 'images/' . $site_logo;
        }
    }
	


    /* footmsg von phpNuke entfernen */
    if (isset($foot1) && preg_match('#phpnuke\.org#i', $foot1)) $foot1 = '_Z3';
    if (isset($foot2) && preg_match('#phpnuke\.org#i', $foot2)) $foot2 = '_Z3';
    if (isset($foot3) && preg_match('#phpnuke\.org#i', $foot3)) $foot3 = '_Z3';
    if (isset($foot4) && preg_match('#phpnuke\.org#i', $foot4)) $foot4 = '_Z3';
	
    // hier jetzt die neuen Einstellungen extrahieren,
    // sie ueberschreiben die bisherigen config-Werte
    extract($setvalues, EXTR_OVERWRITE);
    // weitere Werte, die aus den vorigen Daten verwendet werden koennen:
    if (empty($adminmail) || $adminmail == 'webmaster@yoursite.de') $adminmail = $_SERVER['SERVER_ADMIN'];
    if (empty($notify_email) || $notify_email == 'webmaster@yoursite.de') $notify_email = $adminmail;
    if (empty($notify_from) || $notify_from == 'webmaster@yoursite.de') $notify_from = $adminmail;

	/* neue Daten einfügen */
	
	$mxCookieInfo=(isset($mxCookieInfo))?$mxCookieInfo:'0';
	$mxCookieLink=(isset($mxCookieLink))?$mxCookieLink:'modules.php?name=legal';
	
	/* FTP Data */
    $xmxFTPon = (empty($xmxFTPon)) ? 0 : intval($xmxFTPon);
	$xmxFTPhost= (empty($xmxFTPhost)) ? "localhost" : $xmxFTPhost;
	$xmxFTPport= (empty($xmxFTPport)) ? "21" : $xmxFTPport;
	$xmxFTPuser= (empty($xmxFTPuser)) ? "" : $xmxFTPuser;
	$xmxFTPpass= (empty($xmxFTPpass)) ? "" : $xmxFTPpass;	
	$xmxFTPssl = (empty($xmxFTPssl)) ? 0 : intval($xmxFTPssl);
	$xmxFTPdir= (empty($xmxFTPdir)) ? "" : $xmxFTPdir;	
	
	
    /* nicht interaktiv konfigurierbare Optionen */
    $show_pragmamx_news = (isset($mxShowPragmaMxNews)) ? 1 : intval($show_pragmamx_news);
    $check_chmods = (isset($check_chmods)) ? intval($check_chmods) : 1;
    // Alle Variablen nochaml zusaetzlich mit einem x davor erstellen
    $mxConf = get_defined_vars();
    unset($mxConf['mxConf'], $mxConf['GLOBALS'], $mxConf['_REQUEST'], $mxConf['_GET'], $mxConf['_POST'], $mxConf['_FILES'], $mxConf['_SERVER'], $mxConf['_ENV']);
	
    foreach($mxConf as $key => $value) {
        $new_mxConf['x' . $key] = (is_string($value)) ? addslashes(stripslashes($value)) : $value;
    }
    extract($new_mxConf, EXTR_OVERWRITE);
	
    unset($new_mxConf);

    if (!defined("MX_FIRSTGROUPNAME")) define("MX_FIRSTGROUPNAME", "User");
    // die Versionskonstanten nur hier definieren, dass die updaterkennung nicht durcheinander kommt
    define("PMX_VERSION", MX_SETUP_VERSION);
    define("PMX_VERSION_NUM", MX_SETUP_VERSION_NUM);
    // Die Datei mit den config.php Inhaltsdefinitionen includen
    include(FILE_CONFIG_NEWCONTENT);
    $cont = trim($cont);
	
    // Wenn bereits eine config.php vorhanden, deren Inhalt ermitteln
    $cont_old = (@file_exists(FILE_CONFIG_ROOT)) ? trim(file_get_contents(FILE_CONFIG_ROOT)) : '';

    $err = array();
    $msg = array();
	
    // alles weitere nur, wenn sich die Inhalte unterscheiden
    if ($cont_old != $cont) {
        if (@file_exists(FILE_CONFIG_ROOT)) {
            // wenn config.php bereits vorhanden, Schreibschutz entfernen
            @chmod(FILE_CONFIG_ROOT, octdec(CHMODUNLOCK));
            // Backup der bestehenden config.php
            $arr = pathinfo(realpath(FILE_CONFIG_ROOT));
            $extension = (isset($arr['extension'])) ? $arr['extension'] : '';
            $backfile = PATH_BACKUP . '/' . substr($arr['basename'], 0, - strlen($extension)) . date("Ymdhis", filemtime(FILE_CONFIG_ROOT)) . '_bak_' . SETUP_ID . '.' . $extension;
            $ok = @file_put_contents($backfile, $cont_old);
            @clearstatcache();
            if ($ok && @file_exists($backfile)) {
                @chmod($backfile, octdec(CHMODLOCK));
                $msg[8] = '<li>' . _CONFIG_BACK . ' ' . basename($backfile) . '</li>';
            }
        }
        // mxDebugFuncVars($cont); exit;
        // php-Fehlermeldungen in $php_errormsg zwischenspeichern
        @ini_set('track_errors', '1');
        $php_errormsg = '';
		$retry=false;
		//$out['configphp'] = htmlspecialchars($cont, ENT_QUOTES);
        switch (true) {
            case @file_exists(FILE_CONFIG_ROOT) && !is_writeable(FILE_CONFIG_ROOT):
                $err[] = '<li>' . _CONFIG_ERR_1 . ' (1)</li>';
				
                break;
			case (false===@file_put_contents(FILE_CONFIG_ROOT, $cont)):
                // nicht beschrieben
                $err[] = '<li>' . _CONFIG_ERR_2 . ' (2)</li>';
                if ($php_errormsg) {
                    $err[] = '<li>' . $php_errormsg . '</li>';
                }
				$retry=true;
                break;			
            case @!file_exists(FILE_CONFIG_ROOT):
                // wurde nicht erstellt
                $err[] = '<li>' . _CONFIG_ERR_4 . ' (4)</li>';
                break;
            case $cont != trim(file_get_contents(FILE_CONFIG_ROOT)):
                // Daten nicht korrekt geschrieben
                $err[] = '<li>' . _CONFIG_ERR_6 . ' (6)</li>';
				
                break;
            case @!include(FILE_CONFIG_ROOT):
                // kann nicht includet werden?
                $err[] = '<li>' . _CONFIG_ERR_5 . ' (5)</li>';
                if ($php_errormsg) {
                    $err[] = '<li>' . $php_errormsg . '</li>';
                }
                break;
            default:
                // Schreibschutz
                @chmod(FILE_CONFIG_ROOT, octdec(CHMODLOCK));
                break;
        }
		
        // oki, config.php wurde richtig geschrieben
        if (!count($err)) {
            $msg[1] = '<li>' . _CONFIG_OK_NEW . '</li>';
        }
    } else {
        // config.php war bereits aktuell und ok
        $msg[1] = '<li>' . _CONFIG_OK_OLD . '</li>';
    }
    // wenn bisher ohne Fehler, Datenbankverbindung mit neuer config.php testen
    $out['dbconnect'] = false;
    if (!count($err)) {
        $dbstat = setupConnectDb();
        // uups, da ist was falsch
        if (!isset($dbstat['dbi'])) {
            $err[] = '<li>' . $dbstat['msg'] . ' (7)</li>';
        }
        // aah, alles klar
        else {
            $msg[2] = '<li>' . sprintf(_DB_CONNECTSUCCESS, $setvalues['dbname']) . '</li>';
            $out['dbconnect'] = true;
        }
    }
    if (count($err)) {
        // Datenbankverbindung trotzdem ok?
        if ($out['dbconnect'] && @file_exists(FILE_CONFIG_ROOT) && $old_dbname == $setvalues['dbname']) {
            $err[] = '<li>' . _CONFIG_ERR_8 . ' (8)</li>';
            $out['ok'] = true;
        }
        // jetzt ist aber schluss....
        else {
            $out['ok'] = false;
        }
        $out['err'] = true;
        $out['msg'] = implode('', $err);
    } else {
        ksort($msg);
        $out['msg'] = implode('', $msg);
        $out['ok'] = true;
        $out['err'] = false;
    }
    $out['len'] = strlen($cont);
    $out['config_php'] = htmlentities(addslashes(str_replace("\"","'",$cont)),ENT_QUOTES ,"UTF-8");
	$out['len'] = strlen($out['config_php']);
	$out['retry']=$retry;
    return $out;
}

/**
 * setup_config_utf8encode()
 * Hilfsfunktion zum utf-8 codieren der Varianlen in der
 * bestehenden config.php vor Version 2.0
 *
 * @param mixed $array
 * @return
 */
function setup_config_utf8encode($array)
{
    foreach ($array as $key => $value) {
        switch (true) {
            case is_string($value) && !is_numeric($value):
                $array[$key] = utf8_encode($value);
                break;
            case is_array($value):
                $array[$key] = setup_config_utf8encode($value);
                break;
        }
    }
    return $array;
}

function SetupSaveUserConfigFile()
{
		if (!file_exists(PMX_MODULES_DIR."/Your_Account/config.php")) {
				@copy(PMX_MODULES_DIR."/Your_Account/config.default.php",PMX_MODULES_DIR."/Your_Account/config.php");
		}
}

?>