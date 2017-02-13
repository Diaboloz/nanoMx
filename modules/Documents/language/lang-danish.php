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

define("_DOCUMENTS_TITLE", "Dokumenter");

/* books */

define("_DOCU", "Dokument");
// define("_DOCS_CREATED","oprettet");
define("_DOCS_CHANGED", "ændret");
define("_DOCS_PAGECOUNT", "mængde/side");
define("_DOCS_VIEWCONTENT", "vis indhold");
define("_DOCS_MOVEUP", "næste");
define("_DOCS_MOVEDN", "tilbage");
define("_DOCS_POSITION", "position");
define("_DOCS_PUBLISH", "frigivelse ");
define("_DOCS_ACCESS", "adgang");
define("_DOCS_OWNER", "ejer");
define("_DOCS_PUBLISHED", "udgive");
define("_DOCS_UNPUBLISHED", "lås");
define("_DOCS_EDIT", "Rediger dokument");
define("_DOCS_NEW", "Nyt dokument");

define("_DOCS_PAGE_NEW", "Ny");
define("_DOCS_PAGE_EDIT", "Rediger");

define("_DOCS_SECTION", "Område");
define("_DOCS_EDIT_TEXT", "Her kan du redigere oplysninger af dokumentet");
define("_DOCS_INFO", "Angiv oplysninger om dokumentet. Disse oplysninger vises ikke.");
define("_DOCS_ALIAS", "alias");
define("_DOCS_ALIAS_TEXT", "Indtast et alias-navn (valgfrit)");
define("_DOCS_KEYWORDS", "Nøgleord");
define("_DOCS_KEYWORDS_TEXT", "Indtast her kommaseparerede nøgleord som tekst. Dette forenkler søgningen.");
define("_DOCS_NEW_BOOK", "Nyt dokument");
define("_DOCS_NEW_CONTENT", "Ny artikel");
define("_DOCS_MOVECONTENT", "Udvalgte artikler tilføjes den valgte artikel. Du kan skifte igen til enkelte artikler. <br /> OBS: eventuelle underordnede elementer flyttes med!");

define("_DOCS_CHILDS", "inkluderede artikler");
define("_DOCS_CONTENTDELETEINFO", "Udvalgte artikler vil blive slettet, incl. dens tillæg! Nogen sub artikler er flyttet til den overordnede artikel.");
define("_DOCS_DELETEINFO", "Markerede dokumenter slettes, incl. alle artikler og tillæg! Du kan vælge dokumenter fra igen. ");
define("_DOCS_INDEX", "Indhold");
define("_DOCS_TITLE", "Titel");
define("_DOCS_TITLE_TEXT", "Angiv titlen på dokumentet.");
define("_DOCS_LANGUAGE", "Vælg et sprog");
define("_DOCS_LANGUAGE_TEXT", "Vælg venligst sproget, som dokumentet skal vises i. Bliver altid vist, når du vælger alle.");
define("_DOCS_PREAMBLE", "Introduktion til dokumentet");
define("_DOCS_PREAMBLE_TEXT", "Introduktion til dokumentet vil blive vist på startsiden. Er dette felt tomt, bruges den korte beskrivelse af dokumentet.");

define("_DOCS_COPYRIGHT", "Copyright");
define("_DOCS_COPYRIGHT_TEXT", "Angiv eventuelle ophavsrettigheder");
define("_DOCS_SHORTDESC", "Kort beskrivelse");
define("_DOCS_SHORTDESC_TEXT", "Kort beskrivelse af dokumentet vises på startsiden af dokumentet, ikke på Modul startsiden.");

define("_DOCS_USERGROUP", "Adgang");
define("_DOCS_USERGROUP_TEXT", "Vælg, hvilken brugergruppe har adgang til dokumentet");

/* content*/
define("_DOCS_CONTENT_EDIT", "Redigere indholdet");
define("_DOCS_CONTENT_TITLE", "Indhold titel");
define("_DOCS_CONTENT_TITLE_TEXT", "Indtast her titlen på indholdet");

