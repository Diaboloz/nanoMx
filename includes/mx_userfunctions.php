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

if (file_exists(PMX_SYSTEM_DIR . DS . 'mx_userfunctions_options.php')) {
    include_once(PMX_SYSTEM_DIR . DS . 'mx_userfunctions_options.php');
    // alte Version der YA-Erweiterung aktiv
    if (!defined('PMX_YA_EXTENDED')) {
        if (!defined('MX_PATH_AVATAR')) {
            // Pfad zu den Avataren anpassen
            define('MX_PATH_AVATAR', './');
        }
        if (isset($_POST['op']) && $_POST['op'] == 'saveuser' && isset($_POST['user_avatar'])) {
            unset($_POST['user_avatar']);
        }
    }
}

/**
 * pmxUserPage
 *
 * @package
 * @author tora60
 * @copyright Copyright (c) 2009
 * @version $Id: mx_userfunctions.php 6 2015-07-08 07:07:06Z PragmaMx $
 * @access public
 */
class pmxUserPage {
    private $_user = array();
    private $_modname = 'Your_Account';

    private $_options = array(/* Speicher fuer getter und setter... */
        'nav' => true,
        'tabname' => '',
        'title' => '',
        'subtitle' => '',
        'message' => '',
        'innertab' => '',
        'pagetitle' => '',
        );

    /**
     * pmxUserPage::__construct()
     *
     * @param array $userinfo
     */
    public function __construct($userinfo = false)
    {
        mxGetLangfile($this->_modname);

        switch (true) {
            case is_numeric($userinfo):
                $this->_user = mxGetUserDataFromUid($userinfo);
                break;
            case is_string($userinfo):
                $this->_user = mxGetUserDataFromUsername($userinfo);
                break;
            case is_array($userinfo):
                $this->_user = $userinfo;
                break;
        }
        /* remove nanomx
        if ($this->_user) {
            if ($this->_user['current']) {
                $this->title = _THISISYOURPAGE;
            } else {
                $this->title = $this->_user['uname'];
            }
        }*/
    }

    /**
     * pmxUserPage::fetch()
     *
     * @param mixed $content
     * @return string
     */
    public function fetch($content)
    {
        if (empty($this->_user['uid'])) {
            return '<div class="note">' . _SORRYNOUSERINFO . '</div>';
        }

        /* modulspezifisches Stylesheet einbinden */
        pmxHeader::add_style(PMX_MODULES_PATH . $this->_modname . '/style/style.css');

        $nav = ($this->nav) ? $this->nav() : '';
        $innernav = ($this->innertab) ? $this->innernav() : '';
        $user = $this->_user;

        $template = load_class('Template');
        $template->init_path(PMX_MODULES_DIR . DS . $this->_modname);
        $template->assign($this->_options);
        $template->assign(compact('user', 'nav', 'innernav', 'content'));

        return $template->fetch('profile.html');
    }

    /**
     * pmxUserPage::show()
     *
     * @param mixed $content
     * @return nothing
     */
    public function show($content)
    {
        if ($this->pagetitle) {
            $GLOBALS['pagetitle'] = $this->pagetitle;
        }
        include_once('header.php');
        echo $this->fetch($content);
        include_once('footer.php');
    }

