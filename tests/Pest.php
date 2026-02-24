<?php

uses(Tests\TestCase::class)->in('Feature', 'Unit');

function loadFixture(string $name): array
{
    $path = base_path("tests/Fixtures/{$name}.json");

    return json_decode(file_get_contents($path), true);
}
