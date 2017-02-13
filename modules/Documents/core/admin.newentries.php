<?php
/**
 * This file is part of
 *
 * MailNews
 *
 * for pragmamx (www.pragmamx.org)
 *
 * $Version 1.0 $
 * $Author: PragmaMx $
 * $Date: 2015-07-08 09:07:06 +0200 (Mi, 08. Jul 2015) $
 */

$hook = function($module_name, $options, &$hook_result)
{
    /* $options enthält die kompletten Daten des aktuellen Admins */
    $bookmodule = $module_name;

    if ($options['radminsuper']) {
        $doc_cfg = array();
        $doc = load_class('Book', $bookmodule);
        $doc->module_name = $bookmodule;
        $count = count($doc->getRecords_AdminNews());

        if ($count > 0) {
            $hook_result[] = array(/* Eintrag */
                'module' => $bookmodule,
                'count' => $count,
                'link' => adminUrl($bookmodule),
                'text' => $bookmodule . " : " . _DOCS_NEW_DOCUMENTS);
        }
    }
} ;

?>