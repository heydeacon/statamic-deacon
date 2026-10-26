<?php

namespace HeyDeacon\StatamicDeacon\Tags;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Vite;
use InvalidArgumentException;
use Statamic\Tags\Tags;

class Deacon extends Tags
{
    private const LOADER_SRC = 'https://heydeacon.com/widget/v1/loader.js';

    private const MISSING_KEY = 'statamic-deacon: the key is missing, so the page has no Deacon widget. '
        .'Set DEACON_KEY in .env, or key in config/deacon.php. In Deacon, open Install (the button at '
        .'the top of the dashboard) and copy the data-key value from the snippet. It starts with pk_.';

    /**
     * The {{ deacon }} tag.
     */
    public function index(): string
    {
        $key = config('deacon.key');
        $key = is_string($key) ? trim($key) : '';

        if ($key === '') {
            Log::warning(self::MISSING_KEY);

            return '';
        }

        $context = config('deacon.context');
        $pageViews = config('deacon.page_views', true);

        if ($context !== null && ! $this->isContext($context)) {
            throw new InvalidArgumentException(
                'statamic-deacon: context in config/deacon.php must be an array of names to strings, '
                .'numbers or booleans.'
            );
        }

        if (! is_bool($pageViews)) {
            throw new InvalidArgumentException('statamic-deacon: page_views in config/deacon.php must be true or false.');
        }

        $attributes = ['data-key' => $key];

        if ($context !== null) {
            $attributes['data-context'] = json_encode($context, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        if ($pageViews === false) {
            $attributes['data-no-page-views'] = true;
        }

        return $this->loaderTag($attributes, Vite::cspNonce());
    }

    /**
     * @phpstan-assert-if-true array<string, string|int|float|bool> $context
     */
    private function isContext(mixed $context): bool
    {
        if (! is_array($context)) {
            return false;
        }

        foreach ($context as $name => $value) {
            if (! is_string($name) || ! (is_string($value) || is_int($value) || is_float($value) || is_bool($value))) {
                return false;
            }
        }

        return true;
    }

    /**
     * The tag Install gives, with any of its other attributes between the key and async, and
     * the nonce after them. An attribute that is true is written with no value, as async is.
     *
     * @param  array<string, string|true>  $attributes
     */
    private function loaderTag(array $attributes, ?string $nonce): string
    {
        $rendered = '';

        foreach ($attributes as $name => $value) {
            $rendered .= $value === true ? ' '.$name : ' '.$name.'="'.e($value).'"';
        }

        $nonceAttribute = $nonce === null ? '' : ' nonce="'.e($nonce).'"';

        return '<script src="'.self::LOADER_SRC.'"'.$rendered.' async'.$nonceAttribute.'></script>';
    }
}
