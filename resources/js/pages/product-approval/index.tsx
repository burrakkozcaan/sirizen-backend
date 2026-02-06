import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import { Package, CheckCircle, XCircle, Clock, AlertCircle } from 'lucide-react';
import { Status, StatusIndicator, StatusLabel } from '@/components/ui/status';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Ürün Onayları',
        href: '/product-approvals',
    },
];

interface ProductApproval {
    id: string;
    product_id: string;
    product_name: string;
    status: string;
    rejection_reason: string | null;
    admin_notes: string | null;
    changes_requested: string[] | null;
    reviewer_name: string | null;
    submitted_at: string;
    reviewed_at: string | null;
}

interface StatusCounts {
    pending: number;
    approved: number;
    rejected: number;
    needs_changes: number;
}

interface Props {
    approvals?: ProductApproval[];
    statusCounts?: StatusCounts;
}

export default function ProductApprovals({ approvals = [], statusCounts }: Props) {
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
            case 'approved':
                return 'success';
            case 'rejected':
                return 'error';
            case 'needs_changes':
                return 'warning';
            case 'pending':
            default:
                return 'info';
        }
    };

    const getStatusLabel = (status: string) => {
        switch (status) {
            case 'approved':
                return 'Onaylandı';
            case 'rejected':
                return 'Reddedildi';
            case 'needs_changes':
                return 'Değişiklik Gerekli';
            case 'pending':
            default:
                return 'Beklemede';
        }
    };

    const getStatusIcon = (status: string) => {
        switch (status) {
            case 'approved':
                return CheckCircle;
            case 'rejected':
                return XCircle;
            case 'needs_changes':
                return AlertCircle;
            case 'pending':
            default:
                return Clock;
        }
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Ürün Onayları" />
            <div className="relative flex h-full w-full flex-col gap-y-4">
                <main className="relative flex min-h-0 w-full grow flex-col">
                    <div className="flex h-full w-full flex-row gap-x-2 p-5">
                        <div className="relative flex w-full flex-col items-center rounded-2xl border-gray-200 px-4 md:overflow-y-auto md:border md:bg-white md:px-8 md:shadow-xs dark:border-[#313131] dark:md:bg-[#181818]">
                            <div className="flex h-full w-full max-w-[--breakpoint-xl] flex-col">
                                <div className="flex w-full flex-col gap-y-4 py-8 md:flex-row md:items-center md:justify-between md:py-8">
                                    <h4 className="text-2xl font-medium whitespace-nowrap dark:text-white">
                                        Ürün Onayları
                                    </h4>
                                </div>

                                <div className="flex w-full flex-col gap-y-8 pb-8">
                                    <p className="text-gray-500 dark:text-[#999999]">
                                        Ürünlerinizin onay durumlarını görüntüleyin.
                                    </p>

                                    {statusCounts && (
                                        <div className="grid gap-6 md:grid-cols-4">
                                            <div className="rounded-2xl border border-gray-200 p-6 dark:border-[#313131]">
                                                <div className="flex items-center gap-3">
                                                    <div className="rounded-lg bg-blue-100 p-3 dark:bg-blue-900/20">
                                                        <Clock className="h-5 w-5 text-blue-600 dark:text-blue-400" />
                                                    </div>
                                                    <div>
                                                        <div className="text-sm text-gray-500 dark:text-gray-400">
                                                            Beklemede
                                                        </div>
                                                        <div className="mt-1 text-2xl font-bold dark:text-white">
                                                            {statusCounts.pending}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div className="rounded-2xl border border-gray-200 p-6 dark:border-[#313131]">
                                                <div className="flex items-center gap-3">
                                                    <div className="rounded-lg bg-green-100 p-3 dark:bg-green-900/20">
                                                        <CheckCircle className="h-5 w-5 text-green-600 dark:text-green-400" />
                                                    </div>
                                                    <div>
                                                        <div className="text-sm text-gray-500 dark:text-gray-400">
                                                            Onaylandı
                                                        </div>
                                                        <div className="mt-1 text-2xl font-bold dark:text-white">
                                                            {statusCounts.approved}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div className="rounded-2xl border border-gray-200 p-6 dark:border-[#313131]">
                                                <div className="flex items-center gap-3">
                                                    <div className="rounded-lg bg-red-100 p-3 dark:bg-red-900/20">
                                                        <XCircle className="h-5 w-5 text-red-600 dark:text-red-400" />
                                                    </div>
                                                    <div>
                                                        <div className="text-sm text-gray-500 dark:text-gray-400">
                                                            Reddedildi
                                                        </div>
                                                        <div className="mt-1 text-2xl font-bold dark:text-white">
                                                            {statusCounts.rejected}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div className="rounded-2xl border border-gray-200 p-6 dark:border-[#313131]">
                                                <div className="flex items-center gap-3">
                                                    <div className="rounded-lg bg-yellow-100 p-3 dark:bg-yellow-900/20">
                                                        <AlertCircle className="h-5 w-5 text-yellow-600 dark:text-yellow-400" />
                                                    </div>
                                                    <div>
                                                        <div className="text-sm text-gray-500 dark:text-gray-400">
                                                            Değişiklik Gerekli
                                                        </div>
                                                        <div className="mt-1 text-2xl font-bold dark:text-white">
                                                            {statusCounts.needs_changes}
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
                                                                Ürün
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Durum
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Gönderim Tarihi
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                İnceleme Tarihi
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                İnceleyen
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Notlar
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody className="[&_tr:last-child]:border-0">
                                                        {approvals.length === 0 ? (
                                                            <tr>
                                                                <td
                                                                    colSpan={6}
                                                                    className="p-8 text-center text-gray-500 dark:text-gray-400"
                                                                >
                                                                    Henüz onay kaydı yok.
                                                                </td>
                                                            </tr>
                                                        ) : (
                                                            approvals.map((approval) => (
                                                                <tr
                                                                    key={approval.id}
                                                                    className="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted"
                                                                >
                                                                    <td className="p-4 align-middle">
                                                                        <div className="text-sm font-medium dark:text-white">
                                                                            {approval.product_name}
                                                                        </div>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <Status variant={getStatusVariant(approval.status)}>
                                                                            <StatusIndicator />
                                                                            <StatusLabel>
                                                                                {getStatusLabel(approval.status)}
                                                                            </StatusLabel>
                                                                        </Status>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <span className="text-sm text-gray-500 dark:text-gray-400">
                                                                            {formatDate(approval.submitted_at)}
                                                                        </span>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <span className="text-sm text-gray-500 dark:text-gray-400">
                                                                            {approval.reviewed_at
                                                                                ? formatDate(approval.reviewed_at)
                                                                                : '-'}
                                                                        </span>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <div className="text-sm dark:text-white">
                                                                            {approval.reviewer_name || '-'}
                                                                        </div>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <div className="max-w-md">
                                                                            {approval.rejection_reason && (
                                                                                <p className="text-xs text-red-600 dark:text-red-400 line-clamp-2">
                                                                                    {approval.rejection_reason}
                                                                                </p>
                                                                            )}
                                                                            {approval.admin_notes && (
                                                                                <p className="text-xs text-gray-600 dark:text-gray-400 line-clamp-2">
                                                                                    {approval.admin_notes}
                                                                                </p>
                                                                            )}
                                                                            {approval.changes_requested &&
                                                                                approval.changes_requested.length > 0 && (
                                                                                    <ul className="mt-1 list-disc list-inside text-xs text-yellow-600 dark:text-yellow-400">
                                                                                        {approval.changes_requested
                                                                                            .slice(0, 2)
                                                                                            .map((change, idx) => (
                                                                                                <li key={idx} className="line-clamp-1">
                                                                                                    {change}
                                                                                                </li>
                                                                                            ))}
                                                                                    </ul>
                                                                                )}
                                                                            {!approval.rejection_reason &&
                                                                                !approval.admin_notes &&
                                                                                (!approval.changes_requested ||
                                                                                    approval.changes_requested.length === 0) && (
                                                                                    <span className="text-xs text-gray-400">
                                                                                        -
                                                                                    </span>
                                                                                )}
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

