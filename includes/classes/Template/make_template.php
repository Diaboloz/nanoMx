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

function pmx_make_template ($path, $name, $object)
{
    /* die aktuellen Werte auslesen */
    $vars = get_object_vars($object);
    /* darin die vordefinierten Werte entfernen */
    foreach (get_class_vars(get_class($object)) as $key => $tmp) {
        unset($vars[$key]);
    }

    /* Speicherort bestimmen */
    $resource_name = rtrim($path, '\\/') . DS . basename($name);

    /* Name zum Anzeigen erstellen */
    $showedname = str_replace(DS, '/', trim(mx_strip_sysdirs($resource_name), '\\/'));

    /* die dem Template zugewiesenen Variablen auflisten */
    $values = 'no values';
    if ($vars) {
        $values = "\n<hr />\n";
        foreach ($vars as $key => $brauchmanett) {
            $values .= $key . " = <?php echo \$this->" . $key . " ?>\n<hr />\n";
        }
    }

    /* gesamten Dateiinhalt generieren */
    $template_source = '<div style="background-color: #FFFFE0; padding: 5px; border: 1px dotted Red; margin: 5px;">
      <h2 style="background-color: #FFFFE0; color: Red;">automatically generated template</h2>
      <p>path: ' . $showedname . '</p>
      <hr/>
      <h3>values:</h3>
      ' . $values . '
      </div>

      <?php // $' . 'Id' . ': ' . basename($name) . ',v 1.0 ' . strftime('%Y/%m/%d %H:%M:%S') . ' $ ?>
      ';

    if (!is_readable ($resource_name)) {
        if (!file_exists($path)) {
            mkdir($path, null, true);
        }
        /* erzeuge Template-Datei */
        return file_put_contents($resource_name, trim($template_source));
    }
}

?>