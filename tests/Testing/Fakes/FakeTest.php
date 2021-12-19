<?php

namespace Tests\Testing\Fakes;

use DarkGhostHunter\Laralerts\Facades\Alert;
use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\AssertionFailedError;
use Tests\RegistersPackage;

class FakeTest extends TestCase
{
    use RegistersPackage;

    public function test_fake_bag_keeps_alerts(): void
    {
        Route::get('test', static function (): void {
            Alert::raw('foo');
        })->middleware('web');

        $bag = Alert::fake();

        $this->get('test');

        static::assertEmpty($bag->collect());
        static::assertCount(1, $bag->added);
    }

    public function test_asserts_empty(): void
    {
        $bag = Alert::fake();

        $bag->assertEmpty();
    }

    public function test_asserts_empty_exception(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("Failed to assert that there is no alerts.\nFailed asserting that an object is empty.");

        $bag = Alert::fake();

        Alert::raw('foo');

        $bag->assertEmpty();
    }

    public function test_asserts_not_empty(): void
    {
        $bag = Alert::fake();

        Alert::raw('foo');

        $bag->assertNotEmpty();
    }

    public function test_asserts_not_empty_exception(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("Failed to assert that there is any alert.\nFailed asserting that an object is not empty.");
        $bag = Alert::fake();

        $bag->assertNotEmpty();
    }

    public function test_asserts_has_one(): void
    {
        $bag = Alert::fake();

        Alert::raw('foo');

        $bag->assertHasOne();
    }

    public function test_asserts_has_one_exception(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("Failed to assert that there is only one alert\nFailed asserting that actual size 2 matches expected size 1.");

        $bag = Alert::fake();

        Alert::raw('foo');
        Alert::raw('bar');

        $bag->assertHasOne();
    }

    public function test_asserts_has(): void
    {
        $bag = Alert::fake();

        Alert::raw('foo');
        Alert::raw('bar');
        Alert::raw('baz');

        $bag->assertHas(3);
    }

    public function test_asserts_has_exception(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("Failed to assert that [3] alerts match the expected [2] count.\nFailed asserting that actual size 3 matches expected size 2.");

        $bag = Alert::fake();

        Alert::raw('foo');
        Alert::raw('bar');
        Alert::raw('baz');

        $bag->assertHas(2);
    }

    public function test_assert_persisted(): void
    {
        $bag = Alert::fake();

        Alert::raw('foo')->persistAs('bar');

        $bag->assertPersisted('bar');
    }

    public function test_assert_persisted_exception(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("Failed to assert that there is persisted alerts.\nFailed asserting that actual size 0 matches expected size 1.");

        $bag = Alert::fake();

        Alert::raw('foo');

        $bag->assertPersisted('bar');
    }

    public function test_assert_has_persistent(): void
    {
        $bag = Alert::fake();

        Alert::raw('foo')->persistAs('bar');
        Alert::raw('baz');

        $bag->assertHasPersistent();
    }

    public function test_assert_has_persistent_exception(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("Failed to assert that there is any persistent alert\nFailed asserting that an object is not empty.");

        $bag = Alert::fake();

        Alert::raw('foo');

        $bag->assertHasPersistent();
    }

    public function test_assert_has_no_persistent(): void
    {
        $bag = Alert::fake();

        Alert::raw('baz');

        $bag->assertHasNoPersistent();
    }

    public function test_assert_has_no_persistent_exception(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("Failed to assert that there is no persistent alert.\nFailed asserting that an object is empty.");

        $bag = Alert::fake();

        Alert::raw('foo')->persistAs('bar');

        $bag->assertHasNoPersistent();
    }
}
