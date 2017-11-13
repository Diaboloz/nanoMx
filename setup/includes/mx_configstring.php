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
 * $Revision: 390 $
 * $Author: pragmamx $
 * $Date: 2017-10-08 14:33:39 +0200 (So, 08. Okt 2017) $
 */

defined('mxMainFileLoaded') or die('access denied');

$Source = '$Source';
$Revision = '$Source';
$Author = '$Source';
$Date = '$Source';
// ///////////////////////////////////////////////////////////////
// / den nachfolgenden code direkt, ohne Änderungen
// / aus /admin/modules/settings.php kopieren !!
// ///////////////////////////////////////////////////////////////
// //// beginn setup-string
$cont = "<?php
/*
 pragmaMx - Web Content Management System
 Copyright by pragmaMx Developer Team - http://www.pragmamx.org
 Version: " . PMX_VERSION . "
 */

/**
 * Database & System Config
 *   dbhost:      Database Hostname      (ask your Provider)
 *   dbname:      Database Name          (ask your Provider)
 *   dbuname:     Database Username      (ask your Provider)
 *   dbpass:      Database User-Password (ask your Provider)
 *   prefix:      Your Database table's prefix
 *   user_prefix: Your Users' Database table's prefix (To share it)
 */
\$mxConf['dbhost']      = '$dbhost';
\$mxConf['dbname']      = '$dbname';
\$mxConf['dbuname']     = '$dbuname';
\$mxConf['dbpass']      = '$dbpass';
\$mxConf['prefix']      = '$prefix';
\$mxConf['user_prefix'] = '$user_prefix';
\$mxConf['dbtype']      = 'mysql';
\$mxConf['dbconnect']   = '$dbconnect';

/**
 * all others...
 */
if(!defined('MX_FIRSTGROUPNAME')) define('MX_FIRSTGROUPNAME','" . MX_FIRSTGROUPNAME . "');
\$mxConf['mxSecureKey']           = '$xmxSecureKey';
\$mxConf['vkpSessLifetime']       = '$xvkpSessLifetime';
\$mxConf['mxSessionLoc']          = '$xmxSessionLoc';
\$mxConf['vkpInactiveMins']       = '$xvkpInactiveMins';
\$mxConf['vkpIntranet']           = '$xvkpIntranet';
\$mxConf['vkpSafeCookie1']        = '$xvkpSafeCookie1';
\$mxConf['vkpSafeCookie2']        = '$xvkpSafeCookie2';
\$mxConf['mxCookieInfo']		  = '$xmxCookieInfo';
\$mxConf['mxCookieLink']		  = '$xmxCookieLink';
\$mxConf['mxCookiePos']		  	  = '$xmxCookiePos';
\$mxConf['vkpSafeSqlinject']      = '$xvkpSafeSqlinject';
\$mxConf['mxEntitieLevel']        = '$xmxEntitieLevel';
\$mxConf['vkpsec_logging']        = '$xvkpsec_logging';
\$mxConf['mxUseGzipCompression']  = '$xmxUseGzipCompression';
\$mxConf['mxJpCacheUse']          = '$xmxJpCacheUse';
\$mxConf['mxJpCacheTimeout']      = '$xmxJpCacheTimeout';
\$mxConf['mxFTPon']          = '$xmxFTPon';
\$mxConf['mxFTPhost']        = '$xmxFTPhost';
\$mxConf['mxFTPuser']        = '$xmxFTPuser';
\$mxConf['mxFTPpass']        = '$xmxFTPpass';
\$mxConf['mxFTPport']        = '$xmxFTPport';
\$mxConf['mxFTPssl']         = '$xmxFTPssl';
\$mxConf['mxFTPdir']         = '$xmxFTPdir';

\$mxConf['mailuname']        = '$xmailuname';
\$mxConf['mailhost']         = '$xmailhost';
\$mxConf['mailpass']         = '$xmailpass';
\$mxConf['mailport']         = '$xmailport';
\$mxConf['mailauth']         = '$xmailauth';
\$mxConf['popauth']          = '$xpopauth';
\$mxConf['sitename']         = '$xsitename';
\$mxConf['site_logo']        = '$xsite_logo';
\$mxConf['slogan']           = '$xslogan';
\$mxConf['startdate']        = '$xstartdate';
\$mxConf['adminmail']        = '$xadminmail';
\$mxConf['anonpost']         = '$xanonpost';
\$mxConf['DOCTYPE']          = '$xDOCTYPE';
\$mxConf['TidyOutput']       = '$xTidyOutput';
\$mxConf['juitheme']         = '$xjuitheme';
\$mxConf['foot1']            = '$xfoot1';
\$mxConf['foot2']            = '$xfoot2';
\$mxConf['foot3']            = '$xfoot3';
\$mxConf['foot4']            = '$xfoot4';
\$mxConf['commentlimit']     = '$xcommentlimit';
\$mxConf['anonymous']        = '$xanonymous';
\$mxConf['articlecomm']      = '$xarticlecomm';
\$mxConf['vkpBlocksRight']   = '$xvkpBlocksRight';
\$mxConf['top']              = '$xtop';
\$mxConf['storyhome']        = '$xstoryhome';
\$mxConf['storyhome_cols']   = '$xstoryhome_cols';
\$mxConf['oldnum']           = '$xoldnum';
\$mxConf['language_avalaible']            = $newlanguage_avalaible;
\$mxConf['language']          = '$xlanguage';
\$mxConf['default_timezone']  = '$xdefault_timezone';
\$mxConf['multilingual']      = '$xmultilingual';
\$mxConf['useflags']          = '$xuseflags';
\$mxConf['notify']            = '$xnotify';
\$mxConf['notifycomment']     = '$xnotifycomment';
\$mxConf['notify_email']      = '$xnotify_email';
\$mxConf['notify_subject']    = '$xnotify_subject';
\$mxConf['notify_message']    = '$xnotify_message';
\$mxConf['notify_from']       = '$xnotify_from';
\$mxConf['httprefmax']        = '$xhttprefmax';
\$mxConf['AllowableHTML']     = $newAllowableHTML;
\$mxConf['CensorList']        = $newcensorlist;
\$mxConf['CensorMode']        = '$xCensorMode';
\$mxConf['CensorReplace']     = '$xCensorReplace';
\$mxConf['tipath']            = 'images/topics/';
\$mxConf['mxSiteService']     = '$xmxSiteService';
\$mxConf['mxSiteServiceText'] = '$xmxSiteServiceText';
\$mxConf['mxOfflineMode']     = '$xmxOfflineMode';
\$mxConf['mxOfflineModeText'] = '$xmxOfflineModeText';
\$mxConf['mxUseThemecache']   = '$xmxUseThemecache';
\$mxConf['mxDebug']           = $newdebug;
\$mxConf['show_pragmamx_news']= '$xshow_pragmamx_news';
\$mxConf['check_chmods']      = '$xcheck_chmods';

/**************************************
 * Do not touch the following options!
 */
(stripos(\$_SERVER['PHP_SELF'], basename(__FILE__)) === false) or die('access denied');
// only dbtype MySQL is supported by pragmaMx " . PMX_VERSION . "!

// set globals
foreach (\$mxConf as \$key => \$value) {
  \$mxConf[\$key] = (!is_array(\$value)) ? stripslashes(\$value) : \$value;
}
unset(\$key, \$value);
extract(\$mxConf, EXTR_OVERWRITE);

?>";
// //// ende setup-string
unset($Source, $Revision, $Author, $Date);

?>