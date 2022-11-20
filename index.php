<?php

require_once __DIR__ . '/vendor/autoload.php';

$parse = new Parsedown();

var_dump($parse->text('# Hello from KParsedown!!!'));