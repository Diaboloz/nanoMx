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

define("_VERCHECK_00", "version control");
define("_VERCHECK_01", "file missing");
define("_VERCHECK_02", "unknown");
define("_VERCHECK_03", "newer");
define("_VERCHECK_04", "older");
define("_VERCHECK_05", "edited");
define("_VERCHECK_06", "ok");
define("_VERCHECK_07", "version");
define("_VERCHECK_08", "file");
define("_VERCHECK_09", "ought to be");
define("_VERCHECK_10", "is");
define("_VERCHECK_11", "status");
define("_VERCHECK_12", "in CVS");
define("_VERCHECK_ALLFIL", "all files");
define("_VERCHECK_15", "No differences found.");
define("_VERCHECK_16", "The external file <i>'%s'</i> of the version control couldn't be read.");
define("_VERCHECK_17", "Error in function getDirContents: no valid directory found: <em>%s</em>!");
define("_VERCHECK_18", "Error in fsockopen()");
define("_VERCHECK_19", "Local copy of version control file <i>'%s'</i> is missing!");
define("_VERCHECK_EDITED", "edited files");
define("_VERCHECK_MISSED", "missing files");
define("_VERCHECK_VCONFL", "version conflicts");
define("_VERCHECK_NOTFOUND", "No %s found");

define("_VERCHECK_UNNECESSARY", "unnecessary files ");
define("_VERCHECK_DEL", "delete Folder and files");
define("_VERCHECK_DELSOME", "selected " . _VERCHECK_DEL);
define("_VERCHECK_DELALL", "all " . _VERCHECK_DEL);
define("_VERCHECK_SUREDELSOME", "Sure that the selected files and folders to be deleted? ");
define("_VERCHECK_SUREDELALL", "Sure, to be deleted all the files and folders ? ");
define("_VERCHECK_UNNNOTFOUND", "We have not found any unnecessary files and folders. ");
define("_VERCHECK_UNNDIRS", "unnecessary folders ");
define("_VERCHECK_UNNFILES", "unnecessary files ");
define("_VERCHECK_SELECTALL", "select all");
define("_VERCHECK_DELNONE", "No files or folders selected. ");
define("_VERCHECK_DELNOALL", "It could not be deleted all files and folders. Please try again, or delete manually.");
define("_VERCHECK_DESCRIBE", "This list of files / folders are outdated. You are no longer used by the system. May cause harmful interference and safety problems. You should therefore be deleted.");

?>