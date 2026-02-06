import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/react';
import { useState } from 'react';
import {
    Sheet,
    SheetContent,
    SheetDescription,
    SheetHeader,
    SheetTitle,
    SheetTrigger,
} from '@/components/ui/sheet';
import { Button } from '@/components/ui/button';
import { Plus } from 'lucide-react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import {
    FormControl,
    FormDescription,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from '@/components/ui/form';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Adresler',
        href: '/addresses',
    },
];

interface Address {
    id: string;
    title: string;
    address_line?: string;
    address?: string;
    city: string;
    district: string;
    neighborhood?: string;
    postal_code: string;
    phone: string;
    full_name?: string;
    address_type?: string;
    is_default?: boolean;
    created_at: string;
}

interface Props {
    addresses?: Address[];
}

const addressFormSchema = z.object({
    title: z.string().min(1, 'Başlık gereklidir'),
    full_name: z.string().optional(),
    phone: z.string().optional(),
    address_line: z.string().min(1, 'Adres gereklidir'),
    city: z.string().min(1, 'Şehir gereklidir'),
    district: z.string().min(1, 'İlçe gereklidir'),
    neighborhood: z.string().optional(),
    postal_code: z.string().optional(),
    address_type: z.string().optional(),
    is_default: z.boolean().default(false),
});

type AddressFormValues = z.infer<typeof addressFormSchema>;

