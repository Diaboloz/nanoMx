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
 * $Revision: 242 $
 * $Author: PragmaMx $
 * $Date: 2016-09-30 07:53:15 +0200 (Fr, 30. Sep 2016) $
 */

error_reporting(0);

/* umbenannte Datei nicht ausführen */
if (basename(__file__) != 'index.php' || basename(dirname(__FILE__)) != 'setup') {
    die('access denied');
}

@ini_set('track_errors', '1');
@ini_set("max_execution_time", 400);
@date_default_timezone_set('Europe/Berlin');

/* Benchmarkanzeige initialisieren */
if (!defined("MX_TIME")) {
    define('MX_TIME', microtime(true));
}
// Ausgabe puffern, damit Cookies und Weiterleitungen nicht durch Fehlermeldungen gestoert werden
ob_start();

if (!defined("PMX")) define("PMX", "PMX");
if (!defined("mxMainFileLoaded")) define("mxMainFileLoaded", "mxMainFileLoaded");
if (!defined("mxAdminFileLoaded")) define("mxAdminFileLoaded", "mxAdminFileLoaded");
if (!defined("mxRunInSetup")) define("mxRunInSetup", "mxRunInSetup");

if (!defined("MX_SETUP_DIR")) define("MX_SETUP_DIR", dirname(__FILE__));

/* header senden um charset Fehler bei Debugausgaben zu verhindern */
header('Content-type: text/html; charset=utf-8');

include_once("includes/functions.php");

include_once(dirname(dirname(__FILE__)) . "/includes/mx_api.php");
// ///////////////////////////////////
// /// - simuliert magic_quotes_gpc=1
if (count($_GET)) {
    foreach ($_GET as $key => $value) {
        $_GET[$key] = setupSecureValue($value);
    }
}
if (count($_POST)) {
    foreach ($_POST as $key => $value) {
        $_POST[$key] = setupSecureValue($value);
    }
}
if (count($_COOKIE)) {
    foreach ($_COOKIE as $key => $value) {
        $_COOKIE[$key] = setupSecureValue($value);
    }
}
// falsche Reihenfolge, damit evtl. existierende Cookies überschrieben werden
$_REQUEST = array_merge($_COOKIE, $_GET, $_POST);

include_once("setup-settings.php");

if (!get_cfg_var("register_globals")) {
    extract($_SERVER, EXTR_OVERWRITE);
}
// Initialisiere Startsprache und Option
$_REQUEST["slang"] = (empty($_REQUEST["slang"])) ? SETUP_DEFAULTLANG : $_REQUEST["slang"];
$_REQUEST["op"] = (empty($_REQUEST["op"])) ? 'start' : $_REQUEST["op"];

// mxDebugFuncVars(array_merge($_GET, $_POST));

$_REQUEST["dbconnect"] = 1;/* since PHP7 only prmitted MYSQLI */

/* DB-Treiber laden */
include_once("includes/functions_db.php");

/**
 * Sprache einstellen
 */
if (empty($_REQUEST['slang']) || !file_exists("language/lang-" . $_REQUEST['slang'] . ".php")) {
    $_REQUEST['slang'] = SETUP_DEFAULTLANG;
}
include_once("language/lang-" . $_REQUEST['slang'] . ".php");
// Laendereinstellung, mit den Werten aus dem globalen Sprachfile
setlocale(LC_TIME, _SETLOCALE);

$caption = '';
$option = '';
$output = '';
$arr_header = array();

