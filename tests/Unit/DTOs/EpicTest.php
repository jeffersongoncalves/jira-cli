<?php

use App\DTOs\Epic;

it('creates epic from API response', function () {
    $data = loadFixture('epics');
    $epic = Epic::fromApi($data['issues'][0]);

    expect($epic->key)->toBe('PROJ-100')
        ->and($epic->name)->toBe('User Management')
        ->and($epic->status)->toBe('In Progress')
        ->and($epic->done)->toBeFalse();
});

it('detects done epic via resolution', function () {
    $data = loadFixture('epics');
    $epic = Epic::fromApi($data['issues'][1]);

    expect($epic->done)->toBeTrue()
        ->and($epic->status)->toBe('Done');
});
