<?php
/**
 * mxBoard, pragmaMx Module
 * Copyright by pragmaMx Developer Team - http://www.pragmamx.org
 *
 * $Author: PragmaMx $
 * $Revision: 120 $
 * $Date: 2016-03-31 12:35:00 +0200 (jeu. 31 mars 2016) $
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
include_once(dirname(__FILE__) . DS . 'includes' . DS . 'helper.php');

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
// -----------------------------------------------------------------------------
// Just display config panel
// -----------------------------------------------------------------------------
if (empty($settingsubmit)) {
    /* Build SELECT language */
    $xlangfile = ebGetLangfileFromStetting();
    $langlist = mxbGetAvailableLanguages();
    $langlist = array_merge(array('- ' . _TEXTDEFAULT => ''), $langlist);
    $list = '';
    foreach ($langlist as $key => $value) {
        $sel = ($value == $xlangfile) ? ' selected="selected" class="current"' : ''; // Standard vorselektieren
        $list .= '<option value="' . $value . '"' . $sel . '>' . $key . '</option>';
    }
    $langfileselect = '<select name="langfile">' . $list . '</select>';

    /* Build SELECT themes   */
    $query = sql_query("SELECT name FROM $table_themes ORDER BY name");
    $sel = array();
	$check="";
    while ($row = sql_fetch_object($query)) {
        $check = ($row->name == $XFthemedef) ? ' selected="selected" class="current"' : '';
        $sel[] = '<option value="' . $row->name . '"' . $check . '>' . $row->name . '</option>';
    }
    $themeselect = '<select name="XFtheme" size="1">' . implode("\n", $sel) . '</select>';

    /* Build SELECT time */
    $offsets = array('-12' => '-12:00',
        '-11' => '-11:00',
        '-10' => '-10:00',
        '-9' => '-9:00',
        '-8' => '-8:00',
        '-7' => '-7:00',
        '-6' => '-6:00',
        '-5' => '-5:00',
        '-4' => '-4:00',
        '-3.5' => '-3:30',
        '-3' => '-3:00',
        '-2' => '-2:00',
        '-1' => '-1:00',
        '0' => '0',
        '1' => '+1:00',
        '2' => '+2:00',
        '3' => '+3:00',
        '3.5' => '+3:30',
        '4' => '+4:00',
        '4.5' => '+4:30',
        '5' => '+5:00',
        '5.5' => '+5:30',
        '6' => '+6:00',
        '7' => '+7:00',
        '8' => '+8:00',
        '9' => '+9:00',
        '9.5' => '+9:30',
        '10' => '+10:00',
        '11' => '+11:00',
        '12' => '+12:00',
        );
    $sel = array();
    foreach ($offsets as $value => $show) {
        $check = ($value == $globaltimeoffset) ? ' selected="selected" class="current"' : '';
        $sel[] = '<option value="' . $value . '"' . $check . '>' . $show . '</option>';
    }
    $timeoffsetselect = '<select name="globaltimeoffset" size="1" class="align-right">' . implode("\n", $sel) . '</select>';
    $currdate = gmdate($timecode);
    eval(_EVALOFFSET); // "Zeit-Offset (GMT ist im Moment $currdate)"

    /* Build SELECT admin stars */
    if ($adminstars == 'sameasusers') {
        $selstars2 = '';
        $selstars1 = 'selected="selected" class="current"';
    } else {
        $selstars1 = '';
        $selstars2 = 'selected="selected" class="current"';
    }
    $adminstarsselect = ''
     . '<select name="adminstars" size="1">'
     . '<option value="sameasusers" ' . $selstars1 . '>' . _SAMEASUSERS . '</option>'
     . '<option value="maxusersp3" ' . $selstars2 . '>' . _MAXUSERSP3 . '</option>'
     . '</select>';

    /* Build SELECT admin stars */
    $eb_defstaffon1 = '';
    $eb_defstaffon2 = '';
    $eb_defstaffon3 = '';
    if ($eb_defstaff == 'staff') {
        $eb_defstaffon1 = 'selected="selected" class="current"';
    } elseif ($eb_defstaff == 'user') {
        $eb_defstaffon2 = 'selected="selected" class="current"';
    } else {
        $eb_defstaffon3 = 'selected="selected" class="current"';
    }
    $accessmode_select = ''
     . '<select name="eb_defstaff" size="1">'
     . '<option value="" ' . $eb_defstaffon3 . '>' . _EBF_ALL . '</option>'
     . '<option value="user" ' . $eb_defstaffon2 . '>' . _EBF_MEMBERONLY . '</option>'
     . '<option value="staff" ' . $eb_defstaffon1 . '>' . _EBF_STAFFONLY . '</option>'
     . '</select>';

    /* Build CHECKBOX timeformat */
    if ($timeformat == '24') {
        $check12 = '';
        $check24 = 'checked="checked"';
    } else {
        $check12 = 'checked="checked"';
        $check24 = '';
    }

    /* fix boardname */
    $bbname = str_replace(array('\"', "\'"), array('"', "'"), $bbname);

    /* Ausgabe */
    ?>