/* Search */
define("_DOCS_SEARCH", "Indhold søgning");
define("_DOCS_SEARCH_RESULTS_TEXT", "De følgende sider fandtes.");
define("_DOCS_SEARCH_NORESULTS_TEXT", "Der er ingen søgeresultater, som svarer til din søgning");
define("_DOCS_SEARCHMASK", "Søgeord");
define("_DOCS_SEARCHINFO", "Separer søgeordene med kommaer ");

/*config*/
define("_DOCS_CONFIG", "Konfiguration");
define("_DOCS_CONF_RIGHTBLOCKS", "Vis højre blokke");

define("_DOCS_CONF_TITLE", "Modultitel");
define("_DOCS_CONF_TITLE_TEXT", "Angive her en modultitel. ");

define("_DOCS_CONF_STARTPAGE", "Konfiguration Modul");
define("_DOCS_CONF_STARTPAGE_TEXT", "");

define("_DOCS_CONF_LOGGING", " Log ændringer");
define("_DOCS_CONF_LOGGING_TEXT", "Hvis aktiveret, skrives dato og bruger af ændringer i dokumenter i en log tabel.");

define("_DOCS_CONF_BLOGPAGE", "Konfiguration Blog");
define("_DOCS_CONF_BLOGPAGE_TEXT", "");
define("_DOCS_CONF_INDEXPAGE", "Konfiguration Kategorivisning");
define("_DOCS_CONF_INDEXPAGE_TEXT", "");

define("_DOCS_CONF_RIGHTS", "Konfiguration Rettigheder");
define("_DOCS_CONF_RIGHTS_TEXT", "");
// define("_DOCS_CONF_INDEXVIEW","Vis indholdsoversigt");
// define("_DOCS_CONF_INDEXVIEW_TEXT","Hvis aktiveret vil blive vist en oversigt over alle dokumenter.");
define("_DOCS_CONF_BLOGVIEW", "Visning");
define("_DOCS_CONF_BLOGVIEW_TEXT", "'Kategorivisning' - Viser en oversigt over dokumenter. 'Blogansicht' - viser de nyeste artikler i en Blogansicht.");

define("_DOCS_CONF_BREADCRUMP", "Breadcrump visning");
define("_DOCS_CONF_BREADCRUMP_TEXT", "");
define("_DOCS_CONF_PREAMBLE", "Vis hele introduktion");
define("_DOCS_CONF_PREAMBLE_TEXT", "Når aktiveret, vises på startsiden af modulet, den fuldstændige introduktion af dokumenterne. Hvis deaktiveret, bliver kun det antal af tegn vist, som er angivet i følgende fjelt.");
define("_DOCS_CONF_CHARCOUNT", "Længde af indledningen");
define("_DOCS_CONF_CHARCOUNT_TEXT", "Antal tegn for længden af ??indledningen. Kun aktiv, hvis oven stående indstilling er deaktiveret.");
define("_DOCS_CONF_INDEXCOUNT", "Indholdsfortegnelse");
define("_DOCS_CONF_INDEXCOUNT_TEXT", "0 = ingen indholdsfortegnelse i de enkelte dokumenter på startsiden, en værdi mere end 0 angiver dybden af indholdsfortegnelse for alle dokumenter i oversigten.");
define("_DOCS_CONF_SEARCHCOUNT", "Antal søgeresultater");
define("_DOCS_CONF_SEARCHCOUNT_TEXT", "Angiv, hvor mange søge resultater vises maksimalt.");
define("_DOCS_CONF_LANGUAGE", "Sprogvalg");
define("_DOCS_CONF_LANGUAGE_TEXT", "Vælg standard sproget for nye artikler.");
define("_DOCS_CONF_TABCOUNT", "Antallet af kolonner");
define("_DOCS_CONF_TABCOUNT_TEXT", "Antallet af kolonner, som vises i dokumenter/artikler på startsiden.");

define("_DOCS_PAGE_NEWS", "Vis nye artikler");
define("_DOCS_PAGE_NEWS_TEXT", "Hvis aktiveret, bliver nye artikler vist på startsiden af modulet.");

define("_DOCS_PAGE_NEWSCOUNT", "Tidsrum for nye artikler");
define("_DOCS_PAGE_NEWSCOUNT_TEXT", "(i dage) Hvor længe skal en artikel markeres som ny?");

