<div>

    <p style="text-align: center;">
       <img src="{{config('app.url')}}/images/trivius-logo.png" width="200px" />
    </p>

    <p>Welcome {{ $invitee->first_name }} {{ $invitee->last_name }},</p>

    <p>

    </p>
    <p>
        <a href="{{config('app.url')}}/questions/{{ $game->id }}/{{ $invitee->id }}">Click here</a> for the trivia questions for the game {{ $host->first_name }} {{ $host->last_name }} is hosting at {{ $game->location }} on
        {{ \Carbon\Carbon::parse($game->date_time)->format('m/d/Y') }} at {{ \Carbon\Carbon::parse($game->date_time)->format('h:i A') }}.

        <p>
            After finishing the quiz, create an account if you’re new to Trivius or log in if you’ve played before. This lets you save your answers, edit them anytime, and see who’s joining the fun!
        </p>
    </p>
</div>
