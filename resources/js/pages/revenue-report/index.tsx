import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import { TrendingUp, DollarSign } from 'lucide-react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Gelir Raporları',
        href: '/revenue-reports',
    },
];

interface RevenueReport {
    id: string;
    report_date: string;
    period_type: string;
    total_revenue: string;
    total_commission: string;
    vendor_payouts: string;
    total_orders: number;
    total_vendors: number;
    active_vendors: number;
    new_vendors: number;
    total_customers: number;
    new_customers: number;
    total_products: number;
    avg_order_value: string;
    top_categories: string[];
    top_vendors: string[];
}

interface Props {
    reports?: RevenueReport[];
}

export default function RevenueReports({ reports = [] }: Props) {
    const formatDate = (dateString: string) => {
        if (!dateString) return '-';
        return new Intl.DateTimeFormat('tr-TR', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
        }).format(new Date(dateString));
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Gelir Raporları" />
            <div className="relative flex h-full w-full flex-col gap-y-4">
                <main className="relative flex min-h-0 w-full grow flex-col">
                    <div className="flex h-full w-full flex-row gap-x-2 p-5">
                        <div className="relative flex w-full flex-col items-center rounded-2xl border-gray-200 px-4 md:overflow-y-auto md:border md:bg-white md:px-8 md:shadow-xs dark:border-[#313131] dark:md:bg-[#181818]">
                            <div className="flex h-full w-full max-w-[--breakpoint-xl] flex-col">
                                <div className="flex w-full flex-col gap-y-4 py-8 md:flex-row md:items-center md:justify-between md:py-8">
                                    <h4 className="text-2xl font-medium whitespace-nowrap dark:text-white">
                                        Gelir Raporları
                                    </h4>
                                </div>

                                <div className="flex w-full flex-col gap-y-8 pb-8">
                                    <p className="text-gray-500 dark:text-[#999999]">
                                        Platform geneli gelir raporlarını görüntüleyin.
                                    </p>

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
                                                                Dönem
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Toplam Gelir
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Toplam Komisyon
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Satıcı Ödemeleri
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Toplam Sipariş
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Ort. Sipariş Değeri
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody className="[&_tr:last-child]:border-0">
                                                        {reports.length === 0 ? (
                                                            <tr>
                                                                <td
                                                                    colSpan={7}
                                                                    className="p-8 text-center text-gray-500 dark:text-gray-400"
                                                                >
                                                                    Henüz rapor yok.
                                                                </td>
                                                            </tr>
                                                        ) : (
                                                            reports.map((report) => (
                                                                <tr
                                                                    key={report.id}
                                                                    className="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted"
                                                                >
                                                                    <td className="p-4 align-middle">
                                                                        <span className="text-sm font-medium dark:text-white">
                                                                            {formatDate(report.report_date)}
                                                                        </span>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <span className="text-sm dark:text-white">
                                                                            {report.period_type}
                                                                        </span>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <div className="flex items-center gap-1">
                                                                            <DollarSign className="h-4 w-4 text-green-500" />
                                                                            <span className="text-sm font-medium dark:text-white">
                                                                                {parseFloat(report.total_revenue).toLocaleString('tr-TR', {
                                                                                    style: 'currency',
                                                                                    currency: 'TRY',
                                                                                })}
                                                                            </span>
                                                                        </div>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <span className="text-sm dark:text-white">
                                                                            {parseFloat(report.total_commission).toLocaleString('tr-TR', {
                                                                                style: 'currency',
                                                                                currency: 'TRY',
                                                                            })}
                                                                        </span>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <span className="text-sm dark:text-white">
                                                                            {parseFloat(report.vendor_payouts).toLocaleString('tr-TR', {
                                                                                style: 'currency',
                                                                                currency: 'TRY',
                                                                            })}
                                                                        </span>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <span className="text-sm dark:text-white">
                                                                            {report.total_orders}
                                                                        </span>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <span className="text-sm dark:text-white">
                                                                            {parseFloat(report.avg_order_value).toLocaleString('tr-TR', {
                                                                                style: 'currency',
                                                                                currency: 'TRY',
                                                                            })}
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

