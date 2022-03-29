<?php
require_once '../../settings/config.php';
require_once __DIR__.'/phpQuery-onefile.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

setlocale(LC_ALL, 'ru_RU');
date_default_timezone_set('Europe/Moscow');
header('Content-type: text/html; charset=utf-8');

$file = file(__DIR__ . "/pages/Новая вкладка.html");
$html = implode($file);

$dom = new DOMDocument();
$dom->loadHTMLFile(__DIR__ . "/pages/Новая вкладка.html");

$xpath = new DomXPath($dom);
$i = 1;
// $hrefs = $dom->getElementsByTagName('a.top-site-button');

$query = '//@href';

$entries = $xpath->query($query);

// var_dump($entries);
print_r($entries);

// $titles[] = $dom->find('.title > span[dir=auto]');
// foreach($hrefs as $href) {
// print_r($i.": <a href='".$href."'> $title</a><br>");
// $i++;

// $arr[] = [
// 	[$title,
// 	$href]
// ];

// }

// print_r($arr);

// <books>
//  <book>Шаблоны корпоративных приложений</book>
//  <book>Приёмы объектно-ориентированного проектирования. Паттерны проектирования</book>
//  <book>Чистый код</book>
// </books>
// XML;

// $dom = new DOMDocument;
// $dom->loadXML($xml);
// $books = $dom->getElementsByTagName('book');
// foreach ($books as $book) {
//     echo $book->nodeValue, PHP_EOL;
// }
// 
