<?php

use App\Models\CrispConversation;
use App\Models\CrispMessage;

it('marks conversation as unread for admin when last message is from user', function () {
    $timestamp = now()->subMinute();

    $conversation = CrispConversation::create([
        'session_id' => 'session_1',
        'website_id' => 'website_1',
        'status' => 'open',
        'last_message_at' => $timestamp,
    ]);

    CrispMessage::create([
        'conversation_id' => $conversation->id,
        'from' => 'user',
        'content' => 'Merhaba',
        'type' => 'text',
        'timestamp' => $timestamp,
    ]);

    $conversation->load('latestMessage');

    expect($conversation->hasUnreadForAdmin())->toBeTrue();
});

it('does not mark conversation as unread after admin has seen it', function () {
    $timestamp = now()->subMinute();

    $conversation = CrispConversation::create([
        'session_id' => 'session_2',
        'website_id' => 'website_1',
        'status' => 'open',
        'last_message_at' => $timestamp,
        'admin_last_seen_at' => now(),
    ]);

    CrispMessage::create([
        'conversation_id' => $conversation->id,
        'from' => 'user',
        'content' => 'Merhaba',
        'type' => 'text',
        'timestamp' => $timestamp,
    ]);

    $conversation->load('latestMessage');

    expect($conversation->hasUnreadForAdmin())->toBeFalse();
});

it('does not mark conversation as unread when last message is from operator', function () {
    $timestamp = now()->subMinute();

    $conversation = CrispConversation::create([
        'session_id' => 'session_3',
        'website_id' => 'website_1',
        'status' => 'open',
        'last_message_at' => $timestamp,
    ]);

    CrispMessage::create([
        'conversation_id' => $conversation->id,
        'from' => 'operator',
        'content' => 'Merhaba',
        'type' => 'text',
        'timestamp' => $timestamp,
    ]);

    $conversation->load('latestMessage');

    expect($conversation->hasUnreadForAdmin())->toBeFalse();
});
