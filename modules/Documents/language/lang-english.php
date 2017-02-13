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
 * Author: Olaf Herfurth / TerraProject  http://www.tecmu.de
 *
 * $Revision: 6 $
 * $Author: PragmaMx $
 * $Date: 2015-07-08 09:07:06 +0200 (Mi, 08. Jul 2015) $
 */

defined('mxMainFileLoaded') or die('access denied');

define("_DOCUMENTS_TITLE", "Documents");

/* books */

define("_DOCU", "Document");
// define("_DOCS_CREATED","created");
define("_DOCS_CHANGED", "changed");
define("_DOCS_PAGECOUNT", "Number per page");
define("_DOCS_VIEWCONTENT", "show content");
define("_DOCS_MOVEUP", "move up");
define("_DOCS_MOVEDN", "move down");
define("_DOCS_POSITION", "Position");
define("_DOCS_PUBLISH", "Publish");
define("_DOCS_ACCESS", "Access");
define("_DOCS_OWNER", "Owner");
define("_DOCS_PUBLISHED", "published");
define("_DOCS_UNPUBLISHED", "unpublished");
define("_DOCS_EDIT", "Edit Document");
define("_DOCS_NEW", "New Document");

define("_DOCS_PAGE_NEW", "New");
define("_DOCS_PAGE_EDIT", "Edit");

define("_DOCS_SECTION", "Section");
define("_DOCS_EDIT_TEXT", "Here you can edit the information on the document");
define("_DOCS_INFO", "Specify information about the document. This information is not displayed.");
define("_DOCS_ALIAS", "alias");
define("_DOCS_ALIAS_TEXT", "Please specify a 'alias'-name (optional)");
define("_DOCS_KEYWORDS", "Keywords");
define("_DOCS_KEYWORDS_TEXT", "Please enter here the comma separated keywords to the text. So that the search is made easier.");
define("_DOCS_NEW_BOOK", "New Document");
define("_DOCS_NEW_CONTENT", "New Content");
define("_DOCS_MOVECONTENT", "Selected items will be moved to the selected item. You can also deselect individual items again. <br /> WARNING: possibly minor item will move with it!");

define("_DOCS_CHILDS", "included items");
define("_DOCS_CONTENTDELETEINFO", "Selected items are deleted along with their attachments! Under any item will be moved to the parent article.");
define("_DOCS_DELETEINFO", "Selected documents are deleted, as are all the items in them with your attachments! You can also deselect individual documents.");
define("_DOCS_INDEX", "Index");
define("_DOCS_TITLE", "Title");
define("_DOCS_TITLE_TEXT", "Input title of document");
define("_DOCS_LANGUAGE", "select language");
define("_DOCS_LANGUAGE_TEXT", "Please select the language in which the document should be displayed. If you select ALL, it is always displayed.");
define("_DOCS_PREAMBLE", "Introduction to the document");
define("_DOCS_PREAMBLE_TEXT", "The introduction to the document will appear on the homepage. If left blank, the short description of the document used.");

define("_DOCS_COPYRIGHT", "Copyright");
define("_DOCS_COPYRIGHT_TEXT", "Here, please enter the possibly copyrights");
define("_DOCS_SHORTDESC", "Short description");
define("_DOCS_SHORTDESC_TEXT", "Short description of the document appears on the home page of the document, not on the module's home page.");

define("_DOCS_USERGROUP", "Access");
define("_DOCS_USERGROUP_TEXT", "Select which user group has access to the document");

/* content*/
define("_DOCS_CONTENT_EDIT", "Edit Content");
define("_DOCS_CONTENT_TITLE", "Title Content");
define("_DOCS_CONTENT_TITLE_TEXT", "Please specify the title of the Content");

/* Search */
define("_DOCS_SEARCH", "Search");
define("_DOCS_SEARCH_RESULTS_TEXT", "The following pages were found with their search patterns");
define("_DOCS_SEARCH_NORESULTS_TEXT", "There are no results that match the search parameters.");
define("_DOCS_SEARCHMASK", "search patterns");
define("_DOCS_SEARCHINFO", "Here are the keywords entered separated by a comma");

/*config*/
define("_DOCS_CONFIG", "Configuration");
define("_DOCS_CONF_RIGHTBLOCKS", "show right blocks");

