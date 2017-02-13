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
 * $Revision: 214 $
 * $Author: PragmaMx $
 * $Date: 2016-09-15 15:51:34 +0200 (Do, 15. Sep 2016) $
 *
 * @package pragmaMx
 */

defined('mxMainFileLoaded') or die('access denied');

$toolbarlangarray = array('_NOACTION' => 'Venligst kun ét valg!',
    '_EXPANDALL' => 'åben alle',
    '_COLLAPSEALL' => 'Luk alle',
    '_ADD' => 'Tilføj',
    '_ACCEPT' => 'Overtag',
    '_BACK' => 'tilbage',
    '_CANCEL' => 'Abort',
    '_CATEGORYS' => 'kategorier',
    '_COLOR' => 'farver',
    '_COMMENTS' => 'kommentar',
    '_CONFIG' => 'indstillinger',
    '_CONTENT' => 'indhold',
    '_COPY' => 'kopi',
    '_CPANEL' => 'Admin Panel',
    '_DELETE' => 'Slet',
    '_DOWN' => 'nedenfor',
    '_DOWNLOAD' => 'Hent',
    '_EDIT' => 'Skift',
    '_FOLDER' => 'folder',
    '_HELP' => 'Hjælp',
    '_HOME' => 'Hjem',
    '_IMAGE' => 'billeder',
    '_LINK' => 'link',
    '_MAIL' => 'Email',
    '_MOVE' => 'Flyt',
    '_NEW' => 'nyt',
    '_NEWS' => 'artikel',
    '_NEXT' => 'mere',
    '_PLUS' => 'Tilføj',
    '_PREVIEW' => 'Eksempel',
    '_PUBLISH' => 'offentliggør',
    '_REDIRECT' => 'fremad',
    '_REFRESH' => 'opdater',
    '_SAVE' => 'Gem',
    '_SETTINGS' => 'indstillinger',
    '_TOOLS' => 'optioner',
    '_TRASH' => 'Trash',
    '_UNPUBLISH' => 'Block',
    '_UP' => 'ovenstående',
    '_UPLOAD' => 'Upload',
    '_USER' => 'bruger',
    '_VOTE' => 'vurder',
    '_ZOOM' => 'Zoom',
    '_SELECTTIME' => 'Vælg tid',
    '_DEFAULT' => 'Standard',
	'_HTML_EDIT' => 'Edit HTML',
	'_CSS_EDIT' => 'Edit CSS', 
	'_WRITABLE' => 'writable',
	'_NOWRITABLE' =>'not writable',    
	'_ARCHIVE'=>'Archiv',
	'_EXPORT'=>"Export",
	'_IMPORT'=>"Import",
	);

foreach ($toolbarlangarray as $constant => $value) {
    defined($constant) OR define($constant, $value);
}

?>