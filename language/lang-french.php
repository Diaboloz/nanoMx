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
 * $Revision: 346 $
 * $Author: PragmaMx $
 * $Date: 2017-06-30 12:03:47 +0200 (Fr, 30. Jun 2017) $
 *
 * french language file, translated by:
 * Diabolo from www.pragmamx.fr
 */

defined('mxMainFileLoaded') or die('access denied');

/* Datestrings und locale Einstellungen: */
define("_CHARSET", "utf-8"); // Test:  äöüß
define("_LOCALE", "fr_FR");
$old_setlocale = setlocale(LC_TIME, 0);
$locale = array("fr_FR.UTF-8", "fr_FR.UTF8", "fr_FR", "fr", "fra", "french", "FR", "FRA", "250", "CTRY_FRANCE", "fr_FR.ISO-8859-15");
define("_SETLOCALE", setlocale(LC_TIME, $locale));
setlocale(LC_TIME, $old_setlocale);
define("_SETTIMEZONE", "Europe/Paris");
define("_DECIMAL_SEPARATOR", ",");
define("_THOUSANDS_SEPARATOR", " ");
define("_SPECIALCHARS", "ÀàÂâÆæÇçÈèÉéÊêËëÎîÏïÔôŒœÙùÛû");
define("_SPECIALCHARS_ONLY", false); // Schrift besteht nur aus Nicht-ASCII Zeichen
define("_DOC_LANGUAGE", "fr");
define("_DOC_DIRECTION", "ltr");
define("_DATESTRING", "%A %d %B %Y");
define("_DATESTRING2", "%A %d %B");
define("_XDATESTRING", "le %d.%m.%Y à %H:%M"); //(http://fr.php.net/strftime)
define("_SHORTDATESTRING", "%d.%m.%Y");
define("_XDATESTRING2", "%A %d %B");
define("_DATEPICKER", _SHORTDATESTRING);
define("_TIMEFORMAT", "%I:%M %p");
define("_DATETIME_FORMAT","%d.%m.%Y %H:%M");
define("_SYS_INTERNATIONALDATES", 1); //0 = mm/dd/yyyy, 1 = dd/mm/yyyy
define("_SYS_TIME24HOUR", 1); // 1 = 24 hour time... 0 = AM/PM time
define("_SYS_WEEKBEGINN", 0); # the First Day in the Week: 0 = Sunday, 1 = Monday
define("_Z1", "Tous les logos et les marques présentes sur ce site appartiennent à leurs propriétaires respectifs.<br />Des détails sur les copyrights et les modules installés peuvent être trouvés <a href=\"modules.php?name=Impressum\">Ici</a>.");
define("_Z2", "Les commentaires, les articles et le contenu sont quand à eux sous la responsabilité de leurs rédacteurs.<br />&copy; by <a href=\"" . PMX_HOME_URL . "\">" . $GLOBALS['sitename'] . "</a>");
define("_Z3", "Site propulsé par pragmaMx " . PMX_VERSION . ".");
define("_Z4", "Vous pouvez syndiquer le contenu de ce site : <a href=\"modules.php?name=rss\">Flux-RSS/RDF</a>.");
define("_YES", "Oui");
define("_NO", "Non");
define("_EMAIL", "Courriel");
define("_SEND", "Envoyer");
define("_SEARCH", "Rechercher");
define("_LOGIN", " Connexion ");
define("_WRITES", "a écrit");
define("_POSTEDON", "Posté le");
define("_NICKNAME", "Pseudo");
define("_PASSWORD", "Mot de passe");
define("_WELCOMETO", "Bienvenue sur");
define("_EDIT", "Editer");
define("_DELETE", "Supprimer");
define("_POSTEDBY", "Transmis par");
define("_GOBACK", "[ <a href=\"javascript:history.go(-1)\">Retour</a> ]");
define("_COMMENTS", "commentaire(s)");
define("_BY", "Profil de");
define("_ON", "le");
define("_LOGOUT", "Sortie");
define("_HREADMORE", "suite...");
define("_YOUAREANON", "Vous êtes un visiteur anonyme. Vous pouvez vous enregistrer gratuitement en cliquant <a href=\"modules.php?name=Your_Account\">ici</a>.");
define("_NOTE", "Note:");
define("_ADMIN", "Admin:");
define("_TOPIC", "Sujet");
define("_MVIEWADMIN", "Visualisation: Administrateurs seulement");
define("_MVIEWUSERS", "Visualisation: Utilisateurs enregistrés seulement");
define("_MVIEWANON", "Visualisation: Utilisateurs anonymes seulement");
define("_MVIEWALL", "Visualisation: Tous les visiteurs");
define("_EXPIRELESSHOUR", "Expiration: Moins d'une heure");
define("_EXPIREIN", "Expiration dans");
define("_UNLIMITED", "Illimitées");
define("_HOURS", "heures");
define("_RSSPROBLEM", "La manchette de ce site n'est pas disponible pour le moment.");
define("_SELECTLANGUAGE", "Sélectionnez la langue");
define("_SELECTGUILANG", "Sélectionnez la langue de l'interface:");
define("_BLOCKPROBLEM", "Il y a un problème avec ce bloc.");
define("_BLOCKPROBLEM2", "Ce bloc est vide.");
define("_MODULENOTACTIVE", "Désolé, ce module n'est pas activé");
define("_NOACTIVEMODULES", "Modules inactifs");
define("_NOVIEWEDMODULES", "Modules invisibles");
define("_FORADMINTESTS", "(Pour des tests administratifs)");
define("_ACCESSDENIED", "Accès refusé");
define("_RESTRICTEDAREA", "Vous essayez d'accéder à un espace réservé.");
define("_MODULEUSERS", "Nous sommes désolé mais cette section de notre site est pour les <i>utilisateurs enregistrés seulement</i><br /><br />Vous pouvez vous enregistrer gratuitement en cliquant <a href=\"modules.php?name=User_Registration\">ici</a>, puis vous pourrez<br />accéder à l'ensemble de cette section. Merci.");
define("_MODULESADMINS", "Nous sommes désolé mais cette section de notre site est réservée aux <i>administrateurs seulement</i>");
define("_HOME", "Accueil");
define("_HOMEPROBLEM", "Il y a un gros problème : Nous n'avons pas de page d'accueil!!!");
define("_ADDAHOME", "Ajouter un module à votre accueil");
define("_HOMEPROBLEMUSER", "Il y a un problème avec la page de démarrage. Veuillez vérifier.");
define("_DATE", "Date");
define("_HOUR", "Heure");
define("_UMONTH", "Mois");
define("_YEAR", "Année");
define("_YEARS", "Années");
define("_JANUARY", "Janvier");
define("_FEBRUARY", "Février");
define("_MARCH", "Mars");
define("_APRIL", "Avril");
define("_MAY", "Mai");
define("_JUNE", "Juin");
define("_JULY", "Juillet");
define("_AUGUST", "Août");
define("_SEPTEMBER", "Septembre");
define("_OCTOBER", "Octobre");
define("_NOVEMBER", "Novembre");
define("_DECEMBER", "Décembre");
define("_WEEKFIRSTDAY", "Dimanche");
define("_WEEKSECONDDAY", "Lundi");
define("_WEEKTHIRDDAY", "Mardi");
define("_WEEKFOURTHDAY", "Mercredi");
define("_WEEKFIFTHDAY", "Jeudi");
define("_WEEKSIXTHDAY", "Vendredi");
define("_WEEKSEVENTHDAY", "Samedi");
define("_MAIN", "Index");
define("_TERMS", "Termes");
define("_TOP", "top");
define("_SITECHANGE", "Changer le site:");
define("_BANNED", "Vous avez été banni de ce site internet !<br /><br />Veuillez contacter le Webmaster pour plus d'informations.");
define("_VKPBENCH1", "Page générée en ");
define("_VKPBENCH2", " secondes, avec ");
define("_VKPBENCH3", " requêtes SQL");
define("_ERRNOTOPIC", "Sélectionner un sujet.");
define("_ERRNOTITLE", "Il n'y a pas de titre pour cet article.");
define("_ERRNOTEXT", "Il n'y a pas de contenu pour cet article.");
define("_ERRNOSAVED", "Désolé, cet article n'a pas pu être stocké.");
define("_RETURNACCOUNT", "Retourner à votre compte");
define("_FORADMINGROUPS", "(groupe ne peut voir)");
define("_GROUPRESTRICTEDAREA", "Désolé, vous n'avez pas accès à cette partie de notre site.");
define("_NOGROUPMODULES", "Non-Groupe-Modules");
define("_AB_LOGOUT", "Déconnexion");
define("_AB_SETTINGS", "Réglages");
define("_AB_MESSAGE", "Message admin");
define("_AB_TITLEBAR", "Menu administration");
define("_AB_NOWAITINGCONT", "pas de contenu en attente");
define("_AB_RESETBCACHE", "Purge du cache");
define("_ERR_YOUBAD", "Vous avez essayé d'effectuer une opération illégale!");
define("_REMEMBERLOGIN", "Se souvenir du pseudo");
define("_ADMINMENUEBL", "Administration");
define("_MXSITEBASEDON", "Site basé sur");
define("_WEBMAIL", "Envoyer courriel");
define("_CONTRIBUTEDBY", "Contribution par");
define("_BBFORUMS", "Forums");
define("_BLK_MINIMIZE", "réduire");
define("_BLK_MAXIMIZE", "Agrandir");
define("_BLK_HIDE", "cacher");
define("_BLK_MESSAGE", "Message");
define("_BLK_MYBLOCKS", "Configuration des blocs");
define("_BLK_EDITADMIN", "Changer (Admin)");
define("_BLK_OPTIONS", "Options du bloc");
define("_BLK_OPTIONSCLICK", "Cliquer ici pour régler les options des blocs.");
define("_ADM_MESS_DATEEXPIRE", "Date");
define("_ADM_MESS_TIMES", "temps");
define("_ADM_MESS_DATESTART", "Date-Début");
define("_ADM_MESS_TODAY", "Aujourd'hui");
define("_DEFAULTGROUP", "Groupe par défaut");
define("_YOURELOGGEDIN", 'Merci, vous êtes connecté');
define("_YOUARELOGGEDOUT", "Vous êtes maintenant déconnecté.");
define('_CHANGESAREOK', 'Les changements ont été sauvegardés.');
define('_CHANGESNOTOK', 'Les changements n\'ont pas été sauvegardés.');
define('_DELETEAREOK', 'Les données ont été effacées.');
define('_DELETENOTOK', 'Les données n\'ont pas été effacées.');
define("_RETYPEPASSWD", "Retaper mot de passe");
define('_USERNAMENOTALLOWED', 'Ce nom d\'utilisateur &quot;%s&quot; est réservé.'); // %s = sprintf()
define('_SYSINFOMODULES', 'information sur les modules installés');
define('_SYSINFOTHEMES', 'information sur le design installé');
define("_ACCOUNT", "Votre compte");
define('_MAXIMALCHAR', 'max.');
define("_SELECTPART", "Sélectionner");
define("_CAPTCHAWRONG", "Le code est faux");
define("_CAPTCHARELOAD", "Recharger le code de sécurité");
define("_CAPTCHAINSERT", "Veuillez saisir le code de sécurité:");
define("_ERROROCCURS", "Désolé, les erreurs suivantes sont apparues:");
define("_VISIT", "Visite(s)");
define("_NEWMEMBERON", "Nouvel utilisateur enregistré");
define("_NEWMEMBERINFO", "Information utilisateur");
define("_SUBMIT", "Soumettre");
define("_GONEXT", "suivant");
define("_GOPREV", "précédent");
define("_USERSADMINS", "Administrateurs");
define("_USERSGROUPS", "Groupes utilisateurs");
define("_USERSMEMBERS", "Membres enregistrés");
define("_USERSOTHERS", "Tous les autres");
define("_FILES", "Fichiers");
define("_ACCOUNTACTIVATIONLINK", "Lien d'activation du compte");
define("_YSACCOUNT", "Compte");
define("_NEWSSHORT", "News");
define("_RESETPMXCACHE", "Purge du cache");
define("_MSGDEBUGMODE", "Mode-Debug activé !");
define("_ATTENTION", "Attention");
define("_SETUPWARNING1", "Veuillez renommer ou supprimer le répertoire -setup-!");
define("_SETUPWARNING2", "Pour renommer automatiquement le fichier 'setup/index.php', veuillez <a href='index.php?%s'>cliquer ici</a>.");
define("_AB_EVENT", "nouveau(x) évènement(s)");
define("_EXPAND2COLLAPSE_TITLE", "ouvrir ou fermer");
define("_EXPAND2COLLAPSE_TITLE_E", "ouvrir");
define("_EXPAND2COLLAPSE_TITLE_C", "fermer");
define("_TEXTQUOTE", "Citation");
define('_BBBOLD', 'Gras');
define('_BBITALIC', 'Italique');
define('_BBUNDERLINE', 'Souligné');
define('_BBXCODE', 'Code');
define('_BBEMAIL', 'Email');
define('_BBQUOTE', 'Citer');
define('_BBURL', 'Lien');
define('_BBIMG', 'Image');
define('_BBLIST', 'liste');
define('_BBLINE', 'ligne');
define('_BBNUMLIST', 'liste numérotée');
define('_BBCHARLIST', 'liste abc');
define('_BBCENTER', 'centré');
define('_BBXPHPCODE', 'Code PHP');
define("_ALLOWEDHTML", "HTML autorisé:");
define("_EXTRANS", "Extrans (html tags en texte)");
define("_HTMLFORMATED", "Format HTML");
define("_PLAINTEXT", "Texte seulement");
define("_OK", "Ok !");
define("_SAVE", "Sauver");
define("_FORMCANCEL", "Annuler");
define("_FORMRESET", "Supprimer");
define("_FORMSUBMIT", "Envoyer");
define("_PREVIEW", "Prévisualisation");
define("_NEWUSER", "Nouvel utilisateur");
define("_PRINTER", "Format imprimable");
define("_FRIEND", "Envoyer cet article à un(e) ami(e)");
define("_YOURNAME", "Votre nom");
define("_HITS", "Hits");
define("_LANGUAGE", "Langue");
define("_SCORE", "Score");
define("_NOSUBJECT", "Pas de Sujet");
define("_SUBJECT", "Sujet");
define("_LANGDANISH", "Danois");
define("_LANGENGLISH", "Anglais");
define("_LANGFRENCH", "Français");
define("_LANGGERMAN", "Allemand");
define("_LANGSPANISH", "Espagnol");
define("_LANGTURKISH", "Turc");
define("_LANGUAGES", "langages disponibles");
define("_PREFEREDLANG", "langue préférée");
define("_LEGAL", "Conditions d'utilisation");
// page
define("_PAGE", "Page");
define("_PAGES", "pages");
define("_OFPAGES", "sur");
define("_PAGEOFPAGES", "Page %d sur %d");
define("_GOTOPAGEPREVIOUS", 'page précédente');
define("_GOTOPAGENEXT", 'page suivante');
define("_GOTOPAGE", "vers la page");
define("_GOTOPAGEFIRST", "la première page");
define("_GOTOPAGELAST", "la dernière page");
define("_BLK_NOYETCONTENT", "Pas encore de contenu pour ce bloc");
define("_BLK_ADMINLINK", "Administration du module");
define("_BLK_MODULENOTACTIVE", "Le module '<i>%s</i>' de ce bloc n'est pas activé !");
define("_MODULEFILENOTFOUND", "Désolé, le fichier requis n'existe pas!");
define("_DEBUG_DIE_1", "Une erreur est survenue dans le script de cette page.");
define("_DEBUG_DIE_2", "Veuillez aviser le webmaster du site de l'erreur suivante.");
define("_DEBUG_INFO", "Informations de d&#233;bogage");
define("_DEBUG_QUERIES", "Queues-sql");
define("_DEBUG_REQUEST", "Requêtes");
define("_DEBUG_NOTICES", "Remarques");
define("_COMMENTSNOTIFY", "Il y a un nouveau commentaire sur \"%s\"."); // %s = sprintf $sitename
define("_REDIRECTMESS1", "Patientez un moment, vous allez être redirigé dans %d seconde(s)."); // %d = sprintf()
define("_REDIRECTMESS1A", "{Patientez un moment, vous allez être redirigé dans }s{ seconde(s).}"); // {xx}s{xx} formated: http://eric.garside.name/docs.html?p=epiclock#ec-formatting-options
define("_REDIRECTMESS2", "Ou cliquez ici, si vous ne voulez pas attendre.");
define("_REDIRECTMESS3", "patience...");
define("_DEACTIVATE", "Désactiver");
define("_INACTIVE", "Inactif");
define("_ACTIVATE", "Activer");
define("_XMLERROROCCURED", "Une erreur XML est survenue ligne");
// define("_ERRDEMOMODE", "Désolé, pas en mode demo!");
define("_JSSHOULDBEACTIVE", "Pour pouvoir utiliser cette fonction Javascript doit être activé.");
define("_CLICKFORFULLSIZE", "Cliquer pour afficher...");
define("_REQUIRED", "(requis)");
define("_SAVECHANGES", "Sauver les modifications");
define("_MODULESSYSADMINS", "Nous sommes désolés mais cette section de notre site est seulement accessible <em>aux Administrateurs-Système</em>");
define("_DATEREGISTERED", "Inscrit le");
define("_RESET", "Remise à zéro");
define("_PAGEBREAK", "Si vous voulez plusieurs pages, vous pouvez écrire <strong class=\"nowrap\">" . htmlspecialchars(PMX_PAGE_DELIMITER) . "</strong> à l'endroit où vous voulez un saut de page.");
define("_READMORE", "Lire la suite...");
define("_AND", "et");
define("_HELLO", "Salut");
define("_FUNCTIONS", "Fonctions");
define("_DAY", "Jour");
define("_TITLE", "Titre");
define("_FROM", "De");
define("_TO", "à");
define("_WEEK", "Semaine");
define("_WEEKS", "semaines");
define("_MONTH", "Mois");
define("_MONTHS", "mois");
define("_HELP", "Aide");
define("_COPY", "Copier");
define("_CLONE", "Cloner");
define("_MOVE", "déplacement");
define("_DAYS", "jours");
define("_IN", "Dans");
define("_DESCRIPTION", "Description");
define("_HOMEPAGE", "Page d'accueil");
define("_TOPICNAME", "Nom du sujet");
define("_GOTOADMIN", "Aller dans la partie administration");
define("_SCROLLTOTHETOP", "Vers le haut");
define("_NOTIFYSUBJECT", "Notification");
define("_NOTIFYMESSAGE", "Vous avez une nouvelle notification sur le site.");
define("_NOTITLE", "pas de titre");
define("_ALL", "Tous");
define("_NONE", "Aucun");
define("_BROWSE", "Parcourir");
define("_FILESECURERISK1", "RISQUE DE SECURITE MAJEUR");
define("_FILESECURERISK2", "Vous n'avez pas enlevé");
define("_CANCEL", "Annuler");
// Konstanten zur Passwortstärke
define("_PWD_STRENGTH", "Force du mot de passe:");
define("_PWD_TOOSHORT", "Trop court");
define("_PWD_VERYWEAK", "Très faible");
define("_PWD_WEAK", "Faible");
define("_PWD_GOOD", "Bien");
define("_PWD_STRONG", "Excellent");
define("_LEGALPP", "Votre conduite");
define("_MAILISBLOCKED", "Ce courriel (ou une partie de celui-ci) est bloqué.");
/* since 2.2.5*/
define("_COOKIEINFO","En utilisant notre site, vous acceptez d\'utiliser des cookies pour améliorer votre expérience.");
define("_MOREINFO","plus d\'information");

/* 2.4. */
define("_OFFLINE_1","Ce site est hors ligne maintenant");
define("_OFFLINE_2","Désolé, veuillez réessayer plus tard.");
define("_OFFLINE_3","Merci de votre compréhension.<br/>Votre administrateur");
langdefine("_MINUTE","Minute");
langdefine("_MINUTES","minutes");
langdefine("_READINGTIME","Temps de lecture à propos de");
?>