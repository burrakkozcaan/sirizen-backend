# Filament Admin Panel - Resource'ların Detaylı Açıklaması

## 📋 İÇİNDEKİLER
1. [ÜRÜN YÖNETİMİ](#urun-yonetimi)
2. [SATICI YÖNETİMİ](#satici-yonetimi)
3. [SİPARİŞ YÖNETİMİ](#siparis-yonetimi)
4. [ÖDEME VE KOMİSYON](#odeme-ve-komisyon)
5. [KULLANICI YÖNETİMİ](#kullanici-yonetimi)
6. [MÜŞTERİ YÖNETİMİ](#musteri-yonetimi)
7. [İNCELEME VE SORULAR](#inceleme-ve-sorular)
8. [KAMPANYA VE KUPONLAR](#kampanya-ve-kuponlar)
9. [PAZARLAMA VE ÇEKİLİŞLER](#pazarlama-ve-cekilisler)
10. [ALIŞVERİŞ SEPETİ](#alisveris-sepeti)
11. [FAVORİ VE LİSTELER](#favori-ve-listeler)
12. [KATALOG YÖNETİMİ](#katalog-yonetimi)
13. [İÇERİK YÖNETİMİ](#icerik-yonetimi)
14. [BİLDİRİMLER](#bildirimler)
15. [ARAMA VE ANALYTICS](#arama-ve-analytics)
16. [SİSTEM AYARLARI](#sistem-ayarlari)
17. [FİNANS VE FATURALAR](#finans-ve-faturalar)
18. [KARGO VE LOJİSTİK](#kargo-ve-lojistik)
19. [KVKK VE UYUMLULUK](#kvkk-ve-uyumluluk)

---

## 🛍️ ÜRÜN YÖNETİMİ

### ✅ KULLANILIYOR:

#### 1. **Ürünler (Products)** - 400 adet
**Ne İşe Yarar:** Ana ürün veritabanı. Tüm ürünlerin bilgileri burada tutulur.
**Frontend'te Nerede:** `/product/[slug]` sayfası, ürün kartları, arama sonuçları
**API:** `/api/products`, `/api/pdp/{slug}`
**Durum:** ✅ Aktif kullanılıyor

#### 2. **Kategoriler (Categories)**
**Ne İşe Yarar:** Ürünleri kategorilere ayırır (Elektronik, Giyim, Ev & Yaşam vb.)
**Frontend'te Nerede:** `/category/[slug]` sayfası, mega menü, breadcrumb
**API:** `/api/categories`
**Durum:** ✅ Aktif kullanılıyor

#### 3. **Özellikler (Attributes)**
**Ne İşe Yarar:** Ürün özelliklerini tanımlar (Renk: Kırmızı, Beden: M, Ekran Boyutu: 6.1 inç vb.)
**Frontend'te Nerede:** Ürün detay sayfasında özellikler tablosu, varyant seçici
**Durum:** ✅ Aktif kullanılıyor

#### 4. **PDP Düzenleri (PdpLayouts)** ⚠️ DUPLICATE KONTROLÜ GEREKLİ
**Ne İşe Yarar:** Her kategori grubu için ürün detay sayfasının (PDP) hangi blokların nerede gösterileceğini belirler
**Örnek:** Giyim kategorisinde "Beden Tablosu" üstte, Elektronik'te altta gösterilir
**Frontend'te Nerede:** PdpEngine, PdpEngineV2 component'leri
**API:** `/api/pdp/{slug}` içinde `layout` bilgisi geliyor
**Durum:** ✅ Aktif kullanılıyor
**⚠️ NOT:** "PDP Blokları" ile karışıklık var (aşağıya bakın)

#### 5. **Filtre Yapılandırmaları (FilterConfigs)**
**Ne İşe Yarar:** Kategori sayfalarındaki filtreleri yapılandırır (Marka, Fiyat, Renk vb.)
**Frontend'te Nerede:** `/category/[slug]` sayfasındaki filtre paneli
**API:** `/api/categories/{slug}` içinde `filters` geliyor
**Durum:** ✅ Aktif kullanılıyor

#### 6. **Rozet Tanımları (BadgeDefinitions)**
**Ne İşe Yarar:** Ürün rozetlerini tanımlar (Çok Satan, Yeni, İndirimli, Hızlı Kargo vb.)
**Frontend'te Nerede:** Ürün kartlarında ve ürün detay sayfasında rozetler
**API:** `/api/pdp/{slug}/badges`
**Durum:** ✅ Aktif kullanılıyor

#### 7. **Rozet Kuralları (BadgeRules)**
**Ne İşe Yarar:** Rozetlerin otomatik hesaplanması için kurallar tanımlar
**Örnek:** "Son 30 günde 100+ satış yapan ürünlere 'Çok Satan' rozeti ver"
**Durum:** ✅ Backend'te aktif, frontend'te görünmez

#### 8. **Sosyal Kanıt Kuralları (SocialProofRules)**
**Ne İşe Yarar:** "3.2K kişinin sepetinde", "Son 24 saatte 150 kişi baktı" gibi mesajların kurallarını belirler
**Frontend'te Nerede:** Ürün detay sayfasında sosyal kanıt gösterimi
**API:** `/api/pdp/{slug}/social-proof`
**Durum:** ✅ Aktif kullanılıyor

#### 9. **Benzer Ürünler (SimilarProducts)**
**Ne İşe Yarar:** Ürün detay sayfasında gösterilecek benzer ürünleri belirler
**Frontend'te Nerede:** Ürün detay sayfasının alt kısmında "Benzer Ürünler" bölümü
**API:** `/api/pdp/{slug}/related`
**Durum:** ✅ Aktif kullanılıyor

#### 10. **Hızlı Linkler (QuickLinks)**
**Ne İşe Yarar:** Ana sayfadaki hızlı erişim linklerini yönetir (Fiyatı Düşenler, Süper Fırsatlar, Moda vb.)
**Frontend'te Nerede:** Ana sayfa üst kısmında kaydırılabilir linkler
**API:** `/api/quick-links`
**Durum:** ✅ Aktif kullanılıyor

#### 11. **Kategori Grupları (CategoryGroups)**
**Ne İşe Yarar:** Kategorileri gruplar (Giyim, Elektronik, Ev & Yaşam vb.) - PDP Layout'ları bu gruplara göre çalışır
**Durum:** ✅ Backend'te aktif, PdpLayouts ile ilişkili

#### 12. **Özellik Setleri (AttributeSets)**
**Ne İşe Yarar:** Özellikleri gruplar (Renk Seti, Beden Seti, Teknik Özellikler Seti vb.)
**Durum:** ✅ Backend'te aktif, Attributes ile ilişkili

#### 13. **Ürün İçe Aktarma Logları (ProductImportLogs)**
**Ne İşe Yarar:** Toplu ürün içe aktarma işlemlerini loglar (Excel'den ürün yükleme vb.)
**Durum:** ⚠️ Sadece admin panelinde görünür, frontend'te yok

#### 14. **Ürün Onayları (ProductApprovals)**
**Ne İşe Yarar:** Satıcıların eklediği ürünlerin onay sürecini yönetir
**Durum:** ⚠️ Sadece admin panelinde görünür, frontend'te yok

### ⚠️ DUPLICATE/KARIŞIK:

#### 15. **PDP Blokları (ProductBlocks)** ⚠️ DUPLICATE Mİ?
**Ne İşe Yarar:** Ürüne özel bloklar tanımlar (belirli bir ürün için özel içerik blokları)
**Fark:** 
- **PdpLayouts**: Kategori gruplarına göre genel layout (hangi bloklar nerede gösterilecek)
- **ProductBlocks**: Ürüne özel bloklar (belirli bir ürün için özel blok içeriği)
**Durum:** ❓ Frontend'te kullanılmıyor gibi görünüyor
**ÖNERİ:** Kullanılmıyorsa kaldırılabilir veya PdpLayouts ile birleştirilebilir

### ❓ KULLANILIYOR MU?

#### 16. **Ürün Paketleri (ProductBundles)**
**Ne İşe Yarar:** Ürün paketlerini yönetir (2 al 1 öde, set ürünler vb.)
**Durum:** ❓ Frontend'te bundle gösterimi yok gibi görünüyor
**ÖNERİ:** Kullanılmıyorsa kaldırılabilir

#### 17. **Ürün Garantileri (ProductGuarantees)**
**Ne İşe Yarar:** Ürün garantilerini yönetir (2 yıl garanti, iade garantisi vb.)
**Frontend'te:** PDPGuarantees component var
**Durum:** ✅ Kullanılıyor olabilir, kontrol edilmeli

---

## 🏪 SATICI YÖNETİMİ

### ✅ KULLANILIYOR:

#### 1. **Satıcılar (Vendors)** - 11 adet
**Ne İşe Yarar:** Satıcı bilgilerini yönetir (isim, logo, açıklama, puan vb.)
**Frontend'te Nerede:** `/store/[slug]` sayfası, satıcı profili
**API:** `/api/vendors`, `/api/stores/{slug}`
**Durum:** ✅ Aktif kullanılıyor

#### 2. **Satıcı Rozetleri (SellerBadges)**
**Ne İşe Yarar:** Satıcı rozetlerini yönetir (Güvenilir Satıcı, Hızlı Kargo, Elite Satıcı vb.)
**Frontend'te Nerede:** Satıcı profilinde rozetler
**Durum:** ✅ Aktif kullanılıyor

### ⚠️ ADMIN ONLY:

#### 3. **Ödemeler (VendorPayouts)**
**Ne İşe Yarar:** Satıcılara yapılan ödemeleri yönetir
**Durum:** ⚠️ Sadece admin panelinde görünür

#### 4. **Bakiyeler (VendorBalances)**
**Ne İşe Yarar:** Satıcı bakiyelerini takip eder (bekleyen ödeme, ödenen tutar vb.)
**Durum:** ⚠️ Sadece admin panelinde görünür

#### 5. **Seviyeler (VendorTiers)**
**Ne İşe Yarar:** Satıcı seviye sistemini yönetir (Bronz, Gümüş, Altın Satıcı vb.)
**Durum:** ⚠️ Sadece admin panelinde görünür

#### 6. **Puanlar (VendorScores)**
**Ne İşe Yarar:** Satıcı puanlama sistemini yönetir
**Durum:** ⚠️ Sadece admin panelinde görünür

#### 7. **Takipçiler (VendorFollowers)**
**Ne İşe Yarar:** Satıcıyı takip eden kullanıcıları yönetir
**Durum:** ❓ Frontend'te takipçi gösterimi var ama bu model kullanılıyor mu kontrol edilmeli

#### 8. **Satıcı Belgeleri (VendorDocuments)**
**Ne İşe Yarar:** Satıcı belgelerini yönetir (kimlik, vergi levhası vb.)
**Durum:** ⚠️ Sadece admin panelinde görünür

#### 9. **Cezalar (VendorPenalties)**
**Ne İşe Yarar:** Satıcı cezalarını yönetir (geç teslimat, iptal oranı yüksek vb.)
**Durum:** ⚠️ Sadece admin panelinde görünür

#### 10. **Performans Logları (VendorPerformanceLogs)**
**Ne İşe Yarar:** Satıcı performans metriklerini loglar
**Durum:** ⚠️ Sadece admin panelinde görünür

---

## 📦 SİPARİŞ YÖNETİMİ

### ✅ KULLANILIYOR:

#### 1. **Siparişler (Orders)** - 15 adet
**Ne İşe Yarar:** Kullanıcı siparişlerini yönetir
**Frontend'te Nerede:** `/orders` sayfası, `/order/[id]` detay sayfası
**API:** `/api/orders`
**Durum:** ✅ Aktif kullanılıyor

#### 2. **Sipariş Kalemleri (OrderItems)**
**Ne İşe Yarar:** Sipariş içindeki ürünleri yönetir (hangi ürünler, kaç adet, fiyat vb.)
**Frontend'te Nerede:** Sipariş detay sayfasında ürün listesi
**Durum:** ✅ Aktif kullanılıyor

#### 3. **Gönderi (Shipments)**
**Ne İşe Yarar:** Kargo gönderilerini yönetir (kargo firması, takip numarası vb.)
**Frontend'te Nerede:** Sipariş detay sayfasında kargo takibi
**Durum:** ✅ Aktif kullanılıyor

#### 4. **İadeler (Refunds)** - 6 adet
**Ne İşe Yarar:** İade işlemlerini yönetir
**Frontend'te Nerede:** Sipariş detay sayfasında iade butonu ve formu
**Durum:** ✅ Aktif kullanılıyor

#### 5. **İade Politikaları (ReturnPolicies)**
**Ne İşe Yarar:** İade kurallarını tanımlar (14 gün içinde iade, etiketli ürünler vb.)
**Frontend'te Nerede:** Ürün sayfasında iade bilgisi
**Durum:** ✅ Aktif kullanılıyor

#### 6. **İade Görselleri (ReturnImages)**
**Ne İşe Yarar:** İade formunda yüklenen görselleri yönetir
**Frontend'te Nerede:** İade formunda görsel yükleme
**Durum:** ✅ Aktif kullanılıyor

#### 7. **Anlaşmazlıklar (Disputes)** - 1 adet
**Ne İşe Yarar:** Sipariş anlaşmazlıklarını yönetir (ürün gelmedi, yanlış ürün geldi vb.)
**Frontend'te Nerede:** Sipariş detay sayfasında anlaşmazlık açma butonu
**Durum:** ✅ Aktif kullanılıyor

#### 8. **Kargo Kuralları (ShippingRules)**
**Ne İşe Yarar:** Kargo kurallarını ve ücretlerini yönetir (ücretsiz kargo limiti, bölgesel ücretler vb.)
**Durum:** ✅ Backend'te aktif, sipariş oluşturma sırasında kullanılıyor

---

## 💳 ÖDEME VE KOMİSYON

### ✅ KULLANILIYOR:

#### 1. **Ödemeler (Payments)**
**Ne İşe Yarar:** Ödeme kayıtlarını yönetir
**Frontend'te Nerede:** Checkout sayfası, ödeme işlemleri
**API:** `/api/payments`
**Durum:** ✅ Aktif kullanılıyor

### ⚠️ ADMIN ONLY:

#### 2. **Komisyonlar (Commissions)**
**Ne İşe Yarar:** Platform komisyonlarını yönetir (satıcıdan alınan komisyon oranları)
**Durum:** ⚠️ Sadece admin panelinde görünür

#### 3. **Gateway Ayarları (PaymentGatewaySettings)**
**Ne İşe Yarar:** Ödeme gateway ayarlarını yönetir (iyzico, PayTR vb.)
**Durum:** ⚠️ Sadece admin panelinde görünür

---

## 👤 KULLANICI YÖNETİMİ

### ❓ KULLANILIYOR MU?

#### 1. **Cüzdanlar (UserWallets)**
**Ne İşe Yarar:** Kullanıcı cüzdanlarını yönetir (bakiye, puanlar vb.)
**Durum:** ❓ Frontend'te cüzdan gösterimi var mı kontrol edilmeli

#### 2. **Cüzdan İşlemleri (WalletTransactions)**
**Ne İşe Yarar:** Cüzdan işlemlerini loglar (para yükleme, harcama vb.)
**Durum:** ❓ Cüzdan kullanılıyorsa bu da kullanılıyor olmalı

#### 3. **Üyelik Programları (MembershipPrograms)**
**Ne İşe Yarar:** Üyelik programlarını yönetir (Elite üyelik, Premium üyelik vb.)
**Durum:** ❓ Frontend'te üyelik programı gösterimi var mı kontrol edilmeli

#### 4. **Kullanıcı Üyelikleri (UserMemberships)**
**Ne İşe Yarar:** Kullanıcının üyelik durumunu yönetir
**Durum:** ❓ Kullanılıyor mu kontrol edilmeli

---

## 🧑‍🤝‍🧑 MÜŞTERİ YÖNETİMİ

### ✅ KULLANILIYOR:

#### 1. **Kullanıcılar (Users)** - 18 adet
**Ne İşe Yarar:** Kullanıcı hesaplarını yönetir
**Frontend'te Nerede:** Account sayfası, profil yönetimi
**API:** `/api/user`, `/api/auth`
**Durum:** ✅ Aktif kullanılıyor

#### 2. **Adresler (Addresses)**
**Ne İşe Yarar:** Kullanıcı adreslerini yönetir
**Frontend'te Nerede:** `/account/addresses` sayfası
**API:** `/api/addresses`
**Durum:** ✅ Aktif kullanılıyor

#### 3. **Canlı Destek (CrispConversations)**
**Ne İşe Yarar:** Canlı destek konuşmalarını yönetir
**Frontend'te Nerede:** Crisp chat widget
**Durum:** ✅ Aktif kullanılıyor

---

## 💬 İNCELEME VE SORULAR

### ✅ KULLANILIYOR:

#### 1. **Ürün Soruları (ProductQuestions)**
**Ne İşe Yarar:** Ürün soru-cevap sistemini yönetir
**Frontend'te Nerede:** Ürün detay sayfasında "Sorular" bölümü
**API:** `/api/products/{id}/questions`
**Durum:** ✅ Aktif kullanılıyor

#### 2. **Yorum Görselleri (ReviewImages)**
**Ne İşe Yarar:** Yorumlara eklenen görselleri yönetir
**Frontend'te Nerede:** ProductReviews component'inde yorum görselleri
**Durum:** ✅ Aktif kullanılıyor

#### 3. **Faydalı Oylar (ReviewHelpfulVotes)**
**Ne İşe Yarar:** "Bu yorum faydalı mı?" oylarını yönetir
**Frontend'te Nerede:** ProductReviews component'inde oylama butonu
**Durum:** ✅ Aktif kullanılıyor

#### 4. **Satıcı Yorumları (SellerReviews)**
**Ne İşe Yarar:** Satıcı değerlendirmelerini yönetir
**Frontend'te Nerede:** VendorClient, satıcı yorumları bölümü
**Durum:** ✅ Aktif kullanılıyor

#### 5. **Ürün SSS (ProductFaqs)**
**Ne İşe Yarar:** Ürün sık sorulan sorularını yönetir
**Frontend'te:** PDPFAQ component var
**Durum:** ✅ Kullanılıyor olabilir, kontrol edilmeli

---

## 🎯 KAMPANYA VE KUPONLAR

### ✅ KULLANILIYOR:

#### 1. **Kampanyalar (Campaigns)**
**Ne İşe Yarar:** Kampanyaları yönetir (flash sale, indirim kampanyaları vb.)
**Frontend'te Nerede:** Ana sayfa hero bölümü, kampanya sayfaları
**API:** `/api/campaigns/active`, `/api/campaigns/hero`
**Durum:** ✅ Aktif kullanılıyor

#### 2. **Kuponlar (Coupons)**
**Ne İşe Yarar:** Kupon kodlarını yönetir
**Frontend'te Nerede:** Checkout sayfasında kupon kodu girişi
**API:** `/api/coupons/validate`
**Durum:** ✅ Aktif kullanılıyor

#### 3. **Kupon Kullanımları (CouponUsages)**
**Ne İşe Yarar:** Hangi kuponların kullanıldığını takip eder
**Durum:** ✅ Backend'te aktif, kupon kullanım takibi için

---

## 🎲 PAZARLAMA VE ÇEKİLİŞLER

### ❓ KULLANILIYOR MU?

#### 1. **Çekilişler (Raffles)**
**Ne İşe Yarar:** Çekilişleri yönetir
**Durum:** ❓ Frontend'te çekiliş gösterimi var mı kontrol edilmeli

#### 2. **Çekiliş Katılımları (RaffleEntries)**
**Ne İşe Yarar:** Çekiliş katılımlarını yönetir
**Durum:** ❓ Çekilişler kullanılıyorsa bu da kullanılıyor

#### 3. **Çekiliş Kazananları (RaffleWinners)**
**Ne İşe Yarar:** Çekiliş kazananlarını yönetir
**Durum:** ❓ Çekilişler kullanılıyorsa bu da kullanılıyor

---

## 🛒 ALIŞVERİŞ SEPETİ

### ✅ KULLANILIYOR:

#### 1. **Sepetler (Carts)**
**Ne İşe Yarar:** Kullanıcı sepetlerini yönetir
**Frontend'te Nerede:** CartContext, sepet sayfası
**API:** `/api/cart`
**Durum:** ✅ Aktif kullanılıyor

#### 2. **Sepet Kalemleri (CartItems)**
**Ne İşe Yarar:** Sepet içindeki ürünleri yönetir
**Frontend'te Nerede:** Sepet sayfasında ürün listesi
**Durum:** ✅ Aktif kullanılıyor

---

## ❤️ FAVORİ VE LİSTELER

### ✅ KULLANILIYOR:

#### 1. **Favoriler (Favorites)**
**Ne İşe Yarar:** Kullanıcı favorilerini yönetir (tek bir favori listesi)
**Frontend'te Nerede:** `/favorites` sayfası, favori butonu
**API:** `/api/favorites`
**Durum:** ✅ Aktif kullanılıyor

### ❓ KULLANILIYOR MU?

#### 2. **İstek Listeleri (Wishlists)**
**Ne İşe Yarar:** Çoklu istek listelerini yönetir (Doğum Günü Listesi, Ev Listesi vb.)
**Durum:** ❓ Frontend'te wishlist gösterimi var mı kontrol edilmeli
**NOT:** Favorites ile aynı şey mi yoksa farklı mı kontrol edilmeli

#### 3. **İstek Kalemleri (WishlistItems)**
**Ne İşe Yarar:** İstek listelerindeki ürünleri yönetir
**Durum:** ❓ Wishlists kullanılıyorsa bu da kullanılıyor

---

## 📚 KATALOG YÖNETİMİ

### ✅ KULLANILIYOR:

#### 1. **Özellik Setleri (AttributeSets)**
**Ne İşe Yarar:** Özellikleri gruplar (Renk Seti, Beden Seti vb.)
**Durum:** ✅ Backend'te aktif, Attributes ile ilişkili

#### 2. **Markalar (Brands)**
**Ne İşe Yarar:** Markaları yönetir
**Frontend'te Nerede:** Kategori sayfasında marka filtresi
**API:** `/api/brands`
**Durum:** ✅ Aktif kullanılıyor

---

## 📝 İÇERİK YÖNETİMİ

### ✅ KULLANILIYOR:

#### 1. **Satıcı Sayfaları (SellerPages)**
**Ne İşe Yarar:** Satıcı özel sayfalarını yönetir (Hakkımızda, İletişim vb.)
**Frontend'te Nerede:** VendorClient, satıcı hakkında sayfası
**Durum:** ✅ Aktif kullanılıyor

#### 2. **İletişim Mesajları (ContactMessages)**
**Ne İşe Yarar:** İletişim formu mesajlarını yönetir
**Frontend'te Nerede:** İletişim formu
**Durum:** ✅ Aktif kullanılıyor

### ❓ KULLANILIYOR MU?

#### 3. **Blog Yazıları (BlogPosts)**
**Ne İşe Yarar:** Blog yazılarını yönetir
**Frontend'te:** `app/(site)/blog/[slug]/page.tsx` var
**Durum:** ✅ Blog sayfası var, kullanılıyor olabilir

#### 4. **Sabit Sayfalar (StaticPages)**
**Ne İşe Yarar:** Hakkımızda, İletişim gibi sabit sayfaları yönetir
**Durum:** ❓ Sabit sayfalar başka şekilde yönetiliyorsa kullanılmıyor olabilir

---

## 🔔 BİLDİRİMLER

### ✅ KULLANILIYOR:

#### 1. **Bildirimler (Notifications)**
**Ne İşe Yarar:** Kullanıcı bildirimlerini yönetir
**Frontend'te Nerede:** NotificationBell component, bildirim sistemi
**API:** `/api/notifications`
**Durum:** ✅ Aktif kullanılıyor

#### 2. **Ayarlar (NotificationSettings)**
**Ne İşe Yarar:** Bildirim tercihlerini yönetir (e-posta bildirimleri, SMS bildirimleri vb.)
**Frontend'te Nerede:** Account sayfası, bildirim ayarları
**Durum:** ✅ Aktif kullanılıyor

#### 3. **Fiyat Uyarıları (PriceAlerts)**
**Ne İşe Yarar:** Fiyat düşüş uyarılarını yönetir
**Frontend'te Nerede:** Account sayfası, fiyat takibi
**Durum:** ✅ Aktif kullanılıyor

#### 4. **Stok Uyarıları (StockAlerts)**
**Ne İşe Yarar:** Stok geldiğinde bildirim gönderir
**Frontend'te Nerede:** Ürün sayfasında stok uyarısı butonu
**Durum:** ✅ Aktif kullanılıyor

---

## 🔍 ARAMA VE ANALYTICS

### ✅ KULLANILIYOR:

#### 1. **Arama Geçmişi (SearchHistories)**
**Ne İşe Yarar:** Kullanıcı arama geçmişini yönetir
**Frontend'te Nerede:** Arama kutusunda geçmiş aramalar
**Durum:** ✅ Aktif kullanılıyor

#### 2. **Son Görüntülenen (RecentlyVieweds)**
**Ne İşe Yarar:** Kullanıcının son görüntülediği ürünleri yönetir
**Frontend'te Nerede:** Account sayfası, son görüntülenenler bölümü
**Durum:** ✅ Aktif kullanılıyor

#### 3. **Fiyat Geçmişi (PriceHistories)**
**Ne İşe Yarar:** Ürün fiyat değişim geçmişini yönetir
**Frontend'te Nerede:** Ürün sayfasında fiyat grafiği
**Durum:** ✅ Aktif kullanılıyor

### ⚠️ ADMIN/ANALYTICS:

#### 4. **Arama İndeksleri (SearchIndices)**
**Ne İşe Yarar:** Arama indeksleme için kullanılır
**Durum:** ⚠️ Backend only, frontend'te görünmez

#### 5. **Arama Logları (SearchLogs)**
**Ne İşe Yarar:** Arama analitiği için loglar
**Durum:** ⚠️ Admin only, analitik için

#### 6. **Satıcı Analizleri (VendorAnalytics)**
**Ne İşe Yarar:** Satıcı analitik verilerini yönetir
**Durum:** ⚠️ Admin only

#### 7. **Aktivite Logları (ActivityLogs)**
**Ne İşe Yarar:** Sistem aktivite loglarını yönetir
**Durum:** ⚠️ Admin only

#### 8. **Ürün Canlı İstatistikleri (ProductLiveStats)**
**Ne İşe Yarar:** Ürün canlı istatistiklerini yönetir
**Durum:** ⚠️ Admin only

#### 9. **Satıcı SLA Metrikleri (VendorSlaMetrics)**
**Ne İşe Yarar:** Satıcı SLA takibini yönetir
**Durum:** ⚠️ Admin only

#### 10. **Satıcı Günlük İstatistikleri (VendorDailyStats)**
**Ne İşe Yarar:** Günlük satıcı istatistiklerini yönetir
**Durum:** ⚠️ Admin only

#### 11. **Platform Gelir Raporları (PlatformRevenueReports)**
**Ne İşe Yarar:** Platform gelir raporlarını yönetir
**Durum:** ⚠️ Admin only

---

## ⚙️ SİSTEM AYARLARI

### ✅ KULLANILIYOR:

#### 1. **Çeviriler (Translations)**
**Ne İşe Yarar:** Çoklu dil desteği için çevirileri yönetir
**Frontend'te Nerede:** i18n sistemi
**Durum:** ✅ Aktif kullanılıyor

---

## 💰 FİNANS VE FATURALAR

### ⚠️ ADMIN ONLY:

#### 1. **Faturalar (Invoices)**
**Ne İşe Yarar:** Fatura yönetimini yapar
**Durum:** ⚠️ Sadece admin panelinde görünür

---

## 🚚 KARGO VE LOJİSTİK

### ✅ KULLANILIYOR:

#### 1. **Kargo Entegrasyonları (CargoIntegrations)**
**Ne İşe Yarar:** Kargo firmaları entegrasyonlarını yönetir
**Durum:** ✅ Backend'te aktif

#### 2. **Kargo Firmaları (ShippingCompanies)**
**Ne İşe Yarar:** Kargo firmalarını yönetir
**Durum:** ✅ Backend'te aktif, sipariş oluşturma sırasında kullanılıyor

---

## 🔒 KVKK VE UYUMLULUK

### ✅ KULLANILIYOR:

#### 1. **Kullanıcı Onayları (UserConsents)**
**Ne İşe Yarar:** Cookie consent, KVKK onaylarını yönetir
**Frontend'te Nerede:** Cookie consent popup, KVKK onayları
**Durum:** ✅ Aktif kullanılıyor

#### 2. **Veri Silme İstekleri (DataDeletionRequests)**
**Ne İşe Yarar:** KVKK veri silme isteklerini yönetir
**Frontend'te Nerede:** Account sayfası, veri silme talebi
**Durum:** ✅ Aktif kullanılıyor

---

## 📊 ÖZET TABLO

| Resource | Durum | Frontend'te Kullanılıyor mu? | Admin Only? | Öneri |
|----------|-------|------------------------------|-------------|-------|
| **Ürünler** | ✅ | Evet | Hayır | Kalmalı |
| **Kategoriler** | ✅ | Evet | Hayır | Kalmalı |
| **PDP Düzenleri** | ✅ | Evet | Hayır | Kalmalı |
| **PDP Blokları** | ❓ | Hayır | Hayır | **Kaldırılabilir** (PdpLayouts yeterliyse) |
| **Ürün Paketleri** | ❓ | Hayır | Hayır | **Kaldırılabilir** |
| **Ürün Garantileri** | ✅ | Evet | Hayır | Kalmalı |
| **Çekilişler** | ❓ | Bilinmiyor | Hayır | Kontrol edilmeli |
| **İstek Listeleri** | ❓ | Bilinmiyor | Hayır | Kontrol edilmeli (Favorites yeterliyse kaldırılabilir) |
| **Blog Yazıları** | ✅ | Evet | Hayır | Kalmalı |
| **Sabit Sayfalar** | ❓ | Bilinmiyor | Hayır | Kontrol edilmeli |
| **Ürün SSS** | ✅ | Evet | Hayır | Kalmalı |

---

## 🎯 ÖNERİLER

1. **PDP Blokları vs PDP Düzenleri:** İkisi de farklı amaçlara hizmet ediyor ama kullanıcı karışıklık yaşıyor. ProductBlocks kullanılmıyorsa kaldırılabilir.

2. **Admin Only Resource'lar:** Bu resource'lar frontend'te görünmez ama admin panelinde gerekli. Bunlar kalmalı.

3. **Analytics Resource'ları:** Çoğu admin only, bunlar kalmalı.

4. **Kullanılmayan Özellikler:** Çekiliş, wishlist gibi özellikler kullanılmıyorsa kaldırılabilir veya gelecekte kullanılacaksa saklanabilir.

5. **Duplicate Kontrolü:** PDP Blokları ve PDP Düzenleri arasındaki fark netleştirilmeli veya birleştirilmeli.
