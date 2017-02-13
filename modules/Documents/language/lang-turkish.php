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
 * Author: Olaf Herfurth / TerraProject  http://www.tecmu.de
 *
 * $Revision: 6 $
 * $Author: PragmaMx $
 * $Date: 2015-07-08 09:07:06 +0200 (Mi, 08. Jul 2015) $
 *
 */

defined('mxMainFileLoaded') or die('access denied');


define("_DOCUMENTS_TITLE","Belgeler");

/* books */

define("_DOCU","Belge");
// define("_DOCS_CREATED","oluşturuldu");
define("_DOCS_CHANGED","değiştirildi");
define("_DOCS_PAGECOUNT","Miktar/Sayfa");
define("_DOCS_VIEWCONTENT","İçerikleri göster");
define("_DOCS_MOVEUP","ileri");
define("_DOCS_MOVEDN","geri");
define("_DOCS_POSITION","Pozisyon");
define("_DOCS_PUBLISH","Onaylama");
define("_DOCS_ACCESS","Erişim");
define("_DOCS_OWNER","Sahibi");
define("_DOCS_PUBLISHED","onayla");
define("_DOCS_UNPUBLISHED","kilitle");
define("_DOCS_EDIT","Belgeyi düzenleyin");
define("_DOCS_NEW","Yeni belge");

define("_DOCS_PAGE_NEW","Yeni");
define("_DOCS_PAGE_EDIT","Düzenle");

define("_DOCS_SECTION","Alan");
define("_DOCS_EDIT_TEXT","Burada belgede bilgileri düzenleyebilirsiniz");
define("_DOCS_INFO","Belge ile ilgili bilgileri belirtin. Bu bilgiler görüntülenmez.");
define("_DOCS_ALIAS","takma ad");
define("_DOCS_ALIAS_TEXT","Bir 'takma ad'-ismi (isteğe bağlı) belirtin");
define("_DOCS_KEYWORDS","Anahtar kelimeler");
define("_DOCS_KEYWORDS_TEXT","Metin girmek için burayı virgülle ayırarak Anahtar kelimeleri belirtin. Arama daha kolay yapılır böylece.");
define("_DOCS_NEW_BOOK","Yeni Belge");
define("_DOCS_NEW_CONTENT","Yeni Makale");
define("_DOCS_MOVECONTENT","Seçilen makaleler seçili makaleye taşınır. Ayrıca yine bazı makaleleri kaldırabilirsiniz. <br /> DİKKAT: muhtemelen ikincil makaleler taşınır!");

define("_DOCS_CHILDS","dahil olan makaleler");
define("_DOCS_CONTENTDELETEINFO","Seçilen makaleler olası ekleri ile birlikte silinir! Herhangi alt makaleler ana makaleye taşınacaktır");
define("_DOCS_DELETEINFO","Seçilen belgeler, aynı şekilde içerdiği tüm makaleler ve ekler silinir! Ayrıca belgeleri yine bireysel olarak kaldırabilirsiniz. ");
define("_DOCS_INDEX","İçerik");
define("_DOCS_TITLE","Başlık");
define("_DOCS_TITLE_TEXT","Burada, belgenin başlığını girin.");
define("_DOCS_LANGUAGE","Bir dil seçin");
define("_DOCS_LANGUAGE_TEXT","Burada, belgenin görüntülenmesi gereken dili seçiniz. TÜM seçerseniz, her zaman görüntülenir.");
define("_DOCS_PREAMBLE","Belgenin önsözü");
define("_DOCS_PREAMBLE_TEXT","Belgenin önsözü ana sayfasında görünecektir. Boş bırakılırsa, belgenin özeti kullanılır.");

define("_DOCS_COPYRIGHT","Telif hakkı");
define("_DOCS_COPYRIGHT_TEXT","Burada, muhtemelen telif haklarını giriniz");
define("_DOCS_SHORTDESC","Özet");
define("_DOCS_SHORTDESC_TEXT","Belgenin özeti belgenin ana sayfasında görünür, modülün ana sayfasında değil.");

