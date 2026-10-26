<?php

namespace HeyDeacon\StatamicDeacon\Tests;

use HeyDeacon\StatamicDeacon\ServiceProvider;
use Illuminate\Routing\Router;
use Statamic\Testing\AddonTestCase;

abstract class TestCase extends AddonTestCase
{
    protected string $addonServiceProvider = ServiceProvider::class;

    protected function defineEnvironment($app): void
    {
        $app['config']->set('view.paths', [__DIR__.'/views']);
    }

    /**
     * @param  Router  $router
     */
    protected function defineRoutes($router): void
    {
        $router->get('/antlers', fn () => view('antlers'));
    }

    /**
     * What the template's tag printed, on a page rendered for a request.
     */
    protected function renderedTag(string $path = '/antlers'): string
    {
        return trim($this->get($path)->assertOk()->getContent());
    }
}