define("_DOCS_PAGE_CHANGES", "Vis ændrede artikler");
define("_DOCS_PAGE_CHANGES_TEXT", "Når aktiveret, bliver ændrede artikler vist på startsiden af modulet. Tidsrum som til 'Nye' artikler.");
define("_DOCS_PAGE_CHANGESCOUNT", "Antal artikler");
define("_DOCS_PAGE_CHANGESCOUNT_TEXT", "Maksimal antal af, hvor mange artikler som 'Ny' eller 'Ændret' eller som en Blog vises på startsiden.");

/* attachments */
define("_DOCS_ATTACHMENTS", "Vedhæftede filer");
define("_DOCS_ATTACH_DELETE", "marker denne vedhæftede fil til sletning.");
define("_DOCS_ATTACH_MAX", "maks. vedhæftede filer");
define("_DOCS_ATTACH_MAX_TEXT", "maks. antal vedhæftede filer på hvert dokument");

define("_DOCS_CONF_ATTACH", "Konfiguration vedhæftede filer");
define("_DOCS_CONF_ATTACH_TEXT", "Her angives funktionsmåden for vedhæftede filer til dokumenter. Vedhæftede filer vises nederst i dokumentet som en download-liste og kan downloades af brugeren.");
define("_DOCS_CONF_ATTACH_ON", "Tillad vedhæftede filer");
define("_DOCS_CONF_ATTACH_ON_TEXT", "Hvis aktiveret, bliver vedhæftede filer vedlagt dokumentet. ");
define("_DOCS_CONF_ATTACH_MAXSIZE", "maks. filstørrelse");
define("_DOCS_CONF_ATTACH_MAXSIZE_TEXT", "i kByte pr fil");
define("_DOCS_CONF_ATTACH_PATH", "Fortegnelse");
define("_DOCS_CONF_ATTACH_PATH_TEXT", "Angiv mappen, hvor de vedhæftede filer skal gemmes.");
define("_DOCS_CONF_ATTACH_MEDIA", "Vis Medie filer");
define("_DOCS_CONF_ATTACH_MEDIA_TEXT", "Når aktiveret, vises Medie (mp3, mp4, billeder) under indholdet og ikke på download listen.");
define("_DOCS_CONF_ATTACH_MAXWIDTH", "maks. bredde af Medie");
define("_DOCS_CONF_ATTACH_MAXWIDTH_TEXT", "Angiv mediefilernes bredde i pixel. Specifikationen skal passe til det anvendte tema. ");
define("_DOCS_CONF_ATTACH_MAXWIDTHTHUMB", "maks. størrelse af thumbnails");
define("_DOCS_CONF_ATTACH_MAXWIDTHTHUMB_TEXT", "Angiv den maks. størrelse af thumbnails for billeder. Billederne bliver vist i en Lightbox.");
define("_DOCS_CONF_ATTACH_MAXHEIGHT", "maks. højde af Medie");
define("_DOCS_CONF_ATTACH_MAXHEIGHT_TEXT", "Angiv mediefilernes højde i pixel. Specifikationen skal passe til det anvendte tema. ");

define("_DOCS_CONF_PAGE", "Konfiguration dokumentside");
define("_DOCS_CONF_PAGE_TEXT", "");
define("_DOCS_PAGE_INDEX", "Vis indeks");
define("_DOCS_PAGE_INDEX_TEXT", "Når aktiveret, vises en indholdsfortegnelse på hver side.");
define("_DOCS_PAGE_INDEXFULL", "Vis en komplet indeks");
define("_DOCS_PAGE_INDEXFULL_TEXT", "Når aktiveret, vises en komplet indholdsfortegnelse på hver side.");
define("_DOCS_PAGE_LASTEDITOR", "Vis den sidste forfatter ");
define("_DOCS_PAGE_LASTEDITOR_TEXT", "Når aktiveret, vises sidste ændringsdato og forfatteren");
define("_DOCS_PAGE_VIEWKEYWORDS", "Vis nøgleord ");
define("_DOCS_PAGE_VIEWKEYWORDS_TEXT", "Når aktiveret viser nøgleordene i artiklen.");
define("_DOCS_PAGE_VIEWNAVIGATION", "Vis Navigation");
define("_DOCS_PAGE_VIEWNAVIGATION_TEXT", "Når aktiveret vises knapper under artiklen, til at rulle gennem artiklen.");
define("_DOCS_PAGE_CREATOR", "Vis forfatteren");
define("_DOCS_PAGE_CREATOR_TEXT", "Når aktiveret, vises under artiklen forfatteren.");
define("_DOCS_PAGE_VIEWRATING", "Vis bedømmelser");
define("_DOCS_PAGE_VIEWRATING_TEXT", "Når aktiveret, vises over artiklen bedømmelser af artiklen.");