    /**
     * pmxUserPage::nav()
     *
     * @param mixed $content
     * @return
     */
    public function nav()
    {
        $userinfo = $this->_user; // $userinfo für Core-Files !!
        switch (true) {
            case $this->innertab && !$this->subtitle && !$this->tabname:
                // innerhalb von Einstellungen immer gleich...
                $this->subtitle = _CHANGEHOME;
                $this->tabname = 'userdata';
                break;
            case !$this->tabname:
                $this->tabname = 'xy';
                break;
        }

        if ($this->_user['current']) {
            $hook = 'user.navbar';

            /* Definition der Menüpunkte */
            $item[] = array(/* Übersicht */
                'link' => 'modules.php?name=' . $this->_modname . '',
                'caption' => _YAOVERVIEW,
                'title' => _RETURNACCOUNT,
                'image' => PMX_MODULES_PATH . $this->_modname . '/images/user_gray.png',
                'tabname' => 'overview',
                );

            $item[] = array(/* Einstellungen */
                'link' => 'modules.php?name=' . $this->_modname . '&amp;op=edituser',
                'caption' => _CHANGEHOME,
                'image' => PMX_MODULES_PATH . $this->_modname . '/images/wrench_orange.png',
                'tabname' => 'userdata',
                );
        } else {
            $hook = 'user.visitmenu';

            switch ($this->_user['user_sexus']) {
                case 1:
                    $uimg = 'user_female.png';
                    break;
                case 2:
                    $uimg = 'user_male.png';
                    break;
                default:
                    $uimg = 'user.png';
            }

            /* Definition der Menuepunkte */
            $item[] = array(/* Uebersicht */
                'link' => 'modules.php?name=Userinfo&amp;uname=' . $this->_user['uname'],
                'caption' => _YAOVERVIEW,
                'image' => PMX_MODULES_PATH . $this->_modname . '/images/' . $uimg,
                'tabname' => 'overview',
                );
        }

        /* Modulspezifische Links auslesen und einbinden */
        $hook = load_class('Hook', $hook);
        $hook->set($this->_user);

        $hook->run($item);

        /* nur anzeigen wenn mehrere Tabs */
        if (count($item) < 2) {
            return;
        }
        /* Ausgabegenerierung */
        ob_start();

        ?>

<ul class="tabs-nav">
<?php foreach ($item as $i => $value) : ?>
    <?php $title = (empty($value['title'])) ? '' : ' title="' . $value['title'] . '"';
        $link = '<a href="' . $value['link'] . '" class="img utab-' . $i . '"' . $title . ' onfocus="this.blur()">' . $value['caption'] . '</a>';
        $css[] = '.tabs-nav a.utab-' . $i . '{background-image:url(' . $value['image'] . ')}';
        $current = ($value['tabname'] == $this->tabname) ? ' class="current"' : '';

        ?>
    <li<?php echo $current ?>><?php echo $link ?></li>
<?php endforeach ?>
</ul>
<?php
        pmxHeader::add_jquery();
        pmxHeader::add_style_code(implode('', $css));
        return ob_get_clean();
    }

    /**
     * pmxUserPage::__get()
     *
     * @param string $name
     * @return
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->_options)) {
            return $this->_options[$name];
        }
        $trace = debug_backtrace();
        trigger_error('undefined property \'' . $name . '\' in ' . mx_strip_sysdirs($trace[0]['file']) . ' line ' . $trace[0]['line'], E_USER_NOTICE);
        return null;
    }

    /**
     * pmxUserPage::__set()
     *
     * @param string $name
     * @param mixed $value
     * @return mixed
     */
    public function __set($name, $value)
    {
        $this->_options[$name] = $value;
    }

    /**
     * pmxUserPage::innernav()
     *
     * @return string
     */
    public function innernav()
    {
        /* Berechtigung wird für Tabs benötigt ;-) */
        $this->_userpic = load_class('Userpic');
        $pic_allowed_upload = permission_granted($this->_userpic->access_upload, $this->_user['groups']);
        $pic_allowed_avatar = permission_granted($this->_userpic->access_avatars, $this->_user['groups']);

        $item[] = array(/* Zugangsdaten */
            'link' => ($this->innertab == 'edituser') ? '#ya-access' : 'modules.php?name=Your_Account&amp;op=edituser&amp;tab=access',
            'caption' => _YA_ACCOUNTDATA,
            'tabname' => 'access',
            );
        if ($pic_allowed_avatar || $pic_allowed_upload) {
            $item[] = array(/* pers. Daten */
                'link' => ($this->innertab == 'edituser') ? '#ya-data' : 'modules.php?name=Your_Account&amp;op=edituser&amp;tab=data',
                'caption' => _PERSONALINFO,
                'tabname' => 'data',
                );
        }
        $item[] = array(/* Benutzerbild */
            'link' => ($this->innertab == 'edituser') ? '#ya-photo' : 'modules.php?name=Your_Account&amp;op=edituser&amp;tab=photo',
            'caption' => _UPIC_PIC,
            'tabname' => 'photo',
            );
        $item[] = array(/* Optionen */
            'link' => 'modules.php?name=Your_Account&amp;op=edithome',
            'caption' => _UTAB_OPTIONS,
            'tabname' => 'edithome',
            );

        /* Modulspezifische Links auslesen und einbinden */
        $hook = load_class('Hook', 'user.navbar.inner');
        $hook->set($this->_user);

        $hook->run($item);

        switch (true) {
            case $this->innertab != 'edituser':
            case !isset($_GET['tab']):
                $activetab = 0;
                break;
            case $_GET['tab'] == 'photo':
                $activetab = 3;
                break;
            case $_GET['tab'] == 'data':
                $activetab = 2;
                break;
            case $_GET['tab'] == 'access':
            default:
                $activetab = 1;
                break;
        }

        $template = load_class('Template');
        $template->init_path(PMX_MODULES_DIR . DS . $this->_modname);

        $template->assign(compact('item', 'activetab'));
        $template->assign('innertab', $this->innertab);

        return $template->fetch('nav.innertab.html');
    }
}

