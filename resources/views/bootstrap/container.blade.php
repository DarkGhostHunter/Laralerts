@if(count($alerts))
    <div class="alerts">
        @each('laralerts::bootstrap.alert', $alerts, 'alert')
    </div>
@endif
