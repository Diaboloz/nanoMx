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
 * $Revision: 206 $
 * $Author: PragmaMx $
 * $Date: 2016-09-12 13:33:26 +0200 (Mo, 12. Sep 2016) $
 */

defined('mxMainFileLoaded') or die('access denied');

global $currentlang; 
/* Sprachdatei auswaehlen */
mxGetLangfile();


/* noch mal sicher gehen ... */
if (pmxBase::get("mxOfflineMode")==0) return;


/* header senden */
if (!headers_sent()) {
	header(pmxHeader::Status(423)); 	// es wird "Locked" gesendet -> Die angeforderte Ressource ist zurzeit gesperrt
    header('Content-type: text/html; charset=utf-8');
    header('Content-Language: ' . _DOC_LANGUAGE);
    header('X-Powered-By: pragmaMx ' . PMX_VERSION);
    header('X-UA-Compatible: IE=edge;FF=5;chrome=1');
}

/* wenn vorhanden, eine custom.offline.html ausgeben */
if (file_exists(PMX_LAYOUT_PATH.'templates/custom.offline.html')) {
	/* die Datei muss eine komplette HTML-Datei sein !! */
	include(PMX_LAYOUT_PATH.'templates/custom.offline.html');
	
	die();
}

/* ansonsten wird eine Standard-Seite generiert */

echo '<html lang="'. _DOC_LANGUAGE. '" dir="'. _DOC_DIRECTION. '" >'; 

?>

<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<base href="<?php echo PMX_HOME_URL; ?>" />
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge;chrome=1" />
	<meta name="revisit-after" content="1 day" />
	<meta name="language" content="<?php echo _DOC_LANGUAGE ?>" />
	<meta name="generator" content="pragmaMx <?php echo PMX_VERSION ?> - by http://pragmaMx.org" />
	<title><?php echo pmxBase::get('sitename'); ?></title>
	<link rel="stylesheet" href="<?php echo PMX_HOME_URL.DS.PMX_LAYOUT_PATH .  'style/offline.css' ?>" type="text/css" />
<?php
 /* evtl. custom.offline.css einbinden */
 if (file_exists(PMX_LAYOUT_PATH .  'style' . DS . 'custom.offline.css')) { ?>
	<link rel="stylesheet" href="<?php echo PMX_HOME_URL.DS.PMX_LAYOUT_PATH .  'style/custom.offline.css' ?>" type="text/css" />
<?php } ?>
</head>
	<body class="offline">
		<div class="offline-page">
			<div class="img"><img src="<?php echo PMX_HOME_URL.DS.pmxBase::get('site_logo') ?>" /></div>
			<div class="title"><?php echo pmxBase::get('sitename') ?></div>
			<div class="content">
				<div class="offline-text"><?php echo pmxBase::get("mxOfflineModeText") ?></div>
			</div>
		</div>
	</body>
</html>

<?php 
	die();

	

?>
