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

define('PMX_YA_EXTENDED', true);

/**
 * Info
 * hier kann die Ueberpruefung der Benutzerdaten erweitert werden
 * es stehen alle uebergebenen Formularvariablen im array $pvs zur Verfuegung
 * die entsprechende Fehlermeldung muss in der Variablen $pvs['userCheckError'] gespeichert werden
 */
function userCheck_option($pvs)
{
    /* Änderungen über den Adminbereich ignorieren */
    switch (true) {
        case !isset($pvs['op']):
            break;
        case $pvs['op'] == 'users/update':
        case $pvs['op'] == 'users/add':
        case $pvs['op'] == 'updateUser':
        case $pvs['op'] == 'addUser':
            return $pvs;
    }

    /* diese Variable mit der Fehlermeldung belegen
     * wenn unvollständige oder unkorrekte Daten
     */
    $pvs['userCheckError'] = '';

    /* ein einfaches Beispiel dazu: */
    if (empty($pvs['realname']) && empty($pvs['name'])) {
        $pvs['userCheckError'] .= "Du musst noch Deinen richtigen Namen angeben.<br />";
    }
    /* ende Beispiel */

    return $pvs;
}

/**
 * Info
 * Optionaler Ersatz der Funktion vkpUserform()
 * Das angezeigte Formular kann hier beliebig erweitert oder gekuerzt werden
 */
