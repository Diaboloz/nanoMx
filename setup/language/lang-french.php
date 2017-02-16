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
 * $Revision: 227 $
 * $Author: PragmaMx $
 * $Date: 2016-09-28 15:44:45 +0200 (Mi, 28. Sep 2016) $
 *
 * french language file, translated by:
 * Diabolo from www.pragmamx.fr
 */

defined('mxMainFileLoaded') or die('access denied');

/* Datestrings und locale Einstellungen: */
$old_setlocale = setlocale(LC_TIME, 0);
$locale = array("fr_FR.UTF-8", "fr_FR.UTF8", "fr_FR", "fr", "fra", "french", "FR", "FRA", "250", "CTRY_FRANCE", "fr_FR.ISO-8859-15");
define("_SETLOCALE", setlocale(LC_TIME, $locale));
setlocale(LC_TIME, $old_setlocale);

define("_DOC_LANGUAGE", "fr");
define("_DOC_DIRECTION", "ltr");
define('_DATESTRING', '%d.%m.%Y');

/**
 * Setup Optionen zur Auswahl, siehe setup-settings.php
 */
// Neuinstallation
define('_SETUPOPTION_NEW', 'Nouvelle installation');
define('_SETUPOPTION_NEW_DESC', 'Vous allez lancer une nouvelle installation de pragmaMx, les données déjà présentes dans votre base de données seront conservées.');
// Update
define('_SETUPOPTION_UPDATE', 'Mise à jour d\'une installation existante');
define('_SETUPOPTION_UPDATE_DESC', 'Le script d\'installation va mettre à jour une version obsolète et fonctionnelle de PragmaMx. Ce script peut également convertir les données de phpNuke, vkpMx et clones de phpNuke.');
// Setupschritte
define('_STEP_SELECT', 'Veuillez choisir, nouvelle installation ou mise à jour');
define('_STEP_ISINCORRECT', 'Interrogation de sécurité');
define('_STEP_LICENSE', 'Licence d\'utilisation');
define('_STEP_BACKUP', 'Sauvegarde de la base de données');
define('_STEP_UPDATE', 'Mise à jour de la base de données');
define('_STEP_DELFILES', 'Suppression des fichiers obsolètes');
define('_STEP_FINISHEDINSTALL', 'Installation terminée');
define('_STEP_DBSETTINGS', 'Configuration de l\'accès à la base de données');
define('_STEP_DBSETTINGSCREATE', 'Configuration de la base de données/Création de la base');
define('_STEP_MORESETTINGS', 'Réglages complémentaires');
define('_STEP_MORESETTINGSCHECK', 'Vérification et validation des réglages');
define('_STEP_FINISHEDUPDATE', 'Mise à jour terminée');
define('_STEP_CONFIGURATION', 'Mise à jour de la configuration');
define('_HELLOINSTALL', 'Installation de <span class="label">' . MX_SETUP_VERSION .'</span>');
define('_HELLOINSTALL2', 'Merci d\'avoir choisi ' . preg_replace('#[[:space:]]#', '&nbsp;', MX_SETUP_VERSION) . '. <br /><br />Avant de commencer, veuillez lire attentivement la <a href="' . _MXDOKUSITE . '">documentation sur l\'installation en ligne</a>.<br /> Lecture faite, vous pouvez continuer....');
define('_WHATWILLYOUDO', 'Pour l\'installation, vous avez différents choix, la méthode recommandée est sélectionnée automatiquement par le script d\'installation. Veuillez sélectionner une autre méthode seulement si vous êtes sûr de vous.<br /><br />Que voulez-vous faire ?');
define('_OLDVERSION_ERR1', 'La méthode sélectionnée ne correspond pas à celle choisie par le système,<br />ceci peut engendrer des problèmes par la suite.');
define('_OLDVERSION_ERR2', 'Etes-vous sûr de vouloir exécuter cette méthode &quot;<em>' . @constant($GLOBALS['opt'][@$_REQUEST['setupoption']]['name']) . '</em>&quot;?');
define('_CONFIGSAVEMESS', 'Pour finaliser, le fichier de configuration <em>' . basename(FILE_CONFIG_ROOT) . '</em> est mis à jour.');
define('_CONFIG_OK_NEW', 'Le fichier de configuration <em>' . basename(FILE_CONFIG_ROOT) . '</em> a été crée avec succès.');
define('_CONFIG_OK_OLD', 'Le fichier de configuration <em>' . basename(FILE_CONFIG_ROOT) . '</em> était déjà présent et fonctionnel.');
define('_CONFIG_ERR_1', 'Le fichier <em>' . basename(FILE_CONFIG_ROOT) . '</em> est protégé en écriture !');
define('_CONFIG_ERR_2', 'L\'écriture dans le fichier <em>' . basename(FILE_CONFIG_ROOT) . '</em> n\'a pas pu être effectuée.');
define('_CONFIG_ERR_3', 'Le dossier <em>' . PMX_BASE_PATH . '</em> est protégé en écriture !');
define('_CONFIG_ERR_4', 'Le fichier <em>' . basename(FILE_CONFIG_ROOT) . '</em> n\'a pas pu être créé, ce pour des raisons inconnues.');
define('_CONFIG_ERR_5', 'Le fichier <em>' . basename(FILE_CONFIG_ROOT) . '</em> est effectivement présent, mais il ne peut pas être lu.');
define('_CONFIG_ERR_6', 'Le fichier <em>' . basename(FILE_CONFIG_ROOT) . '</em> est effectivement présent, mais les données n\'ont pas été insérées correctement.');
define('_CONFIG_ERR_8', 'Le fichier de configuration <em>' . basename(FILE_CONFIG_ROOT) . '</em> n\'a pas été créé correctement, mais la connexion avec la base de données est fonctionnelle. Vous devez absolument vérifier vos réglages et au besoin arrêter l\'installation pous faire vos vérifications ce avant de continuer, vous devrez cependant arriver sur le panneau administration.');
define('_CONFIG_BACK', 'Le fichier de configuration existant a été dupliqué avec succès, sous le nom suivant:');
define('_CONFIG_CREATE', 'Veuillez créer vous même un nouveau fichier avec votre éditeur de texte et copier/coller le code php affiché ci-dessous. Nommez ce fichier en <em>' . basename(FILE_CONFIG_ROOT) . '</em> et sauvegardez le dans le répertoire suivant (' . dirname(basename(FILE_CONFIG_ROOT)) . ') de votre installation pragmaMx.<br />Veuillez vérifier que le code complet de la source 1:1 de ce fichier est bien enregistré.<br /><br />Ensuite, vous pouvez continuer l\'installation.');
define('_CONFIG_BUTTONMAN', 'Créer manuellement le fichier de configuration');
define('_CURRENTSTATUS', 'Statut actuel de l\'installation');
define('_THEREERROR', 'Erreurs survenues');
define('_WILL_CREATE_TABLES', 'Dans l\'étape suivante les tables seront créées ou mises à jour dans la base de données, cette opération peut durer quelques instants.');
define('_WILL_CREATE_BACKUP', 'Si vous utilisez l\'option de sauvegarde, une sauvegarde complète des tables déjà présentes sera effectuée avant la création des tables du système.');
define('_CONTINUE_WITHOUTDBBACKUP', 'Continuer sans sauvegarder');
define('_CONTINUE_WITHDBBACKUP', 'Continuer en effectuant une sauvegarde');
define('_DBFOLLOWERRORS', 'Les erreurs suivantes sont apparues');
define('_NODBERRORS', 'Aucune erreur.');
define('_DBNOTEXIST', 'La base de données que vous avez sélectionné n\'existe pas sur le serveur.');
define('_DBNOTSELECT', 'Vous n\'avez pas sélectionné la base de données.');
define('_DBNOACCESS', 'La connexion avec la base de données a été refusée.');
define('_DBOTHERERR', 'Une erreur est survenue lors de la connexion avec la base de données.');
define('_DBVERSIONFALSE', 'Désolé, la version de mySQL est trop ancienne. Pour installer pragmaMx vous devez au minimum disposer de la version %s du Serveur-MySQL.');
define('_NOT_CONNECT', 'Connexion impossible avec la base de données.');
define('_NOT_CONNECTMORE', 'Veuillez vous assurer que le fichier config.php existe bien sur le serveur et que les informations de connexion à la base de données sont correctes.');
define('_DB_CONNECTSUCCESS', 'La connexion avec la base de données <em>%s</em> a été réalisée avec succès.');
define('_CORRECTION', 'Corriger');
define('_REMAKE', 'Recommencer');
define('_IGNORE', 'Ignorer et continuer');
define('_DONOTHING', 'Ignorer');
define('_DBARETABLES', '<li>Des tables sont déjà présentes dans la base de données que vous avez choisi.</li><li>Une sauvegarde de la base de données est fortement recommandée.</li>');
define('_DBARENOTABLES', '<li>La base de données choisie étant vide, vous n\'avez pas besoin d\'en effecuer la sauvegarde.</li>');
define('_SUBMIT', 'Continuer');
define('_OR', 'Ou');
define('_YES', 'Oui');
define('_NO', 'Non');
define('_GOBACK', 'Retour');
define('_CANCEL', 'Annuler');
define('_FILE_NOT_FOUND', 'Fichier non trouvé ou non lisible.');
define('_ACCEPT', 'Acceptez-vous l\'agrément de licence ?');
define('_START', 'Début du site');
define('_INTRANETWARNING', 'Intranet doit être activé si vous n\'arrivez pas à accéder à votre système avec votre nom de domaine (ex: www.monsite.com). Il n\'est pas recommandé d\'activer ce mode sauf si vous êtes derrière un pare-feu ou si les utilisateurs derrière un pare-feu ne peuvent pas accéder à votre site.');
define('_PRERR11', 'Les deux préfixes doivent être écrit en minuscule et doivent commencer par une lettre, ils peuvent contenir des nombres, des lettres et le signe souligné (_) mais ceux-ci ne doivent pas avoir une longueur totale de plus de ' . PREFIX_MAXLENGTH . ' caractères.');
define('_PRERR12', 'Le nouveau préfixe n\'a pas de valeur.<br />Veuillez indiquer un préfixe.');
define('_PRERR13', 'Le nouveau préfixe ne correspond pas à la norme standart pragmaMx ou phpNuke. Veuillez utiliser un autre préfixe pour des raisons de sécurité.');
define('_PRERR14', 'Vous utilisez des caractères interdits pour votre préfixe. Utilisez seulement des lettres, des minuscules, des chiffres et le caractère souligné (_), le préfixe ne peut pas commencer par un nombre.<br />Veuillez corriger et utiliser un autre préfixe.');
define('_PRERR15', 'Le préfixe ne peut pas commencer par un nombre.<br />Veuillez corriger et utiliser un autre préfixe.');
define('_PRERR16', 'Le nouveau préfixe des tables est trop long, le préfixe ne doit pas avoir une longueur totale de plus de ' . PREFIX_MAXLENGTH . ' caractères.<br />Veuillez raccourcir votre préfixe.');
define('_PRERR17', 'Il existe déjà %d tables avec le nouveau préfixe.<br />Veuillez utiliser un autre préfixe.');
define('_PRERR18', 'Le nouveau préfixe des utilisateurs n\'a pas de valeur.<br />Veuillez indiquer un préfixe des utilisateurs.');
define('_PRERR19', 'Vous utilisez des caractères interdits pour votre préfixe des utilisateurs. Utilisez seulement des lettres, des minuscules, des chiffres et le caractère souligné (_), le préfixe ne peut pas commencer par un nombre.<br />Veuillez corriger et utiliser un autre préfixe.');
define('_PRERR20', 'Le préfixe des utilisateurs ne peut pas commencer par un nombre.<br />Veuillez corriger et utiliser un autre préfixe pour les utilisateurs.');
define('_PRERR21', 'Le nouveau préfixe des utilisateurs est trop long, le préfixe ne doit pas avoir une longueur totale de plus de ' . PREFIX_MAXLENGTH . ' caractères.<br />Veuillez raccourcir votre préfixe.');
define('_PRERR22', 'Une table des utilisateurs avec ce nouveau préfixe est déjà présente.<br />Veuillez utiliser un autre préfixe pour la table des utilisateurs.');
define('_SUPPORTINFO', 'Aide et support pour votre système sont disponibles ici: <a href="' . _MXSUPPORTSITE . '">' . _MXSUPPORTSITE . '</a>');
define('_DOKUINFO', 'La documentation en ligne est disponible ici: <a href="' . _MXDOKUSITE . '">' . _MXDOKUSITE . '</a>');
define('_NOBACKUPCREATED', 'La sauvegarde de la base de données n\'a pas pu être effectuée.');
define('_HAVE_CREATE_DBBACKUP', 'Votre base de données a été sauvegardée dans le fichier suivant:');
define('_HAVE_CREATE_BACKUPERR_1', 'Erreur de sauvegarde de la base de données!');
define('_HAVE_CREATE_BACKUPERR_2', 'Si la base de données contient des données, veuillez vérifier :<br />Que vous avez une sauvegarde récente et fonctionnelle de celle-ci AVANT de continuer !');
define('_SETUPHAPPY1', 'Félicitations,');
define('_SETUPHAPPY2', 'Votre système est désormais complètement installé, au prochain clic vous serez automatiquement redirigé vers le panneau d\'administration.');
define('_SETUPHAPPY3', 'Ainsi, vous pourrez à  nouveau vérifier vos réglages initiaux puis validez par la sauvegarde.');
define('_DELETE_FILES', 'Si votre système fonctionne correctement, veuillez absolument supprimer le répertoire &quot;<em>' . basename(dirname(__DIR__)) . '</em>&quot;.<br /><strong>Ce répertoire pourrait présenter un risque pour la sécurité du système!</strong>');
define('_GET_SQLHINTS', 'Ci-dessous, la liste de toutes les requêtes SQL qui ont été importées durant le processus de conversion/intégration');
define('_DATABASEISCURRENT', 'La structure de la base de données était déjà présente, les modifications étaient inutiles.');
define('_SEEALL', 'Journal complet');
define('_DB_UPDATEREADY', 'La conversion/intégration des tables est terminée.');
define('_DB_UPDATEFAIL', 'La conversion/intégration des tables n\'a pas pu être effectuée complètement.');
define('_DB_UPDATEFAIL2', 'Voici les tables importantes du système qui manquent: ');
define('_BACKUPPLEASEDOIT', 'Il est fortement recommandé d\'effectuer une sauvegarde complète de la base de données avant la mise à jour.');
define('_ERRMSG1A', 'Erreur: Un des fichiers nécessaire à la mise à jour est manquant, veuillez vous assurer que le fichier suivant est bien présent:');
define('_YEAHREADY2', 'Votre pragmaMx est désormais à jour.');
define('_SERVERMESSAGE', 'Message du serveur');
define('_ERRDBSYSFILENOFILES', 'Aucune table du système n\'a pu être intégrée/vérifiée, car aucun fichier de définition n\'a été trouvé dans le répertoire <em>' . PATH_SYSTABLES . '</em>.');
define('_ERRDBSYSFILEMISSFILES_1', 'Toutes les tables du système n\'ont pas pu être intégrées/vérifiées.');
define('_ERRDBSYSFILEMISSFILES_2', 'Dans le répertoire <em>' . PATH_SYSTABLES . '</em> , il manque les fichiers de définition suivants');
define('_THESYSTABLES_1', '<strong>%s</strong> table(s) du système n\'ont pas été intégrées/vérifiées, car le(s) fichiers(s) du répertoire ' . PATH_SYSTABLES . ' n\'ont pas pu être chargé.');
define('_THESYSTABLES_2', '<strong>%s</strong> table(s) du système n\'ont pas été intégrées/vérifiées.');
define('_SYSTABLECREATED', 'Intégration/vérification de %d tables du système.');
define('_MODTABLESCREATED', 'Intégration/vérification de %d tables des modules.');
define('_NOMODTABLES', 'Aucune table de module n\'a été intégrée/vérifiée.');
define('_STAT_THEREWAS', 'Il y a');
define('_STAT_TABLES_CREATED', 'table(s) créé(s).');
define('_STAT_TABLES_RENAMED', 'table(s) renommée(s).');
define('_STAT_TABLES_CHANGED', 'table(s) modifiée(s).');
define('_STAT_DATAROWS_CREATED', 'donnée(s) insérée(s)/modifiée(s).');
define('_STAT_DATAROWS_DELETED', 'donnée(s) supprimée(s).');
define('_MOREDEFFILEMISSING', 'Le fichier (<em>' . @ADD_QUERIESFILE . '</em>) et ses requêtes SQL est manquant!');
define('_SETUPMODNOTFOUND1', 'L\'installateur du module <strong>%s</strong> est introuvable!');
define('_ERROR', 'Erreur');
define('_ERROR_FATAL', 'Erreur fatale');
define('_SETUPCANCELED', 'L\'installation a été interrompue!');
define('_GOTOADMIN', 'Aller au panneau administration');
define('_DBSETTINGS', 'Indiquez ici les informations d\'accès à votre base de données. La routine d\'installation continuera seulement si la connexion avec la base de données est fonctionnelle. Normalement, votre hébergeur vous a transmis les paramêtres relatifs à votre base de données.');
define('_DBNAME', 'Nom de la base de données');
define('_DBPASS', 'Mot de passe de la base de données');
define('_DBSERVER', 'Serveur de la base de données');
define('_DBTYP', 'Type de base de données');
define('_DBUSERNAME', 'Nom d\'utilisateur de la base de données');
define('_DBCREATEQUEST', 'Voulez-vous essayer de créer la base de données &quot;<em>' . @$_REQUEST['dbname'] . '</em>&quot;?');
define('_DBISCREATED', 'La base de données &quot;<em>' . @$_REQUEST['dbname'] . '</em>&quot; a été crée avec succès.');
define('_DBNOTCREATED', 'Une erreur est survenue pendant la création de la base de données &quot;<em>' . @$_REQUEST['dbname'] . '</em>&quot;.'); # settings
define('_PREFIXSETTING', 'Les préfixes servent à faire la distinction entre les différentes tables, notamment si vous souhaitez utiliser plusieurs pragmaMx avec la même base de données. Le préfixe de la table des utilisateurs permet l\'utilisation commune des données utilisateurs dans plusieurs pragmaMx distincts. Si vous ne souhaitez pas utiliser cette fonctionnalité, laissez le même préfixe pour la table des utilisateurs.');
define('_PREFIX', 'Préfixe des tables de la base de données');
define('_USERPREFIX', 'Préfixe de la table des utilisateurs');
define('_DEFAULTLANG', 'Langue par défaut');
define('_INTRANETOPT', 'Exécuter en intranet');
define('_ADMINEMAIL', 'Courriel administrateur');
define('_SITENAME', 'Nom du site');
define('_STARTDATE', 'Date de démarrage');
define('_CHECKSETTINGS', 'Veuillez vérifier vos réglages!');
define('_PLEASECHECKSETTINGS', 'Veuillez vérifier une dernière fois vos réglages.<br />Si tout est correct, vous pouvez poursuivre la routine d\'installation.<br />Sinon, vous avez la possibilité de corriger vos réglages.');
define('_HAVE_CREATE_TABLES', 'Tables créées.');
define('_HAVE_CREATE_TABLES_7', 'Les tables nécessaires au système ont été intégrées sans erreur. La routine d\'installation peut continuer, mais avec différentes fonctions du système on peut en venir à des erreurs.');
define('_HAVECREATE_TABLES_ERR', 'La base de données n\'a pas pu être créée complètement. L\'installation a échouée.');
define('_CREATE_DB', 'Créer la base de données');
define('_DELETESETUPDIR', 'Cochez pour neutraliser le programme d\'installation. Ainsi, le fichier index.php sera renommé et l\'accès au répertoire sera refusé grâce à un fichier .htaccess <em>(Ne fonctionne pas sur tous les serveurs.)</em>');
// add for fieldset
define('_PREFIXE', 'Préfixes');
define('_SITE__MORESETTINGS', 'Réglages du site');
define('_SERVER', 'Données serveur');
define('_BACKUPBESHURE', 'Assurez-vous de la suite de la conversion des tables de la base de données, veuillez vérifier que vous avez une sauvegarde récente de votre base de données.');
define('_BACKUPBESHUREYES', 'Oui, j\'ai une sauvegarde récente de ma base de données.');
define('_BACKUPBESHUREOK', 'Veuillez comfirmer que vous avez un sauvegarde récente de vote base de données.');
// Modulbezeichnungen
define('Your_Account', 'Votre compte');
define('News', 'Articles');
define('blank_Home', 'Accueil');
define('Content', 'Contenu');
define('Downloads', 'Téléchargements');
define('eBoard', 'Forums');
define('FAQ', 'FAQ');
define('Feedback', 'Contactez-nous');
define('Guestbook', 'Livre d or');
define('Impressum', 'Copyrights');
define('Kalender', 'Evènements');
define('Statistics', 'Statistiques');
define('Members_List', 'Liste des membres');
define('My_eGallery', 'Galerie médias');
define('Newsletter', 'Newsletter');
define('Private_Messages', 'Messages privés');
define('Recommend_Us', 'Recommandez-nous');
define('Reviews', 'Comptes rendus');
define('Search', 'Rechercher');
define('Sections', 'Sections');
define('Siteupdate', 'News du site');
define('Submit_News', 'Soumettre un article');
define('Surveys', 'Sondages');
define('Top', 'Top');
define('Topics', 'Sujets');
define('UserGuest', 'Livre d or utilisateur');
define('Web_Links', 'Annuaire de liens');
define('Web_News', 'News internet');
define('LinkMe', 'Liens vers nous');
define('Userinfo', 'Information utilisateur');
define('User_Registration', 'Enregistrement utilisateur');
define('Gallery', 'Galerie');
define('Avatar', 'Votre avatar');
define('Banners', 'Vos bannières');
define('Encyclopedia', 'Encyclopédie');
define('IcqList', 'Votre liste ICQ');
define('IrcChat', 'Chat');
define('Members_Web_Mail', 'Votre Web-Mail');
define('Stories_Archive', 'Articles archivés');
define('Themetest', 'Thèmes');
define('User_Blocks', 'Vos blocs');
define('User_Fotoalbum', 'Votre album photo');
define('legal', 'Conditions utilisation');
// die Nachricht für den Begrüssungsblock
define('_NEWINSTALLMESSAGEBLOCKTITLE', 'Bienvenue sur votre pragmaMx ' . MX_SETUP_VERSION_NUM . '');
define('_NEWINSTALLMESSAGEBLOCK', trim(addslashes('
<p>Bienvenue,</p>
<p>Si vous pouvez lire ce message alors pragmaMx semble fonctionner sans erreur, félicitations encore.</p>
<p>Nous voulons d\'abord vous remercier cordialement de vous être décidé à utiliser notre système pragmaMx, nous espérons ainsi que notre CMS répondra à toutes vos attentes.</p>
<p>Des modules supplémentaires dédiés à votre pragmaMx sont également disponibles sur notre site internet : <a href="http://www.pragmamx.org">http://pragmamx.org</a>.</p>
<p>A la fin de l\'installation, si vous n\'avez pas encore créé de compte administrateur pour votre système, veuillez <a href="' . adminUrl() . '"><strong>cliquer ici</strong></a>.</p>
<p>Nous vous souhaitons beaucoup de plaisir lors de la découverte de votre pragmaMx. De même, vous trouverez également dans le panneau d\'administration une documentation succinte du système, n\'hésitez pas à y jeter un oeil.</p>
<p>Votre équipe pragmaMx...</p>
')));
define('_DBUP_WAIT', 'Veuillez patienter');
define('_DBUP_MESSAGE', '
<p>Le programme d\'installation va configurer votre système pragmaMx. </p>
<p>La mise à jour des tables de la base de données peut prendre un certain temps, veuillez attendre la fin du processus. Durant ce temps, veuillez également ne pas quitter, ne pas actualiser la page et ne pas fermer le navigateur.</p>
');

// Blockbeschriftungen:
define('_BLOCK_CAPTION_MAINMENU', 'Sommaire');
define('_BLOCK_CAPTION_INTERNAL', 'Interne');
define('_BLOCK_CAPTION_COMMUNITY', 'Communauté');
define('_BLOCK_CAPTION_OTHER', 'Autres');
define('_BLOCK_CAPTION_1', 'Alarme-Installation');
define('_BLOCK_CAPTION_2', 'Menu administration');
define('_BLOCK_CAPTION_3', 'Langue');
define('_BLOCK_CAPTION_4', 'Connexion');
define('_BLOCK_CAPTION_5', 'Menu utilisateur');
define('_BLOCK_CAPTION_6', 'Qui est en ligne');
define('_BLOCK_CAPTION_7', 'FAQs');
define('_BLOCK_CAPTION_8', 'Sondages');
define('_BLOCK_CAPTION_9', 'News-pragmaMx');
define('_BLOCK_CAPTION_5A', 'Votre menu personnel');

/* Umgebungstest, äquivalent zu pmx_check.php */
define("_TITLE", " " . MX_SETUP_VERSION . "  test d'environnement");
define("_ENVTEST", "Test d'environnement");
define("_SELECTLANG", "Veuillez sélectionner une langue");
define("_TEST_ISOK", "OK, le système peut faire fonctionner  " . MX_SETUP_VERSION . " ");
define("_TEST_ISNOTOK", "Votre système ne répond pas aux exigences requises pour  " . MX_SETUP_VERSION . " ");
define("_LEGEND", "Légende");
define("_LEGEND_OK", "<span>ok</span> - Tout est bon");
define("_LEGEND_WARN", "<span>warning</span> - Pas indispensable, mais recommandé pour profiter de certaines fonctionnalités");
define("_LEGEND_ERR", "<span>error</span> -  " . MX_SETUP_VERSION . "  en a besoin et ne peut pas fonctionner sans");
define("_ENVTEST_PHPFAIL", "La version PHP minimum requise pour faire fonctionner  " . MX_SETUP_VERSION . "  est PHP <strong>%s</strong>. Votre version PHP: <strong>%s</strong>");
define("_ENVTEST_PHPOK", "Votre version PHP est: <strong>%s</strong>");
define("_ENVTEST_MEMOK", "Votre limite de mémoire est: <strong>%s</strong>");
define("_ENVTEST_MEMFAIL", "Votre mémoire est trop faible pour terminer l'installation. La valeur minimum est <strong>%s</strong>, et vous avez: <strong>%s</strong>");
define("_EXTTEST_REQFOUND", "L'extension <strong>'%s'</strong> est présente");
define("_EXTTEST_REQFAIL", "L'extension <strong>'%s'</strong> est requise pour faire fonctionner  " . MX_SETUP_VERSION . " .");
define("_EXTTEST_GD", "GD est utilisé pour la manipulation d'images. Sans cela, le système n'est pas capable de créer des vignettes pour les fichiers ou gérer les avatars, logos et icônes du projet.");
define("_EXTTEST_MB", "Chaîne multi-octets est utilisé pour le travail avec Unicode. Sans cela, le système ne peut pas séparer les mots et les chaines correctement et vous pouvez avoir d'étranges caractères comme par exemple des points d'interrogations dans les activités récentes.");
// define("_EXTTEST_ICONV", "Iconv est utilisé pour la conversion du jeu de caractères. Sans cela, le système est un peu plus lent lors de la conversion d'un jeu de caractères différent.");
define("_EXTTEST_IMAP", "IMAP est utilisé pour se connecter aux serveurs POP3 et IMAP. Sans cela, le module de courrier entrant ne fonctionne pas.");
define("_EXTTEST_CURL", "Cette fonction permet l'accès aux données externes.");
define("_EXTTEST_TIDY", "Lorsque l'extension Tidy est active, la sortie HTML est validée automatiquement. Cela permet également d'accélérer la mise en page dans le navigateur et affiche le site conforme aux normes W3C.");
define("_EXTTEST_XML", "L'extension XML est nécessaire notamment pour générer le flux RSS.");
define("_EXTTEST_RECFOUND", "L'extension <strong>'%s'</strong> est présente");
define("_EXTTEST_RECNOTFOUND", "L'extension <strong>'%s'</strong> est absente <span class=\"details\">%s</span>");
define("_VERCHECK_DESCRIBE", "Les fichiers/dossiers répertoriés ici sont obsolètes pour " . MX_SETUP_VERSION . ". Dans certaines circonstances ils peuvent causer des défauts et problèmes de sécurité. Ils doivent donc être supprimés obligatoirement..");
define("_VERCHECK_DEL", "Fichiers et dossiers supprimés");
define("_FILEDELNOTSURE", "Cette étape peut être reportée et effectuée plus tard dans le gestionnaire de version du système.");
define("_ERRMSG2", "Les fichiers / dossiers suivants ne peuvent pas être supprimés automatiquement. Vous pouvez le faire plus tard en utilisant le gestionnaire de version du système.");
/// OLD !!!  define("_ERRMSG2", "Les fichiers et/ou répertoires suivants n\'ont pas pu être supprimé automatiquement.');
define("_PDOTEST_OK", "Pilote de base de données PDO (%s) est fonctionnel");
define("_PDOTEST_FAIL", "Aucun pilote utilisable de base de données PDO (z.B. %s) trouvé");
define("_EXTTEST_PDO", "L'extension PDO sera dans l'avenir le moteur de base de données par défaut pour le système. L'extension devrait être disponible dès que possible.");
define("_EXTTEST_ZIP", "La fonctionnalité ZIP est utilisée par certains modules/add-on et devrait être disponible.");

define("_DBCONNECT","connexion de base de données");
define("_EXTTEST_FILE_FAIL", "ne peut pas écrire : %s");
define("_EXTTEST_FILE_OK", "Tous les accès de fichiers disponibles.");
?>