# Özellik Setleri vs Özellikler - Fark Açıklaması

## 🔍 İKİSİ DE FARKLI AMA İLİŞKİLİ

### 📦 **Özellik Setleri (AttributeSets)** 
**Konum:** Katalog Yönetimi → Özellik Setleri

**Ne İşe Yarar:**
- Özellikleri **gruplamak** için kullanılır
- Kategorilere göre özellik setleri tanımlar
- Örnekler:
  - **"Renk Seti"** → İçinde: Kırmızı, Mavi, Yeşil, Siyah özellikleri var
  - **"Beden Seti"** → İçinde: XS, S, M, L, XL özellikleri var
  - **"Teknik Özellikler Seti"** → İçinde: Ekran Boyutu, RAM, Depolama özellikleri var

**Model:** `AttributeSet`
- `hasMany` Attribute (bir set içinde birden fazla özellik var)
- `belongsTo` CategoryGroup (kategori gruplarına göre setler)

**Kullanım Senaryosu:**
```
Giyim Kategorisi için:
  - Renk Seti (Kırmızı, Mavi, Yeşil)
  - Beden Seti (S, M, L, XL)
  - Materyal Seti (Pamuk, Polyester)

Elektronik Kategorisi için:
  - Teknik Özellikler Seti (Ekran Boyutu, RAM, Depolama)
  - Bağlantı Seti (WiFi, Bluetooth, USB)
```

---

### 🏷️ **Özellikler (Attributes)**
**Konum:** Ürün Yönetimi → Özellikler

**Ne İşe Yarar:**
- **Tekil özellik tanımlarını** yönetir
- Her özellik bir AttributeSet'e ait olmalı
- Örnekler:
  - **"Renk"** özelliği → Değerler: Kırmızı, Mavi, Yeşil
  - **"Beden"** özelliği → Değerler: S, M, L, XL
  - **"Ekran Boyutu"** özelliği → Değerler: 6.1", 6.7"

**Model:** `Attribute`
- `belongsTo` AttributeSet (her özellik bir sete ait)
- `hasMany` ProductAttributeValue (ürünlerde kullanılan değerler)

**Kullanım Senaryosu:**
```
Renk Seti içinde:
  - Renk özelliği (key: "color", label: "Renk", type: "select")
  - Renk özelliği (key: "secondary_color", label: "İkincil Renk", type: "select")

Beden Seti içinde:
  - Beden özelliği (key: "size", label: "Beden", type: "select")
```

---

## 📊 İLİŞKİ ŞEMASI

```
CategoryGroup (Kategori Grubu)
  └── AttributeSet (Özellik Seti)
        └── Attribute (Özellik)
              └── ProductAttributeValue (Ürün Değeri)
```

**Örnek:**
```
Giyim Kategorisi
  └── Renk Seti
        └── Renk özelliği
              └── Ürün 1: "Kırmızı"
              └── Ürün 2: "Mavi"
```

---

## ✅ SONUÇ

**İkisi de farklı ama birbirine bağlı:**
- **Özellik Setleri:** Gruplama yapar (kategorilere göre)
- **Özellikler:** Tekil özellik tanımları (setlerin içinde)

**Neden İkisi de Var?**
- Kategorilere göre farklı özellik setleri olabilir
- Aynı özellik farklı setlerde kullanılabilir
- Daha organize ve yönetilebilir yapı

**Öneri:** İkisi de kalmalı, çünkü:
- AttributeSet: Kategori bazlı özellik grupları
- Attribute: Tekil özellik tanımları
- İkisi birlikte çalışıyor

---

## 🎯 KULLANIM ÖRNEĞİ

**Admin Panel'de:**
1. **Katalog Yönetimi → Özellik Setleri** → "Renk Seti" oluştur
2. **Ürün Yönetimi → Özellikler** → "Renk" özelliğini oluştur ve "Renk Seti"ne bağla
3. **Ürünler** → Ürün düzenle → "Renk Seti"ni seç → "Renk" özelliğine "Kırmızı" değerini ver

**Frontend'te:**
- Ürün detay sayfasında "Renk Seti" gösterilir
- İçinde "Renk" özelliği ve değerleri (Kırmızı, Mavi) gösterilir