define("_DOCS_PAGE_VIEWSOCIAL", "Vis social links ");
define("_DOCS_PAGE_VIEWSOCIAL_TEXT", "Når aktiveret, bliver under artiklen vist Links til forskellige social-netværke.");

define("_DOCS_PAGE_EDITORS", "Hvem må ændre artikler ");
define("_DOCS_PAGE_EDITORS_TEXT", "Angiv, hvilken bruger gruppe kan oprette og ændre artikler. Bliver valgt 'Bruger', kan alle brugergrupper redigere. Administratorer kan altid redigere. Kun en administrator kan slette artikler.");
define("_DOCS_PAGE_EDITOR_RIGHTS", "Må en forfatter publicere artikler");
define("_DOCS_PAGE_EDITOR_RIGHTS_TEXT", "Angiv, om forfatteren må publicere nye artikler.");

define("_DOCS_PAGE_VIEWSIMILAR", "Vis lignende artikler");
define("_DOCS_PAGE_VIEWSIMILAR_TEXT", "Når aktiveret, vises en liste over lignende artikler nedenfor.");
define("_DOCS_PAGE_SIMILARCOUNT", "Antal lignender artikler");
define("_DOCS_PAGE_SIMILARCOUNT_TEXT", "Angiv, hvor mange lignende artikler skal vises under teksten. Lignende artikler vises kun, når et søgeord er angivet.");

define("_DOCS_PAGE_PRINT", "Udskrivning");
define("_DOCS_PAGE_PRINT_TEXT", "Når aktiveret, vises et print ikon, med hvilket brugeren kan se en printer venlig version af dokumentet.");

define("_DOCS_CONF_INTRO", "Introduktion til modulet");
define("_DOCS_CONF_INTRO_TEXT", "Her kan skrives en introduktion til modulet, hvilken vises på startsiden af modulet.");

define("_DOCS_CONF_LINK", "Konfiguration af links");
define("_DOCS_CONF_LINK_TEXT", "");
define("_DOCS_PAGE_VIEWBOOKLINK", "Artikel link");
define("_DOCS_PAGE_VIEWBOOKLINK_TEXT", "Hvis aktiveret, bliver alle ord de ligner andre artikler, lænket til denne artikel.");
define("_DOCS_PAGE_VIEWBOOKBASE", "Links fra alle bøger");
define("_DOCS_PAGE_VIEWBOOKBASE_TEXT", "Når aktiveret bliver alle dokumenter anvendt til de ovennævnte links. Hvis deaktiveret, kun dokumentet i den nuværende artikel.");
define("_DOCS_PAGE_VIEWENCYLINKS", "Opret links til encyklopædien");
define("_DOCS_PAGE_VIEWENCYLINKS_TEXT", "Hvis aktiveret, sammenkædes elementer fra encyclopedia med artiklen.");
define("_DOCS_PAGE_INDEX_NEW", "Marker ændringer ");
define("_DOCS_PAGE_INDEX_NEW_TEXT", "Når aktiveret, bliver i indholdsfortegnelsen nye og ændrede artikler markeret.");
// define("_DOCS_STATUS","Brug tidsangivelse");
// define("_DOCS_STATUS_TEXT","Når aktiveret, bliver efterfølgende tidsangivelser tilføjet artiklen.");
// define("_DOCS_STARTTIME","Starttidspunkt");
// define("_DOCS_STARTTIME_TEXT","Her kan du angive en starttidspunkt for artiklen. Fra dette tidspunkt bliver artiklen vist på hjemmesiden");
// define("_DOCS_ENDTIME","Tidsangivelse for afslutning");
// define("_DOCS_ENDTIME_TEXT","Her kan du angive et sluttidspunkt for artiklen. Fra dette tidspunkt bliver artiklen ikke mere vist.");
define("_DOCS_VIEWTITLE", "Vis titlen");
define("_DOCS_VIEWTITLE_TEXT", "Vis modultitlen på artikelsiden");
define("_DOCS_VIEWVIEWS", "Vis antal visninger");
define("_DOCS_VIEWVIEWS_TEXT", "");
define("_DOCS_LINKTITLE", "Link til titlen");
define("_DOCS_LINKTITLE_TEXT", "Skal linkes til modultitlen?");
define("_DOCS_VIEWSEARCH", "Vis søgefelt");
define("_DOCS_VIEWSEARCH_TEXT", "Vise søgeformularen på artikelsiden?");