<tr class="bgcolor2">
<td align="center">
<form method="post" action="<?php echo MXB_BM_SETTINGS0 ?>" name="validsettings">
<table cellspacing="0" cellpadding="0" border="0" width="100%" align="center">
<tr><td class="bordercolor">
<table border="0" cellspacing="<?php echo $borderwidth ?>" cellpadding="<?php echo $tablespace ?>" width="100%">

<?php

    colTabLineTitle(_TEXTSETTINGS);
    colTabLineInput ('altbg2', _TEXTBBNAME, $bbname, 'bbname', 40);
    colTabLineSwitch ('altbg1', _TEXTBSTATUS, $bbstatus, 'bbstatus');
    colTabLineTextarea('altbg1', _TEXTBBOFFREASON, stripslashes($bboffreason), 7, 60, 'bboffreason');
    colTabLineSwitch ('altbg2', _CAPTCHAON, $captcha, 'captcha');
    colTabLineSwitch ('altbg2', _CAPTCHAUSERON, $captchauser, 'captchauser');
    colTabLineInput ('altbg1', _TEXTFLOOD, $floodctrl, 'floodctrl', 2);
    colTabLineSwitch ('altbg2', _TEXTREGGEDONLY, $regviewonly, 'regviewonly');
    colTabLineSwitch ('altbg1', _TEXTHIDEPRIV, $hideprivate, 'hideprivate');
    colTabLineSimple ('altbg1', _TEXTLANGUAGE, $langfileselect);
    colTabLineSimple ('altbg2', _TEXTTHEME, $themeselect);
    colTabLineSwitch ('altbg1', _TEXTLASTPOSTSUBJECT, $lastpostsubj, 'lastpostsubj');
    colTabLineInput ('altbg2', _TEXTLASTPOSTSUBJECTCHARS, $lastpostsubjchars, 'lastpostsubjchars', 2);
    colTabLineSwitch ('altbg1', _TEXTLINKFORUMSTATUS, $linkforumstatus, 'linkforumstatus');
    colTabLineSwitch ('altbg2', _TEXTLINKTHREADSTATUS, $linkthreadstatus, 'linkthreadstatus');
    colTabLineInput ('altbg1', _TEXTPPP, $postperpage, 'postperpage', 2);
    colTabLineInput ('altbg2', _TEXTTPP, $topicperpage, 'topicperpage', 2);
    colTabLineInput ('altbg1', _TEXTMPP, $memberperpage, 'memberperpage', 2);
    colTabLineInput ('altbg2', _TEXTHOTTOPIC, $hottopic, 'hottopic', 2);
    colTabLineSwitch ('altbg1', _WHOSONLINE_ON, $whosonlinestatus, 'whosonlinestatus');
    colTabLineSwitch ('altbg1', _TEXTADVANCEDONLINE, $advancedonlinestatus, 'advancedonlinestatus');
    colTabLineSwitch ('altbg1', _TEXTSHOWSORT, $showsort, 'showsort');
    colTabLineSwitch ('altbg2', _TEXTBBRULES, $bbrules, 'bbrules');
    colTabLineTextarea('altbg2', _TEXTBBRULESTXT, stripslashes($bbrulestxt), 15, 60, 'bbrulestxt');
    colTabLineSwitch ('altbg1', _TEXTSEARCHSTATUS, $searchstatus, 'searchstatus');
    colTabLineSwitch ('altbg2', _TEXTMESSOTDSTATUS, $messotdstatus, 'messotdstatus');
    colTabLineInput ('altbg1', _TEXTDUREEMESSOTD, $dureemessotd, 'dureemessotd', 4);
    colTabLineSwitch ('altbg2', _TEXTFAQSTATUS, $faqstatus, 'faqstatus');
    colTabLineSwitch ('altbg1', _TEXTMEMLISTSTATUS, $memliststatus, 'memliststatus');
    colTabLineSwitch ('altbg1', _TEXTMEMLISTANONYMOUSSTATUS, $memlistanonymousstatus, 'memlistanonymousstatus');
    colTabLineSwitch ('altbg2', _STATSPAGE, $statspage, 'statspage');
    colTabLineInput ('altbg1', _NBITEMSINSTATS, $nbitemsinstats, 'nbitemsinstats', 2);
    colTabLineSwitch ('altbg2', _INDEXSTATSCP, $indexstats, 'indexstats');
    colTabLineSwitch ('altbg1', _AFFJUMPER, $affjumper, 'affjumper');
    colTabLineSwitch ('altbg1', _AFFJUMPERDYNAMIC, $affjumperdynamic, 'affjumperdynamic');
    colTabLineSwitch ('altbg2', _TEXTPICONSTATUS, $piconstatus, 'piconstatus');
    colTabLineInput ('altbg1', _TEXTSMILIESLINENUMBER, $smilieslinenumber, 'smilieslinenumber', 1);
    colTabLineInput ('altbg1', _TEXTSMILIESROWSNUMBER, $smiliesrownumber, 'smiliesrownumber', 1);
    colTabLineSwitch ('altbg2', _TEXTAVASTATUS, $avastatus, 'avastatus');
    colTabLineSwitch ('altbg1', _TEXTCOLORSUBJECT, $colorsubject, 'colorsubject');
    colTabLineSwitch ('altbg2', _TEXTALLOWU2U, $allowu2u, 'allowu2u');
    colTabLineSwitch ('altbg1', _REPORTPOSTSTATUS, $reportpost, 'reportpost');
    colTabLineSwitch ('altbg2', _SHOWTOTALTIME, $showtotaltime, 'showtotaltime');
    colTabLineSimple ('altbg1', _ADMINSTARSCONFIG, $adminstarsselect);
    colTabLineTitle(_EMAILREGULATION);
    colTabLineInput ('altbg1', _ADMINEMAIL, $adminemail, 'adminemail', 40);
    colTabLineSwitch ('altbg2', _EMALALLTOADMIN, $emailalltoadmin, 'emailalltoadmin');
    colTabLineSwitch ('altbg2', _MAILONTHREAD, $mailonthread, 'mailonthread');
    colTabLineSwitch ('altbg2', _MAILONPOST, $mailonpost, 'mailonpost');
    colTabLineSwitch ('altbg2', _MAILONEDIT, $mailonedit, 'mailonedit');
    colTabLineSwitch ('altbg2', _MAILONDELE, $mailondele, 'mailondele');
    colTabLineSwitch ('altbg1', _EMAILALLTOMODERATOR, $emailalltomoderator, 'emailalltomoderator');
    colTabLineSwitch ('altbg1', _MODERATORMAILONTHREAD, $moderatormailonthread, 'moderatormailonthread');
    colTabLineSwitch ('altbg1', _MODERATORMAILONPOST, $moderatormailonpost, 'moderatormailonpost');
    colTabLineSwitch ('altbg1', _MODERATORMAILONEDIT, $moderatormailonedit, 'moderatormailonedit');
    colTabLineSwitch ('altbg1', _MODERATORMAILONDELETE, $moderatormailondelete, 'moderatormailondelete');
    colTabLineTitle(_POSTMESSAGEBOXSIZE);
    colTabLineInput ('altbg2', _TEXTAREACOLS, $textareacols, 'textareacols', 4);
    colTabLineInput ('altbg1', _TEXTAREAROWS, $textarearows, 'textarearows', 4);
    colTabLineTitle(_SIG);
    colTabLineSwitch ('altbg2', _SIGBBCODE, $sigbbcode, 'sigbbcode');
    colTabLineSwitch ('altbg1', _SIGHTML, $sightml, 'sightml');
    colTabLineSwitch ('altbg2', _SIGIMGXXXAUTH, $sigimgXxXauth, 'sigimgXxXauth');
    colTabLineInput ('altbg2', _SIGIMGHEIGHT, $sigimgheight, 'sigimgheight', 4);
    colTabLineInput ('altbg2', _SIGIMGWIDTH, $sigimgwidth, 'sigimgwidth', 4);
    colTabLineTitle(_TEXTFORUMTIME);
    colTabLineRadio ('altbg1', _TEXTTIMEFORMAT, _TEXT24HOUR, $check24, '24', _TEXT12HOUR, $check12, '12', 'timeformat');
    colTabLineInput ('altbg2', _DATEFORMAT, $dformatorigconf, 'dateformat', 30);
    colTabLineSwitch ('altbg1', _TEXTGLOBALTIMESTATUS, $globaltimestatus, 'globaltimestatus');
    colTabLineSimple ('altbg2', _TEXTOFFSET, $timeoffsetselect);
    colTabLineTitle(_TEXTDEFAULTVALUES . ' ' . _TEXTDEFAULTVALUESF);
    colTabLineSwitch2 ('altbg1', _TEXTDEFAULTVALUESFHTML, $eb_defhtml, 'eb_defhtml');
    colTabLineSwitch2 ('altbg2', _TEXTDEFAULTVALUESFSMILIES, $eb_defsmilies, 'eb_defsmilies');
    colTabLineSwitch2 ('altbg1', _TEXTDEFAULTVALUESFBBCODE, $eb_defbbcode, 'eb_defbbcode');
    colTabLineSwitch2 ('altbg2', _TEXTDEFAULTVALUESFANOPOSTS, $eb_defanoposts, 'eb_defanoposts');
    colTabLineSwitch2 ('altbg1', _TEXTDEFAULTVALUESFIMGCODE, $eb_defimgcode, 'eb_defimgcode');
    colTabLineSimple ('altbg2', _TEXTDEFAULTVALUESFSTAFF, $accessmode_select);
    colTabLineSwitch2 ('altbg1', _TEXTDEFAULTVALUESFMAIN2SUB, $eb_defmain2sub, 'eb_defmain2sub');
    colTabLineTextarea('altbg2', _TEXTDEFAULTVALUESFMODS, stripslashes($eb_defmods), 7, 60, 'eb_defmods');
    colTabLineTitle(_TEXTDEFAULTVALUES . ' ' . _TEXTDEFAULTVALUESM);
    colTabLineInput ('altbg1', _TEXTDEFAULTVALUESMMSLV, $eb_defmessslv, 'eb_defmessslv', 2);
    colTabLineSwitch2 ('altbg2', _TEXTDEFAULTVALUESMNL, $eb_defmembernews, 'eb_defmembernews');
    colTabLineSwitch2 ('altbg1', _TEXTDEFAULTVALUESMPM, $eb_defmemberu2u, 'eb_defmemberu2u');

    ?>


