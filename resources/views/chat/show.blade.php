<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <a href="{{ route('chat.index') }}" class="text-indigo-600 hover:text-indigo-900">&larr;</a>
            {{ $conversation->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if ($analysis)
                <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-indigo-800">{{ __('Contexte') }}</p>
                            <p class="text-sm text-indigo-600">
                                {{ __('Analyse de') }}
                                <strong>{{ $analysis->candidat?->name ?? 'N/A' }}</strong>
                                {{ __('pour') }}
                                <strong>{{ $analysis->offer?->title ?? 'N/A' }}</strong>
                                &mdash; {{ $analysis->matching_score }}/100
                            </p>
                        </div>
                        <a href="{{ route('offres.show', $analysis->offer) }}" class="text-xs text-indigo-600 hover:text-indigo-800 underline">
                            {{ __('Voir l\'offre') }}
                        </a>
                    </div>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div id="messages" class="space-y-4 mb-6 max-h-96 overflow-y-auto">
                        @forelse ($conversation->messages->sortBy('created_at') as $message)
                            <div class="flex {{ $message->role === 'user' ? 'justify-end' : 'justify-start' }}">
                                <div class="max-w-lg px-4 py-2 rounded-lg {{ $message->role === 'user' ? 'bg-indigo-100 text-gray-900' : 'bg-gray-100 text-gray-900' }}">
                                    <div class="text-xs text-gray-500 mb-1">{{ $message->role === 'user' ? 'Vous' : 'Assistant' }}</div>
                                    <div class="whitespace-pre-wrap">{{ $message->content }}</div>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center py-4">{{ __('Aucun message pour le moment.') }}</p>
                        @endforelse
                    </div>

                    <form id="messageForm" method="POST" action="{{ route('chat.message', $conversation) }}" class="flex gap-2">
                        @csrf
                        <input type="text" name="content" id="messageInput" placeholder="Posez votre question..." class="flex-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <x-primary-button type="submit">{{ __('Envoyer') }}</x-primary-button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.getElementById('messageForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const input = document.getElementById('messageInput');
            const content = input.value.trim();
            if (!content) return;

            const form = this;
            const submitBtn = form.querySelector('button[type="submit"]');
            submitBtn.disabled = true;

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: new URLSearchParams({ content: content })
            })
            .then(response => response.json())
            .then(data => {
                input.value = '';
                location.reload();
            })
            .catch(() => {
                submitBtn.disabled = false;
            });
        });
    </script>
    @endpush
</x-app-layout>
