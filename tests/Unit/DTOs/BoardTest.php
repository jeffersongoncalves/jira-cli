<?php

use App\DTOs\Board;

it('creates board from API response', function () {
    $data = loadFixture('boards');
    $board = Board::fromApi($data['values'][0]);

    expect($board->id)->toBe(1)
        ->and($board->name)->toBe('PROJ Board')
        ->and($board->type)->toBe('scrum');
});
