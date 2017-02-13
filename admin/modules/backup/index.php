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
 * $Revision: 101 $
 * $Author: PragmaMx $
 * $Date: 2015-12-30 21:08:19 +0100 (Mi, 30. Dez 2015) $
 */

defined('mxMainFileLoaded') or die('access denied');

if (!mxGetAdminPref('radminsuper')) {
    return mxRedirect(adminUrl(), 'Access Denied');
}

if (file_exists('dynadata/backup') || file_exists('dynadata')) {
    $savepath = 'dynadata/backup';
} else {
    $savepath = 'DB_backup';
}

/* Sprachdatei auswählen */
mxGetLangfile(__DIR__);

function backupdoit()
{
    global $dbhost, $dbuname, $dbpass, $dbname, $dbi;

    set_time_limit(100);
    if (!sql_connect($dbhost, $dbuname, $dbpass)) {
        $msg[] = sprintf(_DB_BACKUP_1, $dbhost);
    }
    if (!sql_select_db($dbname, $dbi)) {
        $msg[] = sprintf(_DB_BACKUP_2, $dbname);
    }
    $path = realpath(PMX_REAL_BASE_DIR) . '/' . $_POST['savepath'];

    if (!file_exists($path)) {
        $msg[] = sprintf(_DB_BACKUP_3, $path);
        if (mkdir($path, PMX_CHMOD_FULLUNOCK)) {
            $msg[] = sprintf(_DB_BACKUP_4, $path);
        } else {
            $msg[] = sprintf(_DB_BACKUP_5, $path);
        }
    }

    if (!file_exists($path)) {
        $msg[] = sprintf(_DB_BACKUP_3, $path);
    } else {
        // Dummy-HMTL-Datei im Backupverzeichnis erstellen
        if (!file_exists($path . '/index.html')) {
            $ok = mx_write_file($path . '/index.html', 'access denied');
        }
        // .htaccess Datei im Backupverzeichnis erstellen
        if (!file_exists($path . '/.htaccess')) {
            $ok = mx_write_file($path . '/.htaccess', 'deny from all');
        }
        // Befehl ausführen und in Zipfile speichern
        $u = ($dbuname) ? " -u $dbuname " : '';
        $p = ($dbpass) ? " -u $dbpass " : '';
        $msg[] = sprintf(_DB_BACKUP_6, $path);
        if ($_POST['ff'] === 'minigzip') {
            $action = 'mysqldump --opt -h %s -u %s -p%s %s | minigzip > %s/';
            $thefile = $_POST['savefile'] . '.gz';
        } elseif ($_POST['ff'] === 'gzip') {
            $action = 'mysqldump --opt -h %s -u %s -p%s %s | gzip > %s/';
            $thefile = $_POST['savefile'] . '.gz';
        } else {
            $action = 'mysqldump --opt -h %s -u %s -p%s %s > %s/';
            $thefile = $_POST['savefile'] . '.txt';
        }

        if (function_exists('system')) {
            system(sprintf($action . $thefile, $dbhost, $dbuname, $dbpass, $dbname, $path));
        }

        if (is_file("$path/$thefile") && filesize("$path/$thefile")) {
            $msg[] = sprintf(_DB_BACKUP_7, $dbname, $thefile);
        } else {
            $msg[] = sprintf(_DB_BACKUP_8, $dbname);
            if (file_exists("$path/$thefile"))unlink("$path/$thefile"); // loeschen, falls leer
        }
    }
    include('header.php');
    title(_SAVEDATABASE);
    OpenTable();
    echo '<ul><li>' . implode('</li><li>', $msg) . '</li></ul>';
    echo "<br /><br /><div align=\"center\"><a href=\"" . adminUrl(PMX_MODULE) . "\">" . _DB_BACKUP_9 . "</a></div>";
    CloseTable();
    include('footer.php');
}

function backupstart()
{
    global $savepath, $onetoone, $dbname;

    $savefile = $dbname . date("_Y_m_d", time()) . '_' . rand(234, 912) . '.sql';
    $docroot = realpath(PMX_REAL_BASE_DIR . '/');

    include("header.php");
    title(_SAVEDATABASETITLE);
    OpenTable();
    echo "<form action=\"" . adminUrl(PMX_MODULE) . "\" method=\"post\" name=\"doit\">
    <input type=\"hidden\" name=\"op\" value=\"" . PMX_MODULE . "/doit\" />
    <table cellspacing=\"0\" cellpadding=\"3\">
    <tr>
    <td>" . _DB_BACKUP_10 . ":</td>
    <td><input type=\"text\" name=\"savepath\" value=\"" . $savepath . "\" size=\"50\" /><br />
    <font class=\"note tiny\">" . _DB_BACKUP_11 . " (" . $docroot . ")</font></td>
    </tr><tr>
    <td>" . _DB_BACKUP_12 . ":</td>
    <td><input type=\"text\" name=\"savefile\" value=\"" . $savefile . "\" size=\"50\" maxlength=\"80\" /></td>
    </tr><tr>
    <td colspan=\"2\"><br />" . _DB_BACKUP_13 . "</td>
    </tr><tr>
    <td align=\"right\" valign=\"top\"><input type=\"radio\" name=\"ff\" value=\"none\" checked=\"checked\" /></td><td>" . _DB_BACKUP_16 . "</td>
    </tr><tr><td align=\"right\" valign=\"top\">";
    if (!strcmp(substr(PHP_OS, 0, 3), 'WIN')) {
        echo "<input type=\"radio\" name=\"ff\" value=\"minigzip\" /></td><td>" . _DB_BACKUP_14 . "
        <br /><span class=\"tiny\">" . _DB_BACKUP_19 . "</span>";
    } else {
        echo "<input type=\"radio\" name=\"ff\" value=\"gzip\" /></td><td>" . _DB_BACKUP_15 . "
        <br /><span class=\"note tiny\">" . _DB_BACKUP_20 . "</span>";
    }
    echo "</td></tr><tr>
    <td colspan=\"2\" align=\"center\"><br />
    <input type=\"submit\" value=\"" . _DB_BACKUP_17 . "\" />
    &nbsp;<input type=\"reset\" value=\"" . _DB_BACKUP_18 . "\" />
    </td></tr></table>
    </form>";
    CloseTable();
    include("footer.php");
}

switch ($op) {
    case PMX_MODULE . '/doit' :
        backupdoit();
        break;
    default:
        backupstart();
        break;
}

?>