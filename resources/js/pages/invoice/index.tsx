import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import { Status, StatusIndicator, StatusLabel } from '@/components/ui/status';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Faturalar',
        href: '/invoices',
    },
];

interface Invoice {
    id: string;
    invoice_number: string;
    invoice_type: string;
    subtotal: string;
    tax_amount: string;
    total_amount: string;
    currency: string;
    status: string;
    order_id: string | null;
    order_number: string | null;
    sent_at: string | null;
    delivered_at: string | null;
    created_at: string;
}

interface Props {
    invoices?: Invoice[];
}

export default function Invoices({ invoices = [] }: Props) {
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
            case 'sent':
            case 'delivered':
                return 'success';
            case 'cancelled':
            case 'failed':
                return 'error';
            case 'pending':
                return 'warning';
            default:
                return 'default';
        }
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Faturalar" />
            <div className="relative flex h-full w-full flex-col gap-y-4">
                <main className="relative flex min-h-0 w-full grow flex-col">
                    <div className="flex h-full w-full flex-row gap-x-2 p-5">
                        <div className="relative flex w-full flex-col items-center rounded-2xl border-gray-200 px-4 md:overflow-y-auto md:border md:bg-white md:px-8 md:shadow-xs dark:border-[#313131] dark:md:bg-[#181818]">
                            <div className="flex h-full w-full max-w-[--breakpoint-xl] flex-col">
                                <div className="flex w-full flex-col gap-y-4 py-8 md:flex-row md:items-center md:justify-between md:py-8">
                                    <h4 className="text-2xl font-medium whitespace-nowrap dark:text-white">
                                        Faturalar
                                    </h4>
                                </div>

                                <div className="flex w-full flex-col gap-y-8 pb-8">
                                    <p className="text-gray-500 dark:text-[#999999]">
                                        Fatura geçmişinizi görüntüleyin.
                                    </p>

                                    <div className="flex flex-col gap-6">
                                        <div className="overflow-hidden rounded-2xl border border-gray-200 dark:border-[#313131]">
                                            <div className="relative w-full overflow-auto">
                                                <table className="w-full caption-bottom text-sm">
                                                    <thead className="[&_tr]:border-b">
                                                        <tr className="border-b bg-gray-50 transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted dark:bg-[#171719]">
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Fatura No
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Sipariş No
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Tutar
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Durum
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Gönderim Tarihi
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Oluşturulma
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody className="[&_tr:last-child]:border-0">
                                                        {invoices.length === 0 ? (
                                                            <tr>
                                                                <td
                                                                    colSpan={6}
                                                                    className="p-8 text-center text-gray-500 dark:text-gray-400"
                                                                >
                                                                    Henüz fatura yok.
                                                                </td>
                                                            </tr>
                                                        ) : (
                                                            invoices.map((invoice) => (
                                                                <tr
                                                                    key={invoice.id}
                                                                    className="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted"
                                                                >
                                                                    <td className="p-4 align-middle">
                                                                        <div className="text-sm font-medium dark:text-white">
                                                                            {invoice.invoice_number}
                                                                        </div>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <div className="text-sm dark:text-white">
                                                                            {invoice.order_number || '-'}
                                                                        </div>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <div className="text-sm font-medium dark:text-white">
                                                                            {parseFloat(invoice.total_amount).toLocaleString('tr-TR', {
                                                                                style: 'currency',
                                                                                currency: invoice.currency || 'TRY',
                                                                            })}
                                                                        </div>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <Status variant={getStatusVariant(invoice.status)}>
                                                                            <StatusIndicator />
                                                                            <StatusLabel>
                                                                                {invoice.status}
                                                                            </StatusLabel>
                                                                        </Status>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <span className="text-sm text-gray-500 dark:text-gray-400">
                                                                            {formatDate(invoice.sent_at || '')}
                                                                        </span>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <span className="text-sm text-gray-500 dark:text-gray-400">
                                                                            {formatDate(invoice.created_at)}
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

