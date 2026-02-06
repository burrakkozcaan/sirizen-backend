import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import {
    Sheet,
    SheetContent,
    SheetDescription,
    SheetHeader,
    SheetTitle,
    SheetTrigger,
} from '@/components/ui/sheet';
import {
    FormControl,
    FormDescription,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from '@/components/ui/form';
import {
    FileUpload,
    FileUploadDropzone,
    FileUploadItem,
    FileUploadItemDelete,
    FileUploadItemMetadata,
    FileUploadList,
    FileUploadTrigger,
} from '@/components/ui/file-upload';
import { Status, StatusIndicator, StatusLabel } from '@/components/ui/status';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { zodResolver } from '@hookform/resolvers/zod';
import { Head, router } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import { useForm } from 'react-hook-form';
import { z } from 'zod';
import { Plus, Upload, X, Sparkles, Edit } from 'lucide-react';
import { toast } from 'sonner';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Ürünlerim',
        href: '/products',
    },
];

interface Product {
    id: string;
    title: string;
    slug: string;
    price: string;
    stock: number;
    is_active: boolean;
    created_at: string;
    brand?: {
        id: string;
        name: string;
    } | null;
    category?: {
        id: string;
        name: string;
    } | null;
}

interface Category {
    id: string;
    name: string;
}

interface Brand {
    id: string;
    name: string;
}

interface Vendor {
    name: string;
    address: string | null;
    email: string | null;
    phone: string | null;
}

interface Props {
    products?: Product[];
    categories?: Category[];
    brands?: Brand[];
    vendor?: Vendor;
}

const productFormSchema = z.object({
    title: z.string().min(1, 'Ürün adı gereklidir'),
    description: z.string().optional(),
    short_description: z.string().optional(),
    category_id: z.string().min(1, 'Kategori seçmelisiniz'),
    brand_id: z.string().optional(),
    price: z.coerce.number().min(0, 'Fiyat 0 veya daha büyük olmalıdır'),
    discount_price: z.coerce.number().min(0).optional(),
    original_price: z.coerce.number().min(0).optional(),
    discount_percentage: z.coerce.number().min(0).max(100).optional(),
    stock: z.coerce.number().int().min(0, 'Stok 0 veya daha büyük olmalıdır'),
    dispatch_days: z.coerce.number().int().min(0).optional(),
    shipping_type: z.string().optional(),
    shipping_time: z.coerce.number().int().min(0).optional(),
    images: z.any().optional(),
    videos: z.array(z.object({
        url: z.string().url('Geçerli bir URL giriniz'),
        title: z.string().optional(),
    })).optional(),
    new_brand_name: z.string().optional(),
    // Güvenlik Bilgileri
    safety_information: z.string().optional(),
    manufacturer_name: z.string().optional(),
    manufacturer_address: z.string().optional(),
    manufacturer_contact: z.string().optional(),
    responsible_party_name: z.string().optional(),
    responsible_party_address: z.string().optional(),
    responsible_party_contact: z.string().optional(),
    // Ek Bilgiler
    additional_information: z.string().optional(),
    additional_info: z.string().optional(), // Her satır bir madde olarak işlenecek
    tags: z.string().optional(), // Virgülle ayrılmış
    // Kargo ve Variant Bilgileri
    shipping_cost: z.coerce.number().min(0).optional(),
    variants: z.array(z.object({
        sku: z.string().min(1, 'SKU gereklidir'),
        size: z.string().optional(),
        color: z.string().optional(),
        stock: z.coerce.number().int().min(0),
        price: z.coerce.number().min(0).optional(),
        weight: z.coerce.number().min(0).optional(),
    })).optional(),
});

type ProductFormValues = z.infer<typeof productFormSchema>;

