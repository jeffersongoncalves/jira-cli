<?php

use App\Services\BoardService;

it('lists boards', function () {
    $boardService = Mockery::mock(BoardService::class);
    $boardService->shouldReceive('list')
        ->withAnyArgs()
        ->andReturn(loadFixture('boards'));
    $this->app->instance(BoardService::class, $boardService);

    $this->artisan('board:list')
        ->expectsOutputToContain('PROJ Board')
        ->assertExitCode(0);
});
