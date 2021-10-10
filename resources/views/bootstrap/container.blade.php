@if($alerts->isNotEmpty())
    <div class="alerts">
        @each('laralerts::bootstrap.alert', $alerts, 'alert')
    </div>
@endif