</table>
</td></tr></table>
<?php
    if (mxbChangeFilePerm(MXB_SETTINGSFILE, "unlock")) {

        ?>
<center><br/><input type="submit" name="settingsubmit" value="<?php echo _TEXTSUBMITCHANGES ?>" /></center>
<?php
        mxbChangeFilePerm(MXB_SETTINGSFILE, "lock");
    } else {

        ?>
<div  class="error"><?php echo _CHMODFAILEDSETTINGS ?></div>
<?php
    }

    ?>
</form>
</td>
</tr>

<?php
}
// -----------------------------------------------------------------------------
// save settings of the config panel
// -----------------------------------------------------------------------------
if (!empty($settingsubmit)) {
    include(MXB_SETTINGSFILE);

    $currentconfig = mxb_cp_get_config();
    $defaultconfig = mxbHelper::get_defaults();
    $postedconfig = $_POST;

    $repairs = array('postperpage' => 5,
        'topicperpage' => 5,
        'memberperpage' => 5,
        'hottopic' => 5,
        'textareacols' => 20,
        'textarearows' => 2,
        'lastpostsubjchars' => 10,
        'eb_defmessslv' => '',
        'dureemessotd' => '',
        'nbitemsinstats' => '',
        'smilieslinenumber' => '',
        'smiliesrownumber' => '',
        );
    if ($postedconfig['sigimgXxXauth'] == 'on') {
        $repairs['sigimgwidth'] = 50;
        $repairs['sigimgheight'] = 50;
    }
    foreach ($repairs as $key => $value) {
        if (empty($postedconfig[$key]) || ($value && $postedconfig[$key] < $value)) {
            $postedconfig[$key] = $defaultconfig[$key];
        }
    }

    $postedconfig['bbname'] = stripslashes(str_replace(array('"', "'"), array('\"', "\'"), $postedconfig['bbname']));

    if ($postedconfig['emailalltoadmin'] != 'on') {
        $postedconfig['mailonthread'] = 'off';
        $postedconfig['mailonpost'] = 'off';
        $postedconfig['mailonedit'] = 'off';
        $postedconfig['mailondele'] = 'off';
    }

    if ($postedconfig['emailalltomoderator'] != 'on') {
        $postedconfig['moderatormailonthread'] = 'off';
        $postedconfig['moderatormailonpost'] = 'off';
        $postedconfig['moderatormailonedit'] = 'off';
        $postedconfig['moderatormailondelete'] = 'off';
    }

    if (!empty($postedconfig['eb_defmods'])) {
        $temp = preg_split('#\s*[,;]\s*#', trim($postedconfig['eb_defmods'], ', '));
        sort($temp);
        $postedconfig['eb_defmods'] = implode(', ', array_unique($temp));
    }

    $postedconfig = array_merge($defaultconfig, $currentconfig, $postedconfig);

    $content = mxbHelper::get_config_string($postedconfig);

    $r = mx_write_file(MXB_SETTINGSFILE, $content, true);
    // wenn Standartheme geändert wurde
    if ($postedconfig['XFtheme'] != $currentconfig['XFtheme']) {
        // alle User auf neues Standardtheme zurücksetzen
        sql_query("UPDATE `$table_members` SET `theme` = '" . mxAddSlashesForSQL($postedconfig['XFtheme']) . "'");
    }

    echo "<tr class=\"tablerow altbg2\"><td align=\"center\">" . mxbMessageScreen(_TEXTSETTINGSUPDATE) . "</td></tr>";
    echo mxbRedirectScript(MXB_BM_SETTINGS0, 1250);
}
echo "</table></td></tr></table>";