define("_DOCS_CONF_TITLE", "Title of Modul");
define("_DOCS_CONF_TITLE_TEXT", "Here specify a module title. ");

define("_DOCS_CONF_STARTPAGE", "Configuration Modul");
define("_DOCS_CONF_STARTPAGE_TEXT", "");

define("_DOCS_CONF_LOGGING", "record changes");
define("_DOCS_CONF_LOGGING_TEXT", "When enabled, the date and users are written changes to the documents in a log table.");

define("_DOCS_CONF_BLOGPAGE", "Configuration Blog");
define("_DOCS_CONF_BLOGPAGE_TEXT", "");
define("_DOCS_CONF_INDEXPAGE", "Configuration Category View");
define("_DOCS_CONF_INDEXPAGE_TEXT", "");

define("_DOCS_CONF_RIGHTS", "Configuration Rights");
define("_DOCS_CONF_RIGHTS_TEXT", "");
// define("_DOCS_CONF_INDEXVIEW","Inhaltsübersicht anzeigen");
// define("_DOCS_CONF_INDEXVIEW_TEXT","Wenn aktiviert wird eine Übersicht aller Dokumente angezeigt.");
define("_DOCS_CONF_BLOGVIEW", "View");
define("_DOCS_CONF_BLOGVIEW_TEXT", "'Category View' - shows a summary of the documents. 'View Blog' - displays the latest item in a blog view.");

define("_DOCS_CONF_BREADCRUMP", "show breadcrump ");
define("_DOCS_CONF_BREADCRUMP_TEXT", "");
define("_DOCS_CONF_PREAMBLE", "View full introduction");
define("_DOCS_CONF_PREAMBLE_TEXT", "When activated, is displayed on the home page of the module, the full introduction of the documents. If disabled, only the number are displayed character, as indicated in the following input.");
define("_DOCS_CONF_CHARCOUNT", "length Introduction");
define("_DOCS_CONF_CHARCOUNT_TEXT", "Number of characters for the length of the introduction specify. Active only if above setting is disabled.");
define("_DOCS_CONF_INDEXCOUNT", "Index");
define("_DOCS_CONF_INDEXCOUNT_TEXT", "0 = no table of contents for each document on the home page, value> 0 indicates the depth of the table of contents for all documents in the index, which is shown.");
define("_DOCS_CONF_SEARCHCOUNT", "Number of search results");
define("_DOCS_CONF_SEARCHCOUNT_TEXT", "Specify how many search results to be shown up.");
define("_DOCS_CONF_LANGUAGE", "Language");
define("_DOCS_CONF_LANGUAGE_TEXT", "new items to be created in the chosen language");
define("_DOCS_CONF_TABCOUNT", "columns count");
define("_DOCS_CONF_TABCOUNT_TEXT", "Number of columns in which the documents / articles on the home page will be displayed.");

define("_DOCS_PAGE_NEWS", "SHow new article");
define("_DOCS_PAGE_NEWS_TEXT", "When enabled new content appears on the home page of the module");

define("_DOCS_PAGE_NEWSCOUNT", "Period for new/modified content");
define("_DOCS_PAGE_NEWSCOUNT_TEXT", "(in days). How long items are marked as New or Modified");

define("_DOCS_PAGE_CHANGES", "Modified Contents");
define("_DOCS_PAGE_CHANGES_TEXT", "When enabled, displays modified article on the home page of the module.");
define("_DOCS_PAGE_CHANGESCOUNT", "Content count");
define("_DOCS_PAGE_CHANGESCOUNT_TEXT", "maximum number, how many items are displayed as 'NEW' or 'Modified' or as a blog on the home page.");

/* attachments */
define("_DOCS_ATTACHMENTS", "Attachments");
define("_DOCS_ATTACH_DELETE", "Select to delete this attachment.");
define("_DOCS_ATTACH_MAX", "max. Attachments");
define("_DOCS_ATTACH_MAX_TEXT", "max. numbers of attachments ");

