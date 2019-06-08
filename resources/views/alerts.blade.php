@if($alerts)
    <div class="alerts">
        @foreach($alerts as $alert)
            {{ $alert }}
        @endforeach
    </div>
@endif