define("_DOCS_META_PAGE", "Indstillinger for metadata");
define("_DOCS_META_PAGE_TEXT", "Her kan du justere metadata. Ellers bruges standard instillingerne");
define("_DOCS_META_CANONICAL", "Oprindelig URL");
define("_DOCS_META_CANONICAL_TEXT", "Angiv her den oprindelige url til artiklen, for at undgå dobbelte indlæg. Skriv adressen med 'http://'");
define("_DOCS_META_ROBOTS", "Robotter");
define("_DOCS_META_ROBOTS_TEXT", "Robottersætning");
define("_DOCS_META_ALTERNATE", "yderligere Meta-Tags");
define("_DOCS_META_ALTERNATE_TEXT", "Her kan du angive yderligere metakoder. Udfyld oplysningerne komplet og kommasepareret, angive uden første og afsluttende parentes (f.eks. link rel=\"robots\" content=\"all\", meta name=\"author\".... )  begge eksempler er allerede integreret.");
define("_DOCS_META_REVISIT", "Genoptage");
define("_DOCS_META_REVISIT_TEXT", "antal dage.");
define("_DOCS_META_AUTHOR", "Forfatter");
define("_DOCS_META_AUTHOR_TEXT", "Angiv, eventuelt forskellige forfattere.");

/* tools */
define("_DOCS_TOOLS", "Avancerede funktioner");
define("_DOCS_TOOLS_TEXT", "");
define("_DOCS_TOOLS_IMPORT", "Import funktioner");
define("_DOCS_TOOLS_IMPORT_TEXT", "Her, kan indholdet af forskellige dokumenter importeres. Data vil blive kopieret, ikke slettet");
define("_DOCS_TOOLS_IMPORT_SELECT", "Vælg modul");
define("_DOCS_TOOLS_IMPORT_SELECT_TEXT", "Vælg fra hvilken modul skal data importeres. Intet indhold importeres to gange. Indholdet af modulet importeres til et dokument med følgende navn.");
define("_DOCS_TOOLS_DB", "Databasefunktioner");
define("_DOCS_TOOLS_DB_TEXT", "Værktøjer til at vedligeholde databasen af modulet");
define("_DOCS_TOOLS_IMPORT_DOC", "Importer i dokument");
define("_DOCS_TOOLS_IMPORT_DOC_TEXT", "Angiv et navn til base dokumentet hvor indholdet importeres til. Er feltet tomt, bruges modulnavnet.");

/* extendet settings */
define("_DOCS_EXTENDET_SETTINGS", "Avancerede indstillinger");
/* sociol */
define("_DOCS_SOCIAL_INFO_FACEBOOK", "2 klik for mere databeskyttelse: Først når du klikker her, vil knappen være aktiv, og du kan sende din anbefaling til Facebook. Allerede ved aktivering sendes data til tredjemand – se i.");
define("_DOCS_SOCIAL_INFO_TWITTER", "2 klik for mere databeskyttelse: Først når du klikker her, vil knappen være aktiv, og du kan sende din anbefaling til Twitter. Allerede ved aktivering sendes data til tredjemand – se i.");
define("_DOCS_SOCIAL_INFO_GPLUS", "2 klik for mere databeskyttelse: Først når du klikker her, vil knappen være aktiv, og du kan sende din anbefaling til Google+. Allerede ved aktivering sendes data til tredjemand – se i.");
define("_DOCS_SOCIAL_INFO_HELP", "Hvis du aktiverer disse felter ved at klikke, bliver oplysningerne overført til Facebook, Twitter eller Google i USA og kan også være lagret der.");
define("_DOCS_SOCIAL_INFO_TOOLS", "Jeg vil aktivere permanent og er enig med at data bliver overføret:");

