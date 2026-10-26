<?php

namespace HeyDeacon\StatamicDeacon\Tests;

use HeyDeacon\StatamicDeacon\ServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Vite;
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
        $router->get('/blade', fn () => view('blade'));

        $router->get('/antlers-with-vite-nonce', function () {
            Vite::useCspNonce('n0nce');

            return view('antlers');
        });

        $router->get('/antlers-with-spatie-nonce', function () {
            app()->instance('csp-nonce', 'sp4tie');

            return view('antlers');
        });
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

    /**
     * The class and message of the error that stopped the page rendering, under the view
     * error the template engine wraps it in.
     *
     * @return array{class-string, string}|null
     */
    protected function renderingError(string $path = '/antlers'): ?array
    {
        $this->withoutExceptionHandling();

        try {
            $this->get($path);
        } catch (\Throwable $error) {
            while ($error->getPrevious() !== null) {
                $error = $error->getPrevious();
            }

            return [$error::class, $error->getMessage()];
        }

        return null;
    }
}
