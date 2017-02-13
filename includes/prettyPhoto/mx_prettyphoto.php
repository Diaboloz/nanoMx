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
 * based on prettyPhoto-plugin by Olaf Herfurth/TerraProject (Germany)
 * - http://www.terraproject.de
 * enhanced by BdMdesigN
 * - http://www.osc4pragmamx.org/
 *
 * Doku:  http://www.no-margin-for-errors.com/projects/prettyPhoto-jquery-lightbox-clone/
 */

defined('mxMainFileLoaded') or die('access denied');

/*

Fügt die Slimbox der Seite hinzu

*/

function pmxAddprettyPhoto()
{
    pmxHeader::add_lightbox() ;
}

/*
	Parameter:
		$image 	- Pfad zum Bild
		$title	- Optional: Titel des Bildes (wenn leer, wird der Bildname verwendet)
		$alt	- Optional: Beschreibung oder HTML-Code für Thumbnail (Wenn leer, wird der Bildname verwendet)
		$mode   - Optional: Galeriename ohne eckige Klammern

	Rückgabewert:
		String - beinhaltet den HTML-Code für den Link

*/

function pmxCreateLinkForPrettyPhoto ($image, $title = "", $alt = "", $mode = "")
{
    // $border = (int)$border;
    // $size = GetImageSize($image);
    $para = "";
    if (empty($title)) {
        $title = pathinfo($image, PATHINFO_FILENAME);
    }
    $para .= " title=\"" . $title . "\"";

    /*    if (empty($size[3])) {
        if (MX_IS_ADMIN) {
            return "<span class=\"tiny\">missing image:<br />" . $image . "</span>";
        }
        return $alt;
    }
    */
    if (empty($alt)) {
        $alt = pathinfo($image, PATHINFO_BASENAME);
    }
    if (empty($mode)) {
        $more = "";
    } else {
        $more = "[\"" . $mode . "\"]";
    }
    $para = '<a href="' . $image . '" rel="prettyPhoto' . $more . '" ' . $para . '>' . $alt . '</a>';
    return $para;
}

?>