/**
 * Auslesen der gesperrten Namen
 */
function mxGetCensorListUsers()
{
    global $prefix, $CensorListUsers;
    $CensorListUsers = array($GLOBALS['anonymous'], 'anonymous', 'unbekannt'); // das alte array() aus der config.php überschreiben
    $result = sql_system_query("SELECT ban_val FROM " . $prefix . "_user_ban WHERE ban_type='ban_name'");
    while (list($ban_name) = sql_fetch_row($result)) {
        $CensorListUsers[] = $ban_name;
    }
    // die unerlaubten Benutzernamen und die zensierten Wörter als unerlaubte Benutzernamen definieren
    $CensorListUsers = array_merge($CensorListUsers, $GLOBALS['CensorList']);
    return array_map('strtolower', $CensorListUsers);
}

/**
 * Beschreibung
 */
function userNavigation($act = '')
{
    $item['login'] = '<a rel="nofollow" href="modules.php?name=Your_Account">' . _USERLOGIN . '</a>';
    $item['passlost'] = '<a rel="nofollow" href="modules.php?name=Your_Account&amp;op=pass_lost">' . _PASSWORDLOST . '</a>';
    if (mxModuleAllowed('User_Registration')) {
        $item['register'] = '<a rel="nofollow" href="modules.php?name=User_Registration">' . _REGNEWUSER . '</a>';
    }

    ?>
<ul class="tabs-nav">
<?php foreach ($item as $key => $link) : ?>
    <?php $current = ($act == $key) ? ' class="current"' : '' ?>
    <li<?php echo $current ?>><?php echo $link ?></li>
<?php endforeach ?>
</ul>
<?php
}

/**
 * Beschreibung
 */
function prepareUserdataFromRequest($pvs)
{
    global $user_prefix, $prefix;
    if (isset($pvs['uname'])) {
        $pvs['uname'] = trim(substr(preg_replace('~[\t\n\r\x0B\0\xA0]+~', ' ', $pvs['uname']), 0, 25)); // von SMF:
    }
    $pvs['email'] = (empty($pvs['email'])) ? '' : str_replace(" ", '', $pvs['email']);
    $pvs['user_sexus'] = (empty($pvs['user_sexus'])) ? 0 : (int)$pvs['user_sexus'];
    $pvs['url'] = (empty($pvs['url'])) ? '' : mxCutHTTP($pvs['url']);
    // Geburtstag pruefen, ggf. aendern
    switch (true) {
        case isset($pvs['birthday']):
            // falls bereits geprüft, ignorieren
            break;
        case !isset($pvs['birth'], $pvs['birth']['year'], $pvs['birth']['month'], $pvs['birth']['day']):
        case !is_array($pvs['birth']):
            // $pvs['user_bday'] = '';
            $pvs['birthday'] = false;
            break;
        case empty($pvs['birth']['pick']):
            // ohne Javascript
            $cyear_2 = date('y');
            $cyear_4 = date('Y');
            $year = intval($pvs['birth']['year']);
            $month = intval($pvs['birth']['month']);
            $day = intval($pvs['birth']['day']);
            switch (true) {
                case $pvs['birth']['year'] === '':
                    // kein Jahr ist kein Datum
                    $pvs['birthday'] = 0;
                    break;
                case !$day:
                case !$month:
                case !is_numeric($pvs['birth']['year']):
                case ($year >= 100) && ($year < 1000):
                    // unsinniges Datum
                    $pvs['birthday'] = false;
                    break;
                case $year == 0:
                    // exakt 0 ist j2k
                    $pvs['birthday'] = mktime (0, 0, 0, $month, $day, 2000);
                    break;
                case $year <= $cyear_2:
                    // zweistelliug, irgendwann nach j2k
                    $pvs['birthday'] = mktime (0, 0, 0, $month, $day, 2000 + $year);
                    break;
                case $year < 1000 && ($year > $cyear_2) && ($year < $cyear_4):
                    // zweistelliug, irgendwann vor j2k
                    $pvs['birthday'] = mktime (0, 0, 0, $month, $day, 1900 + $year);
                    break;
                default:
                    $pvs['birthday'] = mktime (0, 0, 0, $month, $day, $year);
            }
            break;
        case !empty($pvs['birth']['pick']) && !empty($pvs['birth']['picker']):
            // mit Javascript
            // Gibt im Erfolgsfall einen Timestamp, andernfalls FALSE zurück.
            $pvs['birthday'] = mkfromstrptime($pvs['birth']['picker'], _DATEPICKER);
            break;
        default:
            $pvs['birthday'] = 0;
    }

    unset($pvs['birth']);
    // Laengenbegrenzung, DB-Feld ist als tinytext deklariert
    if (isset($pvs['bio'])) {
        $pvs['bio'] = substr($pvs['bio'], 0, 255);
    }
    // Laengenbegrenzung, da im SMF nur 400 Zeichen zulaessig sind
    // ToDo: Wie sieht's damit im mxBoard aus??
    if (isset($pvs['user_sig'])) {
        $pvs['user_sig'] = substr($pvs['user_sig'], 0, 400);
    }
    return $pvs;
}