/* diverse */
define("_DOCS_START", "Start");
define("_DOCS_ADMIN_PANEEL", "Administration");
define("_DOCS_PREVIOUS", "tilbage");
define("_DOCS_NEXT", "videre");
define("_DOCS_MOVE", "flyt");
define("_DOCS_FROM", "fra");
define("_DOCS_ACTION", "Aktion");
define("_DOCS_HISTORY", "historie");
define("_DOCS_LASTCHANGE", "seneste ændringer");
define("_DOCS_NEWCONTENT", "nye artikler");
define("_DOCS_LASTCHANGES", "seneste ændring");
define("_DOCS_LINKS", "Links");
// define("_ATTACHMENTS","Vedhæftede filer");
// define("_READMORE","Fortsæt læsning");
define("_DOCS_VIEW_INDEX", "Kategorioversigt");
define("_DOCS_VIEW_BLOG", "Blogoversigt");
define("_DOCS_VIEW_LOG", "Protokol over ændringer");

define("_DOCS_DOWNLOAD", "Download");
define("_DOCS_ATTACHMENT", "vedhæftede filer");
define("_DOCS_PAGE_SIMILAR", "Du kunne også være interesseret i disse artikler:");

define("_DOCS_FILENAME", "Filnavn");
define("_DOCS_FILESIZE", "Størrelse");
define("_DOCS_FILETYPE", "MIME-Type");
define("_DOCS_FILETITLE", "Beskrivelse");

define("_DOCS_URL", "Link til oprindelig artikel");
define("_DOCS_URL_TEXT", "Denne artikel stammer fra:");
/* error */

define("_DOCS_NESTEDSET_ERROR", "Fejl fundet i modulets databasestruktur. Klik på 'Indstillinger' til databasevedligeholdelse.");
define("_DOCS_NESTEDSET_IO", "Databasestruktur uden fejl.");
define("_DOCS_REPAIR", "Reparation");
define("_DOCS_DB_REPAIR", "Reparere datastruktur ");
define("_DOCS_DB_REPAIR_TEXT", "Datastruktur af modulet bliver kontrolleret og ved fejl bliver en logfil dannet.");
define("_DOCS_DB_DELLOG", "Slet log tabel ");
define("_DOCS_DB_DELLOG_TEXT", "Indholdet af logtabellen vil blive komplet slettet!");
define("_DOCS_DB_DELLOG_ACTION", "Log tabel er blevet slettet. ");
define("_DOCS_IMPORT_ACTION", "Datastruktur blev importeret. ");

/* --- */
define("_DOCS_CONF_LINKOTHER", "link til andre moduler");
define("_DOCS_CONF_LINKOTHER_TEXT", "Skal artiklen lænke til andre moduler?");
define("_DOCS_LINK_ALL", "hver forekomst");
define("_DOCS_LINK_FIRST", "kun den første forekomst");
define("_DOCS_CONF_LINKCOUNT", "Antallet af links");
define("_DOCS_CONF_LINKCOUNT_TEXT", "Hvor ofte skal artiklen linkes i teksten?");
define("_DOCS_PAGE_VIEWMODULELINK", "Har andre moduler tilladelse til at linke");
define("_DOCS_PAGE_VIEWMODULELINK_TEXT", "Har andre moduler tilladelse til at linke til dette modul ?");

define("_DOCS_NEW2", "Ny");
define("_DOCS_DEFAULT", "Standard");
define("_DOCS_UPDATE", "Update");
define("_DOCS_VIEW_LIST", "Liste");
define("_DOCS_CONF_TABCOUNT_LIST_TEXT", "Antallet af kolonner - gælder kun for listevisning");
// define("_DOCS_NEW_DOCUMENTS","Nyt dokument/nye dokumenter");

