<?php
require_once 'xpe.php';

$content = file(__DIR__ . "\list_of_all_pages.txt");
$dir = './htmls/';

saveHtmls($content,$dir);