define("_DOCS_USERGROUP","Erişim");
define("_DOCS_USERGROUP_TEXT","Belgeye erişim için bir kullanıcı grubu seçiniz");

/* content*/
define("_DOCS_CONTENT_EDIT","İçeriği düzenleyin");
define("_DOCS_CONTENT_TITLE","İçerik başlığı");
define("_DOCS_CONTENT_TITLE_TEXT","Burada, içeriğin başlığını girin");

/* Search */
define("_DOCS_SEARCH","İçerik arama");
define("_DOCS_SEARCH_RESULTS_TEXT","Aşağıdaki sayfalar kendi arama desenleriniz ile bulundu");
define("_DOCS_SEARCH_NORESULTS_TEXT","Arama parametreleriyle eşleşen sonuç yok.");
define("_DOCS_SEARCHMASK","Arama deseni");
define("_DOCS_SEARCHINFO","Burada arama kelimelerini virgülle ayırarak girin ");

/*config*/
define("_DOCS_CONFIG","Yapılandırma");
define("_DOCS_CONF_RIGHTBLOCKS","Sağ blokları göster");

define("_DOCS_CONF_TITLE","Modül başlığı");
define("_DOCS_CONF_TITLE_TEXT","Burada bir modül başlığı belirtin. ");

define("_DOCS_CONF_STARTPAGE","Yapılandırma modülü");
define("_DOCS_CONF_STARTPAGE_TEXT","");

define("_DOCS_CONF_LOGGING","Değişiklikler kaydedilsin");
define("_DOCS_CONF_LOGGING_TEXT","Etkinleştirildiğinde, tarih ve kullanıcıların belgelerde yapılan değişiklikleri günlük tabloda yazılır.");

define("_DOCS_CONF_BLOGPAGE","Yapılandırma bloğu");
define("_DOCS_CONF_BLOGPAGE_TEXT","");
define("_DOCS_CONF_INDEXPAGE","Yapılandırma kategori görünümü");
define("_DOCS_CONF_INDEXPAGE_TEXT","");

define("_DOCS_CONF_RIGHTS","Yapılandırma hakları");
define("_DOCS_CONF_RIGHTS_TEXT","");
//define("_DOCS_CONF_INDEXVIEW","İçeriği görüntüle");
//define("_DOCS_CONF_INDEXVIEW_TEXT","Etkinleştirildiğinde tüm belgelerin bir özeti görüntülenir.");
define("_DOCS_CONF_BLOGVIEW","Görünüm");
define("_DOCS_CONF_BLOGVIEW_TEXT","'Kategori görünümü' - belgelerin bir özetini görüntüler. 'Blog görünümü' - en yeni makaleleri bir blog görünümünde görüntüler. ");

define("_DOCS_CONF_BREADCRUMP","Ekmek kırıntısı göster");
define("_DOCS_CONF_BREADCRUMP_TEXT","");
define("_DOCS_CONF_PREAMBLE","Tam özeti görüntüle");
define("_DOCS_CONF_PREAMBLE_TEXT","Etkinleştirildiğinde, belgelerin tam tanıtımı, modülün ana sayfasında görünür. Devre dışı bırakılırsa aşağıdaki belirtildiği gibi, sadece karakter sayısı görüntülenir.");
define("_DOCS_CONF_CHARCOUNT","Özetin uzunluğu");
define("_DOCS_CONF_CHARCOUNT_TEXT","Özetin uzunluğu için karakter sayısını belirtin. Yukarıdaki ayar devre dışı olduğunda etkindir");
define("_DOCS_CONF_INDEXCOUNT","İçindekiler");
define("_DOCS_CONF_INDEXCOUNT_TEXT","0=bireysel belge için giriş sayfasında içindekiler yok, Değer> 0 içindekilerin derinliğini tüm belgeler için endekste gösterir.");
define("_DOCS_CONF_SEARCHCOUNT","Arama sonuç sayısı");
define("_DOCS_CONF_SEARCHCOUNT_TEXT","Kaç arama sonuçu gösterilmesini belirtin.");
define("_DOCS_CONF_LANGUAGE","Dil seçin");
define("_DOCS_CONF_LANGUAGE_TEXT","Burada, yeni oluşturulacak makaleler için varsayılan dili seçin.");
define("_DOCS_CONF_TABCOUNT","Sütun sayısı");
define("_DOCS_CONF_TABCOUNT_TEXT","Belgeler/makaleler ana sayfada görüntülendiği sütun sayısı.");

