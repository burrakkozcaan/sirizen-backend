import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Takipçiler',
        href: '/followers',
    },
];

interface Follower {
    id: string;
    created_at: string;
    user?: {
        id: string;
        name?: string;
        email?: string;
    } | null;
}

interface Props {
    followers?: Follower[];
}

export default function Followers({ followers = [] }: Props) {
    const formatDate = (dateString: string) => {
        return new Intl.DateTimeFormat('tr-TR', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        }).format(new Date(dateString));
    };

    const getUserInitials = (name?: string, email?: string) => {
        if (name) {
            return name.charAt(0).toUpperCase();
        }
        if (email) {
            return email.charAt(0).toUpperCase();
        }
        return '?';
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Takipçiler" />
            <div className="relative flex h-full w-full flex-col gap-y-4">
                <main className="relative flex min-h-0 w-full grow flex-col">
                    <div className="flex h-full w-full flex-row gap-x-2 p-5">
                        <div className="relative flex w-full flex-col items-center rounded-2xl border-gray-200 px-4 md:overflow-y-auto md:border md:bg-white md:px-8 md:shadow-xs dark:border-[#313131] dark:md:bg-[#181818]">
                            <div className="flex h-full w-full max-w-[--breakpoint-xl] flex-col">
                                <div className="flex w-full flex-col gap-y-4 py-8 md:flex-row md:items-center md:justify-between md:py-8">
                                    <h4 className="text-2xl font-medium whitespace-nowrap dark:text-white">
                                        Takipçiler
                                    </h4>
                                </div>

                                <div className="flex w-full flex-col gap-y-8 pb-8">
                                    <p className="text-gray-500 dark:text-[#999999]">
                                        Mağazanızı takip eden kullanıcıları görüntüleyin.
                                    </p>

                                    <div className="flex flex-col gap-6">
                                        <div className="overflow-hidden rounded-2xl border border-gray-200 dark:border-[#313131]">
                                            <div className="relative w-full overflow-auto">
                                                <table className="w-full caption-bottom text-sm">
                                                    <thead className="[&_tr]:border-b">
                                                        <tr className="border-b bg-gray-50 transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted dark:bg-[#171719]">
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Kullanıcı
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Takip Tarihi
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody className="[&_tr:last-child]:border-0">
                                                        {followers.length === 0 ? (
                                                            <tr>
                                                                <td
                                                                    colSpan={2}
                                                                    className="p-8 text-center text-gray-500 dark:text-gray-400"
                                                                >
                                                                    Henüz takipçi yok.
                                                                </td>
                                                            </tr>
                                                        ) : (
                                                            followers.map((follower) => (
                                                                <tr
                                                                    key={follower.id}
                                                                    className="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted"
                                                                >
                                                                    <td className="p-4 align-middle">
                                                                        <div className="flex flex-row items-center gap-2">
                                                                            <div className="relative z-2 flex h-8 w-8 shrink-0 items-center justify-center overflow-hidden rounded-full border-2 border-gray-200 bg-gray-50 text-sm dark:border-[#313131] dark:bg-[#171719]">
                                                                                <span className="text-xs font-medium dark:text-white">
                                                                                    {getUserInitials(
                                                                                        follower.user?.name,
                                                                                        follower.user?.email,
                                                                                    )}
                                                                                </span>
                                                                            </div>
                                                                            <div className="flex flex-col">
                                                                                <div className="text-sm font-medium dark:text-white">
                                                                                    {follower.user?.name ||
                                                                                        follower.user?.email ||
                                                                                        'Bilinmeyen'}
                                                                                </div>
                                                                                <div className="text-xs text-gray-500 dark:text-gray-400">
                                                                                    {follower.user?.email || ''}
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <span className="text-sm text-gray-500 dark:text-gray-400">
                                                                            {formatDate(follower.created_at)}
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

