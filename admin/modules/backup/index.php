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
    echo '
    	<div class="card">
  			<div class="card-body">
    			<ul class="list-unstyled">
    				<li>' . implode('</li><li>', $msg) . '</li>
    			</ul>			
    				<a href="' . adminUrl(PMX_MODULE) . '" class="btn btn-primary">' . _DB_BACKUP_9 . '</a>	
    		</div>
    	</div>';
    include('footer.php');
}

function backupstart()
{
    global $savepath, $onetoone, $dbname;

    $savefile = $dbname . date("_Y_m_d", time()) . '_' . rand(234, 912) . '.sql';
    $docroot = realpath(PMX_REAL_BASE_DIR . '/');

    include('header.php');
    title(_SAVEDATABASETITLE);
    echo '
    	<div class="card">
  			<div class="card-body">

    			<form action="' . adminUrl(PMX_MODULE) . '" method="post" name="doit">
    				<input type="hidden" name="op" value="' . PMX_MODULE . '/doit" />

				    <div class="row">
      					<label for="savepath" class="col-md-2 form-control-label">' . _DB_BACKUP_10 . '</label>
      					<div class="col-md-5">
                  <input type="text" class="form-control" id="savepath" name="savepath" value="' . $savepath . '" size="50" />
      						<span class="help-block text-muted small">
      							 ' . _DB_BACKUP_11 . ' (' . $docroot . ')
      						</span>   					
      					</div>
    				</div>

				    <div class="row">
      					<label for="savefile" class="col-md-2 form-control-label">' . _DB_BACKUP_12 . '</label>
      					<div class="col-md-5">
      						<input type="text" class="form-control" id="savefile" name="savefile" value="' . $savefile . '" size="50" maxlength="80" />				
      					</div>
    				</div>

    <div class="row">
      <legend class="col-form-legend col-md-2">' . _DB_BACKUP_13 . '</legend>
      <div class="col-sm-10">
        <div class="form-check">
          <label class="form-check-label">
          	<input type="radio" class="form-check-input" name="ff" value="none" checked="checked" /> ' . _DB_BACKUP_16 . '
          </label>
        </div>';

    if (!strcmp(substr(PHP_OS, 0, 3), 'WIN')) {
        echo '
        <div class="form-check">
          <label class="form-check-label">
          	<input class="form-check-input" type="radio" name="ff" value="minigzip" /> ' . _DB_BACKUP_14 . '
          	<span class="help-block">' . _DB_BACKUP_19 . '</span>
          </label>
        </div>';
    } else {
        echo '
        <div class="form-check">
          <label class="form-check-label">
          	<input class="form-check-input" type="radio" name="ff" value="gzip" />' . _DB_BACKUP_15 . '
          	<span class="help-block">' . _DB_BACKUP_20 . '</span>
          </label>
        </div>';
    }

    echo '
        </div>
    </div>
    <div class="row">
      <div class="col-md-2 offset-md-2">
    			<button type="submit" class="btn btn-primary"><i class="fa fa-check fa-lg"></i>&nbsp;' . _DB_BACKUP_17 . '</button>
    </div>
    			</form>
 
    	</div>
    </div>';
    include('footer.php');
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