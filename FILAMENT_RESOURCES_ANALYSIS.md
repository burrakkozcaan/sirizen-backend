# Filament Admin Panel Resource Analizi

## 📊 KULLANILAN RESOURCE'LAR (Frontend'te Aktif)

### URUN_YONETIMI (Ürün Yönetimi)

#### ✅ KULLANILIYOR:
1. **Ürünler (Products)** - ✅ KULLANILIYOR
   - Frontend: `/product/[slug]`, ProductClient, ProductCard
   - API: `/api/products`, `/api/pdp/{slug}`
   - Açıklama: Ana ürün yönetimi, frontend'te aktif kullanılıyor

2. **Kategoriler (Categories)** - ✅ KULLANILIYOR
   - Frontend: `/category/[slug]`, CategoryClient, MegaMenu
   - API: `/api/categories`
   - Açıklama: Kategori sayfaları ve navigasyon için kullanılıyor

3. **Özellikler (Attributes)** - ✅ KULLANILIYOR
   - Frontend: ProductClient, variant selector, filtreler
   - API: Ürün detaylarında attributes gösteriliyor
   - Açıklama: Ürün özellikleri ve varyant seçimi için kullanılıyor

4. **PDP Düzenleri (PdpLayouts)** - ✅ KULLANILIYOR
   - Frontend: PdpEngine, PdpEngineV2, PdpBlockRenderer
   - API: `/api/pdp/{slug}` içinde layout bilgisi geliyor
   - Açıklama: Kategori gruplarına göre PDP sayfasının blok dizilimini belirler
   - Model: `category_group_id` ile kategori gruplarına bağlı, `layout_config` içinde blok sıralaması

5. **Filtre Yapılandırmaları (FilterConfigs)** - ✅ KULLANILIYOR
   - Frontend: CategoryClient, useFilters hook
   - API: `/api/categories/{slug}` içinde filters geliyor
   - Açıklama: Kategori sayfalarındaki filtreleme için kullanılıyor

6. **Rozet Tanımları (BadgeDefinitions)** - ✅ KULLANILIYOR
   - Frontend: ProductCard, ProductClient, badges gösterimi
   - API: `/api/pdp/{slug}/badges`
   - Açıklama: Ürün rozetleri (Çok Satan, Yeni, İndirimli vb.) için kullanılıyor

7. **Rozet Kuralları (BadgeRules)** - ✅ KULLANILIYOR
   - Backend: BadgeDefinitions ile birlikte çalışır
   - Açıklama: Rozetlerin otomatik hesaplanması için kurallar

8. **Sosyal Kanıt Kuralları (SocialProofRules)** - ✅ KULLANILIYOR
   - Frontend: ProductClient, social proof gösterimi
   - API: `/api/pdp/{slug}/social-proof`
   - Açıklama: "3.2K kişinin sepetinde" gibi sosyal kanıt mesajları için

9. **Benzer Ürünler (SimilarProducts)** - ✅ KULLANILIYOR
   - Frontend: ProductClient, related products section
   - API: `/api/pdp/{slug}/related`
   - Açıklama: Ürün detay sayfasında benzer ürünler gösterimi

10. **Ürün İçe Aktarma Logları (ProductImportLogs)** - ⚠️ ADMIN ONLY
    - Açıklama: Toplu ürün içe aktarma işlemlerini loglar, frontend'te görünmez

#### ⚠️ DUPLICATE/KARIŞIK:
11. **PDP Blokları (ProductBlocks)** - ⚠️ DUPLICATE Mİ?
    - Model: `product_id` ile ürüne özel bloklar
    - PdpLayouts ile karışıklık var
    - **AÇIKLAMA**: 
      - **PdpLayouts**: Kategori gruplarına göre genel layout (hangi bloklar nerede gösterilecek)
      - **ProductBlocks**: Ürüne özel bloklar (belirli bir ürün için özel blok içeriği)
    - **ÖNERİ**: ProductBlocks kullanılmıyorsa kaldırılabilir veya birleştirilebilir

