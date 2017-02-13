<?php
/**
 * pragmaMx - Web Content Management System.
 * Copyright by pragmaMx Developer Team - http://www.pragmamx.org
 * $Id: layout.fluid.css.php,v 1.2 2013-04-08 21:29:02 dia_bolo Exp $
 */

define('mxMainFileLoaded', 1);

/* CSS Header senden */
header('Content-Type: text/css');
header('X-Powered-By: pragmaMx-cms');
header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 3600 * 24) . ' GMT'); // 1 day
header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime('../theme.settings.php')) . ' GMT');
header('Etag: ' . md5(filemtime('../theme.settings.php')));

/* default widths - fluid - 100%  */
$defaults = array(/* Breiten */
    'page' => 100,
    'left' => 21,
    'right' => 21,
    'min' => 780,
    'max' => 1200,
    );

error_reporting(0);
include('../theme.settings.php');
error_reporting(E_ALL);
// Einstellungen aus settings.php in Zahlenwert konvertieren
$widths = array_map('floatval', $themesetting['layoutwidth']);
// leere Werte aus settings.php entfernen
$widths = array_filter($widths);
// übrige korrigierte Werte aus settings.php überschreiben Standardwerte
$widths = array_merge($defaults, $widths);
extract($widths, EXTR_OVERWRITE);

if ($page > 100 || $page < 60) {
    $page = $defaults['page'];
}
if ($left > 50) {
    $left = $defaults['left'];
}
if ($right > 50) {
    $right = $defaults['right'];
}

/* Padding in EM unabhängig von Gesamt-Breite */
$colpadding = '1em';

/* minimalen Seitenrand belassen, sonst sieht das doof aus */
$page -= 10;

ob_start();

?>
<style>
<!--
<?php ob_end_clean(); ob_start(); ?>

/* Breiten und Position der Layoutspalten */
#main,
#sidebar-left-inside,
#sidebar-right-inside {
  min-height: 45em;
}

#mainbar,
#sidebar-right,
#sidebar-left {
  float: left;
  overflow: hidden;
  position: relative;
  top: 0;
  z-index: 0;
}

#sidebar-left {
   width: <?php echo $left ?>%;
}

#sidebar-right {
   width: <?php echo $right ?>%;
}

.page {
   max-width: <?php echo $max ?>px;
   min-width: <?php echo $min ?>px;
   padding:0 1em;
}

.full-width #mainbar {
   left: 0;
   width: 100%;
}

.full-width #sidebar-right,
.full-width #sidebar-left {
   display: none;
}

.full-width .block .block.__BLOCK_ID__ {
<?php /* z.B. Blockvorschau im Adminmenue */ ?>
width: <?php echo $left ?>%;
}

.col-2-left #mainbar {
   left: <?php echo $left ?>%;
   width: <?php echo 100 - $left ?>%;
}

.col-2-left #sidebar-left {
   display: block;
   right: <?php echo 100 - $left ?>%;
}

.col-2-left #sidebar-right {
   display: none;
}

.col-2-left.col-to-right #mainbar {
   left: 0;
}

.col-2-left.col-to-right #sidebar-left {
   right: 0;
}

.col-2-right #mainbar {
   left: 0;
   width: <?php echo 100 - $right ?>%;
}

.col-2-right #sidebar-left {
   display: none;
}

.col-2-right #sidebar-right {
   right: 0;
}

.col-2-right.col-to-left #mainbar {
   left: <?php echo $right ?>%;
}

.col-2-right.col-to-left #sidebar-right {
   right: <?php echo 100 - $right ?>%;
}

.col-3 #mainbar {
   left: <?php echo $left ?>%;
}

.col-3 #mainbar,
.col-3-left #mainbar,
.col-3-right #mainbar {
   width: <?php echo 100 - $left - $right ?>%;
}

.col-3 #sidebar-left,
.col-3-left #sidebar-right,
.col-3-left #sidebar-left {
   left: -<?php echo 100 - $left - $right ?>%;
}

.col-3 #sidebar-right {
   right: 0;
}

.col-3-left #mainbar {
   left: <?php echo $left + $right ?>%;
}

.col-3-right #mainbar {
   left: 0;
}

.col-3-right #sidebar-left {
   left: <?php echo $right ?>%;
}

.col-3-right #sidebar-right {
   left: -<?php echo $left ?>%;
}

/* Margins der Layoutspalten */

#sidebar-left-inside,
#sidebar-right-inside,
#mainbar-inside {
   padding-left: <?php echo $colpadding ?>;
   padding-right: <?php echo $colpadding ?>;
}

.full-width #mainbar-inside {
   padding-left: 0;
   padding-right: 0;
}

.col-2-left #mainbar-inside {
   padding-right: <?php echo $colpadding ?>;
}

.col-2-left.col-to-right #mainbar-inside {
   padding-left: <?php echo $colpadding ?>;
   padding-right: <?php echo $colpadding ?>;
}

.col-2-left.col-to-right #sidebar-left-inside {
   padding-left: <?php echo $colpadding ?>;
   padding-right: <?php echo $colpadding ?>;
}

.col-2-right.col-to-left #sidebar-right-inside {
   padding-left: 0;
   padding-right: <?php echo $colpadding ?>;
}

.col-3-left #mainbar-inside {
   padding-right: <?php echo $colpadding ?>;
}

.col-3-left #sidebar-left-inside {
   padding-right: <?php echo $colpadding ?>;
}

.col-3-left #sidebar-right-inside {
   padding-left: 0;
   padding-right: <?php echo $colpadding ?>;
}

.col-3-right #mainbar-inside {
   padding-left: <?php echo $colpadding ?>;
}

.col-3-right #sidebar-left-inside {
   padding-left: <?php echo $colpadding ?>;
   padding-right: <?php echo $colpadding ?>;
}

.col-3-right #sidebar-right-inside {
   padding-left: <?php echo $colpadding ?>;
}


/* Position der vertikalen Linie zwischen den Layoutspalten */

.full-width #main,
.full-width #main-inside,
.col-2-right #main-inside,
.col-2-left #main-inside,
.col-3-right #main-inside,
.col-3-left #main-inside {
   background-image: none;
}

.col-3 #main,
.col-2-left #main {
   background-position: <?php echo $left ?>%;
}

.col-3 #main-inside {
   background-position: <?php echo 100 - $right ?>%;
}

.col-3-left #main {
   background-position: <?php echo $left + $right ?>%;
}

.col-3-right #main {
   background-position: <?php echo 100 - $right - $left ?>%;
}

.col-2-left.col-to-right #main {
   background-position: <?php echo 100 - $left ?>%;
}

.col-2-right.col-to-left #main {
   background-position: <?php echo $right ?>%;
}


<?php 
$out = ob_get_clean();
$out = preg_replace('#/\*.+\*/#', ' ', $out);
$out = preg_replace('#\s+#', ' ', $out);
$out = preg_replace('#\s*([{:;,}])\s*#', '$1', $out);
$out = trim(str_replace('}', "}\n", $out));
die($out);
?>
-->
</style>
