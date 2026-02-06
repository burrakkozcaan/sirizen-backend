<?php

use App\Models\CrispConversation;
use App\Models\CrispMessage;
use App\Models\User;
use App\Models\Vendor;
use Inertia\Testing\AssertableInertia as Assert;

it('renders support page with active conversation and messages', function () {
    $user = User::factory()->create();
    Vendor::factory()
        ->for($user)
        ->active()
        ->create();

    $conversation = CrispConversation::create([
        'session_id' => 'session_123',
        'website_id' => 'website_123',
        'nickname' => 'visitor493',
        'user_id' => $user->id,
        'status' => 'open',
        'last_message_at' => now(),
    ]);

    CrispMessage::create([
        'conversation_id' => $conversation->id,
        'from' => 'user',
        'content' => 'Merhaba',
        'type' => 'text',
        'timestamp' => now(),
    ]);

    $this->actingAs($user)
        ->get(route('support.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('support/index')
            ->where('activeConversationId', (string) $conversation->id)
            ->has('conversations', 1)
            ->has('messages', 1)
        );
});
