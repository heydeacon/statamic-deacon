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

    protected function tearDown(): void
    {
        $this->setEnvironmentKey(null);

        parent::tearDown();
    }

    /**
     * Boots the site again with DEACON_KEY set, as a site reads its .env when it starts.
     */
    protected function bootWithEnvironmentKey(?string $key): void
    {
        $this->setEnvironmentKey($key);
        $this->reloadApplication();
    }

    private function setEnvironmentKey(?string $key): void
    {
        if ($key === null) {
            putenv('DEACON_KEY');
            unset($_ENV['DEACON_KEY'], $_SERVER['DEACON_KEY']);

            return;
        }

        putenv("DEACON_KEY={$key}");
        $_ENV['DEACON_KEY'] = $key;
        $_SERVER['DEACON_KEY'] = $key;
    }

    /**
     * What the template's tag printed, on a page rendered for a request.
     */
    protected function renderedTag(string $path = '/antlers'): string
    {
        return trim($this->get($path)->assertOk()->getContent());
    }
}
