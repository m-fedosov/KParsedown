<?php

require_once 'php-parser/parsedown.php';

$parsedown = new Parsedown();

$text = file_get_contents('parse-it.txt');

echo $parsedown->text($text);