import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/react';
import { MessageSquare } from 'lucide-react';
import { useState } from 'react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Ürün Soruları',
        href: '/product-questions',
    },
];

interface ProductQuestion {
    id: string;
    question: string;
    answer: string | null;
    answered_by_vendor: boolean;
    created_at: string;
    user?: {
        id: string;
        name?: string;
        email?: string;
    } | null;
    product?: {
        id: string;
        name: string; // Backend'den 'title' geliyor ama 'name' olarak map ediliyor
    } | null;
}

interface Props {
    productQuestions?: ProductQuestion[];
}

export default function ProductQuestions({ productQuestions = [] }: Props) {
    const [selectedQuestion, setSelectedQuestion] =
        useState<ProductQuestion | null>(null);
    const [isDialogOpen, setIsDialogOpen] = useState(false);
    const [answer, setAnswer] = useState('');
    const [isSubmitting, setIsSubmitting] = useState(false);

    const formatDate = (dateString: string) => {
        return new Intl.DateTimeFormat('tr-TR', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        }).format(new Date(dateString));
    };

    const getUserInitials = (name?: string, email?: string) => {
        if (name) {
            return name.charAt(0).toUpperCase();
        }
        if (email) {
            return email.charAt(0).toUpperCase();
        }
        return '?';
    };

    const handleAnswerClick = (question: ProductQuestion) => {
        setSelectedQuestion(question);
        setAnswer(question.answer || '');
        setIsDialogOpen(true);
    };

    const handleSubmitAnswer = () => {
        if (!selectedQuestion || !answer.trim()) {
            return;
        }

        setIsSubmitting(true);
        router.put(
            `/product-questions/${selectedQuestion.id}`,
            { answer: answer.trim() },
            {
                onSuccess: () => {
                    setIsDialogOpen(false);
                    setSelectedQuestion(null);
                    setAnswer('');
                },
                onError: () => {
                    setIsSubmitting(false);
                },
                onFinish: () => {
                    setIsSubmitting(false);
                },
            },
        );
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Ürün Soruları" />
            <div className="relative flex h-full w-full flex-col gap-y-4">
                <main className="relative flex min-h-0 w-full grow flex-col">
                    <div className="flex h-full w-full flex-row gap-x-2 p-5">
                        <div className="relative flex w-full flex-col items-center rounded-2xl border-gray-200 px-4 md:overflow-y-auto md:border md:bg-white md:px-8 md:shadow-xs dark:border-[#313131] dark:md:bg-[#181818]">
                            <div className="flex h-full w-full max-w-[--breakpoint-xl] flex-col">
                                <div className="flex w-full flex-col gap-y-4 py-8 md:flex-row md:items-center md:justify-between md:py-8">
                                    <h4 className="text-2xl font-medium whitespace-nowrap dark:text-white">
                                        Ürün Soruları
                                    </h4>
                                </div>

                                <div
                                    className="flex w-full flex-col gap-y-8 pb-8"
                                    style={{ opacity: 1 }}
                                >
                                    <p className="text-gray-500 dark:text-[#999999]">
                                        Kullanıcıların ürünleriniz hakkında
                                        sorduğu soruları görüntüleyin ve
                                        yanıtlayın.
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
                                                                Soru
                                                            </th>
                                                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                                                Cevap
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
                                                        {productQuestions.length ===
                                                        0 ? (
                                                            <tr>
                                                                <td
                                                                    colSpan={6}
                                                                    className="p-8 text-center text-gray-500 dark:text-gray-400"
                                                                >
                                                                    Henüz soru
                                                                    yok.
                                                                </td>
                                                            </tr>
                                                        ) : (
                                                            productQuestions.map(
                                                                (item) => (
                                                                    <tr
                                                                        key={
                                                                            item.id
                                                                        }
                                                                        className="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted"
                                                                    >
                                                                        <td className="p-4 align-middle">
                                                                            <div className="text-sm font-medium dark:text-white">
                                                                                {item
                                                                                    .product
                                                                                    ?.name ||
                                                                                    'Bilinmeyen Ürün'}
                                                                            </div>
                                                                        </td>
                                                                        <td className="p-4 align-middle">
                                                                            <div className="flex flex-row items-center gap-2">
                                                                                <div className="relative z-2 flex h-8 w-8 shrink-0 items-center justify-center overflow-hidden rounded-full border-2 border-gray-200 bg-gray-50 text-sm dark:border-[#313131] dark:bg-[#171719]">
                                                                                    <span className="text-xs font-medium dark:text-white">
                                                                                        {getUserInitials(
                                                                                            item
                                                                                                .user
                                                                                                ?.name,
                                                                                            item
                                                                                                .user
                                                                                                ?.email,
                                                                                        )}
                                                                                    </span>
                                                                                </div>
                                                                                <div className="flex flex-col">
                                                                                    <div className="text-sm font-medium dark:text-white">
                                                                                        {item
                                                                                            .user
                                                                                            ?.name ||
                                                                                            item
                                                                                                .user
                                                                                                ?.email ||
                                                                                            'Bilinmeyen'}
                                                                                    </div>
                                                                                    <div className="text-xs text-gray-500 dark:text-gray-400">
                                                                                        {item
                                                                                            .user
                                                                                            ?.email ||
                                                                                            ''}
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                        <td className="max-w-md p-4 align-middle">
                                                                            <p className="line-clamp-2 text-sm dark:text-gray-300">
                                                                                {
                                                                                    item.question
                                                                                }
                                                                            </p>
                                                                        </td>
                                                                        <td className="max-w-md p-4 align-middle">
                                                                            {item.answer ? (
                                                                                <p className="line-clamp-2 text-sm text-green-600 dark:text-green-400">
                                                                                    {
                                                                                        item.answer
                                                                                    }
                                                                                </p>
                                                                            ) : (
                                                                                <span className="text-xs text-gray-400 italic">
                                                                                    Cevap
                                                                                    bekleniyor
                                                                                </span>
                                                                            )}
                                                                        </td>
                                                                        <td className="p-4 align-middle">
                                                                            <span className="text-sm text-gray-500 dark:text-gray-400">
                                                                                {formatDate(
                                                                                    item.created_at,
                                                                                )}
                                                                            </span>
                                                                        </td>
                                                                        <td className="p-4 align-middle">
                                                                            <button
                                                                                onClick={() =>
                                                                                    handleAnswerClick(
                                                                                        item,
                                                                                    )
                                                                                }
                                                                                className="inline-flex items-center gap-2 rounded-lg border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-medium text-blue-700 transition-colors hover:bg-blue-100 dark:border-blue-800 dark:bg-blue-950/20 dark:text-blue-300 dark:hover:bg-blue-900/40"
                                                                            >
                                                                                <MessageSquare className="h-3 w-3" />
                                                                                {item.answer
                                                                                    ? 'Cevabı Düzenle'
                                                                                    : 'Cevap Ver'}
                                                                            </button>
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
                </main>
            </div>

            <Dialog open={isDialogOpen} onOpenChange={setIsDialogOpen}>
                <DialogContent className="max-w-2xl">
                    <DialogHeader>
                        <DialogTitle>
                            {selectedQuestion?.answer
                                ? 'Cevabı Düzenle'
                                : 'Soruya Cevap Ver'}
                        </DialogTitle>
                        <DialogDescription>
                            {selectedQuestion?.product?.name && (
                                <div className="mt-2">
                                    <span className="text-sm font-medium">
                                        Ürün:{' '}
                                    </span>
                                    <span className="text-sm">
                                        {selectedQuestion.product.name}
                                    </span>
                                </div>
                            )}
                            <div className="mt-2">
                                <span className="text-sm font-medium">
                                    Soru:{' '}
                                </span>
                                <p className="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                    {selectedQuestion?.question}
                                </p>
                            </div>
                        </DialogDescription>
                    </DialogHeader>
                    <div className="space-y-4 py-4">
                        <div>
                            <label className="mb-2 block text-sm font-medium">
                                Cevabınız
                            </label>
                            <textarea
                                value={answer}
                                onChange={(e) => setAnswer(e.target.value)}
                                placeholder="Soruyu yanıtlayın..."
                                className="h-32 w-full rounded-xl border p-3 dark:border-white/10 dark:bg-[#171719]"
                                maxLength={2000}
                            />
                            <p className="mt-1 text-xs text-gray-500">
                                {answer.length}/2000 karakter
                            </p>
                        </div>
                    </div>
                    <DialogFooter>
                        <Button
                            variant="outline"
                            onClick={() => {
                                setIsDialogOpen(false);
                                setSelectedQuestion(null);
                                setAnswer('');
                            }}
                            disabled={isSubmitting}
                        >
                            İptal
                        </Button>
                        <Button
                            onClick={handleSubmitAnswer}
                            disabled={!answer.trim() || isSubmitting}
                        >
                            {isSubmitting ? 'Kaydediliyor...' : 'Cevabı Kaydet'}
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </AppLayout>
    );
}