define("_DOCS_PAGE_NEWS","Yeni Makaleleri göster");
define("_DOCS_PAGE_NEWS_TEXT","Etkinleştirildiğinde, modülün giriş sayfasında yeni makaleler gösterilir");

define("_DOCS_PAGE_NEWSCOUNT","Yeni veya değiştirilmiş makaleler için zaman dönemi");
define("_DOCS_PAGE_NEWSCOUNT_TEXT","(gün olarak) sürece makale Yeni veya Değiştirilmiş olarak işaretlenmiş olacak");

define("_DOCS_PAGE_CHANGES","Değiştirilen makaleler gösterilsin");
define("_DOCS_PAGE_CHANGES_TEXT","Etkinleştirildiğinde, değiştirilmiş makaleler modülün ana sayfasında görüntülenir. Zaman dönemi 'Yeni' makale olarak gibi.");
define("_DOCS_PAGE_CHANGESCOUNT","Makale sayısı");
define("_DOCS_PAGE_CHANGESCOUNT_TEXT","Maksimum kaç makale sayısı 'YENİ' veya 'Değiştirildi' olarak ana sayfada bir blog olarak görüntülensin.");

/* attachments */
define("_DOCS_ATTACHMENTS","Eklentiler");
define("_DOCS_ATTACH_DELETE","Silinmesi için bu Eki işaretleyin.");
define("_DOCS_ATTACH_MAX","maks. Eklentiler");
define("_DOCS_ATTACH_MAX_TEXT","maks. belgeye dosya ek sayısı");

define("_DOCS_CONF_ATTACH","Yapılandırma belge ekleri");
define("_DOCS_CONF_ATTACH_TEXT","Burada belirlenen belgeler için dosya ekleri ayarlanır. Eklentiler belgenin aşağısında indirme listesi olarak görüntülenir ve kullanıcı tarafından indirilebilir.");
define("_DOCS_CONF_ATTACH_ON","Eklere izin ver");
define("_DOCS_CONF_ATTACH_ON_TEXT","Etkin olduğunda, dosya ekleri belgelere eklenir. ");
define("_DOCS_CONF_ATTACH_MAXSIZE","maks. Dosya boyutu");
define("_DOCS_CONF_ATTACH_MAXSIZE_TEXT","dosya başına kByte");
define("_DOCS_CONF_ATTACH_PATH","Dizin");
define("_DOCS_CONF_ATTACH_PATH_TEXT","Burada dosya eklerinin kaydedileceği dizini belirtin.");
define("_DOCS_CONF_ATTACH_MEDIA","Medya verilerini göster");
define("_DOCS_CONF_ATTACH_MEDIA_TEXT","Etkinleştirildiğinde, Medya verileri (mp3, mp4, resimler) içeriklerin altında görüntülenir, indirme listesinde değil.");
define("_DOCS_CONF_ATTACH_MAXWIDTH","maks. Medya dosyalarını genişliği");
define("_DOCS_CONF_ATTACH_MAXWIDTH_TEXT","Burada medya dosyaların içerik bölümünde hangi genişlikte görüntülenecek genişliği piksel olarak belirtin. Belirtilen veriyi kullanılan Tema ile ayarlayın. ");
define("_DOCS_CONF_ATTACH_MAXWIDTHTHUMB","maks. Küçük resim genişletilmesi");
define("_DOCS_CONF_ATTACH_MAXWIDTHTHUMB_TEXT","maks. Resimler için küçük resim genişletilmesi belirtin. Bunlar daha sonra bir lightbox da görüntülenir.");
define("_DOCS_CONF_ATTACH_MAXHEIGHT","maks. Medya dosyaların yüksekliği");
define("_DOCS_CONF_ATTACH_MAXHEIGHT_TEXT","Burada medya dosyaların içerik bölümünde hangi yükseklikte görüntülenecek yüksekliği piksel olarak belirtin. Belirtilen veriyi kullanılan Tema ile ayarlayın. ");