function vkpUserform_option($pvs)
{
    $userconfig = load_class('Userconfig');

    /* optionale Sprachdatei einbinden */
    mxGetLangfile('Your_Account', 'option-*.php');

    $out = ""; /// Ausgabe initialisieren
    // mxDebugFuncVars($pvs);
    // / diese Zeilen stammen direkt von der Original Funktion
    // / und sollten, wenn die Felder verwendet werden nicht veraendert werden
    $cbday = vkpBdaySelect($pvs['user_bday']);
    // / ENDE: diese Zeilen stammen direkt von der Original Funktion
    // / YA Erweiterung von _Gerry_ mit kleinen Anpassungen
    $out .= "<tr valign=\"top\"><td colspan=\"4\" class=\"bgcolor2\"><br /><h4>" . _WERWAS . "</h4></td></tr>"
     . "<tr valign=\"top\"><td width=\"20%\" class=\"bgcolor2\"><b>" . _UREALNAME . ":</b></td><td colspan=\"3\" width=\"80%\" class=\"bgcolor3\">\n"
     . "<input type=\"text\" name=\"realname\" size=\"50\" maxlength=\"60\" value=\"" . ((isset($pvs['realname'])) ? mxPrepareToDisplay($pvs['realname']) : "") . "\" /></td></tr>\n"
     . "<tr valign=\"top\"><td class=\"bgcolor2\"><b>" . _YOCCUPATION . ":</b></td><td colspan=\"3\" class=\"bgcolor3\">\n"
     . "<input type=\"text\" name=\"user_occ\" size=\"60\" maxlength=\"100\" value=\"" . ((isset($pvs['user_occ'])) ? mxPrepareToDisplay($pvs['user_occ']) : "") . "\" /></td></tr>\n"
     . "<tr valign=\"top\"><td class=\"bgcolor2\"><b>" . _YLOCATION . ":</b></td><td class=\"bgcolor3\">\n"
     . "<input type=\"text\" name=\"user_from\" size=\"20\" maxlength=\"100\" value=\"" . ((isset($pvs['user_from'])) ? mxPrepareToDisplay($pvs['user_from']) : "") . "\" /></td><td class=\"bgcolor2\"><b>" . _YLOCATION1 . ":</b></td><td class=\"bgcolor3\">\n"

     . "<select name=\"user_from1\" size=\"1\">

  <option value=\"" . ((isset($pvs['user_from1'])) ? mxPrepareToDisplay($pvs['user_from1']) : '') . "\">" . ((isset($pvs['user_from1'])) ? mxPrepareToDisplay($pvs['user_from1']) : '&nbsp;') . "</option>
  <option value=\"" . _YA_DENMARK . "\">" . _YA_DENMARK . "</option>
  <option value=\"" . _YA_ENGLAND . "\">" . _YA_ENGLAND . "</option>
  <option value=\"" . _YA_FRANCE . "\">" . _YA_FRANCE . "</option>
  <option value=\"" . _YA_GERMANY . "\">" . _YA_GERMANY . "</option>
  <option value=\"" . _YA_SPAIN . "\">" . _YA_SPAIN . "</option>
  <option value=\"" . _YA_TURKEY . "\">" . _YA_TURKEY . "</option>
  <option value=\"" . _YA_ANOTHERCOUNTRY . "\">" . _YA_ANOTHERCOUNTRY . "</option>
  </select></td></tr>\n"

     . "<tr valign=\"top\"><td width=\"20%\" class=\"bgcolor2\"><b>" . _YA_UBDAY . ":</b></td><td colspan=\"3\" width=\"80%\" class=\"bgcolor3\">\n"
     . $cbday . "</td></tr>\n"
     . "<tr valign=\"top\"><td width=\"20%\" class=\"bgcolor2\"><b>" . _YA_ICHBIN . ":</b></td><td colspan=\"3\" width=\"80%\" class=\"bgcolor3\">\n"
     . vkpSexusSelect("user_sexus", (isset($pvs['user_sexus'])) ? $pvs['user_sexus'] : 0) . "</td></tr>\n"

     . "<tr valign=\"top\"><td class=\"bgcolor2\"><b>" . _YA_STATUS . ":</b></td><td colspan=\"3\" class=\"bgcolor3\">\n"
     . "<select name=\"user_famstatus\" size=\"1\">

  <option value=\"" . ((isset($pvs['user_famstatus'])) ? mxPrepareToDisplay($pvs['user_famstatus']) : '') . "\">" . ((isset($pvs['user_famstatus'])) ? mxPrepareToDisplay($pvs['user_famstatus']) : '&nbsp;') . "</option>
  <option value=\"" . _YA_FAMSTAT . "\">" . _YA_FAMSTAT . "</option>
  <option value=\"" . _YA_FAMSTAT1 . "\">" . _YA_FAMSTAT1 . "</option>
  <option value=\"" . _YA_FAMSTAT2 . "\">" . _YA_FAMSTAT2 . "</option>
  <option value=\"" . _YA_FAMSTAT3 . "\">" . _YA_FAMSTAT3 . "</option>
  <option value=\"" . _YA_FAMSTAT4 . "\">" . _YA_FAMSTAT4 . "</option>
  </select></td></tr>\n"
     . "<tr valign=\"top\"><td class=\"bgcolor2\"><b>" . _YA_KINDER . ":</b></td><td colspan=\"3\" class=\"bgcolor3\">\n"
     . "<select name=\"user_kinder\" size=\"1\">

  <option value=\"" . ((isset($pvs['user_kinder'])) ? mxPrepareToDisplay($pvs['user_kinder']) : '') . "\">" . ((isset($pvs['user_kinder'])) ? mxPrepareToDisplay($pvs['user_kinder']) : '&nbsp;') . "</option>
  <option value=\"" . _YA_KIND . "\">" . _YA_KIND . "</option>
  <option value=\"" . _YA_KIND1 . "\">" . _YA_KIND1 . "</option>
  <option value=\"" . _YA_KIND2 . "\">" . _YA_KIND2 . "</option>
  <option value=\"" . _YA_KIND3 . "\">" . _YA_KIND3 . "</option>
  <option value=\"" . _YA_KIND4 . "\">" . _YA_KIND4 . "</option>
  <option value=\"" . _YA_KIND5 . "\">" . _YA_KIND5 . "</option>
  <option value=\"" . _YA_KIND6 . "\">" . _YA_KIND6 . "</option>
  </select></td></tr>\n"
     . "<tr valign=\"top\"><td class=\"bgcolor2\"><b>" . _YGROESSE . ":</b></td><td width=\"20%\" class=\"bgcolor3\">\n"
     . "<input type=\"text\" name=\"user_heigh\" size=\"3\" maxlength=\"3\" value=\"" . ((isset($pvs['user_heigh'])) ? mxPrepareToDisplay($pvs['user_heigh']) : "") . "\" />&nbsp;" . _YCM . "</td>\n"
     . "<td width=\"15%\" class=\"bgcolor2\"><b>" . _YGEWICHT . ":</b></td><td class=\"bgcolor3\">\n"
     . "<input type=\"text\" name=\"user_gewicht\" size=\"3\" maxlength=\"3\" value=\"" . ((isset($pvs['user_gewicht'])) ? mxPrepareToDisplay($pvs['user_gewicht']) : "") . "\" />&nbsp;" . _YKG . "</td></tr>\n"
     . "<tr valign=\"top\"><td class=\"bgcolor2\"><b>" . _YHAAR . ":</b></td><td width=\"20%\" class=\"bgcolor3\">\n"
     . "<input type=\"text\" name=\"user_haar\" size=\"10\" maxlength=\"30\" value=\"" . ((isset($pvs['user_haar'])) ? mxPrepareToDisplay($pvs['user_haar']) : "") . "\" /></td>\n"
     . "<td width=\"15%\" class=\"bgcolor2\"><b>" . _YAUGEN . ":</b></td><td class=\"bgcolor3\">\n"
     . "<input type=\"text\" name=\"user_augen\" size=\"10\" maxlength=\"30\" value=\"" . ((isset($pvs['user_augen'])) ? mxPrepareToDisplay($pvs['user_augen']) : "") . "\" /></td></tr>\n"
     . "<tr valign=\"top\"><td class=\"bgcolor2\"><b>" . _YA_SMOKER . ":</b></td><td class=\"bgcolor3\">\n"
     . "<select name=\"user_smoke\" size=\"1\">
  <option value=\"" . ((isset($pvs['user_smoke'])) ? mxPrepareToDisplay($pvs['user_smoke']) : '') . "\">" . ((isset($pvs['user_smoke'])) ? mxPrepareToDisplay($pvs['user_smoke']) : '&nbsp;') . "</option>
  <option value=\"" . _YA_KA . "\">" . _YA_KA . "</option>
  <option value=\"" . _YA_JA . "\">" . _YA_JA . "</option>
  <option value=\"" . _YA_NEIN . "\">" . _YA_NEIN . "</option>
  <option value=\"" . _YA_GELEGENTLICH . "\">" . _YA_GELEGENTLICH . "</option>
  </select></td><td class=\"bgcolor2\"><b>" . _YA_ALKOHOL . ":</b></td><td class=\"bgcolor3\">\n"
     . "<select name=\"user_alkohol\" size=\"1\">
  <option value=\"" . ((isset($pvs['user_alkohol'])) ? mxPrepareToDisplay($pvs['user_alkohol']) : '') . "\">" . ((isset($pvs['user_alkohol'])) ? mxPrepareToDisplay($pvs['user_alkohol']) : '&nbsp;') . "</option>
  <option value=\"" . _YA_KA . "\">" . _YA_KA . "</option>
  <option value=\"" . _YA_OFT . "\">" . _YA_OFT . "</option>
  <option value=\"" . _YA_GELEGENTLICH . "\">" . _YA_GELEGENTLICH . "</option>
  <option value=\"" . _YA_NIE . "\">" . _YA_NIE . "</option>
  </select></td></tr>\n"
     . "<tr valign=\"top\"><td class=\"bgcolor2\"><b>" . _YA_PIERCING . ":</b></td><td class=\"bgcolor3\">\n"
     . "<select name=\"user_piercing\" size=\"1\">
  <option value=\"" . ((isset($pvs['user_piercing'])) ? mxPrepareToDisplay($pvs['user_piercing']) : '') . "\">" . ((isset($pvs['user_piercing'])) ? mxPrepareToDisplay($pvs['user_piercing']) : '&nbsp;') . "</option>
  <option value=\"" . _YA_KA . "\">" . _YA_KA . "</option>
  <option value=\"" . _YA_JA . "\">" . _YA_JA . "</option>
  <option value=\"" . _YA_NEIN . "\">" . _YA_NEIN . "</option>
  <option value=\"" . _YA_NOCHNICHT . "\">" . _YA_NOCHNICHT . "</option>
  </select></td><td class=\"bgcolor2\"><b>" . _YA_TATTO . ":</b></td><td class=\"bgcolor3\">\n"
     . "<select name=\"user_tatto\" size=\"1\">
  <option value=\"" . ((isset($pvs['user_tatto'])) ? mxPrepareToDisplay($pvs['user_tatto']) : '') . "\">" . ((isset($pvs['user_tatto'])) ? mxPrepareToDisplay($pvs['user_tatto']) : '&nbsp;') . "</option>
  <option value=\"" . _YA_KA . "\">" . _YA_KA . "</option>
  <option value=\"" . _YA_JA . "\">" . _YA_JA . "</option>
  <option value=\"" . _YA_NEIN . "\">" . _YA_NEIN . "</option>
  <option value=\"" . _YA_NOCHNICHT . "\">" . _YA_NOCHNICHT . "</option>
  </select></td></tr>\n"

     . "<tr valign=\"top\"><td width=\"20%\" class=\"bgcolor2\"><b>" . _YA_LIEBLINGS . "</b></td><td colspan=\"3\" width=\"80%\" class=\"bgcolor3\">\n"
     . "</td></tr>\n"

     . "<tr valign=\"top\"><td class=\"bgcolor2\"><b>" . _YA_L1 . ":</b></td><td width=\"20%\" class=\"bgcolor3\">\n"
     . "<input type=\"text\" name=\"user_l1\" size=\"20\" maxlength=\"30\" value=\"" . ((isset($pvs['user_l1'])) ? mxPrepareToDisplay($pvs['user_l1']) : "") . "\" /></td>\n"
     . "<td width=\"15%\" class=\"bgcolor2\"><b>" . _YA_L2 . ":</b></td><td class=\"bgcolor3\">\n"
     . "<input type=\"text\" name=\"user_l2\" size=\"20\" maxlength=\"30\" value=\"" . ((isset($pvs['user_l2'])) ? mxPrepareToDisplay($pvs['user_l2']) : "") . "\" /></td></tr>\n"

     . "<tr valign=\"top\"><td class=\"bgcolor2\"><b>" . _YA_L3 . ":</b></td><td width=\"20%\" class=\"bgcolor3\">\n"
     . "<input type=\"text\" name=\"user_l3\" size=\"20\" maxlength=\"30\" value=\"" . ((isset($pvs['user_l3'])) ? mxPrepareToDisplay($pvs['user_l3']) : "") . "\" /></td>\n"
     . "<td width=\"15%\" class=\"bgcolor2\"><b>" . _YA_L4 . ":</b></td><td class=\"bgcolor3\">\n"
     . "<input type=\"text\" name=\"user_l4\" size=\"20\" maxlength=\"30\" value=\"" . ((isset($pvs['user_l4'])) ? mxPrepareToDisplay($pvs['user_l4']) : "") . "\" /></td></tr>\n"

     . "<tr valign=\"top\"><td class=\"bgcolor2\"><b>" . _YA_L5 . ":</b></td><td width=\"20%\" class=\"bgcolor3\">\n"
     . "<input type=\"text\" name=\"user_l5\" size=\"20\" maxlength=\"30\" value=\"" . ((isset($pvs['user_l5'])) ? mxPrepareToDisplay($pvs['user_l5']) : "") . "\" /></td>\n"
     . "<td width=\"15%\" class=\"bgcolor2\"><b>" . _YA_L6 . ":</b></td><td class=\"bgcolor3\">\n"
     . "<input type=\"text\" name=\"user_l6\" size=\"20\" maxlength=\"30\" value=\"" . ((isset($pvs['user_l6'])) ? mxPrepareToDisplay($pvs['user_l6']) : "") . "\" /></td></tr>\n"

     . "<tr valign=\"top\"><td class=\"bgcolor2\"><b>" . _YA_L7 . ":</b></td><td width=\"20%\" class=\"bgcolor3\">\n"
     . "<input type=\"text\" name=\"user_l7\" size=\"20\" maxlength=\"30\" value=\"" . ((isset($pvs['user_l7'])) ? mxPrepareToDisplay($pvs['user_l7']) : "") . "\" /></td>\n"
     . "<td width=\"15%\" class=\"bgcolor2\"><b>" . _YA_L8 . ":</b></td><td class=\"bgcolor3\">\n"
     . "<input type=\"text\" name=\"user_l8\" size=\"20\" maxlength=\"30\" value=\"" . ((isset($pvs['user_l8'])) ? mxPrepareToDisplay($pvs['user_l8']) : "") . "\" /></td></tr>\n"
     . "<tr valign=\"top\"><td class=\"bgcolor2\"><b>" . _YA_POSITIV . ":</b></td><td colspan=\"3\" class=\"bgcolor3\">\n"
     . "<input type=\"text\" name=\"user_positiv\" size=\"60\" maxlength=\"250\" value=\"" . ((isset($pvs['user_positiv'])) ? mxPrepareToDisplay($pvs['user_positiv']) : "") . "\" /></td></tr>\n"
     . "<tr valign=\"top\"><td class=\"bgcolor2\"><b>" . _YA_NEGATIV . ":</b></td><td colspan=\"3\" class=\"bgcolor3\">\n"
     . "<input type=\"text\" name=\"user_negativ\" size=\"60\" maxlength=\"250\" value=\"" . ((isset($pvs['user_negativ'])) ? mxPrepareToDisplay($pvs['user_negativ']) : "") . "\" /></td></tr>\n"
     . "<tr valign=\"top\"><td class=\"bgcolor2\"><b>" . _YINTERESTS1 . ":</b></td><td colspan=\"3\" class=\"bgcolor3\">\n"
     . "<input type=\"text\" name=\"user_intrest\" size=\"60\" maxlength=\"150\" value=\"" . ((isset($pvs['user_intrest'])) ? mxPrepareToDisplay($pvs['user_intrest']) : "") . "\" /></td></tr>\n"
     . "<tr valign=\"top\"><td class=\"bgcolor2\"><b>" . _EXTRAINFO . ":</b></td><td colspan=\"3\" class=\"bgcolor3\">"
     . "<textarea name=\"bio\" rows=\"5\" cols=\"57\">" . ((isset($pvs['bio'])) ? htmlspecialchars($pvs['bio'], ENT_QUOTES) : "") . "</textarea><br /><span class=\"tiny\">" . _CANKNOWABOUT . "</span></td></tr>\n"
     . "<tr valign=\"top\"><td colspan=\"4\" class=\"bgcolor2\"><br /><h4>" . _WASSUCHSTDU . "</h4></td></tr>"
     . "<tr valign=\"top\"><td class=\"bgcolor2\"><b>" . _YA_SUCHT . ":</b></td><td class=\"bgcolor3\">\n"
     . "<select name=\"user_sucht\" size=\"1\">
  <option value=\"" . ((isset($pvs['user_sucht'])) ? mxPrepareToDisplay($pvs['user_sucht']) : '') . "\">" . ((isset($pvs['user_sucht'])) ? mxPrepareToDisplay($pvs['user_sucht']) : '&nbsp;') . "</option>
  <option value=\"" . _YA_MANN . "\">" . _YA_MANN . "</option>
  <option value=\"" . _YA_FRAU . "\">" . _YA_FRAU . "</option>
  <option value=\"" . _YA_PAAR . "\">" . _YA_PAAR . "</option>
  <option value=\"" . _YA_MENSCHEN . "\">" . _YA_MENSCHEN . "</option>
  </select></td><td class=\"bgcolor2\"><b>" . _YA_BEZIEHUNG . ":</b></td><td class=\"bgcolor3\">\n"
     . "<select name=\"user_beziehung\" size=\"1\">

  <option value=\"" . ((isset($pvs['user_beziehung'])) ? mxPrepareToDisplay($pvs['user_beziehung']) : '') . "\">" . ((isset($pvs['user_beziehung'])) ? mxPrepareToDisplay($pvs['user_beziehung']) : '&nbsp;') . "</option>
  <option value=\"" . _YA_BZ1 . "\">" . _YA_BZ1 . "</option>
  <option value=\"" . _YA_BZ2 . "\">" . _YA_BZ2 . "</option>
  <option value=\"" . _YA_BZ3 . "\">" . _YA_BZ3 . "</option>
  <option value=\"" . _YA_BZ4 . "\">" . _YA_BZ4 . "</option>
  <option value=\"" . _YA_BZ5 . "\">" . _YA_BZ5 . "</option>
  <option value=\"" . _YA_BZ6 . "\">" . _YA_BZ6 . "</option>
  </select></td></tr>\n"

     . "<tr valign=\"top\"><td class=\"bgcolor2\"><b>" . _YA_IMALTER . ":</b></td><td colspan=\"3\" class=\"bgcolor3\">\n"
     . "<input type=\"text\" name=\"user_partneralter1\" size=\"2\" maxlength=\"2\" value=\"" . ((isset($pvs['user_partneralter1'])) ? mxPrepareToDisplay($pvs['user_partneralter1']) : "") . "\" />&nbsp;bis&nbsp;<input type=\"text\" name=\"user_partneralter2\" size=\"2\" maxlength=\"2\" value=\"" . ((isset($pvs['user_partneralter2'])) ? mxPrepareToDisplay($pvs['user_partneralter2']) : "") . "\" /></td></tr>\n"

     . "<tr valign=\"top\"><td class=\"bgcolor2\"><b>" . _YA_FIGUR . ":</b></td><td colspan=\"3\" class=\"bgcolor3\">\n"
     . "<select name=\"user_partnerfigur\" size=\"1\">

  <option value=\"" . ((isset($pvs['user_partnerfigur'])) ? mxPrepareToDisplay($pvs['user_partnerfigur']) : '') . "\">" . ((isset($pvs['user_partnerfigur'])) ? mxPrepareToDisplay($pvs['user_partnerfigur']) : '&nbsp;') . "</option>
  <option value=\"" . _YA_F . "\">" . _YA_F . "</option>
  <option value=\"" . _YA_F1 . "\">" . _YA_F1 . "</option>
  <option value=\"" . _YA_F2 . "\">" . _YA_F2 . "</option>
  <option value=\"" . _YA_F3 . "\">" . _YA_F3 . "</option>
  <option value=\"" . _YA_F4 . "\">" . _YA_F4 . "</option>
  <option value=\"" . _YA_F5 . "\">" . _YA_F5 . "</option>
  <option value=\"" . _YA_F6 . "\">" . _YA_F6 . "</option>
  </select></td></tr>\n"

     . "<tr valign=\"top\"><td class=\"bgcolor2\"><b>" . _EXTRAINFO1 . ":</b></td><td colspan=\"3\" class=\"bgcolor3\">"
     . "<textarea name=\"user_partnerwie\" rows=\"5\" cols=\"57\">" . ((isset($pvs['user_partnerwie'])) ? htmlspecialchars($pvs['user_partnerwie'], ENT_QUOTES) : "") . "</textarea><br /><span class=\"tiny\">" . _CANKNOWABOUT1 . "</span></td></tr>\n"
     . "<tr valign=\"top\"><td colspan=\"4\" class=\"bgcolor2\"><br /><h4>" . _WIEKONTAKT . "</h4></td></tr>"
     . "<tr valign=\"top\"><td class=\"bgcolor2\"><b>" . _YICQ . ":</b></td><td width=\"20%\" class=\"bgcolor3\">\n"
     . "<input type=\"text\" name=\"user_icq\" size=\"20\" maxlength=\"50\" value=\"" . ((isset($pvs['user_icq'])) ? mxPrepareToDisplay($pvs['user_icq']) : "") . "\" /></td>\n"
     . "<td width=\"15%\" class=\"bgcolor2\"><b>" . _YAIM . ":</b></td><td class=\"bgcolor3\">\n"
     . "<input type=\"text\" name=\"user_aim\" size=\"20\" maxlength=\"100\" value=\"" . ((isset($pvs['user_aim'])) ? mxPrepareToDisplay($pvs['user_aim']) : "") . "\" /></td></tr>\n"
     . "<tr valign=\"top\"><td class=\"bgcolor2\"><b>" . _YYIM . ":</b></td><td class=\"bgcolor3\">\n"
     . "<input type=\"text\" name=\"user_yim\" size=\"20\" maxlength=\"100\" value=\"" . ((isset($pvs['user_yim'])) ? mxPrepareToDisplay($pvs['user_yim']) : "") . "\" /></td>\n" . "<td class=\"bgcolor2\"><b>" . _YMSNM . ":</b></td><td class=\"bgcolor3\">"
     . "<input type=\"text\" name=\"user_msnm\" size=\"20\" maxlength=\"100\" value=\"" . ((isset($pvs['user_msnm'])) ? mxPrepareToDisplay($pvs['user_msnm']) : "") . "\" /></td></tr>\n"
     . "<tr valign=\"top\"><td class=\"bgcolor2\"><b>" . _YOURHOMEPAGE . ":</b></td><td colspan=\"3\" class=\"bgcolor3\">\n"
     . "<input type=\"text\" name=\"url\" size=\"50\" maxlength=\"255\" value=\"" . ((isset($pvs['url'])) ? mxPrepareToDisplay($pvs['url']) : "http://") . "\" /><br /><font class=\"tiny\">" . _OPTIONAL3 . "</font></td></tr>\n"
     . "<tr valign=\"top\"><td colspan=\"4\" class=\"bgcolor2\"><br /><h4>" . _SONSTIGES . ":</h4></td></tr>";
    // / ende YA Erweiterung von _Gerry_ mit kleinen Anpassungen
    // / wieder Originalcode aus YA/Userregistration
    $out .= "<tr valign=\"top\"><td class=\"bgcolor2\"><b>" . _SIGNATURE . ":</b></td><td colspan=\"3\" class=\"bgcolor3\">\n";
    $out .= "<textarea name=\"user_sig\" rows=\"6\" cols=\"57\">" . ((isset($pvs['user_sig'])) ? htmlspecialchars($pvs['user_sig'], ENT_QUOTES) : "") . "</textarea><br /><span class=\"tiny\">" . _MAXICHARS . "</span></td></tr>\n";

    return $out;
}

