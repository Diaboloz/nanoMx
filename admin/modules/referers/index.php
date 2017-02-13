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
 *
 */

defined('mxMainFileLoaded') or die('access denied');

/* Sprachdatei auswählen */
mxGetLangfile(dirname(__FILE__));

if (!mxGetAdminPref('radminsuper')) {
    mxErrorScreen("Access Denied");
    die();
}

function hreferer()
{
    global $prefix;
    // referer die unerwünscht sind, ignorieren
    if (@file_exists('admin/.ignore_referer')) {
        $ignore = file_get_contents('admin/.ignore_referer');
        $ignore = preg_split('#\s*,\s*#m', trim($ignore));
    }

    if (isset($_GET['URL'])) {
        $sort = "UCASE(url)";
    } else {
        $sort = "cnt DESC, UCASE(url)";
    }

    $hresult = sql_system_query("select count(rid) as cnt, url from " . $prefix . "_referer group by UCASE(url) order by " . $sort . ";");
    include ("header.php");
    title(_HTTPREFERERS);

    echo '
    <p style="text-align:center"><strong>' . _WHOLINKS . '</strong></p>
    <table class="full list">
      <thead>
        <tr>
          <th><a href="' . adminUrl(PMX_MODULE, '', 'count') . '">#</a></th>
          <th><a href="' . adminUrl(PMX_MODULE, '', 'URL') . '">URL</a></th>
        </tr>
      </thead>
      <tbody>';
    $out = '';
    while (list($rid, $url) = sql_fetch_row($hresult)) {
        $ref = parse_url($url);
        if (isset($ignore) && (in_array($url, $ignore) || in_array($ref['host'], $ignore))) {
            sql_system_query("DELETE from " . $prefix . "_referer WHERE url='" . mxAddSlashesForSQL(mx_urltohtml($url)) . "'");
            continue;
        }
        $url = mx_urltohtml(strip_tags($url));
        $out .= '
        <tr>
          <td>' . $rid . '</td>
          <td><a target="_blank" href="' . $url . '">' . mxCutString($url, 100) . '</a></td>
        </tr>';
    }
    if ($out) {
        echo $out;
    } else {
        echo '
        <tr><td colspan="2"></td></tr>';
    }
    echo '
      </tbody>
    </table>
    <form action="' . adminUrl(PMX_MODULE, 'delete') . '" method="post" id="ereffs">
      <input type="hidden" name="op" value="' . PMX_MODULE . '/delete" />
      <div class="align-center"><input type="submit" value="' . _DELETEREFERERS . '" /></div>
    </form>';
?>

<script type="text/javascript">
/*<![CDATA[*/

$(document).ready(function() {

  $('form#ereffs').submit(function() {
    return confirm("<?php echo _DELETEREFERERS ?> ??");
  });

  $('table.list tbody tr:even').addClass('alternate');

});

/*]]>*/
</script>

<?php
    include('footer.php');
}

function delreferer()
{
    global $prefix;
    sql_query("TRUNCATE TABLE " . $prefix . "_referer");
    mxRedirect(adminUrl(PMX_MODULE));
}

switch ($op) {
    case PMX_MODULE . '/delete':
        delreferer();
        break;
    default:
        hreferer();
        break;
}

?>