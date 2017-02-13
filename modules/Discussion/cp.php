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

if ($action == 'cp') {
    echo '<tr><td align="right" class="f11pix">mxBoard v.' . MXB_VERSION . '</td></tr>';
} else

if ($action == 'forum') {
    if (empty($forumsubmit) && empty($fdetails)) {
        // zu allererst eventuelle Fehler in Foren fixen
        // alle Foren auslesen und durchlaufen
        $result = sql_query("SELECT fid, fup, type, name FROM $table_forums");
        while ($row = sql_fetch_assoc($result)) {
            // wenn kein Name vorhanden, diesen erstellen
            if (empty($row['name'])) {
                $row['name'] = 'forum #' . $row['fid'];
                sql_query("UPDATE $table_forums SET name='" . mxAddSlashesForSQL($row['name']) . "', status='off' WHERE fid=" . intval($row['fid']));
            }
            // 2 versch. Arrays für Foren und Subforen
            if ($row['type'] == 'forum') {
                $arr_forum[$row['fid']] = $row;
            } else if ($row['type'] == 'sub') {
                $arr_sub[$row['fid']] = $row;
            }
            // nochmal ein Array mit allen Foren und Kategorien
            $boards[$row['fid']] = $row;
        }
        // Fehler in Subforen korrigieren
        if (isset($arr_sub)) {
            // alle Unterforen durchlaufen
            foreach ($arr_sub as $fid => $board) {
                // wenn Elternforum ungültig
                if (empty($board['fup']) || !isset($arr_forum[$board['fup']])) {
                    // break;
                    // als Forum ohne Kategorie eintragen
                    sql_query("UPDATE $table_forums SET type='forum', fup='0', status='off' WHERE fid=" . intval($fid));
                    // die Arrays mit den neuen Werten ergänzen
                    $arr_forum[$fid]['fid'] = $fid;
                    $arr_forum[$fid]['fup'] = 0;
                    $arr_forum[$fid]['type'] = 'forum';
                    $boards[$fid]['fid'] = $fid;
                    $boards[$fid]['fup'] = 0;
                    $boards[$fid]['type'] = 'forum';
                    // und das falsche Forum aus dem aktuellen Array entfernen
                    unset($arr_sub[$fid]);
                }
            }
        }
        // Fehler in Hauptforen korrigieren
        if (isset($arr_forum)) {
            // alle Hauptforen durchlaufen
            foreach ($arr_forum as $fid => $board) {
                // break;
                // wenn ein Elterforum (Kategorie) angegeben ist
                if (!empty($board['fup'])) {
                    $ferror = false;
                    // wenn Unterforum von sich selbst
                    if (($board['fup'] == $fid)) {
                        $ferror = true;
                    }
                    // wenn Elternforum (Kategorie) nicht existiert
                    if (!isset($boards[$board['fup']])) {
                        $ferror = true;
                    }
                    // oder Elternforum keine Kategorie ist
                    if ((isset($boards[$board['fup']]) && $boards[$board['fup']]['type'] != 'group')) {
                        $ferror = true;
                    }
                    if ($ferror) {
                        // Fehler in Hauptforen korrigieren
                        sql_query("UPDATE $table_forums SET fup='0', status='off' WHERE fid=" . intval($fid));
                    }
                }
            }
        }

        ?>

<tr class="altbg2">
<td align="center">
<br />

<form method="post" action="<?php echo MXB_BM_CP1 ?>action=forum">
<table cellspacing="0" cellpadding="0" border="0" width="100%" align="center">
<tr><td class="bordercolor">

<table border="0" cellspacing="<?php echo $borderwidth ?>" cellpadding="<?php echo $tablespace ?>" width="100%">

<tr>
<td class="mxb-header"><?php echo _TEXTFORUMOPTS ?></td>
</tr>

<?php
        $on = '';
        $off = '';
        $queryf = sql_query("SELECT * FROM $table_forums WHERE type='forum' AND (fup='' OR fup='0') ORDER BY displayorder");
        $i1 = 0;
        while ($forum = sql_fetch_object($queryf)) {
            $i1++;
            if ($forum->status == 'on') {
                $on = 'selected="selected" class="current"';
            } else {
                $off = 'selected="selected" class="current"';
            }
            // mxbGetForumSelect($forum->fup, $forum->fid);
            ?>
<!-- Foren ohne Kategorie -->
<tr class="tablerow altbg2">
<td class="f11pix">
<input type="text" name="name<?php echo $forum->fid ?>" value="<?php echo htmlspecialchars($forum->name) ?>" />
&nbsp;<?php echo _TEXTORDER ?>&nbsp;<input type="text" name="displayorder<?php echo $forum->fid ?>" size="2" value="<?php echo $i1 ?>" />
&nbsp;<select name="moveto<?php echo $forum->fid ?>"><?php echo mxbGetForumSelect($forum->fup, $forum->fid) ?></select>
&nbsp;<select name="status<?php echo $forum->fid ?>"><option value="on" <?php echo $on ?>><?php echo _TEXTON ?></option><option value="off" <?php echo $off ?>><?php echo _TEXTOFF ?></option></select>
&nbsp;<input type="checkbox" name="delete<?php echo $forum->fid ?>" value="<?php echo $forum->fid ?>" title="<?php echo _TEXTDELETEQUES ?>" />
<br/>
<a href="<?php echo MXB_BM_CP1 . "action=forum&amp;fdetails=" . $forum->fid ?>"><?php echo _TEXTMOREOPTS ?></a></td>
</tr>

<?php
            $toidlink = $forum->fid;

            $querys = sql_query("SELECT * FROM $table_forums WHERE type='sub' AND fup='" . intval($forum->fid) . "' ORDER BY displayorder");
            $i2 = 0;
            while ($forum = sql_fetch_object($querys)) {
                $i2++;
                if ($forum->status == 'on') {
                    $on = 'selected="selected" class="current"';
                } else {
                    $off = 'selected="selected" class="current"';
                }

                ?>
<!-- SUB - Foren ????? -->
<tr class="tablerow altbg2">
<td class="f11pix"><div style="margin: 0 0 0 2em;">
<input type="text" name="name<?php echo $forum->fid ?>" value="<?php echo htmlspecialchars($forum->name) ?>">
&nbsp;<?php echo _TEXTORDER ?>&nbsp;<input type="text" name="displayorder<?php echo $forum->fid ?>" size="2" value="<?php echo $i2 ?>">
&nbsp;<select name="moveto<?php echo $forum->fid ?>"><?php echo mxbGetForumSelect($forum->fup, $forum->fid) ?></select>
&nbsp;<select name="status<?php echo $forum->fid ?>"><option value="on" <?php echo $on ?>><?php echo _TEXTON ?></option><option value="off" <?php echo $off ?>><?php echo _TEXTOFF ?></option></select>
&nbsp;<input type="checkbox" name="delete<?php echo $forum->fid ?>" value="<?php echo $forum->fid ?>" title="<?php echo _TEXTDELETEQUES ?>" />
<br/>
<a href="<?php echo MXB_BM_CP1 . "action=forum&amp;fdetails=" . $forum->fid ?>"><?php echo _TEXTMOREOPTS ?></a></div></td>
</tr>

<?php
                //
                // Link-Display Sub-Foren
                if ($linkforumstatus == 'on') {
                    $queryslink = sql_query("SELECT * FROM $table_links WHERE type='forum' AND toid='" . intval($forum->fid) . "'");
                    while ($link = sql_fetch_object($queryslink)) {
                        $queryslinkfrom = sql_query("SELECT name FROM $table_forums WHERE fid='" . intval($link->fromid) . "'");
                        $linkname = sql_fetch_object($queryslinkfrom);

                        if ($link->status == 'on') {
                            $on = 'selected="selected" class="current"';
                        } else {
                            $off = 'selected="selected" class="current"';
                        }

                        ?>

<tr class="tablerow altbg1">
<td class="f11pix"><div style="margin: 0 0 0 4em;">
<i>&nbsp;- Link: </i><?php echo $linkname->name ?>&nbsp;(Forum <?php echo $link->fromid ?>)
&nbsp;<select name="linkstatus<?php echo $link->lid ?>">
<option value="on" <?php echo $on ?>><?php echo _TEXTON ?></option><option value="off" <?php echo $off ?>><?php echo _TEXTOFF ?></option></select>
<input type="checkbox" name="linkdelete<?php echo $link->lid ?>" value="<?php echo $link->lid ?>" title="<?php echo _TEXTDELETEQUES ?>" />
</div></td></tr>

<?php
                        $on = '';
                        $off = '';
                    }
                }
                // Ende Link-Display Subforen
                //
                $on = '';
                $off = '';
            }
            //
            // Link-Display Foren ohne Gruppe
            if ($linkforumstatus == 'on') {
                $queryslink = sql_query("SELECT * FROM $table_links WHERE type='forum' AND toid='" . intval($toidlink) . "'");
                while ($link = sql_fetch_object($queryslink)) {
                    $queryslinkfrom = sql_query("SELECT name FROM $table_forums WHERE fid='" . intval($link->fromid) . "'");
                    $linkname = sql_fetch_object($queryslinkfrom);

                    if ($link->status == 'on') {
                        $on = 'selected="selected" class="current"';
                    } else {
                        $off = 'selected="selected" class="current"';
                    }

                    ?>

<tr class="tablerow altbg1">
<td class="f11pix"><div style="margin: 0 0 0 2em;">
<i>&nbsp;- Link: </i><?php echo $linkname->name ?>&nbsp;(Forum <?php echo $link->fromid ?>)
&nbsp;<select name="linkstatus<?php echo $link->lid ?>">
<option value="on" <?php echo $on ?>><?php echo _TEXTON ?></option><option value="off" <?php echo $off ?>><?php echo _TEXTOFF ?></option></select>
<input type="checkbox" name="linkdelete<?php echo $link->lid ?>" value="<?php echo $link->lid ?>" title="<?php echo _TEXTDELETEQUES ?>" />
</div></td></tr>

<?php
                    $on = '';
                    $off = '';
                }
            }
            // Ende Link-Display Foren ohne Gruppe
            //
            $on = '';
            $off = '';
        }
        // hier Darstellung von  Gruppen!
        $queryg = sql_query("SELECT * FROM $table_forums WHERE type='group' ORDER BY displayorder");
        $i3 = 0;
        while ($group = sql_fetch_object($queryg)) {
            $i3++;
            if ($group->status == 'on') {
                $on = 'selected="selected" class="current"';
            } else {
                $off = 'selected="selected" class="current"';
            }

            ?>
<!-- Gruppen -->
<tr class="tablerow altbg1">
<td class="f11pix">
<input type="text" name="name<?php echo $group->fid ?>" value="<?php echo htmlspecialchars($group->name) ?>" />
&nbsp;<?php echo _TEXTORDER ?>&nbsp;<input type="text" name="displayorder<?php echo $group->fid ?>" size="2" value="<?php echo $i3 ?>" />
&nbsp;<select name="status<?php echo $group->fid ?>"><option value="on" <?php echo $on ?>><?php echo _TEXTON ?></option><option value="off" <?php echo $off ?>><?php echo _TEXTOFF ?></option></select>
&nbsp;<input type="checkbox" name="delete<?php echo $group->fid ?>" value="<?php echo $group->fid ?>" title="<?php echo _TEXTDELETEQUES ?>" />
</td>
</tr>

<?php
            $queryf = sql_query("SELECT * FROM $table_forums WHERE type='forum' AND fup='" . intval($group->fid) . "' ORDER BY displayorder");
            $i4 = 0;
            while ($forum = sql_fetch_object($queryf)) {
                $i4++;
                if ($forum->status == 'on') {
                    $on = 'selected="selected" class="current"';
                } else {
                    $off = 'selected="selected" class="current"';
                }

                ?>
<!-- Foren in Kategorien-->
<tr class="tablerow altbg2">
<td class="f11pix"><div style="margin: 0 0 0 2em;">
<input type="text" name="name<?php echo $forum->fid ?>" value="<?php echo htmlspecialchars($forum->name) ?>" />
&nbsp;<?php echo _TEXTORDER ?>&nbsp;<input type="text" name="displayorder<?php echo $forum->fid ?>" size="2" value="<?php echo $i4 ?>" />
&nbsp;<select name="moveto<?php echo $forum->fid ?>"><?php echo mxbGetForumSelect($forum->fup, $forum->fid) ?></select>
&nbsp;<select name="status<?php echo $forum->fid ?>"><option value="on" <?php echo $on ?>><?php echo _TEXTON ?></option><option value="off" <?php echo $off ?>><?php echo _TEXTOFF ?></option></select>
&nbsp;<input type="checkbox" name="delete<?php echo $forum->fid ?>" value="<?php echo $forum->fid ?>" title="<?php echo _TEXTDELETEQUES ?>" />
<br/>
<a href="<?php echo MXB_BM_CP1 . "action=forum&amp;fdetails=" . $forum->fid ?>"><?php echo _TEXTMOREOPTS ?></a></div></td>
</tr>

<?php
                $toidlink = $forum->fid;

                $querys = sql_query("SELECT * FROM $table_forums WHERE type='sub' AND fup='" . intval($forum->fid) . "' ORDER BY displayorder");
                $i5 = 0;
                while ($forum = sql_fetch_object($querys)) {
                    $i5++;
                    if ($forum->status == 'on') {
                        $on = 'selected="selected" class="current"';
                    } else {
                        $off = 'selected="selected" class="current"';
                    }

                    ?>
<!-- SUB - Foren -->
<tr class="tablerow altbg2">
<td class="f11pix"><div style="margin: 0 0 0 4em;">
<input type="text" name="name<?php echo $forum->fid ?>" value="<?php echo htmlspecialchars($forum->name) ?>">
&nbsp;<?php echo _TEXTORDER ?>&nbsp;<input type="text" name="displayorder<?php echo $forum->fid ?>" size="2" value="<?php echo $i5 ?>">
&nbsp;<select name="moveto<?php echo $forum->fid ?>"><?php echo mxbGetForumSelect($forum->fup, $forum->fid) ?></select>
&nbsp;<select name="status<?php echo $forum->fid ?>"><option value="on" <?php echo $on ?>><?php echo _TEXTON ?></option><option value="off" <?php echo $off ?>><?php echo _TEXTOFF ?></option></select>
&nbsp;<input type="checkbox" name="delete<?php echo $forum->fid ?>" value="<?php echo $forum->fid ?>" title="<?php echo _TEXTDELETEQUES ?>" />
<br/>
<a href="<?php echo MXB_BM_CP1 . "action=forum&amp;fdetails=" . $forum->fid;

                    ?>"><?php echo _TEXTMOREOPTS ?></a></div></td>
</tr>

<?php
                    //
                    // Link-Display Subforen mit Gruppe
                    if ($linkforumstatus == 'on') {
                        $queryslink = sql_query("SELECT * FROM $table_links WHERE type='forum' AND toid='" . intval($forum->fid) . "'");
                        while ($link = sql_fetch_object($queryslink)) {
                            $queryslinkfrom = sql_query("SELECT name FROM $table_forums WHERE fid='" . intval($link->fromid) . "'");
                            $linkname = sql_fetch_object($queryslinkfrom);

                            if ($link->status == 'on') {
                                $on = 'selected="selected" class="current"';
                            } else {
                                $off = 'selected="selected" class="current"';
                            }

                            ?>

<tr class="tablerow altbg1">
<td class="f11pix"><div style="margin: 0 0 0 6em;">
<i>&nbsp;- Link: </i><?php echo $linkname->name ?>&nbsp;(Forum <?php echo $link->fromid ?>)
&nbsp;<select name="linkstatus<?php echo $link->lid ?>">
<option value="on" <?php echo $on ?>><?php echo _TEXTON ?></option><option value="off" <?php echo $off ?>><?php echo _TEXTOFF ?></option></select>
&nbsp;<input type="checkbox" name="linkdelete<?php echo $link->lid ?>" value="<?php echo $link->lid ?>" title="<?php echo _TEXTDELETEQUES ?>" />
</div></td></tr>

<?php
                            $on = '';
                            $off = '';
                        }
                    }
                    // Ende Link-Display Subforen ohne Gruppe
                    //
                    $on = '';
                    $off = '';
                }
                //
                // Link-Display Foren mit Gruppe
                if ($linkforumstatus == 'on') {
                    $queryslink = sql_query("SELECT * FROM $table_links WHERE type='forum' AND toid='" . intval($toidlink) . "'");
                    while ($link = sql_fetch_object($queryslink)) {
                        $queryslinkfrom = sql_query("SELECT name FROM $table_forums WHERE fid='" . intval($link->fromid) . "'");
                        $linkname = sql_fetch_object($queryslinkfrom);
                        $linkname->name = (empty($linkname->name)) ? '' : $linkname->name;

                        if ($link->status == 'on') {
                            $on = 'selected="selected" class="current"';
                        } else {
                            $off = 'selected="selected" class="current"';
                        }

                        ?>

<tr class="tablerow altbg1">
<td class="f11pix"><div style="margin: 0 0 0 4em;">
<i>&nbsp;- Link: </i><?php echo $linkname->name ?>&nbsp;(Forum <?php echo $link->fromid ?>)
&nbsp;<select name="linkstatus<?php echo $link->lid ?>">
<option value="on" <?php echo $on ?>><?php echo _TEXTON ?></option><option value="off" <?php echo $off ?>><?php echo _TEXTOFF ?></option></select>
&nbsp;<input type="checkbox" name="linkdelete<?php echo $link->lid ?>" value="<?php echo $link->lid ?>" title="<?php echo _TEXTDELETEQUES ?>" />
</div></td></tr>

<?php
                        $on = '';
                        $off = '';
                    }
                }
                // Ende Link-Display foren mit Gruppe
                //
                $on = '';
                $off = '';
            }

            $on = '';
            $off = '';
        }

        ?>

<tr class="tablerow altbg2">
<td class="f11pix"><hr/></td>
</tr>

<tr class="tablerow altbg1">
<td class="f11pix"><input type="text" name="newgname" value="<?php echo htmlspecialchars(_TEXTNEWGROUP) ?>" />
&nbsp;<?php echo _TEXTORDER ?> &nbsp;<input type="text" value="1" name="newgorder" size="2" />
&nbsp;<select name="newgstatus">
<option value="on"><?php echo _TEXTON ?></option><option value="off"><?php echo _TEXTOFF ?></option></select></td>
</tr>

<tr class="tablerow altbg1">
<td class="f11pix"><div style="margin: 0 0 0 2em;"><input type="text" name="newfname" value="<?php echo htmlspecialchars(_TEXTNEWFORUM1) ?>" />
&nbsp;<?php echo _TEXTORDER ?>&nbsp;<input type="text" value="1" name="newforder" size="2" />
&nbsp;<select name="newffup"><?php echo mxbGetForumSelect(0, 0) ?></select>
&nbsp;<select name="newfstatus"><option value="on"><?php echo _TEXTON ?></option><option value="off"><?php echo _TEXTOFF ?></option></select>
</div>
</td></tr>
<?php
        // Forum-Link Erweiterung
        if ($linkforumstatus == 'on') {

            ?>
<tr class="tablerow altbg1">
<td>
<?php echo _TEXTNEWLINK ?><br/>
<br/>
<?php echo _TEXTNEWLINKFROM ?>&nbsp;<br/><select name="fromfid"><?php echo mxbLargeSelect() ?></select><br/><br/>
<?php echo _TEXTNEWLINKTO ?>&nbsp;<br/><select name="tofid"><?php echo mxbLargeSelect() ?></select>
&nbsp;&nbsp;<?php echo _TEXTSTATUS ?>&nbsp;<select name="newlinkstatus"><option value="on"><?php echo _TEXTON ?></option><option value="off"><?php echo _TEXTOFF ?></option></select>
<br/><br/>
<?php echo _TEXTNEWLINKNOTE ?>

</td></tr>
<?php
        }
        // Ende Forumlink-Erweiterung
        ?>
<tr class="tablerow altbg2">
 <td><center><br/><input type="submit" name="forumsubmit" value="<?php echo _TEXTSUBMITCHANGES ?>" /></center></td>
</tr>

</table>


</td></tr></table>
<br/>
</form>

</td>
</tr>

<?php
    }

    if (!empty($fdetails) && empty($forumsubmit)) {

        ?>

<tr class="altbg2">
<td align="center">
<br />

<form method="post" action="<?php echo MXB_BM_CP1 ?>action=forum&amp;fdetails=<?php echo $fdetails ?>">
<table cellspacing="0" cellpadding="0" border="0" width="100%" align="center">
<tr><td class="bordercolor">

<table border="0" cellspacing="<?php echo $borderwidth ?>" cellpadding="<?php echo $tablespace ?>" width="100%">

<tr>
<td class="mxb-header" colspan="2"><?php echo _TEXTFORUMOPTS ?></td>
</tr>

<?php
        // Build of select statement
        $queryg = sql_query("SELECT * FROM $table_forums WHERE fid='" . intval($fdetails) . "'");
        $forum = sql_fetch_object($queryg);

        $XFthemelist = '<select name="XFthemeforumnew">';
        $querytheme = sql_query("SELECT name FROM $table_themes ORDER BY name");

        if ($forum->theme == "") {
            $XFthemelist .= '<option value="" selected="selected"></option>';
        } else {
            $XFthemelist .= '<option value="" selected=""></option>';
        }
        // Schleife durch Suchergebnisse
        while ($XFtheme = sql_fetch_object($querytheme)) {
            if ($XFtheme->name == $forum->theme) {
                $XFthemelist .= '<option value="' . $XFtheme->name . '" selected="selected">' . $XFtheme->name . '</option>';
            } else {
                $XFthemelist .= '<option value="' . $XFtheme->name . '">' . $XFtheme->name . '</option>';
            }
        }
        $XFthemelist .= "</select>";
        // end of building select
        $checked1a = '';
        $checked1b = '';
        $checked1c = '';
        if ($forum->private == "staff") {
            $checked1a = ' checked="checked"';
        } elseif ($forum->private == "user") {
            $checked1b = ' checked="checked"';
        } else {
            $checked1c = ' checked="checked"';
        }

        if ($forum->allowhtml == 'yes') {
            $checked2 = ' checked="checked"';
        } else {
            $checked2 = '';
        }

        if ($forum->allowsmilies == 'yes') {
            $checked3 = ' checked="checked"';
        } else {
            $checked3 = '';
        }

        if ($forum->allowbbcode == 'yes') {
            $checked4 = ' checked="checked"';
        } else {
            $checked4 = '';
        }

        if ($forum->guestposting == 'yes') {
            $checked5 = ' checked="checked"';
        } else {
            $checked5 = '';
        }

        if ($forum->allowimgcode == 'yes') {
            $checked6 = ' checked="checked"';
        } else {
            $checked6 = '';
        }

        $pperm = explode('|', $forum->postperm);

        $type11 = '';
        $type12 = '';
        $type13 = '';
        $type14 = '';
        $type21 = '';
        $type22 = '';
        $type23 = '';
        $type24 = '';
        if ($pperm[0] == "2") {
            $type12 = 'selected="selected" class="current"';
        } elseif ($pperm[0] == "3") {
            $type13 = 'selected="selected" class="current"';
        } elseif ($pperm[0] == "4") {
            $type14 = 'selected="selected" class="current"';
        } elseif ($pperm[0] == "1") {
            $type11 = 'selected="selected" class="current"';
        }

        if ($pperm[1] == "2") {
            $type22 = 'selected="selected" class="current"';
        } elseif ($pperm[1] == "3") {
            $type23 = 'selected="selected" class="current"';
        } elseif ($pperm[0] == "4") {
            $type24 = 'selected="selected" class="current"';
        } elseif ($pperm[1] == "1") {
            $type21 = 'selected="selected" class="current"';
        }

        $forum->private = str_replace("pw|", "", $forum->private) ?>

<tr class="altbg2">
<td class="tablerow"><?php echo _TEXTFORUMNAME ?></td>
<td><input type="text" name="namenew" value="<?php echo htmlspecialchars($forum->name) ?>" /></td>
</tr>

<tr class="altbg2">
<td class="tablerow"><?php echo _TEXTDESC ?></td>
<td><textarea rows="7" cols="<?php echo $textareacols ?>" name="descnew"><?php echo $forum->description ?></textarea></td>
</tr>

<tr class="altbg2">
<td class="tablerow"><?php echo _TEXTALLOW ?></td>
<td class="f11pix">
<input type="checkbox" name="allowhtmlnew" value="yes" <?php echo $checked2 ?> /><?php echo _TEXTHTML ?><br />
<input type="checkbox" name="allowsmiliesnew" value="yes" <?php echo $checked3 ?> /><?php echo _TEXTSMILIES ?><br />
<input type="checkbox" name="allowbbcodenew" value="yes" <?php echo $checked4 ?> /><?php echo _TEXTBBCODE ?><br />
<input type="checkbox" name="allowimgcodenew" value="yes" <?php echo $checked6 ?> /><?php echo _TEXTIMGCODE ?><br /><br />
<input type="checkbox" name="guestpostingnew" value="yes" <?php echo $checked5 ?> /><?php echo _TEXTGUESTPOSTING ?>
</td>
</tr>

<tr class="altbg2">
<td class="tablerow"><?php echo _WHOPOSTOP1 ?></td>
<td><select name="postperm1">
<option value="1" <?php echo $type11 ?>><?php echo _TEXTPOSTPERMISSION1 ?></option>
<option value="2" <?php echo $type12 ?>><?php echo _TEXTPOSTPERMISSION2 ?></option>
<option value="3" <?php echo $type13 ?>><?php echo _TEXTPOSTPERMISSION3 ?></option>
<option value="4" <?php echo $type14 ?>><?php echo _TEXTPOSTPERMISSION4 ?></option>
</select>
</td>
</tr>

<tr class="altbg2">
<td class="tablerow"><?php echo _WHOPOSTOP2 ?></td>
<td><select name="postperm2">
<option value="1" <?php echo $type21 ?>><?php echo _TEXTPOSTPERMISSION1 ?></option>
<option value="2" <?php echo $type22 ?>><?php echo _TEXTPOSTPERMISSION2 ?></option>
<option value="3" <?php echo $type23 ?>><?php echo _TEXTPOSTPERMISSION3 ?></option>
<option value="4" <?php echo $type24 ?>><?php echo _TEXTPOSTPERMISSION4 ?></option>
</select>
</td>
</tr>

<tr class="altbg2">
<td class="tablerow"><?php echo _TEXTMODS ?></td>
<td><textarea rows="4" cols="<?php echo $textareacols ?>" name="moderatornew"><?php echo $forum->moderator ?></textarea><br/><span class="tiny"><?php echo _MULTMODNOTE ?></span></td>
</tr>

<tr class="altbg2">
<td class="tablerow"><?php echo _TEXTUSERLIST ?></td>
<td><textarea rows="4" cols="<?php echo $textareacols ?>" name="userlistnew"><?php echo $forum->userlist ?></textarea></td>
</tr>

<tr class="altbg2">
<td class="tablerow"><?php echo _TEXTSTAFFONLY ?></td>
<td>
<input type="radio" name="privatenew" value="" <?php echo $checked1c ?> /> <?php echo _EBF_ALL ?><br/>
<input type="radio" name="privatenew" value="user" <?php echo $checked1b ?> /> <?php echo _EBF_MEMBERONLY ?><br/>
<input type="radio" name="privatenew" value="staff" <?php echo $checked1a ?> /> <?php echo _EBF_STAFFONLY ?>
</td>
</tr>

<tr class="altbg2">
<td class="tablerow"><?php echo _TEXTTHEME ?></td>
<td><?php echo $XFthemelist ?></td>
</tr>

<tr class="altbg2">
<td class="tablerow"><?php echo _TEXTDELETEQUES ?></td>
<td><input type="checkbox" name="delete" value="<?php echo $forum->fid ?>" /></td>
</tr>

</table>
</td></tr></table>
<center><br/><input type="submit" name="forumsubmit" value="<?php echo _TEXTSUBMITCHANGES ?>" /></center>
</form>

</td>
</tr>
<?php
    }
    // -----------------------------------------------------------------------------
    // Forum submission
    if (!empty($forumsubmit)) {
        $result = sql_query("SELECT fid, type FROM $table_forums");
        $allboards[0] = 'group';
        while ($board = sql_fetch_assoc($result)) {
            $allboards[$board['fid']] = $board['type'];
        }

        if (empty($fdetails)) { // interface des forums généraux
            $queryforum = sql_query("SELECT fid, type, fup, name, displayorder, status FROM $table_forums WHERE type='forum' OR type='sub'");
            while ($forum = sql_fetch_object($queryforum)) {
                $delete = (isset($_REQUEST['delete' . $forum->fid])) ? intval($_REQUEST['delete' . $forum->fid]) : 0;
                if (empty($delete)) {
                    $displayorder = "displayorder" . $forum->fid;
                    $displayorder = @"${$displayorder}";
                    $name = "name" . $forum->fid;
                    $name = @"${$name}";
                    $statusF = "status" . $forum->fid;
                    $statusF = @"${$statusF}";
                    $moveto = "moveto" . $forum->fid;
                    $moveto = @"${$moveto}";

                    $upd = array();
                    if (trim($name) && $forum->name != mxStripSlashes($name)) {
                        $upd[] = "name='" . mxAddSlashesForSQL($name) . "'";
                    }
                    if (intval($forum->displayorder) != intval($displayorder)) {
                        $upd[] = "displayorder='" . intval($displayorder) . "'";
                    }
                    if ($statusF && $forum->status != $statusF) {
                        $upd[] = "status='" . mxAddSlashesForSQL($statusF) . "'";
                    }
                    if (intval($forum->fup) != intval($moveto)) {
                        $upd[] = "fup='" . intval($moveto) . "'";
                    }

                    if (isset($allboards[$moveto]) && $allboards[$moveto] == 'group' && $forum->type != 'forum') {
                        $upd[] = "type='forum'";
                    } else
                    if (isset($allboards[$moveto]) && $allboards[$moveto] == 'forum' && $forum->type != 'sub') {
                        $upd[] = "type='sub'";
                    }

                    if (!empty($upd)) {
                        $qry = "UPDATE $table_forums SET " . implode(', ', $upd) . " WHERE fid='" . intval($forum->fid) . "'";
                        sql_query($qry);
                    }
                } else {
                    $threadnumber = 0;
                    $postnumber = 0;

                    $querythread = sql_query("SELECT * FROM $table_threads WHERE fid='" . intval($delete) . "'");
                    while ($thread = sql_fetch_object($querythread)) {
                        sql_query("DELETE FROM $table_threads WHERE tid='" . $thread->tid . "'");
                        $querythreadlinks = sql_query("SELECT lid FROM $table_links WHERE type ='thread' AND fromid=" . intval($thread->tid));
                        while ($threadlink = sql_fetch_object($querythreadlinks)) {
                            sql_query("DELETE FROM $table_links WHERE lid='" . $threadlink->lid . "'");
                        }
                        // array zum anpassen der Userpostings
                        $check_authors[$thread->author] = $thread->author;
                        $threadnumber++;
                        $postnumber++;

                        $querypost = sql_query("SELECT * FROM $table_posts WHERE tid=" . intval($thread->tid));
                        while ($post = sql_fetch_object($querypost)) {
                            sql_query("DELETE FROM $table_posts WHERE pid='" . intval($post->pid) . "'");
                            // array zum anpassen der Userpostings
                            $check_authors[$post->author] = $post->author;
                            $postnumber++;
                        } // While
                    } // While
                    $queryforumtype = sql_query("SELECT type, fup, lastpost FROM $table_forums WHERE fid='" . intval($delete) . "'");
                    $forumtype = sql_fetch_object($queryforumtype);

                    $fupid = $forumtype->fup;
                    $postingtime = $forumtype->lastpost;
                    $forumtypedelete = $forumtype->type;

                    if ($forumtype->type == 'sub') {
                        sql_query("UPDATE $table_forums SET threads=threads-$threadnumber, posts=posts-$postnumber WHERE fid='" . intval($forumtype->fup) . "'");
                    }

                    $queryforumlinks = sql_query("SELECT lid FROM $table_links WHERE type ='forum' AND (fromid='" . intval($delete) . "' OR toid='" . intval($delete) . "')");
                    while ($forumlink = sql_fetch_object($queryforumlinks)) {
                        sql_query("DELETE FROM $table_links WHERE lid='" . $forumlink->lid . "'");
                    }

                    $querythreadlinks = sql_query("SELECT lid FROM $table_links WHERE type ='thread' AND toid='" . intval($delete) . "'");
                    while ($threadlink = sql_fetch_object($querythreadlinks)) {
                        sql_query("DELETE FROM $table_links WHERE lid='" . $threadlink->lid . "'");
                    }

                    sql_query("DELETE FROM $table_forums WHERE (type='forum' OR type='sub') AND fid='" . intval($delete) . "'");

                    if ($forumtypedelete == 'sub') {
                        mxbLastPostForum($fupid, $postingtime);
                    }
                }
            } // While
            //
            // Link-Update
            if ($linkforumstatus == 'on') {
                $querylink = sql_query("SELECT * FROM $table_links WHERE type='forum'");
                while ($link = sql_fetch_object($querylink)) {
                    $statusL = "linkstatus" . $link->lid;
                    if (isset($$statusL)) {
                        $statusL = $$statusL;
                        sql_query("UPDATE $table_links SET status='$statusL' WHERE lid='" . intval($link->lid) . "'");
                    }
                    $deleteL = "linkdelete" . $link->lid;
                    if (isset($$deleteL)) {
                        $deleteL = $$deleteL;
                        if ($deleteL) {
                            $linklastpost = $link->lastpost;
                            $forumtoid = $link->toid;
                            sql_query("DELETE FROM $table_links WHERE lid='" . intval($deleteL) . "'");
                            mxbLastPostForum($forumtoid, $linklastpost);
                        }
                    }
                } // While
            }
            // Link-Update Ende
            //
            $querygroup = sql_query("SELECT fid FROM $table_forums WHERE type='group'");
            while ($group = sql_fetch_object($querygroup)) {
                $name = "name" . $group->fid;
                $name = "${$name}";
                $displayorder = "displayorder" . $group->fid;
                $displayorder = "${$displayorder}";
                $statusF = "status" . $group->fid;
                $statusF = "${$statusF}";
                $delete = (isset($_REQUEST['delete' . $group->fid])) ? intval($_REQUEST['delete' . $group->fid]) : 0;
                if ($delete) {
                    sql_query("UPDATE $table_forums SET fup='0' WHERE type='forum' AND fup='" . intval($delete) . "'");
                    sql_query("DELETE FROM $table_forums WHERE type='group' AND fid='" . intval($delete) . "'");
                } else {
                    sql_query("UPDATE $table_forums SET name='$name', displayorder='$displayorder', status='$statusF' WHERE fid='" . intval($group->fid) . "'");
                }
            }

            if ($eb_defstaff == "staff") {
                $privatestatus = "staff";
            } elseif ($eb_defstaff == "user") {
                $privatestatus = "user";
            } else {
                $privatestatus = '';
            }

            if ($newgname != _TEXTNEWGROUP && trim($newgname)) {
                sql_query("INSERT INTO $table_forums VALUES ('group', '', '$newgname', '$newgstatus', '', '', '$newgorder', '', '', '', '', '', '', '', '', '0', '0', '', '1|1', '', '')");
            }

            if ($newfname != _TEXTNEWFORUM1 && trim($newfname)) {
                $qry = '';
                if ($allboards[$newffup] == 'group') {
                    $qry = "INSERT INTO $table_forums VALUES ('forum', '', '$newfname', '$newfstatus', '', '" . $eb_defmods . "', '$newforder', '" . $privatestatus . "', '', '" . $eb_defhtml . "', '" . $eb_defsmilies . "', '" . $eb_defbbcode . "', '" . $eb_defanoposts . "', '', '', '0', '0', '" . intval($newffup) . "', '1|1', '" . $eb_defimgcode . "', '')";
                } else if ($allboards[$newffup] == 'forum') {
                    if ($eb_defmain2sub == 'yes') {
                        $querymainauth = sql_query("SELECT moderator, private, allowhtml, allowsmilies, allowbbcode, guestposting, userlist, theme, postperm, allowimgcode FROM $table_forums WHERE fid='" . intval($newffup) . "'");
                        $copy = sql_fetch_object($querymainauth);
                        $qry = "INSERT INTO $table_forums VALUES ('sub', '', '$newfname', '$newfstatus', '', '" . $copy->moderator . "', '$newforder', '" . $copy->private . "', '', '" . $copy->allowhtml . "', '" . $copy->allowsmilies . "', '" . $copy->allowbbcode . "', '" . $copy->guestposting . "', '" . $copy->userlist . "', '" . $copy->theme . "', '0', '0', '" . intval($newffup) . "', '" . $copy->postperm . "', '" . $copy->allowimgcode . "', '')";
                    } else {
                        $qry = "INSERT INTO $table_forums VALUES ('sub', '', '$newfname', '$newfstatus', '', '" . $eb_defmods . "', '$newforder', '" . $privatestatus . "', '', '" . $eb_defhtml . "', '" . $eb_defsmilies . "', '" . $eb_defbbcode . "', '" . $eb_defanoposts . "', '', '', '0', '0', '" . intval($newffup) . "', '1|1', '" . $eb_defimgcode . "', '')";
                    }
                }
                if ($qry) {
                    sql_query($qry);
                }
            }
            // Forum-Links erstellen
            if ($linkforumstatus == 'on') {
                // hier werden Links für Foren erstellt
                if ($fromfid != 0 && $tofid != 0) {
                    $samelink = sql_query("SELECT lid FROM $table_links WHERE type='forum' AND fromid='" . intval($fromfid) . "' AND toid='" . intval($tofid) . "'");

                    if (sql_num_rows($samelink) == 0) {
                        $queryforum = sql_query("SELECT lastpost FROM $table_forums WHERE fid='" . intval($fromfid) . "'");
                        if ($forumlink = sql_fetch_object($queryforum)) {
                            sql_query("INSERT INTO $table_links VALUES ('', 'forum', '$fromfid', '$tofid', '$newlinkstatus', '" . $forumlink->lastpost . "')");
                            mxbLastPostForum($tofid, "checkforum");
                        }
                    }
                }
            }
            /* Ende Link-Erstellen */

            /* reset cached forums-Jumper */
            mxbBuildQuickJump(true);

            echo "<tr class=\"tablerow altbg2\"><td align=\"center\">" . mxbMessageScreen(_TEXTFORUMUPDATE) . "</td></tr>";
            // Detaileigenschaften
        } else { // Soumission des forums (plus d'options)
            if (empty($delete)) {
                $allowhtmlnew = (isset($allowhtmlnew) && $allowhtmlnew == 'yes') ? 'yes' : '';
                $allowsmiliesnew = (isset($allowsmiliesnew) && $allowsmiliesnew == 'yes') ? 'yes' : '';
                $allowbbcodenew = (isset($allowbbcodenew) && $allowbbcodenew == 'yes') ? 'yes' : '';
                $guestpostingnew = (isset($guestpostingnew) && $guestpostingnew == 'yes') ? 'yes' : '';
                $allowimgcodenew = (isset($allowimgcodenew) && $allowimgcodenew == 'yes') ? 'yes' : '';

                $temp = preg_split('#\s*,\s*#', trim($moderatornew, ', '));
                sort($temp);
                $moderatornew = implode(', ', array_unique($temp));

                $temp = preg_split('#\s*,\s*#', trim($userlistnew, ', '));
                sort($temp);
                $userlistnew = implode(', ', array_unique($temp));

                sql_query("UPDATE $table_forums SET name='$namenew', description='$descnew', allowhtml='$allowhtmlnew', allowsmilies='$allowsmiliesnew', allowbbcode='$allowbbcodenew', guestposting='$guestpostingnew', theme='$XFthemeforumnew', moderator='$moderatornew', userlist='$userlistnew', private='$privatenew', postperm='$postperm1|$postperm2', allowimgcode='$allowimgcodenew' WHERE fid='" . intval($fdetails) . "'");
            } else {
                $threadnumber = 0;
                $postnumber = 0;

                $querythread = sql_query("SELECT * FROM $table_threads WHERE fid='" . intval($delete) . "'");
                while ($thread = sql_fetch_object($querythread)) {
                    sql_query("DELETE FROM $table_threads WHERE tid='" . $thread->tid . "'");
                    $querythreadlinks = sql_query("SELECT lid FROM $table_links WHERE type ='thread' AND fromid=" . intval($thread->tid));
                    while ($threadlink = sql_fetch_object($querythreadlinks)) {
                        sql_query("DELETE FROM $table_links WHERE lid='" . $threadlink->lid . "'");
                    }
                    // array zum anpassen der Userpostings
                    $check_authors[$thread->author] = $thread->author;
                    $threadnumber++;
                    $postnumber++;

                    $querypost = sql_query("SELECT * FROM $table_posts WHERE tid=" . intval($thread->tid));
                    while ($post = sql_fetch_object($querypost)) {
                        sql_query("DELETE FROM $table_posts WHERE pid='" . intval($post->pid) . "'");
                        // array zum anpassen der Userpostings
                        $check_authors[$post->author] = $post->author;
                        $postnumber++;
                    } // While
                } // While
                $queryforumtype = sql_query("SELECT type, fup, lastpost FROM $table_forums WHERE fid='" . intval($delete) . "'");
                $forumtype = sql_fetch_object($queryforumtype);

                $fupid = $forumtype->fup;
                $postingtime = $forumtype->lastpost;
                $forumtypedelete = $forumtype->type;

                if ($forumtype->type == 'sub') {
                    sql_query("UPDATE $table_forums SET threads=threads-$threadnumber, posts=posts-$postnumber WHERE fid='" . intval($forumtype->fup) . "'");
                }

                $queryforumlinks = sql_query("SELECT lid FROM $table_links WHERE type ='forum' AND (fromid='" . intval($delete) . "' OR toid='" . intval($delete) . "')");
                while ($forumlink = sql_fetch_object($queryforumlinks)) {
                    sql_query("DELETE FROM $table_links WHERE lid='" . $forumlink->lid . "'");
                }

                $querythreadlinks = sql_query("SELECT lid FROM $table_links WHERE type ='thread' AND toid='" . intval($delete) . "'");
                while ($threadlink = sql_fetch_object($querythreadlinks)) {
                    sql_query("DELETE FROM $table_links WHERE lid='" . $threadlink->lid . "'");
                }

                sql_query("DELETE FROM $table_forums WHERE (type='forum' OR type='sub') AND fid='" . intval($delete) . "'");

                if ($forumtypedelete == 'sub') {
                    mxbLastPostForum($fupid, $postingtime);
                }
            }

            echo "<tr class=\"tablerow altbg2\"><td align=\"center\">" . mxbMessageScreen(_TEXTFORUMUPDATE) . "</td></tr>";
        }
        if (isset($check_authors)) {
            foreach($check_authors as $check_autho) {
                mxbRepairUserPostNum($check_autho);
            }
            unset($check_authors, $check_autho);
        }

        echo mxbRedirectScript(MXB_BM_CP1 . "action=forum", 2000);
    }
}

