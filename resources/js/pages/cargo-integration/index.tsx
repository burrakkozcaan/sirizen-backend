import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import { Status, StatusIndicator, StatusLabel } from '@/components/ui/status';
import { Package } from 'lucide-react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Kargo Entegrasyonları',
        href: '/cargo-integrations',
    },
];

interface CargoIntegration {
    id: string;
    integration_type: string;
    shipping_company_name: string | null;
    customer_code: string | null;
    is_active: boolean;
    is_test_mode: boolean;
    last_sync_at: string | null;
    last_error: string | null;
    created_at: string;
}

interface Props {
    integrations?: CargoIntegration[];
}

export default function CargoIntegrations({ integrations = [] }: Props) {
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

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Kargo Entegrasyonları" />
            <div className="relative flex h-full w-full flex-col gap-y-4">
                <main className="relative flex min-h-0 w-full grow flex-col">
                    <div className="flex h-full w-full flex-row gap-x-2 p-5">
                        <div className="relative flex w-full flex-col items-center rounded-2xl border-gray-200 px-4 md:overflow-y-auto md:border md:bg-white md:px-8 md:shadow-xs dark:border-[#313131] dark:md:bg-[#181818]">
                            <div className="flex h-full w-full max-w-[--breakpoint-xl] flex-col">
                                <div className="flex w-full flex-col gap-y-4 py-8 md:flex-row md:items-center md:justify-between md:py-8">
                                    <h4 className="text-2xl font-medium whitespace-nowrap dark:text-white">
                                        Kargo Entegrasyonları
                                    </h4>
                                </div>

                                <div className="flex w-full flex-col gap-y-8 pb-8">
                                    <p className="text-gray-500 dark:text-[#999999]">
                                        Kargo firmaları ile entegrasyonlarınızı yönetin.
                                    </p>

                                    <div className="flex flex-col gap-6">
                                        <div className="overflow-hidden rounded-2xl border border-gray-200 dark:border-[#313131]">
                                            <div className="relative w-full overflow-auto">
                                                <table className="w-full caption-bottom text-sm">
                                                    <thead className="[&_tr]:border-b">
                                                        <tr className="border-b bg-gray-50 transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted dark:bg-[#171719]">
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Kargo Firması
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Entegrasyon Tipi
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Müşteri Kodu
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Durum
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Son Senkronizasyon
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody className="[&_tr:last-child]:border-0">
                                                        {integrations.length === 0 ? (
                                                            <tr>
                                                                <td
                                                                    colSpan={5}
                                                                    className="p-8 text-center text-gray-500 dark:text-gray-400"
                                                                >
                                                                    Henüz kargo entegrasyonu yok.
                                                                </td>
                                                            </tr>
                                                        ) : (
                                                            integrations.map((integration) => (
                                                                <tr
                                                                    key={integration.id}
                                                                    className="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted"
                                                                >
                                                                    <td className="p-4 align-middle">
                                                                        <div className="flex items-center gap-2">
                                                                            <Package className="h-4 w-4 text-gray-500" />
                                                                            <span className="text-sm font-medium dark:text-white">
                                                                                {integration.shipping_company_name || '-'}
                                                                            </span>
                                                                        </div>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <div className="text-sm dark:text-white">
                                                                            {integration.integration_type}
                                                                        </div>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <div className="text-sm dark:text-white">
                                                                            {integration.customer_code || '-'}
                                                                        </div>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <div className="flex flex-col gap-1">
                                                                            <Status variant={integration.is_active ? 'success' : 'error'}>
                                                                                <StatusIndicator />
                                                                                <StatusLabel>
                                                                                    {integration.is_active ? 'Aktif' : 'Pasif'}
                                                                                </StatusLabel>
                                                                            </Status>
                                                                            {integration.is_test_mode && (
                                                                                <span className="text-xs text-yellow-600 dark:text-yellow-400">
                                                                                    Test Modu
                                                                                </span>
                                                                            )}
                                                                        </div>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <span className="text-sm text-gray-500 dark:text-gray-400">
                                                                            {formatDate(integration.last_sync_at || '')}
                                                                        </span>
                                                                        {integration.last_error && (
                                                                            <div className="mt-1 text-xs text-red-600 dark:text-red-400">
                                                                                Hata: {integration.last_error}
                                                                            </div>
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
                    </div>
                </main>
            </div>
        </AppLayout>
    );
}

