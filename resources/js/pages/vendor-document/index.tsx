import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import { FileText } from 'lucide-react';
import { Status, StatusIndicator, StatusLabel } from '@/components/ui/status';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Belgeler',
        href: '/vendor-documents',
    },
];

interface VendorDocument {
    id: string;
    document_type: string;
    file_name: string;
    file_size: number;
    status: string;
    rejection_reason: string | null;
    verified_at: string | null;
    notes: string | null;
    created_at: string;
}

interface Props {
    documents?: VendorDocument[];
}

export default function VendorDocuments({ documents = [] }: Props) {
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

    const formatFileSize = (bytes: number) => {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(2) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
    };

    const getStatusVariant = (status: string) => {
        switch (status) {
            case 'approved':
            case 'verified':
                return 'success';
            case 'rejected':
            case 'failed':
                return 'error';
            case 'pending':
            case 'reviewing':
                return 'warning';
            default:
                return 'default';
        }
    };

    const getStatusLabel = (status: string) => {
        switch (status) {
            case 'approved':
            case 'verified':
                return 'Onaylandı';
            case 'rejected':
            case 'failed':
                return 'Reddedildi';
            case 'pending':
            case 'reviewing':
                return 'İncelemede';
            default:
                return status;
        }
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Belgeler" />
            <div className="relative flex h-full w-full flex-col gap-y-4">
                <main className="relative flex min-h-0 w-full grow flex-col">
                    <div className="flex h-full w-full flex-row gap-x-2 p-5">
                        <div className="relative flex w-full flex-col items-center rounded-2xl border-gray-200 px-4 md:overflow-y-auto md:border md:bg-white md:px-8 md:shadow-xs dark:border-[#313131] dark:md:bg-[#181818]">
                            <div className="flex h-full w-full max-w-[--breakpoint-xl] flex-col">
                                <div className="flex w-full flex-col gap-y-4 py-8 md:flex-row md:items-center md:justify-between md:py-8">
                                    <h4 className="text-2xl font-medium whitespace-nowrap dark:text-white">
                                        Belgeler
                                    </h4>
                                </div>

                                <div className="flex w-full flex-col gap-y-8 pb-8">
                                    <p className="text-gray-500 dark:text-[#999999]">
                                        Yüklediğiniz belgeleri görüntüleyin ve durumlarını takip edin.
                                    </p>

                                    <div className="flex flex-col gap-6">
                                        <div className="overflow-hidden rounded-2xl border border-gray-200 dark:border-[#313131]">
                                            <div className="relative w-full overflow-auto">
                                                <table className="w-full caption-bottom text-sm">
                                                    <thead className="[&_tr]:border-b">
                                                        <tr className="border-b bg-gray-50 transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted dark:bg-[#171719]">
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Belge Tipi
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Dosya Adı
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Boyut
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Durum
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Onay Tarihi
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Yüklenme Tarihi
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody className="[&_tr:last-child]:border-0">
                                                        {documents.length === 0 ? (
                                                            <tr>
                                                                <td
                                                                    colSpan={6}
                                                                    className="p-8 text-center text-gray-500 dark:text-gray-400"
                                                                >
                                                                    Henüz belge yüklenmedi.
                                                                </td>
                                                            </tr>
                                                        ) : (
                                                            documents.map((document) => (
                                                                <tr
                                                                    key={document.id}
                                                                    className="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted"
                                                                >
                                                                    <td className="p-4 align-middle">
                                                                        <div className="flex items-center gap-2">
                                                                            <FileText className="h-4 w-4 text-gray-500" />
                                                                            <span className="text-sm font-medium dark:text-white">
                                                                                {document.document_type}
                                                                            </span>
                                                                        </div>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <div className="text-sm dark:text-white">
                                                                            {document.file_name}
                                                                        </div>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <span className="text-sm text-gray-500 dark:text-gray-400">
                                                                            {formatFileSize(document.file_size)}
                                                                        </span>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <Status variant={getStatusVariant(document.status)}>
                                                                            <StatusIndicator />
                                                                            <StatusLabel>
                                                                                {getStatusLabel(document.status)}
                                                                            </StatusLabel>
                                                                        </Status>
                                                                        {document.rejection_reason && (
                                                                            <div className="mt-1 text-xs text-red-600 dark:text-red-400">
                                                                                {document.rejection_reason}
                                                                            </div>
                                                                        )}
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <span className="text-sm text-gray-500 dark:text-gray-400">
                                                                            {formatDate(document.verified_at || '')}
                                                                        </span>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <span className="text-sm text-gray-500 dark:text-gray-400">
                                                                            {formatDate(document.created_at)}
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

