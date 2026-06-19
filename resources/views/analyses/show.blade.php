<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Analyse de') }} {{ $analyse->candidat->name }}
            </h2>
            <a href="{{ route('offres.show', $analyse->offer) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                {{ __('Retour à l\'offre') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('Score de correspondance') }}</h3>
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold
                            {{ $analyse->matching_score >= 70 ? 'bg-green-100 text-green-800' : ($analyse->matching_score >= 40 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                            {{ $analyse->matching_score }}/100
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div>
                            <p class="text-sm font-medium text-gray-500">{{ __('Expérience') }}</p>
                            <p class="text-sm text-gray-900">{{ $analyse->years_experience }} an(s)</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">{{ __('Niveau d\'éducation') }}</p>
                            <p class="text-sm text-gray-900">{{ $analyse->education_level ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">{{ __('Langues') }}</p>
                            <div class="flex flex-wrap gap-1 mt-1">
                                @foreach ($analyse->languages ?? [] as $lang)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">{{ $lang }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <p class="text-sm font-medium text-gray-500">{{ __('Compétences extraites') }}</p>
                        <div class="flex flex-wrap gap-2 mt-2">
                            @foreach ($analyse->extracted_skills ?? [] as $skill)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">{{ $skill }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-sm font-medium text-gray-500 mb-3">{{ __('Points forts') }}</h3>
                        <ul class="space-y-2">
                            @forelse ($analyse->strengths ?? [] as $strength)
                                <li class="flex items-start">
                                    <span class="text-green-500 mr-2">&#10003;</span>
                                    <span class="text-sm text-gray-700">{{ $strength }}</span>
                                </li>
                            @empty
                                <li class="text-sm text-gray-400">{{ __('Aucun point fort identifié') }}</li>
                            @endforelse
                        </ul>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-sm font-medium text-gray-500 mb-3">{{ __('Lacunes') }}</h3>
                        <ul class="space-y-2">
                            @forelse ($analyse->weaknesses ?? [] as $weakness)
                                <li class="flex items-start">
                                    <span class="text-red-500 mr-2">&#10007;</span>
                                    <span class="text-sm text-gray-700">{{ $weakness }}</span>
                                </li>
                            @empty
                                <li class="text-sm text-gray-400">{{ __('Aucune lacune identifiée') }}</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

            @if (!empty($analyse->missing_skills))
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-sm font-medium text-gray-500 mb-3">{{ __('Compétences manquantes') }}</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($analyse->missing_skills as $skill)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">{{ $skill }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-sm font-medium text-gray-500 mb-3">{{ __('Recommandation') }}</h3>
                    @php
                        $badgeClass = match($analyse->recommendation) {
                            'convoquer' => 'bg-green-100 text-green-800',
                            'attente' => 'bg-yellow-100 text-yellow-800',
                            'rejeter' => 'bg-red-100 text-red-800',
                            default => 'bg-gray-100 text-gray-800',
                        };
                    @endphp
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $badgeClass }}">
                        {{ ucfirst($analyse->recommendation) }}
                    </span>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-sm font-medium text-gray-500 mb-3">{{ __('Justification') }}</h3>
                    <p class="text-sm text-gray-700">{{ $analyse->justification }}</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