/**
 * Beschreibung
 */
function userCheck($pvs)
{
    global $user_prefix, $prefix;

    $userconfig = load_class('Userconfig');

    /* zuerst alle Werte in das richtige Format bringen */
    $pvs = prepareUserdataFromRequest($pvs);

    if (empty($pvs['email'])) {
        return _ERRORNOEMAIL;
    }
    if (!mxCheckEmail($pvs['email'])) {
        return _ERRORINVEMAIL;
    }

    if (pmx_is_mail_banned($pvs['email'])) {
        return _BLOCKEDMAIL;
    }

    if ($pvs['op'] == "confirm" || $pvs['op'] == "finish") {
        if (!isset($pvs['uname'])) {
            $pvs['uname'] = '';
        }
        $check = mxCheckNickname($pvs['uname']);
        if ($check !== true) {
            return $check;
        }

        $censorednames = mxGetCensorListUsers();
        if (in_array(strtolower($pvs['uname']), $censorednames)) {
            return _NAMERESERVED;
        }
        if (sql_num_rows(sql_query("SELECT email FROM {$user_prefix}_users WHERE email='" . mxAddSlashesForSQL($pvs['email']) . "'")) > 0) {
            return _EMAILREGISTERED;
        }
        if (sql_num_rows(sql_query("SELECT uname FROM {$user_prefix}_users WHERE uname='" . mxAddSlashesForSQL($pvs['uname']) . "'")) > 0) {
            return _NICKTAKEN;
        }
    }

    /* START Mindestalter / Geburtsdatum Pflichtfeld */
    /* Timestamp $pvs['birthday'] wird in prepareUserdataFromRequest() generiert */
    $birthday = (isset($pvs['birthday'])) ? $pvs['birthday'] : 0;
    $proofdate = intval($userconfig->yaproofdate);
    $birth = getdate(intval($pvs['birthday']));
    $birth = $birth['year'] + ($birth['yday'] / 1000);

    $proof = getdate();
    $proof = $proof['year'] - $proofdate + ($proof['yday'] / 1000);

    switch (true) {
        case $birthday === false: // ungültiges Datum
        case !$proofdate && $birth > $proof:
            // ungültiges Datum
            return _ERRFALSEDATE;
        case $proofdate && !$birthday:
            return _ERROREMPYBDATE;
        case $proofdate && $birth > $proof:
            // zu Jung..
            return sprintf(_ERRAPPROVEDATE, $proofdate);
    }

    /* START Benutzer muessen den AGB zustimmen */
    if ($pvs['op'] == "confirm" && $userconfig->agb_agree && empty($pvs['readrules'])) {
        return _NOTAGREE;
    }

    /* Laengenbegrenzung, DB-Feld ist als tinytext deklariert */
    if (isset($pvs['bio'])) {
        $pvs['bio'] = substr($pvs['bio'], 0, 255);
    }

    /* Laengenbegrenzung, da im SMF nur 400 Zeichen zulaessig sind */
    if (isset($pvs['user_sig'])) {
        // ToDo: Wie sieht's damit im mxBoard aus??
        $pvs['user_sig'] = substr($pvs['user_sig'], 0, 400);
    }

    if (function_exists('userCheck_option')) {
        $pvs = userCheck_option($pvs);
        if (!empty($pvs['userCheckError'])) {
            return $pvs['userCheckError'];
        }
    }
    return $pvs;
}

