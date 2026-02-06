<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\ProductQuestion;
use App\Models\ProductReturn;
use App\Models\Dispute;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Bekleyen siparişler (onay bekleyen)
        $pendingOrders = Order::where('status', 'pending')->count();

        // Kargoya verilecek siparişler (hazırlanıyor)
        $toShipOrders = Order::where('status', 'processing')->count();

        // Kargoda olan siparişler
        $shippedOrders = Order::where('status', 'shipped')->count();

        // Cevaplanmamış sorular
        $unansweredQuestions = ProductQuestion::whereNull('answer')
            ->where('is_approved', true)
            ->count();

        // Bekleyen iade talepleri
        $pendingReturns = ProductReturn::where('status', 'pending')->count();

        // Açık anlaşmazlıklar
        $openDisputes = Dispute::whereIn('status', ['open', 'under_review'])->count();

        // Bugünkü satış
        $todaySales = Order::whereDate('created_at', today())
            ->whereNotIn('status', ['cancelled', 'refunded'])
            ->sum('total_price') ?? 0;

        // Bugünkü sipariş sayısı
        $todayOrders = Order::whereDate('created_at', today())->count();

        return [
            Stat::make('Onay Bekleyen', $pendingOrders)
                ->description('Sipariş onayı bekleniyor')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingOrders > 0 ? 'danger' : 'success')
                ->url(route('filament.admin.resources.orders.index', ['tableFilters[status][value]' => 'pending'])),

            Stat::make('Kargoya Verilecek', $toShipOrders)
                ->description('Hazırlanması gereken')
                ->descriptionIcon('heroicon-m-cube')
                ->color($toShipOrders > 0 ? 'warning' : 'success')
                ->url(route('filament.admin.resources.orders.index', ['tableFilters[status][value]' => 'processing'])),

            Stat::make('Kargoda', $shippedOrders)
                ->description('Teslimat sürecinde')
                ->descriptionIcon('heroicon-m-truck')
                ->color('info'),

            Stat::make('Cevaplanmamış Soru', $unansweredQuestions)
                ->description('Yanıt bekleyen')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color($unansweredQuestions > 0 ? 'warning' : 'success')
                ->url(route('filament.admin.resources.product-questions.index')),

            Stat::make('İade Talebi', $pendingReturns)
                ->description('Bekleyen iadeler')
                ->descriptionIcon('heroicon-m-arrow-uturn-left')
                ->color($pendingReturns > 0 ? 'danger' : 'success'),

            Stat::make('Anlaşmazlık', $openDisputes)
                ->description('Açık talepler')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($openDisputes > 0 ? 'danger' : 'success')
                ->url(route('filament.admin.resources.disputes.index')),

            Stat::make('Bugünkü Satış', Number::currency($todaySales, 'TRY'))
                ->description($todayOrders . ' sipariş')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary'),

            Stat::make('Bu Ay', Number::currency(
                Order::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->whereNotIn('status', ['cancelled', 'refunded'])
                    ->sum('total_price') ?? 0,
                'TRY'
            ))
                ->description(now()->translatedFormat('F'))
                ->descriptionIcon('heroicon-m-calendar')
                ->color('success'),
        ];
    }
}