include_once(MXB_BASEMODINCLUDE . 'footer.php');

/**
 * colTabLineTitle()
 *
 * @param mixed $text
 * @return
 */
function colTabLineTitle($text)
{

    ?>
<tr class="mxb-header">
<td colspan="2"><h3><?php echo $text ?></h3></td>
</tr>
  <?php
}

/**
 * colTabLineInput()
 *
 * @param mixed $class
 * @param mixed $text
 * @param mixed $value
 * @param mixed $name
 * @param mixed $size
 * @return
 */
function colTabLineInput($class, $text, $value, $name, $size)
{

    ?>
<tr class="tablerow <?php echo $class ?>">
<td><?php echo $text ?></td>
<td><input type="text"  value="<?php echo mxEntityQuotes($value) ?>" name="<?php echo $name ?>" size="<?php echo $size ?>"></td>
</tr>
  <?php
}

/**
 * colTabLineSwitch2()
 *
 * @param mixed $class
 * @param mixed $text
 * @param mixed $value
 * @param mixed $name
 * @return
 */
function colTabLineSwitch2($class, $text, $value, $name)
{
    if ($value == 'on' || $value == 'yes') {
        $valueon = ' selected="selected" class="current"';
        $valueoff = '';
    } else {
        $valueon = '';
        $valueoff = ' selected="selected" class="current"';
    }

    ?>
<tr class="tablerow <?php echo $class ?>">
<td><?php echo $text ?></td>
<td><select name="<?php echo $name ?>"><option value="yes" <?php echo $valueon ?>><?php echo _TEXTON ?></option><option value="no" <?php echo $valueoff ?>><?php echo _TEXTOFF ?></option></select></td>
</tr>
  <?php
}