export default function Products({
    products: initialProducts = [],
    categories = [],
    brands = [],
    vendor,
}: Props) {
    const [products, setProducts] = useState<Product[]>(initialProducts);
    const [statusFilter, setStatusFilter] = useState('All Products');
    const [isSheetOpen, setIsSheetOpen] = useState(false);
    const [isLoading, setIsLoading] = useState(false);
    const [error, setError] = useState<string | undefined>();
    const [showNewBrand, setShowNewBrand] = useState(false);
    const [isVariantDialogOpen, setIsVariantDialogOpen] = useState(false);
    const [variantType, setVariantType] = useState<'size' | 'number' | 'color' | 'combination'>('size');
    const [sizeValues, setSizeValues] = useState('');
    const [numberRange, setNumberRange] = useState({ from: 36, to: 45 });
    const [colorValues, setColorValues] = useState('');
    const [combinationSizes, setCombinationSizes] = useState('');
    const [combinationColors, setCombinationColors] = useState('');
    const [isManufacturerSelf, setIsManufacturerSelf] = useState(false);
    const [isEditDialogOpen, setIsEditDialogOpen] = useState(false);
    const [selectedProduct, setSelectedProduct] = useState<Product | null>(null);
    const [productStatus, setProductStatus] = useState(false);

    useEffect(() => {
        setProducts(initialProducts);
    }, [initialProducts]);

    const form = useForm<ProductFormValues>({
        resolver: zodResolver(productFormSchema),
        defaultValues: {
            title: '',
            description: '',
            short_description: '',
            category_id: '',
            brand_id: '',
            price: 0,
            discount_price: 0,
            original_price: 0,
            discount_percentage: 0,
            stock: 0,
            dispatch_days: 1,
            shipping_type: 'standard',
            shipping_time: 3,
            images: [],
            videos: [],
            new_brand_name: '',
            safety_information: '',
            manufacturer_name: '',
            manufacturer_address: '',
            manufacturer_contact: '',
            responsible_party_name: '',
            responsible_party_address: '',
            responsible_party_contact: '',
            additional_information: '',
            additional_info: '',
            tags: '',
            shipping_cost: 0,
            variants: [],
        },
    });

    const formatDate = (dateString: string) => {
        return new Intl.DateTimeFormat('tr-TR', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
        }).format(new Date(dateString));
    };

    const formatPrice = (amount: number | string) => {
        return new Intl.NumberFormat('tr-TR', {
            style: 'currency',
            currency: 'TRY',
        }).format(typeof amount === 'string' ? parseFloat(amount) : amount);
    };

    // Otomatik varyant oluşturma fonksiyonları
    const generateVariants = () => {
        const currentVariants = form.getValues('variants') || [];
        const newVariants: Array<{
            sku: string;
            size?: string;
            color?: string;
            stock: number;
            price?: number;
            weight?: number;
        }> = [];

        if (variantType === 'size') {
            // Beden varyantları: S, M, L, XL gibi
            const sizes = sizeValues.split(',').map(s => s.trim()).filter(s => s);
            sizes.forEach((size, index) => {
                newVariants.push({
                    sku: `SKU-${Date.now()}-${index + 1}`,
                    size: size,
                    stock: 0,
                });
            });
        } else if (variantType === 'number') {
            // Numara varyantları: 36, 37, 38, ... 45 gibi
            for (let num = numberRange.from; num <= numberRange.to; num++) {
                newVariants.push({
                    sku: `SKU-${Date.now()}-${num}`,
                    size: String(num),
                    stock: 0,
                });
            }
        } else if (variantType === 'color') {
            // Renk varyantları
            const colors = colorValues.split(',').map(c => c.trim()).filter(c => c);
            colors.forEach((color, index) => {
                newVariants.push({
                    sku: `SKU-${Date.now()}-${index + 1}`,
                    color: color,
                    stock: 0,
                });
            });
        } else if (variantType === 'combination') {
            // Beden + Renk kombinasyonları
            const sizes = combinationSizes.split(',').map(s => s.trim()).filter(s => s);
            const colors = combinationColors.split(',').map(c => c.trim()).filter(c => c);
            let index = 1;
            sizes.forEach((size) => {
                colors.forEach((color) => {
                    newVariants.push({
                        sku: `SKU-${Date.now()}-${index}`,
                        size: size,
                        color: color,
                        stock: 0,
                    });
                    index++;
                });
            });
        }

        // Mevcut varyantlara ekle
        form.setValue('variants', [...currentVariants, ...newVariants]);
        setIsVariantDialogOpen(false);
        
        // Form alanlarını temizle
        setSizeValues('');
        setNumberRange({ from: 36, to: 45 });
        setColorValues('');
        setCombinationSizes('');
        setCombinationColors('');
        
        toast.success(`${newVariants.length} varyant başarıyla oluşturuldu.`);
    };

    // Calculate KPIs
    const totalProducts = products.length;
    const activeProducts = products.filter((p) => p.is_active).length;
    const totalStock = products.reduce((sum, p) => sum + p.stock, 0);

    const filteredProducts = products.filter((product) => {
        if (statusFilter === 'All Products') {
            return true;
        }
        if (statusFilter === 'active') {
            return product.is_active;
        }
        if (statusFilter === 'inactive') {
            return !product.is_active;
        }
        if (statusFilter === 'out_of_stock') {
            return product.stock === 0;
        }
        return true;
    });

    const onSubmit = async (data: ProductFormValues) => {
        setIsLoading(true);
        setError(undefined);

        // Validate brand selection
        if (!data.brand_id && !data.new_brand_name) {
            setError('Marka seçmelisiniz veya yeni marka oluşturmalısınız');
            setIsLoading(false);
            return;
        }

        try {
            const formData = new FormData();
            formData.append('title', data.title);
            if (data.description) {
                formData.append('description', data.description);
            }
            if (data.short_description) {
                formData.append('short_description', data.short_description);
            }
            formData.append('category_id', data.category_id);
            if (data.brand_id) {
                formData.append('brand_id', data.brand_id);
            }
            formData.append('price', data.price.toString());
            if (data.original_price && data.original_price > 0) {
                formData.append('original_price', data.original_price.toString());
            }
            if (data.discount_price && data.discount_price > 0) {
                formData.append('discount_price', data.discount_price.toString());
            }
            if (data.discount_percentage && data.discount_percentage > 0) {
                formData.append('discount_percentage', data.discount_percentage.toString());
            }
            formData.append('stock', data.stock.toString());
            if (data.dispatch_days) {
                formData.append('dispatch_days', data.dispatch_days.toString());
            }
            if (data.shipping_type) {
                formData.append('shipping_type', data.shipping_type);
            }
            // Kargo ücreti
            if (data.shipping_cost !== undefined) {
                formData.append('shipping_cost', data.shipping_cost.toString());
            }

            // Variants
            if (data.variants && data.variants.length > 0) {
                data.variants.forEach((variant, index) => {
                    formData.append(`variants[${index}][sku]`, variant.sku);
                    if (variant.size) {
                        formData.append(`variants[${index}][size]`, variant.size);
                    }
                    if (variant.color) {
                        formData.append(`variants[${index}][color]`, variant.color);
                    }
                    formData.append(`variants[${index}][stock]`, variant.stock.toString());
                    if (variant.price !== undefined) {
                        formData.append(`variants[${index}][price]`, variant.price.toString());
                    }
                    if (variant.weight !== undefined) {
                        formData.append(`variants[${index}][weight]`, variant.weight.toString());
                    }
                });
            }

            // Add images (max 4)
            if (data.images && data.images.length > 0) {
                data.images.slice(0, 4).forEach((image, index) => {
                    formData.append(`images[${index}]`, image);
                });
            }

            // Add videos
            if (data.videos && data.videos.length > 0) {
                data.videos.forEach((video, index) => {
                    if (video.url) {
                        formData.append(`videos[${index}][url]`, video.url);
                        if (video.title) {
                            formData.append(
                                `videos[${index}][title]`,
                                video.title,
                            );
                        }
                    }
                });
            }

            // Add new brand name if creating new brand
            if (data.new_brand_name) {
                formData.append('new_brand_name', data.new_brand_name);
            }

            // Add safety information
            if (data.safety_information) {
                formData.append('safety_information', data.safety_information);
            }
            if (data.manufacturer_name) {
                formData.append('manufacturer_name', data.manufacturer_name);
            }
            if (data.manufacturer_address) {
                formData.append('manufacturer_address', data.manufacturer_address);
            }
            if (data.manufacturer_contact) {
                formData.append('manufacturer_contact', data.manufacturer_contact);
            }
            if (data.responsible_party_name) {
                formData.append('responsible_party_name', data.responsible_party_name);
            }
            if (data.responsible_party_address) {
                formData.append('responsible_party_address', data.responsible_party_address);
            }
            if (data.responsible_party_contact) {
                formData.append('responsible_party_contact', data.responsible_party_contact);
            }

            // Add additional information
            if (data.additional_information) {
                formData.append('additional_information', data.additional_information);
            }
            if (data.additional_info) {
                formData.append('additional_info', data.additional_info);
            }
            if (data.tags) {
                formData.append('tags', data.tags);
            }
            if (data.shipping_time) {
                formData.append('shipping_time', data.shipping_time.toString());
            }

            router.post(
                '/products',
                formData,
                {
                    forceFormData: true,
                    onSuccess: () => {
                        setIsSheetOpen(false);
                        setShowNewBrand(false);
                        setIsManufacturerSelf(false);
                        form.reset();
                    },
                    onError: (errors) => {
                        setError(
                            Object.values(errors).join(', ') ||
                                'Bir hata oluştu',
                        );
                    },
                    onFinish: () => {
                        setIsLoading(false);
                    },
                },
            );
        } catch (err) {
            setError('Beklenmeyen bir hata oluştu');
            setIsLoading(false);
        }
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Ürünlerim" />
            <div className="relative flex h-full w-full flex-col gap-y-4">
                <main className="relative flex min-h-0 w-full grow flex-col">
                    <div className="flex h-full w-full flex-row gap-x-2 p-5">
                        <div className="relative flex w-full flex-col items-center rounded-2xl border border-gray-200 px-4 md:overflow-y-auto md:border md:bg-white md:px-8 md:shadow-xs dark:border-[#313131] dark:md:bg-[#181818]">
                            <div className="flex h-full w-full flex-col">
                                <div className="flex w-full flex-col gap-y-4 py-8 md:flex-row md:items-center md:justify-between md:py-8">
                                    <h4 className="text-lg font-medium whitespace-nowrap dark:text-white">
                                        Ürünlerim
                                    </h4>
                                </div>

                                <div
                                    className="flex w-full flex-col pb-8"
                                    style={{ opacity: 1 }}
                                >
                                    <div className="flex flex-col gap-8">
                                        <div className="flex w-full flex-row items-center justify-between gap-2">
                                            <div className="flex flex-wrap items-center gap-4">
                                                <div className="w-auto">
                                                    <Select
                                                        value={statusFilter}
                                                        onValueChange={(value) =>
                                                            setStatusFilter(
                                                                value,
                                                            )
                                                        }
                                                    >
                                                        <SelectTrigger className="w-[140px] md:w-[180px]">
                                                            <SelectValue placeholder="Durum" />
                                                        </SelectTrigger>
                                                        <SelectContent>
                                                            <SelectItem value="All Products">
                                                                Tüm Ürünler
                                                            </SelectItem>
                                                            <SelectItem value="active">
                                                                Aktif
                                                            </SelectItem>
                                                            <SelectItem value="inactive">
                                                                Pasif
                                                            </SelectItem>
                                                            <SelectItem value="out_of_stock">
                                                                Stokta Yok
                                                            </SelectItem>
                                                        </SelectContent>
                                                    </Select>
                                                </div>
                                            </div>
                                            <Sheet
                                                open={isSheetOpen}
                                                onOpenChange={setIsSheetOpen}
                                            >
                                                <SheetTrigger asChild>
                                                    <button
                                                        type="button"
                                                        className="relative inline-flex h-10 items-center justify-center gap-2 rounded-xl border border-white/10 bg-[#171719] px-4 py-2 text-sm font-medium whitespace-nowrap text-white transition-opacity duration-100 hover:opacity-90 focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none disabled:pointer-events-none disabled:opacity-50 dark:bg-[#171719] dark:hover:bg-[#171719]"
                                                    >
                                                        <Plus className="h-4 w-4" />
                                                        <span>Yeni Ürün</span>
                                                    </button>
                                                </SheetTrigger>
                                                <SheetContent
                                                    side="right"
                                                    className="sheet-content mr-2 ml-2 rounded-3xl p-0 md:mr-4 md:ml-4"
                                                    style={{
                                                        height:
                                                            'calc(100vh - 1rem)',
                                                        top: '0.5rem',
                                                        maxHeight:
                                                            'calc(100vh - 1rem)',
                                                        width:
                                                            'calc(100vw - 1rem)',
                                                        maxWidth: '600px',
                                                    }}
                                                >
                                                    <div className="flex h-full flex-col p-8">
                                                        <SheetHeader className="mb-6 flex-shrink-0">
                                                            <SheetTitle>
                                                                Yeni Ürün Oluştur
                                                            </SheetTitle>
                                                            <SheetDescription>
                                                                Yeni bir ürün
                                                                eklemek için
                                                                aşağıdaki
                                                                formu doldurun.
                                                            </SheetDescription>
                                                        </SheetHeader>

                                                        <div className="relative z-10 max-h-[calc(100vh-12rem)] flex-1 space-y-6 overflow-y-auto pr-3">
                                                            <form
                                                                onSubmit={form.handleSubmit(
                                                                    onSubmit,
                                                                )}
                                                            >
                                                                    <div className="space-y-4">
                                                                        <FormField
                                                                            control={
                                                                                form.control
                                                                            }
                                                                            name="title"
                                                                            render={({
                                                                                field,
                                                                                fieldState,
                                                                            }) => (
                                                                                <FormItem>
                                                                                    <FormLabel className="text-sm font-medium">
                                                                                        Ürün Adı
                                                                                    </FormLabel>
                                                                                    <FormControl>
                                                                                        <input
                                                                                            {...field}
                                                                                            type="text"
                                                                                            placeholder="Örn: iPhone 15 Pro"
                                                                                            className="w-full rounded-xl border p-3 dark:border-white/10 dark:bg-[#171719]"
                                                                                        />
                                                                                    </FormControl>
                                                                                    {fieldState.error && (
                                                                                        <FormMessage>
                                                                                            {
                                                                                                fieldState
                                                                                                    .error
                                                                                                    .message
                                                                                            }
                                                                                        </FormMessage>
                                                                                    )}
                                                                                </FormItem>
                                                                            )}
                                                                        />

                                                                        <FormField
                                                                            control={
                                                                                form.control
                                                                            }
                                                                            name="short_description"
                                                                            render={({
                                                                                field,
                                                                                fieldState,
                                                                            }) => (
                                                                                <FormItem>
                                                                                    <FormLabel className="text-sm font-medium">
                                                                                        Kısa
                                                                                        Açıklama
                                                                                    </FormLabel>
                                                                                    <FormControl>
                                                                                        <textarea
                                                                                            {...field}
                                                                                            placeholder="Kısa ürün açıklaması..."
                                                                                            className="h-20 w-full rounded-xl border p-3 dark:border-white/10 dark:bg-[#171719]"
                                                                                        />
                                                                                    </FormControl>
                                                                                    {fieldState.error && (
                                                                                        <FormMessage>
                                                                                            {
                                                                                                fieldState
                                                                                                    .error
                                                                                                    .message
                                                                                            }
                                                                                        </FormMessage>
                                                                                    )}
                                                                                </FormItem>
                                                                            )}
                                                                        />

                                                                        <FormField
                                                                            control={
                                                                                form.control
                                                                            }
                                                                            name="description"
                                                                            render={({
                                                                                field,
                                                                                fieldState,
                                                                            }) => (
                                                                                <FormItem>
                                                                                    <FormLabel className="text-sm font-medium">
                                                                                        Detaylı
                                                                                        Açıklama
                                                                                    </FormLabel>
                                                                                    <FormControl>
                                                                                        <textarea
                                                                                            {...field}
                                                                                            placeholder="Detaylı ürün açıklaması..."
                                                                                            className="h-32 w-full rounded-xl border p-3 dark:border-white/10 dark:bg-[#171719]"
                                                                                        />
                                                                                    </FormControl>
                                                                                    {fieldState.error && (
                                                                                        <FormMessage>
                                                                                            {
                                                                                                fieldState
                                                                                                    .error
                                                                                                    .message
                                                                                            }
                                                                                        </FormMessage>
                                                                                    )}
                                                                                </FormItem>
                                                                            )}
                                                                        />

                                                                        <div className="grid grid-cols-2 gap-3">
                                                                            <FormField
                                                                                control={
                                                                                    form.control
                                                                                }
                                                                                name="category_id"
                                                                                render={({
                                                                                    field,
                                                                                    fieldState,
                                                                                }) => (
                                                                                    <FormItem>
                                                                                        <FormLabel className="text-sm font-medium">
                                                                                            Kategori
                                                                                        </FormLabel>
                                                                                        <FormControl>
                                                                                            <select
                                                                                                {...field}
                                                                                                className="w-full rounded-xl border p-3 dark:border-white/10 dark:bg-[#171719]"
                                                                                            >
                                                                                                <option value="">
                                                                                                    Seçiniz
                                                                                                </option>
                                                                                                {categories.map(
                                                                                                    (
                                                                                                        category,
                                                                                                    ) => (
                                                                                                        <option
                                                                                                            key={
                                                                                                                category.id
                                                                                                            }
                                                                                                            value={
                                                                                                                category.id
                                                                                                            }
                                                                                                        >
                                                                                                            {
                                                                                                                category.name
                                                                                                            }
                                                                                                        </option>
                                                                                                    ),
                                                                                                )}
                                                                                            </select>
                                                                                        </FormControl>
                                                                                        {fieldState.error && (
                                                                                        <FormMessage>
                                                                                            {
                                                                                                fieldState
                                                                                                    .error
                                                                                                    .message
                                                                                            }
                                                                                        </FormMessage>
                                                                                    )}
                                                                                    </FormItem>
                                                                                )}
                                                                            />

                                                                            <FormField
                                                                                control={
                                                                                    form.control
                                                                                }
                                                                                name="brand_id"
                                                                                render={({
                                                                                    field,
                                                                                    fieldState,
                                                                                }) => (
                                                                                    <FormItem>
                                                                                        <div className="flex items-center justify-between">
                                                                                            <FormLabel className="text-sm font-medium">
                                                                                                Marka
                                                                                            </FormLabel>
                                                                                            <button
                                                                                                type="button"
                                                                                                onClick={() =>
                                                                                                    setShowNewBrand(
                                                                                                        !showNewBrand,
                                                                                                    )
                                                                                                }
                                                                                                className="text-xs text-blue-500 hover:text-blue-600 dark:text-blue-400"
                                                                                            >
                                                                                                {showNewBrand
                                                                                                    ? 'Mevcut Marka Seç'
                                                                                                    : '+ Yeni Marka Oluştur'}
                                                                                            </button>
                                                                                        </div>
                                                                                        <FormControl>
                                                                                            {showNewBrand ? (
                                                                                                <FormField
                                                                                                    control={
                                                                                                        form.control
                                                                                                    }
                                                                                                    name="new_brand_name"
                                                                                                    render={({
                                                                                                        field:
                                                                            newBrandField,
                                                                                                        fieldState:
                                                                            newBrandState,
                                                                                                    }) => (
                                                                                                        <div>
                                                                                                            <input
                                                                                                                {...newBrandField}
                                                                                                                type="text"
                                                                                                                placeholder="Yeni marka adı..."
                                                                                                                className="w-full rounded-xl border p-3 dark:border-white/10 dark:bg-[#171719]"
                                                                                                            />
                                                                                                            {newBrandState.error && (
                                                                                                                <FormMessage>
                                                                                                                    {
                                                                                                                        newBrandState
                                                                                                                            .error
                                                                                                                            .message
                                                                                                                    }
                                                                                                                </FormMessage>
                                                                                                            )}
                                                                                                        </div>
                                                                                                    )}
                                                                                                />
                                                                                            ) : (
                                                                                                <select
                                                                                                    {...field}
                                                                                                    className="w-full rounded-xl border p-3 dark:border-white/10 dark:bg-[#171719]"
                                                                                                >
                                                                                                    <option value="">
                                                                                                        Seçiniz
                                                                                                    </option>
                                                                                                    {brands.map(
                                                                                                        (
                                                                                                            brand,
                                                                                                        ) => (
                                                                                                            <option
                                                                                                                key={
                                                                                                                    brand.id
                                                                                                                }
                                                                                                                value={
                                                                                                                    brand.id
                                                                                                                }
                                                                                                            >
                                                                                                                {
                                                                                                                    brand.name
                                                                                                                }
                                                                                                            </option>
                                                                                                        ),
                                                                                                    )}
                                                                                                </select>
                                                                                            )}
                                                                                        </FormControl>
                                                                                        {fieldState.error && (
                                                                                            <FormMessage>
                                                                                                {
                                                                                                    fieldState
                                                                                                        .error
                                                                                                        .message
                                                                                                }
                                                                                            </FormMessage>
                                                                                        )}
                                                                                    </FormItem>
                                                                                )}
                                                                            />
                                                                        </div>

                                                                        <div className="grid grid-cols-2 gap-3">
                                                                            <FormField
                                                                                control={
                                                                                    form.control
                                                                                }
                                                                                name="price"
                                                                                render={({
                                                                                    field,
                                                                                    fieldState,
                                                                                }) => (
                                                                                    <FormItem>
                                                                                        <FormLabel className="text-sm font-medium">
                                                                                            Fiyat (₺)
                                                                                        </FormLabel>
                                                                                        <FormControl>
                                                                                            <input
                                                                                                type="text"
                                                                                                inputMode="decimal"
                                                                                                placeholder="0.00"
                                                                                                value={field.value === 0 || field.value === undefined ? '' : String(field.value)}
                                                                                                onChange={(e) => {
                                                                                                    const inputValue = e.target.value;
                                                                                                    // Boş değer veya sadece nokta/virgül ise
                                                                                                    if (inputValue === '' || inputValue === '.' || inputValue === ',') {
                                                                                                        field.onChange(0);
                                                                                                        form.setValue('original_price', 0);
                                                                                                        form.setValue('discount_price', 0);
                                                                                                        return;
                                                                                                    }
                                                                                                    const value = parseFloat(inputValue.replace(',', '.')) || 0;
                                                                                                    field.onChange(value);
                                                                                                    // Fiyat girildiğinde Orijinal Fiyat'a kopyala
                                                                                                    form.setValue('original_price', value);
                                                                                                    // Eğer yüzde varsa, indirimli fiyatı hesapla
                                                                                                    const currentPercentage = form.getValues('discount_percentage') || 0;
                                                                                                    if (currentPercentage > 0 && value > 0) {
                                                                                                        const calculatedDiscount = value * (1 - currentPercentage / 100);
                                                                                                        form.setValue('discount_price', Math.round(calculatedDiscount * 100) / 100);
                                                                                                    }
                                                                                                }}
                                                                                                onBlur={field.onBlur}
                                                                                                name={field.name}
                                                                                                className="w-full rounded-xl border p-3 dark:border-white/10 dark:bg-[#171719]"
                                                                                            />
                                                                                        </FormControl>
                                                                                        {fieldState.error && (
                                                                                        <FormMessage>
                                                                                            {
                                                                                                fieldState
                                                                                                    .error
                                                                                                    .message
                                                                                            }
                                                                                        </FormMessage>
                                                                                    )}
                                                                                    </FormItem>
                                                                                )}
                                                                            />

                                                                            <FormField
                                                                                control={
                                                                                    form.control
                                                                                }
                                                                                name="stock"
                                                                                render={({
                                                                                    field,
                                                                                    fieldState,
                                                                                }) => (
                                                                                    <FormItem>
                                                                                        <FormLabel className="text-sm font-medium">
                                                                                            Stok
                                                                                        </FormLabel>
                                                                                        <FormControl>
                                                                                            <input
                                                                                                {...field}
                                                                                                type="number"
                                                                                                min="0"
                                                                                                placeholder="0"
                                                                                                className="w-full rounded-xl border p-3 dark:border-white/10 dark:bg-[#171719]"
                                                                                            />
                                                                                        </FormControl>
                                                                                        {fieldState.error && (
                                                                                        <FormMessage>
                                                                                            {
                                                                                                fieldState
                                                                                                    .error
                                                                                                    .message
                                                                                            }
                                                                                        </FormMessage>
                                                                                    )}
                                                                                    </FormItem>
                                                                                )}
                                                                            />
                                                                        </div>

                                                                        <div className="grid grid-cols-3 gap-3">
                                                                            <FormField
                                                                                control={
                                                                                    form.control
                                                                                }
                                                                                name="original_price"
                                                                                render={({
                                                                                    field,
                                                                                    fieldState,
                                                                                }) => (
                                                                                    <FormItem>
                                                                                        <FormLabel className="text-sm font-medium">
                                                                                            Orijinal Fiyat (₺)
                                                                                        </FormLabel>
                                                                                        <FormControl>
                                                                                            <input
                                                                                                type="text"
                                                                                                inputMode="decimal"
                                                                                                placeholder="0.00"
                                                                                                value={field.value === 0 || field.value === undefined ? '' : String(field.value)}
                                                                                                onChange={(e) => {
                                                                                                    const inputValue = e.target.value;
                                                                                                    // Boş değer veya sadece nokta/virgül ise
                                                                                                    if (inputValue === '' || inputValue === '.' || inputValue === ',') {
                                                                                                        field.onChange(0);
                                                                                                        form.setValue('price', 0);
                                                                                                        form.setValue('discount_price', 0);
                                                                                                        return;
                                                                                                    }
                                                                                                    const value = parseFloat(inputValue.replace(',', '.')) || 0;
                                                                                                    field.onChange(value);
                                                                                                    // Orijinal Fiyat girildiğinde Fiyat'a da kopyala
                                                                                                    form.setValue('price', value);
                                                                                                    // Eğer yüzde varsa, indirimli fiyatı hesapla
                                                                                                    const currentPercentage = form.getValues('discount_percentage') || 0;
                                                                                                    if (currentPercentage > 0 && value > 0) {
                                                                                                        const calculatedDiscount = value * (1 - currentPercentage / 100);
                                                                                                        form.setValue('discount_price', Math.round(calculatedDiscount * 100) / 100);
                                                                                                    }
                                                                                                }}
                                                                                                onBlur={field.onBlur}
                                                                                                name={field.name}
                                                                                                className="w-full rounded-xl border p-3 dark:border-white/10 dark:bg-[#171719]"
                                                                                            />
                                                                                        </FormControl>
                                                                                        {fieldState.error && (
                                                                                        <FormMessage>
                                                                                            {
                                                                                                fieldState
                                                                                                    .error
                                                                                                    .message
                                                                                            }
                                                                                        </FormMessage>
                                                                                    )}
                                                                                    </FormItem>
                                                                                )}
                                                                            />

                                                                            <FormField
                                                                                control={
                                                                                    form.control
                                                                                }
                                                                                name="discount_percentage"
                                                                                render={({
                                                                                    field,
                                                                                    fieldState,
                                                                                }) => (
                                                                                    <FormItem>
                                                                                        <FormLabel className="text-sm font-medium">
                                                                                            İndirim Yüzdesi (%)
                                                                                        </FormLabel>
                                                                                        <FormControl>
                                                                                            <input
                                                                                                type="text"
                                                                                                inputMode="decimal"
                                                                                                placeholder="0.00"
                                                                                                value={field.value === 0 || field.value === undefined ? '' : String(field.value)}
                                                                                                onChange={(e) => {
                                                                                                    const inputValue = e.target.value;
                                                                                                    // Boş değer veya sadece nokta/virgül ise
                                                                                                    if (inputValue === '' || inputValue === '.' || inputValue === ',') {
                                                                                                        field.onChange(0);
                                                                                                        return;
                                                                                                    }
                                                                                                    const value = parseFloat(inputValue.replace(',', '.')) || 0;
                                                                                                    const clampedValue = Math.min(Math.max(value, 0), 100);
                                                                                                    field.onChange(clampedValue);
                                                                                                    // Eğer orijinal fiyat varsa, indirimli fiyatı hesapla
                                                                                                    const currentOriginalPrice = form.getValues('original_price') || 0;
                                                                                                    if (currentOriginalPrice > 0 && clampedValue > 0 && clampedValue <= 100) {
                                                                                                        const calculatedDiscount = currentOriginalPrice * (1 - clampedValue / 100);
                                                                                                        form.setValue('discount_price', Math.round(calculatedDiscount * 100) / 100);
                                                                                                    }
                                                                                                }}
                                                                                                onBlur={field.onBlur}
                                                                                                name={field.name}
                                                                                                className="w-full rounded-xl border p-3 dark:border-white/10 dark:bg-[#171719]"
                                                                                            />
                                                                                        </FormControl>
                                                                                        {fieldState.error && (
                                                                                        <FormMessage>
                                                                                            {
                                                                                                fieldState
                                                                                                    .error
                                                                                                    .message
                                                                                            }
                                                                                        </FormMessage>
                                                                                    )}
                                                                                    </FormItem>
                                                                                )}
                                                                            />

                                                                            <FormField
                                                                                control={
                                                                                    form.control
                                                                                }
                                                                                name="discount_price"
                                                                                render={({
                                                                                    field,
                                                                                    fieldState,
                                                                                }) => (
                                                                                    <FormItem>
                                                                                        <FormLabel className="text-sm font-medium">
                                                                                            İndirimli Fiyat (₺)
                                                                                        </FormLabel>
                                                                                        <FormControl>
                                                                                            <input
                                                                                                type="text"
                                                                                                inputMode="decimal"
                                                                                                placeholder="0.00"
                                                                                                value={field.value === 0 || field.value === undefined ? '' : String(field.value)}
                                                                                                onChange={(e) => {
                                                                                                    const inputValue = e.target.value;
                                                                                                    // Boş değer veya sadece nokta/virgül ise
                                                                                                    if (inputValue === '' || inputValue === '.' || inputValue === ',') {
                                                                                                        field.onChange(0);
                                                                                                        form.setValue('discount_percentage', 0);
                                                                                                        return;
                                                                                                    }
                                                                                                    const value = parseFloat(inputValue.replace(',', '.')) || 0;
                                                                                                    field.onChange(value);
                                                                                                    // Eğer orijinal fiyat varsa, yüzdeyi hesapla
                                                                                                    const currentOriginalPrice = form.getValues('original_price') || 0;
                                                                                                    if (currentOriginalPrice > 0 && value > 0) {
                                                                                                        const calculatedPercentage = ((currentOriginalPrice - value) / currentOriginalPrice) * 100;
                                                                                                        if (calculatedPercentage >= 0 && calculatedPercentage <= 100) {
                                                                                                            form.setValue('discount_percentage', Math.round(calculatedPercentage * 100) / 100);
                                                                                                        }
                                                                                                    }
                                                                                                }}
                                                                                                onBlur={field.onBlur}
                                                                                                name={field.name}
                                                                                                className="w-full rounded-xl border p-3 dark:border-white/10 dark:bg-[#171719]"
                                                                                            />
                                                                                        </FormControl>
                                                                                        {fieldState.error && (
                                                                                        <FormMessage>
                                                                                            {
                                                                                                fieldState
                                                                                                    .error
                                                                                                    .message
                                                                                            }
                                                                                        </FormMessage>
                                                                                    )}
                                                                                    </FormItem>
                                                                                )}
                                                                            />
                                                                        </div>

                                                                        <div className="grid grid-cols-3 gap-3">
                                                                            <FormField
                                                                                control={
                                                                                    form.control
                                                                                }
                                                                                name="dispatch_days"
                                                                                render={({
                                                                                    field,
                                                                                    fieldState,
                                                                                }) => (
                                                                                    <FormItem>
                                                                                        <FormLabel className="text-sm font-medium">
                                                                                            Kargo
                                                                                            Süresi
                                                                                            (Gün)
                                                                                        </FormLabel>
                                                                                        <FormControl>
                                                                                            <input
                                                                                                {...field}
                                                                                                type="number"
                                                                                                min="0"
                                                                                                placeholder="1"
                                                                                                className="w-full rounded-xl border p-3 dark:border-white/10 dark:bg-[#171719]"
                                                                                            />
                                                                                        </FormControl>
                                                                                        {fieldState.error && (
                                                                                        <FormMessage>
                                                                                            {
                                                                                                fieldState
                                                                                                    .error
                                                                                                    .message
                                                                                            }
                                                                                        </FormMessage>
                                                                                    )}
                                                                                    </FormItem>
                                                                                )}
                                                                            />

                                                                            <FormField
                                                                                control={
                                                                                    form.control
                                                                                }
                                                                                name="shipping_type"
                                                                                render={({
                                                                                    field,
                                                                                    fieldState,
                                                                                }) => (
                                                                                    <FormItem>
                                                                                        <FormLabel className="text-sm font-medium">
                                                                                            Kargo
                                                                                            Tipi
                                                                                        </FormLabel>
                                                                                        <FormControl>
                                                                                            <select
                                                                                                {...field}
                                                                                                className="w-full rounded-xl border p-3 dark:border-white/10 dark:bg-[#171719]"
                                                                                            >
                                                                                                <option value="standard">
                                                                                                    Standart
                                                                                                </option>
                                                                                                <option value="express">
                                                                                                    Express
                                                                                                </option>
                                                                                                <option value="free">
                                                                                                    Ücretsiz
                                                                                                </option>
                                                                                            </select>
                                                                                        </FormControl>
                                                                                        {fieldState.error && (
                                                                                        <FormMessage>
                                                                                            {
                                                                                                fieldState
                                                                                                    .error
                                                                                                    .message
                                                                                            }
                                                                                        </FormMessage>
                                                                                    )}
                                                                                    </FormItem>
                                                                                )}
                                                                            />

                                                                            <FormField
                                                                                control={
                                                                                    form.control
                                                                                }
                                                                                name="shipping_time"
                                                                                render={({
                                                                                    field,
                                                                                    fieldState,
                                                                                }) => (
                                                                                    <FormItem>
                                                                                        <FormLabel className="text-sm font-medium">
                                                                                            Teslimat
                                                                                            Süresi
                                                                                            (Gün)
                                                                                        </FormLabel>
                                                                                        <FormControl>
                                                                                            <input
                                                                                                {...field}
                                                                                                type="number"
                                                                                                min="0"
                                                                                                placeholder="3"
                                                                                                className="w-full rounded-xl border p-3 dark:border-white/10 dark:bg-[#171719]"
                                                                                            />
                                                                                        </FormControl>
                                                                                        {fieldState.error && (
                                                                                        <FormMessage>
                                                                                            {
                                                                                                fieldState
                                                                                                    .error
                                                                                                    .message
                                                                                            }
                                                                                        </FormMessage>
                                                                                    )}
                                                                                    </FormItem>
                                                                                )}
                                                                            />
                                                                        </div>

                                                                        {/* Kargo Ücreti */}
                                                                        <FormField
                                                                            control={form.control}
                                                                            name="shipping_cost"
                                                                            render={({ field, fieldState }) => (
                                                                                <FormItem>
                                                                                    <FormLabel className="text-sm font-medium">
                                                                                        Kargo Ücreti (₺)
                                                                                    </FormLabel>
                                                                                    <FormControl>
                                                                                        <input
                                                                                            {...field}
                                                                                            type="number"
                                                                                            step="0.01"
                                                                                            min="0"
                                                                                            placeholder="0.00"
                                                                                            value={field.value || ''}
                                                                                            onChange={(e) => field.onChange(e.target.value ? parseFloat(e.target.value) : 0)}
                                                                                            className="w-full rounded-xl border p-3 dark:border-white/10 dark:bg-[#171719]"
                                                                                        />
                                                                                    </FormControl>
                                                                                    <FormDescription className="text-xs text-gray-500">
                                                                                        Ürün için kargo ücreti belirleyin. Boş bırakılırsa 0 olarak kaydedilir.
                                                                                    </FormDescription>
                                                                                    {fieldState.error && (
                                                                                        <FormMessage>
                                                                                            {fieldState.error.message}
                                                                                        </FormMessage>
                                                                                    )}
                                                                                </FormItem>
                                                                            )}
                                                                        />

                                                                        {/* Variant Bilgileri */}
                                                                        <div className="space-y-4 rounded-xl border border-purple-200 bg-purple-50 p-4 dark:border-purple-800 dark:bg-purple-950/20">
                                                                            <div className="flex items-center justify-between">
                                                                                <h3 className="text-sm font-semibold text-purple-900 dark:text-purple-300">
                                                                                    Ürün Varyantları (Beden/Renk)
                                                                                </h3>
                                                                                <div className="flex gap-2">
                                                                                    <button
                                                                                        type="button"
                                                                                        onClick={() => setIsVariantDialogOpen(true)}
                                                                                        className="flex items-center gap-1.5 rounded-lg border border-purple-300 bg-white px-3 py-1.5 text-xs font-medium text-purple-700 hover:bg-purple-50 dark:border-purple-700 dark:bg-purple-900/20 dark:text-purple-300 dark:hover:bg-purple-900/40"
                                                                                    >
                                                                                        <Sparkles className="h-3.5 w-3.5" />
                                                                                        Otomatik Oluştur
                                                                                    </button>
                                                                                    <button
                                                                                        type="button"
                                                                                        onClick={() => {
                                                                                            const currentVariants = form.getValues('variants') || [];
                                                                                            form.setValue('variants', [
                                                                                                ...currentVariants,
                                                                                                {
                                                                                                    sku: '',
                                                                                                    size: '',
                                                                                                    color: '',
                                                                                                    stock: 0,
                                                                                                    price: undefined,
                                                                                                    weight: undefined,
                                                                                                },
                                                                                            ]);
                                                                                        }}
                                                                                        className="rounded-lg border border-purple-300 bg-white px-3 py-1.5 text-xs font-medium text-purple-700 hover:bg-purple-50 dark:border-purple-700 dark:bg-purple-900/20 dark:text-purple-300 dark:hover:bg-purple-900/40"
                                                                                    >
                                                                                        + Varyant Ekle
                                                                                    </button>
                                                                                </div>
                                                                            </div>

                                                                            <FormField
                                                                                control={form.control}
                                                                                name="variants"
                                                                                render={({ field, fieldState }) => (
                                                                                    <FormItem>
                                                                                        <FormControl>
                                                                                            <div className="space-y-3">
                                                                                                {field.value && field.value.length > 0 ? (
                                                                                                    field.value.map((variant, index) => (
                                                                                                        <div
                                                                                                            key={index}
                                                                                                            className="rounded-lg border border-purple-200 bg-white p-4 dark:border-purple-700 dark:bg-purple-900/10"
                                                                                                        >
                                                                                                            <div className="mb-3 flex items-center justify-between">
                                                                                                                <span className="text-xs font-medium text-purple-700 dark:text-purple-300">
                                                                                                                    Varyant {index + 1}
                                                                                                                </span>
                                                                                                                <button
                                                                                                                    type="button"
                                                                                                                    onClick={() => {
                                                                                                                        const newVariants = field.value.filter((_, i) => i !== index);
                                                                                                                        field.onChange(newVariants);
                                                                                                                    }}
                                                                                                                    className="text-xs text-red-500 hover:text-red-700"
                                                                                                                >
                                                                                                                    <X className="h-4 w-4" />
                                                                                                                </button>
                                                                                                            </div>
                                                                                                            <div className="grid grid-cols-1 gap-3 md:grid-cols-2">
                                                                                                                <div>
                                                                                                                    <label className="mb-1 block text-xs font-medium">
                                                                                                                        SKU *
                                                                                                                    </label>
                                                                                                                    <input
                                                                                                                        type="text"
                                                                                                                        placeholder="SKU-001"
                                                                                                                        value={variant.sku || ''}
                                                                                                                        onChange={(e) => {
                                                                                                                            const newVariants = [...field.value];
                                                                                                                            newVariants[index] = { ...variant, sku: e.target.value };
                                                                                                                            field.onChange(newVariants);
                                                                                                                        }}
                                                                                                                        className="w-full rounded-lg border p-2 text-sm dark:border-white/10 dark:bg-[#171719]"
                                                                                                                    />
                                                                                                                </div>
                                                                                                                <div>
                                                                                                                    <label className="mb-1 block text-xs font-medium">
                                                                                                                        Beden
                                                                                                                    </label>
                                                                                                                    <input
                                                                                                                        type="text"
                                                                                                                        placeholder="S, M, L, XL"
                                                                                                                        value={variant.size || ''}
                                                                                                                        onChange={(e) => {
                                                                                                                            const newVariants = [...field.value];
                                                                                                                            newVariants[index] = { ...variant, size: e.target.value };
                                                                                                                            field.onChange(newVariants);
                                                                                                                        }}
                                                                                                                        className="w-full rounded-lg border p-2 text-sm dark:border-white/10 dark:bg-[#171719]"
                                                                                                                    />
                                                                                                                </div>
                                                                                                                <div>
                                                                                                                    <label className="mb-1 block text-xs font-medium">
                                                                                                                        Renk
                                                                                                                    </label>
                                                                                                                    <input
                                                                                                                        type="text"
                                                                                                                        placeholder="Kırmızı, Mavi, Siyah"
                                                                                                                        value={variant.color || ''}
                                                                                                                        onChange={(e) => {
                                                                                                                            const newVariants = [...field.value];
                                                                                                                            newVariants[index] = { ...variant, color: e.target.value };
                                                                                                                            field.onChange(newVariants);
                                                                                                                        }}
                                                                                                                        className="w-full rounded-lg border p-2 text-sm dark:border-white/10 dark:bg-[#171719]"
                                                                                                                    />
                                                                                                                </div>
                                                                                                                <div>
                                                                                                                    <label className="mb-1 block text-xs font-medium">
                                                                                                                        Stok *
                                                                                                                    </label>
                                                                                                                    <input
                                                                                                                        type="number"
                                                                                                                        min="0"
                                                                                                                        placeholder="0"
                                                                                                                        value={variant.stock || 0}
                                                                                                                        onChange={(e) => {
                                                                                                                            const newVariants = [...field.value];
                                                                                                                            newVariants[index] = { ...variant, stock: parseInt(e.target.value) || 0 };
                                                                                                                            field.onChange(newVariants);
                                                                                                                        }}
                                                                                                                        className="w-full rounded-lg border p-2 text-sm dark:border-white/10 dark:bg-[#171719]"
                                                                                                                    />
                                                                                                                </div>
                                                                                                                <div>
                                                                                                                    <label className="mb-1 block text-xs font-medium">
                                                                                                                        Fiyat (₺)
                                                                                                                    </label>
                                                                                                                    <input
                                                                                                                        type="number"
                                                                                                                        step="0.01"
                                                                                                                        min="0"
                                                                                                                        placeholder="Varyant özel fiyatı"
                                                                                                                        value={variant.price || ''}
                                                                                                                        onChange={(e) => {
                                                                                                                            const newVariants = [...field.value];
                                                                                                                            newVariants[index] = { ...variant, price: e.target.value ? parseFloat(e.target.value) : undefined };
                                                                                                                            field.onChange(newVariants);
                                                                                                                        }}
                                                                                                                        className="w-full rounded-lg border p-2 text-sm dark:border-white/10 dark:bg-[#171719]"
                                                                                                                    />
                                                                                                                </div>
                                                                                                                <div>
                                                                                                                    <label className="mb-1 block text-xs font-medium">
                                                                                                                        Ağırlık (gr)
                                                                                                                    </label>
                                                                                                                    <input
                                                                                                                        type="number"
                                                                                                                        step="0.01"
                                                                                                                        min="0"
                                                                                                                        placeholder="0"
                                                                                                                        value={variant.weight || ''}
                                                                                                                        onChange={(e) => {
                                                                                                                            const newVariants = [...field.value];
                                                                                                                            newVariants[index] = { ...variant, weight: e.target.value ? parseFloat(e.target.value) : undefined };
                                                                                                                            field.onChange(newVariants);
                                                                                                                        }}
                                                                                                                        className="w-full rounded-lg border p-2 text-sm dark:border-white/10 dark:bg-[#171719]"
                                                                                                                    />
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    ))
                                                                                                ) : (
                                                                                                    <p className="text-center text-sm text-gray-500">
                                                                                                        Henüz varyant eklenmedi. Varyant eklemek için yukarıdaki butona tıklayın.
                                                                                                    </p>
                                                                                                )}
                                                                                            </div>
                                                                                        </FormControl>
                                                                                        <FormDescription className="text-xs text-gray-500">
                                                                                            Ürününüz için beden, renk gibi varyantlar ekleyebilirsiniz. Her varyant için SKU ve stok bilgisi gereklidir.
                                                                                        </FormDescription>
                                                                                        {fieldState.error && (
                                                                                            <FormMessage>
                                                                                                {fieldState.error.message}
                                                                                            </FormMessage>
                                                                                        )}
                                                                                    </FormItem>
                                                                                )}
                                                                            />
                                                                        </div>

                                                                        {/* Otomatik Varyant Oluşturma Dialog */}
                                                                        <Dialog open={isVariantDialogOpen} onOpenChange={setIsVariantDialogOpen}>
                                                                            <DialogContent className="sm:max-w-lg">
                                                                                <DialogHeader>
                                                                                    <DialogTitle>Otomatik Varyant Oluştur</DialogTitle>
                                                                                    <DialogDescription>
                                                                                        Beden, numara veya renk bazlı otomatik varyantlar oluşturun.
                                                                                    </DialogDescription>
                                                                                </DialogHeader>
                                                                                <div className="space-y-4 py-4">
                                                                                    <div>
                                                                                        <Label className="mb-2 block">Varyant Tipi</Label>
                                                                                        <select
                                                                                            value={variantType}
                                                                                            onChange={(e) => setVariantType(e.target.value as any)}
                                                                                            className="w-full rounded-lg border p-2 text-sm dark:border-white/10 dark:bg-[#171719]"
                                                                                        >
                                                                                            <option value="size">Beden (S, M, L, XL)</option>
                                                                                            <option value="number">Numara (36-45)</option>
                                                                                            <option value="color">Renk</option>
                                                                                            <option value="combination">Beden + Renk Kombinasyonu</option>
                                                                                        </select>
                                                                                    </div>

                                                                                    {variantType === 'size' && (
                                                                                        <div>
                                                                                            <Label className="mb-2 block">Bedenler (virgülle ayırın)</Label>
                                                                                            <input
                                                                                                type="text"
                                                                                                value={sizeValues}
                                                                                                onChange={(e) => setSizeValues(e.target.value)}
                                                                                                placeholder="S, M, L, XL, XXL"
                                                                                                className="w-full rounded-lg border p-2 text-sm dark:border-white/10 dark:bg-[#171719]"
                                                                                            />
                                                                                            <p className="mt-1 text-xs text-gray-500">
                                                                                                Örnek: S, M, L, XL, XXL
                                                                                            </p>
                                                                                        </div>
                                                                                    )}

                                                                                    {variantType === 'number' && (
                                                                                        <div className="grid grid-cols-2 gap-4">
                                                                                            <div>
                                                                                                <Label className="mb-2 block">Başlangıç</Label>
                                                                                                <input
                                                                                                    type="number"
                                                                                                    value={numberRange.from}
                                                                                                    onChange={(e) => setNumberRange({ ...numberRange, from: parseInt(e.target.value) || 36 })}
                                                                                                    className="w-full rounded-lg border p-2 text-sm dark:border-white/10 dark:bg-[#171719]"
                                                                                                />
                                                                                            </div>
                                                                                            <div>
                                                                                                <Label className="mb-2 block">Bitiş</Label>
                                                                                                <input
                                                                                                    type="number"
                                                                                                    value={numberRange.to}
                                                                                                    onChange={(e) => setNumberRange({ ...numberRange, to: parseInt(e.target.value) || 45 })}
                                                                                                    className="w-full rounded-lg border p-2 text-sm dark:border-white/10 dark:bg-[#171719]"
                                                                                                />
                                                                                            </div>
                                                                                        </div>
                                                                                    )}

                                                                                    {variantType === 'color' && (
                                                                                        <div>
                                                                                            <Label className="mb-2 block">Renkler (virgülle ayırın)</Label>
                                                                                            <input
                                                                                                type="text"
                                                                                                value={colorValues}
                                                                                                onChange={(e) => setColorValues(e.target.value)}
                                                                                                placeholder="Kırmızı, Mavi, Siyah, Beyaz"
                                                                                                className="w-full rounded-lg border p-2 text-sm dark:border-white/10 dark:bg-[#171719]"
                                                                                            />
                                                                                            <p className="mt-1 text-xs text-gray-500">
                                                                                                Örnek: Kırmızı, Mavi, Siyah, Beyaz
                                                                                            </p>
                                                                                        </div>
                                                                                    )}

                                                                                    {variantType === 'combination' && (
                                                                                        <>
                                                                                            <div>
                                                                                                <Label className="mb-2 block">Bedenler (virgülle ayırın)</Label>
                                                                                                <input
                                                                                                    type="text"
                                                                                                    value={combinationSizes}
                                                                                                    onChange={(e) => setCombinationSizes(e.target.value)}
                                                                                                    placeholder="S, M, L, XL"
                                                                                                    className="w-full rounded-lg border p-2 text-sm dark:border-white/10 dark:bg-[#171719]"
                                                                                                />
                                                                                            </div>
                                                                                            <div>
                                                                                                <Label className="mb-2 block">Renkler (virgülle ayırın)</Label>
                                                                                                <input
                                                                                                    type="text"
                                                                                                    value={combinationColors}
                                                                                                    onChange={(e) => setCombinationColors(e.target.value)}
                                                                                                    placeholder="Kırmızı, Mavi, Siyah"
                                                                                                    className="w-full rounded-lg border p-2 text-sm dark:border-white/10 dark:bg-[#171719]"
                                                                                                />
                                                                                                <p className="mt-1 text-xs text-gray-500">
                                                                                                    Her beden için her renk kombinasyonu oluşturulacak.
                                                                                                </p>
                                                                                            </div>
                                                                                        </>
                                                                                    )}
                                                                                </div>
                                                                                <DialogFooter>
                                                                                    <Button
                                                                                        type="button"
                                                                                        variant="outline"
                                                                                        onClick={() => setIsVariantDialogOpen(false)}
                                                                                    >
                                                                                        İptal
                                                                                    </Button>
                                                                                    <Button
                                                                                        type="button"
                                                                                        onClick={generateVariants}
                                                                                        disabled={
                                                                                            (variantType === 'size' && !sizeValues.trim()) ||
                                                                                            (variantType === 'number' && numberRange.from > numberRange.to) ||
                                                                                            (variantType === 'color' && !colorValues.trim()) ||
                                                                                            (variantType === 'combination' && (!combinationSizes.trim() || !combinationColors.trim()))
                                                                                        }
                                                                                    >
                                                                                        Oluştur
                                                                                    </Button>
                                                                                </DialogFooter>
                                                                            </DialogContent>
                                                                        </Dialog>

                                                                        <FormField
                                                                            control={
                                                                                form.control
                                                                            }
                                                                            name="images"
                                                                            render={({
                                                                                field,
                                                                                fieldState,
                                                                            }) => (
                                                                                <FormItem>
                                                                                    <FormLabel className="text-sm font-medium">
                                                                                        Ürün Görselleri
                                                                                    </FormLabel>
                                                                                    <FormControl>
                                                                                        <FileUpload
                                                                                            maxFiles={4}
                                                                                            maxSize={5 * 1024 * 1024}
                                                                                            value={field.value || []}
                                                                                            onValueChange={field.onChange}
                                                                                            onFileReject={(file, message) => {
                                                                                                toast.error(message, {
                                                                                                    description: `"${file.name.length > 20 ? `${file.name.slice(0, 20)}...` : file.name}" reddedildi`,
                                                                                                });
                                                                                            }}
                                                                                            multiple
                                                                                        >
                                                                                            <FileUploadDropzone>
                                                                                                <div className="flex flex-col items-center gap-1 text-center">
                                                                                                    <div className="flex items-center justify-center rounded-full border p-2.5">
                                                                                                        <Upload className="size-6 text-muted-foreground" />
                                                                                                    </div>
                                                                                                    <p className="font-medium text-sm dark:text-white">
                                                                                                        Görselleri buraya sürükleyin
                                                                                                    </p>
                                                                                                    <p className="text-muted-foreground text-xs">
                                                                                                        Veya tıklayarak seçin (maksimum 4
                                                                                                        görsel, her biri en fazla 5MB)
                                                                                                    </p>
                                                                                                </div>
                                                                                                <FileUploadTrigger asChild>
                                                                                                    <Button
                                                                                                        variant="outline"
                                                                                                        size="sm"
                                                                                                        className="mt-2 w-fit"
                                                                                                        type="button"
                                                                                                    >
                                                                                                        Dosya Seç
                                                                                                    </Button>
                                                                                                </FileUploadTrigger>
                                                                                            </FileUploadDropzone>
                                                                                            {field.value &&
                                                                                                field.value.length >
                                                                                                    0 && (
                                                                                                    <FileUploadList>
                                                                                                        {field.value.map(
                                                                                                            (
                                                                                                                file,
                                                                                                                index,
                                                                                                            ) => (
                                                                                                                <FileUploadItem
                                                                                                                    key={
                                                                                                                        index
                                                                                                                    }
                                                                                                                    value={
                                                                                                                        file
                                                                                                                    }
                                                                                                                >
                                                                                                                    <FileUploadItemDelete />
                                                                                                                    <FileUploadItemMetadata>
                                                                                                                        <div className="truncate">
                                                                                                                            {file.name.length >
                                                                                                                            20
                                                                                                                                ? `${file.name.slice(0, 20)}...`
                                                                                                                                : file.name}
                                                                                                                        </div>
                                                                                                                        <div className="text-xs opacity-75">
                                                                                                                            {(
                                                                                                                                file.size /
                                                                                                                                1024 /
                                                                                                                                1024
                                                                                                                            ).toFixed(
                                                                                                                                2,
                                                                                                                            )}{' '}
                                                                                                                            MB
                                                                                                                        </div>
                                                                                                                        {index ===
                                                                                                                        0 && (
                                                                                                                            <Status
                                                                                                                                variant="success"
                                                                                                                                className="mt-1"
                                                                                                                            >
                                                                                                                                <StatusIndicator />
                                                                                                                                <StatusLabel>
                                                                                                                                    Ana
                                                                                                                                    Görsel
                                                                                                                                </StatusLabel>
                                                                                                                            </Status>
                                                                                                                        )}
                                                                                                                    </FileUploadItemMetadata>
                                                                                                                </FileUploadItem>
                                                                                                            ),
                                                                                                        )}
                                                                                                    </FileUploadList>
                                                                                                )}
                                                                                        </FileUpload>
                                                                                    </FormControl>
                                                                                    <FormDescription className="text-xs text-gray-500">
                                                                                        Maksimum 4 görsel
                                                                                        seçebilirsiniz. İlk
                                                                                        görsel otomatik
                                                                                        olarak ana görsel
                                                                                        olacaktır.
                                                                                    </FormDescription>
                                                                                    {fieldState.error && (
                                                                                        <FormMessage>
                                                                                            {
                                                                                                fieldState
                                                                                                    .error
                                                                                                    .message
                                                                                            }
                                                                                        </FormMessage>
                                                                                    )}
                                                                                </FormItem>
                                                                            )}
                                                                        />

                                                                        <FormField
                                                                            control={
                                                                                form.control
                                                                            }
                                                                            name="videos"
                                                                            render={({
                                                                                field,
                                                                                fieldState,
                                                                            }) => (
                                                                                <FormItem>
                                                                                    <FormLabel className="text-sm font-medium">
                                                                                        Ürün Videoları
                                                                                    </FormLabel>
                                                                                    <FormControl>
                                                                                        <div className="space-y-3">
                                                                                            {field.value &&
                                                                                                field.value.length >
                                                                                                    0 &&
                                                                                                field.value.map(
                                                                                                    (
                                                                                                        video,
                                                                                                        index,
                                                                                                    ) => (
                                                                                                        <div
                                                                                                            key={
                                                                                                                index
                                                                                                            }
                                                                                                            className="flex gap-2 rounded-xl border p-3 dark:border-white/10"
                                                                                                        >
                                                                                                            <input
                                                                                                                type="text"
                                                                                                                placeholder="Video URL (YouTube, Vimeo vb.)"
                                                                                                                value={
                                                                                                                    video.url ||
                                                                                                                    ''
                                                                                                                }
                                                                                                                onChange={(
                                                                                                                    e,
                                                                                                                ) => {
                                                                                                                    const newVideos =
                                                                                                                        [
                                                                                                                            ...field.value,
                                                                                                                        ];
                                                                                                                    newVideos[
                                                                                                                        index
                                                                                                                    ] =
                                                                                                                        {
                                                                                                                            ...video,
                                                                                                                            url: e
                                                                                                                                .target
                                                                                                                                .value,
                                                                                                                        };
                                                                                                                    field.onChange(
                                                                                                                        newVideos,
                                                                                                                    );
                                                                                                                }}
                                                                                                                className="flex-1 rounded-xl border p-2 dark:border-white/10 dark:bg-[#171719]"
                                                                                                            />
                                                                                                            <button
                                                                                                                type="button"
                                                                                                                onClick={() => {
                                                                                                                    const newVideos =
                                                                                                                        field.value.filter(
                                                                                                                            (
                                                                                                                                _,
                                                                                                                                i,
                                                                                                                            ) =>
                                                                                                                                i !==
                                                                                                                                index,
                                                                                                                        );
                                                                                                                    field.onChange(
                                                                                                                        newVideos,
                                                                                                                    );
                                                                                                                }}
                                                                                                                className="rounded-xl border px-3 py-2 text-sm text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20"
                                                                                                            >
                                                                                                                Sil
                                                                                                            </button>
                                                                                                        </div>
                                                                                                    ),
                                                                                                )}
                                                                                            {(!field.value ||
                                                                                                field.value.length <
                                                                                                    4) && (
                                                                                                <button
                                                                                                    type="button"
                                                                                                    onClick={() => {
                                                                                                        field.onChange(
                                                                                                            [
                                                                                                                ...(field.value ||
                                                                                                                    []),
                                                                                                                {
                                                                                                                    url: '',
                                                                                                                    title:
                                                                                                                        '',
                                                                                                                },
                                                                                                            ],
                                                                                                        );
                                                                                                    }}
                                                                                                    className="w-full rounded-xl border border-dashed p-3 text-sm text-gray-500 hover:border-solid hover:bg-gray-50 dark:hover:bg-gray-800"
                                                                                                >
                                                                                                    + Video Ekle
                                                                                                </button>
                                                                                            )}
                                                                                        </div>
                                                                                    </FormControl>
                                                                                    <FormDescription className="text-xs text-gray-500">
                                                                                        Maksimum 4 video
                                                                                        ekleyebilirsiniz.
                                                                                        YouTube, Vimeo veya
                                                                                        diğer video
                                                                                        platformlarından URL
                                                                                        ekleyin.
                                                                                    </FormDescription>
                                                                                    {fieldState.error && (
                                                                                        <FormMessage>
                                                                                            {
                                                                                                fieldState
                                                                                                    .error
                                                                                                    .message
                                                                                            }
                                                                                        </FormMessage>
                                                                                    )}
                                                                                </FormItem>
                                                                            )}
                                                                        />

                                                                        {/* Güvenlik Bilgileri */}
                                                                        <div className="space-y-4 rounded-xl border border-blue-200 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-950/20">
                                                                            <h3 className="text-sm font-semibold text-blue-900 dark:text-blue-300">
                                                                                Ürün Güvenliği Bilgileri
                                                                            </h3>
                                                                            
                                                                            <FormField
                                                                                control={form.control}
                                                                                name="safety_information"
                                                                                render={({ field, fieldState }) => (
                                                                                    <FormItem>
                                                                                        <FormLabel className="text-sm font-medium">
                                                                                            Güvenlik Bilgileri
                                                                                        </FormLabel>
                                                                                        <FormControl>
                                                                                            <textarea
                                                                                                {...field}
                                                                                                placeholder="Ürün güvenliği uyarı ve açıklamalarını girin..."
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

                                                                            <div className="mb-3 flex items-center gap-2">
                                                                                <input
                                                                                    type="checkbox"
                                                                                    id="is_manufacturer_self"
                                                                                    checked={isManufacturerSelf}
                                                                                    onChange={(e) => {
                                                                                        setIsManufacturerSelf(e.target.checked);
                                                                                        if (e.target.checked && vendor) {
                                                                                            form.setValue('manufacturer_name', vendor.name || '');
                                                                                            form.setValue('manufacturer_address', vendor.address || '');
                                                                                            form.setValue('manufacturer_contact', vendor.email || vendor.phone || '');
                                                                                        } else {
                                                                                            form.setValue('manufacturer_name', '');
                                                                                            form.setValue('manufacturer_address', '');
                                                                                            form.setValue('manufacturer_contact', '');
                                                                                        }
                                                                                    }}
                                                                                    className="h-4 w-4 rounded border-gray-300 text-purple-600 focus:ring-purple-500"
                                                                                />
                                                                                <label htmlFor="is_manufacturer_self" className="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                                                    Üretici Siz misiniz?
                                                                                </label>
                                                                            </div>

                                                                            <div className="grid grid-cols-1 gap-3 md:grid-cols-2">
                                                                                <FormField
                                                                                    control={form.control}
                                                                                    name="manufacturer_name"
                                                                                    render={({ field, fieldState }) => (
                                                                                        <FormItem>
                                                                                            <FormLabel className="text-sm font-medium">
                                                                                                Üretici Adı
                                                                                            </FormLabel>
                                                                                            <FormControl>
                                                                                                <input
                                                                                                    {...field}
                                                                                                    type="text"
                                                                                                    placeholder="Üretici firma adı"
                                                                                                    disabled={isManufacturerSelf}
                                                                                                    className="w-full rounded-xl border p-3 dark:border-white/10 dark:bg-[#171719] disabled:opacity-50 disabled:cursor-not-allowed"
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
                                                                                    name="manufacturer_contact"
                                                                                    render={({ field, fieldState }) => (
                                                                                        <FormItem>
                                                                                            <FormLabel className="text-sm font-medium">
                                                                                                Üretici İletişim
                                                                                            </FormLabel>
                                                                                            <FormControl>
                                                                                                <input
                                                                                                    {...field}
                                                                                                    type="text"
                                                                                                    placeholder="Email veya telefon"
                                                                                                    disabled={isManufacturerSelf}
                                                                                                    className="w-full rounded-xl border p-3 dark:border-white/10 dark:bg-[#171719] disabled:opacity-50 disabled:cursor-not-allowed"
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

                                                                            <FormField
                                                                                control={form.control}
                                                                                name="manufacturer_address"
                                                                                render={({ field, fieldState }) => (
                                                                                    <FormItem>
                                                                                        <FormLabel className="text-sm font-medium">
                                                                                            Üretici Adresi
                                                                                        </FormLabel>
                                                                                        <FormControl>
                                                                                            <textarea
                                                                                                {...field}
                                                                                                placeholder="Üretici firma adresi"
                                                                                                disabled={isManufacturerSelf}
                                                                                                className="h-20 w-full rounded-xl border p-3 dark:border-white/10 dark:bg-[#171719] disabled:opacity-50 disabled:cursor-not-allowed"
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

                                                                            <div className="grid grid-cols-1 gap-3 md:grid-cols-2">
                                                                                <FormField
                                                                                    control={form.control}
                                                                                    name="responsible_party_name"
                                                                                    render={({ field, fieldState }) => (
                                                                                        <FormItem>
                                                                                            <FormLabel className="text-sm font-medium">
                                                                                                Ürün Sorumlusu
                                                                                            </FormLabel>
                                                                                            <FormControl>
                                                                                                <input
                                                                                                    {...field}
                                                                                                    type="text"
                                                                                                    placeholder="Sorumlu kişi/firma adı"
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
                                                                                    name="responsible_party_contact"
                                                                                    render={({ field, fieldState }) => (
                                                                                        <FormItem>
                                                                                            <FormLabel className="text-sm font-medium">
                                                                                                Sorumlu İletişim
                                                                                            </FormLabel>
                                                                                            <FormControl>
                                                                                                <input
                                                                                                    {...field}
                                                                                                    type="text"
                                                                                                    placeholder="Email veya telefon"
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

                                                                            <FormField
                                                                                control={form.control}
                                                                                name="responsible_party_address"
                                                                                render={({ field, fieldState }) => (
                                                                                    <FormItem>
                                                                                        <FormLabel className="text-sm font-medium">
                                                                                            Sorumlu Adresi
                                                                                        </FormLabel>
                                                                                        <FormControl>
                                                                                            <textarea
                                                                                                {...field}
                                                                                                placeholder="Sorumlu adresi"
                                                                                                className="h-20 w-full rounded-xl border p-3 dark:border-white/10 dark:bg-[#171719]"
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

                                                                        {/* Ek Bilgiler */}
                                                                        <div className="space-y-4 rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/20">
                                                                            <h3 className="text-sm font-semibold dark:text-white">
                                                                                Ek Bilgiler
                                                                            </h3>
                                                                            
                                                                            <FormField
                                                                                control={form.control}
                                                                                name="additional_information"
                                                                                render={({ field, fieldState }) => (
                                                                                    <FormItem>
                                                                                        <FormLabel className="text-sm font-medium">
                                                                                            Ek Bilgiler (Markdown)
                                                                                        </FormLabel>
                                                                                        <FormControl>
                                                                                            <textarea
                                                                                                {...field}
                                                                                                placeholder="Markdown formatında ek bilgiler..."
                                                                                                className="h-32 w-full rounded-xl border p-3 dark:border-white/10 dark:bg-[#171719]"
                                                                                            />
                                                                                        </FormControl>
                                                                                        <FormDescription className="text-xs text-gray-500">
                                                                                            Markdown formatında ek bilgiler ekleyebilirsiniz.
                                                                                        </FormDescription>
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
                                                                                name="additional_info"
                                                                                render={({ field, fieldState }) => (
                                                                                    <FormItem>
                                                                                        <FormLabel className="text-sm font-medium">
                                                                                            Ek Bilgiler (Liste)
                                                                                        </FormLabel>
                                                                                        <FormControl>
                                                                                            <textarea
                                                                                                {...field}
                                                                                                placeholder="Her satır bir madde olacak şekilde ek bilgiler girin..."
                                                                                                className="h-24 w-full rounded-xl border p-3 dark:border-white/10 dark:bg-[#171719]"
                                                                                            />
                                                                                        </FormControl>
                                                                                        <FormDescription className="text-xs text-gray-500">
                                                                                            Her satır bir madde olarak kaydedilecektir.
                                                                                        </FormDescription>
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
                                                                                name="tags"
                                                                                render={({ field, fieldState }) => (
                                                                                    <FormItem>
                                                                                        <FormLabel className="text-sm font-medium">
                                                                                            Etiketler
                                                                                        </FormLabel>
                                                                                        <FormControl>
                                                                                            <input
                                                                                                {...field}
                                                                                                type="text"
                                                                                                placeholder="Etiketler (virgülle ayırın)"
                                                                                                className="w-full rounded-xl border p-3 dark:border-white/10 dark:bg-[#171719]"
                                                                                            />
                                                                                        </FormControl>
                                                                                        <FormDescription className="text-xs text-gray-500">
                                                                                            Etiketleri virgülle ayırarak girin (örn: elektronik, telefon, akıllı telefon).
                                                                                        </FormDescription>
                                                                                        {fieldState.error && (
                                                                                            <FormMessage>
                                                                                                {fieldState.error.message}
                                                                                            </FormMessage>
                                                                                        )}
                                                                                    </FormItem>
                                                                                )}
                                                                            />
                                                                        </div>

                                                                        {error && (
                                                                            <div className="rounded-xl border border-red-300 bg-red-100 p-3 text-sm text-red-700 dark:border-red-800 dark:bg-red-900/20 dark:text-red-400">
                                                                                {
                                                                                    error
                                                                                }
                                                                            </div>
                                                                        )}

                                                                        <div className="sticky bottom-0 z-20 flex flex-shrink-0 gap-3 border-t bg-white pt-6 pb-4 dark:bg-[#17191a]">
                                                                            <button
                                                                                type="submit"
                                                                                disabled={
                                                                                    isLoading
                                                                                }
                                                                                className="flex-1 rounded-xl border border-white/10 bg-[#171719] px-6 py-3 font-medium text-white transition-opacity hover:opacity-90 disabled:opacity-50 dark:bg-[#131313] dark:hover:bg-[#171719]"
                                                                            >
                                                                                {isLoading
                                                                                    ? 'Oluşturuluyor...'
                                                                                    : 'Ürün Oluştur'}
                                                                            </button>
                                                                            <button
                                                                                type="button"
                                                                                className="flex-1 rounded-xl border border-gray-300 px-6 py-3 font-medium transition-colors hover:bg-gray-50 dark:border-white/10 dark:hover:bg-[#171719]"
                                                                                onClick={() =>
                                                                                    setIsSheetOpen(
                                                                                        false,
                                                                                    )
                                                                                }
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

                                        {/* KPI cards */}
                                        <div className="grid grid-cols-1 gap-4 md:grid-cols-2 md:gap-6 lg:grid-cols-3">
                                            <div className="rounded-2xl border border-gray-200 bg-white p-4 text-black md:p-6 dark:border-[#313131] dark:bg-[#171719] dark:text-white">
                                                <div className="space-y-1.5 pb-2">
                                                    <span className="text-sm text-black md:text-base dark:text-white">
                                                        Toplam Ürün
                                                    </span>
                                                </div>
                                                <div className="pt-0">
                                                    <h3 className="text-xl md:text-2xl">
                                                        {totalProducts}
                                                    </h3>
                                                </div>
                                            </div>
                                            <div className="rounded-2xl border border-gray-200 bg-white p-4 text-black md:p-6 dark:border-[#313131] dark:bg-[#171719] dark:text-white">
                                                <div className="space-y-1.5 pb-2">
                                                    <span className="text-sm text-black md:text-base dark:text-white">
                                                        Aktif Ürün
                                                    </span>
                                                </div>
                                                <div className="pt-0">
                                                    <h3 className="text-xl md:text-2xl">
                                                        {activeProducts}
                                                    </h3>
                                                </div>
                                            </div>
                                            <div className="rounded-2xl border border-gray-200 bg-white p-4 text-black md:p-6 dark:border-[#313131] dark:bg-[#171719] dark:text-white">
                                                <div className="space-y-1.5 pb-2">
                                                    <span className="text-sm text-black md:text-base dark:text-white">
                                                        Toplam Stok
                                                    </span>
                                                </div>
                                                <div className="pt-0">
                                                    <h3 className="text-xl md:text-2xl">
                                                        {totalStock}
                                                    </h3>
                                                </div>
                                            </div>
                                        </div>

                                        {/* Products table */}
                                        <div className="flex flex-col gap-4 md:gap-6">
                                            <div className="overflow-hidden rounded-2xl border border-gray-200 dark:border-[#313131]">
                                                <div className="relative w-full overflow-x-auto">
                                                    <table className="w-full table-fixed caption-bottom text-sm">
                                                        <thead>
                                                            <tr className="border-b border-gray-200 bg-white dark:border-[#313131] dark:bg-[#171719]">
                                                                <th
                                                                    className="h-12 px-4 text-left align-middle font-medium"
                                                                    style={{
                                                                        width: 200,
                                                                    }}
                                                                >
                                                                    Ürün Adı
                                                                </th>
                                                                <th
                                                                    className="h-12 px-4 text-left align-middle font-medium"
                                                                    style={{
                                                                        width: 150,
                                                                    }}
                                                                >
                                                                    Marka
                                                                </th>
                                                                <th
                                                                    className="h-12 px-4 text-left align-middle font-medium"
                                                                    style={{
                                                                        width: 150,
                                                                    }}
                                                                >
                                                                    Kategori
                                                                </th>
                                                                <th
                                                                    className="h-12 px-4 text-left align-middle font-medium"
                                                                    style={{
                                                                        width: 120,
                                                                    }}
                                                                >
                                                                    Fiyat
                                                                </th>
                                                                <th
                                                                    className="h-12 px-4 text-left align-middle font-medium"
                                                                    style={{
                                                                        width: 100,
                                                                    }}
                                                                >
                                                                    Stok
                                                                </th>
                                                                <th
                                                                    className="h-12 px-4 text-left align-middle font-medium"
                                                                    style={{
                                                                        width: 120,
                                                                    }}
                                                                >
                                                                    Durum
                                                                </th>
                                                                <th
                                                                    className="hidden h-12 px-4 text-left align-middle font-medium md:table-cell"
                                                                    style={{
                                                                        width: 150,
                                                                    }}
                                                                >
                                                                    Tarih
                                                                </th>
                                                                <th
                                                                    className="h-12 px-4 text-left align-middle font-medium"
                                                                    style={{
                                                                        width: 120,
                                                                    }}
                                                                >
                                                                    İşlemler
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody className="[&_tr:last-child]:border-0">
                                                            {filteredProducts.length ===
                                                            0 ? (
                                                                <tr>
                                                                    <td
                                                                        colSpan={
                                                                            8
                                                                        }
                                                                        className="p-8 text-center text-gray-500 dark:text-gray-400"
                                                                    >
                                                                        Ürün
                                                                        bulunamadı.
                                                                    </td>
                                                                </tr>
                                                            ) : (
                                                                filteredProducts.map(
                                                                    (
                                                                        product,
                                                                    ) => (
                                                                        <tr
                                                                            key={
                                                                                product.id
                                                                            }
                                                                            className="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted"
                                                                        >
                                                                            <td className="p-2 align-middle md:p-4">
                                                                                <div className="text-xs font-medium md:text-sm dark:text-white">
                                                                                    {
                                                                                        product.title
                                                                                    }
                                                                                </div>
                                                                            </td>
                                                                            <td className="p-2 align-middle text-xs md:p-4 md:text-sm">
                                                                                {
                                                                                    product
                                                                                        .brand
                                                                                        ?.name ||
                                                                                    '-'
                                                                                }
                                                                            </td>
                                                                            <td className="p-2 align-middle text-xs md:p-4 md:text-sm">
                                                                                {
                                                                                    product
                                                                                        .category
                                                                                        ?.name ||
                                                                                    '-'
                                                                                }
                                                                            </td>
                                                                            <td className="p-2 align-middle text-xs md:p-4 md:text-sm">
                                                                                {formatPrice(
                                                                                    product.price,
                                                                                )}
                                                                            </td>
                                                                            <td className="p-2 align-middle text-xs md:p-4 md:text-sm">
                                                                                <span
                                                                                    className={`${
                                                                                        product.stock >
                                                                                        0
                                                                                            ? 'text-green-600 dark:text-green-400'
                                                                                            : 'text-red-600 dark:text-red-400'
                                                                                    }`}
                                                                                >
                                                                                    {
                                                                                        product.stock
                                                                                    }
                                                                                </span>
                                                                            </td>
                                                                            <td className="p-2 align-middle md:p-4">
                                                                                <div className="flex shrink">
                                                                                    <div
                                                                                        className={`flex flex-row items-center justify-center rounded-[0.5em] px-[0.5em] py-[0.2em] text-xs font-medium capitalize md:px-[0.7em] md:py-[0.3em] md:text-sm ${
                                                                                            product.is_active
                                                                                                ? 'bg-emerald-100 text-emerald-500 dark:bg-emerald-950 dark:text-emerald-500'
                                                                                                : 'bg-gray-100 text-gray-500 dark:bg-gray-950 dark:text-gray-500'
                                                                                        }`}
                                                                                    >
                                                                                        {product.is_active
                                                                                            ? 'Aktif'
                                                                                            : 'Pasif'}
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                            <td className="hidden p-2 align-middle text-xs md:table-cell md:p-4 md:text-sm">
                                                                                {formatDate(
                                                                                    product.created_at,
                                                                                )}
                                                                            </td>
                                                                            <td className="p-2 align-middle md:p-4">
                                                                                <Button
                                                                                    variant="outline"
                                                                                    size="sm"
                                                                                    onClick={() => {
                                                                                        setSelectedProduct(product);
                                                                                        setProductStatus(product.is_active);
                                                                                        setIsEditDialogOpen(true);
                                                                                    }}
                                                                                    className="flex items-center gap-2"
                                                                                >
                                                                                    <Edit className="h-4 w-4" />
                                                                                    Düzenle
                                                                                </Button>
                                                                            </td>
                                                                        </tr>
                                                                    ),
                                                                )
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
                    </div>
                </main>
            </div>

            {/* Ürün Düzenleme Dialog */}
            <Dialog open={isEditDialogOpen} onOpenChange={setIsEditDialogOpen}>
                <DialogContent className="sm:max-w-lg">
                    <DialogHeader>
                        <DialogTitle>Ürün Durumu Güncelle</DialogTitle>
                        <DialogDescription>
                            Ürün durumunu güncelleyin.
                        </DialogDescription>
                    </DialogHeader>
                    {selectedProduct && (
                        <div className="space-y-4 py-4">
                            <div>
                                <Label className="mb-2 block">Ürün Adı</Label>
                                <p className="text-sm font-medium dark:text-white">
                                    {selectedProduct.title}
                                </p>
                            </div>
                            <div>
                                <Label className="mb-2 block">Durum</Label>
                                <select
                                    value={productStatus ? 'true' : 'false'}
                                    onChange={(e) => setProductStatus(e.target.value === 'true')}
                                    className="w-full rounded-lg border p-2 text-sm dark:border-white/10 dark:bg-[#171719]"
                                >
                                    <option value="true">Aktif</option>
                                    <option value="false">Pasif</option>
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
                        <Button
                            onClick={() => {
                                if (!selectedProduct) {
                                    return;
                                }

                                router.put(
                                    `/products/${selectedProduct.id}`,
                                    {
                                        is_active: productStatus,
                                    },
                                    {
                                        onSuccess: () => {
                                            setIsEditDialogOpen(false);
                                            setSelectedProduct(null);
                                            toast.success('Ürün durumu güncellendi.');
                                        },
                                        onError: (errors) => {
                                            toast.error('Ürün güncellenirken bir hata oluştu.', {
                                                description: Object.values(errors).join(', '),
                                            });
                                        },
                                    },
                                );
                            }}
                        >
                            Kaydet
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </AppLayout>
    );
}