/**
 * Info
 */
function confirmNewUser_option($pvs)
{
    return;

    /* optionale Sprachdatei einbinden */
    mxGetLangfile('Your_Account', 'option-*.php');

    $userdesiredgroup = "";
    for ($i = 0; $i <= 5; $i++) {
        if (defined("_YA_REG_GROUPCAPTION_" . $i) && ((int)$pvs['userdesiredgroup'] == $i)) {
            $userdesiredgroup = constant("_YA_REG_GROUPCAPTION_" . $i);
            break;
        }
    }
    echo "<tr valign=\"top\"><td class=\"bgcolor2\"><b>" . _YA_REG_GROUPCAPTION . ":</b></td><td class=\"bgcolor3\">" . $userdesiredgroup . "</td></tr>\n";
    // if (!empty($pvs['user_sexus']))   echo "<tr valign=\"top\"><td class=\"bgcolor2\"><b>"._YA_USEXUS.":</b></td><td class=\"bgcolor3\">".vkpGetSexusString($pvs['user_sexus'])."</td></tr>\n";
    // if (!empty($pvs['cbday']))        echo "<tr valign=\"top\"><td class=\"bgcolor2\"><b>"._YA_UBDAY.":</b></td><td class=\"bgcolor3\">".$pvs['cbday']."</td></tr>\n";
    // if (!empty($pvs['url']))          echo "<tr valign=\"top\"><td class=\"bgcolor2\"><b>"._YOURHOMEPAGE.":</b></td><td class=\"bgcolor3\">".$pvs['url']."</td></tr>\n";
    // if (!empty($pvs['user_icq']))     echo "<tr valign=\"top\"><td class=\"bgcolor2\"><b>"._YICQ.":</b></td><td class=\"bgcolor3\">".$pvs['user_icq']."</td></tr>\n";
    // if (!empty($pvs['user_aim']))     echo "<tr valign=\"top\"><td class=\"bgcolor2\"><b>"._YAIM."</b></td><td class=\"bgcolor3\">".$pvs['user_aim']."</td></tr>\n";
    // if (!empty($pvs['user_yim']))     echo "<tr valign=\"top\"><td class=\"bgcolor2\"><b>"._YYIM.":</b></td><td class=\"bgcolor3\">".$pvs['user_yim']."</td></tr>\n";
    // if (!empty($pvs['user_msnm']))    echo "<tr valign=\"top\"><td class=\"bgcolor2\"><b>"._YMSNM.":</b></td><td class=\"bgcolor3\">".$pvs['user_msnm']."</td></tr>\n";
    // if (!empty($pvs['user_from']))    echo "<tr valign=\"top\"><td class=\"bgcolor2\"><b>"._YLOCATION.":</b></td><td class=\"bgcolor3\">".$pvs['user_from']."</td></tr>\n";
    // if (!empty($pvs['user_occ']))     echo "<tr valign=\"top\"><td class=\"bgcolor2\"><b>"._YOCCUPATION.":</b></td><td class=\"bgcolor3\">".$pvs['user_occ']."</td></tr>\n";
    // if (!empty($pvs['user_intrest'])) echo "<tr valign=\"top\"><td class=\"bgcolor2\"><b>"._YINTERESTS1.":</b></td><td class=\"bgcolor3\">".$pvs['user_intrest']."</td></tr>\n";
    // if (!empty($pvs['user_sig']))     echo "<tr valign=\"top\"><td class=\"bgcolor2\"><b>"._SIGNATURE.":</b></td><td class=\"bgcolor3\">".$pvs['user_sig']."</td></tr>\n";
}

