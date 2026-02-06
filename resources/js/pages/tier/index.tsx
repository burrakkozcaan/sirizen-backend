import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Seviyeler',
        href: '/tiers',
    },
];

interface TierItem {
    id: string;
    name: string;
    min_total_orders: number;
    min_rating: string;
    max_cancel_rate: string;
    max_return_rate: string;
    priority_boost: number;
    is_current: boolean;
}

interface Tier {
    current_tier?: {
        id: string;
        name: string;
    } | null;
    vendor_stats: {
        total_orders: number;
        rating: string;
        followers: number;
        cancel_rate: string;
        return_rate: string;
    };
    all_tiers: TierItem[];
}

interface Props {
    tier?: Tier;
}

export default function Tiers({ tier }: Props) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Seviyeler" />
            <div className="relative flex h-full w-full flex-col gap-y-4">
                <main className="relative flex min-h-0 w-full grow flex-col">
                    <div className="flex h-full w-full flex-row gap-x-2 p-5">
                        <div className="relative flex w-full flex-col items-center rounded-2xl border-gray-200 px-4 md:overflow-y-auto md:border md:bg-white md:px-8 md:shadow-xs dark:border-[#313131] dark:md:bg-[#181818]">
                            <div className="flex h-full w-full max-w-[--breakpoint-xl] flex-col">
                                <div className="flex w-full flex-col gap-y-4 py-8 md:flex-row md:items-center md:justify-between md:py-8">
                                    <h4 className="text-2xl font-medium whitespace-nowrap dark:text-white">
                                        Seviyeler
                                    </h4>
                                </div>

                                <div className="flex w-full flex-col gap-y-8 pb-8">
                                    <p className="text-gray-500 dark:text-[#999999]">
                                        Satıcı seviyenizi ve istatistiklerinizi görüntüleyin.
                                    </p>

                                    {/* İstatistikler */}
                                    <div className="grid gap-4 md:grid-cols-4">
                                        <div className="rounded-2xl border border-gray-200 p-4 dark:border-[#313131]">
                                            <div className="text-sm text-gray-500 dark:text-gray-400">
                                                Mevcut Seviye
                                            </div>
                                            <div className="mt-2 text-xl font-bold dark:text-white">
                                                {tier?.current_tier?.name || 'Seviye Yok'}
                                            </div>
                                        </div>
                                        <div className="rounded-2xl border border-gray-200 p-4 dark:border-[#313131]">
                                            <div className="text-sm text-gray-500 dark:text-gray-400">
                                                Toplam Sipariş
                                            </div>
                                            <div className="mt-2 text-xl font-bold dark:text-white">
                                                {tier?.vendor_stats.total_orders || 0}
                                            </div>
                                        </div>
                                        <div className="rounded-2xl border border-gray-200 p-4 dark:border-[#313131]">
                                            <div className="text-sm text-gray-500 dark:text-gray-400">
                                                Puan
                                            </div>
                                            <div className="mt-2 text-xl font-bold dark:text-white">
                                                {parseFloat(tier?.vendor_stats.rating || '0').toFixed(1)} / 5.0
                                            </div>
                                        </div>
                                        <div className="rounded-2xl border border-gray-200 p-4 dark:border-[#313131]">
                                            <div className="text-sm text-gray-500 dark:text-gray-400">
                                                Takipçi Sayısı
                                            </div>
                                            <div className="mt-2 text-xl font-bold dark:text-white">
                                                {tier?.vendor_stats.followers || 0}
                                            </div>
                                        </div>
                                    </div>

                                    {/* Seviyeler Tablosu */}
                                    <div className="overflow-hidden rounded-2xl border border-gray-200 dark:border-[#313131]">
                                        <div className="relative w-full overflow-auto">
                                            <table className="w-full caption-bottom text-sm">
                                                <thead className="[&_tr]:border-b">
                                                    <tr className="border-b bg-gray-50 transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted dark:bg-[#171719]">
                                                        <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                            Seviye Adı
                                                        </th>
                                                        <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                            Min. Sipariş
                                                        </th>
                                                        <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                            Min. Puan
                                                        </th>
                                                        <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                            Max. İptal Oranı (%)
                                                        </th>
                                                        <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                            Max. İade Oranı (%)
                                                        </th>
                                                        <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                            Öncelik Artışı
                                                        </th>
                                                        <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                            Durum
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody className="[&_tr:last-child]:border-0">
                                                    {tier?.all_tiers && tier.all_tiers.length === 0 ? (
                                                        <tr>
                                                            <td
                                                                colSpan={7}
                                                                className="p-8 text-center text-gray-500 dark:text-gray-400"
                                                            >
                                                                Henüz seviye tanımlanmamış.
                                                            </td>
                                                        </tr>
                                                    ) : (
                                                        tier?.all_tiers.map((tierItem) => (
                                                            <tr
                                                                key={tierItem.id}
                                                                className={`border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted ${
                                                                    tierItem.is_current
                                                                        ? 'bg-blue-50 dark:bg-blue-950/20'
                                                                        : ''
                                                                }`}
                                                            >
                                                                <td className="p-4 align-middle">
                                                                    <div className="text-sm font-medium dark:text-white">
                                                                        {tierItem.name}
                                                                    </div>
                                                                </td>
                                                                <td className="p-4 align-middle">
                                                                    <div className="text-sm dark:text-white">
                                                                        {tierItem.min_total_orders}
                                                                    </div>
                                                                </td>
                                                                <td className="p-4 align-middle">
                                                                    <div className="text-sm dark:text-white">
                                                                        {parseFloat(tierItem.min_rating).toFixed(1)}
                                                                    </div>
                                                                </td>
                                                                <td className="p-4 align-middle">
                                                                    <div className="text-sm dark:text-white">
                                                                        {parseFloat(tierItem.max_cancel_rate).toFixed(1)}%
                                                                    </div>
                                                                </td>
                                                                <td className="p-4 align-middle">
                                                                    <div className="text-sm dark:text-white">
                                                                        {parseFloat(tierItem.max_return_rate).toFixed(1)}%
                                                                    </div>
                                                                </td>
                                                                <td className="p-4 align-middle">
                                                                    <div className="text-sm dark:text-white">
                                                                        {tierItem.priority_boost}
                                                                    </div>
                                                                </td>
                                                                <td className="p-4 align-middle">
                                                                    {tierItem.is_current ? (
                                                                        <span className="text-xs px-2 py-1 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300">
                                                                            Mevcut Seviye
                                                                        </span>
                                                                    ) : (
                                                                        <span className="text-xs px-2 py-1 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300">
                                                                            -
                                                                        </span>
                                                                    )}
                                                                </td>
                                                            </tr>
                                                        ))
                                                    )}
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </AppLayout>
    );
}

