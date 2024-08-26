<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

it('can validate file types', function () {
    Storage::shouldReceive('exists')
        ->andReturn(true);
    Storage::shouldReceive('size')
        ->andReturn(1000000);

    $validator = new \App\Rules\StorageFile(1000000, ['jpg', 'JPEG', 'png']);
    collect([
        'file_name_' . Str::uuid() . '.jpg',
        'file_name_' . Str::uuid() . '.png',
        'file_name_' . Str::uuid() . '.JPG',
        'file_name_' . Str::uuid() . '.JPEG'
    ])->each(function ($file) use ($validator) {
        $this->assertTrue($validator->passes('file', $file));
    });

    $this->assertFalse($validator->passes('file', 'file_name_' . Str::uuid() . '.pdf'));
});

it('can validate file size', function () {
    Storage::shouldReceive('exists')
        ->andReturn(true);
    Storage::shouldReceive('size')
        ->andReturn(1000000);

    $validator = new \App\Rules\StorageFile(1000000);
    $this->assertTrue($validator->passes('file', 'file_name_' . Str::uuid() . '.jpg'));

    Storage::clearResolvedInstances();
    Storage::shouldReceive('exists')
        ->andReturn(true)
        ->shouldReceive('size')
        ->andReturn(2000000);

    // Fake pdf with 2 times the authorized size
    $this->assertFalse($validator->passes('file', 'file_name_' . Str::uuid() . '.pdf'));
});