/**
 * Beschreibung
 */
function vkpUserIsThisTheUser($uid)
{
    $uinfo = mxGetUserDataFromUid($uid);
    return !empty($uinfo['current']);
}

/**
 * erwartet ein sql-Datum in der Form yyyy-mm-dd
 * deprecated !!!
 */
function vkpGetBdayString($bday)
{
    $timestamp = strtotime($bday);
    if ($timestamp) {
        return mx_strftime(_SHORTDATESTRING, $timestamp);
    } else {
        return '';
    }
}

/**
 * deprecated, pmx_password_create() verwenden!!
 */
function makePass()
{
    return pmx_password_create();
}

/**
 * Ermittelt alle Avatare, die fuer den entsprechenden user auswaehlbar sind
 * deprecated !!
 */
function vkpYaGetAvatars($uid = 0)
{
    $pici = load_class('Userpic');
    return $pici->get_available_avatars();
}

/**
 * Beschreibung
 */
function vkpUserform($pvs)
{
    global $uname;

    $userconfig = load_class('Userconfig');

    $pvs['uid'] = (empty($pvs['uid'])) ? 0 : $pvs['uid'];
    $pvs['uname'] = (empty($pvs['uname'])) ? '' : $pvs['uname'];
    $pvs['user_bday'] = (empty($pvs['user_bday'])) ? '0000-00-00' : $pvs['user_bday'];

    if (function_exists("vkpUserform_option")) {
        return vkpUserform_option($pvs);
    }
    $cbday = vkpBdaySelect($pvs['user_bday']);
    if ($userconfig->yaproofdate) {
        $cbday .= ' ' . _REQUIRED . '<br />' . sprintf(_ERRAPPROVEDATE, $userconfig->yaproofdate);
    }
    $out = '
<div class="control-group">
     <label>' . _UREALNAME . '</label>
    <input type="text" name="realname" size="50" maxlength="60" value="' . ((isset($pvs['realname'])) ? htmlspecialchars($pvs['realname']) : '') . '" />
</div>
<div class="control-group">
    <label>' . _YA_USEXUS . '</label>
    ' . vkpSexusSelect("user_sexus", (isset($pvs['user_sexus'])) ? $pvs['user_sexus'] : 0) . '
</div>
<div class="control-group">
    <label>' . _YA_UBDAY . '</label>
    ' . $cbday . '
</div>
<div class="control-group">
    <label>' . _YOURHOMEPAGE . '</label>
    <input type="text" name="url" size="50" maxlength="255" placeholder="http://" value="' . ((isset($pvs['url'])) ? htmlspecialchars($pvs['url']) : '') . '" />
    <span class="mx-form-message-inline">' . _OPTIONAL3 . '</span>
</div>
<div class="control-group">
    <label>' . _YLOCATION . ':</label>
    <input type="text" name="user_from" size="60" maxlength="100" value="' . ((isset($pvs['user_from'])) ? htmlspecialchars($pvs['user_from']) : '') . '" />
</div>
<div class="control-group">
    <label>' . _EXTRAINFO . ':</label>
    <textarea name="bio" rows="5" cols="60" placeholder="' . _CANKNOWABOUT . '">' . ((isset($pvs['bio'])) ? htmlspecialchars($pvs['bio'], ENT_QUOTES) : '') . '</textarea>
</div>
<div class="control-group">
    <label>' . _SIGNATURE . ':</label>
    <textarea name="user_sig" rows="6" cols="60" placeholder="' . _MAXICHARS . '">' . ((isset($pvs['user_sig'])) ? htmlspecialchars($pvs['user_sig'], ENT_QUOTES) : '') . '</textarea>
</div>';

    return $out;
}


