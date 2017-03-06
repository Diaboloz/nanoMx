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

define("_ADMINBANCONFIG", "Administration du blocage IP et bannissement des courriels");
define("_AUTOBAN", "Désactiver automatiquement les comptes utilisateurs existants");
define("_CUTWITHCOMMATA", "Séparer avec une virgule!");
define("_INFOHOWBAN", "Bannissement d'IPs");
define("_INFOHOWBANHELP", "<ul>\n  <li>\n    Nous attirons votre attention sur le fait que les bannissements doivent êtres effectués avec prudence et en toute connaissance de cause.</li>\n  <li>\n    Vous pouvez bannir ici les IP des personnes indésirables aussi longtemps que leurs IP sont identiques. Normalement (sauf IP dynamique), l'IP ne changera pas pour chaque nouvelle connexion utilisateur.</li>\n  <li>\n    Les IP que vous voulez bloquer doivent être séparées avec une virgule &quot;,&quot; (exemple: 127.0.0.1,192.168.0.1)</li>\n</ul>");
define("_INFOHOWBANMAIL", "Bannissement d'emails");
define("_INFOHOWBANMAILHELP", "<ul>\n  <li>\n    Vous pouvez bannir ici les emails. Leur enregistrement est alors impossible.\n  </li>\n  <li>\n    En tant qu'administrateur vous pouvez bannir les emails de deux façons:<br />\n    1. Entrer une adresse complète (exemple: adresse@domaine.fr)<br />\n    2. Une adresse partielle: (exemple: @domaine.fr)<br />\n    Quand vous entrez une adresse complète, seulement cette adresse exacte est bannie. Quand vous entrez une adresse partielle, toutes les adresses de cette URL sont rejetées.\n  </li>\n  <li>\n    Les emails bannis doivent être séparées avec une virgule &quot;,&quot; (exemple: uneadresse@domaine.fr,@domaine.fr)</li>\n</ul>");
define("_INFOHOWBANNAME", "Bannissement d'utilisateurs");
define("_INFOHOWBANNAMEHELP", "<ul>\n  <li>\n    Vous pouvez bannir ici les noms d'utilisateurs interdits. L'enregistrement est impossible avec ces noms d'utilisateurs.\n  </li>\n  <li>\n    <strong>Note:</strong> Si vous activez l'option &quot;<em>Désactiver automatiquement les comptes utilisateurs existants</em>&quot; alors, les utilisateurs concernés ne pourrons plus se connecter avec leurs comptes!</li>\n  <li>\n    Les noms d'utilisateurs que vous voulez bloquer doivent être séparés avec une virgule &quot;,&quot; (exemple: jaques,sarah,gilbert)</li>\n</ul>");
define("_IPADDED", "IP ajoutée");

?>
