<?php

namespace App\Services;

use App\Models\BadgeDefinition;
use App\Models\BadgeRule;
use App\Models\Product;
use App\Models\ProductBadgeSnapshot;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class BadgeService
{
    /**
     * Ürün için badge'leri hesapla ve cache'le
     */
    public function calculateBadges(Product $product, bool $save = true): Collection
    {
        $categoryGroupId = $product->category_group_id;

        // Tüm aktif kuralları getir (kategori grubuna özel veya genel)
        $rules = BadgeRule::with('badgeDefinition')
            ->where('is_active', true)
            ->where(function ($query) use ($categoryGroupId) {
                $query->whereNull('category_group_id')
                    ->orWhere('category_group_id', $categoryGroupId);
            })
            ->orderBy('priority', 'desc')
            ->get();

        $badges = collect();

        foreach ($rules as $rule) {
            if ($rule->evaluate($product)) {
                $badgeDef = $rule->badgeDefinition;
                if (! $badgeDef || ! $badgeDef->is_active) {
                    continue;
                }

                // Kategori grubuna göre çeviri al
                $displayData = $badgeDef->getDisplayData($categoryGroupId);
                $displayData['rule_id'] = $rule->id;
                $displayData['calculated_at'] = now();

                $badges->push($displayData);
            }
        }

        // Priority'e göre sırala ve duplicate'leri kaldır (key bazında)
        $badges = $badges->sortByDesc('priority')->unique('key')->values();

        if ($save) {
            $this->saveSnapshots($product, $badges);
        }

        return $badges;
    }

    /**
     * Hesaplanan badge'leri veritabanına kaydet
     */
    public function saveSnapshots(Product $product, Collection $badges): void
    {
        DB::transaction(function () use ($product, $badges) {
            // Eski snapshot'ları temizle
            ProductBadgeSnapshot::where('product_id', $product->id)->delete();

            // Yeni snapshot'ları kaydet
            foreach ($badges as $badge) {
                $badgeDef = BadgeDefinition::where('key', $badge['key'])->first();
                if (! $badgeDef) {
                    continue;
                }

                ProductBadgeSnapshot::create([
                    'product_id' => $product->id,
                    'badge_definition_id' => $badgeDef->id,
                    'label' => $badge['label'],
                    'icon' => $badge['icon'],
                    'color' => $badge['color'],
                    'bg_color' => $badge['bg_color'],
                    'text_color' => $badge['text_color'],
                    'priority' => $badge['priority'],
                    'calculated_at' => $badge['calculated_at'] ?? now(),
                    'expires_at' => now()->addHours(24), // 24 saat geçerli
                ]);
            }
        });
    }

    /**
     * Cache'den veya hesaplanmış badge'leri getir
     */
    public function getBadges(Product $product): Collection
    {
        // Snapshot var mı kontrol et
        $snapshots = $product->badgeSnapshots()
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->orderBy('priority', 'desc')
            ->get();

        if ($snapshots->isNotEmpty()) {
            return $snapshots->map(fn ($snapshot) => $snapshot->toArray());
        }

        // Snapshot yoksa veya süresi dolmuşsa yeniden hesapla
        return $this->calculateBadges($product);
    }

    /**
     * API response için badge'leri formatla
     */
    public function formatForApi(Collection $badges): array
    {
        return $badges->map(fn ($badge) => [
            'key' => $badge['key'] ?? $badge->key,
            'label' => $badge['label'],
            'icon' => $badge['icon'],
            'color' => $badge['color'],
            'bg_color' => $badge['bg_color'],
            'text_color' => $badge['text_color'],
        ])->toArray();
    }

    /**
     * Tüm ürünlerin badge'lerini yeniden hesapla (batch işlem)
     */
    public function recalculateAllBadges(int $chunkSize = 100): void
    {
        Product::where('is_active', true)
            ->chunk($chunkSize, function ($products) {
                foreach ($products as $product) {
                    $this->calculateBadges($product);
                }
            });
    }

    /**
     * Belirli bir kategori grubundaki ürünlerin badge'lerini yeniden hesapla
     */
    public function recalculateForCategoryGroup(int $categoryGroupId, int $chunkSize = 100): void
    {
        Product::where('category_group_id', $categoryGroupId)
            ->where('is_active', true)
            ->chunk($chunkSize, function ($products) {
                foreach ($products as $product) {
                    $this->calculateBadges($product);
                }
            });
    }
}
