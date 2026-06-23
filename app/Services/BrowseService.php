<?php

namespace App\Services;

use JeffersonGoncalves\LaravelZero\Support\Browser;

class BrowseService
{
    public function open(string $url): bool
    {
        return Browser::open($url);
    }
}
