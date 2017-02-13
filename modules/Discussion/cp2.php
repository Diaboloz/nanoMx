<?php
/**
 * mxBoard, pragmaMx Module
 * Copyright by pragmaMx Developer Team - http://www.pragmamx.org
 *
 * $Author: PragmaMx $
 * $Revision: 6 $
 * $Date: 2015-07-08 09:07:06 +0200 (mer. 08 juil. 2015) $
 *
 * based on eBoard v1.1, rewrite and modified by
 * vkpMx-Developer-Team (http://www.maax-design.de)
 * Original source-code made by the XMB-team
 * (XMB-Forum, http://www.xmbforum.com), modified for nukestyle-systems
 * by Trollix (XForum, http://www.trollix.com).
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 */

defined('mxMainFileLoaded') or die('access denied');

include_once(dirname(__FILE__) . DS . 'includes' . DS . 'header.php');

if (!$eBoardUser['isadmin']) {
    return mxbExitMessage(_NOTADMIN, true);
}

$mxbnavigator->add(false, _TEXTCP);

?>

<table cellspacing="0" cellpadding="0" border="0" width="<?php echo $tablewidth ?>" align="center">
<tr><td class="bordercolor">

<table border="0" cellspacing="<?php echo $borderwidth ?>" cellpadding="<?php echo $tablespace ?>" width="100%">

<?php
mxbAdminMenu();

if ($action == "themes") {
    $colornames = array('bgcolor',
        'altbg1',
        'altbg2',
        'link',
        'bordercolor',
        'bgcolheader',
        'bgcolheadertext',
        'top',
        'catcolor',
        'tabletext',
        'text',
        'color1',
        'color2',
        );

    if (empty($single) && empty($XFthemesubmit)) {
        $query = sql_query("SELECT name FROM $table_themes ORDER BY name");
        while (list($tname) = sql_fetch_row($query)) {
            $allthemes[$tname] = (file_exists(MXB_BASEMODTEMPLATE . '/' . $tname . '/theme.php')) ? 'html' : 'db';
        }
        $handle = opendir(MXB_BASEMODTEMPLATE);
        if ($handle) {
            while ($file = readdir($handle)) {
                if (!is_file(MXB_BASEMODTEMPLATE . '/' . $file) && !preg_match('#\.|CVS#', $file) && !isset($allthemes[$file])) {
                    $qry = '';
                    if (file_exists(MXB_BASEMODTEMPLATE . '/' . $file . '/theme.php') && file_exists(MXB_BASEMODTEMPLATE . '/' . $file . '/inst.php')) {
                        include (MXB_BASEMODTEMPLATE . '/' . $file . '/inst.php');
                    } else {
                        foreach ($colornames as $color) {
                            $$color = mxb_helper_colors::repair($$color);
                        }
                        $qry = "INSERT INTO $table_themes VALUES('$file', '$bgcolor', '$altbg1', '$altbg2', '$link', '$bordercolor', '$bgcolheader', '$bgcolheadertext', '$top', '$catcolor', '$tabletext', '$text', '$borderwidth', '$tablewidth', '$tablespace','$font','$fontsize','$altfont','$altfontsize', '$replyimg', '$newtopicimg', '', '', '$color1', '$color2')";
                    }
                    if ($qry && sql_query($qry)) {
                        $allthemes[$file] = 'html';
                    }
                }
            }
            closedir($handle);
        }
        ksort($allthemes) ?>

<tr class="altbg2">
<td align="center">
<br />
<form method="post" action="<?php echo MXB_BM_CP21 ?>action=themes">
<table cellspacing="0" cellpadding="0" border="0" width="100%" align="center">
<tr><td class="bordercolor">

<table border="0" cellspacing="<?php echo $borderwidth ?>" cellpadding="<?php echo $tablespace ?>" width="100%">

<tr class="mxb-header">
<td colspan="4"><?php echo _TEXTTHEMENAME ?></td>
<td><?php echo _TEXTDELETEQUES ?></td>
</tr>

<?php

        foreach ($allthemes as $this_theme_name => $ishtml) {
            echo '
            <tr class="tablerow altbg2">
            <td>' . (($this_theme_name == 'default') ? '<strong>default</strong>' : '<input type="text" name="themename[' . $this_theme_name . ']" value="' . $this_theme_name . '" size="35" maxlength="30" />') . '</td>
            <td><a href="' . MXB_BM_CP21 . 'action=themes&amp;single=' . $this_theme_name . '">' . _TEXTDETAILS . '</a></td>
            <td><a href="' . MXB_BM_CP21 . 'action=themes&amp;settheme=' . $this_theme_name . '">test</a></td>
            <td>' . (($ishtml == 'html') ? '<a href="' . MXB_BASEMODTEMPLATE . '/' . $this_theme_name . '/" target="_blank">view</a>' : '&nbsp;') . '</td>
            <td align="center">' . (($this_theme_name == 'default') ? '&nbsp;' : '<input type="checkbox" name="themedelete[' . $this_theme_name . ']" value="' . $this_theme_name . '" />') . '</td>
            </tr>
            ';
        }

        ?>

<tr class="altbg2"><td colspan="5">&nbsp;</td></tr>
<tr class="tablerow altbg1">
<td colspan="5"><a href="<?php echo MXB_BM_CP21 ?>action=themes&amp;single=anewtheme1"><?php echo _TEXTNEWTHEME ?></a></td>
</tr>

</table>
</td></tr></table>
<center><br/><input type="submit" name="XFthemesubmit" value="<?php echo _TEXTSUBMITCHANGES ?>" /></center>
</form>

</td>
</tr>

<?php
    }

    if (!empty($XFthemesubmit)) {
        if (isset($_POST['themedelete'])) {
            foreach ($_POST['themedelete'] as $delete => $dummy) {
                if (!empty($delete) && $delete != 'default') {
                    if (sql_query("DELETE FROM $table_themes WHERE name='$delete' LIMIT 1")) {
                        sql_query("UPDATE $table_members SET theme='' WHERE theme='$delete'");
                        sql_query("UPDATE $table_forums SET theme='' WHERE theme='$delete'");
                        $changes[$delete] = 'default';
                    }
                    unset($_POST['themename'][$delete]);
                    $changes[] = $delete;
                }
            }
        }

        foreach ($_POST['themename'] as $oldname => $newname) {
            if (!empty($newname) && ($oldname != $newname) && ($oldname != 'default') && ($newname != 'default')) {
                if (sql_query("UPDATE $table_themes SET name='$newname' WHERE name='$oldname' LIMIT 1")) {
                    sql_query("UPDATE $table_members SET theme='$newname' WHERE theme='$oldname'");
                    sql_query("UPDATE $table_forums SET theme='$newname' WHERE theme='$oldname'");
                    $changes[$oldname] = $newname;
                }
            }
        }

        echo "<tr class=\"tablerow altbg2\"><td align=\"center\">" . mxbMessageScreen(_TEXTTHEMEUPDATE) . "</td></tr>";
        echo mxbRedirectScript(MXB_BM_CP21 . "action=themes", 2000);
    }

    if (!empty($single) && $single != "anewtheme1" && $single != "submit") {
        $themevars = mxb_theme_getvars($single);

        ?>
<tr class="altbg2">
<td align="center"><br />
  <form method="post" action="<?php echo MXB_BM_CP21 ?>action=themes&amp;single=submit" name="themestuff" id="themestuff">
    <?php mxb_theme_form($themevars) ?>
    <input type="hidden" name="orig" value="<?php echo $single ?>" />
  </form>
</td>
</tr>

<?php
    }

    if (!empty($single) && $single == "anewtheme1") {
        $themevars = mxb_theme_getvars($XFtheme);
        $themevars['name'] = $single; // überschreiben, weil neues Theme...


        ?>
<tr class="altbg2">
<td align="center"><br />
  <form method="post" action="<?php echo MXB_BM_CP21 ?>action=themes&amp;single=submit" name="themestuff" id="themestuff">
    <?php mxb_theme_form($themevars) ?>
    <input type="hidden" name="newtheme" value="<?php echo $single ?>" />
  </form>
</td>
</tr>

<?php
    }
    // Feld gibt's nicht mehr ;)
    $newtopicimgnew = '';

    if (!empty($single) && $single == "submit") {
        foreach ($colornames as $value) {
            $cname = $value . 'new';
            if (empty($_POST[$cname])) {
                $color = $$value;
            } else {
                $color = $_POST[$cname];
            }
            $$cname = mxb_helper_colors::repair($color);
        }

        if (!empty($replyimgnew) && !file_exists(MXB_ROOTMOD . 'imagesets/' . $replyimgnew)) {
            $replyimgnew = '';
        }
        $boardlogonew = '';

        if (empty($newtheme) && !empty($namenew)) {
            sql_query("UPDATE $table_themes SET name='$namenew', bgcolor='$bgcolornew', altbg1='$altbg1new', altbg2='$altbg2new', link='$linknew', bordercolor='$bordercolornew', header='$headernew', headertext='$headertextnew', top='$topnew', catcolor='$catcolornew', tabletext='$tabletextnew', text='$textnew', borderwidth='$borderwidthnew', tablewidth='$tablewidthnew', tablespace='$tablespacenew', fontsize='$fsizenew', font='$fnew', altfontsize='$altfsizenew', altfont='$altfnew', replyimg='$replyimgnew', postscol='', color1='$color1new', color2='$color2new' WHERE name='$orig'");
            echo "<tr class=\"tablerow altbg2\"><td align=\"center\">" . mxbMessageScreen(_TEXTTHEMEUPDATE) . "</td></tr>";
            echo mxbRedirectScript(MXB_BM_CP21 . "action=themes&single=" . $namenew, 2000);
        } else if (!empty($newtheme)) {
            if (empty($namenew)) {
                $namenew = uniqid('new_theme_');
            }
            sql_query("INSERT INTO $table_themes VALUES('$namenew', '$bgcolornew', '$altbg1new', '$altbg2new', '$linknew', '$bordercolornew', '$headernew', '$headertextnew', '$topnew', '$catcolornew', '$tabletextnew', '$textnew', '$borderwidthnew', '$tablewidthnew', '$tablespacenew','$fnew','$fsizenew','$altfnew','$altfsizenew', '$replyimgnew', '$newtopicimgnew', '$boardlogonew', '', '$color1new', '$color2new')");
            echo "<tr class=\"tablerow altbg2\"><td align=\"center\">" . mxbMessageScreen(_TEXTTHEMEUPDATE) . "</td></tr>";
            echo mxbRedirectScript(MXB_BM_CP21 . "action=themes&single=" . $namenew, 2000);
        }
    }
}

