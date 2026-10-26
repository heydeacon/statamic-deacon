<?php

namespace HeyDeacon\StatamicDeacon\Tags;

use Illuminate\Support\Facades\Log;
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

        $attributes = ['data-key' => $key];

        if ($context !== null) {
            $attributes['data-context'] = json_encode($context, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        if ($pageViews === false) {
            $attributes['data-no-page-views'] = true;
        }

        return $this->loaderTag($attributes);
    }

    /**
     * The tag Install gives, with any of its other attributes between the key and async. An
     * attribute that is true is written with no value, as async is.
     *
     * @param  array<string, string|true>  $attributes
     */
    private function loaderTag(array $attributes): string
    {
        $rendered = '';

        foreach ($attributes as $name => $value) {
            $rendered .= $value === true ? ' '.$name : ' '.$name.'="'.e($value).'"';
        }

        return '<script src="'.self::LOADER_SRC.'"'.$rendered.' async></script>';
    }
}
