<x-layouts.error :title="'401 - Acão Restrita'">
    <div class="text-center max-w-md mx-auto px-7 py-16">
        {{-- Code --}}
        <h1 class="display text-center mb-2" style="color: var(--color-red)">
            401
        </h1>

        {{-- Heading --}}
        <h2 class="h1 text-center mb-3 text-ink">Acao restringida.</h2>

        {{-- Subtext --}}
        <p class="body text-center text-ink-2 mb-8">
            @if(auth()->check())
                Voce ja esta logado, mas precisa de mais permissoes para acessar esta pagina.
            @else
                Voce precisa estar autenticado para acessar a pagina solicitada.
            @endif
        </p>

        {{-- Actions --}}
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
            @if(auth()->check())
                <a href="{{ route('dashboard') }}" class="btn btn-primary w-full sm:w-auto">
                    Ir para o painel
                </a>
            @else
                <a href="{{ route('login') }}" class="btn btn-primary w-full sm:w-auto">
                    Fazer login
                </a>
            @endif
            <a href="{{ route('home') }}" class="btn btn-ghost w-full sm:w-auto">
                Voltar à home
            </a>
        </div>
    </div>
</x-layouts.error>
