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
 */

defined('mxMainFileLoaded') or die('access denied');
// Achtung, die Konstanten für den Modultitel müssen in den Sprachdateien definiert sein
// Arrayelemente:
// index = Modulname (Ordner)
// 0 = active: 0 = Nein, 1 = Ja
// 1 = view: 1 = Benutzergruppen, 2 = nur Admins, 3 = nur Anonyme, 0 = alle Besucher, 4 = nur Superadmins
// 2 = main_id: Modulblock wo erscheinen soll (Modules_one, Modules_two, Modules_three oder leer für Modules)
$modarry['Banners'] 		= array('0', '1', 'Modules_three');
$modarry['blank_Home'] 		= array('1', '0', 'hided');
$modarry['Board'] 			= array('0', '2', 'Modules_one');
$modarry['Content'] 		= array('1', '0', 'Modules_one');
$modarry['Contest'] 		= array('0', '2', 'Modules_two');
$modarry['Documents'] 		= array('1', '0', 'Modules_one');
$modarry['Downloads'] 		= array('1', '0', 'Modules_one');
$modarry['eBoard'] 			= array('0', '0', 'Modules_one');
$modarry['Encyclopedia'] 	= array('0', '0', 'Modules_one');
$modarry['FAQ'] 			= array('0', '0', 'Modules_two');
$modarry['Feedback'] 		= array('1', '0', 'Modules_two');
$modarry['Forum'] 			= array('0', '2', 'Modules_one');
$modarry['Gallery'] 		= array('0', '2', 'Modules_one');
$modarry['Guestbook'] 		= array('0', '0', 'Modules_three');
$modarry['Impressum'] 		= array('1', '0', 'Modules_two');
$modarry['Kalender'] 		= array('0', '0', 'Modules_one');
$modarry['legal'] 			= array('1', '0', 'Modules_two');
$modarry['LinkMe'] 			= array('0', '0', 'Modules_two');
$modarry['Members_List'] 	= array('1', '1', 'Modules_three');
$modarry['mxBoard'] 		= array('0', '0', 'Modules_one');
$modarry['My_eGallery'] 	= array('0', '0', 'Modules_one');
$modarry['News'] 			= array('1', '0', 'Modules_one');
$modarry['Newsletter'] 		= array('0', '1', 'Modules_two');
$modarry['Private_Messages'] = array('1', '1', 'Modules_three');
$modarry['Recommend_Us'] 	= array('0', '0', 'Modules_two');
$modarry['Reviews'] 		= array('0', '0', 'Modules_one');
$modarry['Schedule'] 		= array('0', '0', 'Modules_one');
$modarry['Search'] 			= array('1', '0', 'Modules_one');
$modarry['Sections'] 		= array('1', '0', 'Modules_one');
$modarry['SiriusGallery'] 	= array('0', '2', 'Modules_one');
$modarry['Siteupdate'] 		= array('0', '0', 'Modules_two');
$modarry['Statistics'] 		= array('0', '1', 'Modules_two');
$modarry['Stories_Archive'] = array('1', '0', 'Modules_one');
$modarry['Submit_News']		= array('1', '1', 'Modules_two');
$modarry['Surveys'] 		= array('0', '0', 'Modules_one');
$modarry['Themetest'] 		= array('0', '1', 'Modules_three');
$modarry['Top'] 			= array('1', '1', 'Modules_two');
$modarry['Topics'] 			= array('1', '0', 'Modules_one');
$modarry['UserGuest'] 		= array('0', '1', 'Modules_three');
$modarry['Userinfo'] 		= array('1', '1', 'hided');
$modarry['User_Registration'] = array('1', '3', 'Modules_three');
$modarry['Web_Links'] 		= array('1', '0', 'Modules_one');
$modarry['Web_News'] 		= array('0', '0', 'Modules_one');
$modarry['Your_Account'] 	= array('1', '0', 'Modules_three');

?>