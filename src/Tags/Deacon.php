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

        return '<script src="'.self::LOADER_SRC.'" data-key="'.$key.'" async></script>';
    }
}
