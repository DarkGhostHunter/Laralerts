<?php

namespace DarkGhostHunter\Laralerts\Tests;

use BadMethodCallException;
use DarkGhostHunter\Laralerts\Alert;
use Orchestra\Testbench\TestCase;

class AlertTest extends TestCase
{
    use Concerns\RegistersPackage;

    /** @var \DarkGhostHunter\Laralerts\Alert */
    protected $alert;

    protected function setUp(): void
    {
        parent::setUp();

        $this->alert = new Alert;
    }

    public function testGetAndSetCloseHtml()
    {
        $html = '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';

        $this->assertEquals($html, Alert::getCloseHtml());
        Alert::setCloseHtml('test-close');
        $this->assertEquals('test-close', Alert::getCloseHtml());
        Alert::setCloseHtml($html);
    }

    public function testGetAndSetMessage()
    {
        $this->assertNull($this->alert->getMessage());
        $this->alert->message('test-message');
        $this->assertEquals('test-message', $this->alert->getMessage());
    }

    public function testMessageLocalization()
    {
        /** @var \Illuminate\Translation\Translator $translator */
        $translator = $this->app->make('translator');

        $translator->addLines([
            'trans.test' => 'successful'
        ], 're');

        $translator->setLocale('re');

        $this->assertNull($this->alert->getMessage());
        $this->alert->lang('trans.test');
        $this->assertEquals('successful', $this->alert->getMessage());
    }

    public function testMessageEncode()
    {
        $this->assertNull($this->alert->getMessage());

        $this->alert->escape('<script>alert("should be escaped")</script>');

        $escaped = '&lt;script&gt;alert(&quot;should be escaped&quot;)&lt;/script&gt;';

        $this->assertEquals($escaped, $this->alert->getMessage());
    }

    public function testToJson()
    {
        $this->alert->message('test-message');

        $this->assertJson($this->alert->toJson());
        $this->assertStringContainsString('test-message', $this->alert->toJson());
    }

    public function testToArray()
    {
        $this->alert->message('test-message')->dismissible()->info();

        $array = [
            'message' => 'test-message',
            'type' => 'info',
            'dismissible' => true,
        ];

        $this->assertEquals($array, $this->alert->toArray());
    }

    public function testTypes()
    {
        $primary = (new Alert)->primary();
        $secondary = (new Alert)->secondary();
        $success = (new Alert)->success();
        $danger = (new Alert)->danger();
        $warning = (new Alert)->warning();
        $info = (new Alert)->info();
        $light = (new Alert)->light();
        $dark = (new Alert)->dark();

        $this->assertEquals('primary', $primary->getType());
        $this->assertEquals('secondary', $secondary->getType());
        $this->assertEquals('success', $success->getType());
        $this->assertEquals('danger', $danger->getType());
        $this->assertEquals('warning', $warning->getType());
        $this->assertEquals('info', $info->getType());
        $this->assertEquals('light', $light->getType());
        $this->assertEquals('dark', $dark->getType());
    }

    public function testInvalidType()
    {
        $this->expectException(BadMethodCallException::class);

        $this->alert->setType('invalid-type');
    }

    public function testClasses()
    {
        $this->alert->classes('foo', 'bar', 'baz asd');
        $this->assertStringContainsString('foo bar baz asd', $this->alert->toHtml());
        $this->assertEquals('foo bar baz asd', $this->alert->getClasses());

        $this->alert->classes('replaced');
        $this->assertStringContainsString('replaced', $this->alert->toHtml());
        $this->assertEquals('replaced', $this->alert->getClasses());
        $this->assertStringNotContainsString('foo bar baz asd', $this->alert->toHtml());

        $this->alert->classes(['foo', 'bar']);
        $this->assertEquals('foo bar', $this->alert->getClasses());
    }

    public function testRenderToHtml()
    {
        $this->alert->message('test-message')->dismissible()->classes('test-class')->info();

        $expected = '<div class="alert alert-info alert-dismissible fade show test-class" role="alert">test-message<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';

        $this->assertEquals($expected, $this->alert->toHtml());
        $this->assertEquals($expected, (string)$this->alert);
        $this->assertEquals($expected, $this->alert->__toString());
    }

    public function testDismissible()
    {
        $this->alert->message('test-message')->dismissible();

        $this->assertTrue($this->alert->isDismissible());
        $this->assertTrue($this->alert->isShow());
        $this->assertEquals('fade', $this->alert->getAnimationClass());
        $this->assertStringContainsString('show', $this->alert->toHtml());
        $this->assertStringContainsString('alert-dismissible', $this->alert->toHtml());
    }

    public function testCustomDismissible()
    {
        $this->alert->message('test-message')->dismissible(false, 'test-fade');

        $this->assertTrue($this->alert->isDismissible());
        $this->assertFalse($this->alert->isShow());
        $this->assertEquals('test-fade', $this->alert->getAnimationClass());
        $this->assertStringNotContainsString('show', $this->alert->toHtml());
        $this->assertStringContainsString('test-fade', $this->alert->toHtml());
    }


    public function testGetAndSetShow()
    {
        $this->assertTrue($this->alert->isShow());
        $this->alert->setShow(false);
        $this->assertFalse($this->alert->isShow());
    }

    public function testAnimationClass()
    {
        $this->assertEquals('fade', $this->alert->getAnimationClass());
        $this->alert->setAnimationClass('test-animation');
        $this->assertEquals('test-animation', $this->alert->getAnimationClass());
    }
}