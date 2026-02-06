import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import { Status, StatusIndicator, StatusLabel } from '@/components/ui/status';
import { TrendingUp, TrendingDown } from 'lucide-react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'SLA Metrikleri',
        href: '/sla-metrics',
    },
];

interface VendorSlaMetric {
    id: string;
    metric_date: string;
    total_orders: number;
    cancelled_orders: number;
    returned_orders: number;
    late_shipments: number;
    on_time_shipments: number;
    cancel_rate: string;
    return_rate: string;
    late_shipment_rate: string;
    avg_shipment_time: number;
    avg_response_time: number;
    total_questions_answered: number;
    total_reviews_responded: number;
    customer_satisfaction_score: string;
    sla_violations: string[];
}

interface Props {
    metrics?: VendorSlaMetric[];
}

export default function VendorSlaMetrics({ metrics = [] }: Props) {
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
            <Head title="SLA Metrikleri" />
            <div className="relative flex h-full w-full flex-col gap-y-4">
                <main className="relative flex min-h-0 w-full grow flex-col">
                    <div className="flex h-full w-full flex-row gap-x-2 p-5">
                        <div className="relative flex w-full flex-col items-center rounded-2xl border-gray-200 px-4 md:overflow-y-auto md:border md:bg-white md:px-8 md:shadow-xs dark:border-[#313131] dark:md:bg-[#181818]">
                            <div className="flex h-full w-full max-w-[--breakpoint-xl] flex-col">
                                <div className="flex w-full flex-col gap-y-4 py-8 md:flex-row md:items-center md:justify-between md:py-8">
                                    <h4 className="text-2xl font-medium whitespace-nowrap dark:text-white">
                                        SLA Metrikleri
                                    </h4>
                                </div>

                                <div className="flex w-full flex-col gap-y-8 pb-8">
                                    <p className="text-gray-500 dark:text-[#999999]">
                                        Performans metriklerinizi görüntüleyin.
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
                                                                Toplam Sipariş
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                İptal Oranı
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                İade Oranı
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Geç Kargo Oranı
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Ort. Yanıt Süresi
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Müşteri Memnuniyeti
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody className="[&_tr:last-child]:border-0">
                                                        {metrics.length === 0 ? (
                                                            <tr>
                                                                <td
                                                                    colSpan={7}
                                                                    className="p-8 text-center text-gray-500 dark:text-gray-400"
                                                                >
                                                                    Henüz metrik yok.
                                                                </td>
                                                            </tr>
                                                        ) : (
                                                            metrics.map((metric) => (
                                                                <tr
                                                                    key={metric.id}
                                                                    className="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted"
                                                                >
                                                                    <td className="p-4 align-middle">
                                                                        <span className="text-sm font-medium dark:text-white">
                                                                            {formatDate(metric.metric_date)}
                                                                        </span>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <span className="text-sm dark:text-white">
                                                                            {metric.total_orders}
                                                                        </span>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <div className="flex items-center gap-1">
                                                                            {parseFloat(metric.cancel_rate) > 5 ? (
                                                                                <TrendingUp className="h-3 w-3 text-red-500" />
                                                                            ) : (
                                                                                <TrendingDown className="h-3 w-3 text-green-500" />
                                                                            )}
                                                                            <span className="text-sm dark:text-white">
                                                                                %{parseFloat(metric.cancel_rate).toFixed(2)}
                                                                            </span>
                                                                        </div>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <div className="flex items-center gap-1">
                                                                            {parseFloat(metric.return_rate) > 5 ? (
                                                                                <TrendingUp className="h-3 w-3 text-red-500" />
                                                                            ) : (
                                                                                <TrendingDown className="h-3 w-3 text-green-500" />
                                                                            )}
                                                                            <span className="text-sm dark:text-white">
                                                                                %{parseFloat(metric.return_rate).toFixed(2)}
                                                                            </span>
                                                                        </div>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <div className="flex items-center gap-1">
                                                                            {parseFloat(metric.late_shipment_rate) > 5 ? (
                                                                                <TrendingUp className="h-3 w-3 text-red-500" />
                                                                            ) : (
                                                                                <TrendingDown className="h-3 w-3 text-green-500" />
                                                                            )}
                                                                            <span className="text-sm dark:text-white">
                                                                                %{parseFloat(metric.late_shipment_rate).toFixed(2)}
                                                                            </span>
                                                                        </div>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <span className="text-sm dark:text-white">
                                                                            {metric.avg_response_time} dk
                                                                        </span>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <div className="flex items-center gap-1">
                                                                            <span className="text-sm font-medium dark:text-white">
                                                                                {parseFloat(metric.customer_satisfaction_score).toFixed(1)}/5.0
                                                                            </span>
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

