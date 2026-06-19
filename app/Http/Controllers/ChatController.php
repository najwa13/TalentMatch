<?php

namespace App\Http\Controllers;

use App\Models\Analyse;
use App\Models\Conversation;
use Illuminate\Http\JsonResponse;
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
        $data = ['title' => $request->input('title', 'Nouvelle conversation')];

        if ($request->filled('analysis_id')) {
            $data['analysis_id'] = $request->input('analysis_id');
        }

        $conversation = Auth::user()->conversations()->create($data);

        return redirect()->route('chat.show', $conversation);
    }

    public function show(Conversation $conversation): View
    {
        $this->authorize('view', $conversation);

        $conversation->load(['messages' => fn ($q) => $q->latest()->limit(50)]);

        $analysis = $conversation->analysis_id
            ? Analyse::with('candidat', 'offer')->find($conversation->analysis_id)
            : null;

        return view('chat.show', compact('conversation', 'analysis'));
    }

    public function message(Request $request, Conversation $conversation): JsonResponse
    {
        $this->authorize('view', $conversation);

        $conversation->messages()->create([
            'role' => 'user',
            'content' => $request->input('content'),
        ]);

        return response()->json(['status' => 'ok']);
    }
}
