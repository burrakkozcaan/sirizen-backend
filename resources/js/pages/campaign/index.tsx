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
import { Edit, Plus } from 'lucide-react';
import { useState } from 'react';
import { toast } from 'sonner';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Kampanyalar',
        href: '/campaigns',
    },
];

interface Campaign {
    id: string;
    title: string;
    slug: string;
    description: string;
    discount_type: string;
    discount_value: string;
    starts_at: string;
    ends_at: string;
    is_active: boolean;
    created_at: string;
}

interface Props {
    campaigns?: Campaign[];
}

export default function Campaigns({ campaigns = [] }: Props) {
    const [isEditDialogOpen, setIsEditDialogOpen] = useState(false);
    const [isCreateDialogOpen, setIsCreateDialogOpen] = useState(false);
    const [selectedCampaign, setSelectedCampaign] = useState<Campaign | null>(null);
    const [title, setTitle] = useState('');
    const [description, setDescription] = useState('');
    const [discountType, setDiscountType] = useState('percentage');
    const [discountValue, setDiscountValue] = useState('');
    const [startsAt, setStartsAt] = useState('');
    const [endsAt, setEndsAt] = useState('');
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

    const handleEdit = (campaign: Campaign) => {
        setSelectedCampaign(campaign);
        setTitle(campaign.title);
        setDescription(campaign.description);
        setDiscountType(campaign.discount_type);
        setDiscountValue(campaign.discount_value);
        setStartsAt(campaign.starts_at.split('T')[0]);
        setEndsAt(campaign.ends_at.split('T')[0]);
        setIsActive(campaign.is_active);
        setIsEditDialogOpen(true);
    };

    const handleCreate = () => {
        router.post(
            '/campaigns',
            {
                title,
                description,
                discount_type: discountType,
                discount_value: discountValue,
                starts_at: startsAt,
                ends_at: endsAt,
                is_active: isActive,
            },
            {
                onSuccess: () => {
                    setIsCreateDialogOpen(false);
                    setTitle('');
                    setDescription('');
                    setDiscountType('percentage');
                    setDiscountValue('');
                    setStartsAt('');
                    setEndsAt('');
                    setIsActive(false);
                    toast.success('Kampanya başarıyla oluşturuldu.');
                },
                onError: (errors) => {
                    toast.error('Kampanya oluşturulurken bir hata oluştu.', {
                        description: Object.values(errors).join(', '),
                    });
                },
            },
        );
    };

    const handleUpdate = () => {
        if (!selectedCampaign) {
            return;
        }

        router.put(
            `/campaigns/${selectedCampaign.id}`,
            {
                title,
                description,
                discount_type: discountType,
                discount_value: discountValue,
                starts_at: startsAt,
                ends_at: endsAt,
                is_active: isActive,
            },
            {
                onSuccess: () => {
                    setIsEditDialogOpen(false);
                    setSelectedCampaign(null);
                    toast.success('Kampanya başarıyla güncellendi.');
                },
                onError: (errors) => {
                    toast.error('Kampanya güncellenirken bir hata oluştu.', {
                        description: Object.values(errors).join(', '),
                    });
                },
            },
        );
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Kampanyalar" />
            <div className="relative flex h-full w-full flex-col gap-y-4">
                <main className="relative flex min-h-0 w-full grow flex-col">
                    <div className="flex h-full w-full flex-row gap-x-2 p-5">
                        <div className="relative flex w-full flex-col items-center rounded-2xl border-gray-200 px-4 md:overflow-y-auto md:border md:bg-white md:px-8 md:shadow-xs dark:border-[#313131] dark:md:bg-[#181818]">
                            <div className="flex h-full w-full max-w-[--breakpoint-xl] flex-col">
                                <div className="flex w-full flex-col gap-y-4 py-8 md:flex-row md:items-center md:justify-between md:py-8">
                                    <h4 className="text-2xl font-medium whitespace-nowrap dark:text-white">
                                        Kampanyalar
                                    </h4>
                                    <Button
                                        onClick={() => {
                                            setTitle('');
                                            setDescription('');
                                            setDiscountType('percentage');
                                            setDiscountValue('');
                                            setStartsAt('');
                                            setEndsAt('');
                                            setIsActive(false);
                                            setIsCreateDialogOpen(true);
                                        }}
                                        className="flex items-center gap-2"
                                    >
                                        <Plus className="h-4 w-4" />
                                        Ekle
                                    </Button>
                                </div>

                                <div className="flex w-full flex-col gap-y-8 pb-8">
                                    <p className="text-gray-500 dark:text-[#999999]">
                                        Oluşturduğunuz kampanyaları görüntüleyin ve yönetin.
                                    </p>

                                    <div className="flex flex-col gap-6">
                                        <div className="overflow-hidden rounded-2xl border border-gray-200 dark:border-[#313131]">
                                            <div className="relative w-full overflow-auto">
                                                <table className="w-full caption-bottom text-sm">
                                                    <thead className="[&_tr]:border-b">
                                                        <tr className="border-b bg-gray-50 transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted dark:bg-[#171719]">
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Başlık
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                İndirim
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Başlangıç
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Bitiş
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Durum
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
                                                        {campaigns.length === 0 ? (
                                                            <tr>
                                                                <td
                                                                    colSpan={7}
                                                                    className="p-8 text-center text-gray-500 dark:text-gray-400"
                                                                >
                                                                    Henüz kampanya yok.
                                                                </td>
                                                            </tr>
                                                        ) : (
                                                            campaigns.map((campaign) => (
                                                                <tr
                                                                    key={campaign.id}
                                                                    className="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted"
                                                                >
                                                                    <td className="p-4 align-middle">
                                                                        <div className="text-sm font-medium dark:text-white">
                                                                            {campaign.title}
                                                                        </div>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <div className="text-sm dark:text-white">
                                                                            {campaign.discount_type === 'percentage'
                                                                                ? `%${campaign.discount_value}`
                                                                                : `${parseFloat(campaign.discount_value).toLocaleString('tr-TR', {
                                                                                      style: 'currency',
                                                                                      currency: 'TRY',
                                                                                  })}`}
                                                                        </div>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <span className="text-sm text-gray-500 dark:text-gray-400">
                                                                            {formatDate(campaign.starts_at)}
                                                                        </span>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <span className="text-sm text-gray-500 dark:text-gray-400">
                                                                            {formatDate(campaign.ends_at)}
                                                                        </span>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <span
                                                                            className={`text-xs px-2 py-1 rounded-full ${
                                                                                campaign.is_active
                                                                                    ? 'bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300'
                                                                                    : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300'
                                                                            }`}
                                                                        >
                                                                            {campaign.is_active ? 'Aktif' : 'Pasif'}
                                                                        </span>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <span className="text-sm text-gray-500 dark:text-gray-400">
                                                                            {formatDate(campaign.created_at)}
                                                                        </span>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <Button
                                                                            variant="outline"
                                                                            size="sm"
                                                                            onClick={() => handleEdit(campaign)}
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
                        <DialogTitle>Kampanya Düzenle</DialogTitle>
                        <DialogDescription>
                            Kampanya bilgilerini güncelleyin.
                        </DialogDescription>
                    </DialogHeader>
                    {selectedCampaign && (
                        <div className="space-y-4 py-4">
                            <div>
                                <Label className="mb-2 block">Başlık</Label>
                                <input
                                    type="text"
                                    value={title}
                                    onChange={(e) => setTitle(e.target.value)}
                                    placeholder="Kampanya başlığı"
                                    className="w-full rounded-lg border p-2 text-sm dark:border-white/10 dark:bg-[#171719]"
                                />
                            </div>
                            <div>
                                <Label className="mb-2 block">Açıklama</Label>
                                <textarea
                                    value={description}
                                    onChange={(e) => setDescription(e.target.value)}
                                    placeholder="Kampanya açıklaması"
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
                                        value={endsAt}
                                        onChange={(e) => setEndsAt(e.target.value)}
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

            {/* Oluşturma Dialog */}
            <Dialog open={isCreateDialogOpen} onOpenChange={setIsCreateDialogOpen}>
                <DialogContent className="sm:max-w-2xl max-h-[90vh] overflow-y-auto">
                    <DialogHeader>
                        <DialogTitle>Yeni Kampanya Oluştur</DialogTitle>
                        <DialogDescription>
                            Yeni bir kampanya oluşturun.
                        </DialogDescription>
                    </DialogHeader>
                    <div className="space-y-4 py-4">
                        <div>
                            <Label className="mb-2 block">Başlık</Label>
                            <input
                                type="text"
                                value={title}
                                onChange={(e) => setTitle(e.target.value)}
                                placeholder="Kampanya başlığı"
                                className="w-full rounded-lg border p-2 text-sm dark:border-white/10 dark:bg-[#171719]"
                            />
                        </div>
                        <div>
                            <Label className="mb-2 block">Açıklama</Label>
                            <textarea
                                value={description}
                                onChange={(e) => setDescription(e.target.value)}
                                placeholder="Kampanya açıklaması"
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
                                    value={endsAt}
                                    onChange={(e) => setEndsAt(e.target.value)}
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
                    <DialogFooter>
                        <Button
                            variant="outline"
                            onClick={() => setIsCreateDialogOpen(false)}
                        >
                            İptal
                        </Button>
                        <Button onClick={handleCreate}>Oluştur</Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </AppLayout>
    );
}