define("_DOCS_CONF_ATTACH", "Configuration Attachments");
define("_DOCS_CONF_ATTACH_TEXT", "This determines the behavior of file attachments for documents. Attachments are displayed below the document in a download list and can be downloaded at the User.");
define("_DOCS_CONF_ATTACH_ON", "attachments allow");
define("_DOCS_CONF_ATTACH_ON_TEXT", "When enabled, attachments are appended to the document.");
define("_DOCS_CONF_ATTACH_MAXSIZE", "max. filsize");
define("_DOCS_CONF_ATTACH_MAXSIZE_TEXT", "in kByte for each file");
define("_DOCS_CONF_ATTACH_PATH", "Directory");
define("_DOCS_CONF_ATTACH_PATH_TEXT", "here Specify the directory where the file attachments to be saved.");
define("_DOCS_CONF_ATTACH_MEDIA", "Show Media Files");
define("_DOCS_CONF_ATTACH_MEDIA_TEXT", "When activated, the Media (mp3, mp4, images) displayed below the content and not the download list.");
define("_DOCS_CONF_ATTACH_MAXWIDTH", "max. Width of media files for display");
define("_DOCS_CONF_ATTACH_MAXWIDTH_TEXT", "Here specify the width in pixels how wide media files to be displayed in the content. The specification please consult with the used Theme. ");
define("_DOCS_CONF_ATTACH_MAXWIDTHTHUMB", "max. size of Thumbnails");
define("_DOCS_CONF_ATTACH_MAXWIDTHTHUMB_TEXT", "input the max. size of thumbnails. These images are then displayed in a lightbox.");
define("_DOCS_CONF_ATTACH_MAXHEIGHT", "max. Height of media files");
define("_DOCS_CONF_ATTACH_MAXHEIGHT_TEXT", "Here specify the height in pixels, how much media files to be displayed in the content. The specification please consult with the used Theme. ");

define("_DOCS_CONF_PAGE", "Document Page Settings");
define("_DOCS_CONF_PAGE_TEXT", "");
define("_DOCS_PAGE_INDEX", "Show Index");
define("_DOCS_PAGE_INDEX_TEXT", "Activated when a directory is displayed on every page.");
define("_DOCS_PAGE_INDEXFULL", "Index Show complete");
define("_DOCS_PAGE_INDEXFULL_TEXT", "When activated, the complete table of contents is always visible on each page.");
define("_DOCS_PAGE_LASTEDITOR", "Show last author");
define("_DOCS_PAGE_LASTEDITOR_TEXT", "When enabled, the contribution is below the last modification date and the author.");
define("_DOCS_PAGE_VIEWKEYWORDS", "Show Keywords");
define("_DOCS_PAGE_VIEWKEYWORDS_TEXT", "When enabled, the keywords will appear under the article.");
define("_DOCS_PAGE_VIEWNAVIGATION", "Show Navigation ");
define("_DOCS_PAGE_VIEWNAVIGATION_TEXT", "When activated, button will navigate to the below article");
define("_DOCS_PAGE_CREATOR", "Show Creator");
define("_DOCS_PAGE_CREATOR_TEXT", "When activated, is displayed below the contribution of the Creator.");
define("_DOCS_PAGE_VIEWRATING", "View Rating");
define("_DOCS_PAGE_VIEWRATING_TEXT", "When enabled, the rating is displayed on the item.");

define("_DOCS_PAGE_VIEWSOCIAL", "View Social Links");
define("_DOCS_PAGE_VIEWSOCIAL_TEXT", "When enabled, displays links to various social networks.");

define("_DOCS_PAGE_EDITORS", "Who can change the items");
define("_DOCS_PAGE_EDITORS_TEXT", "Specify which users create, change, and group items must. Here is 'user' specified edit all user groups. Administrators can always see everything. Only an administrator can delete.");
define("_DOCS_PAGE_EDITOR_RIGHTS", "Editor may publish");
define("_DOCS_PAGE_EDITOR_RIGHTS_TEXT", "Please specify whether Editors may publish new articles.");

define("_DOCS_PAGE_VIEWSIMILAR", "Show similar items");
define("_DOCS_PAGE_VIEWSIMILAR_TEXT", "When enabled, displays a list of similar items.");
define("_DOCS_PAGE_SIMILARCOUNT", "Count of similar items");
define("_DOCS_PAGE_SIMILARCOUNT_TEXT", "Here, specify how many similar items to display. Similar items are displayed only if the article keywords were deposited.");

define("_DOCS_PAGE_PRINT", "Show Print");
define("_DOCS_PAGE_PRINT_TEXT", "When enabled, the user will see a printer icon through which to view a printer-friendly display of the document.");

