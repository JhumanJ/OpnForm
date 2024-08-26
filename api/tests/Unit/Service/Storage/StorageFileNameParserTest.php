<?php

uses(\Tests\TestCase::class);

use Illuminate\Support\Str;

it('can parse filenames', function () {
    $fileName = 'Notion_app_logo_85e16d7b-58ed-43bc-8dce-7d3ff7d69f41.png';
    $parsedFilename = \App\Service\Storage\StorageFileNameParser::parse($fileName);
    expect($parsedFilename->fileName)->toBe('Notion_app_logo');
    expect($parsedFilename->uuid)->toBe('85e16d7b-58ed-43bc-8dce-7d3ff7d69f41');
    expect($parsedFilename->extension)->toBe('png');
    expect($parsedFilename->getMovedFileName())->toBe($fileName);

    $uuid = Str::uuid()->toString();
    $parsedFilename = \App\Service\Storage\StorageFileNameParser::parse($uuid);
    expect($parsedFilename->uuid)->toBe($uuid);
    expect($parsedFilename->fileName)->toBeNull();
    expect($parsedFilename->extension)->toBeNull();
    expect($parsedFilename->getMovedFileName())->toBe($uuid);

    $randomString = Str::random(20);
    $parsedFilename = \App\Service\Storage\StorageFileNameParser::parse($randomString);
    expect($parsedFilename->fileName)->toBeNull();
    expect($parsedFilename->uuid)->toBeNull();
    expect($parsedFilename->extension)->toBeNull();
    expect($parsedFilename->getMovedFileName())->toBeNull();
});

it('can clean non-utf characters', function () {
    $fileName = 'Образец_для_заполнения_85e16d7b-58ed-43bc-8dce-7d3ff7d69f41.png';
    $parsedFilename = \App\Service\Storage\StorageFileNameParser::parse($fileName);
    expect($parsedFilename->fileName)->toBe('Образец_для_заполнения');
    expect($parsedFilename->uuid)->toBe('85e16d7b-58ed-43bc-8dce-7d3ff7d69f41');
    expect($parsedFilename->extension)->toBe('png');
    expect($parsedFilename->getMovedFileName())->toBe('___85e16d7b-58ed-43bc-8dce-7d3ff7d69f41.png');
});