define("_DOCS_CONF_PAGE","Yapılandırma belgeleri sayfası");
define("_DOCS_CONF_PAGE_TEXT","");
define("_DOCS_PAGE_INDEX","Endeks görünümü");
define("_DOCS_PAGE_INDEX_TEXT","Etkin olduğunda, içindekiler tablosu her sayfada görünecektir.");
define("_DOCS_PAGE_INDEXFULL","Tam Endeksi göster");
define("_DOCS_PAGE_INDEXFULL_TEXT","Etkinleştirildiğinde, komple içindekiler tablosu her sayfada görülebilir.");
define("_DOCS_PAGE_LASTEDITOR","son yazarı göster ");
define("_DOCS_PAGE_LASTEDITOR_TEXT","Etkinleştirildiğinde, son değişiklik tarihi ve yazarı makalenin altında görüntülenir.");
define("_DOCS_PAGE_VIEWKEYWORDS","Anahtar kelimeleri göster ");
define("_DOCS_PAGE_VIEWKEYWORDS_TEXT","Etkin olduğunda, anahtar kelimeleri makalenin altında görünecektir.");
define("_DOCS_PAGE_VIEWNAVIGATION","Navigasyon görüntüle");
define("_DOCS_PAGE_VIEWNAVIGATION_TEXT","Etkinleştirildiğinde, makalenin altında makalelerin arasında ilerlemek için buton görünür.");
define("_DOCS_PAGE_CREATOR","Oluşturanı göster");
define("_DOCS_PAGE_CREATOR_TEXT","Etkinleştirildiğinde, makalenin altında oluşturan görüntülenir.");
define("_DOCS_PAGE_VIEWRATING","Oylama görüntüle");
define("_DOCS_PAGE_VIEWRATING_TEXT","Etkin olduğunda, makalenin üstünde oylama gösterilir.");

define("_DOCS_PAGE_VIEWSOCIAL","Sosyal Bağlantılar görüntüle ");
define("_DOCS_PAGE_VIEWSOCIAL_TEXT","Etkinleştirildiğinde, makalelerin altında farklı sosyal ağlara bağlantılar görüntülenir.");

define("_DOCS_PAGE_EDITORS","Kim makaleleri değişebilir ");
define("_DOCS_PAGE_EDITORS_TEXT","Belirtin, hangi kullanıcı grubu makale oluşturabilir ve değiştirebilir. Burada 'user' belirtilirse, TÜM Kullanıcı Grupları düzenleyebilir. Yöneticiler her şeyi yapabilir. Yalnızca bir yönetici silebilir.");
define("_DOCS_PAGE_EDITOR_RIGHTS","Editörü serbest bırakabilir");
define("_DOCS_PAGE_EDITOR_RIGHTS_TEXT","Belirtin, Editörler ayrıca yeni makaleleri aktive edebilir mi.");

define("_DOCS_PAGE_VIEWSIMILAR","benzer makaleleri görüntüle");
define("_DOCS_PAGE_VIEWSIMILAR_TEXT","Etkinleştirildiğinde, makalenin altında benzer makalelerin bir listesi görüntülenir.");
define("_DOCS_PAGE_SIMILARCOUNT","Benzer makalelerin sayısı");
define("_DOCS_PAGE_SIMILARCOUNT_TEXT","Burada belirtin, metnin altında ne kadar benzer makale görüntülenecek. Benzer makaleler sadece makalede anahtar kelimeler belirtildiği takdirde görüntülenir.");

