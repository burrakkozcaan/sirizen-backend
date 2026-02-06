<?php

use App\Models\CrispConversation;
use App\Models\CrispMessage;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

it('creates a conversation and sends a support message', function () {
    config([
        'services.crisp.website_id' => 'website_123',
        'services.crisp.identifier' => 'identifier',
        'services.crisp.key' => 'key',
    ]);

    Http::fake([
        'https://api.crisp.chat/*' => Http::response(),
    ]);

    $user = User::factory()->create();
    Vendor::factory()->for($user)->active()->create();

    $response = $this->actingAs($user)
        ->post(route('support.messages.store'), [
            'content' => 'Merhaba',
        ]);

    $response->assertRedirect();

    expect(CrispConversation::count())->toBe(1)
        ->and(CrispMessage::count())->toBe(1);

    $conversation = CrispConversation::firstOrFail();
    $message = CrispMessage::firstOrFail();

    expect($conversation->user_id)->toBe($user->id)
        ->and($conversation->website_id)->toBe('website_123')
        ->and($message->content)->toBe('Merhaba')
        ->and($message->conversation_id)->toBe($conversation->id);

    Http::assertSent(function (Request $request) use ($conversation) {
        return $request->url() === "https://api.crisp.chat/v1/website/website_123/conversation/{$conversation->session_id}/message"
            && $request['content'] === 'Merhaba'
            && $request['from'] === 'user';
    });
});

it('requires content when sending a support message', function () {
    config([
        'services.crisp.website_id' => 'website_123',
        'services.crisp.identifier' => 'identifier',
        'services.crisp.key' => 'key',
    ]);

    $user = User::factory()->create();
    Vendor::factory()->for($user)->active()->create();

    $this->actingAs($user)
        ->post(route('support.messages.store'), [
            'content' => '',
        ])
        ->assertSessionHasErrors('content');
});