if ($action == "mods") {
    if (empty($modsubmit)) {

        ?>

<tr class="altbg2">
<td align="center">
<br />

<form method="post" action="<?php echo MXB_BM_CP1 ?>action=mods">

<table cellspacing="0" cellpadding="0" border="0" width="100%" align="center">
<tr><td class="bordercolor">

<table border="0" cellspacing="<?php echo $borderwidth ?>" cellpadding="<?php echo $tablespace ?>" width="100%">

<tr class="mxb-header">
<td><?php echo _TEXTFORUM ?></td>
<td><?php echo _TEXTMODERATOR ?></td>
</tr>

<?php
        $queryf = sql_query("SELECT name, moderator, fid FROM $table_forums WHERE type='forum'");
        while ($mod = sql_fetch_object($queryf)) {

            ?>

<tr class="tablerow altbg2">
<td><?php echo $mod->name ?></td>
<td><input type="text" size="60" name="mod<?php echo $mod->fid ?>" value="<?php echo htmlspecialchars($mod->moderator) ?>" /></td>
</tr>

<?php

            $querys = sql_query("SELECT name, moderator, fid FROM $table_forums WHERE type='sub' AND fup='" . intval($mod->fid) . "' ORDER BY displayorder");
            while ($mod = sql_fetch_object($querys)) {

                ?>

<tr class="tablerow altbg2">
<td align="right">&nbsp;&nbsp;<?php echo $mod->name ?></td>
<td align="right">&nbsp;&nbsp;<input type="text" size="55" name="mod<?php echo $mod->fid ?>" value="<?php echo htmlspecialchars($mod->moderator) ?>" /></td>
</tr>

<?php
            }
        }

        ?>

</table>
</td></tr></table>
<span class="f11pix"><?php echo _MULTMODNOTE ?></span>
<center><br/><input type="submit" name="modsubmit" value="<?php echo _TEXTSUBMITCHANGES ?>" /></center>
</form>

</td>
</tr>

<?php
    }

    if (!empty($modsubmit)) {
        $queryforum = sql_query("SELECT fid FROM $table_forums");
        // erstmal alle Moderatoren auf Member zurücksetzen, sonst bleiben Karteileichen erhalten
        sql_query("UPDATE $table_members SET status='Member' WHERE status='Moderator'");

        while ($forum = sql_fetch_object($queryforum)) {
            $mod = (isset($_REQUEST['mod' . $forum->fid])) ? $_REQUEST['mod' . $forum->fid] : '';
            $modz = preg_split('#\s*,\s*#', trim($mod, ', '));
            sort($modz);
            $mod = trim(implode(', ', array_unique($modz)));
            sql_query("UPDATE $table_forums SET moderator='$mod' WHERE fid='" . intval($forum->fid) . "'");
            foreach($modz as $moderatorname) {
                if (!isset($formod[$moderatorname]) && !empty($moderatorname)) {
                    $moderatorname = trim(substr($moderatorname, 0, 25));
                    $query = sql_query("SELECT status FROM $table_members WHERE username='" . $moderatorname . "'");
                    $usinfo = sql_fetch_object($query);
                    if (is_object($usinfo) && $usinfo->status != "Administrator" && $usinfo->status != "Super Moderator") {
                        sql_query("UPDATE $table_members SET status='Moderator' WHERE username='" . $moderatorname . "'");
                    }
                    $formod[$moderatorname] = $moderatorname;
                }
            }
        }
        echo "<tr class=\"tablerow altbg2\"><td align=\"center\">" . mxbMessageScreen(_TEXTMODUPDATE) . "</td></tr>";
        echo mxbRedirectScript(MXB_BM_CP1 . "action=mods", 2000);
    }
}

