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

switch ($_REQUEST['op']) {
    /**
     */
    case 'setup';
        $option = _STEP_DBSETTINGS;
        // check, ob die Datei mit der Grundkonfiguration vorhanden ist
        if (!@file_exists(FILE_CONFIG_BASE) || !is_readable(FILE_CONFIG_BASE)) {
            $output = '
              <p>' . _THEREERROR . '</p>
              <ul>
                <li>&quot;<em>' . FILE_CONFIG_BASE . '</em>&quot; ' . _FILE_NOT_FOUND . '</li>
              </ul>
              <div class="row">
                <div class="span1">
                  <form action="index.php" method="post">
                    <p>' . hiddenrequest($_REQUEST['lastop']) . '
                      <input class="btn btn-link" type="submit" value="' . _GOBACK . '" />
                    </p>
                  </form>
                </div>
                <div class="span2">
                  <form action="index.php" method="post">
                    <p>' . hiddenrequest('setup') . '
                      <input class="btn" type="submit" value="' . _REMAKE . '" />
                    </p>
                  </form>
                </div>
              </div>';
        } else {
            if (isset($_REQUEST['dbhost']) && isset($_REQUEST['dbname']) && isset($_REQUEST['dbuname']) && isset($_REQUEST['dbpass']) && isset($_REQUEST['dbconnect']) && isset($_REQUEST['prefix']) && isset($_REQUEST['user_prefix'])) {
                $set = $_REQUEST;
            } else {
                $set = setupFormDefaults();
            }
            $output = '
              <p>' . _DBSETTINGS . '</p>
              <form class="form-horizontal" action="index.php" method="post">
                <p>' . hiddenrequest('checkdb') . '</p>
                ' . dbsettings($set) . '
                <div class="control-group">
                  <div class="controls">
                    <input class="btn" type="submit" value="' . _SUBMIT . '" />
                  </div>
                </div>
              </form>';
        }
        break;
    /**
     */
    case 'checkdb';
        $err = '';
        $dbnotexist = false;
        $set = setupFormDefaults();
        $_REQUEST['dbhost'] = (empty($_REQUEST['dbhost'])) ? $set['dbhost'] : $_REQUEST['dbhost'];
        $_REQUEST['prefix'] = (empty($_REQUEST['prefix'])) ? $set['prefix'] : $_REQUEST['prefix'];
        $_REQUEST['user_prefix'] = (empty($_REQUEST['user_prefix'])) ? $set['user_prefix'] : $_REQUEST['user_prefix'];
		$_REQUEST['dbconnect']=(empty($_REQUEST['dbconnect'])) ? $set['dbconnect'] : $_REQUEST['dbconnect'];
		
        $dbi = @sql_connect($_REQUEST['dbhost'], $_REQUEST['dbuname'], $_REQUEST['dbpass'], $_REQUEST['dbname']);
        if (!$dbi) {
            $err = '
              <div class="alert alert-block">
                <p>' . _NOT_CONNECT . '</p>
                <p>' . _SERVERMESSAGE . ': ' . sql_error() . ' (' . sql_errno() . ')</p>
              </div>';
        } else {
            /* Probleme mit evtl. falschem Charset beheben */
            setup_set_sql_names('utf8');

            if (empty($_REQUEST['dbname'])) {
                $err = '<p class="warning">' . _DBNOTSELECT . '</p>';
                $dbnotexist = true;
            } else if (!sql_select_db($_REQUEST['dbname'] , $dbi)) {
                $errno = sql_errno();
                if ($errno == 1046 || $errno == 1049) {
                    // DB existiert nicht oder unbekannt
                    $err = '
                      <div class="alert alert-block">
                        <p>' . _DBNOTEXIST . '</p>
                        <p>' . _SERVERMESSAGE . ': ' . sql_error() . ' (' . $errno . ')</p>
                      </div>';
                    $dbnotexist = true;
                } else if ($errno == 1044 || $errno == 1045) {
                    // Zugriff auf DB/Server verweigert
                    $err = '
                      <div class="alert alert-block">
                        <p>' . _DBNOACCESS . '</p>
                        <p>' . _SERVERMESSAGE . ': ' . sql_error() . ' (' . $errno . ')</p>
                      </div>';
                    $dbnotexist = false;
                } else {
                    // sonstige DB-Fehler
                    $err = '
                      <div class="alert alert-block">
                        <p>' . _DBOTHERERR . '</p>
                        <p>' . _SERVERMESSAGE . ': ' . sql_error() . ' (' . $errno . ')</p>
                      </div>';
                    $dbnotexist = false;
                }
            }
            sql_close();
        }

        if ($err) {
            $option = _STEP_DBSETTINGSCREATE;
            $output = $err;
            if ($dbnotexist && !empty($_REQUEST['dbname'])) {
                $output .= '
                  <p>' . _DBCREATEQUEST . '</p>
                  <div class="row">
                    <div class="span1">
                      <form action="index.php" method="post">
                        <p>' . hiddenrequest('setup') . '
                          <input class="btn" type="submit" value="' . _NO . '" />
                        </p>
                      </form>
                    </div>
                    <div class="span1">
                      <form action="index.php" method="post">
                        <p>' . hiddenrequest('createdb') . '
                          <input class="btn" type="submit" value="' . _YES . '" />
                        </p>
                      </form>
                    </div>
                    <hr />
                    <h3>' . _OR . ':</h3>
                  </div>';
            }
            $output .= '
              <div class="alert alert-info alert-block">' . _CHECKSETTINGS . '</div>
              <p>' . _DBSETTINGS . '</p>
              <form class="form-horizontal" action="index.php" method="post">
                <p>' . hiddenrequest('checkdb') . '</p>
                ' . dbsettings($_REQUEST) . '
                <div class="control-group">
                  <div class="controls">
                    <input class="btn" type="submit" value="' . _SUBMIT . '" />
                  </div>
                </div>
              </form>';
        } else {
            $option = _STEP_MORESETTINGS;
            $output = '
              <p>' . _PREFIXSETTING . '<br />' . _PRERR11 . '</p>
              <form class="form-horizontal" action="index.php" method="post">
              <p>' . hiddenrequest('viewsettings') . '</p>
              <fieldset>
                <legend>' . _PREFIXE . '</legend>
                <div class="control-group">
                    <label class="control-label" for="prefix">' . _PREFIX . ':</label>
                    <div class="controls">
                      <input type="text" id="prefix" name="prefix" value="' . $set['prefix'] . '" size="25" />
                    </div>
                </div>
                <div class="control-group">
                      <label class="control-label" for="user_prefix">' . _USERPREFIX . ':</label>
                      <div class="controls">
                        <input type="text" id="user_prefix" name="user_prefix" value="' . $set['user_prefix'] . '" size="25" />
                      </div>
                </div>
              </fieldset>

              <fieldset>
              <legend>' . _SITE__MORESETTINGS . '</legend>
                <div class="control-group">
                    <label class="control-label" for="defaultlang">' . _DEFAULTLANG . ':</label>
                    <div class="controls">
                      ' . setupLanguageSelect('language', PMX_LANGUAGE_DIR) . '
                  </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="sitename">' . _SITENAME . ':</label>
                    <div class="controls">
                      <input type="text" id="sitename" name="sitename" value="' . $set['sitename'] . '" size="50" maxlength="100" />
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="stardate">' . _STARTDATE . ':</label>
                    <div class="controls">
                      <input type="text" id="stardate" name="startdate" value="' . $set['startdate'] . '" size="30" maxlength="30" />
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="adminmail">' . _ADMINEMAIL . ':</label>
                    <div class="controls">
                      <input type="text" id="adminmail" name="adminmail" value="' . $set['adminmail'] . '" size="30" maxlength="100" />
                    </div>
                </div>
                 <div class="control-group">
                    <label class="control-label" for="vkpIntranet">' . _INTRANETOPT . ':</label>
                    <div class="controls">
                      <p>
                      <input type="radio" id="vkpIntranet" name="vkpIntranet" value="0"' . ((empty($set['vkpIntranet'])) ? ' checked="checked"' : '') . ' />&nbsp;' . _NO . '&nbsp;
                      <input type="radio" name="vkpIntranet" value="1"' . ((empty($set['vkpIntranet'])) ? '' : ' checked="checked"') . ' />&nbsp;' . _YES . '
                      </p>
                      <div class="alert alert-info">' . _INTRANETWARNING . '</div>
                     </div>
                </div>
              </fieldset>
              <div class="control-group">
                <div class="controls">
                  <input class="btn" type="submit" value="' . _SUBMIT . '" />
                </div>
              </div>
            </form>';
        }
        break;
    /**
     */
    case 'viewsettings';
        $option = _STEP_MORESETTINGSCHECK;
        $errprefix = setupCheckNewPrefixes($_REQUEST['prefix'], $_REQUEST['user_prefix'], false);
        $prefixmsg = ($errprefix[0] != 'ok') ? '<div class="alert alert-block">' . $errprefix[1] . '</div>' : '';
		$vdbconn=array(0=>"mysql",1=>"mysqli",2=>"pdo");
        $output = '
          <p>' . _PLEASECHECKSETTINGS . '</p>
          <table class="table table-striped">
            <tbody>
            <tr>
              <td>' . _DBSERVER . '</td>
              <td><strong>' . $_REQUEST['dbhost'] . '</strong></td>
            </tr>
              <td>' . _DBNAME . '</td>
              <td><strong>' . $_REQUEST['dbname'] . '</strong></td>
            </tr>
            <tr>
              <td>' . _DBUSERNAME . '</td>
              <td><strong>' . $_REQUEST['dbuname'] . '&nbsp;</strong></td>
            </tr>
            <tr>
              <td>' . _DBPASS . '</td>
              <td><strong>' . $_REQUEST['dbpass'] . '&nbsp;</strong></td>
            </tr>
            <tr>
              <td>' . _PREFIX . '</td>
              <td><strong>' . $_REQUEST['prefix'] . '</strong></td>
            </tr>
            <tr>
              <td>' . _USERPREFIX . '</td>
              <td><strong>' . $_REQUEST['user_prefix'] . '</strong></td>
            </tr>
            <tr>
              <td>' . _DBCONNECT . '</td>
              <td><strong>' . $vdbconn[$_REQUEST['dbconnect']] . '</strong></td>
            </tr>			
            <tr>
              <td>' . _DEFAULTLANG . '</td>
              <td><strong>' . $_REQUEST['language'] . '</strong></td>
            </tr>
            <tr>
              <td>' . _SITENAME . '</td>
              <td><strong>' . $_REQUEST['sitename'] . '</strong></td>
            </tr>
            <tr>
              <td>' . _ADMINEMAIL . '</td>
              <td><strong>' . $_REQUEST['adminmail'] . '</strong></td>
            </tr>
            <tr>
              <td>' . _STARTDATE . '</td>
              <td><strong>' . $_REQUEST['startdate'] . '</strong></td>
            </tr>
            <tr>
              <td>' . _INTRANETOPT . '</td>
              <td><strong>' . ((empty($_REQUEST['vkpIntranet'])) ? _NO : _YES) . '</strong></td>
            </tr>
            </tbody>
        </table>
        ' . $prefixmsg . '
        <div class="row">
          <div class="span2">
            <form action="index.php" method="post">
              <p>' . hiddenrequest('setup') . '
                <input class="btn" type="submit" value="' . _CORRECTION . '" />&nbsp;&nbsp;
              </p>
            </form>
          </div>';

        if ($errprefix[0] != 'critical') {
            $capt = ($errprefix[0] == 'check') ? _IGNORE : _SUBMIT;
            $output .= '
            <div class="span2">
              <form action="index.php" method="post">
                <p>' . hiddenrequest('savesettings') . '
                  <input class="btn" type="submit" value="' . $capt . '" />
                </p>
              </form>
            </div>';
        }
        $output .= '
        </div>';

        break;
    /**
     */
    case 'createdb';
        $ok = false;
        $dbi = @sql_connect($_REQUEST['dbhost'], $_REQUEST['dbuname'], $_REQUEST['dbpass']);
        if (!$dbi) {
            $msg = _NOT_CONNECT . '<br />' . _SERVERMESSAGE . ': ' . sql_error() . ' (' . sql_errno() . ')';
        } else {
            /* Probleme mit evtl. falschem Charset beheben */
            setup_set_sql_names('utf8');

            if (sql_query('CREATE DATABASE `' . $_REQUEST['dbname'] . '` DEFAULT CHARACTER SET utf8  COLLATE utf8_unicode_ci;', $dbi)) {
                $msg = _DBISCREATED;
                $ok = true;
            } else {
                $msg = _DBNOTCREATED . '<br />' . _SERVERMESSAGE . ': ' . sql_error() . ' (' . sql_errno() . ')';
            }
        }
        if ($ok) {
            $option = _STEP_DBSETTINGS;
            $output = '
              <p><strong>' . $msg . '</strong></p>
              <form action="index.php" method="post">
                <p>' . hiddenrequest('checkdb') . '
                  <input class="btn" type="submit" value="' . _SUBMIT . '" />
                </p>
              </form>';
        } else {
            $option = _STEP_DBSETTINGSCREATE;
            $output = '
              <div class="alert alert-block">' . $msg . '</div>
              <p>' . _DBSETTINGS . '</p>
              <form class="form-horizontal" action="index.php" method="post">
                <p>' . hiddenrequest('checkdb') . '</p>
                ' . dbsettings($_REQUEST) . '
                <div class="control-group">
                  <div class="controls">
                    <input class="btn" type="submit" value="' . _SUBMIT . '" />
                  </div>
                </div>
              </form>';
        }
        break;
    /**
     */
    case 'savesettings';
        $option = _CURRENTSTATUS;
        // echo "Hier1";
        require('includes/function_saveconfigfile.php');
        $status = SetupSaveConfigFile($_REQUEST);
		
		SetupSaveUserConfigFile();  //Usersettings testen 
        //mxDebugFuncVars($status)		;
        if ($status['ok']) {
            include(FILE_CONFIG_ROOT);
            $tablecount = 0;
            $mxcount = 0;
            $mxusercount = 0;
            $dbstat = setupConnectDb();
            if (isset($dbstat['dbi'])) {
                $result = sql_query("SHOW TABLES FROM `" . $dbname . "`");
                if ($result) {
                    while ($table = sql_fetch_row($result)) {
                        preg_match('#^(' . $user_prefix . '_users)|^(' . $prefix . '_)#i', $table[0], $matches);
                        $tablecount++;
                        // mxDebugFuncVars($matches);
                        if (!empty($matches[1])) $mxusercount++;
                        else if (!empty($matches[2])) $mxcount++;
                    }
                    // mxDebugFuncVars($tablecount, $mxcount, $mxusercount);
                }
            }
            if ($mxcount) {
                $dbmsg = sprintf(_PRERR17, $mxcount);
                $errprefix = true;
            } else if ($tablecount) {
                // backup checken
                require('includes/function_dbbackup.php');
                $backup_available = setup_dbbackup('only_check');
                if ($backup_available) {
                    $domsg = '
                      <p>' . _WILL_CREATE_TABLES . '</p>
                      <p>' . _WILL_CREATE_BACKUP . '</p>';
                } else {
                    $domsg = '
                      <p>' . _WILL_CREATE_TABLES . '</p>
                      <p>' . _BACKUPBESHURE . '</p>';
                }
                $dbmsg = _DBARETABLES;
            } else {
                $dbmsg = _DBARENOTABLES;
                $domsg = '<p>' . _WILL_CREATE_TABLES . '</p>';
            }

            if (isset($errprefix)) {
                $output = '
                  <div class="alert alert-block">' . $dbmsg . '</div>
                  <form action="index.php" method="post">
                    <p>' . hiddenrequest('setup') . '
                      <input class="btn" type="submit" value="' . _CORRECTION . '" />
                    </p>
                  </form>';
            } else {
                $output = '
                  <div class="alert alert-success alert-block">
                    <ul>' . $status['msg'] . ' ' . $dbmsg . '</ul>
                  </div>
                  ' . $domsg . '
                  <form action="index.php" method="post">
                    <p>' . hiddenrequest('setup') . '
                      <input class="btn" type="submit" value="' . _CORRECTION . '" />&nbsp;&nbsp;
                    </p>
                  </form>';

                if ($tablecount) {
                    if ($backup_available) {
                        $output .= setup_form_submit_message(_DBUP_MESSAGE, _DBUP_WAIT);
                        $output .= '
                    <div class="row">
                      <div class="span1">
                        <form action="index.php" method="post" class="dbup">
                          <p>' . hiddenrequest('dbup') . '</p>
                            <input type="hidden" name="createbackup" value="1" />
                            <input class="btn" type="submit" value="' . _CONTINUE_WITHDBBACKUP . '" />
                        </form>
                      </div>
                      <div class="span1">
                        <form action="index.php" method="post" class="dbup">
                          <p>' . hiddenrequest('dbup') . '</p>
                            <input type="hidden" name="createbackup" value="0" />
                            <input class="btn" type="submit" value="' . _CONTINUE_WITHOUTDBBACKUP . '" />&nbsp;&nbsp;
                        </form>
                      </div>
                    </div>';
                    } else {
                        $output .= setup_form_submit_message(_DBUP_MESSAGE, _DBUP_WAIT);
                        $output .= '
                  <form action="index.php" method="post" class="dbup">
                    <p>' . hiddenrequest('dbup') . '</p>
                    <div class="row">
                      <div class="span1">
                        <input type="hidden" name="cantbackup" value="0" />
                        <input class="btn" type="submit" value="' . _SUBMIT . '" />
                      </div>
                      <div class="span6">
                         <input class="checkbox" type="checkbox" name="beshure" value="1" title="' . _BACKUPBESHUREOK . '" />
                         &nbsp;' . _BACKUPBESHUREYES . '
                      </div>
                    </div>
                  </form>
                  ';
                    }
                } else {
                    $output .= setup_form_submit_message(_DBUP_MESSAGE, _DBUP_WAIT);
                    $output .= '
                      <form action="index.php" method="post" class="dbup">
                        <p>' . hiddenrequest('dbup') . '
                          <input type="hidden" name="createbackup" value="0" />
                          <input class="btn" type="submit" value="' . _SUBMIT . '" />
                        </p>
                      </form>';
                }
            }
        } else {


				$output = '
				<p><strong>' . _THEREERROR . '</strong></p>
				<div class="info">
				  <ul>' . $status['msg'] . '</ul>
				</div>
				<div class="row">
				  <div class="span1">
					<form action="index.php" method="post">
					  <p>' . hiddenrequest('setup') . '
						<input class="btn" type="submit" value="' . _CORRECTION . '" />
					  </p>
					</form>
				  </div>
				  <div class="span1">
					<form action="index.php" method="post">
					  <p>' . hiddenrequest('showconfig') . '
						<input type="hidden" name="config_php" value="' . $status['config_php'] . '" />
						<input class="btn" type="submit" value="' . _CONFIG_BUTTONMAN . '" />
					  </p>
					</form>
				  </div>
				</div>';
				
        }
        break;
    /**
     */
    case 'showconfig';
		
        $option = _CONFIG_BUTTONMAN;
        $output = '
          <p>' . _CONFIG_CREATE . '</p>
          <p><textarea cols="180" rows="25" name="xxconfigphp" style="width:100%">' . stripslashes($_POST['config_php']) . '</textarea></p>
          <div class="row">
            <div class="span1">
              <form action="index.php" method="post">
                <p>' . hiddenrequest($_REQUEST['lastop']) . '
                  <input class="btn btn-link" type="submit" value="' . _GOBACK . '" />
                </p>
            </form>
            <div class="span1">
              <form action="index.php" method="post">
                <p>' . hiddenrequest('savesettings') . '
                  <input class="btn" type="submit" value="' . _SUBMIT . '" />
                </p>
              </form>
            </div>
          </div>';
        break;
    /**
     */
    case 'dbup';
        $createbackup = (empty($_POST['createbackup'])) ? 0 : 1;
        if ($createbackup) {
            require('includes/function_dbbackup.php');
            $backup = setup_dbbackup();
        } else {
            if (isset($_POST['cantbackup'])) {
                // nicht bestaetigt
                if (empty($_POST['beshure'])) {
                    $option = _STEP_BACKUP;
                    $output .= setup_form_submit_message(_DBUP_MESSAGE, _DBUP_WAIT);
                    $output .= '
                    <p>' . _BACKUPBESHURE . '</p>
                    <div class="alert alert-block">' . _BACKUPBESHUREOK . '</div>
                    <div class="row">
                      <div class="span2">
                        <form action="index.php" method="post">
                          <p>' . hiddenrequest('setup') . '
                            <input class="btn" type="submit" value="' . _CORRECTION . '" />
                          </p>
                        </form>
                      </div>
                      <div class="span6">
                        <form action="index.php" method="post" class="dbup">
                          <p>' . hiddenrequest('dbup') . '
                            <input type="hidden" name="cantbackup" value="0" />
                            <input class="btn" type="submit" value="' . _SUBMIT . '" />&nbsp;&nbsp;&nbsp;
                            <input class="checkbox" type="checkbox" name="beshure" value="1" title="' . _BACKUPBESHUREOK . '" />
                            &nbsp;' . _BACKUPBESHUREYES . '
                          </p>
                        </form>
                      </div>
                    </div>';

                    break;
                } else {
                    $backup['stat'] = 3;
                }
            } else {
                $backup['stat'] = 2;
                $backup['msg'] = _NOBACKUPCREATED;
            }
        }
        // mxDebugFuncVars($backup);
        if (empty($backup['stat'])) {
            $option = _STEP_BACKUP_NEW;
            $output .= setup_form_submit_message(_DBUP_MESSAGE, _DBUP_WAIT);
            $output .= $backup['msg'] . '<br /><br />
              <form action="index.php" method="post" class="dbup">
                <p>' . hiddenrequest('dbup') . '
                  <input type="hidden" name="createbackup" value="0" />
                  <input class="btn" type="submit" value="' . _SUBMIT . '" />
                </p>
              </form>';
        } else {
            $dbstat = setupConnectDb();
            if (!isset($dbstat['dbi'])) {
                // uups, da ist was falsch
                $option = _STEP_UPDATE;
                $msg = (isset($dbstat['msg'])) ? $dbstat['msg'] : _NOT_CONNECT;
                $output .= '
                  <p>' . $msg . '</p><br />
                  <form action="index.php" method="post">
                    <p>' . hiddenrequest('setup') . '
                      <input class="btn" type="submit" value="' . _CORRECTION . '" />
                    </p>
                  </form>';
            } else {
                $option = _CURRENTSTATUS;
                require('includes/function_dbupgrade.php');
                $querystat = setup_dbupgrade($dbstat['dbi']);
                // Nachricht von Backupfunktion hinzufuegen
                if (isset($backup['msg'])) {
                    $querystat['msg'][] = $backup['msg'];
                }
                // mxDebugFuncVars($querystat);
                $output .= '
                  <div class="alert alert-success alert-block">
                    <ul>
                      <li>' . (implode('</li><li>', $querystat['msg'])) . '</li>
                    </ul>
                  </div>';
                // Statusanzeige
                switch ($querystat['status']) {
                    case 'critical':
                        $output .= '<div class="alert alert-error alert-block">' . _HAVECREATE_TABLES_ERR . '</div>';
                        $querystat['count_err'] = count($querystat['msg_err']);
                        break;
                    case 'check':
                        $output .= '<div class="alert alert-block">' . _HAVE_CREATE_TABLES_7 . '</div>';
                        break;
                    default:
                        $output .= '<p class="lead">' . _DB_UPDATEREADY . '</p>';
                }
                // wenn Fehler aufgetreten, diese Anzeigen
                if ($querystat['count_err']) {
                    setup_prettyprinter();
                    $output .= '
                        <p>
                          <span class="label label-important">' . _DBFOLLOWERRORS . '</span>
                        </p>
                        <pre class="prettyprint linenums">' . htmlspecialchars(trim(str_replace("\n\n", "\n", $querystat['msg_err']))) . '</pre>';
                    if (@is_file(FILE_LOG_ERR)) {
                        $output .= '<p>' . _SEEALL . ': <a href="logview.php?file=' . FILE_LOG_ERR . '" target="_blank">' . str_replace(MX_SETUP_DIR, '', FILE_LOG_ERR) . '</a></p>';
                    }
                }

                $output .= '
                <div class="row">
                  <div class="span1">
                    <form action="index.php" method="post">
                      <p>' . hiddenrequest('setup') . '
                        <input class="btn btn-link" type="submit" value="' . _GOBACK . '" />
                      </p>
                    </form>
                  </div>';
                if ($querystat['status'] != 'ok') {
                    // wenn Fehler aufgetreten oder setup gescheitert, wiederholen anbieten
                    $output .= setup_form_submit_message(_DBUP_MESSAGE, _DBUP_WAIT);
                    $output .= '
                      <div class="span2">
                        <form action="index.php" method="post" class="dbup">
                          <p>' . hiddenrequest('dbup') . '
                            <input type="hidden" name="createbackup" value="' . $createbackup . '" />
                            <input class="btn" type="submit" value="' . _REMAKE . '" />
                          </p>
                        </form>
                      </div>';
                }
                if ($querystat['status'] != 'critical') {
                    // wenn setup erfolgreich oder auch mit kleinen Fehlern > ignorieren/weiter mit nextop
                    // standardmässig wird als nächstes ein Admin erstellt
                    $capt = ($querystat['status'] == 'check') ? _IGNORE : _SUBMIT;
                    $output .= '
                    <div class="span2">
                      <form action="index.php" method="post">
                        <p>' . hiddenrequest('finish') . '
                          <input class="btn" type="submit" value="' . $capt . '" />
                        </p>
                      </form>
                    </div>';
                }
                $output .= '
                </div>';

                if ($querystat['count_ok']) {
                    // wenn erfolgreiche Datenbankänderungen stattgefunden haben
                    setup_prettyprinter();
                    $output .= '
                          <hr />
                          <p>' . _GET_SQLHINTS . ':</p>
                          <pre class="prettyprint linenums" style="max-height: 20em; overflow: auto">' . htmlspecialchars(trim(str_replace("\n\n", "\n", $querystat['msg_ok']))) . '</pre>';
                    if (@is_file(FILE_LOG_OK)) {
                        $output .= '<p>' . _SEEALL . ': <a href="logview.php?file=' . FILE_LOG_OK . '" target="_blank">' . str_replace(MX_SETUP_DIR, '', FILE_LOG_OK) . '</a></p>';
                    }
                }
            }
        }
        break;

    /**
     */
    case 'finish';
        $option = _STEP_FINISHEDINSTALL;
        $output .= '
          <h3>' . _SETUPHAPPY1 . '</h3>
          <p>' . _SETUPHAPPY2 . '</p>
          <div class="alert alert-block">' . _DELETE_FILES . '
            ' . printdeleter() . '
          </div>
          <form action="../' . adminUrl('settings') . '">
            <p>
              <input type="hidden" name="op" value="settings" />
              <input class="btn" type="submit" value="' . _GOTOADMIN . '" />
            </p>
          </form>
          <hr />
          <ul class="unstyled">
              <li><i class="icon-question-sign"></i> ' . _SUPPORTINFO . '</li>
            <li><i class="icon-file"></i> ' . _DOKUINFO . '</li>
          </ul>';
        break;
        // end case
}

?>
