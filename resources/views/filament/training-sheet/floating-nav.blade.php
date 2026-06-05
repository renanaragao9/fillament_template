<div
    x-data="{ open: false }"
    class="fixed bottom-6 right-6 z-50 flex flex-col items-end gap-2"
>
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-2"
        class="flex flex-col items-end gap-1 mb-1"
    >
        @foreach([
            ['id' => 'section-dados-da-ficha',       'label' => 'Dados da Ficha'],
            ['id' => 'section-divisoes-exercicios',  'label' => 'Divisões e Exercícios'],
            ['id' => 'section-historico-treinos',    'label' => 'Histórico de Treinos'],
            ['id' => 'section-auditoria',            'label' => 'Auditoria'],
        ] as $item)
            <button
                type="button"
                onclick="
                    const el = document.getElementById('{{ $item['id'] }}');
                    if (el) { el.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
                "
                class="rounded-lg bg-white dark:bg-gray-800 shadow-lg border border-gray-200 dark:border-gray-700 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-primary-50 dark:hover:bg-primary-900 hover:text-primary-600 dark:hover:text-primary-400 transition-colors whitespace-nowrap"
            >
                {{ $item['label'] }}
            </button>
        @endforeach
    </div>

    <button
        type="button"
        @click="open = !open"
        class="flex items-center justify-center w-12 h-12 rounded-full bg-primary-600 hover:bg-primary-700 text-white shadow-lg transition-colors"
        title="Navegação rápida"
    >
        <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
        <svg x-show="open" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>
</div>
