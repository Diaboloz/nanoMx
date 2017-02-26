<?php
/**
 * pragmaMx - Web Content Management System
 * Copyright by pragmaMx Developer Team - http://www.pragmamx.org
 * write with: $Id: config.first.php 220 2016-09-27 08:21:05Z PragmaMx $
 * Version: pragmaMx 2
 */

/**
 * Database & System Config
 * dbhost:      Database Hostname      (ask your Provider)
 * dbname:      Database Name          (ask your Provider)
 * dbuname:     Database Username      (ask your Provider)
 * dbpass:      Database User-Password (ask your Provider)
 * prefix:      Your Database table's prefix
 * user_prefix: Your Users' Database table's prefix (To share it)
 */
$mxConf['dbhost'] = 'localhost';
$mxConf['dbname'] = '??????????????????';
$mxConf['dbuname'] = '??????????????????';
$mxConf['dbpass'] = '??????????????????';
$mxConf['prefix'] = '??????????????????';
$mxConf['user_prefix'] = '??????????????????';
$mxConf['dbconnect'] = '1';

/**
 * all others...
 */
if (!defined('MX_FIRSTGROUPNAME')) define('MX_FIRSTGROUPNAME', 'User');
$mxConf['mxSecureKey'] = md5(time());
$mxConf['vkpSessLifetime'] = '10';
$mxConf['mxSessionLoc']     = '1';
$mxConf['vkpInactiveMins'] = '5';
$mxConf['vkpIntranet'] = '1';
$mxConf['vkpSafeCookie1'] = '0';
$mxConf['vkpSafeCookie2'] = '1';
$mxConf['mxCookieInfo']	='0';
$mxConf['vkpSafeSqlinject'] = '1';
$mxConf['mxEntitieLevel'] = '1';
$mxConf['vkpsec_logging'] = '0';
$mxConf['mxUseGzipCompression'] = '0';
$mxConf['mxJpCacheUse'] = '0';
$mxConf['mxJpCacheTimeout'] = '3000';
$mxConf['mxFTPon']  = '0';
$mxConf['mxFTPhost'] = 'localhost';
$mxConf['mxFTPuser'] = '';
$mxConf['mxFTPpass'] = '';
$mxConf['mxFTPport'] = '21';
$mxConf['mxFTPssl']  = '0';
$mxConf['mxFTPdir']  = '';
$mxConf['mailuname'] = '';
$mxConf['mailhost'] = '';
$mxConf['mailpass'] = '';
$mxConf['mailport'] = '25';
$mxConf['mailauth'] = 'mail';
$mxConf['popauth'] = '';
$mxConf['sitename'] = 'nanoMx for me ...';
$mxConf['site_logo'] = 'images/logo.gif';
$mxConf['slogan'] = 'nanoMx - be special';
$mxConf['startdate'] = '1.Fev 2017';
$mxConf['adminmail'] = 'webmaster@yoursite.de';
$mxConf['anonpost'] = '0';
$mxConf['DOCTYPE'] = '6';
$mxConf['TidyOutput'] = '0';
$mxConf['juitheme'] = 'default';
$mxConf['foot1'] = '_Z1';
$mxConf['foot2'] = '_Z4';
$mxConf['foot3'] = '_Z3';
$mxConf['foot4'] = '_Z2';
$mxConf['commentlimit'] = '4096';
$mxConf['anonymous'] = 'Gast';
$mxConf['articlecomm'] = '1';
$mxConf['vkpBlocksRight'] = '0';
$mxConf['top'] = '10';
$mxConf['storyhome'] = '6';
$mxConf['storyhome_cols'] = '1';
$mxConf['oldnum'] = '10';
$mxConf['banners'] = '0';
$mxConf['language_avalaible'] = array('french');
$mxConf['language'] = 'french';
$mxConf['default_timezone'] = 'Europe/Berlin';
$mxConf['multilingual'] = '1';
$mxConf['useflags'] = '1';
$mxConf['notify'] = '0';
$mxConf['notifycomment'] = '0';
$mxConf['notify_email'] = 'webmaster@yoursite.de';
$mxConf['notify_subject'] = 'NEWS for my Site';
$mxConf['notify_message'] = 'Hallo, es gibt neue Artikeleinsendungen auf Deiner Seite.';
$mxConf['notify_from'] = 'webmaster@yoursite.de';
$mxConf['httprefmax'] = '500';
$mxConf['AllowableHTML'] = array('a' => 2, 'address' => 1, 'b' => 1, 'big' => 1, 'blockquote' => 1, 'br' => 2, 'cite' => 1, 'code' => 1, 'div' => 2, 'dl' => 1, 'dt' => 1, 'em' => 1, 'fieldset' => 1, 'h3' => 2, 'h4' => 2, 'h5' => 2, 'h6' => 2, 'hr' => 2, 'i' => 1, 'img' => 2, 'li' => 1, 'ol' => 1, 'p' => 2, 'pre' => 1, 'small' => 1, 'span' => 2, 'strike' => 1, 'strong' => 1, 'sub' => 1, 'sup' => 1, 'table' => 2, 'tbody' => 1, 'td' => 2, 'tfoot' => 1, 'th' => 2, 'thead' => 1, 'tr' => 2, 'tt' => 1, 'u' => 1, 'ul' => 2);
$mxConf['CensorList'] = array("fuck", "cunt", "fucker", "fucking", "pussy", "cock", "c0ck", "cum", "twat", "clit", "bitch", "fuk", "fuking", "motherfucker", "Arsch", "Arschloch", "fick", "Wixer", "Pimmel", "Votz");
$mxConf['CensorMode'] = '1';
$mxConf['CensorReplace'] = 'uuups';
$mxConf['tipath'] = 'images/topics/';
$mxConf['mxSiteService'] = '0';
$mxConf['mxSiteServiceText'] = 'Siteupdate - single problems possible
Seitenumstellung - vereinzelte Probleme m&ouml;glich
<a href=\"' . adminUrl('settings') . '\">change this message</a>';
$mxConf['mxUseThemecache'] = '1';
$mxConf['mxDebug'] = array('log' => 0, 'screen' => 0, 'enhanced' => 0);
$mxConf['show_pragmamx_news'] = '1';
$mxConf['check_chmods'] = '1';

/**
 * Do not touch the following options!
 */
(stripos($_SERVER['PHP_SELF'], basename(__FILE__)) === false) or die('access denied');
// only dbtype MySQL is supported by pragmaMx 0.1.12!
$mxConf['dbtype'] = 'MySQL';
// set globals
foreach ($mxConf as $key => $value) {
    $mxConf[$key] = (!is_array($value)) ? stripslashes($value) : $value;
}
unset($key, $value);
extract($mxConf, EXTR_OVERWRITE);

?>