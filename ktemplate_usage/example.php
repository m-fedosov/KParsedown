<?php

require_once __DIR__ . '/../vendor/autoload.php';

use KTemplate\Context;
use KTemplate\Engine;
use KTemplate\ArrayLoader;

$ctx = new Context();

$loader = new ArrayLoader([
    'main.html' => file_get_contents('markdown.txt'),
]);

$engine = new Engine($ctx, $loader);

$engine->registerFilter1('markdown_to_html', function ($x) {
    $parse = new Parsedown();
    return $parse->text($x);
});

$result = $engine->render('main.html');
var_dump($result); // => "Example"
file_put_contents("example.html", $result);