/**
 * Info
 * die zusaetzlichen Datenbankfelder als array
 * - um ein zusaetzliches Feld in die Insert-Query einzufuegen,
 * einfach den Feldnamen und den Wert in der entsprechenden
 * SQL-Syntax hinzufuegen (siehe Beispieldaten)
 * - um eine bestehende (Original) Kombination auszuschliessen
 * den entsprechenden Arraywert aus dem $fields-Array löschen
 * z.B. unset($fields[13]);
 */
function finishNewUser_option($session, $fields)
{
    $formvars = mxAddSlashesForSQL($session);
    extract($formvars);
    // mxDebugFuncVars($session);
    // unset($fields[5]); #  = "url            = '$url'";
    // unset($fields[6]); #  = "user_avatar    = '$user_avatar'";
    // unset($fields[8]); #  = "user_icq       = '$user_icq'";
    // unset($fields[9]); #  = "user_occ       = '$user_occ'";
    // unset($fields[10]); # = "user_from      = '$user_from'";
    // unset($fields[11]); # = "user_intrest   = '$user_intrest'";
    // unset($fields[12]); # = "user_sig       = '$user_sig'";
    // unset($fields[13]); # = "user_aim       = '$user_aim'";
    // unset($fields[14]); # = "user_yim       = '$user_yim'";
    // unset($fields[15]); # = "user_msnm      = '$user_msnm'";
    // / Beispiel: die YA-Erweiterung von _Gerry_
    $fields[] = "user_from1         = '$user_from1'";
    $fields[] = "user_heigh         = '$user_heigh'";
    $fields[] = "user_gewicht       = '$user_gewicht'";
    $fields[] = "user_piercing      = '$user_piercing'";
    $fields[] = "user_tatto         = '$user_tatto'";
    $fields[] = "user_sucht         = '$user_sucht'";
    $fields[] = "user_beziehung     = '$user_beziehung'";
    $fields[] = "user_haar          = '$user_haar'";
    $fields[] = "user_augen         = '$user_augen'";
    $fields[] = "user_l1            = '$user_l1'";
    $fields[] = "user_l2            = '$user_l2'";
    $fields[] = "user_l3            = '$user_l3'";
    $fields[] = "user_l4            = '$user_l4'";
    $fields[] = "user_l5            = '$user_l5'";
    $fields[] = "user_l6            = '$user_l6'";
    $fields[] = "user_l7            = '$user_l7'";
    $fields[] = "user_l8            = '$user_l8'";
    $fields[] = "user_positiv       = '$user_positiv'";
    $fields[] = "user_negativ       = '$user_negativ'";
    $fields[] = "user_smoke         = '$user_smoke'";
    $fields[] = "user_alkohol       = '$user_alkohol'";
    $fields[] = "user_partneralter1 = '$user_partneralter1'";
    $fields[] = "user_partneralter2 = '$user_partneralter2'";
    $fields[] = "user_partnerwie    = '$user_partnerwie'";
    $fields[] = "user_famstatus     = '$user_famstatus'";
    $fields[] = "user_partnerfigur  = '$user_partnerfigur'";
    $fields[] = "user_kinder        = '$user_kinder'";
    // / Ende Beispiel
    return $fields;
}