/* Behandlung der Setup-Schritte */
switch ($_REQUEST["op"]) {
    case 'start':
        $mainsetupstep = 1;
        if (version_compare(PHP_VERSION, MX_SETUP_MIN_PHPVERSION, '<')) {
            $output = '
            <div class="alert alert-block">
              <strong>Sorry, PHP-Version >= ' . MX_SETUP_MIN_PHPVERSION . ' is required.</strong>
               Your current Version is: <strong>' . PHP_VERSION . '</strong>
            </div>';
        } else {
			
			$msg = '<div style="text-align:center;"><h2 style="text-align:center;font-size:1.5em;">Welcome - Wilkommen - Bienvenue - Velkommen - Merhaba</h2></div><br /><ul>';
			if (!@file_exists(FILE_CONFIG_ROOT)) {	
				$msg .='<ul>
				<li><b>pragma<span style="color:#799d24;">M</span>x </b>seems not to be installed correctly, or you\'re running pragmaMx for the first time. <br />&nbsp;</li>
				<li><b>pragma<span style="color:#799d24;">M</span>x </b>scheint nicht korrekt installiert zu sein oder Sie starten pragmaMx zum erstem Mal. <br />&nbsp;</li>
				<li><b>pragma<span style="color:#799d24;">M</span>x </b>semble ne pas &ecirc;tre install&eacute; correctement, ou vous ex&eacute;cutez pragmaMx pour la premi&egrave;re fois. <br />&nbsp;</li>
				<li><b>pragma<span style="color:#799d24;">M</span>x </b>düzgün kurulmam&#305;&#351; veya ilk defa pragmaMx &#231;al&#305;&#351;t&#305;r&#305;yorsunuz. <br />&nbsp;</li>
				</ul><br>';		
			}
            $goto = 'index.php?op=check';
            $langlist = mxGetAllLanguages($goto, 'language');
            $output = $msg.$langlist;
        }
        // $option = '- ' . str_replace('-', '<br />- ', _SELECT_LANG);
        $option = _SELECT_LANG;
        break;

    case 'check':
        include_once("includes/functions_check.php");

        $mainsetupstep = 2;
        $option = _ENVTEST;

        /* Werte und Umgebung auslesen */
        $results = array();
        $php_ok = validate_php($results);
        $memory_ok = validate_memory_limit($results);
		$files_ok = validate_files($results);
        $extensions_ok = validate_extensions($results);
		

        if ($php_ok && $memory_ok && $extensions_ok && $files_ok) {
            $output .= '
            <p id="verdict" class="row all_ok">' . _TEST_ISOK . '</p>';
            $output .= '
              <div class="form-actions">
                <form action="index.php" method="post">
                  ' . hiddenrequest('select') . '
                  <input class="btn" type="submit" value="' . _SUBMIT . '" />
                </form>
              </div>';
            $output .= show_test_results($results);
        } else {
            $output .= '
            <p id="verdict" class="row not_ok">' . _TEST_ISNOTOK . '</p>';
            $output .= show_test_results($results);
            $output .= '
            <p class="row">
              <a class="btn" href="index.php?op=check&amp;slang=' . $_REQUEST['slang'] . '&amp;mxsetupid=' . SETUP_ID . '">' . _REMAKE . '</a>
            </p>
            ';
        }

        break;

    case 'select':
        $mainsetupstep = 3;
        $option = _STEP_SELECT;
        $default = setupGetOldVersion();
        foreach($opt as $opt => $arr) {
            // mxDebugFuncVars($arr['version'], $default);
            $select[] = '
            <div class="well">
              <label class="radio"><input type="radio" name="setupoption" value="' . $opt . '"' . (($arr['version'] == $default) ? ' checked="checked"': '') . ' /><span class="label label-info">' . constant($arr['name']) . '</span></label>
              <p>' . constant($arr['description']) . '</p>
            </div>';
        }
        $output = '
        <p>' . _HELLOINSTALL2 . '</p>
        <p>' . _WHATWILLYOUDO . '</p>
        <form action="index.php" method="post">' . implode('', $select) . '
          <p>
            <a class="btn btn-link" href="index.php?mxsetupid=' . SETUP_ID . '">' . _GOBACK . '</a>
            ' . hiddenrequest('license') . '
            <input class="btn" type="submit" value="' . _SUBMIT . '" />
          </p>
        </form>';
        break;

    case 'delsetupdir':
        if (!isset($_GET['ts'])) die('puhh...');
        $new = basename(__FILE__) . SETUP_RENAME;
        // index.php umbenennen
        $ok = @rename(basename(__FILE__), $new);
        if ($ok) {
            print 'OK!<br /><br />' . $new;
        } else {
            print 'FAILED!<br /><br />' . $new;
        }
        clearstatcache();
        // .htaccess anlegen
         @file_put_contents(__DIR__ . '/.htaccess', "deny from all\nErrorDocument 403 \"pragmaMx-Setup is locked\"\n");
         //file_put_contents(__DIR__ . '/index.html', "forbidden");
       exit;
}

