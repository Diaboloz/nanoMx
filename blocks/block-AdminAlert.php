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
 * $Revision: 80 $
 * $Author: PragmaMx $
 * $Date: 2015-08-18 12:50:28 +0200 (Di, 18. Aug 2015) $
 */

defined('mxMainFileLoaded') or die('access denied');

/* nur auf der indexseite fuer Admins anzeigen */
if (MX_IS_ADMIN && defined('MX_HOME_FILE') && file_exists('setup/index.php')) {
    $mxblockcache = false;
    $rok = false;
    $idx = 'q' . substr(md5($GLOBALS['mxSecureKey']), 3, 9);
    if (isset($_GET[$idx])) {
        mx_chmod('setup/index.php', PMX_CHMOD_FULLUNOCK);
        $newname = 'setup/index_' . uniqid(rand()) . '.php';
        $rok = rename("setup/index.php", $newname);
		file_put_contents('setup/.htaccess', "deny from all\nErrorDocument 403 \"pragmaMx-Setup is locked\"\n");
        if ($rok) {
            mx_chmod($newname, PMX_CHMOD_FULLOCK);
            $content = '';
            return;
        } else {
            mx_chmod('setup/index.php', PMX_CHMOD_FULLOCK);
        }
    }
    if (!$rok) {
        $blockfiletitle = "!! " . _ATTENTION . " !!";
        $content = '
          <p style="border:3px solid #C03000; padding:1em">
            Si vous pouvez lire ce message alors vous avez installé nanoMx corectement, félicitations ;-). <br />
            Par contre reste le programme d\'installation, <strong>' . _SETUPWARNING1 . '</strong> pour des raisons de sécurité.<br />
            ' . sprintf(_SETUPWARNING2, $idx) . '
            <br />
            Cette boite de dialogue disparaitera une fois le répertoire /setup/ supprimé.
          </p>';
    }
}

?>