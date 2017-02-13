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

define('MXB_STANDALLONE', true);
if (isset($_GET['theme'])) {
    $theme = preg_replace('#[^A-Za-z0-9_-]#', '_', $_GET['theme']);
} else {
    $theme = mxSessionGetVar('theme');
}

include_once(dirname(__FILE__) . DS . 'includes' . DS . 'header.php');

/* versch. HTTP Header senden */
if (!headers_sent()) {
    header('Content-type: text/html; charset=utf-8');
    header('Content-Language: ' . _DOC_LANGUAGE);
    header('X-Powered-By: pragmaMx ' . PMX_VERSION);
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo _DOC_LANGUAGE ?>" lang="<?php echo _DOC_LANGUAGE ?>">
<head>
  <title><?php echo _TEXTSMILIES ?></title>
  <link rel="stylesheet" href="layout/style/style.css.php?t=<?php echo $theme ?>" type="text/css" />
  <link rel="stylesheet" href="themes/<?php echo $theme ?>/style/style.css" type="text/css" />
  <script src="<?php echo MX_BASE_URL . MXB_BASEMODJS ?>/common.js" type="text/javascript"></script>
  <script src="<?php echo MX_BASE_URL . MXB_BASEMODJS ?>/unb_lib.js" type="text/javascript"></script>
</head>
<body class="allone">
<table border="0" cellspacing="1" cellpadding="2" class="list full">
<tr><th colspan="2"><?php echo _TEXTSMILIES ?></th></tr>
<?php
$str = '';
$result = sql_query("select COUNT(id) from $table_smilies where type='smiley'");
list($allsmilies) = sql_fetch_row($result);
$totalsmilies = intval($smilieslinenumber * $smiliesrownumber);
$allsmilies -= $totalsmilies;
$result = sql_query("select code, url from $table_smilies where type='smiley' ORDER BY id limit $totalsmilies, $allsmilies");
if ($result) {
    $rcolor = 'alternate-a';

    while ($smile = sql_fetch_assoc($result)) {
        $url_smiles = MXB_ROOTMOD . 'images/' . $smile['url'];
        $str .= '<tr class="' . $rcolor . '">'
         . '<td>&nbsp;' . $smile['code'] . '</td>'
         . '<td align="center">
         <div style="cursor: pointer;" title="' . $smile['code'] . '" onclick="UnbInsertText(\' ' . $smile['code'] . ' \'); return false;">
         <img src="' . $url_smiles . '" alt="' . $smile['code'] . '" border="0" class="mxb-input-image" />
         </div>
         </td>'
         . '</tr>
         ';
        if ($rcolor == 'alternate-a') {
            $rcolor = 'alternate-b';
        } else {
            $rcolor = 'alternate-a';
        }
    }
} else {
    $str .= '<tr><td colspan="2"><h3>Could not retrieve data from the database.</h3></td></tr>';
}

echo $str;

?>
<tr><td colspan="2" align="center"><br/><input type="button" name="button" value="<?php echo _TEXTCLOSE ?>" onclick="self.close()" /></td></tr>
</table>
<script type="text/javascript">
/*<![CDATA[*/
    var textbox = opener.document.input.message;
    var openerdoc = opener.document;
/*]]>*/
</script>
</body></html>