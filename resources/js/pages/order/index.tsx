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
        title: 'Siparişler',
        href: '/orders',
    },
];

interface Address {
    id?: string;
    full_name?: string;
    phone?: string;
    address_line?: string;
    city?: string;
    district?: string;
    neighborhood?: string;
    postal_code?: string;
}

interface Order {
    id: string;
    order_number: string;
    total_price: string;
    status: string;
    payment_method?: string;
    created_at: string;
    user?: {
        id: string;
        name?: string;
        email?: string;
    } | null;
    address?: Address | null;
    items_count: number;
}

interface Props {
    orders?: Order[];
}

export default function Orders({ orders = [] }: Props) {
    const [isEditDialogOpen, setIsEditDialogOpen] = useState(false);
    const [selectedOrder, setSelectedOrder] = useState<Order | null>(null);
    const [selectedStatus, setSelectedStatus] = useState('');
    const [paymentMethod, setPaymentMethod] = useState('');
    const [address, setAddress] = useState<Address>({
        full_name: '',
        phone: '',
        address_line: '',
        city: '',
        district: '',
        neighborhood: '',
        postal_code: '',
    });

    const formatDate = (dateString: string) => {
        return new Intl.DateTimeFormat('tr-TR', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        }).format(new Date(dateString));
    };

    const handleEdit = (order: Order) => {
        setSelectedOrder(order);
        setSelectedStatus(order.status);
        setPaymentMethod(order.payment_method || '');
        setAddress({
            full_name: order.address?.full_name || '',
            phone: order.address?.phone || '',
            address_line: order.address?.address_line || '',
            city: order.address?.city || '',
            district: order.address?.district || '',
            neighborhood: order.address?.neighborhood || '',
            postal_code: order.address?.postal_code || '',
        });
        setIsEditDialogOpen(true);
    };

    const handleUpdate = () => {
        if (!selectedOrder) {
            return;
        }

        router.put(
            `/orders/${selectedOrder.id}`,
            {
                status: selectedStatus,
                payment_method: paymentMethod,
                address: address,
            },
            {
                onSuccess: () => {
                    setIsEditDialogOpen(false);
                    setSelectedOrder(null);
                    setSelectedStatus('');
                    setPaymentMethod('');
                    setAddress({
                        full_name: '',
                        phone: '',
                        address_line: '',
                        city: '',
                        district: '',
                        neighborhood: '',
                        postal_code: '',
                    });
                    toast.success('Sipariş başarıyla güncellendi.');
                },
                onError: (errors) => {
                    toast.error('Sipariş güncellenirken bir hata oluştu.', {
                        description: Object.values(errors).join(', '),
                    });
                },
            },
        );
    };

    const getStatusLabel = (status: string) => {
        const statusMap: Record<string, string> = {
            pending: 'Beklemede',
            processing: 'İşleniyor',
            shipped: 'Kargoya Verildi',
            delivered: 'Teslim Edildi',
            cancelled: 'İptal Edildi',
        };
        return statusMap[status] || status;
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Siparişler" />
            <div className="relative flex h-full w-full flex-col gap-y-4">
                <main className="relative flex min-h-0 w-full grow flex-col">
                    <div className="flex h-full w-full flex-row gap-x-2 p-5">
                        <div className="relative flex w-full flex-col items-center rounded-2xl border-gray-200 px-4 md:overflow-y-auto md:border md:bg-white md:px-8 md:shadow-xs dark:border-[#313131] dark:md:bg-[#181818]">
                            <div className="flex h-full w-full max-w-[--breakpoint-xl] flex-col">
                                <div className="flex w-full flex-col gap-y-4 py-8 md:flex-row md:items-center md:justify-between md:py-8">
                                    <h4 className="text-2xl font-medium whitespace-nowrap dark:text-white">
                                        Siparişler
                                    </h4>
                                </div>

                                <div className="flex w-full flex-col gap-y-8 pb-8">
                                    <p className="text-gray-500 dark:text-[#999999]">
                                        Satıcı panelinizden tüm siparişlerinizi görüntüleyin ve yönetin.
                                    </p>

                                    <div className="flex flex-col gap-6">
                                        <div className="overflow-hidden rounded-2xl border border-gray-200 dark:border-[#313131]">
                                            <div className="relative w-full overflow-auto">
                                                <table className="w-full caption-bottom text-sm">
                                                    <thead className="[&_tr]:border-b">
                                                        <tr className="border-b bg-gray-50 transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted dark:bg-[#171719]">
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Sipariş No
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Müşteri
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Toplam
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Durum
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Ürün Sayısı
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Tarih
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                İşlemler
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody className="[&_tr:last-child]:border-0">
                                                        {orders.length === 0 ? (
                                                            <tr>
                                                                <td
                                                                    colSpan={7}
                                                                    className="p-8 text-center text-gray-500 dark:text-gray-400"
                                                                >
                                                                    Henüz sipariş yok.
                                                                </td>
                                                            </tr>
                                                        ) : (
                                                            orders.map((order) => (
                                                                <tr
                                                                    key={order.id}
                                                                    className="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted"
                                                                >
                                                                    <td className="p-4 align-middle">
                                                                        <div className="text-sm font-medium dark:text-white">
                                                                            {order.order_number}
                                                                        </div>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <div className="text-sm dark:text-white">
                                                                            {order.user?.name || order.user?.email || 'Bilinmeyen'}
                                                                        </div>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <div className="text-sm font-medium dark:text-white">
                                                                            {parseFloat(order.total_price).toLocaleString('tr-TR', {
                                                                                style: 'currency',
                                                                                currency: 'TRY',
                                                                            })}
                                                                        </div>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <span className="text-xs px-2 py-1 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300">
                                                                            {getStatusLabel(order.status)}
                                                                        </span>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <div className="text-sm dark:text-white">
                                                                            {order.items_count}
                                                                        </div>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <span className="text-sm text-gray-500 dark:text-gray-400">
                                                                            {formatDate(order.created_at)}
                                                                        </span>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <Button
                                                                            variant="outline"
                                                                            size="sm"
                                                                            onClick={() => handleEdit(order)}
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
                <DialogContent className="sm:max-w-2xl max-h-[90vh] overflow-y-auto">
                    <DialogHeader>
                        <DialogTitle>Sipariş Düzenle</DialogTitle>
                        <DialogDescription>
                            Sipariş bilgilerini ve adres bilgilerini güncelleyin.
                        </DialogDescription>
                    </DialogHeader>
                    {selectedOrder && (
                        <div className="space-y-6 py-4">
                            {/* Sipariş Bilgileri */}
                            <div className="space-y-4">
                                <h3 className="text-sm font-semibold text-gray-900 dark:text-white">
                                    Sipariş Bilgileri
                                </h3>
                                <div className="grid grid-cols-2 gap-4">
                                    <div>
                                        <Label className="mb-2 block">Sipariş No</Label>
                                        <p className="text-sm font-medium dark:text-white">
                                            {selectedOrder.order_number}
                                        </p>
                                    </div>
                                    <div>
                                        <Label className="mb-2 block">Müşteri</Label>
                                        <p className="text-sm dark:text-white">
                                            {selectedOrder.user?.name ||
                                                selectedOrder.user?.email ||
                                                'Bilinmeyen'}
                                        </p>
                                    </div>
                                    <div>
                                        <Label className="mb-2 block">Toplam</Label>
                                        <p className="text-sm font-medium dark:text-white">
                                            {parseFloat(selectedOrder.total_price).toLocaleString(
                                                'tr-TR',
                                                {
                                                    style: 'currency',
                                                    currency: 'TRY',
                                                },
                                            )}
                                        </p>
                                    </div>
                                    <div>
                                        <Label className="mb-2 block">Ödeme Yöntemi</Label>
                                        <select
                                            value={paymentMethod}
                                            onChange={(e) => setPaymentMethod(e.target.value)}
                                            className="w-full rounded-lg border p-2 text-sm dark:border-white/10 dark:bg-[#171719]"
                                        >
                                            <option value="">Seçiniz</option>
                                            <option value="credit_card">Kredi Kartı</option>
                                            <option value="debit_card">Banka Kartı</option>
                                            <option value="bank_transfer">Banka Havalesi</option>
                                            <option value="cash_on_delivery">Kapıda Ödeme</option>
                                            <option value="wallet">Cüzdan</option>
                                            <option value="installment">Taksit</option>
                                        </select>
                                    </div>
                                    <div className="col-span-2">
                                        <Label className="mb-2 block">Durum</Label>
                                        <select
                                            value={selectedStatus}
                                            onChange={(e) => setSelectedStatus(e.target.value)}
                                            className="w-full rounded-lg border p-2 text-sm dark:border-white/10 dark:bg-[#171719]"
                                        >
                                            <option value="pending">Beklemede</option>
                                            <option value="processing">İşleniyor</option>
                                            <option value="shipped">Kargoya Verildi</option>
                                            <option value="delivered">Teslim Edildi</option>
                                            <option value="cancelled">İptal Edildi</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            {/* Adres Bilgileri */}
                            <div className="space-y-4 border-t pt-4">
                                <h3 className="text-sm font-semibold text-gray-900 dark:text-white">
                                    Teslimat Adresi
                                </h3>
                                <div className="grid grid-cols-2 gap-4">
                                    <div className="col-span-2">
                                        <Label className="mb-2 block">Ad Soyad</Label>
                                        <input
                                            type="text"
                                            value={address.full_name}
                                            onChange={(e) =>
                                                setAddress({ ...address, full_name: e.target.value })
                                            }
                                            placeholder="Ad Soyad"
                                            className="w-full rounded-lg border p-2 text-sm dark:border-white/10 dark:bg-[#171719]"
                                        />
                                    </div>
                                    <div>
                                        <Label className="mb-2 block">Telefon</Label>
                                        <input
                                            type="text"
                                            value={address.phone}
                                            onChange={(e) =>
                                                setAddress({ ...address, phone: e.target.value })
                                            }
                                            placeholder="Telefon"
                                            className="w-full rounded-lg border p-2 text-sm dark:border-white/10 dark:bg-[#171719]"
                                        />
                                    </div>
                                    <div>
                                        <Label className="mb-2 block">Posta Kodu</Label>
                                        <input
                                            type="text"
                                            value={address.postal_code}
                                            onChange={(e) =>
                                                setAddress({
                                                    ...address,
                                                    postal_code: e.target.value,
                                                })
                                            }
                                            placeholder="Posta Kodu"
                                            className="w-full rounded-lg border p-2 text-sm dark:border-white/10 dark:bg-[#171719]"
                                        />
                                    </div>
                                    <div>
                                        <Label className="mb-2 block">İl</Label>
                                        <input
                                            type="text"
                                            value={address.city}
                                            onChange={(e) =>
                                                setAddress({ ...address, city: e.target.value })
                                            }
                                            placeholder="İl"
                                            className="w-full rounded-lg border p-2 text-sm dark:border-white/10 dark:bg-[#171719]"
                                        />
                                    </div>
                                    <div>
                                        <Label className="mb-2 block">İlçe</Label>
                                        <input
                                            type="text"
                                            value={address.district}
                                            onChange={(e) =>
                                                setAddress({ ...address, district: e.target.value })
                                            }
                                            placeholder="İlçe"
                                            className="w-full rounded-lg border p-2 text-sm dark:border-white/10 dark:bg-[#171719]"
                                        />
                                    </div>
                                    <div>
                                        <Label className="mb-2 block">Mahalle</Label>
                                        <input
                                            type="text"
                                            value={address.neighborhood}
                                            onChange={(e) =>
                                                setAddress({
                                                    ...address,
                                                    neighborhood: e.target.value,
                                                })
                                            }
                                            placeholder="Mahalle"
                                            className="w-full rounded-lg border p-2 text-sm dark:border-white/10 dark:bg-[#171719]"
                                        />
                                    </div>
                                    <div className="col-span-2">
                                        <Label className="mb-2 block">Adres Satırı</Label>
                                        <textarea
                                            value={address.address_line}
                                            onChange={(e) =>
                                                setAddress({
                                                    ...address,
                                                    address_line: e.target.value,
                                                })
                                            }
                                            placeholder="Adres satırı"
                                            rows={3}
                                            className="w-full rounded-lg border p-2 text-sm dark:border-white/10 dark:bg-[#171719]"
                                        />
                                    </div>
                                </div>
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

