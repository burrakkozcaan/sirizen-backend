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
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/react';
import { MessageCircle, Send } from 'lucide-react';
import { type FormEvent, useMemo, useState } from 'react';
import { toast } from 'sonner';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Canlı Destek',
        href: '/support',
    },
];

interface Conversation {
    id: string;
    session_id: string;
    subject: string;
    status: string;
    last_message_at: string | null;
    created_at: string;
    last_message?: {
        content: string;
        from: string;
    } | null;
}

interface Message {
    id: string;
    from: string;
    content: string;
    type: string;
    timestamp: string;
}

interface Vendor {
    id: string;
    name: string;
    email: string | null;
}

interface Props {
    conversations?: Conversation[];
    activeConversationId?: string | null;
    messages?: Message[];
    vendor?: Vendor;
}

export default function Support({
    conversations = [],
    activeConversationId,
    messages = [],
}: Props) {
    const [isEditDialogOpen, setIsEditDialogOpen] = useState(false);
    const [selectedConversation, setSelectedConversation] = useState<Conversation | null>(null);
    const [status, setStatus] = useState('');
    const [message, setMessage] = useState('');
    const [isSending, setIsSending] = useState(false);

    const activeConversation = useMemo(() => {
        if (!activeConversationId) {
            return null;
        }

        return conversations.find((conversation) => conversation.id === activeConversationId) ?? null;
    }, [activeConversationId, conversations]);

    const formatDate = (dateString: string | null) => {
        if (!dateString) {
            return '-';
        }

        return new Intl.DateTimeFormat('tr-TR', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
        }).format(new Date(dateString));
    };

    const formatTime = (dateString: string | null) => {
        if (!dateString) {
            return '';
        }

        return new Intl.DateTimeFormat('tr-TR', {
            hour: '2-digit',
            minute: '2-digit',
        }).format(new Date(dateString));
    };

    const handleSelectConversation = (conversationId: string) => {
        router.get(
            '/support',
            { conversation: conversationId },
            { preserveScroll: true },
        );
    };

    const handleEdit = (conversation: Conversation) => {
        setSelectedConversation(conversation);
        setStatus(conversation.status);
        setIsEditDialogOpen(true);
    };

    const handleUpdate = () => {
        if (!selectedConversation) {
            return;
        }

        router.put(
            `/support/${selectedConversation.id}`,
            {
                status,
            },
            {
                onSuccess: () => {
                    setIsEditDialogOpen(false);
                    setSelectedConversation(null);
                    toast.success('Konuşma durumu güncellendi.');
                },
                onError: (errors) => {
                    toast.error('Durum güncellenirken bir hata oluştu.', {
                        description: Object.values(errors).join(', '),
                    });
                },
            },
        );
    };

    const handleSendMessage = (event: FormEvent<HTMLFormElement>) => {
        event.preventDefault();

        if (!message.trim()) {
            toast.error('Mesaj boş olamaz.');
            return;
        }

        setIsSending(true);

        router.post(
            '/support/messages',
            {
                content: message.trim(),
                conversation_id: activeConversationId ?? null,
            },
            {
                preserveScroll: true,
                onSuccess: () => {
                    setMessage('');
                },
                onError: (errors) => {
                    toast.error('Mesaj gönderilemedi.', {
                        description: Object.values(errors).join(', '),
                    });
                },
                onFinish: () => {
                    setIsSending(false);
                },
            },
        );
    };

    const getStatusColor = (statusValue: string) => {
        switch (statusValue) {
            case 'open':
                return 'bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300';
            case 'closed':
                return 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300';
            default:
                return 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300';
        }
    };

    const getStatusText = (statusValue: string) => {
        switch (statusValue) {
            case 'open':
                return 'Açık';
            case 'closed':
                return 'Kapalı';
            default:
                return statusValue;
        }
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Canlı Destek" />
            <div className="relative flex h-full w-full flex-col gap-y-4">
                <main className="relative flex min-h-0 w-full grow flex-col">
                    <div className="flex h-full w-full flex-row gap-x-2 p-5">
                        <div className="relative flex w-full flex-col items-center rounded-2xl border-gray-200 px-4 md:overflow-y-auto md:border md:bg-white md:px-8 md:shadow-xs dark:border-[#313131] dark:md:bg-[#181818]">
                            <div className="flex h-full w-full max-w-[--breakpoint-xl] flex-col">
                                <div className="flex w-full flex-col gap-y-4 py-8 md:flex-row md:items-center md:justify-between md:py-8">
                                    <div className="flex flex-col gap-2">
                                        <h4 className="text-2xl font-medium whitespace-nowrap dark:text-white">
                                            Canlı Destek
                                        </h4>
                                        <p className="text-sm text-gray-500 dark:text-[#999999]">
                                            Sirizen ekibiyle yaptığınız görüşmeleri buradan yönetin.
                                        </p>
                                    </div>
                                </div>

                                <div className="grid w-full gap-4 pb-8 lg:grid-cols-[320px_1fr]">
                                    <div className="flex h-full flex-col gap-4 rounded-2xl border border-gray-200 bg-white p-4 shadow-xs dark:border-[#313131] dark:bg-[#171719]">
                                        <div className="flex items-center justify-between">
                                            <span className="text-sm font-semibold text-gray-700 dark:text-gray-200">
                                                Konuşmalar
                                            </span>
                                            <span className="text-xs text-gray-400">
                                                {conversations.length}
                                            </span>
                                        </div>

                                        <div className="flex flex-col gap-2">
                                            {conversations.length === 0 ? (
                                                <div className="rounded-xl border border-dashed border-gray-200 p-4 text-sm text-gray-500 dark:border-[#2b2b2b] dark:text-gray-400">
                                                    Henüz konuşma yok. İlk mesajınızı göndererek sohbet başlatabilirsiniz.
                                                </div>
                                            ) : (
                                                conversations.map((conversation) => {
                                                    const isActive = conversation.id === activeConversationId;

                                                    return (
                                                        <button
                                                            key={conversation.id}
                                                            type="button"
                                                            onClick={() => handleSelectConversation(conversation.id)}
                                                            className={`flex flex-col gap-2 rounded-xl border p-3 text-left transition ${
                                                                isActive
                                                                    ? 'border-blue-200 bg-blue-50 dark:border-blue-900/60 dark:bg-blue-900/20'
                                                                    : 'border-transparent bg-gray-50 hover:border-gray-200 dark:bg-[#1c1c1f] dark:hover:border-[#2b2b2b]'
                                                            }`}
                                                        >
                                                            <div className="flex items-center justify-between gap-2">
                                                                <span className="text-sm font-semibold text-gray-800 dark:text-gray-100">
                                                                    {conversation.subject}
                                                                </span>
                                                                <span className={`text-[11px] px-2 py-0.5 rounded-full ${getStatusColor(conversation.status)}`}>
                                                                    {getStatusText(conversation.status)}
                                                                </span>
                                                            </div>
                                                            <div className="text-xs text-gray-500 dark:text-gray-400">
                                                                {conversation.last_message
                                                                    ? `${conversation.last_message.from === 'user' ? 'Siz' : 'Destek'}: ${conversation.last_message.content}`
                                                                    : 'Henüz mesaj yok'}
                                                            </div>
                                                            <div className="text-[11px] text-gray-400">
                                                                {formatDate(conversation.last_message_at || conversation.created_at)}
                                                            </div>
                                                        </button>
                                                    );
                                                })
                                            )}
                                        </div>
                                    </div>

                                    <div className="flex h-full min-h-[420px] flex-col justify-between rounded-2xl border border-gray-200 bg-white shadow-xs dark:border-[#313131] dark:bg-[#171719]">
                                        <div className="flex items-center justify-between border-b border-gray-100 px-4 py-3 dark:border-[#2b2b2b]">
                                            <div className="flex items-center gap-3">
                                                <div className="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900/40 dark:text-blue-200">
                                                    <MessageCircle className="h-5 w-5" />
                                                </div>
                                                <div>
                                                    <div className="text-sm font-semibold text-gray-800 dark:text-gray-100">
                                                        {activeConversation?.subject ?? 'Yeni Konuşma'}
                                                    </div>
                                                    <div className="text-xs text-gray-500 dark:text-gray-400">
                                                        {activeConversation ? formatDate(activeConversation.created_at) : 'Sohbet başlatın'}
                                                    </div>
                                                </div>
                                            </div>
                                            {activeConversation && (
                                                <Button
                                                    variant="outline"
                                                    size="sm"
                                                    onClick={() => handleEdit(activeConversation)}
                                                >
                                                    Durumu Güncelle
                                                </Button>
                                            )}
                                        </div>

                                        <div className="flex min-h-0 flex-1 flex-col gap-4 overflow-y-auto px-4 py-6">
                                            {messages.length === 0 ? (
                                                <div className="flex h-full flex-col items-center justify-center gap-3 text-center text-sm text-gray-500 dark:text-gray-400">
                                                    <p>Henüz mesaj yok.</p>
                                                    <p>İlk mesajınızı göndererek destek ekibiyle konuşmaya başlayabilirsiniz.</p>
                                                </div>
                                            ) : (
                                                messages.map((item) => {
                                                    const isUser = item.from === 'user';

                                                    return (
                                                        <div
                                                            key={item.id}
                                                            className={`flex flex-col gap-2 ${isUser ? 'items-end' : 'items-start'}`}
                                                        >
                                                            <div
                                                                className={`max-w-[70%] rounded-2xl px-4 py-3 text-sm shadow-sm ${
                                                                    isUser
                                                                        ? 'bg-blue-600 text-white'
                                                                        : 'bg-gray-100 text-gray-800 dark:bg-[#222225] dark:text-gray-100'
                                                                }`}
                                                            >
                                                                <p className="whitespace-pre-wrap">
                                                                    {item.type === 'text' ? item.content : 'Dosya paylaşıldı.'}
                                                                </p>
                                                            </div>
                                                            <span className="text-[11px] text-gray-400">
                                                                {isUser ? 'Siz' : 'Destek'} · {formatTime(item.timestamp)}
                                                            </span>
                                                        </div>
                                                    );
                                                })
                                            )}
                                        </div>

                                        <form
                                            onSubmit={handleSendMessage}
                                            className="flex flex-col gap-3 border-t border-gray-100 px-4 py-4 dark:border-[#2b2b2b]"
                                        >
                                            <Textarea
                                                value={message}
                                                onChange={(event) => setMessage(event.target.value)}
                                                placeholder="Mesajınızı yazın..."
                                                className="min-h-[96px]"
                                            />
                                            <div className="flex items-center justify-end">
                                                <Button type="submit" disabled={isSending} className="gap-2">
                                                    <Send className="h-4 w-4" />
                                                    {isSending ? 'Gönderiliyor...' : 'Gönder'}
                                                </Button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>

            <Dialog open={isEditDialogOpen} onOpenChange={setIsEditDialogOpen}>
                <DialogContent className="sm:max-w-lg">
                    <DialogHeader>
                        <DialogTitle>Konuşma Durumu Güncelle</DialogTitle>
                        <DialogDescription>
                            Konuşma durumunu güncelleyin.
                        </DialogDescription>
                    </DialogHeader>
                    {selectedConversation && (
                        <div className="space-y-4 py-4">
                            <div>
                                <Label className="mb-2 block">Konu</Label>
                                <p className="text-sm font-medium dark:text-white">
                                    {selectedConversation.subject}
                                </p>
                            </div>
                            <div>
                                <Label className="mb-2 block">Session ID</Label>
                                <p className="text-sm font-mono text-xs dark:text-white">
                                    {selectedConversation.session_id}
                                </p>
                            </div>
                            <div>
                                <Label className="mb-2 block">Durum</Label>
                                <select
                                    value={status}
                                    onChange={(e) => setStatus(e.target.value)}
                                    className="w-full rounded-lg border p-2 text-sm dark:border-white/10 dark:bg-[#171719]"
                                >
                                    <option value="open">Açık</option>
                                    <option value="closed">Kapalı</option>
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