12. **Ürün Paketleri (ProductBundles)** - ❓ KULLANILIYOR MU?
    - Frontend'te bundle gösterimi yok gibi görünüyor
    - **ÖNERİ**: Kullanılmıyorsa kaldırılabilir veya gelecekte kullanılacaksa saklanabilir

13. **Ürün Garantileri (ProductGuarantees)** - ❓ KULLANILIYOR MU?
    - Frontend'te guarantee gösterimi yok gibi görünüyor
    - **ÖNERİ**: Kullanılmıyorsa kaldırılabilir

14. **Hızlı Linkler (QuickLinks)** - ✅ KULLANILIYOR
    - Frontend: QuickLinks component, ana sayfa
    - API: `/api/quick-links`
    - Açıklama: Ana sayfadaki hızlı erişim linkleri

15. **Kategori Grupları (CategoryGroups)** - ✅ KULLANILIYOR
    - Backend: PdpLayouts ile ilişkili
    - Açıklama: Kategorileri gruplamak için (Giyim, Elektronik vb.)

16. **Özellik Setleri (AttributeSets)** - ✅ KULLANILIYOR
    - Backend: Attributes ile ilişkili
    - Açıklama: Özellikleri gruplamak için (Renk, Beden vb.)

17. **Ürün Onayları (ProductApprovals)** - ⚠️ ADMIN ONLY
    - Açıklama: Ürün onay süreci için, frontend'te görünmez

### SATICI_YONETIMI (Satıcı Yönetimi)

#### ✅ KULLANILIYOR:
1. **Satıcılar (Vendors)** - ✅ KULLANILIYOR
   - Frontend: `/store/[slug]`, VendorClient
   - API: `/api/vendors`, `/api/stores/{slug}`
   - Açıklama: Satıcı sayfaları ve profil yönetimi

2. **Satıcı Rozetleri (SellerBadges)** - ✅ KULLANILIYOR
   - Frontend: VendorClient, satıcı profilinde rozetler
   - Açıklama: Satıcı rozetleri (Güvenilir Satıcı, Hızlı Kargo vb.)

#### ⚠️ ADMIN/ANALYTICS:
3. **Ödemeler (VendorPayouts)** - ⚠️ ADMIN ONLY
   - Açıklama: Satıcı ödemeleri, frontend'te görünmez

4. **Bakiyeler (VendorBalances)** - ⚠️ ADMIN ONLY
   - Açıklama: Satıcı bakiyeleri, frontend'te görünmez

5. **Seviyeler (VendorTiers)** - ⚠️ ADMIN ONLY
   - Açıklama: Satıcı seviye sistemi, frontend'te görünmez

6. **Puanlar (VendorScores)** - ⚠️ ADMIN ONLY
   - Açıklama: Satıcı puanlama sistemi, frontend'te görünmez

7. **Takipçiler (VendorFollowers)** - ❓ KULLANILIYOR MU?
   - Frontend'te takipçi gösterimi var mı kontrol edilmeli
   - VendorClient'ta follower_count gösteriliyor ama bu model kullanılıyor mu?

8. **Satıcı Belgeleri (VendorDocuments)** - ⚠️ ADMIN ONLY
   - Açıklama: Satıcı belgeleri yönetimi

9. **Cezalar (VendorPenalties)** - ⚠️ ADMIN ONLY
   - Açıklama: Satıcı ceza yönetimi

10. **Performans Logları (VendorPerformanceLogs)** - ⚠️ ADMIN ONLY
    - Açıklama: Satıcı performans takibi

### SIPARIS_YONETIMI (Sipariş Yönetimi)

#### ✅ KULLANILIYOR:
1. **Siparişler (Orders)** - ✅ KULLANILIYOR
   - Frontend: `/orders`, `/order/[id]`, OrdersClient
   - API: `/api/orders`
   - Açıklama: Kullanıcı siparişleri

