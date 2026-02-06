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
        title: 'Kuponlar',
        href: '/coupons',
    },
];

interface Coupon {
    id: string;
    code: string;
    title: string;
    description: string;
    discount_type: string;
    discount_value: string;
    min_order_amount: string;
    usage_limit: number;
    starts_at: string;
    expires_at: string;
    is_active: boolean;
    created_at: string;
    product?: {
        id: string;
        name: string;
    } | null;
}

interface Props {
    coupons?: Coupon[];
}

export default function Coupons({ coupons = [] }: Props) {
    const [isEditDialogOpen, setIsEditDialogOpen] = useState(false);
    const [selectedCoupon, setSelectedCoupon] = useState<Coupon | null>(null);
    const [code, setCode] = useState('');
    const [title, setTitle] = useState('');
    const [description, setDescription] = useState('');
    const [discountType, setDiscountType] = useState('percentage');
    const [discountValue, setDiscountValue] = useState('');
    const [minOrderAmount, setMinOrderAmount] = useState('');
    const [usageLimit, setUsageLimit] = useState('');
    const [startsAt, setStartsAt] = useState('');
    const [expiresAt, setExpiresAt] = useState('');
    const [isActive, setIsActive] = useState(false);

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

    const handleEdit = (coupon: Coupon) => {
        setSelectedCoupon(coupon);
        setCode(coupon.code);
        setTitle(coupon.title);
        setDescription(coupon.description);
        setDiscountType(coupon.discount_type);
        setDiscountValue(coupon.discount_value);
        setMinOrderAmount(coupon.min_order_amount);
        setUsageLimit(coupon.usage_limit.toString());
        setStartsAt(coupon.starts_at.split('T')[0]);
        setExpiresAt(coupon.expires_at.split('T')[0]);
        setIsActive(coupon.is_active);
        setIsEditDialogOpen(true);
    };

    const handleUpdate = () => {
        if (!selectedCoupon) {
            return;
        }

        router.put(
            `/coupons/${selectedCoupon.id}`,
            {
                code,
                title,
                description,
                discount_type: discountType,
                discount_value: discountValue,
                min_order_amount: minOrderAmount || null,
                usage_limit: usageLimit || null,
                starts_at: startsAt,
                expires_at: expiresAt,
                is_active: isActive,
            },
            {
                onSuccess: () => {
                    setIsEditDialogOpen(false);
                    setSelectedCoupon(null);
                    toast.success('Kupon başarıyla güncellendi.');
                },
                onError: (errors) => {
                    toast.error('Kupon güncellenirken bir hata oluştu.', {
                        description: Object.values(errors).join(', '),
                    });
                },
            },
        );
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Kuponlar" />
            <div className="relative flex h-full w-full flex-col gap-y-4">
                <main className="relative flex min-h-0 w-full grow flex-col">
                    <div className="flex h-full w-full flex-row gap-x-2 p-5">
                        <div className="relative flex w-full flex-col items-center rounded-2xl border-gray-200 px-4 md:overflow-y-auto md:border md:bg-white md:px-8 md:shadow-xs dark:border-[#313131] dark:md:bg-[#181818]">
                            <div className="flex h-full w-full max-w-[--breakpoint-xl] flex-col">
                                <div className="flex w-full flex-col gap-y-4 py-8 md:flex-row md:items-center md:justify-between md:py-8">
                                    <h4 className="text-2xl font-medium whitespace-nowrap dark:text-white">
                                        Kuponlar
                                    </h4>
                                </div>

                                <div className="flex w-full flex-col gap-y-8 pb-8">
                                    <p className="text-gray-500 dark:text-[#999999]">
                                        Oluşturduğunuz kuponları görüntüleyin ve yönetin.
                                    </p>

                                    <div className="flex flex-col gap-6">
                                        <div className="overflow-hidden rounded-2xl border border-gray-200 dark:border-[#313131]">
                                            <div className="relative w-full overflow-auto">
                                                <table className="w-full caption-bottom text-sm">
                                                    <thead className="[&_tr]:border-b">
                                                        <tr className="border-b bg-gray-50 transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted dark:bg-[#171719]">
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Kod
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Başlık
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                İndirim
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Ürün
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Kullanım Limiti
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Geçerlilik
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Durum
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                İşlemler
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody className="[&_tr:last-child]:border-0">
                                                        {coupons.length === 0 ? (
                                                            <tr>
                                                                <td
                                                                    colSpan={8}
                                                                    className="p-8 text-center text-gray-500 dark:text-gray-400"
                                                                >
                                                                    Henüz kupon yok.
                                                                </td>
                                                            </tr>
                                                        ) : (
                                                            coupons.map((coupon) => (
                                                                <tr
                                                                    key={coupon.id}
                                                                    className="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted"
                                                                >
                                                                    <td className="p-4 align-middle">
                                                                        <div className="text-sm font-medium dark:text-white font-mono">
                                                                            {coupon.code}
                                                                        </div>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <div className="text-sm dark:text-white">
                                                                            {coupon.title}
                                                                        </div>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <div className="text-sm dark:text-white">
                                                                            {coupon.discount_type === 'percentage'
                                                                                ? `%${coupon.discount_value}`
                                                                                : `${parseFloat(coupon.discount_value).toLocaleString('tr-TR', {
                                                                                      style: 'currency',
                                                                                      currency: 'TRY',
                                                                                  })}`}
                                                                        </div>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <div className="text-sm dark:text-white">
                                                                            {coupon.product?.name || 'Tüm Ürünler'}
                                                                        </div>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <div className="text-sm dark:text-white">
                                                                            {coupon.usage_limit > 0 ? coupon.usage_limit : 'Sınırsız'}
                                                                        </div>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <div className="text-sm dark:text-white">
                                                                            {coupon.expires_at
                                                                                ? formatDate(coupon.expires_at)
                                                                                : 'Süresiz'}
                                                                        </div>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <span
                                                                            className={`text-xs px-2 py-1 rounded-full ${
                                                                                coupon.is_active
                                                                                    ? 'bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300'
                                                                                    : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300'
                                                                            }`}
                                                                        >
                                                                            {coupon.is_active ? 'Aktif' : 'Pasif'}
                                                                        </span>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <Button
                                                                            variant="outline"
                                                                            size="sm"
                                                                            onClick={() => handleEdit(coupon)}
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
                        <DialogTitle>Kupon Düzenle</DialogTitle>
                        <DialogDescription>
                            Kupon bilgilerini güncelleyin.
                        </DialogDescription>
                    </DialogHeader>
                    {selectedCoupon && (
                        <div className="space-y-4 py-4">
                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <Label className="mb-2 block">Kupon Kodu</Label>
                                    <input
                                        type="text"
                                        value={code}
                                        onChange={(e) => setCode(e.target.value)}
                                        placeholder="KUPON2024"
                                        className="w-full rounded-lg border p-2 text-sm dark:border-white/10 dark:bg-[#171719] font-mono"
                                    />
                                </div>
                                <div>
                                    <Label className="mb-2 block">Başlık</Label>
                                    <input
                                        type="text"
                                        value={title}
                                        onChange={(e) => setTitle(e.target.value)}
                                        placeholder="Kupon başlığı"
                                        className="w-full rounded-lg border p-2 text-sm dark:border-white/10 dark:bg-[#171719]"
                                    />
                                </div>
                            </div>
                            <div>
                                <Label className="mb-2 block">Açıklama</Label>
                                <textarea
                                    value={description}
                                    onChange={(e) => setDescription(e.target.value)}
                                    placeholder="Kupon açıklaması"
                                    rows={3}
                                    className="w-full rounded-lg border p-2 text-sm dark:border-white/10 dark:bg-[#171719]"
                                />
                            </div>
                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <Label className="mb-2 block">İndirim Tipi</Label>
                                    <select
                                        value={discountType}
                                        onChange={(e) => setDiscountType(e.target.value)}
                                        className="w-full rounded-lg border p-2 text-sm dark:border-white/10 dark:bg-[#171719]"
                                    >
                                        <option value="percentage">Yüzde (%)</option>
                                        <option value="fixed">Sabit Tutar (₺)</option>
                                    </select>
                                </div>
                                <div>
                                    <Label className="mb-2 block">İndirim Değeri</Label>
                                    <input
                                        type="text"
                                        inputMode="decimal"
                                        value={discountValue}
                                        onChange={(e) => setDiscountValue(e.target.value)}
                                        placeholder={discountType === 'percentage' ? '10' : '50.00'}
                                        className="w-full rounded-lg border p-2 text-sm dark:border-white/10 dark:bg-[#171719]"
                                    />
                                </div>
                            </div>
                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <Label className="mb-2 block">Minimum Sipariş Tutarı (₺)</Label>
                                    <input
                                        type="text"
                                        inputMode="decimal"
                                        value={minOrderAmount}
                                        onChange={(e) => setMinOrderAmount(e.target.value)}
                                        placeholder="0.00"
                                        className="w-full rounded-lg border p-2 text-sm dark:border-white/10 dark:bg-[#171719]"
                                    />
                                </div>
                                <div>
                                    <Label className="mb-2 block">Kullanım Limiti</Label>
                                    <input
                                        type="text"
                                        inputMode="numeric"
                                        value={usageLimit}
                                        onChange={(e) => setUsageLimit(e.target.value)}
                                        placeholder="Sınırsız için boş bırakın"
                                        className="w-full rounded-lg border p-2 text-sm dark:border-white/10 dark:bg-[#171719]"
                                    />
                                </div>
                            </div>
                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <Label className="mb-2 block">Başlangıç Tarihi</Label>
                                    <input
                                        type="date"
                                        value={startsAt}
                                        onChange={(e) => setStartsAt(e.target.value)}
                                        className="w-full rounded-lg border p-2 text-sm dark:border-white/10 dark:bg-[#171719]"
                                    />
                                </div>
                                <div>
                                    <Label className="mb-2 block">Bitiş Tarihi</Label>
                                    <input
                                        type="date"
                                        value={expiresAt}
                                        onChange={(e) => setExpiresAt(e.target.value)}
                                        className="w-full rounded-lg border p-2 text-sm dark:border-white/10 dark:bg-[#171719]"
                                    />
                                </div>
                            </div>
                            <div>
                                <label className="flex items-center gap-2">
                                    <input
                                        type="checkbox"
                                        checked={isActive}
                                        onChange={(e) => setIsActive(e.target.checked)}
                                        className="rounded border-gray-300"
                                    />
                                    <span className="text-sm dark:text-white">Aktif</span>
                                </label>
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