define("_DOCS_CONF_INTRO", "Introduction to the module");
define("_DOCS_CONF_INTRO_TEXT", "Write an introduction to the module. This appears on the module homepage.");

define("_DOCS_CONF_LINK", "Configuration links");
define("_DOCS_CONF_LINK_TEXT", "");
define("_DOCS_PAGE_VIEWBOOKLINK", "items link");
define("_DOCS_PAGE_VIEWBOOKLINK_TEXT", "When activated, all the words that correspond to tracks other items, linked to these articles.");
define("_DOCS_PAGE_VIEWBOOKBASE", "Link from all documents");
define("_DOCS_PAGE_VIEWBOOKBASE_TEXT", "If activated for o.g. Linking all the documents used. If disabled, only the document which contains the current items.");
define("_DOCS_PAGE_VIEWENCYLINKS", "Create links to the encyclopedia");
define("_DOCS_PAGE_VIEWENCYLINKS_TEXT", "When enabled, the encyclopedia of terms in the texts can be linked.");
define("_DOCS_PAGE_INDEX_NEW", "Changes mark");
define("_DOCS_PAGE_INDEX_NEW_TEXT", "When enabled, the table of contents New and amended items are marked");
// define("_DOCS_STATUS","Zeitangaben verwenden");
// define("_DOCS_STATUS_TEXT","Wenn aktiviert, werden nachstehende Zeitangaben für die Veröffentlichung verwendet.");
// define("_DOCS_STARTTIME","Starttermin");
// define("_DOCS_STARTTIME_TEXT","Sie können hier eine Startzeit für den Artikel angeben. Erst ab diesem Zeitpunkt wird der Artikel im Frontend angezeigt.");
// define("_DOCS_ENDTIME","Endtermin");
// define("_DOCS_ENDTIME_TEXT","Sie können hier eine Endzeit für den Artikel angeben. Ab diesem Zeitpunkt wird der Artikel im Frontend nicht mehr angezeigt. Liegt der Endtermin vor dem Starttermin, wird der Artikel unbegrenz ab dem Starttermin angezeigt.");
define("_DOCS_VIEWTITLE", "Show title");
define("_DOCS_VIEWTITLE_TEXT", "If the module title on the article page to be displayed?");
define("_DOCS_VIEWVIEWS", "Show Hits");
define("_DOCS_VIEWVIEWS_TEXT", "");
define("_DOCS_LINKTITLE", "Link Title");
define("_DOCS_LINKTITLE_TEXT", "If the module titles are Linkable?");
define("_DOCS_VIEWSEARCH", "Show Search");
define("_DOCS_VIEWSEARCH_TEXT", "Should the search form displayed on the items page?");

define("_DOCS_META_PAGE", "metadata options");
define("_DOCS_META_PAGE_TEXT", "Here, the metadata can be customized. Are no entries, then automatically the default entries are used.");
define("_DOCS_META_CANONICAL", "Canonical URL");
define("_DOCS_META_CANONICAL_TEXT", "Here specify the source URL of the article to avoid duplicate content. Specification including 'http://'");
define("_DOCS_META_ROBOTS", "Robots");
define("_DOCS_META_ROBOTS_TEXT", "Robot statement");
define("_DOCS_META_ALTERNATE", "additional meta-tags");
define("_DOCS_META_ALTERNATE_TEXT", "Here, additional META tags for the header to be specified. Specify the information below and complete the on-comma separated WITHOUT and final brackets (eg. Link rel = \"robots \" content = \"all \" meta name = \"author \" ....) both exapmles however, are already integrated.");
define("_DOCS_META_REVISIT", "Revisit");
define("_DOCS_META_REVISIT_TEXT", "number of days for revisit.");
define("_DOCS_META_AUTHOR", "Author");
define("_DOCS_META_AUTHOR_TEXT", "optionally specify different author.");

