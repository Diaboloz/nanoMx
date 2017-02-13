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

/* This is a list for automatic related links on article internal page */
/* For this I used a multi-dimensional array so we can show the links titles */
/* as we want, doesn't matter the case of the string in the article. You can */
/* add or remove links from this array as you wish to fit your needs */
$relatedarray = array ("gpl" => array ("GPL" => "http://www.gnu.org"),
    "maax-design" => array ("pragmaMx.org" => "http://www.pragmaMx.org"),
    "vkp" => array ("pragmaMx.org" => "http://www.pragmaMx.org"),
    "vkpMx" => array ("pragmaMx.org" => "http://www.pragmaMx.org"),
    "VKP-M" => array ("VKP-Mxxx" => "http://www.pragmaMx.org"),
    "pragmaMx" => array ("pragmaMx.org" => "http://www.pragmaMx.org"),
    "shiba-design" => array ("shiba-design" => "http://www.shiba-design.de"),
    "linux" => array ("Linux.com" => "http://www.linux.com"),
    "gnu" => array ("GNU Project" => "http://www.gnu.org"),
    "gimp" => array ("The GIMP" => "http://www.gimp.org"),
    "php" => array ("PHP HomePage" => "http://www.php.net"),
    "sourceforge" => array ("SourceForge" => "http://www.sourceforge.net"),
    "source forge" => array ("SourceForge" => "http://www.sourceforge.net"),
    "opensource" => array ("OpenSource" => "http://www.opensource.org"),
    "open source" => array ("OpenSource" => "http://www.opensource.org"),
    "mysql" => array ("MySQL Database Server" => "http://www.mysql.com"),
    "script" => array ("HotScripts" => "http://www.hotscripts.com"),
    "apache" => array ("Apache Web Server" => "http://www.apache.org"),
    "google" => array ("Google Search Engine" => "http://www.google.com"),
    "translat" => array ("Babelfish Translator" => "http://babelfish.altavista.com"),
    "w3" => array ("W3 Consortium" => "http://www.w3.org"),
    "css" => array ("CSS Standard" => "http://www.w3.org/Style/CSS"),
    " html" => array ("HTML Standard" => "http://www.w3.org/MarkUp"),
    "xhmtl" => array ("XHTML Standard" => "http://www.w3.org/MarkUp"),
    "openoffice" => array ("Open Office" => "http://www.openoffice.org"),
    "open office" => array ("Open Office" => "http://www.openoffice.org"),
    "postgre" => array ("PostgreSQL" => "http://www.postgresql.org"),
    "mozilla" => array ("Mozilla" => "http://www.mozilla.org"),
    "netscape" => array ("Netscape" => "http://www.netscape.com"),
    "freshmeat" => array ("Freshmeat" => "http://www.freshmeat.net"),
    "slashdot" => array ("Slashdot" => "http://www.slashdot.org"),
    "pov-ray" => array ("POV Ray" => "http://www.povray.org"),
    "seti" => array ("SETI Institute" => "http://www.seti.org"),
    "amazon" => array ("Amazon.com" => "http://www.amazon.com"),
    );
/* Multi-dimensional array end here */
?>