/**
 * Formularfelder und Javascript für Geburtstagsauswahl
 */
function vkpBdaySelect($bday)
{
    $date = date_parse($bday);
    if (!$date['year'] || !$date['month'] || !$date['day']) {
        $date = false;
    }
    if ($date) {
        $birthday = strftime(_DATEPICKER, mktime(0, 0, 0, $date['month'], $date['day'], $date['year']));
    } else {
        $birthday = '';
        $date = array('year' => 0, 'month' => 0, 'day' => 0);
    }

    $selected = ' selected="selected" class="current"';

    $d[] = '<option value="0" ' . (($date['day']) ? '' : $selected) . '>-</option>';
    for ($i = 1; $i <= 31; $i++) {
        $d[] = '<option value="' . $i . '" ' . (($i == $date['day']) ? $selected : '') . '>' . $i . '</option>';
    }

    $m[] = '<option value="0" ' . (($date['month']) ? '' : $selected) . '>-</option>';
    for ($i = 1; $i <= 12; $i++) {
        $m[] = '<option value="' . $i . '" ' . (($i == $date['month']) ? $selected : '') . '>' . $i . '</option>';
    }

    ob_start();

    ?>
<div id="birth-fields">
<?php echo _YA_BDAY ?>: <select name="birth[day]"><?php echo implode("\n", $d) ?></select>
<?php echo _YA_BMONTH ?>: <select name="birth[month]"><?php echo implode("\n", $m) ?></select>
<?php echo _YA_BYEAR ?>: <input name="birth[year]" value="<?php echo $date['year'] ?>" size="5" maxlength="4" type="text" />
</div>

<input type="text" name="birth[picker]" id="birth-picker" size="50" maxlength="19" value="<?php echo $birthday ?>" style="width: 12ex" class="hide" />
<input type="hidden" name="birth[pick]" id="birth-picked" value="0" />

<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
  $('#birth-fields').hide();
  $('#birth-picked').val('1');
  $('#birth-picker').datepicker({
    changeMonth: true,
    changeYear: true,
    regional: '<?php echo _DOC_LANGUAGE ?>',
    yearRange: '<?php echo (date('Y')-100) ?>:<?php echo date('Y') ?>'
  }).show();
});
/* ]]> */
</script>
<?php
    pmxHeader::add_jquery('ui/jquery.ui.datepicker.js',
        'ui/i18n/jquery.ui.datepicker-' . _DOC_LANGUAGE . '.js'
        );

    $out = ob_get_clean();
    return $out;
}

/**
 * Beschreibung
 */
function vkpSexusSelect($fieldname, $sexus = 0, $hidenull = 0)
{
    $sexus = (empty($sexus)) ? 0 : (int)$sexus;
    $out = "<select name='" . $fieldname . "' size='1'>\n";
    if (!$hidenull) {
        $out .= "<option value='0'" . (($sexus == 0) ? ' selected="selected" class="current"' : '') . " >" . _YA_NOSEX . "</option>\n";
    }
    $out .= "<option value='1'" . (($sexus == 1) ? ' selected="selected" class="current"' : '') . " >" . _YA_FEMALE . "</option>\n";
    $out .= "<option value='2'" . (($sexus == 2) ? ' selected="selected" class="current"' : '') . " >" . _YA_MALE . "</option>\n";
    $out .= "</select>\n";
    return $out;
}

/**
 * Beschreibung
 */
function vkpGetSexusString($sexus = 0)
{
    $sexus = (empty($sexus)) ? 0 : (int)$sexus;
    switch ($sexus) {
        case 1:
            return _YA_FEMALE . " " . mxCreateImage("images/f.gif", _YA_FEMALE);
        case 2:
            return _YA_MALE . " " . mxCreateImage("images/m.gif", _YA_MALE);
        case 0:
        default:
            return _YA_NOSEX;
    }
}

/**
 * Beschreibung
 */
