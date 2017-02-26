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
 * $Revision: 219 $
 * $Author: PragmaMx $
 * $Date: 2016-09-23 11:58:34 +0200 (Fr, 23. Sep 2016) $
 */

defined('mxMainFileLoaded') or die('access denied');

/**
 * definiert, dass diese Datei bereits includet wurde
 * nicht verändern!
 */
if (defined('PMX_FOOTER')) {
    return;
} else {
    define('PMX_FOOTER', true);
}

/* Ausgabe des Moduls etc. kann hier für Hook verwendet werden */
$content = ob_get_clean();

/* vorerst hierrüber mal schaltbar - im Modul muss dann 0(standard)/1 angegeben werden */
global $plugins;
if (MX_MODULE != "admin" && ($plugins == 1)) {
    $hook = load_class('Hook', 'prepare.content');
    $hook->run($content);
}

ob_start();
if ($content) {
    /* eindeutige Modul css-Id drumrum */
    echo '
    <div class="mod-' . MX_MODULE . '">
    ' . $content . '
    </div>';
}

/**
 * untere Centerblöcke anzeigen, direkt unter der Ausgabe des Moduls,
 * Bei vkpMx-Themes ist diese Ausgabe im Theme definiert
 */
if (defined('MX_HOME_FILE') && empty($GLOBALS['VKPTheme'])) {
    blocks('down');
}

/**
 * Funktion 'themefooter' aus Theme aufrufen.
 * Darin werden auch die rechten Blöcke angezeigt
 */
themefooter();

/**
 * zusätzliche Datei für benutzerdefinierte Zusätze am Ende der HTML-Ausgabe
 */
include_once(PMX_SYSTEM_DIR . DS . 'my_footer.php');

/* Cookiechoice integration */
	
if (pmxBase::mxCookieInfo() && (pmxBase::mxCookieInfo()=='1' && (MX_MODULE != "admin")) )
{
	if (!empty(pmxBase::mxCookieLink())){
		$cookieinfo = _COOKIEINFO ."','". _OK ."', '". _MOREINFO ."', '". pmxBase::mxCookieLink() ;
	} else {
		$cookieinfo = _COOKIEINFO ."','". _OK ;
	}
	 pmxHeader::add_style_code("#pmxChoiceInfo{".pmxBase::mxCookiePos().":0;}");
	 pmxHeader::add_script_code("document.addEventListener('DOMContentLoaded', function(event) {cookieChoices.showCookieConsentBar('".$cookieinfo."');});");	
		/* TODO: alternativ umschaltbar machen */
	 //echo"<script>document.addEventListener('DOMContentLoaded', function(event) {cookieChoices.showCookieConsentDialog('".$cookieinfo."');});</script>";		
	
	 echo "<script type=\"text/javascript\" src=\"".PMX_JAVASCRIPT_PATH."cookiechoices/cookiechoices.js\"></script>";
}

/* Debug-Info ausgeben */
echo mxDebugInfo();
	
/**
 * den Ausgabepuffer nachbehandeln
 * Der Ausgabepuffer wurde in mainfile.php explizit gestartet!
 * falls doch leer, ist z.B. nix mit mod_rewrite
 */
