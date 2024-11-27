<div>

    <p style="text-align: center;">
       LOGO
    </p>
    
    <p>Dear {{ $invitee->first_name }},</p>
        
    <p>
   
    </p>
    <p>
            {{ $host->first_name }} {{ $host->last_name }} invited you to play everybody's favorite party game "It's Who You Know" 
            on {{ \Carbon\Carbon::parse($game->date_time)->format('m/d/Y') }} at {{ \Carbon\Carbon::parse($game->date_time)->format('h:i A') }} at {{ $game->location }}.


            
            <p>
                <a href="{{config('app.url')}}/register/{{ $invitee->id }}">Click here to RSVP</a>
            </p>



    </p>
</div>