function vkpYaIsUblockActive()
{
    global $prefix;

    if (!MX_IS_USER) {
        return false;
    }

    $result = sql_system_query("SELECT bid, view FROM " . $prefix . "_blocks WHERE active=1 AND (bkey='userbox' OR blockfile='block-Userblock.php')");
    list($bid, $view) = sql_fetch_row($result);

    switch (true) {
        case !$bid: // Block nicht aktiv
            return false;
        case 4 == $view:
            return (MX_IS_ADMIN && mxGetAdminPref('radminsuper'));
        case 2 == $view:
            return MX_IS_ADMIN;
        case 1 == $view && MX_IS_ADMIN: // Usergruppen und Admin
            return true;
        case 1 == $view: // Usergruppen
            $userinfo = mxGetUserData();
            $qry = "SELECT block_id FROM ${prefix}_groups_blocks
                    WHERE (group_id=" . intval($userinfo['user_ingroup']) . "
                    AND block_id=" . intval($bid) . ")";
            $result = sql_system_query($qry);
            list($bid) = sql_fetch_row($result);
            return (bool)$bid;
        default:
            return false;
    }
}

/**
 * Beschreibung
 */
function vkpYaGetOptionLangfile()
{
    /* optionale Sprachdatei einbinden */
    return mxGetLangfile('Your_Account', 'option-*.php');
}

/**
 * mxCheckUserTempTable()
 *
 * @return
 */
function mxCheckUserTempTable()
{
    global $prefix, $user_prefix;

    $result = sql_query("SHOW COLUMNS FROM `{$user_prefix}_users`");
    while ($row = sql_fetch_assoc($result)) {
        $user_fields[$row['Field']] = $row;
    }
    ksort($user_fields);
    $result = sql_system_query("SHOW COLUMNS FROM `{$user_prefix}_users_temptable`");
    while ($row = sql_fetch_assoc($result)) {
        $temp_fields[$row['Field']] = $row;
    }
    unset($temp_fields['check_key'], $temp_fields['ncheck_time'], $temp_fields['check_ip'], $temp_fields['check_host'], $temp_fields['check_thepss'], $temp_fields['check_isactive']);
    ksort($temp_fields);
    // wenn die beiden Arrays gleich sind, die Funktion beenden
    if ($temp_fields == $user_fields) {
        return true;
    }
    // alle Felder der alten Temp-Table auslesen
    $result = sql_system_query("SHOW COLUMNS FROM `{$user_prefix}_users_temptable`");
    while (list($fieldname) = sql_fetch_row($result)) {
        // nur wenn bereits das entsprechende Feld auch in der neuen Tabelle existiert..
        if (isset($new_fields[$fieldname])) {
            $old_fields[$fieldname] = $fieldname;
        }
    }
    // eindeutiger Name für temporäre Tabelle
    $temptable = uniqid('temp_tbl_');
    // die zusätzlichen Felder für die temporäre Tabelle
    $createfields = "ALTER TABLE `$temptable`
    ADD `check_key` int(5) NOT NULL default 0,
    ADD `check_time` int(11) NOT NULL default 0,
    ADD `check_ip` varchar(16) NOT NULL default '',
    ADD `check_host` varchar(60) NOT NULL default '',
    ADD `check_thepss` varchar(40) NOT NULL default '',
    ADD `check_isactive` tinyint(1) NOT NULL default 0
    ";
    // Struktur der Usertabelle auslesen
    list($tname, $tcreate) = sql_fetch_row(sql_query("SHOW CREATE TABLE `{$user_prefix}_users`;"));
    // Die Struktur bearbeiten, Tabellennamen ersetzen
    $tcreate = preg_replace('#(CREATE\sTABLE\s`?)(' . $tname . ')(`?\s)#i', '$1' . $temptable . '$3', $tcreate);
    // falls temporäre Tabelle existiert, diese löschen
    sql_system_query("DROP TABLE IF EXISTS `$temptable`;");
    // neue temporäre Tabelle erstellen
    sql_system_query($tcreate);
    // die zusätzlichen Felder in der neuen temporäre Tabelle erstellen
    sql_system_query($createfields);
    // prüfen, ob Neuanmeldungen in der alten temporären Tabelle vorhanden sind
    list($newcount) = sql_fetch_row(sql_query("SELECT COUNT(uid) FROM `{$user_prefix}_users_temptable`"));
    // wenn ja, diese in die neue Tabelle importieren
    if ($newcount) {
        // alle Felder der neuen Temp-Table auslesen
        $qry = "SHOW COLUMNS FROM `${temptable}`;";
        $result = sql_system_query($qry);
        while (list($fieldname) = sql_fetch_row($result)) {
            $new_fields[$fieldname] = $fieldname;
        }
        // alle Felder der alten Temp-Table auslesen
        $qry = "SHOW COLUMNS FROM `{$user_prefix}_users_temptable`;";
        $result = sql_system_query($qry);
        while (list($fieldname) = sql_fetch_row($result)) {
            // nur wenn bereits das entsprechende Feld auch in der neuen Tabelle existiert..
            if (isset($new_fields[$fieldname])) {
                $old_fields[$fieldname] = $fieldname;
            }
        }
        // die Arrays noch umgekehrt abgleichen und daraus neues Array erstellen,
        // welches nur Feldnamen enthält, die in beiden Tabellen vorkommen
        foreach ($new_fields as $fieldname) {
            // nur wenn bereits das entsprechende Feld auch in der alten Tabelle existiert..
            if (isset($old_fields[$fieldname])) {
                $change_fields[$fieldname] = $fieldname;
            }
        }

        if (isset($change_fields)) {
            $change_fields = '`' . implode('`,`', $change_fields) . '`';
            // die Insertanweisung zusammensetzen
            $qry = "INSERT INTO `$temptable` (" . $change_fields . ") SELECT " . $change_fields . " FROM `{$user_prefix}_users_temptable`;";
            // Insertanweisung ausführen
            sql_system_query($qry);
        }
    }
    // die alte temporäre Tabelle löschen
    sql_system_query("DROP TABLE IF EXISTS `{$user_prefix}_users_temptable`;");
    // die neue Tabelle umbenennen zum Namen der alten...
    sql_system_query("ALTER TABLE `$temptable` RENAME `{$user_prefix}_users_temptable` ;");
    // Aufräumen falls temporäre Tabelle existiert, diese löschen
    sql_system_query("DROP TABLE IF EXISTS `$temptable`;");
}

