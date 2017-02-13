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
 * $Revision: 175 $
 * $Author: PragmaMx $
 * $Date: 2016-06-30 14:38:26 +0200 (Do, 30. Jun 2016) $
 */

defined('mxMainFileLoaded') or die('access denied');

langdefine("_ADMINBANCONFIG", "Yasaklama Yönetimi");
langdefine("_AUTOBAN", "Mevcut olan kullanıcı hesaplarını otomatik olarak pasifleştir");
langdefine("_AUTOBAN_HEAD","Devre Dışı Bırak Kullanıcı Hesabı");
langdefine("_CUTWITHCOMMATA", "Kelimeleri virgül ile ayırt edin!");
langdefine("_INFOHOWBAN", "IP Yasakla");
langdefine("_INFOHOWBANHELP", "<ul>\n  <li>\n    Buradan kurallara uymayanları IP geçerlilik süresi içersinde yasaklayabilirsiniz. IP adresleri kullanıcın her yeni girişinde değişebilir.</li>\n  <li>\n    Şunu vurgulamak istiyoruz ki, yasaklamanın dikkatli olarak ele alınması ve sadece başarısız ihtarlar sonucu kullanılması.</li>\n  <li>\n	Yasaklanacak IP leri virgül &quot;,&quot; ile ayırt edin (Örnek: 127.0.0.1,192.168.0.1)</li>\n</ul>");
langdefine("_INFOHOWBANMAIL", "E-Posta adresi yasakla");
langdefine("_INFOHOWBANMAILHELP", "<ul>\n  <li>\n    Burada izinsiz e-posta adreslerini yasaklayabilirsiniz. Bu durumda bu E-Posta adresleri ile kayıt mümkün olmayacaktır.\n  </li>\n  <li>\n    E-Posta adresini iki seçenek yoluyla yasaklayabilirsiniz:<br />\n    1. Tam adresin girilmesiyle: (Örnek: adres@site.com)<br />\n    2. Alan adı girilmesiyle: (Örnek: @site.com)<br />\n    Tam adresin girilmesiyle sadece bu adres yasaklanır, Alan adı yasaklama girilmesiyle ise bu URL den gelen tüm adresler reddedilir.\n  </li>\n  <li>\n    Yasaklanacak E-Posta adreslerini virgül &quot;,&quot; ile ayırt edin (Örnek: adres@site.com,@site.com)</li>\n</ul>");
langdefine("_INFOHOWBANNAME", "Kullanıcı adı yasakla");
langdefine("_INFOHOWBANNAMEHELP", "<ul>\n  <li>\n    Burada izinsiz kullanıcı adlarını yasaklayabilirsiniz. Bu durumda bu kullanıcı adları ile kayıt mümkün olmayacaktır.\n  </li>\n  <li>\n    <strong>Dikkat:</strong> Eğer &quot;<em>Devre Dışı Bırak Kullanıcı Hesabı</em>&quot; seçeneğini kullanıyorsanız, sistemde mevcut olan kullanıcı hesapları otomatik olarak pasifleştirilecektir, bu durumda kullanıcı bu hesaba artık giriş yapamayacaktır!</li>\n  <li>\n    Yasaklanacak kullanıcı adları virgül &quot;,&quot; ile ayırt edin (Örnek: ali,hasan,sibel)</li>\n</ul>");
langdefine("_IPADDED", "Yasaklama dosyası güncellendi.");

?>