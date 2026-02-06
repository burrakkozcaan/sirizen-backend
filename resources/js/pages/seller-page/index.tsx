import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import { Button } from '@/components/ui/button';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import {
    Form,
    FormControl,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from '@/components/ui/form';
import { Eye, Save, Upload, Store } from 'lucide-react';
import { toast } from 'sonner';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Mağaza Sayfası',
        href: '/seller-page',
    },
];

interface SellerPage {
    id: string;
    vendor_id: string;
    seo_slug: string;
    description: string | null;
    banner: string | null;
    logo: string | null;
    created_at: string;
    updated_at: string;
}

interface Vendor {
    id: string;
    name: string;
    slug: string;
}

interface Props {
    sellerPage: SellerPage;
    vendor: Vendor;
}

const sellerPageFormSchema = z.object({
    seo_slug: z.string().min(1, 'Mağaza URL gereklidir'),
    description: z.string().max(5000, 'Açıklama en fazla 5000 karakter olabilir').optional(),
    logo: z.any().optional(),
    banner: z.any().optional(),
});

type SellerPageFormValues = z.infer<typeof sellerPageFormSchema>;

export default function SellerPage({ sellerPage, vendor }: Props) {
    const [isLoading, setIsLoading] = useState(false);
    const [error, setError] = useState<string | undefined>();
    const [showPreview, setShowPreview] = useState(false);
    const [logoPreview, setLogoPreview] = useState<string | null>(sellerPage.logo);
    const [bannerPreview, setBannerPreview] = useState<string | null>(sellerPage.banner);

    const form = useForm<SellerPageFormValues>({
        resolver: zodResolver(sellerPageFormSchema),
        defaultValues: {
            seo_slug: sellerPage.seo_slug,
            description: sellerPage.description || '',
            logo: null,
            banner: null,
        },
    });

    const watchedDescription = form.watch('description');
    const watchedSeoSlug = form.watch('seo_slug');

    const handleLogoChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0];
        if (file) {
            if (file.size > 2 * 1024 * 1024) {
                toast.error('Logo dosyası en fazla 2MB olabilir.');
                return;
            }
            form.setValue('logo', file);
            const reader = new FileReader();
            reader.onloadend = () => {
                setLogoPreview(reader.result as string);
            };
            reader.readAsDataURL(file);
        }
    };

    const handleBannerChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0];
        if (file) {
            if (file.size > 5 * 1024 * 1024) {
                toast.error('Banner dosyası en fazla 5MB olabilir.');
                return;
            }
            form.setValue('banner', file);
            const reader = new FileReader();
            reader.onloadend = () => {
                setBannerPreview(reader.result as string);
            };
            reader.readAsDataURL(file);
        }
    };

    const onSubmit = async (data: SellerPageFormValues) => {
        setIsLoading(true);
        setError(undefined);

        try {
            const formData = new FormData();
            formData.append('seo_slug', data.seo_slug);
            if (data.description) {
                formData.append('description', data.description);
            }
            if (data.logo) {
                formData.append('logo', data.logo);
            }
            if (data.banner) {
                formData.append('banner', data.banner);
            }

            router.put(
                '/seller-page',
                formData,
                {
                    forceFormData: true,
                    onSuccess: () => {
                        toast.success('Mağaza sayfası başarıyla güncellendi.');
                        router.reload({ only: ['sellerPage'] });
                    },
                    onError: (errors) => {
                        setError(
                            Object.values(errors).join(', ') ||
                                'Bir hata oluştu',
                        );
                        toast.error('Mağaza sayfası güncellenirken bir hata oluştu.', {
                            description: Object.values(errors).join(', '),
                        });
                    },
                    onFinish: () => {
                        setIsLoading(false);
                    },
                },
            );
        } catch (err) {
            setError('Beklenmeyen bir hata oluştu');
            setIsLoading(false);
            toast.error('Beklenmeyen bir hata oluştu.');
        }
    };

    useEffect(() => {
        setLogoPreview(sellerPage.logo);
        setBannerPreview(sellerPage.banner);
    }, [sellerPage]);

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Mağaza Sayfası" />
            <div className="relative flex h-full w-full flex-col gap-y-4">
                <main className="relative flex min-h-0 w-full grow flex-col">
                    <div className="flex h-full w-full flex-row gap-x-2 p-5">
                        <div className="relative flex w-full flex-col items-center rounded-2xl border-gray-200 px-4 md:overflow-y-auto md:border md:bg-white md:px-8 md:shadow-xs dark:border-[#313131] dark:md:bg-[#181818]">
                            <div className="flex h-full w-full max-w-[--breakpoint-xl] flex-col">
                                <div className="flex w-full flex-col gap-y-4 py-8 md:flex-row md:items-center md:justify-between md:py-8">
                                    <h4 className="text-2xl font-medium whitespace-nowrap dark:text-white">
                                        Mağaza Sayfası
                                    </h4>
                                    <div className="flex gap-2">
                                        <Button
                                            type="button"
                                            variant="outline"
                                            onClick={() => setShowPreview(!showPreview)}
                                        >
                                            <Eye className="mr-2 h-4 w-4" />
                                            {showPreview ? 'Düzenlemeyi Göster' : 'Önizleme'}
                                        </Button>
                                    </div>
                                </div>

                                <div className="flex w-full flex-col gap-y-8 pb-8">
                                    <p className="text-gray-500 dark:text-[#999999]">
                                        Mağaza sayfanızı düzenleyin ve önizleyin. Bu sayfa Next.js
                                        tarafında{' '}
                                        <code className="rounded bg-gray-100 px-2 py-1 text-sm dark:bg-gray-800">
                                            /seller/{watchedSeoSlug || sellerPage.seo_slug}
                                        </code>{' '}
                                        adresinde görüntülenecektir.
                                    </p>

                                    {showPreview ? (
                                        <div className="rounded-2xl border border-gray-200 bg-white dark:border-[#313131] dark:bg-[#181818]">
                                            {/* Preview Section */}
                                            <div className="relative">
                                                {bannerPreview ? (
                                                    <div className="relative h-64 w-full overflow-hidden rounded-t-2xl">
                                                        <img
                                                            src={bannerPreview}
                                                            alt="Banner"
                                                            className="h-full w-full object-cover"
                                                        />
                                                    </div>
                                                ) : (
                                                    <div className="h-64 w-full bg-gradient-to-r from-blue-500 to-purple-600 rounded-t-2xl" />
                                                )}

                                                <div className="relative -mt-16 px-6 pb-6">
                                                    <div className="flex items-end gap-4">
                                                        {logoPreview ? (
                                                            <div className="relative h-32 w-32 overflow-hidden rounded-full border-4 border-white bg-white shadow-lg dark:border-[#181818] dark:bg-[#181818]">
                                                                <img
                                                                    src={logoPreview}
                                                                    alt="Logo"
                                                                    className="h-full w-full object-cover"
                                                                />
                                                            </div>
                                                        ) : (
                                                            <div className="flex h-32 w-32 items-center justify-center rounded-full border-4 border-white bg-gray-200 shadow-lg dark:border-[#181818] dark:bg-gray-700">
                                                                <span className="text-4xl font-bold text-gray-500 dark:text-gray-400">
                                                                    {vendor.name.charAt(0).toUpperCase()}
                                                                </span>
                                                            </div>
                                                        )}
                                                        <div className="flex-1 pb-4">
                                                            <h1 className="text-3xl font-bold dark:text-white">
                                                                {vendor.name}
                                                            </h1>
                                                            <p className="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                                                /seller/{watchedSeoSlug || sellerPage.seo_slug}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div className="px-6 pb-6">
                                                {watchedDescription ? (
                                                    <div className="mt-6 rounded-lg bg-gray-50 p-4 dark:bg-[#171719]">
                                                        <p className="text-sm leading-relaxed text-gray-700 dark:text-gray-300">
                                                            {watchedDescription}
                                                        </p>
                                                    </div>
                                                ) : (
                                                    <div className="mt-6 rounded-lg border border-dashed border-gray-300 p-4 text-center dark:border-gray-600">
                                                        <p className="text-sm text-gray-500 dark:text-gray-400">
                                                            Açıklama eklenmemiş
                                                        </p>
                                                    </div>
                                                )}
                                            </div>
                                        </div>
                                    ) : (
                                        <Form {...form}>
                                            <form
                                                onSubmit={form.handleSubmit(onSubmit)}
                                                className="space-y-6"
                                            >
                                                <div className="rounded-2xl border border-gray-200 p-6 dark:border-[#313131]">
                                                    <h3 className="mb-4 text-lg font-semibold dark:text-white">
                                                        Genel Bilgiler
                                                    </h3>

                                                    <FormField
                                                        control={form.control}
                                                        name="seo_slug"
                                                        render={({ field, fieldState }) => (
                                                            <FormItem>
                                                                <FormLabel className="text-sm font-medium">
                                                                    Mağaza URL
                                                                </FormLabel>
                                                                <FormControl>
                                                                    <div className="flex items-center gap-2">
                                                                        <span className="text-sm text-gray-500 dark:text-gray-400">
                                                                            /seller/
                                                                        </span>
                                                                        <input
                                                                            {...field}
                                                                            type="text"
                                                                            placeholder="magaza-adi"
                                                                            className="flex-1 rounded-xl border p-3 dark:border-white/10 dark:bg-[#171719]"
                                                                        />
                                                                    </div>
                                                                </FormControl>
                                                                {fieldState.error && (
                                                                    <FormMessage>
                                                                        {fieldState.error.message}
                                                                    </FormMessage>
                                                                )}
                                                                <p className="text-xs text-gray-500 dark:text-gray-400">
                                                                    Bu URL, mağaza sayfanızın adresini belirler.
                                                                    Örnek: /seller/magaza-adi
                                                                </p>
                                                            </FormItem>
                                                        )}
                                                    />

                                                    <FormField
                                                        control={form.control}
                                                        name="description"
                                                        render={({ field, fieldState }) => (
                                                            <FormItem className="mt-4">
                                                                <FormLabel className="text-sm font-medium">
                                                                    Açıklama
                                                                </FormLabel>
                                                                <FormControl>
                                                                    <textarea
                                                                        {...field}
                                                                        rows={6}
                                                                        placeholder="Mağazanız hakkında bilgi verin..."
                                                                        className="w-full rounded-xl border p-3 dark:border-white/10 dark:bg-[#171719]"
                                                                        maxLength={5000}
                                                                    />
                                                                </FormControl>
                                                                {fieldState.error && (
                                                                    <FormMessage>
                                                                        {fieldState.error.message}
                                                                    </FormMessage>
                                                                )}
                                                                <p className="text-xs text-gray-500 dark:text-gray-400">
                                                                    {field.value?.length || 0} / 5000 karakter
                                                                </p>
                                                            </FormItem>
                                                        )}
                                                    />
                                                </div>

                                                <div className="grid gap-6 md:grid-cols-2">
                                                    <div className="rounded-2xl border border-gray-200 p-6 dark:border-[#313131]">
                                                        <h3 className="mb-4 text-lg font-semibold dark:text-white">
                                                            Logo
                                                        </h3>
                                                        <div className="space-y-4">
                                                            {logoPreview && (
                                                                <div className="relative mx-auto h-32 w-32 overflow-hidden rounded-full border-4 border-gray-200 dark:border-[#313131]">
                                                                    <img
                                                                        src={logoPreview}
                                                                        alt="Logo Preview"
                                                                        className="h-full w-full object-cover"
                                                                    />
                                                                </div>
                                                            )}
                                                            <div>
                                                                <label className="flex cursor-pointer items-center justify-center gap-2 rounded-xl border border-dashed border-gray-300 p-4 transition-colors hover:bg-gray-50 dark:border-gray-600 dark:hover:bg-[#171719]">
                                                                    <Upload className="h-5 w-5 text-gray-500" />
                                                                    <span className="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                                        {logoPreview ? 'Logoyu Değiştir' : 'Logo Yükle'}
                                                                    </span>
                                                                    <input
                                                                        type="file"
                                                                        accept="image/*"
                                                                        className="hidden"
                                                                        onChange={handleLogoChange}
                                                                    />
                                                                </label>
                                                                <p className="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                                                    Maksimum 2MB (JPEG, PNG, GIF, WebP)
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div className="rounded-2xl border border-gray-200 p-6 dark:border-[#313131]">
                                                        <h3 className="mb-4 text-lg font-semibold dark:text-white">
                                                            Banner
                                                        </h3>
                                                        <div className="space-y-4">
                                                            {bannerPreview && (
                                                                <div className="relative h-48 w-full overflow-hidden rounded-lg border border-gray-200 dark:border-[#313131]">
                                                                    <img
                                                                        src={bannerPreview}
                                                                        alt="Banner Preview"
                                                                        className="h-full w-full object-cover"
                                                                    />
                                                                </div>
                                                            )}
                                                            <div>
                                                                <label className="flex cursor-pointer items-center justify-center gap-2 rounded-xl border border-dashed border-gray-300 p-4 transition-colors hover:bg-gray-50 dark:border-gray-600 dark:hover:bg-[#171719]">
                                                                    <Upload className="h-5 w-5 text-gray-500" />
                                                                    <span className="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                                        {bannerPreview
                                                                            ? 'Banner\'ı Değiştir'
                                                                            : 'Banner Yükle'}
                                                                    </span>
                                                                    <input
                                                                        type="file"
                                                                        accept="image/*"
                                                                        className="hidden"
                                                                        onChange={handleBannerChange}
                                                                    />
                                                                </label>
                                                                <p className="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                                                    Maksimum 5MB (JPEG, PNG, GIF, WebP)
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                {error && (
                                                    <div className="rounded-xl border border-red-300 bg-red-100 p-3 text-sm text-red-700 dark:border-red-800 dark:bg-red-900/20 dark:text-red-400">
                                                        {error}
                                                    </div>
                                                )}

                                                <div className="flex justify-end gap-3">
                                                    <Button
                                                        type="submit"
                                                        disabled={isLoading}
                                                        className="inline-flex items-center gap-2 rounded-xl border border-white/10 bg-[#171719] px-6 py-3 font-medium text-white transition-opacity hover:opacity-90 disabled:opacity-50 dark:bg-[#131313] dark:hover:bg-[#171719]"
                                                    >
                                                        <Save className="h-4 w-4" />
                                                        {isLoading ? 'Kaydediliyor...' : 'Kaydet'}
                                                    </Button>
                                                </div>
                                            </form>
                                        </Form>
                                    )}
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </AppLayout>
    );
}

