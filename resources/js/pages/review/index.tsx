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
import { MessageSquare, Star } from 'lucide-react';
import { useState } from 'react';
import { toast } from 'sonner';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Değerlendirmeler',
        href: '/reviews',
    },
];

interface Review {
    id: string;
    rating: number;
    comment: string;
    created_at: string;
    user?: {
        id: string;
        name?: string;
        email?: string;
    } | null;
    product?: {
        id: string;
        name: string;
    } | null;
}

interface Props {
    reviews?: Review[];
}

export default function Reviews({ reviews = [] }: Props) {
    const [isReplyDialogOpen, setIsReplyDialogOpen] = useState(false);
    const [selectedReview, setSelectedReview] = useState<Review | null>(null);
    const [vendorResponse, setVendorResponse] = useState('');

    const formatDate = (dateString: string) => {
        return new Intl.DateTimeFormat('tr-TR', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        }).format(new Date(dateString));
    };

    const handleReply = (review: Review) => {
        setSelectedReview(review);
        setVendorResponse('');
        setIsReplyDialogOpen(true);
    };

    const handleUpdate = () => {
        if (!selectedReview) {
            return;
        }

        router.put(
            `/reviews/${selectedReview.id}`,
            {
                vendor_response: vendorResponse,
            },
            {
                onSuccess: () => {
                    setIsReplyDialogOpen(false);
                    setSelectedReview(null);
                    setVendorResponse('');
                    toast.success('Cevabınız başarıyla eklendi.');
                },
                onError: (errors) => {
                    toast.error('Cevap eklenirken bir hata oluştu.', {
                        description: Object.values(errors).join(', '),
                    });
                },
            },
        );
    };

    const renderStars = (rating: number) => {
        return Array.from({ length: 5 }, (_, i) => (
            <Star
                key={i}
                className={`h-4 w-4 ${
                    i < rating
                        ? 'fill-yellow-400 text-yellow-400'
                        : 'fill-gray-300 text-gray-300 dark:fill-gray-600 dark:text-gray-600'
                }`}
            />
        ));
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Değerlendirmeler" />
            <div className="relative flex h-full w-full flex-col gap-y-4">
                <main className="relative flex min-h-0 w-full grow flex-col">
                    <div className="flex h-full w-full flex-row gap-x-2 p-5">
                        <div className="relative flex w-full flex-col items-center rounded-2xl border-gray-200 px-4 md:overflow-y-auto md:border md:bg-white md:px-8 md:shadow-xs dark:border-[#313131] dark:md:bg-[#181818]">
                            <div className="flex h-full w-full max-w-[--breakpoint-xl] flex-col">
                                <div className="flex w-full flex-col gap-y-4 py-8 md:flex-row md:items-center md:justify-between md:py-8">
                                    <h4 className="text-2xl font-medium whitespace-nowrap dark:text-white">
                                        Değerlendirmeler
                                    </h4>
                                </div>

                                <div className="flex w-full flex-col gap-y-8 pb-8">
                                    <p className="text-gray-500 dark:text-[#999999]">
                                        Ürünleriniz hakkındaki müşteri değerlendirmelerini görüntüleyin.
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
                                                                Kullanıcı
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Puan
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Yorum
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
                                                        {reviews.length === 0 ? (
                                                            <tr>
                                                                <td
                                                                    colSpan={6}
                                                                    className="p-8 text-center text-gray-500 dark:text-gray-400"
                                                                >
                                                                    Henüz değerlendirme yok.
                                                                </td>
                                                            </tr>
                                                        ) : (
                                                            reviews.map((review) => (
                                                                <tr
                                                                    key={review.id}
                                                                    className="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted"
                                                                >
                                                                    <td className="p-4 align-middle">
                                                                        <div className="text-sm font-medium dark:text-white">
                                                                            {review.product?.name || '-'}
                                                                        </div>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <div className="text-sm dark:text-white">
                                                                            {review.user?.name || review.user?.email || '-'}
                                                                        </div>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <div className="flex items-center gap-1">
                                                                            {renderStars(review.rating)}
                                                                        </div>
                                                                    </td>
                                                                    <td className="max-w-md p-4 align-middle">
                                                                        <p className="line-clamp-2 text-sm dark:text-gray-300">
                                                                            {review.comment || '-'}
                                                                        </p>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <span className="text-sm text-gray-500 dark:text-gray-400">
                                                                            {formatDate(review.created_at)}
                                                                        </span>
                                                                    </td>
                                                                    <td className="p-4 align-middle">
                                                                        <Button
                                                                            variant="outline"
                                                                            size="sm"
                                                                            onClick={() => handleReply(review)}
                                                                            className="flex items-center gap-2"
                                                                        >
                                                                            <MessageSquare className="h-4 w-4" />
                                                                            Cevapla
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

            {/* Cevap Dialog */}
            <Dialog open={isReplyDialogOpen} onOpenChange={setIsReplyDialogOpen}>
                <DialogContent className="sm:max-w-lg">
                    <DialogHeader>
                        <DialogTitle>Değerlendirmeye Cevap Ver</DialogTitle>
                        <DialogDescription>
                            Müşteri değerlendirmesine cevap yazın.
                        </DialogDescription>
                    </DialogHeader>
                    {selectedReview && (
                        <div className="space-y-4 py-4">
                            <div>
                                <Label className="mb-2 block">Müşteri Yorumu</Label>
                                <p className="text-sm text-gray-600 dark:text-gray-400 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                    {selectedReview.comment || '-'}
                                </p>
                            </div>
                            <div>
                                <Label className="mb-2 block">Cevabınız</Label>
                                <textarea
                                    value={vendorResponse}
                                    onChange={(e) => setVendorResponse(e.target.value)}
                                    placeholder="Cevabınızı buraya yazın..."
                                    rows={4}
                                    className="w-full rounded-lg border p-2 text-sm dark:border-white/10 dark:bg-[#171719]"
                                />
                            </div>
                        </div>
                    )}
                    <DialogFooter>
                        <Button
                            variant="outline"
                            onClick={() => setIsReplyDialogOpen(false)}
                        >
                            İptal
                        </Button>
                        <Button onClick={handleUpdate}>Gönder</Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </AppLayout>
    );
}

