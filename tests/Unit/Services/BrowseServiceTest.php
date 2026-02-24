<?php

use App\Services\BrowseService;

it('instantiates browse service', function () {
    $service = new BrowseService;

    expect($service)->toBeInstanceOf(BrowseService::class);
});
