<?php

namespace Tests\Testing;

use DarkGhostHunter\Laralerts\Facades\Alert;
use DarkGhostHunter\Laralerts\Testing\Fakes\BagFake;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\URL;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\AssertionFailedError;
use Tests\RegistersPackage;
use function e;

class BuilderTest extends TestCase
{
    use RegistersPackage;

    protected BagFake $bag;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bag = Alert::fake();
    }

    public function test_exists(): void
    {
        Alert::raw('foo');

        $this->bag->assertAlert()->exists();
    }

    public function test_exists_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("Failed to assert that at least one alert matches the expectations.\nFailed asserting that an object is not empty.");

        $this->bag->assertAlert()->exists();
    }

    public function test_missing(): void
    {
        $this->bag->assertAlert()->missing();
    }

    public function test_missing_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("Failed to assert that no alert matches the expectations.\nFailed asserting that an object is empty.");

        Alert::raw('foo');

        $this->bag->assertAlert()->missing();
    }

    public function test_unique(): void
    {
        Alert::raw('foo');

        $this->bag->assertAlert()->unique();
    }

    public function test_unique_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("Failed to assert that there is only one alert.\nFailed asserting that actual size 2 matches expected size 1.");

        Alert::raw('foo');
        Alert::raw('bar');

        $this->bag->assertAlert()->unique();
    }

    public function test_count(): void
    {
        Alert::raw('foo');
        Alert::raw('bar');

        $this->bag->assertAlert()->count(2);
    }

    public function test_count_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("Failed to assert that [2] alerts match the expected [3] count.\nFailed asserting that actual size 2 matches expected size 3.");

        Alert::raw('foo');
        Alert::raw('bar');

        $this->bag->assertAlert()->count(3);
    }

    public function test_filters_by_raw(): void
    {
        Alert::raw('foo');
        Alert::raw('bar');

        $this->bag->assertAlert()->withRaw('foo')->unique();
    }

    public function test_filters_by_raw_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("Failed to assert that there is only one alert.\nFailed asserting that actual size 0 matches expected size 1.");

        Alert::raw('foo');
        Alert::raw('bar');

        $this->bag->assertAlert()->withRaw('quz')->unique();
    }

    public function test_filters_by_message(): void
    {
        Alert::raw(e('<foo>'));
        Alert::raw('<foo>');

        $this->bag->assertAlert()->withMessage('<foo>')->unique();
    }

    public function test_filters_by_message_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("Failed to assert that there is only one alert.\nFailed asserting that actual size 0 matches expected size 1.");

        Alert::raw(e('<bar>'));
        Alert::raw('<foo>');

        $this->bag->assertAlert()->withMessage('<foo>')->unique();
    }

    public function test_filters_by_trans(): void
    {
        Lang::shouldReceive('get')->with('foo.bar', [], null)->twice()->andReturn('baz');

        Alert::raw(Lang::get('foo.bar', [], null));
        Alert::raw('foo.bar');

        $this->bag->assertAlert()->withTrans('foo.bar')->unique();
    }

    public function test_filters_by_trans_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("Failed to assert that there is only one alert.\nFailed asserting that actual size 0 matches expected size 1.");

        Lang::shouldReceive('get')->with('foo.bar', [], null)->once()->andReturn('baz');

        Alert::raw('foo.bar');

        $this->bag->assertAlert()->withTrans('foo.bar')->unique();
    }

    public function test_filters_by_trans_choice(): void
    {
        Lang::shouldReceive('choice')->with('foo.bar', 2, [], null)->twice()->andReturn('baz');

        Alert::raw(Lang::choice('foo.bar', 2, [], null));
        Alert::raw('foo.bar');

        $this->bag->assertAlert()->withTransChoice('foo.bar', 2)->unique();
    }

    public function test_filters_by_trans_choice_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("Failed to assert that there is only one alert.\nFailed asserting that actual size 0 matches expected size 1.");

        Lang::shouldReceive('choice')->with('foo.bar', 2, [], null)->once()->andReturn('baz');

        Alert::raw('foo.bar');

        $this->bag->assertAlert()->withTransChoice('foo.bar', 2)->unique();
    }

    public function test_filters_by_link_away(): void
    {
        Alert::raw('foo.bar')->away('foo', 'bar')->away('baz', 'quz');

        $this->bag->assertAlert()->withAway('foo', 'bar')->withAway('baz', 'quz')->unique();
    }

    public function test_filters_by_link_away_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("Failed to assert that there is only one alert.\nFailed asserting that actual size 0 matches expected size 1.");

        Alert::raw('foo.bar')->away('bar', 'foo');

        $this->bag->assertAlert()->withAway('foo', 'bar')->unique();
    }

    public function test_filters_by_multiple_link_away(): void
    {
        Alert::raw('foo.bar')->away('foo', 'bar')->away('baz', 'quz');

        $this->bag->assertAlert()->withAway('baz', 'quz')->withAway('foo', 'bar')->unique();
    }

    public function test_filters_by_multiple_link_away_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("Failed to assert that there is only one alert.\nFailed asserting that actual size 0 matches expected size 1.");

        Alert::raw('foo.bar')->away('foo', 'bar')->away('baz', 'quz');

        $this->bag->assertAlert()->withAway('foo', 'bar')->unique();
    }

    public function test_filters_by_link_to(): void
    {
        URL::shouldReceive('to')->with('foo', [], null)->twice()->andReturn('bar');

        Alert::raw('foo.bar')->to('bar', 'foo');

        $this->bag->assertAlert()->withTo('bar', 'foo')->unique();
    }

    public function test_filters_by_link_to_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("Failed to assert that there is only one alert.\nFailed asserting that actual size 0 matches expected size 1.");

        URL::shouldReceive('to')->with('foo', [], null)->once()->andReturn('bar');
        URL::shouldReceive('to')->with('bar', [], null)->once()->andReturn('bar');

        Alert::raw('foo.bar')->to('bar', 'foo');

        $this->bag->assertAlert()->withTo('foo', 'bar')->unique();
    }

    public function test_filters_by_link_route(): void
    {
        URL::shouldReceive('route')->with('foo', [], true)->twice()->andReturn('bar');

        Alert::raw('foo.bar')->route('bar', 'foo');

        $this->bag->assertAlert()->withRoute('bar', 'foo')->unique();
    }

    public function test_filters_by_link_route_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("Failed to assert that there is only one alert.\nFailed asserting that actual size 0 matches expected size 1.");

        URL::shouldReceive('route')->with('foo', [], true)->once()->andReturn('bar');
        URL::shouldReceive('route')->with('bar', [], true)->once()->andReturn('bar');

        Alert::raw('foo.bar')->route('bar', 'foo');

        $this->bag->assertAlert()->withRoute('foo', 'bar')->unique();
    }

    public function test_filters_by_link_action(): void
    {
        URL::shouldReceive('action')->with('foo', [], true)->twice()->andReturn('bar');

        Alert::raw('foo.bar')->action('bar', 'foo');

        $this->bag->assertAlert()->withAction('bar', 'foo')->unique();
    }

    public function test_filters_by_link_action_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("Failed to assert that there is only one alert.\nFailed asserting that actual size 0 matches expected size 1.");

        URL::shouldReceive('action')->with('foo', [], true)->once()->andReturn('bar');
        URL::shouldReceive('action')->with('bar', [], true)->once()->andReturn('bar');

        Alert::raw('foo.bar')->action('bar', 'foo');

        $this->bag->assertAlert()->withAction('foo', 'bar')->unique();
    }

    public function test_filters_by_types(): void
    {
        Alert::raw('foo.bar')->types('foo', 'bar');

        $this->bag->assertAlert()->withTypes('bar', 'foo')->unique();
    }

    public function test_filters_by_types_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("Failed to assert that there is only one alert.\nFailed asserting that actual size 0 matches expected size 1.");

        Alert::raw('foo.bar')->types('foo', 'bar');

        $this->bag->assertAlert()->withTypes('foo')->unique();
    }

    public function test_filters_by_dismissible(): void
    {
        Alert::raw('foo.bar')->dismiss();

        $this->bag->assertAlert()->dismissible()->unique();
    }

    public function test_filters_by_dismissible_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("Failed to assert that there is only one alert.\nFailed asserting that actual size 0 matches expected size 1.");

        Alert::raw('foo.bar')->dismiss(false);

        $this->bag->assertAlert()->dismissible()->unique();
    }

    public function test_filters_by_not_dismissible(): void
    {
        Alert::raw('foo.bar')->dismiss(false);

        $this->bag->assertAlert()->notDismissible()->unique();
    }

    public function test_filters_by_not_dismissible_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("Failed to assert that there is only one alert.\nFailed asserting that actual size 0 matches expected size 1.");

        Alert::raw('foo.bar')->dismiss(true);

        $this->bag->assertAlert()->notDismissible()->unique();
    }

    public function test_filters_by_persisted(): void
    {
        Alert::raw('foo')->persistAs('bar');

        $this->bag->assertAlert()->persisted()->unique();
    }

    public function test_filters_by_persisted_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("Failed to assert that there is only one alert.\nFailed asserting that actual size 0 matches expected size 1.");

        Alert::raw('foo');

        $this->bag->assertAlert()->persisted()->unique();
    }

    public function test_filters_by_not_persisted(): void
    {
        Alert::raw('foo');

        $this->bag->assertAlert()->notPersisted()->unique();
    }

    public function test_filters_by_not_persisted_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("Failed to assert that there is only one alert.\nFailed asserting that actual size 0 matches expected size 1.");

        Alert::raw('foo')->persistAs('foo');

        $this->bag->assertAlert()->notPersisted()->unique();
    }

    public function test_filters_by_persisted_as(): void
    {
        Alert::raw('foo')->persistAs('bar');

        $this->bag->assertAlert()->persistedAs('bar');
    }

    public function test_filters_by_persisted_as_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("Failed to assert that [1] persistent alerts exist.\nFailed asserting that actual size 0 matches expected size 1.");

        Alert::raw('foo')->persistAs('bar');

        $this->bag->assertAlert()->persistedAs('foo');
    }

    public function test_filters_by_persisted_as_array(): void
    {
        Alert::raw('foo')->persistAs('foo');
        Alert::raw('foo')->persistAs('bar');
        Alert::raw('foo')->persistAs('quz');

        $this->bag->assertAlert()->persistedAs('bar', 'quz');
    }

    public function test_filters_by_persisted_as_array_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("Failed to assert that [2] persistent alerts exist.\nFailed asserting that actual size 0 matches expected size 2.");

        Alert::raw('foo')->persistAs('foo');
        Alert::raw('foo')->persistAs('bar');
        Alert::raw('foo')->persistAs('quz');

        $this->bag->assertAlert()->persistedAs('qux', 'quuz');
    }

    public function test_filters_by_tag(): void
    {
        Alert::raw('foo')->tag('bar');
        Alert::raw('baz')->tag('bar', 'quz');
        Alert::raw('qux')->tag('quz');

        $this->bag->assertAlert()->withTag('bar', 'quz')->unique();
        $this->bag->assertAlert()->withTag('bar')->unique();
        $this->bag->assertAlert()->withTag('quz')->unique();
    }

    public function test_filters_by_tag_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("Failed to assert that there is only one alert.\nFailed asserting that actual size 0 matches expected size 1.");

        Alert::raw('foo')->tag('bar');

        $this->bag->assertAlert()->withTag('bar', 'quz')->unique();
    }

    public function test_filters_by_any_tag(): void
    {
        Alert::raw('foo')->tag('bar');
        Alert::raw('baz')->tag('bar', 'quz');
        Alert::raw('qux')->tag('quz');

        $this->bag->assertAlert()->withAnyTag('bar', 'quz')->count(3);
        $this->bag->assertAlert()->withAnyTag('bar')->count(2);
        $this->bag->assertAlert()->withAnyTag('quz')->count(2);
    }
}
