<?php




use App\Open\MentionParser;




test('it replaces mention elements with their corresponding values', function () {

    $content = '<p>Hello <span mention mention-field-id="123">Placeholder</span></p>';

    $data = [['nf_id' => '123', 'value' => 'World']];




    $parser = new MentionParser($content, $data);

    $result = $parser->parse();




    expect($result)->toBe('<p>Hello World</p>');

});




test('it handles multiple mentions', function () {

    $content = '<p><span mention mention-field-id="123">Name</span> is <span mention mention-field-id="456">Age</span> years old</p>';

    $data = [

        ['nf_id' => '123', 'value' => 'John'],

        ['nf_id' => '456', 'value' => 30],

    ];




    $parser = new MentionParser($content, $data);

    $result = $parser->parse();




    expect($result)->toBe('<p>John is 30 years old</p>');

});




test('it uses fallback when value is not found', function () {

    $content = '<p>Hello <span mention mention-field-id="123" mention-fallback="Friend">Placeholder</span></p>';

    $data = [];




    $parser = new MentionParser($content, $data);

    $result = $parser->parse();




    expect($result)->toBe('<p>Hello Friend</p>');

});




test('it removes mention element when no value and no fallback', function () {

    $content = '<p>Hello <span mention mention-field-id="123">Placeholder</span></p>';

    $data = [];




    $parser = new MentionParser($content, $data);

    $result = $parser->parse();




    expect($result)->toBe('<p>Hello </p>');

});




test('it handles array values', function () {

    $content = '<p>Tags: <span mention mention-field-id="123">Placeholder</span></p>';

    $data = [['nf_id' => '123', 'value' => ['PHP', 'Laravel', 'Testing']]];




    $parser = new MentionParser($content, $data);

    $result = $parser->parse();




    expect($result)->toBe('<p>Tags: PHP, Laravel, Testing</p>');

});




test('it preserves HTML structure', function () {

    $content = '<div><p>Hello <span mention mention-field-id="123">Placeholder</span></p><p>How are you?</p></div>';

    $data = [['nf_id' => '123', 'value' => 'World']];




    $parser = new MentionParser($content, $data);

    $result = $parser->parse();




    expect($result)->toBe('<div><p>Hello World</p><p>How are you?</p></div>');

});




test('it handles UTF-8 characters', function () {

    $content = '<p>こんにちは <span mention mention-field-id="123">Placeholder</span></p>';

    $data = [['nf_id' => '123', 'value' => '世界']];




    $parser = new MentionParser($content, $data);

    $result = $parser->parse();




    expect($result)->toBe('<p>こんにちは 世界</p>');

});




test('it handles content without surrounding paragraph tags', function () {

    $content = 'some text <span contenteditable="false" mention="" mention-field-id="123" mention-field-name="Post excerpt" mention-fallback="">Post excerpt</span> dewde';

    $data = [['nf_id' => '123', 'value' => 'replaced text']];




    $parser = new MentionParser($content, $data);

    $result = $parser->parse();




    expect($result)->toBe('some text replaced text dewde');

});