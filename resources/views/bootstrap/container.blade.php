@if($alerts->isNotEmpty())
    <div {{ $attributes ?? 'class="alerts"' }}>
        @each('laralerts::bootstrap.alert', $alerts, 'alert')
    </div>
@endif
