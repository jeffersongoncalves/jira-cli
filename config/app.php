<?php

use App\Providers\AppServiceProvider;

return [

    'name' => 'Jira CLI',

    'version' => app('git.version'),

    'env' => 'development',

    'providers' => [
        AppServiceProvider::class,
    ],

];
