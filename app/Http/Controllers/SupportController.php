<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSupportMessageRequest;
use App\Models\CrispMessage;
use App\Models\CrispConversation;
use App\Services\CrispClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class SupportController extends Controller
{
    public function index(Request $request): Response
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('vendor.application.pending');
        }

        // Vendor'ın Crisp conversations'larını getir (user_id ile eşleştirilmiş olanlar)
        $conversations = CrispConversation::query()
            ->with(['messages' => function ($query) {
                $query->latest('timestamp')->limit(1);
            }])
            ->where('user_id', $vendor->user_id)
            ->orderBy('last_message_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($conversation) {
                $lastMessage = $conversation->messages->first();
                return [
                    'id' => (string) $conversation->id,
                    'session_id' => $conversation->session_id,
                    'subject' => $conversation->nickname ? "Konuşma: {$conversation->nickname}" : 'Yeni Konuşma',
                    'status' => $conversation->status,
                    'last_message_at' => $conversation->last_message_at?->toIso8601String() ?? '',
                    'created_at' => $conversation->created_at?->toIso8601String() ?? '',
                    'last_message' => $lastMessage ? [
                        'content' => substr($lastMessage->content, 0, 100),
                        'from' => $lastMessage->from,
                    ] : null,
                ];
            })
            ->values()
            ->all();

        $activeConversationId = $request->query('conversation');
        $activeConversationId = $activeConversationId ? (int) $activeConversationId : null;

        $activeConversation = $activeConversationId
            ? CrispConversation::where('user_id', $vendor->user_id)->find($activeConversationId)
            : null;

        if (! $activeConversation) {
            $firstConversationId = $conversations[0]['id'] ?? null;
            $activeConversation = $firstConversationId
                ? CrispConversation::where('user_id', $vendor->user_id)->find((int) $firstConversationId)
                : null;
        }

        $messages = [];
        if ($activeConversation) {
            $messages = CrispMessage::query()
                ->where('conversation_id', $activeConversation->id)
                ->orderBy('timestamp')
                ->get()
                ->map(function (CrispMessage $message) {
                    return [
                        'id' => (string) $message->id,
                        'from' => $message->from,
                        'content' => $message->content,
                        'type' => $message->type,
                        'timestamp' => $message->timestamp?->toIso8601String() ?? '',
                    ];
                })
                ->values()
                ->all();
        }

        return Inertia::render('support/index', [
            'conversations' => $conversations,
            'activeConversationId' => $activeConversation?->id ? (string) $activeConversation->id : null,
            'messages' => $messages,
            'vendor' => [
                'id' => (string) $vendor->id,
                'name' => $vendor->name,
                'email' => $vendor->user->email ?? null,
            ],
        ]);
    }

    public function update(Request $request, string $id)
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('vendor.application.pending');
        }

        $conversation = CrispConversation::findOrFail($id);

        // Vendor kontrolü
        if ($conversation->user_id !== $vendor->user_id) {
            abort(403, 'Bu konuşmaya erişim yetkiniz yok.');
        }

        $validated = $request->validate([
            'status' => 'required|string|in:open,closed',
        ]);

        $conversation->update([
            'status' => $validated['status'],
        ]);

        return redirect()->route('support.index')
            ->with('success', 'Konuşma durumu güncellendi.');
    }

    public function storeMessage(StoreSupportMessageRequest $request, CrispClient $crisp): \Illuminate\Http\RedirectResponse
    {
        $vendor = Auth::user()->vendor;

        if (! $vendor) {
            return redirect()->route('vendor.application.pending');
        }

        $conversationId = $request->input('conversation_id');

        if ($conversationId) {
            $conversation = CrispConversation::where('user_id', $vendor->user_id)->findOrFail($conversationId);
        } else {
            $websiteId = config('services.crisp.website_id');

            if (! $websiteId) {
                return redirect()->route('support.index')
                    ->withErrors(['content' => 'Crisp yapılandırması eksik.']);
            }

            try {
                $sessionId = $crisp->createConversation($websiteId, [
                    'nickname' => $vendor->name,
                    'email' => $vendor->user->email ?? null,
                ]);
            } catch (\Throwable $exception) {
                Log::error('Crisp conversation create failed', [
                    'vendor_id' => $vendor->id,
                    'error' => $exception->getMessage(),
                ]);

                return redirect()->route('support.index')
                    ->withErrors(['content' => 'Sohbet başlatılamadı. Lütfen tekrar deneyin.']);
            }

            $conversation = CrispConversation::create([
                'session_id' => $sessionId,
                'website_id' => $websiteId,
                'nickname' => $vendor->name,
                'user_id' => $vendor->user_id,
                'status' => 'open',
            ]);
        }

        $content = $request->string('content')->trim()->toString();

        try {
            $crisp->sendMessage($conversation, $content, 'user');
            $deliveryFailed = false;
        } catch (\Throwable $exception) {
            Log::error('Crisp message send failed', [
                'conversation_id' => $conversation->id,
                'error' => $exception->getMessage(),
            ]);
            $deliveryFailed = true;
        }

        $message = CrispMessage::create([
            'conversation_id' => $conversation->id,
            'from' => 'user',
            'content' => $content,
            'type' => 'text',
            'timestamp' => now(),
            'metadata' => $deliveryFailed ? ['delivery_status' => 'failed'] : null,
        ]);

        $conversation->update([
            'last_message_at' => $message->timestamp,
        ]);

        if ($deliveryFailed) {
            return redirect()->route('support.index', ['conversation' => $conversation->id])
                ->withErrors(['content' => 'Mesaj gönderilemedi. Lütfen tekrar deneyin.']);
        }

        return redirect()->route('support.index', ['conversation' => $conversation->id])
            ->with('success', 'Mesaj gönderildi.');
    }
}