/**
 * Info
 * die zusaetzlichen Datenbankfelder als array
 * - um ein zusaetzliches Feld in die Update-Query einzufuegen,
 * einfach den Feldnamen und den Wert in der entsprechenden
 * SQL-Syntax hinzufuegen (siehe Beispieldaten)
 * - um eine bestehende (Original) Kombination auszuschliessen
 * den entsprechenden Arraywert aus dem $fields-Array löschen
 * z.B. unset($fields[13]);
 * normalerweise sind es die selben Felder wie in der
 * Funktion finishNewUser_option() !
 */
function saveuser_option($pvs, $fields)
{
    $formvars = mxAddSlashesForSQL($pvs);
    extract($formvars);
    // mxDebugFuncVars($session);
    // unset($fields[2]); # = "email          = '$email'";
    // unset($fields[3]); # = "name           = '$realname'";
    // unset($fields[5]); # = "user_sexus     =  $user_sexus";
    // unset($fields[6]); # = "url            = '$url'";
    // unset($fields[7]); # = "user_avatar    = '$user_avatar'";
    // unset($fields[8]); # = "user_occ       = '$user_occ'";
    // unset($fields[9]); # = "user_from      = '$user_from'";
    // unset($fields[10]); # = "bio           = '$bio' ";
    // unset($fields[11]); # = "user_intrest  = '$user_intrest'";
    // unset($fields[12]); # = "user_sig      = '$user_sig'";
    // unset($fields[13]); # = "user_icq      = '$user_icq'";
    // unset($fields[14]); # = "user_aim      = '$user_aim'";
    // unset($fields[15]); # = "user_yim      = '$user_yim'";
    // unset($fields[16]); # = "user_msnm     = '$user_msnm'";
    // unset($fields[18]); # = "user_bday      =  $setbday";
    // / Beispiel: die YA-Erweiterung von _Gerry_
    $fields[] = "user_from1         = '$user_from1'";
    $fields[] = "user_heigh         = '$user_heigh'";
    $fields[] = "user_gewicht       = '$user_gewicht'";
    $fields[] = "user_piercing      = '$user_piercing'";
    $fields[] = "user_tatto         = '$user_tatto'";
    $fields[] = "user_sucht         = '$user_sucht'";
    $fields[] = "user_beziehung     = '$user_beziehung'";
    $fields[] = "user_haar          = '$user_haar'";
    $fields[] = "user_augen         = '$user_augen'";
    $fields[] = "user_l1            = '$user_l1'";
    $fields[] = "user_l2            = '$user_l2'";
    $fields[] = "user_l3            = '$user_l3'";
    $fields[] = "user_l4            = '$user_l4'";
    $fields[] = "user_l5            = '$user_l5'";
    $fields[] = "user_l6            = '$user_l6'";
    $fields[] = "user_l7            = '$user_l7'";
    $fields[] = "user_l8            = '$user_l8'";
    $fields[] = "user_positiv       = '$user_positiv'";
    $fields[] = "user_negativ       = '$user_negativ'";
    $fields[] = "user_smoke         = '$user_smoke'";
    $fields[] = "user_alkohol       = '$user_alkohol'";
    $fields[] = "user_partneralter1 = '$user_partneralter1'";
    $fields[] = "user_partneralter2 = '$user_partneralter2'";
    $fields[] = "user_partnerwie    = '$user_partnerwie'";
    $fields[] = "user_famstatus     = '$user_famstatus'";
    $fields[] = "user_partnerfigur  = '$user_partnerfigur'";
    $fields[] = "user_kinder        = '$user_kinder'";
    // / Ende Beispiel
    return $fields;
}

