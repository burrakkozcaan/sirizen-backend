<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Category;
use App\Models\Commission;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Vendor;
use App\Models\VendorTier;
use Illuminate\Support\Facades\Log;

class CommissionService
{
    /**
     * Komisyon oranını hesapla (Trendyol mantığı)
     * 
     * Öncelik sırası:
     * 1. Ürün bazlı özel komisyon oranı (varsa)
     * 2. Kategori bazlı komisyon oranı
     * 3. Vendor tier bazlı komisyon indirimi
     * 4. Varsayılan komisyon oranı
     */
    public function calculateCommissionRate(
        Product $product,
        Vendor $vendor,
        ?Category $category = null
    ): float {
        // 1. Ürün bazlı özel komisyon oranı
        if ($product->custom_commission_rate) {
            return (float) $product->custom_commission_rate;
        }

        // 2. Kategori bazlı komisyon oranı
        if (!$category) {
            $category = $product->category;
        }

        $categoryCommissionRate = $category?->commission_rate;
        
        // 3. Vendor tier bazlı komisyon indirimi
        $vendorTier = $vendor->tier;
        $tierCommissionDiscount = $vendorTier?->commission_rate ?? 0;

        // Kategori komisyon oranı varsa, tier indirimini uygula
        if ($categoryCommissionRate) {
            // Tier commission_rate bir indirim oranıysa (örn: -2% = 0.02)
            // Kategori komisyonundan tier indirimini çıkar
            $finalRate = $categoryCommissionRate - ($tierCommissionDiscount / 100);
            
            // Negatif olamaz
            return max(0, $finalRate);
        }

        // 4. Varsayılan komisyon oranı (kategori yoksa)
        $defaultCommissionRate = config('marketplace.default_commission_rate', 10.0);
        
        // Tier indirimini uygula
        $finalRate = $defaultCommissionRate - ($tierCommissionDiscount / 100);
        
        return max(0, $finalRate);
    }

    /**
     * Komisyon tutarını hesapla
     */
    public function calculateCommissionAmount(
        float $productPrice,
        int $quantity,
        float $commissionRate
    ): float {
        $totalAmount = $productPrice * $quantity;
        
        // KDV hariç tutar (KDV %20 varsayılıyor)
        $vatRate = config('marketplace.vat_rate', 0.20);
        $amountExcludingVat = $totalAmount / (1 + $vatRate);
        
        // Komisyon tutarı
        $commissionAmount = $amountExcludingVat * ($commissionRate / 100);
        
        return round($commissionAmount, 2);
    }

    /**
     * Order item için komisyon kaydı oluştur
     */
    public function createCommission(OrderItem $orderItem): Commission
    {
        $product = $orderItem->product;
        $vendor = $orderItem->vendor;
        $category = $product->category;

        // Komisyon oranını hesapla
        $commissionRate = $this->calculateCommissionRate($product, $vendor, $category);

        // Komisyon tutarını hesapla
        $grossAmount = $orderItem->price * $orderItem->quantity;
        $commissionAmount = $this->calculateCommissionAmount(
            $orderItem->price,
            $orderItem->quantity,
            $commissionRate
        );

        $netAmount = $grossAmount - $commissionAmount;

        // Komisyon kaydı oluştur
        // payment_id henüz yoksa null olabilir, payment oluşturulduğunda güncellenir
        $commission = Commission::create([
            'payment_id' => $orderItem->order->payments()->first()?->id ?? null,
            'vendor_id' => $vendor->id,
            'order_item_id' => $orderItem->id,
            'gross_amount' => $grossAmount,
            'commission_rate' => $commissionRate,
            'commission_amount' => $commissionAmount,
            'net_amount' => $netAmount,
            'currency' => 'TRY',
            'status' => 'pending', // pending, paid, refunded
        ]);

        Log::info('Commission created', [
            'order_item_id' => $orderItem->id,
            'vendor_id' => $vendor->id,
            'commission_rate' => $commissionRate,
            'commission_amount' => $commissionAmount,
            'net_amount' => $netAmount,
        ]);

        return $commission;
    }

    /**
     * Order için tüm komisyonları oluştur
     */
    public function createCommissionsForOrder(\App\Models\Order $order): void
    {
        foreach ($order->items as $orderItem) {
            $this->createCommission($orderItem);
        }
    }

    /**
     * Komisyon ödemesini işle (split payment)
     */
    public function processCommissionPayment(Commission $commission): void
    {
        // Ödeme başarılı olduğunda komisyon durumunu güncelle
        $commission->update([
            'status' => 'paid',
        ]);

        // Vendor'a ödeme yapılacak tutarı kaydet
        // (Bu kısım ödeme gateway entegrasyonu ile yapılır)
        Log::info('Commission payment processed', [
            'commission_id' => $commission->id,
            'vendor_id' => $commission->vendor_id,
            'net_amount' => $commission->net_amount,
        ]);
    }

    /**
     * İade durumunda komisyonu geri al
     */
    public function refundCommission(Commission $commission, float $refundAmount): void
    {
        $refundedAmount = min($refundAmount, $commission->commission_amount);
        
        $commission->update([
            'refunded_amount' => $refundedAmount,
            'status' => $refundedAmount >= $commission->commission_amount ? 'refunded' : 'partially_refunded',
        ]);

        Log::info('Commission refunded', [
            'commission_id' => $commission->id,
            'refunded_amount' => $refundedAmount,
        ]);
    }
}