export default function Addresses({ addresses = [] }: Props) {
    const [isSheetOpen, setIsSheetOpen] = useState(false);
    const [isLoading, setIsLoading] = useState(false);
    const [error, setError] = useState<string | undefined>();

    const form = useForm<AddressFormValues>({
        resolver: zodResolver(addressFormSchema),
        defaultValues: {
            title: '',
            full_name: '',
            phone: '',
            address_line: '',
            city: '',
            district: '',
            neighborhood: '',
            postal_code: '',
            address_type: 'home',
            is_default: false,
        },
    });

    const formatDate = (dateString: string) => {
        return new Intl.DateTimeFormat('tr-TR', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
        }).format(new Date(dateString));
    };

    const onSubmit = async (data: AddressFormValues) => {
        setIsLoading(true);
        setError(undefined);

        router.post(
            '/addresses',
            data,
            {
                onSuccess: () => {
                    setIsSheetOpen(false);
                    form.reset();
                },
                onError: (errors) => {
                    setError(
                        Object.values(errors).join(', ') || 'Bir hata oluştu',
                    );
                },
                onFinish: () => {
                    setIsLoading(false);
                },
            },
        );
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Adresler" />
            <div className="relative flex h-full w-full flex-col gap-y-4">
                <main className="relative flex min-h-0 w-full grow flex-col">
                    <div className="flex h-full w-full flex-row gap-x-2 p-5">
                        <div className="relative flex w-full flex-col items-center rounded-2xl border-gray-200 px-4 md:overflow-y-auto md:border md:bg-white md:px-8 md:shadow-xs dark:border-[#313131] dark:md:bg-[#181818]">
                            <div className="flex h-full w-full max-w-[--breakpoint-xl] flex-col">
                                <div className="flex w-full flex-col gap-y-4 py-8 md:flex-row md:items-center md:justify-between md:py-8">
                                    <h4 className="text-2xl font-medium whitespace-nowrap dark:text-white">
                                        Adresler
                                    </h4>
                                    <Sheet open={isSheetOpen} onOpenChange={setIsSheetOpen}>
                                        <SheetTrigger asChild>
                                            <Button className="gap-2">
                                                <Plus className="h-4 w-4" />
                                                Yeni Adres
                                            </Button>
                                        </SheetTrigger>
                                        <SheetContent
                                            side="right"
                                            className="sheet-content mr-2 ml-2 rounded-3xl p-0 md:mr-4 md:ml-4"
                                            style={{
                                                height: 'calc(100vh - 1rem)',
                                                top: '0.5rem',
                                                maxHeight: 'calc(100vh - 1rem)',
                                                width: 'calc(100vw - 1rem)',
                                                maxWidth: '600px',
                                            }}
                                        >
                                            <div className="flex h-full flex-col p-8">
                                                <SheetHeader className="mb-6 flex-shrink-0">
                                                    <SheetTitle>Yeni Adres Ekle</SheetTitle>
                                                    <SheetDescription>
                                                        Yeni bir adres eklemek için aşağıdaki formu doldurun.
                                                    </SheetDescription>
                                                </SheetHeader>

                                                <div className="relative z-10 max-h-[calc(100vh-12rem)] flex-1 space-y-6 overflow-y-auto pr-3">
                                                    <form onSubmit={form.handleSubmit(onSubmit)}>
                                                        <div className="space-y-4">
                                                            <FormField
                                                                control={form.control}
                                                                name="title"
                                                                render={({ field, fieldState }) => (
                                                                    <FormItem>
                                                                        <FormLabel className="text-sm font-medium">
                                                                            Başlık *
                                                                        </FormLabel>
                                                                        <FormControl>
                                                                            <input
                                                                                {...field}
                                                                                type="text"
                                                                                placeholder="Ev, İş, vb."
                                                                                className="w-full rounded-xl border p-3 dark:border-white/10 dark:bg-[#171719]"
                                                                            />
                                                                        </FormControl>
                                                                        {fieldState.error && (
                                                                            <FormMessage>
                                                                                {fieldState.error.message}
                                                                            </FormMessage>
                                                                        )}
                                                                    </FormItem>
                                                                )}
                                                            />

                                                            <div className="grid grid-cols-2 gap-3">
                                                                <FormField
                                                                    control={form.control}
                                                                    name="full_name"
                                                                    render={({ field }) => (
                                                                        <FormItem>
                                                                            <FormLabel className="text-sm font-medium">
                                                                                Ad Soyad
                                                                            </FormLabel>
                                                                            <FormControl>
                                                                                <input
                                                                                    {...field}
                                                                                    type="text"
                                                                                    placeholder="Ad Soyad"
                                                                                    className="w-full rounded-xl border p-3 dark:border-white/10 dark:bg-[#171719]"
                                                                                />
                                                                            </FormControl>
                                                                        </FormItem>
                                                                    )}
                                                                />

                                                                <FormField
                                                                    control={form.control}
                                                                    name="phone"
                                                                    render={({ field }) => (
                                                                        <FormItem>
                                                                            <FormLabel className="text-sm font-medium">
                                                                                Telefon
                                                                            </FormLabel>
                                                                            <FormControl>
                                                                                <input
                                                                                    {...field}
                                                                                    type="tel"
                                                                                    placeholder="0555 123 45 67"
                                                                                    className="w-full rounded-xl border p-3 dark:border-white/10 dark:bg-[#171719]"
                                                                                />
                                                                            </FormControl>
                                                                        </FormItem>
                                                                    )}
                                                                />
                                                            </div>

                                                            <FormField
                                                                control={form.control}
                                                                name="address_line"
                                                                render={({ field, fieldState }) => (
                                                                    <FormItem>
                                                                        <FormLabel className="text-sm font-medium">
                                                                            Adres *
                                                                        </FormLabel>
                                                                        <FormControl>
                                                                            <textarea
                                                                                {...field}
                                                                                placeholder="Mahalle, sokak, bina no, daire no"
                                                                                className="h-24 w-full rounded-xl border p-3 dark:border-white/10 dark:bg-[#171719]"
                                                                            />
                                                                        </FormControl>
                                                                        {fieldState.error && (
                                                                            <FormMessage>
                                                                                {fieldState.error.message}
                                                                            </FormMessage>
                                                                        )}
                                                                    </FormItem>
                                                                )}
                                                            />

                                                            <div className="grid grid-cols-2 gap-3">
                                                                <FormField
                                                                    control={form.control}
                                                                    name="city"
                                                                    render={({ field, fieldState }) => (
                                                                        <FormItem>
                                                                            <FormLabel className="text-sm font-medium">
                                                                                Şehir *
                                                                            </FormLabel>
                                                                            <FormControl>
                                                                                <input
                                                                                    {...field}
                                                                                    type="text"
                                                                                    placeholder="İstanbul"
                                                                                    className="w-full rounded-xl border p-3 dark:border-white/10 dark:bg-[#171719]"
                                                                                />
                                                                            </FormControl>
                                                                            {fieldState.error && (
                                                                                <FormMessage>
                                                                                    {fieldState.error.message}
                                                                                </FormMessage>
                                                                            )}
                                                                        </FormItem>
                                                                    )}
                                                                />

                                                                <FormField
                                                                    control={form.control}
                                                                    name="district"
                                                                    render={({ field, fieldState }) => (
                                                                        <FormItem>
                                                                            <FormLabel className="text-sm font-medium">
                                                                                İlçe *
                                                                            </FormLabel>
                                                                            <FormControl>
                                                                                <input
                                                                                    {...field}
                                                                                    type="text"
                                                                                    placeholder="Kadıköy"
                                                                                    className="w-full rounded-xl border p-3 dark:border-white/10 dark:bg-[#171719]"
                                                                                />
                                                                            </FormControl>
                                                                            {fieldState.error && (
                                                                                <FormMessage>
                                                                                    {fieldState.error.message}
                                                                                </FormMessage>
                                                                            )}
                                                                        </FormItem>
                                                                    )}
                                                                />
                                                            </div>

                                                            <div className="grid grid-cols-2 gap-3">
                                                                <FormField
                                                                    control={form.control}
                                                                    name="neighborhood"
                                                                    render={({ field }) => (
                                                                        <FormItem>
                                                                            <FormLabel className="text-sm font-medium">
                                                                                Mahalle
                                                                            </FormLabel>
                                                                            <FormControl>
                                                                                <input
                                                                                    {...field}
                                                                                    type="text"
                                                                                    placeholder="Mahalle adı"
                                                                                    className="w-full rounded-xl border p-3 dark:border-white/10 dark:bg-[#171719]"
                                                                                />
                                                                            </FormControl>
                                                                        </FormItem>
                                                                    )}
                                                                />

                                                                <FormField
                                                                    control={form.control}
                                                                    name="postal_code"
                                                                    render={({ field }) => (
                                                                        <FormItem>
                                                                            <FormLabel className="text-sm font-medium">
                                                                                Posta Kodu
                                                                            </FormLabel>
                                                                            <FormControl>
                                                                                <input
                                                                                    {...field}
                                                                                    type="text"
                                                                                    placeholder="34000"
                                                                                    className="w-full rounded-xl border p-3 dark:border-white/10 dark:bg-[#171719]"
                                                                                />
                                                                            </FormControl>
                                                                        </FormItem>
                                                                    )}
                                                                />
                                                            </div>

                                                            <FormField
                                                                control={form.control}
                                                                name="is_default"
                                                                render={({ field }) => (
                                                                    <FormItem>
                                                                        <div className="flex items-center gap-3 rounded-xl border p-3 dark:border-white/10">
                                                                            <input
                                                                                {...field}
                                                                                type="checkbox"
                                                                                id="is_default"
                                                                                className="h-4 w-4"
                                                                                checked={field.value}
                                                                                onChange={(e) =>
                                                                                    field.onChange(e.target.checked)
                                                                                }
                                                                            />
                                                                            <label
                                                                                htmlFor="is_default"
                                                                                className="text-sm font-medium"
                                                                            >
                                                                                Varsayılan Adres Olarak Ayarla
                                                                            </label>
                                                                        </div>
                                                                    </FormItem>
                                                                )}
                                                            />

                                                            {error && (
                                                                <div className="rounded-xl border border-red-300 bg-red-100 p-3 text-sm text-red-700 dark:border-red-800 dark:bg-red-900/20 dark:text-red-400">
                                                                    {error}
                                                                </div>
                                                            )}

                                                            <div className="sticky bottom-0 z-20 flex flex-shrink-0 gap-3 border-t bg-white pt-6 pb-4 dark:bg-[#17191a]">
                                                                <button
                                                                    type="submit"
                                                                    disabled={isLoading}
                                                                    className="flex-1 rounded-xl border border-white/10 bg-[#171719] px-6 py-3 font-medium text-white transition-opacity hover:opacity-90 disabled:opacity-50 dark:bg-[#131313] dark:hover:bg-[#171719]"
                                                                >
                                                                    {isLoading ? 'Kaydediliyor...' : 'Adres Ekle'}
                                                                </button>
                                                                <button
                                                                    type="button"
                                                                    className="flex-1 rounded-xl border border-gray-300 px-6 py-3 font-medium transition-colors hover:bg-gray-50 dark:border-white/10 dark:hover:bg-[#171719]"
                                                                    onClick={() => setIsSheetOpen(false)}
                                                                >
                                                                    İptal
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </SheetContent>
                                    </Sheet>
                                </div>

                                <div className="flex w-full flex-col gap-y-8 pb-8">
                                    <p className="text-gray-500 dark:text-[#999999]">
                                        Kayıtlı adreslerinizi görüntüleyin ve yönetin.
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
                                                                Adres
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Şehir / İlçe
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Posta Kodu
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Telefon
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Tarih
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody className="[&_tr:last-child]:border-0">
                                                        {addresses.length === 0 ? (
                                                            <tr>
                                                                <td
                                                                    colSpan={6}
                                                                    className="p-8 text-center text-gray-500 dark:text-gray-400"
                                                                >
                                                                    Henüz adres yok.
                                                                </td>
                                                            </tr>
                                                        ) : (
                                                            addresses.map((address) => (
                                                                <tr
                                                                    key={address.id}
                                                                    className="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted"
                                                                >
                                                                    <td className="p-4 align-middle">
                                                                        <div className="text-sm font-medium dark:text-white">
                                                                            {address.title || '-'}
                                                                        </div>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <div className="text-sm dark:text-white max-w-md">
                                                                            {address.address_line || address.address || '-'}
                                                                        </div>
                                                                        {address.neighborhood && (
                                                                            <div className="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                                                {address.neighborhood}
                                                                            </div>
                                                                        )}
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <div className="text-sm dark:text-white">
                                                                            {address.city} / {address.district}
                                                                        </div>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <div className="text-sm dark:text-white">
                                                                            {address.postal_code}
                                                                        </div>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <div className="text-sm dark:text-white">
                                                                            {address.phone}
                                                                        </div>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <span className="text-sm text-gray-500 dark:text-gray-400">
                                                                            {formatDate(address.created_at)}
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

