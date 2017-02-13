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

define("_CAPTCHASAMPLE", "Exemple");
define("_CAPTCHATITLE", "Administration du captcha");
define("_CAPTCHAIMAGEWIDTH", "Largeur de l'image");
define("_CAPTCHAIMAGEHEIGHT", "Hauteur de l'image");
define("_CAPTCHAFONTSIZE", "Taille de la police");
define("_CAPTCHABGINTENSITY", "Intensité d'arrière plan");
define("_CAPTCHABGFONTTYPE", "Type de police (Arrière plan)");
define("_CAPTCHASCRATCHAMOUNT", "Nombre de caractères");
define("_CAPTCHAPASSPHRASELENGHT", "Longueur du texte");
define("_CAPTCHAFILTER", "Utiliser les filtres de déformation");
define("_CAPTCHASCRATCHES", "Utiliser scratches");
define("_CAPTCHASAVESETTINGS", "Sauver les réglages");
define("_CAPTCHAFILTERTYPE", "Type de filtre de déformation");
define("_CAPTCHAADDHORLINES", "Ajouter des lignes de couleur");
define("_CAPTCHAADDAGRID", "Ajouter des grilles");
define("_CAPTCHARANDOMCOLOR", "Utiliser des couleurs au hasard");
define("_CAPTCHAANGLE", "Angle des caractères");
define("_CAPTCHAMINSIZE", "Distance minimum des trames pour les grilles");
// define("_CAPTCHAFEEDBACKON", "Activer dans contactez-nous");
// define("_CAPTCHAFAQON", "Activer dans la FAQ");
// define("_CAPTCHAWEBLINKSON", "Activer dans l'annuaire de liens");
// define("_CAPTCHADOWNLOADSON", "Activer dans les téléchargements");
// define("_CAPTCHANEWSON", "Activer dans le module des news");
// define("_CAPTCHANEWSLETTERON", "Activer dans le module newsletter");
// define("_CAPTCHAGUESTBOOKON", "Activer dans le livre d'or");
// define("_CAPTCHAREVIEWSON", "Activer dans le module des comptes rendus");
define("_CAPTCHAUSERON", "Activer aussi le captcha pour les membres enregistrés");
define("_CAPTCHAREGISTRATIONON", "l'enregistrement des nouveaux utilisateurs");
// define("_CAPTCHARECOMMENDON", "Activer dans le module  '" . _RECOMMEND . "'");
define("_CAPTCHACOMMENTSON", "commentaires");
define("_CAPTCHAANSWERSUSE", "Utiliser des questions");
define("_CAPTCHAANSWERSCOUNT", "Nombre de réponses prédéfinies");
define("_CAPTCHADIGITSRANGE1", "Nombre de possibilitées");
define("_CAPTCHADIGITSRANGE2", "à");
define("_CAPTCHACALCSTEPS", "Nombre d'étapes de calcul");
define("_CAPTCHAERRORINGD", "L'image captcha ne peut pas être générée car la librairie GD de votre installation PHP ne supporte pas JPEG.");
define("_CAPTCHACHARSTOUSE", "Caractères autorisés dans le code");
define("_CAPTCHACHARCASESEN", "Respecter la case lors de la saisie");
define("_CAPTCHAERR", "L'image du captcha ne peut pas être affichée correctement car le problème suivant est survenu :");
define("_CAPTCHAERR_MISSINGGD", "La bibliothèque GD n'est pas installée ou la version utilisée est trop ancienne, utilisez au minimum la version 2.0. (<a href=\"http://www.php.net/manual/ref.image.php\">infos</a>)");
define("_CAPTCHAERR_FALSEFT", "La bibliothèque GD n'est pas installée ou la prise en charge FreeType n'est pas configurée correctement.");
define("_CAPTCHAERR_FALSEGD", "Vous utilisez probablement une version incompatible de la bibliothèque GD, utilisez au minimum la version 2.0. (<a href=\"http://www.php.net/manual/ref.image.php\">infos</a>)");
define("_CAPTCHAERR_NOJPG", "Le support JPG de la bibliothèque GD est indisponible.");
define("_CAPTCHAERR_MISSINGFT", "Le support FreeType de la bibliothèque GD est indisponible.");
define("_CAPTCHASETTINGS", "Réglages");
define("_CAPTCHASESSION", "Requête captcha une seule fois par session ?");
define("_CAPTCHAMODSET", "Activer les modules pour les zones suivantes:");
define("_CAPTCHAMODHAVEOWN", "Les modules suivants utilisent leurs propres paramètres pour activer le Captcha:");
define("_CAPTCHASETTINGS2", "Activation");
define("_CAPTCHASETRESET", "Tout ignorer et rétablir par défaut du système.");

?>