/**
 * colTabLineSwitch()
 *
 * @param mixed $class
 * @param mixed $text
 * @param mixed $value
 * @param mixed $name
 * @return
 */
function colTabLineSwitch($class, $text, $value, $name)
{
    if ($value == 'on' || $value == 'yes') {
        $valueon = ' selected="selected" class="current"';
        $valueoff = '';
    } else {
        $valueon = '';
        $valueoff = ' selected="selected" class="current"';
    }

    ?>
<tr class="tablerow <?php echo $class ?>">
<td><?php echo $text ?></td>
<td><select name="<?php echo $name ?>"><option value="on" <?php echo $valueon ?>><?php echo _TEXTON ?></option><option value="off" <?php echo $valueoff ?>><?php echo _TEXTOFF ?></option></select></td>
</tr>
  <?php
}

/**
 * colTabLineTextarea()
 *
 * @param mixed $class
 * @param mixed $textleft
 * @param mixed $textarea
 * @param mixed $num_rows
 * @param mixed $num_col
 * @param mixed $name
 * @return
 */
function colTabLineTextarea($class, $textleft, $textarea, $num_rows, $num_col, $name)
{

    ?>
<tr class="tablerow <?php echo $class ?>">
<td><?php echo $textleft ?></td>
<td><textarea rows="<?php echo $num_rows ?>" name="<?php echo $name ?>" cols="<?php echo $num_col ?>"><?php echo mxEntityQuotes($textarea) ?></textarea></td>
</tr>
  <?php
}

