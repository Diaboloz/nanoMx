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
 * bestimmte Fehlermeldungen extra dokumentieren, dazu einfach einen Teilstring
 * der entsprechenden Fehlermeldung als Array-Wert hier einfuegen.
 * ACHTUNG: die Dinger muessen natuerlich noch gefixt werden !!!
 */
$reminders['text'] = array (/* ACHTUNG: regulare Ausdruecke !! */
    // 'Assigning the return value of new',
    // 'Please use the public/private/protected modifiers',
    // 'assuming \$this from compatible context mxSession',
    'should not be called statically',
    'Use of undefined constant',
    // 'language/custom/lang',
    'Constant _[A-Z][^ ]+ already defined', // doppelt deklarierte Sprachvariable
    'Cannot modify header information',
    // 'POP3 authentication',
    );

/**
 * Fehlermeldungen aus bestimmten Dateien extra dokumentieren, dazu einfach
 * diese Datei als Array-Wert hier einfuegen.
 * ACHTUNG: die Dinger muessen natuerlich noch gefixt werden !!!
 */
$reminders['file'] = array(/* ACHTUNG: absolute Serverpfade !! */
    '/irgendwas/nochwas/includes/mx_api.php',
    'X:\\path_to_pragmamx\\modules\\irgendwas\\language\\lang-german.php',
    );

?>