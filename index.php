<?php

require_once 'lib/NewParse.php';

$parsedown = new Parsedown();

$text = file_get_contents('article.txt');

echo $parsedown->text($text);