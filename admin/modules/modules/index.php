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





// TODO: Ajax-Konfiguration für die einzelnen Module direkt im Modul




/* Sprachdatei auswählen */
mxGetLangfile(dirname(__FILE__));

if (!mxGetAdminPref("radminsuper")) {
    mxErrorScreen("Access Denied");
    die();
}

/**
 */
function modules()
{
    global $prefix;

    // Icons Bootstrap 4
    $img_activate   = '<i class="fa fa-check fa-lg"></i>&nbsp;' . _ACTIVATE;
    $img_deactivate = '<i class="fa fa-minus-circle"></i>&nbsp;' . _DEACTIVATE;
    $img_edit       = '<i class="fa fa-edit fa-lg"></i>&nbsp;' . _EDIT;
    $img_home       = '<i class="fa fa-home fa-lg"></i>&nbsp;' . _PUTINHOME;
    $img_view       = '<i class="fa fa-eye fa-lg"></i>&nbsp;' . _SHOW;
    $img_admin      = '<i class="fa fa-wrench fa-lg"></i>&nbsp;' . _ADMINISTRATION;
    // Icons Bootstrap 4 - End

    $mainmod = mxGetMainModuleName();
    $excludes = array('.', '..', 'CVS', 'index.html');
    $qry = "SELECT title, mid, custom_title, active, view, main_id 
			FROM ${prefix}_modules";
    $result = sql_query($qry);
    while ($mods = sql_fetch_assoc($result)) {
        if (!@file_exists(PMX_MODULES_DIR . DS . $mods['title'] . '/') || in_array($mods['title'], $excludes) || preg_match('#[^A-Za-z0-9._-]#', $mods['title'])) {
            sql_query("DELETE FROM ${prefix}_groups_modules WHERE module_id=" . $mods['mid']);
            sql_query("DELETE FROM ${prefix}_modules WHERE mid=" . $mods['mid']);
        } else {
            $dbmodlist[strtolower($mods['title'])] = $mods;
        }
    }
    unset($mods);
    $doublemod = array();
    $handle = opendir('modules');
    while (false !== ($file = readdir($handle))) {
        if (in_array(strtolower($file), $doublemod)) {
            $doublerror[] = _ADMIN_WARNDOUBLEMOD1 . $file . _ADMIN_WARNDOUBLEMOD2 . '<br />';
        }
        $doublemod[] = strtolower($file);
        if (!in_array($file, $excludes) && !isset($dbmodlist[strtolower($file)]) && @file_exists(PMX_MODULES_DIR . DS . $file . '/index.php') && !preg_match('#[^A-Za-z0-9._-]#', $file)) {
            /* If you copied a new module is the /modules/ directory, it will be added to the database */
            $ctitle = str_replace("_", " ", $file);
            $qry = "INSERT INTO ${prefix}_modules (title,custom_title,active,view) values ('" . $file . "', '" . $ctitle . "', 0, 0)";
            $result = sql_query($qry);
            if ($result) {
                $result1 = sql_query("SELECT title, mid, custom_title, active, view, main_id 
									FROM ${prefix}_modules 
									WHERE title='" . $file . "'");
                $newmod = sql_fetch_assoc($result1);
                $dbmodlist[strtolower($newmod['title'])] = $newmod;
            }
        }
    }
    closedir($handle);
    unset($doublemod);
    if (empty($dbmodlist)) {
        mxErrorScreen("no modules installed!");
    }
    ksort($dbmodlist);

    include('header.php');

    foreach ($dbmodlist as $module) {
        if (!is_array($module)) {
            // aus unerfindlichen Gründen kann $module auch leer sein.
            continue;
        }
        extract($module);
        $act = (empty($active)) ? 1 : 0;
        $change = (empty($active)) ? $img_activate : $img_deactivate;
        $colorchange = (empty($active)) ? 'success' : 'secondary';
        $custom_title = (empty($custom_title)) ? str_replace("_", " ", $title) : $custom_title;

        $main_id = (empty($main_id)) ? 'Modules' : $main_id;
        $main_id = ($main_id == "hided") ? "<span class=\"tiny\"><i>" . _COSTUMNAVIBLOCKHID . "</i></span>" : $main_id;

        switch ($view) {
            case 1:
                $who_view = _MVGROUPS2;
                break;
            case 2:
                $who_view = _MVADMIN;
                break;
            case 3:
                $who_view = _MVANON;
                break;
            case 4:
                $who_view = _MVSYSADMIN;
                break;
            default:
                $who_view = _MVALL;
        }

        settype($out_active, 'array');
        settype($out_deact, 'array');
        settype($out_main, 'array');

        $casefiles = array(/* mögliche Dateipfade */
            /* Admindatei im Modulordner */
            PMX_MODULES_DIR . DS . $title . '/admin/admin.php',
            /* Admindatei im Adminmodulordner */
            PMX_ADMINMODULES_DIR . DS . $title . '.php',
            /* Ordner im Adminmodulordner */
            PMX_ADMINMODULES_DIR . DS . $title . '/index.php',
            );
        $clickit2 = '';
        foreach ($casefiles as $filename) {
            /* Datei gefunden */
            if (is_file($filename)) {
                /* Datei verlinken und raus hier */
                $clickit2 = "<a class=\"btn btn-warning btn-sm\" href=\"admin.php?op=" . $title . "\" target=\"_blank\" title=\"" . $title . " " . _ADMINISTRATION . "\">" . $img_admin . "</a>";
                break;
            }
        }

        if ($title != $mainmod) {
            $change = "<a class=\"btn btn-$colorchange btn-sm\" href=\"" . adminUrl(PMX_MODULE, 'status', "mid=" . $mid . "&amp;active=" . $act) . "\">" . $change . "</a>";
            $puthome = "<a class=\"btn btn-light btn-sm\" title=\""._PUTINHOME."\" href=\"" . adminUrl(PMX_MODULE, 'set_home', "mid=" . $mid) . "\">" . $img_home . "</a>";
            $clickit = "<a class=\"btn btn-info btn-sm\" title=\""._SHOW."\" href=\"modules.php?name=" . $title . "\" target=\"_blank\">" . $img_view . "</a>";

            if ($active) {
                $out_active[] = '
                    <tr>
                        <td>' . $title . '</td>
                        <td>' . $custom_title . '</td>
                        <td>' . $main_id . '</td>
                        <td>' . $who_view . '</td>
                        <td><a class="btn btn-primary btn-sm" title="'._EDIT.'" href="' . adminUrl(PMX_MODULE, 'edit', 'mid=' . $mid) . '">' . $img_edit . '</a> &nbsp;' . $change . ' ' . $puthome . ' ' . $clickit . ' ' . $clickit2 . '</td>
                    </tr>'; #<td>".$active."</td>
            } else {
                $out_deact[] = '
                    <tr>
                        <td>' . $title . '</td>
                        <td>' . $custom_title . '</td>
                        <td>' . $main_id . '</td>
                        <td>' . $who_view . '</td>
                        <td><a class="btn btn-primary btn-sm" href="' . adminUrl(PMX_MODULE, 'edit', 'mid=' . $mid) . '">' . $img_edit . '</a> &nbsp;' . $change . ' ' . $puthome . ' ' . $clickit . ' ' . $clickit2 . '</td>
                    </tr>';
            }
        } else {
            $clickit = "<a class=\"btn btn-info btn-sm\" href=\"index.php\" target=\"_blank\">" . $img_view . "</a>";
            $out_main[] = '
                <tr>
                    <td>' . $title . '</td>
                    <td><b>' . _HOME . '</b></td>
                    <td>' . $main_id . '</td>
                    <td>' . _MVALL . '</td>
                    <td><a class="btn btn-primary btn-sm" href="' . adminUrl(PMX_MODULE, 'edit', 'mid=' . $mid) . '">' . $img_edit . '</a> &nbsp;' . $clickit . ' ' . $clickit2 . '</td>
                </tr>';
        }
    }
    unset($dbmodlist);
    $headline = '
		<thead>
			<tr>
                <th>' . _TITLE . '</th>
                <th>' . _CUSTOMTITLE . '</th>
                <th>' . _MODULESBLOCKS . '</th>
                <th>' . _VIEW . '</th>
                <th>' . _FUNCTIONS . '</th>
            </tr>
		</thead>';
    echo '
		<a name="additem" id="additem"></a>
        <h2>' . _MODULESADMIN . '</h2>';
    if (isset($doublerror)) {
        echo "
			<div class=\"alert alert-warning\">
				" . implode("\n", $doublerror) . '
				<br />
				' . _ADMIN_WARNDOUBLEMOD3 . "
			</div>";
    }

    ?>
<!-- START: TABS BOOTSTRAP 4 -->
<!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist" id="mymodules">

<?php if ($out_active): ?>
  <li class="nav-item">
    <a class="nav-link active" data-toggle="tab" href="#mod-active" role="tab"><?php echo _ACTIVEMODULES ?></a>
  </li>
<?php endif; ?>

<?php if ($out_main): ?>
  <li class="nav-item">
    <a class="nav-link" data-toggle="tab" href="#mod-main" role="tab"><?php echo _DEFHOMEMODULE ?></a>
  </li>
<?php endif; ?>

<?php if ($out_deact): ?>
  <li class="nav-item">
    <a class="nav-link" data-toggle="tab" href="#mod-inactive" role="tab"><?php echo _NOACTIVEMODULES ?></a>
  </li>
<?php endif; ?>

</ul> <!-- END:.nav nav-tabs -->

<!-- START: ACTIF MODULES -->
<!-- Tab panes -->
<div class="tab-content">

<!-- START: ACTIF MODULES -->
<!-- out_active -->
<?php if ($out_active): ?>
  <div class="tab-pane active" id="mod-active" role="tabpanel">
    <table class="table">
        <?php echo $headline . implode("\n", $out_active) ?>
    </table>
    <p class="alert alert-info"><?php echo _MODULESACTIVATION ?></p>
  </div>
 <?php endif; ?>
 <!-- END:out_active -->
 <!-- END: ACTIF MODULES -->

<!-- START: MODULE IN HOME -->
<!-- out_main -->
<?php if ($out_main): ?>
  <div class="tab-pane" id="mod-main" role="tabpanel">
    <table class="table">
        <?php echo $headline . implode("\n", $out_main) ?>
    </table>
    <p class="alert alert-info"><?php echo _MODULEHOMENOTE ?></p>
  </div>
 <?php endif; ?>
 <!-- END:out_active --> 
 <!-- END: MODULE IN HOME -->

<!-- START: INACTIF MODULES -->
 <!-- out_main -->
<?php if ($out_deact): ?> 
  <div class="tab-pane" id="mod-inactive" role="tabpanel">
    <table class="table">
        <?php echo $headline . implode("\n", $out_deact) ?>
    </table>
  </div>
 <?php endif; ?>
 <!-- END:out_active -->
 <!-- END: INACTIF MODULES --> 

</div> <!-- END:.tab-content -->
<!-- END: TABS NAV -->

<script type="text/javascript">
$('#mymodules a').click(function (e) {
  e.preventDefault()
  $(this).tab('show')
})
</script>

<?php
    /* Javascript & jquery for tabs */
    pmxHeader::add_tabs();

    include('footer.php');
}

/**
 */
function set_home($gvs)
{
    global $prefix;

    $ok = (empty($gvs['ok'])) ? 0 : 1;
    $mid = (int)$gvs['mid'];
    $old_m = mxGetMainModuleName();

    $result = sql_query("SELECT title 
						FROM ${prefix}_modules 
						WHERE mid=" . $mid);
    list($new_m) = sql_fetch_row($result);

    if (empty($ok)) {
        include('header.php');
        title(_HOMEMODULE);
        echo '
       	<div class="card">
      		<div class="card-block">
        		<h4>' . _DEFHOMEMODULE . '</h4>
        		<p>
        			' . _SURETOCHANGEMOD . ' ' . $old_m . ' ' . _TO . ' ' . $new_m . '?
        		</p>
        		<p>
          			<a class="btn btn-warning" href="' . adminUrl(PMX_MODULE) . '">' . _NO . '</a> <a class="btn btn-primary" href="' . adminUrl(PMX_MODULE, 'set_home', 'mid=' . $mid . '&amp;ok=1') . '">' . _YES . '</a>
        		</p>
        	</div>
        </div>';
        include('footer.php');
    } else {
        sql_query("DELETE FROM ${prefix}_main");
        sql_query("INSERT INTO ${prefix}_main values ('" . mxAddSlashesForSQL($new_m) . "')");
        sql_query("UPDATE ${prefix}_modules set active=1, view=0 WHERE mid=" . $mid);
        mxRedirect(adminUrl(PMX_MODULE));
    }
}

/**
 */
function module_status($mid, $active)
{
    global $prefix, $user_prefix;
    /*
    // Begonnen fuer automatische Installation des Moduls
    if ($active) {
        $result = sql_query("select * from ${prefix}_modules WHERE mid=" . intval($mid));
        $row = sql_fetch_assoc($result);
        mxDebugFuncVars($row);
        if (file_exists(PMX_MODULES_DIR . DS . $row['title'] . '/core/install.tabledef.php')) {
            error_reporting(E_ALL);
            include_once(PMX_SYSTEM_DIR . DS . 'mx_install.php');
            $tables = setupGetTables();
            mxDebugFuncVars($tables);
            include(PMX_MODULES_DIR . DS . '' . $row['title'] . '/core/install.tabledef.php');
        }
        exit;
    }
    */

    sql_query("UPDATE ${prefix}_modules SET active=" . intval($active) . " WHERE mid=" . intval($mid));
    mxRedirect(adminUrl(PMX_MODULE));
}

/**
 */
function module_edit($mid)
{
    global $prefix;
    $mid = (int)$mid;
    $main_module = mxGetMainModuleName();
    $result = sql_query("SELECT title, custom_title, active, view, main_id
                        FROM ${prefix}_modules
                        WHERE mid=" . $mid);
    list($title, $custom_title, $active, $view, $main_id) = sql_fetch_row($result);
    $custom_title = (empty($custom_title)) ? str_replace('_', ' ', $title) : $custom_title;
    include('header.php');
    title(_MODULEEDIT);
    echo '
    <div class="card">
        <form action="' . adminUrl(PMX_MODULE, 'edit_save') . '" method="post">   
        <div class="card-header">
          <i class="fa fa-edit"></i>&nbsp;'. _CHANGEMODNAME . ': <strong>' . $title . '</strong>
        </div>
      <div class="card-body">
             <div class="form-group row">
                <label class="col-md-2 col-form-label">' . _CUSTOMMODNAME . '</label>
                <div class="col-md-3">';
    if ($title == $main_module) {
        echo '
				' . _HOME . '
				<input class="form-control" type="hidden" name="custom_title" value="' . mxEntityQuotes($custom_title) . '" />';
    } else {
        echo '
				<input class="form-control" type="text" name="custom_title" value="' . mxEntityQuotes($custom_title) . '" size="50" />';
    }

    echo '
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-2 col-form-label">' . _VIEWPRIV . '</label>
                <div class="col-md-3">';
    if ($title == $main_module) {
        echo '
			' . _MVALL . ' (' . _DEFHOMEMODULE . ')
			<input type="hidden" name="view" value="0" />
			<input type="hidden" name="active" value="1" />
			<input type="hidden" name="main_id" value="" />';
    } else {
        echo getViewSelect($view)
         . '
                </div>
            </div>


            <div class="form-group row">
                <div class="col-md-3 offset-md-2">
                    ' . getGroupSelect($mid) . '
                </div>
            </div>

            <div class="form-group row">
                <label class="col-md-2 col-form-label">' . _ACTIVE . '</label>
                <div class="col-md-3">
                    ' . getActiveSelect($active) . '
                </div>
            </div>

            <div class="form-group row">
                <div class="col-md-5 offset-md-2">
                    <div class="form-check">
                        <label class="form-check-label">
                            <input class="form-check-input" type="checkbox" name="putinhome" value="1" />&nbsp; ' . _PUTINHOME . '
                        </label>
                    </div>
                </div>
            </div>';
    }
    echo '
            <div class="form-group row">
                <label class="col-md-2 col-form-label">' . _COSTUMNAVIBLOCK . '</label>
                <div class="col-md-3">
                    ' . getModuleBlockSelect($main_id) . '
                </div>
            </div>';
    echo '
        </div>
	   <div class="card-footer">
		  <input type="hidden" name="mid" value="' . $mid . '" />
		  <input type="hidden" name="title" value="' . $title . '" />
		  <input type="hidden" name="op" value="' . PMX_MODULE . '/edit_save" />
          <button type="submit" class="btn btn-primary"><i class="fa fa-check fa-lg"></i>&nbsp;' . _SAVECHANGES . '</button>
            ' . _GOBACK . '
        </div>
     </form>
    </div>';
    include('footer.php');
}

/**
 */
function module_edit_save($pvs)
{
    global $prefix;

    $userconfig = load_class('Userconfig');

    $pvs['mid'] = (int)$pvs['mid'];
    $pvs['putinhome'] = (empty($pvs['putinhome'])) ? 0 : 1;
    $pvs['active'] = ($pvs['putinhome']) ? 1 : (int)$pvs['active'];
    $pvs['view'] = ($pvs['putinhome']) ? 0 : (int)$pvs['view'];
    $pvs['custom_title'] = strip_tags($pvs['custom_title']);
    $pvs['custom_title'] = (empty($pvs['custom_title'])) ? str_replace("_", " ", $pvs['title']) : $pvs['custom_title'];
    $pvs['main_id'] = ($pvs['putinhome']) ? "" : $pvs['main_id'];
    $pvs['main_id'] = ($pvs['main_id'] == "Modules") ? "" : $pvs['main_id'];
    $qry = "UPDATE ${prefix}_modules set
            custom_title='" . $pvs['custom_title'] . "',
            view=" . $pvs['view'] . ",
            active=" . $pvs['active'] . ",
            main_id='" . $pvs['main_id'] . "'
            WHERE mid=" . $pvs['mid'];
    $result = sql_query($qry);
    if ($result) {
        // Gruppen aktualisieren
        sql_query("DELETE FROM ${prefix}_groups_modules WHERE module_id=" . $pvs['mid']);
        if ($pvs['view'] == 1 && !$pvs['putinhome']) {
            // Sicherstellen, dass wirklich eine Gruppe selektiert wurde
            if (empty($pvs['groups'])) {
                // keine Gruppe gewaehlt > Standard eintragen
                $pvs['groups'] = array(intval($userconfig->default_group));
            } else if (!is_array($pvs['groups'])) {
                // die Gruppe nicht als Array, sonder ID uebergeben > diese ID als Arraywert deklarieren
                $pvs['groups'] = array(intval($pvs['groups']));
            }
            // immernoch keine Gruppe gewaehlt > Standard eintragen
            if (empty($pvs['groups'][0])) {
                $pvs['groups'] = array(intval($userconfig->default_group));
            }

            foreach ($pvs['groups'] as $groupid) {
                sql_query("INSERT INTO ${prefix}_groups_modules (group_id, module_id) VALUES (" . $groupid . ", " . $pvs['mid'] . ")");
            }
        }
        if ($pvs['putinhome']) {
            $old_m = mxGetMainModuleName();
            if (empty($old_m)) {
                $result = sql_query("INSERT INTO ${prefix}_main values ('" . mxAddSlashesForSQL($pvs['title']) . "')");
            } else {
                $result = sql_query("update ${prefix}_main set main_module='" . mxAddSlashesForSQL($pvs['title']) . "'");
            }
        }
    }
    mxRedirect(adminUrl(PMX_MODULE), _ADMIN_SETTINGSAVED);
}

/**
 */
function getActiveSelect($active = 1)
{
    $active = (int)$active;
    $out = "<input type=\"radio\" name=\"active\" value=\"1\"" . (($active == 1) ? ' checked="checked"' : "") . " />" . _YES . " &nbsp;&nbsp;";
    $out .= "<input type=\"radio\" name=\"active\" value=\"0\"" . (($active == 0) ? ' checked="checked"' : "") . " />" . _NO . "\n";
    return $out;
}

/**
 */
function getViewSelect($view = 0)
{
    $view = (int)$view;
    $out = '
		<select name="view">'
			. '<option value="0"' . (($view == 0) ? ' selected="selected" class="current"' : '') . '>' . _MVALL . '</option>'
			. '<option value="1"' . (($view == 1) ? ' selected="selected" class="current"' : '') . '>' . _MVGROUPS . '</option>'
			. '<option value="2"' . (($view == 2) ? ' selected="selected" class="current"' : '') . '>' . _MVADMIN . '</option>'
			. '<option value="4"' . (($view == 4) ? ' selected="selected" class="current"' : '') . '>' . _MVSYSADMIN . '</option>'
			. '<option value="3"' . (($view == 3) ? ' selected="selected" class="current"' : '') . '>' . _MVANON . '</option>'
	. '</select>';
    return $out;
}

/**
 */
function getGroupSelect($modid = 0)
{
    global $prefix;

    $userconfig = load_class('Userconfig');

    if (!empty($modid)) {
        $qry = "SELECT group_id 
				FROM ${prefix}_groups_modules 
				WHERE module_id=" . intval($modid);
        $result = sql_query($qry);
        while (list($group_id) = sql_fetch_row($result)) {
            $groups[] = $group_id;
        }
    }
    $groups = (empty($groups)) ? $userconfig->default_group : $groups;
    $groupoptions = getAllAccessLevelSelectOptions($groups);
    $cnt = substr_count($groupoptions, '<option') + 1;
    $out = '<select name="groups[]" id="groups" size="' . $cnt . '" multiple="multiple">' . $groupoptions . '</select>';
    return $out;
}

/**
 */
function getModuleBlockSelect($main_id = '')
{
    global $prefix;
    $main_id = (empty($main_id)) ? "Modules" : $main_id;
    $result = sql_query("SELECT bid, title, blockfile 
						FROM ${prefix}_blocks 
						WHERE blockfile LIKE 'block-modules%.php'");
    while (list($bid, $btitle, $bfs) = sql_fetch_row($result)) {
        $dbblocks[$bfs] = $btitle;
    }
    $blocksdir = dir("blocks");
    while ($block = $blocksdir->read()) {
        if (strtolower(substr($block, 0, 13)) == "block-modules") {
            $mode = (isset($dbblocks[$block])) ? 1 : 0;
            $is = ($mode) ? " (" . $dbblocks[$block] . ")": "  *";
            $bl = str_replace("block-", "", $block);
            $bl = str_replace(".php", "", $bl);
            $sel = ($main_id == $bl) ? 'selected="selected" class="current"' : "";
            $options[$mode][strtolower($bl)] = "<option value=\"" . $bl . "\" " . $sel . ">" . $bl . $is . "</option>";
        }
    }
    closedir($blocksdir->handle);
    // if (empty($main_id)) $options[] = "<option value=\"\" selected></option>";
    $view = "";
    $sel = ($main_id == "hided") ? 'selected="selected" class="current"' : "";
    $options[1]["aaaaaa"] = "<option value=\"hided\" " . $sel . ">" . _COSTUMNAVIBLOCKHID . "</option>";
    if (isset($options[1])) {
        ksort($options[1]);
        $view .= implode("\n", $options[1]);
    }
    if (isset($options[0])) {
        ksort($options[0]);
        $view .= implode("\n", $options[0]);
    }
    $out = "\n<select name=\"main_id\">\n" . $view . "</select>\n";
    $out .= " <span class=\"important tiny\">" . _COSTUMNAVIEXAMPLE . "</span>\n";
    return $out;
}

/**
 */
switch ($op) {
    case PMX_MODULE . '/status':
        module_status($mid, $active);
        break;

    case PMX_MODULE . '/edit':
        module_edit($mid);
        break;

    case PMX_MODULE . '/edit_save':
        module_edit_save($_POST);
        break;

    case PMX_MODULE . '/set_home':
        set_home($_GET);
        break;

    default:
        modules();
        break;
}

?>