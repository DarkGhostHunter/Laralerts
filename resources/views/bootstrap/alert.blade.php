<div class="{{ trim('alert ' . $alert->classes) }}" role="alert">
    {!! $alert->message !!}
    @if($alert->dismissible)
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Close') }}"></button>
    @endif
</div>
