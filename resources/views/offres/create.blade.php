<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Créer une offre') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('offres.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <x-input-label for="title" :value="__('Titre')" />
                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title')" required autofocus />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" rows="5" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="required_skills" :value="__('Compétences (séparées par des virgules)')" />
                            <x-text-input id="required_skills" name="required_skills" type="text" class="mt-1 block w-full" :value="old('required_skills')" placeholder="PHP, Laravel, MySQL, Git" required />
                            <x-input-error :messages="$errors->get('required_skills')" class="mt-2" />
                            <p class="mt-1 text-sm text-gray-500">{{ __("Entrez les compétences séparées par des virgules.") }}</p>
                        </div>

                        <div class="mb-4">
                            <x-input-label for="minimum_experience" :value="__('Expérience minimale (années)')" />
                            <x-text-input id="minimum_experience" name="minimum_experience" type="number" class="mt-1 block w-full" :value="old('minimum_experience', 0)" min="0" max="50" required />
                            <x-input-error :messages="$errors->get('minimum_experience')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('offres.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150 mr-3">
                                {{ __('Annuler') }}
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Créer l\'offre') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
