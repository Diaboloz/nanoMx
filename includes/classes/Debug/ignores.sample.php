<?php
/**
 * This file is part of
 * pragmaMx - Web Content Management System.
 * Copyright by pragmaMx Developer Team - http://www.pragmamx.org
 *
 * $Revision: 6 $
 * $Author: PragmaMx $
 * $Date: 2015-07-08 09:07:06 +0200 (Mi, 08. Jul 2015) $
 */

defined('mxMainFileLoaded') or die('access denied');

/**
 * bestimmte Fehlermeldungen nicht beachten, dazu einfach einen Teilstring
 * der entsprechenden Fehlermeldung als Array-Wert hier einfuegen.
 * ACHTUNG: die Dinger muessen natuerlich noch gefixt werden !!!
 */
$ignores['text'] = array (/* ACHTUNG: regulare Ausdruecke !! */
    'chmod(): Operation not permitted',
    'should not be called statically',
    'Use of undefined constant',
    'Constant _[A-Z][^ ]+ already defined', // doppelt deklarierte Sprachvariable
    'Cannot modify header information',
    // 'Assigning the return value of new',
    // 'Please use the public/private/protected modifiers',
    // 'assuming \$this from compatible context mxSession',
    // 'language/custom/lang',
    // 'POP3 authentication',
    );

/**
 * Fehlermeldungen aus bestimmten Dateien nicht beachten, dazu einfach
 * diese Datei als Array-Wert hier einfuegen.
 * ACHTUNG: die Dinger muessen natuerlich noch gefixt werden !!!
 */
$ignores['file'] = array(/* ACHTUNG: absolute Serverpfade !! */
    '/irgendwas/nochwas/includes/mx_api.php',
    'X:\\path_to_pragmamx\\modules\\irgendwas\\language\\lang-german.php',
    );

?>