/* ZWEITE Behandlung der Setup-Schritte, weil $op evtl. durch vorige Behandlung geändert wurde */
switch ($_REQUEST["op"]) {
    case 'license';
        $mainsetupstep = 6;

        $newversion = $GLOBALS['opt'][$_REQUEST['setupoption']]['version'];
        $oldversion = setupGetOldVersion();

        if (($newversion != $oldversion) && empty($_REQUEST['versionignore'])) {
            $mainsetupstep = 4;
            $option = _STEP_ISINCORRECT;
            $output = '
            <div class="alert alert-block">
              ' . _OLDVERSION_ERR1 . '
              <br />
              <strong>' . _OLDVERSION_ERR2 . '</strong>
            </div>
            <div class="row">
              <div class="span1">
                <form action="index.php" method="post">
                  ' . hiddenrequest('license') . '
                  <input type="hidden" name="setupoption" value="' . $_REQUEST['setupoption'] . '" />
                  <input class="btn btn-link" type="submit" name="versionignore" value="' . _YES . '" />
                </form>
              </div>
              <div class="span1">
                <form action="index.php" method="post">
                  ' . hiddenrequest('select') . '
                  <input class="btn" type="submit" value="' . _NO . '" />
                </form>
              </div>
          </div>';
        } else {
            $option = _STEP_LICENSE;
            // $text = file_get_contents('language/'.$GLOBALS['opt'][$_REQUEST['setupoption']]['license'].'-'.$_REQUEST['slang'].'.txt');
            $text = htmlspecialchars(file_get_contents('gpl.txt'));
            $output = '
            <textarea rows="12" readonly="readonly" style="width:600px">' . $text . '</textarea>
            <p>' . _ACCEPT . '</p>
            <div class="row">
              <div class="span1">
                <form action="index.php" method="post">
                  ' . hiddenrequest('cancel') . '
                  <input class="btn" type="submit" name="goback" value="' . _NO . '" />
                </form>
              </div>
              <div class="span1">
                <form action="index.php" method="post">
                  ' . hiddenrequest('setup') . '
                  <input class="btn" type="submit" value="' . _YES . '" />
                </form>
              </div>
            </div>';
        }
        break;

    case 'cancel'; /// Lizenz abgelehnt
        $mainsetupstep = 0;
        $option = _SETUPCANCELED;
        $output = '
          <p><a class="btn" href="index.php?mxsetupid=' . SETUP_ID . '">' . _START . '</a></p>';
        break;

    default;
        if (empty($mainsetupstep)) {
            $output = '';
            $setupfile = PATH_SETUP_INCLUDES . '/option_' . $GLOBALS['opt'][$_REQUEST['setupoption']]['version'] . '.php';
            if (@file_exists($setupfile)) {
                include_once($setupfile);
            } else {
                $option = _ERROR_FATAL;
                $output = '
                  <div class="alert alert-error alert-block">' . sprintf(_SETUPMODNOTFOUND1, basename($setupfile)) . '</div>';
            }
        }
        break;
}

/* Ausgabe von evtl. umgebenden Zeilenumbrüchen befreien */
$output = preg_replace('#^(<br\s*/?>|[[:space:]])*(.*)#i', '\2', $output);
$output = trim(preg_replace('#(<br\s*/?>|[[:space:]])*$#i', '', $output));

/* parsen der Ausgabe */
if (empty($htmlfile)) $htmlfile = FILE_SETUP_TEMPLATE;
if (empty($caption)) $caption = _HELLOINSTALL;
ob_start();
include($htmlfile);
$htmlfile = ob_get_clean();
$htmlfile = str_replace("images/", "html/images/", $htmlfile);
$htmlfile = str_replace("style/", "html/style/" , $htmlfile);
$htmlfile = str_replace('{VERSIONNUM}', MX_SETUP_VERSION , $htmlfile);
$htmlfile = str_replace('{CAPTION}', strip_tags($caption) , $htmlfile);
$htmlfile = str_replace('{OPTION}', strip_tags($option, '<br>') , $htmlfile);
$htmlfile = str_replace('{TITLE}', strip_tags($option) , $htmlfile);
$htmlfile = str_replace('{OUTPUT}', $output , $htmlfile);
$htmlfile = str_replace('{HEADER}', implode("\n", array_unique($arr_header)) , $htmlfile);
$htmlfile = str_replace('{COPYRIGHT}', "&copy;".date("Y") , $htmlfile);

// $htmlfile = str_replace("<hr />", '<img src="html/images/graypixel.gif" alt="" width="450" height="1" vspace="5" border="0" />', $htmlfile);
echo $htmlfile;

?>