/**
 * pmx_user_defaults()
 * Einen leeren Datensatz aus der usertabelle auslesen für Standardwerte
 *
 * @return array ()
 */
function pmx_user_defaults()
{
    global $user_prefix;
    $result = sql_query("SELECT * FROM `{$user_prefix}_users` LIMIT 1");
    $row = sql_fetch_assoc($result);
    foreach ($row as $key => $value) {
        $defaults[$key] = null;
    }
    /* bestimmte Felder gesondert behandeln */
    $defaults['uid'] = 0; // sollte numerisch sein...
    $defaults['pass_salt'] = pmx_password_salt(); // soll nicht leer sein, wegen md5-Check
    $defaults['realname'] = null; // zusätzlich, wird oft in Formularen anstatt "name" verwendet
    return $defaults;
}

/**
 * pmx_user_setlogin()
 * Das schreiben der Logindaten in Session und Cookies
 *
 * @param mixed $data
 * @param mixed $login_hook
 * @return
 */
function pmx_user_setlogin($data, $login_hook = true)
{
    /* mit Standardwerten sicherstellen, dass alle Felder vorhanden sind */
    $data = array_merge(pmx_user_defaults(), $data);

    mxSetUserSession($data['uid'], $data['uname'], $data['pass'], $data['storynum'], $data['umode'], $data['uorder'], $data['thold'], $data['noscore'], $data['ublockon'], $data['theme'], $data['commentmax']);

    if ($login_hook) {
        /* Modulspezifische Logins durchfuehren */
        pmx_run_hook('user.login', $data['uid']);
    }
}

/**
 * mxSetUserSession()
 *
 * setzt die benötigten User-Informationen in die Session
 * bei Bedarf auch in den alten nuke-usercookie
 * Reihenfolge der Argumente:
 * uid, uname, pass, storynum, umode, uorder, thold, noscore, ublockon, theme, commentmax
 *
 * @return
 */
function mxSetUserSession()
{
    $args = func_get_args();
    $info = implode(':', $args);
    $info = base64_encode($info);
    mxSessionSetVar('user', $info);
    mxSessionSetVar('lasttime', 0); // online() ausführen
    mxSessionSetVar('user_uid', intval($args[0]));
    mxSessionSetVar('user_uname', $args[1]);
    mxSessionSafeCookie(MX_SAFECOOKIE_NAME_USER, 1);
   // mxSetNukeCookie('user', $info, 1);
}

?>