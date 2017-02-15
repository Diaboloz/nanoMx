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
 * $Revision: 171 $
 * $Author: PragmaMx $
 * $Date: 2016-06-29 13:59:03 +0200 (Mi, 29. Jun 2016) $
 */

defined('mxMainFileLoaded') or die('access denied');

/**
 * Identifikation von bereits installierten nuke oder pmx Versionen
 * checkt, ob es ein INSTALLIERTES pragmaMx ist
 */
function setupGetOldVersion()
{
    static $oldversion;
    if (isset($oldversion)) return $oldversion;
    $isconfig = false;
    $oldversion = 'new';
    // wenn config.php vorhanden ist
    if (@file_exists(FILE_CONFIG_ROOT)) {
        // diese versuchen zu includen, um Fehlermeldungen zu vermeiden innerhalb von Ausgabepuffer
        ob_start();
        @include(FILE_CONFIG_ROOT);
        ob_end_clean();
        if (isset($dbhost) && isset($dbuname) && isset($dbpass) && isset($dbname) && isset($prefix) && isset($user_prefix)) {
            if ((isset($dbtype) && $dbtype == "MySQL") || isset($mxConf)) {
                $isconfig = true;
            }
        }
    }
    // wenn config.php includet wurde
    if ($isconfig) {
        // und darin die Variable $Version_Num vorhanden ist
        if (isset($Version_Num) || isset($mxConf)) {
            // und MX_FIRSTGROUPNAME oder $mxConf definiert ist
            if (isset($mxConf) || defined('MX_FIRSTGROUPNAME')) {
                // dann ist es eine vkpMx/mX Version
                $oldversion = 'pragmamx';
            } else {
                // ansonsten eine phpNuke Version vor 6.0
                $oldversion = 'nuke56';
            }
        } else { // wenn nicht, könnte es ein phpNuke >= 6.0 sein
            // versuchen Datenbankverbindung herzustellen
            $dbstat = setupConnectDb($dbhost, $dbuname, $dbpass, $dbname);
            if (isset($dbstat['dbi'])) {
                $GLOBALS['dbi'] = $dbstat['dbi'];
                // versuchen die nuke-config Tabelle auszulesen
                $result = sql_query("SELECT Version_Num from ${prefix}_config");
                // wenn erfolgreich...
                if ($result) {
                    // nukeversion feststellen
                    list($nukeversion) = sql_fetch_row($result);
                    // mxDebugFuncVars(doubleval($nukeversion));
                    if (doubleval($nukeversion) == 6.0) {
                        $oldversion = 'nuke56';
                    } else if (doubleval($nukeversion) > 6.0) {
                        $oldversion = 'nuke6x';
                    }
                }
            }
        }
    }
    // TODO: das muss dann wieder verbessert / aktualisiert werden...
    $oldversion = ($oldversion == 'new') ? 'new' : 'update';
    return $oldversion;
}

/**
 * dient nur zur Filterung von $_GET, $_POST, $_COOKIE
 */
function setupSecureValue($value)
{

    /* wenn $value ein Array, das Array durchlaufen
     * und die Funktion rekursiv aufrufen */
    if (is_array($value)) {
        foreach ($value as $key => $xx) {
            $value[$key] = setupSecureValue($xx);
        }
    } else if (is_string($value)) {
        if (get_magic_quotes_gpc()) {
            $value = stripslashes($value);
        }
        $value = htmlspecialchars($value, ENT_QUOTES);
        // $value = str_replace('"','&quot;',$value);
        // $value = str_replace("'",'&acute;',$value);
    }

    return $value;
}

/**
 * ermittelt die verfuegbaren Setup-Sprachen und zeigt eine Liste der Flaggen an
 */
