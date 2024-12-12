<div>

    <p style="text-align: center;">
       LOGO
    </p>

    <p>Dear {{ $invitee->first_name }},</p>

    <p>

    </p>
    <p>
            {{ $host->first_name }} {{ $host->last_name }}  is having a party and you’re invited. At the {{ $game->name }} you’ll be playing everybody’s favorite new game, Trivius - where you become the trivia.

            <p>
                <a href="{{config('app.url')}}/register/{{ $invitee->id }}">Click here to RSVP and take the Trivius quiz. </a>
            </p>

            <p>
            Party Details<br/>
            Location: {{ $game->location }}<br/>
            Date: {{ \Carbon\Carbon::parse($game->date_time)->format('m/d/Y') }}<br/>
            Time: {{ \Carbon\Carbon::parse($game->date_time)->format('h:i A') }}<br/>
            </p>
    </p>
</div>