define("_DOCS_PAGE_PRINT","Yazdır");
define("_DOCS_PAGE_PRINT_TEXT","Etkinleştirildiğinde, kullanıcıya bir yazıcı simgesi görünecektir, ki bunun üzerinden belgenin yazıcı için optimize edilmiş haline bakabilir.");

define("_DOCS_CONF_INTRO","Modüle ait Önsöz");
define("_DOCS_CONF_INTRO_TEXT","Burada modül için bir Önsöz girilebilir. Bu modül ana sayfasında görünür.");

define("_DOCS_CONF_LINK","Yapılandırma bağlantısı");
define("_DOCS_CONF_LINK_TEXT","");
define("_DOCS_PAGE_VIEWBOOKLINK","Makaleleri bağla");
define("_DOCS_PAGE_VIEWBOOKLINK_TEXT","Etkinleştirildiğinde, diğer haber başlıklara uyan tüm kelimeler, bu makaleler ile bağlanır.");
define("_DOCS_PAGE_VIEWBOOKBASE","Tüm kitaplardan bağla");
define("_DOCS_PAGE_VIEWBOOKBASE_TEXT","Etkin olduğunda, yukarıdaki bağlantı için, tüm belgeler kullanılır. Devre dışı bırakılırsa, yalnızca geçerli makaleyi içeren belgenin.");
define("_DOCS_PAGE_VIEWENCYLINKS","Ansiklopediye bağlantılar oluşturun");
define("_DOCS_PAGE_VIEWENCYLINKS_TEXT","Etkinleştirildiğinde, ansiklopedikten terimler metinler ile bağlanır.");
define("_DOCS_PAGE_INDEX_NEW","Değişiklikleri işaretle");
define("_DOCS_PAGE_INDEX_NEW_TEXT","Etkinleştirildiğinde, içeriklerde yeni ve değiştirilmiş makaleler işaretlenir");

//define("_DOCS_STATUS","Zamansal bilgi kullanımı");
//define("_DOCS_STATUS_TEXT","Etkinleştirildiğinde, aşağıdaki zaman bilgileri yayınlanmak için kullanılır.");
//define("_DOCS_STARTTIME","Başlangıç tarihi");
//define("_DOCS_STARTTIME_TEXT","Burada makale için bir başlangıç zamanı belirleyebilirsin. Sadece bu noktadan itibaren makale önyüzde görüntülenir.");
//define("_DOCS_ENDTIME","Bitiş tarihi");
//define("_DOCS_ENDTIME_TEXT","Burada makale için bir bitiş zamanı belirleyebilirsin. Bu noktadan itibaren makale önyüzde görüntülenmez. Eğer bitiş tarihi başlangıç tarihinden önce olursa, makale başlangıç tarihinden itibaren süresiz görüntülenir.");

define("_DOCS_VIEWTITLE","Başlığı göster");
define("_DOCS_VIEWTITLE_TEXT","Modül başlığı makale sayfasında görüntülensin mi");
define("_DOCS_VIEWVIEWS","Gösterilme sayısını göster");
define("_DOCS_VIEWVIEWS_TEXT","");
define("_DOCS_LINKTITLE","Başlığı bağla");
define("_DOCS_LINKTITLE_TEXT","Modül başlığı bağlansın mı?");
define("_DOCS_VIEWSEARCH","Arama kutusunu göster");
define("_DOCS_VIEWSEARCH_TEXT","Arama formu makale sayfasında görüntülensin mi");

