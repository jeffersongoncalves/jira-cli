<?php

use App\DTOs\Project;

it('creates project from API response', function () {
    $data = loadFixture('projects');
    $project = Project::fromApi($data[0]);

    expect($project->key)->toBe('PROJ')
        ->and($project->name)->toBe('My Project')
        ->and($project->lead)->toBe('John Doe')
        ->and($project->projectTypeKey)->toBe('software');
});