/**
 * colTabLineSimple()
 *
 * @param mixed $class
 * @param mixed $textleft
 * @param mixed $textright
 * @return
 */
function colTabLineSimple($class, $textleft, $textright)
{

    ?>
<tr class="tablerow <?php echo $class ?>">
<td><?php echo $textleft ?></td>
<td><?php echo $textright ?></td>
</tr>
  <?php
}

/**
 * colTabLineRadio()
 *
 * @param mixed $class
 * @param mixed $textleft
 * @param mixed $text1
 * @param mixed $check1
 * @param mixed $value1
 * @param mixed $text2
 * @param mixed $check2
 * @param mixed $value2
 * @param mixed $name
 * @return
 */
function colTabLineRadio($class, $textleft, $text1, $check1, $value1, $text2, $check2, $value2, $name)
{

    ?>
<tr class="tablerow <?php echo $class ?>">
<td><?php echo $textleft ?></td>
<td><input type="radio" value="<?php echo $value1 ?>" name="<?php echo $name ?>" <?php echo $check1 ?>/><?php echo $text1 ?> <input type="radio" value="<?php echo $value2 ?>" name="<?php echo $name ?>" <?php echo $check2 ?>/><?php echo $text2 ?></td>
</tr>
  <?php
}

/**
 * ebGetLangfileFromStetting()
 * $langfile ist eine userdefinierte Variable und überschreibt die settings.php
 * deswegen hier extra... ;-))
 *
 * @return
 */
function ebGetLangfileFromStetting()
{
    include(MXB_SETTINGSFILE);
    return $langfile;
}

/**
 * mxb_cp_savesettings()
 *
 * @param mixed $pvs
 * @return
 */
function mxb_cp_savesettings($pvs)
{
    $content = mxbHelper::get_config_string($pvs);

    $result = mx_write_file(str_replace('.php', '', MXB_SETTINGSFILE) . '_bak_' . date('YmdHi') . '.php', file_get_contents(MXB_SETTINGSFILE), true);
    $result = mx_write_file(MXB_SETTINGSFILE, trim($content), true);
    return $result !== false;
}

/**
 * mxb_cp_get_config()
 *
 * @return
 */
function mxb_cp_get_config()
{
    static $conf;
    if (isset($conf)) {
        return $conf;
    }

    $conf = array();
    if (file_exists(MXB_SETTINGSFILE)) {
        include(MXB_SETTINGSFILE);
        $conf = get_defined_vars();
        unset($conf['conf'], $conf['prefix'], $conf['user_prefix']);
    }
    // $conf = array_merge(mxbHelper::get_defaults(), $conf);
    return $conf;
}

?>