function mxGetAllLanguages($goto, $folder = 'language')
{
    static $langlist;
    if (isset($langlist)) return $langlist;

    $replaces = array(/* Sprachen */
        'danish' => 'Dansk',
        'english' => 'English',
        'spanish' => 'Español',
        'french' => 'Français',
        'german' => 'Deutsch',
        'turkish' => 'Türkçe',
        );

    $out = '';
    // $pre = "border=\"0\" hspace=\"3\" vspace=\"3\" width=\"30\" height=\"16\"";
    $langlist = array();
    $handle = @opendir($folder);
    if (!$handle) die("<div class=\"alert alert-error alert-block\">Error: the language-folder (<em>/" . $folder . "</em>) is missing!</div>");
    while (false !== ($file = readdir($handle))) {
        if (preg_match("#^lang\-(.+)\.php$#", $file, $matches)) {
            $key = ucwords(str_replace("_", " ", str_replace(array_keys($replaces), array_values($replaces), $matches[1])));
            $langlist[$key] = "<li class=\"span2\">
            <div class=\"thumbnail\">
              <a href=\"" . $goto . "&amp;slang=" . $matches[1] . "&amp;mxsetupid=" . SETUP_ID . "\" title=\"" . $key . "\"><img src=\"language/images/flag-" . $matches[1] . ".png\" alt=\"" . $key . "\" title=\"" . $key . "\"  />&nbsp;</a>
              <a href=\"" . $goto . "&amp;slang=" . $matches[1] . "&amp;mxsetupid=" . SETUP_ID . "\" title=\"" . $key . "\">" . $key . "</a>
            </div></li>\n";
        }
    }
    closedir($handle);
    if (!count($langlist)) die("<div class=\"alert alert-error alert-block\">Error: the language-folder (<em>/" . $folder . "</em>) is empty!</div>");
    ksort($langlist);
    return '
      <div class="row">
        <div class="span8">
          <ul class="thumbnails">
            ' . implode("", $langlist) . '
          </ul>
        </div>
      </div>';
}

/**
 * ermittelt die verfuegbaren Setup-Sprachen und zeigt eine Liste der Flaggen an
 */
function setupGetLanguages($folder = PMX_LANGUAGE_DIR)
{
    $handle = @opendir($folder);
    if (!$handle) {
        return array();
    }
    $languages = array();
    while (false !== ($file = readdir($handle))) {
        if (preg_match("#^lang\-(.+)\.php$#", $file, $matches)) {
            $key = ucwords(str_replace("_", " ", str_replace("german", "deutsch", $matches[1])));
            $languages[$key] = $matches[1];
        }
    }
    closedir($handle);
    return $languages;
}

/**
 * ermittelt die verfuegbaren Setup-Sprachen und zeigt eine Liste der Flaggen an
 */
function setupLanguageSelect($selectname, $folder = PMX_LANGUAGE_DIR)
{
    $clanguage = "";
    $handle = @opendir($folder);
    if (!$handle) return '<input type="hidden" name="language" value="' . $_REQUEST['slang'] . '" />' . $_REQUEST['slang'];
    while (false !== ($file = readdir($handle))) {
        if (preg_match("#^lang\-(.+)\.php$#", $file, $matches)) {
            $key = ucwords(str_replace("_", " ", str_replace("german", "deutsch", $matches[1])));
            $clanguage .= "<option value=\"" . $matches[1] . "\"" . (($matches[1] == $_REQUEST['slang']) ? ' selected="selected" class="current"' : '') . ">" . $key . "</option>\n";
        }
    }
    closedir($handle);
    if (!$clanguage) return '<input type="hidden" name="language" value="' . $_REQUEST['slang'] . '" />' . $_REQUEST['slang'];
    return "<select name=\"" . $selectname . "\">\n" . $clanguage . "</select>\n";
}

/**
 * ermittelt die Homepage-URL, der aktuellen pragmaMx Installation
 */
function mxSetupSetHomeDir()
{
    // // in Funktion gekapselt, um die Variablen nicht in den globalen scope zu kopieren ;)
    $requri = $_SERVER['REQUEST_URI'];
    if ((empty($requri)) || (substr($requri, -1, 1) == '/')) {
        // wenn leer oder an einen Pfad gebunden, PATH_INFO verwenden
        $requri = getenv('PATH_INFO');
        if (empty($requri)) { // auch leer, SCRIPT_NAME verwenden
            $requri = $_SERVER['SCRIPT_NAME'];
        }
    }
    if (empty($requri)) { // REQUEST_URI kann auf manchen Servern nicht verfügbar sein
        if (isset($_POST['name'])) {
            $_GET['name'] = $_POST['name'];
        }
        if (count($_GET)) {
            foreach ($_GET as $key => $value) {
                $parts[$key] = $key . "=" . $value;
            }
        }
        $requri = (isset($parts)) ? $_SERVER['PHP_SELF'] . "?" . implode("&amp;", $parts) : $_SERVER['PHP_SELF'];
        $_SERVER["REQUEST_URI"] = $requri;
    }
    $requri = preg_replace('/[#\?].*/', '', $requri);
    $requri = dirname(dirname($requri));
    if (preg_match('!^[/\\\]*$!', $requri)) {
        $requri = '';
    }
    $proto = (isset($_SERVER['HTTPS']))? 'https://' : 'http://';
    $server = $_SERVER['HTTP_HOST'];
    $mxroot = preg_replace('#[/]$#', '', $requri);

    if (!defined('PMX_HOME_URL')) define('PMX_HOME_URL', $proto . $server . $mxroot);
}

