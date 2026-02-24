<?php

return [

    'name' => 'Jira CLI',

    'version' => app('git.version'),

    'env' => 'development',

    'providers' => [
        App\Providers\AppServiceProvider::class,
    ],

];
