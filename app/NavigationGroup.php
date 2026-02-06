<?php

namespace App;

enum NavigationGroup: string
{
    case URUN_YONETIMI = 'Ürün Yönetimi';
    case SATICI_YONETIMI = 'Satıcı Yönetimi';
    case SIPARIS_YONETIMI = 'Sipariş Yönetimi';
    case ODEME_VE_KOMISYON = 'Ödeme ve Komisyon';
    case KULLANICI_YONETIMI = 'Kullanıcı Yönetimi';
    case MUSTERI_YONETIMI = 'Müşteri Yönetimi';
    case INCELEME_VE_SORULAR = 'İnceleme ve Sorular';
    case KAMPANYA_VE_KUPONLAR = 'Kampanya ve Kuponlar';
    case PAZARLAMA_VE_CEKILISLER = 'Pazarlama ve Çekilişler';
    case ALISVERIS_SEPETI = 'Alışveriş Sepeti';
    case FAVORI_VE_LISTELER = 'Favori ve Listeler';
    case KATALOG_YONETIMI = 'Katalog Yönetimi';
    case ICERIK_YONETIMI = 'İçerik Yönetimi';
    case BILDIRIMLER = 'Bildirimler';
    case ARAMA_VE_ANALYTICS = 'Arama ve Analytics';
    case SISTEM_AYARLARI = 'Sistem Ayarları';
    case FINANS_VE_FATURALAR = 'Finans ve Faturalar';
    case KARGO_VE_LOJISTIK = 'Kargo ve Lojistik';
    case KVKK_VE_UYUMLULUK = 'KVKK ve Uyumluluk';
}