define("_DOCS_META_PAGE","Meta seçenekleri");
define("_DOCS_META_PAGE_TEXT","Burada, meta verileri uyarlanabilir. Herhangi bir giriş yapılmamışsa, otomatik olarak varsayılan girdiler kullanılır.");
define("_DOCS_META_CANONICAL","Köken URL");
define("_DOCS_META_CANONICAL_TEXT","Burada yinelenen içerik önlemek için makalenin kaynak URL belirtin. Şartname dahil olarak 'http://'");
define("_DOCS_META_ROBOTS","Robotlar");
define("_DOCS_META_ROBOTS_TEXT","Robot beyanı");
define("_DOCS_META_ALTERNATE","ek meta etiketleri");
define("_DOCS_META_ALTERNATE_TEXT","Burada, başlık için ek META etiketleri belirtilmelidir. Aşağıdaki bilgileri belirtin ve on-virgül parantez OLMADAN ve nihai ayrılmış olarak tamamlayın (örn. link rel=\"robots\" content=\"all\", meta name=\"author\".... )  Her iki örnekte de, zaten önceden entegre edilmiştir.");
define("_DOCS_META_REVISIT","Tekrar ziyaret");
define("_DOCS_META_REVISIT_TEXT","gün sayısını belirtin.");
define("_DOCS_META_AUTHOR","Yazar");
define("_DOCS_META_AUTHOR_TEXT","isteğe bağlı olarak farklı bir yazar belirtin.");

/* tools */
define("_DOCS_TOOLS","Gelişmiş fonksiyonlar");
define("_DOCS_TOOLS_TEXT","");
define("_DOCS_TOOLS_IMPORT","İthalat fonksiyonları");
define("_DOCS_TOOLS_IMPORT_TEXT","Burada diğer çeşitli modüllerden, içerik Belgeler içine alınabilir. Veriler silinmez, tablo kopyalanır");
define("_DOCS_TOOLS_IMPORT_SELECT","Modül seçin");
define("_DOCS_TOOLS_IMPORT_SELECT_TEXT","Hangi modülden verilerin ithal edileceğini burada seçin. İçerikler iki kez ithal edilmez. Modülünün içeriği aşağıda isimleri içeren bir belgeye alınır.");
define("_DOCS_TOOLS_DB","Veritabanı fonksiyonları");
define("_DOCS_TOOLS_DB_TEXT","Modülünün veritabanını korumak için araçlar");
define("_DOCS_TOOLS_IMPORT_DOC","Belgeye İthalat");
define("_DOCS_TOOLS_IMPORT_DOC_TEXT","İçeriğin ithal edildiği temel belge için bir ad girin. Eğer alan bos kalırsa, modül adı kullanılır");


/* extendet settings */
define("_DOCS_EXTENDET_SETTINGS","Gelişmiş ayarlar");
/* sociol */
define("_DOCS_SOCIAL_INFO_FACEBOOK","Daha fazla veri koruması için 2 tıklama: Eğer sadece buraya tıkladığın zaman, buton aktif olacak ve Facebook için tavsiye gönderebileceksin. Çalıştırıldığında, veri üçüncü şahıslara transfer edilecektir &ndash; bakın i.");
define("_DOCS_SOCIAL_INFO_TWITTER","Daha fazla veri koruması için 2 tıklama: Eğer sadece buraya tıkladığın zaman, buton aktif olacak ve Twitter için tavsiye gönderebileceksin. Çalıştırıldığında, veri üçüncü şahıslara transfer edilecektir &ndash; bakın i.");
define("_DOCS_SOCIAL_INFO_GPLUS","Daha fazla veri koruması için 2 tıklama: Eğer sadece buraya tıkladığın zaman, buton aktif olacak ve Google+ için tavsiye gönderebileceksin. Çalıştırıldığında, veri üçüncü şahıslara transfer edilecektir &ndash; bakın i.");
define("_DOCS_SOCIAL_INFO_HELP","Eğer tıklatarak bu alanları etkinleştirirseniz, Facebook, Twitter veya Google'ın bilgileri ABD'ye iletilir ve orada da kaydedilebilir.");
define("_DOCS_SOCIAL_INFO_TOOLS","Kalıcı etkinleştirme ve veri iletimini kabul ediyorum:");

