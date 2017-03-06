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
 * $Date: 2015-07-08 09:07:06 +0200 (mer., 08 juil. 2015) $
 */

defined('mxMainFileLoaded') or die('access denied');

define("_ADMINBANCONFIG", "BLOKER");
define("_AUTOBAN", "aktiver eksisterende brugerkontoer automatisk ");
define("_CUTWITHCOMMATA", "Separer med et komma!");
define("_INFOHOWBAN", "Bloker IP");
define("_INFOHOWBANHELP", "<ul>\n  <li>\n    Her kan du blokere fredsforstyrrer\n    sålænge deres IP-adresse er gyldig.</li>\n  <li>\n    Vi vil gør opmærksom på, at blokeringer skal behandles med omtanke\n    og kun bruges efter sucessløse påmindelser.</li>\n  <li>\n    IP'er der skal blokeres skal være med et komma\n    &quot;,&quot; separeret (eksempel: 127.0.0.1,192.168.0.1)</li>\n</ul>");
define("_INFOHOWBANMAIL", "Bloker emailadresser");
define("_INFOHOWBANMAILHELP", "<ul>\n  <li>\n    Her kan du ikke blokere tilladte emailadresser. Med disse\n    emailadresser er ingen registrering mulig.\n  </li>\n  <li>\n    Du kan blokere emailadresser på to måder:<br />\n    1. indtast hele adressen (eksempel: adresse@url.tld)<br />\n    2. Wildcard-blokering: (eksempel: @url.tld)<br />\n    Ved indtastning af hele adressen, bliver kun den ene adresse\n    blokeret. Ved indtastning af en Wildcard blokering bliver alle adresser\n    fra URL'en blokeret.\n  </li>\n  <li>\n    Emailadresser der skal blokeres, må være separeret med et komma\n    &quot;,&quot; (eksempel: adresse@url.tld,@url.tld)</li>\n</ul>");
define("_INFOHOWBANNAME", "Bloker brugernavne");
define("_INFOHOWBANNAMEHELP", "<ul>\n  <li>\n    Her kan du lukke for ikke tilladte\n      brugernavne. En registrering med disse brugernavne er ikke muligt.\n  </li>\n  <li>\n    <strong>Pas på:</strong> Hvis du optionen &quot;<em>deaktivere eksisterende brugerkontoer\n    </em>&quot; aktiverer, bliver eventuelle brugerkontoer\n    automatisk deaktiveret. Det vil sige at brugeren\n    ikke længere kan logge ind!</li>\n  <li>\n    Brugernavne der skal blokeres, skal separeres med et komma\n    &quot;,&quot; (eksempel: søren,jens,sarah)</li>\n</ul>");
define("_IPADDED", "Karantænefil blev aktiveret.");

?>