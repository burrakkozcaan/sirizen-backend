import AppLayout from '@/layouts/app-layout';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/react';
import { AlertTriangle, Box, PackageCheck, RotateCcw, ShoppingBag, Truck, Wallet } from 'lucide-react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

interface DashboardStats {
    total_orders: number;
    active_shipments: number;
    pending_returns: number;
    pending_balance: string;
}

interface ShipmentAlert {
    missing_tracking: number;
}

interface RecentOrder {
    id: string;
    order_number: string;
    status: string;
    items_count: number;
    total_price: string;
    created_at: string;
}

interface RecentShipment {
    id: string;
    tracking_number: string;
    status: string;
    carrier: string;
    created_at: string;
    shipped_at: string | null;
    order?: {
        id: string;
        order_number: string;
    } | null;
    product?: {
        id: string;
        name: string;
    } | null;
}

interface CommissionInfo {
    default_rate: number;
    min_amount: number;
    currency: string;
}

interface MissingItem {
    label: string;
    href: string;
}

interface Props {
    stats: DashboardStats;
    shipment_alerts?: ShipmentAlert;
    recent_orders?: RecentOrder[];
    recent_shipments?: RecentShipment[];
    commission: CommissionInfo;
    missing_items?: MissingItem[];
}

export default function Dashboard({
    stats,
    shipment_alerts,
    recent_orders = [],
    recent_shipments = [],
    commission,
    missing_items = [],
}: Props) {
    const formatDate = (dateString: string | null) => {
        if (!dateString) return '-';
        return new Intl.DateTimeFormat('tr-TR', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        }).format(new Date(dateString));
    };

    const formatCurrency = (value: string | number, currency: string) => {
        const numericValue = typeof value === 'string' ? parseFloat(value || '0') : value;
        return numericValue.toLocaleString('tr-TR', {
            style: 'currency',
            currency,
        });
    };

    const missingTrackingCount = shipment_alerts?.missing_tracking ?? 0;

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <div className="relative flex h-full w-full flex-col gap-y-4">
                <main className="relative flex min-h-0 w-full grow flex-col">
                    <div className="flex h-full w-full flex-row gap-x-2 p-5">
                        <div className="relative flex w-full flex-col items-center rounded-2xl border-gray-200 px-4 md:overflow-y-auto md:border md:bg-white md:px-8 md:shadow-xs dark:border-[#313131] dark:md:bg-[#181818]">
                            <div className="flex h-full w-full max-w-[--breakpoint-xl] flex-col">
                                <div className="flex w-full flex-col gap-y-4 py-8 md:flex-row md:items-center md:justify-between md:py-8">
                                    <h4 className="text-2xl font-medium whitespace-nowrap dark:text-white">
                                        Dashboard
                                    </h4>
                                </div>

                                <div className="flex w-full flex-col gap-y-8 pb-8">
                                    <p className="text-gray-500 dark:text-[#999999]">
                                        Sipariş, kargo ve komisyon özetinizi hızlıca kontrol edin.
                                    </p>

                                    <div className="grid gap-6 md:grid-cols-4">
                                        <div className="rounded-2xl border border-gray-200 p-6 dark:border-[#313131]">
                                            <div className="flex items-center gap-3">
                                                <div className="rounded-lg bg-blue-100 p-3 dark:bg-blue-900/20">
                                                    <ShoppingBag className="h-5 w-5 text-blue-600 dark:text-blue-400" />
                                                </div>
                                                <div>
                                                    <div className="text-sm text-gray-500 dark:text-gray-400">
                                                        Toplam Sipariş
                                                    </div>
                                                    <div className="mt-1 text-2xl font-bold dark:text-white">
                                                        {stats.total_orders.toLocaleString('tr-TR')}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div className="rounded-2xl border border-gray-200 p-6 dark:border-[#313131]">
                                            <div className="flex items-center gap-3">
                                                <div className="rounded-lg bg-emerald-100 p-3 dark:bg-emerald-900/20">
                                                    <Truck className="h-5 w-5 text-emerald-600 dark:text-emerald-400" />
                                                </div>
                                                <div>
                                                    <div className="text-sm text-gray-500 dark:text-gray-400">
                                                        Aktif Kargo
                                                    </div>
                                                    <div className="mt-1 text-2xl font-bold dark:text-white">
                                                        {stats.active_shipments.toLocaleString('tr-TR')}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div className="rounded-2xl border border-gray-200 p-6 dark:border-[#313131]">
                                            <div className="flex items-center gap-3">
                                                <div className="rounded-lg bg-amber-100 p-3 dark:bg-amber-900/20">
                                                    <RotateCcw className="h-5 w-5 text-amber-600 dark:text-amber-400" />
                                                </div>
                                                <div>
                                                    <div className="text-sm text-gray-500 dark:text-gray-400">
                                                        Bekleyen İade
                                                    </div>
                                                    <div className="mt-1 text-2xl font-bold dark:text-white">
                                                        {stats.pending_returns.toLocaleString('tr-TR')}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div className="rounded-2xl border border-gray-200 p-6 dark:border-[#313131]">
                                            <div className="flex items-center gap-3">
                                                <div className="rounded-lg bg-purple-100 p-3 dark:bg-purple-900/20">
                                                    <Wallet className="h-5 w-5 text-purple-600 dark:text-purple-400" />
                                                </div>
                                                <div>
                                                    <div className="text-sm text-gray-500 dark:text-gray-400">
                                                        Bekleyen Bakiye
                                                    </div>
                                                    <div className="mt-1 text-2xl font-bold dark:text-white">
                                                        {formatCurrency(
                                                            stats.pending_balance,
                                                            commission.currency,
                                                        )}
                                                    </div>
                                                    <div className="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                                        Komisyon oranı: %{commission.default_rate.toLocaleString('tr-TR')} ·
                                                        Minimum: {formatCurrency(commission.min_amount, commission.currency)}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div className="grid gap-6 lg:grid-cols-[2fr_1fr]">
                                        <div className="overflow-hidden rounded-2xl border border-gray-200 dark:border-[#313131]">
                                            <div className="flex items-center justify-between border-b border-gray-200 px-6 py-4 dark:border-[#313131]">
                                                <div className="flex items-center gap-2">
                                                    <PackageCheck className="h-5 w-5 text-gray-500 dark:text-gray-400" />
                                                    <h5 className="text-base font-semibold dark:text-white">
                                                        Son Kargolar
                                                    </h5>
                                                </div>
                                                <Link
                                                    href="/shipping"
                                                    className="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300"
                                                >
                                                    Tümünü gör
                                                </Link>
                                            </div>
                                            <div className="relative w-full overflow-auto">
                                                <table className="w-full caption-bottom text-sm">
                                                    <thead className="[&_tr]:border-b">
                                                        <tr className="border-b bg-gray-50 transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted dark:bg-[#171719]">
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Takip No
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Sipariş
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Ürün
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Durum
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Gönderim
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody className="[&_tr:last-child]:border-0">
                                                        {recent_shipments.length === 0 ? (
                                                            <tr>
                                                                <td
                                                                    colSpan={5}
                                                                    className="p-8 text-center text-gray-500 dark:text-gray-400"
                                                                >
                                                                    Henüz kargo gönderisi yok.
                                                                </td>
                                                            </tr>
                                                        ) : (
                                                            recent_shipments.map((shipment) => (
                                                                <tr
                                                                    key={shipment.id}
                                                                    className="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted"
                                                                >
                                                                    <td className="p-4 align-middle">
                                                                        <div className="text-sm font-medium dark:text-white">
                                                                            {shipment.tracking_number || '-'}
                                                                        </div>
                                                                        <div className="text-xs text-gray-500 dark:text-gray-400">
                                                                            {shipment.carrier || '-'}
                                                                        </div>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <div className="text-sm dark:text-white">
                                                                            {shipment.order?.order_number || '-'}
                                                                        </div>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <div className="text-sm dark:text-white">
                                                                            {shipment.product?.name || '-'}
                                                                        </div>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <span className="text-xs rounded-full bg-gray-100 px-2 py-1 text-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                                                            {shipment.status || '-'}
                                                                        </span>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <span className="text-sm text-gray-500 dark:text-gray-400">
                                                                            {formatDate(shipment.shipped_at)}
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                            ))
                                                        )}
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div className="flex flex-col gap-6">
                                            <div className="rounded-2xl border border-gray-200 p-6 dark:border-[#313131]">
                                                <div className="flex items-center gap-2">
                                                    <AlertTriangle className="h-5 w-5 text-amber-500" />
                                                    <div className="text-sm font-medium text-gray-700 dark:text-gray-200">
                                                        Kargo Uyarıları
                                                    </div>
                                                </div>
                                                <div className="mt-4 text-2xl font-bold dark:text-white">
                                                    {missingTrackingCount.toLocaleString('tr-TR')}
                                                </div>
                                                <div className="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                                    Takip numarası veya kargo firması eksik gönderi.
                                                </div>
                                                <Link
                                                    href="/shipping"
                                                    className="mt-4 inline-flex text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300"
                                                >
                                                    Kargolara git
                                                </Link>
                                            </div>

                                            {missing_items.length > 0 && (
                                                <div className="rounded-2xl border border-gray-200 p-6 dark:border-[#313131]">
                                                    <div className="flex items-center gap-2">
                                                        <Box className="h-5 w-5 text-gray-500 dark:text-gray-400" />
                                                        <div className="text-sm font-medium text-gray-700 dark:text-gray-200">
                                                            Eksikler
                                                        </div>
                                                    </div>
                                                    <div className="mt-4 flex flex-col gap-2 text-sm text-gray-600 dark:text-gray-300">
                                                        {missing_items.map((item) => (
                                                            <Link
                                                                key={item.label}
                                                                href={item.href}
                                                                className="hover:text-blue-600 dark:hover:text-blue-400"
                                                            >
                                                                {item.label}
                                                            </Link>
                                                        ))}
                                                    </div>
                                                </div>
                                            )}
                                        </div>
                                    </div>

                                    <div className="overflow-hidden rounded-2xl border border-gray-200 dark:border-[#313131]">
                                        <div className="flex items-center justify-between border-b border-gray-200 px-6 py-4 dark:border-[#313131]">
                                            <div className="flex items-center gap-2">
                                                <PackageCheck className="h-5 w-5 text-gray-500 dark:text-gray-400" />
                                                <h5 className="text-base font-semibold dark:text-white">
                                                    Son Siparişler
                                                </h5>
                                            </div>
                                            <Link
                                                href="/orders"
                                                className="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300"
                                            >
                                                Tümünü gör
                                            </Link>
                                        </div>
                                        <div className="relative w-full overflow-auto">
                                            <table className="w-full caption-bottom text-sm">
                                                <thead className="[&_tr]:border-b">
                                                    <tr className="border-b bg-gray-50 transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted dark:bg-[#171719]">
                                                        <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                            Sipariş No
                                                        </th>
                                                        <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                            Durum
                                                        </th>
                                                        <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                            Ürün Sayısı
                                                        </th>
                                                        <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                            Tutar
                                                        </th>
                                                        <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                            Tarih
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody className="[&_tr:last-child]:border-0">
                                                    {recent_orders.length === 0 ? (
                                                        <tr>
                                                            <td
                                                                colSpan={5}
                                                                className="p-8 text-center text-gray-500 dark:text-gray-400"
                                                            >
                                                                Henüz sipariş yok.
                                                            </td>
                                                        </tr>
                                                    ) : (
                                                        recent_orders.map((order) => (
                                                            <tr
                                                                key={order.id}
                                                                className="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted"
                                                            >
                                                                <td className="p-4 align-middle">
                                                                    <span className="text-sm font-medium dark:text-white">
                                                                        {order.order_number || '-'}
                                                                    </span>
                                                                </td>
                                                                <td className="p-4 align-middle">
                                                                    <span className="text-xs rounded-full bg-gray-100 px-2 py-1 text-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                                                        {order.status || '-'}
                                                                    </span>
                                                                </td>
                                                                <td className="p-4 align-middle">
                                                                    <span className="text-sm dark:text-white">
                                                                        {order.items_count.toLocaleString('tr-TR')}
                                                                    </span>
                                                                </td>
                                                                <td className="p-4 align-middle">
                                                                    <span className="text-sm font-medium dark:text-white">
                                                                        {formatCurrency(
                                                                            order.total_price,
                                                                            commission.currency,
                                                                        )}
                                                                    </span>
                                                                </td>
                                                                <td className="p-4 align-middle">
                                                                    <span className="text-sm text-gray-500 dark:text-gray-400">
                                                                        {formatDate(order.created_at)}
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
                </main>
            </div>
        </AppLayout>
    );
}
