<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOfferRequest;
use App\Http\Requests\UpdateOfferRequest;
use App\Models\Offer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OfferController extends Controller
{
    public function index(): View
    {
        $offres = Auth::user()
            ->offers()
            ->withCount('analyses')
            ->latest()
            ->paginate(10);

        return view('offres.index', compact('offres'));
    }

    public function create(): View
    {
        return view('offres.create');
    }

    public function store(StoreOfferRequest $request): RedirectResponse
    {
        $offre = Auth::user()->offers()->create($request->validated());

        return redirect()->route('offres.show', $offre)
            ->with('success', 'Offre créée avec succès.');
    }

    public function show(Offer $offre): View
    {
        $this->authorize('view', $offre);

        $offre->load(['analyses.candidat' => function ($query) {
            $query->orderByDesc('matching_score');
        }]);

        return view('offres.show', compact('offre'));
    }

    public function edit(Offer $offre): View
    {
        $this->authorize('update', $offre);

        return view('offres.edit', compact('offre'));
    }

    public function update(UpdateOfferRequest $request, Offer $offre): RedirectResponse
    {
        $this->authorize('update', $offre);

        $offre->update($request->validated());

        return redirect()->route('offres.show', $offre)
            ->with('success', 'Offre mise à jour avec succès.');
    }

    public function destroy(Offer $offre): RedirectResponse
    {
        $this->authorize('delete', $offre);

        $offre->delete();

        return redirect()->route('offres.index')
            ->with('success', 'Offre supprimée avec succès.');
    }
}