2. **Sipariş Kalemleri (OrderItems)** - ✅ KULLANILIYOR
   - Frontend: OrderDetailClient, sipariş detaylarında
   - Açıklama: Sipariş içindeki ürünler

3. **Gönderi (Shipments)** - ✅ KULLANILIYOR
   - Frontend: OrderDetailClient, kargo takibi
   - Açıklama: Kargo gönderileri

4. **İadeler (Refunds)** - ✅ KULLANILIYOR
   - Frontend: OrderDetailClient, iade işlemleri
   - Açıklama: İade yönetimi

5. **İade Politikaları (ReturnPolicies)** - ✅ KULLANILIYOR
   - Frontend: Ürün sayfasında iade bilgisi
   - Açıklama: İade kuralları

6. **İade Görselleri (ReturnImages)** - ✅ KULLANILIYOR
   - Frontend: İade formunda görsel yükleme
   - Açıklama: İade görselleri

7. **Anlaşmazlıklar (Disputes)** - ✅ KULLANILIYOR
   - Frontend: OrderDetailClient, anlaşmazlık açma
   - Açıklama: Sipariş anlaşmazlıkları

8. **Kargo Kuralları (ShippingRules)** - ✅ KULLANILIYOR
   - Backend: Sipariş oluşturma sırasında kargo hesaplama
   - Açıklama: Kargo kuralları ve ücretleri

### ODEME_VE_KOMISYON (Ödeme ve Komisyon)

#### ✅ KULLANILIYOR:
1. **Ödemeler (Payments)** - ✅ KULLANILIYOR
   - Frontend: Checkout, ödeme işlemleri
   - API: `/api/payments`
   - Açıklama: Ödeme kayıtları

2. **Komisyonlar (Commissions)** - ⚠️ ADMIN ONLY
   - Açıklama: Platform komisyonları, frontend'te görünmez

3. **Gateway Ayarları (PaymentGatewaySettings)** - ⚠️ ADMIN ONLY
   - Açıklama: Ödeme gateway ayarları

### KULLANICI_YONETIMI (Kullanıcı Yönetimi)

#### ✅ KULLANILIYOR:
1. **Cüzdanlar (UserWallets)** - ❓ KULLANILIYOR MU?
   - Frontend'te cüzdan gösterimi var mı kontrol edilmeli
   - Account sayfasında wallet var mı?

2. **Cüzdan İşlemleri (WalletTransactions)** - ❓ KULLANILIYOR MU?
   - Cüzdan kullanılıyorsa bu da kullanılıyor olmalı

3. **Üyelik Programları (MembershipPrograms)** - ❓ KULLANILIYOR MU?
   - Frontend'te üyelik programı gösterimi var mı?

4. **Kullanıcı Üyelikleri (UserMemberships)** - ❓ KULLANILIYOR MU?
   - Kullanıcının üyelik durumu

### MUSTERI_YONETIMI (Müşteri Yönetimi)

#### ✅ KULLANILIYOR:
1. **Kullanıcılar (Users)** - ✅ KULLANILIYOR
   - Frontend: Account sayfası, profil yönetimi
   - API: `/api/user`, `/api/auth`
   - Açıklama: Kullanıcı hesapları

2. **Adresler (Addresses)** - ✅ KULLANILIYOR
   - Frontend: `/account/addresses`, AddressesSection
   - API: `/api/addresses`
   - Açıklama: Kullanıcı adresleri

3. **Canlı Destek (CrispConversations)** - ✅ KULLANILIYOR
   - Frontend: Crisp chat widget
   - Açıklama: Canlı destek konuşmaları

### INCELEME_VE_SORULAR (İnceleme ve Sorular)

#### ✅ KULLANILIYOR:
1. **Ürün Soruları (ProductQuestions)** - ✅ KULLANILIYOR
   - Frontend: ProductQA component
   - API: `/api/products/{id}/questions`
   - Açıklama: Ürün soru-cevap

