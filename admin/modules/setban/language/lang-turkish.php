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
 * $Date: 2015-07-08 09:07:06 +0200 (mer., 08 juil. 2015) $
 */

defined('mxMainFileLoaded') or die('access denied');

define("_ADMINBANCONFIG", "Yasaklama Yönetimi");
define("_AUTOBAN", "Mevcut olan kullanıcı hesaplarını otomatik olarak pasifleştir");
define("_CUTWITHCOMMATA", "Kelimeleri virgül ile ayırt edin!");
define("_INFOHOWBAN", "IP Yasakla");
define("_INFOHOWBANHELP", "<ul>\n  <li>\n    Buradan kurallara uymayanları IP geçerlilik süresi içersinde yasaklayabilirsiniz. IP adresleri kullanıcın her yeni girişinde değişebilir.</li>\n  <li>\n    Şunu vurgulamak istiyoruz ki, yasaklamanın dikkatli olarak ele alınması ve sadece başarısız ihtarlar sonucu kullanılması.</li>\n  <li>\n	Yasaklanacak IP leri virgül &quot;,&quot; ile ayırt edin (Örnek: 127.0.0.1,192.168.0.1)</li>\n</ul>");
define("_INFOHOWBANMAIL", "E-Posta adresi yasakla");
define("_INFOHOWBANMAILHELP", "<ul>\n  <li>\n    Burada izinsiz e-posta adreslerini yasaklayabilirsiniz. Bu durumda bu E-Posta adresleri ile kayıt mümkün olmayacaktır.\n  </li>\n  <li>\n    E-Posta adresini iki seçenek yoluyla yasaklayabilirsiniz:<br />\n    1. Tam adresin girilmesiyle: (Örnek: adres@site.com)<br />\n    2. Alan adı girilmesiyle: (Örnek: @site.com)<br />\n    Tam adresin girilmesiyle sadece bu adres yasaklanır, Alan adı yasaklama girilmesiyle ise bu URL den gelen tüm adresler reddedilir.\n  </li>\n  <li>\n    Yasaklanacak E-Posta adreslerini virgül &quot;,&quot; ile ayırt edin (Örnek: adres@site.com,@site.com)</li>\n</ul>");
define("_INFOHOWBANNAME", "Kullanıcı adı yasakla");
define("_INFOHOWBANNAMEHELP", "<ul>\n  <li>\n    Burada izinsiz kullanıcı adlarını yasaklayabilirsiniz. Bu durumda bu kullanıcı adları ile kayıt mümkün olmayacaktır.\n  </li>\n  <li>\n    <strong>Dikkat:</strong> Eğer &quot;<em>mevcut olan kullanıcı hesaplarını otomatik olarak pasifleştir</em>&quot; seçeneğini kullanıyorsanız, sistemde mevcut olan kullanıcı hesapları otomatik olarak pasifleştirilecektir, bu durumda kullanıcı bu hesaba artık giriş yapamayacaktır!</li>\n  <li>\n    Yasaklanacak kullanıcı adları virgül &quot;,&quot; ile ayırt edin (Örnek: ali,hasan,sibel)</li>\n</ul>");
define("_IPADDED", "Yasaklama dosyası güncellendi.");

?>