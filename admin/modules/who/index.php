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
 * Copyright (c) 2001 by Jack Kozbial
 * (jack@internetintl.com) http://www.internetintl.com
 */

defined('mxMainFileLoaded') or die('access denied');

/* Sprachdatei auswählen */
mxGetLangfile(__DIR__);

include("header.php");

$past = time() - (MX_SETINACTIVE_MINS * 2) ;

$qry1 = "SELECT user_lastvisit, user_lastip, user_lastmod, user_lasturl, uname FROM {$user_prefix}_users
WHERE (((user_lastmod)<>'logout') AND ((user_lastvisit) >= " . $past . ") AND ((user_stat)=1));";

$qry2 = "SELECT time, ip, module, url FROM ${prefix}_visitors WHERE (time >= " . $past . ") AND uid = 0;";
$result1 = sql_system_query ($qry1);
$result2 = sql_system_query ($qry2);
$guest_online_count = 0;
$member_online_count = 0;
$allrows = array();
$class = '';

while (list($time, $ip, $module, $url, $tuname) = sql_fetch_row($result1)) {
    $username = mxCreateUserprofileLink($tuname);
    $member_online_count++;
    $host = gethostbyaddr($ip);
    $country = gettopdomain($host);
    $stime = gettimeonline($time);
    $url = mx_urltohtml(trim($url, '/. '));
    $where = "<a href=\"" . $url . "\" title=\"" . $url . "\" target=\"blank\">" . $module . "</a>";
    $key = $time + $member_online_count;
    $class = ($class == '') ? ' class="alternate-a"' : '';
    $allrows[$key] = '
        <tr' . $class . '>
          <td>' . $username . '</td>
          <td>' . $ip . '</td>
          <td>' . $host . '</td>
          <td>' . $country . '</td>
          <td>' . $stime . '</td>
          <td>' . $where . '</td>
        </tr>';
} // End while
while (list($time, $ip, $module, $url) = sql_fetch_row($result2)) {
    $guest_online_count++;
    $host = gethostbyaddr($ip);
    $country = gettopdomain($host);
    $stime = gettimeonline($time);
    $url = mx_urltohtml(trim($url, '/. '));
    $where = "<a href=\"" . $url . "\" title=\"" . $url . "\" target=\"blank\">" . $module . "</a>";
    $key = $time + $member_online_count;
    $class = ($class == '') ? ' class="alternate-a"' : '';
    $allrows[$key] = '
        <tr' . $class . '>
          <td>&nbsp;</td>
          <td>' . $ip . '</td>
          <td>' . $host . '</td>
          <td>' . $country . '</td>
          <td>' . $stime . '</td>
          <td>' . $where . '</td>
        </tr>';
} // End while
title(_WHONCAPT);
OpenTable();
if (count($allrows)) {
    krsort($allrows);
    $allrows = implode("\n", $allrows);
    echo '
    <table class="full list">
      <thead>
        <tr>
          <th>' . _WHONUSER . '</th>
          <th>' . _WHONHOST1 . '</th>
          <th>' . _WHONHOST2 . '</th>
          <th>' . _WHONFROM . '</th>
          <th>' . _WHONTIME . '</th>
          <th>' . _WHONWHERE . ' ?</th>
        </tr>
      </thead>
      <tbody>';
    if ($allrows) {
        echo $allrows;
    } else {
        echo '
        <tr><td colspan="6"></td></tr>';
    }
    echo '
      </tbody>
    </table>';
}

echo '<p class="align-center">' . _WHOCURRENTLY . '&nbsp;<b>' . $guest_online_count . '</b>&nbsp;' . (($guest_online_count == 1) ? _WHOGUEST : _WHOGUESTS) . '&nbsp;<b>' . $member_online_count . '</b>&nbsp;' . (($member_online_count == 1) ? _WHOMEMBER : _WHOMEMBERS) . '</p>';
echo '<p class="align-center">' . _WHONSERVTIM . ': <b>' . mx_strftime(_DATESTRING . ' %H:%M:%S', time()) . '</b></p>';
echo '<p class="align-center">[&nbsp;<a href="' . adminUrl(PMX_MODULE) . '">' . _WHONREFRESH . '</a>&nbsp;]</p>';
CloseTable();
include('footer.php');

