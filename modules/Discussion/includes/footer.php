<?php
/**
 * mxBoard, pragmaMx Module
 * Copyright by pragmaMx Developer Team - http://www.pragmamx.org
 *
 * $Author: tora60 $
 * $Revision: 1.24.2.4 $
 * $Date: 2012-04-05 09:46:04 $
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
defined('MXB_INIT') or die('Not in mxBoard...');
// wenn footer.php in einer Funktion includet wird,
// sind etliche globale Variablen nicht verfügbar
// deswegen die bestehenden Variablen, die anschliessend noch geändert werden, nochmal global setzen.
// Das muss auch mit den Variablen in der theme.php des mxBoard gemacht werden.
global $jump, $totaltime, $stats, $foldernote, $mxbnavigator, $page_up, $rulesbutton;
if (!isset($MXB_INIT)) {
    // und den globalen scope bei Bedarf extrahieren
    extract($GLOBALS, EXTR_SKIP);
}

/* Navigation erstellen */
$navigation = $mxbnavigator->get();

$jump = mxbBuildQuickJump();

$totaltime = '';
if ($showtotaltime != 'off') {
    // totaltime erstmal nur mit dummy belegen, sonst wird nicht alles erfasst ;)
    $totaltime = '<li class="leftside"><!-- mxBoard-totaltime-' . $mxbStartTime . ' --></li>';
}
// foldernote wird nur in der forumdisplay.php definiert und wird auch nur darin angezeigt
$foldernote = (isset($foldernote)) ? $foldernote : '';

/* Das Standard-Stylesheet einlesen */
if (isset($tabletext)) {
    $default_css = addslashes(file_get_contents(MXB_BASEMODTHEME . DS . 'default.css'));
    // pmxDebug::pause();
    eval("\$default_css = stripslashes(\"$default_css\");");
    if ($default_css) {
        pmxHeader::add_style_code(mxbThemeCompressCss($default_css));
    }
    // pmxDebug::restore();
}

$mxb_template['head'] = '';
$mxb_template['top'] = '';
$mxb_template['bottom'] = '';
// die verschiedenen mxBoard Themes auswerten
switch (true) {
    case defined('MXBOARD_HTML_THEME'):
        // neue html-Templates
        include_once(MXB_BASEMODINCLUDE . 'themes.php');
        include(MXB_BASEMODTEMPLATE . DS . MXBOARD_HTML_THEME . DS . 'theme.php');
        break;
    case isset($borderwidth) && isset($mxboard_copyright):
        // der Pfad zu den Javascripten
        $BASEMODJS = MXB_BASEMODJS;
        // $stats gibt es nicht mehr..
        $stats = '';
        // die alte header.html einlesen
        $temphtml = addslashes(file_get_contents(MXB_BASEMODTEMPLATE . DS . 'header.html'));
        eval("\$Id='';\$temphtml = stripslashes(\"$temphtml\");"); // $id ist die cvs id vom template
        $mxb_template['top'] = $temphtml;
        // die alte footer.html einlesen
        $temphtml = addslashes(file_get_contents(MXB_BASEMODTEMPLATE . DS . 'footer.html'));
        eval("\$Id='';\$temphtml = stripslashes(\"$temphtml\");"); // $id ist die cvs id vom template
        $mxb_template['bottom'] = $temphtml;

        /* Falls ein Stylesheet mit dem aktuellen Themenamen im Templateordner liegt*/
        $file = MXB_BASEMODTEMPLATE . '/' . $XFtheme . '.css';
        if (file_exists($file)) {
            // dieses einbinden...
            pmxHeader::add_style($file);
        }
        unset($temphtml, $file);
}
// title-tag fixen:
global $pagetitle;
$pagetitle = $mxbnavigator->get_pagetitle();

$mxboard_output = ob_get_clean();
$mxboard_output = mxb_outputhandler($mxboard_output);

if (!defined('MXB_STANDALLONE')) {
    include_once('header.php');
}

?>

<!-- mxBoard Output Beginn -->
<div id="mxbout">
<?php echo $mxboard_output ?>
</div>
<!-- mxBoard Output End -->

<?php

if (!defined('MXB_STANDALLONE')) {
    include_once('footer.php');
}

?>
