<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Assistant conversationnel') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($conversations->isEmpty())
                        <p class="text-gray-500 mb-4">Aucune conversation pour le moment.</p>
                    @else
                        <ul class="divide-y divide-gray-200">
                            @foreach ($conversations as $conv)
                                <li class="py-3">
                                    <a href="{{ route('chat.show', $conv) }}" class="block hover:bg-gray-50 px-4 py-2 rounded">
                                        <div class="font-medium">{{ $conv->title }}</div>
                                        <div class="text-sm text-gray-500">{{ $conv->updated_at->diffForHumans() }}</div>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    <div class="mt-6">
                        <form method="POST" action="{{ route('chat.store') }}" class="inline">
                            @csrf
                            <x-primary-button>{{ __('Nouvelle conversation') }}</x-primary-button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
