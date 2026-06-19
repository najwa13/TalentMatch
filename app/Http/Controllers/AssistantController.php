<?php

namespace App\Http\Controllers;

use App\Ai\Agents\AssistantAgent;
use Illuminate\Http\Request;

class AssistantController extends Controller
{
    public function ask(Request $request)
    {
        $request->validate([
            'message' => ['required', 'string', 'min:1'],
            'conversation_id' => ['nullable', 'string'],
        ]);

        $agent = new AssistantAgent;

        if ($conversationId = $request->input('conversation_id')) {
            $response = $agent
                ->continue($conversationId, as: $request->user())
                ->prompt($request->input('message'));
        } else {
            $response = $agent
                ->forUser($request->user())
                ->prompt($request->input('message'));
        }

        return response()->json([
            'response' => $response->text,
            'conversation_id' => $response->conversationId,
        ]);
    }
}