if (ob_get_length()) {
    /* die HTML-Ausgabe beenden */
    ?>

</body>
</html>
<?php
    // $xx = memory_get_usage(true) /1024  /1024 ;
    // mxDebugFuncVars($xx);
    $mxoutput = trim(ob_get_clean());
    // Das Array mit den versch. Doc-Type Deklarationen auslesen
    $doctype_arr = mxDoctypeArray($GLOBALS['DOCTYPE']);

    /* Lightbox aktivieren */
    if (preg_match('#rel\s*=\s*["\']pretty(Photo)?(\[[^\]]+\])?["\']#', $mxoutput)) {
        pmxHeader::add_lightbox();
    }

	/* eventuell fehlende URL bei media-Links eintragen */
	$mxoutput = str_replace('href="http://media', 'href="'.MX_BASE_URL.'media', $mxoutput);

	
    /* versch. Attribute korrigieren, die durch glob. Stylesheet auf 0 gesetzt werden */
    if (preg_match_all('#(hspace|vspace|cellspacing|cellpadding)\s*=\s*["\']([1-9](?:[0-9]{1,})?)#', $mxoutput, $matches)) {
        $out = array();
        foreach ($matches[2] as $key => $value) {
            switch ($matches[1][$key]) {
                case 'hspace':
                    $out[$matches[0][$key]] = '*[hspace="' . $value . '"]{margin-left:' . $value . 'px;margin-right:' . $value . 'px;}';
                    break;
                case 'vspace':
                    $out[$matches[0][$key]] = '*[vspace="' . $value . '"]{margin-bottom:' . $value . 'px;margin-top:' . $value . 'px;}';
                    break;
                case 'cellspacing':
                    $out[$matches[0][$key]] = 'table[cellspacing="' . $value . '"]{border-collapse: separate;border-spacing: ' . $value . 'px;}';
                    break;
                case 'cellpadding':
                    $out[$matches[0][$key]] = 'table[cellpadding="' . $value . '"] > tbody > tr > td,table[cellpadding="' . $value . '"] > tbody > tr > th {padding:' . $value . 'px;}';
                    break;
            }
        }
        pmxHeader::add_style_code(str_replace(' ', '', implode('', $out)));
    }

    /* zusätzliche headertags vorher noch an die korrekte Stelle setzen */
    $mxoutput = pmxHeader::move($mxoutput);

    /* HTML-Validation */
    if ($GLOBALS['TidyOutput'] && is_tidy_available()) {
        header('X-Tidy: Yes');
        // Specify configuration: http://tidy.sourceforge.net/docs/quickref.html
        $tidyconfig = array(/* Tidy-Konfiguration */
            'output-html' => $doctype_arr['html'],
            'output-xhtml' => $doctype_arr['xhtml'],
            'add-xml-decl' => $doctype_arr['xhtml'],
            'doctype' => $doctype_arr['tidy_doctype'],
            'wrap' => 0,
            'indent' => false,

            'output-bom' => false,
            'char-encoding' => 'utf8',
            // 'input-encoding' => 'utf8',
            // 'output-encoding' => 'utf8',
            'ascii-chars' => false,
            // 'indent-spaces' => 1,
            // 'clean'   => true,
            // 'alt-text' => 'img',
            );
        $tidy = new tidy;
        $tidy->parseString($mxoutput, $tidyconfig, 'utf8');
        $tidy->cleanRepair();
        $mxoutput = $tidy;
        // die tidy-Funktion fügt vor </textara> einen Zeilenumbruch ein, diesen hier entfernen
        $mxoutput = preg_replace('#\s*</textarea>#', '</textarea>', $mxoutput);
        unset($tidy);
    } else {
        if ($doctype_arr['xhtml']) {
            // versuchen, die xhtml-Endung anzufügen
            $mxoutput = preg_replace('#<((?:img|input|hr|br|link|meta|base)(?:[^>]*[^/])?)>#i', '<$1 />', $mxoutput);
            // bei <img> muss ein ALT Attribut spezifiert sein, hier versuchen die anzufügen, falls es fehlt
            
            $mxoutput = preg_replace_callback('#(<img[^>]*)/>#i', function($match)
						{
							if (preg_match("#\salt\s*=#i", $match[1], $mmmmm)) {
								return $match[0];
							} else {
								return $match[1] . ' alt="" />';
							}
						}, $mxoutput);
        } else {
            // die xhtml-Endung < /> entfernen, da Fehler in HTML 4.01
            $mxoutput = preg_replace('#(<[[:alnum:]]+[^>]*)/>#', '$1>', $mxoutput);
            // bei <img> muss ein ALT Attribut spezifiert sein, hier versuchen die anzufügen, falls es fehlt
            
            $mxoutput = preg_replace_callback('#(<img[^>]*)>#i', function($match)
							{
								if (preg_match("#\salt\s*=#i", $match[1], $mmmmm)) {
									return $match[0];
								} else {
									return $match[1] . ' alt="">';
								}
							} , $mxoutput);
        }
        // Link auf ungültige Verweise entfernen, kommt evtl. von Spaw, etc.
        $mxoutput = preg_replace('!\s+href="#?"!i', '', $mxoutput);
    }
    /* ENDE HTML-Validation */
	


    /* mod_rewrite, Ersetzen der Links in ein suchmaschinenfreundliches Format. */
    $seo = load_class('Config', 'pmx.seo');
    if ($mxoutput && array_sum((array)$seo->modrewrite)) {
        load_class('Modrewrite', false);
        $mxoutput = pmxModrewrite::prepare($mxoutput);
    }

    /* Hook um den Content der ganzen Seite modular zu beeinflussen */
    /* z.B. private Nachrichten abfragen */
    $hook = load_class('Hook', 'prepare.page');
    $hook->run($mxoutput);

    /* zusätzliche optionale Datei um den Ausgabepuffer zu manipulieren */
    if (file_exists(PMX_SYSTEM_DIR . DS . 'prepareoutput.php')) {
        include(PMX_SYSTEM_DIR . DS . 'prepareoutput.php');
    }

    /* den html-Output (Ausgabepuffer) ausgeben */
    echo $mxoutput;

    /* Speicher aufräumen */
    unset($mxoutput, $matches, $allstyles);
} else {
    /* falls kein Ausgabepuffer vorhanden, die zusätzlichen Header-Tags zumindest noch ans Ende schreiben... */
    echo pmxHeader::get();
    /* das Ende, der HTML-Ausgabe */
    ?>

</body>
</html>
<?php }

?>