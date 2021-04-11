<div class="{{ trim('alert ' . $alert->classes) }}" role="alert">
    {!! $alert->message !!}
    @if($alert->dismissible)
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    @endif
</div>
