<?php

use App\Models\CrispConversation;
use App\Models\CrispMessage;

it('stores a crisp conversation and message from webhook payload', function () {
    $timestamp = now()->subMinute()->timestamp;

    $payload = [
        'event' => 'message:send',
        'website_id' => 'website_123',
        'data' => [
            'session_id' => 'session_abc',
            'from' => 'user',
            'content' => 'Merhaba',
            'type' => 'text',
            'timestamp' => $timestamp,
            'user' => [
                'nickname' => 'visitor493',
                'user_id' => 'crisp_user_1',
                'email' => 'visitor@example.com',
            ],
        ],
    ];

    $response = $this->postJson('/api/crisp/webhook', $payload);

    $response->assertSuccessful();

    expect(CrispConversation::count())->toBe(1);
    expect(CrispMessage::count())->toBe(1);

    $conversation = CrispConversation::firstOrFail();
    $message = CrispMessage::firstOrFail();

    expect($conversation->session_id)->toBe('session_abc')
        ->and($conversation->website_id)->toBe('website_123')
        ->and($conversation->nickname)->toBe('visitor493')
        ->and($conversation->crisp_user_id)->toBe('crisp_user_1')
        ->and($conversation->last_message_at?->timestamp)->toBe($timestamp);

    expect($message->conversation_id)->toBe($conversation->id)
        ->and($message->from)->toBe('user')
        ->and($message->content)->toBe('Merhaba')
        ->and($message->type)->toBe('text')
        ->and($message->timestamp?->timestamp)->toBe($timestamp);
});

it('ignores webhook payloads missing required identifiers', function () {
    $payload = [
        'event' => 'message:send',
        'website_id' => null,
        'data' => [
            'session_id' => null,
            'from' => 'user',
            'content' => 'Merhaba',
            'type' => 'text',
            'timestamp' => now()->timestamp,
        ],
    ];

    $response = $this->postJson('/api/crisp/webhook', $payload);

    $response->assertSuccessful();

    expect(CrispConversation::count())->toBe(0)
        ->and(CrispMessage::count())->toBe(0);
});