/**
 * Info
 */
function viewuserinfo_option_1($uinfo)
{
    global $istheuser, $privmsgactive, $gbactiv, $showall, $admin;

    /* optionale Sprachdatei einbinden */
    mxGetLangfile('Your_Account', 'option-*.php');

    extract($uinfo);

    echo "<table cellspacing=\"0\" cellpadding=\"0\" class=\"full\">\n";
    echo "<tr><td valign=\"top\">";
    // Beginn Informationen ober Tabelle links
    echo "<table cellspacing=\"1\" cellpadding=\"3\" class=\"bgcolor1 full\">\n";
    echo "<tr valign=\"top\"><td width=\"35%\" class=\"bgcolor2\"><b>" . _NICKNAME . ":</b></td><td valign=\"top\" class=\"bgcolor3\">
  <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
  <tr><td valign=\"top\"><b>" . mxPrepareToDisplay($uname) . "</b></td><td>&nbsp;&nbsp;&nbsp;&nbsp;" . $online . "</td></tr></table></td></tr>\n";

    if (!empty($lastonline)) echo "<tr valign=\"top\"><td class=\"bgcolor2\"><b>" . _YA_LASTONLINE . ":</b></td><td class=\"bgcolor3\">" . $lastonline . "</td></tr>\n";

    if (!empty($name)) echo "<tr valign=\"top\"><td class=\"bgcolor2\"><b>" . _UREALNAME . ":</b></td><td class=\"bgcolor3\">" . mxPrepareToDisplay($name) . "</td></tr>\n";
    if (!empty($user_bday)) echo '<tr valign="top"><td class="bgcolor2"><b>' . _YA_AGE . ':</b></td><td class="bgcolor3">' . $user_age . '&nbsp;' . _YEARS . '&nbsp;' . mxCreateImage('modules/Userinfo/images/info.gif', $user_bday) . '</td></tr>' . "\n";
    if (!empty($user_sexus)) echo "<tr valign=\"top\"><td class=\"bgcolor2\"><b>" . _YA_USEXUS . ":</b></td><td class=\"bgcolor3\">" . vkpGetSexusString($user_sexus) . "</td></tr>\n";
    if (!empty($user_occ)) echo "<tr valign=\"top\"><td class=\"bgcolor2\"><b>" . _OCCUPATION . ":</b></td><td class=\"bgcolor3\">" . mxPrepareToDisplay($user_occ) . "</td></tr>\n";
    if (!empty($user_from)) echo "<tr valign=\"top\"><td class=\"bgcolor2\"><b>" . _LOCATION . ":</b></td><td class=\"bgcolor3\">" . mxPrepareToDisplay($user_from) . "</td></tr>\n";
    if (!empty($user_from1)) echo "<tr valign=\"top\"><td class=\"bgcolor2\"><b>" . _YLOCATION1 . ":</b></td><td class=\"bgcolor3\">" . mxPrepareToDisplay($user_from1) . "</td></tr>\n";
    if (!empty($user_famstatus)) echo "<tr valign=\"top\"><td class=\"bgcolor2\"><b>" . _YA_STATUS . ":</b></td><td class=\"bgcolor3\">" . mxPrepareToDisplay($user_famstatus) . "</td></tr>\n";
    if (!empty($user_kinder)) echo "<tr valign=\"top\"><td class=\"bgcolor2\"><b>" . _YA_KINDER . ":</b></td><td class=\"bgcolor3\">" . mxPrepareToDisplay($user_kinder) . "</td></tr>\n";
    // if ($showall)              echo "<tr valign=\"top\"><td class=\"bgcolor2\"><b>"._UREALEMAIL.":</b></td><td class=\"bgcolor3\"><a href=\"mailto:".mxPrepareToDisplay($email)."\"><b>".mxPrepareToDisplay($email)."</b></a> *</td></tr>\n";
    if (!empty($usergroup)) echo "<tr valign=\"top\"><td class=\"bgcolor2\"><b>" . _YA_INGROUP . ":</b></td><td class=\"bgcolor3\">" . $usergroup . "</td></tr>\n";
    echo "</table>";

    /* aktuelles Foto ermitteln */
    $pici = load_class('Userpic', $uinfo);
    $photo = $pici->getHtml('normal', array('class' => 'align-center'));
    $photo_uploaded = $pici->is_uploaded();

    echo '</td><td>';
    if ($photo_uploaded) {

        ?>
<div class="align-center">
    <?php echo $photo ?>
    <?php if ($admin): ?>
    <br /><button id="upic-upload-delete"><?php echo _UPIC_DELETEIMG ?></button>
    <?php endif ?>
</div>
<?php
    } else {
        echo '&nbsp;';
    }
    echo '</td></tr></table>';
    echo '<br />';
    if (!empty($user_sucht)) {
        echo "<table cellspacing=\"1\" cellpadding=\"3\" class=\"bgcolor1 full\">\n";
        echo "<tr valign=\"top\"><td colspan=\"4\"><h4>$uname sucht:</h4></td></tr>";

        if (!empty($user_sucht)) echo"<tr valign=\"top\"><td width=\"30%\" class=\"bgcolor2\"><b>" . _YA_SUCHT . ":</b></td><td class=\"bgcolor3\">" . $user_sucht . "</td></tr>\n";
        if (!empty($user_beziehung)) echo"<tr valign=\"top\"><td width=\"30%\" class=\"bgcolor2\"><b>" . _YA_BEZIEHUNG . ":</b></td><td class=\"bgcolor3\">" . $user_beziehung . "</td></tr>\n";
        if (!empty($user_partneralter1)) echo"<tr valign=\"top\"><td width=\"30%\" class=\"bgcolor2\"><b>" . _YA_IMALTER . ":</b></td><td class=\"bgcolor3\">" . $user_partneralter1 . "\n";
        if (!empty($user_partneralter2)) echo"&nbsp;bis&nbsp;" . $user_partneralter2 . "</td></tr>\n";
        if (!empty($user_partnerfigur)) echo"<tr valign=\"top\"><td width=\"30%\" class=\"bgcolor2\"><b>" . _YA_FIGUR . ":</b></td><td class=\"bgcolor3\">" . $user_partnerfigur . "</td></tr>\n";
        if (!empty($user_partnerwie)) echo"<tr valign=\"top\"><td width=\"30%\" class=\"bgcolor2\"><b>" . _EXTRAINFO1 . ":</b></td><td class=\"bgcolor3\">" . $user_partnerwie . "</td></tr>\n";
        echo"</table>";
        echo'<br />';
    }

    echo "<table cellspacing=\"1\" cellpadding=\"3\" class=\"bgcolor1 full\">\n";
    echo"<tr valign=\"top\"><td colspan=\"4\"><h3>$uname " . _YA_DESCRIBESELF . ":</h3></td></tr>";
    if (!empty($user_heigh)) echo"<tr valign=\"top\"><td width=\"30%\" class=\"bgcolor2\"><b>" . _YGROESSE . ":</b></td><td class=\"bgcolor3\">" . $user_heigh . "&nbsp;" . _YCM . "</td></tr>\n";
    if (!empty($user_gewicht)) echo"<tr valign=\"top\"><td class=\"bgcolor2\"><b>" . _YGEWICHT . ":</b></td><td class=\"bgcolor3\">" . $user_gewicht . "&nbsp;" . _YKG . "</td></tr>\n";
    if (!empty($user_haar)) echo"<tr valign=\"top\"><td class=\"bgcolor2\"><b>" . _YHAAR . ":</b></td><td class=\"bgcolor3\">" . $user_haar . "</td></tr>\n";
    if (!empty($user_augen)) echo"<tr valign=\"top\"><td class=\"bgcolor2\"><b>" . _YAUGEN . ":</b></td><td class=\"bgcolor3\">" . $user_augen . "</td></tr>\n";
    if (!empty($user_piercing)) echo"<tr valign=\"top\"><td class=\"bgcolor2\"><b>" . _YA_PIERCING . ":</b></td><td class=\"bgcolor3\">" . $user_piercing . "</td></tr>\n";
    if (!empty($user_tatto)) echo"<tr valign=\"top\"><td class=\"bgcolor2\"><b>" . _YA_TATTO . ":</b></td><td class=\"bgcolor3\">" . $user_tatto . "</td></tr>\n";
    if (!empty($user_smoke)) echo"<tr valign=\"top\"><td class=\"bgcolor2\"><b>" . _YA_SMOKER . ":</b></td><td class=\"bgcolor3\">" . $user_smoke . "</td></tr>\n";
    if (!empty($user_alkohol)) echo"<tr valign=\"top\"><td class=\"bgcolor2\"><b>" . _YA_ALKOHOL . ":</b></td><td class=\"bgcolor3\">" . $user_alkohol . "</td></tr>\n";
    echo "<tr valign=\"top\"><td colspan=\"4\" class=\"bgcolor3\"><h4>" . _YA_LIEBLINGS . "</h4></td></tr>";
    if (!empty($user_l1)) echo"<tr valign=\"top\"><td class=\"bgcolor2\"><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . _YA_L1 . ":</b></td><td class=\"bgcolor3\">" . $user_l1 . "</td></tr>\n";
    if (!empty($user_l2)) echo"<tr valign=\"top\"><td class=\"bgcolor2\"><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . _YA_L2 . ":</b></td><td class=\"bgcolor3\">" . $user_l2 . "</td></tr>\n";
    if (!empty($user_l3)) echo"<tr valign=\"top\"><td class=\"bgcolor2\"><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . _YA_L3 . ":</b></td><td class=\"bgcolor3\">" . $user_l3 . "</td></tr>\n";
    if (!empty($user_l4)) echo"<tr valign=\"top\"><td class=\"bgcolor2\"><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . _YA_L4 . ":</b></td><td class=\"bgcolor3\">" . $user_l4 . "</td></tr>\n";
    if (!empty($user_l5)) echo"<tr valign=\"top\"><td class=\"bgcolor2\"><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . _YA_L5 . ":</b></td><td class=\"bgcolor3\">" . $user_l5 . "</td></tr>\n";
    if (!empty($user_l6)) echo"<tr valign=\"top\"><td class=\"bgcolor2\"><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . _YA_L6 . ":</b></td><td class=\"bgcolor3\">" . $user_l6 . "</td></tr>\n";
    if (!empty($user_l7)) echo"<tr valign=\"top\"><td class=\"bgcolor2\"><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . _YA_L7 . ":</b></td><td class=\"bgcolor3\">" . $user_l7 . "</td></tr>\n";
    if (!empty($user_l8)) echo"<tr valign=\"top\"><td class=\"bgcolor2\"><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . _YA_L8 . ":</b></td><td class=\"bgcolor3\">" . $user_l8 . "</td></tr>\n";

    if (!empty($user_positiv)) echo"<tr valign=\"top\"><td class=\"bgcolor2\"><b>" . _YA_POSITIV . ":</b></td><td class=\"bgcolor3\">" . $user_positiv . "</td></tr>\n";
    if (!empty($user_negativ)) echo"<tr valign=\"top\"><td class=\"bgcolor2\"><b>" . _YA_NEGATIV . ":</b></td><td class=\"bgcolor3\">" . $user_negativ . "</td></tr>\n";

    if (!empty($user_intrest)) echo"<tr valign=\"top\"><td class=\"bgcolor2\"><b>" . _YINTERESTS . ":</b></td><td class=\"bgcolor3\">" . $user_intrest . "</td></tr>\n";
    if (!empty($bio)) echo "<tr valign=\"top\"><td class=\"bgcolor2\"><b>" . _EXTRAINFO . ":</b></td><td class=\"bgcolor3\">" . $bio . "</td></tr>\n";
    // if (!empty($url))          echo "<tr valign=\"top\"><td class=\"bgcolor2\"><b>"._WEBSITE."</b></td><td class=\"bgcolor3\"><a href=\"".mxPrepareToDisplay($url)."\" target=\"_blank\">".mxPrepareToDisplay($url)."</a></td></tr>\n";
    // if (!empty($user_icq))     echo "<tr valign=\"top\"><td class=\"bgcolor2\"><b>"._ICQ.":</b></td><td class=\"bgcolor3\">".mxPrepareToDisplay($user_icq)."</td></tr>\n";
    // if (!empty($user_intrest)) echo "<tr valign=\"top\"><td class=\"bgcolor2\"><b>"._INTERESTS.":</b></td><td class=\"bgcolor3\">".mxPrepareToDisplay($user_intrest)."</td></tr>\n";
    // if (!empty($user_sig))     echo "<tr valign=\"top\"><td class=\"bgcolor2\"><b>"._SIGNATURE.":</b></td><td class=\"bgcolor3\">".$user_sig."</td></tr>\n";
    if ($hasuserpoints) {
        echo "<tr valign=\"top\"><td class=\"bgcolor2\"><b>" . _GRANKS . ":</b></td><td class=\"bgcolor3\">" . $hasuserpoints . "</td></tr>\n";
    }
    // if ($privmsgactive && $istheuser) {
    // echo "<tr valign=\"top\"><td class=\"bgcolor2\"><b>"._YA_BWOPMSG.":</b></td><td class=\"bgcolor3\">".$contpm."</td></tr>\n";
    // }
    if ($gbactiv && $gbnewentries) { // falls gaestebuch vorhanden
        echo "<tr valign=\"top\"><td class=\"bgcolor2\"><b>" . _GUESTBOOKVIEW . ":</b></td><td class=\"bgcolor3\"><b>" . $gbnewentries . "</b> " . _YA_BWOPMSGUNREAD . " *</td></tr>\n";
    }

    echo "</table>";

    echo '<br />';
    echo "<table cellspacing=\"1\" cellpadding=\"3\" class=\"bgcolor1 full\">\n";
    if (!empty($user_aim)) echo "<tr valign=\"top\"><td class=\"bgcolor2\"><b>" . _AIM . "</b></td><td class=\"bgcolor3\">" . mxPrepareToDisplay($user_aim) . "</td></tr>\n";
    if (!empty($user_yim)) echo "<tr valign=\"top\"><td class=\"bgcolor2\"><b>" . _YIM . ":</b></td><td class=\"bgcolor3\">" . mxPrepareToDisplay($user_yim) . "</td></tr>\n";
    if (!empty($user_msnm)) echo "<tr valign=\"top\"><td class=\"bgcolor2\"><b>" . _MSNM . ":</b></td><td class=\"bgcolor3\">" . mxPrepareToDisplay($user_msnm) . "</td></tr>\n";
    echo "</table>";

    if ($istheuser) {
        echo '<p align="center">* ' . _YA_ONLYYOUSEE . '!</p>';
    }
}

