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

$toolbarlangarray = array('_NOACTION' => "Veuillez sélectionner un seul choix!",
    '_EXPANDALL' => 'Tout ouvrir',
    '_COLLAPSEALL' => 'Tout fermer',
    '_ADD' => 'Ajouter',
    '_ACCEPT' => 'Valider',
    '_BACK' => 'Retour',
    '_CANCEL' => 'Annuler',
    '_CATEGORYS' => 'Catégories',
    '_COLOR' => 'Couleurs',
    '_COMMENTS' => 'Commentaires',
    '_CONFIG' => 'Réglages',
    '_CONTENT' => 'Contenu',
    '_COPY' => 'Copier',
    '_CPANEL' => 'Menu admin',
    '_DELETE' => 'Supprimer',
    '_DOWN' => 'Dessous',
    '_DOWNLOAD' => 'Télécharger',
    '_EDIT' => 'Changer',
    '_FOLDER' => 'Dossier',
    '_HELP' => 'Aide',
    '_HOME' => 'Accueil',
    '_IMAGE' => 'Image',
    '_LINK' => 'Lien',
    '_MAIL' => 'Email',
    '_MOVE' => 'Déplacer',
    '_NEW' => 'Nouveau',
    '_NEWS' => "Articles",
    '_NEXT' => 'plus',
    '_PLUS' => 'Ajouter',
    '_PREVIEW' => 'Aperçu',
    '_PUBLISH' => 'Activer',
    '_REDIRECT' => 'Redirection',
    '_REFRESH' => 'Mise à jour',
    '_SAVE' => 'Enregistrer',
    '_SETTINGS' => 'Réglages',
    '_TOOLS' => 'Options',
    '_TRASH' => 'Poubelle',
    '_UNPUBLISH' => 'Désactiver',
    '_UP' => 'Dessus',
    '_UPLOAD' => 'Télécharger',
    '_USER' => 'Utilisateur',
    '_VOTE' => 'Evaluer',
    '_ZOOM' => 'Agrandir',
    '_SELECTTIME' => 'Sélectionner l\'heure',
    '_DEFAULT' => 'Norme',
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
