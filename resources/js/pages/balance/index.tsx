import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Bakiye',
        href: '/balance',
    },
];

interface Balance {
    current_balance: string;
    pending_balance: string;
    total_earned: string;
    last_updated: string;
}

interface Props {
    balance?: Balance;
}

export default function Balance({ balance }: Props) {
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
            <Head title="Bakiye" />
            <div className="relative flex h-full w-full flex-col gap-y-4">
                <main className="relative flex min-h-0 w-full grow flex-col">
                    <div className="flex h-full w-full flex-row gap-x-2 p-5">
                        <div className="relative flex w-full flex-col items-center rounded-2xl border-gray-200 px-4 md:overflow-y-auto md:border md:bg-white md:px-8 md:shadow-xs dark:border-[#313131] dark:md:bg-[#181818]">
                            <div className="flex h-full w-full max-w-[--breakpoint-xl] flex-col">
                                <div className="flex w-full flex-col gap-y-4 py-8 md:flex-row md:items-center md:justify-between md:py-8">
                                    <h4 className="text-2xl font-medium whitespace-nowrap dark:text-white">
                                        Bakiye
                                    </h4>
                                </div>

                                <div className="flex w-full flex-col gap-y-8 pb-8">
                                    <p className="text-gray-500 dark:text-[#999999]">
                                        Hesap bakiyenizi ve kazançlarınızı görüntüleyin.
                                    </p>

                                    <div className="grid gap-6 md:grid-cols-3">
                                        <div className="rounded-2xl border border-gray-200 p-6 dark:border-[#313131]">
                                            <div className="text-sm text-gray-500 dark:text-gray-400">
                                                Mevcut Bakiye
                                            </div>
                                            <div className="mt-2 text-2xl font-bold dark:text-white">
                                                {parseFloat(balance?.current_balance || '0').toLocaleString('tr-TR', {
                                                    style: 'currency',
                                                    currency: 'TRY',
                                                })}
                                            </div>
                                        </div>
                                        <div className="rounded-2xl border border-gray-200 p-6 dark:border-[#313131]">
                                            <div className="text-sm text-gray-500 dark:text-gray-400">
                                                Bekleyen Bakiye
                                            </div>
                                            <div className="mt-2 text-2xl font-bold dark:text-white">
                                                {parseFloat(balance?.pending_balance || '0').toLocaleString('tr-TR', {
                                                    style: 'currency',
                                                    currency: 'TRY',
                                                })}
                                            </div>
                                        </div>
                                        <div className="rounded-2xl border border-gray-200 p-6 dark:border-[#313131]">
                                            <div className="text-sm text-gray-500 dark:text-gray-400">
                                                Toplam Kazanç
                                            </div>
                                            <div className="mt-2 text-2xl font-bold dark:text-white">
                                                {parseFloat(balance?.total_earned || '0').toLocaleString('tr-TR', {
                                                    style: 'currency',
                                                    currency: 'TRY',
                                                })}
                                            </div>
                                        </div>
                                    </div>

                                    <div className="text-sm text-gray-500 dark:text-gray-400">
                                        Son güncelleme: {formatDate(balance?.last_updated || '')}
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