if ($action == "smilies") {
    if (empty($smiliesubmit)) {
        $avail = array() ?>

<tr class="altbg2">
<td align="center">
<br /><a name="smilies" id="smilies"></a>
<form method="post" action="<?php echo MXB_BM_CP21 ?>action=smilies">
<table cellspacing="0" cellpadding="0" border="0" width="100%" align="center">
<tr><td class="bordercolor">

<table border="0" cellspacing="<?php echo $borderwidth ?>" cellpadding="<?php echo $tablespace ?>" width="100%">

<tr class="mxb-header">
<th colspan="5"><?php echo _SMILIESLIST ?></th>
</tr>

<tr class="mxb-header">
<td><?php echo _TEXTORDER ?></td>
<td><?php echo _TEXTSMILIECODE ?>:</td>
<td><?php echo _TEXTSMILIEURL ?>:</td>
<td colspan="2">Smilie:</td>
</tr>

<?php
        $totalsmilies = intval($smilieslinenumber * $smiliesrownumber);
        $query = sql_query("SELECT * FROM $table_smilies WHERE type='smiley' ORDER BY id");
        $i = 0;
        while ($smilie = sql_fetch_object($query)) {
            // Added Smilie picture-support for the admin menu, 12.08.2002, Marko, http://fanzone.wnsc.at
            $delcheck = '';
            $warn_url = '';
            $warn_code = '';
            if (in_array($smilie->code, $avail)) {
                $delcheck = ' checked="checked"';
                $warn_code = mxCreateImage(MXB_BASEMODIMG . '/red_dot.gif', 'error: double code') . ' ';
            }
            if (isset($avail[$smilie->url])) {
                // $delcheck = ' checked="checked"';
                $warn_url = mxCreateImage(MXB_BASEMODIMG . '/red_dot.gif', 'error: double url') . ' ';
            }
            if ($size = @getimagesize(MXB_BASEMODIMG . '/' . $smilie->url)) {
                $image = '<img src="' . MXB_BASEMODIMG . '/' . $smilie->url . '" alt="' . $smilie->url . '" border="0" ' . $size[3] . '/>';
            } else {
                $delcheck = ' checked="checked"';
                $image = '<div style="background-color: Yellow; color: Red;"><strong>missing!</strong> ' . mxCreateImage(MXB_BASEMODIMG . '/red_dot.gif', 'error: missing') . ' ' . $smilie->url . '</div>';
            }

            $avail[$smilie->url] = $smilie->code;
            if ($i == $totalsmilies) {

                ?>
<tr class="altbg2" valign="top"><td colspan="3" class="f11px"><?php echo _TEXTSMILIESLINENUMBER ?></td><th><?php echo $smilieslinenumber ?></th><th rowspan="2" valign="middle">= <?php echo $totalsmilies ?></th></tr>
<tr class="altbg2" valign="top"><td colspan="3" class="f11px"><?php echo _TEXTSMILIESROWSNUMBER ?></td><th><?php echo $smiliesrownumber ?></th></tr>
<tr class="mxb-header">
<th colspan="5"><?php echo _MORE_SMILIES ?></th>
</tr>
<tr class="mxb-header">
<td><?php echo _TEXTORDER ?></td>
<td><?php echo _TEXTSMILIECODE ?>:</td>
<td><?php echo _TEXTSMILIEURL ?>:</td>
<td colspan="2">Smilie:</td>
</tr>
<?php
            }
            $i++ ?>

<tr>
<td class="tablerow altbg2" align="center"><input size="3" type="text" name="smilie[<?php echo $smilie->id ?>][id]" value="<?php echo $i ?>" /></td>
<td class="tablerow altbg2"><?php echo $warn_code ?><input size="15" type="text" name="smilie[<?php echo $smilie->id ?>][code]" value="<?php echo $smilie->code ?>" /></td>
<td class="tablerow altbg2"><?php echo $warn_url ?><input size="30" type="text" name="smilie[<?php echo $smilie->id ?>][url]" value="<?php echo $smilie->url ?>" /></td>
<td class="tablerow altbg2" align="center"><?php echo $image ?></td>
<td class="tablerow altbg2" align="center"><?php echo _TEXTDELETEQUES ?> <input type="checkbox" name="smilie[<?php echo $smilie->id ?>][delete]" value="<?php echo $smilie->id ?>"<?php echo $delcheck ?> /></td>
</tr>

<?php
        }

        $handle = opendir(MXB_BASEMODIMG . '/smilies/');
        while ($smil = readdir($handle)) {
            if (isset($avail['smilies/' . $smil])) {
                continue;
            }
            if (preg_match('#(.*)\.(jpe?g|gif|png)$#i', $smil, $match)) {
                $code = preg_replace('#([[:punct:]]|\s)*#', '', $match[1]);
                $i++ ?>
<tr>
<td class="tablerow altbg2" align="center"><input size="3" type="text" name="smilie[<?php echo ($i * 10000) ?>][id]" value="<?php echo $i ?>" /></td>
<td class="tablerow altbg2"><input size="15" type="text" name="smilie[<?php echo ($i * 10000) ?>][code]" value=":<?php echo $code ?>:" /></td>
<td class="tablerow altbg2"><input size="30" type="text" name="smilie[<?php echo ($i * 10000) ?>][url]" value="<?php echo 'smilies/' . $smil ?>" /></td>
<td class="tablerow altbg2" align="center"><?php echo _TEXTNEWSMILIE ?>? <input type="checkbox" name="smilie[<?php echo ($i * 10000) ?>][add]" value="<?php echo $i ?>" /></td>
<td class="tablerow altbg2" align="center"><?php echo mxCreateImage(MXB_BASEMODIMG . '/' . 'smilies/' . $smil, $smil) ?> <input type="hidden" name="smilie[<?php echo ($i * 10000) ?>][new]" value="<?php echo $i ?>"/></td>
</tr>
<?php

            }
        }

        ?>
<tr class="tablerow altbg1">
<td><?php echo _TEXTNEWSMILIE ?>:</td>
<td><input size="15" type="text" name="newcode" /></td>
<td colspan="3"><input size="30" type="text" name="newurl1" /></td>
</tr>

<tr class="altbg2" valign="top"><td colspan="5"><br/></td></tr>

<tr class="mxb-header">
<th colspan="5"><?php echo _PICONS ?></th>
</tr>
<tr class="mxb-header">
<td><?php echo _TEXTORDER ?></td>
<td colspan="2"><?php echo _TEXTSMILIEURL ?>:</td>
<td colspan="2">icon:</td>
</tr>

<?php
        $avail = array();
        $i = 0;
        $query = sql_query("SELECT * FROM $table_smilies WHERE type='picon' ORDER BY id");
        while ($smilie = sql_fetch_object($query)) {
            // Added Smilie picture-support for the admin menu, 12.08.2002, Marko, http://fanzone.wnsc.at
            $delcheck = 0;
            $warn = '';
            if (isset($avail[$smilie->url])) {
                $delcheck = 1;
                $warn = mxCreateImage(MXB_BASEMODIMG . '/red_dot.gif', 'error: double') . '&nbsp;';
            }
            $avail[$smilie->url] = 1;
            if ($size = @getimagesize(MXB_BASEMODIMG . '/' . $smilie->url)) {
                $image = '<img src="' . MXB_BASEMODIMG . '/' . $smilie->url . '" alt="' . $smilie->url . '" border="0" ' . $size[3] . '/>';
            } else {
                $delcheck = 1;
                $image = '<div style="background-color: Yellow; color: Red;">' . mxCreateImage(MXB_BASEMODIMG . '/red_dot.gif', 'error: missing') . ' ' . $smilie->url . '</div>';
            }
            if ($i == $maxposticons) {

                ?>
<tr class="mxb-header">
<td><?php echo _TEXTORDER ?></td>
<td colspan="2"><?php echo _TEXTSMILIEURL ?>:</td>
<td colspan="2">icon:</td>
</tr>

<?php
            }
            $sel = ($i >= $maxposticons || $delcheck) ? ' checked="checked"' : '';
            $i++ ?>
<tr>
<td class="tablerow altbg2" align="center"><?php echo $warn ?><input size="3" type="text" name="smilie[<?php echo $smilie->id ?>][id]" value="<?php echo $i ?>" /></td>
<td class="tablerow altbg2" colspan="2"><input size="30" type="text" name="smilie[<?php echo $smilie->id ?>][url]" value="<?php echo $smilie->url ?>" /></td>
<td class="tablerow altbg2" align="center"><?php echo $image ?></td>
<td class="tablerow altbg2" align="center"><?php echo _TEXTDELETEQUES ?> <input type="checkbox" name="smilie[<?php echo $smilie->id ?>][delete]" value="<?php echo $smilie->id ?>"<?php echo $sel ?> /></td>

</tr>

<?php
        }
        $handle = opendir(MXB_BASEMODIMG . '/posticons/');
        while ($smil = readdir($handle)) {
            if (isset($avail['posticons/' . $smil])) {
                continue;
            }
            $avail['posticons/' . $smil] = 1;
            if (preg_match('#(.*)\.(jpe?g|gif|png)$#i', $smil, $match)) {
                $i++ ?>
<tr>
<td class="tablerow altbg2" align="center"><input size="3" type="text" name="smilie[<?php echo ($i * 10000) ?>][id]" value="<?php echo $i ?>" /></td>
<td class="tablerow altbg2" colspan="2"><input size="30" type="text" name="smilie[<?php echo ($i * 10000) ?>][url]" value="<?php echo 'posticons/' . $smil ?>" /></td>
<td class="tablerow altbg2" align="center"><?php echo _TEXTNEWPICON ?>? <input type="checkbox" name="smilie[<?php echo ($i * 10000) ?>][add]" value="<?php echo $i ?>" /></td>
<td class="tablerow altbg2" align="center"><?php echo mxCreateImage(MXB_BASEMODIMG . '/' . 'posticons/' . $smil, $smil) ?> <input type="hidden" name="smilie[<?php echo ($i * 10000) ?>][new]" value="<?php echo $i ?>"/></td>
</tr>
<?php
            }
        }

        ?>
<tr class="tablerow altbg1">
<td><?php echo _TEXTNEWPICON ?></td>
<td colspan="4"><input size="30" type="text" name="newurl2" /></td>
</tr>

</table>
</td></tr></table>
<center><br/><input type="submit" name="smiliesubmit" value="<?php echo _TEXTSUBMITCHANGES ?>" /></center>
</form>

</td>
</tr>

<?php
    }

    if (!empty($smiliesubmit)) {
        $new = array();
        $i = 0;
        foreach ($_POST['smilie'] as $oldid => $arr) {
            // die zu löschenden, einfach im neuen array weglassen
            if (isset($arr['delete']) || (isset($arr['new']) && empty($arr['add']))) {
                continue;
            }
            $i++;
            // zu der neuen pseudo-id (*1000) wird der aktuelle zähler zugezählt, weil die neue id doppelt sein könnte
            // dies ergibt dann wieder eine eindeutige id, zum sortieren
            $new[intval($arr['id']) * 1000 + $i] = $arr;
        }
        // das neue array, nach der pseudo-id sortieren
        ksort($new);
        // neuer smilie zufügen?
        if (!empty($_POST['newurl1'])) {
            $new[] = array('url' => $newurl1, 'code' => $newcode);
        }
        // neuer picon zufügen?
        if (!empty($_POST['newurl2'])) {
            $new[] = array('url' => $newurl2);
        }
        // das sortierte Array durchlaufen und die query zusammenbauen
        unset($insert);
        $i = 0;
        foreach ($new as $arr) {
            $i++;
            // entscheiden, ob picon oder smilie ;)
            if (isset($arr['code'])) {
                $type = 'smiley';
                $code = $arr['code'];
            } else {
                $type = 'picon';
                $code = '';
            }
            // der insert-Teil für die query
            $insert[] = "('" . $type . "', '" . $code . "', '" . $arr['url'] . "', " . $i . ")";
        }
        // wenn überhaupt smilies vorhanden...
        if (isset($insert)) {
            // alle mit einer id über der höchsten neuen id löschen
            sql_query("DELETE FROM `$table_smilies` WHERE id >= $i");
            // alle anderen, durch die neu sortierten ersetzen
            $insert = implode(',', $insert);
            $qry = "REPLACE INTO `$table_smilies` (`type`, `code`, `url`, `id`) VALUES " . $insert . "";
            sql_query($qry);
        }

        echo "<tr class=\"tablerow altbg2\"><td align=\"center\">" . mxbMessageScreen(_TEXTSMILIEUPDATE) . "</td></tr>";
        echo mxbRedirectScript(MXB_BM_CP21 . "action=smilies", 500);
    }
}

