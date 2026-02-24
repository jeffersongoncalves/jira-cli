<?php

use App\Services\EpicService;
use Laravel\Prompts\Prompt;

beforeEach(function () {
    Prompt::fallbackWhen(true);
});

it('creates an epic with options', function () {
    $epicService = Mockery::mock(EpicService::class);
    $epicService->shouldReceive('create')
        ->withAnyArgs()
        ->once()
        ->andReturn(loadFixture('issue-created'));
    $this->app->instance(EpicService::class, $epicService);

    $this->artisan('epic:create --project=PROJ --summary="New Epic" --description="Test"')
        ->expectsOutputToContain('PROJ-3')
        ->assertExitCode(0);
});
