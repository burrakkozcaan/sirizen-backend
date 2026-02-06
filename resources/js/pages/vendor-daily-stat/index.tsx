import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import { TrendingUp, ShoppingBag, Users, Package } from 'lucide-react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Günlük İstatistikler',
        href: '/daily-stats',
    },
];

interface VendorDailyStat {
    id: string;
    stat_date: string;
    total_sales: number;
    revenue: string;
    commission: string;
    net_revenue: string;
    orders_count: number;
    products_sold: number;
    new_customers: number;
    returning_customers: number;
    avg_order_value: string;
    page_views: number;
    product_views: number;
    conversion_rate: string;
}

interface Props {
    stats?: VendorDailyStat[];
}

export default function VendorDailyStats({ stats = [] }: Props) {
    const formatDate = (dateString: string) => {
        if (!dateString) return '-';
        return new Intl.DateTimeFormat('tr-TR', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
        }).format(new Date(dateString));
    };

    // Calculate totals
    const totals = stats.reduce(
        (acc, stat) => ({
            totalRevenue: acc.totalRevenue + parseFloat(stat.revenue),
            totalOrders: acc.totalOrders + stat.orders_count,
            totalProductsSold: acc.totalProductsSold + stat.products_sold,
            totalCustomers: acc.totalCustomers + stat.new_customers + stat.returning_customers,
        }),
        { totalRevenue: 0, totalOrders: 0, totalProductsSold: 0, totalCustomers: 0 },
    );

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Günlük İstatistikler" />
            <div className="relative flex h-full w-full flex-col gap-y-4">
                <main className="relative flex min-h-0 w-full grow flex-col">
                    <div className="flex h-full w-full flex-row gap-x-2 p-5">
                        <div className="relative flex w-full flex-col items-center rounded-2xl border-gray-200 px-4 md:overflow-y-auto md:border md:bg-white md:px-8 md:shadow-xs dark:border-[#313131] dark:md:bg-[#181818]">
                            <div className="flex h-full w-full max-w-[--breakpoint-xl] flex-col">
                                <div className="flex w-full flex-col gap-y-4 py-8 md:flex-row md:items-center md:justify-between md:py-8">
                                    <h4 className="text-2xl font-medium whitespace-nowrap dark:text-white">
                                        Günlük İstatistikler
                                    </h4>
                                </div>

                                <div className="flex w-full flex-col gap-y-8 pb-8">
                                    <p className="text-gray-500 dark:text-[#999999]">
                                        Günlük performans istatistiklerinizi görüntüleyin.
                                    </p>

                                    {/* KPI Cards */}
                                    <div className="grid gap-6 md:grid-cols-4">
                                        <div className="rounded-2xl border border-gray-200 p-6 dark:border-[#313131]">
                                            <div className="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                                                <TrendingUp className="h-4 w-4" />
                                                Toplam Gelir
                                            </div>
                                            <div className="mt-2 text-2xl font-bold dark:text-white">
                                                {totals.totalRevenue.toLocaleString('tr-TR', {
                                                    style: 'currency',
                                                    currency: 'TRY',
                                                })}
                                            </div>
                                        </div>
                                        <div className="rounded-2xl border border-gray-200 p-6 dark:border-[#313131]">
                                            <div className="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                                                <ShoppingBag className="h-4 w-4" />
                                                Toplam Sipariş
                                            </div>
                                            <div className="mt-2 text-2xl font-bold dark:text-white">
                                                {totals.totalOrders}
                                            </div>
                                        </div>
                                        <div className="rounded-2xl border border-gray-200 p-6 dark:border-[#313131]">
                                            <div className="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                                                <Package className="h-4 w-4" />
                                                Satılan Ürün
                                            </div>
                                            <div className="mt-2 text-2xl font-bold dark:text-white">
                                                {totals.totalProductsSold}
                                            </div>
                                        </div>
                                        <div className="rounded-2xl border border-gray-200 p-6 dark:border-[#313131]">
                                            <div className="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                                                <Users className="h-4 w-4" />
                                                Toplam Müşteri
                                            </div>
                                            <div className="mt-2 text-2xl font-bold dark:text-white">
                                                {totals.totalCustomers}
                                            </div>
                                        </div>
                                    </div>

                                    <div className="flex flex-col gap-6">
                                        <div className="overflow-hidden rounded-2xl border border-gray-200 dark:border-[#313131]">
                                            <div className="relative w-full overflow-auto">
                                                <table className="w-full caption-bottom text-sm">
                                                    <thead className="[&_tr]:border-b">
                                                        <tr className="border-b bg-gray-50 transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted dark:bg-[#171719]">
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Tarih
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Gelir
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Komisyon
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Net Gelir
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Sipariş
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Satılan Ürün
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Yeni Müşteri
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Dönüşüm Oranı
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody className="[&_tr:last-child]:border-0">
                                                        {stats.length === 0 ? (
                                                            <tr>
                                                                <td
                                                                    colSpan={8}
                                                                    className="p-8 text-center text-gray-500 dark:text-gray-400"
                                                                >
                                                                    Henüz istatistik yok.
                                                                </td>
                                                            </tr>
                                                        ) : (
                                                            stats.map((stat) => (
                                                                <tr
                                                                    key={stat.id}
                                                                    className="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted"
                                                                >
                                                                    <td className="p-4 align-middle">
                                                                        <span className="text-sm font-medium dark:text-white">
                                                                            {formatDate(stat.stat_date)}
                                                                        </span>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <span className="text-sm font-medium dark:text-white">
                                                                            {parseFloat(stat.revenue).toLocaleString('tr-TR', {
                                                                                style: 'currency',
                                                                                currency: 'TRY',
                                                                            })}
                                                                        </span>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <span className="text-sm text-gray-500 dark:text-gray-400">
                                                                            {parseFloat(stat.commission).toLocaleString('tr-TR', {
                                                                                style: 'currency',
                                                                                currency: 'TRY',
                                                                            })}
                                                                        </span>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <span className="text-sm font-medium text-green-600 dark:text-green-400">
                                                                            {parseFloat(stat.net_revenue).toLocaleString('tr-TR', {
                                                                                style: 'currency',
                                                                                currency: 'TRY',
                                                                            })}
                                                                        </span>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <span className="text-sm dark:text-white">
                                                                            {stat.orders_count}
                                                                        </span>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <span className="text-sm dark:text-white">
                                                                            {stat.products_sold}
                                                                        </span>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <span className="text-sm dark:text-white">
                                                                            {stat.new_customers}
                                                                        </span>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <span className="text-sm font-medium dark:text-white">
                                                                            %{parseFloat(stat.conversion_rate).toFixed(2)}
                                                                        </span>
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
                    </div>
                </main>
            </div>
        </AppLayout>
    );
}

