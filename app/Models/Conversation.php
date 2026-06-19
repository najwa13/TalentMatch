<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Laravel\Ai\Models\Conversation as AiConversation;

class Conversation extends AiConversation
{
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $conversation) {
            $conversation->{$conversation->getKeyName()} = (string) Str::uuid();
        });
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ConversationMessage::class, 'conversation_id');
    }
}