/* tools */
define("_DOCS_TOOLS", "Advanced Features");
define("_DOCS_TOOLS_TEXT", "");
define("_DOCS_TOOLS_IMPORT", "Import Features");
define("_DOCS_TOOLS_IMPORT_TEXT", "from various other modules, the content can be imported into Documents. The data is copied from the tables, not deleted");
define("_DOCS_TOOLS_IMPORT_SELECT", "Select Modul");
define("_DOCS_TOOLS_IMPORT_SELECT_TEXT", "Select here from which module will be imported. There is no content to be imported twice. The content of the module is imported into a document with the following names.");
define("_DOCS_TOOLS_DB", "Database Functions");
define("_DOCS_TOOLS_DB_TEXT", "Tools to maintain the database of the module");
define("_DOCS_TOOLS_IMPORT_DOC", "Import in Document");
define("_DOCS_TOOLS_IMPORT_DOC_TEXT", "Enter a name for the base document, in which the contents are imported. This field is blank, the module name is used");

/* extendet settings */
define("_DOCS_EXTENDET_SETTINGS", "Extended Settings");
/* sociol */
define("_DOCS_SOCIAL_INFO_FACEBOOK", "2 clicks for more protection: Only when you click here, will the button be active and you can send your recommendation to Facebook. When activating data is transmitted to third parties - see i");
define("_DOCS_SOCIAL_INFO_TWITTER", "2 clicks for more protection: Only when you click here, will the button be active and you can send your recommendation to Twitter. When activating data is transmitted to third parties - see i");
define("_DOCS_SOCIAL_INFO_GPLUS", "2 clicks for more protection: Only when you click here, will the button be active and you can send your recommendation to Google+. When activating data is transmitted to third parties - see i");
define("_DOCS_SOCIAL_INFO_HELP", "If you enable these fields by clicking, information is transmitted to Facebook, Twitter or Google in the United States and may also be stored there.");
define("_DOCS_SOCIAL_INFO_TOOLS", "Permanently activate and accept data transmission:");

/* diverse */
define("_DOCS_START", "Start");
define("_DOCS_ADMIN_PANEEL", "Administration");
define("_DOCS_PREVIOUS", "previous");
define("_DOCS_NEXT", "next");
define("_DOCS_MOVE", "move");
define("_DOCS_FROM", "from");
define("_DOCS_ACTION", "Action");
define("_DOCS_HISTORY", "History");
define("_DOCS_LASTCHANGE", "last change");
define("_DOCS_NEWCONTENT", "new items");
define("_DOCS_LASTCHANGES", "laste changes");
define("_DOCS_LINKS", "Links");
// define("_ATTACHMENTS","Attachments");
// define("_READMORE","read more");
define("_DOCS_VIEW_INDEX", "view index");
define("_DOCS_VIEW_BLOG", "view blog");
define("_DOCS_VIEW_LOG", "view log");

define("_DOCS_DOWNLOAD", "Download");
define("_DOCS_ATTACHMENT", "Attachments");
define("_DOCS_PAGE_SIMILAR", "These articles could be interesting for you:");

define("_DOCS_FILENAME", "Filename");
define("_DOCS_FILESIZE", "Filesize");
define("_DOCS_FILETYPE", "MIME-Type");
define("_DOCS_FILETITLE", "File title");

define("_DOCS_URL", "Link to original article");
define("_DOCS_URL_TEXT", "This article comes from:");
/* error */

define("_DOCS_NESTEDSET_ERROR", "Errors were found in the database structure of the module. Click on 'Options' to database tools.");
define("_DOCS_NESTEDSET_IO", "Database structure correct.");
define("_DOCS_REPAIR", "Repair");
define("_DOCS_DB_REPAIR", "Repair Databas structure");
define("_DOCS_DB_REPAIR_TEXT", "It examines the data structure for the module and is created with errors, an error document.");
define("_DOCS_DB_DELLOG", "delete log table");
define("_DOCS_DB_DELLOG_TEXT", "It will be deleted ALL entries for this module from the log table!");
define("_DOCS_DB_DELLOG_ACTION", "Log table has been deleted! ");
define("_DOCS_IMPORT_ACTION", " Records are imported.");

/* --- */
define("_DOCS_CONF_LINKOTHER", "link in other modules");
define("_DOCS_CONF_LINKOTHER_TEXT", "If the items are linked in other modules?");
define("_DOCS_LINK_ALL", "each occurrence");
define("_DOCS_LINK_FIRST", "only the first occurrence");
define("_DOCS_CONF_LINKCOUNT", "Number of links");
define("_DOCS_CONF_LINKCOUNT_TEXT", "How often in the content of the items should be linked?");
define("_DOCS_PAGE_VIEWMODULELINK", "May link other modules?");
define("_DOCS_PAGE_VIEWMODULELINK_TEXT", "Can other modules in this module link terms?");

