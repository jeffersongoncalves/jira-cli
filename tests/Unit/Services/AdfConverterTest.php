<?php

use App\Services\AdfConverter;

it('converts plain text to a paragraph node', function () {
    expect(AdfConverter::fromMarkdown('Hello world'))->toBe([
        AdfConverter::paragraph('Hello world'),
    ]);
});

it('converts headings and bullet lists', function () {
    $markdown = <<<'MD'
    ### Heading

    Some intro text.

    - item one
    - item two
    MD;

    expect(AdfConverter::fromMarkdown($markdown))->toBe([
        AdfConverter::heading('Heading', 3),
        AdfConverter::paragraph('Some intro text.'),
        AdfConverter::bulletList(['item one', 'item two']),
    ]);
});

it('wraps content nodes in a doc', function () {
    $doc = AdfConverter::doc([AdfConverter::paragraph('x')]);

    expect($doc)->toBe([
        'type' => 'doc',
        'version' => 1,
        'content' => [AdfConverter::paragraph('x')],
    ]);
});