/**
 * neuen Prefix überprüfen
 */
function setupCheckNewPrefixes($new_prefix, $new_user_prefix, $connected)
{
    if (empty($new_prefix)) {
        return(array('critical', _PRERR12));
    }
    if (strtolower($new_prefix) == 'nuke' || strtolower($new_prefix) == 'mx') {
        return(array('check', _PRERR13));
    }
    // [a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*
    if (preg_match("#[^a-z0-9_]#", $new_prefix)) {
        return(array('critical', _PRERR14));
    }
    if (preg_match("#^[0-9]#", $new_prefix)) {
        return(array('critical', _PRERR15));
    }
    if (strlen($new_prefix) > PREFIX_MAXLENGTH) {
        return(array('check', _PRERR16));
    }
    if ($connected) {
        $err_new_prefix = intval(sql_num_rows(sql_query("SHOW TABLES LIKE '" . $new_user_prefix . "_users';")));
        $err_new_prefix += intval(sql_num_rows(sql_query("SHOW TABLES LIKE '" . $new_prefix . "%';"))) - $err_new_prefix;
        if ($err_new_prefix) {
            return(array('critical', sprintf(_PRERR17, $err_new_prefix)));
        }
    }
    // neuen User-Prefix überprüfen
    if (empty($new_user_prefix)) {
        return(array('critical', _PRERR18));
    }
    if (preg_match("#[^a-z0-9_]#", $new_user_prefix)) {
        return(array('critical', _PRERR19));
    }
    if (preg_match("#^[0-9]#", $new_user_prefix)) {
        return(array('critical', _PRERR20));
    }
    if (strlen($new_user_prefix) > PREFIX_MAXLENGTH) {
        return(array('check', _PRERR21));
    }
    if ($connected) {
        $err_new_prefix = intval(sql_num_rows(sql_query("SHOW TABLES LIKE '" . $new_user_prefix . "_users';")));
        if ($err_new_prefix) {
            return(array('check', _PRERR22));
        }
    }
    return(array('ok', ''));
}

/**
 */
function hiddenrequest($nextop = '')
{
    // static $isop;
    // if (!$isop) {
    // $_REQUEST['opcount'] = (empty($_REQUEST['opcount'])) ? 2 : $_REQUEST['opcount']+1;
    // }
    // $isop = TRUE;
    $output = '';
    if ($_REQUEST["op"] != 'start' && $_REQUEST["op"] != 'select') {
        $output .= "<input type=\"hidden\" name=\"setupoption\" value=\"" . ((isset($_REQUEST['setupoption'])) ? $_REQUEST['setupoption'] : '') . "\" />\n";
    }
    $output .= "<input type=\"hidden\" name=\"mxsetupid\" value=\"" . SETUP_ID . "\" />\n";
    $output .= "<input type=\"hidden\" name=\"op\" value=\"" . ((empty($nextop)) ? '' : $nextop) . "\" />\n";
    $output .= "<input type=\"hidden\" name=\"slang\" value=\"" . ((isset($_REQUEST['slang'])) ? $_REQUEST['slang'] : SETUP_DEFAULTLANG) . "\" />\n";
    $output .= "<input type=\"hidden\" name=\"lastop\" value=\"" . ((isset($_REQUEST['op'])) ? $_REQUEST['op'] : 'start') . "\" />\n";
    // $output .= "<input type=\"hidden\" name=\"opcount\" value=\"".$_REQUEST['opcount']."\" />\n";
    // $output .= "<input type=\"hidden\" name=\"module\" value=\"".((isset($_REQUEST['module'])) ? $_REQUEST['module'] : '')."\" />\n";
    // $output .= $_REQUEST['opcount'];
    if (function_exists('additionalhiddenrequest')) {
        $output .= additionalhiddenrequest($nextop);
    }
    return $output;
}