define("_DOCS_NEW2", "New");
define("_DOCS_DEFAULT", "Standard");
define("_DOCS_UPDATE", "Update");
define("_DOCS_VIEW_LIST", "List");
define("_DOCS_CONF_TABCOUNT_LIST_TEXT", "Number of Columns - applies only to list view");
// define("_DOCS_NEW_DOCUMENTS","New document(s)");


define("_DOCS_CONF_INSERTFIRST", "insertion sequence");
define("_DOCS_CONF_INSERTFIRST_TEXT", "Select at which point new documents are inserted in the parent document");

define("_DOCS_INSERTFIRST", "beginning");
define("_DOCS_INSERTLAST", "end");

define("_DOCS_RATE_BAD", "bad");
define("_DOCS_RATE_POOR", "poor");
define("_DOCS_RATE_REGULAR", "regular");
define("_DOCS_RATE_GOOD", "good");
define("_DOCS_RATE_GORGEOUS", "Very Good");
define("_DOCS_RATE_CANCEL", "delete my input");
define("_DOCS_SELECT_ICON", "Here you can select an icon that appears in the title on the module homepage.");

define("_DOCS_ERR_FILESIZE", "File too big");
define("_DOCS_INFO_FILESIZE", "maximum file size [kByte] :");

/* sendfriend */

define("_DOCS_RECYOURNAME", "Your Name:");
define("_DOCS_RECYOUREMAIL", "Your E-mail:");
define("_DOCS_RECFRIENDNAME", "Your Friend's Name:");
define("_DOCS_RECFRIENDEMAIL", "Your Friend's E-mail:");
define("_DOCS_RECREMARKS", "Your personaly Remarks:");
define("_DOCS_RECYOURFRIEND", "Your Friend");
define("_DOCS_RECINTSITE", "Interesting Article:");
define("_DOCS_RECOURSITE", "considered our site");
define("_DOCS_RECINTSENT", "interesting and wanted to send it to you.");
define("_DOCS_RECSITENAME", "Article:");
define("_DOCS_RECSITEURL", "Site URL:");
// define("_DOCS_RECREFERENCE", "The reference to our site has been sent to");
define("_DOCS_RECTHANKS", "Thanks for recommend us!");
define("_DOCS_RECERRORTITLE", "Email sent away, the following error did not arise:");
define("_DOCS_RECERRORNAME", "Please enter your name.");
define("_DOCS_RECERRORRECEIVER", "The receivers email address is invalid.");
define("_DOCS_RECERRORSENDER", "Its senders email address is invalid.");

define("_DOCS_PAGE_SENDFRIEND", "Show send button");
define("_DOCS_PAGE_SENDFRIEND_TEXT", "Show Button 'Send Article to a Friend' ");

define("_DOCS_STARTPAGE", "Startpage");
define("_DOCS_STARTPAGE_TEXT", "When enabling this article appears on the block 'Startpage'");
define("_DOCS_STARTPAGE_OFF", "remove from Startpage");
define("_DOCS_STARTPAGE_ON", "show on Startpage");

define("_DOCS_NONE", "none");
define("_DOCS_PAGE_ALPHA","Show alphabetical index");
define("_DOCS_PAGE_ALPHA_TEXT","when activated, an alphabetical index appears on the article");
define("_DOCS_ALPHA_INDEX","alphabetical Index");

define("_DOCS_FILTER","Filter");
define("_DOCS_CONF_BLOCKS","Block Menu");
define("_DOCS_CONF_BLOCKS_TEXT","Here you can set the module's own Menublock.");
define("_DOCS_CONF_MENUWIDTH","Depth of the menu");
define("_DOCS_CONF_MENUWIDTH_TEXT","select which depth the Blockmenu to be generated.");
define("_DOCS_CONF_MENUCONTENT","Select content");
define("_DOCS_CONF_MENUCONTENT_TEXT","Select which documents to display in Menublock.");

define("_DOCS_PAGE_TITLE","page title");
define("_DOCS_PAGE_TITLE_TEXT","input other page title");
define("_DOCS_UPDATE_DB","Update database");
define("_DOCS_UPDATE_DB_TXT","data set have been tested and adapted");

?>