if ($action == "censor") {
    if (empty($censorsubmit)) {

        ?>

<tr class="altbg2">
<td align="center">
<br />
<form method="post" action="<?php echo MXB_BM_CP21 ?>action=censor">
<table cellspacing="0" cellpadding="0" border="0" width="100%" align="center">
<tr><td class="bordercolor">

<table border="0" cellspacing="<?php echo $borderwidth ?>" cellpadding="<?php echo $tablespace ?>" width="100%">

<tr class="mxb-header">
<td><?php echo _TEXTDELETEQUES ?></td>
<td><?php echo _TEXTCENSORFIND ?></td>
<td><?php echo _TEXTCENSORREPLACE ?></td>
</tr>

<?php
        $query = sql_query("SELECT * FROM $table_words ORDER BY id");
        while ($censor = sql_fetch_object($query)) {

            ?>

<tr class="tablerow altbg2">
<td><input type="checkbox" name="delete<?php echo $censor->id ?>" value="<?php echo $censor->id ?>" /></td>
<td><input type="text" name="find<?php echo $censor->id ?>" value="<?php echo $censor->find ?>" /></td>
<td><input type="text" name="replace<?php echo $censor->id ?>" value="<?php echo $censor->replace1 ?>" /></td>
</tr>

<?php
        }

        ?>

<tr class="altbg2"><td colspan="3"> </td></tr>
<tr class="tablerow altbg1">
<td colspan="2"><?php echo _TEXTNEWCODE ?>&nbsp;&nbsp;<input type="text" name="newfind" /></td>
<td><input type="text" name="newreplace" /></td>
</tr>

</table>
</td></tr></table>
<center><br/><input type="submit" name="censorsubmit" value="<?php echo _TEXTSUBMITCHANGES ?>" /></center>
</form>

</td>
</tr>

<?php
    }

    if (!empty($censorsubmit)) {
        $querycensor = sql_query("SELECT id FROM $table_words");

        while ($censor = sql_fetch_object($querycensor)) {
            $find = 'find' . $censor->id;
            $find = "${$find}";
            $replace = 'replace' . $censor->id;
            $replace = "${$replace}";
            $delete = (isset($_REQUEST['delete' . $censor->id])) ? intval($_REQUEST['delete' . $censor->id]) : 0;
            // $delete = 'delete' . $censor->id;
            // $delete = "${$delete}";
            if ($delete) {
                sql_query("DELETE FROM $table_words WHERE id='$delete'");
            }

            sql_query("UPDATE $table_words SET find='$find', replace1='$replace' WHERE id='" . $censor->id . "'");
        }

        if ($newfind) {
            sql_query("INSERT INTO $table_words VALUES ('$newfind', '$newreplace', '')");
        }

        echo "<tr class=\"tablerow altbg2\"><td align=\"center\">" . mxbMessageScreen(_TEXTCENSORUPDATE) . "</td></tr>";
        echo mxbRedirectScript(MXB_BM_CP21 . "action=censor", 2000);
    }
}

