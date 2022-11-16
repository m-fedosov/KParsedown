<?php

require_once __DIR__ . '/vendor/autoload.php';

use Markdown\kparser\parsedown;

$parse = new parsedown();

var_dump($parse->text('# Hello'));