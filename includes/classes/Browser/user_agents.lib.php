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
 * project:	expCounter
 * version:	1.2
 * copyright: since	2009 © by Volker S. Latainski
 * license:	GPL 2.0 or higher(see docs/license.txt)
 *
 * based on chCounter 3.1.3 by Christoph Bachner and Bert Körn
 */

// browsers
$chC_ualib_browsers = array(

  'Amaya' => array(
    'icon' => 'amaya.png',
    'use_PCRE' => 1,
    'pattern' => '/(Amaya)+(\/)?([0-9a-z.]+)?/i',
    'version' => 3,
    'anti_pattern' => ''
  ),
  'Amiga Voyager' => array(
    'icon' => 'voyager.png',
    'use_PCRE' => 1,
    'pattern' => '/(AmigaVoyager)+(\/)?([0-9a-z.-]+)?/i',
    'version' => 3,
    'anti_pattern' => ''
  ),
  'AOL-Browser' => array(
    'icon' => 'aol.png',
    'use_PCRE' => 1,
    'pattern' => '/(AOL )+([0-9a-z.]+)?/i',
    'version' => 2,
    'anti_pattern' => ''
  ),
  'Avant Browser' => array(
    'icon' => 'avant-browser.png',
    'use_PCRE' => 1,
    'pattern' => '/(Avant Browser)/i',
    'version' => false,
    'anti_pattern' => '/(Crazy Browser)/i'
  ),
  'Camino' => array(
    'icon' => 'camino.png',
    'use_PCRE' => 1,
    'pattern' => '/(Camino|Chimera)+(\/)?([0-9a-z.]+)?/i',
    'version' => 3,
    'anti_pattern' => ''
  ),
  'Crazy Browser' => array(
    'icon' => 'crazy.png',
    'use_PCRE' => 1,
    'pattern' => '/(Crazy Browser )+([0-9a-z.]+)?/i',
    'version' => 2,
    'anti_pattern' => ''
  ),
  'Curl' => array(
    'icon' => 'curl.png',
    'use_PCRE' => 1,
    'pattern' => '/(curl)+(\/| )?([0-9a-z.-]+)?/i',
    'version' => 3,
    'anti_pattern' => ''
  ),
  'ELinks' => array(
    'icon' => 'elinks.png',
    'use_PCRE' => 1,
    'pattern' => '/(ELinks)+((\/)|( \())?([0-9a-z.-]+)?/i',
    'version' => 5,
    'anti_pattern' => ''
  ),
  'Epiphany' => array(
    'icon' => 'epiphany.png',
    'use_PCRE' => 1,
    'pattern' => '/(Epiphany)+(\/)?([0-9a-z.-]+)?/i',
    'version' => 3,
    'anti_pattern' => ''
  ),
  'Firefox' => array(
    'icon' => 'firefox.png',
    'use_PCRE' => 1,
    'pattern' => '/(Firefox|Firebird|Phoenix|BonEcho|GranParadiso)+(\/)?([0-9a-z.]+)?/i',
    'version' => 3,
    'anti_pattern' => '/(Flock|Galeon|Epiphany|Netscape|Navigator|Camino|SeaMonkey|Iceweasel)/i'
  ),
  'Flock' => array(
    'icon' => 'flock.png',
    'use_PCRE' => 1,
    'pattern' => '/(Flock)+(\/)?([0-9a-z.-]+)?/i',
    'version' => 3,
    'anti_pattern' => ''
  ),
  'Galeon' => array(
    'icon' => 'galeon.png',
    'use_PCRE' => 1,
    'pattern' => '/(Galeon)+(\/)?([0-9a-z.-]+)?/i',
    'version' => 3,
    'anti_pattern' => ''
  ),
  'Google Chrome' => array(
    'icon' => 'google-chrome.png',
    'use_PCRE' => 1,
    'pattern' => '/(Chrome)+(\/)?([0-9a-z.-]+)?/i',
    'version' => 3,
    'anti_pattern' => ''
  ),
  'IBrowse' => array(
    'icon' => 'ibrowse.png',
    'use_PCRE' => 1,
    'pattern' => '/(IBrowse)+(\/| )?([0-9a-z.-]+)?/i',
    'version' => 3,
    'anti_pattern' => ''
  ),
  'iCab' => array(
    'icon' => 'icab.png',
    'use_PCRE' => 1,
    'pattern' => '/(iCab)+(\/| )?([0-9a-z.]+)?/i',
    'version' => 3,
    'anti_pattern' => ''
  ),
  'Iceweasel' => array(
    'icon' => 'iceweasel.png',
    'use_PCRE' => 1,
    'pattern' => '/(Iceweasel)+(\/)?([0-9a-z.]+)?/i',
    'version' => 3,
    'anti_pattern' => ''
  ),
  'Internet Explorer' => array(
    'icon' => 'ie.png',
    'use_PCRE' => 1,
    'pattern' => '/(MSIE)+( [0-9.]+)?/i',
    'version' => 2,
    'anti_pattern' => '/(Opera|BecomeBot|Girafabot|Crazy Browser|AOL|T-Online|Avant Browser)/i'
  ),
  'iPhone' => array(
    'icon' => 'safari.png',
    'use_PCRE' => 1,
    'pattern' => '/(iPhone)/i',
    'version' => false,
    'anti_pattern' => '/(iPod|iPad)/i'
  ),
  'iPad' => array(
    'icon' => 'safari.png',
    'use_PCRE' => 0,
    'pattern' => '/(iPad)/i',
    'version' => false,
    'anti_pattern' => ''
  ),
  'iPod' => array(
    'icon' => 'safari.png',
    'use_PCRE' => 0,
    'pattern' => 'iPod',
    'version' => false,
    'anti_pattern' => ''
  ),
  'K-Meleon' => array(
    'icon' => 'k-meleon.png',
    'use_PCRE' => 1,
    'pattern' => '/(K-Meleon)+(\/| )?([0-9a-z.-]+)?/i',
    'version' => 3,
    'anti_pattern' => ''
  ),
  'Konqueror' => array(
    'icon' => 'konqueror.png',
    'use_PCRE' => 1,
    'pattern' => '/(Konqueror)+(\/)?([0-9a-z.-]+)?/i',
    'version' => 3,
    'anti_pattern' => ''
  ),
  'Links' => array(
    'icon' => 'links.png',
    'use_PCRE' => 1,
    'pattern' => '/(Links )+(\()?([0-9a-z.-]+)?/i',
    'version' => 3,
    'anti_pattern' => '/(ELinks)/i'
  ),
  'Lynx' => array(
    'icon' => 'lynx.png',
    'use_PCRE' => 1,
    'pattern' => '/(Lynx)+(\/| )?(([0-9a-z.-])+|(\(textmode\))+)?/i',
    'version' => 3,
    'anti_pattern' => ''
  ),
  'Mozilla' => array(
    'icon' => 'mozilla.png',
    'use_PCRE' => 1,
    'pattern' => '/Mozilla\/5.0(.*); rv:([0-9.]+)(.*)Gecko/',
    'version' => 2,
    'anti_pattern' => '/(MSIE|Opera|Netscape|Navigator|Galeon|K-Meleon|Epiphany|Camino|Chimera|Firefox|Firebird|Phoenix|Beonex|SeaMonkey|Flock|Iceweasel|IBrowse|iCab|OmniWeb|Crazy Browser|AOL|Thunderbird)/i'
  ),
  'Netscape' => array(
    'icon' => 'netscape.png',
    'use_PCRE' => 1,
    'pattern' => '/(Netscape|Navigator)+(\/)?([0-9a-z.-]+)?/i',
    'version' => 3,
    'anti_pattern' => ''
  ),
  'Nintendo Wii' => array(
    'icon' => 'opera.png',
    'use_PCRE' => 0,
    'pattern' => 'Nintendo Wii',
    'version' => false,
    'anti_pattern' => ''
  ),
  'OmniWeb' => array(
    'icon' => 'omniweb.png',
    'use_PCRE' => 1,
    'pattern' => '/(OmniWeb)+(\/)?([0-9a-z.-]+)?/i',
    'version' => 3,
    'anti_pattern' => ''
  ),
  'Opera' => array(
    'icon' => 'opera.png',
    'use_PCRE' => 1,
    'pattern' => '/(Opera)+(\/| )?([0-9a-z.-]+)?/i',
    'version' => 3,
    'anti_pattern' => '/(Mini|Nintendo Wii)/i'
  ),
  'Opera Mini' => array(
    'icon' => 'opera.png',
    'use_PCRE' => 1,
    'pattern' => '/(Opera Mini)+(\/| )?([0-9a-z.-]+)?/i',
    'version' => 3,
    'anti_pattern' => ''
  ),
  'PSP' => array(
    'icon' => 'psp.png',
    'use_PCRE' => 1,
    'pattern' => '/PSP \(PlayStation Portable\); ([0-9.]+)?/i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'Safari' => array(
    'icon' => 'safari.png',
    'use_PCRE' => 1,
    'pattern' => '/(Safari)+(\/)?([0-9a-z.-]+)?/i',
    'version' => 3,
    'anti_pattern' => '/(Chrome|iPod|iPhone|OmniWeb)/i'
  ),
  'SeaMonkey' => array(
    'icon' => 'seamonkey.png',
    'use_PCRE' => 1,
    'pattern' => '/(SeaMonkey)+(\/)?([0-9a-z.]+)?/i',
    'version' => 3,
    'anti_pattern' => ''
  ),
  'Thunderbird' => array(
    'icon' => 'thunderbird.png',
    'use_PCRE' => 1,
    'pattern' => '/(Thunderbird)+(\/)?([0-9a-z.-]+)?/i',
    'version' => 3,
    'anti_pattern' => ''
  ),
  'T-Online-Browser' => array(
    'icon' => 't-online.png',
    'use_PCRE' => 0,
    'pattern' => 'T-Online',
    'version' => false,
    'anti_pattern' => ''
  ),
  'w3m' => array(
    'icon' => 'w3m.png',
    'use_PCRE' => 1,
    'pattern' => '/(w3m)+(\/)?([0-9a-z.-]+)?/i',
    'version' => 3,
    'anti_pattern' => ''
  ),
  'Windows-Media-Player' => array(
    'icon' => 'windows-mediaplayer.png',
    'use_PCRE' => 1,
    'pattern' => '/(Windows-Media-Player)+(\/)?([0-9a-z.-]+)?/i',
    'version' => 3,
    'anti_pattern' => ''
  ),
  'unknown' => array(
    'icon' => 'unknown.png',
    'use_PCRE' => 1,
    'pattern' => '/.*/',
    'version' => false,
    'anti_pattern' => ''
  )
);