/* diverse */
define("_DOCS_START","Başlangıç");
define("_DOCS_ADMIN_PANEEL","Yönetim");
define("_DOCS_PREVIOUS","geri");
define("_DOCS_NEXT","ileri");
define("_DOCS_MOVE","taşı");
define("_DOCS_FROM","ile");
define("_DOCS_ACTION","Eylem");
define("_DOCS_HISTORY","Tarihçe");
define("_DOCS_LASTCHANGE","son değişiklik");
define("_DOCS_NEWCONTENT","yeni makaleler");
define("_DOCS_LASTCHANGES","son değişiklikler");
define("_DOCS_LINKS","Bağlama");
//define("_ATTACHMENTS","Eklentiler");
//define("_READMORE","daha fazla");
define("_DOCS_VIEW_INDEX","Kategori görünümü");
define("_DOCS_VIEW_BLOG","Blog görünümü");
define("_DOCS_VIEW_LOG","Günlük protokolü");

define("_DOCS_DOWNLOAD","İndirin");
define("_DOCS_ATTACHMENT","Ekler");
define("_DOCS_PAGE_SIMILAR","Bu makaleler, sizin için ilginç olabilir:");

define("_DOCS_FILENAME","Dosya adı");
define("_DOCS_FILESIZE","Boyut");
define("_DOCS_FILETYPE","MIME Türü");
define("_DOCS_FILETITLE","Açıklama");

define("_DOCS_URL","Orijinal makaleye bağlantı");
define("_DOCS_URL_TEXT","Bu makale kimden geliyor:");
/* error */

define("_DOCS_NESTEDSET_ERROR","Modülünün veritabanı yapısında hatalar bulunmuştur. Veritabanı bakım için 'Seçenekler' üzerine tıklayın.");
define("_DOCS_NESTEDSET_IO","Veritabanı yapısı hatasız.");
define("_DOCS_REPAIR","Onarın");
define("_DOCS_DB_REPAIR","Veri yapısı onarım ");
define("_DOCS_DB_REPAIR_TEXT","Modülün veri yapısı kontrol edilir ve hatalar durumunda bir hata belgesi oluşturulur.");
define("_DOCS_DB_DELLOG","Günlük tablosunu silin ");
define("_DOCS_DB_DELLOG_TEXT","Günlük tablosundan TÜM kayıtlar silinir!");
define("_DOCS_DB_DELLOG_ACTION","Günlük tablosu silindi! ");
define("_DOCS_IMPORT_ACTION"," Veri setleri ithal edildi. ");

/* --- */
define("_DOCS_CONF_LINKOTHER","diğer modüllere bağla");
define("_DOCS_CONF_LINKOTHER_TEXT","Diğer modüllerdeki makaleler bağlansın mı?");
define("_DOCS_LINK_ALL","her bir oluşum");
define("_DOCS_LINK_FIRST","yalnızca ilk oluşum");
define("_DOCS_CONF_LINKCOUNT","Bağlantı sayısı");
define("_DOCS_CONF_LINKCOUNT_TEXT","Ne kadar sıklıkla makalenin içeriği bağlantılı olmalıdır?");
define("_DOCS_PAGE_VIEWMODULELINK","Diğer modüller bağlantılı olabilir");
define("_DOCS_PAGE_VIEWMODULELINK_TEXT","Diğer modüller bu modülde terim bağlayabilir mi?");

define("_DOCS_NEW2","Yeni");
define("_DOCS_DEFAULT","Standart");
define("_DOCS_UPDATE","Güncellenmiş");
define("_DOCS_VIEW_LIST","Liste");
define("_DOCS_CONF_TABCOUNT_LIST_TEXT","Sütun sayısı - sadece liste görünümüne geçerlidir");
//define("_DOCS_NEW_DOCUMENTS","Yeni belge (ler)");

define("_DOCS_CONF_INSERTFIRST","Yerleştirme sırası");
define("_DOCS_CONF_INSERTFIRST_TEXT","hangi noktada yeni belgeler üst belgeye eklenmesini seçin");
define("_DOCS_INSERTFIRST","başında");
define("_DOCS_INSERTLAST","sonunda");

