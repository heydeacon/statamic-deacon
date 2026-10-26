<?php

namespace HeyDeacon\StatamicDeacon\Tags;

use Statamic\Tags\Tags;

class Deacon extends Tags
{
    private const LOADER_SRC = 'https://heydeacon.com/widget/v1/loader.js';

    /**
     * The {{ deacon }} tag.
     */
    public function index(): string
    {
        $key = config('deacon.key');

        return '<script src="'.self::LOADER_SRC.'" data-key="'.$key.'" async></script>';
    }
}
