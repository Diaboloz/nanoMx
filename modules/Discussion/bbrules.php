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

if (isset($_GET['theme'])) {
    define('MXB_STANDALLONE', true);
    $theme = preg_replace('#[^A-Za-z0-9_-]#', '_', $_GET['theme']);;
}

include_once(dirname(__FILE__) . DS . 'includes' . DS . 'header.php');
if ($bbrules == 'on') {
    $bbrulestxt = stripslashes($bbrulestxt);
} else {
    $bbrulestxt = '';
}

/* Ausgabe mit header/footer... */
if (!isset($_GET['theme'])) {
    $mxbnavigator->add(false, _TEXTBBRULESWINDOW);

    ?>
<h3><?php echo _TEXTBBRULESWINDOW?></h3>
<?php echo $bbrulestxt ?>

<?php
    include_once(MXB_BASEMODINCLUDE . 'footer.php');
    return;
}

/* das Popup, ohne header/footer... */
?>
<!DOCTYPE html>
<html lang="<?php echo _DOC_LANGUAGE ?>">
<head>
  <title><?php echo $bbname ?> - <?php echo _TEXTBBRULESWINDOW ?></title>
  <link rel="stylesheet" href="layout/style/style.css.php?t=<?php echo $theme ?>" type="text/css" />
  <link rel="stylesheet" href="themes/<?php echo $theme ?>/style/style.css" type="text/css" />
</head>
<body class="allone">
  <div class="box">
    <h2><?php echo $bbname ?> - <?php echo _TEXTBBRULESWINDOW ?></h2><?php echo $bbrulestxt ?>
  </div>
  <br />
  <br />
  <div class="align-center">
    <input type="button" value="<?php echo _TEXTCLOSEWINDOW ?>" onclick="window.close()" />
  </div>
</body>
</html>
