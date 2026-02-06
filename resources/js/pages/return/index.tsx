import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/react';
import { Edit } from 'lucide-react';
import { useState } from 'react';
import { toast } from 'sonner';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'İadeler',
        href: '/returns',
    },
];

interface Return {
    id: string;
    reason: string;
    status: string;
    refund_amount: string;
    requested_at: string | null;
    created_at: string;
    product?: {
        id: string;
        name: string;
    } | null;
    user?: {
        id: string;
        name?: string;
        email?: string;
    } | null;
}

interface Props {
    returns?: Return[];
}

export default function Returns({ returns = [] }: Props) {
    const [isEditDialogOpen, setIsEditDialogOpen] = useState(false);
    const [selectedReturn, setSelectedReturn] = useState<Return | null>(null);
    const [status, setStatus] = useState('');
    const [refundAmount, setRefundAmount] = useState('');

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

    const handleEdit = (returnItem: Return) => {
        setSelectedReturn(returnItem);
        setStatus(returnItem.status);
        setRefundAmount(returnItem.refund_amount);
        setIsEditDialogOpen(true);
    };

    const handleUpdate = () => {
        if (!selectedReturn) {
            return;
        }

        router.put(
            `/returns/${selectedReturn.id}`,
            {
                status,
                refund_amount: refundAmount,
            },
            {
                onSuccess: () => {
                    setIsEditDialogOpen(false);
                    setSelectedReturn(null);
                    toast.success('İade durumu güncellendi.');
                },
                onError: (errors) => {
                    toast.error('İade güncellenirken bir hata oluştu.', {
                        description: Object.values(errors).join(', '),
                    });
                },
            },
        );
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="İadeler" />
            <div className="relative flex h-full w-full flex-col gap-y-4">
                <main className="relative flex min-h-0 w-full grow flex-col">
                    <div className="flex h-full w-full flex-row gap-x-2 p-5">
                        <div className="relative flex w-full flex-col items-center rounded-2xl border-gray-200 px-4 md:overflow-y-auto md:border md:bg-white md:px-8 md:shadow-xs dark:border-[#313131] dark:md:bg-[#181818]">
                            <div className="flex h-full w-full max-w-[--breakpoint-xl] flex-col">
                                <div className="flex w-full flex-col gap-y-4 py-8 md:flex-row md:items-center md:justify-between md:py-8">
                                    <h4 className="text-2xl font-medium whitespace-nowrap dark:text-white">
                                        İadeler
                                    </h4>
                                </div>

                                <div className="flex w-full flex-col gap-y-8 pb-8">
                                    <p className="text-gray-500 dark:text-[#999999]">
                                        İade taleplerini görüntüleyin ve yönetin.
                                    </p>

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
                                                                Müşteri
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Sebep
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                İade Tutarı
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Durum
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Talep Tarihi
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                İşlemler
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody className="[&_tr:last-child]:border-0">
                                                        {returns.length === 0 ? (
                                                            <tr>
                                                                <td
                                                                    colSpan={7}
                                                                    className="p-8 text-center text-gray-500 dark:text-gray-400"
                                                                >
                                                                    Henüz iade talebi yok.
                                                                </td>
                                                            </tr>
                                                        ) : (
                                                            returns.map((returnItem) => (
                                                                <tr
                                                                    key={returnItem.id}
                                                                    className="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted"
                                                                >
                                                                    <td className="p-4 align-middle">
                                                                        <div className="text-sm font-medium dark:text-white">
                                                                            {returnItem.product?.name || '-'}
                                                                        </div>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <div className="text-sm dark:text-white">
                                                                            {returnItem.user?.name || returnItem.user?.email || '-'}
                                                                        </div>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <div className="text-sm dark:text-white">
                                                                            {returnItem.reason || '-'}
                                                                        </div>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <div className="text-sm font-medium dark:text-white">
                                                                            {parseFloat(returnItem.refund_amount).toLocaleString('tr-TR', {
                                                                                style: 'currency',
                                                                                currency: 'TRY',
                                                                            })}
                                                                        </div>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <span className="text-xs px-2 py-1 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300">
                                                                            {returnItem.status}
                                                                        </span>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <span className="text-sm text-gray-500 dark:text-gray-400">
                                                                            {formatDate(returnItem.requested_at)}
                                                                        </span>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <Button
                                                                            variant="outline"
                                                                            size="sm"
                                                                            onClick={() => handleEdit(returnItem)}
                                                                            className="flex items-center gap-2"
                                                                        >
                                                                            <Edit className="h-4 w-4" />
                                                                            Düzenle
                                                                        </Button>
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

            {/* Düzenleme Dialog */}
            <Dialog open={isEditDialogOpen} onOpenChange={setIsEditDialogOpen}>
                <DialogContent className="sm:max-w-lg">
                    <DialogHeader>
                        <DialogTitle>İade Düzenle</DialogTitle>
                        <DialogDescription>
                            İade durumunu ve tutarını güncelleyin.
                        </DialogDescription>
                    </DialogHeader>
                    {selectedReturn && (
                        <div className="space-y-4 py-4">
                            <div>
                                <Label className="mb-2 block">İade Tutarı (₺)</Label>
                                <input
                                    type="text"
                                    inputMode="decimal"
                                    value={refundAmount}
                                    onChange={(e) => setRefundAmount(e.target.value)}
                                    placeholder="0.00"
                                    className="w-full rounded-lg border p-2 text-sm dark:border-white/10 dark:bg-[#171719]"
                                />
                            </div>
                            <div>
                                <Label className="mb-2 block">Durum</Label>
                                <select
                                    value={status}
                                    onChange={(e) => setStatus(e.target.value)}
                                    className="w-full rounded-lg border p-2 text-sm dark:border-white/10 dark:bg-[#171719]"
                                >
                                    <option value="pending">Beklemede</option>
                                    <option value="approved">Onaylandı</option>
                                    <option value="rejected">Reddedildi</option>
                                    <option value="processing">İşleniyor</option>
                                    <option value="completed">Tamamlandı</option>
                                </select>
                            </div>
                        </div>
                    )}
                    <DialogFooter>
                        <Button
                            variant="outline"
                            onClick={() => setIsEditDialogOpen(false)}
                        >
                            İptal
                        </Button>
                        <Button onClick={handleUpdate}>Kaydet</Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </AppLayout>
    );
}

