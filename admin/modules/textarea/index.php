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

if (!mxGetAdminPref("radminsuper")) {
    mxErrorScreen("Access Denied");
    die();
}

load_class('Textarea', false);
load_class('Filebrowse', false);

/* Sprachdatei auswählen */
mxGetLangfile(__DIR__);
// mxSessionSetVar('panel', MX_ADMINPANEL_SYSTEM);
/**
 * ReadEditor()
 * Einstellungen Formular
 *
 * @return
 */
function ReadEditor()
{
    global $prefix;

    $wyscnf = Textarea::get_config();

    /* Die Liste der pragmaMx Usergruppen */
    $wyscnf['pmx_groups_list'] = mxeditor_get_usergroups_list();
    if (count($wyscnf['pmx_groups_list']) > 1) {
        $usertitle = _USERSGROUPS;
    } else {
        $usertitle = _USERSMEMBERS;
    }

    $wyscnf['groups']['admin'] = array('title' => _EDITOR_FOR . ' ' . _USERSADMINS);
    $wyscnf['groups']['user'] = array('title' => _EDITOR_FOR . ' ' . $usertitle);
    $wyscnf['groups']['other'] = array('title' => _EDITOR_FOR . ' ' . _USERSOTHERS);

    $langfiletypes = array(/* Übersetzung der Dateitypen */
        'any' => _FILES_ANY,
        'images' => _FILES_IMAGES,
        'flash' => _FILES_FLASH,
        'documents' => _FILES_DOCUMENT,
        'audio' => _FILES_AUDIO,
        'video' => _FILES_VIDEO,
        'archives' => _FILES_ARCHIVES,
        );

    $filetypegroups = Textarea::get_filetypegroups();
    $filetype_options = array();
    foreach ($filetypegroups as $key => $value) {
        if (isset($langfiletypes[$key])) {
            $label = $langfiletypes[$key];
        } else {
            $label = ucfirst($key);
        }
        if ($value) {
            $types = ' <small>(.' . implode(' .', array_keys($value)) . ')</small>';
        } else {
            $types = '';
        }

        $filetype_labels[$key] = htmlspecialchars($label) . $types;
        foreach($wyscnf['groups'] as $group => $tmp) {
            $checked = (empty($wyscnf[$group]['filetype'][$key])) ? '': ' checked="checked"';
            $filetype_options[$group][$key] = '
            <input type="hidden" name="filetype[' . $group . '][' . $key . ']" value="0" />
            <input type="checkbox" name="filetype[' . $group . '][' . $key . ']" value="1"' . $checked . ' />
            ';
        }
    }

    /* all core modes */
    $modes = Textarea::get_mode_levels();
    foreach ($modes as $key => $value) {
        $const = '_EDITOR_MODE_' . strtoupper($value);
        $modes[$key] = (defined($const)) ? constant($const) : ucfirst($value);
    }

    $tmp_editors = Textarea::get_available_editors();
    if (count($tmp_editors) === 2) {
        // nur der Spaw vorhanden
        foreach ($tmp_editors as $editor) {
            if (Textarea::fallback == $editor) {
                $editors['0'] = _NO;
            } else {
                $editors[$editor] = _YES;
            }
        }
    } else {
        // weitere Editoren vorhanden
        foreach ($tmp_editors as $editor) {
            if (Textarea::fallback == $editor) {
                $editors['0'] = '- ' . _NO;
            } else {
                $editors[$editor] = $editor;
            }
        }
    }

    $tmp_managers = pmxFilebrowse::get_available_managers();
    if (count($tmp_managers) === 1) {
        // nur der elfinder vorhanden
        $managers['0'] = _NO;
        $managers[$tmp_managers[0]] = _YES;
    } else {
        $managers['0'] = '- ' . _NO;
        // weitere Editoren vorhanden
        foreach ($tmp_managers as $manager) {
            $managers[$manager] = $manager;
        }
    }

    /* die Standard-Chmods */
    if (!isset($wyscnf['globals']['chmod_dir_to']) || $wyscnf['globals']['chmod_dir_to'] < 0) {
        $mode = mxEditorGetChmods('chmod_dir_to');
        $wyscnf['globals']['chmod_dir_to'] = sprintf("%o", $mode);
    }
    $wyscnf['globals']['chmod_dir_to'] = (empty($wyscnf['globals']['chmod_dir_to'])) ? '' : sprintf('%04d', $wyscnf['globals']['chmod_dir_to']);

    if (!isset($wyscnf['globals']['chmod_to']) || $wyscnf['globals']['chmod_to'] < 0) {
        $mode = mxEditorGetChmods('chmod_to');
        $wyscnf['globals']['chmod_to'] = sprintf("%o", $mode);
    }
    $wyscnf['globals']['chmod_to'] = (empty($wyscnf['globals']['chmod_to'])) ? '' : sprintf('%04d', $wyscnf['globals']['chmod_to']);

    /* Javascript & jquery for tabs */
    pmxHeader::add_tabs();

    pmxHeader::add_style_code('table.editortabs tr { vertical-align: top }');

    include('header.php');
    title(_EDITOR_SETTINGS);

    ?>
<form action="<?php echo adminUrl(PMX_MODULE) ?>" method="post">
<input type="hidden" name="op" value="<?php echo PMX_MODULE ?>/save" />
<div id="group-tabs">
<ul class="tabs-nav">
<?php foreach ($wyscnf['groups'] as $group => $groupdata) {

        ?>
  <li><a href="#group-<?php echo $group ?>"><?php echo $groupdata['title'] ?></a></li>
<?php }

    ?>
  <li><a href="#miscopt"><?php echo _EDITOR_MISCOPT ?></a></li>
</ul>

<?php foreach ($wyscnf['groups'] as $group => $groupdata) {

        ?>
<div id="group-<?php echo $group ?>" class="tabs-panel">
<h3 class="group-hidecaption"><?php echo $groupdata['title'] ?></h3>

<table class="form editortabs">

  <?php if ($group == 'user' && count($wyscnf['pmx_groups_list']) > 1) {

            ?>

  <tr>
    <td><label><?php echo _EDITOR_WHICH_USERS ?></label></td>
    <td>
      <table class="blind" cellspacing="0" cellpadding="0">
        <?php foreach($wyscnf['pmx_groups_list'] as $key => $label) {

                ?>
        <tr>
          <td><?php $checked = ((empty($wyscnf['globals']['pmxgroups'])) || (isset($wyscnf['globals']['pmxgroups']) && !empty($wyscnf['globals']['pmxgroups'][$key]))) ? ' checked="checked"': '' ?><input type="hidden" name="pmxgroups[<?php echo $key ?>]" value="0" /> <input type="checkbox" name="pmxgroups[<?php echo $key ?>]" value="1" <?php echo $checked ?> /></td>
          <td><?php echo $label ?></td>
        </tr>
        <?php }

            ?>
      </table>
    </td>
  </tr>

  <tr><td colspan="2"><hr /></td></tr>

  <?php }

        ?>

  <tr>
    <td width="40%"><label><?php echo _EDITOR_ACTIVE ?></label></td>
    <td>
    <select name="editor[<?php echo $group ?>]">
      <?php foreach ($editors as $key => $value) {

            ?>
      <?php $selected = ($wyscnf[$group]['editor'] === $key) ? ' selected="selected" class="current"': '' ?>
      <option value="<?php echo $key ?>" <?php echo $selected ?>><?php echo $value ?></option>
      <?php }

        ?>
    </select></td>
  </tr>

  <tr>
    <td><label><?php echo _EDITOR_TOOLMODE ?></label></td>
    <td><select name="mode[<?php echo $group ?>]">
      <?php foreach ($modes as $key => $value) {

            ?>
      <?php $selected = ($wyscnf[$group]['mode'] == $key) ? ' selected="selected" class="current"': '' ?>
      <option value="<?php echo $key ?>" <?php echo $selected ?>><?php echo $value ?></option>
      <?php }

        ?>
    </select></td>
  </tr>

  <tr><td colspan="2"><hr /></td></tr>

  <tr>
    <td width="40%"><label><?php echo _FILEMAN_ACTIVE ?></label></td>
    <td>
    <select name="manager[<?php echo $group ?>]">
      <?php foreach ($managers as $key => $value) {

            ?>
      <?php $selected = ($wyscnf[$group]['manager'] === $key) ? ' selected="selected" class="current"': '' ?>
      <option value="<?php echo $key ?>" <?php echo $selected ?>><?php echo $value ?></option>
      <?php }

        ?>
    </select></td>
  </tr>

  <tr>
    <td width="40%"><label><?php echo _FILEMAN_FOLDER ?></label></td>
    <td>
    <?php if ($group == 'admin') {

            ?>

      <table class="form border" id="fhdshdstrh">
        <tr>
          <th><?php echo _FILEMAN_FOLDER_PATH ?></th>
          <th><?php echo _FILEMAN_FOLDER_ALIAS ?></th>
          <th style="text-align: center"><a style="cursor: pointer; text-decoration: none" class="lineplus" title="<?php echo _FILEMAN_FOLDER_ADDLINE ?>">+</a></th>
        </tr>
      <?php foreach($wyscnf[$group]['roots'] as $alias => $path) {
                if (!file_exists($path)) {
                    continue;
                }

                ?>
        <tr>
          <td><input type="text" name="roots[admin][path][]" value="<?php echo $path ?>" size="30" /></td>
          <td colspan="2"><input type="text" name="roots[admin][alias][]" value="<?php echo $alias ?>" size="15" /></td>
        </tr>
      <?php }

            ?>
        <tr>
          <td><input type="text" name="roots[admin][path][]" value="" size="30" /></td>
          <td colspan="2"><input type="text" name="roots[admin][alias][]" value="" size="15" /></td>
        </tr>
      </table>


    <?php } else {

            ?>
        <?php foreach($wyscnf[$group]['roots'] as $alias => $path) {

                ?>
        <input type="text" name="roots[<?php echo $group ?>]" value="<?php echo $path ?>" required="required" size="40" />
        <?php break;
            } // nur den ersten Pfad verwenden

            ?>
    <?php } //endif

        ?>
    </td>
  </tr>


  <tr>
    <td><label><?php echo _EDITOR_FILETYPES ?></label></td>
    <td>
      <table class="blind" cellspacing="0" cellpadding="0">
        <?php foreach($filetype_labels as $key => $label) {

            ?>
        <tr valign="top">
          <td><?php echo $filetype_options[$group][$key] ?></td>
          <td><label><?php echo $label ?></label></td>
        </tr>
        <?php }

        ?>
      </table>
    </td>
  </tr>
  <tr>
    <td><label><?php echo _EDITOR_MFS ?></label></td>
    <td><?php $value = (empty($wyscnf[$group]['upload_max_size'])) ? '0': intval($wyscnf[$group]['upload_max_size']) ?><input type="text" size="6" maxlength="5" name="upload_max_size[<?php echo $group ?>]" value="<?php echo $value ?>" style="text-align: right" />&nbsp;<?php echo _EDITOR_INKB ?></td>
  </tr>
  <tr><td colspan="2"><span class="tiny"><?php echo _EDITOR_MFS_NOTE ?></span></td></tr>
  <tr>
    <td><label><?php echo _EDITOR_ADMINFILES ?></label></td>
    <td><?php $checked = (empty($wyscnf[$group]['allow_modify'])) ? '': ' checked="checked"' ?>
      <input type="hidden" name="allow_modify[<?php echo $group ?>]" value="0" />
      <input type="checkbox" name="allow_modify[<?php echo $group ?>]" value="1" <?php echo $checked ?> />
    </td>
  </tr>

  <tr><td colspan="2"><hr /><input type="submit" value="<?php echo _EDITOR_SAVE ?>"></td></tr>

</table>
</div>
<?php }

    ?>

<div id="miscopt" class="tabs-panel">
<h3 class="group-hidecaption"><?php echo _EDITOR_MISCOPT ?></h3>

<table class="form editortabs">
  <tr>
    <td><label><?php echo _EDITOR_MAX_IMG_WIDTH ?></label></td>
    <td><input type="text" size="6" maxlength="5" name="globals[max_img_width]" value="<?php echo $wyscnf['globals']['max_img_width'] ?>" style="text-align: right" />&nbsp;<?php echo _EDITOR_INPIXEL ?></td>
  </tr>
  <tr>
    <td><label><?php echo _EDITOR_MAX_IMG_HEIGHT ?></label></td>
    <td><input type="text" size="6" maxlength="5" name="globals[max_img_height]" value="<?php echo $wyscnf['globals']['max_img_height'] ?>" style="text-align: right" />&nbsp;<?php echo _EDITOR_INPIXEL ?></td>
  </tr>
  <tr>
    <td><label><?php echo _EDITOR_CHMODFOLDER ?></label></td>
    <td><input type="text" size="5" maxlength="4" name="globals[chmod_dir_to]" value="<?php echo $wyscnf['globals']['chmod_dir_to'] ?>" style="text-align: right" /></td>
  </tr>
  <tr>
    <td><label><?php echo _EDITOR_CHMODFILES ?></label></td>
    <td><input type="text" size="5" maxlength="4" name="globals[chmod_to]" value="<?php echo $wyscnf['globals']['chmod_to'] ?>" style="text-align: right" /></td>
  </tr>
  <tr><td colspan="2"><hr /></td></tr>
  <tr>
    <td><label><?php echo _EDITOR_AREAFORECOLOR ?></label></td>
    <td><input type="text" size="10" name="globals[area_foreground]" value="<?php echo $wyscnf['globals']['area_foreground'] ?>" /></td>
  </tr>
  <tr>
    <td><label><?php echo _EDITOR_AREABACKCOLOR ?></label></td>
    <td><input type="text" size="10" name="globals[area_background]" value="<?php echo $wyscnf['globals']['area_background'] ?>" /></td>
  </tr>
  <tr>
    <td colspan="2"><hr /><input type="submit" value="<?php echo _EDITOR_SAVE ?>"></td>
  </tr>
</table>
</div>


</div><!-- /group-tabs -->
</form>

<script type="text/javascript">
/* <![CDATA[ */
  $(document).ready(function() {
    var tab_cookie_name = "tab_editoradmin";
    $("#group-tabs").tabs({
      active: ($.cookie(tab_cookie_name) || 0),
      activate: function(event, ui) {
        var newIndex = ui.newTab.parent().children().index(ui.newTab);
        $.cookie(tab_cookie_name, newIndex, {
          path: window.location.pathname,
          expires: 7
        });
      }
    });
    $("#group-tabs .group-hidecaption").hide();
  });

  $('#fhdshdstrh a.lineplus').click(function(){
    $('#fhdshdstrh tr:last').after('<tr>' + $('#fhdshdstrh tr:last').html() + '<\/tr>');
  });

/* ]]> */
</script>

    <?php
    include('footer.php');
}