if ($action == "ranks") {
    if (empty($rankssubmit)) {

        ?>

<tr class="altbg2">
<td align="center">
<br />
<form method="post" action="<?php echo MXB_BM_CP21 ?>action=ranks">
<table cellspacing="0" cellpadding="0" border="0" width="100%" align="center">
<tr><td class="bordercolor">

<table border="0" cellspacing="<?php echo $borderwidth ?>" cellpadding="<?php echo $tablespace ?>" width="100%">

<tr class="mxb-header">
<td><?php echo _TEXTDELETEQUES ?></td>
<td><?php echo _TEXTCUSSTATUS ?></td>
<td><?php echo _TEXTPOSTS ?></td>
<td><?php echo _TEXTSTARS ?></td>
</tr>

<?php
        $query = sql_query("SELECT * FROM $table_ranks ORDER BY id");
        while ($rank = sql_fetch_object($query)) {

            ?>

<tr class="tablerow altbg2">
<td><input type="checkbox" name="delete<?php echo $rank->id ?>" value="<?php echo $rank->id ?>" /></td>
<td><input type="text" name="title<?php echo $rank->id ?>" value="<?php echo $rank->title ?>" /></td>
<td><input type="text" name="posts<?php echo $rank->id ?>" value="<?php echo $rank->posts ?>" size="5" /></td>
<td><input type="text" name="stars<?php echo $rank->id ?>" value="<?php echo $rank->stars ?>" size="4" /></td>
</tr>

<?php
        }

        ?>

<tr class="altbg2"><td colspan="6"> </td></tr>
<tr class="tablerow altbg1">
<td colspan="2"><?php echo _TEXTNEWRANK ?>&nbsp;&nbsp;<input type="text" name="newtitle" /></td>
<td><input type="text" name="newposts" size="5" /></td>
<td><input type="text" name="newstars" size="4" /></td>
</tr>

</table>
</td></tr></table>
<center><br/><input type="submit" name="rankssubmit" value="<?php echo _TEXTSUBMITCHANGES ?>" /></center>
</form>

</td>
</tr>

<?php
    }

    if (!empty($rankssubmit)) {
        $query = sql_query("SELECT id FROM $table_ranks");

        while ($ranks = sql_fetch_object($query)) {
            $title = mxAddSlashesForSQL($_POST['title' . $ranks->id]);
            $posts = intval($_POST['posts' . $ranks->id]);
            $stars = intval($_POST['stars' . $ranks->id]);

            if (isset($_POST['delete' . $ranks->id])) {
                $delete = intval($_POST['delete' . $ranks->id]);
                sql_query("DELETE FROM $table_ranks WHERE id='$delete'");
            } else {
                sql_query("UPDATE $table_ranks SET title='$title', posts='$posts', stars='$stars' WHERE id='" . $ranks->id . "'");
            }
        }

        if (!empty($newtitle)) {
            sql_query("INSERT INTO $table_ranks (title, posts, stars) VALUES ('$newtitle', '$newposts', '$newstars')");
        }

        echo "<tr class=\"tablerow altbg2\"><td align=\"center\">" . mxbMessageScreen(_TEXTRANKINGSUPDATE) . "</td></tr>";
        echo mxbRedirectScript(MXB_BM_CP21 . "action=ranks", 2000);
    }
}

