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
 * $Revision: 180 $
 * $Author: PragmaMx $
 * $Date: 2016-07-08 10:13:09 +0200 (Fr, 08. Jul 2016) $
 */

defined('mxMainFileLoaded') or die('access denied');

switch ($_REQUEST['op']) {
    /**
     */
    case 'setup';
        $option = _STEP_UPDATE;
        @include(FILE_CONFIG_ROOT);
        // Datenbankverbindung prüfen
        $dbstat = setupConnectDb();
        if (!isset($dbstat['dbi'])) {
            // uups, da ist was falsch
            $msg = (isset($dbstat['msg'])) ? $dbstat['msg'] : _NOT_CONNECT;
            $err = true;
            $output .= $msg . '
                    <p>' . _NOT_CONNECTMORE . '</p>
                    <div class="row">
                      <div class="span1">
                        <form action="index.php" method="post">
                          <p>' . hiddenrequest('select') . '
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
            // backup checken
            require('includes/function_dbbackup.php');
            $backup_available = setup_dbbackup('only_check');
            $output .= '<div class="alert">' . _BACKUPPLEASEDOIT . '</div>';
            if ($backup_available) {
                $output .= setup_form_submit_message(_DBUP_MESSAGE, _DBUP_WAIT);
                $output .= '
                <p>' . _WILL_CREATE_BACKUP . '</p>
                <div class="row">
                  <div class="span2">
                    <form action="index.php" method="post">
                      ' . hiddenrequest('select') . '
                      <input class="btn btn-link" type="submit" value="' . _GOBACK . '" />
                    </form>
                  </div>
                  <div class="span3">
                    <form action="index.php" method="post" class="dbup">
                      ' . hiddenrequest('dbup') . '
                      <input type="hidden" name="createbackup" value="1" />
                      <input class="btn" type="submit" value="' . _CONTINUE_WITHDBBACKUP . '" />
                    </form>
                  </div>
                  <div class="span2">
                    <form action="index.php" method="post" class="dbup">
                      ' . hiddenrequest('dbup') . '
                      <input type="hidden" name="createbackup" value="0" />
                      <input class="btn" type="submit" value="' . _CONTINUE_WITHOUTDBBACKUP . '" />
                    </form>
                  </div>
                </div>';
            } else {
                $output .= setup_form_submit_message(_DBUP_MESSAGE, _DBUP_WAIT);
                $output .= '
                <p>' . _BACKUPBESHURE . '</p>
                <div class="row">
                  <div class="span1">
                    <form action="index.php" method="post">
                      ' . hiddenrequest('select') . '
                      <input class="btn btn-link" type="submit" value="' . _GOBACK . '" />
                    </form>
                  </div>
                  <div class="span8">
                    <form action="index.php" method="post" class="dbup">
                      ' . hiddenrequest('dbup') . '
                      <input type="hidden" name="cantbackup" value="0" />
                      <input class="btn" type="submit" value="' . _SUBMIT . '" />&nbsp;&nbsp;&nbsp;
                      <input class="checkbox" type="checkbox" name="beshure" value="1" title="' . _BACKUPBESHUREOK . '" />
                      &nbsp;' . _BACKUPBESHUREYES . '
                    </form>
                  </div>
                </div>';
            }
        }
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
                    <div class="span1">
                      <form action="index.php" method="post">
                        <p>' . hiddenrequest('select') . '
                          <input class="btn btn-link" type="submit" value="' . _GOBACK . '" />
                        </p>
                      </form>
                    </div>
                    <div class="span7">
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

        if (empty($backup['stat'])) {
            $option = _STEP_BACKUP;
            $output .= setup_form_submit_message(_DBUP_MESSAGE, _DBUP_WAIT);
            $output .= $backup['msg'] . '
              <div class="row">
                <div class="span2">
                  <form action="index.php" method="post" class="dbup">
                      <p>' . hiddenrequest('dbup') . '
                        <input type="hidden" name="createbackup" value="1" />
                        <input class="btn" type="submit" value="' . _REMAKE . '" />
                      <p>
                  </form>
                </div>
                <div class="span1">
                  <form action="index.php" method="post" class="dbup">
                      <p>' . hiddenrequest('dbup') . '
                        <input type="hidden" name="createbackup" value="0" />
                        <input class="btn" type="submit" value="' . _SUBMIT . '" />
                      </p>
                  </form>
                </div>
              </div>';
        } else {
            $status = "";
            @include(FILE_CONFIG_ROOT);
            // Datenbankverbindung prüfen
            $dbstat = setupConnectDb();
            if (!isset($dbstat['dbi'])) {
                // uups, da ist was falsch
                $option = _STEP_UPDATE;
                $msg = (isset($dbstat['msg'])) ? $dbstat['msg'] : _NOT_CONNECT;
                // $err = true;
                $output .= setup_form_submit_message(_DBUP_MESSAGE, _DBUP_WAIT);
                $output .= '
                  <p><strong>' . $msg . '</strong></p>
                  <p>' . _NOT_CONNECTMORE . '</p>
                  <div class="row">
                    <div class="span1">
                      <form action="index.php" method="post">
                        <p>' . hiddenrequest('select') . '
                          <input class="btn btn-link" type="submit" value="' . _GOBACK . '" />
                        </p>
                      </form>
                    </div>
                    <div class="span2">
                      <form action="index.php" method="post" class="dbup">
                        <p>' . hiddenrequest('dbup') . '
                          <input type="hidden" name="createbackup" value="0" />
                          <input class="btn" type="submit" value="' . _REMAKE . '" />
                        </p>
                      </form>
                    </div>
                  </div>';
            } else {
                $option = _CURRENTSTATUS;
                require('includes/function_dbupgrade.php');
                $querystat = setup_dbupgrade($dbstat['dbi']);
                // Nachricht von Backupfunktion hinzufuegen
                if (isset($backup['msg'])) {
                    $querystat['msg'][] = $backup['msg'];
                }
                // Statusanzeige
                if ($querystat['status'] == 'critical') {
                    $output .= '<div class="alert alert-error alert-block">' . _DB_UPDATEFAIL . '</div>';
                } else {
                    $output .= '<div class="alert alert-success alert-block">' . _DB_UPDATEREADY . '</div>';
                }

                $output .= '
                        <ul>
                            <li>' . (implode('</li><li>', $querystat['msg'])) . '</li>
                        </ul>';
                // wenn Fehler aufgetreten, diese Anzeigen
                if ($querystat['count_err']) {
                    $output .= '
                        <div class="alert alert-error alert-block">
                          <p>' . _DBFOLLOWERRORS . ':</p>
                        </div>
                        <textarea rows="12" name="log_err" class="status" style="width:600px">' . htmlspecialchars(trim(str_replace("\n\n", "\n", $querystat['msg_err']))) . '</textarea>';
                    if (@is_file(FILE_LOG_ERR)) {
                        $output .= '<p>' . _SEEALL . ': <a href="logview.php?file=' . FILE_LOG_ERR . '" target="_blank">' . str_replace(MX_SETUP_DIR, '', FILE_LOG_ERR) . '</a></p>';
                    }
                }

                $output .= '
                        <div class="row">
                          <div class="span1">
                            <form action="index.php" method="post">
                              ' . hiddenrequest('setup') . '
                              <input class="btn btn-link" type="submit" value="' . _GOBACK . '" />
                            </form>
                        </div>';
                if ($querystat['status'] != 'ok') {
                    // wenn Fehler aufgetreten oder setup gescheitert, wiederholen anbieten
                    $output .= setup_form_submit_message(_DBUP_MESSAGE, _DBUP_WAIT);
                    $output .= '
                        <div class="span2">
                          <form action="index.php" method="post" class="dbup">
                            ' . hiddenrequest('dbup') . '
                            <input type="hidden" name="createbackup" value="' . $createbackup . '" />
                            <input class="btn" type="submit" value="' . _REMAKE . '" />
                          </form>
                        </div>';
                }
                if ($querystat['status'] != 'critical') {
                    // wenn setup erfolgreich oder auch mit kleinen Fehlern > ignorieren/weiter
                    $capt = ($querystat['status'] == 'check') ? _IGNORE : _SUBMIT;
                    $output .= '
                        <div class="span2">
                          <form action="index.php" method="post">
                            ' . hiddenrequest('fileup') . '
                            <input class="btn" type="submit" value="' . $capt . '" />
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
    case 'fileup';
        if (!@is_file(FILE_DELETE_FILES)) {
            $option = _ERROR;
            $output .= '
                <div class="alert alert-error alert-block">' . _ERRMSG1A . '<strong>' . FILE_DELETE_FILES . '</strong></div>
                <div class="row">
                  <div class="span2">
                    <form action="index.php" method="post">
                      <p>' . hiddenrequest('fileup') . '
                        <input class="btn" type="submit" value="' . _REMAKE . '" />
                      <p>
                    </form>
                  </div>
                  <div class="span2">
                    <form action="index.php" method="post">
                      <p>' . hiddenrequest('finish') . '
                        <input class="btn" type="submit" value="' . _IGNORE . '" />
                      </p>
                    </form>
                  </div>
                </div>';
        } else {
            include_once(FILE_DELETE_FILES);
            $deletes = delfiles::get();

            if (!empty($deletes['dirs']) || !empty($deletes['files'])) {
                setup_prettyprinter();

                $option = _STEP_DELFILES;
                $output .= '
                    <p>' . _VERCHECK_DESCRIBE . '</p>';

                if (!empty($deletes['dirs'])) {
                    $output .= '
                        <pre class="prettyprint linenums">' . implode("\n", $deletes['dirs']) . '</pre>';
                }
                if (!empty($deletes['files'])) {
                    $output .= '
                        <pre class="prettyprint linenums">' . implode("\n", $deletes['files']) . '</pre>';
                }
                $output .= '
                    <p>' . _FILEDELNOTSURE . '</p>
                    <div class="row">
                      <div class="span1">
                        <form action="index.php" method="post">
                            ' . hiddenrequest('setup') . '
                            <input class="btn btn-link" type="submit" value="' . _GOBACK . '" />
                        </form>
                      </div>
                      <div class="span2">
                        <form action="index.php" method="post">
                            ' . hiddenrequest('filedel') . '
                            <input type="hidden" name="ignore" value="ignore" />
                            <input class="btn" type="submit" value="' . _DONOTHING . '" />
                        </form>
                      </div>
                      <div class="span2">
                        <form action="index.php" method="post">
                            ' . hiddenrequest('filedel') . '
                             <input class="btn" type="submit" value="' . _VERCHECK_DEL . '" />
                        </form>
                      </div>
                    </div>';
            } else {
                $option = _STEP_CONFIGURATION;
                $output .= '
                    <p>' . _CONFIGSAVEMESS . '</p>
                    <form action="index.php" method="post">
                      ' . hiddenrequest('savesettings') . '
                      <input class="btn" type="submit" value="' . _SUBMIT . '" />
                    </form>';
            }
        }
        break;

    /**
     */
    case 'filedel';
        include_once(FILE_DELETE_FILES);

        $deletes = array();
        if (!isset($_REQUEST['ignore'])) {
            delfiles::delete_all();
            $deletes = delfiles::get();
        }

        if (!empty($deletes['dirs']) || !empty($deletes['files'])) {
            $option = _STEP_DELFILES;
            setup_prettyprinter();

            $output .= '
                <p>' . _ERRMSG2 . '</p>';

            if (!empty($deletes['dirs'])) {
                $output .= '
                    <pre class="prettyprint linenums">' . implode("\n", $deletes['dirs']) . '</pre>';
            }
            if (!empty($deletes['files'])) {
                $output .= '
                    <pre class="prettyprint linenums">' . implode("\n", $deletes['files']) . '</pre>';
            }
            $output .= '
                <p class="lead">' . _YEAHREADY2 . '</p>';
        } else {
            $option = _STEP_FINISHEDUPDATE;
        }
        $output .= '
            <p class="info">' . _CONFIGSAVEMESS . '</p>
                <form action="index.php" method="post">
                    <p>' . hiddenrequest('savesettings') . '
                        <input class="btn" type="submit" value="' . _SUBMIT . '" />
                    </p>
                </form>';
        break;

    /**
     */
    case 'savesettings';
        $oldconfig = file_get_contents(FILE_CONFIG_ROOT);
        include(FILE_CONFIG_ROOT);
        $setvalues['dbhost'] = $dbhost;
        $setvalues['dbuname'] = $dbuname;
        $setvalues['dbpass'] = $dbpass;
        $setvalues['dbname'] = $dbname;
        $setvalues['prefix'] = $prefix;
        $setvalues['user_prefix'] = $user_prefix;
        require('includes/function_saveconfigfile.php');
        $status = SetupSaveConfigFile($setvalues);
		SetupSaveUserConfigFile();  //Usersettings testen 
        if ($status['ok']) {
            $option = _STEP_FINISHEDUPDATE;
            $newconfig = file_get_contents(FILE_CONFIG_ROOT);
            if ($newconfig != $oldconfig) {
                $output = '<ul>' . $status['msg'] . '</ul>';
            }
            $output = '
                    <p>' . _SETUPHAPPY1 . '</p>
                    <p>' . _SETUPHAPPY2 . '</p>
                    <p>' . _SETUPHAPPY3 . '</p>
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
        } else {
            $output = '
                <p><strong>' . _THEREERROR . '</strong></p>
                <ul>' . $status['msg'] . '</ul>
                <div class="row">
                  <div class="span3">
                     <form action="index.php" method="post">
                        <p>' . hiddenrequest('showconfig') . '
                            <input type="hidden" name="configphp" value="' . $status['configphp'] . '" />
                            <input class="btn" type="submit" value="' . _CONFIG_BUTTONMAN . '" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        </p>
                    </form>
                  </div>
                  <div class="span2">
                    <form action="index.php" method="post">
                        <p>' . hiddenrequest('finish') . '
                            <input type="hidden" name="configerr" value="1" />
                            <input class="btn" type="submit" value="' . _IGNORE . '" />&nbsp;&nbsp;
                        </p>
                    </form>
                   </div>
                 </div>';
        }
        break;

    /**
     */
    case 'showconfig';
        // <input type="hidden" name="configphp" value="'.$_REQUEST['configphp'].'" />
        $output = '
                <p>' . _CONFIG_CREATE . '</p>
                <p><textarea cols="72" rows="12" name="xxconfigphp" style="width:90%">' . $_REQUEST['configphp'] . '</textarea></p>
                <div class="row">
                  <div class="span1">
                    <form action="index.php" method="post">
                      <p>' . hiddenrequest($_REQUEST['lastop']) . '
                          <input class="btn btn-link" type="submit" value="' . _GOBACK . '" />
                      </p>
                    </form>
                  </div>
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
    case 'finish';
        $option = _STEP_FINISHEDUPDATE;
        $output .= '
            <h3>' . _SETUPHAPPY1 . '</h3>
            <p>' . _SETUPHAPPY2 . '</p>
            <p>' . _SETUPHAPPY3 . '</p>
            <div class="alert alert-block">' . _DELETE_FILES . '
                ' . printdeleter() . '
            </div>
            <div class="row">';
        if (isset($_REQUEST['configerr'])) {
            $output .= '
                <div class="span1">
                    <form action="index.php" method="post">
                        <p>' . hiddenrequest('savesettings') . '
                            <input class="btn btn-link" type="submit" value="' . _GOBACK . '" />
                        </p>
                    </form>
                 </div>';
        }
        $output .= '
              <div class="span1">
                <form action="../' . adminUrl('settings') . '">
                    <p>
                        <input type="hidden" name="op" value="settings" />
                        <input class="btn" type="submit" value="' . _GOTOADMIN . '" />
                    </p>
                </form>
              </div>
            </div>
                <hr />
                <ul class="unstyled">
                  <li><i class="icon-question-sign"></i> ' . _SUPPORTINFO . '</li>
                  <li><i class="icon-file"></i> ' . _DOKUINFO . '</li>
              </ul>';
        break;
        // end case
}

?>
