<div class="relative border border-gray-300 dark:border-gray-700 rounded-xl p-4 pt-2">
    {{-- Label styled like a top-embedded legend, white text --}}
    <div class="absolute -top-3 px-1 text-sm font-semibold text-gray-900 dark:text-white bg-white dark:bg-gray-950">
        Games
    </div>

    {{-- Column headers --}}
    <div class="flex items-center justify-between text-sm font-medium text-gray-800 dark:text-gray-100 my-2">
        <div>Name</div>
        <div>Host</div>
    </div>

    {{-- Game rows --}}
    @foreach ($getRecord()->games as $game)
        <div
            class="flex items-center justify-between text-sm
                {{ $loop->even ? 'bg-gray-100 dark:bg-gray-800 text-white dark:text-gray-400' : '' }}

                m-0 p-0"
        >
            <div>
                {{ $game->name }}
            </div>
            <div>
                <input
                    type="checkbox"
                    disabled
                    @checked($game->pivot->is_host)
                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                >
            </div>
        </div>
    @endforeach
</div>