if ($action == "newsletter") {
    if (empty($newslettersubmit)) {

        ?>

<tr class="altbg2">
<td align="center">
<br />
<form method="post" action="<?php echo MXB_BM_CP21 ?>action=newsletter">
<table cellspacing="0" cellpadding="0" border="0" width="100%" align="center">
<tr><td class="bordercolor">

<table border="0" cellspacing="<?php echo $borderwidth ?>" cellpadding="<?php echo $tablespace ?>" width="100%">

<tr class="mxb-header">
<td colspan=2><?php echo _TEXTNEWSLETTER ?></td>
</tr>

<tr class="tablerow altbg1">
<td><?php echo _TEXTSUBJECT ?></td><td><input type="text" name="newssubject" size="45" /></td>
</tr>
<tr class="tablerow altbg1">
<td valign=top><?php echo _TEXTMESSAGE2 ?></td><td><textarea rows="<?php echo $textarearows ?>" cols="<?php echo $textareacols ?>" name="newsmessage"></textarea></td>
</tr>

</table>
</td></tr></table>
<center><br/><input type="submit" name="newslettersubmit" value="<?php echo _TEXTSUBMITCHANGES ?>" /></center>
</form>

</td>
</tr>

<?php
    }
    if (!empty($newslettersubmit)) {
        $query = sql_query("SELECT u.email
                        FROM $table_members AS fm
                        LEFT JOIN {$user_prefix}_users AS u
                        ON fm.username = u.uname
                        WHERE fm.newsletter='yes' AND u.email<>''");
        while ($memnews = sql_fetch_object($query)) {
            $newsmessage = stripslashes($newsmessage);
            mxMail($memnews->email, $newssubject, $newsmessage);
        }
        echo "<tr class=\"tablerow altbg2\"><td align=\"center\">" . mxbMessageScreen(_TEXTNEWSLETTERSUBMIT) . " </td></tr>";
    }
}
echo "</table></td></tr></table>";
include_once(MXB_BASEMODINCLUDE . 'footer.php');

function mxbImageSetSelect($currentimageset = '')
{
    global $langfile, $imageset_default;
    $previewimage = 'newtopic.png';
    $dummy_key = '- ' . _TEXTDEFAULT . ' (' . $imageset_default . ')';
    if (empty($currentimageset)) {
        $currentimageset = $dummy_key ;
    }
    $lang = (preg_match('#^german#i', $langfile)) ? 'german' : $langfile;
    $lang_alternate = ($lang == 'german') ? 'english' :'german' ;
    $handle = opendir(MXB_ROOTMOD . 'imagesets/');
    while ($imageset = readdir($handle)) {
        if (preg_match('#\.|CVS#', $imageset)) {
            continue;
        } else if (file_exists(MXB_ROOTMOD . 'imagesets/' . $imageset . '/' . $lang . '/' . $previewimage)) {
            $filelist[$imageset] = MXB_ROOTMOD . 'imagesets/' . $imageset . '/' . $lang . '/' . $previewimage;
        } else if (file_exists(MXB_ROOTMOD . 'imagesets/' . $imageset . '/' . $lang_alternate . '/' . $previewimage)) {
            $filelist[$imageset] = MXB_ROOTMOD . 'imagesets/' . $imageset . '/' . $lang_alternate . '/' . $previewimage;
        }
    }
    $filelist[$dummy_key] = MXB_ROOTMOD . 'imagesets/' . $imageset_default . '/' . $lang . '/' . $previewimage;
    ksort($filelist);
    foreach ($filelist as $key => $file) {
        $selected = ($key == $currentimageset) ? ' selected="selected" class="current"' : '';
        $options[] = '<option value="' . $key . '" ' . $selected . '>' . $key . '</option>';
        $jsoptions[] = 'tis["' . $key . '"] = "' . $file . '";';
    }
    $options = implode("\n", $options);
    $jsoptions = implode("\n", $jsoptions);

    if (!empty($currentimageset) && $currentimageset != $dummy_key && isset($filelist[$currentimageset])) {
        if (file_exists($filelist[$currentimageset])) {
            $previewpic = $filelist[$currentimageset];
        } else {
            $previewpic = MXB_ROOTMOD . 'imagesets/' . $currentimageset . '/' . $lang_alternate . '/' . $previewimage;
        }
    } else {
        $previewpic = mxbGetImage($previewimage, _TEXTNEWTOPIC, true, true);
    }

    ?>
<script type="text/javascript">
/*<![CDATA[*/
var tis = new Array();
<?php echo $jsoptions ?>

function showpreviewbutton() {
    if (!document.images) {
        return;
    }
    document.imagesetpic.src=tis[document.themestuff.replyimgnew.options[document.themestuff.replyimgnew.selectedIndex].value];
}
/*]]>*/
</script>
<select name="replyimgnew" onchange="showpreviewbutton()">
<?php echo $options ?>
</select>&nbsp;&nbsp;<img src="<?php echo $previewpic ?>" name="imagesetpic" id="imagesetpic" alt="preview"/>


<?php
}

function mxb_theme_getvars($themename)
{
    global $table_themes;
    $query = sql_query("SELECT * FROM $table_themes WHERE name='" . substr($themename, 0, 30) . "'");
    $themevars = sql_fetch_assoc($query);
    $themevars = mxbThemeFallback($themevars);
    if (!empty($themevars['replyimg']) && file_exists(MXB_ROOTMOD . 'imagesets/' . $themevars['replyimg']) && !is_file(MXB_ROOTMOD . 'imagesets/' . $themevars['replyimg'])) {
        $themevars['imageset'] = $themevars['replyimg'];
    } else {
        $themevars['imageset'] = '';
    }
    $themevars = mxbThemeFallback($themevars);
    unset($themevars['replyimg']);
    return $themevars;
}

function mxb_theme_form($themevars)
{
    global $colornames, $borderwidth, $tablespace;

    foreach ($colornames as $color) {
        $themevars[$color] = trim(mxb_helper_colors::repair($themevars[$color]), '# ');
    }
    $themevars['header'] = $themevars['bgcolheader'];
    $themevars['headertext'] = $themevars['bgcolheadertext'];

    ?>

<table cellspacing="0" cellpadding="0" border="0" width="100%" align="center">
<tr><td class="bordercolor">

<table border="0" cellspacing="<?php echo $borderwidth ?>" cellpadding="<?php echo $tablespace ?>" width="100%">

<tr class="tablerow altbg2">
<td><?php echo _TEXTHEMENAME ?></td>
<td>
<?php
    if ($themevars['name'] == 'default') {
        echo '<strong>default</strong><input type="hidden" name="namenew" value="default"/>';
    } else {
        echo '<input type="text" name="namenew" value="' . $themevars['name'] . '" size="35" maxlength="30" />';
    }

    ?>
</td>
</tr>

<tr class="tablerow altbg2">
<td><?php echo _MXBIMAGESET ?></td>
<td class="nowrap"><?php echo mxbImageSetSelect($themevars['imageset']) ?> </td>
</tr>
<?php
    if ($themevars['name'] != 'default') {

        ?>
<tr class="tablerow altbg2">
<td><?php echo _TEXTBGCOLOR ?></td>
<td><span class="colpick-color-box" style="border-right-color:#<?php echo $themevars['bgcolor'] ?>">
<input type="text" name="bgcolornew" class="color-picker" value="<?php echo $themevars['bgcolor'] ?>" />
</span></td>
</tr>

<tr class="tablerow altbg2">
<td><?php echo _TEXTALTBG1 ?></td>
<td><span class="colpick-color-box" style="border-right-color:#<?php echo $themevars['altbg1'] ?>">
<input type="text" name="altbg1new" class="color-picker" value="<?php echo $themevars['altbg1'] ?>" />
</span></td>
</tr>

<tr class="tablerow altbg2">
<td><?php echo _TEXTALTBG2 ?></td>
<td><span class="colpick-color-box" style="border-right-color:#<?php echo $themevars['altbg2'] ?>">
<input type="text" name="altbg2new" class="color-picker" value="<?php echo $themevars['altbg2'] ?>" />
</span></td>
</tr>

<tr class="tablerow altbg2">
<td><?php echo _TEXTLINKXF ?></td>
<td><span class="colpick-color-box" style="border-right-color:#<?php echo $themevars['link'] ?>">
<input type="text" name="linknew" class="color-picker" value="<?php echo $themevars['link'] ?>" />
</span></td>
</tr>

<tr class="tablerow altbg2">
<td><?php echo _TEXTBORDER ?></td>
<td><span class="colpick-color-box" style="border-right-color:#<?php echo $themevars['bordercolor'] ?>">
<input type="text" name="bordercolornew" class="color-picker" value="<?php echo $themevars['bordercolor'] ?>" />
</span></td>
</tr>

<tr class="tablerow altbg2">
<td><?php echo _TEXTHEADER ?></td>
<td><span class="colpick-color-box" style="border-right-color:#<?php echo $themevars['header'] ?>">
<input type="text" name="headernew" class="color-picker" value="<?php echo $themevars['header'] ?>" />
</span></td>
</tr>

<tr class="tablerow altbg2">
<td><?php echo _TEXTHEADERTEXT ?></td>
<td><span class="colpick-color-box" style="border-right-color:#<?php echo $themevars['headertext'] ?>">
<input type="text" name="headertextnew" class="color-picker" value="<?php echo $themevars['headertext'] ?>" />
</span></td>
</tr>

<tr class="tablerow altbg2">
<td><?php echo _TEXTTOP ?></td>
<td><span class="colpick-color-box" style="border-right-color:#<?php echo $themevars['top'] ?>">
<input type="text" name="topnew" class="color-picker" value="<?php echo $themevars['top'] ?>" />
</span></td>
</tr>

<tr class="tablerow altbg2">
<td><?php echo _TEXTCATCOLOR ?></td>
<td><span class="colpick-color-box" style="border-right-color:#<?php echo $themevars['catcolor'] ?>">
<input type="text" name="catcolornew" class="color-picker" value="<?php echo $themevars['catcolor'] ?>" />
</span></td>
</tr>

<tr class="tablerow altbg2">
<td><?php echo _TEXTTABLETEXT ?></td>
<td><span class="colpick-color-box" style="border-right-color:#<?php echo $themevars['tabletext'] ?>">
<input type="text" name="tabletextnew" class="color-picker" value="<?php echo $themevars['tabletext'] ?>" />
</span></td>
</tr>

<tr class="tablerow altbg2">
<td><?php echo _TEXTTEXT ?></td>
<td><span class="colpick-color-box" style="border-right-color:#<?php echo $themevars['text'] ?>">
<input type="text" name="textnew" class="color-picker" value="<?php echo $themevars['text'] ?>" />
</span></td>
</tr>
<?php
    } // END if ($themevars['name'] != 'default')

    ?>

<tr class="tablerow altbg2">
<td><?php echo _TEXTCOLOR1 ?></td>
<td><span class="colpick-color-box" style="border-right-color:#<?php echo $themevars['color1'] ?>">
<input type="text" name="color1new" class="color-picker" value="<?php echo $themevars['color1'] ?>" />
</span></td>
</tr>

<tr class="tablerow altbg2">
<td><?php echo _TEXTCOLOR2 ?></td>
<td><span class="colpick-color-box" style="border-right-color:#<?php echo $themevars['color2'] ?>">
<input type="text" name="color2new" class="color-picker" value="<?php echo $themevars['color2'] ?>" />
</span></td>
</tr>

<tr class="tablerow altbg2">
<td><?php echo _TEXTBORDERWIDTH ?></td>
<td><input type="text" name="borderwidthnew" value="<?php echo $themevars['borderwidth'] ?>" size="2" /></td>
</tr>

<tr class="tablerow altbg2">
<td><?php echo _TEXTWIDTH ?></td>
<td><input type="text" name="tablewidthnew" value="<?php echo $themevars['tablewidth'] ?>" size="5" /></td>
</tr>

<tr class="tablerow altbg2">
<td><?php echo _TEXTSPACE ?></td>
<td><input type="text" name="tablespacenew" value="<?php echo $themevars['tablespace'] ?>" size="2" /></td>
</tr>

<tr class="tablerow altbg2">
<td><?php echo _TEXTFONT ?></td>
<td><input type="text" name="fnew" value="<?php echo $themevars['font'] ?>" /></td>
</tr>

<tr class="tablerow altbg2">
<td><?php echo _TEXTALTFONT ?></td>
<td><input type="text" name="altfnew" value="<?php echo $themevars['altfont'] ?>" /></td>
</tr>

<tr class="tablerow altbg2">
<td><?php echo _TEXTBIGSIZE ?></td>
<td><input type="text" name="fsizenew" value="<?php echo $themevars['fontsize'] ?>" size="4" /></td>
</tr>

<tr class="tablerow altbg2">
<td><?php echo _TEXTSMALLSIZE ?></td>
<td><input type="text" name="altfsizenew" value="<?php echo $themevars['altfontsize'] ?>" size="4" /></td>
</tr>

</table>
</td></tr></table>
<div class="align-center"><br/>
  <input type="submit" value="<?php echo _TEXTSUBMITCHANGES ?>" />
</div>

<script type="text/javascript">
/*<![CDATA[*/
  $(function() {
    $("span.colpick-color-box input").colpick( {
      colorScheme:'light',
      onSubmit: function(hsb, hex, rgb, el) {
        $(el).val(hex);
        $(el).parent().css('border-right-color', '#'+hex);
        $(el).colpickHide();
      },
      onBeforeShow: function () {
        $(this).colpickSetColor(this.value);
        return false;
      }
    });
    $("span.colpick-color-box input").parent().click(
      function () {
        $(this).children().click();
      });
  });
/*]]>*/
</script>

<?php
    pmxHeader::add_jquery('color/js/colpick.js');
    pmxHeader::add_style('includes/javascript/jquery/color/css/colpick.css');
}

/**
 * mxb_helper_colors
 *
 * @package
 * @author tora60
 * @copyright Copyright (c) 2009
 * @version $Id: cp2.php 6 2015-07-08 07:07:06Z PragmaMx $
 * @access public
 */
class mxb_helper_colors {
    public static function repair($color)
    {
        $color = self::getnamedcolor($color);
        $color = self::correctlen($color);
        return $color;
    }

    /**
     * mxb_helper_colors::correctlen()
     *
     * @param mixed $color
     * @return
     */
    public static function correctlen($color)
    {
        $color = trim($color, '#');
        if (strlen($color) < 6) {
            return '#' . str_repeat($color[0], 2) . str_repeat($color[1], 2) . str_repeat($color[2], 2);
        }
        return '#' . $color;
    }

    /**
     * mxb_helper_colors::getnamedcolor()
     *
     * @param mixed $color
     * @return
     */
    public static function getnamedcolor($color)
    {
        $colors = array(// Farbname => Hexwert
            'aliceblue' => 'F0F8FF',
            'antiquewhite' => 'FAEBD7',
            'aqua' => '00FFFF',
            'aquamarine' => '7FFFD4',
            'azure' => 'F0FFFF',
            'beige' => 'F5F5DC',
            'bisque' => 'FFE4C4',
            'black' => '000000',
            'blanchedalmond' => 'FFEBCD',
            'blue' => '0000FF',
            'blueviolet' => '8A2BE2',
            'brown' => 'A52A2A',
            'burlywood' => 'DEB887',
            'cadetblue' => '5F9EA0',
            'chartreuse' => '7FFF00',
            'chocolate' => 'D2691E',
            'coral' => 'FF7F50',
            'cornflowerblue' => '6495ED',
            'cornsilk' => 'FFF8DC',
            'crimson' => 'DC143C',
            'cyan' => '00FFFF',
            'darkblue' => '00008B',
            'darkcyan' => '008B8B',
            'darkgoldenrod' => 'B8860B',
            'darkgray' => 'A9A9A9',
            'darkgreen' => '006400',
            'darkkhaki' => 'BDB76B',
            'darkmagenta' => '8B008B',
            'darkolivegreen' => '556B2F',
            'darkorange' => 'FF8C00',
            'darkorchid' => '9932CC',
            'darkred' => '8B0000',
            'darksalmon' => 'E9967A',
            'darkseagreen' => '8FBC8B',
            'darkslateblue' => '483D8B',
            'darkslategray' => '2F4F4F',
            'darkturquoise' => '00CED1',
            'darkviolet' => '9400D3',
            'deeppink' => 'FF1493',
            'deepskyblue' => '00BFFF',
            'dimgray' => '696969',
            'dodgerblue' => '1E90FF',
            'firebrick' => 'B22222',
            'floralwhite' => 'FFFAF0',
            'forestgreen' => '228B22',
            'fuchsia' => 'FF00FF',
            'gainsboro' => 'DCDCDC',
            'ghostwhite' => 'F8F8FF',
            'gold' => 'FFD700',
            'goldenrod' => 'DAA520',
            'gray' => '808080',
            'green' => '008000',
            'greenyellow' => 'ADFF2F',
            'honeydew' => 'F0FFF0',
            'hotpink' => 'FF69B4',
            'indianred' => 'CD5C5C',
            'indigo' => '4B0082',
            'ivory' => 'FFFFF0',
            'khaki' => 'F0E68C',
            'lavender' => 'E6E6FA',
            'lavenderblush' => 'FFF0F5',
            'lawngreen' => '7CFC00',
            'lemonchiffon' => 'FFFACD',
            'lightblue' => 'ADD8E6',
            'lightcoral' => 'F08080',
            'lightcyan' => 'E0FFFF',
            'lightgoldenrodyellow' => 'FAFAD2',
            'lightgray' => 'D3D3D3',
            'lightgreen' => '90EE90',
            'lightpink' => 'FFB6C1',
            'lightsalmon' => 'FFA07A',
            'lightseagreen' => '20B2AA',
            'lightskyblue' => '87CEFA',
            'lightslategray' => '778899',
            'lightsteelblue' => 'B0C4DE',
            'lightyellow' => 'FFFFE0',
            'lime' => '00FF00',
            'limegreen' => '32CD32',
            'linen' => 'FAF0E6',
            'magenta' => 'FF00FF',
            'maroon' => '800000',
            'mediumaquamarine' => '66CDAA',
            'mediumblue' => '0000CD',
            'mediumorchid' => 'BA55D3',
            'mediumpurple' => '9370DB',
            'mediumseagreen' => '3CB371',
            'mediumslateblue' => '7B68EE',
            'mediumspringgreen' => '00FA9A',
            'mediumturquoise' => '48D1CC',
            'mediumvioletred' => 'C71585',
            'midnightblue' => '191970',
            'mintcream' => 'F5FFFA',
            'mistyrose' => 'FFE4E1',
            'moccasin' => 'FFE4B5',
            'navajowhite' => 'FFDEAD',
            'navy' => '000080',
            'oldlace' => 'FDF5E6',
            'olive' => '808000',
            'olivedrab' => '6B8E23',
            'orange' => 'FFA500',
            'orangered' => 'FF4500',
            'orchid' => 'DA70D6',
            'palegoldenrod' => 'EEE8AA',
            'palegreen' => '98FB98',
            'paleturquoise' => 'AFEEEE',
            'palevioletred' => 'DB7093',
            'papayawhip' => 'FFEFD5',
            'peachpuff' => 'FFDAB9',
            'peru' => 'CD853F',
            'pink' => 'FFC0CB',
            'plum' => 'DDA0DD',
            'powderblue' => 'B0E0E6',
            'purple' => '800080',
            'red' => 'FF0000',
            'rosybrown' => 'BC8F8F',
            'royalblue' => '4169E1',
            'saddlebrown' => '8B4513',
            'salmon' => 'FA8072',
            'sandybrown' => 'F4A460',
            'seagreen' => '2E8B57',
            'seashell' => 'FFF5EE',
            'sienna' => 'A0522D',
            'silver' => 'C0C0C0',
            'skyblue' => '87CEEB',
            'slateblue' => '6A5ACD',
            'slategray' => '708090',
            'snow' => 'FFFAFA',
            'springgreen' => '00FF7F',
            'steelblue' => '4682B4',
            'tan' => 'D2B48C',
            'teal' => '008080',
            'thistle' => 'D8BFD8',
            'tomato' => 'FF6347',
            'turquoise' => '40E0D0',
            'violet' => 'EE82EE',
            'wheat' => 'F5DEB3',
            'white' => 'FFFFFF',
            'whitesmoke' => 'F5F5F5',
            'yellow' => 'FFFF00',
            'yellowgreen' => '9ACD32',
            );
        if (isset($colors[strtolower($color)])) {
            return '#' . strtolower($colors[$color]);
        }
        return $color;
    }
}

?>