if ($action == "members") {
    if (empty($membersubmit)) {

        ?>
<tr class="altbg2">
<td align="center">
<br />

<?php
        if (empty($members)) {

            ?>

<form method="post" action="<?php echo MXB_BM_CP1 ?>action=members&members=search">

<span class="f12pix"><?php echo _TEXTSRCHUSR ?></span> <input type="text" name="srchmem">
<span class="f12pix"><?php echo _TEXTWITHSTATUS ?></span>

<select name="srchstatus">
<option value="0" selected="selected"><?php echo _ANYSTATUS ?></option>
<option value="Administrator"><?php echo _TEXTADMIN ?></option>
<option value="Super Moderator"><?php echo _TEXTSUPERMOD ?></option>
<option value="Moderator"><?php echo _TEXTMOD ?></option>
<option value="Member"><?php echo _TEXTMEM ?></option>
<option value="Banned"><?php echo _TEXTBANNED ?></option>
</select><br /><br />
<input type="submit" value="<?php echo _TEXTGO ?>" />
</form>
</td></tr>

<?php
        }

        if (isset($members) && $members == "search") {

            ?>

<form method="post" action="<?php echo MXB_BM_CP1 ?>action=members">

<table cellspacing="0" cellpadding="0" border="0" width="100%" align="center">
<tr><td class="bordercolor">

<table border="0" cellspacing="<?php echo $borderwidth ?>" cellpadding="<?php echo $tablespace ?>" width="100%">

<tr class="mxb-header">
<td><?php echo _TEXTDELETEQUES ?></td>
<td><?php echo _TEXTUSERNAME ?></td>
<td><?php echo _TEXTPOSTS ?></td>
<td><?php echo _TEXTSTATUS ?></td>
<td><?php echo _TEXTCUSSTATUS ?></td>
</tr>

<?php
            if ($srchstatus == "0") {
                $query = sql_query("SELECT * FROM $table_members WHERE username LIKE '%" . substr($srchmem, 0, 25) . "%' ORDER BY username");
            } else {
                $query = sql_query("SELECT * FROM $table_members WHERE username LIKE '%" . substr($srchmem, 0, 25) . "%' AND status='$srchstatus' ORDER BY username");
            }
            // Schleife durch Suchergebnisse
            while ($member = sql_fetch_object($query)) {
                switch ($member->status) {
                    case "Administrator":
                        $stat['ad'] = ' selected="selected" class="current"';
                        $stat['sm'] = ' disabled="disabled" style="visibility: hidden; display : none;"';
                        $stat['mo'] = ' disabled="disabled" style="visibility: hidden; display : none;"';
                        $stat['me'] = ' disabled="disabled" style="visibility: hidden; display : none;"';
                        $stat['ba'] = ' disabled="disabled" style="visibility: hidden; display : none;"';
                        break;
                    case "Super Moderator":
                        $issermod = mxbIsModerator($member->username);
                        $stat['ad'] = ' disabled="disabled" style="visibility: hidden; display : none;"';
                        $stat['sm'] = ' selected="selected" class="current"';
                        $stat['mo'] = ($issermod) ? '' : ' disabled="disabled" style="visibility: hidden; display : none;"';
                        $stat['me'] = ($issermod) ? ' disabled="disabled" style="visibility: hidden; display : none;"' : '';
                        $stat['ba'] = ' disabled="disabled" style="visibility: hidden; display : none;"';
                        break;
                    case "Moderator":
                        $stat['ad'] = ' disabled="disabled" style="visibility: hidden; display : none;"';
                        $stat['sm'] = '';
                        $stat['mo'] = ' selected="selected" class="current"';
                        $stat['me'] = ' disabled="disabled" style="visibility: hidden; display : none;"';
                        $stat['ba'] = ' disabled="disabled" style="visibility: hidden; display : none;"';
                        break;
                    case "Banned":
                        $stat['ad'] = ' disabled="disabled" style="visibility: hidden; display : none;"';
                        $stat['sm'] = ' disabled="disabled" style="visibility: hidden; display : none;"';
                        $stat['mo'] = ' disabled="disabled" style="visibility: hidden; display : none;"';
                        $stat['me'] = '';
                        $stat['ba'] = ' selected="selected" class="current"';
                        break;
                    case "Member":
                        $stat['ad'] = ' disabled="disabled" style="visibility: hidden; display : none;"';
                        $stat['sm'] = '';
                        $stat['mo'] = ' disabled="disabled" style="visibility: hidden; display : none;"';
                        $stat['me'] = ' selected="selected" class="current"';
                        $stat['ba'] = '';
                        break;
                    default:
                        $stat['ad'] = ' disabled="disabled" style="visibility: hidden; display : none;"';
                        $stat['sm'] = ' disabled="disabled" style="visibility: hidden; display : none;"';
                        $stat['mo'] = ' disabled="disabled" style="visibility: hidden; display : none;"';
                        $stat['me'] = ' selected="selected" class="current"';
                        $stat['ba'] = '';
                }
                $select = '
<select name="status' . $member->uid . '">
<option value="Administrator" ' . $stat['ad'] . '>' . _TEXTADMIN . '</option>
<option value="Super Moderator" ' . $stat['sm'] . '>' . _TEXTSUPERMOD . '</option>
<option value="Moderator" ' . $stat['mo'] . '>' . _TEXTMOD . '</option>
<option value="Member" ' . $stat['me'] . '>' . _TEXTMEM . '</option>
<option value="Banned" ' . $stat['ba'] . '>' . _TEXTBANNED . '</option>
</select>
' ?>

<tr class="tablerow altbg2">
<td><input type="checkbox" name="delete<?php echo $member->uid ?>" value="<?php echo $member->uid ?>" /></td>
<td><a href="<?php echo MXB_BM_MEMBER1 ?>action=editpro&amp;member=<?php echo $member->username ?>"><?php echo $member->username ?></a></td>
<td><?php echo $member->postnum ?></td>
<td><?php echo $select ?></td>
<td><input type="text" size="25" name="cusstatus<?php echo $member->uid ?>" value="<?php echo htmlspecialchars($member->customstatus) ?>" /></td>
</tr>

<?php
            } /// end while

            ?>

</table>
</td></tr></table>
<center><br/><input type="submit" name="membersubmit" value="<?php echo _TEXTSUBMITCHANGES ?>" /></center>
<input type="hidden" name="srchmem" value="<?php echo $srchmem ?>"/>
<input type="hidden" name="srchstatus" value="<?php echo $srchstatus ?>"/>
</form>

</td>
</tr>

<?php
        }
    }

    if (!empty($membersubmit)) {
        if ($srchstatus == "0") {
            $query = sql_query("SELECT uid, username FROM $table_members WHERE username LIKE '%" . substr($srchmem, 0, 25) . "%'");
        } else {
            $query = sql_query("SELECT uid, username FROM $table_members WHERE username LIKE '%" . substr($srchmem, 0, 25) . "%' AND status='$srchstatus'");
        }
        // Schleife durch Suchergebnisse
        while ($mem = sql_fetch_object($query)) {
            $statusM = "status" . $mem->uid;
            $statusM = "${$statusM}";
            $cusstatus = "cusstatus" . $mem->uid;
            $cusstatus = "${$cusstatus}";
            $delete = "delete" . $mem->uid;
            $delete = (isset($$delete)) ? intval($$delete) : 0;

            if ($delete) {
                $query = sql_query("SELECT username FROM $table_members WHERE uid = " . intval($delete));
                $chkk = sql_fetch_object($query);
                if (!empty($chkk->username)) {
                    mxbCleanBoardAccess($chkk->username);
                }
                sql_query("DELETE FROM $table_members WHERE uid='" . intval($delete) . "'");
            } else {
                $newcustom = addslashes($cusstatus);
                sql_query("UPDATE $table_members SET status='$statusM', customstatus='$newcustom' WHERE uid='" . intval($mem->uid) . "'");
            }
        }

        echo "<tr class=\"tablerow altbg2\"><td align=\"center\">" . mxbMessageScreen(_TEXTMEMBERSUPDATE) . "</td></tr>";
        echo mxbRedirectScript(MXB_BM_CP1 . "action=members", 2000);
    }
}

