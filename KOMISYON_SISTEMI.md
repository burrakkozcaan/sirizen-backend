# Trendyol Tarzı Komisyon Sistemi ✅

## 📋 Özellikler

### 1. **Çok Katmanlı Komisyon Hesaplama**

Komisyon oranı belirlenirken öncelik sırası:

1. **Ürün Bazlı Özel Komisyon** (en yüksek öncelik)
   - `products.custom_commission_rate`
   - Belirli ürünler için özel komisyon oranı

2. **Kategori Bazlı Komisyon**
   - `categories.commission_rate`
   - Her kategori için farklı komisyon oranı
   - Örnek: Elektronik %8, Giyim %12, Kozmetik %15

3. **Vendor Tier İndirimi**
   - `vendor_tiers.commission_rate`
   - Premium satıcılar için komisyon indirimi
   - Örnek: Premium tier -2% indirim

4. **Varsayılan Komisyon**
   - `config('marketplace.default_commission_rate')`
   - Kategori komisyonu yoksa kullanılır

### 2. **Komisyon Hesaplama Mantığı**

```php
// KDV hariç tutar hesaplama
$amountExcludingVat = $totalAmount / (1 + $vatRate);

// Komisyon tutarı
$commissionAmount = $amountExcludingVat * ($commissionRate / 100);

// Satıcıya ödenecek tutar
$netAmount = $grossAmount - $commissionAmount;
```

### 3. **Split Payment (Bölünmüş Ödeme)**

- Müşteriden alınan para: `gross_amount`
- Platform komisyonu: `commission_amount`
- Satıcıya ödenecek: `net_amount`

---

## 🗄️ Veritabanı Yapısı

### Categories Tablosu
```sql
commission_rate DECIMAL(5,2) NULL -- Kategori bazlı komisyon oranı (%)
```

### Products Tablosu
```sql
custom_commission_rate DECIMAL(5,2) NULL -- Ürün bazlı özel komisyon (%)
```

### Vendor Tiers Tablosu
```sql
commission_rate DECIMAL(5,2) NULL -- Tier bazlı komisyon indirimi (%)
```

### Commissions Tablosu
```sql
- payment_id (FK)
- vendor_id (FK)
- order_item_id (FK)
- gross_amount (Toplam tutar)
- commission_rate (Komisyon oranı %)
- commission_amount (Komisyon tutarı)
- net_amount (Satıcıya ödenecek)
- currency
- status (pending, paid, refunded)
- refunded_amount
```

---

## 💻 Kullanım

### Komisyon Servisi

```php
use App\Services\CommissionService;

$commissionService = new CommissionService();

// Order için komisyonları oluştur
$commissionService->createCommissionsForOrder($order);

// Tek bir order item için komisyon oluştur
$commission = $commissionService->createCommission($orderItem);

// Komisyon oranını hesapla
$rate = $commissionService->calculateCommissionRate($product, $vendor, $category);

// Komisyon tutarını hesapla
$amount = $commissionService->calculateCommissionAmount($price, $quantity, $rate);
```

### Order Oluşturulurken

Komisyonlar otomatik olarak hesaplanır ve kaydedilir:

```php
// OrderController.php
$order = Order::create([...]);

// Komisyonları hesapla ve kaydet
$commissionService = new CommissionService();
$commissionService->createCommissionsForOrder($order);
```

### Ödeme Başarılı Olduğunda

```php
// PaymentController.php
if ($payment->status === 'completed') {
    foreach ($order->commissions as $commission) {
        $commissionService->processCommissionPayment($commission);
    }
}
```

### İade Durumunda

```php
// Refund işlemi
$commissionService->refundCommission($commission, $refundAmount);
```

---

## ⚙️ Konfigürasyon

`.env` dosyasına ekleyin:

```env
MARKETPLACE_DEFAULT_COMMISSION_RATE=10.0
MARKETPLACE_VAT_RATE=0.20
MARKETPLACE_MIN_COMMISSION_AMOUNT=1.0
```

---

## 📊 Örnek Senaryolar

### Senaryo 1: Standart Komisyon
- Kategori: Elektronik (%8 komisyon)
- Vendor Tier: Standart (0% indirim)
- Ürün Fiyatı: 1000 TL
- KDV: %20

**Hesaplama:**
- KDV hariç: 1000 / 1.20 = 833.33 TL
- Komisyon: 833.33 * 0.08 = 66.67 TL
- Satıcıya ödenecek: 1000 - 66.67 = 933.33 TL

### Senaryo 2: Premium Tier İndirimi
- Kategori: Giyim (%12 komisyon)
- Vendor Tier: Premium (-2% indirim)
- Ürün Fiyatı: 500 TL

**Hesaplama:**
- Final komisyon oranı: 12% - 2% = 10%
- KDV hariç: 500 / 1.20 = 416.67 TL
- Komisyon: 416.67 * 0.10 = 41.67 TL
- Satıcıya ödenecek: 500 - 41.67 = 458.33 TL

### Senaryo 3: Özel Ürün Komisyonu
- Ürün: Özel komisyon %5
- Kategori: Elektronik (%8)
- Vendor Tier: Standart (0%)

**Hesaplama:**
- Ürün bazlı komisyon öncelikli: %5 kullanılır
- KDV hariç: 1000 / 1.20 = 833.33 TL
- Komisyon: 833.33 * 0.05 = 41.67 TL
- Satıcıya ödenecek: 1000 - 41.67 = 958.33 TL

---

## 🔄 İş Akışı

1. **Sipariş Oluşturulur**
   - Order ve OrderItem'lar kaydedilir
   - Komisyonlar otomatik hesaplanır ve kaydedilir

2. **Ödeme Yapılır**
   - Payment kaydı oluşturulur
   - Commission kayıtları payment_id ile ilişkilendirilir

3. **Ödeme Başarılı**
   - Commission status: `pending` → `paid`
   - Vendor balance güncellenir (gelecekte)

4. **İade Durumu**
   - Commission status: `paid` → `refunded`
   - Refunded amount kaydedilir

---

## ✅ Tamamlananlar

- ✅ CommissionService oluşturuldu
- ✅ Kategori bazlı komisyon sistemi
- ✅ Vendor tier indirim sistemi
- ✅ Ürün bazlı özel komisyon
- ✅ Otomatik komisyon hesaplama
- ✅ Order oluşturulurken komisyon kaydı
- ✅ İade durumu için komisyon geri alma

---

## 🚀 Sonraki Adımlar

1. **Filament Admin Panel**
   - Kategori komisyon oranlarını yönetme
   - Vendor tier komisyon indirimlerini yönetme
   - Ürün bazlı özel komisyon yönetme

2. **Vendor Balance Sistemi**
   - Satıcı bakiyesi takibi
   - Ödeme geçmişi

3. **Komisyon Raporları**
   - Platform komisyon raporları
   - Satıcı komisyon raporları

4. **Otomatik Ödeme**
   - Satıcılara otomatik ödeme
   - Ödeme takvimi