/* OS */
$chC_ualib_os = array(

  'Windows' => array(
    'icon' => 'windows.png',
    'use_PCRE' => 0,
    'pattern' => 'Win',
    'version' => array(
      'NT 5.1' => 'XP',
      'NT 5.2' => 'Server 2003',
      'NT 5.0' => '2000',
      'NT 6.0' => 'Vista / Server 2008',
      'NT 6.1' => '7',
      'NT' => 'NT',
      'ME' => 'ME',
      'Win 9x 4.90' => 'ME',
      '98' => '98',
      '95' => '95',
      'CE' => 'CE',
      'Windows 3.1' => '3.1',
      'XP' => 'XP',
      '2000' => '2000',
      'Win64' => 'XP Professional x64'
    ),
    'anti_pattern' => ''
  ),
  'Linux' => array(
    'icon' => 'linux.png',
    'use_PCRE' => 0,
    'pattern' => 'Linux',
    'version' => array(
      'Kubuntu' => 'Kubuntu',
      'Debian' => 'Debian',
      'Fedora' => 'Fedora',
      'gentoo' => 'gentoo',
      'Mandriva' => 'Mandriva',
      'SUSE' => 'Suse',
      'kanotix' => 'kanotix',
      'Red Hat' => 'Red Hat',
      'CentOS' => 'CentOS',
      'Ubuntu' => 'Ubuntu'
    ),
    'anti_pattern' => ''
  ),
  'Mac OS' => array(
    'icon' => 'mac_os.png',
    'use_PCRE' => 0,
    'pattern' => 'Mac',
    'version' => array(
      'Mac_PowerPC' => 'Mac_PowerPC',
      'Mach-O' => 'PPC Mac OS X Mach-O',
      'PPC Mac' => 'PPC Mac OS X',
      'Intel Mac' => 'Intel Mac OS X',
      'PPC' => 'PPC',
      'Macintosh' => 'Macintosh'
    ),
    'anti_pattern' => ''
  ),
  'SunOS' => array(
    'icon' => 'sunos.png',
    'use_PCRE' => 1,
    'pattern' => '/SunOS ?([0-9.]+)?/',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'FreeBSD' => array(
    'icon' => 'freebsd.png',
    'use_PCRE' => 0,
    'pattern' => 'FreeBSD',
    'version' => false,
    'anti_pattern' => ''
  ),
  'NetBSD' => array(
    'icon' => 'netbsd.png',
    'use_PCRE' => 0,
    'pattern' => 'NetBSD',
    'version' => false,
    'anti_pattern' => ''
  ),
  'OpenBSD' => array(
    'icon' => 'openbsd.png',
    'use_PCRE' => 0,
    'pattern' => 'OpenBSD',
    'version' => false,
    'anti_pattern' => ''
  ),
  'IRIX' => array(
    'icon' => 'irix.png',
    'use_PCRE' => 0,
    'pattern' => 'IRIX',
    'version' => false,
    'anti_pattern' => ''
  ),
  'BeOS' => array(
    'icon' => 'beos.png',
    'use_PCRE' => 1,
    'pattern' => '/BeOS/i',
    'version' => false,
    'anti_pattern' => ''
  ),
  'OS/2' => array(
    'icon' => 'os2.png',
    'use_PCRE' => 0,
    'pattern' => 'OS/2',
    'version' => false,
    'anti_pattern' => ''
  ),
  'AIX' => array(
    'icon' => 'aix.png',
    'use_PCRE' => 0,
    'pattern' => 'AIX',
    'version' => false,
    'anti_pattern' => ''
  ),
  'Amiga' => array(
    'icon' => 'amiga_os.png',
    'use_PCRE' => 1,
    'pattern' => '/AmigaOS ?([0-9.]+)?/',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'Darwin' => array(
    'icon' => 'darwin.png',
    'use_PCRE' => 1,
    'pattern' => '/Darwin/i',
    'version' => false,
    'anti_pattern' => ''
  ),
  'HP-UX' => array(
    'icon' => 'hp-ux.png',
    'use_PCRE' => 0,
    'pattern' => 'HP-UX',
    'version' => false,
    'anti_pattern' => ''
  ),
  'QNX' => array(
    'icon' => 'qnx.png',
    'use_PCRE' => 0,
    'pattern' => 'QNX',
    'version' => false,
    'anti_pattern' => ''
  ),
  'Symbian OS' => array(
    'icon' => 'symbian.png',
    'use_PCRE' => 0,
    'pattern' => 'Symbian OS',
    'version' => false,
    'anti_pattern' => ''
  ),
  'Android' => array(
    'icon' => 'android.png',
    'use_PCRE' => 0,
    'pattern' => 'Android',
    'version' => false,
    'anti_pattern' => ''
  ),
  'unknown' => array(
    'icon' => 'unknown.png',
    'use_PCRE' => 1,
    'pattern' => '/.*/',
    'version' => false,
    'anti_pattern' => ''
  )
);

/* robots */
$chC_ualib_robots = array(
  'Googlebot-Image' => array(
    'icon' => 'google.png',
    'use_PCRE' => 1,
    'pattern' => '/Googlebot-Image\/([0-9.]+)/i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'Google Adsense' => array(
    'icon' => 'google.png',
    'use_PCRE' => 1,
    'pattern' => '#Mediapartners-Google/([0-9.]+)#i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'Google Feedfetcher' => array(
    'icon' => 'google.png',
    'use_PCRE' => 0,
    'pattern' => 'Feedfetcher-Google',
    'version' => false,
    'anti_pattern' => ''
  ),
  'Google Mobile' => array(
    'icon' => 'google.png',
    'use_PCRE' => 1,
    'pattern' => '#Googlebot-Mobile/([0-9.]+)#i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'Google Sitemaps' => array(
    'icon' => 'google.png',
    'use_PCRE' => 1,
    'pattern' => '/Google-Sitemaps\/([0-9.]+)/i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'Googlebot' => array(
    'icon' => 'google.png',
    'use_PCRE' => 1,
    'pattern' => '/Googl(e|ebot)[ \/]([0-9.]+|Test)?;? ?(\(?\+http:\/\/www\.google(bot)?\.com\/bot\.html\)?)?/i',
    'version' => 2,
    'anti_pattern' => ''
  ),
  'Altavista' => array(
    'icon' => 'altavista.png',
    'use_PCRE' => 1,
    'pattern' => '/Scooter\/([0-9.]+)/i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'Inktomi' => array(
    'icon' => 'inktomi.png',
    'use_PCRE' => 0,
    'pattern' => 'inktomi.com',
    'version' => false,
    'anti_pattern' => ''
  ),
  'Yahoo!' => array(
    'icon' => 'yahoo.png',
    'use_PCRE' => 0,
    'pattern' => 'Yahoo! Slurp',
    'version' => false,
    'anti_pattern' => '/(China|MMCrawler)/i'
  ),
  'Yahoo! China' => array(
    'icon' => 'yahoo.png',
    'use_PCRE' => 0,
    'pattern' => 'Yahoo! Slurp China',
    'version' => false,
    'anti_pattern' => ''
  ),
  'Infoseek' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 0,
    'pattern' => 'Infoseek',
    'version' => false,
    'anti_pattern' => ''
  ),
  'Nutch' => array(
    'icon' => 'nutch.png',
    'use_PCRE' => 1,
    'pattern' => '/Nutch(Org|CVS)?\/?([0-9.]+)?/i',
    'version' => 2,
    'anti_pattern' => ''
  ),
  'Fireball' => array(
    'icon' => 'fireball.png',
    'use_PCRE' => 0,
    'pattern' => 'Fireball',
    'version' => false,
    'anti_pattern' => ''
  ),
  'AlltheWeb' => array(
    'icon' => 'alltheweb.png',
    'use_PCRE' => 1,
    'pattern' => '#((FAST[ \-]*WebCrawler[ \/]+([0-9.]+)?)|crawler@fast\.no)#i',  //TODO testen
    'version' => 3,
    'anti_pattern' => ''
  ),
  'Alexa (web.archive.org)' => array(
    'icon' => 'alexa.png',
    'use_PCRE' => 0,
    'pattern' => 'ia_archiver-web.archive.org',
    'version' => false,
    'anti_pattern' => ''
  ),
  'Alexa' => array(
    'icon' => 'alexa.png',
    'use_PCRE' => 0,
    'pattern' => 'ia_archiver',
    'version' => false,
    'anti_pattern' => ''
  ),
  'WiseNutBot' => array(
    'icon' => 'wisenutbot.png',
    'use_PCRE' => 1,
    'pattern' => '/Zyborg[ \/]?([0-9.]+)/i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'W3C Validator' => array(
    'icon' => 'w3c.png',
    'use_PCRE' => 0,
    'pattern' => 'W3C_Validator',
    'version' => false,
    'anti_pattern' => ''
  ),
  'W3C CSS Validator' => array(
    'icon' => 'w3c.png',
    'use_PCRE' => 0,
    'pattern' => 'W3C_CSS_Validator',
    'version' => false,
    'anti_pattern' => ''
  ),
  'SurveyBot' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 1,
    'pattern' => '#SurveyBot/([0-9.]+)?#i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'QuepasaCreep' => array(
    'icon' => 'quepasa.png',
    'use_PCRE' => 0,
    'pattern' => 'QuepasaCreep',
    'version' => false,
    'anti_pattern' => ''
  ),
  'PHP' => array(
    'icon' => 'php.png',
    'use_PCRE' => 1,
    'pattern' => '#PHP[ \/]+([0-9.]+)?#',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'Java' => array(
    'icon' => 'java.png',
    'use_PCRE' => 1,
    'pattern' => '/^Java\/([0-9.]+)?/',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'Yahoo-MMCrawler' => array(
    'icon' => 'yahoo.png',
    'use_PCRE' => 1,
    'pattern' => '#Yahoo-MMCrawler/([0-9.x]+)#i',
    'version' => false,
    'anti_pattern' => ''
  ),
  'MSNBot' => array(
    'icon' => 'msn.png',
    'use_PCRE' => 1,
    'pattern' => '#msnbot[/ ]+([0-9.]+)#i',
    'version' => 1,
    'anti_pattern' => '/(MSNBot-Media)/i'
  ),
  'MSNBot-Media' => array(
    'icon' => 'msn.png',
    'use_PCRE' => 1,
    'pattern' => '/msnbot-media\/([0-9.]+)/i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'Bingbot' => array(
    'icon' => 'msn.png',
    'use_PCRE' => 1,
    'pattern' => '#bingbot/([0-9.]+)#i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'Claymont' => array(
    'icon' => 'claymont.png',
    'use_PCRE' => 0,
    'pattern' => 'Claymont',
    'version' => false,
    'anti_pattern' => ''
  ),

  'Baiduspider' => array(
    'icon' => 'baidu.png',
    'use_PCRE' => 0,
    'pattern' => 'Baiduspider',
    'version' => false,
    'anti_pattern' => ''
  ),

  'Almaden' => array(
    'icon' => 'ibm.png',
    'use_PCRE' => 0,
    'pattern' => 'http://www.almaden.ibm.com/cs/crawler',
    'version' => false,
    'anti_pattern' => ''
  ),
  'Il Trovatore' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 1,
    'pattern' => '/Iltrovatore-Setaccio\/([0-9.]+)/i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'Teoma' => array(
    'icon' => 'teoma.png',
    'use_PCRE' => 0,
    'pattern' => 'Ask Jeeves/Teoma',
    'version' => false,
    'anti_pattern' => ''
  ),
  'Gigabot' => array(
    'icon' => 'gigabot.png',
    'use_PCRE' => 1,
    'pattern' => '#Gigabot/([0-9.]+)#i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'Girafabot' => array(
    'icon' => 'girafabot.png',
    'use_PCRE' => 0,
    'pattern' => 'girafa.com',
    'version' => false,
    'anti_pattern' => ''
  ),
  'Overture' => array(
    'icon' => 'overture.png',
    'use_PCRE' => 1,
    'pattern' => '#Overture[ \-]*WebCrawler#',
    'version' => false,
    'anti_pattern' => ''
  ),
  'WebCopier' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 0,
    'pattern' => 'WebCopier',
    'version' => false,
    'anti_pattern' => ''
  ),
  'HTTrack' => array(
    'icon' => 'httrack.png',
    'use_PCRE' => 1,
    'pattern' => '/HTTrack ?([0-9.x]+)?/',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'WGet' => array(
    'icon' => 'wget.png',
    'use_PCRE' => 1,
    'pattern' => '#Wget/([0-9.]+)?#',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'lwp-request' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 1,
    'pattern' => '#lwp(:|-)+(trivial|request|Simple)/([0-9.]+)#i',
    'version' => 3,
    'anti_pattern' => ''
  ),
  'JetBot' => array(
    'icon' => 'jetbot.png',
    'use_PCRE' => 1,
    'pattern' => '/Jetbot\/([0-9.]+)?/',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'NaverBot' => array(
    'icon' => 'naver.png',
    'use_PCRE' => 1,
    'pattern' => '#NaverBot/([0-9.]+)#i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'Larbin' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 1,
    'pattern' => '/larbin([ \/-_])?([0-9.]+)?/i',
    'version' => 2,
    'anti_pattern' => ''
  ),
  'ObjectsSearch' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 1,
    'pattern' => '/ObjectsSearch\/([0-9.]+)?/',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'Robozilla' => array(
    'icon' => 'robozilla.png',
    'use_PCRE' => 1,
    'pattern' => '/Robozilla\/?(\d(\.\d){0,})?/',
    'version' => false,
    'anti_pattern' => ''
  ),
  'Walhello appie' => array(
    'icon' => 'walhello_appie.png',
    'use_PCRE' => 1,
    'pattern' => '/appie[ \/]([0-9.]+)?.*\(www\.walhello\.com\)/',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'Grub' => array(
    'icon' => 'grub.png',
    'use_PCRE' => 1,
    'pattern' => '/grub-client-([0-9.]+)/',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'Gaisbot' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 1,
    'pattern' => '/Gaisbot\/([0-9.]+)/i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'mozDex' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 1,
    'pattern' => '/mozDex\/([0-9.]+).+\(mozDex;/i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'GeonaBot' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 1,
    'pattern' => '/GeonaBot\/([0-9.]+)/i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'Openbot' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 1,
    'pattern' => '/Openbot\/([0-9.]+)/i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'Boitho' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 1,
    'pattern' => '/boitho\.com(-\w+)?\/([0-9.]+)/i',
    'version' => 2,
    'anti_pattern' => ''
  ),
  'Pompos' => array(
    'icon' => 'pompos.png',
    'use_PCRE' => 1,
    'pattern' => '/Pompos\/([0-9.]+)/i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'Exabot' => array(
    'icon' => 'exabot.png',
    'use_PCRE' => 1,
    'pattern' => '#(NG/([0-9.]+)|Exabot@exava\.com)#',
    'version' => 2,
    'anti_pattern' => ''
  ),
  'Exabot' => array(
    'icon' => 'exabot.png',
    'use_PCRE' => 1,
    'pattern' => '#Exabot/([0-9.]+)#i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'Xenu Link Sleuth' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 1,
    'pattern' => '/Xenu(\'s)? Link Sleuth ?([0-9.]+)?/',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'W3C-checklink' => array(
    'icon' => 'w3c.png',
    'use_PCRE' => 1,
    'pattern' => '/W3C-checklink\/([0-9.]+)/',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'W3C-checklink' => array(
    'icon' => 'w3c.png',
    'use_PCRE' => 1,
    'pattern' => '/W3C-checklink\/([0-9.]+)/',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'Versus' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 1,
    'pattern' => '#versus ?([0-9.]+) ?\(\+http://versus\.integis\.ch\)#i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'FindLinks' => array(
    'icon' => 'findlinks.png',
    'use_PCRE' => 1,
    'pattern' => '#findlinks/?([0-9.]+)? \(\+http://wortschatz\.uni-leipzig\.de/findlinks/\)#i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'wwwster' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 1,
    'pattern' => '#wwwster/([0-9.]+)#i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'Steeler' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 1,
    'pattern' => '#Steeler/([0-9.]+) \(http://www\.tkl\.iis\.u-tokyo\.ac\.jp/~crawler/\)#i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'Ocelli' => array(
    'icon' => 'ocelli.png',
    'use_PCRE' => 1,
    'pattern' => '#Ocelli/([0-9.]+) \(http://www\.globalspec\.com/Ocelli\)#i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'BecomeBot' => array(
    'icon' => 'becomebot.png',
    'use_PCRE' => 1,
    'pattern' => '#Mozilla/5\.0 \(compatible; BecomeBot/([0-9.]+);#i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'Seekbot' => array(
    'icon' => 'seekbot.png',
    'use_PCRE' => 1,
    'pattern' => '#Seekbot/([0-9.]+)#i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'Psbot' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 1,
    'pattern' => '#psbot/([0-9.]+) \(\+http://www\.picsearch\.com/bot\.html\)#i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'IRLbot' => array(
    'icon' => 'irlbot.png',
    'use_PCRE' => 1,
    'pattern' => '#IRLbot/([0-9.]+) \(\+http://irl\.cs\.tamu\.edu/crawler\)#i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'PhpDig' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 1,
    'pattern' => '#PhpDig/([0-9.]+) \(\+http://www\.phpdig\.net/robot\.php\)#i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'gazz' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 1,
    'pattern' => '#gazz/([0-9.]+) \(gazz@nttr\.co\.jp\)#i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'MJ12bot' => array(
    'icon' => 'mj12bot.png',
    'use_PCRE' => 1,
    'pattern' => '#MJ12bot/v([0-9.]+)#i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'getRAX Crawler' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 1,
    'pattern' => '#getRAX/getRAX Crawler ([0-9.]+) \(\+http://www\.getRAX\.com\)#i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'Amfibibot' => array(
    'icon' => 'amfibibot.png',
    'use_PCRE' => 1,
    'pattern' => '#Amfibibot/([0-9.]+) \(Amfibi Robot; http://www\.amfibi\.com; agent@amfibi\.com\)#i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'GigabotSiteSearch' => array(
    'icon' => 'gigabot.png',
    'use_PCRE' => 1,
    'pattern' => '#SiteSearch/([0-9.]+) \(sitesearch\.gigablast\.com\)#i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'ZipppBot' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 1,
    'pattern' => '#ZipppBot/([0-9.]+) \(ZipppBot; http://www\.zippp\.net; webmaster@zippp\.net\)#i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'TurnitinBot' => array(
    'icon' => 'turnitinbot.png',
    'use_PCRE' => 1,
    'pattern' => '#TurnitinBot/([0-9.]+) \(http://www\.turnitin\.com/robot/crawlerinfo\.html\)#i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'KazoomBot' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 1,
    'pattern' => '#KazoomBot/([0-9.dev]+) \(Kazoom; http://www\.kazoom\.ca/bot\.html; kazoombot@kazoom\.ca\)#i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'NetResearchServer' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 1,
    'pattern' => '#NetResearchServer/([0-9.]+)\(loopimprovements\.com/robot\.html\)#i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'gamekitbot' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 1,
    'pattern' => '#gamekitbot/([0-9.]+) \(\+http://www\.uchoose\.de/crawler/gamekitbot/\)#i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'Vagabondo' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 1,
    'pattern' => '#Vagabondo/([0-9.]+)#i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'NetResearchServer' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 1,
    'pattern' => '#NetResearchServer/([0-9.]+)#i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'TheSuBot' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 1,
    'pattern' => '#TheSuBot/([0-9.]+) \(www\.thesubot\.de\)#i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'NPBot' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 1,
    'pattern' => '#NP/([0-9.]+) \(NP; http://www.nameprotect\.com; npbot@nameprotect\.com\)#i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'Cerberian' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 1,
    'pattern' => '#Mozilla/4\.0 \(compatible; Cerberian Drtrs Version-([0-9.]+).*\)#i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'ConveraCrawler' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 1,
    'pattern' => '#ConveraCrawler/([0-9.]+)#i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'search.ch' => array(
    'icon' => 'search.ch.png',
    'use_PCRE' => 1,
    'pattern' => '#search.ch V([0-9.]+) \(spiderman@search\.ch; http://www\.search\.ch\)#i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'ichiro' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 1,
    'pattern' => '#ichiro/([0-9.]+)#i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'CydralSpider' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 1,
    'pattern' => '#CydralSpider/([0-9.]+) \(Cydral Web Image Search; http://www\.cydral\.com\)#i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'Szukacz' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 1,
    'pattern' => '#Szukacz/([0-9.]+)#i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'Patwebbot' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 0,
    'pattern' => 'Patwebbot (http://www.herz-power.de/technik.html)',
    'version' => FALSE,
    'anti_pattern' => ''
  ),
  'SpeedySpider' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 1,
    'pattern' => '#Speedy ?Spider.*/([0-9.]+)?.*entireweb\.com#i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'Mackster' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 0,
    'pattern' => 'Mackster( http://www.ukwizz.com )',
    'version' => FALSE,
    'anti_pattern' => ''
  ),
  'libwww' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 1,
    'pattern' => '/(libwww-FM|libwww-perl)\/?([0-9.]+)?/i',
    'version' => 1,
    'anti_pattern' => '(neomo)'
  ),
  'Python-urllib' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 1,
    'pattern' => '#Python-urllib/([0-9.]+)#i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'thumbshots-de-Bot' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 1,
    'pattern' => '#thumbshots-de-Bot \(Version: ([0-9.]+), powered by www\.thumbshots\.de\)#i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'Digger' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 1,
    'pattern' => '#Digger/([0-9.]+)#i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'Zao' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 1,
    'pattern' => '#Zao/([0-9.]+)#i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'Tutorial Crawler' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 1,
    'pattern' => '#Tutorial Crawler ([0-9.]+)#i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'InelaBot' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 1,
    'pattern' => '#InelaBot/([0-9.]+) \( ?http://inelegant\.org/bot\)#i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'ASPseek' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 1,
    'pattern' => '#ASPseek/([0-9.pre]+)#i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'Neomo' => array(
    'icon' => 'neomo.png',
    'use_PCRE' => 1,
    'pattern' => '#Francis/([0-9.]+)#i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'VoilaBot' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 1,
    'pattern' => '#VoilaBot ?(BETA|/)? ([0-9.]+)#i',
    'version' => 2,
    'anti_pattern' => ''
  ),
  'TutorGigBot' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 1,
    'pattern' => '#TutorGigBot/([0-9.]+) \( \+http://www\.tutorgig\.info \)#i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'CipinetBot' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 0,
    'pattern' => '#CipinetBot (http://www.cipinet.com/bot.html)#i',
    'version' => FALSE,
    'anti_pattern' => ''
  ),
  'ES.NET_Crawler' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 1,
    'pattern' => '#ES\.NET_Crawler/([0-9.]+) \(http://www\.innerprise\.net/\)#i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'eventax' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 1,
    'pattern' => '#eventax/([0-9.]+) \(eventax; http://www\.eventax\.de/; info@eventax\.de\)#i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'stat' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 1,
    'pattern' => '#stat \(?statcrawler@gmail.com\)?#i',
    'version' => FALSE,
    'anti_pattern' => ''
  ),
  'Xaldon WebSpider' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 1,
    'pattern' => '#Xaldon WebSpider ([0-9.b]+)#i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'Faxobot' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 1,
    'pattern' => '#Faxobot/([0-9.]+)#i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'Sherlock' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 1,
    'pattern' => '#sherlock/([0-9.]+)#i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'Holmes' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 1,
    'pattern' => '#Holmes/([0-9.]+)#i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'lmspider' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 0,
    'pattern' => 'lmspider (lmspider@scansoft.com)',
    'version' => FALSE,
    'anti_pattern' => ''
  ),
  'SeznamBot' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 1,
    'pattern' => '#SeznamBot/([0-9.]+)#i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'NG/2.0' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 0,
    'pattern' => 'NG/2.0',
    'version' => FALSE,
    'anti_pattern' => ''
  ),
  'Omni-Explorer' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 1,
    'pattern' => '#OmniExplorer_Bot/([0-9.]+)#',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'WWWeasel' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 1,
    'pattern' => '/WWWeasel Robot ([0-9.]+)/',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'BruinBot' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 0,
    'pattern' => 'BruinBot (+http://webarchive.cs.ucla.edu/bruinbot.html)',
    'version' => FALSE,
    'anti_pattern' => ''
  ),
  'StackRambler' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 1,
    'pattern' => '#StackRambler/([0-9.]+)#',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'aipbot' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 1,
    'pattern' => '#aipbot/([0-9.]+)#',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'JobSpider_BA' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 1,
    'pattern' => '#JobSpider_BA/([0-9.]+)#',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'telnet' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 1,
    'pattern' => '#telnet([0-9.]+) \(noone@example\.org\)#',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'Crawler' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 0,
    'pattern' => 'Crawler (cometsearch@cometsystems.com)',
    'version' => FALSE,
    'anti_pattern' => ''
  ),
  'Istarthere' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 0,
    'pattern' => 'http://www.istarthere.com (spider@istarthere.com)',
    'version' => FALSE,
    'anti_pattern' => ''
  ),
  'LinkWalker' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 0,
    'pattern' => 'LinkWalker',
    'version' => FALSE,
    'anti_pattern' => ''
  ),
  'GoForItBot' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 0,
    'pattern' => 'GOFORITBOT ( http://www.goforit.com/about/ )',
    'version' => FALSE,
    'anti_pattern' => ''
  ),
  'Axadine Crawler' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 0,
    'pattern' => 'axadine/ (Axadine Crawler; http://www.axada.de/; )',
    'version' => FALSE,
    'anti_pattern' => ''
  ),
  'savvybot' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 1,
    'pattern' => '#savvybot/([0-9.]+)#i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'NPBot' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 1,
    'pattern' => '#NP(Bot)?(/([0-9.]+))?.*http://www\.nameprotect\.com.*#i',
    'version' => 3,
    'anti_pattern' => ''
  ),
  'Microsoft URL Control' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 1,
    'pattern' => '#microsoft url control - ([0-9.]+)#',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'WebFilter Robot' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 1,
    'pattern' => '#WebFilter Robot ([0-9.]+)#',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'pipeLiner' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 1,
    'pattern' => '#pipeLiner/([0-9.]+).* \(PipeLine Spider; http://www\.pipeline-search\.com/webmaster\.html.*\)#i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'MoreOver' => array(
    'icon' => 'moreover.png',
    'use_PCRE' => 1,
    'pattern' => '#Moreoverbot/([0-9.]+) \(+http://www\.moreover\.com/\)#i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'Mirago' => array(
    'icon' => 'mirago.png',
    'use_PCRE' => 0,
    'pattern' => 'TheMiragoRobot',
    'version' => FALSE,
    'anti_pattern' => ''
  ),
  'West Wind Internet Protocols' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 1,
    'pattern' => '#West Wind Internet Protocols ?([0-9.]+)?#',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'check_http' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 1,
    'pattern' => '#check_http/([0-9.]+)#i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'Web Downloader' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 1,
    'pattern' => '#Web Downloader/([0-9.]+)#i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'FeedBurner' => array(
    'icon' => 'feedburner.png',
    'use_PCRE' => 1,
    'pattern' => '#FeedBurner/([0-9.]+)#i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'HTMLParser' => array(
    'icon' => 'java.png',
    'use_PCRE' => 1,
    'pattern' => '#HTMLParser/([0-9.]+)#i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'Facebook' => array(
    'icon' => 'facebook.png',
    'use_PCRE' => 1,
    'pattern' => '#facebookexternalhit/([0-9.]+)#i',
    'version' => 1,
    'anti_pattern' => ''
  ),
  'other' => array(
    'icon' => 'robot.png',
    'use_PCRE' => 1,
    'pattern' => '#(Spider|(Ro)?bot|Crawler|Nutch)#i',
    'version' => 1,
    'anti_pattern' => ''
  )
);

return;
/**
 * Routine zum checken der Bilder
 * @version $Id: user_agents.lib.php 6 2015-07-08 07:07:06Z PragmaMx $
 * @copyright 2012
 */
$check = array('chC_ualib_browsers' => 'browser', 'chC_ualib_os' => 'os', 'chC_ualib_robots' => 'robot');
$piclist = array();
foreach ($check as $var => $path) {
    foreach ($$var as $key => $value) {
        $piclist[] = $path . '/' . $value['icon'];
    }
}
$piclist = array_filter(array_unique($piclist));
// print_r($piclist);
$imagepath = realpath(dirname(__file__) . '/../../../modules/Statistics/images/') . DIRECTORY_SEPARATOR;

echo '<pre>';
foreach ($piclist as $key => $value) {
    if (!file_exists($imagepath . $value)) {
        echo($value . "\n");
    }
}
echo '</pre>';


?>