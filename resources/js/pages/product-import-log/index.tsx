import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import { Status, StatusIndicator, StatusLabel } from '@/components/ui/status';
import { Upload } from 'lucide-react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Import Logları',
        href: '/import-logs',
    },
];

interface ProductImportLog {
    id: string;
    file_name: string;
    file_type: string;
    total_rows: number;
    success_count: number;
    failed_count: number;
    skipped_count: number;
    status: string;
    errors: string[];
    summary: string | null;
    started_at: string | null;
    completed_at: string | null;
    created_at: string;
}

interface Props {
    importLogs?: ProductImportLog[];
}

export default function ProductImportLogs({ importLogs = [] }: Props) {
    const formatDate = (dateString: string) => {
        if (!dateString) return '-';
        return new Intl.DateTimeFormat('tr-TR', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        }).format(new Date(dateString));
    };

    const getStatusVariant = (status: string) => {
        switch (status) {
            case 'completed':
            case 'success':
                return 'success';
            case 'failed':
            case 'error':
                return 'error';
            case 'processing':
            case 'in_progress':
                return 'warning';
            default:
                return 'default';
        }
    };

    const getStatusLabel = (status: string) => {
        switch (status) {
            case 'completed':
            case 'success':
                return 'Tamamlandı';
            case 'failed':
            case 'error':
                return 'Başarısız';
            case 'processing':
            case 'in_progress':
                return 'İşleniyor';
            default:
                return status;
        }
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Import Logları" />
            <div className="relative flex h-full w-full flex-col gap-y-4">
                <main className="relative flex min-h-0 w-full grow flex-col">
                    <div className="flex h-full w-full flex-row gap-x-2 p-5">
                        <div className="relative flex w-full flex-col items-center rounded-2xl border-gray-200 px-4 md:overflow-y-auto md:border md:bg-white md:px-8 md:shadow-xs dark:border-[#313131] dark:md:bg-[#181818]">
                            <div className="flex h-full w-full max-w-[--breakpoint-xl] flex-col">
                                <div className="flex w-full flex-col gap-y-4 py-8 md:flex-row md:items-center md:justify-between md:py-8">
                                    <h4 className="text-2xl font-medium whitespace-nowrap dark:text-white">
                                        Ürün Import Logları
                                    </h4>
                                </div>

                                <div className="flex w-full flex-col gap-y-8 pb-8">
                                    <p className="text-gray-500 dark:text-[#999999]">
                                        Ürün import işlemlerinizin geçmişini görüntüleyin.
                                    </p>

                                    <div className="flex flex-col gap-6">
                                        <div className="overflow-hidden rounded-2xl border border-gray-200 dark:border-[#313131]">
                                            <div className="relative w-full overflow-auto">
                                                <table className="w-full caption-bottom text-sm">
                                                    <thead className="[&_tr]:border-b">
                                                        <tr className="border-b bg-gray-50 transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted dark:bg-[#171719]">
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Dosya Adı
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Toplam Satır
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Başarılı
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Başarısız
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Atlandı
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Durum
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Tarih
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody className="[&_tr:last-child]:border-0">
                                                        {importLogs.length === 0 ? (
                                                            <tr>
                                                                <td
                                                                    colSpan={7}
                                                                    className="p-8 text-center text-gray-500 dark:text-gray-400"
                                                                >
                                                                    Henüz import logu yok.
                                                                </td>
                                                            </tr>
                                                        ) : (
                                                            importLogs.map((log) => (
                                                                <tr
                                                                    key={log.id}
                                                                    className="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted"
                                                                >
                                                                    <td className="p-4 align-middle">
                                                                        <div className="flex items-center gap-2">
                                                                            <Upload className="h-4 w-4 text-gray-500" />
                                                                            <span className="text-sm font-medium dark:text-white">
                                                                                {log.file_name}
                                                                            </span>
                                                                        </div>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <span className="text-sm dark:text-white">
                                                                            {log.total_rows}
                                                                        </span>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <span className="text-sm text-green-600 dark:text-green-400 font-medium">
                                                                            {log.success_count}
                                                                        </span>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <span className="text-sm text-red-600 dark:text-red-400 font-medium">
                                                                            {log.failed_count}
                                                                        </span>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <span className="text-sm text-yellow-600 dark:text-yellow-400 font-medium">
                                                                            {log.skipped_count}
                                                                        </span>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <Status variant={getStatusVariant(log.status)}>
                                                                            <StatusIndicator />
                                                                            <StatusLabel>
                                                                                {getStatusLabel(log.status)}
                                                                            </StatusLabel>
                                                                        </Status>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <span className="text-sm text-gray-500 dark:text-gray-400">
                                                                            {formatDate(log.completed_at || log.started_at || log.created_at)}
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

