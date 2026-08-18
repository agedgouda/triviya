<div class="relative border border-gray-300 dark:border-gray-700 rounded-xl p-4 pt-2">
@if($getRecord())
        {{-- Label styled like a top-embedded legend, white text --}}
        <div class="absolute -top-3 px-1 text-sm font-semibold text-gray-900 dark:text-white bg-white dark:bg-gray-950">
            Game Products Purchased
        </div>

        {{-- Column headers --}}
        <div class="flex items-center justify-between text-sm font-medium text-gray-800 dark:text-gray-100 my-2">
            <div>Product</div>
            <div>Games</div>
            <div>Purchased</div>
        </div>

        {{-- Purchase rows --}}
        @forelse ($getRecord()->gameProductPurchases()->with('gameProduct')->latest()->get() as $purchase)
            <div
                class="flex items-center justify-between text-sm
                    {{ $loop->even ? 'bg-gray-100 dark:bg-yellow-800 text-white dark:text-gray-400' : '' }}

                    m-0 p-0"
            >
                <div>
                    {{ $purchase->gameProduct->name }}
                </div>
                <div>
                    {{ $purchase->gameProduct->game_count }}
                </div>
                <div>
                    {{ $purchase->created_at->format('M j, Y') }}
                </div>
            </div>
        @empty
            <div class="text-sm text-gray-500 dark:text-gray-400">No purchases yet.</div>
        @endforelse
    @endif
</div>
