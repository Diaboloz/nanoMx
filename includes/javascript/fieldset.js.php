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

$fieldset_names = @array(/* Überschriften */
    'secopt' => _SECOPT,
    'mailsettings' => _MAILSETTINGS,
    'gensiteinfo' => _GENSITEINFO,
    'graphicopt' => _GRAPHICOPT,
    'newsmodule' => _NEWSMODULE,
    'footermsg' => _FOOTERMSG,
    'miscopt' => _MISCOPT,
    'commentsopt' => _COMMENTSOPT,
    'censoroptions' => _CENSOROPTIONS,
    'htmlopt' => _HTMLOPT,
    'setservice' => _SETSERVICE,
    'dbsettings' => _DBSETTINGS,
    );

/* folgendes nur ausführen, wenn die Datei includet wurde */
if (defined('mxMainFileLoaded')) {
    return;
}

header('Content-Type: text/javascript; charset=UTF-8');
header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 3600 * 24) . ' GMT'); // 1 day
header('X-Powered-By: pragmaMx-cms');

?>
/* jQuery events ausfuehren */
$(document).ready(function() {

    $("button.fieldset-exp_all").click(function () {
        $("fieldset div.toggle").show();
        $("fieldset span.collapsible").addClass("expanded");
        $("fieldset").removeClass("closed");
    });

    $("button.fieldset-cls_all").click(function () {
        $("fieldset div.toggle").hide();
        $("fieldset span.collapsible").removeClass("expanded");
        $("fieldset span.collapsible").addClass("collapsed");
        $("fieldset").addClass("closed");
    });

    <?php foreach($fieldset_names as $key => $var) :?>

    $("fieldset.<?php echo $key ?> legend").click(function () {
        $("fieldset.<?php echo $key ?> div.toggle").slideToggle("normal");
        $("fieldset.<?php echo $key ?> span.collapsible").toggleClass("expanded");
        $("fieldset.<?php echo $key ?>").toggleClass("closed");
    });

    <?php endforeach ?>

    // alle zuklappen
    $("button.fieldset-cls_all").click();

    // und die Buttons anzeigen
    $("button.fieldset-exp_all").toggleClass("show");
    $("button.fieldset-cls_all").toggleClass("show");
});