if ($action == "ipban") {
    if (empty($ipbansubmit)) {

        ?>

<tr class="altbg2">
<td align="center">
<br />

<form method="post" action="<?php echo MXB_BM_CP1 ?>action=ipban">

<table cellspacing="0" cellpadding="0" border="0" width="100%" align="center">
<tr><td class="bordercolor">

<table border="0" cellspacing="<?php echo $borderwidth ?>" cellpadding="<?php echo $tablespace ?>" width="100%">
<tr>
<td class="mxb-header"><?php echo _TEXTDELETEQUES ?></td>
<td class="mxb-header"><?php echo _TEXTIP ?>:</td>
<td class="mxb-header"><?php echo _TEXTADDED ?></td>
</tr>

<?php
        $query = sql_query("SELECT * FROM $table_banned ORDER BY dateline");
        while ($ipaddress = sql_fetch_object($query)) {
            for($i = 1; $i <= 4; ++$i) {
                $j = "ip" . $i;
                if ($ipaddress->$j == -1) $ipaddress->$j = "*";
            }

            $ipdate = date("n/j/y", $ipaddress->dateline + ($timeoffset * 3600)) . " " . _TEXTAT . " " . date($timecode, (int)$ipaddress->dateline + ($timeoffset * 3600));
            $theip = $ipaddress->ip1 . '.' . $ipaddress->ip2 . '.' . $ipaddress->ip3 . '.' . $ipaddress->ip4 ?>

<tr class="altbg2">
<td class="tablerow"><input type="checkbox" name="delete<?php echo $ipaddress->id ?>" value="<?php echo $ipaddress->id ?>" /></td>
<td class="tablerow"><?php echo $theip ?></td>
<td class="tablerow"><?php echo $ipdate ?></td>
</tr>

<?php
        }
        $ips = explode(".", $onlineip);
        $query = sql_query("SELECT id FROM $table_banned WHERE (ip1='$ips[0]' OR ip1='-1') AND (ip2='$ips[1]' OR ip2='-1') AND (ip3='$ips[2]' OR ip3='-1') AND (ip4='$ips[3]' OR ip4='-1')");
        $result = sql_fetch_object($query);
        if ($result) $warning = _IPWARNING;
        else $warning = '' ?>
<tr class="altbg2"><td colspan="3"> </td></tr>
<tr class="altbg1">
<td colspan="3" class="tablerow"><?php echo _TEXTNEWIP ?>
<input type="text" name="newip1" size="3" maxlength="3" />.<input type="text" name="newip2" size="3" maxlength="3" />.<input type="text" name="newip3" size="3" maxlength="3" />.<input type="text" name="newip4" size="3" maxlength="3" /></td>
</tr>

</table>
</td></tr></table>
<span class="f11pix"><?php echo _CURRENTIP ?> <b><?php echo $onlineip ?></b><?php echo $warning ?><br /><?php echo _MULTIPNOTE ?></span>
<center><br/><input type="submit" name="ipbansubmit" value="<?php echo _TEXTSUBMITCHANGES ?>" /></center>
</form>

</td>
</tr>

<?php
        }
        // Attention problèmes potentiels avec les $ip...
        if (!empty($ipbansubmit)) {
            $queryip = sql_query("SELECT id FROM $table_banned");
            $newid = 1;
            while ($ip = sql_fetch_object($queryip)) {
                $delete = (isset($_REQUEST['delete' . $ip->id])) ? intval($_REQUEST['delete' . $ip->id]) : 0;
                if ($delete) {
                    $query = sql_query("DELETE FROM $table_banned WHERE id='" . intval($delete) . "'");
                } elseif ($ip->id > $newid) {
                    $query = sql_query("UPDATE $table_banned SET id='$newid' WHERE id='" . intval($ip->id) . "'");
                }
                $newid++;
            }

            $statusIP = _TEXTIPUPDATE;

            if ($newip1 || $newip2 || $newip3 || $newip4) {
                $invalid = 0;

                for($i = 1;$i <= 4 && !$invalid;++$i) {
                    $newip = "newip$i";
                    $newip = "${$newip}";
                    $newip = trim($newip);
                    if ($newip == "*") $ip[$i] = -1;
                    elseif (preg_match("#^[0-9]+$#", $newip)) $ip[$i] = $newip;
                    else $invalid = 1;
                }

                if ($invalid) {
                    $statusIP = _INVALIDIP;
                } else {
                    $onlinetime = (isset($onlinetime)) ? $onlinetime : '';
                    $query = sql_query("SELECT id FROM $table_banned WHERE (ip1='$ip[1]' OR ip1='-1') AND (ip2='$ip[2]' OR ip2='-1') AND (ip3='$ip[3]' OR ip3='-1') AND (ip4='$ip[4]' OR ip4='-1')");
                    $result = sql_fetch_object($query);
                    if ($result) $statusIP = _EXISTINGIP;
                    else $query = sql_query("INSERT INTO $table_banned VALUES ('$ip[1]', '$ip[2]', '$ip[3]', '$ip[4]', '$onlinetime', '$newid')");
                }
            }

            echo "<tr class=\"altbg2\"><td align=\"center\" class=\"tablerow\">$statusIP</td></tr>";
        }
    }

    if ($action == "upgrade") {
        if (!empty($upgradesubmit)) {
            $explode = explode(";", $upgrade);
            $count = sizeof($explode);

            for($num = 0;$num < $count;$num++) {
                $explode[$num] = stripslashes($explode[$num]);
                if ($explode[$num]) {
                    sql_query($explode[$num]);
                }
            }
            echo "<tr class=\"tablerow altbg2\"><td align=\"center\">" . mxbMessageScreen(_UPGRADESUCCESS) . " </td></tr>";
        }
        if (empty($upgradesubmit)) {

            ?>

<tr class="altbg2">
<td align="center">
<br />

<form method="post" action="<?php echo MXB_BM_CP1 ?>action=upgrade">

<table cellspacing="0" cellpadding="0" border="0" width="100%" align="center">
<tr><td class="bordercolor">

<table border="0" cellspacing="<?php echo $borderwidth ?>" cellpadding="<?php echo $tablespace ?>" width="100%">

<tr class="mxb-header">
<td colspan=2><?php echo _TEXTUPGRADE ?></td>
</tr>

<tr class="tablerow altbg1">
<td valign="top"><?php echo _UPGRADE ?><br /><textarea cols="<?php echo $textareacols ?>" rows="7" name="upgrade"></textarea><br /><?php echo _UPGRADENOTE ?></td>
</tr>
</table>
</td></tr></table>
<center><br/><input type="submit" name="upgradesubmit" value="<?php echo _TEXTSUBMITCHANGES ?>" /></center>
</form>

</td>
</tr>

<?php
        }
    }

    echo "</table></td></tr></table>";

    include_once(MXB_BASEMODINCLUDE . 'footer.php');

    function mxbGetForumSelect($current_fid, $this_fid = 0)
    {
        global $table_forums;

        static $boards, $allboards, $parents;

        if (!isset($boards)) {
            $boards = array();
            $result = sql_query("SELECT fid, fup, type, name FROM $table_forums WHERE type='forum' OR type='group' OR type='sub' ORDER BY type, displayorder");
            while ($row = sql_fetch_assoc($result)) {
                if ($row['type'] == 'sub') {
                    $parents[$row['fup']] = $row;
                } else {
                    $boards[$row['fid']] = $row;
                    $allboards[$row['fid']] = $row;
                }
            }
            unset($parents[0]);
            foreach($boards as $fid => $board) {
                if (!empty($board['fup']) && isset($boards[$board['fup']])) {
                    $boards[$board['fup']]['boards'][$board['fid']] = $board;
                    unset($boards[$board['fid']]);
                }
            }
        }

        $current_fid = (!empty($current_fid) && isset($allboards[$current_fid])) ? $current_fid : 0;
        $this_fid = (!empty($this_fid) && isset($allboards[$this_fid])) ? $this_fid : 0;

        $options[0] = '<option value="0"' . ((empty($current_fid)) ? ' selected="selected" class="current"' : '') . '>-' . _TEXTNONE . '-</option>';
        foreach($boards as $fid => $board) {
            if ($board['type'] == 'group' && $fid != $this_fid) {
                // die Kategorien
                $options[$fid] = '<option value="' . $fid . '"' . (($current_fid == $fid) ? ' selected="selected" class="current"' : '') . '>' . $board['name'] . '</option>';
            } else {
                if (!isset($parents[$this_fid]) && $fid != $this_fid) {
                    // die Foren ohne Kategorie
                    $options[$fid] = '<option value="' . $fid . '"' . (($current_fid == $fid) ? ' selected="selected" class="current"' : '') . '>&middot;&nbsp;' . $board['name'] . '</option>';
                }
            }
            // die Foren in den Kategorien
            if (isset($board['boards']) && !isset($parents[$this_fid])) {
                foreach($board['boards'] as $bfid => $bboard) {
                    if ($bfid != $this_fid) {
                        $options[$bfid] = '<option value="' . $bfid . '"' . (($current_fid == $bfid) ? ' selected="selected" class="current"' : '') . '>&nbsp;&middot;&nbsp;' . $bboard['name'] . '</option>';
                    }
                }
            }
        }

        return "\n" . implode("\n", $options) . "\n";
    }

    ?>
