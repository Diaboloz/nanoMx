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
// define("_DOCS_CREATED","créé");
define("_DOCS_CHANGED", "modifié");
define("_DOCS_PAGECOUNT", "Nombre par page");
define("_DOCS_VIEWCONTENT", "afficher contenu");
define("_DOCS_MOVEUP", "déplacer en haut");
define("_DOCS_MOVEDN", "déplacer en bas");
define("_DOCS_POSITION", "Position");
define("_DOCS_PUBLISH", "Publier");
define("_DOCS_ACCESS", "Accès");
define("_DOCS_OWNER", "Propriétaire");
define("_DOCS_PUBLISHED", "publié");
define("_DOCS_UNPUBLISHED", "non publié");
define("_DOCS_EDIT", "Editer document");
define("_DOCS_NEW", "Nouveau Document");

define("_DOCS_PAGE_NEW", "Nouveau");
define("_DOCS_PAGE_EDIT", "Modifier");

define("_DOCS_SECTION", "Section");
define("_DOCS_EDIT_TEXT", "Ici vous pouvez éditer les informations du document");
define("_DOCS_INFO", "Specifier un information à propos du document. Cette information n'est pas affichée.");
define("_DOCS_ALIAS", "allias");
define("_DOCS_ALIAS_TEXT", "Veuillez sépcifier un nom 'alias' (option)");
define("_DOCS_KEYWORDS", "Mots clefs");
define("_DOCS_KEYWORDS_TEXT", "Veuillez séparer les mots clefs avec une virgule dans le texte. Ceci rend la recherche plus facile.");
define("_DOCS_NEW_BOOK", "Nouveau Document");
define("_DOCS_NEW_CONTENT", "Nouveau Contenu");
define("_DOCS_MOVECONTENT", "Les objets sélectionnez vont être déplacé vers les objets sélectionnés. Vous pouvez également désélectionner individuellment les objets. <br /> ATTENTION: Possibilitées de déplacer également les objets mineurs avec!");

define("_DOCS_CHILDS", "objets inclus");
define("_DOCS_CONTENTDELETEINFO", "Selected items are deleted along with their attachments! Under any item will be moved to the parent article.");
define("_DOCS_DELETEINFO", "Selected documents are deleted, as are all the items in them with your attachments! You can also deselect individual documents.");
define("_DOCS_INDEX", "Index");
define("_DOCS_TITLE", "Titre");
define("_DOCS_TITLE_TEXT", "Indiquer le titre du document");
define("_DOCS_LANGUAGE", "sélection de la langue");
define("_DOCS_LANGUAGE_TEXT", "Veuillez sélectionner le language pour le document affiché. Si vous indiquez TOUS, il seront affiché pour tous les documents.");
define("_DOCS_PREAMBLE", "Introduction du document");
define("_DOCS_PREAMBLE_TEXT", "L'introduction du document apparaitra sur la page d'accueil. Si vide, une courte description du document est utilisée.");

define("_DOCS_COPYRIGHT", "Copyright");
define("_DOCS_COPYRIGHT_TEXT", "Ici, veuillez entrer les copyrights possibles");
define("_DOCS_SHORTDESC", "Description courte");
define("_DOCS_SHORTDESC_TEXT", "La description courte du document apparait sur la page principale du document, pas sur la page d'accueil du module.");

define("_DOCS_USERGROUP", "Accès");
define("_DOCS_USERGROUP_TEXT", "Selectionner le groupe qui aura accès au document");

/* content*/
define("_DOCS_CONTENT_EDIT", "Modifier le Contenu");
define("_DOCS_CONTENT_TITLE", "Titre du Contenu");
define("_DOCS_CONTENT_TITLE_TEXT", "Veuillez spécifier un titre pour le Contenu");

/* Search */
define("_DOCS_SEARCH", "Rechercher");
define("_DOCS_SEARCH_RESULTS_TEXT", "Les pages suivantes ont été trouvées avec le modèles de recherche");
define("_DOCS_SEARCH_NORESULTS_TEXT", "Il n'y a aucun résultat avec les paramêtres de recherche.");
define("_DOCS_SEARCHMASK", "modèles de recherche");
define("_DOCS_SEARCHINFO", "Sélectionner les mots clés séparés avec une virgule");

