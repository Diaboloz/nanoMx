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
 *
 * Diese Datei dient nur noch der Abwärtskompatibilität !!
 */

/**
 * Alle in dem uebergebenen String enthaltenen Links umschreiben
 *
 * deprecated!!
 */
function mxPrepareModRewrite($content)
{
    trigger_error('Use of deprecated function mxPrepareModRewrite().', E_USER_NOTICE);

    /* wenn kein Content, raus hier */
    if (!$content) {
        return $content;
    }

    load_class('Modrewrite', false);
    $content = pmxModrewrite::prepare($content);

    return $content;
}

/**
 * Die per umgeschriebener URL dem Script uebergebene Request Parameter
 * wieder in eine verwendbare Form bringen
 *
 * deprecated!!
 */
function mxUndoModRewrite()
{
    trigger_error('Use of deprecated function mxUndoModRewrite().', E_USER_NOTICE);

    if (defined('PMXMODREWRITE')) {
        load_class('Modrewrite', false);
        pmxModrewrite::undo();
    }
}

?>