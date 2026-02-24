<?php

use App\DTOs\Worklog;

it('creates worklog from API response', function () {
    $data = loadFixture('worklogs');
    $worklog = Worklog::fromApi($data['worklogs'][0]);

    expect($worklog->id)->toBe('10001')
        ->and($worklog->authorDisplayName)->toBe('John Doe')
        ->and($worklog->timeSpent)->toBe('2h')
        ->and($worklog->comment)->toContain('Fixed auth issue');
});