/**
 * zeigt den Link zum loeschen der Setupdatei an
 */
function printdeleter($textbefore = '')
{
    return '<br /><br />' . $textbefore . '<div>
      <label for="delete_self" class="printdeleter"><input type="checkbox" id="delete_self" onclick="doTheDelete();" />&nbsp;' . _DELETESETUPDIR . '</label>
      <script type="text/javascript"><!-- // --><![CDATA[
          function doTheDelete()
          {
              var theCheck = document.getElementById ? document.getElementById("delete_self") : document.all.delete_self;
              var tempImage = new Image();

              tempImage.src = "' . $_SERVER['PHP_SELF'] . '?op=delsetupdir&ts=" + (new Date().getTime());
               tempImage.width = 0;
              theCheck.disabled = true;
          }
          // ]]>
      </script>
        </div>';
}

/**
 */
function additionalhiddenrequest($nextop = '')
{
    // print $nextop;
    $set = setupFormDefaults();
    $output = "\n";
    if ($_REQUEST['op'] != 'setup' && ($_REQUEST['op'] != 'checkdb' || $nextop == 'createdb' || $nextop == 'viewsettings')) {
        // print '<h4>VIEW 1 '.$nextop.'</h4>';
        // $output .= "<input type=\"hidden\" name=\"dbtype\" value=\"".((isset($_REQUEST['dbtype']))   ? $_REQUEST['dbtype']  : $set['dbtype'])."\" />\n";
        $output .= "<input type=\"hidden\" name=\"dbhost\" value=\"" . ((isset($_REQUEST['dbhost'])) ? $_REQUEST['dbhost'] : $set['dbhost']) . "\" />\n";
        $output .= "<input type=\"hidden\" name=\"dbuname\" value=\"" . ((isset($_REQUEST['dbuname'])) ? $_REQUEST['dbuname'] : $set['dbuname']) . "\" />\n";
        $output .= "<input type=\"hidden\" name=\"dbpass\" value=\"" . ((isset($_REQUEST['dbpass'])) ? $_REQUEST['dbpass'] : $set['dbpass']) . "\" />\n";
        $output .= "<input type=\"hidden\" name=\"dbname\" value=\"" . ((isset($_REQUEST['dbname'])) ? $_REQUEST['dbname'] : $set['dbname']) . "\" />\n";
		/* since PHP7 only prmitted MYSQLI */
		$output .= "<input type=\"hidden\" name=\"dbconnect\" value=\"1\" />\n";
    }
    if ($nextop != 'viewsettings') {
        // print '<h4>VIEW 2 '.$nextop.'</h4>';
        $output .= "<input type=\"hidden\" name=\"prefix\" value=\"" . ((isset($_REQUEST['prefix'])) ? $_REQUEST['prefix'] : $set['prefix']) . "\" />\n";
        $output .= "<input type=\"hidden\" name=\"user_prefix\" value=\"" . ((isset($_REQUEST['user_prefix'])) ? $_REQUEST['user_prefix'] : $set['user_prefix']) . "\" />\n";
        $output .= "<input type=\"hidden\" name=\"sitename\" value=\"" . ((isset($_REQUEST['sitename'])) ? $_REQUEST['sitename'] : $set['sitename']) . "\" />\n";
        $output .= "<input type=\"hidden\" name=\"startdate\" value=\"" . ((isset($_REQUEST['startdate'])) ? $_REQUEST['startdate'] : $set['startdate']) . "\" />\n";
        $output .= "<input type=\"hidden\" name=\"adminmail\" value=\"" . ((isset($_REQUEST['adminmail'])) ? $_REQUEST['adminmail'] : $set['adminmail']) . "\" />\n";
        $output .= "<input type=\"hidden\" name=\"vkpIntranet\" value=\"" . ((isset($_REQUEST['vkpIntranet'])) ? $_REQUEST['vkpIntranet'] : $set['vkpIntranet']) . "\" />\n";
        $output .= "<input type=\"hidden\" name=\"language\" value=\"" . ((isset($_REQUEST['language'])) ? $_REQUEST['language'] : $set['language']) . "\" />\n";
    }
    $output .= "\n";
    return $output;
}