2. **Yorum Görselleri (ReviewImages)** - ✅ KULLANILIYOR
   - Frontend: ProductReviews, yorum görselleri
   - Açıklama: Yorumlara eklenen görseller

3. **Faydalı Oylar (ReviewHelpfulVotes)** - ✅ KULLANILIYOR
   - Frontend: ProductReviews, "Bu yorum faydalı mı?" oylama
   - Açıklama: Yorum faydalılık oyları

4. **Satıcı Yorumları (SellerReviews)** - ✅ KULLANILIYOR
   - Frontend: VendorClient, satıcı yorumları
   - Açıklama: Satıcı değerlendirmeleri

5. **Ürün SSS (ProductFaqs)** - ❓ KULLANILIYOR MU?
   - Frontend'te FAQ gösterimi var mı kontrol edilmeli

### KAMPANYA_VE_KUPONLAR (Kampanya ve Kuponlar)

#### ✅ KULLANILIYOR:
1. **Kampanyalar (Campaigns)** - ✅ KULLANILIYOR
   - Frontend: Ana sayfa hero, campaign sayfaları
   - API: `/api/campaigns/active`, `/api/campaigns/hero`
   - Açıklama: Kampanya yönetimi

2. **Kuponlar (Coupons)** - ✅ KULLANILIYOR
   - Frontend: Checkout, kupon kodu girişi
   - API: `/api/coupons/validate`
   - Açıklama: Kupon yönetimi

3. **Kupon Kullanımları (CouponUsages)** - ✅ KULLANILIYOR
   - Backend: Kupon kullanım takibi
   - Açıklama: Hangi kuponlar kullanıldı

### PAZARLAMA_VE_CEKILISLER (Pazarlama ve Çekilişler)

#### ❓ KULLANILIYOR MU?
1. **Çekilişler (Raffles)** - ❓ KULLANILIYOR MU?
   - Frontend'te çekiliş gösterimi var mı?

2. **Çekiliş Katılımları (RaffleEntries)** - ❓ KULLANILIYOR MU?
   - Çekilişler kullanılıyorsa bu da kullanılıyor

3. **Çekiliş Kazananları (RaffleWinners)** - ❓ KULLANILIYOR MU?
   - Çekilişler kullanılıyorsa bu da kullanılıyor

### ALISVERIS_SEPETI (Alışveriş Sepeti)

#### ✅ KULLANILIYOR:
1. **Sepetler (Carts)** - ✅ KULLANILIYOR
   - Frontend: CartContext, sepet sayfası
   - API: `/api/cart`
   - Açıklama: Kullanıcı sepetleri

2. **Sepet Kalemleri (CartItems)** - ✅ KULLANILIYOR
   - Frontend: Cart sayfası, sepet içeriği
   - Açıklama: Sepet içindeki ürünler

### FAVORI_VE_LISTELER (Favori ve Listeler)

#### ✅ KULLANILIYOR:
1. **Favoriler (Favorites)** - ✅ KULLANILIYOR
   - Frontend: FavoritesContext, `/favorites` sayfası
   - API: `/api/favorites`
   - Açıklama: Kullanıcı favorileri

2. **İstek Listeleri (Wishlists)** - ❓ KULLANILIYOR MU?
   - Frontend'te wishlist gösterimi var mı?
   - Favorites ile aynı şey mi?

3. **İstek Kalemleri (WishlistItems)** - ❓ KULLANILIYOR MU?
   - Wishlists kullanılıyorsa bu da kullanılıyor

### KATALOG_YONETIMI (Katalog Yönetimi)

#### ✅ KULLANILIYOR:
1. **Özellik Setleri (AttributeSets)** - ✅ KULLANILIYOR
   - Backend: Attributes ile ilişkili
   - Açıklama: Özellik grupları