define("_DOCS_CONF_INSERTFIRST", "Indsættelse sekvens");
define("_DOCS_CONF_INSERTFIRST_TEXT", "Vælg på hvilket tidspunkt nye dokumenter indsættes i moderselskabets dokument");
define("_DOCS_INSERTFIRST", "i begyndelsen");
define("_DOCS_INSERTLAST", "ved udgangen");

define("_DOCS_RATE_BAD", "elendige");
define("_DOCS_RATE_POOR", "dårlig");
define("_DOCS_RATE_REGULAR", "ok");
define("_DOCS_RATE_GOOD", "god ");
define("_DOCS_RATE_GORGEOUS", "meget god");
define("_DOCS_RATE_CANCEL", "slette mit input");
define("_DOCS_SELECT_ICON", "Her får De vælge et ikon, der vises i titlen på modulet startside.");

define("_DOCS_ERR_FILESIZE", "Fil for stor");
define("_DOCS_INFO_FILESIZE", "maksimal filstørrelse [kByte] :");

/* sendfriend */
define("_DOCS_RECYOURNAME", "Dit navn:");
define("_DOCS_RECYOUREMAIL", "Din email:");
define("_DOCS_RECFRIENDNAME", "Navn på din ven:");
define("_DOCS_RECFRIENDEMAIL", "Din vens email:");
define("_DOCS_RECREMARKS", "personlige tilføjelser:");
define("_DOCS_RECYOURFRIEND", "din ven");
define("_DOCS_RECINTSITE", "interessant artikel:");
define("_DOCS_RECOURSITE", "fandt artikel");
define("_DOCS_RECINTSENT", "interessant og vil anbefale det til dig.");
define("_DOCS_RECSITENAME", "artikel navn:");
define("_DOCS_RECSITEURL", "websitets URL:");
// define("_DOCS_RECREFERENCE", "Anbefalingen af vores artikel blev sendt til");
define("_DOCS_RECTHANKS", "Hjertelig tak for din anbefaling!");
define("_DOCS_RECERRORTITLE", "Email blev IKKE sendt, der var følgende fejl:");
define("_DOCS_RECERRORNAME", "Du må skrive dit navn.");
define("_DOCS_RECERRORRECEIVER", "Modtagers email er ikke gyldig.");
define("_DOCS_RECERRORSENDER", "Din afsender email er ikke gyldig.");

define("_DOCS_PAGE_SENDFRIEND", "Send show");
define("_DOCS_PAGE_SENDFRIEND_TEXT", "Knappen 'til en ven Send' Vis");

define("_DOCS_STARTPAGE", "blokken 'Home'");
define("_DOCS_STARTPAGE_TEXT", "Når du aktiverer denne artikel vises på blokken 'Home'");
define("_DOCS_STARTPAGE_OFF", "Fjern fra hjemmet");
define("_DOCS_STARTPAGE_ON", "Vis på Home");

define("_DOCS_NONE", "ingen");
define("_DOCS_PAGE_ALPHA","Vis alfabetisk indeks");
define("_DOCS_PAGE_ALPHA_TEXT","når den aktiveres, et alfabetisk indeks vises på artiklen");
define("_DOCS_ALPHA_INDEX","alfabetisk indeks");

define("_DOCS_FILTER","Filter");
define("_DOCS_CONF_BLOCKS","Block Menuer");
define("_DOCS_CONF_BLOCKS_TEXT","Her modulets egen Menublock kan justeres.");
define("_DOCS_CONF_MENUWIDTH","Dybde Menuer");
define("_DOCS_CONF_MENUWIDTH_TEXT","Du angiver hvilken dybde Menu blokken skal specificeres.");
define("_DOCS_CONF_MENUCONTENT","Vælg indhold");
define("_DOCS_CONF_MENUCONTENT_TEXT","Vælg hvilke dokumenter vises i Menu blokken.");

define("_DOCS_PAGE_TITLE","sidetitel ");
define("_DOCS_PAGE_TITLE_TEXT","input anden side titel");
define("_DOCS_UPDATE_DB","Opdater database ");
define("_DOCS_UPDATE_DB_TXT","datasæt er blevet testet og tilpasset");

?>