function gettimeonline($time)
{
    /**
     */
    /* Copyright (c) 2001 by Jack Kozbial                                               */
    /* Time online ( time format )                                                      */
    /* (jack@internetintl.com) http://www.internetintl.com                              */
    /*                                                                                  */
    /* This program is free software. You can redistribute it and/or modify             */
    /* it under the terms of the GNU General Public License as published by             */
    /* the Free Software Foundation; either version 2 of the License.                   */
    /*                                                                                  */
    /**
     */
    $min = 0;
    $sec = 0;
    $hour = 0;
    $unixtime = time() - $time;
    if ($unixtime < 60) {
        $sec = $unixtime;
        $min = 0;
        $hour = 0;
    } else if ($unixtime < 3600) {
        $sec = $unixtime % 60;
        $hour = 0;
        $min_t = explode('.', number_format($unixtime / 60, 2));
        $min = $min_t[0];
    } else if ($unixtime >= 216000) {
        $hour_t = explode('.', number_format($unixtime / 216000, 2));
        $hour = $hour_t[0];
        $sec = $unixtime % 60;
        $min_te = $unixtime % 216000;
        $min_t = explode('.', number_format($min_te / 60, 2));
        $min = $min_t[0];
    }
    return "$min min : $sec sec";
}

function gettopdomain($host)
{
    $array = explode(".", $host);
    $top_domain = strtolower($array[sizeof($array)-1]);

    switch ($top_domain) {
        case 'ac': return "Ascension Island";
        case 'ad': return "Andorra";
        case 'ae': return "United Arab Emirates";
        case 'af': return "Afghanistan";
        case 'ag': return "Antigua and Barbuda";
        case 'ai': return "Anguilla";
        case 'al': return "Albania";
        case 'am': return "Armenia";
        case 'an': return "Netherlands Antilles";
        case 'ao': return "Angola";
        case 'aq': return "Antarctica";
        case 'ar': return "Argentina";
        case 'as': return "American Samoa";
        case 'at': return "Austria";
        case 'au': return "Australia";
        case 'aw': return "Aruba";
        case 'ax': return "Åland";
        case 'az': return "Azerbaijan";
        case 'ba': return "Bosnia and Herzegovina";
        case 'bb': return "Barbados";
        case 'bd': return "Bangladesh";
        case 'be': return "Belgium";
        case 'bf': return "Burkina Faso";
        case 'bg': return "Bulgaria";
        case 'bh': return "Bahrain";
        case 'bi': return "Burundi";
        case 'bj': return "Benin";
        case 'bm': return "Bermuda";
        case 'bn': return "Brunei";
        case 'bo': return "Bolivia";
        case 'br': return "Brazil";
        case 'bs': return "Bahamas";
        case 'bt': return "Bhutan";
        case 'bv': return "Bouvet Island";
        case 'bw': return "Botswana";
        case 'by': return "Belarus";
        case 'bz': return "Belize";
        case 'ca': return "Canada";
        case 'cc': return "Cocos (Keeling) Islands";
        case 'cd': return "Democratic Republic of the Congo";
        case 'cf': return "Central African Republic";
        case 'cg': return "Republic of the Congo";
        case 'ch': return "Switzerland";
        case 'ci': return "Côte d'Ivoire";
        case 'ck': return "Cook Islands";
        case 'cl': return "Chile";
        case 'cm': return "Cameroon";
        case 'cn': return "People's Republic of China";
        case 'co': return "Colombia";
        case 'cr': return "Costa Rica";
        case 'cs': return "Czechoslovakia";
        case 'cu': return "Cuba";
        case 'cv': return "Cape Verde";
        case 'cx': return "Christmas Island";
        case 'cy': return "Cyprus";
        case 'cz': return "Czech Republic";
        case 'dd': return "East Germany";
        case 'de': return "Germany";
        case 'dj': return "Djibouti";
        case 'dk': return "Denmark";
        case 'dm': return "Dominica";
        case 'do': return "Dominican Republic";
        case 'dz': return "Algeria";
        case 'ec': return "Ecuador";
        case 'ee': return "Estonia";
        case 'eg': return "Egypt";
        case 'eh': return "Western Sahara";
        case 'er': return "Eritrea";
        case 'es': return "Spain";
        case 'et': return "Ethiopia";
        case 'eu': return "European Union";
        case 'fi': return "Finland";
        case 'fj': return "Fiji";
        case 'fk': return "Falkland Islands";
        case 'fm': return "Federated States of Micronesia";
        case 'fo': return "Faroe Islands";
        case 'fr': return "France";
        case 'fx': return "France (European Territory)";
        case 'ga': return "Gabon";
        case 'gb': return "United Kingdom";
        case 'gd': return "Grenada";
        case 'ge': return "Georgia";
        case 'gf': return "French Guiana";
        case 'gg': return "Guernsey";
        case 'gh': return "Ghana";
        case 'gi': return "Gibraltar";
        case 'gl': return "Greenland";
        case 'gm': return "The Gambia";
        case 'gn': return "Guinea";
        case 'gp': return "Guadeloupe";
        case 'gq': return "Equatorial Guinea";
        case 'gr': return "Greece";
        case 'gs': return "South Georgia and the South Sandwich Islands";
        case 'gt': return "Guatemala";
        case 'gu': return "Guam";
        case 'gw': return "Guinea-Bissau";
        case 'gy': return "Guyana";
        case 'hk': return "Hong Kong";
        case 'hm': return "Heard Island and McDonald Islands";
        case 'hn': return "Honduras";
        case 'hr': return "Croatia";
        case 'ht': return "Haiti";
        case 'hu': return "Hungary";
        case 'id': return "Indonesia";
        case 'ie': return "Ireland";
        case 'il': return "Israel";
        case 'im': return "Isle of Man";
        case 'in': return "India";
        case 'io': return "British Indian Ocean Territory";
        case 'iq': return "Iraq";
        case 'ir': return "Iran";
        case 'is': return "Iceland";
        case 'it': return "Italy";
        case 'je': return "Jersey";
        case 'jm': return "Jamaica";
        case 'jo': return "Jordan";
        case 'jp': return "Japan";
        case 'ke': return "Kenya";
        case 'kg': return "Kyrgyzstan";
        case 'kh': return "Cambodia";
        case 'ki': return "Kiribati";
        case 'km': return "Comoros";
        case 'kn': return "Saint Kitts and Nevis";
        case 'kp': return "Democratic People's Republic of Korea";
        case 'kr': return "Republic of Korea";
        case 'kw': return "Kuwait";
        case 'ky': return "Cayman Islands";
        case 'kz': return "Kazakhstan";
        case 'la': return "Laos";
        case 'lb': return "Lebanon";
        case 'lc': return "Saint Lucia";
        case 'li': return "Liechtenstein";
        case 'lk': return "Sri Lanka";
        case 'lr': return "Liberia";
        case 'ls': return "Lesotho";
        case 'lt': return "Lithuania";
        case 'lu': return "Luxembourg";
        case 'lv': return "Latvia";
        case 'ly': return "Libya";
        case 'ma': return "Morocco";
        case 'mc': return "Monaco";
        case 'md': return "Moldova";
        case 'me': return "Montenegro";
        case 'mg': return "Madagascar";
        case 'mh': return "Marshall Islands";
        case 'mk': return "Macedonia";
        case 'ml': return "Mali";
        case 'mm': return "Myanmar";
        case 'mn': return "Mongolia";
        case 'mo': return "Macau";
        case 'mp': return "Northern Mariana Islands";
        case 'mq': return "Martinique";
        case 'mr': return "Mauritania";
        case 'ms': return "Montserrat";
        case 'mt': return "Malta";
        case 'mu': return "Mauritius";
        case 'mv': return "Maldives";
        case 'mw': return "Malawi";
        case 'mx': return "Mexico";
        case 'my': return "Malaysia";
        case 'mz': return "Mozambique";
        case 'na': return "Namibia";
        case 'nc': return "New Caledonia";
        case 'ne': return "Niger";
        case 'nf': return "Norfolk Island";
        case 'ng': return "Nigeria";
        case 'ni': return "Nicaragua";
        case 'nl': return "Netherlands";
        case 'no': return "Norway";
        case 'np': return "Nepal";
        case 'nr': return "Nauru";
        case 'nt': return "Saudiarab. Irak)";
        case 'nu': return "Niue";
        case 'nz': return "New Zealand";
        case 'om': return "Oman";
        case 'pa': return "Panama";
        case 'pe': return "Peru";
        case 'pf': return "French Polynesia";
        case 'pg': return "Papua New Guinea";
        case 'ph': return "Philippines";
        case 'pk': return "Pakistan";
        case 'pl': return "Poland";
        case 'pm': return "Saint-Pierre and Miquelon";
        case 'pn': return "Pitcairn Islands";
        case 'pr': return "Puerto Rico";
        case 'ps': return "State of Palestine[17]";
        case 'pt': return "Portugal";
        case 'pw': return "Palau";
        case 'py': return "Paraguay";
        case 'qa': return "Qatar";
        case 're': return "Réunion";
        case 'ro': return "Romania";
        case 'rs': return "Serbia";
        case 'ru': return "Russia";
        case 'rw': return "Rwanda";
        case 'sa': return "Saudi Arabia";
        case 'sb': return "Solomon Islands";
        case 'sc': return "Seychelles";
        case 'sd': return "Sudan";
        case 'se': return "Sweden";
        case 'sg': return "Singapore";
        case 'sh': return "Saint Helena";
        case 'si': return "Slovenia";
        case 'sj': return "Svalbard and Jan Mayen Islands";
        case 'sk': return "Slovakia";
        case 'sl': return "Sierra Leone";
        case 'sm': return "San Marino";
        case 'sn': return "Senegal";
        case 'so': return "Somalia";
        case 'sr': return "Suriname";
        case 'ss': return "South Sudan";
        case 'st': return "São Tomé and Príncipe";
        case 'su': return "Soviet Union";
        case 'sv': return "El Salvador";
        case 'sx': return "Sint Maarten";
        case 'sy': return "Syria";
        case 'sz': return "Swaziland";
        case 'tc': return "Turks and Caicos Islands";
        case 'td': return "Chad";
        case 'tf': return "French Southern and Antarctic Lands";
        case 'tg': return "Togo";
        case 'th': return "Thailand";
        case 'tj': return "Tajikistan";
        case 'tk': return "Tokelau";
        case 'tl': return "East Timor";
        case 'tm': return "Turkmenistan";
        case 'tn': return "Tunisia";
        case 'to': return "Tonga";
        case 'tp': return "East Timor";
        case 'tr': return "Turkey";
        case 'tt': return "Trinidad and Tobago";
        case 'tv': return "Tuvalu";
        case 'tw': return "Taiwan";
        case 'tz': return "Tanzania";
        case 'ua': return "Ukraine";
        case 'ug': return "Uganda";
        case 'uk': return "United Kingdom";
        case 'um': return "US Minor outlying Islands";
        case 'us': return "United States of America";
        case 'uy': return "Uruguay";
        case 'uz': return "Uzbekistan";
        case 'va': return "Vatican City";
        case 'vc': return "Saint Vincent and the Grenadines";
        case 've': return "Venezuela";
        case 'vg': return "British Virgin Islands";
        case 'vi': return "United States Virgin Islands";
        case 'vn': return "Vietnam";
        case 'vu': return "Vanuatu";
        case 'wf': return "Wallis and Futuna";
        case 'ws': return "Samoa";
        case 'ye': return "Yemen";
        case 'yt': return "Mayotte";
        case 'yu': return "Yugoslavia";
        case 'za': return "South Africa";
        case 'zm': return "Zambia";
        case 'zr': return "Zaire";
        case 'zw': return "Zimbabwe";

        case 'aero': return "aeronautics";
        case 'arpa': return "ARPANet/USA";
        case 'asia': return "asia";
        case 'biz': return "Business";
        case 'cat': return "catalan";
        case 'com': return "Commercial";
        case 'coop': return "cooperatives";
        case 'edu': return "Education";
        case 'gov': return "Government/USA";
        case 'home': return "Home-Server";
        case 'info': return "Info";
        case 'int': return "Oganization established by an Iinternational Teaty";
        case 'jobs': return "jobs";
        case 'mil': return "Military/USA";
        case 'mobi': return "mobile";
        case 'museum': return "museums";
        case 'name': return "Private Name";
        case 'net': return "Network";
        case 'org': return "Organization/USA";
        case 'post': return "postal";
        case 'pro': return "professionals";
        case 'tel': return "telecommunication";
        case 'travel': return "travel";
        case 'xxx': return "sex";
    }

    switch (true) {
        case is_numeric($host):
            /* host is an ip-address */
            return "Not a domain.";
        case strstr($host, "aol"):
            return "America Online";
        default:
            /* domain not listed above */
            return "Unknown domain.";
    }
}

?>