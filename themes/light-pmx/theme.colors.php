<?php
/**
 * pragmaMx - Web Content Management System.
 * Copyright by pragmaMx Developer Team - http://www.pragmamx.org
 *
 * $Revision: 1.3 $
 * $Author: dia_bolo $
 * $Date: 2013-04-13 14:27:05 $
 */

/* diese Zeile nicht aendern!! */
defined('mxMainFileLoaded') or die('access denied');

/**
 * Standardfarben die im System und den Modulen verwendet werden
 * ACHTUNG!! Diese Farben werden durch die colors.php, des jeweiligen Designs ueberschrieben
 */

global $bgcolor1;
$bgcolor1 = "#fefefe";
global $bgcolor2;
$bgcolor2 = "#efefef";
global $bgcolor3;
$bgcolor3 = "#f3f3f3";
global $bgcolor4;
$bgcolor4 = "#e3e3e3";
global $textcolor1;
$textcolor1 = "#555555";
global $textcolor2;
$textcolor2 = "#5C5C5C";

ob_start();
?>

<style>
*.bgcolor1 {
   background-color: #fefefe;
   color: #555555;
}

*.bgcolor2 {
   background-color: #efefef;
   color: #5C5C5C;
}

*.bgcolor3 {
   background-color: #f3f3f3;
   color: #555555;
}

*.bgcolor4 {
   background-color: #e3e3e3;
   color: #5C5C5C;
}
</style>
<?php
ob_end_clean();
?>