function dbsettings($set)
{
    return '
    <fieldset>
      <legend>' . _SERVER . '</legend>
      <div class="control-group">
          <label class="control-label" for="dbhost">' . _DBSERVER . ':</label>
          <div class="controls">
            <div class="input-prepend">
              <span class="add-on"><i class="icon-hdd"></i></span>
              <input id="dbhost" name="dbhost" type="text" value="' . $set['dbhost'] . '" size="40" />
            </div>
          </div>
      </div>
      <div class="control-group">
          <label class="control-label" for="dbname">' . _DBNAME . ':</label>
          <div class="controls">
            <div class="input-prepend">
              <span class="add-on"><i class="icon-file"></i></span>
                <input id="dbname" name="dbname"type="text" value="' . $set['dbname'] . '" size="40" />
            </div>
          </div>
      </div>
      <div class="control-group">
          <label class="control-label" for="dbuname">' . _DBUSERNAME . ':</label>
          <div class="controls">
            <div class="input-prepend">
              <span class="add-on"><i class="icon-user"></i></span>
              <input id="dbuname" name="dbuname" class="text" type="text" value="' . $set['dbuname'] . '" size="40" />
            </div>
          </div>
      </div>
      <div class="control-group">
          <label class="control-label" for="dbpass">' . _DBPASS . ':</label>
          <div class="controls">
            <div class="input-prepend">
              <span class="add-on"><i class="icon-lock"></i></span>
              <input id="dbpass" name="dbpass" class="text" type="password" value="' . $set['dbpass'] . '" size="40" />
            </div>
          </div>
      </div>
 
    </fieldset>';
}


/*
     <div class="control-group">
          <label class="control-label" for="dbconnect">' . _DBCONNECT . ':</label>
          <div class="controls">
            <div class="input-prepend">
              <span class="add-on"><i class="icon-file"></i></span>
              <select id="dbconnect" name="dbconnect" class="text" size="1">
				<option value="0" '. (($set['dbconnect']==0)?'selected="selected"':'') .'>mysql   </option>
				<option value="1" '. (($set['dbconnect']==1)?'selected="selected"':'') .'>mysqli  </option>
				<option value="2" '. (($set['dbconnect']==2)?'selected="selected"':'') .'>pdo     </option>
			  </select>
            </div>
          </div>
      </div>	  
	  
*/


/**
 * setup_convert_pagebreak()
 *
 * @param mixed $table
 * @param mixed $field
 * @param mixed $id_field
 * @return
 */
function setup_convert_pagebreak($table, $field, $id_field)
{
    $oldstring = '<!--pagebreak-->';
    $newstring = ' <!-- pagebreak --> ';
    $sqlqry = array();

    $qry = "SELECT `$id_field`, `$field` FROM `$table` WHERE `$field` LIKE '%{$oldstring}%'";

    $result = sql_query($qry);
    if ($result) {
        while (list($id, $text) = sql_fetch_row($result)) {
            $sqlqry[] = "UPDATE `$table` SET `$field`='" . sql_real_escape_string(str_replace($oldstring, $newstring, $text)) . "' WHERE `$id_field`=" . intval($id);
        }
    }

    return $sqlqry;
}

/**
 * !! wird in v2.0 noch nicht verwendet !!!
 */
function setup_convert_debugmode($debugmode)
{
    $default = array(16, 2, 0);
    switch (true) {
        case !$debugmode:
            $newmode = array(0, 0, 0);
            break;
        case is_numeric($debugmode):
            $newmode = array_fill(0, 3, intval($debugmode));
            break;
        case is_array($debugmode):
            // das array auf min. 3 Werte bringen und dann auf 3 Werte kürzen
            $newmode = array_slice(array_merge($debugmode, array(0, 0, 0)), 0, 3);
            break;
        default:
            $newmode = $default;
    }
    return implode('|', $newmode);
}

/**
 * setup_get_available_avatars()
 *
 * @param mixed $path
 * @return
 */