2. **Markalar (Brands)** - ✅ KULLANILIYOR
   - Frontend: CategoryClient, filtrelerde marka
   - API: `/api/brands`
   - Açıklama: Marka yönetimi

### ICERIK_YONETIMI (İçerik Yönetimi)

#### ✅ KULLANILIYOR:
1. **Satıcı Sayfaları (SellerPages)** - ✅ KULLANILIYOR
   - Frontend: VendorClient, satıcı hakkında sayfası
   - Açıklama: Satıcı özel sayfaları

2. **Blog Yazıları (BlogPosts)** - ❓ KULLANILIYOR MU?
   - Frontend'te blog sayfası var mı?

3. **Sabit Sayfalar (StaticPages)** - ❓ KULLANILIYOR MU?
   - Hakkımızda, İletişim gibi sayfalar için kullanılıyor mu?

4. **İletişim Mesajları (ContactMessages)** - ✅ KULLANILIYOR
   - Frontend: İletişim formu
   - Açıklama: İletişim mesajları

### BILDIRIMLER (Bildirimler)

#### ✅ KULLANILIYOR:
1. **Bildirimler (Notifications)** - ✅ KULLANILIYOR
   - Frontend: NotificationBell, bildirim sistemi
   - API: `/api/notifications`
   - Açıklama: Kullanıcı bildirimleri

2. **Ayarlar (NotificationSettings)** - ✅ KULLANILIYOR
   - Frontend: Account sayfası, bildirim ayarları
   - Açıklama: Bildirim tercihleri

3. **Fiyat Uyarıları (PriceAlerts)** - ✅ KULLANILIYOR
   - Frontend: Account sayfası, fiyat takibi
   - Açıklama: Fiyat düşüş uyarıları

4. **Stok Uyarıları (StockAlerts)** - ✅ KULLANILIYOR
   - Frontend: Ürün sayfası, stok uyarısı
   - Açıklama: Stok geldiğinde bildirim

### ARAMA_VE_ANALYTICS (Arama ve Analytics)

#### ⚠️ ANALYTICS/LOG:
1. **Arama Geçmişi (SearchHistories)** - ✅ KULLANILIYOR
   - Frontend: Arama geçmişi gösterimi
   - Açıklama: Kullanıcı arama geçmişi

2. **Arama İndeksleri (SearchIndices)** - ⚠️ BACKEND ONLY
   - Açıklama: Arama indeksleme, frontend'te görünmez

3. **Arama Logları (SearchLogs)** - ⚠️ ANALYTICS
   - Açıklama: Arama analitiği

4. **Son Görüntülenen (RecentlyVieweds)** - ✅ KULLANILIYOR
   - Frontend: Account sayfası, son görüntülenenler
   - Açıklama: Kullanıcı geçmişi

5. **Satıcı Analizleri (VendorAnalytics)** - ⚠️ ADMIN ONLY
   - Açıklama: Satıcı analitik verileri

6. **Aktivite Logları (ActivityLogs)** - ⚠️ ADMIN ONLY
   - Açıklama: Sistem aktivite logları

7. **Fiyat Geçmişi (PriceHistories)** - ✅ KULLANILIYOR
   - Frontend: Ürün sayfası, fiyat grafiği
   - Açıklama: Fiyat değişim geçmişi

8. **Ürün Canlı İstatistikleri (ProductLiveStats)** - ⚠️ ADMIN ONLY
   - Açıklama: Ürün canlı istatistikleri

9. **Satıcı SLA Metrikleri (VendorSlaMetrics)** - ⚠️ ADMIN ONLY
   - Açıklama: Satıcı SLA takibi

10. **Satıcı Günlük İstatistikleri (VendorDailyStats)** - ⚠️ ADMIN ONLY
    - Açıklama: Günlük satıcı istatistikleri

11. **Platform Gelir Raporları (PlatformRevenueReports)** - ⚠️ ADMIN ONLY
    - Açıklama: Platform gelir raporları

