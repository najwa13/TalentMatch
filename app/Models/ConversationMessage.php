<?php

namespace App\Models;

use Illuminate\Support\Str;
use Laravel\Ai\Models\ConversationMessage as AiConversationMessage;

class ConversationMessage extends AiConversationMessage
{
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $message) {
            $message->{$message->getKeyName()} = (string) Str::uuid();
        });
    }
}