/**
 * SaveEditor()
 * Editorkonfiguration in Datei speichern
 *
 * @return
 */
function SaveEditor()
{
    $wyscnf = Textarea::get_config();

    $conf_file = $wyscnf['file'];

    $userconfig = load_class('Userconfig');

    /* spezielle Benutzergruppen, falls welche vorhanden */
    if (isset($_POST['pmxgroups']) && is_array($_POST['pmxgroups'])) {
        $newgroups = $_POST['pmxgroups'];
    } else {
        // wenn nicht, gilt das alles nur fuer die Standardgruppe
        $newgroups = array($userconfig->default_group => 1);
    }

    /* die Standard-Chmods */
    if ($_POST['globals']['chmod_dir_to'] < 0) {
        $mode = mxEditorGetChmods('chmod_dir_to');
        $_POST['globals']['chmod_dir_to'] = sprintf("%o", $mode);
    }
    $chmod_dir_to = (empty($_POST['globals']['chmod_dir_to'])) ? '' : sprintf('%04d', intval($_POST['globals']['chmod_dir_to']));

    if ($_POST['globals']['chmod_to'] < 0) {
        $mode = mxEditorGetChmods('chmod_to');
        $_POST['globals']['chmod_to'] = sprintf("%o", $mode);
    }
    $chmod_to = (empty($_POST['globals']['chmod_to'])) ? '' : sprintf('%04d', intval($_POST['globals']['chmod_to']));

    /* die Farben der Textarea */
    $area_foreground = (empty($_POST['globals']['area_foreground'])) ? '' : $_POST['globals']['area_foreground'];
    $area_background = (empty($_POST['globals']['area_background'])) ? '' : $_POST['globals']['area_background'];

    $write['globals'] = serialize(array(// globals-array erstellen
            'pmxgroups' => $newgroups,
            'max_img_width' => intval($_POST['globals']['max_img_width']),
            'max_img_height' => intval($_POST['globals']['max_img_height']),
            'chmod_dir_to' => $chmod_dir_to,
            'chmod_to' => $chmod_to,
            'area_foreground' => $area_foreground,
            'area_background' => $area_background,
            ));

    /* Versuchen die upload_max_filesize aus der php.ini auszuwerten */
    $check = Textarea::get_max_uploadsize();
    if ($check > 0) {
        $check = $check / 1024;
    }

    /* Die Benutzer(gruppen) spezifischen Einstellungen */
    foreach ($_POST['editor'] as $key => $value) {
        $group = array();
        $group['upload_max_size'] = intval($_POST['upload_max_size'][$key]);
        if ($check > 0 && ($group['upload_max_size'] === -1 || $group['upload_max_size'] > $check)) {
            $group['upload_max_size'] = $check;
        }
        $group['editor'] = $value;
        $group['mode'] = $_POST['mode'][$key];
        $group['allow_modify'] = intval($_POST['allow_modify'][$key]);
        $group['filetype'] = $_POST['filetype'][$key];

        $group['manager'] = (is_numeric($_POST['manager'][$key])) ? false : $_POST['manager'][$key];

        if ($key == 'admin') {
            foreach ($_POST['roots']['admin']['path'] as $ii => $path) {
                if ($path && file_exists($path)) {
                    $alias = $_POST['roots']['admin']['alias'][$ii];
                    if (!$alias) {
                        $alias = basename($path);
                    }
                    $group['roots'][$alias] = $path;
                }
            }
        } else {
            // alias = _FILES damit als Konstante verwendbar...
            $group['roots']['_FILES'] = $_POST['roots'][$key];
        }

        $write[$key] = serialize($group);
    }

    /* den zu schreibenden String erstellen */
    foreach ($write as $key => $value) {
        $str[] = '$wyscnf[\'' . $key . '\'] = unserialize(\'' . $value . '\');';
    }

    $out = '<?php
/**
 * pragmaMx - Web Content Management System
 * Copyright by pragmaMx Developer Team - http://www.pragmamx.org
 *
 * write with: $Id: index.php 6 2015-07-08 07:07:06Z PragmaMx $
 */

' . implode("\n", $str) . '

?>';

    $ok = mx_write_file($conf_file, trim($out), true);

    /* alles ok > redirect */
    if ($ok) {
        mxRedirect(adminUrl(PMX_MODULE), _EDITOR_SETTINGSAVED, 1);
        return;
    }
    /* error > exit */
    mxErrorScreen(_EDITOR_SETTINGNOSAVED, _EDITOR_WARN, true);
    return;
}

