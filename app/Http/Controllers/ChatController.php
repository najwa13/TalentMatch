<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ChatController extends Controller
{
    public function index(): View
    {
        $conversations = Auth::user()
            ->conversations()
            ->latest()
            ->get();

        return view('chat.index', compact('conversations'));
    }

    public function store(Request $request): RedirectResponse
    {
        $conversation = Auth::user()->conversations()->create([
            'title' => $request->input('title', 'Nouvelle conversation'),
        ]);

        return redirect()->route('chat.show', $conversation);
    }

    public function show(Conversation $conversation): View
    {
        $this->authorize('view', $conversation);

        $conversation->load('messages');

        return view('chat.show', compact('conversation'));
    }

    public function message(Request $request, Conversation $conversation): RedirectResponse
    {
        $this->authorize('view', $conversation);

        $conversation->messages()->create([
            'role' => 'user',
            'content' => $request->input('content'),
        ]);

        return redirect()->route('chat.show', $conversation);
    }
}
