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
        title: 'Kargo Takibi',
        href: '/shipping',
    },
];

interface Shipment {
    id: string;
    tracking_number: string;
    status: string;
    carrier: string;
    shipped_at: string | null;
    delivered_at: string | null;
    created_at: string;
    product?: {
        id: string;
        name: string;
    } | null;
    order?: {
        id: string;
        order_number: string;
    } | null;
}

interface Props {
    shipments?: Shipment[];
}

export default function Shipping({ shipments = [] }: Props) {
    const [isEditDialogOpen, setIsEditDialogOpen] = useState(false);
    const [selectedShipment, setSelectedShipment] = useState<Shipment | null>(null);
    const [status, setStatus] = useState('');
    const [trackingNumber, setTrackingNumber] = useState('');
    const [carrier, setCarrier] = useState('');

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

    const handleEdit = (shipment: Shipment) => {
        setSelectedShipment(shipment);
        setStatus(shipment.status);
        setTrackingNumber(shipment.tracking_number);
        setCarrier(shipment.carrier);
        setIsEditDialogOpen(true);
    };

    const handleUpdate = () => {
        if (!selectedShipment) {
            return;
        }

        router.put(
            `/shipping/${selectedShipment.id}`,
            {
                status,
                tracking_number: trackingNumber,
                carrier: carrier,
            },
            {
                onSuccess: () => {
                    setIsEditDialogOpen(false);
                    setSelectedShipment(null);
                    toast.success('Kargo bilgileri güncellendi.');
                },
                onError: (errors) => {
                    toast.error('Kargo güncellenirken bir hata oluştu.', {
                        description: Object.values(errors).join(', '),
                    });
                },
            },
        );
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Kargo Takibi" />
            <div className="relative flex h-full w-full flex-col gap-y-4">
                <main className="relative flex min-h-0 w-full grow flex-col">
                    <div className="flex h-full w-full flex-row gap-x-2 p-5">
                        <div className="relative flex w-full flex-col items-center rounded-2xl border-gray-200 px-4 md:overflow-y-auto md:border md:bg-white md:px-8 md:shadow-xs dark:border-[#313131] dark:md:bg-[#181818]">
                            <div className="flex h-full w-full max-w-[--breakpoint-xl] flex-col">
                                <div className="flex w-full flex-col gap-y-4 py-8 md:flex-row md:items-center md:justify-between md:py-8">
                                    <h4 className="text-2xl font-medium whitespace-nowrap dark:text-white">
                                        Kargo Takibi
                                    </h4>
                                </div>

                                <div className="flex w-full flex-col gap-y-8 pb-8">
                                    <p className="text-gray-500 dark:text-[#999999]">
                                        Tüm kargo gönderilerinizi takip edin.
                                    </p>

                                    <div className="flex flex-col gap-6">
                                        <div className="overflow-hidden rounded-2xl border border-gray-200 dark:border-[#313131]">
                                            <div className="relative w-full overflow-auto">
                                                <table className="w-full caption-bottom text-sm">
                                                    <thead className="[&_tr]:border-b">
                                                        <tr className="border-b bg-gray-50 transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted dark:bg-[#171719]">
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Takip No
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Sipariş
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Ürün
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Kargo Firması
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Durum
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Gönderim Tarihi
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                İşlemler
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody className="[&_tr:last-child]:border-0">
                                                        {shipments.length === 0 ? (
                                                            <tr>
                                                                <td
                                                                    colSpan={7}
                                                                    className="p-8 text-center text-gray-500 dark:text-gray-400"
                                                                >
                                                                    Henüz kargo gönderisi yok.
                                                                </td>
                                                            </tr>
                                                        ) : (
                                                            shipments.map((shipment) => (
                                                                <tr
                                                                    key={shipment.id}
                                                                    className="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted"
                                                                >
                                                                    <td className="p-4 align-middle">
                                                                        <div className="text-sm font-medium dark:text-white">
                                                                            {shipment.tracking_number || '-'}
                                                                        </div>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <div className="text-sm dark:text-white">
                                                                            {shipment.order?.order_number || '-'}
                                                                        </div>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <div className="text-sm dark:text-white">
                                                                            {shipment.product?.name || '-'}
                                                                        </div>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <div className="text-sm dark:text-white">
                                                                            {shipment.carrier || '-'}
                                                                        </div>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <span className="text-xs px-2 py-1 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300">
                                                                            {shipment.status}
                                                                        </span>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <span className="text-sm text-gray-500 dark:text-gray-400">
                                                                            {formatDate(shipment.shipped_at)}
                                                                        </span>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <Button
                                                                            variant="outline"
                                                                            size="sm"
                                                                            onClick={() => handleEdit(shipment)}
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
                        <DialogTitle>Kargo Düzenle</DialogTitle>
                        <DialogDescription>
                            Kargo bilgilerini güncelleyin.
                        </DialogDescription>
                    </DialogHeader>
                    {selectedShipment && (
                        <div className="space-y-4 py-4">
                            <div>
                                <Label className="mb-2 block">Takip Numarası</Label>
                                <input
                                    type="text"
                                    value={trackingNumber}
                                    onChange={(e) => setTrackingNumber(e.target.value)}
                                    placeholder="Takip numarası"
                                    className="w-full rounded-lg border p-2 text-sm dark:border-white/10 dark:bg-[#171719]"
                                />
                            </div>
                            <div>
                                <Label className="mb-2 block">Kargo Firması</Label>
                                <input
                                    type="text"
                                    value={carrier}
                                    onChange={(e) => setCarrier(e.target.value)}
                                    placeholder="Kargo firması"
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
                                    <option value="preparing">Hazırlanıyor</option>
                                    <option value="shipped">Kargoya Verildi</option>
                                    <option value="in_transit">Yolda</option>
                                    <option value="out_for_delivery">Dağıtımda</option>
                                    <option value="delivered">Teslim Edildi</option>
                                    <option value="returned">İade</option>
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

