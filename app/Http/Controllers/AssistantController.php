<?php

namespace App\Http\Controllers;

use App\Ai\Agents\AssistantAgent;
use App\Models\Analyse;
use Illuminate\Http\Request;

class AssistantController extends Controller
{
    public function ask(Request $request)
    {
        $request->validate([
            'message' => ['required', 'string', 'min:1', 'max:2000'],
            'conversation_id' => ['nullable', 'string'],
            'analysis_id' => ['nullable', 'integer', 'exists:analyses,id'],
        ]);

        $agent = new AssistantAgent;

        $promptText = $request->input('message');

        if ($analysisId = $request->input('analysis_id')) {
            $analyse = Analyse::with('candidat', 'offer')->find($analysisId);

            if ($analyse) {
                $promptText = sprintf(
                    "Contexte - Analyse du candidat %s pour l'offre %s :\nScore: %d/100\nRecommandation: %s\nJustification: %s\n\nQuestion: %s",
                    $analyse->candidat?->name ?? 'Inconnu',
                    $analyse->offer?->title ?? 'Inconnue',
                    $analyse->matching_score,
                    $analyse->recommendation?->value ?? 'N/A',
                    $analyse->justification ?? 'N/A',
                    $request->input('message'),
                );
            }
        }

        if ($conversationId = $request->input('conversation_id')) {
            try {
                $response = $agent
                    ->continue($conversationId, as: $request->user())
                    ->prompt($promptText);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Conversation introuvable.'], 404);
            }
        } else {
            $response = $agent
                ->forUser($request->user())
                ->prompt($promptText);
        }

        return response()->json([
            'response' => $response->text,
            'conversation_id' => $response->conversationId,
        ]);
    }
}
