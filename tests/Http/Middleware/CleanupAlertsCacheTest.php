<?php declare(strict_types=1);

use DarkGhostHunter\Laralerts\Http\Middleware\CleanupAlertsCache;
use DarkGhostHunter\Laralerts\Tests\Concerns\RegistersPackage;
use Illuminate\Session\Middleware\StartSession;
use Orchestra\Testbench\TestCase;

class CleanupAlertsCacheTest extends TestCase
{
    use RegistersPackage;

    /** @test */
    public function it_should_remove_only_alerts_from_session() : void
    {
        // Create session with some data
        $this->session(['one' => 'flew']);

        // Register views location
        /** @var \Illuminate\View\Factory $factory */
        $factory = $this->app->make('view');
        $factory->addLocation(__DIR__ . '/../../');

        // Register route "test" that creates an alert in session, but with the middleware
        // under test that cleans up the session after the response has been created.
        $this->app->make('router')->get('test', function () {
            alert('this is an alert');
            return response()->view('test-view');
        })->middleware([
            StartSession::class,
            CleanupAlertsCache::class,
        ]);

        // Assert response contains the alert, but the session not anymore
        $this->get('test')
             ->assertSeeText('this is an alert')
             ->assertSessionMissing('_alerts')
             ->assertSessionHas(['one' => 'flew']);
    }
}