function setup_get_available_avatars($path)
{
    $endings = array('gif', 'png', 'jpg', 'jpeg');
    $filelist = array();
    $tmp = (array)glob(str_replace(DS, '/', $path . '/*'));
    foreach ($tmp as $image) {
        $info = pathinfo($image);
        if (isset($info['extension']) && in_array(strtolower($info['extension']), $endings)) {
            $filelist[$info['basename']] = $image;
        }
    }
    natcasesort($filelist);
    return $filelist;
}

function setup_pushsettings($section, $settings_array, $replace = false)
{
    global $prefix;

    $parts = array();
    $exist = array();

    $qry = " SELECT `key`
             FROM `{$prefix}_sys_config`
             WHERE `section`='" . sql_real_escape_string($section) . "'";
    $result = sql_query($qry);
    $out = array();
    while (list($key) = sql_fetch_row($result)) {
        $exist[] = $key;
    }
    foreach ($settings_array as $key => $value) {
        if (in_array($key, $exist)) {
            continue;
        }
        if (is_scalar($value)) {
            $isserial = 0;
        } else {
            $value = serialize($value);
            $isserial = 1;
        }
        $parts[] = "('" . sql_real_escape_string($section) . "', '" . sql_real_escape_string($key) . "', '" . sql_real_escape_string($value) . "', '" . $isserial . "')";
    }

    if (!$parts) {
        return false;
    }

    if ($replace) {
        $qry = "REPLACE INTO";
    } else {
        $qry = "INSERT IGNORE ";
    }
    $qry .= "INTO `{$prefix}_sys_config` (`section`, `key`, `value`, `serialized`)
              VALUES " . implode(', ', $parts);
    // mxDebugFuncVars($qry);
    return $qry;
}

function setup_set_config_section($section, $settings_array, $replace = false)
{
    global $prefix;

    $sqlqry = setup_pushsettings($section, $settings_array, $replace);
    if ($sqlqry) {
        setupDoAllQueries($sqlqry);
        return true;
    }

    return false;
}

function setup_set_config_value($section, $key, $value)
{
    $settings_array = array($key => $value);
    return setup_set_config_section($section, $settings_array, true);
}

function setup_get_config_value($section, $key)
{
    global $prefix;

    $qry = "SELECT `key`, `value`, `serialized`
           FROM `{$prefix}_sys_config`
           WHERE `section`='" . sql_real_escape_string($section) . "'
             AND `key`='" . sql_real_escape_string($key) . "'";
    $result = sql_query($qry);
    if ($row = sql_fetch_assoc($result)) {
        if ($row['serialized']) {
            return unserialize($row['value']);
        }
        return $row['value'];
    }
    return false;
}

/**
 * setup_set_sql_names()
 * Probleme mit evtl. falschem Charset beheben
 *
 * @param string $charset
 * @return null
 */