define("_DOCS_RATE_BAD","berbat");
define("_DOCS_RATE_POOR","kötü");
define("_DOCS_RATE_REGULAR","idare eder");
define("_DOCS_RATE_GOOD","iyi");
define("_DOCS_RATE_GORGEOUS","çok iyi");
define("_DOCS_RATE_CANCEL","kaydı sil");
define("_DOCS_SELECT_ICON","Burada modülün ana sayfasında başlıkta görünür bir simge seçebilirsiniz.");

define("_DOCS_ERR_FILESIZE","Dosya çok büyük");
define("_DOCS_INFO_FILESIZE","maksimum dosya boyutu [kByte] :");

/*  sendfriend */
define("_DOCS_RECYOURNAME", "Adınız:");
define("_DOCS_RECYOUREMAIL", "E-postanız:");
define("_DOCS_RECFRIENDNAME", "Arkadaşınızın Adı:");
define("_DOCS_RECFRIENDEMAIL", "Arkadaşınızın E-posta adresi:");
define("_DOCS_RECREMARKS", "Kişisel İlaveler:");
define("_DOCS_RECYOURFRIEND", "Arkadaşınız");
define("_DOCS_RECINTSITE", "İlginç makale:");
define("_DOCS_RECOURSITE", "makaleyi ilginç buldum");
define("_DOCS_RECINTSENT", "ve sizi teşvik etmek istedim.");
define("_DOCS_RECSITENAME", "Makale Önizleme:");
define("_DOCS_RECSITEURL", "Web sitenin URLsi:");
define("_DOCS_RECTHANKS", "Sizin tavsiye için teşekkür ederiz!");
define("_DOCS_RECERRORTITLE", "E-posta gönderilemedi, aşağıdaki hata oluştu:");
define("_DOCS_RECERRORNAME", "Lütfen adınızı girin.");
define("_DOCS_RECERRORRECEIVER", "Alıcı e-posta adresi geçersiz.");
define("_DOCS_RECERRORSENDER", "Sizin gönderenin e-posta adresi geçersiz.");

define("_DOCS_PAGE_SENDFRIEND","Gönderimi göster");
define("_DOCS_PAGE_SENDFRIEND_TEXT","Buton 'Makaleyi Bir Arkadaşına Gönder' göster");

define("_DOCS_STARTPAGE","Ana Sayfa");
define("_DOCS_STARTPAGE_TEXT","Etkinleştirildiğinde, bu makale 'Ana Safya' bloğunda görüntülenir");
define("_DOCS_STARTPAGE_OFF","Ana sayfadan kaldırın");
define("_DOCS_STARTPAGE_ON","Ana sayfada gösterin");

define("_DOCS_NONE","hiçbiri");
define("_DOCS_PAGE_ALPHA","Alfabetik bir dizin göster");
define("_DOCS_PAGE_ALPHA_TEXT","etkinleştirildiğinde, alfabetik bir dizin makaleyi görünür");
define("_DOCS_ALPHA_INDEX","Alfabetik İndeks");

define("_DOCS_FILTER","Filter");
define("_DOCS_CONF_BLOCKS","Menü blok");
define("_DOCS_CONF_BLOCKS_TEXT","Burada modülün kendi Menublock ayarlayabilirsiniz.");
define("_DOCS_CONF_MENUWIDTH","Menü derinliği");
define("_DOCS_CONF_MENUWIDTH_TEXT","Block menü oluşturulabilir hangi derinliği seçin.");
define("_DOCS_CONF_MENUCONTENT","içeriği seçin");
define("_DOCS_CONF_MENUCONTENT_TEXT","Hangi belgelerin Block menü görüntülemek için seçin.");

define("_DOCS_PAGE_TITLE","sayfa başlığı ");
define("_DOCS_PAGE_TITLE_TEXT","girişi, diğer sayfa başlığı");
define("_DOCS_UPDATE_DB","Update veritabanı ");
define("_DOCS_UPDATE_DB_TXT","veri seti test edilmiş ve adapte edilmiştir");

?>