/*config*/
define("_DOCS_CONFIG", "Configuration");
define("_DOCS_CONF_RIGHTBLOCKS", "afficher les blocs de droite");

define("_DOCS_CONF_TITLE", "Titre du module");
define("_DOCS_CONF_TITLE_TEXT", "Spécifiez ici le titre du module. ");

define("_DOCS_CONF_STARTPAGE", "Configuration du module");
define("_DOCS_CONF_STARTPAGE_TEXT", "");

define("_DOCS_CONF_LOGGING", "record changes");
define("_DOCS_CONF_LOGGING_TEXT", "When enabled, the date and users are written changes to the documents in a log table.");

define("_DOCS_CONF_BLOGPAGE", "Configuration du blog");
define("_DOCS_CONF_BLOGPAGE_TEXT", "");
define("_DOCS_CONF_INDEXPAGE", "Configuration de l'affichage des Catégories");
define("_DOCS_CONF_INDEXPAGE_TEXT", "");

define("_DOCS_CONF_RIGHTS", "Configuration des droits");
define("_DOCS_CONF_RIGHTS_TEXT", "");
// define("_DOCS_CONF_INDEXVIEW","Inhaltsübersicht anzeigen");
// define("_DOCS_CONF_INDEXVIEW_TEXT","Wenn aktiviert wird eine Übersicht aller Dokumente angezeigt.");
define("_DOCS_CONF_BLOGVIEW", "Vue");
define("_DOCS_CONF_BLOGVIEW_TEXT", "'Vue catégorie' - affiche un sommaire des documents. 'Vue Blog' - affiche les derniers objets de la vue blog.");

define("_DOCS_CONF_BREADCRUMP", "afficher le fil d'ariane ");
define("_DOCS_CONF_BREADCRUMP_TEXT", "");
define("_DOCS_CONF_PREAMBLE", "Voir l'introduction complête");
define("_DOCS_CONF_PREAMBLE_TEXT", "When activated, is displayed on the home page of the module, the full introduction of the documents. If disabled, only the number are displayed character, as indicated in the following input.");
define("_DOCS_CONF_CHARCOUNT", "length Introduction");
define("_DOCS_CONF_CHARCOUNT_TEXT", "Number of characters for the length of the introduction specify. Active only if above setting is disabled.");
define("_DOCS_CONF_INDEXCOUNT", "Index");
define("_DOCS_CONF_INDEXCOUNT_TEXT", "0 = no table of contents for each document on the home page, value> 0 indicates the depth of the table of contents for all documents in the index, which is shown.");
define("_DOCS_CONF_SEARCHCOUNT", "Nombre de résultats de la recherche");
define("_DOCS_CONF_SEARCHCOUNT_TEXT", "Specify how many search results to be shown up.");
define("_DOCS_CONF_LANGUAGE", "Langue");
define("_DOCS_CONF_LANGUAGE_TEXT", "new items to be created in the chosen language");
define("_DOCS_CONF_TABCOUNT", "compteur de colonnes");
define("_DOCS_CONF_TABCOUNT_TEXT", "Number of columns in which the documents / articles on the home page will be displayed.");

define("_DOCS_PAGE_NEWS", "Afficher un nouvel article");
define("_DOCS_PAGE_NEWS_TEXT", "When enabled new content appears on the home page of the module");

define("_DOCS_PAGE_NEWSCOUNT", "Period for new/modified content");
define("_DOCS_PAGE_NEWSCOUNT_TEXT", "(in days). How long items are marked as New or Modified");

define("_DOCS_PAGE_CHANGES", "Contenus modifiés");
define("_DOCS_PAGE_CHANGES_TEXT", "When enabled, displays modified article on the home page of the module.");
define("_DOCS_PAGE_CHANGESCOUNT", "Compteur de Contenu");
define("_DOCS_PAGE_CHANGESCOUNT_TEXT", "maximum number, how many items are displayed as 'NEW' or 'Modified' or as a blog on the home page.");