function setup_set_sql_names($charset = 'utf8')
{
    sql_query("SET
      names '$charset',
      character set '$charset',
      character_set_results = '$charset',
      character_set_client = '$charset',
      character_set_connection = '$charset',
      character_set_database = '$charset',
      character_set_server = '$charset'
    ");
}

/**
 * setup_prettyprinter()
 * benötigter header-code für pretty-printer anzeigen
 *
 * @return
 */
function setup_prettyprinter()
{
    global $arr_header;
    $arr_header[] = '<script type="text/javascript" src="../includes/javascript/jquery/jquery.min.js"></script>';
    $arr_header[] = '<script type="text/javascript" src="html/js/prettify.js"></script>';
    $arr_header[] = '<script type="text/javascript">$(document).ready(function () {prettyPrint();});</script>';
}

/**
 * setup_form_submit_message()
 * Bitte Warten Anzeige und Check der Backup-Option
 *
 * @param mixed $message
 * @param string $caption
 * @return
 */
function setup_form_submit_message($message, $caption = '')
{
    global $arr_header;
    $arr_header[] = '<link rel="stylesheet" type="text/css" href="../layout/jquery/css/prettyPhoto.css" />';
    $arr_header[] = '<script type="text/javascript" src="../includes/javascript/jquery/jquery.min.js"></script>';
    $arr_header[] = '<script type="text/javascript" src="../includes/javascript/jquery/jquery.prettyPhoto.min.js"></script>';

    ob_start();
    // margin: xx, verursacht bei prettyPhoto einen Balken über dem Inhalt, deswegen expliziet 0
    ?>
<div id="inline-1" class="hide" style="margin: 0">
<h3 style="margin: 0"><?php echo $caption ?></h3>
<div><?php echo $message ?></div>
</div>

<script type="text/javascript">

$(document).ready(function () {
  $.fn.prettyPhoto({
    animation_speed: 'fast',
    opacity: 0.4,
    show_title: false,
    allow_resize: true,
    allow_expand: false,
    theme: 'pp_default',
    wmode: 'opaque',
    modal: true,
    deeplinking: false,
    keyboard_shortcuts: false,
    social_tools: false,
    ie6_fallback: false,
    // close button ist über markup ausgeblendet
    markup: '<div class="pp_pic_holder"><div class="ppt">&nbsp;<\/div><div class="pp_top"><div class="pp_left"><\/div><div class="pp_middle"><\/div><div class="pp_right"><\/div><\/div><div class="pp_content_container"><div class="pp_left"><div class="pp_right"><div class="pp_content"><div class="pp_loaderIcon"><\/div><div class="pp_fade"><div id="pp_full_res"><\/div><div class="pp_details"><\/div><\/div><\/div><\/div><\/div><\/div><div class="pp_bottom"><div class="pp_left"><\/div><div class="pp_middle"><\/div><div class="pp_right"><\/div><\/div><\/div><div class="pp_overlay"><\/div>'
  });


  $("form.dbup").submit(function (event) {

    // check ob Bestätigung angekreuzt
    if ($(this).find("input[name='beshure']").val() && (!$(this).find("input[name='beshure']").is(':checked'))) {
        alert('<?php echo addslashes(_BACKUPBESHUREOK) ?>');
        event.preventDefault();
        return false;
    }
    // beim Schliessen Warteanzeige....
    $.prettyPhoto.open('#inline-1');
  });
});

</script>
<?php
    return ob_get_clean();
}

/*
// ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Funktionen, die normalerweise im System vorhanden sind, aber genauso auch im Setup verwendet werden müssen ///
// ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 */

/**
 */
// if (!function_exists('mxDemoSetCookie')) {
// function mxDemoSetCookie()
// {
// return true;
// }
// }
/**
 */
// if (!function_exists('mxGetModServiceContent')) {
// function mxGetModServiceContent($type)
// {
// $modservicecontent = '';
// if (is_file('FILE_CONFIG_ROOT')) {
// include('FILE_CONFIG_ROOT');
// }
// return $modservicecontent;
// }
// }
/**
 * Local function to provide list of all possible HTML tags
 */
// if (!function_exists('settingsGetHTMLTags')) {
// function settingsGetHTMLTags()
// {
// // Possible allowed HTML tags
// $allowed = array('!--',
// 'a',
// 'abbr',
// 'acronym',
// 'address',
// 'applet',
// 'area',
// 'b',
// 'base',
// 'basefont',
// 'bdo',
// 'big',
// 'blockquote',
// 'br',
// 'br /',
// 'button',
// 'caption',
// 'center',
// 'cite',
// 'code',
// 'col',
// 'colgroup',
// 'del',
// 'dfn',
// 'dir',
// 'div',
// 'dl',
// 'dd',
// 'dt',
// 'em',
// 'embed',
// 'fieldset',
// 'font',
// 'form',
// 'h1',
// 'h2',
// 'h3',
// 'h4',
// 'h5',
// 'h6',
// 'hr',
// 'i',
// 'iframe',
// 'img',
// 'input',
// 'ins',
// 'kbd',
// 'label',
// 'legend',
// 'li',
// 'map',
// 'marquee',
// 'menu',
// 'nobr',
// 'object',
// 'ol',
// 'optgroup',
// 'option',
// 'p',
// 'param',
// 'pre',
// 'q',
// 's',
// 'samp',
// 'script',
// 'select',
// 'small',
// 'span',
// 'strike',
// 'strong',
// 'sub',
// 'sup',
// 'table',
// 'tbody',
// 'td',
// 'textarea',
// 'tfoot',
// 'th',
// 'thead',
// 'tr',
// 'tt',
// 'u',
// 'ul',
// 'var');
// asort($allowed);
// return $allowed;
// }
// }

?>