/**
 * Info
 */
function viewuserinfo_option_2($uinfo)
{
    global $prefix, $user_prefix, $istheuser, $privmsgactive, $gbactiv, $showall; // definiert in viewuserinfo()

    /* optionale Sprachdatei einbinden */
    mxGetLangfile('Your_Account', 'option-*.php');

    /* private Nachrichten */
    if ($privmsgactive) {
        if (!$istheuser) {
            echo '<br />';
            echo "<center><br />";
            $xuname = (MX_IS_USER || MX_IS_ADMIN) ? $uinfo['uname'] : "";
            echo
            "<form action=\"modules.php?name=Private_Messages\" method=\"post\">"
             . "<input type=\"hidden\" name=\"name\" value=\"Private_Messages\" />"
             . _USENDPRIVATEMSG . ": <input type=\"text\" name=\"to_user\" size=\"20\" maxlength=\"25\" value=\"$xuname\" />&nbsp;&nbsp;"
             . "<input type=\"hidden\" name=\"op\" value=\"send_to\" />"
             . "<input type=\"submit\" name=\"submita\" value=\"" . _SUBMIT . "\" />"
             . "</form></center>";
        }
    }

    /* Listings */
    $result1 = sql_query("select tid, sid, subject, comment from " . $prefix . "_comments where uid='" . intval($uinfo['uid']) . "' order by tid DESC limit 0,10");
    $view1 = sql_num_rows($result1);

    $result2 = sql_query("select sid, title from ${prefix}_stories where informant='" . mxAddSlashesForSQL($uinfo['uname']) . "' AND `time` <= now() order by time DESC limit 0,10");
    $view2 = sql_num_rows($result2);

    if (!empty($view1) || !empty($view2)) {
        echo '<br />';
        echo "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"5\"><tr>";
        if ($view2) {
            echo "<td valign=\"top\"><b>" . _LAST10SUBMISSIONS . " " . $uinfo['uname'] . ":</b><ul>";
            while (list($sid, $title) = sql_fetch_row($result2)) {
                echo "<li><a href=\"modules.php?name=News&amp;file=article&amp;sid=" . $sid . "\">" . $title . "</a></li>";
            }
            echo "</ul></td>";
        }
        if ($view1) {
            echo "<td valign=\"top\"><b>" . _LAST10COMMENTS . " " . $uinfo['uname'] . ":</b><ul>";
            while (list($tid, $sid, $subject, $comment) = sql_fetch_row($result1)) {
                $subject = strip_tags($subject);
                $subject = (empty($subject)) ? mxCutString(strip_tags($comment), 50) : $subject;
                if ($subject) {
                    echo "<li><a href=\"modules.php?name=News&amp;file=article&amp;sid=$sid#$tid\">$subject</a></li>";
                }
            }
            echo "</ul></td>";
        }
        echo "</tr></table>";
    }
}

/**
 * Info
 */
function sendnewusermail_option($pvs)
{
    return;

    /* optionale Sprachdatei einbinden */
    mxGetLangfile('Your_Account', 'option-*.php');
    $userdesiredgroup = "";
    for ($i = 0; $i <= 5; $i++) {
        if (defined("_YA_REG_GROUPCAPTION_" . $i) && ((int)$pvs['userdesiredgroup'] == $i)) {
            $userdesiredgroup = constant("_YA_REG_GROUPCAPTION_" . $i);
            break;
        }
    }
    $out = "  -" . _YA_REG_GROUPCAPTION . ":\t " . $userdesiredgroup . "\n";
    return $out;
}

?>