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
 * $Revision: 205 $
 * $Author: PragmaMx $
 * $Date: 2016-08-19 10:09:41 +0200 (Fr, 19. Aug 2016) $
 *
 * based on prettyPhoto-plugin by Olaf Herfurth/TerraProject (Germany)
 * - http://www.terraproject.de
 * enhanced by BdMdesigN
 * - http://www.osc4pragmamx.org/
 */

error_reporting(0);
ob_start();

/* erstmal mainfile laden */
include("../../../mainfile.php");

//define('mxMainFileLoaded', 1);

/* Standardwerte */
/* fast/slow/normal */
$animation_speed = 'normal';
/* ajaxcallback */
$ajaxcallback = '';
/* false OR interval time in ms */
$slideshow = 5000;
/* true/false */
$autoplay_slideshow = false;
/* Value between 0 and 1 */
$opacity = 0.5;
/* true/false */
$show_title = true;
/* Resize the photos bigger than viewport. true/false */
$allow_resize = true;
/* Allow the user to expand a resized image. true/false */
$allow_expand = true;
$default_width = 0;
$default_height = 0;
/* The separator for the gallery counter 1 'of' 2 */
$counter_separator_label = '/';
/* light_rounded / dark_rounded / light_square / dark_square */
$theme = 'light_rounded';
/* The padding on each side of the picture */
$horizontal_padding = 0;
/* Hides all the flash object on a page, set to TRUE if flash appears over prettyPhoto */
$hideflash = true;
/* Set the flash wmode attribute */
$wmode = 'opaque';
/* Automatically start videos: True/False */
$autoplay = true;
/* If set to true, only the close button will close the window */
$modal = false;
/* Allow prettyPhoto to update the url to enable deeplinking. */
$deeplinking = true;
/* If set to true, a gallery will overlay the fullscreen image on mouse over */
$overlay_gallery = true;
/* Maximum number of pictures in the overlay gallery */
$overlay_gallery_max = 30;
/* Set to false if you open forms inside prettyPhoto */
$keyboard_shortcuts = true;
/* Called when prettyPhoto is closed */
$callback = '';
/* Called everytime an item is shown/changed */
$changepicturecallback = '';
/* ie6 fallback */
$ie6_fallback = false;
/* show social_tools *//* html or false to disable */
$social_tools = false;

$rel = 'pretty';

/* Standardwerte mit config überschreiben */
//$configfile = '../../prettyPhoto/config.php';
//$ok = include($configfile);

/* ab pmx 2.3 wird aus der DB geladen */
//include_once("mainfile.php");
$config=load_class('Config','lightbox');
$mxPrettyPhoto = $config->getSection('lightbox');
$ok=true; 

/* ------------------------------------ */

if ($ok && isset($mxPrettyPhoto)) {
    extract($mxPrettyPhoto, EXTR_OVERWRITE);
    /* alte Version der Konfigurationsdatei */
    if (isset($animationSpeed, $autoplaySlideshow, $showTitle, $allowresize)) {
        $animation_speed = $animationSpeed;
        $autoplay_slideshow = $autoplaySlideshow;
        $show_title = $showTitle;
        $allow_resize = $allowresize;
    }
}

/* Standardwerte und config mit $_GET überschreiben */
if ($ok && !empty($_GET['rel'])) {
    /* aber nur, wenn $_GET[rel] vorhanden ist */
    extract($_GET, EXTR_OVERWRITE);

    /* _GET Parameter cleanen */
    $rel = preg_replace('#[^a-zA-Z0-9_-]#', 'x', $rel);

    $theme = preg_replace('#[^a-zA-Z0-9_-]#', 'x', $theme);

    if ($ajaxcallback) {
        // die Callback-Parameter dürfen nur Funktionsnamen sein
        $ajaxcallback = preg_replace('#[^a-zA-Z0-9_-]#', 'x', $ajaxcallback);
    }
    if ($callback) {
        $callback = preg_replace('#[^a-zA-Z0-9_-]#', 'x', $callback);
    }
    if ($changepicturecallback) {
        $changepicturecallback = preg_replace('#[^a-zA-Z0-9_-]#', 'x', $changepicturecallback);
    }

    $check = array('normal', 'slow', 'fast');
    if (!in_array($animation_speed, $check)) {
        $animation_speed = 'normal';
    }

    $check = array('window', 'direct', 'opaque', 'transparent', 'gpu');
    if (!in_array($wmode, $check)) {
        $wmode = 'opaque';
    }
}

header('Content-Type: text/javascript; charset=UTF-8');
header('X-Powered-By: pragmaMx-cms');
header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 3600 * 24 * 10) . ' GMT'); // 10 days
if ($ok) {
    // aktueller Zeitstempel 
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s', time()) . ' GMT');
    header('Etag: ' . $rel . md5(__FILE__));
}

?>

var static_<?php echo $rel ?>;

$(document).ready(function(){

  if(static_<?php echo $rel ?> != undefined) {
    return;
  }
  static_<?php echo $rel ?> = true;

  $('a[rel*=<?php echo $rel ?>]').prettyPhoto({
    animation_speed: '<?php echo $animation_speed ?>',
<?php if ($ajaxcallback) {    ?>
    ajaxcallback: <?php echo $ajaxcallback ?>,
<?php } ?>
    slideshow: <?php echo (($slideshow) ? intval($slideshow) : 'false') ?>,
    autoplay_slideshow: <?php echo (($autoplay_slideshow) ? 'true' : 'false') ?>,
    opacity: <?php echo floatval($opacity) ?>,
    show_title: <?php echo (($show_title) ? 'true' : 'false') ?>,
    allow_resize: <?php echo (($allow_resize) ? 'true' : 'false') ?>,
    allow_expand: <?php echo (($allow_expand) ? 'true' : 'false') ?>,
<?php if ($default_width) {    ?>
    default_width: <?php echo intval($default_width) ?>,
<?php } ?>
<?php if ($default_height) {    ?>
    default_height: <?php echo intval($default_height) ?>,
<?php } ?>
    counter_separator_label: '<?php echo addslashes($counter_separator_label) ?>',
    theme: '<?php echo $theme ?>',
<?php if ($horizontal_padding) {    ?>
    horizontal_padding: <?php echo intval($horizontal_padding) ?>,
<?php } ?>
    hideflash: <?php echo (($hideflash) ? 'true' : 'false') ?>,
    wmode: '<?php echo $wmode ?>',
    autoplay: <?php echo (($autoplay) ? 'true' : 'false') ?>,
    modal: <?php echo (($modal) ? 'true' : 'false') ?>,
    deeplinking: <?php echo (($deeplinking) ? 'true' : 'false') ?>,
    overlay_gallery: <?php echo (($overlay_gallery) ? 'true' : 'false') ?>,
    overlay_gallery_max: <?php echo intval($overlay_gallery_max) ?>,
    keyboard_shortcuts: <?php echo (($keyboard_shortcuts) ? 'true' : 'false') ?>,
<?php if ($callback) {    ?>
    callback: <?php echo $callback ?>,
<?php } ?>
<?php if ($changepicturecallback) {    ?>
    changepicturecallback: <?php echo $changepicturecallback ?>,
<?php } ?>
<?php if (!$social_tools) {    ?>
    social_tools: false,
<?php } ?>
    ie6_fallback: <?php echo (($ie6_fallback) ? 'true' : 'false') ?>
  });

});

<?php
$out = ob_get_clean();
$out = preg_replace('#[\r\n]\s*#', "", $out); // Zeilenumbrüche entfernen
$out = str_replace(',})', '})', $out); // unnötiges Komma am Schluss entfernen
echo $out;

?>