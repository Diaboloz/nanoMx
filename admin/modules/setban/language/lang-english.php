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
 * $Revision: 175 $
 * $Author: PragmaMx $
 * $Date: 2016-06-30 14:38:26 +0200 (Do, 30. Jun 2016) $
 */

defined('mxMainFileLoaded') or die('access denied');

langdefine("_ADMINBANCONFIG", "Banning Administration");
langdefine("_AUTOBAN", "Deactivate existing user accounts automatically");
langdefine("_AUTOBAN_HEAD","Disable User Account");
langdefine("_CUTWITHCOMMATA", "Seperate the word with comma!");
langdefine("_INFOHOWBAN", "Banning - IP-Adresses");
langdefine("_INFOHOWBANHELP", "<ul>\n  <li>\n    We point out that Bans are to be treated with caution and only one means according to unsuccessful reminders are.</li>\n  <li>\n    You can ban here disturbing People by there IP so long there IP is valid - the IP will change under normal conditions with each new connection of the User.</li>\n  <li>\n    The IPs that you want to close must be separated with a comma ", " (example: 127.0.0.1,192.168.0.1)</li>\n</ul>");
langdefine("_INFOHOWBANMAIL", "Banning - eMailadresses");
langdefine("_INFOHOWBANMAILHELP", "<ul>\n  <li>\n    Here you can ban desired email addresses. With these email addresses there is no registration allowed.\n  </li>\n  <li>\n    As an Administrator you can ban the email address in two kinds:<br />\n    1. Input of the full address (example: adress@url.tld)<br />\n    2. Wildcard Ban: (example: @url.tld)<br />\n    During the input of the full address only this the certified address is banned, during input of a Wildcard Ban each address of the URL is given will be rejected.\n  </li>\n  <li>\n    Banned email addresses must be separated with a comma ", " (example: adresse@url.tld,@url.tld)</li>\n</ul>");
langdefine("_INFOHOWBANNAME", "Banning - Usernames");
langdefine("_INFOHOWBANNAMEHELP", "<ul>\n  <li>\n    Here you can Ban not permitted user names. With this banned username names there is no registration possible.\n  </li>\n  <li>\n    <strong>Note:</strong> If you activate the option &quot;<em>Disable User Account</em>&quot; existing user accounts are possibly deactivated automatically, i.e. the user cannot log-in itself into his user account longer!</li>\n  <li>\n    Banned user names must be separated with a comma &quot;,&quot; (example: ken,sarah,joe)</li>\n</ul>");
langdefine("_IPADDED", "Database is refreshed");

?>