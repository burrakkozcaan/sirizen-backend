import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import { BarChart3, TrendingUp, Users, ShoppingBag, DollarSign } from 'lucide-react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Analitik',
        href: '/vendor-analytics',
    },
];

interface VendorAnalytic {
    id: string;
    date: string;
    total_sales: string;
    total_orders: number;
    average_order_value: string;
    units_sold: number;
    commission_amount: string;
    net_earnings: string;
    pending_payout: string;
    active_products: number;
    out_of_stock_products: number;
    products_views: number;
    conversion_rate: string;
    unique_customers: number;
    new_customers: number;
    returning_customers: number;
    total_reviews: number;
    average_rating: string;
    questions_answered: number;
    response_time_hours: string;
    shipped_on_time: number;
    late_shipments: number;
    cancelled_orders: number;
    returned_orders: number;
}

interface Summary {
    total_sales: string;
    total_orders: number;
    total_earnings: string;
    total_customers: number;
}

interface Props {
    analytics?: VendorAnalytic[];
    summary?: Summary;
}

export default function VendorAnalytics({ analytics = [], summary }: Props) {
    const formatDate = (dateString: string) => {
        return new Intl.DateTimeFormat('tr-TR', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
        }).format(new Date(dateString));
    };

    const formatCurrency = (value: string) => {
        return parseFloat(value || '0').toLocaleString('tr-TR', {
            style: 'currency',
            currency: 'TRY',
        });
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Analitik" />
            <div className="relative flex h-full w-full flex-col gap-y-4">
                <main className="relative flex min-h-0 w-full grow flex-col">
                    <div className="flex h-full w-full flex-row gap-x-2 p-5">
                        <div className="relative flex w-full flex-col items-center rounded-2xl border-gray-200 px-4 md:overflow-y-auto md:border md:bg-white md:px-8 md:shadow-xs dark:border-[#313131] dark:md:bg-[#181818]">
                            <div className="flex h-full w-full max-w-[--breakpoint-xl] flex-col">
                                <div className="flex w-full flex-col gap-y-4 py-8 md:flex-row md:items-center md:justify-between md:py-8">
                                    <h4 className="text-2xl font-medium whitespace-nowrap dark:text-white">
                                        Analitik
                                    </h4>
                                </div>

                                <div className="flex w-full flex-col gap-y-8 pb-8">
                                    <p className="text-gray-500 dark:text-[#999999]">
                                        Mağaza performansınızı ve istatistiklerinizi görüntüleyin.
                                    </p>

                                    {summary && (
                                        <div className="grid gap-6 md:grid-cols-4">
                                            <div className="rounded-2xl border border-gray-200 p-6 dark:border-[#313131]">
                                                <div className="flex items-center gap-3">
                                                    <div className="rounded-lg bg-blue-100 p-3 dark:bg-blue-900/20">
                                                        <DollarSign className="h-5 w-5 text-blue-600 dark:text-blue-400" />
                                                    </div>
                                                    <div>
                                                        <div className="text-sm text-gray-500 dark:text-gray-400">
                                                            Toplam Satış
                                                        </div>
                                                        <div className="mt-1 text-2xl font-bold dark:text-white">
                                                            {formatCurrency(summary.total_sales)}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div className="rounded-2xl border border-gray-200 p-6 dark:border-[#313131]">
                                                <div className="flex items-center gap-3">
                                                    <div className="rounded-lg bg-green-100 p-3 dark:bg-green-900/20">
                                                        <ShoppingBag className="h-5 w-5 text-green-600 dark:text-green-400" />
                                                    </div>
                                                    <div>
                                                        <div className="text-sm text-gray-500 dark:text-gray-400">
                                                            Toplam Sipariş
                                                        </div>
                                                        <div className="mt-1 text-2xl font-bold dark:text-white">
                                                            {summary.total_orders.toLocaleString('tr-TR')}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div className="rounded-2xl border border-gray-200 p-6 dark:border-[#313131]">
                                                <div className="flex items-center gap-3">
                                                    <div className="rounded-lg bg-purple-100 p-3 dark:bg-purple-900/20">
                                                        <TrendingUp className="h-5 w-5 text-purple-600 dark:text-purple-400" />
                                                    </div>
                                                    <div>
                                                        <div className="text-sm text-gray-500 dark:text-gray-400">
                                                            Toplam Kazanç
                                                        </div>
                                                        <div className="mt-1 text-2xl font-bold dark:text-white">
                                                            {formatCurrency(summary.total_earnings)}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div className="rounded-2xl border border-gray-200 p-6 dark:border-[#313131]">
                                                <div className="flex items-center gap-3">
                                                    <div className="rounded-lg bg-orange-100 p-3 dark:bg-orange-900/20">
                                                        <Users className="h-5 w-5 text-orange-600 dark:text-orange-400" />
                                                    </div>
                                                    <div>
                                                        <div className="text-sm text-gray-500 dark:text-gray-400">
                                                            Toplam Müşteri
                                                        </div>
                                                        <div className="mt-1 text-2xl font-bold dark:text-white">
                                                            {summary.total_customers.toLocaleString('tr-TR')}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    )}

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
                                                                Satış
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Sipariş
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Kazanç
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Müşteri
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Dönüşüm
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody className="[&_tr:last-child]:border-0">
                                                        {analytics.length === 0 ? (
                                                            <tr>
                                                                <td
                                                                    colSpan={6}
                                                                    className="p-8 text-center text-gray-500 dark:text-gray-400"
                                                                >
                                                                    Henüz analitik verisi yok.
                                                                </td>
                                                            </tr>
                                                        ) : (
                                                            analytics.map((analytic) => (
                                                                <tr
                                                                    key={analytic.id}
                                                                    className="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted"
                                                                >
                                                                    <td className="p-4 align-middle">
                                                                        <span className="text-sm dark:text-white">
                                                                            {formatDate(analytic.date)}
                                                                        </span>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <div className="text-sm font-medium dark:text-white">
                                                                            {formatCurrency(analytic.total_sales)}
                                                                        </div>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <div className="text-sm dark:text-white">
                                                                            {analytic.total_orders}
                                                                        </div>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <div className="text-sm font-medium text-green-600 dark:text-green-400">
                                                                            {formatCurrency(analytic.net_earnings)}
                                                                        </div>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <div className="text-sm dark:text-white">
                                                                            {analytic.unique_customers}
                                                                        </div>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <div className="text-sm dark:text-white">
                                                                            %{parseFloat(analytic.conversion_rate || '0').toFixed(2)}
                                                                        </div>
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

