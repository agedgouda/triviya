<div>

    <p style="text-align: center;">
       <img src="{{config('app.url')}}/images/trivius-logo.png" width="200px" />
    </p>

    <p>Hi {{ $host->first_name }},</p>

    <p>

    </p>
    <p>
        Good news — {{ $player->first_name }} {{ $player->last_name }} has completed their Trivius quiz! 🎉<br>
        Here are a few things to keep in mind before game time:

        <p>
            <ul>
                <li>✅Players can update their answers anytime — right up until you press Start Game.</li>
                <li>➕ You can add or remove players at any time before the game begins.</li>
                <li>🔐 All answers will be securely stored for 48 hours after the game wraps — then they’re gone!</li>
            </ul>
        </p>
        <p>
            You’re one step closer to game night greatness.
        </p>
        <p>
            Need to check who’s in or make changes?
             👉 {{config('app.url')}}/games/{{ $game->id }}
        </p>
ř3

            Let the countdown begin!<br>
             – The Trivius Team
        </p>
    </p>
</div>
