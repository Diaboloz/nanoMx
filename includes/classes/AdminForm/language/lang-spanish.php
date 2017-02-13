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
 * $Revision: 153 $
 * $Author: PragmaMx $
 * $Date: 2016-05-11 21:04:30 +0200 (Mi, 11. Mai 2016) $
 *
 * @package pragmaMx
 */

defined('mxMainFileLoaded') or die('access denied');

$toolbarlangarray = array('_NOACTION' => 'Please select option !',
    '_EXPANDALL' => 'expand all',
    '_COLLAPSEALL' => 'collapse all',
    '_ADD' => 'Add',
    '_ACCEPT' => 'Accept',
    '_BACK' => 'Back',
    '_CANCEL' => 'Cancel',
    '_CATEGORYS' => 'Categorys',
    '_COLOR' => 'Color',
    '_COMMENTS' => 'Comment',
    '_CONFIG' => 'Config',
    '_CONTENT' => 'Content',
    '_COPY' => 'Copy',
    '_CPANEL' => 'Admin Panel',
    '_DELETE' => 'Delete',
    '_DOWN' => 'Down',
    '_DOWNLOAD' => 'Download',
    '_EDIT' => 'Edit',
    '_FOLDER' => 'Folder',
    '_HELP' => 'Help',
    '_HOME' => 'Home',
    '_IMAGE' => 'Image',
    '_LINK' => 'Link',
    '_MAIL' => 'Email',
    '_MOVE' => 'Move',
    '_NEW' => 'New',
    '_NEWS' => 'News',
    '_NEXT' => 'Next',
    '_PLUS' => 'Add',
    '_PREVIEW' => 'Preview',
    '_PUBLISH' => 'Publish',
    '_REDIRECT' => 'Redirect',
    '_REFRESH' => 'Refresh',
    '_SAVE' => 'Save',
    '_SETTINGS' => 'Settings',
    '_TOOLS' => 'Tools',
    '_TRASH' => 'Trash',
    '_UNPUBLISH' => 'unpublish',
    '_UP' => 'Up',
    '_UPLOAD' => 'Upload',
    '_USER' => 'User',
    '_VOTE' => 'Vote',
    '_ZOOM' => 'Zoom',
    '_SELECTTIME' => 'seleccionar el tiempo de',
    '_DEFAULT' => 'Standard',
	'_HTML_EDIT' => 'Edit HTML',
	'_CSS_EDIT' => 'Edit CSS', 
	'_WRITABLE' => 'writable',
	'_NOWRITABLE' =>'not writable',
    );

foreach ($toolbarlangarray as $constant => $value) {
    defined($constant) OR define($constant, $value);
}

?>