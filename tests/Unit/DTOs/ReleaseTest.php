<?php

use App\DTOs\Release;

it('creates release from API response', function () {
    $data = loadFixture('releases');
    $release = Release::fromApi($data[0]);

    expect($release->id)->toBe(10000)
        ->and($release->name)->toBe('v1.0.0')
        ->and($release->released)->toBeTrue()
        ->and($release->releaseDate)->toBe('2024-01-15');
});

it('handles unreleased version', function () {
    $data = loadFixture('releases');
    $release = Release::fromApi($data[1]);

    expect($release->released)->toBeFalse()
        ->and($release->releaseDate)->toBeNull();
});
