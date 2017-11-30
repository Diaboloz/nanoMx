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
 *
 *
 */

defined('mxMainFileLoaded') or die('access denied');

if (!isset($_REQUEST['sid'])) {
    include(dirname(__FILE__) . '/index.php');
    exit();
}

$module_name = basename(dirname(__FILE__));

$target = 'modules.php?name=' . $module_name . '&file=article&sid=' . intval($_REQUEST['sid']);
if (!MX_IS_ADMIN && (empty($_SERVER['HTTP_REFERER']) || strpos($_SERVER['HTTP_REFERER'], PMX_HOME_URL) === false)) {
    header('HTTP/1.1 301 Moved Permanently');
    header('Status: 301 Moved Permanently');
    return mxRedirect($target);
}

/* versch. HTTP Header senden */
if (!headers_sent()) {
    header('Content-type: text/html; charset=utf-8');
    header('Content-Language: ' . _DOC_LANGUAGE);
    header('X-Powered-By: pragmaMx ' . PMX_VERSION);
}

mxGetLangfile($module_name);
include_once(PMX_SYSTEM_DIR . "/mxNewsFunctions.php");

$result = sql_query("SELECT title, time, hometext, bodytext, topic, notes 
					FROM ${prefix}_stories 
					WHERE sid=" . intval($_REQUEST['sid']));
list($title, $time, $hometext, $bodytext, $topic, $notes) = sql_fetch_row($result);
$result = sql_query("SELECT topictext FROM " . $prefix . "_topics WHERE topicid=" . intval($topic));
list($topictext) = sql_fetch_row($result);
$time = formatTimestamp($time);
/**
 * sicherstellen, dass der Seitentitel keine Tags enthält und Sonderzeichen nicht zerstückelt werden
 */
$title = strip_tags(str_replace('&nbsp;', ' ', $title));
$logo = (file_exists($site_logo)) ? '<p>' . mxCreateImage($site_logo, $sitename) . '</p>' : '';

$content = '';
switch (true) {
    case $hometext && $bodytext:
        $content .= $hometext . '<br /><br />' . $bodytext;
        break;
    case $hometext:
        $content .= $hometext;
        break;
    case $bodytext:
        $content .= $bodytext;
        break;
}
if ($notes) {
    $content .= '
		<br />
		<br />
		<br />
		<em>' . $notes . '</em>';
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <meta http-equiv="content-language" content="<?php echo _DOC_LANGUAGE ?>" />
  <meta name="robots" content="NOINDEX, NOFOLLOW" />
  <link rel="stylesheet" type="text/css" href="layout/style/style.css.php?t=<?php echo MX_THEME ?>" />
  <link rel="stylesheet" type="text/css" href="themes/<?php echo MX_THEME ?>/style/style.css" />
  <link rel="stylesheet" type="text/css" href="layout/style/printpage.css" />
  <title><?php echo $title ?></title>
</head>

<body class="printpage">
  <div id="p-page">
    <div id="p-head">
      <?php echo $logo ?>
      <h2><?php echo $title ?></h2>
    </div>

    <div id="p-main" class="content">
      <?php echo $content ?>
    </div>

    <div id="p-foot">
      <p><b><?php echo _PDATE ?></b> <?php echo $time ?></p>
      <p><b><?php echo _PTOPIC ?></b> <?php echo $topictext ?></p><br />

      <p><?php echo _COMESFROM ?> <?php echo $sitename ?><br />
      <?php echo PMX_HOME_URL ?></p>

      <p><?php echo _THEURL ?><br />
      <?php echo PMX_HOME_URL."/". htmlspecialchars($target) ?></p>
    </div>
  </div>
</body>
</html>