### SISTEM_AYARLARI (Sistem Ayarları)

#### ✅ KULLANILIYOR:
1. **Çeviriler (Translations)** - ✅ KULLANILIYOR
   - Frontend: i18n sistemi
   - Açıklama: Çoklu dil desteği

### FINANS_VE_FATURALAR (Finans ve Faturalar)

#### ⚠️ ADMIN ONLY:
1. **Faturalar (Invoices)** - ⚠️ ADMIN ONLY
   - Açıklama: Fatura yönetimi, frontend'te görünmez

### KARGO_VE_LOJISTIK (Kargo ve Lojistik)

#### ✅ KULLANILIYOR:
1. **Kargo Entegrasyonları (CargoIntegrations)** - ✅ KULLANILIYOR
   - Backend: Kargo entegrasyonları
   - Açıklama: Kargo firmaları entegrasyonu

2. **Kargo Firmaları (ShippingCompanies)** - ✅ KULLANILIYOR
   - Backend: Sipariş oluşturma sırasında kargo seçimi
   - Açıklama: Kargo firmaları listesi

### KVKK_VE_UYUMLULUK (KVKK ve Uyumluluk)

#### ⚠️ COMPLIANCE:
1. **Kullanıcı Onayları (UserConsents)** - ✅ KULLANILIYOR
   - Frontend: Cookie consent, KVKK onayları
   - Açıklama: Kullanıcı onayları

2. **Veri Silme İstekleri (DataDeletionRequests)** - ✅ KULLANILIYOR
   - Frontend: Account sayfası, veri silme talebi
   - Açıklama: KVKK veri silme istekleri

---

## 🔍 DUPLICATE/KARIŞIK RESOURCE'LAR

### 1. PDP Blokları vs PDP Düzenleri
- **PdpLayouts**: Kategori gruplarına göre genel layout (hangi bloklar nerede gösterilecek)
- **ProductBlocks**: Ürüne özel bloklar (belirli bir ürün için özel blok içeriği)
- **ÖNERİ**: ProductBlocks kullanılmıyorsa kaldırılabilir veya birleştirilebilir

### 2. Favoriler vs İstek Listeleri
- **Favorites**: Basit favori sistemi (tek bir favori listesi)
- **Wishlists**: Çoklu liste sistemi (farklı istek listeleri)
- **ÖNERİ**: Eğer çoklu liste özelliği kullanılmıyorsa Wishlists kaldırılabilir

---

## ❌ KULLANILMAYAN/KALDIRILABİLECEK RESOURCE'LAR

### Önerilen Kaldırılacaklar:
1. **ProductBlocks** - Eğer PdpLayouts yeterliyse
2. **ProductBundles** - Frontend'te kullanılmıyorsa
3. **ProductGuarantees** - Frontend'te kullanılmıyorsa
4. **Raffles/RaffleEntries/RaffleWinners** - Çekiliş özelliği kullanılmıyorsa
5. **Wishlists/WishlistItems** - Eğer Favorites yeterliyse
6. **BlogPosts** - Blog özelliği kullanılmıyorsa
7. **StaticPages** - Sabit sayfalar başka şekilde yönetiliyorsa
8. **ProductFaqs** - Eğer ProductQuestions yeterliyse

---

## 📝 ÖNERİLER

1. **PDP Blokları ve PDP Düzenleri**: İkisi de farklı amaçlara hizmet ediyor ama kullanıcı karışıklık yaşıyor. Birleştirilebilir veya daha net isimlendirilebilir.

2. **Admin Only Resource'lar**: Bu resource'lar frontend'te görünmez ama admin panelinde gerekli. Bunlar kalmalı.

3. **Analytics Resource'ları**: Çoğu admin only, bunlar kalmalı.

4. **Kullanılmayan Özellikler**: Çekiliş, blog, wishlist gibi özellikler kullanılmıyorsa kaldırılabilir veya gelecekte kullanılacaksa saklanabilir.