/* attachments */
define("_DOCS_ATTACHMENTS", "Pièces-jointes");
define("_DOCS_ATTACH_DELETE", "Selectionner pour supprimer la pièces-jointe.");
define("_DOCS_ATTACH_MAX", "max. Pièces-jointes");
define("_DOCS_ATTACH_MAX_TEXT", "max. nombre de pièces-jointes ");

define("_DOCS_CONF_ATTACH", "Configuration des Pièces-Jointes");
define("_DOCS_CONF_ATTACH_TEXT", "This determines the behavior of file attachments for documents. Attachments are displayed below the document in a download list and can be downloaded at the User.");
define("_DOCS_CONF_ATTACH_ON", "autoriser les pièces jointes");
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





// TODO: !!!!!!!!!!!!!!! TRANSLATE !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
define("_DOCS_CONF_PAGE", "Konfiguration Dokumentenseite");

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
define("_DOCS_START", "Démarrer");
define("_DOCS_ADMIN_PANEEL", "Administration");
define("_DOCS_PREVIOUS", "précédent");
define("_DOCS_NEXT", "suivant");
define("_DOCS_MOVE", "déplacer");
define("_DOCS_FROM", "de");
define("_DOCS_ACTION", "Action");
define("_DOCS_HISTORY", "Historique");
define("_DOCS_LASTCHANGE", "dernier changement");
define("_DOCS_NEWCONTENT", "new items");
define("_DOCS_LASTCHANGES", "derniers changements");
define("_DOCS_LINKS", "Liens");
// define("_ATTACHMENTS","Attachments");
// define("_READMORE","lire la suite");
define("_DOCS_VIEW_INDEX", "voir l'index");
define("_DOCS_VIEW_BLOG", "voir le blog");
define("_DOCS_VIEW_LOG", "voir le journal");

define("_DOCS_DOWNLOAD", "Téléchargement");
define("_DOCS_ATTACHMENT", "Pièces-jointes");
define("_DOCS_PAGE_SIMILAR", "These articles could be interesting for you:");

define("_DOCS_FILENAME", "Nom du fichier");
define("_DOCS_FILESIZE", "Taille du fichier");
define("_DOCS_FILETYPE", "MIME-Type");
define("_DOCS_FILETITLE", "Titre du fichier");

define("_DOCS_URL", "Lien vers l'article original");
define("_DOCS_URL_TEXT", "Cet article provient de:");
/* error */

define("_DOCS_NESTEDSET_ERROR", "Errors were found in the database structure of the module. Click on 'Options' to database tools.");
define("_DOCS_NESTEDSET_IO", "Database structure correct.");
define("_DOCS_REPAIR", "Reparer");
define("_DOCS_DB_REPAIR", "Réparer la structure de la base de données");
define("_DOCS_DB_REPAIR_TEXT", "It examines the data structure for the module and is created with errors, an error document.");
define("_DOCS_DB_DELLOG", "delete log table");
define("_DOCS_DB_DELLOG_TEXT", "It will be deleted ALL entries for this module from the log table!");
define("_DOCS_DB_DELLOG_ACTION", "La table des journeaux a été effacée! ");
define("_DOCS_IMPORT_ACTION", " Enregistrements importés.");

/* --- */
define("_DOCS_CONF_LINKOTHER", "link in other modules");
define("_DOCS_CONF_LINKOTHER_TEXT", "If the items are linked in other modules?");
define("_DOCS_LINK_ALL", "each occurrence");
define("_DOCS_LINK_FIRST", "only the first occurrence");
define("_DOCS_CONF_LINKCOUNT", "Nombre de liens");
define("_DOCS_CONF_LINKCOUNT_TEXT", "How often in the content of the items should be linked?");
define("_DOCS_PAGE_VIEWMODULELINK", "May link other modules?");
define("_DOCS_PAGE_VIEWMODULELINK_TEXT", "Can other modules in this module link terms?");

define("_DOCS_NEW2", "Nouveautés");
define("_DOCS_DEFAULT", "Standard");
define("_DOCS_UPDATE", "Mise à jour");
define("_DOCS_VIEW_LIST", "Liste");
define("_DOCS_CONF_TABCOUNT_LIST_TEXT", "Number of Columns - applies only to list view");
// define("_DOCS_NEW_DOCUMENTS","New document(s)");

