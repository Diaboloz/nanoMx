<?php
/*
 pragmaMx - Web Content Management System
 Copyright by pragmaMx Developer Team - http://www.pragmamx.org
 write with: $Id: mx_configstring.php 179 2016-07-05 13:00:35Z PragmaMx $
 Version: pragmaMx pragmaMx 2.3.
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
$mxConf['dbhost']      = 'localhost';
$mxConf['dbname']      = 'nanomxpro';
$mxConf['dbuname']     = 'nanomxsql';
$mxConf['dbpass']      = 'ZSf8#Sc3xBPnw';
$mxConf['prefix']      = 'mx60370e';
$mxConf['user_prefix'] = 'mx60370e';
$mxConf['dbtype']      = 'mysql';
$mxConf['dbconnect']   = '1';

/**
 * all others...
 */
if(!defined('MX_FIRSTGROUPNAME')) define('MX_FIRSTGROUPNAME','User');
$mxConf['mxSecureKey']           = '1b5b813dbaa9b3b62f95a6b865877423';
$mxConf['vkpSessLifetime']       = '10';
$mxConf['mxSessionLoc']          = '1';
$mxConf['vkpInactiveMins']       = '5';
$mxConf['vkpIntranet']           = '0';
$mxConf['vkpSafeCookie1']        = '0';
$mxConf['vkpSafeCookie2']        = '1';
$mxConf['mxCookieInfo']		  = '0';
$mxConf['mxCookieLink']		  = 'modules.php?name=legal';
$mxConf['vkpSafeSqlinject']      = '1';
$mxConf['mxEntitieLevel']        = '1';
$mxConf['vkpsec_logging']        = '0';
$mxConf['mxUseGzipCompression']  = '0';
$mxConf['mxJpCacheUse']          = '0';
$mxConf['mxJpCacheTimeout']      = '3000';
$mxConf['mxFTPon']          = '0';
$mxConf['mxFTPhost']        = 'localhost';
$mxConf['mxFTPuser']        = '';
$mxConf['mxFTPpass']        = '';
$mxConf['mxFTPport']        = '21';
$mxConf['mxFTPssl']         = '0';
$mxConf['mxFTPdir']         = '';

$mxConf['mailuname']        = '';
$mxConf['mailhost']         = '';
$mxConf['mailpass']         = '';
$mxConf['mailport']         = '25';
$mxConf['mailauth']         = 'mail';
$mxConf['popauth']          = '';
$mxConf['sitename']         = 'pragmaMx 2.3. from pragmaMx.org';
$mxConf['site_logo']        = 'images/logo.gif';
$mxConf['slogan']           = 'pragmaMx - the fast CMS';
$mxConf['startdate']        = '27.01.2017';
$mxConf['adminmail']        = 'webmaster@pragmamx.fr';
$mxConf['anonpost']         = '0';
$mxConf['DOCTYPE']          = '3';
$mxConf['TidyOutput']       = '0';
$mxConf['juitheme']         = 'default';
$mxConf['foot1']            = '_Z1';
$mxConf['foot2']            = '_Z4';
$mxConf['foot3']            = '_Z3';
$mxConf['foot4']            = '_Z2';
$mxConf['commentlimit']     = '4096';
$mxConf['anonymous']        = 'Gast';
$mxConf['articlecomm']      = '1';
$mxConf['vkpBlocksRight']   = '0';
$mxConf['top']              = '10';
$mxConf['storyhome']        = '6';
$mxConf['storyhome_cols']   = '1';
$mxConf['oldnum']           = '10';
$mxConf['banners']          = '0';
$mxConf['language_avalaible']            = array('german');
$mxConf['language']          = 'french';
$mxConf['default_timezone']  = 'Europe/Berlin';
$mxConf['multilingual']      = '1';
$mxConf['useflags']          = '1';
$mxConf['notify']            = '0';
$mxConf['notifycomment']     = '0';
$mxConf['notify_email']      = 'webmaster@pragmamx.fr';
$mxConf['notify_subject']    = 'NEWS for my Site';
$mxConf['notify_message']    = 'Hallo, es gibt neue Artikeleinsendungen auf Deiner Seite.';
$mxConf['notify_from']       = 'webmaster@pragmamx.fr';
$mxConf['httprefmax']        = '500';
$mxConf['vkpTracking']       = '0';
$mxConf['AllowableHTML']     = array('a'=>2,'address'=>1,'b'=>1,'big'=>1,'blockquote'=>1,'br'=>2,'cite'=>1,'code'=>1,'div'=>2,'dl'=>1,'dt'=>1,'em'=>1,'fieldset'=>1,'h3'=>2,'h4'=>2,'h5'=>2,'h6'=>2,'hr'=>2,'i'=>1,'img'=>2,'li'=>1,'ol'=>1,'p'=>2,'pre'=>1,'small'=>1,'span'=>2,'strike'=>1,'strong'=>1,'sub'=>1,'sup'=>1,'table'=>2,'tbody'=>1,'td'=>2,'tfoot'=>1,'th'=>2,'thead'=>1,'tr'=>2,'tt'=>1,'u'=>1,'ul'=>2);
$mxConf['CensorList']        = array('fuck','cunt','fucker','fucking','pussy','cock','c0ck','cum','twat','clit','bitch','fuk','fuking','motherfucker','Arsch','Arschloch','fick','Wixer','Pimmel','Votz');
$mxConf['CensorMode']        = '1';
$mxConf['CensorReplace']     = 'uuups';
$mxConf['tipath']            = 'images/topics/';
$mxConf['mxSiteService']     = '0';
$mxConf['mxSiteServiceText'] = 'Siteupdate - single problems possible
Seitenumstellung - vereinzelte Probleme m&ouml;glich
<a href=\'admin.php?op=settings\'>change this message</a>';
$mxConf['mxUseThemecache']   = '1';
$mxConf['mxDebug']           = array('log'=>16,'screen'=>0,'enhanced'=>0);
$mxConf['show_pragmamx_news']= '1';
$mxConf['check_chmods']      = '1';

/**************************************
 * Do not touch the following options!
 */
(stripos($_SERVER['PHP_SELF'], basename(__FILE__)) === false) or die('access denied');
// only dbtype MySQL is supported by pragmaMx pragmaMx 2.3.!
$mxConf['dbtype'] = 'MySQL';
// set globals
foreach ($mxConf as $key => $value) {
  $mxConf[$key] = (!is_array($value)) ? stripslashes($value) : $value;
}
unset($key, $value);
extract($mxConf, EXTR_OVERWRITE);

?>