/**
 * mxEditorGetChmods()
 * chmods automatisch ermitteln
 *
 * @param mixed $mode
 * @return
 */
function mxEditorGetChmods($mode)
{
    /* die Standard-Chmods */
    if ($mode === 'chmod_dir_to') {
        if ($file = realpath(PMX_IMAGE_DIR . '/iupload')) {
            $mode = fileperms($file);
        } else if ($file = realpath(PMX_REAL_BASE_DIR . '/media/images')) {
            $mode = fileperms($file);
        } else {
            $mode = fileperms(__DIR__);
        }
    } else {
        $found = false;
        $endings = array('gif', 'png', 'jpg', 'jpeg');
        foreach ((array)glob(PMX_IMAGE_DIR . DS . 'admin' . DS . '*') as $image) {
            if ($image) {
                $info = pathinfo($image);
                if (isset($info['extension']) && in_array(strtolower($info['extension']), $endings)) {
                    $mode = fileperms($image);
                    $found = true;
                    break;
                }
            }
        }
        if (!$found) {
            $mode = fileperms(__FILE__);
        }
    }
    $mode &= 0x1ff; # Remove the bits we don't need
    return $mode;
}

/**
 * mxeditor_get_usergroups_list()
 * Die Liste der pragmaMx Usergruppen
 *
 * @return
 */
function mxeditor_get_usergroups_list()
{
    static $groups;
    if (isset($groups)) {
        return $groups;
    }
    global $prefix;

    $userconfig = load_class('Userconfig');

    $result = sql_system_query("SELECT access_id, access_title FROM " . $prefix . "_groups_access ORDER BY access_title");
    $groups[$userconfig->default_group] = MX_FIRSTGROUPNAME;
    while ($row = sql_fetch_assoc($result)) {
        $groups[$row['access_id']] = $row['access_title'];
    }
    return $groups;
}

/* Was ist zu tun... */
switch ($op) {
    case PMX_MODULE . '/save':
        SaveEditor();
        break;
    default:
        ReadEditor();
        break;
}

?>