define("_DOCS_CONF_INSERTFIRST", "la séquence d'insertion");
define("_DOCS_CONF_INSERTFIRST_TEXT", "Sélectionner à quel point les nouveaux documents sont insérés dans le document parent");

define("_DOCS_INSERTFIRST", "début");
define("_DOCS_INSERTLAST", "fin");

define("_DOCS_RATE_BAD", "mauvais");
define("_DOCS_RATE_POOR", "bof");
define("_DOCS_RATE_REGULAR", "moyen");
define("_DOCS_RATE_GOOD", "bien");
define("_DOCS_RATE_GORGEOUS", "Très bien");
define("_DOCS_RATE_CANCEL", "delete my input");
define("_DOCS_SELECT_ICON", "Here you can select an icon that appears in the title on the module homepage.");

define("_DOCS_ERR_FILESIZE", "Données trop grosses");
define("_DOCS_INFO_FILESIZE", "Poids maximum des données [kByte] :");

/* sendfriend */
define("_DOCS_RECYOURNAME", "Votre Nom:");
define("_DOCS_RECYOUREMAIL", "Votre E-mail:");
define("_DOCS_RECFRIENDNAME", "Nom de vos amis:");
define("_DOCS_RECFRIENDEMAIL", "Adresse E-mail de vos amis:");


define("_DOCS_RECREMARKS", "Remarques personnelles:");
define("_DOCS_RECYOURFRIEND", "Votre ami");
define("_DOCS_RECINTSITE", "Intéressant article:");
define("_DOCS_RECOURSITE", "trouvé l'article");
define("_DOCS_RECINTSENT", "intéressant et je le recommande à vous.");
define("_DOCS_RECSITENAME", "Article précédent:");
define("_DOCS_RECSITEURL", "URL du site:");
define("_DOCS_RECTHANKS", "Merci pour votre recommandation!");
define("_DOCS_RECERRORTITLE", "Adresse e-mail n'a pas été envoyé, l'erreur suivante s'est produite:");
define("_DOCS_RECERRORNAME", "S'il vous plaît entrer votre nom.");
define("_DOCS_RECERRORRECEIVER", "L'adresse e-mail du destinataire n'est pas valide.");
define("_DOCS_RECERRORSENDER", "Votre adresse e-mail de l'expéditeur n'est pas valide.");

define("_DOCS_PAGE_SENDFRIEND", "Voir \"Envoyer\"");
define("_DOCS_PAGE_SENDFRIEND_TEXT", "Bouton 'Envoyer l'article à un ami' Show");

define("_DOCS_STARTPAGE", "Accueil");
define("_DOCS_STARTPAGE_TEXT", "Lorsque cette option est activée apparaît sur le bloc 'Accueil'");
define("_DOCS_STARTPAGE_OFF", "Retirer de la Accueil");
define("_DOCS_STARTPAGE_ON", "Afficher sur la Accueil");

define("_DOCS_NONE", "aucun");
define("_DOCS_PAGE_ALPHA","Afficher l'index alphabétique");
define("_DOCS_PAGE_ALPHA_TEXT","lorsqu'il est activé, un indice alphabétique apparaît sur l'article");
define("_DOCS_ALPHA_INDEX","index alphabétique");

define("_DOCS_FILTER","filtre");
define("_DOCS_CONF_BLOCKS","Bloc Menu");
define("_DOCS_CONF_BLOCKS_TEXT","ci vous pouvez régler propre Menublock du module.");
define("_DOCS_CONF_MENUWIDTH","Profondeur du menu");
define("_DOCS_CONF_MENUWIDTH_TEXT","sélectionner la profondeur de la Bloc menu à générer.");
define("_DOCS_CONF_MENUCONTENT","Sélectionner le contenu");
define("_DOCS_CONF_MENUCONTENT_TEXT","Sélectionnez les documents à afficher dans bloc menu.");

define("_DOCS_PAGE_TITLE","titre de la page");
define("_DOCS_PAGE_TITLE_TEXT","l'autre entrée titre de la page");
define("_DOCS_UPDATE_DB","base de données de Update");
define("_DOCS_UPDATE_DB_TXT","ensemble de données ont été testés et adaptés");

?>