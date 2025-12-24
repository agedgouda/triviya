<div class="bg-transparent p-0 m-0">
    <h3 class="font-semibold mb-2">Players</h3>

    <table class="min-w-full border-collapse">
        <thead>
            <tr>
                <th class="text-left p-2">Name</th>
                <th class="text-left p-2">Status</th>
                <th class="text-center p-2">Host</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($getRecord()->players as $player)
                @php
                    $bg = $loop->even ? '#000000' : '#CCCCCC';
                    $text = $loop->even ? '#CCCCCC' : '#000000';
                @endphp

                <tr
                    style="background-color: {{ $bg }}; color: {{ $text }}; cursor: pointer;"
                    onmouseover="this.style.backgroundColor='#E6E7F6'; this.style.color='#000000';"
                    onmouseout="this.style.backgroundColor='{{ $bg }}'; this.style.color='{{ $text }}';"
                    onclick="window.location.href='{{ url('/admin/users/' . $player->id) .'/edit'}}';"
                >
                    <td class="p-2">{{ $player->first_name }} {{ $player->last_name }}</td>
                    <td class="p-2">{{ $player->pivot->status }}</td>
                    <td class="p-2 text-right">
                        @if($player->pivot->is_host)
                        <div style="display: flex; justify-content: center; align-items: center; height: 100%;">
                            <x-heroicon-o-check style="width: 1.25rem; height: 1.25rem;" />
                        </div>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
