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
import { Status, StatusIndicator, StatusLabel } from '@/components/ui/status';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/react';
import { Shield, X, Plus, FileText } from 'lucide-react';
import { useState } from 'react';
import { toast } from 'sonner';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Marka Yetkilendirmeleri',
        href: '/brand-authorizations',
    },
];

interface AuthorizedVendor {
    id: string;
    name: string;
    authorization_type: string;
    status: string;
    valid_from: string | null;
    valid_until: string | null;
}

interface OwnedBrand {
    id: string;
    name: string;
    slug: string;
    logo: string | null;
    authorized_vendors: AuthorizedVendor[];
}

interface AuthorizedBrand {
    id: string;
    name: string;
    slug: string;
    logo: string | null;
    authorization_type: string;
    status: string;
    valid_from: string | null;
    valid_until: string | null;
}

interface Brand {
    id: string;
    name: string;
}

interface Vendor {
    id: string;
    name: string;
}

interface Props {
    ownedBrands?: OwnedBrand[];
    authorizedBrands?: AuthorizedBrand[];
    allBrands?: Brand[];
    allVendors?: Vendor[];
}

export default function BrandAuthorization({
    ownedBrands = [],
    authorizedBrands = [],
    allBrands = [],
    allVendors = [],
}: Props) {
    const [isAuthorizeDialogOpen, setIsAuthorizeDialogOpen] = useState(false);
    const [selectedBrandId, setSelectedBrandId] = useState('');
    const [selectedVendorId, setSelectedVendorId] = useState('');
    const [authorizationType, setAuthorizationType] = useState('authorized_dealer');
    const [validFrom, setValidFrom] = useState('');
    const [validUntil, setValidUntil] = useState('');

    const handleAuthorize = () => {
        if (!selectedBrandId || !selectedVendorId) {
            toast.error('Lütfen marka ve satıcı seçin.');
            return;
        }

        router.post(
            '/brand-authorizations/authorize',
            {
                brand_id: selectedBrandId,
                vendor_id: selectedVendorId,
                authorization_type: authorizationType,
                valid_from: validFrom || null,
                valid_until: validUntil || null,
            },
            {
                onSuccess: () => {
                    setIsAuthorizeDialogOpen(false);
                    setSelectedBrandId('');
                    setSelectedVendorId('');
                    setAuthorizationType('authorized_dealer');
                    setValidFrom('');
                    setValidUntil('');
                    toast.success('Yetkilendirme başarıyla oluşturuldu.');
                },
                onError: (errors) => {
                    toast.error('Yetkilendirme oluşturulurken bir hata oluştu.', {
                        description: Object.values(errors).join(', '),
                    });
                },
            },
        );
    };

    const handleRevoke = (brandId: string, vendorId: string) => {
        if (!confirm('Bu yetkilendirmeyi iptal etmek istediğinize emin misiniz?')) {
            return;
        }

        router.post(
            '/brand-authorizations/revoke',
            {
                brand_id: brandId,
                vendor_id: vendorId,
            },
            {
                onSuccess: () => {
                    toast.success('Yetkilendirme iptal edildi.');
                },
                onError: (errors) => {
                    toast.error('Yetkilendirme iptal edilirken bir hata oluştu.', {
                        description: Object.values(errors).join(', '),
                    });
                },
            },
        );
    };

    const getStatusVariant = (status: string) => {
        switch (status) {
            case 'approved':
                return 'success';
            case 'pending':
                return 'warning';
            case 'rejected':
                return 'error';
            case 'expired':
                return 'error';
            default:
                return 'secondary';
        }
    };

    const getAuthorizationTypeLabel = (type: string) => {
        switch (type) {
            case 'owner':
                return 'Marka Sahibi';
            case 'authorized_dealer':
                return 'Yetkili Satıcı';
            case 'invoice_chain':
                return 'Fatura Silsilesi';
            default:
                return type;
        }
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Marka Yetkilendirmeleri" />
            <div className="relative flex h-full w-full flex-col gap-y-4">
                <main className="relative flex min-h-0 w-full grow flex-col">
                    <div className="flex h-full w-full flex-row gap-x-2 p-5">
                        <div className="relative flex w-full flex-col items-center rounded-2xl border border-gray-200 px-4 md:overflow-y-auto md:border md:bg-white md:px-8 md:shadow-xs dark:border-[#313131] dark:md:bg-[#181818]">
                            <div className="flex h-full w-full flex-col">
                                <div className="flex w-full flex-col gap-y-4 py-8 md:flex-row md:items-center md:justify-between md:py-8">
                                    <h4 className="text-lg font-medium whitespace-nowrap dark:text-white">
                                        Marka Yetkilendirmeleri
                                    </h4>
                                </div>

                                {/* Sahip Olunan Markalar */}
                                <div className="mb-8 space-y-4">
                                    <div className="flex items-center justify-between">
                                        <h5 className="text-base font-semibold text-gray-900 dark:text-white">
                                            Sahip Olduğum Markalar
                                        </h5>
                                        <Button
                                            onClick={() => setIsAuthorizeDialogOpen(true)}
                                            className="flex items-center gap-2"
                                        >
                                            <Plus className="h-4 w-4" />
                                            Satıcı Yetkilendir
                                        </Button>
                                    </div>

                                    {ownedBrands.length === 0 ? (
                                        <div className="rounded-lg border border-gray-200 bg-gray-50 p-8 text-center dark:border-[#313131] dark:bg-[#1a1a1a]">
                                            <Shield className="mx-auto h-12 w-12 text-gray-400" />
                                            <p className="mt-4 text-sm text-gray-500 dark:text-gray-400">
                                                Henüz sahip olduğunuz bir marka yok.
                                            </p>
                                        </div>
                                    ) : (
                                        <div className="space-y-4">
                                            {ownedBrands.map((brand) => (
                                                <div
                                                    key={brand.id}
                                                    className="rounded-lg border border-gray-200 bg-white p-4 dark:border-[#313131] dark:bg-[#1a1a1a]"
                                                >
                                                    <div className="mb-3 flex items-center gap-3">
                                                        {brand.logo && (
                                                            <img
                                                                src={brand.logo}
                                                                alt={brand.name}
                                                                className="h-10 w-10 rounded object-cover"
                                                            />
                                                        )}
                                                        <div>
                                                            <h6 className="font-semibold text-gray-900 dark:text-white">
                                                                {brand.name}
                                                            </h6>
                                                            <p className="text-xs text-gray-500 dark:text-gray-400">
                                                                {brand.authorized_vendors.length} yetkili satıcı
                                                            </p>
                                                        </div>
                                                    </div>

                                                    {brand.authorized_vendors.length === 0 ? (
                                                        <p className="text-sm text-gray-500 dark:text-gray-400">
                                                            Henüz yetkili satıcı yok.
                                                        </p>
                                                    ) : (
                                                        <div className="space-y-2">
                                                            {brand.authorized_vendors.map((vendor) => (
                                                                <div
                                                                    key={vendor.id}
                                                                    className="flex items-center justify-between rounded border border-gray-200 bg-gray-50 p-3 dark:border-[#313131] dark:bg-[#252525]"
                                                                >
                                                                    <div className="flex items-center gap-3">
                                                                        <div>
                                                                            <p className="font-medium text-gray-900 dark:text-white">
                                                                                {vendor.name}
                                                                            </p>
                                                                            <p className="text-xs text-gray-500 dark:text-gray-400">
                                                                                {getAuthorizationTypeLabel(
                                                                                    vendor.authorization_type,
                                                                                )}
                                                                            </p>
                                                                        </div>
                                                                        <Status
                                                                            variant={
                                                                                getStatusVariant(
                                                                                    vendor.status,
                                                                                ) as any
                                                                            }
                                                                        >
                                                                            <StatusLabel>
                                                                                {vendor.status === 'approved'
                                                                                    ? 'Onaylandı'
                                                                                    : vendor.status === 'pending'
                                                                                      ? 'Beklemede'
                                                                                      : vendor.status === 'rejected'
                                                                                        ? 'Reddedildi'
                                                                                        : 'Süresi Doldu'}
                                                                            </StatusLabel>
                                                                        </Status>
                                                                    </div>
                                                                    <Button
                                                                        variant="outline"
                                                                        size="sm"
                                                                        onClick={() =>
                                                                            handleRevoke(
                                                                                brand.id,
                                                                                vendor.id,
                                                                            )
                                                                        }
                                                                        className="text-red-600 hover:text-red-700"
                                                                    >
                                                                        <X className="h-4 w-4" />
                                                                    </Button>
                                                                </div>
                                                            ))}
                                                        </div>
                                                    )}
                                                </div>
                                            ))}
                                        </div>
                                    )}
                                </div>

                                {/* Yetkili Olduğum Markalar */}
                                <div className="space-y-4">
                                    <h5 className="text-base font-semibold text-gray-900 dark:text-white">
                                        Yetkili Olduğum Markalar
                                    </h5>

                                    {authorizedBrands.length === 0 ? (
                                        <div className="rounded-lg border border-gray-200 bg-gray-50 p-8 text-center dark:border-[#313131] dark:bg-[#1a1a1a]">
                                            <FileText className="mx-auto h-12 w-12 text-gray-400" />
                                            <p className="mt-4 text-sm text-gray-500 dark:text-gray-400">
                                                Henüz yetkili olduğunuz bir marka yok.
                                            </p>
                                        </div>
                                    ) : (
                                        <div className="space-y-4">
                                            {authorizedBrands.map((brand) => (
                                                <div
                                                    key={brand.id}
                                                    className="rounded-lg border border-gray-200 bg-white p-4 dark:border-[#313131] dark:bg-[#1a1a1a]"
                                                >
                                                    <div className="flex items-center justify-between">
                                                        <div className="flex items-center gap-3">
                                                            {brand.logo && (
                                                                <img
                                                                    src={brand.logo}
                                                                    alt={brand.name}
                                                                    className="h-10 w-10 rounded object-cover"
                                                                />
                                                            )}
                                                            <div>
                                                                <h6 className="font-semibold text-gray-900 dark:text-white">
                                                                    {brand.name}
                                                                </h6>
                                                                <p className="text-xs text-gray-500 dark:text-gray-400">
                                                                    {getAuthorizationTypeLabel(
                                                                        brand.authorization_type,
                                                                    )}
                                                                </p>
                                                            </div>
                                                        </div>
                                                        <Status
                                                            variant={
                                                                getStatusVariant(
                                                                    brand.status,
                                                                ) as any
                                                            }
                                                        >
                                                            <StatusLabel>
                                                                {brand.status === 'approved'
                                                                    ? 'Onaylandı'
                                                                    : brand.status === 'pending'
                                                                      ? 'Beklemede'
                                                                      : brand.status === 'rejected'
                                                                        ? 'Reddedildi'
                                                                        : 'Süresi Doldu'}
                                                            </StatusLabel>
                                                        </Status>
                                                    </div>
                                                    {brand.valid_from && (
                                                        <p className="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                                            Geçerlilik:{' '}
                                                            {new Date(
                                                                brand.valid_from,
                                                            ).toLocaleDateString('tr-TR')}
                                                            {brand.valid_until &&
                                                                ` - ${new Date(
                                                                    brand.valid_until,
                                                                ).toLocaleDateString('tr-TR')}`}
                                                        </p>
                                                    )}
                                                </div>
                                            ))}
                                        </div>
                                    )}
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>

            {/* Yetkilendirme Dialog */}
            <Dialog
                open={isAuthorizeDialogOpen}
                onOpenChange={setIsAuthorizeDialogOpen}
            >
                <DialogContent className="sm:max-w-lg">
                    <DialogHeader>
                        <DialogTitle>Satıcı Yetkilendir</DialogTitle>
                        <DialogDescription>
                            Bir marka için satıcı yetkilendirmesi oluşturun.
                        </DialogDescription>
                    </DialogHeader>
                    <div className="space-y-4 py-4">
                        <div>
                            <Label className="mb-2 block">Marka</Label>
                            <select
                                value={selectedBrandId}
                                onChange={(e) =>
                                    setSelectedBrandId(e.target.value)
                                }
                                className="w-full rounded-lg border p-2 text-sm dark:border-white/10 dark:bg-[#171719]"
                            >
                                <option value="">Marka seçin</option>
                                {ownedBrands.map((brand) => (
                                    <option key={brand.id} value={brand.id}>
                                        {brand.name}
                                    </option>
                                ))}
                            </select>
                        </div>

                        <div>
                            <Label className="mb-2 block">Satıcı</Label>
                            <select
                                value={selectedVendorId}
                                onChange={(e) =>
                                    setSelectedVendorId(e.target.value)
                                }
                                className="w-full rounded-lg border p-2 text-sm dark:border-white/10 dark:bg-[#171719]"
                            >
                                <option value="">Satıcı seçin</option>
                                {allVendors.map((vendor) => (
                                    <option key={vendor.id} value={vendor.id}>
                                        {vendor.name}
                                    </option>
                                ))}
                            </select>
                        </div>

                        <div>
                            <Label className="mb-2 block">Yetkilendirme Tipi</Label>
                            <select
                                value={authorizationType}
                                onChange={(e) =>
                                    setAuthorizationType(e.target.value)
                                }
                                className="w-full rounded-lg border p-2 text-sm dark:border-white/10 dark:bg-[#171719]"
                            >
                                <option value="authorized_dealer">
                                    Yetkili Satıcı
                                </option>
                                <option value="invoice_chain">
                                    Fatura Silsilesi
                                </option>
                            </select>
                        </div>

                        <div className="grid grid-cols-2 gap-4">
                            <div>
                                <Label className="mb-2 block">
                                    Geçerlilik Başlangıcı
                                </Label>
                                <input
                                    type="date"
                                    value={validFrom}
                                    onChange={(e) => setValidFrom(e.target.value)}
                                    className="w-full rounded-lg border p-2 text-sm dark:border-white/10 dark:bg-[#171719]"
                                />
                            </div>
                            <div>
                                <Label className="mb-2 block">
                                    Geçerlilik Bitişi
                                </Label>
                                <input
                                    type="date"
                                    value={validUntil}
                                    onChange={(e) =>
                                        setValidUntil(e.target.value)
                                    }
                                    className="w-full rounded-lg border p-2 text-sm dark:border-white/10 dark:bg-[#171719]"
                                />
                            </div>
                        </div>
                    </div>
                    <DialogFooter>
                        <Button
                            variant="outline"
                            onClick={() => setIsAuthorizeDialogOpen(false)}
                        >
                            İptal
                        </Button>
                        <Button
                            onClick={handleAuthorize}
                            disabled={!selectedBrandId || !selectedVendorId}
                        >
                            Yetkilendir
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </AppLayout>
    );
}

