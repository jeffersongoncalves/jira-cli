<?php

use App\DTOs\Sprint;
use App\Enums\SprintState;

it('creates sprint from API response', function () {
    $data = loadFixture('sprints');
    $sprint = Sprint::fromApi($data['values'][1]);

    expect($sprint->id)->toBe(2)
        ->and($sprint->name)->toBe('Sprint 2')
        ->and($sprint->state)->toBe(SprintState::Active)
        ->and($sprint->goal)->toBe('Dashboard features');
});

it('handles future sprint with null dates', function () {
    $data = loadFixture('sprints');
    $sprint = Sprint::fromApi($data['values'][2]);

    expect($sprint->state)->toBe(SprintState::Future)
        ->and($sprint->startDate)->toBeNull()
        ->and($sprint->endDate)->toBeNull()
        ->and($sprint->goal)->toBeNull();
});
